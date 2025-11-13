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
    public function index(Request $request)
    {
        $this->checkAdmin();

        $query = Exam::with(['teacher', 'class', 'subject', 'attempts']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
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
            'total' => Exam::count(),
            'published' => Exam::where('is_published', true)->count(),
            'draft' => Exam::where('is_published', false)->count(),
            'archived' => Exam::where('is_archived', true)->count(),
            'attempts' => ExamAttempt::count(),
        ];

        $classes = SchoolClass::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();

        return $this->renderView('modules.exams.index', [
            'exams' => $exams,
            'stats' => $stats,
            'classes' => $classes,
            'subjects' => $subjects,
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
     * Get questions from the question bank with filters
     */
    public function getQuestionBank(Request $request)
    {
        $this->checkAdmin();
        Log::info('🔍 DEBUG: AdminExamsController method called');

        try {
            $filters = $request->validate([
                'subject_id' => 'nullable|exists:subjects,id',
                'type' => 'nullable|in:mcq,true_false,short_answer,essay,fill_blank',
                'difficulty' => 'nullable|in:easy,medium,hard',
                'search' => 'nullable|string|max:255'
            ]);

            // FOR ADMIN USERS: Show all active questions from all subjects
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

            Log::info('🔍 Questions found:', ['count' => $questions->count()]);

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

                // CRITICAL FIX: Convert times from local to UTC and handle timezone issues
                $startTime = $this->convertToUTC($validated['start_time']);
                $endTime = $this->convertToUTC($validated['end_time']);

                // DEBUG: Log time conversions
                Log::info('🕒 TIME CONVERSION DEBUG:', [
                    'input_start_time' => $validated['start_time'],
                    'input_end_time' => $validated['end_time'],
                    'utc_start_time' => $startTime->format('Y-m-d H:i:s'),
                    'utc_end_time' => $endTime->format('Y-m-d H:i:s'),
                    'server_now_utc' => now()->format('Y-m-d H:i:s'),
                    'app_timezone' => config('app.timezone'),
                    'duration_minutes' => $validated['duration']
                ]);

                // If exam should start immediately but time is in future due to timezone issues
                if ($startTime->isFuture() && $this->shouldStartImmediately($validated['start_time'])) {
                    $startTime = now();
                    $endTime = now()->addMinutes($validated['duration']);
                    Log::info('🕒 ADJUSTED TO IMMEDIATE START:', [
                        'new_start' => $startTime->format('Y-m-d H:i:s'),
                        'new_end' => $endTime->format('Y-m-d H:i:s')
                    ]);
                }

                // 2. Validate that we have at least one question if not draft
                $bankQuestions = $this->parseBankQuestions($validated['selected_bank_questions'] ?? '');
                $newQuestions = $validated['questions'] ?? [];

                if ($validated['is_draft'] == '0' && empty($bankQuestions) && empty($newQuestions)) {
                    Log::error('Exam creation validation failed: No questions found', [
                        'bank_questions' => $bankQuestions,
                        'new_questions' => $newQuestions
                    ]);
                    throw ValidationException::withMessages([
                        'questions' => 'At least one question is required for published exams.'
                    ]);
                }

                // 3. Process Checkbox and Draft Status
                $data = array_merge($validated, [
                    'teacher_id' => Auth::id(),
                    'randomize_questions' => $request->has('randomize_questions'),
                    'require_fullscreen' => $request->has('require_fullscreen'),
                    'show_results' => $request->has('show_results'),
                    'is_published' => $request->input('is_draft') == '1' ? false : $request->has('is_published'),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ]);

                // 4. Create Exam
                $exam = Exam::create(Arr::except($data, ['questions', 'selected_bank_questions', 'is_draft']));

                // 5. Attach questions from bank and create new questions
                $this->attachQuestionsToExam($exam, $bankQuestions, $newQuestions, $request);

                // Final debug log
                Log::info('✅ EXAM CREATED SUCCESSFULLY:', [
                    'exam_id' => $exam->id,
                    'title' => $exam->title,
                    'start_time_utc' => $exam->start_time,
                    'end_time_utc' => $exam->end_time,
                    'start_time_local' => $exam->start_time->setTimezone('Africa/Nairobi')->format('Y-m-d H:i:s'),
                    'is_published' => $exam->is_published,
                    'current_time' => now(),
                    'time_until_start' => $exam->start_time->diffForHumans(),
                ]);

                $message = $exam->is_published ? 'Exam created and published successfully!' : 'Exam saved as draft successfully!';

                return redirect()->route('admin.exams.index')
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
     * Convert datetime from local time to UTC
     */
    private function convertToUTC($dateTimeString)
    {
        try {
            // Assume input is in East Africa Time (UTC+3)
            $localTimezone = 'Africa/Nairobi';

            // Parse in local timezone first
            $localTime = \Carbon\Carbon::parse($dateTimeString)->timezone($localTimezone);

            // Convert to UTC
            $utcTime = $localTime->copy()->setTimezone('UTC');

            Log::info('🕒 TIME CONVERSION:', [
                'input' => $dateTimeString,
                'local_time' => $localTime->format('Y-m-d H:i:s'),
                'utc_time' => $utcTime->format('Y-m-d H:i:s'),
                'offset' => $localTime->format('P') . ' → UTC'
            ]);

            return $utcTime;

        } catch (\Exception $e) {
            Log::error('Time conversion failed:', [
                'input' => $dateTimeString,
                'error' => $e->getMessage()
            ]);

            // Fallback: parse as UTC
            return \Carbon\Carbon::parse($dateTimeString)->timezone('UTC');
        }
    }

    /**
     * Check if the exam should start immediately based on user intention
     */
    private function shouldStartImmediately($inputTime)
    {
        // If user sets a time very close to current local time, assume they want immediate start
        $localTime = \Carbon\Carbon::parse($inputTime)->timezone('Africa/Nairobi');
        $currentLocalTime = now()->setTimezone('Africa/Nairobi');

        $timeDifference = $localTime->diffInMinutes($currentLocalTime);

        // If within 10 minutes of current local time, assume immediate start intended
        return $timeDifference <= 10;
    }

    /**
     * Create exam that starts immediately (bypass timezone issues)
     */
    public function storeInstant(Request $request)
    {
        $this->checkAdmin();

        return DB::transaction(function () use ($request) {
            try {
                $validated = $request->validate([
                    'title' => 'required|string|max:255',
                    'class_id' => 'required|exists:school_classes,id',
                    'subject_id' => 'required|exists:subjects,id',
                    'instructions' => 'nullable|string',
                    'description' => 'nullable|string',
                    'type' => ['required', Rule::in('quiz', 'midterm', 'end_of_term', 'practice', 'mock')],
                    'duration' => 'required|integer|min:5', // at least 5 minutes
                    'total_marks' => 'required|integer|min:1',
                    'passing_marks' => 'required|integer|min:0',
                    'max_attempts' => 'required|integer|min:1',
                    'selected_bank_questions' => 'nullable|string',
                    'questions' => 'nullable|array',
                ]);

                // Force immediate start in UTC
                $startTime = now();
                $endTime = now()->addMinutes($validated['duration']);

                // Validate questions
                $bankQuestions = $this->parseBankQuestions($validated['selected_bank_questions'] ?? '');
                $newQuestions = $validated['questions'] ?? [];

                if (empty($bankQuestions) && empty($newQuestions)) {
                    throw ValidationException::withMessages([
                        'questions' => 'At least one question is required for exams.'
                    ]);
                }

                // Create exam data
                $examData = array_merge($validated, [
                    'teacher_id' => Auth::id(),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'is_published' => true,
                    'randomize_questions' => $request->has('randomize_questions'),
                    'require_fullscreen' => $request->has('require_fullscreen'),
                    'show_results' => $request->has('show_results'),
                ]);

                $exam = Exam::create($examData);

                // Attach questions
                $this->attachQuestionsToExam($exam, $bankQuestions, $newQuestions, $request);

                Log::info('🚀 INSTANT EXAM CREATED:', [
                    'exam_id' => $exam->id,
                    'start_time' => $exam->start_time,
                    'end_time' => $exam->end_time,
                    'current_time' => now(),
                ]);

                return redirect()->route('admin.exams.index')
                    ->with('success', 'Instant exam created and started successfully!');

            } catch (ValidationException $e) {
                Log::error('Instant exam validation failed:', ['errors' => $e->errors()]);
                throw $e;
            } catch (\Exception $e) {
                Log::error('Instant exam creation failed:', ['error' => $e->getMessage()]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to create instant exam. Please try again.');
            }
        });
    }

    /**
     * Parse bank questions from comma-separated string
     */
    private function parseBankQuestions($bankQuestionsString)
    {
        if (empty($bankQuestionsString) || $bankQuestionsString === '') {
            Log::info('🔍 DEBUG: parseBankQuestions - Empty string received');
            return [];
        }

        $questionIds = array_map('intval', explode(',', $bankQuestionsString));
        $questionIds = array_filter($questionIds); // Remove empty values

        Log::info('🔍 DEBUG: parseBankQuestions - Processing IDs', [
            'raw_input' => $bankQuestionsString,
            'parsed_ids' => $questionIds,
            'count' => count($questionIds)
        ]);

        if (empty($questionIds)) {
            Log::info('🔍 DEBUG: parseBankQuestions - No valid IDs found');
            return [];
        }

        // FOR ADMIN USERS: Show all active questions (no teacher assignment restriction)
        $validQuestions = Question::whereIn('id', $questionIds)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        Log::info('🔍 DEBUG: parseBankQuestions - Valid questions found', [
            'valid_questions' => $validQuestions,
            'valid_count' => count($validQuestions)
        ]);

        return $validQuestions;
    }

    /**
     * Attach questions to exam (both bank and new questions)
     */
    private function attachQuestionsToExam(Exam $exam, array $bankQuestionIds, array $newQuestionsData, Request $request)
    {
        $questionOrder = 0;
        $totalPoints = 0;

        Log::info('🔍 DEBUG: attachQuestionsToExam - Starting', [
            'bank_question_ids' => $bankQuestionIds,
            'new_questions_count' => count($newQuestionsData),
            'bank_question_points_from_request' => $request->input('bank_question_points', [])
        ]);

        // 1. Attach bank questions
        foreach ($bankQuestionIds as $questionId) {
            $question = Question::find($questionId);
            if ($question) {
                // Use points from request or fall back to question's default points
                $points = $request->input("bank_question_points.{$questionId}", $question->points);

                $exam->questions()->attach($question->id, [
                    'order' => $questionOrder++,
                    'points' => $points
                ]);

                $totalPoints += $points;
                Log::info('🔍 DEBUG: Bank question attached to exam', [
                    'exam_id' => $exam->id,
                    'question_id' => $question->id,
                    'points' => $points,
                    'question_order' => $questionOrder - 1
                ]);
            } else {
                Log::warning('🔍 DEBUG: Bank question not found', ['question_id' => $questionId]);
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

        Log::info('🔍 DEBUG: attachQuestionsToExam - Completed', [
            'total_questions_attached' => $questionOrder,
            'total_points' => $totalPoints
        ]);

        // 3. Update exam total marks if needed (optional)
        // You might want to update the exam's total_marks based on actual points
        // $exam->update(['total_marks' => $totalPoints]);
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

            // Log the created question
            Log::info('New question created:', [
                'question_id' => $question->id,
                'type' => $qData['type'],
                'save_to_bank' => $qData['save_to_bank'] ?? false
            ]);

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
                Log::info('MCQ options created:', [
                    'question_id' => $question->id,
                    'options_count' => count($qData['options'])
                ]);
            }

            $savedQuestions[] = $qData;
        }

        Log::info('All new questions saved successfully for exam:', [
            'exam_id' => $exam->id,
            'questions_count' => count($validatedQuestions)
        ]);

        return $savedQuestions;
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        $this->checkAdmin();

        // Eager load questions and their options
        $exam->load(['teacher', 'class', 'subject', 'attempts.student', 'questions' => fn ($q) => $q->with('options')]);

        // For admin view, we don't need attempt data since it's not student-specific
        $attempt = null;
        $canTakeExam = false;

        return $this->renderView('modules.exams.student-show', [
            'exam' => $exam,
            'attempt' => $attempt,
            'canTakeExam' => $canTakeExam,
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
        // Verify ownership for teacher
        if (auth()->user()->isTeacher() && $exam->teacher_id !== auth()->id()) {
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

                    // Custom questions
                    'custom_questions' => 'nullable|array',
                ]);

                // CRITICAL FIX: Convert times from local to UTC for update as well
                $startTime = $this->convertToUTC($validated['start_time']);
                $endTime = $this->convertToUTC($validated['end_time']);

                // 2. Validate that we have at least one question if not draft
                $bankQuestions = $this->parseBankQuestions($validated['selected_bank_questions'] ?? '');
                $customQuestions = $validated['custom_questions'] ?? [];

                // DEBUG: Log what we're receiving
                Log::info('🔍 DEBUG: Update method - Questions validation', [
                    'selected_bank_questions_input' => $validated['selected_bank_questions'] ?? 'empty',
                    'parsed_bank_questions' => $bankQuestions,
                    'bank_questions_count' => count($bankQuestions),
                    'custom_questions_count' => count($customQuestions),
                    'is_draft' => $validated['is_draft'],
                    'bank_question_points' => $request->input('bank_question_points', [])
                ]);

                if ($validated['is_draft'] == '0' && empty($bankQuestions) && empty($customQuestions)) {
                    Log::error('Exam update validation failed: No questions found', [
                        'bank_questions' => $bankQuestions,
                        'custom_questions' => $customQuestions
                    ]);
                    throw ValidationException::withMessages([
                        'questions' => 'At least one question is required for published exams.'
                    ]);
                }

                // 3. Process Checkbox and Draft Status
                $data = array_merge($validated, [
                    'randomize_questions' => $request->has('randomize_questions'),
                    'require_fullscreen' => $request->has('require_fullscreen'),
                    'show_results' => $request->has('show_results'),
                    'is_published' => $request->input('is_draft') == '1' ? false : $request->has('is_published'),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ]);

                // 4. Update Exam
                $exam->update(Arr::except($data, ['custom_questions', 'selected_bank_questions', 'is_draft']));

                // 5. Clean up old questions and re-attach
                $questionIds = $exam->questions()->pluck('question_id');
                $exam->questions()->detach(); // Remove pivot records

                // Delete only custom questions (not bank questions)
                Question::whereIn('id', $questionIds)
                    ->where('is_bank_question', false)
                    ->delete();

                QuestionOption::whereIn('question_id', $questionIds)->delete();

                // 6. Re-attach questions
                $this->attachQuestionsToExam($exam, $bankQuestions, $customQuestions, $request);

                $message = $exam->is_published ? 'Exam updated and published successfully!' : 'Exam updated and saved as draft successfully!';

                return redirect()->route(auth()->user()->isAdmin() ? 'admin.exams.index' : 'teacher.exams.index')
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
        $this->checkAdmin();

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

                return redirect()->route('admin.exams.index')
                    ->with('success', 'Exam deleted successfully!');

            } catch (\Exception $e) {
                Log::error('Exam deletion failed:', ['error' => $e->getMessage()]);
                return redirect()->route('admin.exams.index')
                    ->with('error', 'Failed to delete exam. Please try again.');
            }
        });
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
        $this->checkAdmin();

        $exam->update([
            'is_published' => !$exam->is_published
        ]);

        $status = $exam->is_published ? 'published' : 'unpublished';

        return redirect()->route('admin.exams.index')
            ->with('success', "Exam {$status} successfully!");
    }

    /**
     * Toggle archive status of exam.
     */
    public function toggleArchive(Exam $exam)
    {
        $this->checkAdmin();

        $exam->update([
            'is_archived' => !$exam->is_archived
        ]);

        $status = $exam->is_archived ? 'archived' : 'unarchived';

        return redirect()->route('admin.exams.index')
            ->with('success', "Exam {$status} successfully!");
    }

    /**
     * Fix exam times for existing exams (utility method)
     */
    public function fixExamTimes(Exam $exam)
    {
        $this->checkAdmin();

        try {
            $originalStart = $exam->start_time;
            $originalEnd = $exam->end_time;

            // Adjust times by subtracting 3 hours (UTC+3 to UTC conversion)
            $exam->update([
                'start_time' => $exam->start_time->subHours(3),
                'end_time' => $exam->end_time->subHours(3),
            ]);

            Log::info('🕒 EXAM TIMES FIXED:', [
                'exam_id' => $exam->id,
                'original_start' => $originalStart,
                'fixed_start' => $exam->start_time,
                'original_end' => $originalEnd,
                'fixed_end' => $exam->end_time,
            ]);

            return redirect()->back()
                ->with('success', 'Exam times fixed successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to fix exam times:', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Failed to fix exam times.');
        }
    }
}
