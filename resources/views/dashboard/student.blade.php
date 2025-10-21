@extends('layouts.app')

@section('title', 'Student Dashboard - Lincoln High School')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Student Dashboard Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Student Dashboard</h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-2">Welcome back, Sarah Johnson!</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Academic Year 2024</p>
                        <p class="text-lg font-semibold text-blue-600 dark:text-blue-400">Grade 12 - Science</p>
                    </div>
                </div>
            </div>

            <!-- Student Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Upcoming Tests -->
                <a href="#"
                   class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-l-4 border-red-500 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1 cursor-pointer group">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30 group-hover:bg-red-200 dark:group-hover:bg-red-800/50 transition-colors">
                            <i class="fas fa-calendar-alt text-red-600 dark:text-red-400"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Upcoming Tests</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">3</p>
                            <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                                Next: Physics
                            </p>
                        </div>
                    </div>
                </a>

                <!-- Pending Homework -->
                <a href="#"
                   class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-l-4 border-orange-500 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1 cursor-pointer group">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100 dark:bg-orange-900/30 group-hover:bg-orange-200 dark:group-hover:bg-orange-800/50 transition-colors">
                            <i class="fas fa-tasks text-orange-600 dark:text-orange-400"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Assignments Due</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">5</p>
                            <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">
                                2 this week
                            </p>
                        </div>
                    </div>
                </a>

                <!-- Current GPA -->
                <a href="#"
                   class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-l-4 border-green-500 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1 cursor-pointer group">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 group-hover:bg-green-200 dark:group-hover:bg-green-800/50 transition-colors">
                            <i class="fas fa-chart-line text-green-600 dark:text-green-400"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Current GPA</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">3.8</p>
                            <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                                +0.2 this term
                            </p>
                        </div>
                    </div>
                </a>

                <!-- Enrolled Subjects -->
                <a href="#"
                   class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border-l-4 border-blue-500 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1 cursor-pointer group">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 transition-colors">
                            <i class="fas fa-book text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">My Subjects</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">6</p>
                            <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                All active
                            </p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Priority Items -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Today's Priorities -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Today's Priorities</h2>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-full bg-red-100 dark:bg-red-800 mr-4">
                                        <i class="fas fa-clock text-red-600 dark:text-red-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">Physics Test Prep</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">Chapter 1-5: Mechanics</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-red-600 dark:text-red-400 font-medium">Tomorrow</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">10:00 AM</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 rounded-lg bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-800">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-full bg-orange-100 dark:bg-orange-800 mr-4">
                                        <i class="fas fa-tasks text-orange-600 dark:text-orange-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">Math Homework</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">Calculus Problems</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-orange-600 dark:text-orange-400 font-medium">Due: 2 days</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">11:59 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Access -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Quick Access</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <a href="#"
                            class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200 group">
                                <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30 group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 transition-colors">
                                    <i class="fas fa-book-open text-blue-600 dark:text-blue-400 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <span class="text-gray-700 dark:text-gray-200 font-medium">My Subjects</span>
                                </div>
                            </a>

                            <a href="#"
                            class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors duration-200 group">
                                <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900/30 group-hover:bg-green-200 dark:group-hover:bg-green-800/50 transition-colors">
                                    <i class="fas fa-file-upload text-green-600 dark:text-green-400 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <span class="text-gray-700 dark:text-gray-200 font-medium">Submit Work</span>
                                </div>
                            </a>

                            <a href="#"
                            class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors duration-200 group">
                                <div class="p-3 rounded-lg bg-purple-100 dark:bg-purple-900/30 group-hover:bg-purple-200 dark:group-hover:bg-purple-800/50 transition-colors">
                                    <i class="fas fa-download text-purple-600 dark:text-purple-400 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <span class="text-gray-700 dark:text-gray-200 font-medium">Materials</span>
                                </div>
                            </a>

                            <a href="#"
                            class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors duration-200 group">
                                <div class="p-3 rounded-lg bg-orange-100 dark:bg-orange-900/30 group-hover:bg-orange-200 dark:group-hover:bg-orange-800/50 transition-colors">
                                    <i class="fas fa-graduation-cap text-orange-600 dark:text-orange-400 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <span class="text-gray-700 dark:text-gray-200 font-medium">My Grades</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Progress & Recent -->
                <div class="space-y-6">
                    <!-- My Progress -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">My Progress</h2>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300 mb-1">
                                    <span>Mathematics</span>
                                    <span>75%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 75%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300 mb-1">
                                    <span>Physics</span>
                                    <span>60%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 60%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300 mb-1">
                                    <span>Chemistry</span>
                                    <span>45%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" style="width: 45%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Results -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Recent Results</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Math Quiz</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Weekly #5</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-green-600 dark:text-green-400 font-bold">92%</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">A</p>
                                </div>
                            </div>

                            <div class="flex justify-between items-center p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Physics Lab</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Report #2</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-blue-600 dark:text-blue-400 font-bold">85%</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">B+</p>
                                </div>
                            </div>

                            <a href="#" class="block text-center text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium pt-2">
                                View All Results
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
