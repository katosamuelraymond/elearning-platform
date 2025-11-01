@extends('layouts.app')

@section('title', 'Attempt Details - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Attempt Details</h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-2">{{ $exam->title }}</p>
                        <p class="text-gray-500 dark:text-gray-400">{{ $exam->subject->name }} - {{ $exam->class->name }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.exams.attempts.index', $exam) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                            Back to Attempts
                        </a>
                        <a href="{{ route('admin.exams.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                            All Exams
                        </a>
                    </div>
                </div>
            </div>

            <!-- Student Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Student Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-16 w-16">
                            <div class="h-16 w-16 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold text-xl">
                                {{ substr($attempt->student->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $attempt->student->name }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $attempt->student->email }}
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Student ID</label>
                        <p class="text-gray-900 dark:text-white">{{ $attempt->student->student_id ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Class</label>
                        <p class="text-gray-900 dark:text-white">{{ $exam->class->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Attempt Summary -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $attempt->score ?? 'Pending' }}</div>
                        <div class="text-blue-800 dark:text-blue-200">Score</div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $exam->total_marks }}</div>
                        <div class="text-green-800 dark:text-green-200">Total Marks</div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ $attempt->score ? round(($attempt->score / $exam->total_marks) * 100, 1) : 0 }}%
                        </div>
                        <div class="text-purple-800 dark:text-purple-200">Percentage</div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-center">
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
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Attempt Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $attempt->status === 'graded' ? 'bg-green-100 text-green-800' :
                           ($attempt->status === 'submitted' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ ucfirst($attempt->status) }}
                    </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Started At</label>
                        <p class="text-gray-900 dark:text-white">{{ $attempt->started_at->format('M j, Y g:i A') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Submitted At</label>
                        <p class="text-gray-900 dark:text-white">{{ $attempt->submitted_at ? $attempt->submitted_at->format('M j, Y g:i A') : 'Not submitted' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time Spent</label>
                        <p class="text-gray-900 dark:text-white">{{ $attempt->time_spent ? gmdate('H:i:s', $attempt->time_spent) : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Answers Review -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Question Review</h2>
                <div class="space-y-6">
                    @foreach($exam->questions as $index => $question)
                        @php
                            $userAnswer = $attempt->answers[$question->id] ?? null;
                            $isCorrect = false;
                            $pointsAwarded = 0;

                            if ($question->type === 'mcq') {
                                $correctOption = $question->options->where('is_correct', true)->first();
                                $isCorrect = $userAnswer == $correctOption->order;
                                $pointsAwarded = $isCorrect ? $question->pivot->points : 0;
                            } elseif ($question->type === 'true_false') {
                                $isCorrect = $userAnswer === $question->correct_answer;
                                $pointsAwarded = $isCorrect ? $question->pivot->points : 0;
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
                                <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $isCorrect ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $isCorrect ? 'Correct' : 'Incorrect' }}
                                </span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $pointsAwarded }}/{{ $question->pivot->points }}
                                </span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <p class="text-gray-700 dark:text-gray-300 text-lg">{{ $question->question_text }}</p>
                            </div>

                            <!-- User Answer -->
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student's Answer:</label>
                                <div class="bg-white dark:bg-gray-700 rounded-lg p-3 border border-gray-300 dark:border-gray-600">
                                    @if($question->type === 'mcq')
                                        <p class="text-gray-900 dark:text-white">
                                            {{ $question->options[$userAnswer]->option_text ?? 'Not answered' }}
                                        </p>
                                    @elseif($question->type === 'true_false')
                                        <p class="text-gray-900 dark:text-white">{{ ucfirst($userAnswer ?? 'Not answered') }}</p>
                                    @else
                                        <p class="text-gray-900 dark:text-white">{{ $userAnswer ?? 'Not answered' }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Correct Answer -->
                            @if(!$isCorrect)
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Correct Answer:</label>
                                    <div class="bg-green-50 dark:bg-green-900 rounded-lg p-3 border border-green-200 dark:border-green-700">
                                        @if($question->type === 'mcq')
                                            <p class="text-green-900 dark:text-green-100">
                                                {{ $question->options->where('is_correct', true)->first()->option_text }}
                                            </p>
                                        @else
                                            <p class="text-green-900 dark:text-green-100">{{ $question->correct_answer }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Manual Grading for subjective questions -->
                            @if(in_array($question->type, ['short_answer', 'essay', 'fill_blank']))
                                <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900 rounded-lg border border-yellow-200 dark:border-yellow-700">
                                    <label class="block text-sm font-medium text-yellow-800 dark:text-yellow-200 mb-2">
                                        Manual Grading Required
                                    </label>
                                    <div class="flex items-center space-x-3">
                                        <input type="number"
                                               name="scores[{{ $question->id }}]"
                                               min="0"
                                               max="{{ $question->pivot->points }}"
                                               value="{{ $pointsAwarded }}"
                                               class="w-20 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                        <span class="text-sm text-yellow-700 dark:text-yellow-300">
                                    / {{ $question->pivot->points }} points
                                </span>
                                        <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                            Save Score
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
