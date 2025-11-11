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
    public function index()
    {
        $exams = Exam::where('teacher_id', auth()->id())
            ->with(['class', 'subject'])
            ->withCount(['attempts', 'attempts as submitted_count' => function($query) {
                $query->where('status', 'submitted');
            }])
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => Exam::where('teacher_id', auth()->id())->count(),
            'published' => Exam::where('teacher_id', auth()->id())->where('is_published', true)->count(),
            'draft' => Exam::where('teacher_id', auth()->id())->where('is_published', false)->count(),
            'attempts' => ExamAttempt::whereHas('exam', function($query) {
                $query->where('teacher_id', auth()->id());
            })->count(),
        ];

        return $this->renderView('modules.exams.index', [
            'exams' => $exams,
            'stats' => $stats,
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
                $this->attachQuestionsToExam($exam, $bankQuestions, $newQuestions);

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
     * Attach questions to exam (both bank and new questions)
     */
    private function attachQuestionsToExam(Exam $exam, array $bankQuestionIds, array $newQuestionsData)
    {
        $questionOrder = 0;
        $totalPoints = 0;

        // 1. Attach bank questions
        foreach ($bankQuestionIds as $questionId) {
            $question = Question::find($questionId);
            if ($question) {
                $points = request()->input("bank_question_points.{$questionId}", $question->points);

                $exam->questions()->attach($question->id, [
                    'order' => $questionOrder++,
                    'points' => $points
                ]);

                $totalPoints += $points;
                Log::info('Bank question attached to exam:', [
                    'exam_id' => $exam->id,
                    'question_id' => $question->id,
                    'points' => $points
                ]);
            }
        }

        // 2. Create and attach new questions
        if (!empty($newQuestionsData)) {
            $validatedNewQuestions = $this->validateAndSaveNewQuestions($exam, $newQuestionsData);

            foreach ($validatedNewQuestions as $qData) {
                $totalPoints += $qData['points'];
                $questionOrder++;
            }
        }
    }

    /**
     * Validate and save new questions
     */
    private function validateAndSaveNewQuestions(Exam $exam, array $questionsData)
    {
        Log::info('New questions data received:', ['questions' => $questionsData]);

        // Define validation rules for each question item
        $rules = [];

        foreach ($questionsData as $index => $question) {
            $questionType = $question['type'] ?? null;

            // Base rules for all questions
            $rules["{$index}.type"] = ['required', Rule::in(['mcq', 'true_false', 'short_answer', 'essay', 'fill_blank'])];
            $rules["{$index}.points"] = ['required', 'numeric', 'min:0.5', 'max:100'];
            $rules["{$index}.question_text"] = ['required', 'string'];
            $rules["{$index}.save_to_bank"] = ['nullable', 'boolean'];

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
            Log::error('New question validation failed:', ['errors' => $validator->errors()->toArray()]);
            throw new ValidationException($validator);
        }

        $validatedQuestions = $validator->validated();
        $savedQuestions = [];

        // Save questions
        $questionOrder = $exam->questions()->count();
        foreach ($validatedQuestions as $qData) {
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

            // Create the base Question record
            $questionData = [
                'subject_id' => $exam->subject_id,
                'created_by' => Auth::id(),
                'type' => $qData['type'],
                'points' => $qData['points'],
                'question_text' => $qData['question_text'],
                'details' => !empty($details) ? $details : null,
                'correct_answer' => $correctAnswer,
                'is_active' => true,
            ];

            // Save to bank if requested
            if ($qData['save_to_bank'] ?? false) {
                $questionData['is_bank_question'] = true;
            }

            $question = Question::create($questionData);

            // Attach to exam
            $exam->questions()->attach($question->id, [
                'order' => $questionOrder++,
                'points' => $qData['points']
            ]);

            // Handle Multiple Choice Options
            if ($qData['type'] === 'mcq' && !empty($qData['options'])) {
                foreach ($qData['options'] as $index => $optionText) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optionText,
                        'is_correct' => ($index == $qData['correct_answer']),
                        'order' => $index,
                    ]);
                }
            }

            $savedQuestions[] = $qData;
        }

        return $savedQuestions;
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        // Verify teacher owns this exam
        if ($exam->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Use transaction to ensure data consistency
        return DB::transaction(function () use ($request, $exam) {
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
                    'randomize_questions' => $request->has('randomize_questions'),
                    'require_fullscreen' => $request->has('require_fullscreen'),
                    'show_results' => $request->has('show_results'),
                    'is_published' => $request->input('is_draft') == '1' ? false : $request->has('is_published'),
                ]);

                // 5. Update Exam
                $exam->update(Arr::except($data, ['questions', 'selected_bank_questions', 'is_draft']));

                // 6. Clean up old questions and re-attach
                $questionIds = $exam->questions()->pluck('question_id');
                $exam->questions()->detach();

                // Delete only new questions (not bank questions)
                Question::whereIn('id', $questionIds)
                    ->where('is_bank_question', false)
                    ->delete();

                QuestionOption::whereIn('question_id', $questionIds)->delete();

                // 7. Re-attach questions
                $this->attachQuestionsToExam($exam, $bankQuestions, $newQuestions);

                $message = $exam->is_published ? 'Exam updated and published successfully!' : 'Exam updated and saved as draft successfully!';

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
     * Display exam attempts
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

    /**
     * Show individual attempt details
     */
    public function showAttempt(Exam $exam, ExamAttempt $attempt)
    {
        // Verify teacher owns this exam
        if ($exam->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $attempt->load(['student', 'exam']);

        return $this->renderView('modules.exams.attempts.show', [
            'exam' => $exam,
            'attempt' => $attempt,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }
}
