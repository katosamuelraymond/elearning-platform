@extends('layouts.app')

@section('title', 'Edit Quiz - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Quiz</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">Update quiz details and questions</p>
            </div>

            <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST" id="quizForm">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Quiz Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Quiz Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Quiz Title *
                            </label>
                            <input type="text" id="title" name="title" required
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Enter quiz title" value="{{ old('title', $quiz->title) }}">
                            @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subject -->
                        <div>
                            <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Subject *
                            </label>
                            <select id="subject_id" name="subject_id" required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id', $quiz->subject_id) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Class -->
                        <div>
                            <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Class *
                            </label>
                            <select id="class_id" name="class_id" required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id', $quiz->class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Quiz Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Quiz Type *
                            </label>
                            <select id="type" name="type" required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select Type</option>
                                <option value="practice" {{ old('type', $quiz->type) == 'practice' ? 'selected' : '' }}>Practice Quiz</option>
                                <option value="chapter_test" {{ old('type', $quiz->type) == 'chapter_test' ? 'selected' : '' }}>Chapter Test</option>
                                <option value="quick_check" {{ old('type', $quiz->type) == 'quick_check' ? 'selected' : '' }}>Quick Check</option>
                            </select>
                            @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div>
                            <label for="duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Duration (minutes) *
                            </label>
                            <input type="number" id="duration" name="duration" required min="1" max="180"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="60" value="{{ old('duration', $quiz->duration) }}">
                            @error('duration')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Total Marks -->
                        <div>
                            <label for="total_marks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Total Marks *
                            </label>
                            <input type="number" id="total_marks" name="total_marks" required min="1" max="500"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="100" value="{{ old('total_marks', $quiz->total_marks) }}">
                            @error('total_marks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Start Time -->
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Start Time *
                            </label>
                            <input type="datetime-local" id="start_time" name="start_time" required
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   value="{{ old('start_time', $quiz->start_time->format('Y-m-d\TH:i')) }}">
                            @error('start_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Time -->
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                End Time *
                            </label>
                            <input type="datetime-local" id="end_time" name="end_time" required
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   value="{{ old('end_time', $quiz->end_time->format('Y-m-d\TH:i')) }}">
                            @error('end_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="mt-4">
                        <label for="instructions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Instructions
                        </label>
                        <textarea id="instructions" name="instructions" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                  placeholder="Enter quiz instructions (optional)">{{ old('instructions', $quiz->instructions) }}</textarea>
                        @error('instructions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Settings -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="randomize_questions" name="randomize_questions" value="1"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                {{ old('randomize_questions', $quiz->randomize_questions) ? 'checked' : '' }}>
                            <label for="randomize_questions" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Randomize Questions
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="show_answers" name="show_answers" value="1"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                {{ old('show_answers', $quiz->show_answers) ? 'checked' : '' }}>
                            <label for="show_answers" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Show Answers After Submission
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="is_published" name="is_published" value="1"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                {{ old('is_published', $quiz->is_published) ? 'checked' : '' }}>
                            <label for="is_published" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Publish Quiz
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Current Questions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Current Questions</h2>

                    <div class="space-y-4">
                        @foreach($quiz->questions as $index => $question)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center space-x-3">
                                    <span class="flex items-center justify-center w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full text-xs font-medium">
                                        {{ $index + 1 }}
                                    </span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">
                                        {{ str_replace('_', ' ', $question->type) }}
                                    </span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $question->pivot->points }} points
                                    </span>
                                    </div>
                                    <button type="button" class="text-red-600 hover:text-red-800 dark:text-red-400">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <p class="text-gray-900 dark:text-white">{{ $question->question_text }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.quizzes.show', $quiz) }}"
                       class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                        Update Quiz
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
