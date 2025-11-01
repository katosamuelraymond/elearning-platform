@extends('layouts.app')

@section('title', 'Exam Details - Lincoln eLearning')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $exam->title }}</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">{{ $exam->subject->name }} - {{ $exam->class->name }}</p>

            @if($exam->description)
            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                <p class="text-blue-800 dark:text-blue-200">{{ $exam->description }}</p>
            </div>
            @endif
        </div>

        <!-- Exam Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Basic Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Exam Information</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Type:</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ ucfirst($exam->type) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Duration:</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $exam->duration }} minutes</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Total Marks:</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $exam->total_marks }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Passing Marks:</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $exam->passing_marks }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Max Attempts:</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $exam->max_attempts }}</span>
                    </div>
                </div>
            </div>

            <!-- Timing -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Timing</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Start Time:</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $exam->start_time->format('M j, Y g:i A') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">End Time:</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $exam->end_time->format('M j, Y g:i A') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Time Remaining:</span>
                        <span class="text-gray-900 dark:text-white font-medium {{ $exam->end_time->isPast() ? 'text-red-600' : 'text-green-600' }}">
                            @if($exam->end_time->isPast())
                                Ended
                            @else
                                {{ $exam->end_time->diffForHumans() }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        @if($exam->instructions)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Instructions</h2>
            <div class="prose dark:prose-invert max-w-none">
                {!! nl2br(e($exam->instructions)) !!}
            </div>
        </div>
        @endif

        <!-- Attempt Status -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Your Attempt</h2>

            @if($attempt)
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Previous Attempt</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Status: <span class="font-medium capitalize">{{ $attempt->status }}</span>
                                @if($attempt->score)
                                    | Score: {{ $attempt->score }}/{{ $exam->total_marks }}
                                @endif
                            </p>
                        </div>
                        <div class="flex space-x-3">
                            @if($attempt->status === 'in_progress')
                                <a href="{{ route('student.exams.take', [$exam, $attempt]) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                                    Continue Exam
                                </a>
                            @elseif($attempt->status === 'graded' || $exam->show_results)
                                <a href="{{ route('student.exams.results', [$exam, $attempt]) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                                    View Results
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Start Exam Button -->
            <div class="mt-6 text-center">
                @if($canTakeExam)
                    <form action="{{ route('student.exams.start', $exam) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-colors duration-200">
                            Start Exam
                        </button>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">
                            You have {{ $exam->max_attempts - $exam->attempts()->where('student_id', auth()->id())->count() }} attempts remaining
                        </p>
                    </form>
                @else
                    <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                        <p class="text-yellow-800 dark:text-yellow-200">
                            @if(now() < $exam->start_time)
                                This exam will be available on {{ $exam->start_time->format('M j, Y g:i A') }}
                            @elseif(now() > $exam->end_time)
                                This exam has ended
                            @elseif($exam->attempts()->where('student_id', auth()->id())->count() >= $exam->max_attempts)
                                You have used all your attempts for this exam
                            @else
                                You cannot take this exam at this time
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Back Button -->
        <div class="text-center">
            <a href="{{ route('student.exams.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                Back to Exams
            </a>
        </div>
    </div>
</div>
@endsection
