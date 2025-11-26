@extends('layouts.app')

@section('title', 'Taking Exam - ' . $exam->title)

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Header with Timer Only -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-3">
                    <div class="flex items-center space-x-4">
                        <h1 class="text-lg font-bold text-gray-900 dark:text-white">{{ $exam->title }}</h1>
                        <span class="text-sm text-gray-500 dark:text-gray-400">•</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $exam->subject->name }}</span>
                    </div>

                    <!-- Timer -->
                    <div id="timer-container" class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Time Remaining</div>
                            <div id="exam-timer-display" class="text-xl font-mono font-bold text-green-600 dark:text-green-400">
                                00:00:00
                            </div>
                            <div id="timer-status" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Based on exam schedule
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions Container -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            @if($attempt->status === 'in_progress')
                <form id="exam-form" action="{{ route('student.exams.submit', ['exam' => $exam, 'attempt' => $attempt]) }}" method="POST">
                    @csrf

                    <!-- Questions -->
                    <div class="space-y-6">
                        @foreach($exam->questions as $index => $question)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 question-container" data-question-id="{{ $question->id }}">
                                <!-- Question Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        Question {{ $index + 1 }}
                                    </h3>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $question->pivot->points ?? $question->points }} point{{ ($question->pivot->points ?? $question->points) > 1 ? 's' : '' }}
                                    </span>
                                </div>

                                <!-- Question Text -->
                                <div class="mb-6">
                                    <p class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed whitespace-pre-wrap">
                                        {{ $question->question_text }}
                                    </p>
                                </div>

                                <!-- Answer Input -->
                                <div class="answer-container">
                                    @if($question->type === 'mcq')
                                        <div class="space-y-3">
                                            @foreach($question->options as $optionIndex => $option)
                                                <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-150">
                                                    <input type="radio"
                                                           name="answers[{{ $question->id }}]"
                                                           value="{{ $option->id }}"

                                                           class="question-answer mr-4 h-5 w-5 text-blue-600 focus:ring-blue-500"
                                                           data-question-id="{{ $question->id }}"
                                                        {{ isset($savedAnswers[$question->id]) && $savedAnswers[$question->id] == $optionIndex ? 'checked' : '' }}>
                                                    <span class="text-gray-700 dark:text-gray-300">
                                                        <span class="font-medium">{{ chr(65 + $optionIndex) }}.</span>
                                                        {{ $option->option_text }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @elseif($question->type === 'true_false')
                                        <div class="flex space-x-4">
                                            <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-150 flex-1">
                                                <input type="radio"
                                                       name="answers[{{ $question->id }}]"
                                                       value="true"
                                                       class="question-answer mr-4 h-5 w-5 text-blue-600 focus:ring-blue-500"
                                                       data-question-id="{{ $question->id }}"
                                                    {{ isset($savedAnswers[$question->id]) && $savedAnswers[$question->id] == 'true' ? 'checked' : '' }}>
                                                <span class="text-gray-700 dark:text-gray-300 font-medium">True</span>
                                            </label>
                                            <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-150 flex-1">
                                                <input type="radio"
                                                       name="answers[{{ $question->id }}]"
                                                       value="false"
                                                       class="question-answer mr-4 h-5 w-5 text-blue-600 focus:ring-blue-500"
                                                       data-question-id="{{ $question->id }}"
                                                    {{ isset($savedAnswers[$question->id]) && $savedAnswers[$question->id] == 'false' ? 'checked' : '' }}>
                                                <span class="text-gray-700 dark:text-gray-300 font-medium">False</span>
                                            </label>
                                        </div>
                                    @elseif($question->type === 'short_answer')
                                        <textarea name="answers[{{ $question->id }}]"
                                                  class="question-answer w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white resize-none"
                                                  rows="3"
                                                  placeholder="Type your answer here..."
                                                  data-question-id="{{ $question->id }}">{{ $savedAnswers[$question->id] ?? '' }}</textarea>
                                    @elseif($question->type === 'essay')
                                        <textarea name="answers[{{ $question->id }}]"
                                                  class="question-answer w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white resize-none"
                                                  rows="6"
                                                  placeholder="Write your essay answer here..."
                                                  data-question-id="{{ $question->id }}">{{ $savedAnswers[$question->id] ?? '' }}</textarea>
                                    @elseif($question->type === 'fill_blank')
                                        @php
                                            $parts = explode('[blank]', $question->question_text);
                                        @endphp
                                        <div class="space-y-3">
                                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                                @foreach($parts as $partIndex => $part)
                                                    {{ $part }}
                                                    @if($partIndex < count($parts) - 1)
                                                        <input type="text"
                                                               name="answers[{{ $question->id }}][]"
                                                               class="question-answer inline-block w-32 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white mx-1"
                                                               placeholder="_____"
                                                               value="{{ $savedAnswers[$question->id][$partIndex] ?? '' }}"
                                                               data-question-id="{{ $question->id }}"
                                                               data-blank-index="{{ $partIndex }}">
                                                    @endif
                                                @endforeach
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Auto-save Status -->
                                <div class="mt-3">
                                    <span class="answer-status text-sm text-green-600 dark:text-green-400 hidden">
                                        <i class="fas fa-check mr-1"></i>Saved
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Submit Button -->
                    <div class="fixed bottom-6 right-6">
                        <button type="button" id="submit-exam-btn"
                                class="px-8 py-4 bg-red-600 hover:bg-red-700 text-white rounded-full shadow-lg font-semibold transition-all duration-200 hover:scale-105 flex items-center space-x-2">
                            <i class="fas fa-paper-plane"></i>
                            <span>Submit Exam</span>
                        </button>
                    </div>
                </form>
            @else
                <!-- Completed Exam Message -->
                <div class="text-center py-16">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 max-w-md mx-auto">
                        <i class="fas fa-check-circle text-green-500 text-5xl mb-4"></i>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Exam Completed</h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">This exam has already been submitted.</p>
                        <a href="{{ route('student.exams.results', ['exam' => $exam, 'attempt' => $attempt]) }}"
                           class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                            View Results
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-4xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Submit Exam?</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Are you sure you want to submit? You cannot return after submitting.</p>

                <div class="flex justify-center space-x-3">
                    <button type="button" id="cancel-submit"
                            class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="button" id="confirm-submit"
                            class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200">
                        Submit Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Debug Information (Remove in production) -->
    @if(config('app.debug'))
        <div class="fixed bottom-0 left-0 bg-black bg-opacity-80 text-white p-4 text-xs max-w-md z-50">
            <div class="font-bold mb-2">Debug Info:</div>
            <div>Exam: {{ $exam->title }}</div>
            <div>Attempt: {{ $attempt->id }}</div>
            <div>Status: {{ $attempt->status }}</div>
            <div>Saved Answers: <span id="debug-answer-count">0</span></div>
            <div>Time Remaining: <span id="debug-time-remaining">0</span>s</div>
            <div>Last Save: <span id="debug-last-save">Never</span></div>
        </div>
    @endif
@endsection

<script>
    class ExamManager {
        constructor(examId, attemptId, timeData) {
            this.examId = examId;
            this.attemptId = attemptId;
            this.timeData = timeData;
            this.savedAnswers = {};
            this.isSubmitting = false;
            this.lastSaveTime = null;

            // Calculate initial time remaining from server data
            this.examEndTime = new Date(timeData.end_time).getTime();
            this.timeRemaining = this.calculateTimeRemaining();

            console.log('⏰ Timer initialized:', {
                examEndTime: new Date(this.examEndTime).toLocaleString(),
                initialTimeRemaining: this.timeRemaining,
                examDuration: timeData.duration_minutes + ' minutes'
            });

            this.init();
        }

        calculateTimeRemaining() {
            const now = Date.now();
            const remaining = Math.floor((this.examEndTime - now) / 1000);
            return Math.max(0, remaining);
        }

        init() {
            this.loadSavedProgress().then(() => {
                this.bindEvents();
                this.startTimer();
                this.updateDebugInfo();
            });
        }

        bindEvents() {
            // Auto-save on answer change
            document.addEventListener('change', (e) => {
                if (e.target.classList.contains('question-answer')) {
                    this.handleAnswerChange(e.target);
                }
            });

            document.addEventListener('input', (e) => {
                if (e.target.classList.contains('question-answer')) {
                    this.handleAnswerChange(e.target);
                }
            });

            // Submit exam
            document.getElementById('submit-exam-btn').addEventListener('click', () => {
                this.showConfirmationModal();
            });

            document.getElementById('confirm-submit').addEventListener('click', () => {
                this.submitExam();
            });

            document.getElementById('cancel-submit').addEventListener('click', () => {
                this.hideConfirmationModal();
            });

            // Warn before leaving
            window.addEventListener('beforeunload', (e) => {
                if (!this.isSubmitting && Object.keys(this.savedAnswers).length > 0) {
                    e.preventDefault();
                    e.returnValue = 'Your answers may not be saved. Are you sure you want to leave?';
                    return e.returnValue;
                }
            });

            // Handle page hide event (for mobile/tab switching)
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    console.log('📱 Tab inactive, auto-saving...');
                    this.saveProgress();
                }
            });
        }

        startTimer() {
            this.updateTimerDisplay();

            this.timerInterval = setInterval(() => {
                this.timeRemaining = this.calculateTimeRemaining();

                if (this.timeRemaining <= 0) {
                    this.handleTimeUp();
                    return;
                }

                this.updateTimerDisplay();
                this.updateDebugInfo();

                // Auto-save every 2 minutes
                if (this.timeRemaining % 120 === 0) {
                    this.saveProgress();
                }

                // Visual warnings
                if (this.timeRemaining === 300) { // 5 minutes
                    this.showTimeWarning('5 minutes remaining!');
                }
                if (this.timeRemaining === 60) { // 1 minute
                    this.showTimeWarning('1 minute remaining!');
                    this.startBlinkingTimer();
                }

                // Update timer status every 30 seconds for debugging
                if (this.timeRemaining % 30 === 0) {
                    this.updateTimerStatus();
                }
            }, 1000);
        }

        updateTimerDisplay() {
            const hours = Math.floor(this.timeRemaining / 3600);
            const minutes = Math.floor((this.timeRemaining % 3600) / 60);
            const seconds = this.timeRemaining % 60;

            const display = document.getElementById('exam-timer-display');
            display.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            // Color changes based on time
            if (this.timeRemaining > 300) { // More than 5 minutes
                display.className = 'text-xl font-mono font-bold text-green-600 dark:text-green-400';
            } else if (this.timeRemaining > 60) { // 1-5 minutes
                display.className = 'text-xl font-mono font-bold text-orange-500 dark:text-orange-400';
            } else { // Less than 1 minute
                display.className = 'text-xl font-mono font-bold text-red-600 dark:text-red-400 animate-pulse';
            }
        }

        updateTimerStatus() {
            const statusElement = document.getElementById('timer-status');
            if (statusElement) {
                const endTime = new Date(this.examEndTime).toLocaleTimeString();
                statusElement.textContent = `Exam ends at ${endTime}`;
            }
        }

        startBlinkingTimer() {
            const display = document.getElementById('exam-timer-display');
            const blinkInterval = setInterval(() => {
                display.classList.toggle('opacity-50');
            }, 500);

            // Stop blinking when time is up
            setTimeout(() => {
                clearInterval(blinkInterval);
                display.classList.remove('opacity-50');
            }, 60000);
        }

        showTimeWarning(message) {
            // Create a temporary notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-20 right-6 bg-red-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            notification.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 5000);
        }

        handleAnswerChange(element) {
            const questionId = element.dataset.questionId;
            this.showSavingStatus(questionId);
            this.debouncedSave();
        }

        showSavingStatus(questionId) {
            const container = document.querySelector(`[data-question-id="${questionId}"]`);
            const statusElement = container.querySelector('.answer-status');

            statusElement.classList.remove('hidden');
            setTimeout(() => {
                statusElement.classList.add('hidden');
            }, 1500);
        }

        collectAnswers() {
            this.savedAnswers = {};

            document.querySelectorAll('.question-container').forEach(container => {
                const questionId = container.dataset.questionId;
                if (!questionId) return;

                // Radios (MCQ, true/false) - use querySelectorAll to inspect group
                const radios = container.querySelectorAll('input[type="radio"].question-answer');
                if (radios && radios.length > 0) {
                    const selected = container.querySelector('input[type="radio"].question-answer:checked');
                    if (selected) {
                        // Save the raw value (we expect option.id for MCQ, 'true'/'false' for TF)
                        this.savedAnswers[questionId] = selected.value;
                    }
                    return; // move to next container
                }

                // Textareas (short_answer, essay)
                const textarea = container.querySelector('textarea.question-answer');
                if (textarea) {
                    this.savedAnswers[questionId] = textarea.value.trim();
                    return;
                }

                // Fill-in-the-blank (multiple inputs type="text")
                const textInputs = container.querySelectorAll('input[type="text"].question-answer');
                if (textInputs && textInputs.length > 0) {
                    const arr = Array.from(textInputs).map(i => i.value.trim());
                    this.savedAnswers[questionId] = arr;
                    return;
                }

                // Fallback: single text input (if any)
                const singleText = container.querySelector('input[type="text"].question-answer:not([data-blank-index])');
                if (singleText) {
                    this.savedAnswers[questionId] = singleText.value.trim();
                }
            });

            console.log('📝 Collected answers:', this.savedAnswers);
            return this.savedAnswers;
        }


        async saveProgress() {
            const answers = this.collectAnswers();

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('answers', JSON.stringify(answers));

            try {
                const response = await fetch('{{ route("student.exams.save-progress", ["exam" => $exam, "attempt" => $attempt]) }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const result = await response.json();
                if (result.success) {
                    this.lastSaveTime = new Date();
                    this.updateDebugInfo();
                    console.log('💾 Progress saved:', result.saved_count, 'answers');
                } else {
                    console.error('❌ Save failed:', result.message);
                }
            } catch (error) {
                console.error('💥 Save failed:', error);
            }
        }

        async loadSavedProgress() {
            try {
                const response = await fetch('{{ route("student.exams.get-progress", ["exam" => $exam, "attempt" => $attempt]) }}');
                const result = await response.json();

                if (result.success && result.answers) {
                    this.savedAnswers = result.answers;
                    this.populateSavedAnswers();
                    console.log('📥 Loaded saved progress:', Object.keys(this.savedAnswers).length, 'answers');
                }
            } catch (error) {
                console.error('💥 Load progress failed:', error);
            }
        }

        populateSavedAnswers() {
            Object.entries(this.savedAnswers).forEach(([questionId, answer]) => {
                const container = document.querySelector(`[data-question-id="${questionId}"]`);
                if (!container) return;

                if (Array.isArray(answer)) {
                    // Fill in the blank answers
                    answer.forEach((value, index) => {
                        const input = container.querySelector(`input[data-blank-index="${index}"]`);
                        if (input) input.value = value;
                    });
                    return;
                }

                // Try to restore radio first (MCQ / true_false)
                // We search by value matching (strings)
                if (answer !== null && answer !== undefined) {
                    // radio value might be numeric (option id) or text like 'true'/'false'
                    const radio = container.querySelector(`input[type="radio"].question-answer[value="${answer}"]`);
                    if (radio) {
                        radio.checked = true;
                        return;
                    }

                    // Try radio with numeric cast (some browsers store numbers differently)
                    const radioNumeric = container.querySelector(`input[type="radio"].question-answer[value="${Number(answer)}"]`);
                    if (radioNumeric) {
                        radioNumeric.checked = true;
                        return;
                    }
                }

                // If not radio, try textarea
                const textarea = container.querySelector('textarea.question-answer');
                if (textarea && typeof answer === 'string') {
                    textarea.value = answer;
                    return;
                }

                // If it's a single text input (fallback)
                const singleText = container.querySelector('input[type="text"].question-answer:not([data-blank-index])');
                if (singleText && typeof answer === 'string') {
                    singleText.value = answer;
                    return;
                }
            });
        }


        addAnswersToForm() {
            const form = document.getElementById('exam-form');
            if (!form) return;

            // Remove any existing hidden answer fields we previously injected
            document.querySelectorAll('input[name^="answers["][type="hidden"]').forEach(input => input.remove());

            // Add all answers as hidden fields
            Object.entries(this.savedAnswers).forEach(([questionId, answer]) => {
                // if array, store as JSON string so server can decode (answers[Q] => JSON)
                if (Array.isArray(answer)) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `answers[${questionId}]`;
                    hiddenInput.value = JSON.stringify(answer);
                    form.appendChild(hiddenInput);
                    return;
                }

                // Non-array: add single hidden input
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = `answers[${questionId}]`;
                hiddenInput.value = (answer === null || answer === undefined) ? '' : answer;
                form.appendChild(hiddenInput);
            });

            console.log('📤 Added answers to form for submission:', this.savedAnswers);
        }

        showConfirmationModal() {
            document.getElementById('confirmation-modal').classList.remove('hidden');
        }

        hideConfirmationModal() {
            document.getElementById('confirmation-modal').classList.add('hidden');
        }

        async submitExam() {
            if (this.isSubmitting) return;

            this.isSubmitting = true;
            const submitBtn = document.getElementById('submit-exam-btn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Submitting...</span>';

            try {
                // Final save
                await this.saveProgress();

                // Add answers to form for submission
                this.addAnswersToForm();

                console.log('🚀 Submitting exam with answers:', this.savedAnswers);

                // Submit form
                document.getElementById('exam-form').submit();

            } catch (error) {
                console.error('❌ Submission failed:', error);
                this.isSubmitting = false;
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i><span>Submit Exam</span>';

                alert('Submission failed. Please try again.');
            }
        }

        handleTimeUp() {
            clearInterval(this.timerInterval);
            console.log('⏰ Time up! Auto-submitting...');
            this.autoSubmitExam();
        }

        async autoSubmitExam() {
            await this.saveProgress();
            this.addAnswersToForm();
            document.getElementById('exam-form').submit();
        }

        updateDebugInfo() {
            if (!document.getElementById('debug-answer-count')) return;

            document.getElementById('debug-answer-count').textContent =
                Object.keys(this.savedAnswers).length;
            document.getElementById('debug-time-remaining').textContent =
                this.timeRemaining;

            if (this.lastSaveTime) {
                document.getElementById('debug-last-save').textContent =
                    this.lastSaveTime.toLocaleTimeString();
            }
        }

        debouncedSave() {
            clearTimeout(this.saveTimeout);
            this.saveTimeout = setTimeout(() => {
                this.saveProgress();
            }, 1000);
        }
    }

    // Initialize exam
    document.addEventListener('DOMContentLoaded', function() {
        @if($attempt->status === 'in_progress')
        const examId = '{{ $exam->id }}';
        const attemptId = '{{ $attempt->id }}';
        const timeData = @json($timeData);

        console.log('🚀 Starting exam with time data:', timeData);
        window.examManager = new ExamManager(examId, attemptId, timeData);
        @endif
    });
</script>
