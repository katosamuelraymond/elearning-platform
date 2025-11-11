<?php

namespace App\Http\Controllers\Modules\Exams;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Exam;
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

        // Debug: Check ALL student data
        \Log::info('🔍 STUDENT DEBUG INFO:', [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'student_email' => $student->email,
            'student_class_id' => $student->class_id,
            'student_class_name' => $student->class->name ?? 'No class',
            'all_student_attributes' => $student->toArray()
        ]);

        // Check if student has a class assigned
        if (!$student->class_id) {
            \Log::warning('❌ Student has no class assigned!', [
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
            ->where('class_id', $student->class_id)
            ->where('is_published', true)
            ->where('is_archived', false);

        // Debug: Check what exams exist for this class
        $allExamsForClass = Exam::where('class_id', $student->class_id)->get();
        \Log::info('📊 EXAMS FOR CLASS ' . $student->class_id . ':', [
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
                    $query->availableForStudent($student);
                    break;
                case 'upcoming':
                    $query->where('start_time', '>', now());
                    break;
                case 'in_progress':
                    $query->inProgressByStudent($student);
                    break;
                case 'completed':
                    $query->completedByStudent($student);
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
            'total' => Exam::where('class_id', $student->class_id)
                ->where('is_published', true)
                ->where('is_archived', false)
                ->count(),
            'available' => Exam::availableForStudent($student)->count(),
            'in_progress' => Exam::inProgressByStudent($student)->count(),
            'completed' => Exam::completedByStudent($student)->count(),
        ];

        // Get subjects that have exams for this student's class
        $subjects = \App\Models\Academic\Subject::whereHas('exams', function($q) use ($student) {
            $q->where('class_id', $student->class_id)
                ->where('is_published', true)
                ->where('is_archived', false);
        })->get();

        // Final debug log
        \Log::info('🎯 FINAL RESULTS:', [
            'student_class_id' => $student->class_id,
            'exams_found' => $exams->count(),
            'stats' => $stats,
            'query_sql' => $query->toSql()
        ]);

        return $this->renderView('modules.exams.student-index', [
            'exams' => $exams,
            'stats' => $stats,
            'subjects' => $subjects,
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

        // Check if student can access this exam using model method
        if (!$exam->isAssignedToStudent($student)) {
            abort(403, 'This exam is not assigned to you.');
        }

        $exam->load(['subject', 'class', 'teacher', 'questions']);

        $attempts = $exam->attempts()
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        $canTakeExam = $exam->canStudentAttempt($student);
        $nextAttemptAvailable = $exam->isNextAttemptAvailable($student);

        return $this->renderView('modules.exams.student-show', [
            'exam' => $exam,
            'attempts' => $attempts,
            'canTakeExam' => $canTakeExam,
            'nextAttemptAvailable' => $nextAttemptAvailable,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }
}
