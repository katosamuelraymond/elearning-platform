<?php

namespace App\Http\Controllers\Modules\Exams;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Exam;
use App\Models\Assessment\ExamAttempt;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Subject;
use App\Models\Academic\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Import the Assessment Models
use App\Models\Assessment\Question;
use App\Models\Assessment\QuestionOption;

class TeacherExamsController extends Controller
{
    /**
     * Get teacher's assigned classes and subjects
     */
    private function getTeacherAssignments()
    {
        return TeacherAssignment::where('teacher_id', Auth::id())
            ->where('is_active', true)
            ->with(['class', 'subject'])
            ->get();
    }

    /**
     * Get assigned classes for the teacher
     */
    private function getAssignedClasses()
    {
        $assignments = $this->getTeacherAssignments();
        return $assignments->pluck('class')->unique('id')->values();
    }

    /**
     * Get assigned subjects for the teacher
     */
    private function getAssignedSubjects()
    {
        $assignments = $this->getTeacherAssignments();
        return $assignments->pluck('subject')->unique('id')->values();
    }

    /**
     * Validate if teacher is assigned to the class and subject
     */
    private function validateTeacherAssignment($classId, $subjectId)
    {
        return TeacherAssignment::where('teacher_id', Auth::id())
            ->where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Display teacher's exams
     */

    /**
     * Check if attempt can be auto-graded (all questions are auto-gradable)
     */
    private function shouldAttemptBeAutoGraded(Exam $exam, ExamAttempt $attempt, array $manualGrades = [])
    {
        $answers = $attempt->answers ?? [];

        // If there are any manual grades, it's already being handled manually
        if (!empty($manualGrades)) {
            return false;
        }

        // Check if all questions are auto-gradable types
        foreach ($exam->questions as $question) {
            $studentAnswer = $answers[$question->id] ?? null;

            // If it's a subjective question type AND has an answer, it needs manual grading
            if (in_array($question->type, ['short_answer', 'essay', 'fill_blank']) && !empty($studentAnswer)) {
                return false;
            }
        }

        // All questions are either objective types or subjective types without answers
        return true;
    }


    public function showAttempt(Exam $exam, ExamAttempt $attempt)
    {
        // Verify teacher owns this exam
        if ($exam->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $attempt->load(['student', 'exam', 'exam.questions' => function($query) {
            $query->with(['options' => function($q) {
                $q->orderBy('order');
            }]);
        }]);

        // Get answers and manual grades
        $answers = $attempt->answers ?? [];
        $manualGrades = $attempt->manual_grades ?? [];

        // Robust JSON handling for manual_grades
        if (is_string($manualGrades)) {
            $manualGrades = json_decode($manualGrades, true) ?? [];
        }

        // Ensure it's always an array
        if (!is_array($manualGrades)) {
            $manualGrades = [];
        }

        // 🔍 CRITICAL DEBUG: Check if attempt should be auto-graded
        $shouldBeAutoGraded = $this->shouldAttemptBeAutoGraded($exam, $attempt, $manualGrades);

        \Log::info('🔍 ATTEMPT STATUS DEBUG:', [
            'attempt_id' => $attempt->id,
            'current_status' => $attempt->status,
            'should_be_auto_graded' => $shouldBeAutoGraded,
            'has_manual_grades' => !empty($manualGrades),
            'manual_grades_count' => count($manualGrades),
            'total_questions' => $exam->questions->count()
        ]);

        // 🔧 AUTO-GRADE ATTEMPT IF NEEDED
        if ($shouldBeAutoGraded && $attempt->status === 'submitted') {
            \Log::info('🔄 AUTO-GRADING ATTEMPT:', ['attempt_id' => $attempt->id]);

            $totalScore = $this->calculateScore($exam, $attempt, $manualGrades);

            $attempt->update([
                'total_score' => $totalScore,
                'status' => 'graded'
            ]);

            \Log::info('✅ ATTEMPT AUTO-GRADED:', [
                'attempt_id' => $attempt->id,
                'new_score' => $totalScore,
                'new_status' => 'graded'
            ]);

            // Reload the attempt with updated data
            $attempt->refresh();
        }

        return $this->renderView('modules.exams.attempts.show', [
            'exam' => $exam,
            'attempt' => $attempt,
            'answers' => $answers,
            'manualGrades' => $manualGrades,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Update score for subjective questions with manual grading support
     */
    public function updateScore(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        // Verify teacher owns this exam
        if ($exam->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'score' => 'required|numeric|min:0'
        ]);

        $questionId = $request->question_id;
        $score = (float) $request->score;

        // Get the question to check max points
        $question = $exam->questions->where('id', $questionId)->first();
        if (!$question) {
            return redirect()->back()->with('error', 'Question not found in this exam.');
        }

        $maxPoints = $question->pivot->points ?? $question->points ?? 0;

        // Ensure score doesn't exceed max points
        $finalScore = min($score, $maxPoints);

        // PROPERLY handle manual grades
        $manualGrades = $attempt->manual_grades ?? [];
        if (is_string($manualGrades)) {
            $manualGrades = json_decode($manualGrades, true) ?? [];
        }
        if (!is_array($manualGrades)) {
            $manualGrades = [];
        }

        // Update the manual grades array
        $manualGrades[$questionId] = $finalScore;

        // Recalculate total score
        $totalScore = $this->calculateScore($exam, $attempt, $manualGrades);

        // Update the attempt with manual grades
        $attempt->update([
            'manual_grades' => $manualGrades,
            'total_score' => $totalScore,
            'status' => 'graded'
        ]);

        \Log::info('📊 MANUAL SCORE UPDATE SUCCESS:', [
            'teacher_id' => auth()->id(),
            'exam_id' => $exam->id,
            'attempt_id' => $attempt->id,
            'question_id' => $questionId,
            'score' => $finalScore,
            'max_points' => $maxPoints,
            'new_total_score' => $totalScore
        ]);

        return redirect()->back()->with('success',
            "Score updated: {$finalScore}/{$maxPoints} points awarded for this question."
        );
    }

    private function calculateScore(Exam $exam, $attempt, $manualGrades = null)
    {
        $score = 0;
        $answers = $attempt->answers ?? [];

        // Use provided manual grades or get from attempt
        if ($manualGrades === null) {
            $manualGrades = $attempt->manual_grades ?? [];
        }

        // Ensure manual_grades is properly decoded
        if (is_string($manualGrades)) {
            $manualGrades = json_decode($manualGrades, true) ?? [];
        }
        if (!is_array($manualGrades)) {
            $manualGrades = [];
        }

        \Log::info('🔢 CALCULATING SCORE:', [
            'attempt_id' => $attempt->id,
            'manual_grades_count' => count($manualGrades),
            'answers_count' => count($answers),
            'total_questions' => $exam->questions->count()
        ]);

        foreach ($exam->questions as $question) {
            $studentAnswer = $answers[$question->id] ?? null;
            $maxPoints = $question->pivot->points ?? $question->points ?? 0;

            // ✅ FIXED LOGIC: Check if manual grade exists AND is not null
            if (isset($manualGrades[$question->id]) && $manualGrades[$question->id] !== null) {
                // Use manual grade for this question
                $manualScore = (float) $manualGrades[$question->id];
                $score += $manualScore;
                \Log::info('📊 USING MANUAL GRADE:', [
                    'question_id' => $question->id,
                    'type' => $question->type,
                    'manual_grade' => $manualScore,
                    'added_to_total' => $manualScore
                ]);
            } else {
                // Auto-grade objective questions (MCQ, True/False)
                if (in_array($question->type, ['mcq', 'true_false'])) {
                    if (!empty($studentAnswer)) {
                        $autoScore = $this->evaluateStudentAnswer($question, $studentAnswer);
                        $score += $autoScore;

                        \Log::info('📊 AUTO-GRADED QUESTION:', [
                            'question_id' => $question->id,
                            'type' => $question->type,
                            'student_answer' => $studentAnswer,
                            'correct_answer' => $question->correct_answer,
                            'auto_score' => $autoScore,
                            'max_points' => $maxPoints
                        ]);
                    } else {
                        // No answer provided for objective question
                        \Log::info('📊 NO ANSWER PROVIDED FOR OBJECTIVE QUESTION:', [
                            'question_id' => $question->id,
                            'type' => $question->type,
                            'added_to_total' => 0
                        ]);
                    }
                } else {
                    // Subjective questions (short_answer, essay, fill_blank) get 0 until manually graded
                    // But only log if they actually have an answer
                    if (!empty($studentAnswer)) {
                        \Log::info('📊 SUBJECTIVE QUESTION (NEEDS MANUAL GRADING):', [
                            'question_id' => $question->id,
                            'type' => $question->type,
                            'has_answer' => true,
                            'added_to_total' => 0
                        ]);
                    } else {
                        \Log::info('📊 SUBJECTIVE QUESTION (NO ANSWER):', [
                            'question_id' => $question->id,
                            'type' => $question->type,
                            'has_answer' => false,
                            'added_to_total' => 0
                        ]);
                    }
                }
            }
        }

        \Log::info('🔢 FINAL CALCULATED SCORE:', ['total_score' => $score]);
        return $score;
    }
    /**
     * Bulk update manual grades for multiple questions
     */
    public function bulkUpdateGrades(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        // Verify teacher owns this exam
        if ($exam->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'numeric|min:0'
        ]);

        // PROPERLY handle manual grades
        $manualGrades = $attempt->manual_grades ?? [];
        if (is_string($manualGrades)) {
            $manualGrades = json_decode($manualGrades, true) ?? [];
        }
        if (!is_array($manualGrades)) {
            $manualGrades = [];
        }

        foreach ($request->scores as $questionId => $score) {
            $question = $exam->questions->where('id', $questionId)->first();
            if ($question) {
                $maxPoints = $question->pivot->points ?? $question->points ?? 0;
                $manualGrades[$questionId] = min($score, $maxPoints);
            }
        }

        // Recalculate total score
        $totalScore = $this->calculateScore($exam, $attempt, $manualGrades);

        $attempt->update([
            'manual_grades' => $manualGrades,
            'total_score' => $totalScore,
            'status' => 'graded'
        ]);

        \Log::info('📊 BULK MANUAL GRADES UPDATE:', [
            'teacher_id' => auth()->id(),
            'exam_id' => $exam->id,
            'attempt_id' => $attempt->id,
            'updated_questions' => count($request->scores),
            'new_total_score' => $attempt->total_score
        ]);

        return redirect()->back()->with('success',
            "Updated scores for " . count($request->scores) . " questions successfully!"
        );
    }
    public function index(Request $request)
    {
        $query = Exam::where('teacher_id', auth()->id())
            ->with(['class', 'subject'])
            ->withCount(['attempts', 'attempts as submitted_count' => function($query) {
                $query->where('status', 'submitted');
            }]);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhereHas('class', function($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('subject', function($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            } elseif ($request->status === 'archived') {
                $query->where('is_archived', true);
            }
        }

        // Filter by class
        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }

        // Filter by subject
        if ($request->has('subject_id') && $request->subject_id != '') {
            $query->where('subject_id', $request->subject_id);
        }

        $exams = $query->latest()->paginate(10);

        $stats = [
            'total' => Exam::where('teacher_id', auth()->id())->count(),
            'published' => Exam::where('teacher_id', auth()->id())->where('is_published', true)->count(),
            'draft' => Exam::where('teacher_id', auth()->id())->where('is_published', false)->count(),
            'archived' => Exam::where('teacher_id', auth()->id())->where('is_archived', true)->count(),
            'attempts' => ExamAttempt::whereHas('exam', function($query) {
                $query->where('teacher_id', auth()->id());
            })->count(),
        ];

        // Get teacher's assigned classes and subjects
        $assignedClasses = TeacherAssignment::where('teacher_id', auth()->id())
            ->where('is_active', true)
            ->with('class')
            ->get()
            ->pluck('class')
            ->unique('id')
            ->filter();

        $assignedSubjects = TeacherAssignment::where('teacher_id', auth()->id())
            ->where('is_active', true)
            ->with('subject')
            ->get()
            ->pluck('subject')
            ->unique('id')
            ->filter();

        return $this->renderView('modules.exams.index', [
            'exams' => $exams,
            'stats' => $stats,
            'classes' => $assignedClasses,
            'subjects' => $assignedSubjects,
            'filters' => $request->only(['search', 'status', 'class_id', 'subject_id']),
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Only show classes and subjects the teacher is assigned to
        $classes = $this->getAssignedClasses();
        $subjects = $this->getAssignedSubjects();

        return $this->renderView('modules.exams.create', [
            'classes' => $classes,
            'subjects' => $subjects,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Get questions from the question bank with filters
     */
    public function getQuestionBank(Request $request)
    {
        try {
            $filters = $request->validate([
                'subject_id' => 'nullable|exists:subjects,id',
                'type' => 'nullable|in:mcq,true_false,short_answer,essay,fill_blank',
                'difficulty' => 'nullable|in:easy,medium,hard',
                'search' => 'nullable|string|max:255'
            ]);

            // Get teacher's assigned subjects
            $assignedSubjectIds = TeacherAssignment::where('teacher_id', Auth::id())
                ->where('is_active', true)
                ->pluck('subject_id')
                ->unique()
                ->toArray();

            $query = Question::with(['subject', 'options'])
                ->whereIn('subject_id', $assignedSubjectIds)
                ->where('is_active', true);

            // Apply filters
            if (!empty($filters['subject_id'])) {
                // Validate that the teacher is assigned to this subject
                if (!in_array($filters['subject_id'], $assignedSubjectIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are not assigned to this subject.'
                    ], 403);
                }
                $query->where('subject_id', $filters['subject_id']);
            }

            if (!empty($filters['type'])) {
                $query->where('type', $filters['type']);
            }

            if (!empty($filters['difficulty'])) {
                $query->where('difficulty', $filters['difficulty']);
            }

            if (!empty($filters['search'])) {
                $query->where('question_text', 'like', '%' . $filters['search'] . '%');
            }

            $questions = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'questions' => $questions
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch question bank:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load questions from bank.'
            ], 500);
        }
    }

    /**
     * Parse bank questions from comma-separated string
     */
    private function parseBankQuestions($bankQuestionsString)
    {
        if (empty($bankQuestionsString)) {
            return [];
        }

        $questionIds = array_map('intval', explode(',', $bankQuestionsString));
        $questionIds = array_filter($questionIds); // Remove empty values

        // Verify these questions exist and belong to teacher's subjects
        $assignedSubjectIds = TeacherAssignment::where('teacher_id', Auth::id())
            ->where('is_active', true)
            ->pluck('subject_id')
            ->unique()
            ->toArray();

        $validQuestions = Question::whereIn('id', $questionIds)
            ->whereIn('subject_id', $assignedSubjectIds)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        return $validQuestions;
    }

    /**
     * Validate and save new questions for store operation
     */
    /**
     * Validate and save new questions for store operation - FIXED VERSION
     */
    /**
     * Validate and save new questions for store operation - FIXED VERSION
     */
    /**
     * Validate and save new questions for store operation - FIXED VERSION
     */
    private function validateAndSaveNewQuestions(Exam $exam, array $questionsData)
    {
        Log::info('New questions data received for store:', ['questions_count' => count($questionsData)]);

        if (empty($questionsData)) {
            Log::info('No new questions to process');
            return [];
        }

        // Validation rules (including save_to_bank as nullable in:0,1)
        $rules = [];
        $attributes = [];

        foreach ($questionsData as $index => $question) {
            $questionType = $question['type'] ?? null;

            $rules["{$index}.type"] = ['required', Rule::in(['mcq', 'true_false', 'short_answer', 'essay', 'fill_blank'])];
            $rules["{$index}.points"] = ['required', 'numeric', 'min:0.5', 'max:100'];
            $rules["{$index}.question_text"] = ['required', 'string', 'min:1'];
            $rules["{$index}.save_to_bank"] = ['required', 'in:0,1'];

            $attributes["{$index}.question_text"] = "question {$index} text";
            $attributes["{$index}.type"] = "question {$index} type";
            $attributes["{$index}.points"] = "question {$index} points";

            switch ($questionType) {
                case 'mcq':
                    $rules["{$index}.correct_answer"] = ['required', 'integer', 'min:0'];
                    $rules["{$index}.options"] = ['required', 'array', 'min:2', 'max:6'];
                    $rules["{$index}.options.*"] = ['required', 'string', 'max:500'];
                    $attributes["{$index}.correct_answer"] = "question {$index} correct answer";
                    break;
                case 'true_false':
                    $rules["{$index}.correct_answer"] = ['required', Rule::in(['true', 'false'])];
                    $attributes["{$index}.correct_answer"] = "question {$index} correct answer";
                    break;
                case 'short_answer':
                    $rules["{$index}.expected_answer"] = ['nullable', 'string'];
                    $attributes["{$index}.expected_answer"] = "question {$index} expected answer";
                    break;
                case 'essay':
                    $rules["{$index}.grading_rubric"] = ['nullable', 'string'];
                    $attributes["{$index}.grading_rubric"] = "question {$index} grading rubric";
                    break;
                case 'fill_blank':
                    $rules["{$index}.question_text"] = ['required', 'string'];
                    $rules["{$index}.blank_answers"] = ['required', 'string'];
                    $attributes["{$index}.blank_answers"] = "question {$index} blank answers";
                    break;
            }
        }

        $validator = Validator::make($questionsData, $rules);
        $validator->setAttributeNames($attributes);

        if ($validator->fails()) {
            Log::error('New question validation failed:', ['errors' => $validator->errors()->toArray()]);
            throw new ValidationException($validator);
        }

        $validatedQuestions = $validator->validated();
        $savedQuestions = [];
        $questionOrder = $exam->questions()->count();

        foreach ($validatedQuestions as $index => $qData) {
            try {
                $details = [];
                $correctAnswer = null;

                switch ($qData['type']) {
                    case 'mcq':
                        $correctAnswer = $qData['correct_answer'];
                        break;
                    case 'true_false':
                        $correctAnswer = $qData['correct_answer'];
                        break;
                    case 'short_answer':
                        $details['expected_answer'] = $qData['expected_answer'] ?? null;
                        $correctAnswer = $details['expected_answer'];
                        break;
                    case 'essay':
                        $details['grading_rubric'] = $qData['grading_rubric'] ?? null;
                        break;
                    case 'fill_blank':
                        $blankAnswers = array_map('trim', explode(',', $qData['blank_answers'] ?? ''));
                        $details['blank_answers'] = $blankAnswers;
                        $correctAnswer = json_encode($blankAnswers);
                        break;
                }

                $saveToBank = isset($qData['save_to_bank']) && $qData['save_to_bank'] === '1';

                $questionData = [
                    'subject_id' => $exam->subject_id,
                    'created_by' => Auth::id(),
                    'type' => $qData['type'],
                    'points' => $qData['points'],
                    'question_text' => $qData['question_text'],
                    'details' => !empty($details) ? $details : null,
                    'correct_answer' => $correctAnswer,
                    'is_active' => true,
                    'is_bank_question' => $saveToBank, // only if explicitly checked
                ];

                $question = Question::create($questionData);

                $exam->questions()->attach($question->id, [
                    'order' => $questionOrder++,
                    'points' => $qData['points']
                ]);

                if ($qData['type'] === 'mcq' && !empty($qData['options'])) {
                    foreach ($qData['options'] as $optIndex => $optionText) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $optionText,
                            'is_correct' => ($optIndex == $qData['correct_answer']),
                            'order' => $optIndex,
                        ]);
                    }
                }

                $savedQuestions[] = $question->id;

                Log::info('Question created successfully:', [
                    'question_id' => $question->id,
                    'type' => $question->type,
                    'is_bank_question' => $saveToBank,
                    'exam_id' => $exam->id
                ]);

            } catch (\Exception $e) {
                Log::error('Failed to create question:', [
                    'index' => $index,
                    'error' => $e->getMessage(),
                    'data' => $qData
                ]);
                throw $e;
            }
        }

        Log::info('New questions saved successfully for exam:', [
            'exam_id' => $exam->id,
            'questions_count' => count($savedQuestions)
        ]);

        return $savedQuestions;
    }

    /**
     * Attach questions to exam for store operation - FIXED VERSION
     */
    private function attachQuestionsToExamForStore(Exam $exam, array $bankQuestionIds, array $newQuestionsData)
    {
        $questionOrder = 0;
        $totalPoints = 0;

        Log::info('🔍 DEBUG: attachQuestionsToExamForStore - Starting', [
            'bank_question_ids' => $bankQuestionIds,
            'new_questions_count' => count($newQuestionsData)
        ]);

        // 1. Attach bank questions
        foreach ($bankQuestionIds as $questionId) {
            $question = Question::find($questionId);
            if ($question) {
                $exam->questions()->attach($question->id, [
                    'order' => $questionOrder++,
                    'points' => $question->points
                ]);

                $totalPoints += $question->points;
                Log::info('🔍 DEBUG: Bank question attached to exam', [
                    'exam_id' => $exam->id,
                    'question_id' => $question->id,
                    'points' => $question->points,
                    'question_order' => $questionOrder - 1
                ]);
            }
        }

        // 2. Create and attach new questions
        if (!empty($newQuestionsData)) {
            $savedQuestionIds = $this->validateAndSaveNewQuestions($exam, $newQuestionsData);

            // Calculate points for new questions
            foreach ($newQuestionsData as $questionData) {
                $totalPoints += $questionData['points'] ?? 0;
            }

            Log::info('🔍 DEBUG: New questions created and attached', [
                'questions_count' => count($savedQuestionIds),
                'points_added' => $totalPoints
            ]);
        }

        Log::info('🔍 DEBUG: attachQuestionsToExamForStore - Completed', [
            'total_questions_attached' => $questionOrder + count($newQuestionsData),
            'total_points' => $totalPoints
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('📝 EXAM STORE REQUEST DATA:', [
            'all_data' => $request->all(),
            'questions_data' => $request->input('questions', []),
            'selected_bank_questions' => $request->input('selected_bank_questions')
        ]);
        // Use transaction to ensure data consistency
        return DB::transaction(function () use ($request) {
            try {
                // 1. Initial Validation
                $validated = $request->validate([
                    'title' => 'required|string|max:255',
                    'class_id' => 'required|exists:school_classes,id',
                    'subject_id' => 'required|exists:subjects,id',
                    'instructions' => 'nullable|string',
                    'description' => 'nullable|string',
                    'type' => ['required', Rule::in('quiz', 'midterm', 'end_of_term', 'practice', 'mock')],
                    'duration' => 'required|integer|min:1',
                    'total_marks' => 'required|integer|min:1',
                    'passing_marks' => 'required|integer|min:0',
                    'start_time' => 'required|date',
                    'end_time' => ['required', 'date', 'after:start_time'],
                    'max_attempts' => 'required|integer|min:1',

                    // Hidden field for 'Save as Draft' logic
                    'is_draft' => 'required|in:0,1',

                    // Bank questions (comma-separated IDs)
                    'selected_bank_questions' => 'nullable|string',

                    // New questions
                    'questions' => 'nullable|array',
                ]);

                // 2. Validate teacher assignment
                if (!$this->validateTeacherAssignment($validated['class_id'], $validated['subject_id'])) {
                    throw ValidationException::withMessages([
                        'class_id' => 'You are not assigned to teach this subject for the selected class.',
                        'subject_id' => 'You are not assigned to teach this subject for the selected class.'
                    ]);
                }

                // 3. Validate that we have at least one question if not draft
                $bankQuestions = $this->parseBankQuestions($validated['selected_bank_questions'] ?? '');
                $newQuestions = $validated['questions'] ?? [];

                if ($validated['is_draft'] == '0' && empty($bankQuestions) && empty($newQuestions)) {
                    throw ValidationException::withMessages([
                        'questions' => 'At least one question is required for published exams.'
                    ]);
                }

                // 4. Process Checkbox and Draft Status
                $data = array_merge($validated, [
                    'teacher_id' => Auth::id(),
                    'randomize_questions' => $request->has('randomize_questions'),
                    'require_fullscreen' => $request->has('require_fullscreen'),
                    'show_results' => $request->has('show_results'),
                    'is_published' => $request->input('is_draft') == '1' ? false : $request->has('is_published'),
                ]);

                // 5. Create Exam
                $exam = Exam::create(Arr::except($data, ['questions', 'selected_bank_questions', 'is_draft']));

                // 6. Attach questions from bank and create new questions
                $this->attachQuestionsToExamForStore($exam, $bankQuestions, $newQuestions);

                $message = $exam->is_published ? 'Exam created and published successfully!' : 'Exam saved as draft successfully!';

                return redirect()->route('teacher.exams.index')
                    ->with('success', $message);

            } catch (ValidationException $e) {
                Log::error('Exam creation validation failed:', ['errors' => $e->errors()]);
                throw $e;
            } catch (\Exception $e) {
                Log::error('Exam creation failed:', ['error' => $e->getMessage()]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to create exam. Please try again.');
            }
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        // Verify teacher owns this exam
        if ($exam->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $exam->load(['class', 'subject', 'attempts.student', 'questions']);

        return $this->renderView('modules.exams.show', [
            'exam' => $exam,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
    {
        // Verify teacher owns this exam
        if ($exam->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Only show classes and subjects the teacher is assigned to
        $classes = $this->getAssignedClasses();
        $subjects = $this->getAssignedSubjects();

        // Eager load questions for the edit form
        $exam->load('questions');

        return $this->renderView('modules.exams.edit', [
            'exam' => $exam,
            'classes' => $classes,
            'subjects' => $subjects,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Validate and save custom questions for update operation
     */
    private function validateAndSaveCustomQuestions(Exam $exam, array $questionsData)
    {
        Log::info('Custom questions data received for update:', ['questions_count' => count($questionsData)]);

        // Define validation rules for each question item
        $rules = [];

        foreach ($questionsData as $index => $question) {
            $questionType = $question['type'] ?? null;

            // Base rules for all questions
            $rules["{$index}.type"] = ['required', Rule::in(['mcq', 'true_false', 'short_answer', 'essay', 'fill_blank'])];
            $rules["{$index}.points"] = ['required', 'numeric', 'min:0.5', 'max:100'];
            $rules["{$index}.question_text"] = ['required', 'string'];

            // Type-specific rules
            switch ($questionType) {
                case 'mcq':
                    $rules["{$index}.correct_answer"] = ['required', 'integer', 'min:0'];
                    $rules["{$index}.options"] = ['required', 'array', 'min:2', 'max:6'];
                    $rules["{$index}.options.*"] = ['required', 'string', 'max:500'];
                    break;

                case 'true_false':
                    $rules["{$index}.correct_answer"] = ['required', Rule::in(['true', 'false'])];
                    break;

                case 'short_answer':
                    $rules["{$index}.expected_answer"] = ['nullable', 'string'];
                    break;

                case 'essay':
                    $rules["{$index}.grading_rubric"] = ['nullable', 'string'];
                    break;

                case 'fill_blank':
                    $rules["{$index}.blank_question"] = ['required', 'string'];
                    $rules["{$index}.blank_answers"] = ['required', 'string'];
                    break;
            }
        }

        // Validate the questions array
        $validator = Validator::make($questionsData, $rules);

        if ($validator->fails()) {
            Log::error('Custom question validation failed:', ['errors' => $validator->errors()->toArray()]);
            throw new ValidationException($validator);
        }

        $validatedQuestions = $validator->validated();
        $savedQuestions = [];

        // Save questions
        foreach ($validatedQuestions as $index => $qData) {
            $details = [];
            $correctAnswer = null;

            // Prepare question data based on type
            switch ($qData['type']) {
                case 'mcq':
                    $correctAnswer = $qData['correct_answer'];
                    break;
                case 'true_false':
                    $correctAnswer = $qData['correct_answer'];
                    break;
                case 'short_answer':
                    $details['expected_answer'] = $qData['expected_answer'] ?? null;
                    $correctAnswer = $details['expected_answer'];
                    break;
                case 'essay':
                    $details['grading_rubric'] = $qData['grading_rubric'] ?? null;
                    break;
                case 'fill_blank':
                    $details['blank_question'] = $qData['blank_question'];
                    $blankAnswers = array_map('trim', explode(',', $qData['blank_answers'] ?? ''));
                    $details['blank_answers'] = $blankAnswers;
                    $correctAnswer = json_encode($blankAnswers);
                    break;
            }

            // Check if this is an existing question (has ID) or new question
            $questionId = $questionsData[$index]['id'] ?? null;

            if ($questionId && is_numeric($questionId)) {
                // Update existing question
                $question = Question::find($questionId);
                if ($question && $question->created_by === Auth::id()) {
                    $question->update([
                        'type' => $qData['type'],
                        'points' => $qData['points'],
                        'question_text' => $qData['question_text'],
                        'details' => !empty($details) ? $details : null,
                        'correct_answer' => $correctAnswer,
                    ]);

                    // Update options for MCQ questions
                    if ($qData['type'] === 'mcq' && !empty($qData['options'])) {
                        // Delete existing options
                        QuestionOption::where('question_id', $question->id)->delete();

                        // Create new options
                        foreach ($qData['options'] as $optIndex => $optionText) {
                            QuestionOption::create([
                                'question_id' => $question->id,
                                'option_text' => $optionText,
                                'is_correct' => ($optIndex == $qData['correct_answer']),
                                'order' => $optIndex,
                            ]);
                        }
                    }

                    // Update exam-question pivot
                    $exam->questions()->syncWithoutDetaching([$question->id => [
                        'points' => $qData['points']
                    ]]);
                }
            } else {
                // Create new question
                $questionData = [
                    'subject_id' => $exam->subject_id,
                    'created_by' => Auth::id(),
                    'type' => $qData['type'],
                    'points' => $qData['points'],
                    'question_text' => $qData['question_text'],
                    'details' => !empty($details) ? $details : null,
                    'correct_answer' => $correctAnswer,
                    'is_active' => true,
                    'is_bank_question' => false,
                ];

                $question = Question::create($questionData);

                // Attach to exam
                $exam->questions()->attach($question->id, [
                    'order' => $exam->questions()->count(),
                    'points' => $qData['points']
                ]);

                // Handle Multiple Choice Options
                if ($qData['type'] === 'mcq' && !empty($qData['options'])) {
                    foreach ($qData['options'] as $optIndex => $optionText) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $optionText,
                            'is_correct' => ($optIndex == $qData['correct_answer']),
                            'order' => $optIndex,
                        ]);
                    }
                }
            }

            $savedQuestions[] = $qData;
        }

        Log::info('All custom questions processed successfully for exam:', [
            'exam_id' => $exam->id,
            'questions_count' => count($validatedQuestions)
        ]);

        return $savedQuestions;
    }

    /**
     * Update exam questions without deleting everything
     */
    private function updateExamQuestions(Exam $exam, array $bankQuestionIds, array $newQuestionsData, Request $request)
    {
        Log::info('🔧 DEBUG: updateExamQuestions - Starting', [
            'exam_id' => $exam->id,
            'bank_question_ids' => $bankQuestionIds,
            'new_questions_count' => count($newQuestionsData),
            'has_questions_input' => $request->has('questions'),
            'has_selected_bank_questions' => $request->has('selected_bank_questions'),
        ]);

        // ---------------------------------------------------------
        // 1. If no question-related inputs were provided, do nothing.
        //    This prevents accidental removal when the form didn't include question fields.
        // ---------------------------------------------------------
        $questionsProvided = $request->has('questions') || $request->has('selected_bank_questions') || $request->has('deleted_custom_question_ids');

        if (!$questionsProvided) {
            Log::warning("⚠ No question inputs provided in update — NOT modifying questions to avoid accidental deletion.");
            return;
        }

        // ---------------------------------------------------------
        // 2. Prepare current state
        // ---------------------------------------------------------
        // All current question IDs associated with this exam
        $currentQuestions = $exam->questions()->pluck('questions.id')->map(function ($v) { return (string) $v; })->toArray();

        // current bank question IDs attached to exam (as strings)
        $currentBankIds = $exam->questions()
            ->where('is_bank_question', true)
            ->pluck('questions.id')
            ->map(function ($v) { return (string) $v; })->toArray();

        // Normalize provided desired bank ids (strings)
        $desiredBankIds = array_map('strval', $bankQuestionIds ?: []);

        // ---------------------------------------------------------
        // 3. Handle bank question attachments / detaches / pivot updates
        //    Only act on bank questions if the form included selected_bank_questions (even if it's empty)
        // ---------------------------------------------------------
        if ($request->has('selected_bank_questions')) {
            // compute ids to detach (previous bank questions that are no longer selected)
            $toDetach = array_values(array_diff($currentBankIds, $desiredBankIds));
            // compute ids to attach (new bank questions selected)
            $toAttach = array_values(array_diff($desiredBankIds, $currentBankIds));
            // compute ids that remain attached (but may have points changed)
            $stillAttached = array_values(array_intersect($desiredBankIds, $currentBankIds));

            // Detach removed bank-question links (only the pivot); DO NOT delete Question records
            if (!empty($toDetach)) {
                Log::info('🔧 Detaching removed bank question pivot entries', ['to_detach' => $toDetach]);
                $exam->questions()->detach($toDetach);
            }

            // Attach newly selected bank questions with pivot data (order & points)
            $order = 0;
            foreach ($desiredBankIds as $id) {
                // guard: skip invalid ids
                $q = Question::find($id);
                if (!$q) {
                    Log::warning("⚠ Bank question id not found, skipping attach/update: {$id}");
                    continue;
                }

                // get points from request or fallback to question default/previous
                $points = $request->input("bank_question_points.{$id}", $q->points);

                if (in_array($id, $toAttach, true)) {
                    // attach newly selected
                    $exam->questions()->attach($id, [
                        'order' => $order,
                        'points' => $points,
                    ]);
                    Log::info("➕ Attached bank question {$id} with points={$points} order={$order}");
                } elseif (in_array($id, $stillAttached, true)) {
                    // update existing pivot (points/order) for already-attached bank question
                    $exam->questions()->updateExistingPivot($id, [
                        'order' => $order,
                        'points' => $points,
                    ]);
                    Log::info("🔁 Updated pivot for bank question {$id} with points={$points} order={$order}");
                }
                $order++;
            }
        } else {
            Log::info('ℹ selected_bank_questions not present in request — leaving bank-question associations unchanged.');
        }

        // ---------------------------------------------------------
        // 4. Handle custom-question deletions ONLY if explicitly requested
        //    The user must supply 'deleted_custom_question_ids' (array) to remove saved custom questions.
        // ---------------------------------------------------------
        $deletedCustomIds = $request->input('deleted_custom_question_ids', []);
        if (!empty($deletedCustomIds) && is_array($deletedCustomIds)) {
            // sanitize ints
            $deletedCustomIds = array_map('intval', $deletedCustomIds);

            // Ensure we only delete custom questions that are actually attached to the exam and are custom
            $safeToDelete = Question::whereIn('id', $deletedCustomIds)
                ->where('is_bank_question', false)
                ->whereHas('exams', function ($q) use ($exam) {
                    $q->where('exams.id', $exam->id);
                })
                ->pluck('id')
                ->toArray();

            if (!empty($safeToDelete)) {
                Log::info('🗑 Deleting user-requested custom questions', ['ids' => $safeToDelete]);

                // Delete options first
                QuestionOption::whereIn('question_id', $safeToDelete)->delete();

                // Delete the question records
                Question::whereIn('id', $safeToDelete)->delete();

                // Detach them from the exam pivot
                $exam->questions()->detach($safeToDelete);
            } else {
                Log::info('ℹ No safe custom questions found to delete from deleted_custom_question_ids');
            }
        } else {
            Log::info('ℹ No deleted_custom_question_ids provided — preserving all existing custom questions.');
        }

        // ---------------------------------------------------------
        // 5. Preserve existing custom questions: no blanket sync/detach.
        //    We will not call ->sync(...) which would detach unspecified items.
        //    Instead: only attach new custom questions and update existing ones in validateAndSaveCustomQuestions().
        // ---------------------------------------------------------

        // ---------------------------------------------------------
        // 6. Attach/update custom questions (new or updated) if provided
        //    validateAndSaveCustomQuestions should create new Question rows, update existing ones,
        //    and attach them to the exam if needed. It MUST NOT delete other questions.
        // ---------------------------------------------------------
        if (!empty($newQuestionsData)) {
            Log::info('🔧 Processing new/updated custom questions', ['count' => count($newQuestionsData)]);
            $this->validateAndSaveCustomQuestions($exam, $newQuestionsData);
        }

        // Finalize: ensure selected_bank_questions hidden inputs reflect current selection
        $this->logSelectedQuestionsSummary($exam);

        Log::info('🔧 DEBUG: updateExamQuestions - Completed successfully');
    }

    /**
     * Helper to log current selected questions summary (non-destructive)
     */

    private function logSelectedQuestionsSummary(Exam $exam)
    {
        $bankIds = $exam->questions()->where('is_bank_question', true)->pluck('questions.id')->toArray();
        $customIds = $exam->questions()->where('is_bank_question', false)->pluck('questions.id')->toArray();

        Log::info('🔎 Current exam question summary', [
            'exam_id' => $exam->id,
            'bank_question_count' => count($bankIds),
            'bank_question_ids' => $bankIds,
            'custom_question_count' => count($customIds),
            'custom_question_ids' => $customIds,
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        // Verify ownership for teacher
        if (auth()->user()->isTeacher() && $exam->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Use transaction to ensure data consistency
        return DB::transaction(function () use ($request, $exam) {
            try {
                // 1. Initial Validation (same as store)
                $validated = $request->validate([
                    'title' => 'required|string|max:255',
                    'class_id' => 'required|exists:school_classes,id',
                    'subject_id' => 'required|exists:subjects,id',
                    'instructions' => 'nullable|string',
                    'description' => 'nullable|string',
                    'type' => ['required', Rule::in('quiz', 'midterm', 'end_of_term', 'practice', 'mock')],
                    'duration' => 'required|integer|min:1',
                    'total_marks' => 'required|integer|min:1',
                    'passing_marks' => 'required|integer|min:0',
                    'start_time' => 'required|date',
                    'end_time' => ['required', 'date', 'after:start_time'],
                    'max_attempts' => 'required|integer|min:1',
                    'is_draft' => 'required|in:0,1',
                    'selected_bank_questions' => 'nullable|string',
                    'questions' => 'nullable|array',
                ]);

                // 2. Validate teacher assignment
                if (!$this->validateTeacherAssignment($validated['class_id'], $validated['subject_id'])) {
                    throw ValidationException::withMessages([
                        'class_id' => 'You are not assigned to teach this subject for the selected class.',
                        'subject_id' => 'You are not assigned to teach this subject for the selected class.'
                    ]);
                }

                // 3. Validate that we have at least one question if not draft
                $bankQuestions = $this->parseBankQuestions($validated['selected_bank_questions'] ?? '');
                $newQuestions = $validated['questions'] ?? [];

                if ($validated['is_draft'] == '0' && empty($bankQuestions) && empty($newQuestions)) {
                    throw ValidationException::withMessages([
                        'questions' => 'At least one question is required for published exams.'
                    ]);
                }

                // 4. Process Checkbox and Draft Status
                $data = array_merge($validated, [
                    'teacher_id' => Auth::id(),
                    'randomize_questions' => $request->has('randomize_questions'),
                    'require_fullscreen' => $request->has('require_fullscreen'),
                    'show_results' => $request->has('show_results'),
                    'is_published' => $request->input('is_draft') == '1' ? false : $request->has('is_published'),
                ]);

                // 5. Update Exam
                $exam->update(Arr::except($data, ['questions', 'selected_bank_questions', 'is_draft']));

                // 6. Handle questions update - THE FIXED LOGIC
                $this->updateExamQuestions($exam, $bankQuestions, $newQuestions, $request);

                $message = $exam->is_published ? 'Exam updated and published successfully!' : 'Exam updated as draft successfully!';

                return redirect()->route('teacher.exams.index')
                    ->with('success', $message);

            } catch (ValidationException $e) {
                Log::error('Exam update validation failed:', ['errors' => $e->errors()]);
                throw $e;
            } catch (\Exception $e) {
                Log::error('Exam update failed:', ['error' => $e->getMessage()]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to update exam. Please try again.');
            }
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        // Verify teacher owns this exam
        if ($exam->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Use transaction to ensure data consistency
        return DB::transaction(function () use ($exam) {
            try {
                // Get IDs before deleting the exam
                $questionIds = $exam->questions()->pluck('question_id');

                $exam->delete();

                // Delete only new questions (not bank questions)
                Question::whereIn('id', $questionIds)
                    ->where('is_bank_question', false)
                    ->delete();

                QuestionOption::whereIn('question_id', $questionIds)->delete();

                return redirect()->route('teacher.exams.index')
                    ->with('success', 'Exam deleted successfully!');

            } catch (\Exception $e) {
                Log::error('Exam deletion failed:', ['error' => $e->getMessage()]);
                return redirect()->route('teacher.exams.index')
                    ->with('error', 'Failed to delete exam. Please try again.');
            }
        });
    }



    /**
     * Display exam for printing
     */
    public function print(Exam $exam)
    {
        // Verify teacher owns this exam (for teacher controller)
        if (auth()->user()->isTeacher() && $exam->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $exam->load(['class', 'subject', 'questions' => function($query) {
            $query->orderBy('pivot_order');
        }, 'questions.options']);

        $includeAnswers = request()->has('answers');
        $studentVersion = request()->has('student');

        return view('modules.exams.print', [
            'exam' => $exam,
            'includeAnswers' => $includeAnswers,
            'studentVersion' => $studentVersion,
            'showNavbar' => false,
            'showSidebar' => false,
            'showFooter' => false
        ]);
    }

    /**
     * Generate PDF version of exam
     */
    public function printPDF(Exam $exam)
    {
        // Verify teacher owns this exam (for teacher controller)
        if (auth()->user()->isTeacher() && $exam->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $exam->load(['class', 'subject', 'questions' => function($query) {
            $query->orderBy('pivot_order');
        }, 'questions.options']);

        $includeAnswers = request()->has('answers');
        $studentVersion = request()->has('student');

        // For now, we'll redirect to print view. Later you can integrate DomPDF or similar
        return redirect()->route(auth()->user()->isAdmin() ? 'admin.exams.print' : 'teacher.exams.print', [
            'exam' => $exam,
            'answers' => $includeAnswers ? '1' : '0',
            'student' => $studentVersion ? '1' : '0'
        ]);
    }

    /**
     * Toggle publish status of exam.
     */
    public function togglePublish(Exam $exam)
    {
        // Verify teacher owns this exam
        if ($exam->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $exam->update([
            'is_published' => !$exam->is_published
        ]);

        $status = $exam->is_published ? 'published' : 'unpublished';

        return redirect()->route('teacher.exams.index')
            ->with('success', "Exam {$status} successfully!");
    }

    /**
     * Toggle archive status of exam.
     */
    public function toggleArchive(Exam $exam)
    {
        // Verify teacher owns this exam
        if ($exam->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $exam->update([
            'is_archived' => !$exam->is_archived
        ]);

        $status = $exam->is_archived ? 'archived' : 'unarchived';

        return redirect()->route('teacher.exams.index')
            ->with('success', "Exam {$status} successfully!");
    }

    /**
     * Update score for subjective questions with manual grading support
     */


    /**
     * Bulk update manual grades for multiple questions
     */

    /**
     * Evaluate individual answer (for backward compatibility)
     */
    /**
     * Evaluate MCQ answer with robust type handling
     */
    /**
     * Evaluate student answer with robust type handling
     */
    private function evaluateStudentAnswer($question, $studentAnswer)
    {
        $maxPoints = $question->pivot->points ?? $question->points ?? 0;

        // Handle null/empty answers
        if (empty($studentAnswer) && $studentAnswer !== 0 && $studentAnswer !== '0') {
            \Log::info('📝 EMPTY ANSWER:', [
                'question_id' => $question->id,
                'type' => $question->type,
                'student_answer' => $studentAnswer
            ]);
            return 0;
        }

        \Log::info('🔍 EVALUATING ANSWER:', [
            'question_id' => $question->id,
            'type' => $question->type,
            'student_answer' => $studentAnswer,
            'student_answer_type' => gettype($studentAnswer),
            'correct_answer' => $question->correct_answer,
            'correct_answer_type' => gettype($question->correct_answer),
            'max_points' => $maxPoints
        ]);

        switch ($question->type) {
            case 'mcq':
                return $this->evaluateMcqAnswer($question, $studentAnswer, $maxPoints);

            case 'true_false':
                return $this->evaluateTrueFalseAnswer($question, $studentAnswer, $maxPoints);

            case 'short_answer':
            case 'essay':
            case 'fill_blank':
                // Subjective questions need manual grading
                return 0;

            default:
                return 0;
        }
    }

    /**
     * Evaluate MCQ answer with robust type handling and options checking
     */
    private function evaluateMcqAnswer($question, $studentAnswer, $points)
    {
        \Log::info('🎯 MCQ EVALUATION (START)', [
            'question_id' => $question->id,
            'raw_student_answer' => $studentAnswer,
            'points' => $points,
        ]);

        // If empty or null, no points
        if ($studentAnswer === null || $studentAnswer === '' || $studentAnswer === []) {
            \Log::info('📝 EMPTY MCQ ANSWER', ['question_id' => $question->id]);
            return 0;
        }

        // Normalize studentAnswer to integer id when possible
        $studentAnswerId = null;
        if (is_numeric($studentAnswer)) {
            $studentAnswerId = (int) $studentAnswer;
        } elseif (is_string($studentAnswer) && ctype_digit($studentAnswer)) {
            $studentAnswerId = (int) $studentAnswer;
        }

        // Priority: find correct option row
        $correctOption = $question->options->where('is_correct', true)->first();

        if (!$correctOption) {
            \Log::warning('⚠️ MCQ NO CORRECT OPTION SET', ['question_id' => $question->id]);
            return 0;
        }

        // If we have a numeric id to compare, compare id
        if ($studentAnswerId !== null) {
            $isCorrect = $studentAnswerId === (int) $correctOption->id;

            \Log::info('🎯 MCQ EVALUATION - ID COMPARISON', [
                'question_id' => $question->id,
                'student_answer_id' => $studentAnswerId,
                'correct_option_id' => $correctOption->id,
                'is_correct' => $isCorrect
            ]);

            return $isCorrect ? (float) $points : 0;
        }

        // Fallback: compare text (case-insensitive, trimmed)
        $studentText = is_string($studentAnswer) ? trim(strtolower($studentAnswer)) : null;
        $correctText = trim(strtolower($correctOption->option_text));

        $isCorrect = ($studentText !== null && $studentText === $correctText);

        \Log::info('🎯 MCQ EVALUATION - TEXT FALLBACK', [
            'question_id' => $question->id,
            'student_text' => $studentText,
            'correct_text' => $correctText,
            'is_correct' => $isCorrect
        ]);

        return $isCorrect ? (float) $points : 0;
    }

    private function evaluateTrueFalseAnswer($question, $studentAnswer, $points)
    {
        \Log::info('✅ TRUE/FALSE EVALUATION (START)', [
            'question_id' => $question->id,
            'raw_student_answer' => $studentAnswer
        ]);

        // Normalize studentAnswer to boolean-like
        if ($studentAnswer === null || $studentAnswer === '') {
            return 0;
        }

        $studentNormalized = null;
        if (is_bool($studentAnswer)) {
            $studentNormalized = $studentAnswer ? 'true' : 'false';
        } elseif (is_string($studentAnswer)) {
            $val = strtolower(trim($studentAnswer));
            if (in_array($val, ['1', 'true', 'yes', 't', 'y'], true)) {
                $studentNormalized = 'true';
            } elseif (in_array($val, ['0', 'false', 'no', 'f', 'n'], true)) {
                $studentNormalized = 'false';
            }
        } elseif (is_numeric($studentAnswer)) {
            $studentNormalized = ((int)$studentAnswer) === 1 ? 'true' : 'false';
        }

        // Determine correct answer source
        $correct = trim(strtolower((string)$question->correct_answer)); // DB might store 'true'/'false' or '1'/'0'
        if ($correct === '1') $correct = 'true';
        if ($correct === '0') $correct = 'false';

        $isCorrect = ($studentNormalized !== null && $studentNormalized === $correct);

        \Log::info('✅ TRUE/FALSE EVALUATION (RESULT)', [
            'question_id' => $question->id,
            'student_normalized' => $studentNormalized,
            'correct_answer' => $correct,
            'is_correct' => $isCorrect
        ]);

        return $isCorrect ? (float) $points : 0;
    }


    /**
     * Initialize manual grades for subjective questions that need grading
     */
    private function initializeManualGrades(Exam $exam, $attempt, $answers)
    {
        $manualGrades = $attempt->manual_grades ?? [];

        if (is_string($manualGrades)) {
            $manualGrades = json_decode($manualGrades, true) ?? [];
        }
        if (!is_array($manualGrades)) {
            $manualGrades = [];
        }

        // For subjective questions with answers but no manual grade, set to null (needs grading)
        foreach ($exam->questions as $question) {
            if (in_array($question->type, ['short_answer', 'essay', 'fill_blank'])) {
                $studentAnswer = $answers[$question->id] ?? null;

                if (!empty($studentAnswer) && !isset($manualGrades[$question->id])) {
                    $manualGrades[$question->id] = null; // Mark as needs grading
                }
            }
        }

        return $manualGrades;
    }
    private function evaluateAnswer($question, $studentAnswer)
    {
        $maxPoints = $question->pivot->points ?? $question->points ?? 0;
        return $this->evaluateStudentAnswer($question, $studentAnswer, $maxPoints);
    }




    /**
     * Check if student can see detailed results based on academic logic
     */
    private function canStudentSeeDetailedResults(Exam $exam, ExamAttempt $attempt)
    {
        // If exam doesn't show results at all
        if (!$exam->show_results) {
            return false;
        }

        // If attempt is not graded yet
        if ($attempt->status !== 'graded') {
            return false;
        }

        // Practice quizzes and some formative assessments can show immediate results
        if (in_array($exam->type, ['practice', 'quiz'])) {
            return true;
        }

        // For high-stakes exams, only show results if explicitly released
        if (in_array($exam->type, ['midterm', 'end_of_term', 'final', 'mock'])) {
            return $exam->show_results && $exam->results_released_at !== null;
        }

        // Default: only show if teacher has released results
        return $exam->show_results;
    }

    /**
     * Check if student can see only score (without detailed questions)
     */
    private function canStudentSeeScoreOnly(Exam $exam, ExamAttempt $attempt)
    {
        // If attempt is graded, student can always see their score
        return $attempt->status === 'graded';
    }

    public function releaseResults(Exam $exam)
    {
        \Log::info('🔍 DEBUG: releaseResults called', [
            'exam_id' => $exam->id,
            'current_show_results' => $exam->show_results,
            'current_results_released_at' => $exam->results_released_at
        ]);

        // Verify teacher owns this exam
        if ($exam->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $updated = $exam->update([
            'show_results' => true,
            'results_released_at' => now()
        ]);

        \Log::info('🔍 DEBUG: Update result', [
            'update_success' => $updated,
            'new_show_results' => $exam->fresh()->show_results,
            'new_results_released_at' => $exam->fresh()->results_released_at
        ]);

        \Log::info('📊 RESULTS RELEASED TO STUDENTS:', [
            'teacher_id' => auth()->id(),
            'exam_id' => $exam->id,
            'exam_title' => $exam->title
        ]);

        return redirect()->back()->with('success', 'Exam results have been released to students successfully!');
    }

    /**
     * Withdraw results from students for a specific exam
     */
    public function withdrawResults(Exam $exam)
    {
        // Verify teacher owns this exam
        if ($exam->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $exam->update([
            'show_results' => false,
            'results_released_at' => null
        ]);

        \Log::info('📊 RESULTS WITHDRAWN FROM STUDENTS:', [
            'teacher_id' => auth()->id(),
            'exam_id' => $exam->id,
            'exam_title' => $exam->title
        ]);

        return redirect()->back()->with('success', 'Exam results have been withdrawn from students.');
    }

    /**
     * Show exam attempts with result release controls
     */
    public function attempts(Exam $exam)
    {
        // Verify teacher owns this exam
        if ($exam->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $attempts = $exam->attempts()
            ->with(['student'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => $attempts->total(),
            'submitted' => $exam->attempts()->where('status', 'submitted')->count(),
            'graded' => $exam->attempts()->where('status', 'graded')->count(),
            'in_progress' => $exam->attempts()->where('status', 'in_progress')->count(),
        ];

        return $this->renderView('modules.exams.attempts.index', [
            'exam' => $exam,
            'attempts' => $attempts,
            'stats' => $stats,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }
}
