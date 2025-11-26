<?php

namespace App\Http\Controllers\Modules\Quizzes;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Quiz;
use App\Models\Assessment\Question;
use App\Models\Assessment\QuizAttempt;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminQuizzesController extends Controller
{
    /**
     * Check if user is admin
     */
    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Admin privileges required.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkAdmin();

        $query = Quiz::with(['teacher', 'class', 'subject', 'attempts'])
            ->withCount(['attempts', 'attempts as submitted_count' => function($query) {
                $query->where('status', 'submitted');
            }]);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('instructions', 'LIKE', "%{$search}%")
                    ->orWhereHas('teacher', function($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })
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
            } elseif ($request->status === 'active') {
                $now = now();
                $query->where('is_published', true)
                    ->where('start_time', '<=', $now)
                    ->where('end_time', '>=', $now);
            } elseif ($request->status === 'upcoming') {
                $query->where('is_published', true)
                    ->where('start_time', '>', now());
            } elseif ($request->status === 'completed') {
                $query->where('end_time', '<', now());
            }
        }

        // Filter by type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Filter by class
        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }

        $quizzes = $query->latest()->paginate(10);

        $stats = [
            'total' => Quiz::count(),
            'published' => Quiz::where('is_published', true)->count(),
            'draft' => Quiz::where('is_published', false)->count(),
            'active' => Quiz::where('is_published', true)
                ->where('start_time', '<=', now())
                ->where('end_time', '>=', now())
                ->count(),
            'attempts' => QuizAttempt::count(),
        ];

        $classes = SchoolClass::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();

        return $this->renderView('modules.quizzes.index', [
            'quizzes' => $quizzes,
            'stats' => $stats,
            'classes' => $classes,
            'subjects' => $subjects,
            'filters' => $request->only(['search', 'status', 'type', 'class_id']),
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
        $this->checkAdmin();

        $classes = SchoolClass::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();

        return $this->renderView('modules.quizzes.create', [
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
        $this->checkAdmin();

        try {
            $filters = $request->validate([
                'subject_id' => 'nullable|exists:subjects,id',
                'type' => 'nullable|in:mcq,true_false,short_answer,essay,fill_blank',
                'difficulty' => 'nullable|in:easy,medium,hard',
                'search' => 'nullable|string|max:255'
            ]);

            $query = Question::with(['subject', 'options'])
                ->where('is_active', true);

            // Apply filters
            if (!empty($filters['subject_id'])) {
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
        $this->checkAdmin();

        return DB::transaction(function () use ($request) {
            try {
                // 1. Initial Validation
                $validated = $request->validate([
                    'title' => 'required|string|max:255',
                    'class_id' => 'required|exists:school_classes,id',
                    'subject_id' => 'required|exists:subjects,id',
                    'instructions' => 'nullable|string',
                    'type' => ['required', Rule::in(['practice', 'chapter_test', 'quick_check'])],
                    'duration' => 'required|integer|min:1|max:180', // max 3 hours
                    'total_marks' => 'required|integer|min:1|max:500',
                    'start_time' => 'required|date',
                    'end_time' => ['required', 'date', 'after:start_time'],
                    'selected_bank_questions' => 'nullable|string',
                    'questions' => 'nullable|array',
                ]);

                // 2. Validate that we have at least one question
                $bankQuestions = $this->parseBankQuestions($validated['selected_bank_questions'] ?? '');
                $newQuestions = $validated['questions'] ?? [];

                if (empty($bankQuestions) && empty($newQuestions)) {
                    throw ValidationException::withMessages([
                        'questions' => 'At least one question is required.'
                    ]);
                }

                // 3. Prepare Quiz Data
                $data = array_merge($validated, [
                    'teacher_id' => Auth::id(),
                    'randomize_questions' => $request->has('randomize_questions'),
                    'show_answers' => $request->has('show_answers'),
                    'is_published' => $request->has('is_published'),
                ]);

                // 4. Create Quiz
                $quiz = Quiz::create(Arr::except($data, ['questions', 'selected_bank_questions']));

                // 5. Attach questions from bank and create new questions
                $this->attachQuestionsToQuiz($quiz, $bankQuestions, $newQuestions, $request);

                $message = $quiz->is_published ? 'Quiz created and published successfully!' : 'Quiz saved as draft successfully!';

                return redirect()->route('admin.quizzes.index')
                    ->with('success', $message);

            } catch (ValidationException $e) {
                Log::error('Quiz creation validation failed:', ['errors' => $e->errors()]);
                throw $e;
            } catch (\Exception $e) {
                Log::error('Quiz creation failed:', ['error' => $e->getMessage()]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to create quiz. Please try again.');
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
        $questionIds = array_filter($questionIds);

        $validQuestions = Question::whereIn('id', $questionIds)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        return $validQuestions;
    }

    /**
     * Attach questions to quiz
     */
    private function attachQuestionsToQuiz(Quiz $quiz, array $bankQuestionIds, array $newQuestionsData, Request $request)
    {
        $questionOrder = 0;
        $totalPoints = 0;

        // 1. Attach bank questions
        foreach ($bankQuestionIds as $questionId) {
            $question = Question::find($questionId);
            if ($question) {
                $points = $request->input("bank_question_points.{$questionId}", $question->points);

                $quiz->questions()->attach($question->id, [
                    'order' => $questionOrder++,
                    'points' => $points
                ]);

                $totalPoints += $points;
            }
        }

        // 2. Create and attach new questions
        if (!empty($newQuestionsData)) {
            $savedQuestionIds = $this->validateAndSaveNewQuestions($quiz, $newQuestionsData);

            // Calculate points for new questions
            foreach ($newQuestionsData as $questionData) {
                $totalPoints += $questionData['points'] ?? 0;
            }
        }

        // Update total marks if different
        if ($totalPoints > 0 && $totalPoints != $quiz->total_marks) {
            $quiz->update(['total_marks' => $totalPoints]);
        }
    }

    /**
     * Validate and save new questions
     */
    private function validateAndSaveNewQuestions(Quiz $quiz, array $questionsData)
    {
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
                    $rules["{$index}.blank_answers"] = ['required', 'string'];
                    $attributes["{$index}.blank_answers"] = "question {$index} blank answers";
                    break;
            }
        }

        $validator = Validator::make($questionsData, $rules);
        $validator->setAttributeNames($attributes);

        if ($validator->fails()) {
            Log::error('Question validation failed:', ['errors' => $validator->errors()->toArray()]);
            throw new ValidationException($validator);
        }

        // Rest of the method remains the same...
        $validatedQuestions = $validator->validated();
        $savedQuestions = [];
        $questionOrder = $quiz->questions()->count();

        foreach ($validatedQuestions as $index => $qData) {
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
                'subject_id' => $quiz->subject_id,
                'created_by' => Auth::id(),
                'type' => $qData['type'],
                'points' => $qData['points'],
                'question_text' => $qData['question_text'],
                'details' => !empty($details) ? $details : null,
                'correct_answer' => $correctAnswer,
                'is_active' => true,
                'is_bank_question' => $saveToBank,
            ];

            $question = Question::create($questionData);

            $quiz->questions()->attach($question->id, [
                'order' => $questionOrder++,
                'points' => $qData['points']
            ]);

            if ($qData['type'] === 'mcq' && !empty($qData['options'])) {
                foreach ($qData['options'] as $optIndex => $optionText) {
                    \App\Models\Assessment\QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optionText,
                        'is_correct' => ($optIndex == $qData['correct_answer']),
                        'order' => $optIndex,
                    ]);
                }
            }

            $savedQuestions[] = $question->id;
        }

        return $savedQuestions;
    }

    /**
     * Display the specified resource.
     */
    public function show(Quiz $quiz)
    {
        $this->checkAdmin();

        $quiz->load(['teacher', 'class', 'subject', 'attempts.student', 'questions.options']);

        return $this->renderView('modules.quizzes.show', [
            'quiz' => $quiz,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quiz $quiz)
    {
        $this->checkAdmin();

        $classes = SchoolClass::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();

        $quiz->load('questions.options');

        return $this->renderView('modules.quizzes.edit', [
            'quiz' => $quiz,
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
    public function update(Request $request, Quiz $quiz)
    {
        $this->checkAdmin();

        return DB::transaction(function () use ($request, $quiz) {
            try {
                $validated = $request->validate([
                    'title' => 'required|string|max:255',
                    'class_id' => 'required|exists:school_classes,id',
                    'subject_id' => 'required|exists:subjects,id',
                    'instructions' => 'nullable|string',
                    'type' => ['required', Rule::in(['practice', 'chapter_test', 'quick_check'])],
                    'duration' => 'required|integer|min:1|max:180',
                    'total_marks' => 'required|integer|min:1|max:500',
                    'start_time' => 'required|date',
                    'end_time' => ['required', 'date', 'after:start_time'],
                    'selected_bank_questions' => 'nullable|string',
                    'questions' => 'nullable|array',
                ]);

                $data = array_merge($validated, [
                    'randomize_questions' => $request->has('randomize_questions'),
                    'show_answers' => $request->has('show_answers'),
                    'is_published' => $request->has('is_published'),
                ]);

                $quiz->update(Arr::except($data, ['questions', 'selected_bank_questions']));

                $message = $quiz->is_published ? 'Quiz updated and published successfully!' : 'Quiz updated successfully!';

                return redirect()->route('admin.quizzes.index')
                    ->with('success', $message);

            } catch (ValidationException $e) {
                throw $e;
            } catch (\Exception $e) {
                Log::error('Quiz update failed:', ['error' => $e->getMessage()]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to update quiz. Please try again.');
            }
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quiz $quiz)
    {
        $this->checkAdmin();

        try {
            $quiz->delete();

            return redirect()->route('admin.quizzes.index')
                ->with('success', 'Quiz deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Quiz deletion failed:', ['error' => $e->getMessage()]);
            return redirect()->route('admin.quizzes.index')
                ->with('error', 'Failed to delete quiz. Please try again.');
        }
    }

    /**
     * Toggle publish status of quiz.
     */
    public function togglePublish(Quiz $quiz)
    {
        $this->checkAdmin();

        $quiz->update([
            'is_published' => !$quiz->is_published
        ]);

        $status = $quiz->is_published ? 'published' : 'unpublished';

        return redirect()->route('admin.quizzes.index')
            ->with('success', "Quiz {$status} successfully!");
    }

    /**
     * Display quiz attempts
     */
    public function attempts(Quiz $quiz)
    {
        $this->checkAdmin();

        $attempts = $quiz->attempts()
            ->with(['student'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => $attempts->total(),
            'submitted' => $quiz->attempts()->where('status', 'submitted')->count(),
            'graded' => $quiz->attempts()->where('status', 'graded')->count(),
            'in_progress' => $quiz->attempts()->where('status', 'in_progress')->count(),
        ];

        return $this->renderView('modules.quizzes.attempts.index', [
            'quiz' => $quiz,
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
    /**
     * Show individual attempt details (Admin view)
     */
    /**
     * Show individual attempt details (Admin view)
     */
    public function showAttempt(Quiz $quiz, QuizAttempt $quizAttempt)
    {
        $this->checkAdmin();

        // Use the quizAttempt parameter instead of attempt
        $attempt = $quizAttempt;

        // Eager load the quiz with its relationships
        $attempt->load([
            'quiz' => function($query) {
                $query->with(['subject', 'class', 'teacher', 'questions.options']);
            },
            'student'
        ]);

        // Check if quiz exists
        if (!$attempt->quiz) {
            abort(404, 'Quiz not found for this attempt.');
        }

        $quiz = $attempt->quiz;
        $student = $attempt->student;

        // Pre-calculate stats
        $correctAnswers = 0;
        $totalQuestions = $quiz->questions->count();
        $answers = $attempt->answers ?? [];
        $questionResults = [];

        if ($attempt->status === 'submitted' || $attempt->status === 'graded') {
            foreach ($quiz->questions as $question) {
                $studentAnswer = $answers[$question->id] ?? null;
                $isCorrect = $this->isAnswerCorrect($question, $studentAnswer);

                $questionResults[] = [
                    'question' => $question,
                    'studentAnswer' => $studentAnswer,
                    'isCorrect' => $isCorrect,
                    'points' => $question->pivot->points ?? $question->points,
                ];

                if ($isCorrect) {
                    $correctAnswers++;
                }
            }
        }

        return $this->renderView('modules.quizzes.attempts.show', [
            'attempt' => $attempt,
            'quiz' => $quiz,
            'student' => $student,
            'questionResults' => $questionResults,
            'correctAnswers' => $correctAnswers,
            'totalQuestions' => $totalQuestions,
            'answers' => $answers,
            'isAdminView' => true,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }
    /**
     * Check if answer is correct
     */
    private function isAnswerCorrect(Question $question, $studentAnswer)
    {
        if (empty($studentAnswer)) {
            return false;
        }

        switch ($question->type) {
            case 'mcq':
                return $this->evaluateMcqAnswer($question, $studentAnswer);
            case 'true_false':
                return $studentAnswer === $question->correct_answer;
            case 'short_answer':
                $expected = strtolower(trim($question->correct_answer));
                $given = strtolower(trim($studentAnswer));
                return $expected === $given;
            default:
                return false; // Essay and fill_blank need manual grading
        }
    }

    /**
     * Evaluate MCQ answer
     */
    private function evaluateMcqAnswer($question, $studentAnswer)
    {
        // Use options table to find correct answer
        if ($question->relationLoaded('options') && $question->options->isNotEmpty()) {
            $correctOption = $question->options->where('is_correct', true)->first();
            if ($correctOption) {
                $studentAnswerId = (int) $studentAnswer;
                $correctOptionId = (int) $correctOption->id;
                return $studentAnswerId === $correctOptionId;
            }
        }

        // Fallback: use questions.correct_answer field
        if (!empty($question->correct_answer)) {
            $studentAnswerId = (int) $studentAnswer;
            $correctAnswerId = (int) $question->correct_answer;
            return $studentAnswerId === $correctAnswerId;
        }

        return false;
    }
}
