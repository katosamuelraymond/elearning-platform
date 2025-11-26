<?php

namespace App\Http\Controllers\Modules\Resources;

use App\Http\Controllers\Controller;
use App\Models\Teaching\Resource;
use App\Models\Teaching\Topic;
use App\Models\Academic\Subject;
use App\Models\Academic\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminResourcesController extends Controller
{
    /**
     * Check if user is admin
     */
    private function checkAdmin()
    {
        try {
            if (!Auth::check()) {
                Log::warning('Admin check failed: User not authenticated');
                abort(403, 'Admin privileges required.');
            }

            if (!Auth::user()->isAdmin()) {
                Log::warning('Admin check failed: User does not have admin privileges', [
                    'user_id' => Auth::id(),
                    'user_email' => Auth::user()->email
                ]);
                abort(403, 'Admin privileges required.');
            }
        } catch (\Exception $e) {
            Log::error('Error during admin check:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Error verifying admin privileges.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $this->checkAdmin();

            $query = Resource::with(['topic.subject', 'uploadedBy', 'accessibleClass']);

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

            // Filter by subject
            if ($request->has('subject_id') && $request->subject_id != '') {
                $query->whereHas('topic', function($q) use ($request) {
                    $q->where('subject_id', $request->subject_id);
                });
            }

            // Filter by topic
            if ($request->has('topic_id') && $request->topic_id != '') {
                $query->where('topic_id', $request->topic_id);
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

            $stats = [
                'total' => Resource::count(),
                'active' => Resource::where('is_active', true)->count(),
                'public' => Resource::where('access_level', 'public')->count(),
                'class_only' => Resource::where('access_level', 'class_only')->count(),
                'teacher_only' => Resource::where('access_level', 'teacher_only')->count(),
                'total_size' => Resource::sum('file_size'),
            ];

            $fileTypes = Resource::select('file_type')
                ->distinct()
                ->pluck('file_type')
                ->filter();

            $subjects = Subject::active()->get();
            $topics = Topic::active()->ordered()->get();

            return $this->renderView('modules.resources.index', [
                'resources' => $resources,
                'stats' => $stats,
                'fileTypes' => $fileTypes,
                'subjects' => $subjects,
                'topics' => $topics,
                'filters' => $request->only(['search', 'subject_id', 'topic_id', 'file_type', 'access_level', 'status']),
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading resources index page:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'request_params' => $request->all()
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
            $this->checkAdmin();

            $subjects = Subject::active()->get();
            $classes = SchoolClass::where('is_active', true)->get();

            // Get the pre-selected topic if provided
            $preSelectedTopic = null;
            if (request()->has('topic_id')) {
                $preSelectedTopic = Topic::with('subject')->find(request()->topic_id);
            }

            return $this->renderView('modules.resources.create', [
                'subjects' => $subjects,
                'classes' => $classes,
                'preSelectedTopic' => $preSelectedTopic,
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading resource creation form:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('admin.resources.index')
                ->with('error', 'Failed to load resource creation form.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkAdmin();

        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'subject_id' => 'required|exists:subjects,id',
                'topic_id' => 'required|exists:topics,id',
                'file' => 'required|file|max:102400', // 100MB max
                'access_level' => 'required|in:public,class_only,teacher_only',
                'class_id' => 'required_if:access_level,class_only|exists:school_classes,id',
                'is_active' => 'boolean'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Resource creation validation failed:', [
                'errors' => $e->errors(),
                'user_id' => Auth::id(),
                'request_data' => $request->except('file') // Exclude file for privacy
            ]);
            throw $e;
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

                Log::info('File uploaded successfully:', [
                    'file_path' => $filePath,
                    'file_name' => $fileName,
                    'file_type' => $fileType,
                    'file_size_mb' => round($fileSize, 2),
                    'user_id' => Auth::id()
                ]);

                // Create resource
                $resource = Resource::create([
                    'topic_id' => $request->topic_id,
                    'uploaded_by' => Auth::id(),
                    'class_id' => $request->class_id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'file_path' => $filePath,
                    'file_name' => $fileName,
                    'file_type' => $fileType,
                    'file_size' => round($fileSize, 2),
                    'access_level' => $request->access_level,
                    'is_active' => $request->has('is_active'),
                ]);

                Log::info('Resource created successfully:', [
                    'resource_id' => $resource->id,
                    'title' => $resource->title,
                    'user_id' => Auth::id()
                ]);

                return redirect()->route('admin.resources.index')
                    ->with('success', 'Resource uploaded successfully!');

            } catch (\Exception $e) {
                Log::error('Resource upload failed:', [
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
                        Log::info('Cleaned up orphaned file after failed resource creation:', [
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
    }

    /**
     * Display the specified resource.
     */
    public function show(Resource $resource)
    {
        try {
            $this->checkAdmin();

            $resource->load(['topic.subject', 'uploadedBy', 'accessibleClass']);

            return $this->renderView('modules.resources.show', [
                'resource' => $resource,
                'showNavbar' => true,
                'showSidebar' => true,
                'showFooter' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Error displaying resource:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'resource_id' => $resource->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('admin.resources.index')
                ->with('error', 'Failed to load resource details.');
        }
    }

    /**
     * Download the specified resource.
     */
    public function download(Resource $resource)
    {
        try {
            $this->checkAdmin();

            if (!Storage::disk('public')->exists($resource->file_path)) {
                Log::error('Resource file not found for download:', [
                    'resource_id' => $resource->id,
                    'file_path' => $resource->file_path,
                    'user_id' => Auth::id()
                ]);

                return redirect()->back()->with('error', 'File not found.');
            }

            Log::info('Resource downloaded by admin:', [
                'resource_id' => $resource->id,
                'file_name' => $resource->file_name,
                'user_id' => Auth::id()
            ]);

            // Increment download count if you add that field later
            // $resource->increment('download_count');

            return Storage::disk('public')->download($resource->file_path, $resource->file_name . '.' . $resource->file_type);

        } catch (\Exception $e) {
            Log::error('Error downloading resource:', [
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
     * Toggle resource active status.
     */
    public function toggleStatus(Resource $resource)
    {
        try {
            $this->checkAdmin();

            $oldStatus = $resource->is_active;
            $resource->update([
                'is_active' => !$resource->is_active
            ]);

            $status = $resource->is_active ? 'activated' : 'deactivated';

            Log::info('Resource status toggled:', [
                'resource_id' => $resource->id,
                'old_status' => $oldStatus,
                'new_status' => $resource->is_active,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('admin.resources.index')
                ->with('success', "Resource {$status} successfully!");

        } catch (\Exception $e) {
            Log::error('Error toggling resource status:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'resource_id' => $resource->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('admin.resources.index')
                ->with('error', 'Failed to update resource status.');
        }
    }

    /**
     * Get topics by subject (AJAX)
     */
    public function getTopicsBySubject(Request $request)
    {
        try {
            $this->checkAdmin();

            $request->validate([
                'subject_id' => 'required|exists:subjects,id'
            ]);

            $topics = Topic::where('subject_id', $request->subject_id)
                ->where('is_active', true)
                ->ordered()
                ->get();

            Log::debug('Topics retrieved for subject:', [
                'subject_id' => $request->subject_id,
                'topics_count' => $topics->count(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'topics' => $topics
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Invalid subject ID for topics request:', [
                'subject_id' => $request->subject_id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid subject ID'
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error fetching topics by subject:', [
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
     * Bulk actions for resources
     */
    public function bulkAction(Request $request)
    {
        try {
            $this->checkAdmin();

            $request->validate([
                'action' => 'required|in:activate,deactivate,delete',
                'resources' => 'required|array',
                'resources.*' => 'exists:resources,id'
            ]);

            $resources = Resource::whereIn('id', $request->resources)->get();

            if ($resources->isEmpty()) {
                Log::warning('Bulk action attempted with no valid resources:', [
                    'action' => $request->action,
                    'resource_ids' => $request->resources,
                    'user_id' => Auth::id()
                ]);

                return redirect()->route('admin.resources.index')
                    ->with('error', 'No valid resources selected.');
            }

            $action = $request->action;
            $resourceIds = $resources->pluck('id')->toArray();

            switch ($action) {
                case 'activate':
                    $resources->each->update(['is_active' => true]);
                    $message = 'Selected resources activated successfully!';
                    break;
                case 'deactivate':
                    $resources->each->update(['is_active' => false]);
                    $message = 'Selected resources deactivated successfully!';
                    break;
                case 'delete':
                    $resources->each(function($resource) {
                        try {
                            if (Storage::disk('public')->exists($resource->file_path)) {
                                Storage::disk('public')->delete($resource->file_path);
                            }
                            $resource->delete();
                        } catch (\Exception $e) {
                            Log::error('Error deleting resource in bulk action:', [
                                'resource_id' => $resource->id,
                                'error' => $e->getMessage()
                            ]);
                            throw $e;
                        }
                    });
                    $message = 'Selected resources deleted successfully!';
                    break;
            }

            Log::info('Bulk action completed successfully:', [
                'action' => $action,
                'resource_count' => $resources->count(),
                'resource_ids' => $resourceIds,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('admin.resources.index')
                ->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Bulk action validation failed:', [
                'errors' => $e->errors(),
                'action' => $request->action,
                'resources' => $request->resources,
                'user_id' => Auth::id()
            ]);
            throw $e;

        } catch (\Exception $e) {
            Log::error('Bulk action failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'action' => $request->action,
                'resources' => $request->resources,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('admin.resources.index')
                ->with('error', 'Failed to perform bulk action. Please try again.');
        }
    }
}
