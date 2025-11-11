@extends('layouts.app')

@section('title', $exam->title . ' - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('student.exams.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 mb-4">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Exams
                </a>

                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $exam->title }}</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">{{ $exam->description }}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Exam Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Exam Information</h2>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Subject</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $exam->subject->name }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Class</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $exam->class->name }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Duration</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $exam->duration }} minutes</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Total Points</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $exam->total_marks }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Passing Marks</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $exam->passing_marks }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Start Time</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $exam->start_time->format('M j, Y g:i A') }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">End Time</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $exam->end_time->format('M j, Y g:i A') }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600 dark:text-gray-400">Max Attempts</span>
                                <span class="text-gray-900 dark:text-white font-medium">{{ $exam->max_attempts }}</span>
                            </div>
                        </div>

                        @if($exam->instructions)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Instructions</h3>
                                <p class="text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $exam->instructions }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 sticky top-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Exam Actions</h3>

                        @if($canTakeExam)
                            <a href="#"
                               class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium text-center block transition-colors duration-200 mb-4">
                                Start Exam
                            </a>
                        @else
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4 text-center">
                                <p class="text-gray-600 dark:text-gray-300 text-sm">
                                    @if(now()->lt($exam->start_time))
                                        Exam starts in {{ $exam->start_time->diffForHumans() }}
                                    @elseif(now()->gt($exam->end_time))
                                        Exam has ended
                                    @else
                                        You have reached the maximum attempts
                                    @endif
                                </p>
                            </div>
                        @endif

                        <!-- Attempt History -->
                        @if($attempts->isNotEmpty())
                            <div class="mt-6">
                                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">Attempt History</h4>
                                <div class="space-y-3">
                                    @foreach($attempts as $attempt)
                                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    Attempt {{ $loop->iteration }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $attempt->created_at->format('M j, g:i A') }}
                                                </p>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $attempt->status === 'graded' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' :
                                               ($attempt->status === 'submitted' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' :
                                               'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200') }}">
                                            {{ ucfirst($attempt->status) }}
                                        </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
