@extends('layouts.app')

@section('title', 'Create Teacher Assignment - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
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
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Teacher Assignment</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Assign subjects across multiple classes and levels
                        </p>
                    </div>
                </div>
            </div>

            <!-- System Status Card -->
            <div class="mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $teachers->count() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Teachers</div>
                        </div>
                        <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $classes->count() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Classes</div>
                        </div>
                        <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $subjects->count() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Subjects</div>
                        </div>
                        <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $streams->count() }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Streams</div>
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

            @if (session('warnings'))
                <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-lg mt-0.5"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-400">
                                Assignment Warnings
                            </h3>
                            <div class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach (session('warnings') as $warning)
                                        <li>{{ $warning }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Form Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <form action="{{ route('admin.teacher-assignments.store') }}" method="POST" id="assignmentForm">
                    @csrf

                    <!-- Form Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                        <h2 class="text-lg font-semibold text-white">Teacher Assignment Details</h2>
                        <p class="text-blue-100 text-sm mt-1">Fill in the basic information and add class assignments</p>
                    </div>

                    <div class="p-6 space-y-8">
                        <!-- Basic Information Section -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                <i class="fas fa-user-circle mr-2 text-blue-600"></i>
                                Basic Information
                            </h3>

                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
                                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->name }} - {{ $teacher->email }}
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
                                                $selected = old('academic_year', $currentAcademicYear) == $academicYear ? 'selected' : '';
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
                                            <option value="{{ $value }}" {{ old('role') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Class Assignments Section -->
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    <i class="fas fa-tasks mr-2 text-green-600"></i>
                                    Class Assignments
                                </h3>
                                <button type="button" id="addAssignment"
                                        class="inline-flex items-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add Class
                                </button>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                                    <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                                    Add classes and assign multiple subjects to each. Example: Math & Physics in Grade 10, Chemistry in Grade 11.
                                </p>
                            </div>

                            <!-- Assignments Container -->
                            <div id="assignments-container" class="space-y-4">
                                <div class="text-center py-12 text-gray-500 dark:text-gray-400" id="no-assignments-message">
                                    <i class="fas fa-inbox text-4xl mb-4 opacity-50"></i>
                                    <p class="text-lg font-medium">No classes added yet</p>
                                    <p class="text-sm mt-1">Click "Add Class" to start creating assignments</p>
                                </div>
                            </div>
                        </div>

                        <!-- Active Status -->
                        <div class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 bg-white dark:bg-gray-700 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <label for="is_active" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Activate this assignment immediately
                            </label>
                        </div>
                    </div>

                    <!-- Form Footer -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-lightbulb mr-1 text-yellow-500"></i>
                                You can assign multiple classes and subjects in one go
                            </div>
                            <div class="flex space-x-3">
                                <a href="{{ route('admin.teacher-assignments.index') }}"
                                   class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200 font-medium">
                                    Cancel
                                </a>
                                <button type="submit" id="submit-btn"
                                        class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 shadow-sm hover:shadow-md font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                                    Create Assignments
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assignment Row Template -->
    <template id="assignment-template">
        <div class="assignment-row bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-200 shadow-sm hover:shadow-md">
            <div class="p-5">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-grip-vertical text-gray-400"></i>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Assignment</span>
                    </div>
                    <button type="button" class="remove-assignment p-2 text-gray-400 hover:text-red-500 rounded-lg transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Form Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">
                    <!-- Class Selection -->
                    <div class="lg:col-span-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Class *
                        </label>
                        <select name="assignments[INDEX][class_id]" required
                                class="assignment-class w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="">Select class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" data-level="{{ $class->level }}">
                                    {{ $class->name }} ({{ $class->level }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Stream Selection -->
                    <div class="lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Stream
                        </label>
                        <select name="assignments[INDEX][stream_id]"
                                class="assignment-stream w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="">No stream</option>
                        </select>
                    </div>

                    <!-- Subjects Selection -->
                    <div class="lg:col-span-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Subjects * (Multiple)
                        </label>
                        <select name="assignments[INDEX][subject_ids][]" multiple required
                                class="assignment-subjects w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 min-h-[100px]">
                            <option value="">Select class first...</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-mouse-pointer mr-1"></i>Hold Ctrl/Cmd to select multiple
                        </p>
                    </div>

                    <!-- Status Indicator -->
                    <div class="lg:col-span-1">
                        <div class="assignment-status h-10 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700">
                            <div class="w-3 h-3 rounded-full bg-gray-400 animate-pulse" title="Loading..."></div>
                        </div>
                    </div>
                </div>

                <!-- Assignment Summary -->
                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 assignment-summary hidden">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        <span class="summary-text text-sm text-blue-700 dark:text-blue-300 font-medium"></span>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <style>
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .assignment-row {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Custom scrollbar for selects */
        select[multiple] {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }

        select[multiple]::-webkit-scrollbar {
            width: 6px;
        }

        select[multiple]::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        select[multiple]::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        select[multiple]::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const teacherSelect = document.getElementById('teacher_id');
            const academicYearSelect = document.getElementById('academic_year');
            const assignmentsContainer = document.getElementById('assignments-container');
            const noAssignmentsMessage = document.getElementById('no-assignments-message');
            const addAssignmentBtn = document.getElementById('addAssignment');
            const assignmentTemplate = document.getElementById('assignment-template');
            const submitBtn = document.getElementById('submit-btn');
            const form = document.getElementById('assignmentForm');

            let assignmentIndex = 0;
            let usedClassIds = new Set();

            // Add new assignment row
            addAssignmentBtn.addEventListener('click', function() {
                addAssignmentRow();
            });

            // Form submission handler
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                submitForm();
            });

            function addAssignmentRow() {
                noAssignmentsMessage.style.display = 'none';

                const template = assignmentTemplate.innerHTML.replace(/INDEX/g, assignmentIndex);
                const div = document.createElement('div');
                div.innerHTML = template;
                assignmentsContainer.appendChild(div);

                const newRow = assignmentsContainer.lastElementChild;
                const classSelect = newRow.querySelector('.assignment-class');
                const streamSelect = newRow.querySelector('.assignment-stream');
                const subjectSelect = newRow.querySelector('.assignment-subjects');
                const removeBtn = newRow.querySelector('.remove-assignment');
                const summaryDiv = newRow.querySelector('.assignment-summary');
                const summaryText = newRow.querySelector('.summary-text');
                const statusIndicator = newRow.querySelector('.assignment-status');

                // Load available classes
                loadAvailableClasses(classSelect, statusIndicator);

                // When class changes, load streams and subjects
                classSelect.addEventListener('change', function() {
                    const classId = this.value;
                    const selectedOption = this.options[this.selectedIndex];
                    const className = selectedOption.textContent;

                    if (classId) {
                        usedClassIds.add(classId);
                        updateStatus(statusIndicator, 'loading', 'Loading...');

                        // Load streams for this class
                        loadClassStreams(classId, streamSelect, statusIndicator);

                        // Load available subjects
                        loadAvailableSubjects(classId, subjectSelect, summaryText, className, statusIndicator);
                    } else {
                        summaryDiv.classList.add('hidden');
                        streamSelect.innerHTML = '<option value="">No stream</option>';
                        subjectSelect.innerHTML = '<option value="">Select class first...</option>';
                        subjectSelect.disabled = true;
                        updateStatus(statusIndicator, 'idle', 'Select class');
                    }

                    updateClassSelections();
                });

                // When subjects change, update summary
                subjectSelect.addEventListener('change', function() {
                    updateAssignmentSummary(classSelect, subjectSelect, summaryText, summaryDiv, statusIndicator);
                });

                // Remove assignment row
                removeBtn.addEventListener('click', function() {
                    const classId = classSelect.value;
                    if (classId) {
                        usedClassIds.delete(classId);
                    }
                    newRow.style.opacity = '0';
                    newRow.style.transform = 'translateX(20px)';

                    setTimeout(() => {
                        newRow.remove();
                        updateClassSelections();

                        if (assignmentsContainer.querySelectorAll('.assignment-row').length === 0) {
                            noAssignmentsMessage.style.display = 'block';
                        }

                        updateSubmitButton();
                    }, 300);
                });

                assignmentIndex++;
                updateSubmitButton();
            }

            function updateStatus(indicator, status, message = '') {
                const dot = indicator.querySelector('div');
                switch(status) {
                    case 'loading':
                        dot.className = 'w-3 h-3 rounded-full bg-yellow-500 animate-pulse';
                        dot.title = message;
                        break;
                    case 'success':
                        dot.className = 'w-3 h-3 rounded-full bg-green-500';
                        dot.title = message;
                        break;
                    case 'error':
                        dot.className = 'w-3 h-3 rounded-full bg-red-500';
                        dot.title = message;
                        break;
                    default:
                        dot.className = 'w-3 h-3 rounded-full bg-gray-400';
                        dot.title = message;
                }
            }

            function loadAvailableClasses(selectElement, statusIndicator) {
                const teacherId = teacherSelect.value;
                const academicYear = academicYearSelect.value;

                if (!teacherId || !academicYear) {
                    selectElement.innerHTML = '<option value="">Select teacher and academic year first</option>';
                    selectElement.disabled = true;
                    updateStatus(statusIndicator, 'error', 'Missing info');
                    return;
                }

                selectElement.disabled = false;
                selectElement.innerHTML = '<option value="">Loading classes...</option>';
                updateStatus(statusIndicator, 'loading', 'Loading classes...');

                const url = `{{ route('admin.teacher-assignments.available-classes', ['teacher' => 'TEACHER_ID']) }}`.replace('TEACHER_ID', teacherId) + `?academic_year=${academicYear}`;

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const currentValue = selectElement.value;
                        selectElement.innerHTML = '<option value="">Select class</option>';

                        if (data.classes && data.classes.length > 0) {
                            data.classes.forEach(cls => {
                                if (!usedClassIds.has(cls.id.toString())) {
                                    const option = document.createElement('option');
                                    option.value = cls.id;
                                    option.textContent = `${cls.name} (${cls.level})`;
                                    option.dataset.assigned = data.assigned_class_ids.includes(cls.id) ? 'true' : 'false';

                                    if (data.assigned_class_ids.includes(cls.id)) {
                                        option.textContent += ' 📚';
                                        option.title = 'Already has assignments';
                                    }

                                    selectElement.appendChild(option);
                                }
                            });

                            if (currentValue) {
                                selectElement.value = currentValue;
                            }

                            updateStatus(statusIndicator, 'success', 'Classes loaded');
                        } else {
                            selectElement.innerHTML = '<option value="">No classes available</option>';
                            updateStatus(statusIndicator, 'error', 'No classes');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading classes:', error);
                        selectElement.innerHTML = '<option value="">Error loading classes</option>';
                        updateStatus(statusIndicator, 'error', 'Load failed');
                    });
            }

            function loadClassStreams(classId, streamSelect, statusIndicator) {
                const url = `{{ route('admin.teacher-assignments.class-streams', ['class' => 'CLASS_ID']) }}`.replace('CLASS_ID', classId);

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => response.json())
                    .then(data => {
                        streamSelect.innerHTML = '<option value="">No stream</option>';
                        if (data.streams && data.streams.length > 0) {
                            data.streams.forEach(stream => {
                                const option = document.createElement('option');
                                option.value = stream.id;
                                option.textContent = stream.name;
                                streamSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error loading streams:', error);
                        streamSelect.innerHTML = '<option value="">Error loading streams</option>';
                    });
            }

            function loadAvailableSubjects(classId, subjectSelect, summaryText, className, statusIndicator) {
                const teacherId = teacherSelect.value;
                const academicYear = academicYearSelect.value;

                if (!teacherId || !academicYear || !classId) {
                    subjectSelect.innerHTML = '<option value="">Select class first</option>';
                    subjectSelect.disabled = true;
                    return;
                }

                subjectSelect.disabled = true;
                subjectSelect.innerHTML = '<option value="">Loading subjects...</option>';
                updateStatus(statusIndicator, 'loading', 'Loading subjects...');

                const url = `{{ route('admin.teacher-assignments.available-subjects', ['teacher' => 'TEACHER_ID']) }}`.replace('TEACHER_ID', teacherId) + `?academic_year=${academicYear}&class_id=${classId}`;

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        subjectSelect.innerHTML = '';

                        if (data.subjects && data.subjects.length > 0) {
                            data.subjects.forEach(subject => {
                                const option = document.createElement('option');
                                option.value = subject.id;
                                option.textContent = `${subject.name} (${subject.code})`;
                                subjectSelect.appendChild(option);
                            });
                            subjectSelect.disabled = false;
                            summaryText.textContent = `Ready to assign subjects for ${className}`;
                            updateStatus(statusIndicator, 'success', 'Subjects loaded');
                        } else {
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'No available subjects for this class';
                            subjectSelect.appendChild(option);
                            subjectSelect.disabled = true;
                            summaryText.textContent = `No subjects available for ${className}`;
                            updateStatus(statusIndicator, 'error', 'No subjects');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading subjects:', error);
                        subjectSelect.innerHTML = '<option value="">Error loading subjects</option>';
                        subjectSelect.disabled = false;
                        updateStatus(statusIndicator, 'error', 'Load failed');
                    });
            }

            function updateAssignmentSummary(classSelect, subjectSelect, summaryText, summaryDiv, statusIndicator) {
                const selectedClass = classSelect.options[classSelect.selectedIndex]?.textContent || '';
                const selectedSubjects = Array.from(subjectSelect.selectedOptions).map(opt => opt.textContent.split(' (')[0]);

                if (selectedClass && selectedSubjects.length > 0) {
                    summaryText.textContent = `${selectedClass}: ${selectedSubjects.join(', ')}`;
                    summaryDiv.classList.remove('hidden');
                    updateStatus(statusIndicator, 'success', `${selectedSubjects.length} subjects selected`);
                } else if (selectedClass) {
                    summaryText.textContent = `${selectedClass}: No subjects selected`;
                    summaryDiv.classList.remove('hidden');
                    updateStatus(statusIndicator, 'idle', 'No subjects selected');
                } else {
                    summaryDiv.classList.add('hidden');
                    updateStatus(statusIndicator, 'idle', 'Select class');
                }
            }

            function updateClassSelections() {
                document.querySelectorAll('.assignment-class').forEach(select => {
                    const currentValue = select.value;
                    Array.from(select.options).forEach(option => {
                        if (option.value && option.value !== currentValue) {
                            option.disabled = usedClassIds.has(option.value);
                            option.style.opacity = option.disabled ? '0.5' : '1';
                        }
                    });
                });
            }

            function updateSubmitButton() {
                const hasAssignments = assignmentsContainer.querySelectorAll('.assignment-row').length > 0;
                submitBtn.disabled = !hasAssignments;

                if (hasAssignments) {
                    const assignmentCount = assignmentsContainer.querySelectorAll('.assignment-row').length;
                    const totalSubjects = Array.from(assignmentsContainer.querySelectorAll('.assignment-subjects'))
                        .reduce((total, select) => total + Array.from(select.selectedOptions).length, 0);

                    submitBtn.textContent = `Create ${assignmentCount} Assignment${assignmentCount > 1 ? 's' : ''} (${totalSubjects} Subject${totalSubjects !== 1 ? 's' : ''})`;
                    submitBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                    submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                } else {
                    submitBtn.textContent = 'Create Assignments';
                    submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    submitBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                }
            }

            function submitForm() {
                const formData = new FormData(form);
                const originalText = submitBtn.innerHTML;

                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Creating Assignments...
                `;

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
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

            // When teacher or academic year changes, update all assignment rows
            [teacherSelect, academicYearSelect].forEach(select => {
                select.addEventListener('change', function() {
                    usedClassIds.clear();
                    document.querySelectorAll('.assignment-row').forEach(row => {
                        const classSelect = row.querySelector('.assignment-class');
                        const subjectSelect = row.querySelector('.assignment-subjects');
                        const summaryDiv = row.querySelector('.assignment-summary');
                        const statusIndicator = row.querySelector('.assignment-status');

                        loadAvailableClasses(classSelect, statusIndicator);
                        summaryDiv.classList.add('hidden');
                        updateStatus(statusIndicator, 'loading', 'Refreshing...');
                    });
                });
            });

            // Add first assignment row on page load
            addAssignmentRow();
        });
    </script>
@endsection
