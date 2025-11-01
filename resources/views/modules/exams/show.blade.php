@extends('layouts.app')

@section('title', 'Exam Details - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>

                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $exam->title }}</h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-2">
                        {{ $exam->subject->name }} • {{ $exam->class->name }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.exams.index') }}"
                       class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Exams
                    </a>
                    <a href="{{ route('admin.exams.edit', $exam) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Exam
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Exam Details -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-info-circle mr-3 text-blue-500"></i>
                            Exam Details
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Exam Type</h3>
                                <p class="text-lg text-gray-900 dark:text-white mt-1 flex items-center">
                                    <i class="fas fa-tag mr-2 text-purple-500"></i>
                                    {{ ucfirst(str_replace('_', ' ', $exam->type)) }}
                                </p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</h3>
                                <p class="text-lg text-gray-900 dark:text-white mt-1 flex items-center">
                                    <i class="fas fa-clock mr-2 text-orange-500"></i>
                                    {{ $exam->duration }} minutes
                                </p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Marks</h3>
                                <p class="text-lg text-gray-900 dark:text-white mt-1 flex items-center">
                                    <i class="fas fa-star mr-2 text-yellow-500"></i>
                                    {{ $exam->total_marks }} marks
                                </p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Passing Marks</h3>
                                <p class="text-lg text-gray-900 dark:text-white mt-1 flex items-center">
                                    <i class="fas fa-check-circle mr-2 text-green-500"></i>
                                    {{ $exam->passing_marks }} marks
                                </p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Max Attempts</h3>
                                <p class="text-lg text-gray-900 dark:text-white mt-1 flex items-center">
                                    <i class="fas fa-redo mr-2 text-blue-500"></i>
                                    {{ $exam->max_attempts }} attempt(s)
                                </p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Attempts</h3>
                                <p class="text-lg text-gray-900 dark:text-white mt-1 flex items-center">
                                    <i class="fas fa-users mr-2 text-purple-500"></i>
                                    {{ $exam->attempts->count() }} attempts
                                </p>
                            </div>
                        </div>

                        <!-- Time Period -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Start Time</h3>
                                <p class="text-lg text-gray-900 dark:text-white mt-1 flex items-center">
                                    <i class="fas fa-calendar-start mr-2 text-green-500"></i>
                                    {{ $exam->start_time->format('F j, Y \a\t g:i A') }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $exam->start_time->isPast() ? 'Started' : 'Starts' }} {{ $exam->start_time->diffForHumans() }}
                                </p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">End Time</h3>
                                <p class="text-lg text-gray-900 dark:text-white mt-1 flex items-center">
                                    <i class="fas fa-calendar-end mr-2 text-red-500"></i>
                                    {{ $exam->end_time->format('F j, Y \a\t g:i A') }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $exam->end_time->isPast() ? 'Ended' : 'Ends' }} {{ $exam->end_time->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        <!-- Exam Settings -->
                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Exam Settings</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center">
                                    <i class="fas fa-random mr-3 {{ $exam->randomize_questions ? 'text-green-500' : 'text-gray-400' }}"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Randomize Questions</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-expand mr-3 {{ $exam->require_fullscreen ? 'text-green-500' : 'text-gray-400' }}"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Require Fullscreen</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-eye mr-3 {{ $exam->show_results ? 'text-green-500' : 'text-gray-400' }}"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Show Results to Students</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-eye mr-3 {{ $exam->is_published ? 'text-green-500' : 'text-gray-400' }}"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Published</span>
                                </div>
                            </div>
                        </div>

                        @if($exam->instructions)
                            <div class="mt-6">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Instructions</h3>
                                <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4">
                                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $exam->instructions }}</p>
                                </div>
                            </div>
                        @endif

                        @if($exam->description)
                            <div class="mt-6">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</h3>
                                <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4">
                                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $exam->description }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Questions Section -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-question-circle mr-3 text-green-500"></i>
                            Exam Questions ({{ $exam->questions->count() }})
                        </h2>

                        <div class="space-y-4">
                            @foreach($exam->questions as $index => $question)
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                            Question {{ $index + 1 }}
                                            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                                ({{ $question->pivot->points }} points)
                                            </span>
                                        </h3>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 capitalize">
                                            {{ str_replace('_', ' ', $question->type) }}
                                        </span>
                                    </div>

                                    <div class="mb-4">
                                        <p class="text-gray-700 dark:text-gray-300">{{ $question->question_text }}</p>
                                    </div>

                                    @if($question->type === 'mcq')
                                        <div class="space-y-2">
                                            @foreach($question->options as $optionIndex => $option)
                                                <div class="flex items-center space-x-3 p-2 rounded {{ $option->is_correct ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-700' }}">
                                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400 w-6">
                                                        {{ chr(65 + $optionIndex) }}.
                                                    </span>
                                                    <span class="text-gray-700 dark:text-gray-300 {{ $option->is_correct ? 'font-semibold text-green-700 dark:text-green-300' : '' }}">
                                                        {{ $option->option_text }}
                                                    </span>
                                                    @if($option->is_correct)
                                                        <span class="ml-auto text-green-600 dark:text-green-400 text-sm">
                                                            <i class="fas fa-check"></i> Correct
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($question->type === 'true_false')
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                            <p class="text-gray-700 dark:text-gray-300">
                                                Correct Answer: <span class="font-semibold">{{ ucfirst($question->correct_answer) }}</span>
                                            </p>
                                        </div>
                                    @elseif($question->type === 'short_answer' && $question->details['expected_answer'])
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Expected Answer:</p>
                                            <p class="text-gray-700 dark:text-gray-300">{{ $question->details['expected_answer'] }}</p>
                                        </div>
                                    @elseif($question->type === 'essay' && $question->details['grading_rubric'])
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Grading Rubric:</p>
                                            <p class="text-gray-700 dark:text-gray-300">{{ $question->details['grading_rubric'] }}</p>
                                        </div>
                                    @elseif($question->type === 'fill_blank')
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Question with Blanks:</p>
                                            <p class="text-gray-700 dark:text-gray-300 mb-2">{{ $question->details['blank_question'] }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Correct Answers:</p>
                                            <p class="text-gray-700 dark:text-gray-300">{{ implode(', ', $question->details['blank_answers']) }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Exam Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Exam Status</h3>
                        <div class="space-y-3">
                            @php
                                $isActive = $exam->start_time <= now() && $exam->end_time >= now();
                                $isUpcoming = $exam->start_time > now();
                                $isPast = $exam->end_time < now();
                            @endphp

                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Availability</span>
                                <span class="font-semibold text-gray-900 dark:text-white capitalize">
                                    {{ $isActive ? 'Active' : ($isUpcoming ? 'Upcoming' : 'Completed') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Attempts</span>
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ $exam->attempts->count() }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Time Remaining</span>
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    @if($isActive)
                                        {{ $exam->end_time->diffForHumans(['parts' => 2]) }}
                                    @elseif($isUpcoming)
                                        Starts {{ $exam->start_time->diffForHumans() }}
                                    @else
                                        Ended
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Status</span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $exam->is_published ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                    {{ $exam->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('admin.exams.attempts.index', $exam) }}"
                               class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                                <i class="fas fa-users mr-2"></i>
                                View Attempts ({{ $exam->attempts->count() }})
                            </a>

                            <form action="{{ route('admin.exams.toggle-publish', $exam) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="w-full {{ $exam->is_published ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                                    <i class="fas fa-eye{{ $exam->is_published ? '-slash' : '' }} mr-2"></i>
                                    {{ $exam->is_published ? 'Unpublish Exam' : 'Publish Exam' }}
                                </button>
                            </form>

                            <a href="{{ route('admin.exams.edit', $exam) }}"
                               class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Exam
                            </a>
                        </div>
                    </div>

                    <!-- Recent Attempts -->
                    @if($exam->attempts->count() > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Attempts</h3>
                            <div class="space-y-3">
                                @foreach($exam->attempts->sortByDesc('created_at')->take(5) as $attempt)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $attempt->student->name }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $attempt->started_at->format('M j, g:i A') }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-sm font-medium {{ $attempt->status === 'graded' ? 'text-green-600 dark:text-green-400' : ($attempt->status === 'submitted' ? 'text-blue-600 dark:text-blue-400' : 'text-orange-600 dark:text-orange-400') }}">
                                                {{ ucfirst($attempt->status) }}
                                            </span>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                @if($attempt->score)
                                                    {{ $attempt->score }}/{{ $exam->total_marks }}
                                                @else
                                                    In progress
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endforeach

                                @if($exam->attempts->count() > 5)
                                    <div class="text-center">
                                        <a href="{{ route('admin.exams.attempts.index', $exam) }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm">
                                            View all {{ $exam->attempts->count() }} attempts
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
