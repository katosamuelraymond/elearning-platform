<?php

namespace App\Http\Controllers\Modules\Assignments;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Assignment;
use App\Models\Assessment\AssignmentSubmission;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminAssignmentsController extends Controller
{
    /**
     * Check if user is admin
     */
    private function checkAdmin()
    {
        if (!Auth::check()) {
            abort(403, 'Authentication required.');
        }

        if (!Auth::user()->isAdmin()) {
            abort(403, 'Admin privileges required. You are not authorized to access this page.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkAdmin();

        $assignments = Assignment::with(['teacher', 'class', 'subject', 'submissions'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => Assignment::count(),
            'published' => Assignment::where('is_published', true)->count(),
            'draft' => Assignment::where('is_published', false)->count(),
            'submitted' => \App\Models\Assessment\AssignmentSubmission::count(),
        ];

        $data = [
            'assignments' => $assignments,
            'stats' => $stats,
        ];

        // AJAX request → return only #main-content
        if ($request->ajax()) {
            return view('modules.assignments.partials.table', $data)
                ->renderSections()['content'];
        }

        // Normal full page load
        return view('modules.assignments.index', $data)
            ->with([
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true,
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

        return $this->renderView('modules.assignments.create', [
            'classes' => $classes,
            'subjects' => $subjects,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkAdmin();

        Log::info('=== ADMIN ASSIGNMENT CREATION ===', ['user_id' => Auth::id(), 'user_role' => 'admin']);

        // Validate all fields
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'instructions' => 'nullable|string',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'max_points' => 'required|integer|min:1',
            'allowed_formats' => 'nullable|array',
            'max_file_size' => 'required|integer|min:1',
            'is_published' => 'boolean',
            'assignment_file' => 'nullable|file|max:10240',
        ]);

        // Initialize file data
        $fileData = [
            'assignment_file' => null,
            'original_filename' => null,
            'file_size' => null
        ];

        // Handle file upload if present
        if ($request->hasFile('assignment_file') && $request->file('assignment_file')->isValid()) {
            Log::info('Admin file upload detected');

            try {
                $file = $request->file('assignment_file');

                $fileData = [
                    'assignment_file' => $file->store('assignment_files', 'public'),
                    'original_filename' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize()
                ];

            } catch (\Exception $e) {
                Log::error('Admin file upload failed:', ['error' => $e->getMessage()]);
            }
        }

        // Prepare final data
        $assignmentData = array_merge($validated, [
            'teacher_id' => Auth::id(),
            'allowed_formats' => $validated['allowed_formats'] ?? ['pdf', 'doc', 'docx'],
        ], $fileData);

        // Handle draft
        if ($request->has('draft')) {
            $assignmentData['is_published'] = false;
        }

        try {
            $assignment = Assignment::create($assignmentData);

            Log::info('Admin assignment created successfully:', [
                'admin_id' => Auth::id(),
                'assignment_id' => $assignment->id,
            ]);

            return redirect()->route('admin.assignments.index')
                ->with('success', 'Assignment created successfully!');

        } catch (\Exception $e) {
            Log::error('Admin assignment creation failed:', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to create assignment.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment)
    {
        $this->checkAdmin();

        $assignment->load(['teacher', 'class', 'subject', 'submissions.student']);

        return $this->renderView('modules.assignments.show', [
            'assignment' => $assignment,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Assignment $assignment)
    {
        $this->checkAdmin();

        $classes = SchoolClass::all();
        $subjects = Subject::all();

        return $this->renderView('modules.assignments.edit', [
            'assignment' => $assignment,
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
    public function update(Request $request, Assignment $assignment)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'instructions' => 'nullable|string',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'max_points' => 'required|integer|min:1',
            'allowed_formats' => 'nullable|array',
            'max_file_size' => 'required|integer|min:1',
            'is_published' => 'boolean',
            'assignment_file' => 'nullable|file|max:10240',
        ]);

        $validated['allowed_formats'] = $validated['allowed_formats'] ?? ['pdf', 'doc', 'docx'];

        // Handle file upload for update
        if ($request->hasFile('assignment_file')) {
            // Delete old file if exists
            if ($assignment->assignment_file && Storage::disk('public')->exists($assignment->assignment_file)) {
                Storage::disk('public')->delete($assignment->assignment_file);
            }

            $file = $request->file('assignment_file');
            $extension = $file->getClientOriginalExtension();
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $uniqueFileName = Str::slug($originalName) . '_' . time() . '.' . $extension;

            $filePath = $file->storeAs(
                "assignments/files",
                $uniqueFileName,
                'public'
            );

            $validated['assignment_file'] = $filePath;
            $validated['original_filename'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
        }

        $assignment->update($validated);

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Assignment updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment)
    {
        $this->checkAdmin();

        $assignment->delete();

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Assignment deleted successfully!');
    }

    /**
     * Toggle publish status of assignment.
     */
    public function togglePublish(Assignment $assignment)
    {
        $this->checkAdmin();

        $assignment->update([
            'is_published' => !$assignment->is_published
        ]);

        $status = $assignment->is_published ? 'published' : 'unpublished';

        return redirect()->route('admin.assignments.index')
            ->with('success', "Assignment {$status} successfully!");
    }

    // ==================== SUBMISSION METHODS ====================

    /**
     * Display all submissions for an assignment
     */
    public function submissions(Assignment $assignment)
    {
        $this->checkAdmin();

        $submissions = $assignment->submissions()
            ->with(['student', 'grader'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => $submissions->total(),
            'submitted' => $assignment->submissions()->where('status', 'submitted')->count(),
            'graded' => $assignment->submissions()->where('status', 'graded')->count(),
            'late' => $assignment->submissions()->where('status', 'late')->count(),
            'missing' => $assignment->submissions()->where('status', 'missing')->count(),
        ];

        return $this->renderView('modules.assignments.submissions.index', [
            'assignment' => $assignment,
            'submissions' => $submissions,
            'stats' => $stats,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Show individual submission details
     */
    public function showSubmission(Assignment $assignment, AssignmentSubmission $submission)
    {
        $this->checkAdmin();

        $submission->load(['assignment', 'student', 'grader']);

        return $this->renderView('modules.assignments.submissions.show', [
            'assignment' => $assignment,
            'submission' => $submission,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Download submission file
     */
    public function downloadSubmission(Assignment $assignment, AssignmentSubmission $submission)
    {
        $this->checkAdmin();

        if (!Storage::disk('public')->exists($submission->submission_file)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download(
            $submission->submission_file,
            $submission->original_filename
        );
    }

    /**
     * Show grading form
     */
    public function editSubmission(Assignment $assignment, AssignmentSubmission $submission)
    {
        $this->checkAdmin();

        $submission->load(['assignment', 'student']);

        return $this->renderView('modules.assignments.submissions.grade', [
            'assignment' => $assignment,
            'submission' => $submission,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Grade submission
     */
    public function updateSubmission(Request $request, Assignment $assignment, AssignmentSubmission $submission)
    {
        $this->checkAdmin();

        $request->validate([
            'points_obtained' => "required|integer|min:0|max:{$assignment->max_points}",
            'feedback' => 'nullable|string|max:2000',
            'status' => 'required|in:submitted,graded,late,missing',
        ]);

        $submission->update([
            'points_obtained' => $request->points_obtained,
            'feedback' => $request->feedback,
            'status' => $request->status,
            'graded_by' => auth()->id(),
            'graded_at' => now(),
        ]);

        return redirect()->route('admin.assignments.submissions', $assignment)
            ->with('success', 'Submission graded successfully!');
    }

    /**
     * Delete submission
     */
    public function destroySubmission(Assignment $assignment, AssignmentSubmission $submission)
    {
        $this->checkAdmin();

        // Delete file from storage
        if (Storage::disk('public')->exists($submission->submission_file)) {
            Storage::disk('public')->delete($submission->submission_file);
        }

        $submission->delete();

        return redirect()->route('admin.assignments.submissions', $assignment)
            ->with('success', 'Submission deleted successfully!');
    }

    /**
     * Bulk actions for submissions
     */
    public function bulkSubmissionAction(Request $request, Assignment $assignment)
    {
        $this->checkAdmin();

        $request->validate([
            'action' => 'required|in:delete,mark_graded,mark_missing',
            'submission_ids' => 'required|array',
            'submission_ids.*' => 'exists:assignment_submissions,id',
        ]);

        switch ($request->action) {
            case 'delete':
                $this->bulkDeleteSubmissions($request->submission_ids);
                $message = 'Submissions deleted successfully!';
                break;
            case 'mark_graded':
                $this->bulkMarkSubmissionsGraded($request->submission_ids);
                $message = 'Submissions marked as graded!';
                break;
            case 'mark_missing':
                $this->bulkMarkSubmissionsMissing($request->submission_ids);
                $message = 'Submissions marked as missing!';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    private function bulkDeleteSubmissions($submissionIds)
    {
        $submissions = AssignmentSubmission::whereIn('id', $submissionIds)->get();

        foreach ($submissions as $submission) {
            if (Storage::disk('public')->exists($submission->submission_file)) {
                Storage::disk('public')->delete($submission->submission_file);
            }
            $submission->delete();
        }
    }

    private function bulkMarkSubmissionsGraded($submissionIds)
    {
        AssignmentSubmission::whereIn('id', $submissionIds)->update([
            'status' => 'graded',
            'graded_by' => auth()->id(),
            'graded_at' => now(),
        ]);
    }

    private function bulkMarkSubmissionsMissing($submissionIds)
    {
        AssignmentSubmission::whereIn('id', $submissionIds)->update([
            'status' => 'missing',
            'graded_by' => null,
            'graded_at' => null,
            'points_obtained' => null,
            'feedback' => null,
        ]);
    }
}
