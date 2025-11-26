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
                    @if(auth()->user()->isTeacher())
                        <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">Teacher View</p>
                    @else
                        <p class="text-sm text-purple-600 dark:text-purple-400 mt-1">Admin View</p>
                    @endif
                </div>
                <div class="flex space-x-3">
                    @if(auth()->user()->isTeacher())
                        <a href="{{ route('teacher.exams.index') }}"
                           class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Exams
                        </a>
                        <a href="{{ route('teacher.exams.edit', $exam) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Exam
                        </a>
                    @else
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
                    @endif
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

                    <!-- Result Release Control -->
                    @if($exam->attempts()->where('status', 'graded')->exists())
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Result Release Control</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                                Control when students can see their detailed exam results.
                            </p>

                            @if($exam->show_results && $exam->results_released_at)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                        <span class="text-green-700 dark:text-green-300">
                    Results released to students on {{ $exam->results_released_at->format('M j, Y g:i A') }}
                </span>
                                    </div>
                                    <form action="{{ route('teacher.exams.withdraw-results', $exam) }}" method="POST" id="withdraw-form">
                                        @csrf
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm" onclick="debugForm('withdraw')">
                                            <i class="fas fa-eye-slash mr-1"></i>Withdraw Results
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fas fa-clock text-yellow-500 mr-2"></i>
                                        <span class="text-yellow-700 dark:text-yellow-300">
                    Results not released to students
                </span>
                                    </div>
                                    <form action="{{ route('teacher.exams.release-results', $exam) }}" method="POST" id="release-form">
                                        @csrf
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm" onclick="debugForm('release')">
                                            <i class="fas fa-eye mr-1"></i>Release Results
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <script>
                            function debugForm(action) {
                                const form = document.getElementById(action + '-form');
                                console.log('Form action:', form.action);
                                console.log('Form method:', form.method);
                                console.log('Exam ID:', {{ $exam->id }});

                                // Add a small delay to see the console before form submission
                                setTimeout(() => {
                                    console.log('Form submitted for:', action);
                                }, 100);
                            }
                        </script>
                    @endif

                    <!-- Questions Section - Enhanced -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                                <i class="fas fa-question-circle mr-3 text-green-500"></i>
                                Exam Questions ({{ $exam->questions->count() }})
                            </h2>
                            <div class="flex space-x-2">
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                    {{ $exam->questions->where('is_bank_question', true)->count() }} Bank Questions
                                </span>
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    {{ $exam->questions->where('is_bank_question', false)->count() }} Custom Questions
                                </span>
                            </div>
                        </div>

                        @if($exam->questions->count() > 0)
                            <div class="space-y-6">
                                @foreach($exam->questions->sortBy('pivot.order') as $index => $question)
                                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                                        <div class="flex justify-between items-start mb-4">
                                            <div class="flex items-center space-x-3">
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                                    Question {{ $index + 1 }}
                                                </h3>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 capitalize">
                                                    {{ str_replace('_', ' ', $question->type) }}
                                                </span>
                                                @if($question->is_bank_question)
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 flex items-center">
                                                        <i class="fas fa-database mr-1"></i>
                                                        Bank Question
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 flex items-center">
                                                        <i class="fas fa-pen mr-1"></i>
                                                        Custom Question
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <span class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">
                                                    {{ $question->pivot->points }} points
                                                </span>
                                                @if($question->difficulty)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 capitalize">
                                                        Difficulty: {{ $question->difficulty }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Question Text -->
                                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                            <p class="text-gray-800 dark:text-gray-200 text-lg leading-relaxed">
                                                {{ $question->question_text }}
                                            </p>
                                        </div>

                                        <!-- Question Content Based on Type -->
                                        @if($question->type === 'mcq')
                                            <div class="space-y-3">
                                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Options:</h4>
                                                @foreach($question->options->sortBy('order') as $optionIndex => $option)
                                                    <div class="flex items-center space-x-3 p-3 rounded-lg border {{ $option->is_correct ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20' : 'border-gray-200 bg-gray-50 dark:border-gray-600 dark:bg-gray-700' }}">
                                                        <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full {{ $option->is_correct ? 'bg-green-500 text-white' : 'bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300' }} font-medium text-sm">
                                                            {{ chr(65 + $optionIndex) }}
                                                        </span>
                                                        <span class="flex-1 text-gray-700 dark:text-gray-300 {{ $option->is_correct ? 'font-semibold' : '' }}">
                                                            {{ $option->option_text }}
                                                        </span>
                                                        @if($option->is_correct)
                                                            <span class="flex-shrink-0 text-green-600 dark:text-green-400 text-sm font-medium flex items-center">
                                                                <i class="fas fa-check-circle mr-1"></i>
                                                                Correct Answer
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>

                                        @elseif($question->type === 'true_false')
                                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Correct Answer:</h4>
                                                <div class="flex items-center space-x-4">
                                                    <div class="flex items-center">
                                                        <div class="w-4 h-4 rounded-full border-2 {{ $question->correct_answer === 'true' ? 'border-green-500 bg-green-500' : 'border-gray-300' }} mr-2"></div>
                                                        <span class="text-gray-700 dark:text-gray-300 {{ $question->correct_answer === 'true' ? 'font-semibold text-green-600 dark:text-green-400' : '' }}">True</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <div class="w-4 h-4 rounded-full border-2 {{ $question->correct_answer === 'false' ? 'border-green-500 bg-green-500' : 'border-gray-300' }} mr-2"></div>
                                                        <span class="text-gray-700 dark:text-gray-300 {{ $question->correct_answer === 'false' ? 'font-semibold text-green-600 dark:text-green-400' : '' }}">False</span>
                                                    </div>
                                                </div>
                                            </div>

                                        @elseif($question->type === 'short_answer')
                                            <div class="space-y-4">
                                                @if(isset($question->details['expected_answer']) && $question->details['expected_answer'])
                                                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expected Answer (for grading):</h4>
                                                        <p class="text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-3 rounded border">
                                                            {{ $question->details['expected_answer'] }}
                                                        </p>
                                                    </div>
                                                @endif
                                                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Student Answer Area:</h4>
                                                    <div class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded p-3 min-h-[100px] text-gray-500 dark:text-gray-400">
                                                        Student will type their answer here...
                                                    </div>
                                                </div>
                                            </div>

                                        @elseif($question->type === 'essay')
                                            <div class="space-y-4">
                                                @if(isset($question->details['grading_rubric']) && $question->details['grading_rubric'])
                                                    <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                                                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Grading Rubric:</h4>
                                                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line bg-white dark:bg-gray-800 p-3 rounded border">
                                                            {{ $question->details['grading_rubric'] }}
                                                        </p>
                                                    </div>
                                                @endif
                                                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Student Answer Area:</h4>
                                                    <div class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded p-3 min-h-[200px] text-gray-500 dark:text-gray-400">
                                                        Student will write their essay here...
                                                    </div>
                                                </div>
                                            </div>

                                        @elseif($question->type === 'fill_blank')
                                            <div class="space-y-4">
                                                @if(isset($question->details['blank_question']) && $question->details['blank_question'])
                                                    <div class="p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-200 dark:border-indigo-800">
                                                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question with Blanks:</h4>
                                                        <p class="text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-3 rounded border">
                                                            {!! str_replace('[blank]', '<span class="bg-yellow-200 dark:bg-yellow-800 px-2 py-1 rounded border border-yellow-300 dark:border-yellow-600 font-medium">______</span>', e($question->details['blank_question'])) !!}
                                                        </p>
                                                    </div>
                                                @endif
                                                @if(isset($question->details['blank_answers']) && $question->details['blank_answers'])
                                                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Correct Answers:</h4>
                                                        <div class="flex flex-wrap gap-2">
                                                            @foreach((array)$question->details['blank_answers'] as $answer)
                                                                <span class="bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 px-3 py-1 rounded-full text-sm font-medium">
                                                                    {{ $answer }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <!-- Question Metadata -->
                                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600 flex justify-between items-center text-sm text-gray-500 dark:text-gray-400">
                                            <div class="flex space-x-4">
                                                <span>ID: {{ $question->id }}</span>
                                                @if($question->subject)
                                                    <span>Subject: {{ $question->subject->name }}</span>
                                                @endif
                                            </div>
                                            <div>
                                                Created: {{ $question->created_at->format('M j, Y') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                                <i class="fas fa-inbox text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400 text-lg">No questions added to this exam yet</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Add questions from the question bank or create new ones</p>
                                @if(auth()->user()->isTeacher())
                                    <a href="{{ route('teacher.exams.edit', $exam) }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                                        <i class="fas fa-plus mr-2"></i>
                                        Add Questions
                                    </a>
                                @else
                                    <a href="{{ route('admin.exams.edit', $exam) }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                                        <i class="fas fa-plus mr-2"></i>
                                        Add Questions
                                    </a>
                                @endif
                            </div>
                        @endif
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
                            @if(auth()->user()->isTeacher())
                                <a href="{{ route('teacher.exams.attempts', $exam) }}"
                                   class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                                    <i class="fas fa-users mr-2"></i>
                                    View Attempts ({{ $exam->attempts->count() }})
                                </a>

                                <form action="{{ route('teacher.exams.toggle-publish', $exam) }}" method="POST" class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="w-full {{ $exam->is_published ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                                        <i class="fas fa-eye{{ $exam->is_published ? '-slash' : '' }} mr-2"></i>
                                        {{ $exam->is_published ? 'Unpublish Exam' : 'Publish Exam' }}
                                    </button>
                                </form>

                                <a href="{{ route('teacher.exams.edit', $exam) }}"
                                   class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                                    <i class="fas fa-edit mr-2"></i>
                                    Edit Exam
                                </a>

                                <!-- Print/Export Button -->
                                <a href="{{ route('teacher.exams.print', $exam) }}" target="_blank"
                                   class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                                    <i class="fas fa-print mr-2"></i>
                                    Print Exam
                                </a>
                            @else
                                <a href="{{ route('admin.exams.attempts', $exam) }}"
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

                                <!-- Print/Export Button -->
                                <a href="{{ route('admin.exams.print', $exam) }}" target="_blank"
                                   class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                                    <i class="fas fa-print mr-2"></i>
                                    Print Exam
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Question Statistics -->
                    @if($exam->questions->count() > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Question Statistics</h3>
                            <div class="space-y-4">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Question Types</h4>
                                    <div class="space-y-2">
                                        @php
                                            $typeCounts = $exam->questions->groupBy('type')->map->count();
                                        @endphp
                                        @foreach($typeCounts as $type => $count)
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-700 dark:text-gray-300 capitalize">
                                                    {{ str_replace('_', ' ', $type) }}
                                                </span>
                                                <span class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $count }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Points Summary</h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">Total Points</span>
                                            <span class="font-semibold text-yellow-600 dark:text-yellow-400">
                                                {{ $exam->questions->sum('pivot.points') }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">Average per Question</span>
                                            <span class="font-semibold text-gray-900 dark:text-white">
                                                {{ number_format($exam->questions->avg('pivot.points'), 1) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

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
                                                @if($attempt->total_score)
                                                    {{ $attempt->total_score }}/{{ $exam->total_marks }}
                                                @else
                                                    In progress
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endforeach

                                @if($exam->attempts->count() > 5)
                                    <div class="text-center">
                                        @if(auth()->user()->isTeacher())
                                            <a href="{{ route('teacher.exams.attempts', $exam) }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm">
                                                View all {{ $exam->attempts->count() }} attempts
                                            </a>
                                        @else
                                            <a href="{{ route('admin.exams.attempts', $exam) }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm">
                                                View all {{ $exam->attempts->count() }} attempts
                                            </a>
                                        @endif
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
