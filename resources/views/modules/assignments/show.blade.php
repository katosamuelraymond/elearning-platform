@extends('layouts.app')

@section('title', $assignment->title . ' - Lincoln eLearning')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $assignment->title }}</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">
                    {{ $assignment->subject->name }} • {{ $assignment->class->name }}
                    @if($assignment->teacher)
                        • Created by {{ $assignment->teacher->name }}
                    @endif
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.assignments.edit', $assignment->id) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Assignment
                </a>
                <form action="{{ route('admin.assignments.toggle-publish', $assignment->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="px-4 py-2 border {{ $assignment->is_published ? 'border-orange-600 text-orange-600 dark:text-orange-400' : 'border-green-600 text-green-600 dark:text-green-400' }} rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center">
                        <i class="fas {{ $assignment->is_published ? 'fa-eye-slash' : 'fa-eye' }} mr-2"></i>
                        {{ $assignment->is_published ? 'Unpublish' : 'Publish' }}
                    </button>
                </form>
                <a href="{{ route('admin.assignments.index') }}"
                   class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Assignment Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-3 text-blue-500"></i>
                        Assignment Details
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Due Date</h3>
                            <p class="text-lg text-gray-900 dark:text-white mt-1 flex items-center">
                                <i class="fas fa-calendar-day mr-2 text-orange-500"></i>
                                {{ $assignment->due_date->format('F j, Y \a\t g:i A') }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $assignment->due_date->isPast() ? 'Overdue' : 'Due' }} {{ $assignment->due_date->diffForHumans() }}
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Maximum Points</h3>
                            <p class="text-lg text-gray-900 dark:text-white mt-1 flex items-center">
                                <i class="fas fa-star mr-2 text-yellow-500"></i>
                                {{ $assignment->max_points }} points
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h3>
                            <p class="mt-1">
                                @if($assignment->is_published)
                                    <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-sm font-medium rounded-full">
                                        Published
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm font-medium rounded-full">
                                        Draft
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">File Requirements</h3>
                            <p class="text-sm text-gray-900 dark:text-white mt-1">
                                Max size: {{ $assignment->max_file_size }}MB<br>
                                Formats: {{ implode(', ', $assignment->allowed_formats) }}
                            </p>
                        </div>
                    </div>

                    @if($assignment->instructions)
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Instructions</h3>
                        <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4">
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $assignment->instructions }}</p>
                        </div>
                    </div>
                    @endif

                    @if($assignment->description)
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</h3>
                        <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4">
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $assignment->description }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Submissions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-users mr-3 text-green-500"></i>
                        Submissions ({{ $assignment->submissions->count() }})
                    </h2>

                    @if($assignment->submissions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Student</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Submitted</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Grade</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($assignment->submissions as $submission)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ $submission->student->name ?? 'Unknown Student' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $submission->submitted_at->format('M j, Y g:i A') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            {{ $submission->status == 'graded' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                               ($submission->status == 'late' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                                               'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200') }}">
                                            {{ ucfirst($submission->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        @if($submission->points_obtained)
                                            {{ $submission->points_obtained }}/{{ $assignment->max_points }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">No submissions yet.</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Students will appear here once they submit their work.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Stats -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Assignment Stats</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Submissions</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $assignment->submissions->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Graded</span>
                            <span class="font-semibold text-gray-900 dark:text-white">
                                {{ $assignment->submissions->where('status', 'graded')->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Pending Review</span>
                            <span class="font-semibold text-gray-900 dark:text-white">
                                {{ $assignment->submissions->where('status', 'submitted')->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Late Submissions</span>
                            <span class="font-semibold text-gray-900 dark:text-white">
                                {{ $assignment->submissions->where('status', 'late')->count() }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.assignments.edit', $assignment->id) }}"
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Assignment
                        </a>
                        <form action="{{ route('admin.assignments.toggle-publish', $assignment->id) }}" method="POST" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="w-full {{ $assignment->is_published ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                                <i class="fas {{ $assignment->is_published ? 'fa-eye-slash' : 'fa-eye' }} mr-2"></i>
                                {{ $assignment->is_published ? 'Unpublish' : 'Publish' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.assignments.destroy', $assignment->id) }}" method="POST" class="w-full" onsubmit="return confirm('Are you sure you want to delete this assignment? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                                <i class="fas fa-trash mr-2"></i>
                                Delete Assignment
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Assignment Info -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Assignment Info</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Created</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $assignment->created_at->format('M j, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Last Updated</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $assignment->updated_at->format('M j, Y \a\t g:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Class</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $assignment->class->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Subject</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $assignment->subject->name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
