<?php

namespace App\Http\Controllers\Modules\Exams;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Exam;
use App\Models\Assessment\ExamAttempt;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr; // Required for data manipulation
use App\Models\Assessment\Question; // Assuming this model is available

class TeacherExamsController extends Controller
{
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
        // 1. Initial Validation
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'instructions' => 'nullable|string',
            'description' => 'nullable|string',
            'type' => 'required|in:quiz,midterm,end_of_term,practice,mock',
            'duration' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:0',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'max_attempts' => 'required|integer|min:1',

            // Hidden field for 'Save as Draft' logic
            'is_draft' => 'required|in:0,1',

            // General question array validation (questions are required unless saving as draft)
            'questions' => $request->input('is_draft') == '0' ? 'required|array|min:1' : 'nullable|array',
        ]);

        // 2. Process Checkbox and Draft Status
        $data = array_merge($validated, [
            'teacher_id' => Auth::id(),
            // Checkboxes are only present if checked, so default to false
            'randomize_questions' => $request->has('randomize_questions'),
            'require_fullscreen' => $request->has('require_fullscreen'),
            'show_results' => $request->has('show_results'),

            // Set is_published based on the 'Save as Draft' button (is_draft = 1 means NOT published)
            'is_published' => $request->input('is_draft') == '1' ? false : $request->has('is_published'),
        ]);

        // 3. Create Exam
        $exam = Exam::create(Arr::except($data, ['questions', 'is_draft']));

        // 4. Validate and Save Questions (conditional on questions existing)
        if (isset($validated['questions'])) {
            $this->validateAndSaveQuestions($exam, $validated['questions']);
        }

        $message = $exam->is_published ? 'Exam created and published successfully!' : 'Exam saved as draft successfully!';

        return redirect()->route('teacher.exams.index')
            ->with('success', $message);
    }

    /**
     * Validate and save the nested question data to the database.
     * @param Exam $exam
     * @param array $questionsData
     */
    private function validateAndSaveQuestions(Exam $exam, array $questionsData)
    {
        // Define validation rules for each question item dynamically
        $rules = [
            '*.type' => 'required|in:multiple_choice,true_false,short_answer,essay,fill_blank',
            '*.points' => 'required|integer|min:1',
            '*.question_text' => 'required|string',

            // Multiple Choice
            '*.correct_answer' => 'required_if:*.type,multiple_choice|integer|min:0',
            '*.options' => 'required_if:*.type,multiple_choice|array|min:2|max:6',
            '*.options.*' => 'required_if:*.type,multiple_choice|string|max:500',

            // True/False
            '*.correct_answer_tf' => 'required_if:*.type,true_false|in:true,false',

            // Short Answer / Essay
            '*.expected_answer' => 'nullable|string', // Short Answer reference
            '*.grading_rubric' => 'nullable|string', // Essay instructions

            // Fill in the Blanks
            '*.blank_question' => 'required_if:*.type,fill_blank|string',
            '*.blank_answers' => 'required_if:*.type,fill_blank|string', // Comma-separated
        ];

        // Manually validate the array of questions
        $validator = \Illuminate\Support\Facades\Validator::make(['questions' => $questionsData], [
            'questions' => 'array',
            'questions.*' => $rules,
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        $validatedQuestions = $validator->validated()['questions'];

        // Save questions
        foreach ($validatedQuestions as $qData) {
            $question = $exam->questions()->create([
                'type' => $qData['type'],
                'points' => $qData['points'],
                'question_text' => $qData['question_text'],
                // Add any other common fields
            ]);

            // Prepare complex data based on type (assumes Question model uses a JSON column for details)
            $details = [];
            $correctAnswer = null;

            switch ($qData['type']) {
                case 'multiple_choice':
                    $details['options'] = array_values($qData['options']);
                    $correctAnswer = $qData['correct_answer']; // Index of the correct option
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
                    $details['blank_answers'] = array_map('trim', explode(',', $qData['blank_answers']));
                    $correctAnswer = $details['blank_answers'];
                    break;
            }

            // Update the question with type-specific details
            $question->update([
                'details' => $details, // Assumes 'details' is a JSON column on the Question model
                'correct_answer' => $correctAnswer, // Assumes 'correct_answer' is a JSON or string column
            ]);
        }
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

        $exam->load(['class', 'subject', 'attempts.student', 'questions']); // Eager load questions

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

        $classes = SchoolClass::all();
        $subjects = Subject::all();

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

        // 1. Initial Validation
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'instructions' => 'nullable|string',
            'description' => 'nullable|string',
            'type' => 'required|in:quiz,midterm,end_of_term,practice,mock',
            'duration' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:0',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'max_attempts' => 'required|integer|min:1',

            // Hidden field for 'Save as Draft' logic
            'is_draft' => 'required|in:0,1',

            // General question array validation (questions are required unless saving as draft)
            'questions' => $request->input('is_draft') == '0' ? 'required|array|min:1' : 'nullable|array',
        ]);

        // 2. Process Checkbox and Draft Status
        $data = array_merge($validated, [
            // Checkboxes are only present if checked, so default to false
            'randomize_questions' => $request->has('randomize_questions'),
            'require_fullscreen' => $request->has('require_fullscreen'),
            'show_results' => $request->has('show_results'),

            // Set is_published based on the 'Save as Draft' button (is_draft = 1 means NOT published)
            'is_published' => $request->input('is_draft') == '1' ? false : $request->has('is_published'),
        ]);

        // 3. Update Exam Details
        $exam->update(Arr::except($data, ['questions', 'is_draft']));

        // 4. Update Questions: Delete existing and insert new ones
        $exam->questions()->delete();
        if (isset($validated['questions'])) {
            $this->validateAndSaveQuestions($exam, $validated['questions']);
        }

        $message = $exam->is_published ? 'Exam updated and published successfully!' : 'Exam updated and saved as draft successfully!';

        return redirect()->route('teacher.exams.index')
            ->with('success', $message);
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

        $exam->delete();

        return redirect()->route('teacher.exams.index')
            ->with('success', 'Exam deleted successfully!');
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
