<?php

namespace App\Http\Controllers\Modules\Exams;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Exam;
use App\Models\Assessment\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class StudentExamsController extends Controller
{
    /**
     * Display student's exams
     */
    public function index(Request $request)
    {
        $student = Auth::user();

        // Get student's current class assignment with class relationship
        $currentAssignment = $student->currentClassAssignment()->with(['class', 'stream'])->first();

        // Check if student has an active class assignment
        if (!$currentAssignment) {
            return $this->renderView('modules.exams.student-index', [
                'exams' => collect([]),
                'stats' => ['total' => 0, 'available' => 0, 'in_progress' => 0, 'completed' => 0],
                'subjects' => collect([]),
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true
            ]);
        }

        // Get exams assigned to student's class
        $query = Exam::with(['subject', 'class', 'attempts' => function ($q) use ($student) {
            $q->where('student_id', $student->id);
        }])
            ->where('class_id', $currentAssignment->class_id)
            ->where('is_published', true)
            ->where('is_archived', false);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            switch ($request->status) {
                case 'available':
                    $query->where(function ($q) use ($student) {
                        $q->whereDoesntHave('attempts', function ($q2) use ($student) {
                            $q2->where('student_id', $student->id);
                        })
                            ->where('start_time', '<=', now())
                            ->where('end_time', '>=', now());
                    });
                    break;
                case 'upcoming':
                    $query->where('start_time', '>', now());
                    break;
                case 'in_progress':
                    $query->whereHas('attempts', function ($q) use ($student) {
                        $q->where('student_id', $student->id)
                            ->where('status', 'in_progress');
                    });
                    break;
                case 'completed':
                    $query->whereHas('attempts', function ($q) use ($student) {
                        $q->where('student_id', $student->id)
                            ->whereIn('status', ['submitted', 'graded']);
                    });
                    break;
                case 'missed':
                    $query->where('end_time', '<', now())
                        ->whereDoesntHave('attempts', function ($q) use ($student) {
                            $q->where('student_id', $student->id);
                        });
                    break;
            }
        }

        $exams = $query->latest()->paginate(12);

        // Calculate stats
        $stats = [
            'total' => Exam::where('class_id', $currentAssignment->class_id)
                ->where('is_published', true)
                ->where('is_archived', false)
                ->count(),
            'available' => Exam::where('class_id', $currentAssignment->class_id)
                ->where('is_published', true)
                ->where('is_archived', false)
                ->where(function ($q) use ($student) {
                    $q->whereDoesntHave('attempts', function ($q2) use ($student) {
                        $q2->where('student_id', $student->id);
                    })
                        ->where('start_time', '<=', now())
                        ->where('end_time', '>=', now());
                })->count(),
            'in_progress' => Exam::where('class_id', $currentAssignment->class_id)
                ->where('is_published', true)
                ->where('is_archived', false)
                ->whereHas('attempts', function ($q) use ($student) {
                    $q->where('student_id', $student->id)
                        ->where('status', 'in_progress');
                })->count(),
            'completed' => Exam::where('class_id', $currentAssignment->class_id)
                ->where('is_published', true)
                ->where('is_archived', false)
                ->whereHas('attempts', function ($q) use ($student) {
                    $q->where('student_id', $student->id)
                        ->whereIn('status', ['submitted', 'graded']);
                })->count(),
        ];

        // Get subjects that have exams for this student's class
        $subjects = \App\Models\Academic\Subject::whereHas('exams', function ($q) use ($currentAssignment) {
            $q->where('class_id', $currentAssignment->class_id)
                ->where('is_published', true)
                ->where('is_archived', false);
        })->get();

        return $this->renderView('modules.exams.student-index', [
            'exams' => $exams,
            'stats' => $stats,
            'subjects' => $subjects,
            'currentAssignment' => $currentAssignment,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Show exam details for student
     */
    public function show(Exam $exam)
    {
        $student = Auth::user();

        // Get student's current class assignment
        $currentAssignment = $student->currentClassAssignment;

        // Check if student can access this exam
        if (!$currentAssignment || $exam->class_id !== $currentAssignment->class_id) {
            abort(403, 'This exam is not assigned to your class.');
        }

        if (!$exam->is_published || $exam->is_archived) {
            abort(403, 'This exam is not available.');
        }

        $exam->load(['subject', 'class', 'teacher', 'questions']);

        $attempts = $exam->attempts()
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        // Get current in-progress attempt
        $currentAttempt = $exam->attempts()
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->first();

        // Calculate best score
        $bestScore = $exam->attempts()
            ->where('student_id', $student->id)
            ->whereIn('status', ['submitted', 'graded'])
            ->max('total_score');

        // Check if student can take the exam
        $canTakeExam = $this->canStudentTakeExam($exam, $student);

        return $this->renderView('modules.exams.student-show', [
            'exam' => $exam,
            'attempts' => $attempts,
            'currentAttempt' => $currentAttempt,
            'canTakeExam' => $canTakeExam,
            'bestScore' => $bestScore,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Start a new exam attempt
     */
    public function start(Request $request, Exam $exam)
    {
        $student = Auth::user();

        // Check if student can take the exam
        if (!$this->canStudentTakeExam($exam, $student)) {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'You cannot start this exam at this time.');
        }

        // Check for existing in-progress attempt
        $existingAttempt = $exam->attempts()
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            return redirect()->route('student.exams.take', ['exam' => $exam, 'attempt' => $existingAttempt]);
        }

        // Create new attempt with empty answers array
        $attempt = $exam->attempts()->create([
            'student_id' => $student->id,
            'started_at' => now(),
            'status' => 'in_progress',
            'total_score' => 0,
            'answers' => [], // Initialize empty answers array
            'manual_grades' => [] // Initialize empty manual grades array
        ]);

        \Log::info('🎯 EXAM ATTEMPT STARTED:', [
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'attempt_id' => $attempt->id
        ]);

        return redirect()->route('student.exams.take', ['exam' => $exam, 'attempt' => $attempt]);
    }

    /**
     * Display exam taking interface
     */
    public function take(Exam $exam, ExamAttempt $attempt)
    {
        $student = Auth::user();

        // Verify attempt belongs to student and exam
        if ($attempt->student_id !== $student->id || $attempt->exam_id !== $exam->id) {
            abort(403, 'Invalid exam attempt.');
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'This attempt has already been submitted.');
        }

        // Calculate time remaining based on exam end time and attempt start time
        $now = now();
        $examEndTime = \Carbon\Carbon::parse($exam->end_time);
        $attemptStartTime = \Carbon\Carbon::parse($attempt->started_at);

        // Calculate maximum allowed time (whichever comes first: exam end time or duration limit)
        $maxAllowedTime = $attemptStartTime->copy()->addMinutes($exam->duration);
        $actualEndTime = $examEndTime->lt($maxAllowedTime) ? $examEndTime : $maxAllowedTime;

        $timeRemaining = $now->diffInSeconds($actualEndTime, false); // false returns negative if expired

        // Check if time has expired
        if ($timeRemaining <= 0) {
            // Auto-submit if time has expired
            $this->autoSubmitAttempt($exam, $attempt);
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'Exam time has expired. Your attempt has been automatically submitted.');
        }

        // Load questions with correct ordering and options
        $exam->load(['questions' => function ($query) {
            $query->with(['options' => function ($q) {
                $q->orderBy('order');
            }])->orderBy('pivot_order');
        }, 'subject', 'class']);

        // Get answers from the JSON field
        $savedAnswers = $attempt->answers ?? [];

        // Pass time data to frontend
        $timeData = [
            'start_time' => $attempt->started_at->toISOString(),
            'end_time' => $actualEndTime->toISOString(),
            'duration_minutes' => $exam->duration,
            'time_remaining' => $timeRemaining,
            'exam_end_time' => $exam->end_time->toISOString(),
            'max_allowed_time' => $maxAllowedTime->toISOString(),
        ];

        \Log::info('⏰ TIME CALCULATIONS:', $timeData);
        \Log::info('📝 LOADED EXAM DATA:', [
            'exam_id' => $exam->id,
            'attempt_id' => $attempt->id,
            'questions_count' => $exam->questions->count(),
            'saved_answers_count' => count($savedAnswers)
        ]);

        return $this->renderView('modules.exams.take', [
            'exam' => $exam,
            'attempt' => $attempt,
            'savedAnswers' => $savedAnswers,
            'timeData' => $timeData,
            'showNavbar' => false,
            'showSidebar' => false,
            'showFooter' => false,
        ]);
    }

    /**
     * Save exam progress
     */
    public function saveProgress(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        $student = Auth::user();

        // Verify attempt belongs to student
        if ($attempt->student_id !== $student->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid attempt'
            ], 403);
        }

        if ($attempt->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Attempt is not in progress'
            ]);
        }

        $answers = json_decode($request->answers, true) ?? [];

        \Log::info('💾 SAVE PROGRESS REQUEST:', [
            'attempt_id' => $attempt->id,
            'answers_received' => count($answers),
            'sample_answers' => array_slice($answers, 0, 2)
        ]);

        // Update the answers JSON field directly
        $currentAnswers = $attempt->answers ?? [];
        $updatedAnswers = array_merge($currentAnswers, $answers);

        $attempt->update([
            'answers' => $updatedAnswers
        ]);

        \Log::info('💾 EXAM PROGRESS SAVED:', [
            'attempt_id' => $attempt->id,
            'saved_answers' => count($answers),
            'total_answers_now' => count($updatedAnswers),
            'student_id' => $student->id
        ]);

        return response()->json([
            'success' => true,
            'saved_count' => count($answers),
            'total_answers' => count($updatedAnswers),
            'timestamp' => now()->format('H:i:s')
        ]);
    }

    /**
     * Get saved progress
     */
    public function getProgress(Exam $exam, ExamAttempt $attempt)
    {
        $student = Auth::user();

        // Verify attempt belongs to student
        if ($attempt->student_id !== $student->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid attempt'
            ], 403);
        }

        // Get answers from JSON field
        $answers = $attempt->answers ?? [];

        \Log::info('📥 GET PROGRESS:', [
            'attempt_id' => $attempt->id,
            'answers_count' => count($answers)
        ]);

        return response()->json([
            'success' => true,
            'answers' => $answers
        ]);
    }

    /**
     * Submit exam attempt
     */
    public function submit(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        $student = Auth::user();

        // Verify attempt belongs to student
        if ($attempt->student_id !== $student->id) {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'Invalid exam attempt.');
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'This attempt has already been submitted.');
        }

        \Log::info('📝 EXAM SUBMISSION STARTED:', [
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'attempt_id' => $attempt->id,
            'has_answers' => $request->has('answers'),
            'answers_count' => $request->has('answers') ? count($request->answers) : 0
        ]);

        // Process answers from request - USE SUBMITTED ANSWERS DIRECTLY
        $submittedAnswers = $request->answers ?? [];
        $processedAnswers = [];

        foreach ($submittedAnswers as $questionId => $answer) {
            // Store answers exactly as submitted
            $processedAnswers[$questionId] = $answer;
        }

        \Log::info('🔍 PROCESSED ANSWERS FOR SUBMISSION:', [
            'total_answers' => count($processedAnswers),
            'answers' => $processedAnswers
        ]);

        // Use submitted answers as the final answers
        $finalAnswers = $processedAnswers;

        // Update attempt with final answers
        $attempt->update([
            'answers' => $finalAnswers
        ]);

        // Calculate score with proper evaluation
        $score = $this->calculateScore($exam, $attempt);

        // Finalize attempt
        $attempt->update([
            'submitted_at' => now(),
            'status' => 'submitted',
            'total_score' => $score,
        ]);

        // Detailed submission log
        \Log::info('✅ EXAM SUBMITTED SUCCESSFULLY:', [
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'attempt_id' => $attempt->id,
            'total_answers' => count($finalAnswers),
            'calculated_score' => $score,
            'total_marks' => $exam->total_marks,
            'percentage' => $exam->total_marks > 0 ? round(($score / $exam->total_marks) * 100, 2) : 0
        ]);

        return redirect()->route('student.exams.results', ['exam' => $exam, 'attempt' => $attempt])
            ->with('success', 'Exam submitted successfully! Your score: ' . $score . '/' . $exam->total_marks);
    }

    /**
     * Display exam results
     */


    /**
     * Display student's exam history
     */
    public function myAttempts(Request $request)
    {
        $student = Auth::user();

        $attempts = ExamAttempt::with(['exam' => function ($query) {
            $query->with(['subject', 'class']);
        }])
            ->where('student_id', $student->id)
            ->latest()
            ->paginate(15);

        return $this->renderView('modules.exams.history', [
            'attempts' => $attempts,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true,
        ]);
    }

    /**
     * Check if student can take the exam
     */
    private function canStudentTakeExam(Exam $exam, $student)
    {
        $now = now();

        // Check if exam is within time window
        if ($now < $exam->start_time || $now > $exam->end_time) {
            return false;
        }

        // Check if student has already completed the exam
        $completedAttempt = $exam->attempts()
            ->where('student_id', $student->id)
            ->whereIn('status', ['submitted', 'graded'])
            ->exists();

        if ($completedAttempt) {
            return false;
        }

        // Check attempt limit
        if ($exam->max_attempts > 0) {
            $attemptCount = $exam->attempts()
                ->where('student_id', $student->id)
                ->whereIn('status', ['submitted', 'graded'])
                ->count();

            if ($attemptCount >= $exam->max_attempts) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate exam score with manual grading support
     */
    private function calculateScore(Exam $exam, $attempt)
    {
        $score = 0;
        $answers = $attempt->answers ?? [];
        $manualGrades = $attempt->manual_grades ?? [];

        \Log::info('🧮 SCORE CALCULATION STARTED:', [
            'attempt_id' => $attempt->id,
            'total_questions' => $exam->questions->count(),
            'answers_provided' => count($answers),
            'manual_grades' => $manualGrades
        ]);

        foreach ($exam->questions as $question) {
            $studentAnswer = $answers[$question->id] ?? null;
            $maxPoints = $question->pivot->points ?? $question->points ?? 0;

            // Check if manual grade exists for this question
            if (isset($manualGrades[$question->id])) {
                $questionScore = $manualGrades[$question->id];
                \Log::info("📝 USING MANUAL GRADE FOR QUESTION {$question->id}:", [
                    'manual_score' => $questionScore,
                    'max_points' => $maxPoints
                ]);
            } else {
                $questionScore = $this->evaluateAutoGradedAnswer($question, $studentAnswer, $maxPoints);
            }

            $score += $questionScore;

            \Log::info("📊 QUESTION {$question->id} EVALUATION:", [
                'type' => $question->type,
                'student_answer' => $studentAnswer,
                'max_points' => $maxPoints,
                'points_earned' => $questionScore,
                'grading_method' => isset($manualGrades[$question->id]) ? 'manual' : 'auto'
            ]);
        }

        \Log::info('🎯 FINAL SCORE CALCULATED:', [
            'attempt_id' => $attempt->id,
            'total_score' => $score,
            'total_possible' => $exam->total_marks
        ]);

        return $score;
    }

    /**
     * Evaluate only auto-gradable questions (MCQ, True/False)
     */
    private function evaluateAutoGradedAnswer($question, $studentAnswer, $maxPoints)
    {
        if (empty($studentAnswer)) {
            return 0;
        }

        // Only auto-grade objective questions
        if (in_array($question->type, ['mcq', 'true_false'])) {
            switch ($question->type) {
                case 'mcq':
                    return $this->evaluateMcqAnswer($question, $studentAnswer, $maxPoints);

                case 'true_false':
                    return $studentAnswer === $question->correct_answer ? $maxPoints : 0;
            }
        }

        // For subjective questions (essay, short_answer, fill_blank), return 0 initially
        // They need manual grading
        return 0;
    }

    /**
     * Evaluate MCQ answer using options table is_correct field
     */


    /**
     * Update manual grade for a question (for teachers)
     */
    public function updateManualGrade(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        // Check if user is teacher or admin
        if (!auth()->user()->isTeacher() && !auth()->user()->isAdmin()) {
            abort(403, 'Only teachers and admins can update grades.');
        }

        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'score' => 'required|numeric|min:0'
        ]);

        $questionId = $request->question_id;
        $score = (float)$request->score;

        // Get the question to check max points
        $question = $exam->questions->where('id', $questionId)->first();
        if (!$question) {
            return redirect()->back()->with('error', 'Question not found in this exam.');
        }

        $maxPoints = $question->pivot->points ?? $question->points ?? 0;

        // Ensure score doesn't exceed max points
        $score = min($score, $maxPoints);

        // Get current manual grades
        $manualGrades = $attempt->manual_grades ?? [];
        $manualGrades[$questionId] = $score;

        // Update attempt with new manual grade
        $attempt->update([
            'manual_grades' => $manualGrades
        ]);

        // Recalculate total score
        $totalScore = $this->calculateScore($exam, $attempt);
        $attempt->update(['total_score' => $totalScore]);

        \Log::info('📝 MANUAL GRADE UPDATED:', [
            'attempt_id' => $attempt->id,
            'question_id' => $questionId,
            'awarded_score' => $score,
            'max_points' => $maxPoints,
            'new_total_score' => $totalScore,
            'graded_by' => auth()->user()->id
        ]);

        return redirect()->back()->with('success',
            "Score updated: {$score}/{$maxPoints} points awarded for this question."
        );
    }

    /**
     * Bulk update manual grades (for teachers)
     */
    public function bulkUpdateGrades(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        if (!auth()->user()->isTeacher() && !auth()->user()->isAdmin()) {
            abort(403, 'Only teachers and admins can update grades.');
        }

        $manualGrades = $attempt->manual_grades ?? [];

        foreach ($request->scores as $questionId => $score) {
            $question = $exam->questions->where('id', $questionId)->first();
            if ($question) {
                $maxPoints = $question->pivot->points ?? $question->points ?? 0;
                $manualGrades[$questionId] = min($score, $maxPoints);
            }
        }

        $attempt->update([
            'manual_grades' => $manualGrades,
            'total_score' => $this->calculateScore($exam, $attempt)
        ]);

        return redirect()->back()->with('success', 'All grades updated successfully!');
    }

    /**
     * Auto-submit attempt when time expires
     */
    private function autoSubmitAttempt(Exam $exam, $attempt)
    {
        $score = $this->calculateScore($exam, $attempt);

        $attempt->update([
            'submitted_at' => now(),
            'status' => 'submitted',
            'total_score' => $score,
        ]);

        \Log::info('⏰ AUTO-SUBMITTED EXAM:', [
            'attempt_id' => $attempt->id,
            'reason' => 'Time expired',
            'score' => $score
        ]);
    }

    /**
     * Check if student can see detailed results based on academic logic
     */

    /**
     * Display exam results with proper academic logic
     */
    public function results(Exam $exam, ExamAttempt $attempt)
    {
        $student = Auth::user();

        // Verify attempt belongs to student
        if ($attempt->student_id !== $student->id) {
            abort(403, 'Invalid exam attempt.');
        }

        if ($attempt->status === 'in_progress') {
            return redirect()->route('student.exams.take', ['exam' => $exam, 'attempt' => $attempt]);
        }

        $exam->load(['questions' => function($query) {
            $query->with(['options' => function($q) {
                $q->orderBy('order');
            }]);
        }]);

        // Get answers and manual grades from JSON fields
        $answers = $attempt->answers ?? [];
        $manualGrades = $attempt->manual_grades ?? [];

        // Determine what student can see based on academic logic
        $resultsStatus = $this->getStudentResultsStatus($exam, $attempt);
        $canSeeDetailedResults = $resultsStatus['canSeeDetails'];
        $canSeeScoreOnly = $resultsStatus['canSeeScore'];

        // Special messages for high-stakes exams
        if (in_array($exam->type, ['midterm', 'end_of_term', 'final', 'mock']) && !$canSeeDetailedResults) {
            $resultsStatus['message'] = 'Results for this ' . str_replace('_', ' ', $exam->type) . ' exam will be released after official review.';
            $resultsStatus['color'] = 'yellow';
            $resultsStatus['icon'] = 'clock';
        }

        return $this->renderView('modules.exams.results', [
            'exam' => $exam,
            'attempt' => $attempt,
            'answers' => $answers,
            'manualGrades' => $manualGrades,
            'resultsStatus' => $resultsStatus,
            'canSeeDetailedResults' => $canSeeDetailedResults,
            'canSeeScoreOnly' => $canSeeScoreOnly,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true,
        ]);
    }

    /**
     * CORRECTED: Evaluate MCQ answer with proper option ID comparison
     */
    private function evaluateMcqAnswer($question, $studentAnswer, $points)
    {
        \Log::info('🎯 MCQ EVALUATION DETAILED:', [
            'question_id' => $question->id,
            'student_answer' => $studentAnswer,
            'student_answer_type' => gettype($studentAnswer),
            'points' => $points
        ]);

        // If empty or null, no points
        if ($studentAnswer === null || $studentAnswer === '' || $studentAnswer === []) {
            return 0;
        }

        // Use options table to find correct answer
        if ($question->relationLoaded('options') && $question->options->isNotEmpty()) {
            // Find the correct option
            $correctOption = $question->options->where('is_correct', true)->first();

            if ($correctOption) {
                // ✅ CORRECT LOGIC: Compare student answer with correct option ID
                $studentAnswerId = (int) $studentAnswer;
                $correctOptionId = (int) $correctOption->id;

                $isCorrect = $studentAnswerId === $correctOptionId;

                \Log::info('🎯 MCQ OPTION ID COMPARISON:', [
                    'student_answer_id' => $studentAnswerId,
                    'correct_option_id' => $correctOptionId,
                    'is_correct' => $isCorrect
                ]);

                return $isCorrect ? $points : 0;
            }
        }

        // Fallback: use questions.correct_answer field
        if (!empty($question->correct_answer)) {
            $studentAnswerId = (int) $studentAnswer;
            $correctAnswerId = (int) $question->correct_answer;

            $isCorrect = $studentAnswerId === $correctAnswerId;

            \Log::info('🎯 MCQ FALLBACK COMPARISON:', [
                'student_answer_id' => $studentAnswerId,
                'correct_answer_id' => $correctAnswerId,
                'is_correct' => $isCorrect
            ]);

            return $isCorrect ? $points : 0;
        }

        return 0;
    }



    private function canStudentSeeDetailedResults(Exam $exam, ExamAttempt $attempt)
    {
        // If attempt is not graded yet
        if ($attempt->status !== 'graded') {
            return false;
        }

        // Practice quizzes and some formative assessments can show immediate results
        if (in_array($exam->type, ['practice', 'quiz'])) {
            return true;
        }

        // Check if results_released_at column exists in the database
        $hasResultsReleasedAt = Schema::hasColumn('exams', 'results_released_at');

        // For high-stakes exams, only show results if explicitly released
        if (in_array($exam->type, ['midterm', 'end_of_term', 'final', 'mock'])) {
            if ($hasResultsReleasedAt) {
                return $exam->show_results && $exam->results_released_at !== null;
            } else {
                // Fallback for existing exams without results_released_at
                return $exam->show_results;
            }
        }

        // Default: only show if teacher has released results
        return $exam->show_results;
    }

    /**
     * Check if student can see only score (without detailed questions)
     */
    private function canStudentSeeScoreOnly(Exam $exam, ExamAttempt $attempt)
    {
        // Students should NOT see scores until results are released
        return $attempt->status === 'graded' &&
            $exam->show_results &&
            $exam->results_released_at !== null;
    }

    /**
     * Get appropriate status message for student
     */
    private function getStudentResultsStatus(Exam $exam, ExamAttempt $attempt)
    {
        $canSeeDetailedResults = $this->canStudentSeeDetailedResults($exam, $attempt);
        $canSeeScoreOnly = $this->canStudentSeeScoreOnly($exam, $attempt);

        if ($canSeeDetailedResults) {
            return [
                'message' => 'Your exam results are available.',
                'color' => 'green',
                'icon' => 'check-circle',
                'canSeeScore' => true,
                'canSeeDetails' => true
            ];
        } elseif ($canSeeScoreOnly && !$canSeeDetailedResults) {
            // Check if this is a high-stakes exam that needs teacher approval
            if (in_array($exam->type, ['midterm', 'end_of_term', 'final', 'mock'])) {
                return [
                    'message' => 'Your score is available. Detailed results will be released after official review.',
                    'color' => 'yellow',
                    'icon' => 'clock',
                    'canSeeScore' => true,
                    'canSeeDetails' => false
                ];
            } else {
                return [
                    'message' => 'Your score is available. Detailed results will be released soon.',
                    'color' => 'yellow',
                    'icon' => 'clock',
                    'canSeeScore' => true,
                    'canSeeDetails' => false
                ];
            }
        } elseif ($attempt->status === 'submitted') {
            return [
                'message' => 'Your exam is being graded. Please check back later.',
                'color' => 'blue',
                'icon' => 'hourglass-half',
                'canSeeScore' => false,
                'canSeeDetails' => false
            ];
        } else {
            return [
                'message' => 'Results are being processed.',
                'color' => 'blue',
                'icon' => 'info-circle',
                'canSeeScore' => false,
                'canSeeDetails' => false
            ];
        }
    }

    /**
     * Poll exam status for real-time updates
     */
    public function pollStatus(Exam $exam)
    {
        $student = Auth::user();

        // Get current class assignment
        $currentAssignment = $student->currentClassAssignment;

        // Verify student can access this exam
        if (!$currentAssignment || $exam->class_id !== $currentAssignment->class_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $now = now();
        $examStartTime = $exam->start_time;
        $examEndTime = $exam->end_time;

        // Get current attempt
        $currentAttempt = $exam->attempts()
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->first();

        // Calculate time until start/end
        $timeUntilStart = $examStartTime->diffInSeconds($now, false) * -1; // Negative if not started
        $timeUntilEnd = $examEndTime->diffInSeconds($now, false) * -1;

        // Determine exam status
        $status = 'upcoming';
        if ($now->between($examStartTime, $examEndTime)) {
            $status = 'active';
        } elseif ($now->gt($examEndTime)) {
            $status = 'ended';
        }

        // Check if student can take exam now
        $canTakeExam = $this->canStudentTakeExam($exam, $student);

        return response()->json([
            'status' => $status,
            'can_take_exam' => $canTakeExam,
            'current_time' => $now->toISOString(),
            'exam_start_time' => $examStartTime->toISOString(),
            'exam_end_time' => $examEndTime->toISOString(),
            'time_until_start' => max(0, $timeUntilStart),
            'time_until_end' => max(0, $timeUntilEnd),
            'has_current_attempt' => !is_null($currentAttempt),
            'current_attempt_status' => $currentAttempt ? $currentAttempt->status : null,
            'attempts_count' => $exam->attempts()->where('student_id', $student->id)->count(),
            'max_attempts' => $exam->max_attempts,
        ]);
    }
}
