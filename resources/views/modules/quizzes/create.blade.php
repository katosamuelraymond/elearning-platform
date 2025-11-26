@extends('layouts.app')

@section('title', 'Create Quiz - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create New Quiz</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">Create a quiz for your students</p>
            </div>

            <form action="{{ route('admin.quizzes.store') }}" method="POST" id="quizForm">
                @csrf

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
                                   placeholder="Enter quiz title" value="{{ old('title') }}">
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
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
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
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
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
                                <option value="practice" {{ old('type') == 'practice' ? 'selected' : '' }}>Practice Quiz</option>
                                <option value="chapter_test" {{ old('type') == 'chapter_test' ? 'selected' : '' }}>Chapter Test</option>
                                <option value="quick_check" {{ old('type') == 'quick_check' ? 'selected' : '' }}>Quick Check</option>
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
                                   placeholder="60" value="{{ old('duration') }}">
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
                                   placeholder="100" value="{{ old('total_marks') }}">
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
                                   value="{{ old('start_time') }}">
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
                                   value="{{ old('end_time') }}">
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
                                  placeholder="Enter quiz instructions (optional)">{{ old('instructions') }}</textarea>
                        @error('instructions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Settings -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="randomize_questions" name="randomize_questions" value="1"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                {{ old('randomize_questions', true) ? 'checked' : '' }}>
                            <label for="randomize_questions" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Randomize Questions
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="show_answers" name="show_answers" value="1"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                {{ old('show_answers') ? 'checked' : '' }}>
                            <label for="show_answers" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Show Answers After Submission
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="is_published" name="is_published" value="1"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                {{ old('is_published') ? 'checked' : '' }}>
                            <label for="is_published" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Publish Immediately
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Questions Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Quiz Questions</h2>
                        <div class="flex space-x-3">
                            <button type="button" id="addFromBankBtn"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                                <i class="fas fa-database mr-2"></i>
                                Add from Question Bank
                            </button>
                            <button type="button" id="addNewQuestionBtn"
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                                <i class="fas fa-plus mr-2"></i>
                                Create New Question
                            </button>
                        </div>
                    </div>

                    <!-- Selected Questions -->
                    <div id="selectedQuestionsSection" class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Selected Questions</h3>
                        <div id="selectedQuestionsList" class="space-y-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 min-h-32">
                            <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                                <i class="fas fa-question-circle text-4xl mb-3"></i>
                                <p>No questions added yet. Add questions from the question bank or create new ones.</p>
                            </div>
                        </div>
                        <input type="hidden" name="selected_bank_questions" id="selected_bank_questions" value="">
                    </div>

                    <!-- New Questions Container -->
                    <div id="newQuestionsContainer" class="space-y-4">
                        <!-- New questions will be added here dynamically -->
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4">
                    <button type="button" id="saveDraft"
                            class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        Save as Draft
                    </button>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                        Create Quiz
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Question Bank Modal -->
    <div id="questionBankModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-6xl shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Question Bank</h3>
                    <button type="button" id="closeBankModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Filters -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                            <select id="filter_subject" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                            <select id="filter_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white">
                                <option value="">All Types</option>
                                <option value="mcq">Multiple Choice</option>
                                <option value="true_false">True/False</option>
                                <option value="short_answer">Short Answer</option>
                                <option value="essay">Essay</option>
                                <option value="fill_blank">Fill in Blank</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Difficulty</label>
                            <select id="filter_difficulty" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white">
                                <option value="">All Difficulties</option>
                                <option value="easy">Easy</option>
                                <option value="medium">Medium</option>
                                <option value="hard">Hard</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                            <input type="text" id="filter_search" placeholder="Search questions..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white">
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="button" id="searchQuestions"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                            Search Questions
                        </button>
                    </div>
                </div>

                <!-- Question Bank Results -->
                <div id="questionBankResults" class="max-h-96 overflow-y-auto space-y-3 p-2">
                    <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                        Use the filters above to search for questions from the question bank.
                    </div>
                </div>

                <div class="mt-4 flex justify-end space-x-3">
                    <button type="button" id="cancelBankSelection"
                            class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                        Cancel
                    </button>
                    <button type="button" id="addSelectedQuestions"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Add Selected Questions
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- New Question Modal -->
    <div id="newQuestionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Create New Question</h3>
                    <button type="button" id="closeNewQuestionModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Type</label>
                    <select id="new_question_type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        <option value="mcq">Multiple Choice</option>
                        <option value="true_false">True/False</option>
                        <option value="short_answer">Short Answer</option>
                        <option value="essay">Essay</option>
                        <option value="fill_blank">Fill in Blank</option>
                    </select>
                </div>

                <div id="newQuestionForm" class="space-y-4">
                    <!-- Dynamic form will be loaded here -->
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" id="cancelNewQuestion"
                            class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                        Cancel
                    </button>
                    <button type="button" id="saveNewQuestion"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Add Question
                    </button>
                </div>
            </div>
        </div>
    </div>


        <script>
            // Global variables
            let selectedBankQuestions = new Set();
            let newQuestionsCount = 0;
            let selectedQuestionsInModal = new Set();

            document.addEventListener('DOMContentLoaded', function() {
                // Question Bank Modal
                document.getElementById('addFromBankBtn').addEventListener('click', showQuestionBankModal);
                document.getElementById('closeBankModal').addEventListener('click', hideQuestionBankModal);
                document.getElementById('cancelBankSelection').addEventListener('click', hideQuestionBankModal);
                document.getElementById('searchQuestions').addEventListener('click', searchQuestions);
                document.getElementById('addSelectedQuestions').addEventListener('click', addSelectedBankQuestions);

                // New Question Modal
                document.getElementById('addNewQuestionBtn').addEventListener('click', showNewQuestionModal);
                document.getElementById('closeNewQuestionModal').addEventListener('click', hideNewQuestionModal);
                document.getElementById('cancelNewQuestion').addEventListener('click', hideNewQuestionModal);
                document.getElementById('new_question_type').addEventListener('change', updateNewQuestionForm);
                document.getElementById('saveNewQuestion').addEventListener('click', saveNewQuestion);

                // Initialize new question form
                updateNewQuestionForm();
            });

            // Question Bank Functions
            function showQuestionBankModal() {
                document.getElementById('questionBankModal').classList.remove('hidden');
                searchQuestions(); // Load initial questions
            }

            function hideQuestionBankModal() {
                document.getElementById('questionBankModal').classList.add('hidden');
                selectedQuestionsInModal.clear();
            }

            function searchQuestions() {
                const subjectId = document.getElementById('filter_subject').value;
                const type = document.getElementById('filter_type').value;
                const difficulty = document.getElementById('filter_difficulty').value;
                const search = document.getElementById('filter_search').value;

                const url = `{{ route('admin.quizzes.question-bank') }}?subject_id=${subjectId}&type=${type}&difficulty=${difficulty}&search=${encodeURIComponent(search)}`;

                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            displayQuestionBankResults(data.questions);
                        } else {
                            throw new Error(data.message || 'Failed to load questions');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('questionBankResults').innerHTML = `
                <div class="text-center text-red-500 dark:text-red-400 py-8">
                    <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                    <p>Failed to load questions. Please try again.</p>
                </div>
            `;
                    });
            }

            function displayQuestionBankResults(questions) {
                const container = document.getElementById('questionBankResults');

                if (questions.length === 0) {
                    container.innerHTML = '<div class="text-center text-gray-500 dark:text-gray-400 py-8">No questions found matching your criteria.</div>';
                    return;
                }

                container.innerHTML = questions.map(question => {
                    const isSelected = selectedQuestionsInModal.has(question.id);
                    return `
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 ${isSelected ? 'bg-blue-50 dark:bg-blue-900 border-blue-300 dark:border-blue-700' : ''}">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="text-xs font-medium px-2 py-1 rounded-full
                                ${question.difficulty === 'easy' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                        question.difficulty === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'}">
                                ${question.difficulty}
                            </span>
                            <span class="text-xs font-medium px-2 py-1 rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 capitalize">
                                ${question.type.replace('_', ' ')}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                ${question.points} points
                            </span>
                        </div>
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">${question.question_text}</h4>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Subject: ${question.subject.name}
                        </div>
                        ${question.type === 'mcq' && question.options ? `
                            <div class="mt-2 space-y-1">
                                ${question.options.map(option => `
                                    <div class="flex items-center text-sm">
                                        <span class="w-4 mr-2">${String.fromCharCode(65 + option.order)}.</span>
                                        <span class="${option.is_correct ? 'text-green-600 dark:text-green-400 font-medium' : 'text-gray-600 dark:text-gray-400'}">
                                            ${option.option_text}
                                            ${option.is_correct ? ' ✓' : ''}
                                        </span>
                                    </div>
                                `).join('')}
                            </div>
                        ` : ''}
                    </div>
                    <div class="ml-4 flex items-center">
                        <input type="checkbox"
                               onchange="toggleQuestionSelection(${question.id}, this.checked)"
                               ${isSelected ? 'checked' : ''}
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    </div>
                </div>
            </div>
        `;
                }).join('');
            }

            function toggleQuestionSelection(questionId, isSelected) {
                if (isSelected) {
                    selectedQuestionsInModal.add(questionId);
                } else {
                    selectedQuestionsInModal.delete(questionId);
                }
                // Update UI to show selection
                searchQuestions(); // Refresh to update styles
            }

            function addSelectedBankQuestions() {
                selectedQuestionsInModal.forEach(questionId => {
                    selectedBankQuestions.add(questionId);
                });
                updateSelectedQuestionsList();
                hideQuestionBankModal();
            }

            function updateSelectedQuestionsList() {
                const container = document.getElementById('selectedQuestionsList');
                const hiddenInput = document.getElementById('selected_bank_questions');

                hiddenInput.value = Array.from(selectedBankQuestions).join(',');

                if (selectedBankQuestions.size === 0) {
                    container.innerHTML = `
            <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                <i class="fas fa-question-circle text-4xl mb-3"></i>
                <p>No questions added yet. Add questions from the question bank or create new ones.</p>
            </div>
        `;
                    return;
                }

                // In a real implementation, you would fetch question details here
                // For now, we'll just show the IDs
                container.innerHTML = Array.from(selectedBankQuestions).map(id => `
        <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
            <div class="flex items-center space-x-3">
                <i class="fas fa-question text-blue-500"></i>
                <span class="text-gray-700 dark:text-gray-300">Question ID: ${id}</span>
            </div>
            <button type="button" onclick="removeBankQuestion(${id})"
                class="text-red-600 hover:text-red-800 dark:text-red-400">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `).join('');
            }

            function removeBankQuestion(questionId) {
                selectedBankQuestions.delete(questionId);
                updateSelectedQuestionsList();
            }

            // New Question Functions
            function showNewQuestionModal() {
                document.getElementById('newQuestionModal').classList.remove('hidden');
            }

            function hideNewQuestionModal() {
                document.getElementById('newQuestionModal').classList.add('hidden');
            }

            function updateNewQuestionForm() {
                const type = document.getElementById('new_question_type').value;
                const formContainer = document.getElementById('newQuestionForm');

                let formHtml = '';

                switch(type) {
                    case 'mcq':
                        formHtml = `
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Text *</label>
                    <textarea id="mcq_question_text" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" rows="3" placeholder="Enter your question" required></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Points *</label>
                    <input type="number" id="mcq_points" class="w-32 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" value="1" min="0.5" step="0.5" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Options *</label>
                    <div class="space-y-2" id="mcqOptions">
                        <div class="flex items-center space-x-2">
                            <input type="radio" name="mcq_correct_option" value="0" class="text-blue-600" checked>
                            <input type="text" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" placeholder="Option A" required>
                            <button type="button" class="text-red-600 hover:text-red-800" onclick="removeOption(this)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="radio" name="mcq_correct_option" value="1" class="text-blue-600">
                            <input type="text" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" placeholder="Option B" required>
                            <button type="button" class="text-red-600 hover:text-red-800" onclick="removeOption(this)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" onclick="addOption()" class="mt-2 text-blue-600 hover:text-blue-800 text-sm flex items-center">
                        <i class="fas fa-plus mr-1"></i> Add Option
                    </button>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="mcq_save_to_bank" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="mcq_save_to_bank" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Save to question bank</label>
                </div>
            `;
                        break;

                    case 'true_false':
                        formHtml = `
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Text *</label>
                    <textarea id="tf_question_text" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" rows="3" placeholder="Enter your question" required></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Points *</label>
                    <input type="number" id="tf_points" class="w-32 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" value="1" min="0.5" step="0.5" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Correct Answer *</label>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <input type="radio" id="tf_true" name="tf_correct_answer" value="true" class="text-blue-600" checked>
                            <label for="tf_true" class="ml-2 text-gray-700 dark:text-gray-300">True</label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" id="tf_false" name="tf_correct_answer" value="false" class="text-blue-600">
                            <label for="tf_false" class="ml-2 text-gray-700 dark:text-gray-300">False</label>
                        </div>
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="tf_save_to_bank" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="tf_save_to_bank" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Save to question bank</label>
                </div>
            `;
                        break;

                    // Add cases for other question types as needed
                    default:
                        formHtml = `
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Text *</label>
                    <textarea id="default_question_text" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" rows="3" placeholder="Enter your question" required></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Points *</label>
                    <input type="number" id="default_points" class="w-32 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" value="1" min="0.5" step="0.5" required>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="default_save_to_bank" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="default_save_to_bank" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Save to question bank</label>
                </div>
            `;
                }

                formContainer.innerHTML = formHtml;
            }

            function addOption() {
                const optionsContainer = document.getElementById('mcqOptions');
                const optionCount = optionsContainer.children.length;

                if (optionCount < 6) {
                    const newOption = document.createElement('div');
                    newOption.className = 'flex items-center space-x-2';
                    newOption.innerHTML = `
            <input type="radio" name="mcq_correct_option" value="${optionCount}" class="text-blue-600">
            <input type="text" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" placeholder="Option ${String.fromCharCode(65 + optionCount)}" required>
            <button type="button" class="text-red-600 hover:text-red-800" onclick="removeOption(this)">
                <i class="fas fa-times"></i>
            </button>
        `;
                    optionsContainer.appendChild(newOption);
                }
            }

            function removeOption(button) {
                if (document.getElementById('mcqOptions').children.length > 2) {
                    button.parentElement.remove();
                }
            }

            function saveNewQuestion() {
                const type = document.getElementById('new_question_type').value;
                const questionData = {
                    type: type,
                    save_to_bank: document.getElementById(`${type}_save_to_bank`).checked ? '1' : '0'
                };

                // Validate required fields
                let isValid = true;
                let errorMessage = '';

                // Collect data based on question type
                switch(type) {
                    case 'mcq':
                        const questionText = document.getElementById('mcq_question_text').value;
                        const points = document.getElementById('mcq_points').value;
                        const options = Array.from(document.querySelectorAll('#mcqOptions input[type="text"]')).map(input => input.value).filter(opt => opt.trim() !== '');

                        if (!questionText.trim()) {
                            isValid = false;
                            errorMessage = 'Question text is required';
                        } else if (options.length < 2) {
                            isValid = false;
                            errorMessage = 'At least 2 options are required for MCQ questions';
                        } else {
                            questionData.question_text = questionText;
                            questionData.points = points;
                            questionData.correct_answer = document.querySelector('input[name="mcq_correct_option"]:checked').value;
                            questionData.options = options;
                        }
                        break;

                    case 'true_false':
                        const tfQuestionText = document.getElementById('tf_question_text').value;
                        const tfPoints = document.getElementById('tf_points').value;

                        if (!tfQuestionText.trim()) {
                            isValid = false;
                            errorMessage = 'Question text is required';
                        } else {
                            questionData.question_text = tfQuestionText;
                            questionData.points = tfPoints;
                            questionData.correct_answer = document.querySelector('input[name="tf_correct_answer"]:checked').value;
                        }
                        break;

                    default:
                        const defaultQuestionText = document.getElementById('default_question_text').value;
                        const defaultPoints = document.getElementById('default_points').value;

                        if (!defaultQuestionText.trim()) {
                            isValid = false;
                            errorMessage = 'Question text is required';
                        } else {
                            questionData.question_text = defaultQuestionText;
                            questionData.points = defaultPoints;
                        }
                }

                if (!isValid) {
                    alert(errorMessage);
                    return;
                }

                // Add the new question to the form
                addNewQuestionToForm(questionData);
                hideNewQuestionModal();
                resetNewQuestionForm();
            }

            function addNewQuestionToForm(questionData) {
                newQuestionsCount++;
                const container = document.getElementById('newQuestionsContainer');

                const questionHtml = `
        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4" data-question-index="${newQuestionsCount}">
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">New Question ${newQuestionsCount}</span>
                    <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 capitalize">
                        ${questionData.type.replace('_', ' ')}
                    </span>
                </div>
                <button type="button" onclick="removeNewQuestion(${newQuestionsCount})" class="text-red-600 hover:text-red-800 dark:text-red-400">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-gray-900 dark:text-white mb-2">${questionData.question_text}</p>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Points: ${questionData.points} |
                ${questionData.save_to_bank === '1' ? 'Will be saved to bank' : 'Not saved to bank'}
            </div>
            ${createQuestionInputs(questionData, newQuestionsCount)}
        </div>
    `;

                container.insertAdjacentHTML('beforeend', questionHtml);
            }

            function createQuestionInputs(questionData, index) {
                let inputs = `
        <input type="hidden" name="questions[${index}][type]" value="${questionData.type}">
        <input type="hidden" name="questions[${index}][question_text]" value="${questionData.question_text}">
        <input type="hidden" name="questions[${index}][points]" value="${questionData.points}">
        <input type="hidden" name="questions[${index}][save_to_bank]" value="${questionData.save_to_bank}">
    `;

                if (questionData.type === 'mcq') {
                    inputs += `<input type="hidden" name="questions[${index}][correct_answer]" value="${questionData.correct_answer}">`;

                    // Create options array properly
                    if (questionData.options && questionData.options.length > 0) {
                        questionData.options.forEach((option, optIndex) => {
                            if (option && option.trim() !== '') { // Only add non-empty options
                                inputs += `<input type="hidden" name="questions[${index}][options][${optIndex}]" value="${option}">`;
                            }
                        });
                    }
                } else if (questionData.type === 'true_false') {
                    inputs += `<input type="hidden" name="questions[${index}][correct_answer]" value="${questionData.correct_answer}">`;
                } else if (questionData.type === 'short_answer' && questionData.expected_answer) {
                    inputs += `<input type="hidden" name="questions[${index}][expected_answer]" value="${questionData.expected_answer}">`;
                } else if (questionData.type === 'essay' && questionData.grading_rubric) {
                    inputs += `<input type="hidden" name="questions[${index}][grading_rubric]" value="${questionData.grading_rubric}">`;
                } else if (questionData.type === 'fill_blank' && questionData.blank_answers) {
                    inputs += `<input type="hidden" name="questions[${index}][blank_answers]" value="${questionData.blank_answers}">`;
                }

                return inputs;
            }

            function removeNewQuestion(index) {
                const element = document.querySelector(`[data-question-index="${index}"]`);
                if (element) {
                    element.remove();
                }
            }

            function resetNewQuestionForm() {
                // Reset form fields
                const type = document.getElementById('new_question_type').value;
                document.getElementById(`${type}_question_text`).value = '';
                document.getElementById(`${type}_points`).value = '1';
                document.getElementById(`${type}_save_to_bank`).checked = false;

                // Reset MCQ options
                if (type === 'mcq') {
                    const optionsContainer = document.getElementById('mcqOptions');
                    optionsContainer.innerHTML = `
            <div class="flex items-center space-x-2">
                <input type="radio" name="mcq_correct_option" value="0" class="text-blue-600" checked>
                <input type="text" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" placeholder="Option A" required>
                <button type="button" class="text-red-600 hover:text-red-800" onclick="removeOption(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="flex items-center space-x-2">
                <input type="radio" name="mcq_correct_option" value="1" class="text-blue-600">
                <input type="text" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" placeholder="Option B" required>
                <button type="button" class="text-red-600 hover:text-red-800" onclick="removeOption(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
                }
            }
        </script>

@endsection
