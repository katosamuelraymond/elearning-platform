@extends('layouts.app')

@section('title', 'Admin Dashboard - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Exam</h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-2">Modify exam details and questions</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.exams.index') : route('teacher.exams.index') }}"
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" form="exam-form" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                </div>
            </div>

            <form action="{{ auth()->user()->isAdmin() ? route('admin.exams.update', $exam->id) : route('teacher.exams.update', $exam->id) }}" method="POST" id="exam-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="is_draft" id="is_draft" value="0">
                <input type="hidden" name="selected_bank_questions" id="selected_bank_questions" value="{{ $exam->questions->where('is_bank_question', true)->pluck('id')->implode(',') }}">
                <!-- Dynamic route for question bank -->
                <input type="hidden" id="question-bank-route" value="{{ auth()->user()->isAdmin() ? route('admin.exams.question-bank') : route('teacher.exams.question-bank') }}">
                <!-- Container for bank question points data -->
                <div id="bank-questions-data-container"></div>

                <!-- Exam Details Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Exam Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Exam Title *</label>
                            <input type="text" id="title" name="title" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="{{ old('title', $exam->title) }}" required>
                            @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject *</label>
                            <select id="subject_id" name="subject_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id', $exam->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            @error('subject_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Class *</label>
                            <select id="class_id" name="class_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id', $exam->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('class_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Exam Type</label>
                            <select id="type" name="type" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="quiz" {{ old('type', $exam->type) == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                <option value="midterm" {{ old('type', $exam->type) == 'midterm' ? 'selected' : '' }}>Midterm Exam</option>
                                <option value="end_of_term" {{ old('type', $exam->type) == 'end_of_term' ? 'selected' : '' }}>Final Exam</option>
                                <option value="practice" {{ old('type', $exam->type) == 'practice' ? 'selected' : '' }}>Practice Test</option>
                                <option value="mock" {{ old('type', $exam->type) == 'mock' ? 'selected' : '' }}>Mock Exam</option>
                            </select>
                            @error('type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <textarea id="description" name="description" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Exam description...">{{ old('description', $exam->description) }}</textarea>
                            @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="instructions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Instructions</label>
                            <textarea id="instructions" name="instructions" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Exam instructions...">{{ old('instructions', $exam->instructions) }}</textarea>
                            @error('instructions')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Timing & Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Timing & Settings</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Time *</label>
                            <input type="datetime-local" id="start_time" name="start_time" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="{{ old('start_time', $exam->start_time ? $exam->start_time->format('Y-m-d\TH:i') : '') }}" required>
                            @error('start_time')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Time *</label>
                            <input type="datetime-local" id="end_time" name="end_time" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="{{ old('end_time', $exam->end_time ? $exam->end_time->format('Y-m-d\TH:i') : '') }}" required>
                            @error('end_time')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (minutes) *</label>
                            <input type="number" id="duration" name="duration" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="120" value="{{ old('duration', $exam->duration) }}" required>
                            @error('duration')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="total_marks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total Points *</label>
                            <input type="number" id="total_marks" name="total_marks" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="100" value="{{ old('total_marks', $exam->total_marks) }}" required>
                            @error('total_marks')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="passing_marks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Passing Marks *</label>
                            <input type="number" id="passing_marks" name="passing_marks" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="40" value="{{ old('passing_marks', $exam->passing_marks) }}" required>
                            @error('passing_marks')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="max_attempts" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Attempts *</label>
                            <input type="number" id="max_attempts" name="max_attempts" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="1" value="{{ old('max_attempts', $exam->max_attempts) }}" required>
                            @error('max_attempts')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Security & Proctoring</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Anti-Cheating Measures</h3>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="randomize_questions" value="1" class="rounded text-blue-600 focus:ring-blue-500" {{ old('randomize_questions', $exam->randomize_questions) ? 'checked' : '' }}>
                                    <span class="ml-3 text-gray-700 dark:text-gray-300">Randomize question order</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="require_fullscreen" value="1" class="rounded text-blue-600 focus:ring-blue-500" {{ old('require_fullscreen', $exam->require_fullscreen) ? 'checked' : '' }}>
                                    <span class="ml-3 text-gray-700 dark:text-gray-300">Require fullscreen mode</span>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Results & Visibility</h3>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="show_results" value="1" class="rounded text-blue-600 focus:ring-blue-500" {{ old('show_results', $exam->show_results ?? true) ? 'checked' : '' }}>
                                    <span class="ml-3 text-gray-700 dark:text-gray-300">Show results to students</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_published" value="1" class="rounded text-blue-600 focus:ring-blue-500" {{ old('is_published', $exam->is_published) ? 'checked' : '' }}>
                                    <span class="ml-3 text-gray-700 dark:text-gray-300">Publish exam immediately</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Exam Questions</h2>
                        <div class="flex space-x-3">
                            <!-- Question Bank Button -->
                            <button type="button" id="open-question-bank-btn" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                                <i class="fas fa-database mr-2"></i>Add from Bank
                            </button>
                            <!-- Add New Question Button -->
                            <button type="button" id="add-question-btn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                                <i class="fas fa-plus mr-2"></i>Add New Question
                            </button>
                        </div>
                    </div>

                    <!-- Question Tabs -->
                    <div class="mb-6">
                        <div class="border-b border-gray-200 dark:border-gray-600">
                            <nav class="-mb-px flex space-x-8">
                                <button type="button" id="all-questions-tab" class="question-tab py-2 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600 dark:text-blue-400 flex items-center">
                                    <i class="fas fa-list-check mr-2"></i>
                                    All Questions
                                    <span id="all-questions-count" class="ml-2 bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100 text-xs px-2 py-1 rounded-full">0</span>
                                </button>
                                <button type="button" id="bank-questions-tab" class="question-tab py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 flex items-center">
                                    <i class="fas fa-database mr-2"></i>
                                    From Question Bank
                                    <span id="bank-questions-count" class="ml-2 bg-purple-100 dark:bg-purple-800 text-purple-800 dark:text-purple-100 text-xs px-2 py-1 rounded-full">0</span>
                                </button>
                                <button type="button" id="custom-questions-tab" class="question-tab py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 flex items-center">
                                    <i class="fas fa-pen mr-2"></i>
                                    Custom Questions
                                    <span id="custom-questions-count" class="ml-2 bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 text-xs px-2 py-1 rounded-full">0</span>
                                </button>
                                <button type="button" id="preview-questions-tab" class="question-tab py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 flex items-center">
                                    <i class="fas fa-eye mr-2"></i>
                                    Preview
                                    <span id="preview-count" class="ml-2 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-xs px-2 py-1 rounded-full">0</span>
                                </button>
                            </nav>
                        </div>
                    </div>

                    <!-- All Questions Panel -->
                    <div id="all-questions-panel" class="question-panel active">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300">Editing Instructions</h4>
                                    <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">
                                        • <strong>Bank Questions</strong> (purple badge): Can only change points. Edit the original in Question Bank.
                                        <br>• <strong>Custom Questions</strong> (green badge): Fully editable - modify text, options, and points.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div id="all-questions-container" class="space-y-4">
                            <!-- All questions will be displayed here -->
                        </div>
                    </div>

                    <!-- Bank Questions Panel -->
                    <div id="bank-questions-panel" class="question-panel hidden">
                        <div id="bank-questions-container" class="space-y-4">
                            <!-- Only bank questions will be displayed here -->
                        </div>
                    </div>

                    <!-- Custom Questions Panel -->
                    <div id="custom-questions-panel" class="question-panel hidden">
                        <div id="custom-questions-container" class="space-y-4">
                            <!-- Only custom questions will be displayed here -->
                        </div>
                        <button type="button" id="add-another-custom-question" class="w-full py-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center justify-center mt-4">
                            <i class="fas fa-plus mr-2"></i>Add Another Custom Question
                        </button>
                    </div>

                    <!-- Preview Questions Panel -->
                    <div id="preview-questions-panel" class="question-panel hidden">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300">Exam Preview</h4>
                                    <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">This is how your students will see the exam. All questions from both the question bank and custom ones are shown here.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Validation Alerts Container -->
                        <div id="validation-alerts-container" class="mb-6"></div>

                        <div id="preview-questions-container" class="space-y-6 mb-6">
                            <!-- Preview questions will appear here -->
                            <div id="no-questions-preview" class="text-center py-12 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                                <i class="fas fa-eye-slash text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400">No questions to preview</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Add questions from the bank or create custom ones to see the preview</p>
                            </div>
                        </div>

                        <!-- Preview Summary -->
                        <div id="preview-summary" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 hidden">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Exam Summary</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Total Questions:</span>
                                    <span id="preview-total-questions" class="font-medium ml-2">0</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Total Points:</span>
                                    <span id="preview-total-points" class="font-medium ml-2">0</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Question Types:</span>
                                    <span id="preview-question-types" class="font-medium ml-2">-</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                    <span id="preview-status" class="font-medium ml-2 text-green-600 dark:text-green-400">Ready</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center">
                    <form action="{{ auth()->user()->isAdmin() ? route('admin.exams.destroy', $exam->id) : route('teacher.exams.destroy', $exam->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this exam? This action cannot be undone.')" class="px-6 py-3 border border-red-300 dark:border-red-600 text-red-700 dark:text-red-300 rounded-lg font-medium hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200">
                            <i class="fas fa-trash mr-2"></i>Delete Exam
                        </button>
                    </form>

                    <div class="flex space-x-4">
                        <button type="button" id="save-draft-btn" class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            Save as Draft
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                            Update Exam
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Question Bank Modal -->
    <div id="question-bank-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-xl bg-white dark:bg-gray-800">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Question Bank</h3>
                <button type="button" id="close-question-bank" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <!-- Filters -->
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                        <select id="bank-subject-filter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                        <select id="bank-type-filter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                            <option value="">All Types</option>
                            <option value="mcq">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="short_answer">Short Answer</option>
                            <option value="essay">Essay</option>
                            <option value="fill_blank">Fill in Blanks</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Difficulty</label>
                        <select id="bank-difficulty-filter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                            <option value="">All Levels</option>
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                        <input type="text" id="bank-search" placeholder="Search questions..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- Question Bank Content -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <span id="bank-results-count" class="text-sm text-gray-600 dark:text-gray-400">Loading questions...</span>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" id="select-all-questions" class="rounded text-blue-600 focus:ring-blue-500 mr-2">
                            Select All
                        </label>
                        <button type="button" id="load-bank-questions" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-sync-alt mr-2"></i>Refresh
                        </button>
                    </div>
                </div>

                <div id="question-bank-content" class="space-y-4 max-h-96 overflow-y-auto p-2">
                    <!-- Questions will be loaded here via AJAX -->
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Loading questions...</p>
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                <button type="button" id="cancel-bank-selection" class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    Cancel
                </button>
                <button type="button" id="add-selected-questions" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Add Selected Questions
                    <span id="selected-bank-count" class="ml-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">0</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Include Question Templates -->
    @include('modules.exams.partials.question-templates')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the exam editor with existing data
            initializeExamEditor(@json($exam), @json($exam->questions->load('options', 'subject')));
        });

        function initializeExamEditor(examData, existingQuestions) {
            // Global state
            const state = {
                selectedQuestions: new Map(), // Map of selected bank questions
                customQuestions: new Map(), // Map of custom questions
                currentTab: 'all',
                existingQuestions: existingQuestions || []
            };

            // Initialize with existing questions
            initializeExistingQuestions(existingQuestions);

            // Initialize event listeners and other functionality
            initializeEventListeners();
            updateQuestionCounts();
            updatePreview();

            function initializeExistingQuestions(questions) {
                if (!questions || questions.length === 0) return;

                questions.forEach((question, index) => {
                    const questionData = {
                        id: question.id,
                        type: question.type,
                        difficulty: question.difficulty,
                        subject: question.subject?.name || 'Unknown',
                        question_text: question.question_text,
                        points: question.pivot?.points || question.points,
                        options: question.options ? question.options.map(opt => ({
                            text: opt.option_text,
                            is_correct: opt.is_correct
                        })) : [],
                        details: question.details || {},
                        is_bank_question: question.is_bank_question || false
                    };

                    if (question.is_bank_question) {
                        state.selectedQuestions.set(question.id, questionData);
                    } else {
                        state.customQuestions.set(index, questionData);
                    }
                });

                updateQuestionDisplays();
            }

            function updateQuestionDisplays() {
                updateAllQuestionsDisplay();
                updateBankQuestionsDisplay();
                updateCustomQuestionsDisplay();
                updateSelectedBankQuestionsInput();
            }

            function updateAllQuestionsDisplay() {
                const container = document.getElementById('all-questions-container');
                container.innerHTML = '';

                // Add bank questions
                state.selectedQuestions.forEach((question, questionId) => {
                    const questionElement = createBankQuestionEditElement(question, questionId);
                    container.appendChild(questionElement);
                });

                // Add custom questions
                state.customQuestions.forEach((question, index) => {
                    const questionElement = createCustomQuestionEditElement(question, index);
                    container.appendChild(questionElement);
                });

                updateQuestionCounts();
            }

            function updateBankQuestionsDisplay() {
                const container = document.getElementById('bank-questions-container');
                container.innerHTML = '';

                state.selectedQuestions.forEach((question, questionId) => {
                    const questionElement = createBankQuestionEditElement(question, questionId);
                    container.appendChild(questionElement);
                });
            }

            function updateCustomQuestionsDisplay() {
                const container = document.getElementById('custom-questions-container');
                container.innerHTML = '';

                state.customQuestions.forEach((question, index) => {
                    const questionElement = createCustomQuestionEditElement(question, index);
                    container.appendChild(questionElement);
                });
            }

            function createBankQuestionEditElement(question, questionId) {
                const template = document.getElementById('bank-question-edit-template');
                const questionElement = template.content.cloneNode(true);
                const item = questionElement.querySelector('.bank-question-edit-item');

                item.setAttribute('data-question-id', questionId);
                item.querySelector('.question-type-badge').textContent = getQuestionTypeLabel(question.type);
                item.querySelector('.question-subject').textContent = question.subject;
                item.querySelector('.question-text').textContent = question.question_text;
                item.querySelector('.bank-question-points').value = question.points;
                item.querySelector('.question-id span').textContent = questionId;

                // Update edit link
                const editLink = item.querySelector('a[onclick]');
                editLink.setAttribute('onclick', `openQuestionBankEditor(${questionId})`);

                // Add options preview for MCQ questions
                if (question.type === 'mcq' && question.options && question.options.length > 0) {
                    const optionsContainer = item.querySelector('.question-options');
                    question.options.forEach((option, index) => {
                        const optionDiv = document.createElement('div');
                        optionDiv.className = 'flex items-center';
                        optionDiv.innerHTML = `
                        <span class="w-2 h-2 rounded-full ${option.is_correct ? 'bg-green-500' : 'bg-gray-300'} mr-2"></span>
                        <span class="${option.is_correct ? 'text-green-600 dark:text-green-400 font-medium' : ''}">
                            ${option.text}
                        </span>
                    `;
                        optionsContainer.appendChild(optionDiv);
                    });
                }

                return questionElement;
            }

            function createCustomQuestionEditElement(question, index) {
                const template = document.getElementById('custom-question-edit-template');
                const questionElement = template.content.cloneNode(true);
                const item = questionElement.querySelector('.custom-question-edit-item');

                item.setAttribute('data-question-id', index);
                item.querySelector('.question-number').textContent = index + 1;

                // Set form field values
                const typeSelect = item.querySelector('.custom-question-type');
                typeSelect.value = question.type;
                typeSelect.setAttribute('name', `custom_questions[${index}][type]`);

                const pointsInput = item.querySelector('.custom-question-points');
                pointsInput.value = question.points;
                pointsInput.setAttribute('name', `custom_questions[${index}][points]`);

                const questionText = item.querySelector('textarea[name^="custom_questions"]');
                questionText.value = question.question_text;
                questionText.setAttribute('name', `custom_questions[${index}][question_text]`);

                // Handle question type specific fields
                handleCustomQuestionType(item, question, index);

                return questionElement;
            }

            function handleCustomQuestionType(container, question, index) {
                // Hide all option containers first
                const allContainers = container.querySelectorAll('.custom-options-container');
                allContainers.forEach(container => container.classList.add('hidden'));

                // Show the appropriate container
                const targetContainer = container.querySelector(`.custom-options-container[data-type="${question.type}"]`);
                if (targetContainer) {
                    targetContainer.classList.remove('hidden');
                }

                // Populate based on question type
                switch (question.type) {
                    case 'mcq':
                        populateMCQOptions(container, question, index);
                        break;
                    case 'true_false':
                        populateTrueFalseOptions(container, question, index);
                        break;
                    case 'short_answer':
                        populateShortAnswerOptions(container, question, index);
                        break;
                    case 'essay':
                        populateEssayOptions(container, question, index);
                        break;
                    case 'fill_blank':
                        populateFillBlankOptions(container, question, index);
                        break;
                }
            }

            function populateMCQOptions(container, question, index) {
                const optionGroup = container.querySelector('.option-group');
                optionGroup.innerHTML = '';

                question.options.forEach((option, optIndex) => {
                    const optionDiv = document.createElement('div');
                    optionDiv.className = 'flex items-center option-item';
                    optionDiv.innerHTML = `
                    <input type="radio" name="custom_questions[${index}][correct_answer]" value="${optIndex}"
                           class="mr-3 text-blue-600" ${option.is_correct ? 'checked' : ''}>
                    <input type="text" name="custom_questions[${index}][options][${optIndex}]"
                           class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           value="${option.text}" placeholder="Option ${String.fromCharCode(65 + optIndex)}">
                    <button type="button" class="remove-option ml-2 text-red-600 hover:text-red-800 ${question.options.length <= 2 ? 'disabled:opacity-50 disabled:cursor-not-allowed' : ''}"
                            ${question.options.length <= 2 ? 'disabled' : ''}>
                        <i class="fas fa-times"></i>
                    </button>
                `;
                    optionGroup.appendChild(optionDiv);
                });
            }

            function populateTrueFalseOptions(container, question, index) {
                const trueRadio = container.querySelector('input[value="true"]');
                const falseRadio = container.querySelector('input[value="false"]');

                trueRadio.setAttribute('name', `custom_questions[${index}][correct_answer]`);
                falseRadio.setAttribute('name', `custom_questions[${index}][correct_answer]`);

                if (question.correct_answer === 'true') {
                    trueRadio.checked = true;
                } else {
                    falseRadio.checked = true;
                }
            }

            function populateShortAnswerOptions(container, question, index) {
                const expectedAnswer = container.querySelector('textarea[name$="[expected_answer]"]');
                expectedAnswer.setAttribute('name', `custom_questions[${index}][expected_answer]`);
                expectedAnswer.value = question.details.expected_answer || '';
            }

            function populateEssayOptions(container, question, index) {
                const gradingRubric = container.querySelector('textarea[name$="[grading_rubric]"]');
                gradingRubric.setAttribute('name', `custom_questions[${index}][grading_rubric]`);
                gradingRubric.value = question.details.grading_rubric || '';
            }

            function populateFillBlankOptions(container, question, index) {
                const blankQuestion = container.querySelector('textarea[name$="[blank_question]"]');
                const blankAnswers = container.querySelector('input[name$="[blank_answers]"]');

                blankQuestion.setAttribute('name', `custom_questions[${index}][blank_question]`);
                blankAnswers.setAttribute('name', `custom_questions[${index}][blank_answers]`);

                blankQuestion.value = question.details.blank_question || '';
                blankAnswers.value = Array.isArray(question.details.blank_answers) ?
                    question.details.blank_answers.join(', ') : question.details.blank_answers || '';
            }

            function getQuestionTypeLabel(type) {
                const labels = {
                    'mcq': 'MCQ',
                    'true_false': 'True/False',
                    'short_answer': 'Short Answer',
                    'essay': 'Essay',
                    'fill_blank': 'Fill in Blanks'
                };
                return labels[type] || type;
            }

            function updateQuestionCounts() {
                const allCount = state.selectedQuestions.size + state.customQuestions.size;
                const bankCount = state.selectedQuestions.size;
                const customCount = state.customQuestions.size;

                document.getElementById('all-questions-count').textContent = allCount;
                document.getElementById('bank-questions-count').textContent = bankCount;
                document.getElementById('custom-questions-count').textContent = customCount;
                document.getElementById('preview-count').textContent = allCount;
            }

            function updateSelectedBankQuestionsInput() {
                const selectedIds = Array.from(state.selectedQuestions.keys());
                document.getElementById('selected_bank_questions').value = selectedIds.join(',');

                // Update points data container
                const container = document.getElementById('bank-questions-data-container');
                container.innerHTML = '';

                state.selectedQuestions.forEach((question, questionId) => {
                    const pointsInput = document.createElement('input');
                    pointsInput.type = 'hidden';
                    pointsInput.name = `bank_question_points[${questionId}]`;
                    pointsInput.value = question.points;
                    container.appendChild(pointsInput);
                });
            }

            // PREVIEW FUNCTIONALITY
            function updatePreview() {
                const container = document.getElementById('preview-questions-container');
                const noQuestions = document.getElementById('no-questions-preview');
                const template = document.getElementById('preview-question-template');

                // Clear container
                container.innerHTML = '';
                container.appendChild(noQuestions);

                const totalQuestions = state.selectedQuestions.size + state.customQuestions.size;

                if (totalQuestions === 0) {
                    noQuestions.classList.remove('hidden');
                    document.getElementById('preview-summary').classList.add('hidden');
                    return;
                }

                noQuestions.classList.add('hidden');
                document.getElementById('preview-summary').classList.remove('hidden');

                let questionNumber = 1;
                let totalPoints = calculateTotalPoints();
                const questionTypes = new Set();

                // Add bank questions to preview
                state.selectedQuestions.forEach((question, questionId) => {
                    const questionElement = createPreviewQuestion(question, questionNumber, 'bank', questionId);
                    container.appendChild(questionElement);
                    questionNumber++;
                    questionTypes.add(question.type);
                });

                // Add custom questions to preview
                state.customQuestions.forEach((question, questionIndex) => {
                    const questionElement = createPreviewQuestion(question, questionNumber, 'custom', questionIndex);
                    container.appendChild(questionElement);
                    questionNumber++;
                    questionTypes.add(question.type);
                });

                // Update preview summary
                document.getElementById('preview-total-questions').textContent = totalQuestions;
                document.getElementById('preview-total-points').textContent = totalPoints;
                document.getElementById('preview-question-types').textContent = Array.from(questionTypes).map(type => getQuestionTypeLabel(type)).join(', ');

                // Update status
                const statusElement = document.getElementById('preview-status');
                const validationResults = validateExam();

                if (!validationResults.isValid) {
                    statusElement.textContent = 'Errors Found';
                    statusElement.className = 'font-medium ml-2 text-red-600 dark:text-red-400';
                } else if (validationResults.warnings.length > 0) {
                    statusElement.textContent = 'Warnings';
                    statusElement.className = 'font-medium ml-2 text-yellow-600 dark:text-yellow-400';
                } else {
                    statusElement.textContent = 'Ready';
                    statusElement.className = 'font-medium ml-2 text-green-600 dark:text-green-400';
                }

                // Display validation results
                displayValidationResults(validationResults);
            }

            function createPreviewQuestion(question, questionNumber, source, id) {
                const template = document.getElementById('preview-question-template');
                const questionElement = template.content.cloneNode(true);
                const item = questionElement.querySelector('.preview-question-item');

                // Set question data
                item.querySelector('.question-type-badge').textContent = getQuestionTypeLabel(question.type);
                item.querySelector('.difficulty-badge').textContent = question.difficulty || 'Not set';
                item.querySelector('.question-source').textContent = source === 'bank' ? 'From Bank' : 'Custom';
                item.querySelector('.question-points').textContent = `${question.points} points`;
                item.querySelector('.question-number').textContent = questionNumber;
                item.querySelector('.question-text').textContent = question.question_text || '[Question text not entered]';

                // Add answer area based on question type
                const answerArea = item.querySelector('.answer-area');
                answerArea.classList.remove('hidden');
                const answerInput = item.querySelector('.answer-input');

                if (question.type === 'mcq') {
                    const optionsContainer = item.querySelector('.question-options');
                    optionsContainer.classList.remove('hidden');

                    // Create student answer interface
                    answerInput.innerHTML = '';

                    if (question.options && question.options.length > 0) {
                        question.options.forEach((option, index) => {
                            if (option && option.text) {
                                const answerOption = document.createElement('div');
                                answerOption.className = 'flex items-center mb-2';
                                answerOption.innerHTML = `
                                <input type="radio" name="student_answer_${source}_${id}" value="${index}" class="mr-3 text-blue-600">
                                <span>${String.fromCharCode(65 + index)}. ${option.text}</span>
                            `;
                                answerInput.appendChild(answerOption);
                            }
                        });
                    } else {
                        answerInput.innerHTML = '<p class="text-gray-500 dark:text-gray-400">No options defined</p>';
                    }
                } else if (question.type === 'true_false') {
                    answerInput.innerHTML = `
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="student_answer_${source}_${id}" value="true" class="mr-3 text-blue-600">
                            <span>True</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="student_answer_${source}_${id}" value="false" class="mr-3 text-blue-600">
                            <span>False</span>
                        </label>
                    </div>
                `;
                } else if (question.type === 'short_answer') {
                    answerInput.innerHTML = `
                    <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white" rows="2" placeholder="Type your short answer here..."></textarea>
                `;
                } else if (question.type === 'essay') {
                    answerInput.innerHTML = `
                    <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white" rows="4" placeholder="Write your essay answer here..."></textarea>
                `;
                } else if (question.type === 'fill_blank') {
                    answerInput.innerHTML = `
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white" placeholder="Enter your answer...">
                `;
                }

                return questionElement;
            }

            function calculateTotalPoints() {
                let total = 0;

                // Calculate from bank questions
                state.selectedQuestions.forEach(question => {
                    total += parseInt(question.points) || 0;
                });

                // Calculate from custom questions
                state.customQuestions.forEach(question => {
                    total += parseInt(question.points) || 0;
                });

                return total;
            }

            function validateExam() {
                const validationResults = {
                    warnings: [],
                    errors: [],
                    isValid: true
                };

                const totalQuestions = state.selectedQuestions.size + state.customQuestions.size;
                const totalPoints = calculateTotalPoints();
                const examTotalMarks = parseInt(document.getElementById('total_marks').value) || 0;

                // 1. Check if there are any questions
                if (totalQuestions === 0) {
                    validationResults.errors.push({
                        type: 'no_questions',
                        message: 'Exam must have at least one question',
                        severity: 'error'
                    });
                    validationResults.isValid = false;
                }

                // 2. Check if total points match exam total marks
                if (examTotalMarks > 0 && totalPoints > 0) {
                    const pointsDifference = Math.abs(totalPoints - examTotalMarks);
                    const pointsRatio = pointsDifference / examTotalMarks;

                    if (pointsRatio > 0.1) { // 10% difference threshold
                        validationResults.warnings.push({
                            type: 'points_mismatch',
                            message: `Total questions points (${totalPoints}) don't match exam total marks (${examTotalMarks})`,
                            severity: 'warning'
                        });
                    }
                }

                // 3. Check for long questions
                const longQuestions = checkLongQuestions();
                if (longQuestions.length > 0) {
                    validationResults.warnings.push({
                        type: 'long_questions',
                        message: `${longQuestions.length} question(s) may be too long`,
                        severity: 'warning',
                        details: longQuestions
                    });
                }

                // 4. Check for incomplete custom questions
                const incompleteQuestions = checkIncompleteQuestions();
                if (incompleteQuestions.length > 0) {
                    validationResults.warnings.push({
                        type: 'incomplete_questions',
                        message: `${incompleteQuestions.length} custom question(s) are incomplete`,
                        severity: 'warning',
                        details: incompleteQuestions
                    });
                }

                return validationResults;
            }

            function checkLongQuestions() {
                const longQuestions = [];

                // Check bank questions
                state.selectedQuestions.forEach((question, id) => {
                    if (question.question_text && question.question_text.length > 500) {
                        longQuestions.push({
                            source: 'bank',
                            id: id,
                            length: question.question_text.length,
                            preview: question.question_text.substring(0, 100) + '...'
                        });
                    }
                });

                // Check custom questions
                state.customQuestions.forEach((question, index) => {
                    if (question.question_text && question.question_text.length > 500) {
                        longQuestions.push({
                            source: 'custom',
                            number: index + 1,
                            length: question.question_text.length,
                            preview: question.question_text.substring(0, 100) + '...'
                        });
                    }
                });

                return longQuestions;
            }

            function checkIncompleteQuestions() {
                const incomplete = [];

                // Check custom questions for completeness
                state.customQuestions.forEach((question, index) => {
                    const issues = [];

                    if (!question.question_text || question.question_text.trim().length === 0) {
                        issues.push('Missing question text');
                    }

                    if (question.type === 'mcq') {
                        const validOptions = question.options ? question.options.filter(opt => opt && opt.text && opt.text.trim().length > 0) : [];
                        if (validOptions.length < 2) {
                            issues.push('Need at least 2 valid options for MCQ');
                        }
                    }

                    if (question.type === 'fill_blank') {
                        if (!question.details.blank_question || !question.details.blank_question.includes('[blank]')) {
                            issues.push('Fill blank question must contain [blank] placeholder');
                        }
                    }

                    if (issues.length > 0) {
                        incomplete.push({
                            questionNumber: index + 1,
                            type: question.type,
                            issues: issues
                        });
                    }
                });

                return incomplete;
            }

            function displayValidationResults(validationResults) {
                const container = document.getElementById('validation-alerts-container');
                container.innerHTML = '';

                // Display validation alerts
                validationResults.errors.forEach(error => {
                    const alert = createValidationAlert(error, 'error');
                    container.appendChild(alert);
                });

                validationResults.warnings.forEach(warning => {
                    const alert = createValidationAlert(warning, 'warning');
                    container.appendChild(alert);
                });
            }

            function createValidationAlert(validation, type) {
                const alert = document.createElement('div');
                alert.className = `validation-alert p-4 rounded-lg mb-4 ${
                    type === 'error'
                        ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800'
                        : 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800'
                }`;

                const icon = type === 'error' ? 'fa-exclamation-circle' : 'fa-exclamation-triangle';
                const title = type === 'error' ? 'Error' : 'Warning';

                alert.innerHTML = `
                <div class="flex items-start">
                    <i class="fas ${icon} mt-0.5 mr-3 ${
                    type === 'error'
                        ? 'text-red-600 dark:text-red-400'
                        : 'text-yellow-600 dark:text-yellow-400'
                }"></i>
                    <div class="flex-1">
                        <h4 class="text-sm font-medium ${
                    type === 'error'
                        ? 'text-red-800 dark:text-red-300'
                        : 'text-yellow-800 dark:text-yellow-300'
                }">${title}</h4>
                        <p class="text-sm ${
                    type === 'error'
                        ? 'text-red-700 dark:text-red-400'
                        : 'text-yellow-700 dark:text-yellow-400'
                } mt-1">${validation.message}</p>
                        ${validation.details ? renderValidationDetails(validation.details, type) : ''}
                    </div>
                </div>
            `;

                return alert;
            }

            function renderValidationDetails(details, type) {
                if (!details || details.length === 0) return '';

                const textColor = type === 'error' ? 'text-red-600 dark:text-red-400' : 'text-yellow-600 dark:text-yellow-400';

                return `
                <div class="mt-2 text-xs ${textColor}">
                    <ul class="list-disc list-inside space-y-1">
                        ${details.map(detail => {
                    if (detail.source === 'bank') {
                        return `<li>Bank Question ID ${detail.id}: ${detail.preview || detail.issue}</li>`;
                    } else if (detail.source === 'custom') {
                        return `<li>Custom Question ${detail.number}: ${detail.preview || detail.issue}</li>`;
                    } else if (detail.questionNumber) {
                        return `<li>Question ${detail.questionNumber} (${detail.type}): ${Array.isArray(detail.issues) ? detail.issues.join(', ') : detail.issue}</li>`;
                    }
                    return `<li>${detail.preview || detail.issue}</li>`;
                }).join('')}
                    </ul>
                </div>
            `;
            }

            function initializeEventListeners() {
                // Tab switching
                document.getElementById('all-questions-tab').addEventListener('click', () => switchTab('all'));
                document.getElementById('bank-questions-tab').addEventListener('click', () => switchTab('bank'));
                document.getElementById('custom-questions-tab').addEventListener('click', () => switchTab('custom'));
                document.getElementById('preview-questions-tab').addEventListener('click', () => switchTab('preview'));

                // Add question button
                document.getElementById('add-question-btn').addEventListener('click', addNewCustomQuestion);
                document.getElementById('add-another-custom-question').addEventListener('click', addNewCustomQuestion);

                // Question bank modal
                document.getElementById('open-question-bank-btn').addEventListener('click', openQuestionBank);
                document.getElementById('close-question-bank').addEventListener('click', closeQuestionBank);
                document.getElementById('cancel-bank-selection').addEventListener('click', closeQuestionBank);

                // Save draft button
                document.getElementById('save-draft-btn').addEventListener('click', function() {
                    document.getElementById('is_draft').value = '1';
                    document.getElementById('exam-form').submit();
                });

                // Real-time preview updates
                document.getElementById('total_marks').addEventListener('input', updatePreview);

                // Event delegation for dynamic elements
                document.addEventListener('input', function(e) {
                    // Update points in real-time
                    if (e.target.classList.contains('bank-question-points')) {
                        const questionItem = e.target.closest('.bank-question-edit-item');
                        const questionId = questionItem.getAttribute('data-question-id');
                        if (state.selectedQuestions.has(questionId)) {
                            state.selectedQuestions.get(questionId).points = e.target.value;
                        }
                        updatePreview();
                        updateSelectedBankQuestionsInput();
                    }

                    if (e.target.classList.contains('custom-question-points')) {
                        updatePreview();
                    }
                });
            }

            function addNewCustomQuestion() {
                const newIndex = state.customQuestions.size;
                const newQuestion = {
                    type: 'mcq',
                    points: 5,
                    question_text: '',
                    options: [
                        { text: '', is_correct: true },
                        { text: '', is_correct: false }
                    ],
                    correct_answer: '0',
                    details: {}
                };

                state.customQuestions.set(newIndex, newQuestion);
                updateQuestionDisplays();
                updatePreview();
                switchTab('custom');
            }

            function openQuestionBank() {
                // Implement question bank modal opening
                alert('Question bank functionality would open here');
            }

            function closeQuestionBank() {
                // Implement question bank modal closing
            }

            function switchTab(tabName) {
                state.currentTab = tabName;

                // Update tabs
                document.querySelectorAll('.question-tab').forEach(tab => {
                    tab.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                    tab.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                });

                document.getElementById(`${tabName}-questions-tab`).classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                document.getElementById(`${tabName}-questions-tab`).classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');

                // Update panels
                document.querySelectorAll('.question-panel').forEach(panel => panel.classList.add('hidden'));
                document.getElementById(`${tabName}-questions-panel`).classList.remove('hidden');

                if (tabName === 'preview') {
                    updatePreview();
                }
            }

            // Make functions available globally
            window.openQuestionBankEditor = function(questionId) {
                // Implement question bank editor opening
                alert('This would open the question bank editor for question ID: ' + questionId);
            };
        }
    </script>

    <style>
        .hidden {
            display: none !important;
        }
        .disabled\:opacity-50:disabled {
            opacity: 0.5;
        }
        .disabled\:cursor-not-allowed:disabled {
            cursor: not-allowed;
        }
        .question-item, .bank-question-edit-item, .custom-question-edit-item, .preview-question-item {
            animation: slideIn 0.3s ease;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection
