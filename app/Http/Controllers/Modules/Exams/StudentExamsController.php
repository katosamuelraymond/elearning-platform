<?php

namespace App\Http\Controllers\Modules\Exams;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Exam;
use App\Models\Assessment\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Debug: Check student's class assignment
        \Log::info('🔍 STUDENT DEBUG INFO:', [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'has_class_assignment' => !is_null($currentAssignment),
            'class_assignment_details' => $currentAssignment ? [
                'class_id' => $currentAssignment->class_id,
                'class_name' => $currentAssignment->class->name ?? 'No class',
                'stream_id' => $currentAssignment->stream_id,
                'stream_name' => $currentAssignment->stream->name ?? 'No stream',
                'academic_year' => $currentAssignment->academic_year
            ] : null
        ]);

        // Check if student has an active class assignment
        if (!$currentAssignment) {
            \Log::warning('❌ Student has no active class assignment!', [
                'student_id' => $student->id,
                'student_name' => $student->name
            ]);

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
        $query = Exam::with(['subject', 'class', 'attempts' => function($q) use ($student) {
            $q->where('student_id', $student->id);
        }])
            ->where('class_id', $currentAssignment->class_id)
            ->where('is_published', true)
            ->where('is_archived', false);

        // Debug: Check what exams exist for this class
        $allExamsForClass = Exam::where('class_id', $currentAssignment->class_id)->get();
        \Log::info('📊 EXAMS FOR CLASS ' . $currentAssignment->class_id . ':', [
            'total_exams_in_class' => $allExamsForClass->count(),
            'published_exams' => $allExamsForClass->where('is_published', true)->count(),
            'exam_details' => $allExamsForClass->map(function($exam) {
                return [
                    'id' => $exam->id,
                    'title' => $exam->title,
                    'class_id' => $exam->class_id,
                    'is_published' => $exam->is_published,
                    'is_archived' => $exam->is_archived
                ];
            })->toArray()
        ]);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            switch ($request->status) {
                case 'available':
                    $query->where(function($q) use ($student) {
                        $q->whereDoesntHave('attempts', function($q2) use ($student) {
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
                    $query->whereHas('attempts', function($q) use ($student) {
                        $q->where('student_id', $student->id)
                            ->where('status', 'in_progress');
                    });
                    break;
                case 'completed':
                    $query->whereHas('attempts', function($q) use ($student) {
                        $q->where('student_id', $student->id)
                            ->whereIn('status', ['submitted', 'graded']);
                    });
                    break;
                case 'missed':
                    $query->where('end_time', '<', now())
                        ->whereDoesntHave('attempts', function($q) use ($student) {
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
                ->where(function($q) use ($student) {
                    $q->whereDoesntHave('attempts', function($q2) use ($student) {
                        $q2->where('student_id', $student->id);
                    })
                        ->where('start_time', '<=', now())
                        ->where('end_time', '>=', now());
                })->count(),
            'in_progress' => Exam::where('class_id', $currentAssignment->class_id)
                ->where('is_published', true)
                ->where('is_archived', false)
                ->whereHas('attempts', function($q) use ($student) {
                    $q->where('student_id', $student->id)
                        ->where('status', 'in_progress');
                })->count(),
            'completed' => Exam::where('class_id', $currentAssignment->class_id)
                ->where('is_published', true)
                ->where('is_archived', false)
                ->whereHas('attempts', function($q) use ($student) {
                    $q->where('student_id', $student->id)
                        ->whereIn('status', ['submitted', 'graded']);
                })->count(),
        ];

        // Get subjects that have exams for this student's class
        $subjects = \App\Models\Academic\Subject::whereHas('exams', function($q) use ($currentAssignment) {
            $q->where('class_id', $currentAssignment->class_id)
                ->where('is_published', true)
                ->where('is_archived', false);
        })->get();

        // Final debug log
        \Log::info('🎯 FINAL RESULTS:', [
            'student_class_id' => $currentAssignment->class_id,
            'exams_found' => $exams->count(),
            'stats' => $stats
        ]);

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

        // Create new attempt
        $attempt = $exam->attempts()->create([
            'student_id' => $student->id,
            'start_time' => now(),
            'status' => 'in_progress',
            'total_score' => 0,
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

        // Check if exam is still within time limits
        if (now()->gt($exam->end_time)) {
            // Auto-submit if time has expired
            $this->autoSubmitAttempt($exam, $attempt);
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'Exam time has expired. Your attempt has been automatically submitted.');
        }

        $exam->load(['questions' => function($query) {
            $query->with('options')->orderBy('position');
        }, 'subject', 'class']);

        // Load saved answers
        $savedAnswers = $attempt->answers()->pluck('answer', 'question_id')->toArray();

        return $this->renderView('modules.exams.take', [
            'exam' => $exam,
            'attempt' => $attempt,
            'savedAnswers' => $savedAnswers,
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

        // Save answers to attempt
        foreach ($answers as $questionId => $answer) {
            $attempt->answers()->updateOrCreate(
                ['question_id' => $questionId],
                ['answer' => $answer, 'updated_at' => now()]
            );
        }

        // Update attempt updated_at timestamp
        $attempt->touch();

        \Log::info('💾 EXAM PROGRESS SAVED:', [
            'attempt_id' => $attempt->id,
            'saved_answers' => count($answers),
            'student_id' => $student->id
        ]);

        return response()->json([
            'success' => true,
            'saved_count' => count($answers),
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

        $answers = $attempt->answers()->pluck('answer', 'question_id')->toArray();

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

        // Save final answers if provided
        if ($request->has('answers')) {
            $answers = json_decode($request->answers, true) ?? [];
            foreach ($answers as $questionId => $answer) {
                $attempt->answers()->updateOrCreate(
                    ['question_id' => $questionId],
                    ['answer' => $answer]
                );
            }
        }

        // Calculate score
        $score = $this->calculateScore($exam, $attempt);

        // Update attempt
        $attempt->update([
            'end_time' => now(),
            'status' => 'submitted',
            'total_score' => $score,
            'submitted_at' => now(),
        ]);

        // Log the submission
        \Log::info('📝 EXAM SUBMITTED:', [
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'attempt_id' => $attempt->id,
            'score' => $score,
            'total_marks' => $exam->total_marks
        ]);

        return redirect()->route('student.exams.results', ['exam' => $exam, 'attempt' => $attempt])
            ->with('success', 'Exam submitted successfully!');
    }

    /**
     * Display exam results
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
            $query->with('options');
        }]);

        $answers = $attempt->answers()->pluck('answer', 'question_id')->toArray();

        return $this->renderView('modules.exams.results', [
            'exam' => $exam,
            'attempt' => $attempt,
            'answers' => $answers,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true,
        ]);
    }

    /**
     * Display student's exam history
     */
    public function myAttempts(Request $request)
    {
        $student = Auth::user();

        $attempts = ExamAttempt::with(['exam' => function($query) {
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

        if ($completedAttempt && !$exam->allow_multiple_attempts) {
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
     * Calculate exam score
     */
    private function calculateScore(Exam $exam, $attempt)
    {
        $score = 0;
        $answers = $attempt->answers;

        foreach ($exam->questions as $question) {
            $studentAnswer = $answers->where('question_id', $question->id)->first();

            if (!$studentAnswer) continue;

            $score += $this->evaluateAnswer($question, $studentAnswer->answer);
        }

        return $score;
    }

    /**
     * Evaluate individual answer
     */
    private function evaluateAnswer($question, $studentAnswer)
    {
        if ($question->type === 'multiple_choice') {
            return $studentAnswer === $question->correct_answer ? $question->marks : 0;
        } elseif ($question->type === 'true_false') {
            return $studentAnswer === $question->correct_answer ? $question->marks : 0;
        } else {
            // For essay questions, return full marks for now (manual grading required)
            return !empty(trim($studentAnswer)) ? $question->marks : 0;
        }
    }

    /**
     * Auto-submit attempt when time expires
     */
    private function autoSubmitAttempt(Exam $exam, $attempt)
    {
        $score = $this->calculateScore($exam, $attempt);

        $attempt->update([
            'end_time' => now(),
            'status' => 'submitted',
            'total_score' => $score,
            'submitted_at' => now(),
        ]);

        \Log::info('⏰ AUTO-SUBMITTED EXAM:', [
            'attempt_id' => $attempt->id,
            'reason' => 'Time expired',
            'score' => $score
        ]);
    }
}
