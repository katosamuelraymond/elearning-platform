@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $exam->title }}</h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-2">
                        {{ $exam->subject->name }} • {{ $exam->class->name }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('student.exams.index') }}"
                       class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Exams
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
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Your Attempts</h3>
                                <p class="text-lg text-gray-900 dark:text-white mt-1 flex items-center">
                                    <i class="fas fa-history mr-2 text-purple-500"></i>
                                    {{ $exam->attempts->count() }}/{{ $exam->max_attempts }}
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

                    <!-- Your Attempt -->
                    @if($attempt)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="fas fa-history mr-3 text-green-500"></i>
                                Your Attempt
                            </h2>

                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center shadow-sm">
                                            <i class="fas fa-file-alt text-blue-500 text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900 dark:text-white">Attempt Details</h3>
                                            <p class="text-blue-600 dark:text-blue-400 text-sm">
                                                Started on {{ $attempt->started_at->format('M j, Y \a\t g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 text-sm font-medium rounded-full
                                {{ $attempt->status === 'graded' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                   ($attempt->status === 'submitted' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                   'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200') }}">
                                {{ ucfirst($attempt->status) }}
                            </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-600 dark:text-gray-400">Time Spent</p>
                                        <p class="font-semibold text-gray-900 dark:text-white">
                                            {{ floor($attempt->time_spent / 60) }}:{{ sprintf('%02d', $attempt->time_spent % 60) }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 dark:text-gray-400">Submitted At</p>
                                        <p class="font-semibold text-gray-900 dark:text-white">
                                            {{ $attempt->submitted_at ? $attempt->submitted_at->format('M j, g:i A') : 'Not submitted' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 dark:text-gray-400">Score</p>
                                        <p class="font-semibold text-gray-900 dark:text-white">
                                            @if($attempt->total_score !== null)
                                                {{ $attempt->total_score }}/{{ $exam->total_marks }}
                                                @if($attempt->total_score >= $exam->passing_marks)
                                                    <span class="text-green-600 dark:text-green-400 ml-1">✓</span>
                                                @else
                                                    <span class="text-red-600 dark:text-red-400 ml-1">✗</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                @if($attempt->status === 'submitted' && $exam->show_results)
                                    <div class="mt-4 flex justify-center">
                                        <a href="{{ route('student.exams.results', ['exam' => $exam, 'attempt' => $attempt]) }}"
                                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors duration-200">
                                            <i class="fas fa-chart-bar mr-2"></i>
                                            View Results
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
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
                                <span class="text-sm text-gray-600 dark:text-gray-400">Your Attempts</span>
                                <span class="font-semibold text-gray-900 dark:text-white">
                                {{ $exam->attempts->count() }}/{{ $exam->max_attempts }}
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
                        </div>
                    </div>

                    <!-- Action Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Take Exam</h3>

                        @if($canTakeExam)
                            @if($attempt && $attempt->status === 'in_progress')
                                <div class="text-center">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                        You have an exam in progress. Continue where you left off.
                                    </p>
                                    <a href="{{ route('student.exams.start', $exam) }}"
                                       class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 px-4 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                                        <i class="fas fa-play mr-2"></i>
                                        Continue Exam
                                    </a>
                                </div>
                            @else
                                <div class="text-center">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                        Ready to start the exam? Make sure you have a stable internet connection.
                                    </p>
                                    <a href="{{ route('student.exams.start', $exam) }}"
                                       class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                                        <i class="fas fa-play mr-2"></i>
                                        Start Exam
                                    </a>
                                </div>
                            @endif
                        @elseif($isUpcoming)
                            <div class="text-center">
                                <i class="fas fa-clock text-3xl text-orange-500 mb-3"></i>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    This exam will be available on<br>
                                    <strong>{{ $exam->start_time->format('F j, Y \a\t g:i A') }}</strong>
                                </p>
                            </div>
                        @elseif($exam->attempts->count() >= $exam->max_attempts)
                            <div class="text-center">
                                <i class="fas fa-ban text-3xl text-red-500 mb-3"></i>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    You have used all your attempts for this exam.
                                </p>
                            </div>
                        @elseif($isPast)
                            <div class="text-center">
                                <i class="fas fa-calendar-times text-3xl text-gray-500 mb-3"></i>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    This exam has ended and is no longer available.
                                </p>
                            </div>
                        @else
                            <div class="text-center">
                                <i class="fas fa-exclamation-triangle text-3xl text-yellow-500 mb-3"></i>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    You are not eligible to take this exam at this time.
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Previous Attempts -->
                    @if($exam->attempts->count() > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Previous Attempts</h3>
                            <div class="space-y-3">
                                @foreach($exam->attempts->sortByDesc('created_at') as $previousAttempt)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                Attempt #{{ $loop->iteration }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $previousAttempt->started_at->format('M j, g:i A') }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                <span class="text-sm font-medium
                                    {{ $previousAttempt->total_score !== null && $previousAttempt->total_score >= $exam->passing_marks ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $previousAttempt->total_score ?? '-' }}/{{ $exam->total_marks }}
                                </span>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">
                                                {{ $previousAttempt->status }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
