<?php

namespace App\Http\Controllers\Modules\Assignments;

use App\Http\Controllers\Controller;
use App\Models\Academic\StudentClassAssignment; // 🛑 NEW: Required to find the student's class ID
use App\Models\Assessment\Assignment;
use App\Models\Assessment\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentAssignmentsController extends Controller
{
    /**
     * Helper method to get the student's current class ID from the academic assignment table.
     * @return int|null
     */
    protected function getCurrentClassId()
    {
        // Find the latest active class assignment for the student.
        $studentClassAssignment = StudentClassAssignment::where('student_id', auth()->id())
            ->where('status', 'active') // Only consider currently active class assignments
            ->latest() // Get the latest record (in case of overlap or data issues)
            ->first();

        // Return the class_id or null if the student isn't assigned to an active class.
        return $studentClassAssignment ? $studentClassAssignment->class_id : null;
    }

    /**
     * Display student's assignments
     */
    public function index()
    {
        $user = auth()->user();
        $currentClassId = $this->getCurrentClassId(); // 🛑 FIX: Get class ID correctly

        // 🛑 Handle case where student has no active class (should return empty list)
        if (!$currentClassId) {
            $assignments = Assignment::whereRaw('1=0')->paginate(10); // Returns an empty paginated collection
            $stats = ['total' => 0, 'submitted' => 0, 'graded' => 0, 'pending' => 0];
        } else {
            // Get assignments for student's class that are published
            // 🛑 FIX: Use $currentClassId for filtering
            $assignments = Assignment::where('class_id', $currentClassId)
                ->where('is_published', true)
                ->with(['subject', 'teacher'])
                ->with(['submissions' => function ($query) use ($user) {
                    $query->where('student_id', $user->id);
                }])
                ->latest()
                ->paginate(10);

            $stats = [
                'total' => $assignments->total(),
                'submitted' => AssignmentSubmission::where('student_id', $user->id)->count(),
                'graded' => AssignmentSubmission::where('student_id', $user->id)->where('status', 'graded')->count(),
                'pending' => AssignmentSubmission::where('student_id', $user->id)->whereIn('status', ['submitted', 'late'])->count(),
            ];
        }

        return $this->renderView('modules.assignments.student-index', [
            'assignments' => $assignments,
            'stats' => $stats,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Show assignment details and submission form
     */
// In App\Http\Controllers\Modules\Assignments\StudentAssignmentsController.php

    /**
     * Show assignment details and submission form
     */
    public function show(Assignment $assignment)
    {
        $currentClassId = $this->getCurrentClassId();

        // Verify student is in the correct class
        if (!$currentClassId || $assignment->class_id !== $currentClassId) {
            abort(403, 'Unauthorized action or assignment not for your current class.');
        }

        // Verify assignment is published
        if (!$assignment->is_published) {
            abort(404, 'Assignment not found or not published.');
        }

        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', auth()->id())
            ->first();

        // 🛑 FIX: Render the student-specific view
        return $this->renderView('modules.assignments.student-show', [
            'assignment' => $assignment,
            'submission' => $submission,
            'isStudent' => true,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Submit assignment
     */
    public function submit(Request $request, Assignment $assignment)
    {
        $currentClassId = $this->getCurrentClassId(); // 🛑 FIX: Get class ID correctly

        // 🛑 FIX: Verify student is in the correct class
        if (!$currentClassId || $assignment->class_id !== $currentClassId) {
            abort(403, 'Unauthorized action.');
        }

        // Validate assignment availability
        if (!$assignment->is_published) {
            return redirect()->back()->with('error', 'This assignment is not available for submission.');
        }

        $request->validate([
            'submission_file' => [
                'required',
                'file',
                'max:' . ($assignment->max_file_size * 1024),
                function ($attribute, $value, $fail) use ($assignment) {
                    $extension = strtolower($value->getClientOriginalExtension());
                    $allowedFormats = $assignment->allowed_formats ?? [];

                    if (!in_array($extension, $allowedFormats)) {
                        $fail("The file must be one of the following formats: " . implode(', ', $allowedFormats));
                    }
                }
            ],
            'submission_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $file = $request->file('submission_file');
            $extension = $file->getClientOriginalExtension();

            // Generate unique filename
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $uniqueFileName = Str::slug($originalName) . '_' . time() . '.' . $extension;

            // Store file
            $filePath = $file->storeAs(
                "assignments/{$assignment->id}/submissions",
                $uniqueFileName,
                'public'
            );

            // Check if submission is late
            $status = now()->gt($assignment->due_date) ? 'late' : 'submitted';

            // Create or update submission
            AssignmentSubmission::updateOrCreate(
                [
                    'assignment_id' => $assignment->id,
                    'student_id' => auth()->id(),
                ],
                [
                    'submission_file' => $filePath,
                    'original_filename' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'submission_notes' => $request->submission_notes,
                    'submitted_at' => now(),
                    'status' => $status,
                    // Reset grading info if resubmitting
                    'points_obtained' => null,
                    'feedback' => null,
                    'graded_by' => null,
                    'graded_at' => null,
                ]
            );

            return redirect()->back()->with('success', 'Assignment submitted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to submit assignment: ' . $e->getMessage());
        }
    }

    /**
     * Download assignment file (teacher's file)
     */
    public function downloadAssignment(Assignment $assignment)
    {
        $currentClassId = $this->getCurrentClassId(); // 🛑 FIX: Get class ID correctly

        // 🛑 FIX: Verify student is in the correct class
        if (!$currentClassId || $assignment->class_id !== $currentClassId) {
            abort(403, 'Unauthorized action.');
        }

        if (!$assignment->assignment_file || !Storage::disk('public')->exists($assignment->assignment_file)) {
            abort(404, 'Assignment file not found.');
        }

        return Storage::disk('public')->download(
            $assignment->assignment_file,
            $assignment->original_filename ?? 'assignment_file.' . pathinfo($assignment->assignment_file, PATHINFO_EXTENSION)
        );
    }

    /**
     * Download own submission
     */
    public function downloadSubmission(Assignment $assignment, AssignmentSubmission $submission)
    {
        // NOTE: This method does NOT need $currentClassId because it checks ownership directly.

        // Check if student owns this submission
        if ($submission->student_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!Storage::disk('public')->exists($submission->submission_file)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download(
            $submission->submission_file,
            $submission->original_filename
        );
    }

    /**
     * View submission history
     */
    public function mySubmissions()
    {
        // NOTE: This method is inherently student-specific and does not rely on class ID for filtering.
        $submissions = AssignmentSubmission::where('student_id', auth()->id())
            ->with(['assignment.subject', 'assignment.teacher', 'grader'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => $submissions->total(),
            'graded' => AssignmentSubmission::where('student_id', auth()->id())->where('status', 'graded')->count(),
            'pending' => AssignmentSubmission::where('student_id', auth()->id())->whereIn('status', ['submitted', 'late'])->count(),
        ];


        return $this->renderView('modules.assignments.submissions.student-index', [
            'submissions' => $submissions,
            'stats' => $stats,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }
}
