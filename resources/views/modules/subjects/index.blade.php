@extends('layouts.app')

@section('title', 'All Subjects - Lincoln High School')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Academic Subjects</h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-1">Manage all UNEB-approved subjects at Lincoln High School</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <a href="{{ route('admin.subjects.create') }}"
                       class="ajax-link inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add New Subject
                    </a>

                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Subjects</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $analyticsSubjects->count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Compulsory</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $analyticsSubjects->where('type', 'compulsory')->count() }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Elective</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $analyticsSubjects->where('type', 'elective')->count() }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Optional</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $analyticsSubjects->where('type', 'optional')->count() }}
                </p>
            </div>

        </div>

        <!-- Subjects Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">All Subjects</h2>
            </div>

            <!-- Search and Filters -->
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                <form id="filterForm" method="GET" action="{{ route('admin.subjects.index') }}">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text"
                                   name="search"
                                   value="{{ $filters['search'] ?? '' }}"
                                   placeholder="Search subjects by name or code..."
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ajax-filter">
                        </div>
                        <select name="type" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ajax-filter">
                            <option value="">All Types</option>
                            <option value="compulsory" {{ ($filters['type'] ?? '') == 'compulsory' ? 'selected' : '' }}>Compulsory</option>
                            <option value="elective" {{ ($filters['type'] ?? '') == 'elective' ? 'selected' : '' }}>Elective</option>
                            <option value="optional" {{ ($filters['type'] ?? '') == 'optional' ? 'selected' : '' }}>Optional</option>
                        </select>
                        <select name="status" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ajax-filter">
                            <option value="">All Status</option>
                            <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Table Content (Will be loaded via AJAX) -->
            <div id="table-container">
                @include('modules.subjects.partials.table')
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900">
                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-2">Delete Subject</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Are you sure you want to delete this subject? This action cannot be undone.</p>
            <div class="flex justify-center space-x-3 mt-4">
                <button id="cancelDelete" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        Delete
                    </button>
                </form>
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
    function loadTableContent(url = null) {
        // Cancel previous request if it exists
        if (currentRequest) {
            currentRequest.abort();
        }

        const formData = new FormData(document.getElementById('filterForm'));
        const queryString = new URLSearchParams(formData).toString();
        const targetUrl = url || `{{ route('admin.subjects.index') }}?${queryString}`;

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
                throw new Error(`HTTP error! status: ${response.status}`);
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
            // Show error message
            tableContainer.innerHTML = `
                <div class="text-center py-12">
                    <div class="text-red-600 dark:text-red-400 mb-2">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">Failed to load content. Please try again.</p>
                    <button onclick="loadTableContent()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Retry
                    </button>
                </div>
            `;
            currentRequest = null;
        });
    }

    // Delete Modal Functions
    function showDeleteModal(subjectId, subjectName) {
        const deleteUrl = `{{ route('admin.subjects.destroy', '') }}/${subjectId}`;

        document.getElementById('deleteForm').action = deleteUrl;
        document.querySelector('#deleteModal p').textContent =
            `Are you sure you want to delete "${subjectName}"? This action cannot be undone.`;

        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function hideDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Event Listeners Attachment
    function attachEventListeners() {
        // Delete buttons
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const subjectId = this.getAttribute('data-id');
                const subjectName = this.getAttribute('data-name');
                showDeleteModal(subjectId, subjectName);
            });
        });

        // Pagination links
        document.querySelectorAll('.pagination-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.href;
                loadTableContent(url);
            });
        });

        // Navigation links (create, edit, etc.)
        document.querySelectorAll('.ajax-nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.href;

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
                    document.getElementById('main-content').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error loading page:', error);
                    // Fallback to normal page load
                    window.location.href = url;
                });
            });
        });
    }

    // Modal event listeners (only attach once)
    document.getElementById('cancelDelete').addEventListener('click', hideDeleteModal);

    document.getElementById('deleteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const url = form.action;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(new FormData(form))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                hideDeleteModal();
                loadTableContent();
                // You can add a toast notification here
                showToast(data.message, 'success');
            } else {
                showToast(data.message || 'Failed to delete subject', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to delete subject. Please try again.', 'error');
        });
    });

    // Close modal when clicking outside
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideDeleteModal();
        }
    });

    // Toast notification function
    function showToast(message, type = 'info') {
        // Simple toast implementation - you can replace with your preferred toast library
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-6 py-3 rounded-md text-white ${
            type === 'success' ? 'bg-green-600' :
            type === 'error' ? 'bg-red-600' : 'bg-blue-600'
        } z-50`;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Initial attachment
    attachEventListeners();
});
</script>
@endsection
