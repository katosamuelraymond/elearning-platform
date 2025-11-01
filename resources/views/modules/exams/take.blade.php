@extends('layouts.app')

@section('title', 'Take Exam - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Exam Header -->
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $exam->title }}</h1>
                        <p class="text-gray-600 dark:text-gray-300">{{ $exam->subject->name }} - {{ $exam->class->name }}</p>
                    </div>
                    <div class="text-right">
                        <div id="timer" class="text-2xl font-bold text-red-600 dark:text-red-400"></div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Time Remaining</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exam Content -->
        <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <form id="exam-form" action="{{ route('student.exams.submit', [$exam, $attempt]) }}" method="POST">
                @csrf

                <!-- Instructions -->
                <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">Exam Instructions</h3>
                    <p class="text-blue-800 dark:text-blue-200">{{ $exam->instructions ?: 'Please answer all questions to the best of your ability.' }}</p>
                </div>

                <!-- Questions -->
                <div class="space-y-6">
                    @foreach($exam->questions as $index => $question)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Question {{ $index + 1 }}
                                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                    ({{ $question->pivot->points }} points)
                                </span>
                                </h3>
                            </div>

                            <div class="mb-4">
                                <p class="text-gray-700 dark:text-gray-300 text-lg">{{ $question->question_text }}</p>
                            </div>

                            <!-- Question Input Based on Type -->
                            @if($question->type === 'mcq')
                                <div class="space-y-3">
                                    @foreach($question->options as $optionIndex => $option)
                                        <label class="flex items-center space-x-3 p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $optionIndex }}" class="text-blue-600 focus:ring-blue-500">
                                            <span class="text-gray-700 dark:text-gray-300">{{ $option->option_text }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @elseif($question->type === 'true_false')
                                <div class="space-y-3">
                                    <label class="flex items-center space-x-3 p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                        <input type="radio" name="answers[{{ $question->id }}]" value="true" class="text-blue-600 focus:ring-blue-500">
                                        <span class="text-gray-700 dark:text-gray-300">True</span>
                                    </label>
                                    <label class="flex items-center space-x-3 p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                        <input type="radio" name="answers[{{ $question->id }}]" value="false" class="text-blue-600 focus:ring-blue-500">
                                        <span class="text-gray-700 dark:text-gray-300">False</span>
                                    </label>
                                </div>
                            @elseif($question->type === 'short_answer')
                                <textarea name="answers[{{ $question->id }}]" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Type your answer here..."></textarea>
                            @elseif($question->type === 'essay')
                                <textarea name="answers[{{ $question->id }}]" rows="6" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Write your essay here..."></textarea>
                            @elseif($question->type === 'fill_blank')
                                <div class="space-y-3">
                                    <p class="text-gray-700 dark:text-gray-300 mb-3">
                                        {!! str_replace('[blank]', '<input type="text" name="answers['.$question->id.'][]" class="px-2 py-1 border-b-2 border-blue-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:border-blue-700" placeholder="Answer">', $question->details['blank_question']) !!}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Submit Button -->
                <div class="fixed bottom-6 right-6">
                    <button type="submit" id="submit-btn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold shadow-lg transition-colors duration-200">
                        Submit Exam
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const examDuration = {{ $exam->duration }} * 60; // Convert to seconds
            let timeLeft = examDuration;
            const timerElement = document.getElementById('timer');
            const submitBtn = document.getElementById('submit-btn');
            const examForm = document.getElementById('exam-form');

            function updateTimer() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                if (timeLeft <= 300) { // 5 minutes warning
                    timerElement.classList.add('text-red-600', 'animate-pulse');
                }

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    alert('Time is up! Submitting your exam automatically.');
                    examForm.submit();
                }

                timeLeft--;
            }

            const timerInterval = setInterval(updateTimer, 1000);

            // Prevent accidental navigation
            window.addEventListener('beforeunload', function(e) {
                if (timeLeft > 0) {
                    e.preventDefault();
                    e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                }
            });

            // Fullscreen requirement
            @if($exam->require_fullscreen)
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.log('Fullscreen request failed:', err);
                });
            }
            @endif
        });
    </script>
@endsection
