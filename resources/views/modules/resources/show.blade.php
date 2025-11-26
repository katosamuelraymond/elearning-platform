@extends('layouts.app')

@section('title', $resource->title . ' - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $resource->title }}</h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-2">Resource Details</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.resources.index') }}"
                           class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Resources
                        </a>
                    </div>
                </div>
            </div>

            <!-- Resource Details -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                                <p class="text-gray-900 dark:text-white">{{ $resource->title }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                <p class="text-gray-900 dark:text-white">
                                    {{ $resource->description ?: 'No description provided' }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subject</label>
                                    <p class="text-gray-900 dark:text-white">{{ $resource->topic->subject->name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Topic</label>
                                    <p class="text-gray-900 dark:text-white">{{ $resource->topic->title }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- File Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">File Information</h2>

                        <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <i class="{{ $resource->file_icon }} text-4xl"></i>
                            <div class="flex-1">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $resource->file_name }}.{{ $resource->file_type }}
                                </h3>
                                <p class="text-gray-600 dark:text-gray-300">
                                    {{ $resource->formatted_file_size }} • {{ strtoupper($resource->file_type) }} File
                                </p>
                            </div>
                            <a href="{{ route('admin.resources.download', $resource) }}"
                               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                                <i class="fas fa-download mr-2"></i>
                                Download
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status & Access -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Status & Access</h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $resource->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ $resource->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Access Level</label>
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $resource->access_level === 'public' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                       ($resource->access_level === 'class_only' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                       'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200') }}">
                                    {{ str_replace('_', ' ', $resource->access_level) }}
                                </span>

                                @if($resource->access_level === 'class_only' && $resource->accessibleClass)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Accessible by: {{ $resource->accessibleClass->name }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Upload Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Upload Information</h2>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Uploaded By</label>
                                <p class="text-gray-900 dark:text-white">{{ $resource->uploadedBy->name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upload Date</label>
                                <p class="text-gray-900 dark:text-white">{{ $resource->created_at->format('M j, Y g:i A') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Updated</label>
                                <p class="text-gray-900 dark:text-white">{{ $resource->updated_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Actions</h2>

                        <div class="space-y-3">
                            <a href="{{ route('admin.resources.download', $resource) }}"
                               class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                                <i class="fas fa-download mr-2"></i>
                                Download File
                            </a>

                            <form action="{{ route('admin.resources.toggle-status', $resource) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <i class="fas {{ $resource->is_active ? 'fa-pause' : 'fa-play' }} mr-2"></i>
                                    {{ $resource->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            <form action="{{ route('admin.resources.destroy', $resource) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Are you sure you want to delete this resource? This action cannot be undone.')"
                                        class="w-full flex items-center justify-center px-4 py-2 border border-red-300 dark:border-red-600 text-red-700 dark:text-red-300 rounded-lg font-medium hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200">
                                    <i class="fas fa-trash mr-2"></i>
                                    Delete Resource
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
