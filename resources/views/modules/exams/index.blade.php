@extends('layouts.app')

@section('title', 'Admin Dashboard - Lincoln eLearning')

@section('content')

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Mathematics Final Examination</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">View exam details and statistics</p>
            </div>
            <div class="flex space-x-3">
                <button class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <i class="fas fa-edit mr-2"></i>Edit Exam
                </button>
            </div>
        </div>

        <!-- Exam Status Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 text-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 inline-flex">
                    <i class="fas fa-question-circle text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-3">25</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">Total Questions</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 text-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 inline-flex">
                    <i class="fas fa-star text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-3">100</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">Total Points</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 text-center">
                <div class="p-3 rounded-full bg-orange-100 dark:bg-orange-900/30 inline-flex">
                    <i class="fas fa-clock text-orange-600 dark:text-orange-400 text-xl"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-3">120</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">Minutes</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 text-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 inline-flex">
                    <i class="fas fa-users text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-3">45</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">Enrolled Students</p>
            </div>
        </div>

        <!-- Exam Details Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Exam Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Exam Title</label>
                    <p class="text-gray-900 dark:text-white font-medium">Mathematics Final Examination</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                    <p class="text-gray-900 dark:text-white font-medium">Mathematics</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Grade Level</label>
                    <p class="text-gray-900 dark:text-white font-medium">Grade 10</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Exam Type</label>
                    <p class="text-gray-900 dark:text-white font-medium">Final Exam</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Exam Date & Time</label>
                    <p class="text-gray-900 dark:text-white font-medium">March 15, 2024 at 9:00 AM</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration</label>
                    <p class="text-gray-900 dark:text-white font-medium">120 minutes</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Instructions</label>
                    <p class="text-gray-900 dark:text-white">This exam covers chapters 1-8 from the textbook. Show all your work for calculation questions. No calculators allowed.</p>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Security Settings</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Proctoring Options</h3>
                    <div class="space-y-2">
                        <div class="flex items-center text-green-600 dark:text-green-400">
                            <i class="fas fa-check-circle mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">Randomize question order</span>
                        </div>
                        <div class="flex items-center text-green-600 dark:text-green-400">
                            <i class="fas fa-check-circle mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">Disable copy/paste</span>
                        </div>
                        <div class="flex items-center text-gray-400 dark:text-gray-500">
                            <i class="fas fa-times-circle mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">Require fullscreen mode</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Access Control</h3>
                    <div class="space-y-2">
                        <div class="flex items-center text-gray-400 dark:text-gray-500">
                            <i class="fas fa-times-circle mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">Require access code</span>
                        </div>
                        <div class="flex items-center text-green-600 dark:text-green-400">
                            <i class="fas fa-check-circle mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">Allow late submission</span>
                        </div>
                        <div class="flex items-center text-green-600 dark:text-green-400">
                            <i class="fas fa-check-circle mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">Late penalty: 10%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions Overview -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Questions Overview</h2>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-600">
                            <th class="text-left py-3 text-sm font-medium text-gray-700 dark:text-gray-300">#</th>
                            <th class="text-left py-3 text-sm font-medium text-gray-700 dark:text-gray-300">Question Preview</th>
                            <th class="text-left py-3 text-sm font-medium text-gray-700 dark:text-gray-300">Type</th>
                            <th class="text-left py-3 text-sm font-medium text-gray-700 dark:text-gray-300">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="py-4 text-sm text-gray-700 dark:text-gray-300">1</td>
                            <td class="py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">What is the capital of France?</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Options: Paris, London, Berlin, Madrid</div>
                            </td>
                            <td class="py-4">
                                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs rounded-full">Multiple Choice</span>
                            </td>
                            <td class="py-4 text-sm text-gray-700 dark:text-gray-300">5</td>
                        </tr>

                        <tr class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="py-4 text-sm text-gray-700 dark:text-gray-300">2</td>
                            <td class="py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">Solve the equation: 2x + 5 = 13</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Short answer question</div>
                            </td>
                            <td class="py-4">
                                <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs rounded-full">Short Answer</span>
                            </td>
                            <td class="py-4 text-sm text-gray-700 dark:text-gray-300">8</td>
                        </tr>

                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="py-4 text-sm text-gray-700 dark:text-gray-300">3</td>
                            <td class="py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">Explain the Pythagorean theorem with an example</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Essay question</div>
                            </td>
                            <td class="py-4">
                                <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 text-xs rounded-full">Essay</span>
                            </td>
                            <td class="py-4 text-sm text-gray-700 dark:text-gray-300">15</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-center">
                <button class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                    View All 25 Questions
                </button>
            </div>
        </div>

        <!-- Student Performance -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Student Performance</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">78%</p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Average Score</p>
                </div>
                <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">35</p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Completed</p>
                </div>
                <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">10</p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Pending</p>
                </div>
            </div>

            <div class="flex justify-center space-x-4">
                <button class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    View Results
                </button>
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                    Grade Submissions
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
