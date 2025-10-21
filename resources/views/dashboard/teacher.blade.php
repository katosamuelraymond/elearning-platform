@extends('layouts.app')

@section('title', 'Admin Dashboard - Lincoln eLearning')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Teacher Dashboard</h1>
                    <p class="text-gray-600 dark:text-gray-300">Welcome back, Mr. Johnson</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button class="p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">MJ</span>
                        </div>
                        <span class="text-gray-700 dark:text-gray-300 font-medium">Mr. Johnson</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Students -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                        <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Students</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">156</p>
                    </div>
                </div>
            </div>

            <!-- Active Classes -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-purple-100 dark:bg-purple-900/30">
                        <i class="fas fa-chalkboard-teacher text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Active Classes</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">6</p>
                    </div>
                </div>
            </div>

            <!-- Pending Grading -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-orange-100 dark:bg-orange-900/30">
                        <i class="fas fa-file-signature text-orange-600 dark:text-orange-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Pending Grading</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">23</p>
                    </div>
                </div>
            </div>

            <!-- Upcoming Deadlines -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-red-100 dark:bg-red-900/30">
                        <i class="fas fa-clock text-red-600 dark:text-red-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Upcoming Deadlines</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">5</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('teacher.assignments.create') }}"
                           class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                                <i class="fas fa-plus-circle text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div class="ml-4">
                                <span class="text-gray-700 dark:text-gray-200 font-medium">Create Assignment</span>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Set new homework or project</p>
                            </div>
                        </a>

                        <a href="{{ route('teacher.exams.create') }}"
                           class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="p-3 rounded-lg bg-purple-100 dark:bg-purple-900/30">
                                <i class="fas fa-file-circle-plus text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <div class="ml-4">
                                <span class="text-gray-700 dark:text-gray-200 font-medium">Create Exam</span>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Set up a new examination</p>
                            </div>
                        </a>

                        <a href="{{ route('teacher.grades.index') }}"
                           class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900/30">
                                <i class="fas fa-chart-simple text-green-600 dark:text-green-400"></i>
                            </div>
                            <div class="ml-4">
                                <span class="text-gray-700 dark:text-gray-200 font-medium">Grade Assignments</span>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Review and grade submissions</p>
                            </div>
                        </a>

                        <a href="{{ route('teacher.resources.index') }}"
                           class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="p-3 rounded-lg bg-orange-100 dark:bg-orange-900/30">
                                <i class="fas fa-folder-tree text-orange-600 dark:text-orange-400"></i>
                            </div>
                            <div class="ml-4">
                                <span class="text-gray-700 dark:text-gray-200 font-medium">Upload Resources</span>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Share learning materials</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Recent Assignments -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Assignments</h2>
                        <a href="{{ route('teacher.assignments.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">View All</a>
                    </div>

                    <div class="space-y-4">
                        <!-- Assignment 1 -->
                        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/30 mr-3">
                                    <i class="fas fa-file-text text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-white">Algebra Problem Set</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Due: Mar 15, 2024</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-600 dark:text-gray-300">45/56 submitted</div>
                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-1">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: 80%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Assignment 2 -->
                        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 rounded-lg bg-purple-100 dark:bg-purple-900/30 mr-3">
                                    <i class="fas fa-flask text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-white">Chemistry Lab Report</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Due: Mar 12, 2024</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-600 dark:text-gray-300">38/42 submitted</div>
                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-1">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: 90%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Assignment 3 -->
                        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 rounded-lg bg-orange-100 dark:bg-orange-900/30 mr-3">
                                    <i class="fas fa-book text-orange-600 dark:text-orange-400"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-white">Literature Essay</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Due: Mar 20, 2024</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-600 dark:text-gray-300">12/48 submitted</div>
                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-1">
                                    <div class="bg-orange-600 h-2 rounded-full" style="width: 25%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-8">
                <!-- Upcoming Classes -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Today's Classes</h2>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-600 rounded-lg">
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">Mathematics - S4A</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">8:00 AM - 9:00 AM</p>
                            </div>
                            <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded text-xs font-medium">Room 12</span>
                        </div>

                        <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-600 rounded-lg">
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">Physics - S6 Science</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">10:00 AM - 11:30 AM</p>
                            </div>
                            <span class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-2 py-1 rounded text-xs font-medium">Lab 3</span>
                        </div>

                        <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-600 rounded-lg">
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">Mathematics - S4B</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">2:00 PM - 3:00 PM</p>
                            </div>
                            <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded text-xs font-medium">Room 15</span>
                        </div>
                    </div>
                </div>

                <!-- Pending Tasks -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pending Tasks</h2>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 border border-red-200 dark:border-red-800 rounded-lg bg-red-50 dark:bg-red-900/20">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-white">Grade Physics Tests</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Due tomorrow</p>
                                </div>
                            </div>
                            <span class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-2 py-1 rounded text-xs font-medium">Urgent</span>
                        </div>

                        <div class="flex items-center justify-between p-3 border border-orange-200 dark:border-orange-800 rounded-lg bg-orange-50 dark:bg-orange-900/20">
                            <div class="flex items-center">
                                <i class="fas fa-clock text-orange-500 mr-3"></i>
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-white">Prepare Exam Questions</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Due in 3 days</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 border border-blue-200 dark:border-blue-800 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-blue-500 mr-3"></i>
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-white">Submit Term Reports</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Due next week</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Student Overview -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Student Performance</h2>

                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300 mb-1">
                                <span>Class Average - Mathematics</span>
                                <span>78%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 78%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300 mb-1">
                                <span>Assignment Completion</span>
                                <span>85%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: 85%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300 mb-1">
                                <span>Students Needing Help</span>
                                <span>12</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-orange-600 h-2 rounded-full" style="width: 24%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h2>

            <div class="space-y-3">
                <div class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg">
                    <div class="p-2 rounded-full bg-green-100 dark:bg-green-900/30 mr-3">
                        <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <p class="text-gray-700 dark:text-gray-200">You graded assignments for <span class="font-medium">Physics S6</span></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">2 hours ago</p>
                    </div>
                </div>

                <div class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg">
                    <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900/30 mr-3">
                        <i class="fas fa-file-plus text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-gray-700 dark:text-gray-200">New assignment created: <span class="font-medium">Algebra Problem Set</span></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Yesterday</p>
                    </div>
                </div>

                <div class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg">
                    <div class="p-2 rounded-full bg-purple-100 dark:bg-purple-900/30 mr-3">
                        <i class="fas fa-comment text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div>
                        <p class="text-gray-700 dark:text-gray-200">5 new student messages received</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">2 days ago</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
