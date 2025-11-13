@extends('layouts.app')

@section('title', 'Assign Student to Class - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Assign Student to Class</h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-2">Assign students to classes and streams</p>
                    </div>
                    <a href="{{ route('admin.student-assignments.index') }}"
                       class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Assignments
                    </a>
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-red-800 dark:text-red-400">Please fix the following errors:</h4>
                            <ul class="mt-1 text-sm text-red-600 dark:text-red-300 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <form action="{{ route('admin.student-assignments.store') }}" method="POST">
                    @csrf

                    <!-- Student Selection -->
                    <div class="mb-6">
                        <label for="student_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Select Student *
                        </label>
                        <select id="student_id" name="student_id" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select a student</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}"
                                    {{ (request('student_id') == $student->id || old('student_id') == $student->id) ? 'selected' : '' }}>
                                    {{ $student->name }}
                                    @if($student->profile->student_id ?? false)
                                        ({{ $student->profile->student_id }})
                                    @endif
                                    - {{ $student->email }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Only unassigned students are shown in this list
                        </p>
                    </div>

                    <!-- Class Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Class *
                            </label>
                            <select id="class_id" name="class_id" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }} ({{ $class->level }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="stream_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Stream *
                            </label>
                            <select id="stream_id" name="stream_id" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select Stream</option>
                                @foreach($streams as $stream)
                                    <option value="{{ $stream->id }}" {{ old('stream_id') == $stream->id ? 'selected' : '' }}>
                                        {{ $stream->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Academic Year -->
                    <div class="mb-6">
                        <label for="academic_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Academic Year *
                        </label>
                        <input type="text" id="academic_year" name="academic_year" value="{{ old('academic_year') }}" required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                               placeholder="e.g., 2024-2025">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Enter the academic year in format: YYYY-YYYY
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.student-assignments.index') }}"
                           class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                            Assign to Class
                        </button>
                    </div>
                </form>
            </div>

            <!-- Bulk Assignment Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mt-8">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Bulk Assignment</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Assign multiple students to the same class at once</p>

                <form action="{{ route('admin.student-assignments.bulk-assign') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Select Students *
                            </label>
                            <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 max-h-60 overflow-y-auto">
                                @forelse($students as $student)
                                    <label class="flex items-center mb-2 last:mb-0">
                                        <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                               class="rounded text-blue-600 focus:ring-blue-500">
                                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $student->name }}
                                            @if($student->profile->student_id ?? false)
                                                ({{ $student->profile->student_id }})
                                            @endif
                                    </span>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No unassigned students available</p>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <div class="mb-4">
                                <label for="bulk_class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Class *
                                </label>
                                <select id="bulk_class_id" name="class_id" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">
                                            {{ $class->name }} ({{ $class->level }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="bulk_stream_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Stream *
                                </label>
                                <select id="bulk_stream_id" name="stream_id" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select Stream</option>
                                    @foreach($streams as $stream)
                                        <option value="{{ $stream->id }}">{{ $stream->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="bulk_academic_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Academic Year *
                                </label>
                                <input type="text" id="bulk_academic_year" name="academic_year" required
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="e.g., 2024-2025">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                            Bulk Assign Students
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sync academic year between individual and bulk forms
            const academicYear = document.getElementById('academic_year');
            const bulkAcademicYear = document.getElementById('bulk_academic_year');

            if (academicYear && bulkAcademicYear) {
                academicYear.addEventListener('input', function() {
                    bulkAcademicYear.value = this.value;
                });

                bulkAcademicYear.addEventListener('input', function() {
                    academicYear.value = this.value;
                });
            }
        });
    </script>
@endsection
