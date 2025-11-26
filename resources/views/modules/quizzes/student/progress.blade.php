@extends('layouts.app')

@section('title', 'My Quiz Progress - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Quiz Progress</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">Track your quiz performance and improvement</p>
            </div>

            <!-- Navigation Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8">
                        <a href="{{ route('student.quizzes.index') }}"
                           class="border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 whitespace-nowrap py-4 px-1 text-sm font-medium">
                            Available Quizzes
                        </a>
                        <a href="{{ route('student.quizzes.upcoming') }}"
                           class="border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 whitespace-nowrap py-4 px-1 text-sm font-medium">
                            Upcoming Quizzes
                        </a>
                        <a href="{{ route('student.quizzes.completed') }}"
                           class="border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 whitespace-nowrap py-4 px-1 text-sm font-medium">
                            Completed Quizzes
                        </a>
                        <a href="{{ route('student.quizzes.progress') }}"
                           class="border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 whitespace-nowrap py-4 px-1 text-sm font-medium">
                            My Progress
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Overall Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400">
                            <i class="fas fa-file-alt text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Quizzes</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $overallStats['total_quizzes'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completed</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $overallStats['completed_quizzes'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400">
                            <i class="fas fa-chart-line text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Average Score</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($overallStats['average_score'], 1) }}%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100 dark:bg-orange-900 text-orange-600 dark:text-orange-400">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Time</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                @php
                                    $hours = floor($overallStats['total_time_spent'] / 3600);
                                    $minutes = floor(($overallStats['total_time_spent'] % 3600) / 60);
                                @endphp
                                {{ $hours }}h {{ $minutes }}m
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Subject Performance -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Subject Performance</h2>

                        <div class="space-y-4">
                            @forelse($subjectStats as $subject => $stats)
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <h3 class="font-medium text-gray-900 dark:text-white">{{ $subject }}</h3>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $stats['attempts'] }} attempts</span>
                                    </div>

                                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1">
                                        <span>Average Score</span>
                                        <span>{{ number_format($stats['average_score'], 1) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-3">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                             style="width: {{ $stats['average_score'] }}%"></div>
                                    </div>

                                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                        <span>Highest Score</span>
                                        <span class="font-medium text-green-600 dark:text-green-400">{{ number_format($stats['highest_score'], 1) }}%</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <i class="fas fa-chart-bar text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                    <p class="text-gray-500 dark:text-gray-400">No quiz data available yet.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h2>

                        <div class="space-y-4">
                            @forelse($recentAttempts as $attempt)
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                    <div class="flex justify-between items-start mb-1">
                                        <h3 class="font-medium text-gray-900 dark:text-white text-sm truncate">
                                            {{ $attempt->quiz->title }}
                                        </h3>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ $attempt->submitted_at->format('M j') }}
                                </span>
                                    </div>

                                    <div class="flex justify-between items-center text-xs text-gray-600 dark:text-gray-400">
                                        <span>{{ $attempt->quiz->subject->name }}</span>
                                        <span class="font-medium {{ $attempt->total_score >= ($attempt->quiz->total_marks * 0.7) ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $attempt->total_score }}/{{ $attempt->quiz->total_marks }}
                                </span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <i class="fas fa-history text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                    <p class="text-gray-500 dark:text-gray-400">No recent activity.</p>
                                </div>
                            @endforelse
                        </div>

                        @if($recentAttempts->count() > 0)
                            <div class="mt-4 text-center">
                                <a href="{{ route('student.quizzes.completed') }}"
                                   class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                    View All Attempts
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Improvement Tips -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mt-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Improvement Tips</h2>

                        <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex items-start">
                                <i class="fas fa-lightbulb text-yellow-500 mt-1 mr-2"></i>
                                <span>Review your incorrect answers to understand mistakes</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-book text-blue-500 mt-1 mr-2"></i>
                                <span>Focus on subjects with lower average scores</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-clock text-green-500 mt-1 mr-2"></i>
                                <span>Practice time management during quizzes</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-redo text-purple-500 mt-1 mr-2"></i>
                                <span>Retake quizzes to improve your scores</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
