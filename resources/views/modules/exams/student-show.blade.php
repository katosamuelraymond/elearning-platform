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
                                <span class="text-gray-900 dark:text-white font-medium">{{ $exam->passing_marks ?? 'N/A' }}</span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Start Time</span>
                                <span class="text-gray-900 dark:text-white font-medium">
                                    {{ $exam->start_time->format('M j, Y g:i A') }}
                                    <div id="start-time-countdown" class="text-sm text-blue-600 dark:text-blue-400 mt-1"></div>
                                </span>
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

                        <!-- Current Attempt Status -->
                        @if($currentAttempt)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Current Attempt</h3>
                                <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-blue-800 dark:text-blue-200 font-medium">
                                                Attempt started {{ $currentAttempt->created_at->diffForHumans() }}
                                            </p>
                                            <p class="text-blue-700 dark:text-blue-300 text-sm mt-1">
                                                Status: <span class="font-semibold capitalize">{{ $currentAttempt->status }}</span>
                                            </p>
                                        </div>
                                        <a href="{{ route('student.exams.take', ['exam' => $exam, 'attempt' => $currentAttempt]) }}"
                                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                            Continue Exam
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 sticky top-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Exam Actions</h3>

                        <!-- Real-time Status Display -->
                        <div id="exam-status-container" class="mb-4">
                            @if($canTakeExam)
                                <form action="{{ route('student.exams.start', $exam) }}" method="POST" class="mb-4">
                                    @csrf
                                    <button type="submit"
                                            class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-medium text-center block transition-colors duration-200">
                                        @if($currentAttempt)
                                            Resume Exam
                                        @else
                                            Start Exam
                                        @endif
                                    </button>
                                </form>

                                <!-- Countdown to Start -->
                                <div id="countdown-to-start" class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-3 text-center hidden">
                                    <p class="text-yellow-800 dark:text-yellow-200 text-sm font-medium mb-1">Exam starts in:</p>
                                    <div id="countdown-display" class="text-lg font-bold text-yellow-700 dark:text-yellow-300"></div>
                                </div>
                            @else
                                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4 text-center mb-4">
                                    <p id="exam-status-message" class="text-gray-600 dark:text-gray-300 text-sm">
                                        @if(now()->lt($exam->start_time))
                                            <i class="fas fa-clock mr-2"></i>
                                            Exam starts soon
                                        @elseif(now()->gt($exam->end_time))
                                            <i class="fas fa-ban mr-2"></i>
                                            Exam has ended
                                        @elseif($attempts->count() >= $exam->max_attempts)
                                            <i class="fas fa-times-circle mr-2"></i>
                                            Maximum attempts reached
                                        @else
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Exam not available
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Quick Stats -->
                        <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4 mb-4">
                            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Your Progress</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-blue-700 dark:text-blue-300">Attempts Used:</span>
                                    <span class="text-blue-800 dark:text-blue-100 font-medium">
                                        {{ $attempts->count() }} / {{ $exam->max_attempts }}
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-blue-700 dark:text-blue-300">Best Score:</span>
                                    <span class="text-blue-800 dark:text-blue-100 font-medium">
                                        {{ $bestScore ?? 0 }} / {{ $exam->total_marks }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Attempt History -->
                        @if($attempts->isNotEmpty())
                            <div class="mt-6">
                                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">Attempt History</h4>
                                <div class="space-y-3 max-h-64 overflow-y-auto">
                                    @foreach($attempts as $attempt)
                                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    Attempt {{ $loop->iteration }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $attempt->created_at->format('M j, g:i A') }}
                                                </p>
                                                @if($attempt->status === 'submitted' || $attempt->status === 'graded')
                                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                                        Score: {{ $attempt->total_score ?? 0 }}/{{ $exam->total_marks }}
                                                    </p>
                                                @endif
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

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const examStartTime = new Date('{{ $exam->start_time }}').getTime();
            const examEndTime = new Date('{{ $exam->end_time }}').getTime();
            const now = Date.now();

            const countdownContainer = document.getElementById('countdown-to-start');
            const countdownDisplay = document.getElementById('countdown-display');
            const startButton = document.querySelector('button[type="submit"]');
            const examStatusMessage = document.getElementById('exam-status-message');
            const startTimeCountdown = document.getElementById('start-time-countdown');

            function updateCountdown() {
                const now = Date.now();
                const timeUntilStart = examStartTime - now;
                const timeUntilEnd = examEndTime - now;

                // Update start time countdown
                if (timeUntilStart > 0) {
                    const hours = Math.floor(timeUntilStart / (1000 * 60 * 60));
                    const minutes = Math.floor((timeUntilStart % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((timeUntilStart % (1000 * 60)) / 1000);

                    startTimeCountdown.innerHTML = `Starts in: ${hours}h ${minutes}m ${seconds}s`;

                    // Show countdown in sidebar if exam hasn't started
                    if (countdownContainer) {
                        countdownContainer.classList.remove('hidden');
                        countdownDisplay.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                        // Disable start button if countdown is active
                        if (startButton) {
                            startButton.disabled = true;
                            startButton.innerHTML = 'Starting Soon...';
                            startButton.classList.add('opacity-50', 'cursor-not-allowed');
                        }
                    }
                } else {
                    // Exam has started
                    startTimeCountdown.innerHTML = '<span class="text-green-600 dark:text-green-400">✓ Exam Started</span>';

                    if (countdownContainer) {
                        countdownContainer.classList.add('hidden');
                    }

                    // Enable start button if exam has started
                    if (startButton && timeUntilEnd > 0) {
                        startButton.disabled = false;
                        startButton.classList.remove('opacity-50', 'cursor-not-allowed');
                        startButton.innerHTML = '{{ $currentAttempt ? "Resume Exam" : "Start Exam" }}';
                    }
                }

                // Update exam status message
                if (examStatusMessage) {
                    if (timeUntilStart > 0) {
                        const hours = Math.floor(timeUntilStart / (1000 * 60 * 60));
                        const minutes = Math.floor((timeUntilStart % (1000 * 60 * 60)) / (1000 * 60));
                        examStatusMessage.innerHTML = `<i class="fas fa-clock mr-2"></i>Exam starts in ${hours}h ${minutes}m`;
                    } else if (timeUntilEnd > 0) {
                        examStatusMessage.innerHTML = '<i class="fas fa-play-circle mr-2"></i>Exam is ongoing';
                    } else {
                        examStatusMessage.innerHTML = '<i class="fas fa-ban mr-2"></i>Exam has ended';
                    }
                }
            }

            // Update countdown every second
            updateCountdown();
            setInterval(updateCountdown, 1000);
        });
    </script>
@endsection
