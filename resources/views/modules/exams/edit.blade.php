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
                <button class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i>Cancel
                </button>
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </div>

        <!-- Exam Details Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Exam Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Exam Title *</label>
                    <input type="text" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="Mathematics Final Examination">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject *</label>
                    <select class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option selected>Mathematics</option>
                        <option>Physics</option>
                        <option>Chemistry</option>
                        <option>Biology</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Grade Level *</label>
                    <select class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option selected>Grade 10</option>
                        <option>Grade 11</option>
                        <option>Grade 12</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Exam Type</label>
                    <select class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option selected>Final Exam</option>
                        <option>Midterm Exam</option>
                        <option>Chapter Test</option>
                        <option>Quiz</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Exam Date *</label>
                    <input type="date" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="2024-03-15">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Time *</label>
                    <input type="time" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="09:00">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (minutes) *</label>
                    <input type="number" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="120">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total Points</label>
                    <input type="number" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="100">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Instructions</label>
                    <textarea class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" rows="3">This exam covers chapters 1-8 from the textbook. Show all your work for calculation questions. No calculators allowed.</textarea>
                </div>
            </div>
        </div>

        <!-- Questions Management -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Manage Questions</h2>
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <i class="fas fa-plus mr-2"></i>Add Question
                </button>
            </div>

            <!-- Questions List -->
            <div class="space-y-4">
                <!-- Question 1 -->
                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Question 1</h3>
                        <div class="flex space-x-2">
                            <button class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300">
                                <i class="fas fa-arrow-up"></i>
                            </button>
                            <button class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300">
                                <i class="fas fa-arrow-down"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Type</label>
                            <select class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option selected>Multiple Choice</option>
                                <option>True/False</option>
                                <option>Short Answer</option>
                                <option>Essay</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Points</label>
                            <input type="number" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="5">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Text</label>
                        <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" rows="2">What is the capital of France?</textarea>
                    </div>

                    <!-- Multiple Choice Options -->
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input type="radio" name="q1" class="mr-3 text-blue-600" checked>
                            <input type="text" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="Paris">
                        </div>
                        <div class="flex items-center">
                            <input type="radio" name="q1" class="mr-3 text-blue-600">
                            <input type="text" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="London">
                        </div>
                        <div class="flex items-center">
                            <input type="radio" name="q1" class="mr-3 text-blue-600">
                            <input type="text" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="Berlin">
                        </div>
                        <div class="flex items-center">
                            <input type="radio" name="q1" class="mr-3 text-blue-600">
                            <input type="text" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="Madrid">
                        </div>
                    </div>
                </div>

                <!-- Add Question Button -->
                <button class="w-full py-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i>Add Another Question
                </button>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Security Settings</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Proctoring Options</h3>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded text-blue-600 focus:ring-blue-500" checked>
                            <span class="ml-3 text-gray-700 dark:text-gray-300">Randomize question order</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded text-blue-600 focus:ring-blue-500" checked>
                            <span class="ml-3 text-gray-700 dark:text-gray-300">Disable copy/paste</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-gray-700 dark:text-gray-300">Require fullscreen mode</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-gray-700 dark:text-gray-300">Monitor tab switching</span>
                        </label>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Access Control</h3>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-gray-700 dark:text-gray-300">Require access code</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded text-blue-600 focus:ring-blue-500" checked>
                            <span class="ml-3 text-gray-700 dark:text-gray-300">Allow late submission</span>
                        </label>
                        <div class="ml-6">
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Late submission penalty (%)</label>
                            <input type="number" class="w-24 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="10" min="0" max="100">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Final Actions -->
        <div class="flex justify-between items-center">
            <button class="px-6 py-3 border border-red-300 dark:border-red-600 text-red-700 dark:text-red-300 rounded-lg font-medium hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200">
                <i class="fas fa-trash mr-2"></i>Delete Exam
            </button>

            <div class="flex space-x-3">
                <button class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    Cancel
                </button>
                <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                    Update Exam
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
