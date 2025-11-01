@extends('layouts.app')

@section('title', 'Edit Question - Lincoln eLearning')

@section('content')
    @php
        $isAdmin = auth()->user()->isAdmin();
        $isTeacher = auth()->user()->isTeacher();

        // Determine routes based on user role
        $indexRoute = $isAdmin ? route('admin.questions.index') : route('teacher.questions.index');
        $updateRoute = $isAdmin ? route('admin.questions.update', $question) : route('teacher.questions.update', $question);

        $pageTitle = $isAdmin ? 'Edit Question' : 'Update My Question';
        $pageDescription = $isAdmin ? 'Update question details in the question bank' : 'Update your question details';
        $buttonText = $isAdmin ? 'Update Question' : 'Save Changes';
    @endphp

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Back Button -->
            <div class="mb-8">
                <a href="{{ $indexRoute }}"
                   class="inline-flex items-center px-5 py-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 hover:scale-105 hover:shadow-lg">
                    <i class="fas fa-arrow-left mr-3 text-blue-500"></i>
                    <span class="font-medium">Back to Questions</span>
                </a>
            </div>

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $pageTitle }}
                </h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">
                    {{ $pageDescription }}
                </p>

                @if($isTeacher)
                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                            <div class="text-sm text-blue-700 dark:text-blue-300">
                                <p class="font-medium">Editing Your Question</p>
                                <p class="mt-1">Changes will be reflected in all exams using this question.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-lg mb-6" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-400">
                                Please fix the following issues:
                            </h3>
                            <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Question Form -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                <form action="{{ $updateRoute }}" method="POST" id="question-form" class="ajax-form">
                    @csrf
                    @method('PUT')

                    <div class="p-8 space-y-8">
                        <!-- Basic Information Card -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                                Basic Information
                            </h3>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Subject -->
                                <div>
                                    <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        <span class="text-red-500">*</span> Subject
                                    </label>
                                    <select id="subject_id" name="subject_id" required
                                            class="w-full rounded-xl border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 @error('subject_id') border-red-500 @enderror">
                                        <option value="">Select a subject</option>
                                        @foreach($subjects as $subject)
                                            @if($isAdmin || in_array($subject->id, $teacherSubjectIds ?? []))
                                                <option value="{{ $subject->id }}" {{ old('subject_id', $question->subject_id) == $subject->id ? 'selected' : '' }}>
                                                    {{ $subject->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <!-- Question Type -->
                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        <span class="text-red-500">*</span> Question Type
                                    </label>
                                    <select id="type" name="type" required
                                            class="w-full rounded-xl border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                                        <option value="">Select question type</option>
                                        @foreach($questionTypes as $type)
                                            <option value="{{ $type }}" {{ old('type', $question->type) == $type ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $type)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <!-- Difficulty -->
                                <div>
                                    <label for="difficulty" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        <span class="text-red-500">*</span> Difficulty Level
                                    </label>
                                    <select id="difficulty" name="difficulty" required
                                            class="w-full rounded-xl border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                                        @foreach($difficulties as $difficulty)
                                            <option value="{{ $difficulty }}" {{ old('difficulty', $question->difficulty) == $difficulty ? 'selected' : '' }}>
                                                {{ ucfirst($difficulty) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Points -->
                                <div>
                                    <label for="points" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        <span class="text-red-500">*</span> Points
                                    </label>
                                    <input type="number" id="points" name="points" step="0.5" min="0.5" max="100"
                                           value="{{ old('points', $question->points) }}" required
                                           class="w-full rounded-xl border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 @error('points') border-red-500 @enderror"
                                           placeholder="Enter points (0.5 - 100)">
                                    @error('points')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Active Toggle -->
                            <div class="mt-6 flex items-center">
                                <div class="relative inline-block w-12 mr-3 align-middle select-none">
                                    <input type="checkbox" id="is_active" name="is_active" value="1"
                                           {{ old('is_active', $question->is_active) ? 'checked' : '' }}
                                           class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 border-gray-300 appearance-none cursor-pointer transition-all duration-200"/>
                                    <label for="is_active" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer transition-all duration-200"></label>
                                </div>
                                <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Active Question
                                </label>
                            </div>
                        </div>

                        <!-- Question Content Card -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                                <i class="fas fa-edit text-green-500 mr-3"></i>
                                Question Content
                            </h3>

                            <!-- Question Text -->
                            <div class="mb-6">
                                <label for="question_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    <span class="text-red-500">*</span> Question Text
                                </label>
                                <div class="relative">
                                    <textarea id="question_text" name="question_text" rows="4" required
                                              class="w-full rounded-xl border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 resize-none @error('question_text') border-red-500 @enderror"
                                              placeholder="Enter your question text here... (Minimum 10 characters)">{{ old('question_text', $question->question_text) }}</textarea>
                                    <div class="absolute bottom-3 right-3">
                                        <span id="question-text-count" class="text-xs px-2 py-1 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300"></span>
                                    </div>
                                </div>
                                @error('question_text')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                </p>
                                @enderror
                            </div>

                            <!-- Explanation -->
                            <div>
                                <label for="explanation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>Explanation (Optional)
                                </label>
                                <textarea id="explanation" name="explanation" rows="3"
                                          class="w-full rounded-xl border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 resize-none"
                                          placeholder="Provide an explanation for the correct answer (this helps students understand why it's correct)...">{{ old('explanation', $question->explanation) }}</textarea>
                            </div>
                        </div>

                        <!-- Dynamic Type-Specific Fields -->
                        <div id="type-specific-fields" class="transition-all duration-300">
                            <!-- Fields will be dynamically loaded here -->
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-8 py-6 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 rounded-b-xl">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-shield-alt mr-2"></i>
                                All fields marked with <span class="text-red-500">*</span> are required
                            </div>
                            <div class="flex space-x-4">
                                <a href="{{ $indexRoute }}"
                                   class="inline-flex items-center px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 hover:scale-105 font-medium">
                                    <i class="fas fa-times mr-2"></i>
                                    Cancel
                                </a>
                                <button type="submit"
                                        class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-semibold transition-all duration-200 hover:scale-105 shadow-lg hover:shadow-xl">
                                    <i class="fas fa-save mr-2"></i>
                                    {{ $buttonText }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .toggle-checkbox:checked {
            right: 0;
            border-color: #3b82f6;
            background-color: #3b82f6;
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #3b82f6;
        }
        .option-correct {
            border-color: #10b981 !important;
            background-color: #f0fdf4 !important;
        }
        .option-default {
            border-color: #d1d5db !important;
            background-color: #ffffff !important;
        }
        .dark .option-default {
            border-color: #4b5563 !important;
            background-color: #1f2937 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeQuestionTypeWithData(@json($question));
            initializeAjaxForms();
            initializeCharacterCount();
            initializeToggleSwitch();
        });

        function initializeQuestionTypeWithData(question) {
            const typeSelect = document.getElementById('type');
            const container = document.getElementById('type-specific-fields');

            if (typeSelect) {
                // Load initial state
                updateTypeFields(typeSelect.value, question, container);

                typeSelect.addEventListener('change', function() {
                    updateTypeFields(this.value, question, container);
                });
            }
        }

        function updateTypeFields(type, question, container) {
            let html = '';

            switch(type) {
                case 'mcq':
                    html = createMCQFields(question);
                    break;
                case 'true_false':
                    html = createTrueFalseFields(question);
                    break;
                case 'short_answer':
                    html = createShortAnswerFields(question);
                    break;
                case 'essay':
                    html = createEssayFields(question);
                    break;
                case 'fill_blank':
                    html = createFillBlankFields(question);
                    break;
                default:
                    html = createDefaultMessage();
                    break;
            }

            container.innerHTML = html;

            // Initialize any dynamic functionality for the new fields
            if (type === 'mcq') {
                initializeMCQOptions();
            }
        }

        function createMCQFields(question) {
            const options = question.options || [];
            const correctAnswerIndex = findCorrectAnswerIndex(options);

            return `
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-list-ol text-purple-500 mr-3"></i>
                        Multiple Choice Options
                    </h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center mb-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Add at least 2 options and select the correct one
                            </p>
                            <button type="button" onclick="addMCQOption()"
                                    class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-all duration-200">
                                <i class="fas fa-plus mr-2"></i>
                                Add Option
                            </button>
                        </div>

                        <div id="mcq-options" class="space-y-3">
                            ${options.map((option, index) => `
                                <div class="option-item option-default flex items-center space-x-3 p-4 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl transition-all duration-200 hover:scale-[1.02]" data-option-id="${index}">
                                    <div class="flex items-center justify-center w-6 h-6 rounded-full border-2 border-gray-400 dark:border-gray-500 cursor-pointer option-radio transition-all duration-200" data-option-id="${index}">
                                        <div class="w-3 h-3 rounded-full ${index === correctAnswerIndex ? 'bg-emerald-500 ring-4 ring-emerald-200' : 'bg-gray-300 dark:bg-gray-600'} transition-all duration-200"></div>
                                    </div>
                                    <input type="text"
                                           name="options[${index}][text]"
                                           value="${option.option_text || option.text || ''}"
                                           placeholder="Enter option ${index + 1}..."
                                           class="flex-1 bg-transparent border-none focus:ring-0 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-sm font-medium option-input"
                                           required>
                                    <input type="hidden" name="options[${index}][is_correct]" value="${index === correctAnswerIndex ? '1' : '0'}" class="correct-input">
                                    ${index >= 2 ? `
                                    <button type="button" onclick="removeMCQOption(${index})"
                                            class="text-gray-400 hover:text-red-500 transition-colors duration-200 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                    ` : ''}
                                </div>
                            `).join('')}
                        </div>

                        <div id="mcq-error" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                                <div>
                                    <p class="text-sm font-medium text-red-800 dark:text-red-400" id="mcq-error-text"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function createTrueFalseFields(question) {
            const correctAnswer = question.correct_answer;
            return `
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        True/False Configuration
                    </h3>

                    <div class="max-w-md">
                        <label for="correct_answer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            <span class="text-red-500">*</span> Correct Answer
                        </label>
                        <select id="correct_answer" name="correct_answer" required
                                class="w-full rounded-xl border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                            <option value="true" ${correctAnswer === 'true' || correctAnswer === true ? 'selected' : ''}>True</option>
                            <option value="false" ${correctAnswer === 'false' || correctAnswer === false ? 'selected' : ''}>False</option>
                        </select>
                    </div>
                </div>
            `;
        }

        function createShortAnswerFields(question) {
            const expectedAnswer = question.correct_answer || question.details?.expected_answer || '';
            return `
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-pencil-alt text-orange-500 mr-3"></i>
                        Short Answer Configuration
                    </h3>

                    <div>
                        <label for="correct_answer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            <span class="text-red-500">*</span> Expected Answer
                        </label>
                        <input type="text" id="correct_answer" name="correct_answer" required
                               value="${expectedAnswer}"
                               class="w-full rounded-xl border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                               placeholder="Enter the expected short answer...">
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            This helps with automated grading.
                        </p>
                    </div>
                </div>
            `;
        }

        function createEssayFields(question) {
            const gradingRubric = question.details?.grading_rubric || '';
            return `
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-file-alt text-indigo-500 mr-3"></i>
                        Essay Question Configuration
                    </h3>

                    <div>
                        <label for="grading_rubric" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Grading Rubric (Optional)
                        </label>
                        <textarea id="grading_rubric" name="details[grading_rubric]" rows="4"
                                  class="w-full rounded-xl border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 resize-none"
                                  placeholder="Provide grading criteria and expectations for this essay question...">${gradingRubric}</textarea>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            This helps maintain consistent grading standards.
                        </p>
                    </div>
                </div>
            `;
        }

        function createFillBlankFields(question) {
            const blankQuestion = question.question_text || '';
            const blankAnswers = question.correct_answer || question.details?.blank_answers?.join(', ') || '';
            return `
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-tasks text-teal-500 mr-3"></i>
                        Fill in the Blanks Configuration
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label for="question_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                <span class="text-red-500">*</span> Question with Blanks
                            </label>
                            <input type="text" id="question_text" name="question_text" required
                                   value="${blankQuestion}"
                                   class="w-full rounded-xl border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                                   placeholder="e.g., The capital of France is ______ and it's known for the ______.">
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Use underscores (______) to indicate blank spaces.
                            </p>
                        </div>

                        <div>
                            <label for="correct_answer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                <span class="text-red-500">*</span> Correct Answers
                            </label>
                            <input type="text" id="correct_answer" name="correct_answer" required
                                   value="${blankAnswers}"
                                   class="w-full rounded-xl border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                                   placeholder="e.g., Paris,Eiffel Tower">
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Enter answers in order, separated by commas.
                            </p>
                        </div>
                    </div>
                </div>
            `;
        }

        function createDefaultMessage() {
            return `
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 text-center">
                    <i class="fas fa-mouse-pointer text-blue-500 text-3xl mb-3"></i>
                    <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-2">
                        Select a Question Type
                    </h3>
                    <p class="text-blue-600 dark:text-blue-400">
                        Choose a question type from the dropdown above to see specific configuration options.
                    </p>
                </div>
            `;
        }

        // Helper function to find the correct answer index for MCQ
        function findCorrectAnswerIndex(options) {
            for (let i = 0; i < options.length; i++) {
                if (options[i].is_correct || options[i].correct) {
                    return i;
                }
            }
            return 0;
        }

        // Rest of your JavaScript functions (initializeMCQOptions, setCorrectOption, etc.)
        // ... include all the JavaScript functions from the previous improved version
        // including initializeMCQOptions, addMCQOption, removeMCQOption, setCorrectOption,
        // initializeCharacterCount, initializeAjaxForms, handleAjaxFormSubmit, etc.

        // Initialize MCQ Options Management
        let optionCount = 0;

        function initializeMCQOptions() {
            const optionsContainer = document.getElementById('mcq-options');
            optionCount = optionsContainer.children.length;

            // Initialize click handlers for existing options
            initializeOptionClickHandlers();
        }

        function addMCQOption() {
            if (optionCount >= 6) {
                showMCQError('Maximum 6 options allowed');
                return;
            }

            const optionsContainer = document.getElementById('mcq-options');
            const optionId = optionCount;

            const optionHTML = `
                <div class="option-item option-default flex items-center space-x-3 p-4 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl transition-all duration-200 hover:scale-[1.02]" data-option-id="${optionId}">
                    <div class="flex items-center justify-center w-6 h-6 rounded-full border-2 border-gray-400 dark:border-gray-500 cursor-pointer option-radio transition-all duration-200" data-option-id="${optionId}">
                        <div class="w-3 h-3 rounded-full bg-gray-300 dark:bg-gray-600 transition-all duration-200"></div>
                    </div>
                    <input type="text"
                           name="options[${optionId}][text]"
                           placeholder="Enter option ${optionId + 1}..."
                           class="flex-1 bg-transparent border-none focus:ring-0 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-sm font-medium option-input"
                           required>
                    <input type="hidden" name="options[${optionId}][is_correct]" value="0" class="correct-input">
                    <button type="button" onclick="removeMCQOption(${optionId})"
                            class="text-gray-400 hover:text-red-500 transition-colors duration-200 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </div>
            `;

            optionsContainer.insertAdjacentHTML('beforeend', optionHTML);
            optionCount++;

            initializeOptionClickHandlers();
            hideMCQError();
        }

        function removeMCQOption(optionId) {
            if (optionCount <= 2) {
                showMCQError('Minimum 2 options required');
                return;
            }

            const optionElement = document.querySelector(`[data-option-id="${optionId}"]`);
            if (optionElement) {
                optionElement.remove();
                optionCount--;

                // Re-index remaining options
                reindexMCQOptions();
                hideMCQError();
            }
        }

        function reindexMCQOptions() {
            const optionItems = document.querySelectorAll('.option-item');
            optionItems.forEach((item, index) => {
                const newId = index;
                item.setAttribute('data-option-id', newId);

                const radio = item.querySelector('.option-radio');
                radio.setAttribute('data-option-id', newId);

                const textInput = item.querySelector('.option-input');
                textInput.name = `options[${newId}][text]`;
                textInput.placeholder = `Enter option ${newId + 1}...`;

                const hiddenInput = item.querySelector('.correct-input');
                hiddenInput.name = `options[${newId}][is_correct]`;

                // Update remove button if needed
                const removeBtn = item.querySelector('button');
                if (removeBtn) {
                    removeBtn.setAttribute('onclick', `removeMCQOption(${newId})`);
                }
            });
        }

        function initializeOptionClickHandlers() {
            document.querySelectorAll('.option-radio').forEach(radio => {
                radio.addEventListener('click', function() {
                    const optionId = this.getAttribute('data-option-id');
                    setCorrectOption(optionId);
                });
            });
        }

        function setCorrectOption(optionId) {
            // Reset all options
            document.querySelectorAll('.option-item').forEach(item => {
                item.classList.remove('option-correct');
                item.classList.add('option-default');
                const radio = item.querySelector('.option-radio div');
                radio.classList.remove('bg-emerald-500', 'ring-4', 'ring-emerald-200');
                radio.classList.add('bg-gray-300', 'dark:bg-gray-600');

                const hiddenInput = item.querySelector('.correct-input');
                hiddenInput.value = '0';
            });

            // Set selected option as correct
            const selectedOption = document.querySelector(`[data-option-id="${optionId}"]`);
            if (selectedOption) {
                selectedOption.classList.remove('option-default');
                selectedOption.classList.add('option-correct');
                const radio = selectedOption.querySelector('.option-radio div');
                radio.classList.remove('bg-gray-300', 'dark:bg-gray-600');
                radio.classList.add('bg-emerald-500', 'ring-4', 'ring-emerald-200');

                const hiddenInput = selectedOption.querySelector('.correct-input');
                hiddenInput.value = '1';
            }

            hideMCQError();
        }

        function showMCQError(message) {
            const errorDiv = document.getElementById('mcq-error');
            const errorText = document.getElementById('mcq-error-text');
            errorText.textContent = message;
            errorDiv.classList.remove('hidden');
        }

        function hideMCQError() {
            const errorDiv = document.getElementById('mcq-error');
            errorDiv.classList.add('hidden');
        }

        function initializeCharacterCount() {
            const questionText = document.getElementById('question_text');
            if (questionText) {
                const charCount = document.getElementById('question-text-count');

                questionText.addEventListener('input', function() {
                    const length = this.value.length;
                    charCount.textContent = `${length} characters`;

                    if (length < 10) {
                        charCount.classList.remove('bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');
                        charCount.classList.add('bg-red-100', 'text-red-800');
                    } else {
                        charCount.classList.remove('bg-red-100', 'text-red-800', 'bg-green-100', 'text-green-800');
                        charCount.classList.add('bg-green-100', 'text-green-800');
                    }
                });

                // Trigger initial count
                questionText.dispatchEvent(new Event('input'));
            }
        }

        function initializeToggleSwitch() {
            const toggle = document.getElementById('is_active');
            if (toggle) {
                toggle.addEventListener('change', function() {
                    // Toggle-specific logic here
                });
            }
        }

        // AJAX form handling functions
        function initializeAjaxForms() {
            document.querySelectorAll('.ajax-form').forEach(form => {
                form.removeEventListener('submit', handleAjaxFormSubmit);
                form.addEventListener('submit', handleAjaxFormSubmit);
            });
        }

        function handleAjaxFormSubmit(e) {
            e.preventDefault();
            e.stopPropagation();

            const form = e.target;
            const type = document.getElementById('type')?.value;

            // Validate MCQ options before submission
            if (type === 'mcq' && !validateMCQOptions()) {
                return;
            }

            const url = form.action;
            const method = form.method;
            const formData = new FormData(form);

            showFormLoading(form, true);

            fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.error || 'Form submission failed');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        setTimeout(() => {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                window.location.reload();
                            }
                        }, 1500);
                    } else {
                        throw new Error(data.error || 'Form submission failed');
                    }
                })
                .catch(error => {
                    console.error('Form submission failed:', error);
                    showNotification(error.message || 'Failed to update question. Please try again.', 'error');
                    showFormLoading(form, false);
                });
        }

        function validateMCQOptions() {
            const options = document.querySelectorAll('.option-item');
            if (options.length < 2) {
                showMCQError('At least 2 options are required for MCQ questions.');
                return false;
            }

            let hasCorrectOption = false;
            options.forEach(option => {
                const hiddenInput = option.querySelector('.correct-input');
                if (hiddenInput.value === '1') {
                    hasCorrectOption = true;
                }
            });

            if (!hasCorrectOption) {
                showMCQError('Please select the correct answer by clicking on the circle next to the option.');
                return false;
            }

            // Check if all options have text
            let allOptionsFilled = true;
            options.forEach(option => {
                const textInput = option.querySelector('.option-input');
                if (!textInput.value.trim()) {
                    allOptionsFilled = false;
                    textInput.classList.add('border-red-500');
                }
            });

            if (!allOptionsFilled) {
                showMCQError('All options must have text.');
                return false;
            }

            hideMCQError();
            return true;
        }

        function showFormLoading(form, isLoading) {
            const submitButton = form.querySelector('button[type="submit"]');
            const cancelButton = form.querySelector('a[href]');

            if (isLoading) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating...';
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');

                if (cancelButton) {
                    cancelButton.classList.add('opacity-50', 'cursor-not-allowed');
                    cancelButton.onclick = (e) => e.preventDefault();
                }

                const inputs = form.querySelectorAll('input, select, textarea, button');
                inputs.forEach(input => {
                    input.disabled = true;
                });
            } else {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-save mr-2"></i>' +
                    (window.location.pathname.includes('/admin/') ? 'Update Question' : 'Save Changes');
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');

                if (cancelButton) {
                    cancelButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    cancelButton.onclick = null;
                }

                const inputs = form.querySelectorAll('input, select, textarea, button');
                inputs.forEach(input => {
                    input.disabled = false;
                });
            }
        }

        function showNotification(message, type = 'success') {
            const existingNotifications = document.querySelectorAll('.ajax-notification');
            existingNotifications.forEach(notification => notification.remove());

            const notification = document.createElement('div');
            notification.className = `ajax-notification fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-lg transform transition-all duration-300 translate-x-full opacity-0 ${
                type === 'success'
                    ? 'bg-green-500 text-white'
                    : 'bg-red-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center space-x-3">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} text-lg"></i>
                    <span class="font-medium">${message}</span>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.remove('translate-x-full', 'opacity-0');
                notification.classList.add('translate-x-0', 'opacity-100');
            }, 10);

            setTimeout(() => {
                notification.classList.remove('translate-x-0', 'opacity-100');
                notification.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 5000);
        }
    </script>
@endsection
