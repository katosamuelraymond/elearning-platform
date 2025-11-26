@extends('layouts.app')

@section('title', 'Available Quizzes - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Available Quizzes</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">Take quizzes assigned to your class</p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400">
                            <i class="fas fa-file-alt text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Available Quizzes</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['available'] }}</p>
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
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['completed'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400">
                            <i class="fas fa-chart-line text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Attempts</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['attempted'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8">
                        <a href="{{ route('student.quizzes.index') }}"
                           class="border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 whitespace-nowrap py-4 px-1 text-sm font-medium">
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
                           class="border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 whitespace-nowrap py-4 px-1 text-sm font-medium">
                            My Progress
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                <form method="GET" action="{{ route('student.quizzes.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Search quizzes...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                            <select name="subject_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">All Subjects</option>
                                <!-- Add subjects dynamically -->
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                            <select name="type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">All Types</option>
                                <option value="practice" {{ ($filters['type'] ?? '') == 'practice' ? 'selected' : '' }}>Practice</option>
                                <option value="chapter_test" {{ ($filters['type'] ?? '') == 'chapter_test' ? 'selected' : '' }}>Chapter Test</option>
                                <option value="quick_check" {{ ($filters['type'] ?? '') == 'quick_check' ? 'selected' : '' }}>Quick Check</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Quizzes Grid -->
            @if($quizzes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($quizzes as $quiz)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
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

                                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    <span>Ends: {{ $quiz->end_time->format('M j, g:i A') }}</span>
                                    <span>{{ $quiz->attempts_count }} attempt(s)</span>
                                </div>

                                <div class="flex space-x-3">
                                    <a href="{{ route('student.quizzes.show', $quiz) }}"
                                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg font-medium transition-colors duration-200">
                                        {{ $quiz->attempts_count > 0 ? 'Retake Quiz' : 'Start Quiz' }}
                                    </a>
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
                    <i class="fas fa-file-alt text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No quizzes available</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">There are no quizzes available for you at the moment.</p>
                    <a href="{{ route('student.quizzes.upcoming') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                        Check Upcoming Quizzes
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
