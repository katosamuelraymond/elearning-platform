<?php

namespace App\Http\Controllers\Modules\Assignments;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Assignment;
use App\Models\Assessment\AssignmentSubmission;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Subject;
use App\Models\Academic\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherAssignmentsController extends Controller
{
    /**
     * Get teacher's assigned subjects
     */
    private function getTeacherSubjects()
    {
        $teacherId = Auth::id();

        // Get subjects from teacher assignments
        $teacherAssignments = TeacherAssignment::where('teacher_id', $teacherId)
            ->with(['subject'])
            ->get();

        $teacherSubjects = $teacherAssignments
            ->pluck('subject')
            ->filter() // Remove null subjects
            ->unique('id') // Remove duplicates
            ->values();

        Log::info('Teacher subjects for assignments', [
            'teacher_id' => $teacherId,
            'subjects_count' => $teacherSubjects->count(),
            'subject_names' => $teacherSubjects->pluck('name')
        ]);

        return $teacherSubjects;
    }

    /**
     * Get teacher's assigned classes
     */
    private function getTeacherClasses()
    {
        $teacherId = Auth::id();

        // Get classes from teacher assignments
        $teacherAssignments = TeacherAssignment::where('teacher_id', $teacherId)
            ->with(['class'])
            ->get();

        $teacherClasses = $teacherAssignments
            ->pluck('class')
            ->filter() // Remove null classes
            ->unique('id') // Remove duplicates
            ->values();

        Log::info('Teacher classes for assignments', [
            'teacher_id' => $teacherId,
            'classes_count' => $teacherClasses->count(),
            'class_names' => $teacherClasses->pluck('name')
        ]);

        return $teacherClasses;
    }

    /**
     * Display teacher's assignments
     */
    public function index()
    {
        $assignments = Assignment::where('teacher_id', auth()->id())
            ->with(['class', 'subject'])
            ->withCount(['submissions', 'submissions as graded_count' => function($query) {
                $query->where('status', 'graded');
            }])
            ->latest()
            ->paginate(10);

        // FIX: Proper stats with all required keys
        $stats = [
            'total' => Assignment::where('teacher_id', auth()->id())->count(),
            'published' => Assignment::where('teacher_id', auth()->id())->where('is_published', true)->count(),
            'draft' => Assignment::where('teacher_id', auth()->id())->where('is_published', false)->count(),
            'submitted' => AssignmentSubmission::whereHas('assignment', function($query) {
                $query->where('teacher_id', auth()->id());
            })->count(),
        ];

        return $this->renderView('modules.assignments.index', [
            'assignments' => $assignments,
            'stats' => $stats,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = $this->getTeacherClasses();
        $subjects = $this->getTeacherSubjects();

        // Check if teacher has any assigned subjects/classes
        if ($subjects->isEmpty() || $classes->isEmpty()) {
            return redirect()->route('teacher.assignments.index')
                ->with('error', 'You are not assigned to any subjects or classes. Please contact administrator.');
        }

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
        Log::info('=== ASSIGNMENT CREATION WITH FILE UPLOAD ===');

        // First, validate the teacher can assign to this subject and class
        $teacherSubjects = $this->getTeacherSubjects();
        $teacherClasses = $this->getTeacherClasses();

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

        // Verify the teacher is assigned to this subject
        $isValidSubject = $teacherSubjects->contains('id', $validated['subject_id']);
        $isValidClass = $teacherClasses->contains('id', $validated['class_id']);

        if (!$isValidSubject) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'You are not assigned to teach this subject.');
        }

        if (!$isValidClass) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'You are not assigned to teach this class.');
        }

        // Initialize file data
        $fileData = [
            'assignment_file' => null,
            'original_filename' => null,
            'file_size' => null
        ];

        // Handle file upload if present
        if ($request->hasFile('assignment_file') && $request->file('assignment_file')->isValid()) {
            Log::info('File upload detected and valid');

            try {
                $file = $request->file('assignment_file');

                Log::info('Uploading file:', [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ]);

                // Store the file
                $path = $file->store('assignment_files', 'public');

                Log::info('File stored successfully:', ['path' => $path]);

                $fileData = [
                    'assignment_file' => $path,
                    'original_filename' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize()
                ];

            } catch (\Exception $e) {
                Log::error('File upload failed:', ['error' => $e->getMessage()]);
                // Continue without file - don't fail the assignment
            }
        } else {
            Log::info('No file uploaded or file invalid');
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

            Log::info('Assignment created successfully:', [
                'id' => $assignment->id,
                'has_file' => !empty($assignment->assignment_file) ? 'YES' : 'NO',
                'file_path' => $assignment->assignment_file,
            ]);

            return redirect()->route('teacher.assignments.index')
                ->with('success', 'Assignment created successfully!');

        } catch (\Exception $e) {
            Log::error('Assignment creation failed:', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to create assignment.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment)
    {
        // Verify teacher owns this assignment
        if ($assignment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $assignment->load(['class', 'subject', 'submissions.student']);

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
        // Verify teacher owns this assignment
        if ($assignment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $classes = $this->getTeacherClasses();
        $subjects = $this->getTeacherSubjects();

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
        // Verify teacher owns this assignment
        if ($assignment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // First, validate the teacher can assign to this subject and class
        $teacherSubjects = $this->getTeacherSubjects();
        $teacherClasses = $this->getTeacherClasses();

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

        // Verify the teacher is assigned to this subject and class
        $isValidSubject = $teacherSubjects->contains('id', $validated['subject_id']);
        $isValidClass = $teacherClasses->contains('id', $validated['class_id']);

        if (!$isValidSubject) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'You are not assigned to teach this subject.');
        }

        if (!$isValidClass) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'You are not assigned to teach this class.');
        }

        $validated['allowed_formats'] = $validated['allowed_formats'] ?? ['pdf', 'doc', 'docx'];

        // Handle assignment file upload for update
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

        return redirect()->route('teacher.assignments.index')
            ->with('success', 'Assignment updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment)
    {
        // Verify teacher owns this assignment
        if ($assignment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $assignment->delete();

        return redirect()->route('teacher.assignments.index')
            ->with('success', 'Assignment deleted successfully!');
    }

    /**
     * Toggle publish status of assignment.
     */
    public function togglePublish(Assignment $assignment)
    {
        // Verify teacher owns this assignment
        if ($assignment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $assignment->update([
            'is_published' => !$assignment->is_published
        ]);

        $status = $assignment->is_published ? 'published' : 'unpublished';

        return redirect()->route('teacher.assignments.index')
            ->with('success', "Assignment {$status} successfully!");
    }

    // ==================== FILE DOWNLOAD METHOD ====================

    /**
     * Download assignment file
     */
    public function download(Assignment $assignment)
    {
        // Verify teacher owns this assignment
        if ($assignment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$assignment->assignment_file || !Storage::disk('public')->exists($assignment->assignment_file)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download(
            $assignment->assignment_file,
            $assignment->original_filename
        );
    }

    // ... rest of your methods remain the same (submissions, grading, etc.)
    // ==================== SUBMISSION METHODS ====================

    /**
     * Show submissions for a specific assignment
     */
    public function submissions(Assignment $assignment)
    {
        // Verify teacher owns this assignment
        if ($assignment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $submissions = $assignment->submissions()
            ->with(['student', 'grader'])
            ->latest()
            ->paginate(10);

        // FIX: Proper stats with all required keys
        $stats = [
            'total' => $assignment->submissions()->count(),
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
     * Show individual submission
     */
    public function showSubmission(Assignment $assignment, AssignmentSubmission $submission)
    {
        // Verify teacher owns this assignment
        if ($assignment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $submission->load(['student', 'grader']);

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
        // Verify teacher owns this assignment
        if ($assignment->teacher_id !== auth()->id()) {
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
     * Show grading form
     */
    public function gradeSubmission(Assignment $assignment, AssignmentSubmission $submission)
    {
        // Verify teacher owns this assignment
        if ($assignment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $submission->load(['student']);

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
        // Verify teacher owns this assignment
        if ($assignment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'points_obtained' => "required|integer|min:0|max:{$assignment->max_points}",
            'feedback' => 'nullable|string|max:2000',
        ]);

        $submission->update([
            'points_obtained' => $request->points_obtained,
            'feedback' => $request->feedback,
            'status' => 'graded',
            'graded_by' => auth()->id(),
            'graded_at' => now(),
        ]);

        return redirect()->route('teacher.assignments.submissions', $assignment)
            ->with('success', 'Submission graded successfully!');
    }

    /**
     * Delete submission
     */
    public function destroySubmission(Assignment $assignment, AssignmentSubmission $submission)
    {
        // Verify teacher owns this assignment
        if ($assignment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($submission->submission_file)) {
            Storage::disk('public')->delete($submission->submission_file);
        }

        $submission->delete();

        return redirect()->route('teacher.assignmissions.submissions', $assignment)
            ->with('success', 'Submission deleted successfully!');
    }

    /**
     * Bulk actions for submissions
     */
    public function bulkSubmissionAction(Request $request, Assignment $assignment)
    {
        // Verify teacher owns this assignment
        if ($assignment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

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
