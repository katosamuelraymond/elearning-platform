@extends('layouts.app')

@section('title', 'User Management - Lincoln eLearning')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">User Management</h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-2">Manage all users in the system</p>
                </div>
                <a href="{{ route('admin.users.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center"
                   data-no-ajax>
                    <i class="fas fa-plus mr-2"></i>Create User
                </a>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                <form id="search-form" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Users</label>
                        <div class="relative">
                            <input type="text"
                                   id="search"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Search by name or email..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    <div class="sm:w-48">
                        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                        <select id="role" name="role" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">All Roles</option>
                            <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                            <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div class="sm:w-48">
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                        @if(request()->hasAny(['search', 'role', 'status']))
                        <a href="{{ route('admin.users.index') }}"
                           class="ml-2 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center ajax-link">
                            <i class="fas fa-times mr-2"></i>Clear
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table Container for AJAX updates -->
        <div id="users-table-container">
            @include('modules.users.partials.users-table', ['users' => $users])
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <div class="mt-4 text-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Delete User</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-500 dark:text-gray-300">
                        Are you sure you want to delete <span id="userName" class="font-semibold text-gray-900 dark:text-white"></span>? This action cannot be undone.
                    </p>
                </div>
            </div>
            <div class="mt-6 flex justify-center space-x-3">
                <button type="button" id="cancelDelete" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-md transition-colors duration-200">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-colors duration-200 flex items-center">
                        <i class="fas fa-trash mr-2"></i> Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('User Management initialized');

    // Define modal functions first
    let deleteModal, deleteForm, userNameElement, cancelDeleteBtn;

    function showDeleteModal(userId, userName, deleteUrl) {
        if (!deleteForm || !userNameElement) {
            console.error('Modal elements not found');
            return;
        }
        deleteForm.action = deleteUrl;
        userNameElement.textContent = userName;
        deleteModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideDeleteModal() {
        if (!deleteModal) return;
        deleteModal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Initialize everything
    initializeUserManagement();

    function initializeUserManagement() {
        initializeModal();
        initializeSearchAndFilters();
        initializePagination();
        initializeAJAXLinks();
    }

    function initializeModal() {
        deleteModal = document.getElementById('deleteModal');
        deleteForm = document.getElementById('deleteForm');
        userNameElement = document.getElementById('userName');
        cancelDeleteBtn = document.getElementById('cancelDelete');

        if (!deleteModal || !deleteForm || !userNameElement || !cancelDeleteBtn) {
            console.error('Modal elements not found');
            return;
        }

        // Cancel button
        cancelDeleteBtn.addEventListener('click', hideDeleteModal);

        // Close modal when clicking outside
        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                hideDeleteModal();
            }
        });

        // Escape key to close modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !deleteModal.classList.contains('hidden')) {
                hideDeleteModal();
            }
        });

        // Form submission
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitDeleteForm(this);
        });
    }

    function initializeSearchAndFilters() {
        const searchForm = document.getElementById('search-form');
        const searchInput = document.getElementById('search');
        const roleFilter = document.getElementById('role');
        const statusFilter = document.getElementById('status');

        let searchTimeout;

        // Real-time search
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performSearch();
                }, 500);
            });
        }

        // Filter changes
        if (roleFilter) {
            roleFilter.addEventListener('change', performSearch);
        }

        if (statusFilter) {
            statusFilter.addEventListener('change', performSearch);
        }

        // Form submission
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                performSearch();
            });
        }
    }

    function initializePagination() {
        document.addEventListener('click', function(e) {
            if (e.target.closest('.ajax-pagination a') ||
                (e.target.closest('a') && e.target.closest('a').classList.contains('page-link'))) {
                e.preventDefault();
                const url = e.target.closest('a').href;
                loadPage(url);
            }
        });
    }

    function initializeAJAXLinks() {
        document.addEventListener('click', function(e) {
            if (e.target.closest('.ajax-link') && !e.target.closest('.ajax-link').hasAttribute('data-no-ajax')) {
                e.preventDefault();
                const url = e.target.closest('.ajax-link').href;
                loadPage(url);
            }

            // Delete buttons in table
            if (e.target.closest('.delete-user-btn')) {
                e.preventDefault();
                const button = e.target.closest('.delete-user-btn');
                const userId = button.getAttribute('data-user-id');
                const userName = button.getAttribute('data-user-name');
                const deleteUrl = button.getAttribute('data-delete-url');

                showDeleteModal(userId, userName, deleteUrl);
            }
        });
    }

    function performSearch() {
        const searchForm = document.getElementById('search-form');
        if (!searchForm) return;

        const formData = new FormData(searchForm);
        const params = new URLSearchParams(formData);
        const url = '{{ route('admin.users.index') }}?' + params.toString();
        loadPage(url);
    }

    function loadPage(url) {
        showLoading();

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json, text/html'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            const contentType = response.headers.get('content-type');

            if (contentType && contentType.includes('application/json')) {
                return response.json().then(data => {
                    if (data.success && data.html) {
                        return data.html;
                    }
                    throw new Error('Invalid JSON response');
                });
            }
            return response.text();
        })
        .then(html => {
            const container = document.getElementById('users-table-container');
            if (container) {
                container.innerHTML = html;
                window.history.pushState({ url: url }, '', url);

                // Re-initialize event listeners for new content
                setTimeout(() => {
                    initializePagination();
                    initializeAJAXLinks();
                }, 100);

                showNotification('Page updated successfully', 'success');
            }
        })
        .catch(error => {
            console.error('AJAX Error:', error);
            showNotification('Error loading page', 'error');
            // Fallback to full page load if AJAX fails
            setTimeout(() => {
                window.location.href = url;
            }, 2000);
        })
        .finally(() => {
            hideLoading();
        });
    }

    function submitDeleteForm(form) {
        const formData = new FormData(form);
        const url = form.action;

        showLoading();
        hideDeleteModal();

        fetch(url, {
            method: 'DELETE',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                loadPage('{{ route('admin.users.index') }}');
                showNotification(data.message || 'User deleted successfully', 'success');
            } else {
                showNotification(data.message || 'Delete failed', 'error');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showNotification('Error deleting user', 'error');
        })
        .finally(() => {
            hideLoading();
        });
    }

    function showLoading() {
        hideLoading();
        const loading = document.createElement('div');
        loading.id = 'ajax-loading';
        loading.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50';
        loading.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex items-center space-x-3 shadow-lg">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Loading...</span>
            </div>
        `;
        document.body.appendChild(loading);
    }

    function hideLoading() {
        const loading = document.getElementById('ajax-loading');
        if (loading && loading.parentElement) {
            loading.remove();
        }
    }

    function showNotification(message, type = 'info') {
        // Remove existing notification
        const existing = document.getElementById('ajax-notification');
        if (existing) existing.remove();

        const bgColor = type === 'success' ? 'bg-green-500' :
                       type === 'error' ? 'bg-red-500' :
                       'bg-blue-500';
        const icon = type === 'success' ? 'fa-check-circle' :
                    type === 'error' ? 'fa-exclamation-triangle' :
                    'fa-info-circle';

        const notification = document.createElement('div');
        notification.id = 'ajax-notification';
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${bgColor} text-white`;
        notification.innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="fas ${icon} text-lg"></i>
                <span class="font-medium">${message}</span>
                <button class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        // Add click event to close button
        notification.querySelector('button').addEventListener('click', function() {
            notification.remove();
        });

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function(event) {
        if (event.state && event.state.url) {
            loadPage(event.state.url);
        }
    });
});
</script>
@endsection
