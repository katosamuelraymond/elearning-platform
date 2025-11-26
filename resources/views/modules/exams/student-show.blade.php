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

                <!-- Clean Timer Display -->
                @if(now() < $exam->start_time)
                    <div class="mt-4 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-clock text-blue-500 text-lg"></i>
                                <div>
                                    <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-200">Exam Starts In</h3>
                                    <p class="text-xs text-blue-600 dark:text-blue-400">The exam will begin automatically when the timer reaches zero</p>
                                </div>
                            </div>
                            <div id="main-timer" class="text-xl font-mono font-bold text-blue-700 dark:text-blue-300 bg-white dark:bg-blue-800 px-4 py-2 rounded border border-blue-200 dark:border-blue-600">
                                @php
                                    $secondsUntilStart = max(0, $exam->start_time->diffInSeconds(now()));
                                    $hours = floor($secondsUntilStart / 3600);
                                    $minutes = floor(($secondsUntilStart % 3600) / 60);
                                    $seconds = $secondsUntilStart % 60;
                                    echo sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                                @endphp
                            </div>
                        </div>
                    </div>
                @endif
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

                        <!-- Results Section for Students -->
                        @php
                            $gradedAttempts = $attempts->where('status', 'graded');
                            $hasGradedAttempts = $gradedAttempts->isNotEmpty();
                            $latestGradedAttempt = $gradedAttempts->sortByDesc('submitted_at')->first();

                            // Check if results are released to students
                            $resultsReleased = $exam->show_results && $exam->results_released_at;
                        @endphp

                        @if($hasGradedAttempts)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Your Results</h3>

                                @if($resultsReleased)
                                    <!-- Results are released - show everything -->
                                    <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                                <span class="text-green-700 dark:text-green-300 font-medium">
                                                    Results released on {{ $exam->results_released_at->format('M j, Y g:i A') }}
                                                </span>
                                            </div>
                                            @if($latestGradedAttempt)
                                                <a href="{{ route('student.exams.results', ['exam' => $exam, 'attempt' => $latestGradedAttempt]) }}"
                                                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                                                    <i class="fas fa-chart-bar mr-2"></i>
                                                    View Detailed Results
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Quick Results Overview -->
                                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($gradedAttempts as $attempt)
                                            <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                                <div class="flex justify-between items-start mb-2">
                                                    <h4 class="font-medium text-gray-900 dark:text-white">
                                                        Attempt {{ $loop->iteration }}
                                                    </h4>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $attempt->submitted_at->format('M j, g:i A') }}
                                                    </span>
                                                </div>
                                                <div class="space-y-2">
                                                    <div class="flex justify-between">
                                                        <span class="text-sm text-gray-600 dark:text-gray-400">Score:</span>
                                                        <span class="font-semibold {{ $attempt->total_score >= ($exam->passing_marks ?? 0) ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                            {{ $attempt->total_score }}/{{ $exam->total_marks }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-sm text-gray-600 dark:text-gray-400">Percentage:</span>
                                                        <span class="font-medium text-gray-900 dark:text-white">
                                                            {{ number_format(($attempt->total_score / $exam->total_marks) * 100, 1) }}%
                                                        </span>
                                                    </div>
                                                    @if($exam->passing_marks)
                                                        <div class="flex justify-between">
                                                            <span class="text-sm text-gray-600 dark:text-gray-400">Status:</span>
                                                            <span class="font-medium {{ $attempt->total_score >= $exam->passing_marks ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                                {{ $attempt->total_score >= $exam->passing_marks ? 'Passed' : 'Failed' }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                                    <a href="{{ route('student.exams.results', ['exam' => $exam, 'attempt' => $attempt]) }}"
                                                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium flex items-center">
                                                        <i class="fas fa-external-link-alt mr-1"></i>
                                                        View attempt details
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <!-- Results are graded but not released yet - show NOTHING about scores -->
                                    <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-clock text-yellow-500 mr-2"></i>
                                            <div>
                                                <p class="text-yellow-700 dark:text-yellow-300 font-medium">
                                                    Results Pending Release
                                                </p>
                                                <p class="text-yellow-600 dark:text-yellow-400 text-sm mt-1">
                                                    Your exam has been graded. Results will be available once your teacher releases them.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Show only that attempts exist, but NO scores -->
                                    <div class="mt-4 space-y-3">
                                        @foreach($gradedAttempts as $attempt)
                                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        Attempt {{ $loop->iteration }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        Submitted: {{ $attempt->submitted_at->format('M j, g:i A') }}
                                                    </p>
                                                    <!-- NO SCORE SHOWN HERE -->
                                                </div>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Graded
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @elseif($attempts->where('status', 'submitted')->isNotEmpty())
                            <!-- Attempts submitted but not graded yet -->
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Results Status</h3>
                                <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-hourglass-half text-blue-500 mr-2"></i>
                                        <div>
                                            <p class="text-blue-700 dark:text-blue-300 font-medium">
                                                Awaiting Grading
                                            </p>
                                            <p class="text-blue-600 dark:text-blue-400 text-sm mt-1">
                                                Your submitted attempts are currently being graded by your teacher.
                                                Results will be available once grading is complete and released.
                                            </p>
                                        </div>
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
                                    <span class="text-blue-800 dark:text-blue-100 font-medium attempts-count" data-attempts-count>
                                        {{ $attempts->count() }} / {{ $exam->max_attempts }}
                                    </span>
                                </div>
                                @if($resultsReleased && $hasGradedAttempts)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-blue-700 dark:text-blue-300">Best Score:</span>
                                        <span class="text-blue-800 dark:text-blue-100 font-medium">
                                            {{ $bestScore ?? 0 }} / {{ $exam->total_marks }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-blue-700 dark:text-blue-300">Results Status:</span>
                                        <span class="text-green-600 dark:text-green-400 font-medium">
                                            Available
                                        </span>
                                    </div>
                                @elseif($hasGradedAttempts)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-blue-700 dark:text-blue-300">Results Status:</span>
                                        <span class="text-yellow-600 dark:text-yellow-400 font-medium">
                                            Pending Release
                                        </span>
                                    </div>
                                @else
                                    <div class="flex justify-between text-sm">
                                        <span class="text-blue-700 dark:text-blue-300">Best Score:</span>
                                        <span class="text-blue-800 dark:text-blue-100 font-medium">
                                            {{ $bestScore ?? 0 }} / {{ $exam->total_marks }}
                                        </span>
                                    </div>
                                @endif
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
                                                <!-- Only show scores when results are released -->
                                                @if($resultsReleased && ($attempt->status === 'submitted' || $attempt->status === 'graded'))
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

                        <!-- Results Quick Action -->
                        @if($resultsReleased && $hasGradedAttempts && $latestGradedAttempt)
                            <div class="mt-6">
                                <a href="{{ route('student.exams.results', ['exam' => $exam, 'attempt' => $latestGradedAttempt]) }}"
                                   class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                                    <i class="fas fa-chart-bar mr-2"></i>
                                    View Detailed Results
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Initializing exam countdown timer...');

            const examId = {{ $exam->id }};
            let countdownTimer;

            // Elements
            const mainTimer = document.getElementById('main-timer');
            const startTimeCountdown = document.getElementById('start-time-countdown');
            const countdownContainer = document.getElementById('countdown-to-start');
            const countdownDisplay = document.getElementById('countdown-display');
            const startButton = document.querySelector('button[type="submit"]');
            const examStatusMessage = document.getElementById('exam-status-message');

            // Get exam times from server
            const examStartTime = new Date('{{ $exam->start_time->toISOString() }}').getTime();
            const examEndTime = new Date('{{ $exam->end_time->toISOString() }}').getTime();
            const serverNow = new Date('{{ now()->toISOString() }}').getTime();

            console.log('⏰ Time data:', {
                examStartTime: new Date(examStartTime).toLocaleString(),
                examEndTime: new Date(examEndTime).toLocaleString(),
                serverNow: new Date(serverNow).toLocaleString(),
                clientNow: new Date().toLocaleString(),
                timeUntilStart: Math.floor((examStartTime - serverNow) / 1000) + ' seconds'
            });

            // Check if we should show the countdown
            const shouldShowCountdown = {{ now() < $exam->start_time ? 'true' : 'false' }};
            console.log('📊 Should show countdown:', shouldShowCountdown);

            function updateAllTimers() {
                const now = Date.now();
                const timeUntilStart = Math.max(0, Math.floor((examStartTime - now) / 1000));

                // Format time as HH:MM:SS
                const hours = Math.floor(timeUntilStart / 3600);
                const minutes = Math.floor((timeUntilStart % 3600) / 60);
                const seconds = timeUntilStart % 60;

                const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                const readableString = `${hours}h ${minutes}m ${seconds}s`;

                console.log('⏰ Timer update:', timeString, '(', timeUntilStart, 'seconds remaining)');

                // Update main timer
                if (mainTimer) {
                    mainTimer.textContent = timeString;

                    // Add visual effects based on time remaining
                    if (timeUntilStart < 600 && timeUntilStart > 0) { // Under 10 minutes
                        mainTimer.classList.add('animate-pulse', 'text-red-600', 'dark:text-red-400');
                    } else if (timeUntilStart < 3600 && timeUntilStart > 0) { // Under 1 hour
                        mainTimer.classList.add('text-orange-600', 'dark:text-orange-400');
                        mainTimer.classList.remove('text-red-600', 'dark:text-red-400', 'animate-pulse');
                    } else {
                        mainTimer.classList.remove('animate-pulse', 'text-red-600', 'dark:text-red-400', 'text-orange-600', 'dark:text-orange-400');
                    }
                }

                // Update start time countdown
                if (startTimeCountdown) {
                    if (timeUntilStart > 0) {
                        startTimeCountdown.innerHTML = `Starts in: ${readableString}`;
                    } else {
                        startTimeCountdown.innerHTML = '<span class="text-green-600 dark:text-green-400">✓ Exam Started</span>';
                    }
                }

                // Update sidebar countdown
                if (countdownContainer && countdownDisplay) {
                    if (timeUntilStart > 0) {
                        countdownContainer.classList.remove('hidden');
                        countdownDisplay.textContent = timeString;

                        // Disable start button
                        if (startButton) {
                            startButton.disabled = true;
                            startButton.innerHTML = 'Starting Soon...';
                            startButton.classList.add('opacity-50', 'cursor-not-allowed');
                        }
                    } else {
                        countdownContainer.classList.add('hidden');

                        // Enable start button if exam is active
                        if (startButton) {
                            startButton.disabled = false;
                            startButton.classList.remove('opacity-50', 'cursor-not-allowed');
                            startButton.innerHTML = '{{ $currentAttempt ? "Resume Exam" : "Start Exam" }}';
                        }
                    }
                }

                // Update status message
                if (examStatusMessage) {
                    if (timeUntilStart > 0) {
                        examStatusMessage.innerHTML = `<i class="fas fa-clock mr-2"></i>Exam starts in ${readableString}`;
                    } else {
                        examStatusMessage.innerHTML = '<i class="fas fa-play-circle mr-2"></i>Exam is ongoing';
                    }
                }

                // Stop timer when exam starts
                if (timeUntilStart <= 0) {
                    clearInterval(countdownTimer);
                    console.log('✅ Exam start time reached!');

                    if (mainTimer) {
                        mainTimer.innerHTML = '<span class="text-green-600 dark:text-green-400">READY!</span>';
                    }

                    // Reload page after 2 seconds to update the UI
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            }

            // Only start timer if exam hasn't started yet
            if (shouldShowCountdown) {
                // Start the countdown timer immediately
                countdownTimer = setInterval(updateAllTimers, 1000);
                console.log('▶️ Countdown timer started');

                // Initial update
                updateAllTimers();
            } else {
                console.log('⏹️ Exam has already started, no countdown needed');

                // Update UI for started exam
                if (mainTimer) {
                    mainTimer.innerHTML = '<span class="text-green-600 dark:text-green-400">EXAM STARTED</span>';
                }
                if (startTimeCountdown) {
                    startTimeCountdown.innerHTML = '<span class="text-green-600 dark:text-green-400">✓ Exam Started</span>';
                }
                if (countdownContainer) {
                    countdownContainer.classList.add('hidden');
                }
            }

            // Clean up on page unload
            window.addEventListener('beforeunload', function() {
                if (countdownTimer) {
                    clearInterval(countdownTimer);
                    console.log('🧹 Countdown timer cleaned up');
                }
            });

            // Optional: Add polling for status updates (every 30 seconds)
            function pollExamStatus() {
                if (!shouldShowCountdown) return; // Only poll if countdown is active

                fetch(`/exams/${examId}/poll-status`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('📡 Polling response:', data);

                        // Sync time if there's a significant difference
                        const serverTime = new Date(data.current_time).getTime();
                        const clientTime = Date.now();
                        const timeDiff = Math.abs(serverTime - clientTime);

                        if (timeDiff > 5000) {
                            console.log('🕒 Time sync needed:', timeDiff + 'ms difference');
                        }
                    })
                    .catch(error => {
                        console.log('⚠️ Polling error:', error);
                    });
            }

            // Start polling every 30 seconds if countdown is active
            if (shouldShowCountdown) {
                setInterval(pollExamStatus, 30000);
            }
        });
    </script>

