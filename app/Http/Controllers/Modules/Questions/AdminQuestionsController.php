<?php

namespace App\Http\Controllers\Modules\Questions;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Question;
use App\Models\Assessment\QuestionOption;
use App\Models\Academic\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminQuestionsController extends Controller
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
     * Display a listing of the questions.
     */
    public function index(Request $request)
    {
        $this->checkAdmin();

        $query = Question::with(['subject', 'creator', 'options'])
            ->latest();

        // Search filter
        if ($request->has('search') && $request->search) {
            $query->where('question_text', 'like', '%' . $request->search . '%');
        }

        // Subject filter
        if ($request->has('subject_id') && $request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }

        // Type filter
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Difficulty filter
        if ($request->has('difficulty') && $request->difficulty) {
            $query->where('difficulty', $request->difficulty);
        }

        $questions = $query->paginate(20);
        $subjects = Subject::all();
        $questionTypes = ['mcq', 'true_false', 'short_answer', 'essay', 'fill_blank'];
        $difficulties = ['easy', 'medium', 'hard'];

        $stats = [
            'total' => Question::count(),
            'active' => Question::where('is_active', true)->count(),
            'mcq' => Question::where('type', 'mcq')->count(),
            'essay' => Question::where('type', 'essay')->count(),
        ];

        $data = [
            'questions' => $questions,
            'subjects' => $subjects,
            'questionTypes' => $questionTypes,
            'difficulties' => $difficulties,
            'stats' => $stats,
            'filters' => $request->only(['search', 'subject_id', 'type', 'difficulty']),
        ];

        // AJAX request → return only #main-content
        if ($request->ajax()) {
            return view('modules.questions.index', $data)
                ->renderSections()['content'];
        }

        // Normal full page load
        return view('modules.questions.index', $data)
            ->with([
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true,
            ]);
    }

    public function create(Request $request)
    {
        $this->checkAdmin();

        $subjects = Subject::all();
        $questionTypes = ['mcq', 'true_false', 'short_answer', 'essay', 'fill_blank'];
        $difficulties = ['easy', 'medium', 'hard'];

        $data = [
            'subjects' => $subjects,
            'questionTypes' => $questionTypes,
            'difficulties' => $difficulties,
        ];

        // AJAX request → return only #main-content
        if ($request->ajax()) {
            return view('modules.questions.create', $data)
                ->renderSections()['content'];
        }

        // Normal full page load
        return view('modules.questions.create', $data)
            ->with([
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true,
            ]);
    }

    /**
     * Store a newly created question in storage.
     */
    public function store(Request $request)
    {
        $this->checkAdmin();

        return DB::transaction(function () use ($request) {
            try {
                $validated = $request->validate([
                    'subject_id' => 'required|exists:subjects,id',
                    'type' => ['required', Rule::in(['mcq', 'true_false', 'short_answer', 'essay', 'fill_blank'])],
                    'question_text' => 'required|string|min:10',
                    'explanation' => 'nullable|string',
                    'difficulty' => ['required', Rule::in(['easy', 'medium', 'hard'])],
                    'points' => 'required|numeric|min:0.5|max:100',
                    'is_active' => 'boolean',

                    // Type-specific validation
                    'correct_answer' => 'nullable|string',
                    'options' => 'nullable|array',
                    'options.*' => 'required|string|max:500',
                    'is_correct' => 'nullable|array',
                    'is_correct.*' => 'nullable|boolean',
                    'expected_answer' => 'nullable|string',
                    'grading_rubric' => 'nullable|string',
                    'blank_question' => 'nullable|string',
                    'blank_answers' => 'nullable|string',
                ]);

                $details = [];
                $correctAnswer = null;

                // Prepare question data based on type
                switch ($validated['type']) {
                    case 'mcq':
                        if (empty($validated['options']) || count(array_filter($validated['options'])) < 2) {
                            $error = 'At least 2 options are required for MCQ questions.';
                            if ($request->ajax()) {
                                return response()->json(['error' => $error], 422);
                            }
                            return redirect()->back()->withInput()->with('error', $error);
                        }

                        // Find the correct option index
                        $correctAnswer = 0;
                        if (!empty($validated['is_correct'])) {
                            foreach ($validated['is_correct'] as $index => $isCorrect) {
                                if ($isCorrect == '1') {
                                    $correctAnswer = $index;
                                    break;
                                }
                            }
                        }

                        $details['options'] = $validated['options'] ?? [];
                        break;

                    case 'true_false':
                        $correctAnswer = $validated['correct_answer'] ?? 'true';
                        break;

                    case 'short_answer':
                        $details['expected_answer'] = $validated['expected_answer'] ?? null;
                        $correctAnswer = $details['expected_answer'];
                        break;

                    case 'essay':
                        $details['grading_rubric'] = $validated['grading_rubric'] ?? null;
                        break;

                    case 'fill_blank':
                        if (empty($validated['blank_question']) || empty($validated['blank_answers'])) {
                            $error = 'Both blank question and answers are required for fill-in-the-blank questions.';
                            if ($request->ajax()) {
                                return response()->json(['error' => $error], 422);
                            }
                            return redirect()->back()->withInput()->with('error', $error);
                        }
                        $details['blank_question'] = $validated['blank_question'];
                        $blankAnswers = array_map('trim', explode(',', $validated['blank_answers'] ?? ''));
                        $details['blank_answers'] = $blankAnswers;
                        $correctAnswer = json_encode($blankAnswers);
                        break;
                }

                // Create the question
                $question = Question::create([
                    'subject_id' => $validated['subject_id'],
                    'created_by' => Auth::id(),
                    'type' => $validated['type'],
                    'question_text' => $validated['question_text'],
                    'explanation' => $validated['explanation'] ?? null,
                    'difficulty' => $validated['difficulty'],
                    'points' => $validated['points'],
                    'is_active' => $validated['is_active'] ?? true,
                    'details' => !empty($details) ? $details : null,
                    'correct_answer' => $correctAnswer,
                ]);

                // Create MCQ options if needed
                if ($validated['type'] === 'mcq' && !empty($validated['options'])) {
                    foreach ($validated['options'] as $index => $optionText) {
                        if (!empty(trim($optionText))) {
                            $isCorrect = isset($validated['is_correct'][$index]) && $validated['is_correct'][$index] == '1';

                            QuestionOption::create([
                                'question_id' => $question->id,
                                'option_text' => trim($optionText),
                                'is_correct' => $isCorrect,
                                'order' => $index,
                            ]);
                        }
                    }
                }

                // AJAX response
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Question created successfully!',
                        'redirect' => route('admin.questions.index')
                    ]);
                }

                // Normal response
                return redirect()->route('admin.questions.index')
                    ->with('success', 'Question created successfully!');

            } catch (\Exception $e) {
                Log::error('Question creation failed:', ['error' => $e->getMessage()]);

                // AJAX response
                if ($request->ajax()) {
                    return response()->json([
                        'error' => 'Failed to create question. Please try again.'
                    ], 500);
                }

                // Normal response
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to create question. Please try again.');
            }
        });
    }

    /**
     * Display the specified question.
     */
    public function show(Request $request, Question $question)
    {
        $this->checkAdmin();

        $question->load(['subject', 'creator', 'options', 'exams']);

        $data = [
            'question' => $question,
        ];

        // AJAX request → return only #main-content
        if ($request->ajax()) {
            return view('modules.questions.show', $data)
                ->renderSections()['content'];
        }

        // Normal full page load
        return view('modules.questions.show', $data)
            ->with([
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true,
            ]);
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(Request $request, Question $question)
    {
        $this->checkAdmin();

        $question->load('options');
        $subjects = Subject::all();
        $questionTypes = ['mcq', 'true_false', 'short_answer', 'essay', 'fill_blank'];
        $difficulties = ['easy', 'medium', 'hard'];

        $data = [
            'question' => $question,
            'subjects' => $subjects,
            'questionTypes' => $questionTypes,
            'difficulties' => $difficulties,
        ];

        // AJAX request → return only #main-content
        if ($request->ajax()) {
            return view('modules.questions.edit', $data)
                ->renderSections()['content'];
        }

        // Normal full page load
        return view('modules.questions.edit', $data)
            ->with([
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true,
            ]);
    }

    /**
     * Update the specified question in storage.
     */
    public function update(Request $request, Question $question)
    {
        $this->checkAdmin();

        return DB::transaction(function () use ($request, $question) {
            try {
                $validated = $request->validate([
                    'subject_id' => 'required|exists:subjects,id',
                    'type' => ['required', Rule::in(['mcq', 'true_false', 'short_answer', 'essay', 'fill_blank'])],
                    'question_text' => 'required|string|min:10',
                    'explanation' => 'nullable|string',
                    'difficulty' => ['required', Rule::in(['easy', 'medium', 'hard'])],
                    'points' => 'required|numeric|min:0.5|max:100',
                    'is_active' => 'boolean',

                    // Type-specific validation
                    'correct_answer' => 'nullable|string',
                    'options' => 'nullable|array',
                    'options.*' => 'required|string|max:500',
                    'is_correct' => 'nullable|array',
                    'is_correct.*' => 'nullable|boolean',
                    'expected_answer' => 'nullable|string',
                    'grading_rubric' => 'nullable|string',
                    'blank_question' => 'nullable|string',
                    'blank_answers' => 'nullable|string',
                ]);

                $details = [];
                $correctAnswer = null;

                // Prepare question data based on type
                switch ($validated['type']) {
                    case 'mcq':
                        if (empty($validated['options']) || count(array_filter($validated['options'])) < 2) {
                            $error = 'At least 2 options are required for MCQ questions.';
                            if ($request->ajax()) {
                                return response()->json(['error' => $error], 422);
                            }
                            return redirect()->back()->withInput()->with('error', $error);
                        }

                        // Find the correct option index
                        $correctAnswer = 0;
                        if (!empty($validated['is_correct'])) {
                            foreach ($validated['is_correct'] as $index => $isCorrect) {
                                if ($isCorrect == '1') {
                                    $correctAnswer = $index;
                                    break;
                                }
                            }
                        }

                        $details['options'] = $validated['options'] ?? [];
                        break;

                    case 'true_false':
                        $correctAnswer = $validated['correct_answer'] ?? 'true';
                        break;

                    case 'short_answer':
                        $details['expected_answer'] = $validated['expected_answer'] ?? null;
                        $correctAnswer = $details['expected_answer'];
                        break;

                    case 'essay':
                        $details['grading_rubric'] = $validated['grading_rubric'] ?? null;
                        break;

                    case 'fill_blank':
                        if (empty($validated['blank_question']) || empty($validated['blank_answers'])) {
                            $error = 'Both blank question and answers are required for fill-in-the-blank questions.';
                            if ($request->ajax()) {
                                return response()->json(['error' => $error], 422);
                            }
                            return redirect()->back()->withInput()->with('error', $error);
                        }
                        $details['blank_question'] = $validated['blank_question'];
                        $blankAnswers = array_map('trim', explode(',', $validated['blank_answers'] ?? ''));
                        $details['blank_answers'] = $blankAnswers;
                        $correctAnswer = json_encode($blankAnswers);
                        break;
                }

                // Update the question
                $question->update([
                    'subject_id' => $validated['subject_id'],
                    'type' => $validated['type'],
                    'question_text' => $validated['question_text'],
                    'explanation' => $validated['explanation'] ?? null,
                    'difficulty' => $validated['difficulty'],
                    'points' => $validated['points'],
                    'is_active' => $validated['is_active'] ?? true,
                    'details' => !empty($details) ? $details : null,
                    'correct_answer' => $correctAnswer,
                ]);

                // Delete existing options and recreate if MCQ
                $question->options()->delete();
                if ($validated['type'] === 'mcq' && !empty($validated['options'])) {
                    foreach ($validated['options'] as $index => $optionText) {
                        if (!empty(trim($optionText))) {
                            $isCorrect = isset($validated['is_correct'][$index]) && $validated['is_correct'][$index] == '1';

                            QuestionOption::create([
                                'question_id' => $question->id,
                                'option_text' => trim($optionText),
                                'is_correct' => $isCorrect,
                                'order' => $index,
                            ]);
                        }
                    }
                }

                // AJAX response
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Question updated successfully!',
                        'redirect' => route('admin.questions.index')
                    ]);
                }

                // Normal response
                return redirect()->route('admin.questions.index')
                    ->with('success', 'Question updated successfully!');

            } catch (\Exception $e) {
                Log::error('Question update failed:', ['error' => $e->getMessage()]);

                // AJAX response
                if ($request->ajax()) {
                    return response()->json([
                        'error' => 'Failed to update question. Please try again.'
                    ], 500);
                }

                // Normal response
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to update question. Please try again.');
            }
        });
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroy(Request $request, Question $question)
    {
        $this->checkAdmin();

        try {
            // Check if question is used in any exams
            if ($question->exams()->count() > 0) {
                $error = 'Cannot delete question. It is being used in one or more exams.';

                // AJAX response
                if ($request->ajax()) {
                    return response()->json([
                        'error' => $error
                    ], 422);
                }

                // Normal response
                return redirect()->route('admin.questions.index')
                    ->with('error', $error);
            }

            $question->options()->delete();
            $question->delete();

            // AJAX response
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Question deleted successfully!',
                    'redirect' => route('admin.questions.index')
                ]);
            }

            // Normal response
            return redirect()->route('admin.questions.index')
                ->with('success', 'Question deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Question deletion failed:', ['error' => $e->getMessage()]);

            // AJAX response
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to delete question. Please try again.'
                ], 500);
            }

            // Normal response
            return redirect()->route('admin.questions.index')
                ->with('error', 'Failed to delete question. Please try again.');
        }
    }

    /**
     * Toggle question active status
     */
    public function toggleStatus(Request $request, Question $question)
    {
        $this->checkAdmin();

        try {
            $question->update([
                'is_active' => !$question->is_active
            ]);

            $status = $question->is_active ? 'activated' : 'deactivated';
            $message = "Question {$status} successfully!";

            // AJAX response
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'is_active' => $question->is_active
                ]);
            }

            // Normal response
            return redirect()->back()
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Question status toggle failed:', ['error' => $e->getMessage()]);

            // AJAX response
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to update question status.'
                ], 500);
            }

            // Normal response
            return redirect()->back()
                ->with('error', 'Failed to update question status.');
        }
    }
}
