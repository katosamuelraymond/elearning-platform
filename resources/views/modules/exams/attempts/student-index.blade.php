
@extends('layouts.app')

@section('title', 'My Exam History - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Exam History</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">View your past exam attempts and results</p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-500 text-white mr-4">
                            <i class="fas fa-history"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Attempts</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-500 text-white mr-4">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completed</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['completed'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-500 text-white mr-4">
                            <i class="fas fa-spinner"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">In Progress</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['in_progress'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attempts Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">My Exam Attempts</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Exam
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Subject
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Score
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Time Spent
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Submitted At
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($attempts as $attempt)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $attempt->exam->title }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $attempt->exam->type }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $attempt->exam->subject->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($attempt->status === 'graded') bg-green-100 text-green-800
                                        @elseif($attempt->status === 'submitted') bg-yellow-100 text-yellow-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ ucfirst($attempt->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($attempt->score)
                                        {{ $attempt->score }}/{{ $attempt->exam->total_marks }}
                                        <span class="text-gray-500 dark:text-gray-400">
                                            ({{ round(($attempt->score / $attempt->exam->total_marks) * 100, 1) }}%)
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($attempt->time_spent)
                                        {{ gmdate('H:i:s', $attempt->time_spent) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $attempt->submitted_at ? $attempt->submitted_at->format('M j, Y g:i A') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($attempt->status === 'graded' || $attempt->exam->show_results)
                                        <a href="{{ route('student.exams.results', [$attempt->exam, $attempt]) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                            View Results
                                        </a>
                                    @endif
                                    @if($attempt->status === 'in_progress')
                                        <a href="{{ route('student.exams.take', [$attempt->exam, $attempt]) }}" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                            Continue Exam
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    You haven't attempted any exams yet.
                                    <a href="{{ route('student.exams.index') }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 ml-1">
                                        Browse available exams
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($attempts->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $attempts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
