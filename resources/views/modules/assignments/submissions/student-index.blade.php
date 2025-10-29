@extends('layouts.app')

@section('title', 'My Submissions History')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Submission History</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">
                    View all your assignment submissions and grades
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                {{-- Total Submissions --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <i class="fas fa-file-upload text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Submissions</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Graded Submissions --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                            <i class="fas fa-star text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Graded</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['graded'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Pending Submissions --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                            <i class="fas fa-clock text-orange-600 dark:text-orange-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['pending'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">All Submissions</h2>
                </div>

                {{-- The check for the variable that was causing the error --}}
                @if($submissions->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($submissions as $submission)
                            <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                {{ $submission->assignment->title }}
                                            </h3>
                                            {{-- Status Badge --}}
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                            {{ $submission->status === 'graded' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                               ($submission->status === 'late' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                                               'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200') }}">
                                                {{ ucfirst($submission->status) }}
                                            </span>
                                        </div>

                                        {{-- Assignment Details --}}
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600 dark:text-gray-400 mb-3">
                                            <div class="flex items-center">
                                                <i class="fas fa-book mr-2 text-purple-500"></i>
                                                <span>{{ $submission->assignment->subject->name }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-user mr-2 text-blue-500"></i>
                                                <span>{{ $submission->assignment->teacher->name }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar mr-2 text-orange-500"></i>
                                                <span>Submitted: {{ $submission->submitted_at->format('M j, Y g:i A') }}</span>
                                            </div>
                                        </div>

                                        {{-- Grading Info --}}
                                        @if($submission->points_obtained !== null)
                                            <div class="flex items-center space-x-4 text-sm mb-2">
                                                <span class="font-medium text-gray-900 dark:text-white">
                                                    Grade: {{ $submission->points_obtained }}/{{ $submission->assignment->max_points }}
                                                </span>
                                                @if($submission->graded_at)
                                                    <span class="text-gray-600 dark:text-gray-400">
                                                        Graded: {{ $submission->graded_at->format('M j, Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Feedback --}}
                                        @if($submission->feedback)
                                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mt-2">
                                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                                    <strong>Feedback:</strong> {{ $submission->feedback }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex items-center space-x-3 ml-4">
                                        {{-- Download Submission Link --}}
                                        <a href="{{ route('student.assignments.submissions.download', ['assignment' => $submission->assignment, 'submission' => $submission]) }}"
                                           class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                                            <i class="fas fa-download mr-2"></i>
                                            Download
                                        </a>

                                        {{-- View Assignment Link --}}
                                        <a href="{{ route('student.assignments.show', $submission->assignment) }}"
                                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                            <i class="fas fa-eye mr-2"></i>
                                            View Assignment
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $submissions->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 text-lg">No submissions yet</p>
                        <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Your submissions will appear here once you submit assignments.</p>
                        <a href="{{ route('student.assignments.index') }}"
                           class="inline-flex items-center px-4 py-2 mt-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-list-check mr-2"></i>
                            View Assignments
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
