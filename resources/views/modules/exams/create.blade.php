@extends('layouts.app')

@section('title', 'Admin Dashboard - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create New Exam</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">Set up a comprehensive examination for your students</p>
            </div>

            <form action="{{ route('admin.exams.store') }}" method="POST" id="exam-form">
                @csrf
                {{-- Hidden field to handle 'Save as Draft' functionality --}}
                <input type="hidden" name="is_draft" id="is_draft" value="0">

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

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Exam Questions</h2>
                        <button type="button" id="add-question-btn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                            <i class="fas fa-plus mr-2"></i>Add Question
                        </button>
                    </div>

                    <div id="questions-container">
                    </div>

                    <button type="button" id="add-another-question" class="w-full py-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i>Add Another Question
                    </button>
                </div>

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

    <template id="question-template">
        <div class="question-item border border-gray-200 dark:border-gray-600 rounded-lg p-4 mb-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Question <span class="question-number">1</span></h3>
                <button type="button" class="remove-question text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Type</label>
                    <select name="questions[0][type]" class="question-type w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="true_false">True/False</option>
                        <option value="short_answer">Short Answer</option>
                        <option value="essay">Essay</option>
                        <option value="fill_blank">Fill in the Blanks</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Points</label>
                    <input type="number" name="questions[0][points]" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="5" min="1">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Text</label>
                <textarea name="questions[0][question_text]" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" rows="3" placeholder="Enter your question here..." required></textarea>
            </div>

            <div class="options-container space-y-3" data-type="multiple_choice">
                {{-- IMPORTANT: Added option-group wrapper for JS functions --}}
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
                        <button type="button" class="remove-option ml-2 text-red-600 hover:text-red-800 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="add-option text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center mt-2">
                    <i class="fas fa-plus mr-1"></i> Add Option
                </button>
            </div>

            <div class="options-container hidden" data-type="true_false">
                <div class="space-y-3">
                    {{-- Note: Renamed name to avoid collision with multiple_choice radio names --}}
                    <label class="flex items-center">
                        <input type="radio" name="questions[0][correct_answer_tf]" value="true" class="mr-3 text-blue-600" checked>
                        <span class="text-gray-700 dark:text-gray-300">True</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="questions[0][correct_answer_tf]" value="false" class="mr-3 text-blue-600">
                        <span class="text-gray-700 dark:text-gray-300">False</span>
                    </label>
                </div>
            </div>

            <div class="options-container hidden" data-type="short_answer">
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-300">Students will provide a short written answer to this question.</p>
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expected Answer (for grading reference)</label>
                        <textarea name="questions[0][expected_answer]" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white" rows="2" placeholder="Provide a sample answer for grading reference..."></textarea>
                    </div>
                </div>
            </div>

            <div class="options-container hidden" data-type="essay">
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-300">Students will write an essay in response to this question.</p>
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Grading Rubric/Instructions</label>
                        <textarea name="questions[0][grading_rubric]" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white" rows="3" placeholder="Provide grading criteria or instructions for the essay..."></textarea>
                    </div>
                </div>
            </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let questionCount = 0;
            const questionsContainer = document.getElementById('questions-container');
            const questionTemplate = document.getElementById('question-template');
            const addQuestionBtn = document.getElementById('add-question-btn');
            const addAnotherQuestionBtn = document.getElementById('add-another-question');
            const saveDraftBtn = document.getElementById('save-draft-btn');
            const isDraftInput = document.getElementById('is_draft');
            const examForm = document.getElementById('exam-form');

            // --- Core Functions ---

            function updateQuestionNumbers() {
                const questions = questionsContainer.querySelectorAll('.question-item');
                questionCount = questions.length;

                questions.forEach((question, index) => {
                    const questionNumber = index + 1;
                    question.querySelector('.question-number').textContent = questionNumber;

                    // Update all input names with the new index
                    const inputs = question.querySelectorAll('[name]');
                    inputs.forEach(input => {
                        const oldName = input.getAttribute('name');
                        // Use a regex to replace the old index ([0]) with the current index
                        const newName = oldName.replace(/\[\d+\]/, `[${index}]`);
                        input.setAttribute('name', newName);
                    });

                    // Update option numbering inside the question if it's a multiple choice
                    const typeSelect = question.querySelector('.question-type');
                    if (typeSelect.value === 'multiple_choice') {
                        updateOptionRadios(question);
                    }
                });
            }

            function addQuestion() {
                const newQuestion = questionTemplate.content.cloneNode(true);
                const questionElement = newQuestion.querySelector('.question-item');

                questionsContainer.appendChild(questionElement);

                // Initial update to set the correct question index/number
                updateQuestionNumbers();
            }

            function handleQuestionTypeChange(target) {
                const questionItem = target.closest('.question-item');
                const selectedType = target.value;
                const optionsContainers = questionItem.querySelectorAll('.options-container');

                // Hide all options containers
                optionsContainers.forEach(container => {
                    container.classList.add('hidden');
                });

                // Show the appropriate options container
                const targetContainer = questionItem.querySelector(`.options-container[data-type="${selectedType}"]`);
                if (targetContainer) {
                    targetContainer.classList.remove('hidden');
                }
            }

            // --- Multiple Choice Option Management ---

            function getQuestionIndex(questionElement) {
                const questionNumberText = questionElement.querySelector('.question-number').textContent;
                return parseInt(questionNumberText) - 1;
            }

            function addOptionToQuestion(questionElement) {
                const optionGroup = questionElement.querySelector('.option-group');
                const options = optionGroup.querySelectorAll('.option-item');
                const optionCount = options.length;
                const questionIndex = getQuestionIndex(questionElement);

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
                updateOptionRadios(questionElement);
            }

            function removeOptionFromQuestion(questionElement, removeButton) {
                const optionGroup = questionElement.querySelector('.option-group');
                const options = optionGroup.querySelectorAll('.option-item');

                if (options.length <= 2) {
                    alert('Multiple choice questions must have at least 2 options.');
                    return;
                }

                const optionToRemove = removeButton.closest('.option-item');
                if (optionToRemove) {
                    // Check if the removed option was the checked one
                    const wasChecked = optionToRemove.querySelector('input[type="radio"]').checked;
                    optionToRemove.remove();

                    // If the removed option was checked, check the first remaining option
                    if (wasChecked) {
                        const remainingOptions = optionGroup.querySelectorAll('.option-item');
                        if (remainingOptions.length > 0) {
                            remainingOptions[0].querySelector('input[type="radio"]').checked = true;
                        }
                    }

                    updateOptionRadios(questionElement);
                }
            }

            function updateOptionRadios(questionElement) {
                const optionGroup = questionElement.querySelector('.option-group');
                if (!optionGroup) return;

                const options = optionGroup.querySelectorAll('.option-item');
                const questionIndex = getQuestionIndex(questionElement);

                options.forEach((option, index) => {
                    const radio = option.querySelector('input[type="radio"]');
                    const textInput = option.querySelector('input[type="text"]');
                    const removeBtn = option.querySelector('.remove-option');

                    // Re-index radio and text input names/values
                    if (radio) {
                        radio.value = index;
                        radio.name = `questions[${questionIndex}][correct_answer]`;
                    }
                    if (textInput) {
                        textInput.name = `questions[${questionIndex}][options][${index}]`;
                        textInput.placeholder = `Option ${String.fromCharCode(65 + index)}`;
                    }

                    // Enable/Disable remove button
                    const canRemove = options.length > 2;
                    if (removeBtn) {
                        removeBtn.disabled = !canRemove;
                        removeBtn.classList.toggle('disabled:opacity-50', !canRemove);
                        removeBtn.classList.toggle('disabled:cursor-not-allowed', !canRemove);
                    }
                });
            }

            // --- Event Delegation ---

            // 1. Add Question Buttons
            addQuestionBtn.addEventListener('click', addQuestion);
            addAnotherQuestionBtn.addEventListener('click', addQuestion);

            // 2. Save as Draft Button
            saveDraftBtn.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent default submission
                isDraftInput.value = '1'; // Set hidden field to indicate draft save
                examForm.submit();
            });

            // 3. Event Delegation for dynamic elements inside questionsContainer
            questionsContainer.addEventListener('click', function(e) {
                const target = e.target;
                const questionItem = target.closest('.question-item');

                if (!questionItem) return;

                // Handle Remove Question
                if (target.closest('.remove-question')) {
                    if (confirm('Are you sure you want to remove this question?')) {
                        questionItem.remove();
                        updateQuestionNumbers(); // Re-index everything
                    }
                }

                // Handle Add Option
                if (target.closest('.add-option')) {
                    addOptionToQuestion(questionItem);
                }

                // Handle Remove Option
                const removeOptionButton = target.closest('.remove-option');
                if (removeOptionButton) {
                    if (!removeOptionButton.disabled) {
                        removeOptionFromQuestion(questionItem, removeOptionButton);
                    }
                }
            });

            questionsContainer.addEventListener('change', function(e) {
                // Handle Question Type Change
                if (e.target.classList.contains('question-type')) {
                    handleQuestionTypeChange(e.target);
                }
            });

            // Add the first question when the page loads
            if (questionCount === 0) {
                addQuestion();
            }
        });
    </script>
@endsection
