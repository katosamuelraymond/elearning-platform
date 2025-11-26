@extends('layouts.app')

@section('title', 'Quiz Results - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full mb-4">
                    <i class="fas fa-trophy text-green-600 dark:text-green-400 text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Quiz Completed!</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">{{ $quiz->title }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $quiz->subject->name }} •
                    {{ $quiz->class->name }} •
                    {{ $quiz->teacher->name }}
                </p>
            </div>

            <!-- Score Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-center">
                    <div>
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">
                            {{ $attempt->total_score }}/{{ $quiz->total_marks }}
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Score</p>
                    </div>

                    <div>
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">
                            {{ number_format(($attempt->total_score / $quiz->total_marks) * 100, 1) }}%
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Percentage</p>
                    </div>

                    <div>
                        <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-2">

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

                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Time Spent</p>
                    </div>

                    <div>
                        <div class="text-3xl font-bold text-orange-600 dark:text-orange-400 mb-2">
                            {{ $correctAnswers }}/{{ $totalQuestions }}
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Correct Answers</p>
                    </div>
                </div>
            </div>

            <!-- Performance Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Performance Summary</h2>

                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1">
                            <span>Correct Answers</span>
                            <span>{{ $correctAnswers }}/{{ $totalQuestions }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full transition-all duration-300"
                                 style="width: {{ ($correctAnswers / $totalQuestions) * 100 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1">
                            <span>Incorrect Answers</span>
                            <span>{{ $totalQuestions - $correctAnswers }}/{{ $totalQuestions }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-red-600 h-2 rounded-full transition-all duration-300"
                                 style="width: {{ (($totalQuestions - $correctAnswers) / $totalQuestions) * 100 }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Performance Message -->
                @php
                    $percentage = ($attempt->total_score / $quiz->total_marks) * 100;
                    if ($percentage >= 80) {
                        $performance = 'Excellent!';
                        $color = 'text-green-600 dark:text-green-400';
                        $icon = 'fa-star';
                    } elseif ($percentage >= 60) {
                        $performance = 'Good job!';
                        $color = 'text-blue-600 dark:text-blue-400';
                        $icon = 'fa-thumbs-up';
                    } elseif ($percentage >= 40) {
                        $performance = 'Fair';
                        $color = 'text-yellow-600 dark:text-yellow-400';
                        $icon = 'fa-meh';
                    } else {
                        $performance = 'Needs improvement';
                        $color = 'text-red-600 dark:text-red-400';
                        $icon = 'fa-redo';
                    }
                @endphp

                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                    <i class="fas {{ $icon }} {{ $color }} text-xl mb-2"></i>
                    <p class="text-lg font-semibold {{ $color }}">{{ $performance }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        You scored {{ $attempt->total_score }} out of {{ $quiz->total_marks }} points.
                    </p>
                </div>
            </div>

            <!-- Question Review -->
            @if($quiz->show_answers)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Question Review</h2>

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
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Your Answer:</p>
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

                                <!-- Explanation -->
                                @if($question->explanation)
                                    <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-600">
                                        <p class="text-sm font-medium text-blue-700 dark:text-blue-300 mb-1">
                                            <i class="fas fa-lightbulb mr-2"></i>Explanation:
                                        </p>
                                        <p class="text-blue-600 dark:text-blue-300">
                                            {{ $question->explanation }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 text-center">
                    <i class="fas fa-eye-slash text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Answers Hidden</h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        The quiz settings do not allow viewing answers after submission.
                    </p>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4 mt-8">
                <a href="{{ route('student.quizzes.index') }}"
                   class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 text-center">
                    <i class="fas fa-home mr-2"></i>
                    Back to Quizzes
                </a>

                <a href="{{ route('student.quizzes.progress') }}"
                   class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 text-center">
                    <i class="fas fa-chart-line mr-2"></i>
                    View Progress
                </a>

                @if($quiz->show_answers)
                    <button onclick="window.print()"
                            class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 text-center">
                        <i class="fas fa-print mr-2"></i>
                        Print Results
                    </button>
                @endif
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
            .bg-gray-50, .bg-white, .bg-green-50, .bg-red-50, .bg-blue-50 {
                background: white !important;
            }
            .text-gray-900, .text-gray-700, .text-green-800, .text-red-800, .text-blue-700 {
                color: black !important;
            }
            .border {
                border-color: #ccc !important;
            }
        }
    </style>
@endsection
