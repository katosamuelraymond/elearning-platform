<?php

namespace App\Http\Controllers\Modules\Students;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Academic\SchoolClass;
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

        // Get students with their class information
        $query = User::students()->with(['class']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('student_id', 'LIKE', "%{$search}%")
                    ->orWhereHas('class', function($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Filter by class
        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }

        // Filter by assignment status
        if ($request->has('assignment_status') && $request->assignment_status != '') {
            if ($request->assignment_status === 'assigned') {
                $query->whereNotNull('class_id');
            } elseif ($request->assignment_status === 'unassigned') {
                $query->whereNull('class_id');
            }
        }

        $students = $query->latest()->paginate(20);

        $classes = SchoolClass::where('is_active', true)->orderBy('level')->orderBy('name')->get();

        $stats = [
            'total' => User::students()->count(),
            'assigned' => User::students()->whereNotNull('class_id')->count(),
            'unassigned' => User::students()->whereNull('class_id')->count(),
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

        $students = User::students()->whereNull('class_id')->get();
        $classes = SchoolClass::where('is_active', true)->orderBy('level')->orderBy('name')->get();

        Log::debug('StudentAssignmentController: Create form data loaded', [
            'unassigned_students_count' => $students->count(),
            'classes_count' => $classes->count()
        ]);

        return $this->renderView('modules.students.assignments.create', [
            'students' => $students,
            'classes' => $classes,
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
            'class_id' => 'required|exists:school_classes,id'
        ]);

        try {
            DB::beginTransaction();

            $student = User::findOrFail($validated['student_id']);
            $class = SchoolClass::findOrFail($validated['class_id']);

            // Check if student is already assigned to a class
            if ($student->class_id) {
                $currentClass = SchoolClass::find($student->class_id);
                throw new \Exception("Student is already assigned to class: {$currentClass->name}");
            }

            $student->update([
                'class_id' => $validated['class_id']
            ]);

            DB::commit();

            Log::debug('StudentAssignmentController: Student assigned successfully', [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'class_id' => $class->id,
                'class_name' => $class->name
            ]);

            return redirect()->route('admin.student-assignments.index')
                ->with('success', "Student {$student->name} successfully assigned to class {$class->name}!");

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
            'class_id' => 'required|exists:school_classes,id'
        ]);

        try {
            DB::beginTransaction();

            $class = SchoolClass::findOrFail($validated['class_id']);
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($validated['student_ids'] as $studentId) {
                $student = User::findOrFail($studentId);

                // Check if student is already assigned to a class
                if ($student->class_id) {
                    $currentClass = SchoolClass::find($student->class_id);
                    $errorCount++;
                    $errors[] = "{$student->name} is already assigned to class: {$currentClass->name}";
                    continue;
                }

                $student->update([
                    'class_id' => $validated['class_id']
                ]);

                $successCount++;
                Log::debug('StudentAssignmentController: Student bulk assigned', [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'class_id' => $class->id
                ]);
            }

            DB::commit();

            $message = "{$successCount} students successfully assigned to class {$class->name}!";

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
            'class_id' => 'required|exists:school_classes,id'
        ]);

        try {
            DB::beginTransaction();

            $oldClass = $student->class ? $student->class->name : 'No Class';
            $newClass = SchoolClass::findOrFail($validated['class_id']);

            $student->update([
                'class_id' => $validated['class_id']
            ]);

            DB::commit();

            Log::debug('StudentAssignmentController: Student assignment updated', [
                'student_id' => $student->id,
                'old_class' => $oldClass,
                'new_class' => $newClass->name
            ]);

            return redirect()->route('admin.student-assignments.index')
                ->with('success', "Student {$student->name} successfully moved from {$oldClass} to {$newClass->name}!");

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
            'student_name' => $student->name,
            'current_class' => $student->class ? $student->class->name : 'No Class'
        ]);

        try {
            DB::beginTransaction();

            $className = $student->class ? $student->class->name : 'No Class';

            $student->update([
                'class_id' => null
            ]);

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

        $students = User::students()
            ->whereNull('class_id')
            ->select('id', 'name', 'email', 'student_id')
            ->orderBy('name')
            ->get();

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

        $students = $class->students()
            ->select('id', 'name', 'email', 'student_id')
            ->orderBy('name')
            ->get();

        Log::debug('StudentAssignmentController: Students by class loaded', [
            'class_id' => $class->id,
            'students_count' => $students->count()
        ]);

        return response()->json([
            'students' => $students
        ]);
    }
}
