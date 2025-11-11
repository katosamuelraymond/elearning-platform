@extends('layouts.app')

@section('title', 'Admin Dashboard - Lincoln eLearning')

@section('content')
    <!-- DEBUG INFO -->

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create New Exam</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">Set up a comprehensive examination for your students</p>
            </div>

            <form action="{{ auth()->user()->isAdmin() ? route('admin.exams.store') : route('teacher.exams.store') }}" method="POST" id="exam-form">
                @csrf
                <input type="hidden" name="is_draft" id="is_draft" value="0">
                <input type="hidden" name="selected_bank_questions" id="selected_bank_questions" value="">
                <!-- Dynamic route for question bank -->
                <input type="hidden" id="question-bank-route" value="{{ auth()->user()->isAdmin() ? route('admin.exams.question-bank') : route('teacher.exams.question-bank') }}">
                <!-- Container for bank question points data -->
                <div id="bank-questions-data-container"></div>

                <!-- Exam Details Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Exam Details</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Exam Title *</label>
                            <input type="text" id="title" name="title" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="e.g., Mathematics Final Examination" value="{{ old('title') }}" required>
                            @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject *</label>
                            <select id="subject_id" name="subject_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
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
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('class_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Exam Type</label>
                            <select id="type" name="type" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="quiz" {{ old('type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                <option value="midterm" {{ old('type') == 'midterm' ? 'selected' : '' }}>Midterm Exam</option>
                                <option value="end_of_term" {{ old('type') == 'end_of_term' ? 'selected' : '' }}>Final Exam</option>
                                <option value="practice" {{ old('type') == 'practice' ? 'selected' : '' }}>Practice Test</option>
                                <option value="mock" {{ old('type') == 'mock' ? 'selected' : '' }}>Mock Exam</option>
                            </select>
                            @error('type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <textarea id="description" name="description" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Exam description...">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="instructions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Instructions</label>
                            <textarea id="instructions" name="instructions" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Exam instructions...">{{ old('instructions') }}</textarea>
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
                            <input type="datetime-local" id="start_time" name="start_time" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="{{ old('start_time') }}" required>
                            @error('start_time')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Time *</label>
                            <input type="datetime-local" id="end_time" name="end_time" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="{{ old('end_time') }}" required>
                            @error('end_time')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (minutes) *</label>
                            <input type="number" id="duration" name="duration" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="120" value="{{ old('duration') }}" required>
                            @error('duration')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="total_marks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total Points *</label>
                            <input type="number" id="total_marks" name="total_marks" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="100" value="{{ old('total_marks') }}" required>
                            @error('total_marks')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="passing_marks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Passing Marks *</label>
                            <input type="number" id="passing_marks" name="passing_marks" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="40" value="{{ old('passing_marks') }}" required>
                            @error('passing_marks')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="max_attempts" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Attempts *</label>
                            <input type="number" id="max_attempts" name="max_attempts" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="1" value="{{ old('max_attempts', 1) }}" required>
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
                                    <input type="checkbox" name="randomize_questions" value="1" class="rounded text-blue-600 focus:ring-blue-500" {{ old('randomize_questions') ? 'checked' : '' }}>
                                    <span class="ml-3 text-gray-700 dark:text-gray-300">Randomize question order</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="require_fullscreen" value="1" class="rounded text-blue-600 focus:ring-blue-500" {{ old('require_fullscreen') ? 'checked' : '' }}>
                                    <span class="ml-3 text-gray-700 dark:text-gray-300">Require fullscreen mode</span>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Results & Visibility</h3>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="show_results" value="1" class="rounded text-blue-600 focus:ring-blue-500" {{ old('show_results', true) ? 'checked' : '' }}>
                                    <span class="ml-3 text-gray-700 dark:text-gray-300">Show results to students</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_published" value="1" class="rounded text-blue-600 focus:ring-blue-500" {{ old('is_published') ? 'checked' : '' }}>
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
                                <i class="fas fa-database mr-2"></i>Question Bank
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
                                <button type="button" id="selected-questions-tab" class="question-tab py-2 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600 dark:text-blue-400 flex items-center">
                                    <i class="fas fa-list-check mr-2"></i>
                                    Selected Questions
                                    <span id="selected-count" class="ml-2 bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100 text-xs px-2 py-1 rounded-full">0</span>
                                </button>
                                <button type="button" id="new-questions-tab" class="question-tab py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 flex items-center">
                                    <i class="fas fa-pen mr-2"></i>
                                    Create New Questions
                                </button>
                                <button type="button" id="preview-questions-tab" class="question-tab py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 flex items-center">
                                    <i class="fas fa-eye mr-2"></i>
                                    Exam Preview
                                    <span id="preview-count" class="ml-2 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-xs px-2 py-1 rounded-full">0</span>
                                </button>
                            </nav>
                        </div>
                    </div>

                    <!-- Selected Questions Panel -->
                    <div id="selected-questions-panel" class="question-panel active">
                        <div id="selected-questions-container" class="space-y-4 mb-6">
                            <!-- Selected questions will appear here -->
                            <div id="no-questions-selected" class="text-center py-12 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                                <i class="fas fa-inbox text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400">No questions selected yet</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Choose questions from the bank or create new ones</p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Total Points: <span id="total-points">0</span>
                            </div>
                            <button type="button" id="open-question-bank-bottom" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                                <i class="fas fa-database mr-2"></i>Add from Question Bank
                            </button>
                        </div>
                    </div>

                    <!-- New Questions Panel -->
                    <div id="new-questions-panel" class="question-panel hidden">
                        <div id="new-questions-container" class="space-y-4 mb-6">
                            <!-- New questions will be added here dynamically -->
                        </div>

                        <button type="button" id="add-another-question" class="w-full py-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i>Add Another Question
                        </button>
                    </div>

                    <!-- Preview Questions Panel -->
                    <div id="preview-questions-panel" class="question-panel hidden">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300">Exam Preview</h4>
                                    <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">This is how your students will see the exam. All questions from both the question bank and newly created ones are shown here.</p>
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
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Add questions from the bank or create new ones to see the preview</p>
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
                <div class="flex justify-end space-x-4">
                    <button type="button" id="save-draft-btn" class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        Save as Draft
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                        Create Exam
                    </button>
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

    <!-- New Question Template -->
    <template id="new-question-template">
        <div class="question-item border border-gray-200 dark:border-gray-600 rounded-lg p-4 mb-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">New Question <span class="question-number">1</span></h3>
                <div class="flex items-center space-x-3">
                    <label class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" name="questions[0][save_to_bank]" value="1" class="rounded text-blue-600 focus:ring-blue-500 mr-2">
                        Save to Bank
                    </label>
                    <button type="button" class="remove-question text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Type</label>
                    <select name="questions[0][type]" class="question-type w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="mcq">Multiple Choice</option>
                        <option value="true_false">True/False</option>
                        <option value="short_answer">Short Answer</option>
                        <option value="essay">Essay</option>
                        <option value="fill_blank">Fill in the Blanks</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Points</label>
                    <input type="number" name="questions[0][points]" class="question-points w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="5" min="1">
                </div>

            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Text</label>
                <textarea name="questions[0][question_text]" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" rows="3" placeholder="Enter your question here..." required></textarea>
            </div>

            <!-- Multiple Choice Options -->
            <div class="options-container" data-type="mcq">
                <div class="option-group space-y-3">
                    <div class="flex items-center option-item">
                        <input type="radio" name="questions[0][correct_answer]" value="0" class="mr-3 text-blue-600" checked>
                        <input type="text" name="questions[0][options][0]" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Option A" required>
                        <button type="button" class="remove-option ml-2 text-red-600 hover:text-red-800 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="flex items-center option-item">
                        <input type="radio" name="questions[0][correct_answer]" value="1" class="mr-3 text-blue-600">
                        <input type="text" name="questions[0][options][1]" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Option B" required>
                        <button type="button" class="remove-option ml-2 text-red-600 hover:text-red-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="add-option text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center mt-2">
                    <i class="fas fa-plus mr-1"></i> Add Option
                </button>
            </div>

            <!-- True/False Options -->
            <div class="options-container hidden" data-type="true_false">
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="radio" name="questions[0][correct_answer]" value="true" class="mr-3 text-blue-600" checked>
                        <span class="text-gray-700 dark:text-gray-300">True</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="questions[0][correct_answer]" value="false" class="mr-3 text-blue-600">
                        <span class="text-gray-700 dark:text-gray-300">False</span>
                    </label>
                </div>
            </div>

            <!-- Short Answer -->
            <div class="options-container hidden" data-type="short_answer">
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-300">Students will provide a short written answer to this question.</p>
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expected Answer (for grading reference)</label>
                        <textarea name="questions[0][expected_answer]" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white" rows="2" placeholder="Provide a sample answer for grading reference..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Essay -->
            <div class="options-container hidden" data-type="essay">
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-300">Students will write an essay in response to this question.</p>
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Grading Rubric/Instructions</label>
                        <textarea name="questions[0][grading_rubric]" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white" rows="3" placeholder="Provide grading criteria or instructions for the essay..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Fill in the Blanks -->
            <div class="options-container hidden" data-type="fill_blank">
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">Create fill-in-the-blank questions. Use <code class="bg-yellow-100 dark:bg-yellow-900 px-1 rounded">[blank]</code> to indicate where the blank should appear.</p>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question with Blanks</label>
                            <textarea name="questions[0][blank_question]" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white" rows="2" placeholder="E.g., The capital of France is [blank]."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Correct Answers (comma-separated)</label>
                            <input type="text" name="questions[0][blank_answers]" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white" placeholder="E.g., Paris, paris">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Selected Question Template -->
    <template id="selected-question-template">
        <div class="selected-question-item border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-700" data-question-id="">
            <div class="flex justify-between items-start mb-3">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100 question-type-badge">
                            MCQ
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 difficulty-badge">
                            Medium
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 question-subject">
                            Mathematics
                        </span>
                    </div>
                    <h4 class="text-md font-medium text-gray-900 dark:text-white question-text mb-2"></h4>
                    <div class="question-options text-sm text-gray-600 dark:text-gray-300 space-y-1 hidden"></div>
                </div>
                <div class="flex items-center space-x-2 ml-4">
                    <div class="w-24">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Points</label>
                        <input type="number" class="selected-question-points w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white" value="5" min="1">
                    </div>
                    <button type="button" class="remove-selected-question text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 p-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400 flex justify-between items-center">
                <span>From Question Bank</span>
                <span class="question-id">ID: <span></span></span>
            </div>
        </div>
    </template>

    <!-- Preview Question Template -->
    <template id="preview-question-template">
        <div class="preview-question-item border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-700">
            <div class="flex justify-between items-start mb-3">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100 question-type-badge">
                            MCQ
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 difficulty-badge">
                            Medium
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 question-source">
                            From Bank
                        </span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 question-points">
                            5 points
                        </span>
                    </div>
                    <h4 class="text-md font-medium text-gray-900 dark:text-white question-number-text mb-2">
                        <span class="question-number">1</span>. <span class="question-text"></span>
                    </h4>
                    <div class="question-options text-sm text-gray-600 dark:text-gray-300 space-y-2 mt-3 hidden"></div>

                    <!-- Answer area for student view -->
                    <div class="answer-area mt-4 pt-4 border-t border-gray-200 dark:border-gray-600 hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Answer:</label>
                        <div class="answer-input"></div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Question Bank Item Template -->
    <template id="bank-question-template">
        <div class="bank-question-item border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-150">
            <div class="flex items-start space-x-3">
                <input type="checkbox" class="bank-question-checkbox mt-1 rounded text-blue-600 focus:ring-blue-500" value="">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100 type-badge">
                            MCQ
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 difficulty-badge">
                            Medium
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 subject-name">
                            Mathematics
                        </span>
                    </div>
                    <p class="text-sm text-gray-800 dark:text-gray-200 question-text mb-2 line-clamp-2"></p>
                    <div class="question-preview text-xs text-gray-600 dark:text-gray-400 space-y-1 hidden"></div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            Points: <span class="question-points">5</span>
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            ID: <span class="question-id"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Global state
            const state = {
                selectedQuestions: new Map(), // Map of selected bank questions
                newQuestionsCount: 0,
                currentTab: 'selected',
                newQuestionsData: new Map() // Store new questions data for preview
            };

            // Validation constants
            const VALIDATION_RULES = {
                MAX_QUESTION_LENGTH: 1000,
                MIN_QUESTIONS: 1,
                WARNING_THRESHOLDS: {
                    QUESTION_LENGTH: 500,
                    POINTS_DISCREPANCY: 0.1 // 10% difference
                }
            };

            // DOM Elements
            const questionBankModal = document.getElementById('question-bank-modal');
            const selectedQuestionsPanel = document.getElementById('selected-questions-panel');
            const newQuestionsPanel = document.getElementById('new-questions-panel');
            const previewQuestionsPanel = document.getElementById('preview-questions-panel');
            const selectedQuestionsContainer = document.getElementById('selected-questions-container');
            const newQuestionsContainer = document.getElementById('new-questions-container');
            const previewQuestionsContainer = document.getElementById('preview-questions-container');
            const selectedBankQuestionsInput = document.getElementById('selected_bank_questions');
            const bankQuestionsDataContainer = document.getElementById('bank-questions-data-container');
            const selectedCountSpan = document.getElementById('selected-count');
            const previewCountSpan = document.getElementById('preview-count');
            const totalPointsSpan = document.getElementById('total-points');
            const questionBankRouteInput = document.getElementById('question-bank-route');
            const previewSummary = document.getElementById('preview-summary');
            const validationAlertsContainer = document.getElementById('validation-alerts-container');

            // Core Functions - Defined First
            function calculateTotalPoints() {
                let total = 0;

                // Calculate from selected bank questions
                document.querySelectorAll('.selected-question-item').forEach(item => {
                    const pointsInput = item.querySelector('.selected-question-points');
                    total += parseInt(pointsInput.value) || 0;
                });

                // Calculate from new questions
                document.querySelectorAll('.question-item').forEach(item => {
                    const pointsInput = item.querySelector('.question-points');
                    total += parseInt(pointsInput.value) || 0;
                });

                return total;
            }

            function updateTotalPoints() {
                const total = calculateTotalPoints();
                totalPointsSpan.textContent = total;
            }

            function getQuestionIndex(questionItem) {
                const questions = newQuestionsContainer.querySelectorAll('.question-item');
                return Array.from(questions).indexOf(questionItem);
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

            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // Validation Functions
            function validateExam() {
                const validationResults = {
                    warnings: [],
                    errors: [],
                    isValid: true
                };

                const totalQuestions = state.selectedQuestions.size + state.newQuestionsCount;
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

                    if (pointsRatio > VALIDATION_RULES.WARNING_THRESHOLDS.POINTS_DISCREPANCY) {
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

                // 4. Check for incomplete new questions
                const incompleteQuestions = checkIncompleteQuestions();
                if (incompleteQuestions.length > 0) {
                    validationResults.warnings.push({
                        type: 'incomplete_questions',
                        message: `${incompleteQuestions.length} new question(s) are incomplete`,
                        severity: 'warning',
                        details: incompleteQuestions
                    });
                }

                return validationResults;
            }

            function checkLongQuestions() {
                const longQuestions = [];

                // Check selected bank questions
                state.selectedQuestions.forEach((question, id) => {
                    if (question.question_text && question.question_text.length > VALIDATION_RULES.WARNING_THRESHOLDS.QUESTION_LENGTH) {
                        longQuestions.push({
                            source: 'bank',
                            id: id,
                            length: question.question_text.length,
                            preview: question.question_text.substring(0, 100) + '...'
                        });
                    }
                });

                // Check new questions
                state.newQuestionsData.forEach((question, index) => {
                    if (question.question_text && question.question_text.length > VALIDATION_RULES.WARNING_THRESHOLDS.QUESTION_LENGTH) {
                        longQuestions.push({
                            source: 'new',
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

                // Check new questions for completeness
                state.newQuestionsData.forEach((question, index) => {
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
                        if (!question.blank_question || !question.blank_question.includes('[blank]')) {
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
                // Clear previous validation alerts
                validationAlertsContainer.innerHTML = '';

                // Display validation alerts
                validationResults.errors.forEach(error => {
                    const alert = createValidationAlert(error, 'error');
                    validationAlertsContainer.appendChild(alert);
                });

                validationResults.warnings.forEach(warning => {
                    const alert = createValidationAlert(warning, 'warning');
                    validationAlertsContainer.appendChild(alert);
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
                    } else if (detail.source === 'new') {
                        return `<li>New Question ${detail.number}: ${detail.preview || detail.issue}</li>`;
                    } else if (detail.questionNumber) {
                        return `<li>Question ${detail.questionNumber} (${detail.type}): ${Array.isArray(detail.issues) ? detail.issues.join(', ') : detail.issue}</li>`;
                    }
                    return `<li>${detail.preview || detail.issue}</li>`;
                }).join('')}
                    </ul>
                </div>
            `;
            }

            // Preview functionality
            function updatePreview() {
                const container = document.getElementById('preview-questions-container');
                const noQuestions = document.getElementById('no-questions-preview');
                const template = document.getElementById('preview-question-template');

                // Clear container
                container.innerHTML = '';
                container.appendChild(noQuestions);

                const totalQuestions = state.selectedQuestions.size + state.newQuestionsCount;

                if (totalQuestions === 0) {
                    noQuestions.classList.remove('hidden');
                    previewSummary.classList.add('hidden');
                    previewCountSpan.textContent = '0';
                    return;
                }

                noQuestions.classList.add('hidden');
                previewSummary.classList.remove('hidden');

                let questionNumber = 1;
                let totalPoints = calculateTotalPoints();
                const questionTypes = new Set();

                // Add selected bank questions to preview
                state.selectedQuestions.forEach((question, questionId) => {
                    const questionElement = createPreviewQuestion(question, questionNumber, 'bank', questionId);
                    container.appendChild(questionElement);
                    questionNumber++;
                    questionTypes.add(question.type);
                });

                // Add new questions to preview
                state.newQuestionsData.forEach((question, questionIndex) => {
                    const questionElement = createPreviewQuestion(question, questionNumber, 'new', questionIndex);
                    container.appendChild(questionElement);
                    questionNumber++;
                    questionTypes.add(question.type);
                });

                // Update preview summary
                previewCountSpan.textContent = totalQuestions;
                document.getElementById('preview-total-questions').textContent = totalQuestions;
                document.getElementById('preview-total-points').textContent = totalPoints;
                document.getElementById('preview-question-types').textContent = Array.from(questionTypes).map(type => getQuestionTypeLabel(type)).join(', ');

                // Update status and run validation
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

                // Add validation classes for long questions
                if (question.question_text && question.question_text.length > VALIDATION_RULES.WARNING_THRESHOLDS.QUESTION_LENGTH) {
                    item.classList.add('border-yellow-300', 'dark:border-yellow-600');
                }

                item.querySelector('.question-type-badge').textContent = getQuestionTypeLabel(question.type);
                item.querySelector('.difficulty-badge').textContent = question.difficulty || 'Not set';
                item.querySelector('.question-source').textContent = source === 'bank' ? 'From Bank' : 'New Question';
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

            // Question Bank Functions
            function openQuestionBank() {
                questionBankModal.classList.remove('hidden');
                loadBankQuestions();
            }

            function closeQuestionBank() {
                questionBankModal.classList.add('hidden');
                // Clear selections
                document.querySelectorAll('.bank-question-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateSelectedBankCount();
            }

            async function loadBankQuestions() {
                const content = document.getElementById('question-bank-content');
                content.innerHTML = `
        <div class="text-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Loading questions...</p>
        </div>
    `;

                try {
                    const questionBankRoute = document.getElementById('question-bank-route')?.value;
                    if (!questionBankRoute) throw new Error('Question bank route not found.');

                    const filters = {
                        subject_id: document.getElementById('bank-subject-filter')?.value || '',
                        type: document.getElementById('bank-type-filter')?.value || '',
                        difficulty: document.getElementById('bank-difficulty-filter')?.value || '',
                        search: document.getElementById('bank-search')?.value || ''
                    };

                    const fullUrl = questionBankRoute + '?' + new URLSearchParams(filters);

                    const response = await fetch(fullUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.success) {
                        renderBankQuestions(data.questions);
                        window.bankQuestions = data.questions;
                    } else {
                        throw new Error(data.message || 'Failed to load questions');
                    }

                } catch (error) {
                    content.innerHTML = `
            <div class="text-center py-8 text-red-600 dark:text-red-400">
                <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                <p>Failed to load questions. Please try again.</p>
                <p class="text-sm mt-2">Error: ${error.message}</p>
                <div class="mt-3">
                    <button type="button" onclick="loadBankQuestions()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                        <i class="fas fa-refresh mr-2"></i>Retry
                    </button>
                </div>
            </div>
        `;
                }
            }

            function renderBankQuestions(questions) {
                const content = document.getElementById('question-bank-content');
                const template = document.getElementById('bank-question-template');

                if (questions.length === 0) {
                    content.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-inbox text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400">No questions found</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Try adjusting your filters</p>
            </div>
        `;
                    return;
                }

                content.innerHTML = '';

                questions.forEach((question) => {
                    const questionElement = template.content.cloneNode(true);
                    const item = questionElement.querySelector('.bank-question-item');

                    // Set question data
                    const checkbox = item.querySelector('.bank-question-checkbox');
                    checkbox.value = question.id;

                    // Update question details
                    item.querySelector('.type-badge').textContent = getQuestionTypeLabel(question.type);
                    item.querySelector('.difficulty-badge').textContent = question.difficulty;
                    item.querySelector('.subject-name').textContent = question.subject?.name || 'Unknown Subject';
                    item.querySelector('.question-text').textContent = question.question_text;
                    item.querySelector('.question-points').textContent = question.points;
                    item.querySelector('.question-id').textContent = question.id;

                    // Add preview for MCQ questions
                    if (question.type === 'mcq' && question.options) {
                        const preview = item.querySelector('.question-preview');
                        preview.classList.remove('hidden');
                        question.options.forEach(option => {
                            const optionDiv = document.createElement('div');
                            optionDiv.className = 'flex items-center';
                            optionDiv.innerHTML = `
                    <span class="w-2 h-2 rounded-full ${option.is_correct ? 'bg-green-500' : 'bg-gray-300'} mr-2"></span>
                    <span class="${option.is_correct ? 'text-green-600 dark:text-green-400 font-medium' : ''}">
                        ${option.option_text}
                    </span>
                `;
                            preview.appendChild(optionDiv);
                        });

                        // Store the complete question data including options for later retrieval
                        item.setAttribute('data-question-data', JSON.stringify(question));
                    }

                    content.appendChild(questionElement);
                });

                document.getElementById('bank-results-count').textContent = `Found ${questions.length} questions`;
            }


            function handleBankQuestionCheckboxChange(e) {
                if (e.target.classList.contains('bank-question-checkbox')) {
                    updateSelectedBankCount();
                }
            }

            function updateSelectedBankCount() {
                const selected = document.querySelectorAll('.bank-question-checkbox:checked');
                const count = selected.length;
                document.getElementById('selected-bank-count').textContent = count;
            }

            function toggleSelectAllBankQuestions(e) {
                const checkboxes = document.querySelectorAll('.bank-question-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = e.target.checked;
                });
                updateSelectedBankCount();
            }

            function addSelectedBankQuestions() {
                const selectedCheckboxes = document.querySelectorAll('.bank-question-checkbox:checked');

                selectedCheckboxes.forEach(checkbox => {
                    const questionId = checkbox.value;
                    if (!state.selectedQuestions.has(questionId)) {
                        const questionItem = checkbox.closest('.bank-question-item');

                        // Try to get complete question data from data attribute first
                        const questionDataAttr = questionItem.getAttribute('data-question-data');

                        if (questionDataAttr) {
                            // Use the complete question data from the AJAX response
                            const questionData = JSON.parse(questionDataAttr);

                            // Ensure options are properly formatted for our system
                            if (questionData.options) {
                                questionData.options = questionData.options.map(option => ({
                                    text: option.option_text,
                                    is_correct: option.is_correct
                                }));
                            }

                            state.selectedQuestions.set(questionId, questionData);
                        } else {
                            // Fallback: extract from preview (original method)
                            const options = [];
                            const previewContainer = questionItem.querySelector('.question-preview');

                            if (previewContainer && !previewContainer.classList.contains('hidden')) {
                                const optionElements = previewContainer.querySelectorAll('.flex.items-center');
                                optionElements.forEach((optionElement, index) => {
                                    const optionText = optionElement.querySelector('span:last-child').textContent;
                                    const isCorrect = optionElement.querySelector('.bg-green-500') !== null;
                                    options.push({
                                        text: optionText,
                                        is_correct: isCorrect
                                    });
                                });
                            }

                            const questionData = {
                                id: questionId,
                                type: questionItem.querySelector('.type-badge').textContent.toLowerCase().replace(' ', '_'),
                                difficulty: questionItem.querySelector('.difficulty-badge').textContent,
                                subject: questionItem.querySelector('.subject-name').textContent,
                                question_text: questionItem.querySelector('.question-text').textContent,
                                points: questionItem.querySelector('.question-points').textContent,
                                options: options
                            };

                            state.selectedQuestions.set(questionId, questionData);
                        }
                    }
                });

                updateSelectedQuestionsDisplay();
                closeQuestionBank();
            }

            function updateSelectedQuestionsDisplay() {
                const container = document.getElementById('selected-questions-container');
                const noQuestions = document.getElementById('no-questions-selected');
                const template = document.getElementById('selected-question-template');

                // Clear container
                container.innerHTML = '';
                container.appendChild(noQuestions);

                if (state.selectedQuestions.size === 0) {
                    noQuestions.classList.remove('hidden');
                    selectedCountSpan.textContent = '0';
                    updateTotalPoints();
                    updatePreview();
                    updateSelectedBankQuestionsInput();
                    return;
                }

                noQuestions.classList.add('hidden');

                state.selectedQuestions.forEach((question, questionId) => {
                    const questionElement = template.content.cloneNode(true);
                    const item = questionElement.querySelector('.selected-question-item');

                    item.setAttribute('data-question-id', questionId);
                    item.querySelector('.question-type-badge').textContent = getQuestionTypeLabel(question.type);
                    item.querySelector('.difficulty-badge').textContent = question.difficulty;
                    item.querySelector('.question-subject').textContent = question.subject;
                    item.querySelector('.question-text').textContent = question.question_text;
                    item.querySelector('.selected-question-points').value = question.points;
                    item.querySelector('.question-id span').textContent = questionId;

                    // Add options preview for MCQ questions in selected questions panel
                    if (question.type === 'mcq' && question.options && question.options.length > 0) {
                        const optionsContainer = item.querySelector('.question-options');
                        optionsContainer.classList.remove('hidden');

                        question.options.forEach((option, index) => {
                            const optionDiv = document.createElement('div');
                            optionDiv.className = 'flex items-center';
                            optionDiv.innerHTML = `
                    <span class="w-2 h-2 rounded-full ${option.is_correct ? 'bg-green-500' : 'bg-gray-300'} mr-2"></span>
                    <span class="${option.is_correct ? 'text-green-600 dark:text-green-400 font-medium' : ''}">
                        ${option.text || option.option_text}
                    </span>
                `;
                            optionsContainer.appendChild(optionDiv);
                        });
                    }

                    container.appendChild(questionElement);
                });

                selectedCountSpan.textContent = state.selectedQuestions.size;
                updateTotalPoints();
                updateSelectedBankQuestionsInput();
                updatePreview();
            }

            function updateSelectedBankQuestionsInput() {
                const selectedIds = Array.from(state.selectedQuestions.keys());
                selectedBankQuestionsInput.value = selectedIds.join(',');

                // Also update the points data container
                const container = document.getElementById('bank-questions-data-container');
                container.innerHTML = '';

                state.selectedQuestions.forEach((question, questionId) => {
                    const pointsInput = document.createElement('input');
                    pointsInput.type = 'hidden';
                    pointsInput.name = `bank_question_points[${questionId}]`;

                    // Get the current points value from the visible input
                    const questionElement = document.querySelector(`.selected-question-item[data-question-id="${questionId}"]`);
                    if (questionElement) {
                        const pointsField = questionElement.querySelector('.selected-question-points');
                        pointsInput.value = pointsField ? pointsField.value : question.points;
                    } else {
                        pointsInput.value = question.points;
                    }

                    container.appendChild(pointsInput);
                });
            }

            // New Question Functions
            function addNewQuestion() {
                const template = document.getElementById('new-question-template');
                const newQuestion = template.content.cloneNode(true);
                const questionElement = newQuestion.querySelector('.question-item');

                state.newQuestionsCount++;
                const questionIndex = state.newQuestionsCount - 1;

                // Update all input names with the new index
                const inputs = questionElement.querySelectorAll('[name]');
                inputs.forEach(input => {
                    const oldName = input.getAttribute('name');
                    const newName = oldName.replace(/questions\[\d+\]/, `questions[${questionIndex}]`);
                    input.setAttribute('name', newName);
                });

                questionElement.querySelector('.question-number').textContent = state.newQuestionsCount;
                newQuestionsContainer.appendChild(questionElement);

                // Initialize question data with complete structure
                state.newQuestionsData.set(questionIndex, {
                    type: 'mcq',
                    points: 5,
                    question_text: '',
                    options: [],
                    correct_answer: '0',
                    expected_answer: '',
                    grading_rubric: '',
                    blank_question: '',
                    blank_answers: '',
                    isNew: true
                });

                // Switch to new questions tab
                switchTab('new');
                updateTotalPoints();
                updatePreview();
            }

            function handleQuestionTypeChange(selectElement) {
                const questionItem = selectElement.closest('.question-item');
                const selectedType = selectElement.value;

                const optionsContainers = questionItem.querySelectorAll('.options-container');
                optionsContainers.forEach(container => {
                    container.classList.add('hidden');
                });

                const targetContainer = questionItem.querySelector(`.options-container[data-type="${selectedType}"]`);
                if (targetContainer) {
                    targetContainer.classList.remove('hidden');
                }

                // Update stored data
                const questionIndex = getQuestionIndex(questionItem);
                if (state.newQuestionsData.has(questionIndex)) {
                    state.newQuestionsData.get(questionIndex).type = selectedType;
                }

                updatePreview();
            }

            function addOption(button) {
                const questionItem = button.closest('.question-item');
                const optionGroup = questionItem.querySelector('.option-group');
                const options = optionGroup.querySelectorAll('.option-item');
                const optionCount = options.length;
                const questionIndex = getQuestionIndex(questionItem);

                if (optionCount >= 6) {
                    alert('Maximum 6 options allowed per question.');
                    return;
                }

                const optionDiv = document.createElement('div');
                optionDiv.className = 'flex items-center option-item';
                optionDiv.innerHTML = `
                <input type="radio" name="questions[${questionIndex}][correct_answer]" value="${optionCount}" class="mr-3 text-blue-600">
                <input type="text" name="questions[${questionIndex}][options][${optionCount}]" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Option ${String.fromCharCode(65 + optionCount)}" required>
                <button type="button" class="remove-option ml-2 text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            `;

                optionGroup.appendChild(optionDiv);
                updateOptionButtons(questionItem);

                // Initialize option data
                const questionData = state.newQuestionsData.get(questionIndex);
                if (questionData && !questionData.options[optionCount]) {
                    questionData.options[optionCount] = { text: '', is_correct: false };
                }
            }

            function removeOption(button) {
                const questionItem = button.closest('.question-item');
                const optionGroup = questionItem.querySelector('.option-group');
                const options = optionGroup.querySelectorAll('.option-item');

                if (options.length <= 2) {
                    alert('Multiple choice questions must have at least 2 options.');
                    return;
                }

                button.closest('.option-item').remove();
                updateOptionButtons(questionItem);
            }

            function updateOptionButtons(questionItem) {
                const optionGroup = questionItem.querySelector('.option-group');
                const options = optionGroup.querySelectorAll('.option-item');
                const removeButtons = optionGroup.querySelectorAll('.remove-option');

                removeButtons.forEach((button, index) => {
                    const isDisabled = options.length <= 2;
                    button.disabled = isDisabled;
                    button.classList.toggle('disabled:opacity-50', isDisabled);
                    button.classList.toggle('disabled:cursor-not-allowed', isDisabled);
                });
            }

            // Event Handlers
            function handleSelectedQuestionsClick(e) {
                if (e.target.closest('.remove-selected-question')) {
                    const questionItem = e.target.closest('.selected-question-item');
                    const questionId = questionItem.getAttribute('data-question-id');
                    state.selectedQuestions.delete(questionId);
                    updateSelectedQuestionsDisplay();
                }
            }

            function handleSelectedQuestionsInput(e) {
                if (e.target.classList.contains('selected-question-points')) {
                    const questionItem = e.target.closest('.selected-question-item');
                    const questionId = questionItem.getAttribute('data-question-id');
                    if (state.selectedQuestions.has(questionId)) {
                        state.selectedQuestions.get(questionId).points = e.target.value;
                    }
                    updateTotalPoints();
                    updatePreview();
                    // Update the hidden inputs when points change
                    updateSelectedBankQuestionsInput();
                }
            }

            function handleNewQuestionsClick(e) {
                // Remove question
                if (e.target.closest('.remove-question')) {
                    if (confirm('Are you sure you want to remove this question?')) {
                        const questionItem = e.target.closest('.question-item');
                        const questionIndex = getQuestionIndex(questionItem);
                        state.newQuestionsData.delete(questionIndex);
                        questionItem.remove();
                        state.newQuestionsCount--;
                        updateTotalPoints();
                        updatePreview();
                    }
                }

                // Add option
                if (e.target.closest('.add-option')) {
                    addOption(e.target.closest('.add-option'));
                }

                // Remove option
                if (e.target.closest('.remove-option')) {
                    const removeBtn = e.target.closest('.remove-option');
                    if (!removeBtn.disabled) {
                        removeOption(removeBtn);
                    }
                }

                // Question type change
                if (e.target.classList.contains('question-type')) {
                    handleQuestionTypeChange(e.target);
                }
            }

            function handleNewQuestionsInput(e) {
                const questionItem = e.target.closest('.question-item');
                const questionIndex = getQuestionIndex(questionItem);
                const questionData = state.newQuestionsData.get(questionIndex);

                if (!questionData) return;

                if (e.target.classList.contains('question-points')) {
                    questionData.points = e.target.value;
                    updateTotalPoints();
                } else if (e.target.classList.contains('question-type')) {
                    questionData.type = e.target.value;
                } else if (e.target.name && e.target.name.includes('question_text')) {
                    questionData.question_text = e.target.value;
                } else if (e.target.name && e.target.name.includes('expected_answer')) {
                    questionData.expected_answer = e.target.value;
                } else if (e.target.name && e.target.name.includes('grading_rubric')) {
                    questionData.grading_rubric = e.target.value;
                } else if (e.target.name && e.target.name.includes('blank_question')) {
                    questionData.blank_question = e.target.value;
                } else if (e.target.name && e.target.name.includes('blank_answers')) {
                    questionData.blank_answers = e.target.value;
                } else if (e.target.name && e.target.name.includes('options')) {
                    // Update MCQ options
                    const optionIndex = parseInt(e.target.name.match(/options\]\[(\d+)\]/)[1]);
                    if (!questionData.options[optionIndex]) {
                        questionData.options[optionIndex] = { text: '', is_correct: false };
                    }
                    questionData.options[optionIndex].text = e.target.value;
                }

                updatePreview();
            }

            function handleNewQuestionsChange(e) {
                const questionItem = e.target.closest('.question-item');
                const questionIndex = getQuestionIndex(questionItem);
                const questionData = state.newQuestionsData.get(questionIndex);

                if (!questionData) return;

                if (e.target.name && e.target.name.includes('correct_answer')) {
                    questionData.correct_answer = e.target.value;
                    updatePreview();
                }
            }

            // Main initialization
            function initializeEventListeners() {
                // Tab switching
                document.getElementById('selected-questions-tab').addEventListener('click', () => switchTab('selected'));
                document.getElementById('new-questions-tab').addEventListener('click', () => switchTab('new'));
                document.getElementById('preview-questions-tab').addEventListener('click', () => switchTab('preview'));

                // Question Bank Modal
                document.getElementById('open-question-bank-btn').addEventListener('click', openQuestionBank);
                document.getElementById('open-question-bank-bottom').addEventListener('click', openQuestionBank);
                document.getElementById('close-question-bank').addEventListener('click', closeQuestionBank);
                document.getElementById('cancel-bank-selection').addEventListener('click', closeQuestionBank);

                // Question Bank Actions
                document.getElementById('add-selected-questions').addEventListener('click', addSelectedBankQuestions);
                document.getElementById('select-all-questions').addEventListener('change', toggleSelectAllBankQuestions);
                document.getElementById('load-bank-questions').addEventListener('click', loadBankQuestions);

                // New Questions
                document.getElementById('add-question-btn').addEventListener('click', addNewQuestion);
                document.getElementById('add-another-question').addEventListener('click', addNewQuestion);

                // Exam details changes
                document.getElementById('total_marks').addEventListener('input', updatePreview);

                // Event delegation for dynamic elements
                selectedQuestionsContainer.addEventListener('click', handleSelectedQuestionsClick);
                selectedQuestionsContainer.addEventListener('input', handleSelectedQuestionsInput);
                newQuestionsContainer.addEventListener('click', handleNewQuestionsClick);
                newQuestionsContainer.addEventListener('input', handleNewQuestionsInput);
                newQuestionsContainer.addEventListener('change', handleNewQuestionsChange);
                document.getElementById('question-bank-content').addEventListener('change', handleBankQuestionCheckboxChange);

                // Point calculation
                selectedQuestionsContainer.addEventListener('input', debounce(updateTotalPoints, 300));
                newQuestionsContainer.addEventListener('input', debounce(updateTotalPoints, 300));
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

                // Update preview when switching to preview tab
                if (tabName === 'preview') {
                    updatePreview();
                }
            }

            // Initialize the application
            initializeEventListeners();
            updateSelectedQuestionsDisplay();
            updatePreview();

            // Make loadBankQuestions available globally for retry button
            window.loadBankQuestions = loadBankQuestions;

            // Load initial bank questions
            loadBankQuestions();
        });
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
        .question-item, .selected-question-item, .bank-question-item, .preview-question-item {
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
