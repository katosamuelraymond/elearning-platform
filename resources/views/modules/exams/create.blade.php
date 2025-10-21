@extends('layouts.app')

@section('title', 'Admin Dashboard - Lincoln eLearning')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create New Exam</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Set up a comprehensive examination for your students</p>
        </div>

        <!-- Exam Details Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Exam Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Exam Title *</label>
                    <input type="text" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="e.g., Mathematics Final Examination">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject *</label>
                    <select class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select Subject</option>
                        <option>Mathematics</option>
                        <option>Physics</option>
                        <option>Chemistry</option>
                        <option>Biology</option>
                        <option>English Language</option>
                        <option>History</option>
                        <option>Geography</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Grade Level *</label>
                    <select class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select Grade</option>
                        <option>Grade 7</option>
                        <option>Grade 8</option>
                        <option>Grade 9</option>
                        <option>Grade 10</option>
                        <option>Grade 11</option>
                        <option>Grade 12</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Exam Type</label>
                    <select class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option>Midterm Exam</option>
                        <option>Final Exam</option>
                        <option>Chapter Test</option>
                        <option>Quiz</option>
                        <option>Practice Test</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Timing & Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Timing & Settings</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Exam Date *</label>
                    <input type="date" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Time *</label>
                    <input type="time" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (minutes) *</label>
                    <input type="number" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="120">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total Points</label>
                    <input type="number" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="100">
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

                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Access Control</h3>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-gray-700 dark:text-gray-300">Require access code</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded text-blue-600 focus:ring-blue-500" checked>
                            <span class="ml-3 text-gray-700 dark:text-gray-300">Allow late submission</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-gray-700 dark:text-gray-300">Enable time extension</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Exam Questions</h2>
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <i class="fas fa-plus mr-2"></i>Add Question
                </button>
            </div>

            <!-- Question 1 -->
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 mb-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Question 1</h3>
                    <div class="flex space-x-2">
                        <button class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Type</label>
                        <select class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option>Multiple Choice</option>
                            <option>True/False</option>
                            <option>Short Answer</option>
                            <option>Essay</option>
                            <option>Fill in the Blanks</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Points</label>
                        <input type="number" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" value="5">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Text</label>
                    <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" rows="3" placeholder="Enter your question here..."></textarea>
                </div>

                <!-- Multiple Choice Options -->
                <div class="space-y-3">
                    <div class="flex items-center">
                        <input type="radio" name="q1" class="mr-3 text-blue-600" checked>
                        <input type="text" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Option A" value="Paris">
                    </div>
                    <div class="flex items-center">
                        <input type="radio" name="q1" class="mr-3 text-blue-600">
                        <input type="text" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Option B" value="London">
                    </div>
                    <div class="flex items-center">
                        <input type="radio" name="q1" class="mr-3 text-blue-600">
                        <input type="text" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Option C" value="Berlin">
                    </div>
                    <div class="flex items-center">
                        <input type="radio" name="q1" class="mr-3 text-blue-600">
                        <input type="text" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Option D" value="Madrid">
                    </div>
                </div>
            </div>

            <!-- Add Question Button -->
            <button class="w-full py-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center justify-center">
                <i class="fas fa-plus mr-2"></i>Add Another Question
            </button>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4">
            <button class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                Save as Draft
            </button>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                Create Exam
            </button>
        </div>
    </div>
</div>

@endsection
