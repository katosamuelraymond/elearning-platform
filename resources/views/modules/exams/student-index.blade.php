@extends('layouts.app')

@section('title', 'My Exams - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Exams</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">
                    View and take your scheduled exams
                </p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
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

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                            <i class="fas fa-play text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Available Now</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['available'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                            <i class="fas fa-clock text-orange-600 dark:text-orange-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Upcoming</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['upcoming'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                            <i class="fas fa-check-circle text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Completed</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['completed'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exams List -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Available Exams</h2>
                </div>

                @if($exams->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($exams as $exam)
                            @php
                                $attempt = $exam->attempts->first();
                                $isActive = $exam->start_time <= now() && $exam->end_time >= now();
                                $isUpcoming = $exam->start_time > now();
                                $isPast = $exam->end_time < now();
                                $canTakeExam = $isActive && (!$attempt || $exam->attempts->count() < $exam->max_attempts);
                            @endphp

                            <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                <a href="{{ route('student.exams.show', $exam) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                    {{ $exam->title }}
                                                </a>
                                            </h3>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                            {{ $isActive ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                               ($isUpcoming ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                               'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200') }}">
                                            {{ $isActive ? 'Active' : ($isUpcoming ? 'Upcoming' : 'Completed') }}
                                        </span>
                                            @if($attempt)
                                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                                {{ $attempt->status === 'graded' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                                   ($attempt->status === 'submitted' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                                   'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200') }}">
                                                {{ ucfirst($attempt->status) }}
                                            </span>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm text-gray-600 dark:text-gray-400 mb-3">
                                            <div class="flex items-center">
                                                <i class="fas fa-book mr-2 text-purple-500"></i>
                                                <span>{{ $exam->subject->name }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-user mr-2 text-blue-500"></i>
                                                <span>{{ $exam->teacher->name }}</span>
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

                                        @if($attempt && $attempt->total_score !== null)
                                            <div class="mt-2">
                                            <span class="font-medium text-gray-900 dark:text-white">
                                                Score: {{ $attempt->total_score }}/{{ $exam->total_marks }}
                                            </span>
                                                @if($attempt->total_score >= $exam->passing_marks)
                                                    <span class="ml-2 text-green-600 dark:text-green-400">✓ Passed</span>
                                                @else
                                                    <span class="ml-2 text-red-600 dark:text-red-400">✗ Failed</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex items-center space-x-3 ml-4">
                                        @if($canTakeExam)
                                            <a href="{{ route('student.exams.start', $exam) }}"
                                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                                <i class="fas fa-play mr-2"></i>
                                                {{ $attempt ? 'Continue' : 'Start Exam' }}
                                            </a>
                                        @elseif($attempt && $exam->show_results)
                                            <a href="{{ route('student.exams.results', ['exam' => $exam, 'attempt' => $attempt]) }}"
                                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                                <i class="fas fa-chart-bar mr-2"></i>
                                                View Results
                                            </a>
                                        @elseif($isUpcoming)
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                            Starts {{ $exam->start_time->diffForHumans() }}
                                        </span>
                                        @endif

                                        <a href="{{ route('student.exams.show', $exam) }}"
                                           class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Details
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
                        <p class="text-gray-500 dark:text-gray-400 text-lg">No exams available</p>
                        <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">You don't have any exams scheduled for your class yet.</p>
                    </div>
                @endif
            </div>

            <!-- Quick Links -->
            <div class="mt-8 flex justify-center">
                <a href=""
                   class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-history mr-3"></i>
                    View My Exam History
                </a>
            </div>
        </div>
    </div>
@endsection
