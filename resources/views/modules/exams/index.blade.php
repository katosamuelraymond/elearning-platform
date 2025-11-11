@extends('layouts.app')

@section('title', 'Manage Exams - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ auth()->user()->isAdmin() ? 'All Exams' : 'My Exams' }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-2">
                            Manage and monitor all your examinations
                        </p>
                    </div>
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.exams.create') : route('teacher.exams.create') }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Create New Exam
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900">
                            <i class="fas fa-file-alt text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Exams</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900">
                            <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Published</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['published'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-yellow-100 dark:bg-yellow-900">
                            <i class="fas fa-edit text-yellow-600 dark:text-yellow-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Drafts</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['draft'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-purple-100 dark:bg-purple-900">
                            <i class="fas fa-archive text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Archived</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['archived'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-red-100 dark:bg-red-900">
                            <i class="fas fa-users text-red-600 dark:text-red-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Attempts</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['attempts'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                <form method="GET" action="{{ request()->url() }}">
                    <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                        <!-- Search -->
                        <div class="flex-1">
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Exams</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" name="search" id="search"
                                       value="{{ $filters['search'] ?? '' }}"
                                       placeholder="Search by title, description..."
                                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div class="w-full lg:w-48">
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <div class="relative">
                                <select name="status" id="status" class="block w-full pl-3 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white appearance-none">
                                    <option value="">All Status</option>
                                    <option value="published" {{ ($filters['status'] ?? '') == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="draft" {{ ($filters['status'] ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="archived" {{ ($filters['status'] ?? '') == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Class Filter -->
                        <div class="w-full lg:w-48">
                            <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Class</label>
                            <div class="relative">
                                <select name="class_id" id="class_id" class="block w-full pl-3 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white appearance-none">
                                    <option value="">All Classes</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ ($filters['class_id'] ?? '') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Subject Filter -->
                        <div class="w-full lg:w-48">
                            <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                            <div class="relative">
                                <select name="subject_id" id="subject_id" class="block w-full pl-3 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white appearance-none">
                                    <option value="">All Subjects</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ ($filters['subject_id'] ?? '') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-end space-x-3">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center whitespace-nowrap">
                                <i class="fas fa-filter mr-2"></i>
                                Apply
                            </button>
                            <a href="{{ request()->url() }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center whitespace-nowrap">
                                <i class="fas fa-redo mr-2"></i>
                                Reset
                            </a>
                        </div>
                    </div>

                    <!-- Active Filters Badges -->
                    @if($filters && array_filter($filters))
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active filters:</span>
                                <div class="flex flex-wrap gap-2">
                                    @if(!empty($filters['search']))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100">
                        Search: "{{ $filters['search'] }}"
                        <a href="{{ request()->fullUrlWithoutQuery('search') }}" class="ml-1 hover:text-blue-600">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                                    @endif

                                    @if(!empty($filters['status']))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100">
                        Status: {{ ucfirst($filters['status']) }}
                        <a href="{{ request()->fullUrlWithoutQuery('status') }}" class="ml-1 hover:text-green-600">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                                    @endif

                                    @if(!empty($filters['class_id']))
                                        @php $selectedClass = $classes->firstWhere('id', $filters['class_id']); @endphp
                                        @if($selectedClass)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-800 text-purple-800 dark:text-purple-100">
                        Class: {{ $selectedClass->name }}
                        <a href="{{ request()->fullUrlWithoutQuery('class_id') }}" class="ml-1 hover:text-purple-600">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                                        @endif
                                    @endif

                                    @if(!empty($filters['subject_id']))
                                        @php $selectedSubject = $subjects->firstWhere('id', $filters['subject_id']); @endphp
                                        @if($selectedSubject)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-800 text-orange-800 dark:text-orange-100">
                        Subject: {{ $selectedSubject->name }}
                        <a href="{{ request()->fullUrlWithoutQuery('subject_id') }}" class="ml-1 hover:text-orange-600">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Exams Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                @if($exams->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Exam Details</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Class & Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Timing</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Attempts</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($exams as $exam)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-file-alt text-blue-600 dark:text-blue-400"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $exam->title }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $exam->type }} • {{ $exam->total_marks }} points
                                                </div>
                                                @if($exam->description)
                                                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                                        {{ Str::limit($exam->description, 50) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $exam->class->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $exam->subject->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ \Carbon\Carbon::parse($exam->start_time)->format('M j, Y g:i A') }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $exam->duration }} mins
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $exam->attempts_count }} total
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $exam->submitted_count ?? 0 }} submitted
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($exam->is_archived)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-800 text-purple-800 dark:text-purple-100">
                                                <i class="fas fa-archive mr-1"></i>
                                                Archived
                                            </span>
                                        @elseif($exam->is_published)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Published
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100">
                                                <i class="fas fa-edit mr-1"></i>
                                                Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <!-- View Attempts -->
{{--                                            <a href="{{ auth()->user()->isAdmin() ? route('admin.exams.attempts', $exam) : route('teacher.exams.attempts', $exam) }}"--}}
{{--                                               class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1"--}}
{{--                                               title="View Attempts">--}}
{{--                                                <i class="fas fa-users"></i>--}}
{{--                                            </a>--}}

                                            <!-- View Exam -->
                                            <a href="{{ auth()->user()->isAdmin() ? route('admin.exams.show', $exam) : route('teacher.exams.show', $exam) }}"
                                               class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 p-1"
                                               title="View Exam">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- Edit Exam -->
                                            <a href="{{ auth()->user()->isAdmin() ? route('admin.exams.edit', $exam) : route('teacher.exams.edit', $exam) }}"
                                               class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 p-1"
                                               title="Edit Exam">
                                                <i class="fas fa-edit"></i>
                                            </a>


                                            <!-- Print Exam -->
                                            <a href="{{ auth()->user()->isAdmin() ? route('admin.exams.print', $exam) : route('teacher.exams.print', $exam) }}"
                                               target="_blank"
                                               class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 p-1"
                                               title="Print Exam">
                                                <i class="fas fa-print"></i>
                                            </a>

                                            <!-- Clone Exam -->
                                            <button onclick="cloneExam({{ $exam->id }})"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1"
                                                    title="Clone Exam">
                                                <i class="fas fa-copy"></i>
                                            </button>

                                            <!-- Toggle Publish -->
                                            @if(!$exam->is_archived)
                                                <form action="{{ auth()->user()->isAdmin() ? route('admin.exams.toggle-publish', $exam) : route('teacher.exams.toggle-publish', $exam) }}"
                                                      method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="text-{{ $exam->is_published ? 'orange' : 'blue' }}-600 hover:text-{{ $exam->is_published ? 'orange' : 'blue' }}-900 dark:text-{{ $exam->is_published ? 'orange' : 'blue' }}-400 dark:hover:text-{{ $exam->is_published ? 'orange' : 'blue' }}-300 p-1"
                                                            title="{{ $exam->is_published ? 'Unpublish' : 'Publish' }}">
                                                        <i class="fas fa-{{ $exam->is_published ? 'eye-slash' : 'eye' }}"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Archive/Unarchive -->
                                            <form action="{{ auth()->user()->isAdmin() ? route('admin.exams.toggle-archive', $exam) : route('teacher.exams.toggle-archive', $exam) }}"
                                                  method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="text-{{ $exam->is_archived ? 'green' : 'gray' }}-600 hover:text-{{ $exam->is_archived ? 'green' : 'gray' }}-900 dark:text-{{ $exam->is_archived ? 'green' : 'gray' }}-400 dark:hover:text-{{ $exam->is_archived ? 'green' : 'gray' }}-300 p-1"
                                                        title="{{ $exam->is_archived ? 'Unarchive' : 'Archive' }}">
                                                    <i class="fas fa-{{ $exam->is_archived ? 'box-open' : 'archive' }}"></i>
                                                </button>
                                            </form>

                                            <!-- Delete Exam -->
                                            <form action="{{ auth()->user()->isAdmin() ? route('admin.exams.destroy', $exam) : route('teacher.exams.destroy', $exam) }}"
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this exam? This action cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1"
                                                        title="Delete Exam">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                        {{ $exams->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-file-alt text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No exams found</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">
                            {{ $filters ? 'Try adjusting your filters' : 'Get started by creating your first exam' }}
                        </p>
                        @if(!$filters)
                            <a href="{{ auth()->user()->isAdmin() ? route('admin.exams.create') : route('teacher.exams.create') }}"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 inline-flex items-center">
                                <i class="fas fa-plus mr-2"></i>
                                Create New Exam
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function printExam(examId) {
            // This will be implemented in the next step
            alert('Print functionality for exam ' + examId + ' will be implemented next');
        }

        function cloneExam(examId) {
            if (confirm('Are you sure you want to clone this exam? This will create a copy that you can modify.')) {
                // This will be implemented in the next step
                alert('Clone functionality for exam ' + examId + ' will be implemented next');
            }
        }
    </script>
@endsection
