@extends('layouts.app')

@section('title', 'Create Assignment - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Create New Assignment
                </h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">
                    Set up a new assignment for your students
                </p>
            </div>

            <!-- Assignment Form -->
            <form action="{{
            auth()->user()->role === 'admin' ? route('admin.assignments.store') : route('teacher.assignments.store')
        }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6 border border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-file-circle-plus mr-3 text-blue-500"></i>
                        Assignment Details
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Assignment Title *
                            </label>
                            <input type="text" name="title"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200"
                                   placeholder="Enter assignment title"
                                   value="{{ old('title') }}" required>
                            @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Class and Subject -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Class *
                            </label>
                            <select name="class_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Subject *
                            </label>
                            <select name="subject_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Due Date and Points -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Due Date *
                            </label>
                            <input type="datetime-local" name="due_date"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200"
                                   value="{{ old('due_date') }}" required>
                            @error('due_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Maximum Points *
                            </label>
                            <input type="number" name="max_points"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200"
                                   placeholder="100"
                                   value="{{ old('max_points', 100) }}" required min="1">
                            @error('max_points')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Instructions -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Instructions
                            </label>
                            <textarea name="instructions"
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200"
                                      rows="3"
                                      placeholder="Provide instructions for this assignment...">{{ old('instructions') }}</textarea>
                            @error('instructions')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description
                            </label>
                            <textarea name="description"
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200"
                                      rows="4"
                                      placeholder="Provide detailed description for this assignment...">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- File Submission Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6 border border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-paperclip mr-3 text-green-500"></i>
                        Student Submission Settings
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Allowed File Formats -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Allowed File Formats for Students *
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                @php
                                    $defaultFormats = old('allowed_formats', ['pdf', 'doc', 'docx']);
                                    $fileTypes = [
                                        'pdf' => 'PDF',
                                        'doc' => 'DOC',
                                        'docx' => 'DOCX',
                                        'txt' => 'TXT',
                                        'jpg' => 'JPG',
                                        'jpeg' => 'JPEG',
                                        'png' => 'PNG',
                                        'ppt' => 'PPT',
                                        'pptx' => 'PPTX',
                                        'xls' => 'XLS',
                                        'xlsx' => 'XLSX',
                                        'zip' => 'ZIP',
                                        'rar' => 'RAR'
                                    ];
                                @endphp
                                @foreach($fileTypes as $value => $label)
                                    <label class="flex items-center p-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg hover:border-blue-400 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer transition-all duration-200">
                                        <input type="checkbox" name="allowed_formats[]" value="{{ $value }}"
                                               class="rounded text-blue-600 focus:ring-blue-500 transform scale-110"
                                            {{ in_array($value, $defaultFormats) ? 'checked' : '' }}>
                                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('allowed_formats')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- File Size -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Maximum File Size (MB) *
                            </label>
                            <input type="number" name="max_file_size"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200"
                                   value="{{ old('max_file_size', 10) }}" required min="1" max="100">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Maximum allowed: 100MB
                            </p>
                            @error('max_file_size')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Publication Status -->
                        <div class="flex items-center justify-center">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_published" value="1"
                                       class="rounded text-blue-600 focus:ring-blue-500 h-5 w-5 transform scale-110"
                                    {{ old('is_published') ? 'checked' : '' }}>
                                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Publish immediately
                            </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assignment File Attachment Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6 border border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-paperclip mr-3 text-purple-500"></i>
                        Assignment File Attachment
                    </h2>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                            Attach Assignment File (Optional)
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">PDF, DOC, PPT, XLS, ZIP (Max: 10MB)</span>
                        </label>

                        <!-- File Upload Area -->
                        <div class="space-y-4">
                            <!-- Upload Box -->
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center transition-all duration-300 hover:border-purple-400 dark:hover:border-purple-500 hover:bg-purple-50 dark:hover:bg-purple-900/10" id="upload-area">
                                <div id="upload-content">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                    <p class="text-lg font-semibold text-gray-600 dark:text-gray-400 mb-2">
                                        Drop your file here or click to browse
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                        Supports: PDF, DOC, DOCX, PPT, XLS, ZIP, RAR
                                    </p>
                                    <button type="button" onclick="document.getElementById('assignment_file').click()"
                                            class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center mx-auto">
                                        <i class="fas fa-folder-open mr-2"></i>
                                        Choose File
                                    </button>
                                </div>
                                <input id="assignment_file" name="assignment_file" type="file" class="hidden"
                                       accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar" onchange="previewFile(this)">
                            </div>

                            <!-- File Preview Area -->
                            <div id="file-preview" class="hidden bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-2 border-green-200 dark:border-green-700 rounded-xl p-6 transition-all duration-300">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                            <i class="fas fa-file text-2xl text-green-500" id="file-icon"></i>
                                        </div>
                                        <div>
                                            <p class="text-lg font-semibold text-gray-900 dark:text-white" id="file-name">file_name.pdf</p>
                                            <div class="flex items-center space-x-4 mt-1">
                                                <span class="text-sm text-gray-600 dark:text-gray-400" id="file-size">2.5 MB</span>
                                                <span class="text-sm px-2 py-1 bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 rounded-full" id="file-type">PDF</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button type="button" onclick="clearFile()"
                                                class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors duration-200"
                                                title="Remove file">
                                            <i class="fas fa-times text-lg"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Progress Bar (for future upload progress) -->
                                <div class="mt-4 hidden" id="upload-progress">
                                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1">
                                        <span>Uploading...</span>
                                        <span id="progress-percentage">0%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div id="progress-bar" class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Help Text -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-blue-800 dark:text-blue-300 font-medium">File Attachment Tips</p>
                                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                            Attach assignment instructions, templates, or reference materials. Students will be able to download this file when viewing the assignment.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @error('assignment_file')
                        <p class="text-red-500 text-sm mt-3">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center">
                    <a href="{{
                    auth()->user()->role === 'admin' ? route('admin.assignments.index') : route('teacher.assignments.index')
                }}"
                       class="px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 flex items-center hover:border-gray-400 dark:hover:border-gray-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Assignments
                    </a>

                    <div class="flex space-x-3">
                        <button type="submit" name="draft" value="1"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 flex items-center shadow-sm hover:shadow-md">
                            <i class="fas fa-save mr-2"></i>
                            Save as Draft
                        </button>
                        <button type="submit"
                                class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-medium transition-all duration-200 flex items-center shadow-lg hover:shadow-xl transform hover:scale-105">
                            <i class="fas fa-check mr-2"></i>
                            Create Assignment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // File type icons mapping
        const fileIcons = {
            'pdf': 'fas fa-file-pdf',
            'doc': 'fas fa-file-word',
            'docx': 'fas fa-file-word',
            'txt': 'fas fa-file-alt',
            'jpg': 'fas fa-file-image',
            'jpeg': 'fas fa-file-image',
            'png': 'fas fa-file-image',
            'ppt': 'fas fa-file-powerpoint',
            'pptx': 'fas fa-file-powerpoint',
            'xls': 'fas fa-file-excel',
            'xlsx': 'fas fa-file-excel',
            'zip': 'fas fa-file-archive',
            'rar': 'fas fa-file-archive'
        };

        // File type colors mapping
        const fileColors = {
            'pdf': 'text-red-500',
            'doc': 'text-blue-500',
            'docx': 'text-blue-500',
            'txt': 'text-gray-500',
            'jpg': 'text-green-500',
            'jpeg': 'text-green-500',
            'png': 'text-green-500',
            'ppt': 'text-orange-500',
            'pptx': 'text-orange-500',
            'xls': 'text-green-600',
            'xlsx': 'text-green-600',
            'zip': 'text-yellow-500',
            'rar': 'text-yellow-500'
        };

        function previewFile(input) {
            const file = input.files[0];
            if (file) {
                const filePreview = document.getElementById('file-preview');
                const uploadArea = document.getElementById('upload-area');
                const uploadContent = document.getElementById('upload-content');
                const fileName = document.getElementById('file-name');
                const fileSize = document.getElementById('file-size');
                const fileType = document.getElementById('file-type');
                const fileIcon = document.getElementById('file-icon');

                // Get file extension
                const extension = file.name.split('.').pop().toLowerCase();

                // Update file info
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                fileType.textContent = extension.toUpperCase();

                // Update icon and color
                fileIcon.className = fileIcons[extension] || 'fas fa-file';
                fileIcon.className += ' ' + (fileColors[extension] || 'text-gray-500');

                // Show preview and hide upload content
                filePreview.classList.remove('hidden');
                uploadContent.style.display = 'none';
                uploadArea.classList.remove('hover:border-purple-400', 'dark:hover:border-purple-500', 'hover:bg-purple-50', 'dark:hover:bg-purple-900/10');
                uploadArea.classList.add('border-green-300', 'dark:border-green-600', 'bg-green-50', 'dark:bg-green-900/10');
            }
        }

        function clearFile() {
            const fileInput = document.getElementById('assignment_file');
            const filePreview = document.getElementById('file-preview');
            const uploadArea = document.getElementById('upload-area');
            const uploadContent = document.getElementById('upload-content');

            // Reset file input
            fileInput.value = '';

            // Hide preview and show upload content
            filePreview.classList.add('hidden');
            uploadContent.style.display = 'block';
            uploadArea.classList.remove('border-green-300', 'dark:border-green-600', 'bg-green-50', 'dark:bg-green-900/10');
            uploadArea.classList.add('hover:border-purple-400', 'dark:hover:border-purple-500', 'hover:bg-purple-50', 'dark:hover:bg-purple-900/10');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Drag and drop functionality
        const dropArea = document.getElementById('upload-area');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            dropArea.classList.add('border-purple-500', 'bg-purple-100', 'dark:bg-purple-900/20');
            dropArea.classList.remove('border-gray-300', 'dark:border-gray-600');
        }

        function unhighlight() {
            dropArea.classList.remove('border-purple-500', 'bg-purple-100', 'dark:bg-purple-900/20');
            dropArea.classList.add('border-gray-300', 'dark:border-gray-600');
        }

        dropArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            const fileInput = document.getElementById('assignment_file');

            if (files.length > 0) {
                fileInput.files = files;
                previewFile(fileInput);
            }
        }

        // Set minimum datetime to current time
        const now = new Date();
        const timezoneOffset = now.getTimezoneOffset() * 60000;
        const localISOTime = new Date(now - timezoneOffset).toISOString().slice(0, 16);
        document.querySelector('input[name="due_date"]').min = localISOTime;
    </script>

    <style>
        #upload-area {
            transition: all 0.3s ease;
        }

        #file-preview {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hover-lift:hover {
            transform: translateY(-2px);
        }
    </style>
@endsection
