<?php

namespace App\Http\Controllers\Modules\Quizzes;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Quiz;
use App\Models\Assessment\QuizAttempt;
use App\Models\Assessment\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StudentQuizzesController extends Controller
{
    /**
     * Display a listing of available quizzes for the student
     */
    public function index(Request $request)
    {
        $student = Auth::user();

        // Get student's current class assignment (same pattern as exams)
        $currentAssignment = $student->currentClassAssignment()->with(['class', 'stream'])->first();

        // Check if student has an active class assignment
        if (!$currentAssignment) {
            return $this->renderView('modules.quizzes.student.index', [
                'quizzes' => collect([]),
                'stats' => ['available' => 0, 'attempted' => 0, 'completed' => 0],
                'filters' => $request->only(['subject_id', 'type', 'search']),
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true
            ]);
        }

        $query = Quiz::with(['teacher', 'subject', 'class'])
            ->where('class_id', $currentAssignment->class_id)
            ->where('is_published', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->withCount(['attempts' => function($query) use ($student) {
                $query->where('student_id', $student->id);
            }]);

        // Filter by subject
        if ($request->has('subject_id') && $request->subject_id != '') {
            $query->where('subject_id', $request->subject_id);
        }

        // Filter by type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'LIKE', "%{$request->search}%");
        }

        $quizzes = $query->latest()->paginate(12);

        $stats = [
            'available' => $quizzes->total(),
            'attempted' => QuizAttempt::where('student_id', $student->id)->count(),
            'completed' => QuizAttempt::where('student_id', $student->id)
                ->where('status', 'submitted')
                ->count(),
        ];

        return $this->renderView('modules.quizzes.student.index', [
            'quizzes' => $quizzes,
            'stats' => $stats,
            'filters' => $request->only(['subject_id', 'type', 'search']),
            'currentAssignment' => $currentAssignment,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Display quizzes that are upcoming
     */
    public function upcoming(Request $request)
    {
        $student = Auth::user();

        // Get student's current class assignment
        $currentAssignment = $student->currentClassAssignment()->first();

        if (!$currentAssignment) {
            return $this->renderView('modules.quizzes.student.upcoming', [
                'quizzes' => collect([]),
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true
            ]);
        }

        $quizzes = Quiz::with(['teacher', 'subject', 'class'])
            ->where('class_id', $currentAssignment->class_id)
            ->where('is_published', true)
            ->where('start_time', '>', now())
            ->latest()
            ->paginate(12);

        return $this->renderView('modules.quizzes.student.upcoming', [
            'quizzes' => $quizzes,
            'currentAssignment' => $currentAssignment,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Display completed quizzes and attempts
     */
    public function completed(Request $request)
    {
        $student = Auth::user();

        $attempts = QuizAttempt::with(['quiz', 'quiz.subject', 'quiz.teacher'])
            ->where('student_id', $student->id)
            ->where('status', 'submitted')
            ->latest()
            ->paginate(15);

        $stats = [
            'total_attempts' => $attempts->total(),
            'average_score' => QuizAttempt::where('student_id', $student->id)
                    ->where('status', 'submitted')
                    ->avg('total_score') ?? 0,
        ];

        return $this->renderView('modules.quizzes.student.completed', [
            'attempts' => $attempts,
            'stats' => $stats,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Show quiz instructions and start page
     */
    public function show(Quiz $quiz)
    {
        $student = Auth::user();

        // Get student's current class assignment
        $currentAssignment = $student->currentClassAssignment()->first();

        // Check if student can access this quiz
        if (!$currentAssignment || $quiz->class_id !== $currentAssignment->class_id) {
            abort(403, 'You are not enrolled in this class.');
        }

        if (!$quiz->is_published) {
            abort(404, 'Quiz not found.');
        }

        // Check if quiz is available
        if ($quiz->start_time > now()) {
            return redirect()->route('student.quizzes.upcoming')
                ->with('error', 'This quiz is not available yet. It will start on ' . $quiz->start_time->format('M j, Y g:i A'));
        }

        if ($quiz->end_time < now()) {
            return redirect()->route('student.quizzes.completed')
                ->with('error', 'This quiz has ended.');
        }

        // Check for existing attempts
        $existingAttempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->first();

        $maxAttemptsReached = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('student_id', $student->id)
                ->count() >= 1; // You can make this configurable per quiz

        return $this->renderView('modules.quizzes.student.show', [
            'quiz' => $quiz,
            'existingAttempt' => $existingAttempt,
            'maxAttemptsReached' => $maxAttemptsReached,
            'currentAssignment' => $currentAssignment,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Start a new quiz attempt
     */
    public function start(Quiz $quiz)
    {
        $student = Auth::user();

        // Get student's current class assignment
        $currentAssignment = $student->currentClassAssignment()->first();

        // Validation checks
        if (!$currentAssignment || $quiz->class_id !== $currentAssignment->class_id) {
            abort(403, 'You are not enrolled in this class.');
        }

        if (!$quiz->is_published || $quiz->start_time > now() || $quiz->end_time < now()) {
            abort(403, 'Quiz is not available at this time.');
        }

        // Check for existing in-progress attempt
        $existingAttempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            return redirect()->route('student.quizzes.attempt', $existingAttempt);
        }

        // Check max attempts
        $attemptCount = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->count();

        if ($attemptCount >= 1) { // Configurable per quiz
            return redirect()->route('student.quizzes.show', $quiz)
                ->with('error', 'You have reached the maximum number of attempts for this quiz.');
        }

        // Create new attempt
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'started_at' => now(),
            'status' => 'in_progress',
            'answers' => [],
        ]);

        return redirect()->route('student.quizzes.attempt', $attempt);
    }

    /**
     * Display quiz attempt interface
     */
    public function attempt(QuizAttempt $attempt)
    {
        $student = Auth::user();

        // Verify ownership
        if ($attempt->student_id !== $student->id) {
            abort(403, 'Unauthorized access.');
        }

        // Check if attempt is still in progress
        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.quizzes.result', $attempt)
                ->with('info', 'This attempt has already been submitted.');
        }

        // Check time limit
        $timeElapsed = now()->diffInMinutes($attempt->started_at);
        $timeRemaining = $attempt->quiz->duration - $timeElapsed;

        if ($timeRemaining <= 0) {
            return $this->autoSubmit($attempt);
        }

        // Load questions
        $quiz = $attempt->quiz->load(['questions' => function($query) {
            $query->with('options');
        }]);

        // Randomize questions if enabled
        $questions = $quiz->randomize_questions
            ? $quiz->questions->shuffle()
            : $quiz->questions;

        return $this->renderView('modules.quizzes.student.attempt', [
            'quiz' => $quiz,
            'attempt' => $attempt,
            'questions' => $questions,
            'timeRemaining' => $timeRemaining,
            'showNavbar' => false, // Hide navbar during quiz
            'showSidebar' => false,
            'showFooter' => false,
        ]);
    }

    /**
     * Save quiz answer
     */
    public function saveAnswer(Request $request, QuizAttempt $attempt)
    {
        $student = Auth::user();

        if ($attempt->student_id !== $student->id || $attempt->status !== 'in_progress') {
            return response()->json(['error' => 'Invalid attempt'], 403);
        }

        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'nullable'
        ]);

        $answers = $attempt->answers ?? [];
        $answers[$request->question_id] = $request->answer;

        $attempt->update([
            'answers' => $answers,
            'time_spent' => now()->diffInSeconds($attempt->started_at)
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Submit quiz attempt
     */
    public function submit(Request $request, QuizAttempt $attempt)
    {
        $student = Auth::user();

        if ($attempt->student_id !== $student->id || $attempt->status !== 'in_progress') {
            return redirect()->route('student.quizzes.index')
                ->with('error', 'Invalid attempt.');
        }

        return DB::transaction(function () use ($attempt) {
            // Calculate score
            $score = $this->calculateScore($attempt);

            $attempt->update([
                'submitted_at' => now(),
                'total_score' => $score,
                'status' => 'submitted',
                'time_spent' => now()->diffInSeconds($attempt->started_at)
            ]);

            return redirect()->route('student.quizzes.result', $attempt)
                ->with('success', 'Quiz submitted successfully!');
        });
    }

    /**
     * Auto-submit when time expires
     */
    private function autoSubmit(QuizAttempt $attempt)
    {
        $score = $this->calculateScore($attempt);

        $attempt->update([
            'submitted_at' => now(),
            'total_score' => $score,
            'status' => 'submitted',
            'time_spent' => $attempt->quiz->duration * 60 // Convert to seconds
        ]);

        return redirect()->route('student.quizzes.result', $attempt)
            ->with('info', 'Time expired! Your quiz has been automatically submitted.');
    }

    /**
     * Calculate quiz score
     */
    private function calculateScore(QuizAttempt $attempt)
    {
        $score = 0;
        $answers = $attempt->answers ?? [];
        $quiz = $attempt->quiz->load('questions.options');

        foreach ($quiz->questions as $question) {
            $studentAnswer = $answers[$question->id] ?? null;
            $points = $question->pivot->points ?? $question->points;

            if ($this->isAnswerCorrect($question, $studentAnswer)) {
                $score += $points;
            }
        }

        return $score;
    }

    /**
     * Check if answer is correct (using same logic as exams)
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
                // For short answer, we might want more flexible matching
                $expected = strtolower(trim($question->correct_answer));
                $given = strtolower(trim($studentAnswer));
                return $expected === $given;

            default:
                return false; // Essay and fill_blank need manual grading
        }
    }

    /**
     * Evaluate MCQ answer (same as exams controller)
     */
    private function evaluateMcqAnswer($question, $studentAnswer)
    {
        // Use options table to find correct answer
        if ($question->relationLoaded('options') && $question->options->isNotEmpty()) {
            // Find the correct option
            $correctOption = $question->options->where('is_correct', true)->first();

            if ($correctOption) {
                // Compare student answer with correct option ID
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

    /**
     * Display quiz results
     */
    /**
     * Display quiz results
     */
    public function result(QuizAttempt $attempt)
    {
        $student = Auth::user();

        if ($attempt->student_id !== $student->id) {
            abort(403, 'Unauthorized access.');
        }

        if ($attempt->status !== 'submitted') {
            return redirect()->route('student.quizzes.attempt', $attempt);
        }

        $quiz = $attempt->quiz->load(['questions.options', 'questions' => function($query) {
            $query->with('options');
        }]);

        $answers = $attempt->answers ?? [];

        // Pre-calculate question results
        $questionResults = [];
        $correctAnswers = 0;

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

        return $this->renderView('modules.quizzes.student.result', [
            'quiz' => $quiz,
            'attempt' => $attempt,
            'answers' => $answers,
            'questionResults' => $questionResults,
            'correctAnswers' => $correctAnswers,
            'totalQuestions' => $quiz->questions->count(),
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Display quiz statistics and progress
     */
    public function progress()
    {
        $student = Auth::user();

        $recentAttempts = QuizAttempt::with(['quiz', 'quiz.subject'])
            ->where('student_id', $student->id)
            ->where('status', 'submitted')
            ->latest()
            ->limit(5)
            ->get();

        $subjectStats = QuizAttempt::with('quiz.subject')
            ->where('student_id', $student->id)
            ->where('status', 'submitted')
            ->get()
            ->groupBy('quiz.subject.name')
            ->map(function($attempts, $subject) {
                return [
                    'subject' => $subject,
                    'attempts' => $attempts->count(),
                    'average_score' => $attempts->avg('total_score') ?? 0,
                    'highest_score' => $attempts->max('total_score') ?? 0,
                ];
            });

        $overallStats = [
            'total_quizzes' => QuizAttempt::where('student_id', $student->id)->count(),
            'completed_quizzes' => QuizAttempt::where('student_id', $student->id)
                ->where('status', 'submitted')
                ->count(),
            'average_score' => QuizAttempt::where('student_id', $student->id)
                    ->where('status', 'submitted')
                    ->avg('total_score') ?? 0,
            'total_time_spent' => QuizAttempt::where('student_id', $student->id)
                ->where('status', 'submitted')
                ->sum('time_spent'),
        ];

        return $this->renderView('modules.quizzes.student.progress', [
            'recentAttempts' => $recentAttempts,
            'subjectStats' => $subjectStats,
            'overallStats' => $overallStats,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Check if student can take the quiz (similar to exams)
     */
    private function canStudentTakeQuiz(Quiz $quiz, $student)
    {
        $now = now();

        // Check if quiz is within time window
        if ($now < $quiz->start_time || $now > $quiz->end_time) {
            return false;
        }

        // Check if student has already completed the quiz
        $completedAttempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->whereIn('status', ['submitted', 'graded'])
            ->exists();

        if ($completedAttempt) {
            return false;
        }

        // Check attempt limit
        $attemptCount = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->whereIn('status', ['submitted', 'graded'])
            ->count();

        // Use 1 as default max attempts for quizzes
        if ($attemptCount >= 1) {
            return false;
        }

        return true;
    }

    /**
     * Display quiz attempt details
     */

}
