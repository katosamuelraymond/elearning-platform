@extends('layouts.app')

@section('title', 'Exam Results - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Results Header -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6 text-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Exam Results</h1>
                <p class="text-gray-600 dark:text-gray-300">{{ $exam->title }}</p>
                <p class="text-gray-500 dark:text-gray-400">{{ $exam->subject->name }} - {{ $exam->class->name }}</p>

                <!-- Score Card -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $attempt->score ?? 'Pending' }}</div>
                        <div class="text-blue-800 dark:text-blue-200">Your Score</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900 rounded-lg p-4">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $exam->total_marks }}</div>
                        <div class="text-green-800 dark:text-green-200">Total Marks</div>
                    </div>
                    <div class="bg-{{ $attempt->score >= $exam->passing_marks ? 'green' : 'red' }}-50 dark:bg-{{ $attempt->score >= $exam->passing_marks ? 'green' : 'red' }}-900 rounded-lg p-4">
                        <div class="text-2xl font-bold text-{{ $attempt->score >= $exam->passing_marks ? 'green' : 'red' }}-600 dark:text-{{ $attempt->score >= $exam->passing_marks ? 'green' : 'red' }}-400">
                            {{ $attempt->score >= $exam->passing_marks ? 'Passed' : 'Failed' }}
                        </div>
                        <div class="text-{{ $attempt->score >= $exam->passing_marks ? 'green' : 'red' }}-800 dark:text-{{ $attempt->score >= $exam->passing_marks ? 'green' : 'red' }}-200">
                            Passing: {{ $exam->passing_marks }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attempt Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Attempt Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Started At</label>
                        <p class="text-gray-900 dark:text-white">{{ $attempt->started_at->format('M j, Y g:i A') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Submitted At</label>
                        <p class="text-gray-900 dark:text-white">{{ $attempt->submitted_at->format('M j, Y g:i A') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time Spent</label>
                        <p class="text-gray-900 dark:text-white">{{ gmdate('H:i:s', $attempt->time_spent) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $attempt->status === 'graded' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($attempt->status) }}
                    </span>
                    </div>
                </div>
            </div>

            <!-- Question Review -->
            @if($exam->show_results || $attempt->status === 'graded')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Question Review</h2>
                    <div class="space-y-6">
                        @foreach($exam->questions as $index => $question)
                            @php
                                $userAnswer = $attempt->answers[$question->id] ?? null;
                                $isCorrect = false;

                                if ($question->type === 'mcq') {
                                    $correctOption = $question->options->where('is_correct', true)->first();
                                    $isCorrect = $userAnswer == $correctOption->order;
                                } elseif ($question->type === 'true_false') {
                                    $isCorrect = $userAnswer === $question->correct_answer;
                                }
                            @endphp

                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 {{ $isCorrect ? 'bg-green-50 dark:bg-green-900' : 'bg-red-50 dark:bg-red-900' }}">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        Question {{ $index + 1 }}
                                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                    ({{ $question->pivot->points }} points)
                                </span>
                                    </h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $isCorrect ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $isCorrect ? 'Correct' : 'Incorrect' }}
                            </span>
                                </div>

                                <div class="mb-4">
                                    <p class="text-gray-700 dark:text-gray-300">{{ $question->question_text }}</p>
                                </div>

                                <!-- User Answer -->
                                <div class="mb-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Your Answer:</label>
                                    <p class="text-gray-900 dark:text-white">
                                        @if($question->type === 'mcq')
                                            {{ $question->options[$userAnswer]->option_text ?? 'Not answered' }}
                                        @else
                                            {{ $userAnswer ?? 'Not answered' }}
                                        @endif
                                    </p>
                                </div>

                                <!-- Correct Answer -->
                                @if(!$isCorrect)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correct Answer:</label>
                                        <p class="text-gray-900 dark:text-white">
                                            @if($question->type === 'mcq')
                                                {{ $question->options->where('is_correct', true)->first()->option_text }}
                                            @else
                                                {{ $question->correct_answer }}
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 text-center">
                    <p class="text-yellow-800 dark:text-yellow-200">Results are not available yet. Please check back later.</p>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex justify-center space-x-4 mt-6">
                <a href="{{ route('student.exams.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                    Back to Exams
                </a>
                <a href="{{ route('student.exams.history') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                    View All Attempts
                </a>
            </div>
        </div>
    </div>
@endsection
