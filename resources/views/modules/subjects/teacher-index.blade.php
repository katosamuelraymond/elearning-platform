@extends('layouts.app')

@section('title', 'My Subjects - Lincoln High School')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Teaching Subjects</h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-1">Subjects assigned to you for teaching</p>
                    </div>
                    <div class="mt-4 sm:mt-0">
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        Teaching {{ count($subjects) }} subject(s)
                    </span>
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Assignments</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $analyticsAssignments->count() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Unique Subjects</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $analyticsAssignments->unique('subject_id')->count() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Classes</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $analyticsAssignments->unique('class_id')->count() }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">As Class Teacher</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $analyticsAssignments->where('role', 'class_teacher')->count() }}
                    </p>
                </div>
            </div>

            <!-- Subjects Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">My Subjects</h2>
                </div>

                <!-- Search and Filters -->
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <form id="filterForm" method="GET" action="{{ route('teacher.subjects.index') }}">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <input type="text"
                                       name="search"
                                       value="{{ $filters['search'] ?? '' }}"
                                       placeholder="Search my subjects..."
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ajax-filter">
                            </div>
                            <select name="type" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ajax-filter">
                                <option value="">All Types</option>
                                <option value="compulsory" {{ ($filters['type'] ?? '') == 'compulsory' ? 'selected' : '' }}>Compulsory</option>
                                <option value="elective" {{ ($filters['type'] ?? '') == 'elective' ? 'selected' : '' }}>Elective</option>
                                <option value="optional" {{ ($filters['type'] ?? '') == 'optional' ? 'selected' : '' }}>Optional</option>
                            </select>
                        </div>
                    </form>
                </div>

                <!-- Table Content -->
                <div id="table-container">
                    @include('modules.subjects.partials.teacher-table')
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentRequest = null;

            // AJAX Filtering with debounce
            const filterInputs = document.querySelectorAll('.ajax-filter');
            let filterTimeout;

            filterInputs.forEach(input => {
                input.addEventListener('input', function() {
                    clearTimeout(filterTimeout);
                    filterTimeout = setTimeout(() => {
                        loadTableContent();
                    }, 300);
                });
            });

            // AJAX Table Loading
            // AJAX Table Loading
            // AJAX Table Loading
            function loadTableContent(url = null) {
                // Cancel previous request if it exists
                if (currentRequest) {
                    currentRequest.abort();
                }

                const formData = new FormData(document.getElementById('filterForm'));
                const queryString = new URLSearchParams(formData).toString();
                const targetUrl = url || `{{ route('teacher.subjects.index') }}?${queryString}`;

                // Show loading state
                const tableContainer = document.getElementById('table-container');
                tableContainer.innerHTML = `
        <div class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-gray-600 dark:text-gray-400">Loading...</span>
        </div>
    `;

                currentRequest = new AbortController();

                fetch(targetUrl, {
                    signal: currentRequest.signal,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'text/html'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            // Get more details about the error
                            return response.text().then(text => {
                                throw new Error(`HTTP error! status: ${response.status}, response: ${text.substring(0, 200)}`);
                            });
                        }
                        return response.text();
                    })
                    .then(html => {
                        tableContainer.innerHTML = html;
                        attachEventListeners();
                        currentRequest = null;
                    })
                    .catch(error => {
                        if (error.name === 'AbortError') {
                            console.log('Request aborted');
                            return;
                        }
                        console.error('Error loading table content:', error);
                        tableContainer.innerHTML = `
            <div class="text-center py-12">
                <div class="text-red-600 dark:text-red-400 mb-2">
                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Error Loading Content</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Failed to load subjects. Please try again.</p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mb-4">Error: ${error.message}</p>
                <button onclick="loadTableContent()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Retry
                </button>
            </div>
        `;
                        currentRequest = null;
                    });
            }

            // Event Listeners Attachment
            // Event Listeners Attachment
            function attachEventListeners() {
                // Pagination links
                document.querySelectorAll('.pagination-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = this.href;
                        loadTableContent(url);
                    });
                });

                // Navigation links (view, lessons, etc.)
                document.querySelectorAll('.ajax-nav-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = this.href;

                        // Show loading state for main content
                        const mainContent = document.getElementById('main-content');
                        mainContent.innerHTML = `
                <div class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="ml-2 text-gray-600 dark:text-gray-400">Loading...</span>
                </div>
            `;

                        fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'text/html'
                            }
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                }
                                return response.text();
                            })
                            .then(html => {
                                // Replace the main content area with the FULL page content
                                document.getElementById('main-content').innerHTML = html;
                                // Re-initialize event listeners for the new content
                                initializePageScripts();
                                // Update browser URL without reload
                                window.history.pushState({}, '', url);
                            })
                            .catch(error => {
                                console.error('Error loading page:', error);
                                // Fallback to normal page load
                                window.location.href = url;
                            });
                    });
                });
            }

// Function to initialize page-specific scripts
            function initializePageScripts() {
                // Re-attach event listeners for the new content
                attachEventListeners();

                // If you have any other page-specific initialization code, put it here
                console.log('Page scripts re-initialized');
            }
        });
    </script>
@endsection
