<?php

namespace App\Http\Controllers\Modules\Subjects;

use App\Http\Controllers\Controller;
use App\Models\Academic\Subject;
use App\Models\Academic\StudentSubject;
use App\Models\Academic\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentSubjectsController extends Controller
{
    /**
     * Display a listing of available subjects for student.
     */
    public function index(Request $request)
    {
        $student = Auth::user()->student;

        // Get all active subjects
        $query = Subject::where('is_active', true);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Get paginated subjects
        $subjects = $query->latest()->paginate(12);

        // Get student's enrolled subjects
        $enrolledSubjects = $student->subjects()->wherePivot('status', 'enrolled')->get();
        $enrolledSubjectIds = $enrolledSubjects->pluck('id')->toArray();

        // Get student's completed subjects
        $completedSubjects = $student->subjects()->wherePivot('status', 'completed')->get();
        $completedSubjectIds = $completedSubjects->pluck('id')->toArray();

        $data = [
            'subjects' => $subjects,
            'enrolledSubjects' => $enrolledSubjects,
            'enrolledSubjectIds' => $enrolledSubjectIds,
            'completedSubjects' => $completedSubjects,
            'completedSubjectIds' => $completedSubjectIds,
            'filters' => $request->only(['search', 'type'])
        ];

        // AJAX request → return only #main-content
        if ($request->ajax()) {
            return view('modules.student.subjects.partials.subjects-grid', $data)
                ->renderSections()['content'];
        }

        // Normal full page load
        return view('modules.student.subjects.index', $data)
            ->with([
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true,
                'pageTitle' => 'Available Subjects'
            ]);
    }

    /**
     * Display student's enrolled subjects.
     */
    public function mySubjects(Request $request)
    {
        $student = Auth::user()->student;

        $query = $student->subjects()->where('is_active', true);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->wherePivot('status', $request->status);
        }

        $subjects = $query->latest()->paginate(10);

        $data = [
            'subjects' => $subjects,
            'filters' => $request->only(['search', 'type', 'status'])
        ];

        // AJAX request → return only #main-content
        if ($request->ajax()) {
            return view('modules.student.subjects.partials.my-subjects-table', $data)
                ->renderSections()['content'];
        }

        return view('modules.student.subjects.my-subjects', $data)
            ->with([
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true,
                'pageTitle' => 'My Subjects'
            ]);
    }

    /**
     * Show subject details for student.
     */
    public function show(Request $request, Subject $subject)
    {
        // Check if subject is active
        if (!$subject->is_active) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This subject is not available.'
                ], 404);
            }
            return back()->with('error', 'This subject is not available.');
        }

        $student = Auth::user()->student;

        // Check if student is enrolled in this subject
        $isEnrolled = $student->subjects()
            ->where('subject_id', $subject->id)
            ->wherePivot('status', 'enrolled')
            ->exists();

        // Check if student has completed this subject
        $isCompleted = $student->subjects()
            ->where('subject_id', $subject->id)
            ->wherePivot('status', 'completed')
            ->exists();

        $data = [
            'subject' => $subject,
            'isEnrolled' => $isEnrolled,
            'isCompleted' => $isCompleted
        ];

        // AJAX request → return only #main-content
        if ($request->ajax()) {
            return view('modules.student.subjects.partials.subject-details', $data)
                ->renderSections()['content'];
        }

        return view('modules.student.subjects.show', $data)
            ->with([
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true,
                'pageTitle' => $subject->name
            ]);
    }

    /**
     * Enroll student in a subject.
     */
    public function enroll(Request $request, Subject $subject)
    {
        $student = Auth::user()->student;

        // Check if subject is active
        if (!$subject->is_active) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot enroll in an inactive subject.'
                ], 400);
            }
            return back()->with('error', 'Cannot enroll in an inactive subject.');
        }

        // Check if already enrolled
        $existingEnrollment = $student->subjects()
            ->where('subject_id', $subject->id)
            ->whereIn('status', ['enrolled', 'completed'])
            ->exists();

        if ($existingEnrollment) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are already enrolled in this subject.'
                ], 400);
            }
            return back()->with('error', 'You are already enrolled in this subject.');
        }

        try {
            DB::beginTransaction();

            // Enroll student in subject
            $student->subjects()->attach($subject->id, [
                'status' => 'enrolled',
                'enrolled_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            $message = 'Successfully enrolled in ' . $subject->name . '!';

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => route('student.subjects.show', $subject)
                ]);
            }

            return redirect()->route('student.subjects.show', $subject)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to enroll in subject. Please try again.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return back()->with('error', 'Failed to enroll in subject. Please try again.');
        }
    }

    /**
     * Unenroll student from a subject.
     */
    public function unenroll(Request $request, Subject $subject)
    {
        $student = Auth::user()->student;

        try {
            DB::beginTransaction();

            // Remove enrollment
            $student->subjects()->detach($subject->id);

            DB::commit();

            $message = 'Successfully unenrolled from ' . $subject->name . '!';

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => route('student.subjects.index')
                ]);
            }

            return redirect()->route('student.subjects.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to unenroll from subject. Please try again.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return back()->with('error', 'Failed to unenroll from subject. Please try again.');
        }
    }

    /**
     * Mark subject as completed.
     */
    public function markCompleted(Request $request, Subject $subject)
    {
        $student = Auth::user()->student;

        // Check if enrolled
        $enrollment = $student->subjects()
            ->where('subject_id', $subject->id)
            ->wherePivot('status', 'enrolled')
            ->first();

        if (!$enrollment) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not enrolled in this subject or it is already completed.'
                ], 400);
            }
            return back()->with('error', 'You are not enrolled in this subject or it is already completed.');
        }

        try {
            DB::beginTransaction();

            // Update enrollment status to completed
            $student->subjects()->updateExistingPivot($subject->id, [
                'status' => 'completed',
                'completed_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            $message = 'Subject marked as completed: ' . $subject->name . '!';

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'is_completed' => true
                ]);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to mark subject as completed. Please try again.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return back()->with('error', 'Failed to mark subject as completed. Please try again.');
        }
    }

    /**
     * Get student's subject progress.
     */
    public function progress(Request $request)
    {
        $student = Auth::user()->student;

        $enrolledCount = $student->subjects()->wherePivot('status', 'enrolled')->count();
        $completedCount = $student->subjects()->wherePivot('status', 'completed')->count();
        $totalSubjects = Subject::where('is_active', true)->count();

        $progress = $totalSubjects > 0 ? round(($completedCount / $totalSubjects) * 100, 2) : 0;

        $data = [
            'enrolled_count' => $enrolledCount,
            'completed_count' => $completedCount,
            'total_subjects' => $totalSubjects,
            'progress_percentage' => $progress
        ];

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }

        return view('modules.student.subjects.progress', $data)
            ->with([
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true,
                'pageTitle' => 'My Subject Progress'
            ]);
    }
}
