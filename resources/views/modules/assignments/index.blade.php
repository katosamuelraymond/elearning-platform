@extends('layouts.app')

@section('title', 'Assignments - Lincoln eLearning')

@section('content')
    @php
        $isAdmin = auth()->user()->isAdmin();
        $isTeacher = auth()->user()->isTeacher();

        // Determine routes based on user role
        $indexRoute = $isAdmin ? route('admin.assignments.index') : route('teacher.assignments.index');
        $createRoute = $isAdmin ? route('admin.assignments.create') : route('teacher.assignments.create');
        $pageTitle = $isAdmin ? 'All Assignments' : 'My Assignments';
        $pageDescription = $isAdmin ? 'Manage and monitor all assignments across the school' : 'Manage and track your teaching assignments';
    @endphp

    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-3">
                            <div class="p-3 rounded-2xl bg-white dark:bg-gray-800 shadow-lg border border-gray-100 dark:border-gray-700 mr-4">
                                <i class="fas fa-file-pen text-2xl text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $pageTitle }}</h1>
                                <p class="text-gray-600 dark:text-gray-300 mt-2">{{ $pageDescription }}</p>
                            </div>
                        </div>

                        @if($isTeacher)
                            <div class="mt-3 flex items-center text-sm text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded-lg px-4 py-2 border border-blue-100 dark:border-blue-800">
                                <i class="fas fa-user-graduate mr-2"></i>
                                <span>Managing assignments for your classes</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 lg:mt-0 flex space-x-3">
                        <a href="{{ $createRoute }}"
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                            <i class="fas fa-plus-circle mr-3 text-lg"></i>
                            Create Assignment
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 transform hover:scale-105 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Total Assignments</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/30">
                            <i class="fas fa-layer-group text-2xl text-blue-600 dark:text-blue-400"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm text-green-600 dark:text-green-400">
                        <i class="fas fa-trending-up mr-1"></i>
                        <span>All assignments</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 transform hover:scale-105 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Published</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['published'] }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-green-50 dark:bg-green-900/30">
                            <i class="fas fa-bullhorn text-2xl text-green-600 dark:text-green-400"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm text-green-600 dark:text-green-400">
                        <i class="fas fa-eye mr-1"></i>
                        <span>Visible to students</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 transform hover:scale-105 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">In Draft</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['draft'] }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-orange-50 dark:bg-orange-900/30">
                            <i class="fas fa-edit text-2xl text-orange-600 dark:text-orange-400"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm text-orange-600 dark:text-orange-400">
                        <i class="fas fa-clock mr-1"></i>
                        <span>Under preparation</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 transform hover:scale-105 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Submissions</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['submitted'] }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-900/30">
                            <i class="fas fa-file-upload text-2xl text-purple-600 dark:text-purple-400"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm text-purple-600 dark:text-purple-400">
                        <i class="fas fa-users mr-1"></i>
                        <span>Student responses</span>
                    </div>
                </div>
            </div>

            <!-- Assignments Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                <!-- Table Header -->
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-700">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center mb-4 sm:mb-0">
                            <i class="fas fa-list-ul text-blue-600 dark:text-blue-400 text-xl mr-3"></i>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ $isAdmin ? 'All Assignments' : 'My Recent Assignments' }}
                            </h2>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-lg px-3 py-2">
                            <i class="fas fa-filter mr-2"></i>
                            Showing {{ $assignments->count() }} of {{ $assignments->total() }} assignments
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-file-lines mr-2 text-blue-500"></i>
                                    Assignment Details
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-graduation-cap mr-2 text-green-500"></i>
                                    Class & Subject
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-day mr-2 text-orange-500"></i>
                                    Due Date
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-tag mr-2 text-purple-500"></i>
                                    Status
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-chart-bar mr-2 text-indigo-500"></i>
                                    Progress
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-cogs mr-2 text-gray-500"></i>
                                    Actions
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($assignments as $assignment)
                            @php
                                $showRoute = $isAdmin ? route('admin.assignments.show', $assignment->id) : route('teacher.assignments.show', $assignment->id);
                                $editRoute = $isAdmin ? route('admin.assignments.edit', $assignment->id) : route('teacher.assignments.edit', $assignment->id);
                                $destroyRoute = $isAdmin ? route('admin.assignments.destroy', $assignment->id) : route('teacher.assignments.destroy', $assignment->id);
                                $submissionsRoute = $isAdmin ? route('admin.assignments.submissions', $assignment->id) : route('teacher.assignments.submissions', $assignment->id);
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all duration-200 group">
                                <!-- Assignment Details -->
                                <td class="px-6 py-5">
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0 p-3 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg">
                                            <i class="fas fa-file-alt text-lg"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                {{ $assignment->title }}
                                            </h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">
                                                {{ $assignment->description ? Str::limit($assignment->description, 80) : 'No description provided' }}
                                            </p>
                                            <div class="flex items-center mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                <i class="fas fa-star mr-1 text-yellow-500"></i>
                                                <span>{{ $assignment->max_points }} points</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Class & Subject -->
                                <td class="px-6 py-5">
                                    <div class="space-y-2">
                                        <div class="flex items-center">
                                            <i class="fas fa-chalkboard-teacher mr-2 text-blue-500"></i>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $assignment->class->name }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-book mr-2 text-green-500"></i>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $assignment->subject->name }}</span>
                                        </div>
                                        @if($isAdmin && $assignment->teacher)
                                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                <i class="fas fa-user-tie mr-1"></i>
                                                <span>By {{ $assignment->teacher->name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Due Date -->
                                <td class="px-6 py-5">
                                    <div class="flex items-center space-x-3">
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                                {{ $assignment->due_date->format('d') }}
                                            </div>
                                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                                {{ $assignment->due_date->format('M') }}
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $assignment->due_date->format('l') }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $assignment->due_date->format('g:i A') }}
                                            </div>
                                            @if($assignment->due_date->isPast())
                                                <div class="text-xs text-red-500 dark:text-red-400 font-medium mt-1">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>Overdue
                                                </div>
                                            @elseif($assignment->due_date->isToday())
                                                <div class="text-xs text-orange-500 dark:text-orange-400 font-medium mt-1">
                                                    <i class="fas fa-clock mr-1"></i>Due Today
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-5">
                                    @if($assignment->is_published)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Published
                                </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-600">
                                    <i class="fas fa-edit mr-2"></i>
                                    Draft
                                </span>
                                    @endif
                                </td>

                                <!-- Progress -->
                                <td class="px-6 py-5">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">Submissions</span>
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ $assignment->submissions_count ?? 0 }}</span>
                                        </div>
                                        @if($assignment->submissions_count > 0 && isset($assignment->graded_count))
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-gray-600 dark:text-gray-400">Graded</span>
                                                <span class="font-semibold text-green-600 dark:text-green-400">{{ $assignment->graded_count }}</span>
                                            </div>
                                        @endif
                                        @if($assignment->submissions_count > 0)
                                            <a href="{{ $submissionsRoute }}" class="inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                                                <i class="fas fa-external-link-alt mr-1 text-xs"></i>
                                                View submissions
                                            </a>
                                        @endif
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-5">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ $showRoute }}"
                                           class="inline-flex items-center p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all duration-200 group/tooltip relative"
                                           title="View Assignment">
                                            <i class="fas fa-eye"></i>
                                            <span class="tooltip-text">View</span>
                                        </a>

                                        <a href="{{ $editRoute }}"
                                           class="inline-flex items-center p-2 text-gray-400 hover:text-green-600 dark:hover:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-all duration-200 group/tooltip relative"
                                           title="Edit Assignment">
                                            <i class="fas fa-edit"></i>
                                            <span class="tooltip-text">Edit</span>
                                        </a>

                                        <form action="{{ $destroyRoute }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200 group/tooltip relative"
                                                    onclick="return confirm('Are you sure you want to delete this assignment?')"
                                                    title="Delete Assignment">
                                                <i class="fas fa-trash"></i>
                                                <span class="tooltip-text">Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="p-4 rounded-2xl bg-gray-100 dark:bg-gray-700 mb-4">
                                            <i class="fas fa-file-slash text-3xl text-gray-400 dark:text-gray-500"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Assignments Found</h3>
                                        <p class="text-gray-600 dark:text-gray-400 mb-4 max-w-md">
                                            {{ $isAdmin ? 'There are no assignments created yet.' : 'You haven\'t created any assignments yet.' }}
                                        </p>
                                        <a href="{{ $createRoute }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                                            <i class="fas fa-plus mr-2"></i>
                                            Create Your First Assignment
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($assignments->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-4 sm:mb-0">
                                <i class="fas fa-list mr-2"></i>
                                Showing <span class="font-semibold">{{ $assignments->firstItem() }}</span> to
                                <span class="font-semibold">{{ $assignments->lastItem() }}</span> of
                                <span class="font-semibold">{{ $assignments->total() }}</span> assignments
                            </div>
                            <div class="flex items-center space-x-2">
                                {{ $assignments->links('vendor.pagination.tailwind') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .tooltip-text {
            position: absolute;
            bottom: -30px;
            left: 50%;
            transform: translateX(-50%);
            background: #1f2937;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            z-index: 10;
        }

        .group\/tooltip:hover .tooltip-text {
            opacity: 1;
            visibility: visible;
            bottom: -35px;
        }

        .group\/tooltip:hover .tooltip-text::before {
            content: '';
            position: absolute;
            top: -4px;
            left: 50%;
            transform: translateX(-50%);
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-bottom: 4px solid #1f2937;
        }
    </style>
@endsection
