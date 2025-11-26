@extends('layouts.app')

@section('title', 'Quiz Attempt Details - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Quiz Attempt Details</h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-2">
                            Review attempt for: {{ $quiz->title }}
                        </p>
                        @if(isset($isAdminView) && $isAdminView)
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Student: {{ $student->name }} ({{ $student->email }})
                            </p>
                        @endif
                    </div>
                    <div class="flex space-x-3">
                        @if(isset($isAdminView) && $isAdminView)
                            <a href="{{ route('admin.quizzes.attempts', $quiz) }}"
                               class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors duration-200">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back to Attempts
                            </a>
                        @else
                            <a href="{{ route('student.quizzes.completed') }}"
                               class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors duration-200">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back to Completed Quizzes
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Attempt Information -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Basic Info -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Attempt Information</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Quiz:</span>
                            <span class="text-gray-900 dark:text-white font-medium">{{ $quiz->title }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Subject:</span>
                            <span class="text-gray-900 dark:text-white">{{ $quiz->subject->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Class:</span>
                            <span class="text-gray-900 dark:text-white">{{ $quiz->class->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Started At:</span>
                            <span class="text-gray-900 dark:text-white">{{ $attempt->started_at->format('M j, Y g:i A') }}</span>
                        </div>
                        @if($attempt->submitted_at)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Submitted At:</span>
                                <span class="text-gray-900 dark:text-white">{{ $attempt->submitted_at->format('M j, Y g:i A') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Performance -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Status:</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $attempt->status === 'submitted' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                   ($attempt->status === 'graded' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                   ($attempt->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                   'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200')) }}">
                                {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Score:</span>
                            <span class="text-gray-900 dark:text-white font-medium">
                                {{ $attempt->total_score }}/{{ $quiz->total_marks }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Percentage:</span>
                            <span class="text-gray-900 dark:text-white">
                                {{ $quiz->total_marks > 0 ? number_format(($attempt->total_score / $quiz->total_marks) * 100, 1) : 0 }}%
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Time Spent:</span>
                            <span class="text-gray-900 dark:text-white">
                               @if($attempt->submitted_at && $attempt->started_at)
                                    @php
                                        // Use the raw database timestamps (they're already comparable)
                                        $startedAt = \Carbon\Carbon::parse($attempt->started_at);
                                        $submittedAt = \Carbon\Carbon::parse($attempt->submitted_at);

                                        // Calculate absolute difference to avoid any negative values
                                        $timeSpent = abs($submittedAt->diffInSeconds($startedAt));

                                        $hours = floor($timeSpent / 3600);
                                        $minutes = floor(($timeSpent % 3600) / 60);
                                        $seconds = $timeSpent % 60;

                                        $timeDisplay = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                                    @endphp
                                    {{ $timeDisplay }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                @if($attempt->status === 'submitted' || $attempt->status === 'graded')
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Overview</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $correctAnswers }}</div>
                                <div class="text-sm text-green-600 dark:text-green-400">Correct</div>
                            </div>
                            <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $totalQuestions - $correctAnswers }}</div>
                                <div class="text-sm text-red-600 dark:text-red-400">Incorrect</div>
                            </div>
                            <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalQuestions }}</div>
                                <div class="text-sm text-blue-600 dark:text-blue-400">Total</div>
                            </div>
                            <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                    {{ $totalQuestions > 0 ? number_format(($correctAnswers / $totalQuestions) * 100, 1) : 0 }}%
                                </div>
                                <div class="text-sm text-purple-600 dark:text-purple-400">Accuracy</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Detailed Results -->
            @if($attempt->status === 'submitted' || $attempt->status === 'graded')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Detailed Results</h2>

                    <div class="space-y-6">
                        @foreach($questionResults as $index => $result)
                            @php
                                $question = $result['question'];
                                $studentAnswer = $result['studentAnswer'];
                                $isCorrect = $result['isCorrect'];
                                $points = $result['points'];
                            @endphp
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 {{ $isCorrect ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700' : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-700' }}">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center space-x-3">
                                        <span class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium {{ $isCorrect ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200' }}">
                                            {{ $index + 1 }}
                                        </span>
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 capitalize">
                                            {{ str_replace('_', ' ', $question->type) }}
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $points }} points
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($isCorrect)
                                            <span class="text-sm font-medium text-green-600 dark:text-green-400">
                                                +{{ $points }} points
                                            </span>
                                        @else
                                            <span class="text-sm font-medium text-red-600 dark:text-red-400">
                                                0 points
                                            </span>
                                        @endif
                                        <span class="text-sm font-medium {{ $isCorrect ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $isCorrect ? 'Correct' : 'Incorrect' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <p class="text-gray-900 dark:text-white font-medium">{{ $question->question_text }}</p>
                                </div>

                                <!-- Student Answer -->
                                <div class="mb-3">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student's Answer:</p>
                                    <div class="bg-white dark:bg-gray-700 rounded-lg p-3 border {{ $isCorrect ? 'border-green-200 dark:border-green-600' : 'border-red-200 dark:border-red-600' }}">
                                        @if($question->type === 'mcq')
                                            @php
                                                $selectedOption = $question->options->firstWhere('id', $studentAnswer);
                                            @endphp
                                            <p class="text-gray-900 dark:text-white">
                                                {{ $selectedOption ? $selectedOption->option_text : 'No answer provided' }}
                                            </p>
                                        @elseif($question->type === 'true_false')
                                            <p class="text-gray-900 dark:text-white">
                                                {{ $studentAnswer ? ucfirst($studentAnswer) : 'No answer provided' }}
                                            </p>
                                        @else
                                            <p class="text-gray-900 dark:text-white">
                                                {{ $studentAnswer ?? 'No answer provided' }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Correct Answer -->
                                @if(!$isCorrect && $question->type !== 'essay')
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correct Answer:</p>
                                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 border border-green-200 dark:border-green-600">
                                            @if($question->type === 'mcq')
                                                @php
                                                    $correctOption = $question->options->where('is_correct', true)->first();
                                                @endphp
                                                <p class="text-green-800 dark:text-green-200 font-medium">
                                                    {{ $correctOption->option_text ?? 'N/A' }}
                                                </p>
                                            @elseif($question->type === 'true_false')
                                                <p class="text-green-800 dark:text-green-200 font-medium">
                                                    {{ ucfirst($question->correct_answer) }}
                                                </p>
                                            @elseif($question->type === 'short_answer')
                                                <p class="text-green-800 dark:text-green-200 font-medium">
                                                    {{ $question->correct_answer ?? 'N/A' }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Admin Actions -->
                                @if(isset($isAdminView) && $isAdminView && $question->type === 'essay')
                                    <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-600">
                                        <p class="text-sm font-medium text-yellow-700 dark:text-yellow-300 mb-2">
                                            <i class="fas fa-edit mr-2"></i>Manual Grading Required
                                        </p>
                                        <form action="{{ route('admin.quizzes.attempts.grade', $attempt) }}" method="POST" class="flex items-end space-x-3">
                                            @csrf
                                            <input type="hidden" name="question_id" value="{{ $question->id }}">
                                            <div class="flex-1">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    Award Points (Max: {{ $points }})
                                                </label>
                                                <input type="number" name="score"
                                                       min="0" max="{{ $points }}" step="0.5"
                                                       value="{{ $points }}"
                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                            </div>
                                            <button type="submit"
                                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                                                <i class="fas fa-save mr-2"></i>Save Grade
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 text-center">
                    <i class="fas fa-hourglass-half text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Attempt in Progress</h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        This attempt is still in progress. Results will be available after submission.
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection
