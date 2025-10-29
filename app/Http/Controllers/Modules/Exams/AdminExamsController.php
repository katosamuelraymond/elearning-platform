<?php

namespace App\Http\Controllers\Modules\Exams;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Exam;
use App\Models\Assessment\ExamAttempt;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

// Import the Assessment Models
use App\Models\Assessment\Question;
use App\Models\Assessment\QuestionOption;

class AdminExamsController extends Controller
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
    public function index()
    {
        $this->checkAdmin();

        $exams = Exam::with(['teacher', 'class', 'subject', 'attempts'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => Exam::count(),
            'published' => Exam::where('is_published', true)->count(),
            'draft' => Exam::where('is_published', false)->count(),
            'attempts' => ExamAttempt::count(),
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
        $this->checkAdmin();

        $classes = SchoolClass::all();
        $subjects = Subject::all();

        return $this->renderView('modules.exams.create', [
            'classes' => $classes,
            'subjects' => $subjects,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkAdmin();

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

            // General question array validation (questions are required unless saving as draft)
            'questions' => $request->input('is_draft') == '0' ? 'required|array|min:1' : 'nullable|array',
        ]);

        // 2. Process Checkbox and Draft Status
        $data = array_merge($validated, [
            'teacher_id' => Auth::id(),
            'randomize_questions' => $request->has('randomize_questions'),
            'require_fullscreen' => $request->has('require_fullscreen'),
            'show_results' => $request->has('show_results'),
            'is_published' => $request->input('is_draft') == '1' ? false : $request->has('is_published'),
        ]);

        // 3. Create Exam
        $exam = Exam::create(Arr::except($data, ['questions', 'is_draft']));

        // 4. Validate and Save Questions (conditional on questions existing)
        if (isset($validated['questions'])) {
            $this->validateAndSaveQuestions($exam, $validated['questions']);
        }

        $message = $exam->is_published ? 'Exam created and published successfully!' : 'Exam saved as draft successfully!';

        return redirect()->route('admin.exams.index')
            ->with('success', $message);
    }

    /**
     * Validate and save the nested question data to the database, ensuring schema compliance.
     * @param Exam $exam
     * @param array $questionsData
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateAndSaveQuestions(Exam $exam, array $questionsData)
    {
        // Define validation rules for each question item dynamically
        $rules = [
            // Using 'mcq' to match the migration enum and the robust Rule::in()
            '*.type' => ['required', Rule::in('mcq', 'true_false', 'short_answer', 'essay', 'fill_blank')],
            '*.points' => ['required', 'numeric', 'min:0.5', 'max:100'],
            '*.question_text' => ['required', 'string'],

            // Multiple Choice (mcq) - Using fully qualified paths for required_if
            '*.correct_answer' => ['required_if:questions.*.type,mcq', 'integer', 'min:0'],
            '*.options' => ['required_if:questions.*.type,mcq', 'array', 'min:2', 'max:6'],
            '*.options.*' => ['required_if:questions.*.type,mcq', 'string', 'max:500'],

            // True/False
            '*.correct_answer_tf' => ['required_if:questions.*.type,true_false', Rule::in('true', 'false')],

            // Fill in the Blanks
            '*.blank_question' => ['required_if:questions.*.type,fill_blank', 'string'],
            '*.blank_answers' => ['required_if:questions.*.type,fill_blank', 'string'], // Comma-separated
        ];

        // Manually validate the array of questions
        $validator = Validator::make(['questions' => $questionsData], [
            'questions' => 'array',
            'questions.*' => $rules,
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validatedQuestions = $validator->validated()['questions'];

        // Save questions
        $questionOrder = 0;
        foreach ($validatedQuestions as $qData) {

            $details = [];
            $correctAnswer = null;

            // 1. Prepare question data based on type
            switch ($qData['type']) {
                case 'mcq':
                    // correct_answer here is the INDEX of the correct option
                    $correctAnswer = $qData['correct_answer'];
                    break;
                case 'true_false':
                    $correctAnswer = $qData['correct_answer_tf'];
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
                    // Store comma-separated answers as a JSON array string
                    $blankAnswers = array_map('trim', explode(',', $qData['blank_answers'] ?? ''));
                    $details['blank_answers'] = $blankAnswers;
                    $correctAnswer = json_encode($blankAnswers);
                    break;
            }

            // 2. Create the base Question record (Reusable Question Pool)
            $question = Question::create([
                'subject_id' => $exam->subject_id,
                'created_by' => Auth::id(),
                'type' => $qData['type'],
                'points' => $qData['points'],
                'question_text' => $qData['question_text'],
                'details' => $details, // Stored as JSON
                'correct_answer' => $correctAnswer, // Stored as TEXT
            ]);

            // 3. Attach the new Question to the Exam via the pivot table (exam_question)
            $exam->questions()->attach($question->id, [
                'order' => $questionOrder++,
                'points' => $qData['points']
            ]);

            // 4. Handle Multiple Choice Options (save to question_options table)
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
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        $this->checkAdmin();

        // Eager load questions and their options
        $exam->load(['teacher', 'class', 'subject', 'attempts.student', 'questions' => fn ($q) => $q->with('options')]);

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
        $this->checkAdmin();

        $classes = SchoolClass::all();
        $subjects = Subject::all();

        // Load questions and their options for editing
        $exam->load(['questions' => fn ($q) => $q->with('options')]);

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
        $this->checkAdmin();

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

            'questions' => $request->input('is_draft') == '0' ? 'required|array|min:1' : 'nullable|array',
        ]);

        // 2. Process Checkbox and Draft Status
        $data = array_merge($validated, [
            'randomize_questions' => $request->has('randomize_questions'),
            'require_fullscreen' => $request->has('require_fullscreen'),
            'show_results' => $request->has('show_results'),
            'is_published' => $request->input('is_draft') == '1' ? false : $request->has('is_published'),
        ]);

        // 3. Update Exam
        $exam->update(Arr::except($data, ['questions', 'is_draft']));

        // 4. Clean up old questions and re-save new ones
        $questionIds = $exam->questions()->pluck('question_id');
        $exam->questions()->detach(); // Remove pivot records

        // Delete related options and the question records themselves
        QuestionOption::whereIn('question_id', $questionIds)->delete();
        Question::whereIn('id', $questionIds)->delete();

        if (isset($validated['questions'])) {
            $this->validateAndSaveQuestions($exam, $validated['questions']);
        }

        $message = $exam->is_published ? 'Exam updated and published successfully!' : 'Exam updated and saved as draft successfully!';

        return redirect()->route('admin.exams.index')
            ->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        $this->checkAdmin();

        // Get IDs before deleting the exam (which may cascade the pivot table)
        $questionIds = $exam->questions()->pluck('question_id');

        $exam->delete();

        // Delete the related options and the question records themselves (reusable pool)
        QuestionOption::whereIn('question_id', $questionIds)->delete();
        Question::whereIn('id', $questionIds)->delete();

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam deleted successfully!');
    }

    /**
     * Display exam attempts
     */
    public function attempts(Exam $exam)
    {
        $this->checkAdmin();

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
        $this->checkAdmin();

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
