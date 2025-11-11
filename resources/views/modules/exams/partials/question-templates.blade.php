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

<!-- Bank Question Edit Template -->
<template id="bank-question-edit-template">
    <div class="bank-question-edit-item border border-purple-200 dark:border-purple-600 rounded-lg p-4 bg-white dark:bg-gray-700" data-question-id="">
        <div class="flex justify-between items-start mb-3">
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-800 text-purple-800 dark:text-purple-100">
                        <i class="fas fa-database mr-1"></i>Bank Question
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100 question-type-badge">
                        MCQ
                    </span>
                    <span class="text-sm text-gray-500 dark:text-gray-400 question-subject">
                        Mathematics
                    </span>
                </div>
                <h4 class="text-md font-medium text-gray-900 dark:text-white question-text mb-2"></h4>
                <div class="question-options text-sm text-gray-600 dark:text-gray-300 space-y-1"></div>

                <!-- Edit in Bank Notice -->
                <div class="mt-2 p-2 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-600 rounded text-xs text-yellow-700 dark:text-yellow-300">
                    <i class="fas fa-info-circle mr-1"></i>
                    This question is from the question bank. To edit the content,
                    <a href="#" class="underline font-medium" onclick="openQuestionBankEditor('')">edit it in the Question Bank</a>.
                </div>
            </div>
            <div class="flex items-center space-x-2 ml-4">
                <div class="w-24">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Points</label>
                    <input type="number" class="bank-question-points w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white" value="5" min="1">
                </div>
                <button type="button" class="remove-bank-question text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="text-xs text-gray-500 dark:text-gray-400 flex justify-between items-center">
            <span>Shared across multiple exams</span>
            <span class="question-id">ID: <span></span></span>
        </div>
    </div>
</template>

<!-- Custom Question Edit Template -->
<template id="custom-question-edit-template">
    <div class="custom-question-edit-item border border-green-200 dark:border-green-600 rounded-lg p-4 bg-white dark:bg-gray-700" data-question-id="">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Custom Question <span class="question-number">1</span></h3>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100">
                    <i class="fas fa-pen mr-1"></i>Custom
                </span>
                <button type="button" class="remove-custom-question text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Type</label>
                <select name="custom_questions[0][type]" class="custom-question-type w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="mcq">Multiple Choice</option>
                    <option value="true_false">True/False</option>
                    <option value="short_answer">Short Answer</option>
                    <option value="essay">Essay</option>
                    <option value="fill_blank">Fill in the Blanks</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Points</label>
                <input type="number" name="custom_questions[0][points]" class="custom-question-points w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="5" min="1">
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Text</label>
            <textarea name="custom_questions[0][question_text]" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" rows="3" placeholder="Enter your question here..."></textarea>
        </div>

        <!-- Multiple Choice Options -->
        <div class="custom-options-container" data-type="mcq">
            <div class="option-group space-y-3">
                <!-- Options will be populated dynamically -->
            </div>
            <button type="button" class="add-option text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center mt-2">
                <i class="fas fa-plus mr-1"></i> Add Option
            </button>
        </div>

        <!-- True/False Options -->
        <div class="custom-options-container hidden" data-type="true_false">
            <div class="space-y-3">
                <label class="flex items-center">
                    <input type="radio" name="custom_questions[0][correct_answer]" value="true" class="mr-3 text-blue-600">
                    <span class="text-gray-700 dark:text-gray-300">True</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="custom_questions[0][correct_answer]" value="false" class="mr-3 text-blue-600">
                    <span class="text-gray-700 dark:text-gray-300">False</span>
                </label>
            </div>
        </div>

        <!-- Short Answer -->
        <div class="custom-options-container hidden" data-type="short_answer">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-300">Students will provide a short written answer to this question.</p>
                <div class="mt-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expected Answer (for grading reference)</label>
                    <textarea name="custom_questions[0][expected_answer]" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white" rows="2" placeholder="Provide a sample answer for grading reference..."></textarea>
                </div>
            </div>
        </div>

        <!-- Essay -->
        <div class="custom-options-container hidden" data-type="essay">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-300">Students will write an essay in response to this question.</p>
                <div class="mt-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Grading Rubric/Instructions</label>
                    <textarea name="custom_questions[0][grading_rubric]" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white" rows="3" placeholder="Provide grading criteria or instructions for the essay..."></textarea>
                </div>
            </div>
        </div>

        <!-- Fill in the Blanks -->
        <div class="custom-options-container hidden" data-type="fill_blank">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">Create fill-in-the-blank questions. Use <code class="bg-yellow-100 dark:bg-yellow-900 px-1 rounded">[blank]</code> to indicate where the blank should appear.</p>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question with Blanks</label>
                        <textarea name="custom_questions[0][blank_question]" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white" rows="2" placeholder="E.g., The capital of France is [blank]."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Correct Answers (comma-separated)</label>
                        <input type="text" name="custom_questions[0][blank_answers]" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-white" placeholder="E.g., Paris, paris">
                    </div>
                </div>
            </div>
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
