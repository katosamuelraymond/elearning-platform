@extends('layouts.app')

@section('title', 'Take Exam - ' . $exam->title)

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $exam->title }}</h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-2">{{ $exam->description }}</p>
                    </div>
                    <div class="text-right">
                        <!-- Timer Display -->
                        <div id="timer-container" class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 min-w-48">
                            <div id="countdown-timer" class="hidden">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Exam starts in:</p>
                                <div id="countdown-display" class="text-2xl font-bold text-blue-600 dark:text-blue-400"></div>
                            </div>
                            <div id="exam-timer" class="hidden">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Time remaining:</p>
                                <div id="exam-timer-display" class="text-2xl font-bold text-red-600 dark:text-red-400"></div>
                            </div>
                            <div id="exam-completed" class="hidden">
                                <p class="text-sm text-green-600 dark:text-green-400 font-semibold">Exam Completed</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exam Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-blue-600 dark:text-blue-400 mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Duration</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $exam->duration }} minutes</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <i class="fas fa-file-alt text-green-600 dark:text-green-400 mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Questions</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $exam->questions->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <i class="fas fa-star text-yellow-600 dark:text-yellow-400 mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Marks</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $exam->total_marks }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <i class="fas fa-book text-purple-600 dark:text-purple-400 mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Subject</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $exam->subject->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exam Instructions -->
            <div id="exam-instructions" class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-4">Exam Instructions</h3>
                <ul class="space-y-2 text-blue-700 dark:text-blue-400">
                    <li class="flex items-start">
                        <i class="fas fa-info-circle mt-1 mr-2"></i>
                        <span>You have <strong>{{ $exam->duration }} minutes</strong> to complete this exam</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-save mt-1 mr-2"></i>
                        <span>Answers are saved automatically as you progress</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-clock mt-1 mr-2"></i>
                        <span>The exam will automatically submit when time expires</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-ban mt-1 mr-2"></i>
                        <span>Do not refresh the page or navigate away during the exam</span>
                    </li>
                </ul>
            </div>

            <!-- Exam Content (Hidden until start) -->
            <div id="exam-content" class="hidden">
                <form id="exam-form" action="{{ route('student.exams.submit', $exam) }}" method="POST">
                    @csrf
                    <input type="hidden" name="attempt_id" value="{{ $attempt->id }}">

                    <!-- Progress Bar -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progress</span>
                            <span id="progress-text" class="text-sm font-medium text-gray-700 dark:text-gray-300">0/{{ $exam->questions->count() }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div id="progress-bar" class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Questions -->
                    <div class="space-y-6">
                        @foreach($exam->questions as $index => $question)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 question-container" data-question-id="{{ $question->id }}">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        Question {{ $index + 1 }}
                                        <span class="text-sm text-gray-500 dark:text-gray-400">({{ $question->marks }} marks)</span>
                                    </h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300">
                                    {{ ucfirst($question->type) }}
                                </span>
                                </div>

                                <div class="mb-4">
                                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{!! $question->content !!}</p>
                                </div>

                                <!-- Answer Input -->
                                <div class="answer-container">
                                    @if($question->type === 'multiple_choice')
                                        <div class="space-y-2">
                                            @foreach($question->options as $optionKey => $optionValue)
                                                <label class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $optionKey }}"
                                                           class="question-answer mr-3" data-question-id="{{ $question->id }}">
                                                    <span class="text-gray-700 dark:text-gray-300">{{ $optionValue }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @elseif($question->type === 'true_false')
                                        <div class="space-y-2">
                                            <label class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                                <input type="radio" name="answers[{{ $question->id }}]" value="true"
                                                       class="question-answer mr-3" data-question-id="{{ $question->id }}">
                                                <span class="text-gray-700 dark:text-gray-300">True</span>
                                            </label>
                                            <label class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                                <input type="radio" name="answers[{{ $question->id }}]" value="false"
                                                       class="question-answer mr-3" data-question-id="{{ $question->id }}">
                                                <span class="text-gray-700 dark:text-gray-300">False</span>
                                            </label>
                                        </div>
                                    @else
                                        <textarea name="answers[{{ $question->id }}]"
                                                  class="question-answer w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                                  rows="4"
                                                  placeholder="Type your answer here..."
                                                  data-question-id="{{ $question->id }}"></textarea>
                                    @endif
                                </div>

                                <!-- Answer Status -->
                                <div class="mt-3">
                                <span class="answer-status text-sm text-gray-500 dark:text-gray-400 hidden">
                                    <i class="fas fa-check-circle text-green-500 mr-1"></i>
                                    <span>Saved</span>
                                </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Action Buttons -->
                    <div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 shadow-lg">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <span id="saved-count">0</span> of {{ $exam->questions->count() }} answers saved
                                </div>
                                <div class="flex space-x-3">
                                    <button type="button" id="save-progress-btn"
                                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                                        Save Progress
                                    </button>
                                    <button type="button" id="submit-exam-btn"
                                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200">
                                        Submit Exam
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Start Exam Button -->
            <div id="start-exam-container" class="text-center py-8">
                <button id="start-exam-btn"
                        class="px-8 py-4 bg-green-600 hover:bg-green-700 text-white text-lg font-semibold rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    Start Exam
                </button>
                <p class="text-gray-500 dark:text-gray-400 mt-4" id="start-exam-message"></p>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Submit Exam</h3>
            <p class="text-gray-600 dark:text-gray-300 mb-6">Are you sure you want to submit your exam? This action cannot be undone.</p>
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancel-submit"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium">
                    Cancel
                </button>
                <button type="button" id="confirm-submit"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                    Submit Exam
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        class ExamTimer {
            constructor(examStartTime, examDuration, attemptId) {
                this.examStartTime = new Date(examStartTime).getTime();
                this.examDuration = examDuration * 60 * 1000; // Convert to milliseconds
                this.attemptId = attemptId;
                this.examStarted = false;
                this.examEnded = false;
                this.interval = null;

                this.init();
            }

            init() {
                const now = Date.now();
                const timeUntilStart = this.examStartTime - now;

                if (timeUntilStart > 0) {
                    // Exam hasn't started yet
                    this.showCountdownTimer();
                    this.startCountdown();
                } else {
                    // Exam has started or should start
                    const elapsedTime = now - this.examStartTime;
                    const remainingTime = this.examDuration - elapsedTime;

                    if (remainingTime > 0) {
                        this.startExamTimer(remainingTime);
                    } else {
                        this.handleTimeUp();
                    }
                }
            }

            showCountdownTimer() {
                document.getElementById('countdown-timer').classList.remove('hidden');
                document.getElementById('exam-instructions').classList.remove('hidden');
                document.getElementById('start-exam-container').classList.add('hidden');
                document.getElementById('start-exam-btn').disabled = true;

                const message = document.getElementById('start-exam-message');
                message.textContent = 'Please wait for the exam to start...';
                message.classList.add('text-yellow-600', 'dark:text-yellow-400');
            }

            showExamTimer() {
                document.getElementById('countdown-timer').classList.add('hidden');
                document.getElementById('exam-timer').classList.remove('hidden');
                document.getElementById('exam-instructions').classList.add('hidden');
                document.getElementById('exam-content').classList.remove('hidden');
                document.getElementById('start-exam-container').classList.add('hidden');
            }

            startCountdown() {
                this.interval = setInterval(() => {
                    const now = Date.now();
                    const timeUntilStart = this.examStartTime - now;

                    if (timeUntilStart <= 0) {
                        clearInterval(this.interval);
                        this.startExamTimer(this.examDuration);
                        return;
                    }

                    this.updateCountdownDisplay(timeUntilStart);
                }, 1000);
            }

            updateCountdownDisplay(milliseconds) {
                const hours = Math.floor(milliseconds / (1000 * 60 * 60));
                const minutes = Math.floor((milliseconds % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((milliseconds % (1000 * 60)) / 1000);

                const display = document.getElementById('countdown-display');
                display.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }

            startExamTimer(remainingTime) {
                this.examStarted = true;
                this.showExamTimer();

                // Enable start button if we're in the exam window
                document.getElementById('start-exam-btn').disabled = false;
                document.getElementById('start-exam-message').textContent = 'Click "Start Exam" to begin';

                // Start the exam timer
                this.interval = setInterval(() => {
                    remainingTime -= 1000;

                    if (remainingTime <= 0) {
                        this.handleTimeUp();
                        return;
                    }

                    this.updateExamTimerDisplay(remainingTime);

                    // Auto-save every 30 seconds
                    if (Math.floor(remainingTime / 1000) % 30 === 0) {
                        this.autoSaveProgress();
                    }

                    // Warning when 5 minutes remaining
                    if (remainingTime === 5 * 60 * 1000) {
                        this.showTimeWarning();
                    }
                }, 1000);
            }

            updateExamTimerDisplay(milliseconds) {
                const hours = Math.floor(milliseconds / (1000 * 60 * 60));
                const minutes = Math.floor((milliseconds % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((milliseconds % (1000 * 60)) / 1000);

                const display = document.getElementById('exam-timer-display');
                display.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                // Change color when time is running out
                if (milliseconds < 5 * 60 * 1000) { // Less than 5 minutes
                    display.classList.remove('text-red-600', 'dark:text-red-400');
                    display.classList.add('text-orange-500', 'dark:text-orange-400');
                }
            }

            showTimeWarning() {
                // Create and show a warning notification
                const warning = document.createElement('div');
                warning.className = 'fixed top-4 right-4 bg-orange-500 text-white p-4 rounded-lg shadow-lg z-50';
                warning.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span>Only 5 minutes remaining!</span>
            </div>
        `;
                document.body.appendChild(warning);

                setTimeout(() => {
                    warning.remove();
                }, 5000);
            }

            async autoSaveProgress() {
                if (!this.examStarted || this.examEnded) return;

                try {
                    await window.examManager.saveProgress();
                } catch (error) {
                    console.error('Auto-save failed:', error);
                }
            }

            handleTimeUp() {
                clearInterval(this.interval);
                this.examEnded = true;

                document.getElementById('exam-timer').classList.add('hidden');
                document.getElementById('exam-completed').classList.remove('hidden');

                // Auto-submit the exam
                this.autoSubmitExam();
            }

            async autoSubmitExam() {
                try {
                    // Save final progress
                    await window.examManager.saveProgress();

                    // Submit the exam
                    document.getElementById('exam-form').submit();
                } catch (error) {
                    console.error('Auto-submit failed:', error);
                    // Force submit even if save fails
                    document.getElementById('exam-form').submit();
                }
            }

            stop() {
                if (this.interval) {
                    clearInterval(this.interval);
                }
            }
        }

        class ExamManager {
            constructor(examId, attemptId) {
                this.examId = examId;
                this.attemptId = attemptId;
                this.savedAnswers = new Set();
                this.isSubmitting = false;

                this.init();
            }

            init() {
                this.bindEvents();
                this.loadSavedProgress();
            }

            bindEvents() {
                // Start exam button
                document.getElementById('start-exam-btn').addEventListener('click', () => {
                    this.startExam();
                });

                // Answer change events
                document.querySelectorAll('.question-answer').forEach(element => {
                    if (element.type === 'radio' || element.type === 'checkbox') {
                        element.addEventListener('change', (e) => this.handleAnswerChange(e.target));
                    } else {
                        element.addEventListener('input', (e) => this.handleAnswerChange(e.target));
                    }
                });

                // Save progress button
                document.getElementById('save-progress-btn').addEventListener('click', () => {
                    this.saveProgress();
                });

                // Submit exam button
                document.getElementById('submit-exam-btn').addEventListener('click', () => {
                    this.showConfirmationModal();
                });

                // Confirmation modal
                document.getElementById('confirm-submit').addEventListener('click', () => {
                    this.submitExam();
                });

                document.getElementById('cancel-submit').addEventListener('click', () => {
                    this.hideConfirmationModal();
                });

                // Prevent form submission on enter key
                document.getElementById('exam-form').addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                        e.preventDefault();
                    }
                });

                // Warn before leaving
                window.addEventListener('beforeunload', (e) => {
                    if (this.savedAnswers.size > 0 && !this.isSubmitting) {
                        e.preventDefault();
                        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                    }
                });
            }

            startExam() {
                document.getElementById('exam-content').classList.remove('hidden');
                document.getElementById('start-exam-container').classList.add('hidden');
                document.getElementById('exam-instructions').classList.add('hidden');

                // Start the exam timer if not already started
                if (window.examTimer && !window.examTimer.examStarted) {
                    window.examTimer.startExamTimer(window.examTimer.examDuration);
                }
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
                }, 2000);
            }

            async saveProgress() {
                const formData = new FormData();
                const answers = this.collectAnswers();

                formData.append('_token', '{{ csrf_token() }}');
                formData.append('attempt_id', this.attemptId);
                formData.append('answers', JSON.stringify(answers));

                try {
                    const response = await fetch('{{ route("student.exams.save-progress", $exam) }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.updateProgress(answers);
                        this.savedAnswers = new Set(Object.keys(answers));
                        this.updateSavedCount();
                    }
                } catch (error) {
                    console.error('Save failed:', error);
                }
            }

            collectAnswers() {
                const answers = {};

                document.querySelectorAll('.question-answer').forEach(element => {
                    const questionId = element.dataset.questionId;

                    if (element.type === 'radio') {
                        if (element.checked) {
                            answers[questionId] = element.value;
                        }
                    } else if (element.type === 'checkbox') {
                        if (!answers[questionId]) {
                            answers[questionId] = [];
                        }
                        if (element.checked) {
                            answers[questionId].push(element.value);
                        }
                    } else {
                        answers[questionId] = element.value;
                    }
                });

                return answers;
            }

            updateProgress(answers) {
                const answeredCount = Object.keys(answers).length;
                const totalQuestions = {{ $exam->questions->count() }};
                const percentage = (answeredCount / totalQuestions) * 100;

                document.getElementById('progress-text').textContent = `${answeredCount}/${totalQuestions}`;
                document.getElementById('progress-bar').style.width = `${percentage}%`;
            }

            updateSavedCount() {
                document.getElementById('saved-count').textContent = this.savedAnswers.size;
            }

            async loadSavedProgress() {
                try {
                    const response = await fetch(`/student/exams/{{ $exam->id }}/attempt/${this.attemptId}/progress`);
                    const result = await response.json();

                    if (result.success && result.answers) {
                        this.populateAnswers(result.answers);
                        this.updateProgress(result.answers);
                        this.savedAnswers = new Set(Object.keys(result.answers));
                        this.updateSavedCount();
                    }
                } catch (error) {
                    console.error('Load progress failed:', error);
                }
            }

            populateAnswers(answers) {
                Object.entries(answers).forEach(([questionId, answer]) => {
                    const container = document.querySelector(`[data-question-id="${questionId}"]`);
                    if (!container) return;

                    if (Array.isArray(answer)) {
                        // Checkbox answers
                        answer.forEach(value => {
                            const input = container.querySelector(`input[type="checkbox"][value="${value}"]`);
                            if (input) input.checked = true;
                        });
                    } else {
                        const input = container.querySelector(`input[type="radio"][value="${answer}"]`);
                        if (input) {
                            input.checked = true;
                        } else {
                            const textarea = container.querySelector('textarea');
                            if (textarea) textarea.value = answer;
                        }
                    }
                });
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
                document.getElementById('submit-exam-btn').disabled = true;
                document.getElementById('submit-exam-btn').textContent = 'Submitting...';

                // Save final progress
                await this.saveProgress();

                // Submit the form
                document.getElementById('exam-form').submit();
            }

            debouncedSave() {
                clearTimeout(this.saveTimeout);
                this.saveTimeout = setTimeout(() => {
                    this.saveProgress();
                }, 1000);
            }
        }

        // Initialize the exam system
        document.addEventListener('DOMContentLoaded', function() {
            const examStartTime = '{{ $exam->start_time }}';
            const examDuration = {{ $exam->duration }};
            const attemptId = '{{ $attempt->id }}';
            const examId = '{{ $exam->id }}';

            // Initialize timer
            window.examTimer = new ExamTimer(examStartTime, examDuration, attemptId);

            // Initialize exam manager
            window.examManager = new ExamManager(examId, attemptId);
        });
    </script>
@endsection
