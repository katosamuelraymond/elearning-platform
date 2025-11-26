@extends('layouts.app')

@section('title', $topic->title . ' - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $topic->title }}</h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-2">Topic Details & Resources</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.topics.index') }}"
                           class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Topics
                        </a>
                        <a href="{{ route('admin.topics.edit', $topic) }}"
                           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Topic
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Topic Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Topic Information</h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subject</label>
                                <p class="text-gray-900 dark:text-white">{{ $topic->subject->name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                <p class="text-gray-900 dark:text-white">
                                    {{ $topic->description ?: 'No description provided' }}
                                </p>
                            </div>

                            @if($topic->learning_objectives)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Learning Objectives</label>
                                    <div class="prose dark:prose-invert max-w-none">
                                        {!! nl2br(e($topic->learning_objectives)) !!}
                                    </div>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Duration</label>
                                    <p class="text-gray-900 dark:text-white">
                                        {{ $topic->duration_weeks }} week{{ $topic->duration_weeks > 1 ? 's' : '' }}
                                        (approx. {{ $topic->duration_hours }} hours)
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Display Order</label>
                                    <p class="text-gray-900 dark:text-white">{{ $topic->order }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resources -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Resources</h2>
                            <span class="px-3 py-1 text-sm bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                                {{ $topic->resources->count() }} resource{{ $topic->resources->count() != 1 ? 's' : '' }}
                            </span>
                        </div>

                        @if($topic->resources->count() > 0)
                            <div class="space-y-4">
                                @foreach($topic->resources as $resource)
                                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                                        <div class="flex items-center space-x-4">
                                            <i class="{{ $resource->file_icon }} text-2xl"></i>
                                            <div>
                                                <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $resource->title }}
                                                </h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $resource->file_name }}.{{ $resource->file_type }} • {{ $resource->formatted_file_size }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                {{ $resource->access_level === 'public' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                                   ($resource->access_level === 'class_only' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                                   'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200') }}">
                                                {{ str_replace('_', ' ', $resource->access_level) }}
                                            </span>
                                            <a href="{{ route('admin.resources.download', $resource) }}"
                                               class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                               title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="{{ route('admin.resources.show', $resource) }}"
                                               class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-file-alt text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">No resources uploaded for this topic yet.</p>
                                <a href="{{ route('admin.resources.upload') }}?topic_id={{ $topic->id }}"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                                    <i class="fas fa-upload mr-2"></i>
                                    Upload Resource
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status & Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Status & Information</h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $topic->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ $topic->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Created</label>
                                <p class="text-gray-900 dark:text-white">{{ $topic->created_at->format('M j, Y') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Updated</label>
                                <p class="text-gray-900 dark:text-white">{{ $topic->updated_at->format('M j, Y') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Resources Count</label>
                                <p class="text-gray-900 dark:text-white">
                                    {{ $topic->active_resources_count }} active resource{{ $topic->active_resources_count != 1 ? 's' : '' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Topic Navigation</h2>

                        <div class="space-y-3">
                            @php
                                $previousTopic = $topic->previousTopic();
                                $nextTopic = $topic->nextTopic();
                            @endphp

                            @if($previousTopic)
                                <a href="{{ route('admin.topics.show', $previousTopic) }}"
                                   class="w-full flex items-center justify-between p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <div class="flex items-center">
                                        <i class="fas fa-arrow-left text-gray-400 mr-3"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">Previous</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Str::limit($previousTopic->title, 30) }}</p>
                                        </div>
                                    </div>
                                </a>
                            @else
                                <div class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center">No previous topic</p>
                                </div>
                            @endif

                            @if($nextTopic)
                                <a href="{{ route('admin.topics.show', $nextTopic) }}"
                                   class="w-full flex items-center justify-between p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <div class="flex items-center">
                                        <i class="fas fa-arrow-right text-gray-400 mr-3"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">Next</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Str::limit($nextTopic->title, 30) }}</p>
                                        </div>
                                    </div>
                                </a>
                            @else
                                <div class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center">No next topic</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Actions</h2>

                        <div class="space-y-3">
                            <a href="{{ route('admin.resources.upload') }}?topic_id={{ $topic->id }}"
                               class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                                <i class="fas fa-upload mr-2"></i>
                                Upload Resource
                            </a>

                            <a href="{{ route('admin.topics.edit', $topic) }}"
                               class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Topic
                            </a>

                            <form action="{{ route('admin.topics.toggle-status', $topic) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <i class="fas {{ $topic->is_active ? 'fa-pause' : 'fa-play' }} mr-2"></i>
                                    {{ $topic->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            @if($topic->can_delete)
                                <form action="{{ route('admin.topics.destroy', $topic) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this topic? This action cannot be undone.')"
                                            class="w-full flex items-center justify-center px-4 py-2 border border-red-300 dark:border-red-600 text-red-700 dark:text-red-300 rounded-lg font-medium hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200">
                                        <i class="fas fa-trash mr-2"></i>
                                        Delete Topic
                                    </button>
                                </form>
                            @else
                                <div class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                        Cannot delete - has resources
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
