<?php

namespace App\Http\Controllers\Modules\Questions;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Question;
use App\Models\Assessment\QuestionOption;
use App\Models\Academic\Subject;
use App\Models\Academic\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherQuestionsController extends Controller
{
    /**
     * Check if user is teacher
     */
    private function checkTeacher()
    {
        if (!Auth::check() || !Auth::user()->isTeacher()) {
            abort(403, 'Teacher privileges required.');
        }
    }

    /**
     * Get teacher's accessible subjects - USING THE SAME LOGIC AS TeacherSubjectsController
     */
    private function getTeacherSubjects()
    {
        $user = Auth::user();

        Log::info('Fetching teacher subjects - USING SIMPLIFIED QUERY', [
            'teacher_id' => $user->id
        ]);

        // Use the EXACT same query as TeacherSubjectsController
        $teacherAssignments = TeacherAssignment::where('teacher_id', $user->id)
            ->with(['subject', 'class', 'stream'])
            ->get();

        Log::info('Teacher assignments found', [
            'count' => $teacherAssignments->count(),
            'assignments' => $teacherAssignments->pluck('id'),
            'subjects' => $teacherAssignments->pluck('subject_id')
        ]);

        $teacherSubjects = $teacherAssignments
            ->pluck('subject')
            ->filter() // Remove null subjects
            ->unique('id') // Remove duplicates
            ->values();

        Log::info('Final teacher subjects', [
            'subjects_count' => $teacherSubjects->count(),
            'subject_names' => $teacherSubjects->pluck('name')
        ]);

        return $teacherSubjects;
    }

    /**
     * Get teacher's subject IDs for validation
     */
    private function getTeacherSubjectIds()
    {
        return $this->getTeacherSubjects()->pluck('id')->toArray();
    }

    /**
     * Display a listing of the teacher's questions.
     */
    public function index(Request $request)
    {
        $this->checkTeacher();

        $teacherId = Auth::id();
        $teacherSubjectIds = $this->getTeacherSubjectIds();

        Log::info('Teacher questions index', [
            'teacher_id' => $teacherId,
            'teacher_subject_ids' => $teacherSubjectIds
        ]);

        $query = Question::with(['subject', 'options'])
            ->where('created_by', $teacherId)
            ->latest();

        // Search filter
        if ($request->has('search') && $request->search) {
            $query->where('question_text', 'like', '%' . $request->search . '%');
        }

        // Subject filter - only teacher's subjects
        if ($request->has('subject_id') && $request->subject_id) {
            if (in_array($request->subject_id, $teacherSubjectIds)) {
                $query->where('subject_id', $request->subject_id);
            } else {
                // If trying to filter by unauthorized subject, show no results
                $query->where('subject_id', 0);
            }
        } else {
            // If no subject filter, only show questions from teacher's subjects
            if (!empty($teacherSubjectIds)) {
                $query->whereIn('subject_id', $teacherSubjectIds);
            } else {
                // If teacher has no subjects, show no questions
                $query->where('subject_id', 0);
            }
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
        $subjects = $this->getTeacherSubjects();
        $questionTypes = ['mcq', 'true_false', 'short_answer', 'essay', 'fill_blank'];
        $difficulties = ['easy', 'medium', 'hard'];

        // Update stats to only count questions from teacher's subjects
        $statsQuery = Question::where('created_by', $teacherId);
        if (!empty($teacherSubjectIds)) {
            $statsQuery->whereIn('subject_id', $teacherSubjectIds);
        } else {
            $statsQuery->where('subject_id', 0);
        }

        $stats = [
            'total' => $statsQuery->count(),
            'active' => $statsQuery->clone()->where('is_active', true)->count(),
            'mcq' => $statsQuery->clone()->where('type', 'mcq')->count(),
            'essay' => $statsQuery->clone()->where('type', 'essay')->count(),
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
            return view('modules.questions.teacher.partials.table', $data)
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
        $this->checkTeacher();

        $subjects = $this->getTeacherSubjects();
        $teacherSubjectIds = $this->getTeacherSubjectIds();

        Log::info('Create method - teacher subjects', [
            'subjects_count' => $subjects->count(),
            'teacher_subject_ids' => $teacherSubjectIds
        ]);

        // REMOVE THE REDIRECT - just show the form even if no subjects
        // if ($subjects->isEmpty()) {
        //     if ($request->ajax()) {
        //         return response()->json([
        //             'error' => 'You are not assigned to teach any subjects. Please contact administrator.'
        //         ], 403);
        //     }
        //     return redirect()->route('teacher.questions.index')
        //         ->with('error', 'You are not assigned to teach any subjects. Please contact administrator.');
        // }

        $questionTypes = ['mcq', 'true_false', 'short_answer', 'essay', 'fill_blank'];
        $difficulties = ['easy', 'medium', 'hard'];

        $data = [
            'subjects' => $subjects,
            'teacherSubjectIds' => $teacherSubjectIds,
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
        $this->checkTeacher();

        return DB::transaction(function () use ($request) {
            try {
                $teacherSubjectIds = $this->getTeacherSubjectIds();

                Log::info('Store method - teacher subject IDs', [
                    'teacher_subject_ids' => $teacherSubjectIds
                ]);

                // If teacher has subjects, validate subject_id is in their list
                // If no subjects, allow any subject (or handle as needed)
                $subjectValidation = !empty($teacherSubjectIds)
                    ? ['required', 'exists:subjects,id', Rule::in($teacherSubjectIds)]
                    : ['required', 'exists:subjects,id'];

                $validated = $request->validate([
                    'subject_id' => $subjectValidation,
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

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Question created successfully!',
                        'redirect' => route('teacher.questions.index')
                    ]);
                }

                return redirect()->route('teacher.questions.index')
                    ->with('success', 'Question created successfully!');

            } catch (\Exception $e) {
                Log::error('Teacher question creation failed:', ['error' => $e->getMessage(), 'teacher_id' => Auth::id()]);

                if ($request->ajax()) {
                    return response()->json([
                        'error' => 'Failed to create question. Please try again.'
                    ], 500);
                }

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
        $this->checkTeacher();

        if ($question->created_by !== Auth::id()) {
            abort(403, 'Access denied.');
        }

        $question->load(['subject', 'options']);

        $data = [
            'question' => $question,
        ];

        if ($request->ajax()) {
            return view('modules.questions.show', $data)
                ->renderSections()['content'];
        }

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
        $this->checkTeacher();

        if ($question->created_by !== Auth::id()) {
            abort(403, 'Access denied.');
        }

        $question->load('options');
        $subjects = $this->getTeacherSubjects();
        $teacherSubjectIds = $this->getTeacherSubjectIds();
        $questionTypes = ['mcq', 'true_false', 'short_answer', 'essay', 'fill_blank'];
        $difficulties = ['easy', 'medium', 'hard'];

        $data = [
            'question' => $question,
            'subjects' => $subjects,
            'teacherSubjectIds' => $teacherSubjectIds,
            'questionTypes' => $questionTypes,
            'difficulties' => $difficulties,
        ];

        if ($request->ajax()) {
            return view('modules.questions.edit', $data)
                ->renderSections()['content'];
        }

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
        $this->checkTeacher();

        if ($question->created_by !== Auth::id()) {
            abort(403, 'Access denied.');
        }

        return DB::transaction(function () use ($request, $question) {
            try {
                $teacherSubjectIds = $this->getTeacherSubjectIds();

                // If teacher has subjects, validate subject_id is in their list
                $subjectValidation = !empty($teacherSubjectIds)
                    ? ['required', 'exists:subjects,id', Rule::in($teacherSubjectIds)]
                    : ['required', 'exists:subjects,id'];

                $validated = $request->validate([
                    'subject_id' => $subjectValidation,
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

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Question updated successfully!',
                        'redirect' => route('teacher.questions.index')
                    ]);
                }

                return redirect()->route('teacher.questions.index')
                    ->with('success', 'Question updated successfully!');

            } catch (\Exception $e) {
                Log::error('Teacher question update failed:', ['error' => $e->getMessage(), 'teacher_id' => Auth::id()]);

                if ($request->ajax()) {
                    return response()->json([
                        'error' => 'Failed to update question. Please try again.'
                    ], 500);
                }

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
        $this->checkTeacher();

        if ($question->created_by !== Auth::id()) {
            abort(403, 'Access denied.');
        }

        try {
            if ($question->exams()->count() > 0) {
                $error = 'Cannot delete question. It is being used in one or more exams.';

                if ($request->ajax()) {
                    return response()->json([
                        'error' => $error
                    ], 422);
                }

                return redirect()->route('teacher.questions.index')
                    ->with('error', $error);
            }

            $question->options()->delete();
            $question->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Question deleted successfully!',
                    'redirect' => route('teacher.questions.index')
                ]);
            }

            return redirect()->route('teacher.questions.index')
                ->with('success', 'Question deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Teacher question deletion failed:', ['error' => $e->getMessage(), 'teacher_id' => Auth::id()]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to delete question. Please try again.'
                ], 500);
            }

            return redirect()->route('teacher.questions.index')
                ->with('error', 'Failed to delete question. Please try again.');
        }
    }

    /**
     * Toggle question active status
     */
    public function toggleStatus(Request $request, Question $question)
    {
        $this->checkTeacher();

        if ($question->created_by !== Auth::id()) {
            abort(403, 'Access denied.');
        }

        try {
            $question->update([
                'is_active' => !$question->is_active
            ]);

            $status = $question->is_active ? 'activated' : 'deactivated';
            $message = "Question {$status} successfully!";

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'is_active' => $question->is_active
                ]);
            }

            return redirect()->back()
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Teacher question status toggle failed:', ['error' => $e->getMessage(), 'teacher_id' => Auth::id()]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to update question status.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update question status.');
        }
    }

    /**
     * Get questions for exam creation (AJAX endpoint)
     */
    public function getQuestionsForExam(Request $request)
    {
        $this->checkTeacher();

        $teacherId = Auth::id();
        $teacherSubjectIds = $this->getTeacherSubjectIds();

        $query = Question::with(['subject', 'options'])
            ->where('created_by', $teacherId)
            ->where('is_active', true)
            ->latest();

        // Only show questions from teacher's assigned subjects
        if (!empty($teacherSubjectIds)) {
            $query->whereIn('subject_id', $teacherSubjectIds);
        } else {
            // If teacher has no subjects, return empty
            return response()->json(['questions' => []]);
        }

        if ($request->has('subject_id') && $request->subject_id) {
            if (in_array($request->subject_id, $teacherSubjectIds)) {
                $query->where('subject_id', $request->subject_id);
            }
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('difficulty') && $request->difficulty) {
            $query->where('difficulty', $request->difficulty);
        }

        $questions = $query->get();

        return response()->json([
            'questions' => $questions
        ]);
    }

    /**
     * Debug method to check teacher subjects
     */
    public function debugSubjects()
    {
        $this->checkTeacher();

        $subjects = $this->getTeacherSubjects();
        $subjectIds = $this->getTeacherSubjectIds();

        // Also check the raw assignments
        $rawAssignments = TeacherAssignment::where('teacher_id', Auth::id())->get();

        return response()->json([
            'teacher_id' => Auth::id(),
            'teacher_name' => Auth::user()->name,
            'raw_assignments_count' => $rawAssignments->count(),
            'raw_assignments' => $rawAssignments->pluck('id', 'subject_id'),
            'processed_subjects_count' => $subjects->count(),
            'assigned_subjects' => $subjects->pluck('name', 'id'),
            'subject_ids' => $subjectIds,
        ]);
    }
}
