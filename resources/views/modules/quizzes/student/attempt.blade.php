<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $quiz->title }} - Lincoln eLearning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .question-number {
            width: 40px;
            height: 40px;
        }
        .timer-warning {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
<!-- Quiz Header -->
<header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $quiz->title }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $quiz->subject->name }}</p>
            </div>

            <!-- Timer -->
            <div id="timer" class="text-right">
                <div class="text-2xl font-bold text-gray-900 dark:text-white" id="time-display">
                    {{ sprintf('%02d:%02d', floor($timeRemaining), $timeRemaining % 60) }}
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Time Remaining</p>
            </div>
        </div>
    </div>
</header>

<!-- Progress Bar -->
<div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-2">
            <div class="flex justify-between items-center text-sm text-gray-600 dark:text-gray-400 mb-1">
                <span>Progress</span>
                <span id="progress-text">0/{{ $questions->count() }}</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Questions Navigation -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sticky top-4">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Questions</h3>
                <div class="grid grid-cols-5 lg:grid-cols-3 gap-2 max-h-96 overflow-y-auto">
                    @foreach($questions as $index => $question)
                        <button type="button"
                                onclick="showQuestion({{ $index }})"
                                class="question-nav-btn aspect-square rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium transition-colors duration-200"
                                data-question="{{ $index }}"
                                id="nav-{{ $index }}">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button"
                            onclick="submitQuiz()"
                            class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Quiz
                    </button>

                    <button type="button"
                            onclick="showExitConfirm()"
                            class="w-full mt-2 bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Exit Quiz
                    </button>
                </div>
            </div>
        </div>

        <!-- Question Area -->
        <div class="lg:col-span-3">
            <form id="quiz-form" action="{{ route('student.quizzes.submit', $attempt) }}" method="POST">
                @csrf

                @foreach($questions as $index => $question)
                    <div class="question-section bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6 {{ $index === 0 ? '' : 'hidden' }}"
                         id="question-{{ $index }}">

                        <!-- Question Header -->
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex items-center space-x-4">
                                <div class="question-number flex items-center justify-center bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full font-semibold">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400 capitalize">
                                        {{ str_replace('_', ' ', $question->type) }}
                                    </span>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $question->pivot->points ?? $question->points }} points
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Question Text -->
                        <div class="mb-6">
                            <p class="text-lg text-gray-900 dark:text-white leading-relaxed">{{ $question->question_text }}</p>
                        </div>

                        <!-- Answer Options -->
                        <div class="space-y-3">
                            @if($question->type === 'mcq')
                                @foreach($question->options as $option)
                                    <div class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <input type="radio"
                                               name="answers[{{ $question->id }}]"
                                               value="{{ $option->id }}"
                                               id="option-{{ $option->id }}"
                                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                               onchange="saveAnswer({{ $question->id }}, {{ $option->id }})">
                                        <label for="option-{{ $option->id }}" class="ml-3 text-gray-900 dark:text-white cursor-pointer flex-1">
                                            {{ $option->option_text }}
                                        </label>
                                    </div>
                                @endforeach

                            @elseif($question->type === 'true_false')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <input type="radio"
                                               name="answers[{{ $question->id }}]"
                                               value="true"
                                               id="true-{{ $question->id }}"
                                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                               onchange="saveAnswer({{ $question->id }}, 'true')">
                                        <label for="true-{{ $question->id }}" class="ml-3 text-gray-900 dark:text-white cursor-pointer flex-1">
                                            True
                                        </label>
                                    </div>
                                    <div class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <input type="radio"
                                               name="answers[{{ $question->id }}]"
                                               value="false"
                                               id="false-{{ $question->id }}"
                                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                               onchange="saveAnswer({{ $question->id }}, 'false')">
                                        <label for="false-{{ $question->id }}" class="ml-3 text-gray-900 dark:text-white cursor-pointer flex-1">
                                            False
                                        </label>
                                    </div>
                                </div>

                            @elseif($question->type === 'short_answer')
                                <div>
                                    <textarea name="answers[{{ $question->id }}]"
                                              id="answer-{{ $question->id }}"
                                              rows="4"
                                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                              placeholder="Type your answer here..."
                                              onblur="saveAnswer({{ $question->id }}, this.value)"></textarea>
                                </div>

                            @elseif($question->type === 'essay')
                                <div>
                                    <textarea name="answers[{{ $question->id }}]"
                                              id="answer-{{ $question->id }}"
                                              rows="6"
                                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                              placeholder="Write your essay answer here..."
                                              onblur="saveAnswer({{ $question->id }}, this.value)"></textarea>
                                </div>
                            @endif
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="button"
                                    onclick="showPreviousQuestion({{ $index }})"
                                    class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 {{ $index === 0 ? 'invisible' : '' }}">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Previous
                            </button>

                            <button type="button"
                                    onclick="showNextQuestion({{ $index }})"
                                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                                Next
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </form>
        </div>
    </div>
</div>

<!-- Exit Confirmation Modal -->
<div id="exit-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900">
                <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-3">Exit Quiz?</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Your progress will be saved and you can return later. Are you sure you want to exit?
                </p>
            </div>
            <div class="flex justify-center space-x-3 mt-6">
                <button onclick="hideExitConfirm()"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-400 dark:hover:bg-gray-500">
                    Cancel
                </button>
                <a href="{{ route('student.quizzes.index') }}"
                   class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700">
                    Exit Quiz
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Quiz state
    let currentQuestion = 0;
    const totalQuestions = {{ $questions->count() }};
    let answeredQuestions = new Set();
    let timeRemaining = {{ $timeRemaining }} * 60; // Convert to seconds
    let timerInterval;

    // Initialize quiz
    document.addEventListener('DOMContentLoaded', function() {
        startTimer();
        updateProgress();
        loadSavedAnswers();
    });

    // Timer functions
    function startTimer() {
        timerInterval = setInterval(function() {
            timeRemaining--;

            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                autoSubmit();
                return;
            }

            updateTimerDisplay();

            // Warning when 5 minutes left
            if (timeRemaining === 300) {
                showTimeWarning();
            }
        }, 1000);
    }

    function updateTimerDisplay() {
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        document.getElementById('time-display').textContent =
            `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

        // Add warning class when time is low
        const timerElement = document.getElementById('timer');
        if (timeRemaining <= 300) {
            timerElement.classList.add('timer-warning');
            document.getElementById('time-display').classList.add('text-red-600', 'dark:text-red-400');
        }
    }

    function showTimeWarning() {
        // You can implement a more prominent warning here
        console.log('5 minutes remaining!');
    }

    // Question navigation
    function showQuestion(index) {
        document.querySelectorAll('.question-section').forEach(section => {
            section.classList.add('hidden');
        });
        document.getElementById(`question-${index}`).classList.remove('hidden');
        currentQuestion = index;
    }

    function showNextQuestion(currentIndex) {
        if (currentIndex < totalQuestions - 1) {
            showQuestion(currentIndex + 1);
        }
    }

    function showPreviousQuestion(currentIndex) {
        if (currentIndex > 0) {
            showQuestion(currentIndex - 1);
        }
    }

    // Answer handling
    function saveAnswer(questionId, answer) {
        fetch('{{ route("student.quizzes.save-answer", $attempt) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                question_id: questionId,
                answer: answer
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    answeredQuestions.add(questionId);
                    updateProgress();
                    updateQuestionNav(questionId);
                }
            })
            .catch(error => console.error('Error saving answer:', error));
    }

    function loadSavedAnswers() {
        // This would typically load from server, but for now we'll just initialize
        // In a real implementation, you'd fetch the saved answers from the server
    }

    // Progress tracking
    function updateProgress() {
        const progress = (answeredQuestions.size / totalQuestions) * 100;
        document.getElementById('progress-bar').style.width = `${progress}%`;
        document.getElementById('progress-text').textContent =
            `${answeredQuestions.size}/${totalQuestions}`;
    }

    function updateQuestionNav(questionId) {
        // Find the question index and update its navigation button
        const questions = @json($questions);
        const questionIndex = questions.findIndex(q => q.id === questionId);
        if (questionIndex !== -1) {
            const navBtn = document.getElementById(`nav-${questionIndex}`);
            navBtn.classList.add('bg-green-100', 'dark:bg-green-900', 'text-green-800', 'dark:text-green-200', 'border-green-300', 'dark:border-green-700');
        }
    }

    // Quiz submission
    function submitQuiz() {
        if (confirm('Are you sure you want to submit your quiz? You cannot change your answers after submission.')) {
            document.getElementById('quiz-form').submit();
        }
    }

    function autoSubmit() {
        if (confirm('Time is up! Your quiz will be automatically submitted.')) {
            document.getElementById('quiz-form').submit();
        } else {
            document.getElementById('quiz-form').submit();
        }
    }

    // Exit modal
    function showExitConfirm() {
        document.getElementById('exit-modal').classList.remove('hidden');
    }

    function hideExitConfirm() {
        document.getElementById('exit-modal').classList.add('hidden');
    }

    // Prevent accidental navigation
    window.addEventListener('beforeunload', function (e) {
        if (answeredQuestions.size > 0 && answeredQuestions.size < totalQuestions) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
</script>
</body>
</html>
