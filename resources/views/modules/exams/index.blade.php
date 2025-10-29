@extends('layouts.app')

@section('title', 'Exams - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Exams</h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-2">
                        Manage and monitor all exams
                    </p>
                </div>
                <a href="{{ route(auth()->user()->isAdmin() ? 'admin.exams.create' : 'teacher.exams.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Create New Exam
                </a>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <i class="fas fa-file-alt text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Exams</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                            <i class="fas fa-eye text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Published</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['published'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            <i class="fas fa-edit text-gray-600 dark:text-gray-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Draft</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['draft'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                            <i class="fas fa-users text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Attempts</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['attempts'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exams List -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">All Exams</h2>
                </div>

                @if($exams->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($exams as $exam)
                            @php
                                $isAdmin = auth()->user()->isAdmin();
                                $baseRoute = $isAdmin ? 'admin.exams' : 'teacher.exams';
                                $isActive = $exam->start_time <= now() && $exam->end_time >= now();
                                $isUpcoming = $exam->start_time > now();
                                $isPast = $exam->end_time < now();
                            @endphp

                            <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                <a href="{{ route($baseRoute . '.show', $exam) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                    {{ $exam->title }}
                                                </a>
                                            </h3>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                            {{ $exam->is_published ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                            {{ $exam->is_published ? 'Published' : 'Draft' }}
                                        </span>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                            {{ $isActive ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                               ($isUpcoming ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                               'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200') }}">
                                            {{ $isActive ? 'Active' : ($isUpcoming ? 'Upcoming' : 'Completed') }}
                                        </span>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm text-gray-600 dark:text-gray-400 mb-3">
                                            <div class="flex items-center">
                                                <i class="fas fa-book mr-2 text-purple-500"></i>
                                                <span>{{ $exam->subject->name }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-users mr-2 text-blue-500"></i>
                                                <span>{{ $exam->class->name }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-clock mr-2 text-orange-500"></i>
                                                <span>{{ $exam->duration }} mins</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-star mr-2 text-yellow-500"></i>
                                                <span>{{ $exam->total_marks }} marks</span>
                                            </div>
                                        </div>

                                        <div class="flex items-center space-x-4 text-sm">
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar-start mr-2 text-green-500"></i>
                                                <span>Starts: {{ $exam->start_time->format('M j, Y g:i A') }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar-end mr-2 text-red-500"></i>
                                                <span>Ends: {{ $exam->end_time->format('M j, Y g:i A') }}</span>
                                            </div>
                                        </div>

                                        @if($exam->attempts_count > 0)
                                            <div class="mt-2">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $exam->attempts_count }} attempts •
                                                {{ $exam->submitted_count ?? 0 }} submitted
                                            </span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex items-center space-x-3 ml-4">
                                        <a href="{{ route($baseRoute . '.attempts', $exam) }}"
                                           class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                                            <i class="fas fa-users mr-2"></i>
                                            Attempts
                                        </a>

                                        <a href="{{ route($baseRoute . '.show', $exam) }}"
                                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                            <i class="fas fa-eye mr-2"></i>
                                            View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $exams->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-file-alt text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 text-lg">No exams created yet</p>
                        <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Get started by creating your first exam.</p>
                        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.exams.create' : 'teacher.exams.create') }}"
                           class="inline-flex items-center px-4 py-2 mt-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Create Exam
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
