@extends('layouts.app')

@section('title', $quiz->title . ' - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $quiz->title }}</h1>
                        <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600 dark:text-gray-400">
                            <span>Created by: {{ $quiz->teacher->name ?? 'N/A' }}</span>
                            <span>•</span>
                            <span>Class: {{ $quiz->class->name ?? 'N/A' }}</span>
                            <span>•</span>
                            <span>Subject: {{ $quiz->subject->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.quizzes.attempts', $quiz) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                            <i class="fas fa-users mr-2"></i>
                            View Attempts
                        </a>
                        <a href="{{ route('admin.quizzes.edit', $quiz) }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Quiz
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quiz Details -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Quiz Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Quiz Type</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white capitalize">{{ str_replace('_', ' ', $quiz->type) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Duration</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $quiz->duration }} minutes</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Total Marks</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $quiz->total_marks }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                                <p class="mt-1">
                                    @if($quiz->is_published)
                                        @if($quiz->isActive())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Active
                                        </span>
                                        @elseif($quiz->isUpcoming())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            Upcoming
                                        </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                            Completed
                                        </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Draft
                                    </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Start Time</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $quiz->start_time->format('M j, Y g:i A') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">End Time</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $quiz->end_time->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>

                        <!-- Settings -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center">
                                <i class="fas fa-random text-gray-400 mr-3"></i>
                                <span class="text-sm text-gray-900 dark:text-white">
                                {{ $quiz->randomize_questions ? 'Questions randomized' : 'Questions in order' }}
                            </span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-eye text-gray-400 mr-3"></i>
                                <span class="text-sm text-gray-900 dark:text-white">
                                {{ $quiz->show_answers ? 'Answers shown after submission' : 'Answers hidden after submission' }}
                            </span>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions -->
                    @if($quiz->instructions)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Instructions</h2>
                            <div class="prose dark:prose-invert max-w-none">
                                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $quiz->instructions }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Questions -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Questions ({{ $quiz->questions->count() }})</h2>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Total Points: {{ $quiz->total_marks }}</span>
                        </div>

                        <div class="space-y-6">
                            @foreach($quiz->questions as $index => $question)
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center space-x-3">
                                        <span class="flex items-center justify-center w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full text-sm font-medium">
                                            {{ $index + 1 }}
                                        </span>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 capitalize">
                                            {{ str_replace('_', ' ', $question->type) }}
                                        </span>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $question->pivot->points }} points
                                        </span>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <p class="text-gray-900 dark:text-white text-lg">{{ $question->question_text }}</p>
                                    </div>

                                    @if($question->type === 'mcq' && $question->options->count() > 0)
                                        <div class="space-y-2 ml-4">
                                            @foreach($question->options as $option)
                                                <div class="flex items-center">
                                                    <div class="w-6 h-6 flex items-center justify-center mr-3">
                                                        @if($option->is_correct)
                                                            <i class="fas fa-check text-green-500"></i>
                                                        @else
                                                            <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                                                        @endif
                                                    </div>
                                                    <span class="text-gray-700 dark:text-gray-300 {{ $option->is_correct ? 'font-medium text-green-600 dark:text-green-400' : '' }}">
                                                    {{ $option->option_text }}
                                                </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($question->type === 'true_false')
                                        <div class="ml-4">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Correct Answer:
                                            <span class="text-green-600 dark:text-green-400 capitalize">{{ $question->correct_answer }}</span>
                                        </span>
                                        </div>
                                    @elseif($question->type === 'short_answer' && $question->correct_answer)
                                        <div class="ml-4">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Expected Answer:
                                            <span class="text-green-600 dark:text-green-400">{{ $question->correct_answer }}</span>
                                        </span>
                                        </div>
                                    @endif

                                    @if($question->explanation)
                                        <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                <strong>Explanation:</strong> {{ $question->explanation }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Stats -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quiz Statistics</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Attempts</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $quiz->attempts_count }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Submitted</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $quiz->submitted_count }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">In Progress</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $quiz->attempts_count - $quiz->submitted_count }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Average Score</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                @php
                                    $avgScore = $quiz->attempts->where('status', 'graded')->avg('total_score');
                                @endphp
                                    {{ $avgScore ? number_format($avgScore, 1) : 'N/A' }}
                            </span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <form action="{{ route('admin.quizzes.toggle-publish', $quiz) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-left rounded-lg
                                           {{ $quiz->is_published ? 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-green-50 text-green-700 hover:bg-green-100 dark:bg-green-900 dark:text-green-200' }}">
                                    <span>{{ $quiz->is_published ? 'Unpublish Quiz' : 'Publish Quiz' }}</span>
                                    <i class="fas fa-{{ $quiz->is_published ? 'eye-slash' : 'eye' }}"></i>
                                </button>
                            </form>

                            <a href="{{ route('admin.quizzes.attempts', $quiz) }}"
                               class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900 dark:text-blue-200 rounded-lg">
                                <span>View All Attempts</span>
                                <i class="fas fa-users"></i>
                            </a>

                            <a href="{{ route('admin.quizzes.edit', $quiz) }}"
                               class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900 dark:text-indigo-200 rounded-lg">
                                <span>Edit Quiz</span>
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Are you sure you want to delete this quiz? This action cannot be undone.')"
                                        class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 dark:bg-red-900 dark:text-red-200 rounded-lg">
                                    <span>Delete Quiz</span>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Time Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Time Information</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Created</span>
                                <span class="text-gray-900 dark:text-white">{{ $quiz->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Last Updated</span>
                                <span class="text-gray-900 dark:text-white">{{ $quiz->updated_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Time Remaining</span>
                                <span class="text-gray-900 dark:text-white">
                                @if($quiz->end_time->isFuture())
                                        {{ $quiz->end_time->diffForHumans() }}
                                    @else
                                        Ended
                                    @endif
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
