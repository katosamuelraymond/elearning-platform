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
                        @if(auth()->user()->isTeacher())
                            <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">Teacher View</p>
                        @else
                            <p class="text-sm text-purple-600 dark:text-purple-400 mt-1">Admin View</p>
                        @endif
                    </div>
                    <div class="flex space-x-3">
                        @if(auth()->user()->isTeacher())
                            <a href="{{ route('teacher.exams.attempts', $exam) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                Back to Attempts
                            </a>
                            <a href="{{ route('teacher.exams.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                All Exams
                            </a>
                        @else
                            <a href="{{ route('admin.exams.attempts', $exam) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                Back to Attempts
                            </a>
                            <a href="{{ route('admin.exams.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                All Exams
                            </a>
                        @endif
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
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $attempt->total_score ?? 'Pending' }}</div>
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
                            @php
                                $percentage = $attempt->total_score && $exam->total_marks > 0
                                    ? round(($attempt->total_score / $exam->total_marks) * 100, 1)
                                    : 0;
                            @endphp
                            {{ $percentage }}%
                        </div>
                        <div class="text-purple-800 dark:text-purple-200">Percentage</div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-center">
                        @php
                            $passed = $attempt->total_score && $attempt->total_score >= $exam->passing_marks;
                            $statusColor = $passed ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
                            $statusText = $passed ? 'Passed' : 'Failed';
                        @endphp
                        <div class="text-2xl font-bold {{ $statusColor }}">
                            {{ $statusText }}
                        </div>
                        <div class="{{ $passed ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200' }}">
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
                        {{ $attempt->status === 'graded' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' :
                           ($attempt->status === 'submitted' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' :
                           ($attempt->status === 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100')) }}">
                        {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
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
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </div>


            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Question Review</h2>

                <!-- Bulk Grading Form for Teachers -->
                @if(auth()->user()->isTeacher() && $attempt->status === 'submitted')
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-blue-800 dark:text-blue-300">Bulk Grading</h3>
                                <p class="text-sm text-blue-600 dark:text-blue-400">Update scores for multiple questions at once</p>
                            </div>
                            <button type="button" id="toggle-bulk-grading" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                <i class="fas fa-edit mr-2"></i>Enable Bulk Grading
                            </button>
                        </div>

                        <form id="bulk-grading-form" action="{{ route('teacher.exams.attempts.bulk-update-grades', [$exam, $attempt]) }}" method="POST" class="mt-4 hidden">
                            @csrf
                            @method('PATCH')
                            <div class="flex justify-end mb-4">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                                    <i class="fas fa-save mr-2"></i>Save All Scores
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="space-y-6" id="questions-container">
                    @foreach($exam->questions as $index => $question)
                        @php
                            $userAnswer = $attempt->answers[$question->id] ?? null;
                            $manualGrade = $manualGrades[$question->id] ?? null;
                            $maxPoints = $question->pivot->points ?? $question->points ?? 0;

                            // Use CONTROLLER LOGIC to determine correctness
                            $pointsAwarded = 0;
                            $isCorrect = false;
                            $gradingMethod = 'auto';

                           // FIXED Blade template logic:
if ($manualGrade !== null) {
    $pointsAwarded = (float)$manualGrade;
    $gradingMethod = 'manual';
    $isCorrect = $pointsAwarded > 0;
} else {
    // Auto-grade only objective questions
    if (in_array($question->type, ['mcq', 'true_false']) && !empty($userAnswer)) {
        if ($question->type === 'mcq') {
            // Correct answer is option id
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

                            $bgColorClass = 'bg-gray-50 dark:bg-gray-700';
                            $borderColorClass = 'border-gray-200 dark:border-gray-600';

                            if ($gradingMethod === 'manual') {
                                $bgColorClass = 'bg-green-50 dark:bg-green-900/20';
                                $borderColorClass = 'border-green-200 dark:border-green-700';
                            } elseif ($needsGrading) {
                                $bgColorClass = 'bg-yellow-50 dark:bg-yellow-900/20';
                                $borderColorClass = 'border-yellow-200 dark:border-yellow-700';
                            } elseif ($isCorrect) {
                                $bgColorClass = 'bg-green-50 dark:bg-green-900/20';
                                $borderColorClass = 'border-green-200 dark:border-green-700';
                            } elseif (!$isCorrect && $userAnswer) {
                                $bgColorClass = 'bg-red-50 dark:bg-red-900/20';
                                $borderColorClass = 'border-red-200 dark:border-red-700';
                            }
                        @endphp
                        <div class="border rounded-lg p-4 {{ $borderColorClass }} {{ $bgColorClass }} question-item" data-question-id="{{ $question->id }}">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    Question {{ $index + 1 }}
                                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ $maxPoints }} points)</span>
                                </h3>
                                <div class="flex items-center space-x-2">
                                    @if($gradingMethod === 'manual')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                <i class="fas fa-user-edit mr-1"></i>Manually Graded
                            </span>
                                    @elseif($needsGrading)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                <i class="fas fa-clock mr-1"></i>Needs Grading
                            </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $isCorrect ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                {{ $isCorrect ? 'Correct' : 'Incorrect' }}
                            </span>
                                    @endif
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $pointsAwarded }}/{{ $maxPoints }}
                        </span>
                                </div>
                            </div>

                            <!-- Question Text -->
                            <div class="mb-4">
                                <p class="text-gray-700 dark:text-gray-300 text-lg whitespace-pre-wrap">{{ $question->question_text }}</p>
                                <div class="flex items-center mt-2 space-x-2 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-600">
                                        <i class="fas fa-tag mr-1"></i>{{ ucfirst(str_replace('_', ' ', $question->type)) }}
                                    </span>
                                    @if($question->difficulty)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full
                                            {{ $question->difficulty === 'easy' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' :
                                               ($question->difficulty === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' :
                                               'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100') }}">
                                            {{ ucfirst($question->difficulty) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- User Answer -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Student's Answer:</label>
                                <div class="bg-white dark:bg-gray-600 rounded-lg p-4 border border-gray-300 dark:border-gray-500">
                                    @if($question->type === 'mcq')
                                        <div class="space-y-2">
                                            @php
                                                // Get correct option ID
                                                $correctOption = $question->options->where('is_correct', true)->first();
                                                $correctOptionId = $correctOption ? (int)$correctOption->id : (int)$question->correct_answer;
                                                $studentAnswerId = (int)$userAnswer;
                                            @endphp

                                            @foreach($question->options as $option)
                                                @php
                                                    $isStudentAnswer = ((int)$option->id === $studentAnswerId);
                                                    $isCorrectOption = (bool)$option->is_correct;

                                                    $studentGotItRight = $isStudentAnswer && $isCorrectOption;
                                                    $studentGotItWrong = $isStudentAnswer && !$isCorrectOption;

                                                    $bgClass = $isStudentAnswer
                                                        ? ($studentGotItRight ? 'bg-green-50 dark:bg-green-900/30' : 'bg-red-50 dark:bg-red-900/30')
                                                        : 'bg-gray-50 dark:bg-gray-700';

                                                    $borderClass = $isStudentAnswer
                                                        ? ($studentGotItRight ? 'border border-green-200 dark:border-green-700' : 'border border-red-200 dark:border-red-700')
                                                        : 'border border-gray-300 dark:border-gray-600';

                                                    $textClass = $isStudentAnswer
                                                        ? ($studentGotItRight ? 'text-green-700 dark:text-green-300 font-medium' : 'text-red-700 dark:text-red-300 font-medium')
                                                        : 'text-gray-700 dark:text-gray-300';
                                                @endphp

                                                <div class="flex items-center p-2 rounded {{ $bgClass }} {{ $borderClass }}">
                                                    <div class="flex items-center justify-center w-6 h-6 rounded-full border mr-3
                                            {{ $isStudentAnswer
                                                ? ($studentGotItRight ? 'bg-green-600 border-green-600' : 'bg-red-600 border-red-600')
                                                : 'border-gray-400'
                                            }}">
                                                        @if($isStudentAnswer)
                                                            <i class="fas fa-check text-white text-xs"></i>
                                                        @endif
                                                    </div>
                                                    <span class="{{ $textClass }}">
                                            {{ $option->option_text }}

                                                        @if($isCorrectOption)
                                                            <span class="ml-2 text-green-600 dark:text-green-400 text-xs">
                                                    <i class="fas fa-check-circle"></i> Correct Answer
                                                </span>
                                                        @endif

                                                        @if($studentGotItRight)
                                                            <span class="ml-2 text-green-600 dark:text-green-400 text-xs">
                                                    <i class="fas fa-star"></i> You selected this (Correct)
                                                </span>
                                                        @elseif($studentGotItWrong)
                                                            <span class="ml-2 text-red-600 dark:text-red-400 text-xs">
                                                    <i class="fas fa-times-circle"></i> You selected this (Wrong)
                                                </span>
                                                        @endif
                                        </span>
                                                </div>
                                            @endforeach

                                            <div class="mt-3 p-2 bg-gray-100 dark:bg-gray-700 rounded text-xs text-gray-600 dark:text-gray-300">
                                                <strong>Debug Info:</strong>
                                                Student Answer (option id): <code>{{ $studentAnswerId }}</code> |
                                                Correct Answer (option id): <code>{{ $correctOptionId }}</code> |
                                                Controller Result: <code>{{ $studentAnswerId === $correctOptionId ? 'CORRECT' : 'INCORRECT' }}</code> |
                                                Blade Calculated: <code>{{ $isCorrect ? 'CORRECT' : 'INCORRECT' }}</code> |
                                                Points: <code>{{ $pointsAwarded }}/{{ $maxPoints }}</code>
                                            </div>

                                            @if(!isset($userAnswer) || $userAnswer === '')
                                                <p class="text-red-500 dark:text-red-400 italic">Not answered</p>
                                            @endif
                                        </div>
                                    @elseif($question->type === 'true_false')
                                        @php
                                            $studentAnswerStr = strtolower(trim($userAnswer ?? ''));
                                            $correctAnswerStr = strtolower(trim($question->correct_answer ?? ''));
                                            $isCorrectTrueFalse = $studentAnswerStr === $correctAnswerStr;
                                        @endphp
                                        <div class="flex space-x-4">
                                            <div class="flex-1 p-3 rounded border {{ $userAnswer === 'true' ?
                                    ($isCorrectTrueFalse ? 'bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-700' : 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-700') :
                                    'bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600' }}">
                                                <div class="flex items-center">
                                                    <div class="w-5 h-5 rounded-full border mr-3 flex items-center justify-center
                                            {{ $userAnswer === 'true' ?
                                                ($isCorrectTrueFalse ? 'bg-green-600 border-green-600' : 'bg-red-600 border-red-600') :
                                                'border-gray-400' }}">
                                                        @if($userAnswer === 'true')
                                                            <i class="fas fa-check text-white text-xs"></i>
                                                        @endif
                                                    </div>
                                                    <span class="{{ $userAnswer === 'true' ?
                                            ($isCorrectTrueFalse ? 'text-green-700 dark:text-green-300 font-medium' : 'text-red-700 dark:text-red-300 font-medium') :
                                            'text-gray-700 dark:text-gray-300' }}">True</span>
                                                    @if($question->correct_answer === 'true')
                                                        <span class="ml-auto text-green-600 dark:text-green-400 text-sm">
                                                <i class="fas fa-check-circle"></i> Correct Answer
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex-1 p-3 rounded border {{ $userAnswer === 'false' ?
                                    ($isCorrectTrueFalse ? 'bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-700' : 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-700') :
                                    'bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600' }}">
                                                <div class="flex items-center">
                                                    <div class="w-5 h-5 rounded-full border mr-3 flex items-center justify-center
                                            {{ $userAnswer === 'false' ?
                                                ($isCorrectTrueFalse ? 'bg-green-600 border-green-600' : 'bg-red-600 border-red-600') :
                                                'border-gray-400' }}">
                                                        @if($userAnswer === 'false')
                                                            <i class="fas fa-check text-white text-xs"></i>
                                                        @endif
                                                    </div>
                                                    <span class="{{ $userAnswer === 'false' ?
                                            ($isCorrectTrueFalse ? 'text-green-700 dark:text-green-300 font-medium' : 'text-red-700 dark:text-red-300 font-medium') :
                                            'text-gray-700 dark:text-gray-300' }}">False</span>
                                                    @if($question->correct_answer === 'false')
                                                        <span class="ml-auto text-green-600 dark:text-green-400 text-sm">
                                                <i class="fas fa-check-circle"></i> Correct Answer
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @if(!$userAnswer || $userAnswer === '')
                                            <p class="text-red-500 dark:text-red-400 italic mt-2">Not answered</p>
                                        @endif
                                    @elseif($question->type === 'fill_blank' && is_array($userAnswer))
                                        <div class="space-y-2">
                                            @php
                                                $parts = explode('[blank]', $question->question_text);
                                            @endphp
                                            <p class="text-gray-700 dark:text-gray-300">
                                                @foreach($parts as $partIndex => $part)
                                                    {{ $part }}
                                                    @if($partIndex < count($parts) - 1)
                                                        <span class="inline-block mx-1 px-2 py-1 bg-yellow-100 dark:bg-yellow-900/50 border border-yellow-300 dark:border-yellow-700 rounded">
                                                {{ $userAnswer[$partIndex] ?? '______' }}
                                            </span>
                                                    @endif
                                                @endforeach
                                            </p>
                                        </div>
                                    @else
                                        <div class="whitespace-pre-wrap text-gray-700 dark:text-gray-300 min-h-[100px] p-3 bg-gray-50 dark:bg-gray-700 rounded border">
                                            {!! $userAnswer ?: '<span class="text-red-500 dark:text-red-400 italic">Not answered</span>' !!}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Manual Grading for subjective questions -->
                            @if(auth()->user()->isTeacher() && in_array($question->type, ['short_answer', 'essay', 'fill_blank']))
                                <div class="manual-grading-section mt-4 p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-300 dark:border-gray-600">
                                    <div class="flex justify-between items-center mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            @if($gradingMethod === 'manual')
                                                <i class="fas fa-check-circle text-green-500 mr-2"></i>Manually Graded
                                            @else
                                                <i class="fas fa-edit text-yellow-500 mr-2"></i>Manual Grading Required
                                            @endif
                                        </label>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            Max: {{ $maxPoints }} points
                                        </span>
                                    </div>

                                    <!-- Individual Grading Form - FIXED: Using POST -->
                                    <form action="{{ route('teacher.exams.attempts.update-score', [$exam, $attempt]) }}" method="POST" class="individual-grading-form">
                                        @csrf
                                        <input type="hidden" name="question_id" value="{{ $question->id }}">

                                        <div class="flex items-end space-x-4">
                                            <div class="flex-1">
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Award Points (0 - {{ $maxPoints }})
                                                </label>
                                                <div class="flex items-center space-x-3">
                                                    <input type="range"
                                                           name="score"
                                                           min="0"
                                                           max="{{ $maxPoints }}"
                                                           value="{{ $pointsAwarded }}"
                                                           step="0.5"
                                                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-600 slider-{{ $question->id }}"
                                                           oninput="updateScoreDisplay('{{ $question->id }}', this.value)">

                                                    <input type="number"
                                                           id="score-input-{{ $question->id }}"
                                                           name="score"
                                                           min="0"
                                                           max="{{ $maxPoints }}"
                                                           value="{{ $pointsAwarded }}"
                                                           step="0.5"
                                                           class="w-20 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-white text-center individual-score-input">

                                                    <span class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                                        / {{ $maxPoints }} points
                                                    </span>
                                                </div>

                                                <!-- Quick action buttons -->
                                                <div class="flex space-x-2 mt-2">
                                                    <button type="button"
                                                            onclick="setScore('{{ $question->id }}', 0)"
                                                            class="text-xs px-2 py-1 bg-gray-500 hover:bg-gray-600 text-white rounded transition-colors duration-200">
                                                        Zero
                                                    </button>
                                                    <button type="button"
                                                            onclick="setScore('{{ $question->id }}', {{ $maxPoints * 0.25 }})"
                                                            class="text-xs px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded transition-colors duration-200">
                                                        25%
                                                    </button>
                                                    <button type="button"
                                                            onclick="setScore('{{ $question->id }}', {{ $maxPoints * 0.5 }})"
                                                            class="text-xs px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded transition-colors duration-200">
                                                        50%
                                                    </button>
                                                    <button type="button"
                                                            onclick="setScore('{{ $question->id }}', {{ $maxPoints * 0.75 }})"
                                                            class="text-xs px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded transition-colors duration-200">
                                                        75%
                                                    </button>
                                                    <button type="button"
                                                            onclick="setScore('{{ $question->id }}', {{ $maxPoints }})"
                                                            class="text-xs px-2 py-1 bg-green-500 hover:bg-green-600 text-white rounded transition-colors duration-200">
                                                        Full
                                                    </button>
                                                </div>
                                            </div>

                                            <button type="submit"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 whitespace-nowrap flex items-center">
                                                <i class="fas fa-save mr-2"></i>Save Score
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Bulk Grading Input (Hidden by default) -->
                                    <div class="bulk-grading-input mt-3 hidden">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Bulk Grading Score:
                                        </label>
                                        <input type="number"
                                               name="scores[{{ $question->id }}]"
                                               min="0"
                                               max="{{ $maxPoints }}"
                                               value="{{ $pointsAwarded }}"
                                               step="0.5"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-white bulk-score-input"
                                               data-max-points="{{ $maxPoints }}">
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Total Score Summary -->
                <div class="mt-8 p-6 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Total Score Summary</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                @if($attempt->status === 'graded')
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>Exam has been graded
                                @elseif($attempt->status === 'submitted')
                                    <i class="fas fa-clock text-yellow-500 mr-2"></i>Awaiting manual grading
                                @else
                                    Automatically calculated based on answers
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $attempt->total_score ?? 0 }}/{{ $exam->total_marks }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $percentage }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global functions to handle score updates for all questions
        function updateScoreDisplay(questionId, value) {
            const input = document.getElementById('score-input-' + questionId);
            if (input) {
                input.value = value;
            }
        }

        function setScore(questionId, score) {
            const maxPoints = parseFloat(document.querySelector('.bulk-score-input[data-max-points]')?.dataset.maxPoints || 100);
            const roundedScore = Math.min(Math.max(parseFloat(score), 0), maxPoints);

            const input = document.getElementById('score-input-' + questionId);
            if (input) {
                input.value = roundedScore;
            }

            // Also update the range slider
            const rangeSlider = document.querySelector('.slider-' + questionId);
            if (rangeSlider) {
                rangeSlider.value = roundedScore;
            }

            // Update bulk grading input if it exists and bulk grading is enabled
            const bulkInput = document.querySelector(`input[name="scores[${questionId}]"]`);
            if (bulkInput && window.bulkGradingEnabled) {
                bulkInput.value = roundedScore;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const toggleBulkGradingBtn = document.getElementById('toggle-bulk-grading');
            const bulkGradingForm = document.getElementById('bulk-grading-form');
            const bulkGradingInputs = document.querySelectorAll('.bulk-grading-input');
            const individualGradingForms = document.querySelectorAll('.individual-grading-form');
            const individualScoreInputs = document.querySelectorAll('.individual-score-input');
            const bulkScoreInputs = document.querySelectorAll('.bulk-score-input');

            // Make bulkGradingEnabled a global variable
            window.bulkGradingEnabled = false;

            // Toggle bulk grading mode
            if (toggleBulkGradingBtn) {
                toggleBulkGradingBtn.addEventListener('click', function() {
                    window.bulkGradingEnabled = !window.bulkGradingEnabled;

                    if (window.bulkGradingEnabled) {
                        // Enable bulk grading
                        bulkGradingForm.classList.remove('hidden');
                        bulkGradingInputs.forEach(input => input.classList.remove('hidden'));
                        individualGradingForms.forEach(form => form.classList.add('hidden'));
                        toggleBulkGradingBtn.innerHTML = '<i class="fas fa-times mr-2"></i>Cancel Bulk Grading';
                        toggleBulkGradingBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                        toggleBulkGradingBtn.classList.add('bg-gray-600', 'hover:bg-gray-700');

                        // Sync individual scores to bulk inputs
                        individualScoreInputs.forEach((input, index) => {
                            if (bulkScoreInputs[index]) {
                                bulkScoreInputs[index].value = input.value;
                            }
                        });
                    } else {
                        // Disable bulk grading
                        bulkGradingForm.classList.add('hidden');
                        bulkGradingInputs.forEach(input => input.classList.add('hidden'));
                        individualGradingForms.forEach(form => form.classList.remove('hidden'));
                        toggleBulkGradingBtn.innerHTML = '<i class="fas fa-edit mr-2"></i>Enable Bulk Grading';
                        toggleBulkGradingBtn.classList.remove('bg-gray-600', 'hover:bg-gray-700');
                        toggleBulkGradingBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    }
                });
            }

            // Sync individual scores with bulk scores when bulk grading is enabled
            individualScoreInputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    if (window.bulkGradingEnabled && bulkScoreInputs[index]) {
                        bulkScoreInputs[index].value = this.value;
                    }
                });
            });

            // Validate bulk scores before submission
            if (bulkGradingForm) {
                bulkGradingForm.addEventListener('submit', function(e) {
                    let hasErrors = false;
                    const errorMessages = [];

                    bulkScoreInputs.forEach(input => {
                        const maxPoints = parseFloat(input.dataset.maxPoints);
                        const currentValue = parseFloat(input.value) || 0;

                        if (currentValue > maxPoints) {
                            hasErrors = true;
                            errorMessages.push(`Score for question cannot exceed ${maxPoints} points`);
                        }

                        if (isNaN(currentValue)) {
                            hasErrors = true;
                            errorMessages.push(`Please enter a valid number for all scores`);
                        }
                    });

                    if (hasErrors) {
                        e.preventDefault();
                        alert('Please fix the following errors:\n\n' + errorMessages.join('\n'));
                        return false;
                    }

                    // Show loading state
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
                    submitBtn.disabled = true;

                    // Re-enable after 3 seconds if still on page
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 3000);
                });
            }

            // Add input validation for individual score inputs
            individualScoreInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const maxPoints = parseFloat(this.max);
                    const currentValue = parseFloat(this.value) || 0;

                    if (currentValue > maxPoints) {
                        this.value = maxPoints;
                        alert(`Score cannot exceed ${maxPoints} points`);
                    }

                    if (currentValue < 0) {
                        this.value = 0;
                    }
                });
            });
        });
    </script>
@endsection
