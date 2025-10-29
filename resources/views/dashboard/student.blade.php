@extends('layouts.app')

@section('title', 'Student Dashboard - Lincoln High School')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Student Dashboard Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Student Dashboard</h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-2">Welcome back, {{ auth()->user()->name }}!</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Academic Year 2024</p>
                        <p class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                            @php
                                $currentClass = \App\Models\Academic\StudentClassAssignment::where('student_id', auth()->id())
                                    ->where('status', 'active')
                                    ->with('class')
                                    ->first();
                            @endphp
                            {{ $currentClass ? $currentClass->class->name : 'No Active Class' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Student Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Upcoming Assignments -->
                <a href="{{ route('student.assignments.index') }}"
                   class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-l-4 border-red-500 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1 cursor-pointer group">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30 group-hover:bg-red-200 dark:group-hover:bg-red-800/50 transition-colors">
                            <i class="fas fa-file-alt text-red-600 dark:text-red-400"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Pending Assignments</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                @php
                                    $pendingAssignments = \App\Models\Assessment\Assignment::where('class_id', $currentClass?->class_id ?? 0)
                                        ->where('is_published', true)
                                        ->whereDoesntHave('submissions', function($query) {
                                            $query->where('student_id', auth()->id());
                                        })
                                        ->count();
                                @endphp
                                {{ $pendingAssignments }}
                            </p>
                            <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                                Need your attention
                            </p>
                        </div>
                    </div>
                </a>

                <!-- Submitted Work -->
                <a href="{{ route('student.assignments.my-submissions') }}"
                   class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-l-4 border-orange-500 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1 cursor-pointer group">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100 dark:bg-orange-900/30 group-hover:bg-orange-200 dark:group-hover:bg-orange-800/50 transition-colors">
                            <i class="fas fa-tasks text-orange-600 dark:text-orange-400"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Submitted Work</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ \App\Models\Assessment\AssignmentSubmission::where('student_id', auth()->id())->count() }}
                            </p>
                            <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">
                                All submissions
                            </p>
                        </div>
                    </div>
                </a>

                <!-- Graded Assignments -->
                <a href="{{ route('student.assignments.my-submissions') }}"
                   class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-l-4 border-green-500 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1 cursor-pointer group">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 group-hover:bg-green-200 dark:group-hover:bg-green-800/50 transition-colors">
                            <i class="fas fa-star text-green-600 dark:text-green-400"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Graded Work</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ \App\Models\Assessment\AssignmentSubmission::where('student_id', auth()->id())->where('status', 'graded')->count() }}
                            </p>
                            <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                                With feedback
                            </p>
                        </div>
                    </div>
                </a>

                <!-- Active Subjects -->
                <a href="{{ route('student.subjects.index') }}"
                   class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-l-4 border-blue-500 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1 cursor-pointer group">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 transition-colors">
                            <i class="fas fa-book text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">My Subjects</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                @php
                                    $subjectCount = \App\Models\Assessment\Assignment::where('class_id', $currentClass?->class_id ?? 0)
                                        ->where('is_published', true)
                                        ->distinct('subject_id')
                                        ->count('subject_id');
                                @endphp
                                {{ $subjectCount }}
                            </p>
                            <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                With assignments
                            </p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Priority Items -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Today's Priorities -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Upcoming Assignments</h2>
                        <div class="space-y-4">
                            @php
                                $upcomingAssignments = \App\Models\Assessment\Assignment::where('class_id', $currentClass?->class_id ?? 0)
                                    ->where('is_published', true)
                                    ->where('due_date', '>', now())
                                    ->whereDoesntHave('submissions', function($query) {
                                        $query->where('student_id', auth()->id());
                                    })
                                    ->orderBy('due_date')
                                    ->take(3)
                                    ->get();
                            @endphp

                            @if($upcomingAssignments->count() > 0)
                                @foreach($upcomingAssignments as $assignment)
                                    <div class="flex items-center justify-between p-4 rounded-lg bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-800">
                                        <div class="flex items-center">
                                            <div class="p-2 rounded-full bg-orange-100 dark:bg-orange-800 mr-4">
                                                <i class="fas fa-file-pen text-orange-600 dark:text-orange-400"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white">{{ $assignment->title }}</p>
                                                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $assignment->subject->name }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-orange-600 dark:text-orange-400 font-medium">
                                                Due: {{ $assignment->due_date->diffForHumans() }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $assignment->due_date->format('M j, g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                                    <p class="text-gray-500 dark:text-gray-400">No upcoming assignments!</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500">You're all caught up.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Access -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Quick Access</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <a href="{{ route('student.assignments.index') }}"
                               class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200 group">
                                <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30 group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 transition-colors">
                                    <i class="fas fa-list-check text-blue-600 dark:text-blue-400 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <span class="text-gray-700 dark:text-gray-200 font-medium">My Assignments</span>
                                </div>
                            </a>

                            <a href="{{ route('student.assignments.my-submissions') }}"
                               class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors duration-200 group">
                                <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900/30 group-hover:bg-green-200 dark:group-hover:bg-green-800/50 transition-colors">
                                    <i class="fas fa-history text-green-600 dark:text-green-400 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <span class="text-gray-700 dark:text-gray-200 font-medium">My Submissions</span>
                                </div>
                            </a>

                            <a href="{{ route('student.subjects.index') }}"
                               class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors duration-200 group">
                                <div class="p-3 rounded-lg bg-purple-100 dark:bg-purple-900/30 group-hover:bg-purple-200 dark:group-hover:bg-purple-800/50 transition-colors">
                                    <i class="fas fa-book text-purple-600 dark:text-purple-400 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <span class="text-gray-700 dark:text-gray-200 font-medium">My Subjects</span>
                                </div>
                            </a>

                            <a href="{{ route('student.grades.index') }}"
                               class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors duration-200 group">
                                <div class="p-3 rounded-lg bg-orange-100 dark:bg-orange-900/30 group-hover:bg-orange-200 dark:group-hover:bg-orange-800/50 transition-colors">
                                    <i class="fas fa-chart-line text-orange-600 dark:text-orange-400 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <span class="text-gray-700 dark:text-gray-200 font-medium">My Grades</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Progress & Recent -->
                <div class="space-y-6">
                    <!-- Recent Submissions -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Recent Submissions</h2>
                        <div class="space-y-3">
                            @php
                                $recentSubmissions = \App\Models\Assessment\AssignmentSubmission::where('student_id', auth()->id())
                                    ->with('assignment')
                                    ->latest()
                                    ->take(3)
                                    ->get();
                            @endphp

                            @if($recentSubmissions->count() > 0)
                                @foreach($recentSubmissions as $submission)
                                    <div class="flex justify-between items-center p-3 rounded-lg
                                        {{ $submission->status === 'graded' ? 'bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800' :
                                           'bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800' }}">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $submission->assignment->title }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                                {{ $submission->submitted_at->format('M j') }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            @if($submission->points_obtained)
                                                <p class="{{ $submission->status === 'graded' ? 'text-green-600 dark:text-green-400' : 'text-blue-600 dark:text-blue-400' }} font-bold">
                                                    {{ $submission->points_obtained }}/{{ $submission->assignment->max_points }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ ucfirst($submission->status) }}
                                                </p>
                                            @else
                                                <p class="text-blue-600 dark:text-blue-400 font-bold">Submitted</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Pending</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox text-gray-400 text-2xl mb-2"></i>
                                    <p class="text-gray-500 dark:text-gray-400">No submissions yet</p>
                                </div>
                            @endif

                            <a href="{{ route('student.assignments.my-submissions') }}" class="block text-center text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium pt-2">
                                View All Submissions
                            </a>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Quick Stats</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Assignments Due</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $pendingAssignments }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Submitted This Week</span>
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ \App\Models\Assessment\AssignmentSubmission::where('student_id', auth()->id())->where('submitted_at', '>=', now()->subWeek())->count() }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Average Grade</span>
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    @php
                                        $gradedSubmissions = \App\Models\Assessment\AssignmentSubmission::where('student_id', auth()->id())->whereNotNull('points_obtained')->get();
                                        $average = $gradedSubmissions->count() > 0 ? $gradedSubmissions->avg('points_obtained') : 0;
                                    @endphp
                                    {{ number_format($average, 1) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
