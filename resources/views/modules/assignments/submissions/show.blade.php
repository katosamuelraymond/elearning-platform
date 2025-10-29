@extends('layouts.app')

@section('title', 'Submission - ' . $submission->student->name . ' - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Submission Details</h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-2">
                            {{ $assignment->title }} • {{ $submission->student->name }}
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.assignments.submissions', $assignment) }}"
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Submissions
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-file-upload mr-3 text-blue-500"></i>
                            Submission Information
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Student</label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $submission->student->name }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $submission->student->email }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Submitted At</label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $submission->submitted_at->format('M j, Y g:i A') }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $submission->submitted_at->diffForHumans() }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                @php
                                    $statusColors = [
                                        'submitted' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                        'graded' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                        'late' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                        'missing' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
                                    ];
                                @endphp
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColors[$submission->status] }}">
                                {{ ucfirst($submission->status) }}
                            </span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">File</label>
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-file text-gray-400"></i>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $submission->original_filename }}</span>
                                    <a href="{{ route('admin.assignments.submissions.download', [$assignment, $submission]) }}"
                                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400" title="Download">
                                        <i class="fas fa-download ml-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        @if($submission->submission_notes)
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Student Notes</label>
                                <p class="text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    {{ $submission->submission_notes }}
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- CORRECTED: Check status directly since isGraded() method doesn't exist on the model --}}
                    @if($submission->status == 'graded')
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="fas fa-graduation-cap mr-3 text-green-500"></i>
                                Grading Information
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Points Obtained</label>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ $submission->points_obtained }}/{{ $assignment->max_points }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Graded By</label>
                                    {{-- Grader is loaded via $submission->load(['grader']) in the controller --}}
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $submission->grader->name ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $submission->graded_at->format('M j, Y g:i A') }}
                                    </p>
                                </div>

                                @if($submission->feedback)
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Feedback</label>
                                        <p class="text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                            {{ $submission->feedback }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('admin.assignments.submissions.download', [$assignment, $submission]) }}"
                               class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                                <i class="fas fa-download mr-2"></i>
                                Download File
                            </a>

                            <a href="{{ route('admin.assignments.submissions.edit', [$assignment, $submission]) }}"
                               class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                                <i class="fas fa-edit mr-2"></i>
                                {{-- CORRECTED: Check status directly --}}
                                {{ $submission->status == 'graded' ? 'Regrade' : 'Grade' }}
                            </a>

                            <form action="{{ route('admin.assignments.submissions.destroy', [$assignment, $submission]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center"
                                        onclick="return confirm('Are you sure you want to delete this submission?')">
                                    <i class="fas fa-trash mr-2"></i>
                                    Delete Submission
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Assignment Info</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Title</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $assignment->title }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Due Date</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $assignment->due_date->format('M j, Y g:i A') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Max Points</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $assignment->max_points }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
