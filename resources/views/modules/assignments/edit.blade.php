@extends('layouts.app')

@section('title', 'Edit Assignment - Lincoln eLearning')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Edit Assignment
            </h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">
                Update assignment details and requirements
            </p>
        </div>

        <!-- Assignment Form -->
        <form action="{{ route('admin.assignments.update', $assignment->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                    <i class="fas fa-edit mr-3 text-blue-500"></i>
                    Assignment Details
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Assignment Title *
                        </label>
                        <input type="text" name="title"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Enter assignment title"
                            value="{{ old('title', $assignment->title) }}" required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Class and Subject -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Class *
                        </label>
                        <select name="class_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id', $assignment->class_id) == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Subject *
                        </label>
                        <select name="subject_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $assignment->subject_id) == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Due Date and Points -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Due Date *
                        </label>
                        <input type="datetime-local" name="due_date"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            value="{{ old('due_date', $assignment->due_date->format('Y-m-d\TH:i')) }}" required>
                        @error('due_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Maximum Points *
                        </label>
                        <input type="number" name="max_points"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="100"
                            value="{{ old('max_points', $assignment->max_points) }}" required min="1">
                        @error('max_points')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Instructions -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Instructions
                        </label>
                        <textarea name="instructions"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            rows="3"
                            placeholder="Provide instructions for this assignment...">{{ old('instructions', $assignment->instructions) }}</textarea>
                        @error('instructions')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea name="description"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            rows="4"
                            placeholder="Provide detailed description for this assignment...">{{ old('description', $assignment->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Requirements -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            <i class="fas fa-paperclip mr-2 text-gray-500"></i>
                            Allowed File Formats
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @php
                                $allowedFormats = old('allowed_formats', $assignment->allowed_formats ?? ['pdf', 'doc', 'docx']);
                            @endphp
                            <label class="flex items-center">
                                <input type="checkbox" name="allowed_formats[]" value="pdf" class="rounded text-blue-600 focus:ring-blue-500"
                                    {{ in_array('pdf', $allowedFormats) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">PDF</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="allowed_formats[]" value="doc" class="rounded text-blue-600 focus:ring-blue-500"
                                    {{ in_array('doc', $allowedFormats) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">DOC</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="allowed_formats[]" value="docx" class="rounded text-blue-600 focus:ring-blue-500"
                                    {{ in_array('docx', $allowedFormats) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">DOCX</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="allowed_formats[]" value="jpg" class="rounded text-blue-600 focus:ring-blue-500"
                                    {{ in_array('jpg', $allowedFormats) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">JPG</span>
                            </label>
                        </div>
                    </div>

                    <!-- File Size -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Maximum File Size (MB) *
                        </label>
                        <input type="number" name="max_file_size"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            value="{{ old('max_file_size', $assignment->max_file_size) }}" required min="1">
                        @error('max_file_size')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Publication Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Publication Status
                        </label>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_published" value="1" class="rounded text-blue-600 focus:ring-blue-500"
                                {{ old('is_published', $assignment->is_published) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Publish assignment</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center">
                <a href="{{ route('admin.assignments.index') }}"
                    class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Assignments
                </a>

                <div class="flex space-x-3">
                    <a href="{{ route('admin.assignments.show', $assignment->id) }}"
                        class="px-6 py-3 border border-blue-600 text-blue-600 dark:text-blue-400 rounded-lg font-medium hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                        <i class="fas fa-check mr-2"></i>
                        Update Assignment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
