<?php

namespace App\Http\Controllers\Modules\Assignments;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Assignment;
use App\Models\Assessment\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentAssignmentsController extends Controller
{
    /**
     * Display student's assignments
     */
    public function index()
    {
        $user = auth()->user();

        // Get assignments for student's class that are published
        $assignments = Assignment::where('class_id', $user->class_id)
            ->where('is_published', true)
            ->with(['subject', 'teacher'])
            ->with(['submissions' => function($query) use ($user) {
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

        // FIX: Create a student-specific index view or use shared one
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
    public function show(Assignment $assignment)
    {
        // Verify student is in the correct class
        if ($assignment->class_id !== auth()->user()->class_id) {
            abort(403, 'Unauthorized action.');
        }

        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', auth()->id())
            ->first();

        // FIX: Use the shared assignments show view
        return $this->renderView('modules.assignments.show', [
            'assignment' => $assignment,
            'submission' => $submission,
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
        // Verify student is in the correct class
        if ($assignment->class_id !== auth()->user()->class_id) {
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
     * Download own submission
     */
    public function downloadSubmission(Assignment $assignment, AssignmentSubmission $submission)
    {
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
        $submissions = AssignmentSubmission::where('student_id', auth()->id())
            ->with(['assignment.subject', 'assignment.teacher', 'grader'])
            ->latest()
            ->paginate(10);

        // FIX: Create a student submissions view or use shared one
        return $this->renderView('modules.assignments.submissions.student-index', [
            'submissions' => $submissions,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }
}
