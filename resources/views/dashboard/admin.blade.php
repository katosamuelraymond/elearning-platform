@extends('layouts.app')

@section('title', 'Admin Dashboard - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">


            <!-- Dashboard Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-1">Welcome back, Administrator</p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            {{ now()->format('l, F j, Y') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Clickable Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Students Card -->
                <a href=""
                   class="group bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:border-blue-200 dark:hover:border-blue-600 hover:bg-gradient-to-br hover:from-blue-50 hover:to-white dark:hover:from-blue-900/10 dark:hover:to-gray-800 transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">Total Students</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white group-hover:text-blue-700 dark:group-hover:text-blue-300">1,234</p>
                            <p class="text-xs text-green-600 dark:text-green-400 mt-2 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                5.2% increase
                            </p>
                        </div>
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900/30 dark:to-blue-800/20 group-hover:from-blue-200 group-hover:to-blue-100 dark:group-hover:from-blue-800/40 dark:group-hover:to-blue-700/30 transition-all duration-300">
                            <!-- Graduation Cap Icon -->
                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" opacity="0.5"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v6l-9-5v-6l9 5z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            Manage students
                        </span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>

                <!-- Teachers Card -->
                <a href=""
                   class="group bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:border-green-200 dark:hover:border-green-600 hover:bg-gradient-to-br hover:from-green-50 hover:to-white dark:hover:from-green-900/10 dark:hover:to-gray-800 transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">Teaching Staff</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white group-hover:text-green-700 dark:group-hover:text-green-300">45</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                12 departments
                            </p>
                        </div>
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-green-100 to-green-50 dark:from-green-900/30 dark:to-green-800/20 group-hover:from-green-200 group-hover:to-green-100 dark:group-hover:from-green-800/40 dark:group-hover:to-green-700/30 transition-all duration-300">
                            <!-- Chalkboard Teacher Icon -->
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">
                            View staff
                        </span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-green-600 dark:group-hover:text-green-400 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>

                <!-- Subjects Card -->
                <a href=""
                   class="group bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:border-purple-200 dark:hover:border-purple-600 hover:bg-gradient-to-br hover:from-purple-50 hover:to-white dark:hover:from-purple-900/10 dark:hover:to-gray-800 transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">Subjects</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white group-hover:text-purple-700 dark:group-hover:text-purple-300">28</p>
                            <p class="text-xs text-blue-600 dark:text-blue-400 mt-2 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                </svg>
                                3 new this month
                            </p>
                        </div>
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-purple-100 to-purple-50 dark:from-purple-900/30 dark:to-purple-800/20 group-hover:from-purple-200 group-hover:to-purple-100 dark:group-hover:from-purple-800/40 dark:group-hover:to-purple-700/30 transition-all duration-300">
                            <!-- Books Icon -->
                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                            Manage curriculum
                        </span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-purple-600 dark:group-hover:text-purple-400 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>

                <!-- Pending Tasks Card -->
                <a href=""
                   class="group bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:border-orange-200 dark:hover:border-orange-600 hover:bg-gradient-to-br hover:from-orange-50 hover:to-white dark:hover:from-orange-900/10 dark:hover:to-gray-800 transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">Pending Tasks</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white group-hover:text-orange-700 dark:group-hover:text-orange-300">12</p>
                            <p class="text-xs text-red-600 dark:text-red-400 mt-2 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                3 require attention
                            </p>
                        </div>
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-900/30 dark:to-orange-800/20 group-hover:from-orange-200 group-hover:to-orange-100 dark:group-hover:from-orange-800/40 dark:group-hover:to-orange-700/30 transition-all duration-300">
                            <!-- Checklist Icon -->
                            <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-400 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                            View tasks
                        </span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-orange-600 dark:group-hover:text-orange-400 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - 2/3 width -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <a href="{{ route('admin.subjects.index') }}"
                                   class="group p-4 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/30 group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 mr-3">
                                            <i class="fas fa-book text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">Manage Subjects</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Curriculum management</p>
                                        </div>
                                    </div>
                                </a>

                                <a href="#"
                                   class="group p-4 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-green-300 dark:hover:border-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 transition-all duration-200">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-lg bg-green-100 dark:bg-green-900/30 group-hover:bg-green-200 dark:group-hover:bg-green-800/50 mr-3">
                                            <i class="fas fa-users text-green-600 dark:text-green-400"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">User Management</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Students & staff</p>
                                        </div>
                                    </div>
                                </a>

                                <a href="#"
                                   class="group p-4 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-purple-300 dark:hover:border-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-200">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-lg bg-purple-100 dark:bg-purple-900/30 group-hover:bg-purple-200 dark:group-hover:bg-purple-800/50 mr-3">
                                            <i class="fas fa-cog text-purple-600 dark:text-purple-400"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">System Settings</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Platform configuration</p>
                                        </div>
                                    </div>
                                </a>

                                <a href="#"
                                   class="group p-4 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-orange-300 dark:hover:border-orange-600 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-all duration-200">
                                    <div class="flex items-center">
                                        <div class="p-2 rounded-lg bg-orange-100 dark:bg-orange-900/30 group-hover:bg-orange-200 dark:group-hover:bg-orange-800/50 mr-3">
                                            <i class="fas fa-chart-bar text-orange-600 dark:text-orange-400"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">Reports</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Analytics & insights</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</h2>
                                <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All</a>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Activity Item -->
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                        <i class="fas fa-user-plus text-blue-600 dark:text-blue-400 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-900 dark:text-white">
                                            New student <span class="font-medium">Sarah Johnson</span> registered
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">2 hours ago</p>
                                    </div>
                                </div>

                                <!-- Activity Item -->
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                        <i class="fas fa-book text-green-600 dark:text-green-400 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-900 dark:text-white">
                                            New subject <span class="font-medium">Advanced Mathematics</span> added
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">5 hours ago</p>
                                    </div>
                                </div>

                                <!-- Activity Item -->
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                        <i class="fas fa-exclamation-circle text-orange-600 dark:text-orange-400 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-900 dark:text-white">
                                            3 assignments pending grading
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">1 day ago</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - 1/3 width -->
                <div class="space-y-6">
                    <!-- Upcoming Events -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Upcoming Events</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Event Item -->
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 w-2 h-2 rounded-full bg-purple-500"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Faculty Meeting</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Tomorrow, 10:00 AM</p>
                                    </div>
                                </div>

                                <!-- Event Item -->
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 w-2 h-2 rounded-full bg-indigo-500"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Semester Exams</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Starts in 2 weeks</p>
                                    </div>
                                </div>

                                <!-- Event Item -->
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 w-2 h-2 rounded-full bg-blue-500"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Teacher Training</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Next Friday, 9:00 AM</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">System Status</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Server Uptime</span>
                                    <span class="text-sm font-medium text-green-600 dark:text-green-400">99.8%</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Active Users</span>
                                    <span class="text-sm font-medium text-blue-600 dark:text-blue-400">247</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">Storage Used</span>
                                    <span class="text-sm font-medium text-orange-600 dark:text-orange-400">68%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
