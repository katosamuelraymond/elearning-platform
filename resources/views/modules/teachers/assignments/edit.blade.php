@extends('layouts.app')

@section('title', 'Edit Teacher Assignment - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <a href="{{ route('admin.teacher-assignments.index') }}"
                           class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-arrow-left mr-2 text-blue-600"></i>
                            Back to Assignments
                        </a>
                    </div>
                    <div class="text-right">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Teacher Assignment</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Update assignment details for {{ $assignment->teacher->name }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Assignment Info Card -->
            <div class="mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-2">Teacher</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $assignment->teacher->name }}</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-2">Subject</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $assignment->subject->name }}</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                <i class="fas fa-chalkboard"></i>
                            </div>
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-2">Class</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $assignment->class->name }}</div>
                        </div>
                        <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-2">Academic Year</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $assignment->academic_year }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-lg p-4 animate-fade-in">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500 text-lg mt-0.5"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-semibold text-red-800 dark:text-red-400">
                                Validation Errors
                            </h3>
                            <div class="mt-1 text-sm text-red-700 dark:text-red-300">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Form Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <form action="{{ route('admin.teacher-assignments.update', $assignment) }}" method="POST" id="editAssignmentForm">
                    @csrf
                    @method('PUT')

                    <!-- Form Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                        <h2 class="text-lg font-semibold text-white">Edit Assignment Details</h2>
                        <p class="text-blue-100 text-sm mt-1">Update the assignment information as needed</p>
                    </div>

                    <div class="p-6 space-y-8">
                        <!-- Basic Information Section -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                <i class="fas fa-edit mr-2 text-blue-600"></i>
                                Assignment Information
                            </h3>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Teacher Selection -->
                                <div>
                                    <label for="teacher_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-user-tie mr-1 text-gray-400"></i>
                                        Teacher *
                                    </label>
                                    <select id="teacher_id" name="teacher_id" required
                                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                        <option value="">Select a teacher</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}"
                                                {{ old('teacher_id', $assignment->teacher_id) == $teacher->id ? 'selected' : '' }}
                                                {{ $teacher->id == $assignment->teacher_id ? 'data-current="true"' : '' }}>
                                                {{ $teacher->name }} - {{ $teacher->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Subject Selection -->
                                <div>
                                    <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-book mr-1 text-gray-400"></i>
                                        Subject *
                                    </label>
                                    <select id="subject_id" name="subject_id" required
                                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                        <option value="">Select a subject</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                {{ old('subject_id', $assignment->subject_id) == $subject->id ? 'selected' : '' }}
                                                {{ $subject->id == $assignment->subject_id ? 'data-current="true"' : '' }}>
                                                {{ $subject->name }} ({{ $subject->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Class Selection -->
                                <div>
                                    <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-chalkboard mr-1 text-gray-400"></i>
                                        Class *
                                    </label>
                                    <select id="class_id" name="class_id" required
                                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                        <option value="">Select a class</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ old('class_id', $assignment->class_id) == $class->id ? 'selected' : '' }}
                                                {{ $class->id == $assignment->class_id ? 'data-current="true"' : '' }}>
                                                {{ $class->name }} ({{ $class->level }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Stream Selection -->
                                <div>
                                    <label for="stream_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-stream mr-1 text-gray-400"></i>
                                        Stream
                                    </label>
                                    <select id="stream_id" name="stream_id"
                                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                        <option value="">No stream</option>
                                        @foreach($streams as $stream)
                                            <option value="{{ $stream->id }}"
                                                {{ old('stream_id', $assignment->stream_id) == $stream->id ? 'selected' : '' }}
                                                {{ $stream->id == $assignment->stream_id ? 'data-current="true"' : '' }}>
                                                {{ $stream->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Academic Year -->
                                <div>
                                    <label for="academic_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-calendar-alt mr-1 text-gray-400"></i>
                                        Academic Year *
                                    </label>
                                    <select id="academic_year" name="academic_year" required
                                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                        <option value="">Select academic year</option>
                                        @for($year = date('Y') - 1; $year <= date('Y') + 1; $year++)
                                            @php
                                                $academicYear = $year . '/' . ($year + 1);
                                                $selected = old('academic_year', $assignment->academic_year) == $academicYear ? 'selected' : '';
                                            @endphp
                                            <option value="{{ $academicYear }}" {{ $selected }}>
                                                {{ $academicYear }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                <!-- Role Selection -->
                                <div>
                                    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-briefcase mr-1 text-gray-400"></i>
                                        Role *
                                    </label>
                                    <select id="role" name="role" required
                                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                        <option value="">Select role</option>
                                        @foreach($roles as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('role', $assignment->role) == $value ? 'selected' : '' }}
                                                {{ $value == $assignment->role ? 'data-current="true"' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Status Section -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                <i class="fas fa-toggle-on mr-2 text-green-600"></i>
                                Assignment Status
                            </h3>

                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30">
                                        <i class="fas fa-power-off text-blue-600"></i>
                                    </div>
                                    <div>
                                        <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Assignment Status
                                        </label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Activate or deactivate this assignment
                                        </p>
                                    </div>
                                </div>
                                <div class="relative inline-block w-12 h-6">
                                    <input type="checkbox" id="is_active" name="is_active" value="1"
                                           {{ old('is_active', $assignment->is_active) ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-12 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </div>
                            </div>

                            <!-- Current Status Badge -->
                            <div class="flex items-center space-x-3 p-3 bg-{{ $assignment->is_active ? 'green' : 'red' }}-50 dark:bg-{{ $assignment->is_active ? 'green' : 'red' }}-900/20 rounded-lg border border-{{ $assignment->is_active ? 'green' : 'red' }}-200 dark:border-{{ $assignment->is_active ? 'green' : 'red' }}-800">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-{{ $assignment->is_active ? 'check-circle' : 'times-circle' }} text-{{ $assignment->is_active ? 'green' : 'red' }}-500 text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-{{ $assignment->is_active ? 'green' : 'red' }}-800 dark:text-{{ $assignment->is_active ? 'green' : 'red' }}-400">
                                        This assignment is currently <span class="font-bold">{{ $assignment->is_active ? 'ACTIVE' : 'INACTIVE' }}</span>
                                    </p>
                                    <p class="text-xs text-{{ $assignment->is_active ? 'green' : 'red' }}-700 dark:text-{{ $assignment->is_active ? 'green' : 'red' }}-300 mt-1">
                                        {{ $assignment->is_active ? 'Students and teachers can see this assignment.' : 'This assignment is hidden from students and teachers.' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Assignment Summary -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800 p-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center">
                                        <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 text-xl"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100">Assignment Summary</h4>
                                    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">Teacher:</span>
                                                <span class="font-medium text-gray-900 dark:text-white" id="summary-teacher">{{ $assignment->teacher->name }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">Subject:</span>
                                                <span class="font-medium text-gray-900 dark:text-white" id="summary-subject">{{ $assignment->subject->name }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">Class:</span>
                                                <span class="font-medium text-gray-900 dark:text-white" id="summary-class">{{ $assignment->class->name }}</span>
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">Stream:</span>
                                                <span class="font-medium text-gray-900 dark:text-white" id="summary-stream">{{ $assignment->stream ? $assignment->stream->name : 'None' }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">Academic Year:</span>
                                                <span class="font-medium text-gray-900 dark:text-white" id="summary-year">{{ $assignment->academic_year }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">Role:</span>
                                                <span class="font-medium text-gray-900 dark:text-white" id="summary-role">{{ $roles[$assignment->role] ?? $assignment->role }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Footer -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-history mr-1 text-blue-500"></i>
                                Last updated: {{ $assignment->updated_at->format('M j, Y g:i A') }}
                            </div>
                            <div class="flex space-x-3">
                                <!-- Delete Button -->
                                <button type="button" onclick="confirmDelete()"
                                        class="px-6 py-2.5 border border-red-300 dark:border-red-600 text-red-700 dark:text-red-300 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200 font-medium">
                                    <i class="fas fa-trash mr-2"></i>
                                    Delete
                                </button>

                                <!-- Cancel Button -->
                                <a href="{{ route('admin.teacher-assignments.index') }}"
                                   class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200 font-medium">
                                    Cancel
                                </a>

                                <!-- Update Button -->
                                <button type="submit"
                                        class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 shadow-sm hover:shadow-md font-medium">
                                    <i class="fas fa-save mr-2"></i>
                                    Update Assignment
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6 animate-scale-in">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Delete Assignment</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to delete this assignment? This action cannot be undone.
                </p>
                <div class="mt-6 bg-red-50 dark:bg-red-900/20 rounded-lg p-4 border border-red-200 dark:border-red-800">
                    <p class="text-sm text-red-700 dark:text-red-300 font-medium">
                        {{ $assignment->teacher->name }} - {{ $assignment->subject->name }} ({{ $assignment->class->name }})
                    </p>
                </div>
            </div>
            <div class="mt-6 flex justify-center space-x-3">
                <button type="button" onclick="closeDeleteModal()"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200 font-medium">
                    Cancel
                </button>
                <form action="{{ route('admin.teacher-assignments.destroy', $assignment) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium">
                        <i class="fas fa-trash mr-2"></i>
                        Delete Assignment
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        .animate-scale-in {
            animation: scaleIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        /* Custom toggle switch */
        input:checked ~ .dot {
            transform: translateX(100%);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editAssignmentForm');
            const summaryElements = {
                teacher: document.getElementById('summary-teacher'),
                subject: document.getElementById('summary-subject'),
                class: document.getElementById('summary-class'),
                stream: document.getElementById('summary-stream'),
                year: document.getElementById('summary-year'),
                role: document.getElementById('summary-role')
            };

            // Update summary in real-time
            function updateSummary() {
                const teacherSelect = document.getElementById('teacher_id');
                const subjectSelect = document.getElementById('subject_id');
                const classSelect = document.getElementById('class_id');
                const streamSelect = document.getElementById('stream_id');
                const yearSelect = document.getElementById('academic_year');
                const roleSelect = document.getElementById('role');

                if (teacherSelect.value) {
                    summaryElements.teacher.textContent = teacherSelect.options[teacherSelect.selectedIndex].text.split(' - ')[0];
                }

                if (subjectSelect.value) {
                    summaryElements.subject.textContent = subjectSelect.options[subjectSelect.selectedIndex].text.split(' (')[0];
                }

                if (classSelect.value) {
                    summaryElements.class.textContent = classSelect.options[classSelect.selectedIndex].text;
                }

                if (streamSelect.value) {
                    summaryElements.stream.textContent = streamSelect.options[streamSelect.selectedIndex].text;
                } else {
                    summaryElements.stream.textContent = 'None';
                }

                if (yearSelect.value) {
                    summaryElements.year.textContent = yearSelect.value;
                }

                if (roleSelect.value) {
                    const roleText = roleSelect.options[roleSelect.selectedIndex].text;
                    summaryElements.role.textContent = roleText;
                }
            }

            // Add event listeners to all form fields
            document.querySelectorAll('select').forEach(select => {
                select.addEventListener('change', updateSummary);
            });

            // Form submission handler
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                submitForm();
            });

            function submitForm() {
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Updating Assignment...
                `;

                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                            if (data.redirect) {
                                setTimeout(() => {
                                    window.location.href = data.redirect;
                                }, 1500);
                            }
                        } else {
                            throw new Error(data.message || 'An error occurred');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification(error.message, 'error');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
            }

            function showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                const bgColor = type === 'success' ? 'bg-green-500' :
                    type === 'error' ? 'bg-red-500' :
                        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';

                notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-transform duration-300 translate-x-full`;
                notification.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas ${type === 'success' ? 'fa-check-circle' :
                    type === 'error' ? 'fa-exclamation-circle' :
                        type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle'} mr-3"></i>
                        <span>${message}</span>
                        <button class="ml-4 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;

                document.body.appendChild(notification);

                // Animate in
                setTimeout(() => {
                    notification.classList.remove('translate-x-full');
                }, 100);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 5000);
            }

            // Initialize summary on page load
            updateSummary();
        });

        function confirmDelete() {
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('deleteModal');
            if (e.target === modal) {
                closeDeleteModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
@endsection
