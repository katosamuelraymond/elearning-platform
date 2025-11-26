@extends('layouts.app')

@section('title', 'Admin Dashboard - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-4 sm:py-8">
        <div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6 sm:mb-8">
                <div class="flex-1">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Edit Exam</h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-1 sm:mt-2 text-sm sm:text-base">Modify exam details and questions</p>
                </div>
                <div class="flex flex-wrap gap-2 sm:gap-3">
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.exams.index') : route('teacher.exams.index') }}"
                       class="flex-1 sm:flex-none px-3 sm:px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 text-sm sm:text-base flex items-center justify-center">
                        <i class="fas fa-times mr-1 sm:mr-2 text-xs sm:text-sm"></i>Cancel
                    </a>
                    <button type="submit" form="exam-form" class="flex-1 sm:flex-none bg-green-600 hover:bg-green-700 text-white px-3 sm:px-4 py-2 rounded-lg font-medium transition-colors duration-200 text-sm sm:text-base flex items-center justify-center">
                        <i class="fas fa-save mr-1 sm:mr-2 text-xs sm:text-sm"></i>Save Changes
                    </button>
                </div>
            </div>

            <!-- Delete Form - Outside main form -->
            <form action="{{ auth()->user()->isAdmin() ? route('admin.exams.destroy', $exam->id) : route('teacher.exams.destroy', $exam->id) }}" method="POST" class="mb-4 sm:mb-6">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Are you sure you want to delete this exam? This action cannot be undone.')" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 border border-red-300 dark:border-red-600 text-red-700 dark:text-red-300 rounded-lg font-medium hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200 text-sm sm:text-base flex items-center justify-center sm:justify-start">
                    <i class="fas fa-trash mr-1 sm:mr-2 text-xs sm:text-sm"></i>Delete Exam
                </button>
            </form>

            <!-- Main Exam Form -->
            <form action="{{ auth()->user()->isAdmin() ? route('admin.exams.update', $exam->id) : route('teacher.exams.update', $exam->id) }}" method="POST" id="exam-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="is_draft" id="is_draft" value="0">
                <input type="hidden" name="selected_bank_questions" id="selected_bank_questions" value="{{ $exam->questions->where('is_bank_question', true)->pluck('id')->implode(',') }}">

                <!-- Dynamic route for question bank -->
                <input type="hidden" id="question-bank-route" value="{{ auth()->user()->isAdmin() ? route('admin.exams.question-bank') : route('teacher.exams.question-bank') }}">

                <!-- Question edit route -->
                <input type="hidden" id="question-edit-route" value="{{ auth()->user()->isAdmin() ? route('admin.questions.edit', 'QUESTION_ID') : route('teacher.questions.edit', 'QUESTION_ID') }}">

                <!-- Container for bank question points data -->
                <div id="bank-questions-data-container"></div>

                <!-- Container for custom questions data -->
                <div id="custom-questions-data-container"></div>

                <!-- Exam Details Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm sm:shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4">Exam Information</h2>

                    <div class="grid grid-cols-1 gap-4 sm:gap-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Exam Title *</label>
                            <input type="text" id="title" name="title" class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="{{ old('title', $exam->title) }}" required>
                            @error('title')
                            <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Subject *</label>
                                <select id="subject_id" name="subject_id" class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id', $exam->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Class *</label>
                                <select id="class_id" name="class_id" class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id', $exam->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Exam Type</label>
                                <select id="type" name="type" class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="quiz" {{ old('type', $exam->type) == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                    <option value="midterm" {{ old('type', $exam->type) == 'midterm' ? 'selected' : '' }}>Midterm Exam</option>
                                    <option value="end_of_term" {{ old('type', $exam->type) == 'end_of_term' ? 'selected' : '' }}>Final Exam</option>
                                    <option value="practice" {{ old('type', $exam->type) == 'practice' ? 'selected' : '' }}>Practice Test</option>
                                    <option value="mock" {{ old('type', $exam->type) == 'mock' ? 'selected' : '' }}>Mock Exam</option>
                                </select>
                                @error('type')
                                <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:gap-6">
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Description</label>
                                <textarea id="description" name="description" rows="3" class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Exam description...">{{ old('description', $exam->description) }}</textarea>
                                @error('description')
                                <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="instructions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Instructions</label>
                                <textarea id="instructions" name="instructions" rows="3" class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Exam instructions...">{{ old('instructions', $exam->instructions) }}</textarea>
                                @error('instructions')
                                <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timing & Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm sm:shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4">Timing & Settings</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6">
                        <div class="sm:col-span-2 lg:col-span-1">
                            <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Start Time *</label>
                            <input type="datetime-local" id="start_time" name="start_time" class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="{{ old('start_time', $exam->start_time ? $exam->start_time->format('Y-m-d\TH:i') : '') }}" required>
                            @error('start_time')
                            <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2 lg:col-span-1">
                            <label for="end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">End Time *</label>
                            <input type="datetime-local" id="end_time" name="end_time" class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="{{ old('end_time', $exam->end_time ? $exam->end_time->format('Y-m-d\TH:i') : '') }}" required>
                            @error('end_time')
                            <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Duration (minutes) *</label>
                            <input type="number" id="duration" name="duration" class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="120" value="{{ old('duration', $exam->duration) }}" required>
                            @error('duration')
                            <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="total_marks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Total Points *</label>
                            <input type="number" id="total_marks" name="total_marks" class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="100" value="{{ old('total_marks', $exam->total_marks) }}" required>
                            @error('total_marks')
                            <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="passing_marks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Passing Marks *</label>
                            <input type="number" id="passing_marks" name="passing_marks" class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="40" value="{{ old('passing_marks', $exam->passing_marks) }}" required>
                            @error('passing_marks')
                            <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="max_attempts" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Max Attempts *</label>
                            <input type="number" id="max_attempts" name="max_attempts" class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="1" value="{{ old('max_attempts', $exam->max_attempts) }}" required>
                            @error('max_attempts')
                            <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm sm:shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4">Security & Proctoring</h2>

                    <div class="grid grid-cols-1 gap-4 sm:gap-6">
                        <div class="space-y-3 sm:space-y-4">
                            <h3 class="text-base sm:text-lg font-medium text-gray-900 dark:text-white">Anti-Cheating Measures</h3>
                            <div class="space-y-2 sm:space-y-3">
                                <label class="flex items-center text-sm sm:text-base">
                                    <input type="checkbox" name="randomize_questions" value="1" class="rounded text-blue-600 focus:ring-blue-500 w-4 h-4 sm:w-5 sm:h-5" {{ old('randomize_questions', $exam->randomize_questions) ? 'checked' : '' }}>
                                    <span class="ml-2 sm:ml-3 text-gray-700 dark:text-gray-300">Randomize question order</span>
                                </label>
                                <label class="flex items-center text-sm sm:text-base">
                                    <input type="checkbox" name="require_fullscreen" value="1" class="rounded text-blue-600 focus:ring-blue-500 w-4 h-4 sm:w-5 sm:h-5" {{ old('require_fullscreen', $exam->require_fullscreen) ? 'checked' : '' }}>
                                    <span class="ml-2 sm:ml-3 text-gray-700 dark:text-gray-300">Require fullscreen mode</span>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-3 sm:space-y-4">
                            <h3 class="text-base sm:text-lg font-medium text-gray-900 dark:text-white">Results & Visibility</h3>
                            <div class="space-y-2 sm:space-y-3">
                                <label class="flex items-center text-sm sm:text-base">
                                    <input type="checkbox" name="show_results" value="1" class="rounded text-blue-600 focus:ring-blue-500 w-4 h-4 sm:w-5 sm:h-5" {{ old('show_results', $exam->show_results ?? true) ? 'checked' : '' }}>
                                    <span class="ml-2 sm:ml-3 text-gray-700 dark:text-gray-300">Show results to students</span>
                                </label>
                                <label class="flex items-center text-sm sm:text-base">
                                    <input type="checkbox" name="is_published" value="1" class="rounded text-blue-600 focus:ring-blue-500 w-4 h-4 sm:w-5 sm:h-5" {{ old('is_published', $exam->is_published) ? 'checked' : '' }}>
                                    <span class="ml-2 sm:ml-3 text-gray-700 dark:text-gray-300">Publish exam immediately</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm sm:shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-0 mb-4 sm:mb-6">
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">Exam Questions</h2>
                        <div class="flex flex-col xs:flex-row gap-2 sm:gap-3">
                            <!-- Question Bank Button -->
                            <button type="button" id="open-question-bank-btn" class="bg-purple-600 hover:bg-purple-700 text-white px-3 sm:px-4 py-2 rounded-lg font-medium transition-colors duration-200 text-sm sm:text-base flex items-center justify-center">
                                <i class="fas fa-database mr-1 sm:mr-2 text-xs sm:text-sm"></i>Add from Bank
                            </button>
                            <!-- Add New Question Button -->
                            <button type="button" id="add-question-btn" class="bg-green-600 hover:bg-green-700 text-white px-3 sm:px-4 py-2 rounded-lg font-medium transition-colors duration-200 text-sm sm:text-base flex items-center justify-center">
                                <i class="fas fa-plus mr-1 sm:mr-2 text-xs sm:text-sm"></i>Add New Question
                            </button>
                        </div>
                    </div>

                    <!-- Question Tabs -->
                    <div class="mb-4 sm:mb-6">
                        <div class="border-b border-gray-200 dark:border-gray-600">
                            <nav class="-mb-px flex space-x-2 sm:space-x-8 overflow-x-auto">
                                <button type="button" id="all-questions-tab" class="question-tab whitespace-nowrap py-2 px-2 sm:px-1 border-b-2 border-blue-500 font-medium text-xs sm:text-sm text-blue-600 dark:text-blue-400 flex items-center">
                                    <i class="fas fa-list-check mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                                    All Questions
                                    <span id="all-questions-count" class="ml-1 sm:ml-2 bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100 text-xs px-1 sm:px-2 py-0.5 sm:py-1 rounded-full">0</span>
                                </button>
                                <button type="button" id="bank-questions-tab" class="question-tab whitespace-nowrap py-2 px-2 sm:px-1 border-b-2 border-transparent font-medium text-xs sm:text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 flex items-center">
                                    <i class="fas fa-database mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                                    From Bank
                                    <span id="bank-questions-count" class="ml-1 sm:ml-2 bg-purple-100 dark:bg-purple-800 text-purple-800 dark:text-purple-100 text-xs px-1 sm:px-2 py-0.5 sm:py-1 rounded-full">0</span>
                                </button>
                                <button type="button" id="custom-questions-tab" class="question-tab whitespace-nowrap py-2 px-2 sm:px-1 border-b-2 border-transparent font-medium text-xs sm:text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 flex items-center">
                                    <i class="fas fa-pen mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                                    Custom
                                    <span id="custom-questions-count" class="ml-1 sm:ml-2 bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 text-xs px-1 sm:px-2 py-0.5 sm:py-1 rounded-full">0</span>
                                </button>
                                <button type="button" id="preview-questions-tab" class="question-tab whitespace-nowrap py-2 px-2 sm:px-1 border-b-2 border-transparent font-medium text-xs sm:text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 flex items-center">
                                    <i class="fas fa-eye mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                                    Preview
                                    <span id="preview-count" class="ml-1 sm:ml-2 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100 text-xs px-1 sm:px-2 py-0.5 sm:py-1 rounded-full">0</span>
                                </button>
                            </nav>
                        </div>
                    </div>

                    <!-- All Questions Panel -->
                    <div id="all-questions-panel" class="question-panel active">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mr-2 sm:mr-3 mt-0.5 text-sm sm:text-base"></i>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-medium text-blue-800 dark:text-blue-300">Editing Instructions</h4>
                                    <p class="text-xs sm:text-sm text-blue-700 dark:text-blue-400 mt-1">
                                        • <strong>Bank Questions</strong> (purple badge): Can only change points. Edit the original in Question Bank.
                                        <br>• <strong>Custom Questions</strong> (green badge): Fully editable - modify text, options, and points.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div id="all-questions-container" class="space-y-3 sm:space-y-4">
                            <!-- All questions will be displayed here -->
                        </div>
                    </div>

                    <!-- Bank Questions Panel -->
                    <div id="bank-questions-panel" class="question-panel hidden">
                        <div id="bank-questions-container" class="space-y-3 sm:space-y-4">
                            <!-- Only bank questions will be displayed here -->
                        </div>
                    </div>

                    <!-- Custom Questions Panel -->
                    <div id="custom-questions-panel" class="question-panel hidden">
                        <div id="custom-questions-container" class="space-y-3 sm:space-y-4">
                            <!-- Only custom questions will be displayed here -->
                        </div>
                        <button type="button" id="add-another-custom-question" class="w-full py-3 sm:py-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center justify-center mt-3 sm:mt-4 text-sm sm:text-base">
                            <i class="fas fa-plus mr-1 sm:mr-2 text-xs sm:text-sm"></i>Add Another Custom Question
                        </button>
                    </div>

                    <!-- Preview Questions Panel -->
                    <div id="preview-questions-panel" class="question-panel hidden">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 sm:p-4 mb-4 sm:mb-6">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mr-2 sm:mr-3 mt-0.5 text-sm sm:text-base"></i>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-medium text-blue-800 dark:text-blue-300">Exam Preview</h4>
                                    <p class="text-xs sm:text-sm text-blue-700 dark:text-blue-400 mt-1">This is how your students will see the exam. All questions from both the question bank and custom ones are shown here.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Validation Alerts Container -->
                        <div id="validation-alerts-container" class="mb-4 sm:mb-6"></div>

                        <div id="preview-questions-container" class="space-y-4 sm:space-y-6 mb-4 sm:mb-6">
                            <!-- Preview questions will appear here -->
                            <div id="no-questions-preview" class="text-center py-8 sm:py-12 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                                <i class="fas fa-eye-slash text-2xl sm:text-4xl text-gray-400 dark:text-gray-500 mb-3 sm:mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400 text-sm sm:text-base">No questions to preview</p>
                                <p class="text-xs sm:text-sm text-gray-400 dark:text-gray-500 mt-1">Add questions from the bank or create custom ones to see the preview</p>
                            </div>
                        </div>

                        <!-- Preview Summary -->
                        <div id="preview-summary" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 sm:p-4 hidden">
                            <h4 class="text-base sm:text-lg font-medium text-gray-900 dark:text-white mb-2 sm:mb-3">Exam Summary</h4>
                            <div class="grid grid-cols-2 gap-3 sm:gap-4 text-xs sm:text-sm">
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Total Questions:</span>
                                    <span id="preview-total-questions" class="font-medium ml-1 sm:ml-2">0</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Total Points:</span>
                                    <span id="preview-total-points" class="font-medium ml-1 sm:ml-2">0</span>
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <span class="text-gray-600 dark:text-gray-400">Question Types:</span>
                                    <span id="preview-question-types" class="font-medium ml-1 sm:ml-2">-</span>
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                    <span id="preview-status" class="font-medium ml-1 sm:ml-2 text-green-600 dark:text-green-400">Ready</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 sm:gap-4">
                    <button type="button" id="save-draft-btn" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 text-sm sm:text-base">
                        Save as Draft
                    </button>
                    <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-medium transition-colors duration-200 text-sm sm:text-base">
                        Update Exam
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Question Bank Modal -->
    <div id="question-bank-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-4 sm:top-10 lg:top-20 mx-auto p-3 sm:p-5 border w-11/12 sm:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-xl bg-white dark:bg-gray-800 max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex justify-between items-center mb-4 sm:mb-6 flex-shrink-0">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Question Bank</h3>
                <button type="button" id="close-question-bank" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl sm:text-2xl"></i>
                </button>
            </div>

            <!-- Filters -->
            <div class="bg-gray-50 dark:bg-gray-700 p-3 sm:p-4 rounded-lg mb-4 sm:mb-6 flex-shrink-0">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Subject</label>
                        <select id="bank-subject-filter" class="w-full px-2 sm:px-3 py-2 text-xs sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Type</label>
                        <select id="bank-type-filter" class="w-full px-2 sm:px-3 py-2 text-xs sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                            <option value="">All Types</option>
                            <option value="mcq">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="short_answer">Short Answer</option>
                            <option value="essay">Essay</option>
                            <option value="fill_blank">Fill in Blanks</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Difficulty</label>
                        <select id="bank-difficulty-filter" class="w-full px-2 sm:px-3 py-2 text-xs sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                            <option value="">All Levels</option>
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 sm:mb-2">Search</label>
                        <input type="text" id="bank-search" placeholder="Search questions..." class="w-full px-2 sm:px-3 py-2 text-xs sm:text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- Question Bank Content -->
            <div class="mb-4 sm:mb-6 flex-1 overflow-hidden flex flex-col">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-0 mb-3 sm:mb-4 flex-shrink-0">
                    <span id="bank-results-count" class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Loading questions...</span>
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        <label class="flex items-center text-xs sm:text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" id="select-all-questions" class="rounded text-blue-600 focus:ring-blue-500 mr-1 sm:mr-2 w-3 h-3 sm:w-4 sm:h-4">
                            Select All
                        </label>
                        <button type="button" id="load-bank-questions" class="bg-blue-600 hover:bg-blue-700 text-white px-2 sm:px-4 py-1 sm:py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-sync-alt mr-1 sm:mr-2 text-xs"></i>Refresh
                        </button>
                    </div>
                </div>

                <div id="question-bank-content" class="space-y-3 sm:space-y-4 overflow-y-auto flex-1 p-1 sm:p-2">
                    <!-- Questions will be loaded here via AJAX -->
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-6 w-6 sm:h-8 sm:w-8 border-b-2 border-blue-600 mx-auto"></div>
                        <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm">Loading questions...</p>
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 pt-3 sm:pt-4 border-t border-gray-200 dark:border-gray-600 flex-shrink-0">
                <button type="button" id="cancel-bank-selection" class="w-full sm:w-auto px-4 sm:px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 text-sm sm:text-base">
                    Cancel
                </button>
                <button type="button" id="add-selected-questions" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white px-4 sm:px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center text-sm sm:text-base">
                    <i class="fas fa-plus mr-1 sm:mr-2 text-xs sm:text-sm"></i>
                    Add Selected
                    <span id="selected-bank-count" class="ml-1 sm:ml-2 bg-green-500 text-white text-xs px-1 sm:px-2 py-0.5 sm:py-1 rounded-full">0</span>
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
            // Using string keys for Maps to avoid type mismatch between DOM dataset (strings) and numeric ids
            const state = {
                selectedQuestions: new Map(), // keys: String(questionId)  -> bank questions currently selected/attached
                customQuestions: new Map(),   // keys: String(index)
                currentTab: 'all',
                existingQuestions: existingQuestions || [],

                // UI pagination / bank cache
                bankCache: [],                // cached bank questions (array)
                bankFilters: { subject: '', type: '', difficulty: '', q: '' }
            };

            // Initialize with existing questions
            initializeExistingQuestions(existingQuestions);

            // Set up event listeners & initial UI
            initializeEventListeners();
            updateQuestionCounts();
            updatePreview();

            // ---------------------
            // Initialization helpers
            // ---------------------
            function initializeExistingQuestions(questions) {
                if (!questions || questions.length === 0) return;

                questions.forEach((question, index) => {
                    const questionData = {
                        id: question.id,
                        type: question.type,
                        difficulty: question.difficulty,
                        subject: question.subject?.name || 'Unknown',
                        question_text: question.question_text,
                        points: question.pivot?.points ?? question.points,
                        options: question.options ? question.options.map(opt => ({
                            text: opt.option_text,
                            is_correct: opt.is_correct
                        })) : [],
                        details: question.details || {},
                        is_bank_question: question.is_bank_question || false
                    };

                    if (question.is_bank_question) {
                        state.selectedQuestions.set(String(question.id), questionData);
                    } else {
                        // Use index as key for custom questions (string)
                        state.customQuestions.set(String(index), questionData);
                    }
                });

                updateQuestionDisplays();
            }

            // ---------------------
            // Display / Renderers
            // ---------------------
            function updateQuestionDisplays() {
                updateAllQuestionsDisplay();
                updateBankQuestionsDisplay();
                updateCustomQuestionsDisplay();
                updateSelectedBankQuestionsInput();
                updateCustomQuestionsInput();
            }

            function updateAllQuestionsDisplay() {
                const container = document.getElementById('all-questions-container');
                if (!container) return;
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
                if (!container) return;
                container.innerHTML = '';

                state.selectedQuestions.forEach((question, questionId) => {
                    const questionElement = createBankQuestionEditElement(question, questionId);
                    container.appendChild(questionElement);
                });
            }

            function updateCustomQuestionsDisplay() {
                const container = document.getElementById('custom-questions-container');
                if (!container) return;
                container.innerHTML = '';

                state.customQuestions.forEach((question, index) => {
                    const questionElement = createCustomQuestionEditElement(question, index);
                    container.appendChild(questionElement);
                });

                // Attach event handlers inside newly added custom question elements
                container.querySelectorAll('.custom-question-edit-item').forEach(item => {
                    const addOptBtn = item.querySelector('.add-option-btn');
                    if (addOptBtn) {
                        addOptBtn.addEventListener('click', function() {
                            const qIndex = item.getAttribute('data-question-id');
                            handleAddOption(item, qIndex);
                        });
                    }

                    const removeBtn = item.querySelector('.remove-question-btn');
                    if (removeBtn) {
                        removeBtn.addEventListener('click', function() {
                            const qIndex = item.getAttribute('data-question-id');
                            state.customQuestions.delete(String(qIndex));
                            updateQuestionDisplays();
                            updatePreview();
                        });
                    }
                });
            }

            function createBankQuestionEditElement(question, questionId) {
                const template = document.getElementById('bank-question-edit-template');
                if (!template) return document.createElement('div');

                const questionElement = template.content.cloneNode(true);
                const item = questionElement.querySelector('.bank-question-edit-item');
                if (!item) return document.createElement('div');

                item.setAttribute('data-question-id', String(questionId));

                const typeBadge = item.querySelector('.question-type-badge');
                if (typeBadge) typeBadge.textContent = getQuestionTypeLabel(question.type);

                const subjectEl = item.querySelector('.question-subject');
                if (subjectEl) subjectEl.textContent = question.subject || '';

                const textEl = item.querySelector('.question-text');
                if (textEl) textEl.textContent = question.question_text || '';

                const pointsInput = item.querySelector('.bank-question-points');
                if (pointsInput) {
                    pointsInput.value = question.points ?? 0;
                    pointsInput.addEventListener('input', function(e) {
                        const qid = item.getAttribute('data-question-id');
                        if (state.selectedQuestions.has(String(qid))) {
                            state.selectedQuestions.get(String(qid)).points = e.target.value;
                            updatePreview();
                            updateSelectedBankQuestionsInput();
                        }
                    });
                }

                const idSpan = item.querySelector('.question-id span');
                if (idSpan) idSpan.textContent = questionId;

                // Add a remove button to detach bank question
                let removeBtn = item.querySelector('.remove-bank-question-btn');
                if (!removeBtn) {
                    removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-bank-question-btn text-red-600 mt-2 text-xs sm:text-sm';
                    removeBtn.textContent = 'Remove from Exam';
                    const rightCol = item.querySelector('.w-40');
                    if (rightCol) rightCol.appendChild(removeBtn);
                    else item.appendChild(removeBtn);
                }
                removeBtn.addEventListener('click', function() {
                    const qid = String(item.getAttribute('data-question-id'));
                    if (state.selectedQuestions.has(qid)) {
                        state.selectedQuestions.delete(qid);
                        updateQuestionDisplays();
                        updatePreview();
                    }
                });

                // Update edit link to open actual question editor
                const editLink = item.querySelector('a');
                if (editLink) {
                    // Get the edit route template and replace QUESTION_ID with actual ID
                    const editRouteTemplate = document.getElementById('question-edit-route').value;
                    const editUrl = editRouteTemplate.replace('QUESTION_ID', questionId);
                    editLink.href = editUrl;
                    editLink.target = '_blank'; // Open in new tab
                    editLink.addEventListener('click', function(e) {
                        // Allow default behavior (navigation to edit page)
                    });
                }

                // Add options preview for MCQ questions
                if (question.type === 'mcq' && question.options && question.options.length > 0) {
                    const optionsContainer = item.querySelector('.question-options');
                    if (optionsContainer) {
                        optionsContainer.innerHTML = '';
                        question.options.forEach((option, idx) => {
                            const optionDiv = document.createElement('div');
                            optionDiv.className = 'flex items-center text-xs sm:text-sm';
                            optionDiv.innerHTML = `
                        <span class="w-2 h-2 rounded-full ${option.is_correct ? 'bg-green-500' : 'bg-gray-300'} mr-2"></span>
                        <span class="${option.is_correct ? 'text-green-600 dark:text-green-400 font-medium' : ''}">
                            ${option.text || ''}
                        </span>
                    `;
                            optionsContainer.appendChild(optionDiv);
                        });
                    }
                }

                return questionElement;
            }

            function createCustomQuestionEditElement(question, index) {
                const template = document.getElementById('custom-question-edit-template');
                if (!template) return document.createElement('div');

                const questionElement = template.content.cloneNode(true);
                const item = questionElement.querySelector('.custom-question-edit-item');
                if (!item) return document.createElement('div');

                const key = String(index);
                item.setAttribute('data-question-id', key);

                const qNumberEl = item.querySelector('.question-number');
                if (qNumberEl) qNumberEl.textContent = Number(index) + 1;

                // Type select
                const typeSelect = item.querySelector('.custom-question-type');
                if (typeSelect) {
                    typeSelect.value = question.type || 'mcq';
                    typeSelect.setAttribute('name', `questions[${key}][type]`);
                    typeSelect.addEventListener('change', function() {
                        const newType = this.value;
                        handleCustomQuestionType(item, { type: newType, options: question.options || [], details: question.details || {} }, key);
                    });
                }

                // Points input
                const pointsInput = item.querySelector('.custom-question-points');
                if (pointsInput) {
                    pointsInput.value = question.points ?? 0;
                    pointsInput.setAttribute('name', `questions[${key}][points]`);
                    pointsInput.addEventListener('input', function(e) {
                        const qk = item.getAttribute('data-question-id');
                        if (state.customQuestions.has(String(qk))) {
                            state.customQuestions.get(String(qk)).points = e.target.value;
                            updatePreview();
                        }
                    });
                }

                // Question text textarea
                const questionText = item.querySelector('.custom-question-text') || item.querySelector('textarea');
                if (questionText) {
                    questionText.value = question.question_text || '';
                    questionText.setAttribute('name', `questions[${key}][question_text]`);
                    questionText.addEventListener('input', function(e) {
                        const qk = item.getAttribute('data-question-id');
                        if (state.customQuestions.has(String(qk))) {
                            state.customQuestions.get(String(qk)).question_text = e.target.value;
                            updatePreview();
                        }
                    });
                }

                // Include question ID for existing questions
                if (question.id) {
                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = `questions[${key}][id]`;
                    idInput.value = question.id;
                    item.appendChild(idInput);
                }

                // Add options and other fields based on type
                handleCustomQuestionType(item, question, key);

                // Hook up add/remove option buttons
                const addOptBtn = item.querySelector('.add-option-btn');
                if (addOptBtn) {
                    addOptBtn.addEventListener('click', function() {
                        handleAddOption(item, key);
                    });
                }

                return questionElement;
            }

            // ---------------------
            // Custom question helpers
            // ---------------------
            function handleAddOption(container, qKey) {
                const optionGroup = container.querySelector('.option-group');
                if (!optionGroup) return;

                const currentOptions = state.customQuestions.get(String(qKey))?.options || [];
                const nextIndex = currentOptions.length;

                if (!state.customQuestions.has(String(qKey))) {
                    state.customQuestions.set(String(qKey), {
                        type: 'mcq',
                        points: 0,
                        question_text: '',
                        options: []
                    });
                }
                const questionModel = state.customQuestions.get(String(qKey));
                questionModel.options = questionModel.options || [];
                questionModel.options.push({ text: '', is_correct: nextIndex === 0 });

                updateCustomQuestionsDisplay();
                updatePreview();
            }

            function handleCustomQuestionType(container, question, index) {
                const allContainers = container.querySelectorAll('.custom-options-container');
                allContainers.forEach(c => c.classList.add('hidden'));

                const type = question.type || 'mcq';
                const targetContainer = container.querySelector(`.custom-options-container[data-type="${type}"]`);
                if (targetContainer) targetContainer.classList.remove('hidden');

                switch (type) {
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
                if (!optionGroup) return;
                optionGroup.innerHTML = '';

                const options = Array.isArray(question.options) ? question.options : [];

                if (options.length < 2) {
                    while (options.length < 2) {
                        options.push({ text: '', is_correct: options.length === 0 });
                    }
                }

                options.forEach((option, optIndex) => {
                    const optionDiv = document.createElement('div');
                    optionDiv.className = 'flex items-center option-item space-x-2';
                    optionDiv.innerHTML = `
                <input type="radio" name="questions[${index}][correct_answer]" value="${optIndex}"
                       class="text-blue-600 w-4 h-4" ${option.is_correct ? 'checked' : ''}>
                <input type="text" name="questions[${index}][options][${optIndex}]"
                       class="flex-1 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                       value="${option.text || ''}" placeholder="Option ${String.fromCharCode(65 + optIndex)}">
                <button type="button" class="remove-option text-red-600 hover:text-red-800 ${options.length <= 2 ? 'disabled:opacity-50 disabled:cursor-not-allowed' : ''}"
                        ${options.length <= 2 ? 'disabled' : ''}>
                    <i class="fas fa-times text-sm"></i>
                </button>
            `;

                    const removeBtn = optionDiv.querySelector('.remove-option');
                    if (removeBtn) {
                        removeBtn.addEventListener('click', function() {
                            const qModel = state.customQuestions.get(String(index));
                            if (qModel && Array.isArray(qModel.options) && qModel.options.length > optIndex) {
                                qModel.options.splice(optIndex, 1);
                                if (!qModel.options.some(o => o.is_correct)) qModel.options[0].is_correct = true;
                                updateCustomQuestionsDisplay();
                                updatePreview();
                            }
                        });
                    }

                    const textInput = optionDiv.querySelector('input[type="text"]');
                    if (textInput) {
                        textInput.addEventListener('input', function(e) {
                            const qModel = state.customQuestions.get(String(index));
                            if (qModel) {
                                qModel.options = qModel.options || [];
                                qModel.options[optIndex] = qModel.options[optIndex] || { text: '', is_correct: false };
                                qModel.options[optIndex].text = e.target.value;
                                updatePreview();
                            }
                        });
                    }

                    const radio = optionDiv.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.addEventListener('change', function(e) {
                            const qModel = state.customQuestions.get(String(index));
                            if (qModel && Array.isArray(qModel.options)) {
                                qModel.options.forEach((o, i) => o.is_correct = (i === optIndex));
                                updatePreview();
                            }
                        });
                    }

                    optionGroup.appendChild(optionDiv);
                });

                if (!state.customQuestions.has(String(index))) {
                    state.customQuestions.set(String(index), {
                        type: 'mcq',
                        points: question.points || 0,
                        question_text: question.question_text || '',
                        options: options
                    });
                } else {
                    const existing = state.customQuestions.get(String(index));
                    existing.options = options;
                }
            }

            function populateTrueFalseOptions(container, question, index) {
                const trueRadio = container.querySelector('input[value="true"]');
                const falseRadio = container.querySelector('input[value="false"]');

                if (trueRadio) trueRadio.setAttribute('name', `questions[${index}][correct_answer]`);
                if (falseRadio) falseRadio.setAttribute('name', `questions[${index}][correct_answer]`);

                if (question.correct_answer === 'true' && trueRadio) {
                    trueRadio.checked = true;
                } else if (falseRadio) {
                    falseRadio.checked = true;
                }

                if (!state.customQuestions.has(String(index))) {
                    state.customQuestions.set(String(index), {
                        type: 'true_false',
                        points: question.points || 0,
                        question_text: question.question_text || '',
                        details: question.details || {}
                    });
                }
            }

            function populateShortAnswerOptions(container, question, index) {
                const expectedAnswer = container.querySelector('.short-answer-expected');
                if (!expectedAnswer) return;
                expectedAnswer.setAttribute('name', `questions[${index}][expected_answer]`);
                expectedAnswer.value = (question.details && question.details.expected_answer) || '';
                expectedAnswer.addEventListener('input', function(e) {
                    const qModel = state.customQuestions.get(String(index));
                    if (qModel) {
                        qModel.details = qModel.details || {};
                        qModel.details.expected_answer = e.target.value;
                    }
                });

                if (!state.customQuestions.has(String(index))) {
                    state.customQuestions.set(String(index), {
                        type: 'short_answer',
                        points: question.points || 0,
                        question_text: question.question_text || '',
                        details: question.details || {}
                    });
                }
            }

            function populateEssayOptions(container, question, index) {
                const gradingRubric = container.querySelector('.essay-rubric');
                if (!gradingRubric) return;
                gradingRubric.setAttribute('name', `questions[${index}][grading_rubric]`);
                gradingRubric.value = (question.details && question.details.grading_rubric) || '';
                gradingRubric.addEventListener('input', function(e) {
                    const qModel = state.customQuestions.get(String(index));
                    if (qModel) {
                        qModel.details = qModel.details || {};
                        qModel.details.grading_rubric = e.target.value;
                    }
                });

                if (!state.customQuestions.has(String(index))) {
                    state.customQuestions.set(String(index), {
                        type: 'essay',
                        points: question.points || 0,
                        question_text: question.question_text || '',
                        details: question.details || {}
                    });
                }
            }

            function populateFillBlankOptions(container, question, index) {
                const blankQuestion = container.querySelector('.fill-blank-question');
                const blankAnswers = container.querySelector('.fill-blank-answers');

                if (blankQuestion) {
                    blankQuestion.setAttribute('name', `questions[${index}][blank_question]`);
                    blankQuestion.value = (question.details && question.details.blank_question) || '';
                    blankQuestion.addEventListener('input', function(e) {
                        const qModel = state.customQuestions.get(String(index));
                        if (qModel) {
                            qModel.details = qModel.details || {};
                            qModel.details.blank_question = e.target.value;
                        }
                    });
                }
                if (blankAnswers) {
                    blankAnswers.setAttribute('name', `questions[${index}][blank_answers]`);
                    blankAnswers.value = Array.isArray(question.details?.blank_answers) ? question.details.blank_answers.join(', ') : (question.details?.blank_answers || '');
                    blankAnswers.addEventListener('input', function(e) {
                        const qModel = state.customQuestions.get(String(index));
                        if (qModel) {
                            qModel.details = qModel.details || {};
                            qModel.details.blank_answers = e.target.value.split(',').map(s => s.trim());
                        }
                    });
                }

                if (!state.customQuestions.has(String(index))) {
                    state.customQuestions.set(String(index), {
                        type: 'fill_blank',
                        points: question.points || 0,
                        question_text: question.question_text || '',
                        details: question.details || {}
                    });
                }
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

                const allCountEl = document.getElementById('all-questions-count');
                const bankCountEl = document.getElementById('bank-questions-count');
                const customCountEl = document.getElementById('custom-questions-count');
                const previewCountEl = document.getElementById('preview-count');

                if (allCountEl) allCountEl.textContent = allCount;
                if (bankCountEl) bankCountEl.textContent = bankCount;
                if (customCountEl) customCountEl.textContent = customCount;
                if (previewCountEl) previewCountEl.textContent = allCount;
            }

            // ---------------------
            // Hidden inputs sync
            // ---------------------
            function updateSelectedBankQuestionsInput() {
                const selectedIds = Array.from(state.selectedQuestions.keys());
                const el = document.getElementById('selected_bank_questions');
                if (el) el.value = selectedIds.join(',');

                const container = document.getElementById('bank-questions-data-container');
                if (!container) return;
                container.innerHTML = '';

                state.selectedQuestions.forEach((question, questionId) => {
                    const pointsInput = document.createElement('input');
                    pointsInput.type = 'hidden';
                    pointsInput.name = `bank_question_points[${questionId}]`;
                    pointsInput.value = question.points ?? 0;
                    container.appendChild(pointsInput);
                });
            }

            function updateCustomQuestionsInput() {
                const container = document.getElementById('custom-questions-data-container');
                if (!container) return;
                container.innerHTML = '';
            }

            // ---------------------
            // Preview
            // ---------------------
            function updatePreview() {
                const container = document.getElementById('preview-questions-container');
                const noQuestions = document.getElementById('no-questions-preview');
                if (!container || !noQuestions) return;

                container.innerHTML = '';
                container.appendChild(noQuestions);

                const totalQuestions = state.selectedQuestions.size + state.customQuestions.size;

                if (totalQuestions === 0) {
                    noQuestions.classList.remove('hidden');
                    const summary = document.getElementById('preview-summary');
                    if (summary) summary.classList.add('hidden');
                    return;
                }

                noQuestions.classList.add('hidden');
                const summary = document.getElementById('preview-summary');
                if (summary) summary.classList.remove('hidden');

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
                const totalQEl = document.getElementById('preview-total-questions');
                const totalPointsEl = document.getElementById('preview-total-points');
                const typesEl = document.getElementById('preview-question-types');

                if (totalQEl) totalQEl.textContent = totalQuestions;
                if (totalPointsEl) totalPointsEl.textContent = totalPoints;
                if (typesEl) typesEl.textContent = Array.from(questionTypes).map(type => getQuestionTypeLabel(type)).join(', ');

                // Update status
                const statusElement = document.getElementById('preview-status');
                const validationResults = validateExam();

                if (!statusElement) return;

                if (!validationResults.isValid) {
                    statusElement.textContent = 'Errors Found';
                    statusElement.className = 'font-medium ml-1 sm:ml-2 text-red-600 dark:text-red-400';
                } else if (validationResults.warnings.length > 0) {
                    statusElement.textContent = 'Warnings';
                    statusElement.className = 'font-medium ml-1 sm:ml-2 text-yellow-600 dark:text-yellow-400';
                } else {
                    statusElement.textContent = 'Ready';
                    statusElement.className = 'font-medium ml-1 sm:ml-2 text-green-600 dark:text-green-400';
                }

                // Display validation results
                displayValidationResults(validationResults);
            }

            function createPreviewQuestion(question, questionNumber, source, id) {
                const template = document.getElementById('preview-question-template');
                if (!template) return document.createElement('div');

                const questionElement = template.content.cloneNode(true);
                const item = questionElement.querySelector('.preview-question-item');
                if (!item) return document.createElement('div');

                const qTypeBadge = item.querySelector('.question-type-badge');
                if (qTypeBadge) qTypeBadge.textContent = getQuestionTypeLabel(question.type);

                const diff = item.querySelector('.difficulty-badge');
                if (diff) diff.textContent = question.difficulty || 'Not set';

                const src = item.querySelector('.question-source');
                if (src) src.textContent = source === 'bank' ? 'From Bank' : 'Custom';

                const pts = item.querySelector('.question-points');
                if (pts) pts.textContent = `${question.points ?? 0} points`;

                const num = item.querySelector('.question-number');
                if (num) num.textContent = questionNumber;

                const text = item.querySelector('.question-text');
                if (text) text.textContent = question.question_text || '[Question text not entered]';

                const answerArea = item.querySelector('.answer-area');
                if (answerArea) answerArea.classList.remove('hidden');

                const answerInput = item.querySelector('.answer-input');
                if (!answerInput) return item;

                answerInput.innerHTML = '';

                if (question.type === 'mcq') {
                    const optionsContainer = item.querySelector('.question-options');
                    if (optionsContainer) optionsContainer.classList.remove('hidden');

                    if (question.options && question.options.length > 0) {
                        question.options.forEach((option, index) => {
                            if (option && option.text) {
                                const answerOption = document.createElement('div');
                                answerOption.className = 'flex items-center mb-2';
                                answerOption.innerHTML = `
                            <input type="radio" name="student_answer_${source}_${id}" value="${index}" class="mr-2 sm:mr-3 text-blue-600 w-4 h-4">
                            <span class="text-sm">${String.fromCharCode(65 + index)}. ${option.text}</span>
                        `;
                                answerInput.appendChild(answerOption);
                            }
                        });
                    } else {
                        answerInput.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-sm">No options defined</p>';
                    }
                } else if (question.type === 'true_false') {
                    answerInput.innerHTML = `
                <div class="space-y-2">
                    <label class="flex items-center text-sm">
                        <input type="radio" name="student_answer_${source}_${id}" value="true" class="mr-2 sm:mr-3 text-blue-600 w-4 h-4">
                        <span>True</span>
                    </label>
                    <label class="flex items-center text-sm">
                        <input type="radio" name="student_answer_${source}_${id}" value="false" class="mr-2 sm:mr-3 text-blue-600 w-4 h-4">
                        <span>False</span>
                    </label>
                </div>
            `;
                } else if (question.type === 'short_answer') {
                    answerInput.innerHTML = `<textarea class="w-full px-3 py-2 border rounded text-sm" rows="2" placeholder="Type your short answer here..."></textarea>`;
                } else if (question.type === 'essay') {
                    answerInput.innerHTML = `<textarea class="w-full px-3 py-2 border rounded text-sm" rows="4" placeholder="Write your essay answer here..."></textarea>`;
                } else if (question.type === 'fill_blank') {
                    answerInput.innerHTML = `<input type="text" class="w-full px-3 py-2 border rounded text-sm" placeholder="Enter your answer...">`;
                }

                return questionElement;
            }

            function calculateTotalPoints() {
                let total = 0;

                state.selectedQuestions.forEach(question => {
                    total += parseInt(question.points) || 0;
                });

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
                const examTotalMarksEl = document.getElementById('total_marks');
                const examTotalMarks = examTotalMarksEl ? (parseInt(examTotalMarksEl.value) || 0) : 0;

                if (totalQuestions === 0) {
                    validationResults.errors.push({
                        type: 'no_questions',
                        message: 'Exam must have at least one question',
                        severity: 'error'
                    });
                    validationResults.isValid = false;
                }

                if (examTotalMarks > 0 && totalPoints > 0) {
                    const pointsDifference = Math.abs(totalPoints - examTotalMarks);
                    const pointsRatio = pointsDifference / examTotalMarks;

                    if (pointsRatio > 0.1) {
                        validationResults.warnings.push({
                            type: 'points_mismatch',
                            message: `Total questions points (${totalPoints}) don't match exam total marks (${examTotalMarks})`,
                            severity: 'warning'
                        });
                    }
                }

                const longQuestions = checkLongQuestions();
                if (longQuestions.length > 0) {
                    validationResults.warnings.push({
                        type: 'long_questions',
                        message: `${longQuestions.length} question(s) may be too long`,
                        severity: 'warning',
                        details: longQuestions
                    });
                }

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

                state.customQuestions.forEach((question, index) => {
                    if (question.question_text && question.question_text.length > 500) {
                        longQuestions.push({
                            source: 'custom',
                            number: index,
                            length: question.question_text.length,
                            preview: question.question_text.substring(0, 100) + '...'
                        });
                    }
                });

                return longQuestions;
            }

            function checkIncompleteQuestions() {
                const incomplete = [];

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
                        if (!question.details || !question.details.blank_question || !question.details.blank_question.includes('[blank]')) {
                            issues.push('Fill blank question must contain [blank] placeholder');
                        }
                    }

                    if (issues.length > 0) {
                        incomplete.push({
                            questionNumber: index,
                            type: question.type,
                            issues: issues
                        });
                    }
                });

                return incomplete;
            }

            function displayValidationResults(validationResults) {
                const container = document.getElementById('validation-alerts-container');
                if (!container) return;
                container.innerHTML = '';

                validationResults.errors.forEach(error => {
                    const alert = createValidationAlert(error, 'error');
                    container.appendChild(alert);
                });

                validationResults.warnings.forEach(warning  => {
                    const alert = createValidationAlert(warning, 'warning');
                    container.appendChild(alert);
                });
            }

            function createValidationAlert(validation, type) {
                const alert = document.createElement('div');
                alert.className = `validation-alert p-3 sm:p-4 rounded-lg mb-3 sm:mb-4 ${
                    type === 'error'
                        ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800'
                        : 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800'
                }`;

                const icon = type === 'error' ? 'fa-exclamation-circle' : 'fa-exclamation-triangle';
                const title = type === 'error' ? 'Error' : 'Warning';

                alert.innerHTML = `
            <div class="flex items-start">
                <i class="fas ${icon} mt-0.5 mr-2 sm:mr-3 text-sm ${
                    type === 'error' ? 'text-red-600 dark:text-red-400' : 'text-yellow-600 dark:text-yellow-400'
                }"></i>
                <div class="flex-1">
                    <h4 class="text-xs sm:text-sm font-medium ${
                    type === 'error' ? 'text-red-800 dark:text-red-300' : 'text-yellow-800 dark:text-yellow-300'
                }">${title}</h4>
                    <p class="text-xs sm:text-sm ${
                    type === 'error' ? 'text-red-700 dark:text-red-400' : 'text-yellow-700 dark:text-yellow-400'
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
                    } else if (detail.questionNumber !== undefined) {
                        return `<li>Question ${detail.questionNumber} (${detail.type}): ${Array.isArray(detail.issues) ? detail.issues.join(', ') : detail.issue}</li>`;
                    }
                    return `<li>${detail.preview || detail.issue}</li>`;
                }).join('')}
                </ul>
            </div>
        `;
            }

            // ---------------------
            // Event listeners
            // ---------------------
            function initializeEventListeners() {
                // Tab switching
                document.getElementById('all-questions-tab')?.addEventListener('click', () => switchTab('all'));
                document.getElementById('bank-questions-tab')?.addEventListener('click', () => switchTab('bank'));
                document.getElementById('custom-questions-tab')?.addEventListener('click', () => switchTab('custom'));
                document.getElementById('preview-questions-tab')?.addEventListener('click', () => switchTab('preview'));

                // Add question buttons
                document.getElementById('add-question-btn')?.addEventListener('click', addNewCustomQuestion);
                document.getElementById('add-another-custom-question')?.addEventListener('click', addNewCustomQuestion);

                // Open question bank buttons
                document.getElementById('open-question-bank-btn')?.addEventListener('click', openQuestionBank);

                // Modal controls
                document.getElementById('close-question-bank')?.addEventListener('click', closeQuestionBank);
                document.getElementById('cancel-bank-selection')?.addEventListener('click', closeQuestionBank);

                // Load bank questions (refresh)
                document.getElementById('load-bank-questions')?.addEventListener('click', function() {
                    loadQuestionBank();
                });

                // Select-all checkbox in modal
                document.getElementById('select-all-questions')?.addEventListener('change', function(e) {
                    toggleSelectAll(e.target.checked);
                });

                // Add selected questions from modal to state
                document.getElementById('add-selected-questions')?.addEventListener('click', function() {
                    addSelectedFromModal();
                });

                // Save draft
                document.getElementById('save-draft-btn')?.addEventListener('click', function() {
                    document.getElementById('is_draft').value = '1';
                    document.getElementById('exam-form').submit();
                });

                // Real-time preview updates when total marks changed
                document.getElementById('total_marks')?.addEventListener('input', updatePreview);

                // Filter changes in modal
                document.getElementById('bank-subject-filter')?.addEventListener('change', function(e) {
                    state.bankFilters.subject = e.target.value;
                    loadQuestionBank();
                });

                document.getElementById('bank-type-filter')?.addEventListener('change', function(e) {
                    state.bankFilters.type = e.target.value;
                    loadQuestionBank();
                });

                document.getElementById('bank-difficulty-filter')?.addEventListener('change', function(e) {
                    state.bankFilters.difficulty = e.target.value;
                    loadQuestionBank();
                });

                document.getElementById('bank-search')?.addEventListener('input', function(e) {
                    state.bankFilters.q = e.target.value;
                    loadQuestionBank();
                });

                // Delegated input listener for dynamic content
                document.addEventListener('input', function(e) {
                    if (e.target.classList.contains('bank-question-points')) {
                        const questionItem = e.target.closest('.bank-question-edit-item');
                        if (!questionItem) return;
                        const questionId = String(questionItem.getAttribute('data-question-id'));
                        if (state.selectedQuestions.has(questionId)) {
                            state.selectedQuestions.get(questionId).points = e.target.value;
                        }
                        updatePreview();
                        updateSelectedBankQuestionsInput();
                    }

                    if (e.target.classList.contains('custom-question-points')) {
                        const questionItem = e.target.closest('.custom-question-edit-item');
                        if (!questionItem) return;
                        const qid = questionItem.getAttribute('data-question-id');
                        if (qid && state.customQuestions.has(String(qid))) {
                            state.customQuestions.get(String(qid)).points = e.target.value;
                        }
                        updatePreview();
                    }
                });

                // Delegated click for remove bank question buttons
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.remove-bank-question-btn')) {
                        const btn = e.target.closest('.remove-bank-question-btn');
                        const item = btn.closest('[data-question-id]');
                        if (item) {
                            const qid = String(item.getAttribute('data-question-id'));
                            if (state.selectedQuestions.has(qid)) {
                                state.selectedQuestions.delete(qid);
                                updateQuestionDisplays();
                                updatePreview();
                            }
                        }
                    }
                });
            }

            // ---------------------
            // Question Bank: load, render, select management
            // ---------------------
            async function loadQuestionBank() {
                const routeEl = document.getElementById('question-bank-route');
                if (!routeEl) return Promise.reject('Missing route');

                const url = routeEl.value;
                const params = new URLSearchParams();
                if (state.bankFilters.subject) params.append('subject', state.bankFilters.subject);
                if (state.bankFilters.type) params.append('type', state.bankFilters.type);
                if (state.bankFilters.difficulty) params.append('difficulty', state.bankFilters.difficulty);
                if (state.bankFilters.q) params.append('q', state.bankFilters.q);

                const content = document.getElementById('question-bank-content');
                const count = document.getElementById('bank-results-count');
                if (content) content.innerHTML = `<div class="text-center py-8"><div class="animate-spin rounded-full h-6 w-6 sm:h-8 sm:w-8 border-b-2 border-blue-600 mx-auto"></div><p class="text-gray-500 dark:text-gray-400 mt-2 text-sm">Loading questions...</p></div>`;
                if (count) count.textContent = 'Loading questions...';

                try {
                    const response = await fetch(`${url}?${params.toString()}`, {
                        credentials: 'same-origin',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!response.ok) throw new Error('Failed to fetch bank questions');

                    const json = await response.json();
                    state.bankCache = Array.isArray(json) ? json : (json.data || []);
                    renderQuestionBankContent();
                    return Promise.resolve();
                } catch (err) {
                    if (content) content.innerHTML = `<div class="text-center py-6"><p class="text-red-500 text-sm">Failed to load questions</p></div>`;
                    if (count) count.textContent = 'Failed to load';
                    return Promise.reject(err);
                }
            }

            function renderQuestionBankContent() {
                const container = document.getElementById('question-bank-content');
                const count = document.getElementById('bank-results-count');
                if (!container) return;

                container.innerHTML = '';
                const questions = state.bankCache || [];

                if (count) count.textContent = `${questions.length} question(s) found`;

                if (questions.length === 0) {
                    container.innerHTML = `<div class="text-center py-8"><p class="text-gray-500 dark:text-gray-400 text-sm">No questions found</p></div>`;
                    return;
                }

                questions.forEach(q => {
                    const item = document.createElement('div');
                    item.className = 'bank-item p-3 border rounded flex justify-between items-start';

                    const left = document.createElement('div');
                    left.className = 'flex-1 pr-3';

                    const checkboxWrap = document.createElement('label');
                    checkboxWrap.className = 'flex items-start space-x-2';
                    const cb = document.createElement('input');
                    cb.type = 'checkbox';
                    cb.className = 'bank-select-checkbox w-4 h-4';
                    cb.setAttribute('data-question-id', String(q.id));
                    cb.value = String(q.id);

                    const title = document.createElement('div');
                    title.className = 'text-xs sm:text-sm';
                    title.innerHTML = `<strong>${getQuestionTypeLabel(q.type)}</strong> — ${q.question_text ? (q.question_text.length > 100 ? q.question_text.substring(0, 97) + '...' : q.question_text) : '[No text]'}<div class="text-xs text-gray-500 mt-1">${q.subject?.name || ''} • ${q.difficulty || 'N/A'}</div>`;

                    checkboxWrap.appendChild(cb);
                    checkboxWrap.appendChild(title);
                    left.appendChild(checkboxWrap);

                    const right = document.createElement('div');
                    right.className = 'w-20 sm:w-40 text-right';
                    const pts = document.createElement('div');
                    pts.className = 'text-xs sm:text-sm font-medium';
                    pts.textContent = `${q.points ?? 0} pts`;
                    right.appendChild(pts);

                    item.appendChild(left);
                    item.appendChild(right);
                    container.appendChild(item);
                });

                markBankCheckboxesFromState();
            }

            function markBankCheckboxesFromState() {
                const container = document.getElementById('question-bank-content');
                if (!container) return;

                container.querySelectorAll('.bank-select-checkbox').forEach(cb => {
                    const qid = String(cb.getAttribute('data-question-id'));
                    cb.checked = state.selectedQuestions.has(qid);
                });

                updateSelectedCountInModal();
            }

            function toggleSelectAll(checked) {
                const container = document.getElementById('question-bank-content');
                if (!container) return;

                container.querySelectorAll('.bank-select-checkbox').forEach(cb => {
                    cb.checked = !!checked;
                });

                updateSelectedCountInModal();
            }

            function updateSelectedCountInModal() {
                const container = document.getElementById('question-bank-content');
                const badge = document.getElementById('selected-bank-count');
                if (!container || !badge) return;

                const checkedBoxes = container.querySelectorAll('.bank-select-checkbox:checked');
                badge.textContent = String(checkedBoxes.length);
            }

            function addSelectedFromModal() {
                const container = document.getElementById('question-bank-content');
                if (!container) {
                    closeQuestionBank();
                    return;
                }

                const checkedBoxes = Array.from(container.querySelectorAll('.bank-select-checkbox:checked'));
                if (checkedBoxes.length === 0) {
                    closeQuestionBank();
                    return;
                }

                const bankMap = new Map((state.bankCache || []).map(q => [String(q.id), q]));

                checkedBoxes.forEach(cb => {
                    const qid = String(cb.getAttribute('data-question-id'));
                    if (!state.selectedQuestions.has(qid)) {
                        const q = bankMap.get(qid);
                        const model = {
                            id: q?.id ?? qid,
                            type: q?.type ?? 'mcq',
                            difficulty: q?.difficulty ?? null,
                            subject: q?.subject?.name ?? '',
                            question_text: q?.question_text ?? '',
                            points: q?.points ?? 0,
                            options: q?.options?.map(opt => ({ text: opt.option_text, is_correct: opt.is_correct })) || [],
                            details: q?.details || {},
                            is_bank_question: true
                        };
                        state.selectedQuestions.set(qid, model);
                    }
                });

                updateQuestionDisplays();
                updatePreview();
                closeQuestionBank();
            }

            // ---------------------
            // Modal open/close
            // ---------------------
            function openQuestionBank() {
                const modal = document.getElementById('question-bank-modal');
                if (modal) modal.classList.remove('hidden');
                loadQuestionBank().catch(() => {});
            }

            function closeQuestionBank() {
                const modal = document.getElementById('question-bank-modal');
                if (modal) modal.classList.add('hidden');
            }

            function switchTab(tabName) {
                state.currentTab = tabName;

                document.querySelectorAll('.question-tab').forEach(tab => {
                    tab.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                    tab.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                });

                const activeTab = document.getElementById(`${tabName}-questions-tab`);
                if (activeTab) {
                    activeTab.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                    activeTab.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                }

                document.querySelectorAll('.question-panel').forEach(panel => panel.classList.add('hidden'));
                const panel = document.getElementById(`${tabName}-questions-panel`);
                if (panel) panel.classList.remove('hidden');

                if (tabName === 'preview') {
                    updatePreview();
                }
            }

            // ---------------------
            // Add / Remove helpers
            // ---------------------
            function addNewCustomQuestion() {
                let next = 0;
                while (state.customQuestions.has(String(next))) next++;

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

                state.customQuestions.set(String(next), newQuestion);
                updateQuestionDisplays();
                updatePreview();
                switchTab('custom');
            }

            // Final safety: ensure the initial hidden input matches client-side state on load
            updateSelectedBankQuestionsInput();
            updateCustomQuestionsInput();
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

        /* Custom responsive improvements */
        @media (max-width: 640px) {
            .xs\:flex-row {
                flex-direction: row;
            }

            .question-tab {
                font-size: 0.75rem;
                padding: 0.5rem 0.5rem;
            }

            .bank-item {
                flex-direction: column;
                gap: 0.5rem;
            }

            .bank-item .w-20 {
                width: 100%;
                text-align: left;
            }
        }

        /* Improve touch targets for mobile */
        @media (max-width: 768px) {
            button,
            input[type="checkbox"],
            input[type="radio"],
            select {
                min-height: 44px;
                min-width: 44px;
            }

            .option-item input[type="text"] {
                min-height: 44px;
            }
        }
    </style>
@endsection
