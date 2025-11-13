<?php

namespace App\Http\Controllers\Modules\Students;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Stream;
use App\Models\Academic\StudentClassAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentAssignmentController extends Controller
{
    /**
     * Display student assignments management page.
     */
    public function index(Request $request)
    {
        Log::debug('StudentAssignmentController: index method called');

        // Get students with their class assignments
        $query = User::with(['roles', 'profile', 'studentClassAssignments.class', 'studentClassAssignments.stream'])
            ->whereHas('roles', function($q) {
                $q->where('name', 'student');
            });

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhereHas('profile', function($q2) use ($search) {
                        $q2->where('student_id', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('studentClassAssignments.class', function($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Filter by class
        if ($request->has('class_id') && $request->class_id != '') {
            $query->whereHas('studentClassAssignments', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        // Filter by assignment status
        if ($request->has('assignment_status') && $request->assignment_status != '') {
            if ($request->assignment_status === 'assigned') {
                $query->whereHas('studentClassAssignments');
            } elseif ($request->assignment_status === 'unassigned') {
                $query->whereDoesntHave('studentClassAssignments');
            }
        }

        $students = $query->latest()->paginate(20);
        $classes = SchoolClass::where('is_active', true)->orderBy('level')->orderBy('name')->get();

        // Get stats
        $totalStudents = User::whereHas('roles', function($q) {
            $q->where('name', 'student');
        })->count();

        $assignedStudents = User::whereHas('roles', function($q) {
            $q->where('name', 'student');
        })->whereHas('studentClassAssignments')->count();

        $stats = [
            'total' => $totalStudents,
            'assigned' => $assignedStudents,
            'unassigned' => $totalStudents - $assignedStudents,
        ];

        Log::debug('StudentAssignmentController: Data loaded for index', [
            'students_count' => $students->count(),
            'classes_count' => $classes->count(),
            'stats' => $stats
        ]);

        return $this->renderView('modules.students.assignments.index', [
            'students' => $students,
            'classes' => $classes,
            'stats' => $stats,
            'filters' => $request->only(['search', 'class_id', 'assignment_status']),
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Show form to assign class to student.
     */
    public function create(Request $request)
    {
        Log::debug('StudentAssignmentController: create method called');

        // Get unassigned students
        $students = User::whereHas('roles', function($q) {
            $q->where('name', 'student');
        })->whereDoesntHave('studentClassAssignments')->get();

        $classes = SchoolClass::where('is_active', true)->orderBy('level')->orderBy('name')->get();
        $streams = Stream::where('is_active', true)->orderBy('name')->get();

        Log::debug('StudentAssignmentController: Create form data loaded', [
            'unassigned_students_count' => $students->count(),
            'classes_count' => $classes->count()
        ]);

        return $this->renderView('modules.students.assignments.create', [
            'students' => $students,
            'classes' => $classes,
            'streams' => $streams,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Assign class to student.
     */
    public function store(Request $request)
    {
        Log::debug('StudentAssignmentController: store method called', [
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:school_classes,id',
            'stream_id' => 'required|exists:streams,id',
            'academic_year' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $student = User::findOrFail($validated['student_id']);
            $class = SchoolClass::findOrFail($validated['class_id']);
            $stream = Stream::findOrFail($validated['stream_id']);

            // Check if student already has an active class assignment
            $existingAssignment = StudentClassAssignment::where('student_id', $student->id)
                ->where('status', 'active')
                ->first();

            if ($existingAssignment) {
                $currentClass = $existingAssignment->class;
                $currentStream = $existingAssignment->stream;
                throw new \Exception("Student is already assigned to class: {$currentClass->name} - {$currentStream->name}");
            }

            // Create new class assignment
            StudentClassAssignment::create([
                'student_id' => $student->id,
                'class_id' => $validated['class_id'],
                'stream_id' => $validated['stream_id'],
                'academic_year' => $validated['academic_year'],
                'status' => 'active'
            ]);

            DB::commit();

            Log::debug('StudentAssignmentController: Student assigned successfully', [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'class_id' => $class->id,
                'class_name' => $class->name,
                'stream_id' => $stream->id,
                'stream_name' => $stream->name
            ]);

            return redirect()->route('admin.student-assignments.index')
                ->with('success', "Student {$student->name} successfully assigned to class {$class->name} - {$stream->name}!");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('StudentAssignmentController: Store method failed', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Bulk assign students to class.
     */
    public function bulkAssign(Request $request)
    {
        Log::debug('StudentAssignmentController: bulkAssign method called', [
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:users,id',
            'class_id' => 'required|exists:school_classes,id',
            'stream_id' => 'required|exists:streams,id',
            'academic_year' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $class = SchoolClass::findOrFail($validated['class_id']);
            $stream = Stream::findOrFail($validated['stream_id']);
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($validated['student_ids'] as $studentId) {
                $student = User::findOrFail($studentId);

                // Check if student already has an active class assignment
                $existingAssignment = StudentClassAssignment::where('student_id', $student->id)
                    ->where('status', 'active')
                    ->first();

                if ($existingAssignment) {
                    $currentClass = $existingAssignment->class;
                    $currentStream = $existingAssignment->stream;
                    $errorCount++;
                    $errors[] = "{$student->name} is already assigned to class: {$currentClass->name} - {$currentStream->name}";
                    continue;
                }

                // Create new class assignment
                StudentClassAssignment::create([
                    'student_id' => $student->id,
                    'class_id' => $validated['class_id'],
                    'stream_id' => $validated['stream_id'],
                    'academic_year' => $validated['academic_year'],
                    'status' => 'active'
                ]);

                $successCount++;
                Log::debug('StudentAssignmentController: Student bulk assigned', [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'class_id' => $class->id,
                    'stream_id' => $stream->id
                ]);
            }

            DB::commit();

            $message = "{$successCount} students successfully assigned to class {$class->name} - {$stream->name}!";

            if ($errorCount > 0) {
                $message .= " {$errorCount} assignments failed.";

                return redirect()->route('admin.student-assignments.index')
                    ->with('success', $message)
                    ->with('warnings', $errors);
            }

            Log::debug('StudentAssignmentController: Bulk assignment completed', [
                'success_count' => $successCount,
                'error_count' => $errorCount
            ]);

            return redirect()->route('admin.student-assignments.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('StudentAssignmentController: BulkAssign method failed', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Update student class assignment.
     */
    public function update(Request $request, User $student)
    {
        Log::debug('StudentAssignmentController: update method called', [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'stream_id' => 'required|exists:streams,id',
            'academic_year' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $class = SchoolClass::findOrFail($validated['class_id']);
            $stream = Stream::findOrFail($validated['stream_id']);

            // Get current assignment
            $currentAssignment = StudentClassAssignment::where('student_id', $student->id)
                ->where('status', 'active')
                ->first();

            $oldClass = $currentAssignment ? "{$currentAssignment->class->name} - {$currentAssignment->stream->name}" : 'No Class';

            // Deactivate current assignment
            if ($currentAssignment) {
                $currentAssignment->update(['status' => 'inactive']);
            }

            // Create new assignment
            StudentClassAssignment::create([
                'student_id' => $student->id,
                'class_id' => $validated['class_id'],
                'stream_id' => $validated['stream_id'],
                'academic_year' => $validated['academic_year'],
                'status' => 'active'
            ]);

            DB::commit();

            Log::debug('StudentAssignmentController: Student assignment updated', [
                'student_id' => $student->id,
                'old_class' => $oldClass,
                'new_class' => "{$class->name} - {$stream->name}"
            ]);

            return redirect()->route('admin.student-assignments.index')
                ->with('success', "Student {$student->name} successfully moved from {$oldClass} to {$class->name} - {$stream->name}!");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('StudentAssignmentController: Update method failed', [
                'student_id' => $student->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Remove student from class.
     */
    public function destroy(Request $request, User $student)
    {
        Log::debug('StudentAssignmentController: destroy method called', [
            'student_id' => $student->id,
            'student_name' => $student->name
        ]);

        try {
            DB::beginTransaction();

            // Get current active assignment
            $currentAssignment = StudentClassAssignment::where('student_id', $student->id)
                ->where('status', 'active')
                ->first();

            if (!$currentAssignment) {
                throw new \Exception('Student is not currently assigned to any class.');
            }

            $className = "{$currentAssignment->class->name} - {$currentAssignment->stream->name}";

            // Deactivate the assignment
            $currentAssignment->update(['status' => 'inactive']);

            DB::commit();

            Log::debug('StudentAssignmentController: Student unassigned successfully', [
                'student_id' => $student->id,
                'previous_class' => $className
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Student successfully removed from class!'
                ]);
            }

            return redirect()->route('admin.student-assignments.index')
                ->with('success', "Student {$student->name} successfully removed from {$className}!");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('StudentAssignmentController: Destroy method failed', [
                'student_id' => $student->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove student from class: ' . $e->getMessage()
                ], 422);
            }

            return back()->with('error', 'Failed to remove student from class: ' . $e->getMessage());
        }
    }

    /**
     * Get unassigned students for AJAX.
     */
    public function getUnassignedStudents(Request $request)
    {
        Log::debug('StudentAssignmentController: getUnassignedStudents method called');

        $students = User::whereHas('roles', function($q) {
            $q->where('name', 'student');
        })
            ->whereDoesntHave('studentClassAssignments', function($q) {
                $q->where('status', 'active');
            })
            ->select('id', 'name', 'email')
            ->with(['profile:user_id,student_id'])
            ->orderBy('name')
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'student_id' => $student->profile->student_id ?? 'N/A'
                ];
            });

        Log::debug('StudentAssignmentController: Unassigned students loaded', [
            'count' => $students->count()
        ]);

        return response()->json([
            'students' => $students
        ]);
    }

    /**
     * Get students by class for AJAX.
     */
    public function getStudentsByClass(Request $request, SchoolClass $class)
    {
        Log::debug('StudentAssignmentController: getStudentsByClass method called', [
            'class_id' => $class->id,
            'class_name' => $class->name
        ]);

        $students = User::whereHas('studentClassAssignments', function($q) use ($class) {
            $q->where('class_id', $class->id)
                ->where('status', 'active');
        })
            ->select('id', 'name', 'email')
            ->with(['profile:user_id,student_id'])
            ->orderBy('name')
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'student_id' => $student->profile->student_id ?? 'N/A'
                ];
            });

        Log::debug('StudentAssignmentController: Students by class loaded', [
            'class_id' => $class->id,
            'students_count' => $students->count()
        ]);

        return response()->json([
            'students' => $students
        ]);
    }
}
