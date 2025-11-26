<?php

namespace App\Http\Controllers\Modules\Resources;

use App\Http\Controllers\Controller;
use App\Models\Teaching\Resource;
use App\Models\Teaching\Topic;
use App\Models\Academic\Subject;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherResourcesController extends Controller
{
    /**
     * Get teacher's assigned classes and subjects
     */
    private function getTeacherAssignments()
    {
        return TeacherAssignment::where('teacher_id', Auth::id())
            ->where('is_active', true)
            ->with(['class', 'subject'])
            ->get();
    }

    /**
     * Get assigned classes for the teacher
     */
    private function getAssignedClasses()
    {
        $assignments = $this->getTeacherAssignments();
        return $assignments->pluck('class')->unique('id')->values();
    }

    /**
     * Get assigned subjects for the teacher
     */
    private function getAssignedSubjects()
    {
        $assignments = $this->getTeacherAssignments();
        return $assignments->pluck('subject')->unique('id')->values();
    }

    /**
     * Validate if teacher is assigned to the class and subject
     */
    private function validateTeacherAssignment($classId, $subjectId)
    {
        return TeacherAssignment::where('teacher_id', Auth::id())
            ->where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Get teacher's assigned classes and subjects
            $assignedClasses = $this->getAssignedClasses();
            $assignedSubjects = $this->getAssignedSubjects();

            // Get assigned class and subject IDs
            $assignedClassIds = $assignedClasses->pluck('id')->toArray();
            $assignedSubjectIds = $assignedSubjects->pluck('id')->toArray();

            // Base query - only show resources for teacher's assigned classes/subjects
            $query = Resource::with(['topic.subject', 'uploadedBy', 'accessibleClass'])
                ->whereHas('topic', function($q) use ($assignedSubjectIds) {
                    $q->whereIn('subject_id', $assignedSubjectIds);
                })
                ->where(function($q) use ($assignedClassIds) {
                    // Resources that are public OR class_only for assigned classes
                    $q->where('access_level', 'public')
                        ->orWhere(function($q2) use ($assignedClassIds) {
                            $q2->where('access_level', 'class_only')
                                ->whereIn('class_id', $assignedClassIds);
                        })
                        ->orWhere('access_level', 'teacher_only');
                });

            // Search functionality
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%")
                        ->orWhere('file_name', 'LIKE', "%{$search}%")
                        ->orWhereHas('topic', function($q) use ($search) {
                            $q->where('title', 'LIKE', "%{$search}%")
                                ->orWhereHas('subject', function($q) use ($search) {
                                    $q->where('name', 'LIKE', "%{$search}%");
                                });
                        })
                        ->orWhereHas('uploadedBy', function($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%");
                        });
                });
            }

            // Filter by subject (only assigned subjects)
            if ($request->has('subject_id') && $request->subject_id != '') {
                $subjectId = $request->subject_id;
                if (in_array($subjectId, $assignedSubjectIds)) {
                    $query->whereHas('topic', function($q) use ($subjectId) {
                        $q->where('subject_id', $subjectId);
                    });
                }
            }

            // Filter by class (only assigned classes)
            if ($request->has('class_id') && $request->class_id != '') {
                $classId = $request->class_id;
                if (in_array($classId, $assignedClassIds)) {
                    $query->where(function($q) use ($classId) {
                        $q->where('access_level', 'public')
                            ->orWhere('access_level', 'teacher_only')
                            ->orWhere(function($q2) use ($classId) {
                                $q2->where('access_level', 'class_only')
                                    ->where('class_id', $classId);
                            });
                    });
                }
            }

            // Filter by file type
            if ($request->has('file_type') && $request->file_type != '') {
                $query->where('file_type', $request->file_type);
            }

            // Filter by access level
            if ($request->has('access_level') && $request->access_level != '') {
                $query->where('access_level', $request->access_level);
            }

            // Filter by status
            if ($request->has('status') && $request->status != '') {
                $query->where('is_active', $request->status === 'active');
            }

            $resources = $query->latest()->paginate(12);

            // Statistics for teacher's view
            $stats = [
                'total' => $query->count(),
                'active' => $query->where('is_active', true)->count(),
                'public' => $query->where('access_level', 'public')->count(),
                'class_only' => $query->where('access_level', 'class_only')->count(),
                'teacher_only' => $query->where('access_level', 'teacher_only')->count(),
            ];

            $fileTypes = Resource::select('file_type')
                ->whereIn('id', $query->pluck('id'))
                ->distinct()
                ->pluck('file_type')
                ->filter();

            return $this->renderView('modules.resources.teacher.index', [
                'resources' => $resources,
                'stats' => $stats,
                'fileTypes' => $fileTypes,
                'classes' => $assignedClasses,
                'subjects' => $assignedSubjects,
                'filters' => $request->only(['search', 'subject_id', 'class_id', 'file_type', 'access_level', 'status']),
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading teacher resources index:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to load resources. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            // Only show classes and subjects the teacher is assigned to
            $classes = $this->getAssignedClasses();
            $subjects = $this->getAssignedSubjects();

            if ($classes->isEmpty() || $subjects->isEmpty()) {
                return redirect()->route('teacher.resources.index')
                    ->with('error', 'You are not assigned to any classes or subjects. Please contact administration.');
            }

            return $this->renderView('modules.resources.teacher.create', [
                'classes' => $classes,
                'subjects' => $subjects,
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading teacher resource creation form:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('teacher.resources.index')
                ->with('error', 'Failed to load resource creation form.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Custom validation logic to handle class_id conditionally
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'subject_id' => 'required|exists:subjects,id',
                'topic_id' => 'required|exists:topics,id',
                'file' => 'required|file|max:102400', // 100MB max
                'access_level' => 'required|in:public,class_only,teacher_only',
                'is_active' => 'boolean'
            ]);

            // Add conditional validation for class_id
            $validator->sometimes('class_id', 'required|exists:school_classes,id', function ($input) {
                return $input->access_level === 'class_only';
            });

            if ($validator->fails()) {
                throw new \Illuminate\Validation\ValidationException($validator);
            }

            // Validate teacher assignment
            if (!$this->validateTeacherAssignment($request->class_id, $request->subject_id)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'You are not assigned to teach this subject for the selected class.');
            }

            // Verify topic belongs to subject
            $topic = Topic::where('id', $request->topic_id)
                ->where('subject_id', $request->subject_id)
                ->first();

            if (!$topic) {
                Log::warning('Topic-subject mismatch during resource creation:', [
                    'topic_id' => $request->topic_id,
                    'subject_id' => $request->subject_id,
                    'user_id' => Auth::id()
                ]);

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Selected topic does not belong to the chosen subject.');
            }

            return DB::transaction(function () use ($request, $topic) {
                try {
                    $file = $request->file('file');
                    $fileSize = $file->getSize() / (1024 * 1024); // Convert to MB
                    $fileType = $file->getClientOriginalExtension();
                    $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                    // Generate unique file name
                    $uniqueFileName = time() . '_' . \Str::slug($fileName) . '.' . $fileType;
                    $filePath = $file->storeAs('resources', $uniqueFileName, 'public');

                    Log::info('File uploaded successfully by teacher:', [
                        'file_path' => $filePath,
                        'file_name' => $fileName,
                        'file_type' => $fileType,
                        'file_size_mb' => round($fileSize, 2),
                        'user_id' => Auth::id()
                    ]);

                    // Only set class_id if access_level is class_only
                    $classId = $request->access_level === 'class_only' ? $request->class_id : null;

                    // Create resource
                    $resource = Resource::create([
                        'topic_id' => $request->topic_id,
                        'uploaded_by' => Auth::id(),
                        'class_id' => $classId,
                        'title' => $request->title,
                        'description' => $request->description,
                        'file_path' => $filePath,
                        'file_name' => $fileName,
                        'file_type' => $fileType,
                        'file_size' => round($fileSize, 2),
                        'access_level' => $request->access_level,
                        'is_active' => $request->has('is_active'),
                    ]);

                    Log::info('Resource created successfully by teacher:', [
                        'resource_id' => $resource->id,
                        'title' => $resource->title,
                        'access_level' => $resource->access_level,
                        'class_id' => $resource->class_id,
                        'user_id' => Auth::id()
                    ]);

                    return redirect()->route('teacher.resources.index')
                        ->with('success', 'Resource uploaded successfully!');

                } catch (\Exception $e) {
                    Log::error('Teacher resource upload failed:', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'user_id' => Auth::id(),
                        'file_name' => $fileName ?? 'unknown',
                        'file_type' => $fileType ?? 'unknown'
                    ]);

                    // Clean up uploaded file if resource creation failed
                    if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                        try {
                            Storage::disk('public')->delete($filePath);
                            Log::info('Cleaned up orphaned file after failed teacher resource creation:', [
                                'file_path' => $filePath
                            ]);
                        } catch (\Exception $cleanupException) {
                            Log::error('Failed to clean up orphaned file:', [
                                'file_path' => $filePath,
                                'error' => $cleanupException->getMessage()
                            ]);
                        }
                    }

                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Failed to upload resource. Please try again.');
                }
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Teacher resource creation validation failed:', [
                'errors' => $e->errors(),
                'user_id' => Auth::id(),
                'request_data' => $request->except('file')
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Teacher resource store failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create resource. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Resource $resource)
    {
        try {
            // Verify teacher has access to this resource
            if (!$this->canTeacherAccessResource($resource)) {
                abort(403, 'You do not have access to this resource.');
            }

            $resource->load(['topic.subject', 'uploadedBy', 'accessibleClass']);

            return $this->renderView('modules.resources.teacher.show', [
                'resource' => $resource,
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Error displaying teacher resource:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'resource_id' => $resource->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('teacher.resources.index')
                ->with('error', 'Failed to load resource details.');
        }
    }

    /**
     * Download the specified resource.
     */
    public function download(Resource $resource)
    {
        try {
            // Verify teacher has access to this resource
            if (!$this->canTeacherAccessResource($resource)) {
                abort(403, 'You do not have access to this resource.');
            }

            if (!Storage::disk('public')->exists($resource->file_path)) {
                Log::error('Resource file not found for teacher download:', [
                    'resource_id' => $resource->id,
                    'file_path' => $resource->file_path,
                    'user_id' => Auth::id()
                ]);

                return redirect()->back()->with('error', 'File not found.');
            }

            Log::info('Resource downloaded by teacher:', [
                'resource_id' => $resource->id,
                'file_name' => $resource->file_name,
                'user_id' => Auth::id()
            ]);

            return Storage::disk('public')->download($resource->file_path, $resource->file_name . '.' . $resource->file_type);

        } catch (\Exception $e) {
            Log::error('Error downloading resource by teacher:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'resource_id' => $resource->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to download resource. Please try again.');
        }
    }

    /**
     * Check if teacher can access the resource
     */
    private function canTeacherAccessResource(Resource $resource)
    {
        // Get teacher's assigned classes and subjects
        $assignedClasses = $this->getAssignedClasses();
        $assignedSubjects = $this->getAssignedSubjects();

        $assignedClassIds = $assignedClasses->pluck('id')->toArray();
        $assignedSubjectIds = $assignedSubjects->pluck('id')->toArray();

        // Check if resource subject is in teacher's assigned subjects
        $resourceSubjectId = $resource->topic->subject_id ?? null;
        if (!$resourceSubjectId || !in_array($resourceSubjectId, $assignedSubjectIds)) {
            return false;
        }

        // Check access level permissions
        switch ($resource->access_level) {
            case 'public':
                return true;
            case 'class_only':
                return in_array($resource->class_id, $assignedClassIds);
            case 'teacher_only':
                return true; // Teachers can always access teacher_only resources
            default:
                return false;
        }
    }

    /**
     * Get topics by subject (AJAX)
     */
    public function getTopicsBySubject(Request $request)
    {
        try {
            $request->validate([
                'subject_id' => 'required|exists:subjects,id'
            ]);

            // Verify teacher is assigned to this subject
            $assignedSubjectIds = $this->getAssignedSubjects()->pluck('id')->toArray();
            if (!in_array($request->subject_id, $assignedSubjectIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not assigned to this subject.'
                ], 403);
            }

            $topics = Topic::where('subject_id', $request->subject_id)
                ->where('is_active', true)
                ->ordered()
                ->get();

            Log::debug('Topics retrieved for subject by teacher:', [
                'subject_id' => $request->subject_id,
                'topics_count' => $topics->count(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'topics' => $topics
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Invalid subject ID for teacher topics request:', [
                'subject_id' => $request->subject_id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid subject ID'
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error fetching topics by subject for teacher:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'subject_id' => $request->subject_id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch topics'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resource $resource)
    {
        try {
            // Verify teacher owns this resource
            if ($resource->uploaded_by !== Auth::id()) {
                abort(403, 'You can only edit resources you uploaded.');
            }

            // Only show classes and subjects the teacher is assigned to
            $classes = $this->getAssignedClasses();
            $subjects = $this->getAssignedSubjects();

            $resource->load(['topic.subject']);

            return $this->renderView('modules.resources.teacher.edit', [
                'resource' => $resource,
                'classes' => $classes,
                'subjects' => $subjects,
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading teacher resource edit form:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'resource_id' => $resource->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('teacher.resources.index')
                ->with('error', 'Failed to load resource edit form.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resource $resource)
    {
        try {
            // Verify teacher owns this resource
            if ($resource->uploaded_by !== Auth::id()) {
                abort(403, 'You can only update resources you uploaded.');
            }

            // Custom validation logic
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'subject_id' => 'required|exists:subjects,id',
                'topic_id' => 'required|exists:topics,id',
                'access_level' => 'required|in:public,class_only,teacher_only',
                'is_active' => 'boolean'
            ]);

            // Add conditional validation for class_id
            $validator->sometimes('class_id', 'required|exists:school_classes,id', function ($input) {
                return $input->access_level === 'class_only';
            });

            if ($validator->fails()) {
                throw new \Illuminate\Validation\ValidationException($validator);
            }

            // Validate teacher assignment
            if (!$this->validateTeacherAssignment($request->class_id, $request->subject_id)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'You are not assigned to teach this subject for the selected class.');
            }

            // Verify topic belongs to subject
            $topic = Topic::where('id', $request->topic_id)
                ->where('subject_id', $request->subject_id)
                ->first();

            if (!$topic) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Selected topic does not belong to the chosen subject.');
            }

            // Only set class_id if access_level is class_only
            $classId = $request->access_level === 'class_only' ? $request->class_id : null;

            $resource->update([
                'topic_id' => $request->topic_id,
                'class_id' => $classId,
                'title' => $request->title,
                'description' => $request->description,
                'access_level' => $request->access_level,
                'is_active' => $request->has('is_active'),
            ]);

            Log::info('Resource updated by teacher:', [
                'resource_id' => $resource->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('teacher.resources.index')
                ->with('success', 'Resource updated successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Teacher resource update validation failed:', [
                'errors' => $e->errors(),
                'user_id' => Auth::id(),
                'resource_id' => $resource->id
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Teacher resource update failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'resource_id' => $resource->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update resource. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resource $resource)
    {
        try {
            // Verify teacher owns this resource
            if ($resource->uploaded_by !== Auth::id()) {
                abort(403, 'You can only delete resources you uploaded.');
            }

            // Delete the file from storage
            if (Storage::disk('public')->exists($resource->file_path)) {
                Storage::disk('public')->delete($resource->file_path);
            }

            $resource->delete();

            Log::info('Resource deleted by teacher:', [
                'resource_id' => $resource->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('teacher.resources.index')
                ->with('success', 'Resource deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Teacher resource deletion failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'resource_id' => $resource->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('teacher.resources.index')
                ->with('error', 'Failed to delete resource. Please try again.');
        }
    }
}
