<?php

namespace App\Http\Controllers\Modules\Teachers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Academic\Subject;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Stream;
use App\Models\Academic\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminTeacherAssignmentController extends Controller
{
    /**
     * Display teacher assignments management page.
     */
    public function index(Request $request)
    {
        Log::debug('TeacherAssignmentController: index method called', [
            'request_params' => $request->all(),
            'user_id' => auth()->id()
        ]);

        $query = TeacherAssignment::with(['teacher', 'subject', 'class', 'stream']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('teacher', function($q2) use ($search) {
                    $q2->where('name', 'LIKE', "%{$search}%");
                })->orWhereHas('subject', function($q2) use ($search) {
                    $q2->where('name', 'LIKE', "%{$search}%");
                })->orWhereHas('class', function($q2) use ($search) {
                    $q2->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        // Filter by academic year
        if ($request->has('academic_year') && $request->academic_year != '') {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by role
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        // Filter by active status
        if ($request->has('status') && $request->status != '') {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        $assignments = $query->latest()->paginate(20);

        $teachers = User::teachers()->active()->get();
        $subjects = Subject::where('is_active', true)->get();
        $classes = SchoolClass::where('is_active', true)->get();
        $streams = Stream::where('is_active', true)->get();

        $roles = [
            'class_teacher' => 'Class Teacher',
            'subject_teacher' => 'Subject Teacher',
            'head_teacher' => 'Head Teacher'
        ];

        // Get current academic year (you might want to make this dynamic)
        $currentAcademicYear = date('Y') . '/' . (date('Y') + 1);

        Log::debug('TeacherAssignmentController: Data loaded for index', [
            'assignments_count' => $assignments->count(),
            'teachers_count' => $teachers->count(),
            'subjects_count' => $subjects->count(),
            'classes_count' => $classes->count(),
            'streams_count' => $streams->count()
        ]);

        $data = [
            'assignments' => $assignments,
            'teachers' => $teachers,
            'subjects' => $subjects,
            'classes' => $classes,
            'streams' => $streams,
            'roles' => $roles,
            'currentAcademicYear' => $currentAcademicYear,
            'filters' => $request->only(['search', 'academic_year', 'role', 'status'])
        ];

        if ($request->ajax()) {
            return view('modules.teachers.assignments.partials.assignments-table', $data)
                ->renderSections()['content'];
        }

        return view('modules.teachers.assignments.index', $data)
            ->with([
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true,
            ]);
    }

    /**
     * Show form to create new teacher assignment.
     */
    public function create(Request $request)
    {
        Log::debug('TeacherAssignmentController: create method called');

        $teachers = User::teachers()->active()->get();
        $subjects = Subject::where('is_active', true)->get();
        $classes = SchoolClass::where('is_active', true)->orderBy('level')->orderBy('name')->get();
        $streams = Stream::where('is_active', true)->get();

        $roles = [
            'class_teacher' => 'Class Teacher',
            'subject_teacher' => 'Subject Teacher',
            'head_teacher' => 'Head Teacher'
        ];

        $currentAcademicYear = date('Y') . '/' . (date('Y') + 1);

        Log::debug('TeacherAssignmentController: Create form data loaded', [
            'teachers_count' => $teachers->count(),
            'subjects_count' => $subjects->count(),
            'classes_count' => $classes->count(),
            'streams_count' => $streams->count(),
            'classes_list' => $classes->pluck('name', 'id')->toArray()
        ]);

        $data = [
            'teachers' => $teachers,
            'subjects' => $subjects,
            'classes' => $classes,
            'streams' => $streams,
            'roles' => $roles,
            'currentAcademicYear' => $currentAcademicYear
        ];

        if ($request->ajax()) {
            return view('modules.teachers.assignments.partials.create-form', $data)
                ->renderSections()['content'];
        }

        return view('modules.teachers.assignments.create', $data)
            ->with([
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true,
            ]);
    }

    /**
     * Store new teacher assignment with flexible class-subject combinations.
     */
    public function store(Request $request)
    {
        Log::debug('TeacherAssignmentController: store method called', [
            'request_data' => $request->all(),
            'user_id' => auth()->id()
        ]);

        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'assignments' => 'required|array|min:1',
            'assignments.*.class_id' => 'required|exists:school_classes,id',
            'assignments.*.subject_ids' => 'required|array|min:1',
            'assignments.*.subject_ids.*' => 'exists:subjects,id',
            'assignments.*.stream_id' => 'nullable|exists:streams,id',
            'academic_year' => 'required|string|max:9',
            'role' => 'required|in:class_teacher,subject_teacher,head_teacher',
            'is_active' => 'boolean'
        ]);

        Log::debug('TeacherAssignmentController: Validation passed', [
            'validated_data' => $validated
        ]);

        try {
            DB::beginTransaction();

            $createdCount = 0;
            $skippedCount = 0;
            $errors = [];

            Log::debug('TeacherAssignmentController: Starting assignment creation', [
                'assignments_count' => count($validated['assignments'])
            ]);

            foreach ($validated['assignments'] as $index => $assignmentData) {
                Log::debug('TeacherAssignmentController: Processing assignment', [
                    'assignment_index' => $index,
                    'assignment_data' => $assignmentData
                ]);

                $class = SchoolClass::find($assignmentData['class_id']);

                foreach ($assignmentData['subject_ids'] as $subjectId) {
                    $subject = Subject::find($subjectId);

                    Log::debug('TeacherAssignmentController: Checking for existing assignment', [
                        'teacher_id' => $validated['teacher_id'],
                        'subject_id' => $subjectId,
                        'class_id' => $assignmentData['class_id'],
                        'stream_id' => $assignmentData['stream_id'] ?? null,
                        'academic_year' => $validated['academic_year']
                    ]);

                    // Check if assignment already exists
                    $existingAssignment = TeacherAssignment::where([
                        'teacher_id' => $validated['teacher_id'],
                        'subject_id' => $subjectId,
                        'class_id' => $assignmentData['class_id'],
                        'academic_year' => $validated['academic_year']
                    ])->when(isset($assignmentData['stream_id']) && $assignmentData['stream_id'], function($query) use ($assignmentData) {
                        return $query->where('stream_id', $assignmentData['stream_id']);
                    })->when(!isset($assignmentData['stream_id']) || !$assignmentData['stream_id'], function($query) {
                        return $query->whereNull('stream_id');
                    })->first();

                    if ($existingAssignment) {
                        $stream = isset($assignmentData['stream_id']) ? Stream::find($assignmentData['stream_id']) : null;
                        $streamText = $stream ? " (Stream: {$stream->name})" : '';
                        $skippedCount++;
                        $errorMsg = "Assignment for {$subject->name} in {$class->name}{$streamText} already exists";
                        $errors[] = $errorMsg;

                        Log::debug('TeacherAssignmentController: Assignment skipped - duplicate', [
                            'error' => $errorMsg
                        ]);
                        continue;
                    }

                    Log::debug('TeacherAssignmentController: Creating new assignment', [
                        'teacher_id' => $validated['teacher_id'],
                        'subject_id' => $subjectId,
                        'class_id' => $assignmentData['class_id'],
                        'stream_id' => $assignmentData['stream_id'] ?? null,
                        'academic_year' => $validated['academic_year'],
                        'role' => $validated['role']
                    ]);

                    TeacherAssignment::create([
                        'teacher_id' => $validated['teacher_id'],
                        'subject_id' => $subjectId,
                        'class_id' => $assignmentData['class_id'],
                        'stream_id' => $assignmentData['stream_id'] ?? null,
                        'academic_year' => $validated['academic_year'],
                        'role' => $validated['role'],
                        'is_active' => $validated['is_active'] ?? true,
                    ]);

                    $createdCount++;
                    Log::debug('TeacherAssignmentController: Assignment created successfully', [
                        'created_count' => $createdCount
                    ]);
                }
            }

            DB::commit();

            $message = "{$createdCount} assignments created successfully!";
            Log::debug('TeacherAssignmentController: Store completed', [
                'created_count' => $createdCount,
                'skipped_count' => $skippedCount,
                'message' => $message
            ]);

            if ($skippedCount > 0) {
                $message .= " {$skippedCount} assignments skipped.";

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'warnings' => $errors,
                        'redirect' => route('admin.teacher-assignments.index')
                    ]);
                }

                return redirect()->route('admin.teacher-assignments.index')
                    ->with('success', $message)
                    ->with('warnings', $errors);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => route('admin.teacher-assignments.index')
                ]);
            }

            return redirect()->route('admin.teacher-assignments.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('TeacherAssignmentController: Store method failed', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Get available classes for a teacher.
     */
    public function getAvailableClasses(Request $request, User $teacher)
    {
        Log::debug('TeacherAssignmentController: getAvailableClasses method called', [
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->name,
            'request_params' => $request->all()
        ]);

        $academicYear = $request->get('academic_year', date('Y') . '/' . (date('Y') + 1));

        Log::debug('TeacherAssignmentController: Fetching assigned classes', [
            'academic_year' => $academicYear
        ]);

        // Get all classes where teacher is already assigned in this academic year
        $assignedClassIds = $teacher->teacherAssignments()
            ->where('academic_year', $academicYear)
            ->pluck('class_id')
            ->unique()
            ->toArray();

        Log::debug('TeacherAssignmentController: Assigned class IDs', [
            'assigned_class_ids' => $assignedClassIds
        ]);

        // Get all active classes, ordered by level and name
        $availableClasses = SchoolClass::where('is_active', true)
            ->orderBy('level')
            ->orderBy('name')
            ->get();

        Log::debug('TeacherAssignmentController: Available classes loaded', [
            'available_classes_count' => $availableClasses->count(),
            'available_classes' => $availableClasses->pluck('name', 'id')->toArray()
        ]);

        $response = [
            'classes' => $availableClasses,
            'assigned_class_ids' => $assignedClassIds
        ];

        Log::debug('TeacherAssignmentController: Sending response for available classes', $response);

        return response()->json($response);
    }

    /**
     * Get available subjects for a teacher in specific class.
     */
    public function getAvailableSubjects(Request $request, User $teacher)
    {
        Log::debug('TeacherAssignmentController: getAvailableSubjects method called', [
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->name,
            'request_params' => $request->all()
        ]);

        $academicYear = $request->get('academic_year', date('Y') . '/' . (date('Y') + 1));
        $classId = $request->get('class_id');

        Log::debug('TeacherAssignmentController: Subject request details', [
            'academic_year' => $academicYear,
            'class_id' => $classId
        ]);

        if (!$classId) {
            Log::warning('TeacherAssignmentController: No class_id provided for available subjects');
            return response()->json(['subjects' => []]);
        }

        // Get the class
        $class = SchoolClass::find($classId);
        if (!$class) {
            Log::warning('TeacherAssignmentController: Class not found', ['class_id' => $classId]);
            return response()->json(['subjects' => []]);
        }

        Log::debug('TeacherAssignmentController: Class found', [
            'class_id' => $class->id,
            'class_name' => $class->name
        ]);

        // Get subjects that are linked to this specific class through the class_subject pivot table
        $classSubjects = Subject::whereHas('classes', function($query) use ($classId) {
            $query->where('school_classes.id', $classId);
        })->where('is_active', true)->orderBy('name')->get();

        Log::debug('TeacherAssignmentController: Class subjects loaded', [
            'class_subjects_count' => $classSubjects->count(),
            'class_subjects' => $classSubjects->pluck('name', 'id')->toArray()
        ]);

        // Get subjects already assigned to this teacher for the specific class and academic year
        $assignedSubjectIds = $teacher->teacherAssignments()
            ->where('academic_year', $academicYear)
            ->where('class_id', $classId)
            ->pluck('subject_id')
            ->toArray();

        Log::debug('TeacherAssignmentController: Assigned subject IDs', [
            'assigned_subject_ids' => $assignedSubjectIds
        ]);

        // Filter out already assigned subjects
        $availableSubjects = $classSubjects->filter(function($subject) use ($assignedSubjectIds) {
            return !in_array($subject->id, $assignedSubjectIds);
        })->values();

        Log::debug('TeacherAssignmentController: Available subjects filtered', [
            'available_subjects_count' => $availableSubjects->count(),
            'available_subjects' => $availableSubjects->pluck('name', 'id')->toArray()
        ]);

        $response = ['subjects' => $availableSubjects];
        Log::debug('TeacherAssignmentController: Sending response for available subjects', $response);

        return response()->json($response);
    }

    /**
     * Get streams for a specific class.
     */
    public function getClassStreams(Request $request, SchoolClass $class)
    {
        Log::debug('TeacherAssignmentController: getClassStreams method called', [
            'class_id' => $class->id,
            'class_name' => $class->name,
            'request_params' => $request->all()
        ]);

        $streams = $class->streams()->where('is_active', true)->orderBy('name')->get();

        Log::debug('TeacherAssignmentController: Class streams loaded', [
            'streams_count' => $streams->count(),
            'streams' => $streams->pluck('name', 'id')->toArray()
        ]);

        $response = ['streams' => $streams];
        Log::debug('TeacherAssignmentController: Sending response for class streams', $response);

        return response()->json($response);
    }

    /**
     * Show form to edit teacher assignment.
     */
    public function edit(Request $request, TeacherAssignment $assignment)
    {
        Log::debug('TeacherAssignmentController: edit method called', [
            'assignment_id' => $assignment->id
        ]);

        $teachers = User::teachers()->active()->get();
        $subjects = Subject::where('is_active', true)->get();
        $classes = SchoolClass::where('is_active', true)->get();
        $streams = Stream::where('is_active', true)->get();

        $roles = [
            'class_teacher' => 'Class Teacher',
            'subject_teacher' => 'Subject Teacher',
            'head_teacher' => 'Head Teacher'
        ];

        $data = [
            'assignment' => $assignment,
            'teachers' => $teachers,
            'subjects' => $subjects,
            'classes' => $classes,
            'streams' => $streams,
            'roles' => $roles
        ];

        if ($request->ajax()) {
            return view('modules.teachers.assignments.partials.edit-form', $data)
                ->renderSections()['content'];
        }

        return view('modules.teachers.assignments.edit', $data)
            ->with([
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true,
            ]);
    }

    /**
     * Update teacher assignment.
     */
    public function update(Request $request, TeacherAssignment $assignment)
    {
        Log::debug('TeacherAssignmentController: update method called', [
            'assignment_id' => $assignment->id,
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:school_classes,id',
            'stream_id' => 'nullable|exists:streams,id',
            'academic_year' => 'required|string|max:9',
            'role' => 'required|in:class_teacher,subject_teacher,head_teacher',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // IMPROVED: Check if the EXACT same assignment already exists (excluding current)
            $existingAssignment = TeacherAssignment::where([
                'teacher_id' => $validated['teacher_id'],
                'subject_id' => $validated['subject_id'],
                'class_id' => $validated['class_id'],
                'academic_year' => $validated['academic_year']
            ])->when($validated['stream_id'], function($query) use ($validated) {
                return $query->where('stream_id', $validated['stream_id']);
            })->when(!$validated['stream_id'], function($query) {
                return $query->whereNull('stream_id');
            })->where('id', '!=', $assignment->id)->first();

            if ($existingAssignment) {
                Log::warning('TeacherAssignmentController: Duplicate assignment found during update', [
                    'existing_assignment_id' => $existingAssignment->id
                ]);
                throw new \Exception('This teacher is already assigned to teach this specific subject for the selected class and academic year.');
            }

            $assignment->update([
                'teacher_id' => $validated['teacher_id'],
                'subject_id' => $validated['subject_id'],
                'class_id' => $validated['class_id'],
                'stream_id' => $validated['stream_id'],
                'academic_year' => $validated['academic_year'],
                'role' => $validated['role'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            Log::debug('TeacherAssignmentController: Assignment updated successfully', [
                'assignment_id' => $assignment->id
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Teacher assignment updated successfully!',
                    'redirect' => route('admin.teacher-assignments.index')
                ]);
            }

            return redirect()->route('admin.teacher-assignments.index')
                ->with('success', 'Teacher assignment updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('TeacherAssignmentController: Update method failed', [
                'assignment_id' => $assignment->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Delete teacher assignment.
     */
    public function destroy(Request $request, TeacherAssignment $assignment)
    {
        Log::debug('TeacherAssignmentController: destroy method called', [
            'assignment_id' => $assignment->id,
            'assignment_details' => [
                'teacher_id' => $assignment->teacher_id,
                'teacher_name' => $assignment->teacher->name,
                'subject_id' => $assignment->subject_id,
                'subject_name' => $assignment->subject->name,
                'class_id' => $assignment->class_id,
                'class_name' => $assignment->class->name,
                'academic_year' => $assignment->academic_year
            ]
        ]);

        try {
            DB::beginTransaction();

            $assignmentDetails = "{$assignment->teacher->name} - {$assignment->subject->name} - {$assignment->class->name} ({$assignment->academic_year})";

            $assignment->delete();

            DB::commit();

            Log::debug('TeacherAssignmentController: Assignment deleted successfully', [
                'assignment_id' => $assignment->id,
                'assignment_details' => $assignmentDetails
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Teacher assignment deleted successfully!'
                ]);
            }

            return redirect()->route('admin.teacher-assignments.index')
                ->with('success', 'Teacher assignment deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('TeacherAssignmentController: Destroy method failed', [
                'assignment_id' => $assignment->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete teacher assignment: ' . $e->getMessage()
                ], 422);
            }

            return back()->with('error', 'Failed to delete teacher assignment: ' . $e->getMessage());
        }
    }

    /**
     * Toggle assignment active status.
     */
    public function toggleStatus(Request $request, TeacherAssignment $assignment)
    {
        Log::debug('TeacherAssignmentController: toggleStatus method called', [
            'assignment_id' => $assignment->id,
            'current_status' => $assignment->is_active
        ]);

        try {
            DB::beginTransaction();

            $assignment->update([
                'is_active' => !$assignment->is_active
            ]);

            DB::commit();

            $newStatus = $assignment->is_active ? 'active' : 'inactive';
            Log::debug('TeacherAssignmentController: Assignment status toggled successfully', [
                'assignment_id' => $assignment->id,
                'new_status' => $newStatus
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Assignment status updated successfully!',
                'is_active' => $assignment->is_active,
                'status_text' => $newStatus
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('TeacherAssignmentController: ToggleStatus method failed', [
                'assignment_id' => $assignment->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update assignment status: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get teacher assignments for AJAX requests.
     */
    public function getTeacherAssignments(Request $request, User $teacher)
    {
        Log::debug('TeacherAssignmentController: getTeacherAssignments method called', [
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->name,
            'request_params' => $request->all()
        ]);

        $academicYear = $request->get('academic_year', date('Y') . '/' . (date('Y') + 1));

        $assignments = $teacher->teacherAssignments()
            ->with(['subject', 'class', 'stream'])
            ->where('academic_year', $academicYear)
            ->get()
            ->map(function($assignment) {
                return [
                    'id' => $assignment->id,
                    'subject_name' => $assignment->subject->name,
                    'class_name' => $assignment->class->name,
                    'stream_name' => $assignment->stream ? $assignment->stream->name : null,
                    'role' => $assignment->role,
                    'is_active' => $assignment->is_active
                ];
            });

        Log::debug('TeacherAssignmentController: Teacher assignments loaded', [
            'assignments_count' => $assignments->count(),
            'assignments' => $assignments->toArray()
        ]);

        return response()->json([
            'assignments' => $assignments
        ]);
    }
}
