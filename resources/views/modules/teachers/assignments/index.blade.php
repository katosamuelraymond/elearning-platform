@extends('layouts.app')

@section('title', 'Teacher Assignments - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-4 sm:py-8">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
            <!-- Header -->
            <div class="mb-6 sm:mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white truncate">Teacher Assignments</h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-1 text-sm sm:text-base">
                            Manage teacher assignments to classes and subjects
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('admin.teacher-assignments.create') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center px-4 sm:px-6 py-2.5 sm:py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 hover:scale-105 hover:shadow-lg text-sm sm:text-base">
                            <i class="fas fa-plus mr-2 text-xs sm:text-sm"></i>
                            New Assignment
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filters Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-6">
                <form id="filterForm" method="GET" action="{{ route('admin.teacher-assignments.index') }}">
                    <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                        <!-- Search -->
                        <div class="xs:col-span-2 lg:col-span-1">
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Search
                            </label>
                            <input type="text" name="search" id="search"
                                   value="{{ request('search') }}"
                                   placeholder="Search teachers, subjects..."
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm sm:text-base px-3 py-2">
                        </div>

                        <!-- Academic Year -->
                        <div>
                            <label for="academic_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Academic Year
                            </label>
                            <select name="academic_year" id="academic_year"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm sm:text-base px-3 py-2">
                                <option value="">All Years</option>
                                @for($year = date('Y') - 2; $year <= date('Y') + 1; $year++)
                                    @php
                                        $academicYear = $year . '/' . ($year + 1);
                                        $selected = request('academic_year') == $academicYear ? 'selected' : '';
                                    @endphp
                                    <option value="{{ $academicYear }}" {{ $selected }}>
                                        {{ $academicYear }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Role -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Role
                            </label>
                            <select name="role" id="role"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm sm:text-base px-3 py-2">
                                <option value="">All Roles</option>
                                @foreach($roles as $value => $label)
                                    <option value="{{ $value }}" {{ request('role') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status
                            </label>
                            <select name="status" id="status"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm sm:text-base px-3 py-2">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col xs:flex-row justify-end space-y-2 xs:space-y-0 xs:space-x-3 mt-4">
                        <a href="{{ route('admin.teacher-assignments.index') }}"
                           class="order-2 xs:order-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-center text-sm sm:text-base">
                            Reset Filters
                        </a>
                        <button type="submit"
                                class="order-1 xs:order-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm sm:text-base">
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="p-2 sm:p-3 bg-blue-100 dark:bg-blue-900 rounded-lg mr-3 sm:mr-4">
                            <i class="fas fa-chalkboard-teacher text-blue-600 dark:text-blue-400 text-lg sm:text-xl"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Total Assignments</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white truncate">{{ $assignments->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="p-2 sm:p-3 bg-green-100 dark:bg-green-900 rounded-lg mr-3 sm:mr-4">
                            <i class="fas fa-users text-green-600 dark:text-green-400 text-lg sm:text-xl"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Active Teachers</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white truncate">{{ $teachers->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="p-2 sm:p-3 bg-purple-100 dark:bg-purple-900 rounded-lg mr-3 sm:mr-4">
                            <i class="fas fa-book text-purple-600 dark:text-purple-400 text-lg sm:text-xl"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Subjects</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white truncate">{{ $subjects->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="p-2 sm:p-3 bg-orange-100 dark:bg-orange-900 rounded-lg mr-3 sm:mr-4">
                            <i class="fas fa-door-open text-orange-600 dark:text-orange-400 text-lg sm:text-xl"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Classes</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white truncate">{{ $classes->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignments Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <div class="min-w-[900px]">
                        <table class="w-full">
                            <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                    Teacher
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                    Subject
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                    Class
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                    Role
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                    Year
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                    Status
                                </th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                    Actions
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($assignments as $assignment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <div class="flex items-center min-w-0">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                @if($assignment->teacher->profile_image)
                                                    <img class="h-8 w-8 rounded-full object-cover"
                                                         src="{{ asset('storage/' . $assignment->teacher->profile_image) }}"
                                                         alt="{{ $assignment->teacher->name }}">
                                                @else
                                                    <div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                                        <span class="text-blue-600 dark:text-blue-400 font-medium text-xs">
                                                            {{ substr($assignment->teacher->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-2 min-w-0 flex-1">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                    {{ $assignment->teacher->name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                                    {{ $assignment->teacher->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <div class="min-w-0">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $assignment->subject->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $assignment->subject->code }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <div class="min-w-0">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $assignment->class->name }}
                                            </div>
                                            @if($assignment->stream)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $assignment->stream->name }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($assignment->role === 'class_teacher') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @elseif($assignment->role === 'subject_teacher') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @else bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                            @endif">
                                            {{ $roles[$assignment->role] }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $assignment->academic_year }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <button type="button"
                                                class="toggle-status-btn inline-flex px-2 py-1 text-xs font-semibold rounded-full transition-colors cursor-pointer
                                                    @if($assignment->is_active)
                                                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 hover:bg-green-200 dark:hover:bg-green-800
                                                    @else
                                                        bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 hover:bg-red-200 dark:hover:bg-red-800
                                                    @endif"
                                                data-assignment-id="{{ $assignment->id }}">
                                            {{ $assignment->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-1">
                                            <a href="{{ route('admin.teacher-assignments.edit', $assignment) }}"
                                               class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors p-1 rounded"
                                               title="Edit">
                                                <i class="fas fa-edit text-sm"></i>
                                            </a>
                                            <button type="button"
                                                    class="delete-assignment-btn text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors p-1 rounded"
                                                    data-assignment-id="{{ $assignment->id }}"
                                                    data-teacher-name="{{ $assignment->teacher->name }}"
                                                    data-subject-name="{{ $assignment->subject->name }}"
                                                    data-class-name="{{ $assignment->class->name }}"
                                                    data-academic-year="{{ $assignment->academic_year }}"
                                                    title="Delete">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-chalkboard-teacher text-4xl mb-4 text-gray-300 dark:text-gray-600"></i>
                                            <p class="text-lg font-medium mb-2">No assignments found</p>
                                            <p class="text-sm mb-4">Get started by creating your first teacher assignment.</p>
                                            <a href="{{ route('admin.teacher-assignments.create') }}"
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                                <i class="fas fa-plus mr-2"></i>
                                                Create Assignment
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                @if($assignments->hasPages())
                    <div class="bg-white dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $assignments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3 text-center">
                <!-- Warning Icon -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                </div>

                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Delete Assignment</h3>

                <div class="mt-2 px-4 py-3">
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                        Are you sure you want to delete this teacher assignment? This action cannot be undone.
                    </p>

                    <!-- Assignment Details -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mb-4 text-left">
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            <div class="font-medium" id="modalTeacherName"></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <span id="modalSubjectName"></span> •
                                <span id="modalClassName"></span> •
                                <span id="modalAcademicYear"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-3 px-4 py-3">
                    <button id="modalCancelButton"
                            class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 text-sm font-medium rounded-md transition-colors">
                        Cancel
                    </button>
                    <button id="modalConfirmButton"
                            class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors flex items-center justify-center">
                        <i class="fas fa-trash mr-2"></i>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for AJAX functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // AJAX filtering
            const filterForm = document.getElementById('filterForm');
            const tableContainer = document.getElementById('assignments-table-container');

            // Modal elements
            const deleteModal = document.getElementById('deleteConfirmationModal');
            const modalCancelButton = document.getElementById('modalCancelButton');
            const modalConfirmButton = document.getElementById('modalConfirmButton');
            const modalTeacherName = document.getElementById('modalTeacherName');
            const modalSubjectName = document.getElementById('modalSubjectName');
            const modalClassName = document.getElementById('modalClassName');
            const modalAcademicYear = document.getElementById('modalAcademicYear');

            let currentAssignmentId = null;
            let currentDeleteButton = null;

            // Debounce function to prevent too many requests
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // Show modal function
            function showDeleteModal(button) {
                currentAssignmentId = button.dataset.assignmentId;
                currentDeleteButton = button;

                // Populate modal with assignment details
                modalTeacherName.textContent = button.dataset.teacherName;
                modalSubjectName.textContent = button.dataset.subjectName;
                modalClassName.textContent = button.dataset.className;
                modalAcademicYear.textContent = button.dataset.academicYear;

                // Show modal
                deleteModal.classList.remove('hidden');
                deleteModal.classList.add('flex');
            }

            // Hide modal function
            function hideDeleteModal() {
                deleteModal.classList.add('hidden');
                deleteModal.classList.remove('flex');
                currentAssignmentId = null;
                currentDeleteButton = null;
            }

            // Handle filter changes with AJAX
            const handleFilterChange = debounce(function() {
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);

                // Show loading state
                tableContainer.innerHTML = `
                    <div class="flex justify-center items-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    </div>
                `;

                fetch(`{{ route('admin.teacher-assignments.index') }}?${params}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.text())
                    .then(html => {
                        tableContainer.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        tableContainer.innerHTML = `
                            <div class="flex justify-center items-center py-12">
                                <div class="text-center">
                                    <i class="fas fa-exclamation-triangle text-red-500 text-3xl mb-2"></i>
                                    <p class="text-gray-600 dark:text-gray-400">Failed to load data</p>
                                </div>
                            </div>
                        `;
                    });
            }, 300);

            // Add event listeners to filter inputs
            const filterInputs = filterForm.querySelectorAll('input, select');
            filterInputs.forEach(input => {
                input.addEventListener('change', handleFilterChange);
                if (input.type === 'text') {
                    input.addEventListener('input', handleFilterChange);
                }
            });

            // Handle assignment status toggling
            document.addEventListener('click', function(e) {
                if (e.target.closest('.toggle-status-btn')) {
                    e.preventDefault();
                    const button = e.target.closest('.toggle-status-btn');
                    const assignmentId = button.dataset.assignmentId;
                    const url = `{{ route('admin.teacher-assignments.toggle-status', '') }}/${assignmentId}`;

                    // Show loading state on button
                    const originalHTML = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    button.disabled = true;

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Reload the table to reflect changes
                                handleFilterChange();
                            } else {
                                alert('Failed to update status');
                                button.innerHTML = originalHTML;
                                button.disabled = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to update status');
                            button.innerHTML = originalHTML;
                            button.disabled = false;
                        });
                }
            });

            // Handle assignment deletion button click
            document.addEventListener('click', function(e) {
                if (e.target.closest('.delete-assignment-btn')) {
                    e.preventDefault();
                    const button = e.target.closest('.delete-assignment-btn');
                    showDeleteModal(button);
                }
            });

            // Modal event listeners
            modalCancelButton.addEventListener('click', hideDeleteModal);

            modalConfirmButton.addEventListener('click', function() {
                if (!currentAssignmentId) return;

                // Show loading state on modal button
                const originalHTML = modalConfirmButton.innerHTML;
                modalConfirmButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                modalConfirmButton.disabled = true;

                const url = `{{ route('admin.teacher-assignments.destroy', '') }}/${currentAssignmentId}`;

                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Hide modal and reload the table
                            hideDeleteModal();
                            handleFilterChange();
                        } else {
                            alert('Failed to delete assignment');
                            modalConfirmButton.innerHTML = originalHTML;
                            modalConfirmButton.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to delete assignment');
                        modalConfirmButton.innerHTML = originalHTML;
                        modalConfirmButton.disabled = false;
                    });
            });

            // Close modal when clicking outside
            deleteModal.addEventListener('click', function(e) {
                if (e.target === deleteModal) {
                    hideDeleteModal();
                }
            });

            // Handle pagination clicks
            document.addEventListener('click', function(e) {
                if (e.target.closest('.pagination a') || e.target.closest('nav a')) {
                    e.preventDefault();
                    const link = e.target.closest('a');
                    const url = link.getAttribute('href');

                    // Show loading state
                    tableContainer.innerHTML = `
                        <div class="flex justify-center items-center py-12">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                        </div>
                    `;

                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => response.text())
                        .then(html => {
                            tableContainer.innerHTML = html;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            tableContainer.innerHTML = `
                                <div class="flex justify-center items-center py-12">
                                    <div class="text-center">
                                        <i class="fas fa-exclamation-triangle text-red-500 text-3xl mb-2"></i>
                                        <p class="text-gray-600 dark:text-gray-400">Failed to load data</p>
                                    </div>
                                </div>
                            `;
                        });
                }
            });
        });
    </script>

    <style>
        /* Custom breakpoint for extra small screens */
        @media (min-width: 475px) {
            .xs\:grid-cols-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .xs\:col-span-2 {
                grid-column: span 2 / span 2;
            }
            .xs\:flex-row {
                flex-direction: row;
            }
            .xs\:space-x-3 > :not([hidden]) ~ :not([hidden]) {
                --tw-space-x-reverse: 0;
                margin-right: calc(0.75rem * var(--tw-space-x-reverse));
                margin-left: calc(0.75rem * calc(1 - var(--tw-space-x-reverse)));
            }
            .xs\:space-y-0 > :not([hidden]) ~ :not([hidden]) {
                --tw-space-y-reverse: 0;
                margin-top: calc(0px * calc(1 - var(--tw-space-y-reverse)));
                margin-bottom: calc(0px * var(--tw-space-y-reverse));
            }
        }

        /* Ensure table is scrollable on mobile */
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }

        /* Custom scrollbar for webkit browsers */
        .overflow-x-auto::-webkit-scrollbar {
            height: 6px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Dark mode scrollbar */
        .dark .overflow-x-auto::-webkit-scrollbar-track {
            background: #374151;
        }

        .dark .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #6b7280;
        }

        .dark .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* Improve touch targets on mobile */
        @media (max-width: 640px) {
            button, a, select, input {
                min-height: 44px;
            }

            .pagination a, .pagination span {
                padding: 8px 12px;
                min-width: 44px;
            }

            /* Reduce padding on mobile for table cells */
            .px-4 {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .py-3 {
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }
        }

        /* Ensure table has proper minimum width */
        .min-w-\[900px\] {
            min-width: 900px;
        }

        /* Smooth transitions for dark mode */
        .transition-colors {
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
        }

        /* Modal animations */
        #deleteConfirmationModal {
            transition: opacity 0.3s ease;
        }
    </style>
@endsection
