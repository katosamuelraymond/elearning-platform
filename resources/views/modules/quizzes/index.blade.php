@extends('layouts.app')

@section('title', 'Admin Dashboard - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Quiz Header -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mathematics Quiz: Algebra Basics</h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-1">Mr. Johnson • Grade 10 Mathematics</p>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-semibold text-red-600 dark:text-red-400" id="timer">45:00</div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Time Remaining</p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-300">Questions</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">10</p>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-300">Total Points</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">50</p>
                    </div>
                    <div class="bg-orange-50 dark:bg-orange-900/20 p-3 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-300">Time Limit</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">45 min</p>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 p-3 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-300">Due Date</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">Today</p>
                    </div>
                </div>
            </div>

            <!-- Quiz Instructions -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-yellow-600 dark:text-yellow-400 mt-1 mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-yellow-800 dark:text-yellow-300">Important Instructions</h3>
                        <ul class="text-yellow-700 dark:text-yellow-400 text-sm mt-1 list-disc list-inside">
                            <li>This quiz has a time limit of 45 minutes</li>
                            <li>Once you start, the timer cannot be paused</li>
                            <li>Answer all questions before submitting</li>
                            <li>You cannot go back to previous questions</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Quiz Questions -->
            <div class="space-y-6">
                <!-- Question 1 -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Question 1</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Multiple Choice • 5 points</p>
                        </div>
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm px-3 py-1 rounded-full">Current</span>
                    </div>

                    <p class="text-gray-700 dark:text-gray-300 mb-6 text-lg">
                        What is the solution to the equation: 2x + 5 = 13?
                    </p>

                    <div class="space-y-3">
                        <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-200">
                            <input type="radio" name="q1" class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-gray-700 dark:text-gray-300">x = 4</span>
                        </label>
                        <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-200">
                            <input type="radio" name="q1" class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-gray-700 dark:text-gray-300">x = 5</span>
                        </label>
                        <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-200">
                            <input type="radio" name="q1" class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-gray-700 dark:text-gray-300">x = 6</span>
                        </label>
                        <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-200">
                            <input type="radio" name="q1" class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-gray-700 dark:text-gray-300">x = 7</span>
                        </label>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Question 2</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">True/False • 3 points</p>
                        </div>
                        <span class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-sm px-3 py-1 rounded-full">Not Started</span>
                    </div>

                    <p class="text-gray-700 dark:text-gray-300 mb-6 text-lg">
                        The equation y = mx + c represents a linear function.
                    </p>

                    <div class="space-y-3">
                        <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-200">
                            <input type="radio" name="q2" class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-gray-700 dark:text-gray-300">True</span>
                        </label>
                        <label class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-200">
                            <input type="radio" name="q2" class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-gray-700 dark:text-gray-300">False</span>
                        </label>
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Question 3</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Short Answer • 8 points</p>
                        </div>
                        <span class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-sm px-3 py-1 rounded-full">Not Started</span>
                    </div>

                    <p class="text-gray-700 dark:text-gray-300 mb-6 text-lg">
                        Explain the difference between an expression and an equation in algebra.
                    </p>

                    <textarea
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        rows="4"
                        placeholder="Type your answer here...">
                    </textarea>
                </div>
            </div>

            <!-- Navigation & Submit -->
            <div class="flex justify-between items-center mt-8">
                <button class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Previous
                </button>

                <div class="flex space-x-4">
                    <button class="px-6 py-3 border border-blue-600 text-blue-600 dark:text-blue-400 rounded-lg font-medium hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200">
                        Save Progress
                    </button>
                    <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                        Submit Quiz
                        <i class="fas fa-check ml-2"></i>
                    </button>
                </div>

                <button class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center">
                    Next
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>
    </div>

@endsection
