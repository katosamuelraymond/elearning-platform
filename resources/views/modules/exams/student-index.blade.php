@extends('layouts.app')

@section('title', 'My Exams - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Exams</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">View and take your assigned exams</p>
            </div>

            <!-- Debug Info -->
            <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300">Student Information</h4>
                        <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">
                            <strong>Student:</strong> {{ Auth::user()->name }} |
                            <strong>Class:</strong> {{ $currentAssignment->class->name ?? 'Not Assigned' }} |
                            <strong>Stream:</strong> {{ $currentAssignment->stream->name ?? 'Not Assigned' }} |
                            <strong>Academic Year:</strong> {{ $currentAssignment->academic_year ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 mr-4">
                            <i class="fas fa-clipboard-list text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Exams</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 mr-4">
                            <i class="fas fa-play-circle text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Available</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['available'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 mr-4">
                            <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">In Progress</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['in_progress'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 mr-4">
                            <i class="fas fa-check-circle text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completed</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['completed'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(!$currentAssignment)
                <!-- No Class Assigned -->
                <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-6 text-center">
                    <i class="fas fa-exclamation-triangle text-3xl text-yellow-600 dark:text-yellow-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-yellow-800 dark:text-yellow-300 mb-2">No Class Assigned</h3>
                    <p class="text-yellow-700 dark:text-yellow-400 mb-4">
                        You are not assigned to any class. Please contact your administrator to be assigned to a class.
                    </p>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 max-w-md mx-auto">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <strong>Current Status:</strong><br>
                            - Student: {{ Auth::user()->name }}<br>
                            - Class Assignment: <span class="text-red-500">Not Found</span><br>
                            - Email: {{ Auth::user()->email }}
                        </p>
                    </div>
                </div>
            @elseif($exams->count() > 0)
                <!-- Filters and Exams Grid -->
                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                            <div class="flex space-x-4">
                                <select id="status-filter" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                                    <option value="">All Status</option>
                                    <option value="available">Available</option>
                                    <option value="upcoming">Upcoming</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="missed">Missed</option>
                                </select>

                                <select id="subject-filter" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                                    <option value="">All Subjects</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="relative">
                                <input type="text" id="search-exams" placeholder="Search exams..." class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white w-full md:w-64 text-sm">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Exams Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6" id="exams-grid">
                    @foreach($exams as $exam)
                        @php
                            $status = $exam->getStatusForStudent(Auth::user());
                            $statusText = $exam->getStatusTextForStudent(Auth::user());
                            $statusBadgeClass = $exam->getStatusBadgeClassForStudent(Auth::user());
                            $canAttempt = $exam->canStudentAttempt(Auth::user());
                            $hasAttempts = $exam->attempts->isNotEmpty();
                            $attemptsCount = $exam->attempts->count();
                            $latestAttempt = $exam->attempts->sortByDesc('created_at')->first();
                            $completionPercentage = $latestAttempt && $exam->total_marks > 0 ?
                                ($latestAttempt->total_score / $exam->total_marks) * 100 : 0;
                        @endphp

                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow duration-200 exam-card"
                             data-status="{{ $status }}"
                             data-subject="{{ $exam->subject_id }}">
                            <div class="p-6">
                                <!-- Exam Header -->
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusBadgeClass }}">
                                            {{ $statusText }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 ml-2">
                                            {{ ucfirst(str_replace('_', ' ', $exam->type)) }}
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $exam->total_marks }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Points</p>
                                    </div>
                                </div>

                                <!-- Exam Details -->
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $exam->title }}</h3>
                                <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-2">{{ $exam->description ?? 'No description' }}</p>

                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-book mr-2 w-4"></i>
                                        <span>{{ $exam->subject->name }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-users mr-2 w-4"></i>
                                        <span>{{ $exam->class->name }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-clock mr-2 w-4"></i>
                                        <span>{{ $exam->duration }} minutes</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-calendar mr-2 w-4"></i>
                                        <span>{{ $exam->start_time->format('M j, Y g:i A') }}</span>
                                    </div>
                                </div>

                                <!-- Progress and Attempts -->
                                <div class="mb-4">
                                    @if($latestAttempt)
                                        <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 mb-1">
                                            <span>Progress</span>
                                            <span>{{ $latestAttempt->total_score ?? 0 }}/{{ $exam->total_marks }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $completionPercentage }}%"></div>
                                        </div>
                                    @endif

                                    @if($attemptsCount > 0)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            Attempt {{ $attemptsCount }} of {{ $exam->max_attempts }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Action Button -->
                                <div class="flex space-x-2">
                                    <a href="{{ route('student.exams.show', $exam) }}"
                                       class="flex-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg text-sm font-medium text-center transition-colors duration-200">
                                        View Details
                                    </a>

                                    @if($canAttempt)
                                        <a href="#"
                                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium text-center transition-colors duration-200">
                                            {{ $hasAttempts ? 'Continue' : 'Start Exam' }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($exams->hasPages())
                    <div class="mt-8">
                        {{ $exams->links() }}
                    </div>
                @endif
            @else
                <!-- No Exams Message -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                    <i class="fas fa-clipboard-list text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Exams Found</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        There are no exams assigned to your class ({{ $currentAssignment->class->name ?? 'Class Not Found' }}) at the moment.
                    </p>
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 max-w-md mx-auto">
                        <p class="text-sm text-blue-700 dark:text-blue-400">
                            <strong>Debug Information:</strong><br>
                            - Student Class: {{ $currentAssignment->class->name ?? 'Not found' }}<br>
                            - Student Stream: {{ $currentAssignment->stream->name ?? 'Not found' }}<br>
                            - Total Exams in System: {{ $stats['total'] }}<br>
                            - Available Exams: {{ $stats['available'] }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($exams->count() > 0)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const statusFilter = document.getElementById('status-filter');
                const subjectFilter = document.getElementById('subject-filter');
                const searchInput = document.getElementById('search-exams');
                const examCards = document.querySelectorAll('.exam-card');

                function filterExams() {
                    const statusValue = statusFilter.value;
                    const subjectValue = subjectFilter.value;
                    const searchValue = searchInput.value.toLowerCase();

                    examCards.forEach(card => {
                        const status = card.getAttribute('data-status');
                        const subject = card.getAttribute('data-subject');
                        const title = card.querySelector('h3').textContent.toLowerCase();
                        const description = card.querySelector('p').textContent.toLowerCase();

                        const statusMatch = !statusValue || status === statusValue;
                        const subjectMatch = !subjectValue || subject === subjectValue;
                        const searchMatch = !searchValue || title.includes(searchValue) || description.includes(searchValue);

                        if (statusMatch && subjectMatch && searchMatch) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }

                statusFilter.addEventListener('change', filterExams);
                subjectFilter.addEventListener('change', filterExams);
                searchInput.addEventListener('input', filterExams);
            });
        </script>
    @endif

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection
