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

                <!-- Status Alert -->
                <div class="mt-4 bg-{{ $resultsStatus['color'] }}-50 dark:bg-{{ $resultsStatus['color'] }}-900/20 border border-{{ $resultsStatus['color'] }}-200 dark:border-{{ $resultsStatus['color'] }}-700 rounded-lg p-4">
                    <div class="flex items-center justify-center">
                        <i class="fas fa-{{ $resultsStatus['icon'] }} text-{{ $resultsStatus['color'] }}-600 dark:text-{{ $resultsStatus['color'] }}-400 mr-3"></i>
                        <span class="text-{{ $resultsStatus['color'] }}-700 dark:text-{{ $resultsStatus['color'] }}-300">
                            {{ $resultsStatus['message'] }}
                        </span>
                    </div>
                </div>

                <!-- Score Card (only show if allowed) -->
                @if($canSeeScoreOnly || $canSeeDetailedResults)
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ $attempt->total_score ?? 'Pending' }}
                            </div>
                            <div class="text-blue-800 dark:text-blue-200">Your Score</div>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900 rounded-lg p-4">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ $exam->total_marks }}
                            </div>
                            <div class="text-green-800 dark:text-green-200">Total Marks</div>
                        </div>
                        <div class="bg-{{ $attempt->total_score >= $exam->passing_marks ? 'green' : 'red' }}-50 dark:bg-{{ $attempt->total_score >= $exam->passing_marks ? 'green' : 'red' }}-900 rounded-lg p-4">
                            <div class="text-2xl font-bold text-{{ $attempt->total_score >= $exam->passing_marks ? 'green' : 'red' }}-600 dark:text-{{ $attempt->total_score >= $exam->passing_marks ? 'green' : 'red' }}-400">
                                {{ $attempt->total_score >= $exam->passing_marks ? 'Passed' : 'Failed' }}
                            </div>
                            <div class="text-{{ $attempt->total_score >= $exam->passing_marks ? 'green' : 'red' }}-800 dark:text-{{ $attempt->total_score >= $exam->passing_marks ? 'green' : 'red' }}-200">
                                Passing: {{ $exam->passing_marks }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Attempt Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Attempt Information</h2>
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
                        <p class="text-gray-900 dark:text-white">
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
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $attempt->status === 'graded' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' :
                               ($attempt->status === 'submitted' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' :
                               'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100') }}">
                            {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Question Review - ONLY show if allowed -->
            @if($canSeeDetailedResults)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Question Review</h2>

                    <!-- Warning for high-stakes exams -->
                    @if(in_array($exam->type, ['midterm', 'end_of_term', 'final', 'mock']))
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300">Official Results</h4>
                                    <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">
                                        These are your official exam results. Contact your instructor if you have any questions.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-6">
                        @foreach($exam->questions as $index => $question)
                            @php
                                $userAnswer = $answers[$question->id] ?? null;
                                $manualGrade = $manualGrades[$question->id] ?? null;
                                $maxPoints = $question->pivot->points ?? $question->points ?? 0;

                                // CORRECTED MCQ LOGIC - same as teacher view
                                $pointsAwarded = 0;
                                $isCorrect = false;
                                $gradingMethod = 'auto';

                                if ($manualGrade !== null) {
                                    $pointsAwarded = (float)$manualGrade;
                                    $gradingMethod = 'manual';
                                    $isCorrect = $pointsAwarded > 0;
                                } else {
                                    // Auto-grade only objective questions
                                    if (in_array($question->type, ['mcq', 'true_false']) && !empty($userAnswer)) {
                                        if ($question->type === 'mcq') {
                                            // ✅ CORRECT: Compare option IDs, not order
                                            $correctOption = $question->options->where('is_correct', true)->first();
                                            $correctAnswerId = $correctOption ? (int)$correctOption->id : (int)$question->correct_answer;
                                            $studentAnswerId = (int)$userAnswer;
                                            $isCorrect = $studentAnswerId === $correctAnswerId;
                                            $pointsAwarded = $isCorrect ? $maxPoints : 0;
                                        } elseif ($question->type === 'true_false') {
                                            $studentAnswerStr = strtolower(trim($userAnswer ?? ''));
                                            $correctAnswerStr = strtolower(trim($question->correct_answer ?? ''));
                                            $isCorrect = $studentAnswerStr === $correctAnswerStr;
                                            $pointsAwarded = $isCorrect ? $maxPoints : 0;
                                        }
                                    } else {
                                        // For subjective questions without manual grading OR objective questions without answers
                                        $pointsAwarded = 0;
                                        $isCorrect = false;
                                    }
                                }

                                $needsGrading = in_array($question->type, ['short_answer', 'essay', 'fill_blank']) &&
                                               $manualGrade === null &&
                                               !empty($userAnswer);
                            @endphp

                            <div class="border rounded-lg p-4
                                {{ $gradingMethod === 'manual' ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700' :
                                   ($needsGrading ? 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-700' :
                                   ($isCorrect ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700' :
                                   (!$isCorrect && $userAnswer ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-700' :
                                   'bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600'))) }}">

                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        Question {{ $index + 1 }}
                                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                            ({{ $pointsAwarded }}/{{ $maxPoints }} points)
                                        </span>
                                    </h3>
                                    <div class="flex items-center space-x-2">
                                        @if($gradingMethod === 'manual')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                <i class="fas fa-user-edit mr-1"></i>Manually Graded
                                            </span>
                                        @elseif($needsGrading)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                                <i class="fas fa-clock mr-1"></i>Awaiting Grading
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $isCorrect ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                                {{ $isCorrect ? 'Correct' : 'Incorrect' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Question Text -->
                                <div class="mb-4">
                                    <p class="text-gray-700 dark:text-gray-300 text-lg">{{ $question->question_text }}</p>
                                </div>

                                <!-- User Answer -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Answer:</label>
                                    <div class="bg-white dark:bg-gray-600 rounded-lg p-4 border border-gray-300 dark:border-gray-500">
                                        @if($question->type === 'mcq')
                                            @php
                                                $studentAnswerId = (int)$userAnswer;
                                                $selectedOption = $question->options->where('id', $studentAnswerId)->first();
                                            @endphp
                                            @if($selectedOption)
                                                <div class="flex items-center p-2 rounded {{ $isCorrect ? 'bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700' : 'bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700' }}">
                                                    <div class="flex items-center justify-center w-6 h-6 rounded-full border mr-3 {{ $isCorrect ? 'bg-green-600 border-green-600' : 'bg-red-600 border-red-600' }}">
                                                        <i class="fas fa-check text-white text-xs"></i>
                                                    </div>
                                                    <span class="{{ $isCorrect ? 'text-green-700 dark:text-green-300 font-medium' : 'text-red-700 dark:text-red-300 font-medium' }}">
                                                        {{ $selectedOption->option_text }}
                                                    </span>
                                                </div>
                                            @else
                                                <p class="text-red-500 dark:text-red-400 italic">Not answered</p>
                                            @endif
                                        @else
                                            <div class="whitespace-pre-wrap text-gray-700 dark:text-gray-300">
                                                {{ $userAnswer ?: 'Not answered' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Correct Answer (only show if incorrect and not awaiting grading) -->
                                @if(!$isCorrect && !$needsGrading && in_array($question->type, ['mcq', 'true_false']))
                                    <div class="mt-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Correct Answer:</label>
                                        <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-3 border border-green-200 dark:border-green-700">
                                            @if($question->type === 'mcq')
                                                @php
                                                    $correctOption = $question->options->where('is_correct', true)->first();
                                                    $correctAnswer = $correctOption ? $correctOption->option_text : 'No correct answer set';
                                                @endphp
                                                <div class="flex items-center">
                                                    <div class="flex items-center justify-center w-6 h-6 rounded-full bg-green-600 border border-green-600 mr-3">
                                                        <i class="fas fa-check text-white text-xs"></i>
                                                    </div>
                                                    <span class="text-green-700 dark:text-green-300 font-medium">{{ $correctAnswer }}</span>
                                                </div>
                                            @elseif($question->type === 'true_false')
                                                <span class="text-green-700 dark:text-green-300 font-medium">{{ ucfirst($question->correct_answer) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Grading Status for subjective questions -->
                                @if($needsGrading)
                                    <div class="mt-3 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg p-3 border border-yellow-200 dark:border-yellow-700">
                                        <div class="flex items-center">
                                            <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 mr-2"></i>
                                            <span class="text-yellow-700 dark:text-yellow-300 text-sm">
                                                This question requires manual grading. Your score will be updated soon.
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif($canSeeScoreOnly && !$canSeeDetailedResults)
                <!-- Show only basic score without question details -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-6 text-center">
                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-3xl mb-3"></i>
                    <h3 class="text-lg font-medium text-yellow-800 dark:text-yellow-300 mb-2">
                        Detailed Results Pending Review
                    </h3>
                    <p class="text-yellow-700 dark:text-yellow-400">
                        Your score is available, but detailed question-by-question results are being reviewed
                        by your instructor and will be released soon.
                    </p>
                </div>
            @else
                <!-- No results available at all -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-6 text-center">
                    <i class="fas fa-hourglass-half text-blue-600 dark:text-blue-400 text-3xl mb-3"></i>
                    <h3 class="text-lg font-medium text-blue-800 dark:text-blue-300 mb-2">
                        Results Being Processed
                    </h3>
                    <p class="text-blue-700 dark:text-blue-400">
                        Your exam is currently being graded. You will be notified when results are available.
                    </p>
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
