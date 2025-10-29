@extends('layouts.app')

@section('title', 'Grade Submission - ' . $submission->student->name . ' - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{-- CORRECTION 1: Check status directly --}}
                            {{ $submission->status == 'graded' ? 'Regrade' : 'Grade' }} Submission
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-2">
                            {{ $assignment->title }} • {{ $submission->student->name }}
                        </p>
                    </div>
                    <a href="{{ route('admin.assignments.submissions.show', [$assignment, $submission]) }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Submission
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.assignments.submissions.update', [$assignment, $submission]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-graduation-cap mr-3 text-green-500"></i>
                        Grading Details
                    </h2>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Points Obtained *
                            </label>
                            <input type="number" name="points_obtained"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white"
                                   min="0" max="{{ $assignment->max_points }}"
                                   value="{{ old('points_obtained', $submission->points_obtained) }}" required>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Maximum points: {{ $assignment->max_points }}
                            </p>
                            @error('points_obtained')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status *
                            </label>
                            <select name="status" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" required>
                                <option value="submitted" {{ old('status', $submission->status) == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="graded" {{ old('status', $submission->status) == 'graded' ? 'selected' : '' }}>Graded</option>
                                <option value="late" {{ old('status', $submission->status) == 'late' ? 'selected' : '' }}>Late</option>
                                <option value="missing" {{ old('status', $submission->status) == 'missing' ? 'selected' : '' }}>Missing</option>
                            </select>
                            @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Feedback
                            </label>
                            <textarea name="feedback" rows="6"
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white"
                                      placeholder="Provide feedback to the student...">{{ old('feedback', $submission->feedback) }}</textarea>
                            @error('feedback')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-user-graduate mr-3 text-blue-500"></i>
                        Student Submission
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Student</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $submission->student->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Submitted</p>
                            <p class="font-medium text-gray-900 dark:text-white">
                                {{ $submission->submitted_at->format('M j, Y g:i A') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">File</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $submission->original_filename }}</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.assignments.submissions.download', [$assignment, $submission]) }}"
                               class="text-blue-600 hover:text-blue-800 dark:text-blue-400 flex items-center">
                                <i class="fas fa-download mr-2"></i>
                                Download File
                            </a>
                        </div>
                    </div>

                    @if($submission->submission_notes)
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Student Notes</p>
                            <p class="text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg mt-1">
                                {{ $submission->submission_notes }}
                            </p>
                        </div>
                    @endif
                </div>

                <div class="flex justify-between items-center">
                    <a href="{{ route('admin.assignments.submissions.show', [$assignment, $submission]) }}"
                       class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>

                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                        <i class="fas fa-check mr-2"></i>
                        {{-- CORRECTION 2: Check status directly --}}
                        {{ $submission->status == 'graded' ? 'Update Grade' : 'Submit Grade' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
