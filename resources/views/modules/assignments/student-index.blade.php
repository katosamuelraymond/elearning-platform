@extends('layouts.app')

@section('title', 'Available Assignments')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Available Assignments</h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-2">
                        View assignments for your class. Due dates and submission status are listed below.
                    </p>
                </div>
                <a href="{{ route('student.assignments.my-submissions') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                    <i class="fas fa-history mr-2"></i>
                    My Submissions
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                {{-- Total Assignments --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <i class="fas fa-list-check text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Assignments</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Submitted --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                            <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Submitted</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['submitted'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Graded --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                            <i class="fas fa-star text-orange-600 dark:text-orange-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Graded</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['graded'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Assignments for your Class</h2>
                </div>

                @if($assignments->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($assignments as $assignment)
                            {{-- Check if the student has a submission for this assignment --}}
                            @php
                                $submission = $assignment->submissions->first();
                                $status = 'pending';
                                if ($submission) {
                                    $status = $submission->status;
                                } elseif (now()->gt($assignment->due_date)) {
                                    $status = 'missing';
                                }
                            @endphp

                            <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                                {{ $assignment->title }}
                                            </h3>
                                            {{-- Status Badge --}}
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @if($status === 'graded') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($status === 'submitted' || $status === 'late') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @elseif($status === 'missing') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @endif">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </div>

                                        {{-- Assignment Details --}}
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600 dark:text-gray-400 mt-2">
                                            <div class="flex items-center">
                                                <i class="fas fa-book mr-2 text-purple-500"></i>
                                                <span>{{ $assignment->subject->name }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-user mr-2 text-blue-500"></i>
                                                <span>{{ $assignment->teacher->name }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar-alt mr-2 text-orange-500"></i>
                                                <span>Due: {{ \Carbon\Carbon::parse($assignment->due_date)->format('M j, Y g:i A') }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-percent mr-2 text-gray-500"></i>
                                                <span>Max Points: {{ $assignment->max_points }}</span>
                                            </div>
                                        </div>

                                        @if($submission && $submission->points_obtained !== null)
                                            <p class="mt-3 text-sm font-medium text-gray-900 dark:text-white">
                                                Your Grade: <span class="text-green-600 dark:text-green-400">{{ $submission->points_obtained }}/{{ $assignment->max_points }}</span>
                                            </p>
                                        @endif
                                    </div>

                                    <div class="ml-4">
                                        {{-- View Assignment Button --}}
                                        <a href="{{ route('student.assignments.show', $assignment) }}"
                                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                            <i class="fas fa-eye mr-2"></i>
                                            @if($submission)
                                                View/Resubmit
                                            @else
                                                View & Submit
                                            @endif
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $assignments->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-clipboard-check text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 text-lg">No assignments currently available</p>
                        <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Check back later for new tasks from your teachers.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
