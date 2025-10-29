@extends('layouts.app')

@section('title', $assignment->title)

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $assignment->title }}</h1>
                {{-- Back button --}}
                <a href="{{ route('student.assignments.index') }}"
                   class="inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Assignments
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-700 dark:text-gray-300 mb-4 border-b pb-4 dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-500 dark:text-gray-400">Subject</p>
                            <p class="text-lg font-semibold text-purple-600 dark:text-purple-400">{{ $assignment->subject->name }}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500 dark:text-gray-400">Due Date</p>
                            <p class="text-lg font-semibold @if(now()->gt($assignment->due_date) && !$submission) text-red-600 dark:text-red-400 @else text-green-600 dark:text-green-400 @endif">
                                {{ \Carbon\Carbon::parse($assignment->due_date)->format('M j, Y g:i A') }}
                            </p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500 dark:text-gray-400">Max Points</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $assignment->max_points }}</p>
                        </div>
                    </div>

                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Instructions</h2>
                    <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                        {!! nl2br(e($assignment->instructions)) !!}
                    </div>

                    @if($assignment->assignment_file)
                        <div class="mt-6 pt-4 border-t dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Attached File</h3>
                            <a href="{{ route('student.assignments.downloadAssignment', $assignment) }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors duration-200">
                                <i class="fas fa-file-download mr-2"></i>
                                Download Assignment File ({{ $assignment->original_filename ?? 'File' }})
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Your Submission</h2>

                @if($submission)
                    {{-- Submission Status and History --}}
                    <div class="bg-yellow-50 dark:bg-yellow-900/50 border border-yellow-200 dark:border-yellow-700 rounded-lg p-6 mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-yellow-800 dark:text-yellow-200">
                                    Submission Status: <span class="{{ $submission->status === 'graded' ? 'text-green-600' : 'text-orange-600' }} dark:text-white">{{ ucfirst($submission->status) }}</span>
                                </h3>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                    Submitted: {{ $submission->submitted_at->format('M j, Y g:i A') }}
                                    @if($submission->status === 'late') (LATE) @endif
                                </p>
                            </div>
                            <a href="{{ route('student.assignments.submissions.download', ['assignment' => $assignment, 'submission' => $submission]) }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 transition-colors duration-200">
                                <i class="fas fa-file-arrow-down mr-2"></i>
                                Download My File
                            </a>
                        </div>

                        {{-- Grading Info --}}
                        @if($submission->points_obtained !== null)
                            <div class="mt-4 pt-4 border-t border-yellow-200 dark:border-yellow-700">
                                <p class="text-lg font-bold text-yellow-800 dark:text-yellow-200">
                                    Grade: <span class="text-green-600 dark:text-green-400">{{ $submission->points_obtained }} / {{ $assignment->max_points }}</span>
                                </p>
                                @if($submission->feedback)
                                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-2">
                                        <strong>Teacher Feedback:</strong> {{ $submission->feedback }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif


                {{-- Submission/Resubmission Form --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        @if($submission) Resubmit Assignment @else Submit Your Work @endif
                    </h3>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('student.assignments.submit', $assignment) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="submission_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Upload File <span class="text-xs text-gray-500 dark:text-gray-400">(Max size: {{ $assignment->max_file_size }}MB. Formats: {{ implode(', ', $assignment->allowed_formats ?? ['Any']) }})</span>
                            </label>
                            <input type="file" name="submission_file" id="submission_file" class="mt-1 block w-full text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer focus:outline-none @error('submission_file') border-red-500 @enderror">
                            @error('submission_file')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="submission_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Submission Notes (Optional)</label>
                            <textarea name="submission_notes" id="submission_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white @error('submission_notes') border-red-500 @enderror">{{ old('submission_notes', $submission->submission_notes ?? '') }}</textarea>
                            @error('submission_notes')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full inline-flex justify-center py-3 px-4 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <i class="fas fa-upload mr-2"></i>
                            @if($submission) Update Submission @else Submit Assignment @endif
                        </button>

                        @if(now()->gt($assignment->due_date))
                            <p class="text-center text-red-500 text-sm mt-3">⚠️ Warning: The due date has passed. Any submission now will be marked as **LATE**.</p>
                        @endif
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
