@extends('layouts.app')

@section('title', 'Question Bank - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @php
                $isAdmin = auth()->user()->isAdmin();
                $isTeacher = auth()->user()->isTeacher();

                // Determine routes based on user role
                $indexRoute = $isAdmin ? route('admin.questions.index') : route('teacher.questions.index');
                $createRoute = $isAdmin ? route('admin.questions.create') : route('teacher.questions.create');
                $pageTitle = $isAdmin ? 'Question Bank' : 'My Question Bank';
                $pageDescription = $isAdmin ? 'Manage all questions for assessments' : 'Manage your personal question bank';
            @endphp

                <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $pageTitle }}</h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-2">
                        {{ $pageDescription }}
                    </p>

                    @if($isTeacher)
                        <div class="mt-2 flex items-center text-sm text-blue-600 dark:text-blue-400">
                            <i class="fas fa-user-circle mr-2"></i>
                            <span>Personal question bank for {{ auth()->user()->name }}</span>
                        </div>
                    @endif
                </div>
                <a href="{{ $createRoute }}"
                   class="ajax-link bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    {{ $isAdmin ? 'Create New Question' : 'Add New Question' }}
                </a>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <i class="fas fa-question-circle text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Questions</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                            <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Active</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                            <i class="fas fa-list-ul text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">MCQ</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['mcq'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                            <i class="fas fa-file-alt text-orange-600 dark:text-orange-400 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Essay</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['essay'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow mb-6">
                <div class="p-6">
                    <form id="filter-form" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                   placeholder="Search questions...">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                            <select name="subject_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ ($filters['subject_id'] ?? '') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                            <select name="type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">All Types</option>
                                @foreach($questionTypes as $type)
                                    <option value="{{ $type }}" {{ ($filters['type'] ?? '') == $type ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Difficulty</label>
                            <select name="difficulty" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">All Difficulties</option>
                                @foreach($difficulties as $difficulty)
                                    <option value="{{ $difficulty }}" {{ ($filters['difficulty'] ?? '') == $difficulty ? 'selected' : '' }}>
                                        {{ ucfirst($difficulty) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-4 flex justify-end space-x-3">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                                Apply Filters
                            </button>
                            <a href="{{ $indexRoute }}"
                               class="ajax-link bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg font-medium">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Questions List -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $isAdmin ? 'All Questions' : 'My Questions' }}
                    </h2>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $questions->total() }} question(s) found
                    </span>
                </div>

                @if($questions->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($questions as $question)
                            @php
                                // Determine routes for this specific question
                                $showRoute = $isAdmin ? route('admin.questions.show', $question) : route('teacher.questions.show', $question);
                                $editRoute = $isAdmin ? route('admin.questions.edit', $question) : route('teacher.questions.edit', $question);
                                $toggleRoute = $isAdmin ? route('admin.questions.toggle-status', $question) : route('teacher.questions.toggle-status', $question);
                                $destroyRoute = $isAdmin ? route('admin.questions.destroy', $question) : route('teacher.questions.destroy', $question);

                                // Check if user can edit/delete this question
                                $canEdit = $isAdmin || $question->created_by == auth()->id();
                            @endphp

                            <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                <a href="{{ $showRoute }}" class="ajax-link hover:text-blue-600 dark:hover:text-blue-400">
                                                    {{ Str::limit($question->question_text, 100) }}
                                                </a>
                                            </h3>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                            {{ $question->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                            {{ $question->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                                        </span>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                            {{ $question->difficulty == 'easy' ? 'bg-green-100 text-green-800' :
                                               ($question->difficulty == 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($question->difficulty) }}
                                        </span>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600 dark:text-gray-400 mb-3">
                                            <div class="flex items-center">
                                                <i class="fas fa-book mr-2 text-purple-500"></i>
                                                <span>{{ $question->subject->name }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-star mr-2 text-yellow-500"></i>
                                                <span>{{ $question->points }} points</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-user mr-2 text-blue-500"></i>
                                                <span>
                                                    @if($isAdmin)
                                                        By {{ $question->creator->name }}
                                                    @else
                                                        {{ $question->created_by == auth()->id() ? 'Created by you' : 'Shared with you' }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>

                                        @if($question->exams_count > 0)
                                            <div class="mt-2">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                Used in {{ $question->exams_count }} exam(s)
                                            </span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex items-center space-x-3 ml-4">
                                        <a href="{{ $showRoute }}"
                                           class="ajax-link inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                                            <i class="fas fa-eye mr-2"></i>
                                            View
                                        </a>

                                        @if($canEdit)
                                            <a href="{{ $editRoute }}"
                                               class="ajax-link inline-flex items-center px-3 py-2 border border-blue-300 dark:border-blue-600 text-sm font-medium rounded-md text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200">
                                                <i class="fas fa-edit mr-2"></i>
                                                Edit
                                            </a>

                                            <form action="{{ $toggleRoute }}" method="POST" class="inline ajax-form">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-2 border
                                                        {{ $question->is_active ? 'border-orange-300 dark:border-orange-600 text-orange-700 dark:text-orange-300 bg-orange-50 dark:bg-orange-900/20 hover:bg-orange-100 dark:hover:bg-orange-900/30' :
                                                           'border-green-300 dark:border-green-600 text-green-700 dark:text-green-300 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30' }}
                                                        text-sm font-medium rounded-md transition-colors duration-200">
                                                    <i class="fas {{ $question->is_active ? 'fa-eye-slash' : 'fa-eye' }} mr-2"></i>
                                                    {{ $question->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>

                                            @if($question->exams_count == 0 || $isAdmin)
                                                <form action="{{ $destroyRoute }}" method="POST" class="inline ajax-form"
                                                      onsubmit="return confirm('Are you sure you want to delete this question?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center px-3 py-2 border border-red-300 dark:border-red-600 text-sm font-medium rounded-md text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors duration-200">
                                                        <i class="fas fa-trash mr-2"></i>
                                                        Delete
                                                    </button>
                                                </form>
                                            @else
                                                <span class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 cursor-not-allowed"
                                                      title="Cannot delete - question is used in exams">
                                                    <i class="fas fa-trash mr-2"></i>
                                                    Delete
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-500 dark:text-gray-400 italic">
                                                Read-only
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {!! $questions->withQueryString()->links('vendor.pagination.ajax') !!}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-question-circle text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 text-lg">
                            @if($isTeacher && $subjects->isEmpty())
                                No subjects assigned yet
                            @else
                                No questions found
                            @endif
                        </p>
                        <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">
                            @if(request()->anyFilled(['search', 'subject_id', 'type', 'difficulty']))
                                Try adjusting your filters
                            @elseif($isTeacher && $subjects->isEmpty())
                                Please contact administrator to get assigned to subjects
                            @else
                                Get started by creating your first question.
                            @endif
                        </p>
                        @if(!($isTeacher && $subjects->isEmpty()))
                            <a href="{{ $createRoute }}"
                               class="ajax-link inline-flex items-center px-4 py-2 mt-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                                <i class="fas fa-plus mr-2"></i>
                                {{ $isAdmin ? 'Create Question' : 'Add New Question' }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize AJAX links
            initializeAjaxLinks();

            // Initialize AJAX forms
            initializeAjaxForms();

            // Initialize filter form
            initializeFilterForm();
        });

        function initializeAjaxLinks() {
            document.querySelectorAll('.ajax-link').forEach(link => {
                link.removeEventListener('click', handleAjaxLinkClick);
                link.addEventListener('click', handleAjaxLinkClick);
            });
        }

        function handleAjaxLinkClick(e) {
            e.preventDefault();
            e.stopPropagation();

            const url = this.href;
            loadContent(url);
        }

        function initializeAjaxForms() {
            document.querySelectorAll('.ajax-form').forEach(form => {
                form.removeEventListener('submit', handleAjaxFormSubmit);
                form.addEventListener('submit', handleAjaxFormSubmit);
            });
        }

        function handleAjaxFormSubmit(e) {
            e.preventDefault();

            const form = e.target;
            const url = form.action;
            const method = form.method;
            const formData = new FormData(form);

            // Show loading
            showLoading();

            fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.text())
                .then(html => {
                    // For form submissions, reload the questions list
                    const currentUrl = window.location.href;
                    loadContent(currentUrl);
                })
                .catch(error => {
                    console.error('Form submission failed:', error);
                    window.location.reload();
                });
        }

        function initializeFilterForm() {
            const filterForm = document.getElementById('filter-form');
            if (filterForm) {
                filterForm.removeEventListener('submit', handleFilterSubmit);
                filterForm.addEventListener('submit', handleFilterSubmit);

                // Also trigger on input changes for real-time filtering
                const inputs = filterForm.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.removeEventListener('change', handleFilterChange);
                    input.addEventListener('change', handleFilterChange);
                });
            }
        }

        function handleFilterSubmit(e) {
            e.preventDefault();
            applyFilters();
        }

        function handleFilterChange() {
            // Debounce the filter application
            clearTimeout(window.filterTimeout);
            window.filterTimeout = setTimeout(applyFilters, 500);
        }

        function applyFilters() {
            const form = document.getElementById('filter-form');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);

            // Get the base URL based on user role
            const baseUrl = '{{ $indexRoute }}';
            const url = baseUrl + '?' + params.toString();
            loadContent(url);
        }

        function loadContent(url) {
            // Show loading indicator
            showLoading();

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.text())
                .then(html => {
                    // Update main content area
                    document.querySelector('main').innerHTML = html;

                    // Update browser history
                    history.pushState(null, null, url);

                    // Re-initialize AJAX handlers
                    initializeAjaxLinks();
                    initializeAjaxForms();
                    initializeFilterForm();
                })
                .catch(error => {
                    console.error('AJAX loading failed:', error);
                    window.location.href = url;
                });
        }

        function showLoading() {
            document.querySelector('main').innerHTML = `
                <div class="flex justify-center items-center h-64">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                </div>
            `;
        }

        // Handle browser back/forward buttons
        window.addEventListener('popstate', function() {
            loadContent(window.location.href);
        });
    </script>
@endpush
