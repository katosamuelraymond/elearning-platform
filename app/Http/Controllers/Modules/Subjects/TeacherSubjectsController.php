<?php

namespace App\Http\Controllers\Modules\Subjects;

use App\Http\Controllers\Controller;
use App\Models\Academic\Subject;
use App\Models\Academic\TeacherAssignment;
use Illuminate\Http\Request;

class TeacherSubjectsController extends Controller
{
    /**
     * Display a listing of the teacher's subjects.
     */
    public function index(Request $request)
    {
        $teacher = auth()->user();
        $currentAcademicYear = '2024-2025';

        // Debug: Check teacher and assignments
        \Log::info('Teacher ID: ' . $teacher->id);

        // Start with base query - ensure we only get assignments with existing subjects
        $assignmentsQuery = TeacherAssignment::where('teacher_id', $teacher->id)
            ->with(['subject', 'class', 'stream'])
            ->whereHas('subject'); // This ensures we only get assignments with valid subjects

        \Log::info('Teacher assignments count with valid subjects: ' . $assignmentsQuery->count());

        // Debug: Check all assignments with their subjects
        $allAssignments = TeacherAssignment::where('teacher_id', $teacher->id)
            ->with(['subject'])
            ->get();

        \Log::info('All assignments with subjects:', $allAssignments->map(function($assignment) {
            return [
                'assignment_id' => $assignment->id,
                'subject_id' => $assignment->subject_id,
                'subject_name' => $assignment->subject ? $assignment->subject->name : 'NULL/INVALID',
                'subject_exists' => $assignment->subject ? 'YES' : 'NO'
            ];
        })->toArray());

        // Apply filters
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $assignmentsQuery->whereHas('subject', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && !empty($request->type)) {
            $type = $request->type;
            $assignmentsQuery->whereHas('subject', function($q) use ($type) {
                $q->where('type', $type);
            });
        }

        $assignments = $assignmentsQuery->get();

        // Manual grouping - ONLY include assignments with valid subjects
        $subjects = [];
        foreach ($assignments as $assignment) {
            // Skip assignments without valid subjects
            if (!$assignment->subject) {
                \Log::warning('Assignment has null subject', [
                    'assignment_id' => $assignment->id,
                    'subject_id' => $assignment->subject_id
                ]);
                continue;
            }

            $subjectId = $assignment->subject_id;

            if (!isset($subjects[$subjectId])) {
                $subjects[$subjectId] = [
                    'subject' => $assignment->subject,
                    'assignments' => [],
                    'classes' => []
                ];
            }

            $subjects[$subjectId]['assignments'][] = $assignment;
            $classInfo = $assignment->class->name .
                ($assignment->stream ? ' - ' . $assignment->stream->name : '');

            if (!in_array($classInfo, $subjects[$subjectId]['classes'])) {
                $subjects[$subjectId]['classes'][] = $classInfo;
            }
        }

        $data = [
            'assignments' => $assignments,
            'subjects' => $subjects,
            'analyticsAssignments' => $assignments,
            'filters' => $request->only(['search', 'type'])
        ];

        // Debug: Final data
        \Log::info('Final subjects count: ' . count($subjects));
        \Log::info('Subjects found:', array_map(function($subjectData) {
            return [
                'subject_id' => $subjectData['subject']->id,
                'subject_name' => $subjectData['subject']->name,
                'assignments_count' => count($subjectData['assignments']),
                'classes' => $subjectData['classes']
            ];
        }, $subjects));

        // AJAX request → return only the table partial (no renderSections)
        if ($request->ajax()) {
            return view('modules.subjects.partials.teacher-table', $data);
        }

        // Normal full page load
        return view('modules.subjects.teacher-index', $data)
            ->with([
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true,
            ]);
    }

    /**
     * Display the specified subject with teacher-specific details.
     */
    public function show(Request $request, Subject $subject)
    {
        $teacher = auth()->user();
        $currentAcademicYear = '2024-2025';

        // Verify the teacher is assigned to this subject
        $assignment = TeacherAssignment::where('teacher_id', $teacher->id)
            ->where('subject_id', $subject->id)
            ->forAcademicYear($currentAcademicYear)
            ->active()
            ->firstOrFail();

        // Load all assignments for this subject
        $assignments = TeacherAssignment::where('teacher_id', $teacher->id)
            ->where('subject_id', $subject->id)
            ->forAcademicYear($currentAcademicYear)
            ->active()
            ->with(['class', 'stream'])
            ->get();

        $data = [
            'subject' => $subject,
            'assignments' => $assignments
        ];

        // AJAX request → return the view directly
        if ($request->ajax()) {
            return view('modules.subjects.teacher-show', $data);
        }

        return view('modules.subjects.teacher-show', $data)
            ->with([
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true,
            ]);
    }

    /**
     * Show lessons for a specific subject.
     */
    public function lessons(Request $request, Subject $subject)
    {
        $teacher = auth()->user();
        $currentAcademicYear = '2024-2025';

        // Verify the teacher is assigned to this subject
        $assignment = TeacherAssignment::where('teacher_id', $teacher->id)
            ->where('subject_id', $subject->id)
            ->forAcademicYear($currentAcademicYear)
            ->active()
            ->firstOrFail();

        // Get lessons for this subject (you can implement this later)
        $lessons = []; // Placeholder for lessons logic

        $data = [
            'subject' => $subject,
            'lessons' => $lessons
        ];

        // AJAX request → return the view directly
        if ($request->ajax()) {
            return view('modules.subjects.teacher-lessons', $data);
        }

        return view('modules.subjects.teacher-lessons', $data)
            ->with([
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true,
            ]);
    }

    /**
     * Group assignments by subject for display
     */
    private function groupAssignmentsBySubject($assignments)
    {
        $grouped = [];

        foreach ($assignments as $assignment) {
            $subjectId = $assignment->subject_id;

            if (!isset($grouped[$subjectId])) {
                $grouped[$subjectId] = [
                    'subject' => $assignment->subject,
                    'assignments' => [],
                    'classes' => []
                ];
            }

            $grouped[$subjectId]['assignments'][] = $assignment;
            $classInfo = $assignment->class->name .
                ($assignment->stream ? ' - ' . $assignment->stream->name : '');

            if (!in_array($classInfo, $grouped[$subjectId]['classes'])) {
                $grouped[$subjectId]['classes'][] = $classInfo;
            }
        }

        return $grouped;
    }
}
