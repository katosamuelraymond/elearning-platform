<?php

namespace App\Http\Controllers\Modules\Subjects;

use App\Http\Controllers\Controller;
use App\Models\Academic\Subject;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSubjectsController extends Controller
{
    /**
     * Display a listing of the subjects.
     */
    public function index(Request $request)
    {
        // Get analytics from all subjects (not paginated)
        $allSubjects = Subject::query();

        // Apply filters for analytics
        if ($request->has('type') && $request->type != '') {
            $allSubjects->where('type', $request->type);
        }

        if ($request->has('status') && $request->status != '') {
            $isActive = $request->status === 'active' ? true : false;
            $allSubjects->where('is_active', $isActive);
        }

        $analyticsSubjects = $allSubjects->get();

        // Get paginated subjects for table
        $query = Subject::query();

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
            $isActive = $request->status === 'active' ? true : false;
            $query->where('is_active', $isActive);
        }

        // Paginate with 10 items per page
        $subjects = $query->latest()->paginate(10);

        $data = [
            'subjects' => $subjects,
            'analyticsSubjects' => $analyticsSubjects,
            'filters' => $request->only(['search', 'type', 'status'])
        ];

        // AJAX request → return only #main-content
        if ($request->ajax()) {
            return view('modules.subjects.partials.table', $data)
                ->renderSections()['content'];
        }

        // Normal full page load
        return view('modules.subjects.index', $data)
            ->with([
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true,

            ]);
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create(Request $request)
    {
        // AJAX request → return only #main-content
        if ($request->ajax()) {
            return view('modules.subjects.create')
                ->renderSections()['content'];
        }

        return view('modules.subjects.create')
            ->with([
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true,
                'pageTitle' => 'Create Subject'
            ]);
    }

    /**
     * Store a newly created subject in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name',
            'code' => 'required|string|max:10|unique:subjects,code',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:compulsory,elective,optional',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'Subject name is required.',
            'name.unique' => 'A subject with this name already exists.',
            'code.required' => 'Subject code is required.',
            'code.unique' => 'A subject with this code already exists.',
            'code.max' => 'Subject code must not exceed 10 characters.',
            'type.required' => 'Subject type is required.',
            'type.in' => 'Please select a valid subject type.'
        ]);

        try {
            DB::beginTransaction();

            // Create the subject using Eloquent
            $subject = Subject::create([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'description' => $validated['description'],
                'type' => $validated['type'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            // Return success response
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Subject created successfully!',
                    'redirect' => route('admin.subjects.index')
                ]);
            }

            return redirect()->route('admin.subjects.index')
                ->with('success', 'Subject created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create subject. Please try again.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return back()->with('error', 'Failed to create subject. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified subject.
     */
    public function edit(Request $request, Subject $subject)
    {
        // AJAX request → return only #main-content
        if ($request->ajax()) {
            return view('modules.subjects.edit', compact('subject'))
                ->renderSections()['content'];
        }

        return view('modules.subjects.edit', compact('subject'))
            ->with([
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true,
                'pageTitle' => 'Edit Subject - ' . $subject->name
            ]);
    }

    /**
     * Update the specified subject in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name,' . $subject->id,
            'code' => 'required|string|max:10|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:compulsory,elective,optional',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'Subject name is required.',
            'name.unique' => 'A subject with this name already exists.',
            'code.required' => 'Subject code is required.',
            'code.unique' => 'A subject with this code already exists.',
            'code.max' => 'Subject code must not exceed 10 characters.',
            'type.required' => 'Subject type is required.',
            'type.in' => 'Please select a valid subject type.'
        ]);

        try {
            DB::beginTransaction();

            // Update the subject using Eloquent
            $subject->update([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'description' => $validated['description'],
                'type' => $validated['type'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            // Return success response
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Subject updated successfully!',
                    'redirect' => route('admin.subjects.index')
                ]);
            }

            return redirect()->route('admin.subjects.index')
                ->with('success', 'Subject updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update subject. Please try again.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return back()->with('error', 'Failed to update subject. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified subject from storage.
     */
    public function destroy(Request $request, Subject $subject)
    {
        try {
            DB::beginTransaction();

            // Check if subject has any dependencies (you can add these later)
            // if ($subject->papers()->exists()) {
            //     throw new \Exception('Cannot delete subject because it has associated papers.');
            // }

            $subject->delete();

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Subject deleted successfully!'
                ]);
            }

            return redirect()->route('admin.subjects.index')
                ->with('success', 'Subject deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            $errorMessage = $e->getMessage();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage ?: 'Failed to delete subject. Please try again.'
                ], 500);
            }

            return redirect()->route('admin.subjects.index')
                ->with('error', $errorMessage ?: 'Failed to delete subject. Please try again.');
        }
    }

    /**
     * Toggle the active status of a subject.
     */
    public function toggleStatus(Request $request, Subject $subject)
    {
        try {
            DB::beginTransaction();

            $subject->update([
                'is_active' => !$subject->is_active
            ]);

            DB::commit();

            $status = $subject->is_active ? 'activated' : 'deactivated';
            $message = "Subject {$status} successfully!";

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'is_active' => $subject->is_active
                ]);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update subject status. Please try again.'
                ], 500);
            }

            return back()->with('error', 'Failed to update subject status. Please try again.');
        }
    }
}
