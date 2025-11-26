@extends('layouts.app')

@section('title', 'Upcoming Quizzes - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Upcoming Quizzes</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">Quizzes that will be available soon</p>
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
                           class="border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 whitespace-nowrap py-4 px-1 text-sm font-medium">
                            Upcoming Quizzes
                        </a>
                        <a href="{{ route('student.quizzes.completed') }}"
                           class="border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 whitespace-nowrap py-4 px-1 text-sm font-medium">
                            Completed Quizzes
                        </a>
                        <a href="{{ route('student.quizzes.progress') }}"
                           class="border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 whitespace-nowrap py-4 px-1 text-sm font-medium">
                            My Progress
                        </a>
                    </nav>
                </div>
            </div>

            @if($quizzes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($quizzes as $quiz)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-blue-200 dark:border-blue-700">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                            {{ $quiz->title }}
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $quiz->subject->name }} • {{ $quiz->class->name }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 capitalize">
                                {{ str_replace('_', ' ', $quiz->type) }}
                            </span>
                                </div>

                                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-clock mr-2"></i>
                                        <span>{{ $quiz->duration }} minutes</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-star mr-2"></i>
                                        <span>{{ $quiz->total_marks }} points</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-user mr-2"></i>
                                        <span>{{ $quiz->teacher->name }}</span>
                                    </div>
                                </div>

                                <!-- Countdown -->
                                <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-3 mb-4">
                                    <div class="text-center">
                                        <p class="text-sm text-blue-700 dark:text-blue-300 font-medium mb-1">
                                            Starts In
                                        </p>
                                        <div class="flex justify-center space-x-2 text-blue-800 dark:text-blue-200">
                                            <div class="text-center">
                                                <div class="text-lg font-bold">{{ $quiz->start_time->diffInDays(now()) }}</div>
                                                <div class="text-xs">Days</div>
                                            </div>
                                            <div class="text-center">
                                                <div class="text-lg font-bold">{{ $quiz->start_time->diffInHours(now()) % 24 }}</div>
                                                <div class="text-xs">Hours</div>
                                            </div>
                                            <div class="text-center">
                                                <div class="text-lg font-bold">{{ $quiz->start_time->diffInMinutes(now()) % 60 }}</div>
                                                <div class="text-xs">Minutes</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                                    Available on {{ $quiz->start_time->format('M j, Y g:i A') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($quizzes->hasPages())
                    <div class="mt-6">
                        {{ $quizzes->links() }}
                    </div>
                @endif
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-12 text-center">
                    <i class="fas fa-calendar-plus text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Upcoming Quizzes</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">There are no upcoming quizzes scheduled for you.</p>
                    <a href="{{ route('student.quizzes.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                        Check Available Quizzes
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
