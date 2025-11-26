<?php

namespace App\Http\Controllers\Modules\Topics;

use App\Http\Controllers\Controller;
use App\Models\Teaching\Topic;
use App\Models\Academic\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminTopicsController extends Controller
{
    /**
     * Check if user is admin
     */
    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Admin privileges required.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkAdmin();

        $query = Topic::with(['subject', 'resources'])->withCount('activeResources');

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhereHas('subject', function($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Filter by subject
        if ($request->has('subject_id') && $request->subject_id != '') {
            $query->where('subject_id', $request->subject_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status === 'active');
        }

        $topics = $query->ordered()->paginate(15);

        $stats = [
            'total' => Topic::count(),
            'active' => Topic::where('is_active', true)->count(),
            'with_resources' => Topic::has('resources')->count(),
        ];

        $subjects = Subject::active()->get();

        return $this->renderView('modules.topics.index', [
            'topics' => $topics,
            'stats' => $stats,
            'subjects' => $subjects,
            'filters' => $request->only(['search', 'subject_id', 'status']),
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
        $this->checkAdmin();

        $subjects = Subject::active()->get();

        return $this->renderView('modules.topics.create', [
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

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'learning_objectives' => 'nullable|string',
            'duration_weeks' => 'required|integer|min:1|max:52',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        try {
            Topic::create([
                'subject_id' => $request->subject_id,
                'title' => $request->title,
                'description' => $request->description,
                'learning_objectives' => $request->learning_objectives,
                'duration_weeks' => $request->duration_weeks,
                'order' => $request->order,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.topics.index')
                ->with('success', 'Topic created successfully!');

        } catch (\Exception $e) {
            Log::error('Topic creation failed:', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create topic. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Topic $topic)
    {
        $this->checkAdmin();

        $topic->load(['subject', 'resources' => function($query) {
            $query->with(['uploadedBy', 'accessibleClass'])->latest();
        }]);

        return $this->renderView('modules.topics.show', [
            'topic' => $topic,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Topic $topic)
    {
        $this->checkAdmin();

        $subjects = Subject::active()->get();

        return $this->renderView('modules.topics.edit', [
            'topic' => $topic,
            'subjects' => $subjects,
            'showNavbar' => true,
            'showSidebar' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Topic $topic)
    {
        $this->checkAdmin();

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'learning_objectives' => 'nullable|string',
            'duration_weeks' => 'required|integer|min:1|max:52',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        try {
            $topic->update([
                'subject_id' => $request->subject_id,
                'title' => $request->title,
                'description' => $request->description,
                'learning_objectives' => $request->learning_objectives,
                'duration_weeks' => $request->duration_weeks,
                'order' => $request->order,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.topics.index')
                ->with('success', 'Topic updated successfully!');

        } catch (\Exception $e) {
            Log::error('Topic update failed:', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update topic. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topic $topic)
    {
        $this->checkAdmin();

        if ($topic->resources()->count() > 0) {
            return redirect()->route('admin.topics.index')
                ->with('error', 'Cannot delete topic that has resources attached. Please delete or move the resources first.');
        }

        try {
            $topic->delete();

            return redirect()->route('admin.topics.index')
                ->with('success', 'Topic deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Topic deletion failed:', ['error' => $e->getMessage()]);
            return redirect()->route('admin.topics.index')
                ->with('error', 'Failed to delete topic. Please try again.');
        }
    }

    /**
     * Toggle topic active status.
     */
    public function toggleStatus(Topic $topic)
    {
        $this->checkAdmin();

        $topic->update([
            'is_active' => !$topic->is_active
        ]);

        $status = $topic->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.topics.index')
            ->with('success', "Topic {$status} successfully!");
    }

    /**
     * Get topics by subject (AJAX)
     */
    public function getBySubject(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'subject_id' => 'required|exists:subjects,id'
        ]);

        $topics = Topic::where('subject_id', $request->subject_id)
            ->where('is_active', true)
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'topics' => $topics
        ]);
    }
}
