<?php

namespace App\Http\Controllers\Modules\Exams;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Exam;
use App\Models\Assessment\ExamAttempt;
use App\Models\Academic\StudentClassAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class StudentExamsController extends Controller
{
    /**
     * Helper method to get the student's current class ID
     */
    protected function getCurrentClassId()
    {
        $studentClassAssignment = StudentClassAssignment::where('student_id', auth()->id())
            ->where('status', 'active')
            ->latest()
            ->first();

        return $studentClassAssignment ? $studentClassAssignment->class_id : null;
    }

    /**
     * Display student's exams
     */
    public function index()
    {
        $currentClassId = $this->getCurrentClassId();

        if (!$currentClassId) {
            $exams = Exam::whereRaw('1=0')->paginate(10);
            $stats = ['total' => 0, 'upcoming' => 0, 'completed' => 0, 'available' => 0];
        } else {
            $exams = Exam::where('class_id', $currentClassId)
                ->where('is_published', true)
                ->with(['subject', 'teacher'])
                ->with(['attempts' => function($query) {
                    $query->where('student_id', auth()->id());
                }])
                ->latest()
                ->paginate(10);

            $stats = [
                'total' => $exams->total(),
                'upcoming' => Exam::where('class_id', $currentClassId)
                    ->where('is_published', true)
                    ->where('start_time', '>', now())
                    ->count(),
                'completed' => ExamAttempt::where('student_id', auth()->id())
                    ->whereIn('status', ['submitted', 'graded'])
                    ->count(),
                'available' => Exam::where('class_id', $currentClassId)
                    ->where('is_published', true)
                    ->where('start_time', '<=', now())
                    ->where('end_time', '>=', now())
                    ->count(),
            ];
        }

        return $this->renderView('modules.exams.student-index', [
            'exams' => $exams,
            'stats' => $stats,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Show exam details and start attempt
     */
    public function show(Exam $exam)
    {
        $currentClassId = $this->getCurrentClassId();

        // Verify student is in the correct class
        if (!$currentClassId || $exam->class_id !== $currentClassId) {
            abort(403, 'Unauthorized action.');
        }

        // Verify exam is published
        if (!$exam->is_published) {
            abort(404, 'Exam not found or not published.');
        }

        // Eager load questions for showing details
        $exam->load('questions');

        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', auth()->id())
            ->first();

        $canTakeExam = $this->canTakeExam($exam, $attempt);

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
     * Check if student can take the exam
     */
    private function canTakeExam(Exam $exam, $existingAttempt)
    {
        // Check if exam is active
        if (now() < $exam->start_time || now() > $exam->end_time) {
            return false;
        }

        // Check max attempts
        if ($existingAttempt && $exam->attempts()->where('student_id', auth()->id())->count() >= $exam->max_attempts) {
            return false;
        }

        // Check if there's an existing in-progress attempt
        if ($existingAttempt && $existingAttempt->status === 'in_progress') {
            return true;
        }

        return true;
    }

    /**
     * Start exam attempt
     */
    public function start(Exam $exam)
    {
        $currentClassId = $this->getCurrentClassId();

        // Verify student is in the correct class
        if (!$currentClassId || $exam->class_id !== $currentClassId) {
            abort(403, 'Unauthorized action.');
        }

        // Check if student can take exam
        $existingAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', auth()->id())
            ->first();

        if (!$this->canTakeExam($exam, $existingAttempt)) {
            return redirect()->back()->with('error', 'You cannot take this exam at this time.');
        }

        // Create or resume attempt
        if ($existingAttempt && $existingAttempt->status === 'in_progress') {
            $attempt = $existingAttempt;
        } else {
            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'student_id' => auth()->id(),
                'started_at' => now(),
                'status' => 'in_progress',
            ]);
        }

        return redirect()->route('student.exams.take', ['exam' => $exam, 'attempt' => $attempt]);
    }

    /**
     * Take exam (exam interface)
     */
    public function take(Exam $exam, ExamAttempt $attempt)
    {
        // Verify student owns this attempt
        if ($attempt->student_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Verify attempt is in progress
        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'This exam attempt has already been submitted.');
        }

        // Eager load questions
        $exam->load('questions');

        return $this->renderView('modules.exams.take', [
            'exam' => $exam,
            'attempt' => $attempt,
            'showNavbar' => false, // Hide navbar during exam
            'showSidebar' => false, // Hide sidebar during exam
            'showFooter' => false, // Hide footer during exam
        ]);
    }

    /**
     * Submit exam attempt
     */
    public function submit(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        // Verify student owns this attempt
        if ($attempt->student_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Simple validation for answers array structure
        $request->validate([
            'answers' => 'nullable|array',
            'answers.*' => 'nullable', // Allow dynamic content within the answers array
        ]);

        $attempt->update([
            'submitted_at' => now(),
            'status' => 'submitted',
            // Ensure answers are captured as a structured array for JSON column
            'answers' => $request->input('answers', []),
            'time_spent' => now()->diffInSeconds($attempt->started_at),
        ]);

        return redirect()->route('student.exams.show', $exam)
            ->with('success', 'Exam submitted successfully!');
    }

    /**
     * View exam results
     */
    public function results(Exam $exam, ExamAttempt $attempt)
    {
        // Verify student owns this attempt
        if ($attempt->student_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Verify exam shows results
        if (!$exam->show_results && $attempt->status !== 'graded') {
            abort(403, 'Results are not available yet.');
        }

        // Eager load questions for result analysis
        $exam->load('questions');

        return $this->renderView('modules.exams.results', [
            'exam' => $exam,
            'attempt' => $attempt,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * View exam history
     */
    public function myAttempts()
    {
        $attempts = ExamAttempt::where('student_id', auth()->id())
            ->with(['exam.subject', 'exam.teacher'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => $attempts->total(),
            'completed' => ExamAttempt::where('student_id', auth()->id())
                ->whereIn('status', ['submitted', 'graded'])
                ->count(),
            'in_progress' => ExamAttempt::where('student_id', auth()->id())
                ->where('status', 'in_progress')
                ->count(),
        ];

        return $this->renderView('modules.exams.attempts.student-index', [
            'attempts' => $attempts,
            'stats' => $stats,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }
}
