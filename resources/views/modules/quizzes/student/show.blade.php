@extends('layouts.app')

@section('title', $quiz->title . ' - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $quiz->title }}</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">{{ $quiz->subject->name }} • {{ $quiz->class->name }}</p>
            </div>

            <!-- Quiz Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="text-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 inline-flex items-center justify-center mb-3">
                            <i class="fas fa-clock text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Duration</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ $quiz->duration }} minutes</p>
                    </div>

                    <div class="text-center">
                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 inline-flex items-center justify-center mb-3">
                            <i class="fas fa-star text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Total Points</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ $quiz->total_marks }} points</p>
                    </div>

                    <div class="text-center">
                        <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 inline-flex items-center justify-center mb-3">
                            <i class="fas fa-question-circle text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Questions</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ $quiz->questions_count ?? $quiz->questions->count() }}</p>
                    </div>
                </div>

                <!-- Instructions -->
                @if($quiz->instructions)
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Instructions</h3>
                        <div class="prose dark:prose-invert max-w-none">
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $quiz->instructions }}</p>
                        </div>
                    </div>
                @endif

                <!-- Important Notes -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Important Notes</h3>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-2"></i>
                            <span>You have <strong>{{ $quiz->duration }} minutes</strong> to complete this quiz</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-save text-blue-500 mt-1 mr-2"></i>
                            <span>Your answers are saved automatically as you progress</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-ban text-red-500 mt-1 mr-2"></i>
                            <span>Do not refresh the page or navigate away during the quiz</span>
                        </li>
                        @if($quiz->randomize_questions)
                            <li class="flex items-start">
                                <i class="fas fa-random text-purple-500 mt-1 mr-2"></i>
                                <span>Questions will appear in random order</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Attempt Status -->
            @if($existingAttempt)
                <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-xl p-6 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-yellow-600 dark:text-yellow-400 text-xl mr-3"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">Previous Attempt Found</h3>
                            <p class="text-yellow-700 dark:text-yellow-300 mt-1">
                                You have an existing attempt from {{ $existingAttempt->started_at->format('M j, g:i A') }}.
                                @if($existingAttempt->status === 'in_progress')
                                    You can continue where you left off.
                                @else
                                    This attempt has been submitted.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                    <a href="{{ route('student.quizzes.index') }}"
                       class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 text-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Quizzes
                    </a>

                    @if($maxAttemptsReached && !$existingAttempt)
                        <button disabled
                                class="px-6 py-3 bg-gray-400 dark:bg-gray-600 text-gray-200 dark:text-gray-400 rounded-lg font-medium cursor-not-allowed text-center">
                            <i class="fas fa-ban mr-2"></i>
                            Maximum Attempts Reached
                        </button>
                    @else
                        <form action="{{ route('student.quizzes.start', $quiz) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200 text-center w-full sm:w-auto">
                                <i class="fas fa-play mr-2"></i>
                                {{ $existingAttempt && $existingAttempt->status === 'in_progress' ? 'Continue Attempt' : 'Start Quiz' }}
                            </button>
                        </form>
                    @endif
                </div>

                @if($existingAttempt && $existingAttempt->status === 'submitted')
                    <div class="mt-4 text-center">
                        <a href="{{ route('student.quizzes.result', $existingAttempt) }}"
                           class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                            View Previous Results
                        </a>
                    </div>
                @endif
            </div>

            <!-- Time Information -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                    <div class="flex items-center">
                        <i class="fas fa-hourglass-start text-blue-500 mr-3"></i>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Starts</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $quiz->start_time->format('M j, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                    <div class="flex items-center">
                        <i class="fas fa-hourglass-end text-red-500 mr-3"></i>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Ends</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $quiz->end_time->format('M j, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
