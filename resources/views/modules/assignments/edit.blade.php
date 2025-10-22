@extends('layouts.app')

@section('title', 'Edit Assignment - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Edit Assignment
                </h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">
                    Update assignment details and requirements
                </p>
            </div>

            <!-- Assignment Form -->
            <form action="{{
            auth()->user()->isAdmin() ? route('admin.assignments.update', $assignment->id) : route('teacher.assignments.update', $assignment->id)
        }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-edit mr-3 text-blue-500"></i>
                        Assignment Details
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Assignment Title *
                            </label>
                            <input type="text" name="title"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Enter assignment title"
                                   value="{{ old('title', $assignment->title) }}" required>
                            @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Class and Subject -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Class *
                            </label>
                            <select name="class_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id', $assignment->class_id) == $class->id ? 'selected' : '' }}>
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
                            <select name="subject_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id', $assignment->subject_id) == $subject->id ? 'selected' : '' }}>
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
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   value="{{ old('due_date', $assignment->due_date->format('Y-m-d\TH:i')) }}" required>
                            @error('due_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Maximum Points *
                            </label>
                            <input type="number" name="max_points"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="100"
                                   value="{{ old('max_points', $assignment->max_points) }}" required min="1">
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
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                      rows="3"
                                      placeholder="Provide instructions for this assignment...">{{ old('instructions', $assignment->instructions) }}</textarea>
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
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                      rows="4"
                                      placeholder="Provide detailed description for this assignment...">{{ old('description', $assignment->description) }}</textarea>
                            @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- File Upload Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-paperclip mr-3 text-green-500"></i>
                        Assignment File
                    </h2>

                    <!-- Current File Display -->
                    @if($assignment->assignment_file)
                        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <i class="fas fa-file mr-2 text-blue-500"></i>
                                Current Attached File
                            </h3>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center shadow-sm">
                                        @php
                                            $extension = pathinfo($assignment->assignment_file, PATHINFO_EXTENSION);
                                            $fileIcons = [
                                                'pdf' => 'fas fa-file-pdf text-red-500',
                                                'doc' => 'fas fa-file-word text-blue-500',
                                                'docx' => 'fas fa-file-word text-blue-500',
                                                'txt' => 'fas fa-file-alt text-gray-500',
                                                'jpg' => 'fas fa-file-image text-green-500',
                                                'jpeg' => 'fas fa-file-image text-green-500',
                                                'png' => 'fas fa-file-image text-green-500',
                                                'zip' => 'fas fa-file-archive text-yellow-500',
                                                'rar' => 'fas fa-file-archive text-yellow-500'
                                            ];
                                            $fileIcon = $fileIcons[$extension] ?? 'fas fa-file text-gray-500';
                                        @endphp
                                        <i class="{{ $fileIcon }} text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white text-sm">
                                            {{ basename($assignment->assignment_file) }}
                                        </h4>
                                        <p class="text-blue-600 dark:text-blue-400 text-xs">Currently attached</p>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $assignment->assignment_file) }}"
                                   target="_blank"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                                    <i class="fas fa-download mr-2"></i>
                                    Download
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- File Upload Area -->
                    <div class="relative">
                        <input type="file" name="assignment_file" id="assignment_file"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               accept=".pdf,.doc,.docx,.txt,.jpg,.png,.zip,.rar">

                        <div id="uploadArea" class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center bg-gray-50 dark:bg-gray-700/30 transition-all duration-300 hover:border-blue-300 dark:hover:border-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                            <div id="uploadContent">
                                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-blue-500 dark:text-blue-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                    Update Assignment File
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">
                                    Drag & drop or <span class="text-blue-500 dark:text-blue-400 font-semibold">click to browse</span>
                                </p>
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 max-w-md mx-auto">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                        Supported formats:
                                    </p>
                                    <div class="flex flex-wrap justify-center gap-2">
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs">PDF</span>
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs">DOC</span>
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs">DOCX</span>
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs">TXT</span>
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs">JPG</span>
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs">PNG</span>
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs">ZIP</span>
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs">RAR</span>
                                    </div>
                                </div>
                            </div>

                            <!-- File Preview -->
                            <div id="filePreview" class="hidden mt-4">
                                <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg p-4 border-2 border-green-200 dark:border-green-700">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center shadow-sm">
                                                <i class="fas fa-file text-lg" id="filePreviewIcon"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-900 dark:text-white text-sm" id="filePreviewName">filename.pdf</h4>
                                                <p class="text-green-600 dark:text-green-400 text-xs">Ready to upload</p>
                                            </div>
                                        </div>
                                        <button type="button" onclick="clearFile()"
                                                class="w-8 h-8 bg-white dark:bg-gray-800 border border-red-200 dark:border-red-800 text-red-500 dark:text-red-400 rounded-lg flex items-center justify-center hover:bg-red-50 dark:hover:bg-red-900/30 hover:border-red-300 dark:hover:border-red-700 transition-all duration-200 hover:scale-110">
                                            <i class="fas fa-times text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
                        Leave empty to keep the current file. Upload a new file to replace the existing one.
                    </p>
                    @error('assignment_file')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submission Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-cog mr-3 text-purple-500"></i>
                        Submission Settings
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- File Requirements -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                <i class="fas fa-paperclip mr-2 text-gray-500"></i>
                                Allowed File Formats
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @php
                                    $allowedFormats = old('allowed_formats', $assignment->allowed_formats ?? ['pdf', 'doc', 'docx']);
                                @endphp
                                <label class="flex items-center">
                                    <input type="checkbox" name="allowed_formats[]" value="pdf" class="rounded text-blue-600 focus:ring-blue-500"
                                        {{ in_array('pdf', $allowedFormats) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">PDF</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="allowed_formats[]" value="doc" class="rounded text-blue-600 focus:ring-blue-500"
                                        {{ in_array('doc', $allowedFormats) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">DOC</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="allowed_formats[]" value="docx" class="rounded text-blue-600 focus:ring-blue-500"
                                        {{ in_array('docx', $allowedFormats) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">DOCX</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="allowed_formats[]" value="jpg" class="rounded text-blue-600 focus:ring-blue-500"
                                        {{ in_array('jpg', $allowedFormats) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">JPG</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="allowed_formats[]" value="png" class="rounded text-blue-600 focus:ring-blue-500"
                                        {{ in_array('png', $allowedFormats) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">PNG</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="allowed_formats[]" value="txt" class="rounded text-blue-600 focus:ring-blue-500"
                                        {{ in_array('txt', $allowedFormats) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">TXT</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="allowed_formats[]" value="zip" class="rounded text-blue-600 focus:ring-blue-500"
                                        {{ in_array('zip', $allowedFormats) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">ZIP</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="allowed_formats[]" value="rar" class="rounded text-blue-600 focus:ring-blue-500"
                                        {{ in_array('rar', $allowedFormats) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">RAR</span>
                                </label>
                            </div>
                        </div>

                        <!-- File Size -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Maximum File Size (MB) *
                            </label>
                            <input type="number" name="max_file_size"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   value="{{ old('max_file_size', $assignment->max_file_size) }}" required min="1">
                            @error('max_file_size')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Publication Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Publication Status
                            </label>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_published" value="1" class="rounded text-blue-600 focus:ring-blue-500"
                                    {{ old('is_published', $assignment->is_published) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Publish assignment</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center">
                    <a href="{{
                    auth()->user()->isAdmin() ? route('admin.assignments.index') : route('teacher.assignments.index')
                }}"
                       class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Assignments
                    </a>

                    <div class="flex space-x-3">
                        <a href="{{
                        auth()->user()->isAdmin() ? route('admin.assignments.show', $assignment->id) : route('teacher.assignments.show', $assignment->id)
                    }}"
                           class="px-6 py-3 border border-blue-600 text-blue-600 dark:text-blue-400 rounded-lg font-medium hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                            <i class="fas fa-check mr-2"></i>
                            Update Assignment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('assignment_file');
            const uploadArea = document.getElementById('uploadArea');
            const uploadContent = document.getElementById('uploadContent');
            const filePreview = document.getElementById('filePreview');
            const filePreviewName = document.getElementById('filePreviewName');
            const filePreviewIcon = document.getElementById('filePreviewIcon');

            // File type icons and colors
            const fileIcons = {
                'pdf': 'fas fa-file-pdf text-red-500',
                'doc': 'fas fa-file-word text-blue-500',
                'docx': 'fas fa-file-word text-blue-500',
                'txt': 'fas fa-file-alt text-gray-500',
                'jpg': 'fas fa-file-image text-green-500',
                'jpeg': 'fas fa-file-image text-green-500',
                'png': 'fas fa-file-image text-green-500',
                'zip': 'fas fa-file-archive text-yellow-500',
                'rar': 'fas fa-file-archive text-yellow-500'
            };

            // File input change handler
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];

                if (file) {
                    // Get file extension
                    const extension = file.name.split('.').pop().toLowerCase();

                    // Update file preview
                    filePreviewName.textContent = file.name;
                    filePreviewIcon.className = fileIcons[extension] || 'fas fa-file text-gray-500';

                    // Show preview and hide upload content
                    uploadContent.style.display = 'none';
                    filePreview.classList.remove('hidden');

                    // Update upload area styling
                    uploadArea.classList.remove('border-gray-300', 'dark:border-gray-600', 'bg-gray-50', 'dark:bg-gray-700/30');
                    uploadArea.classList.add('border-green-200', 'dark:border-green-800', 'bg-green-50', 'dark:bg-green-900/20');

                    console.log('File selected for upload:', {
                        name: file.name,
                        size: file.size,
                        type: file.type
                    });
                }
            });

            // Drag and drop functionality
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                uploadArea.classList.add('border-blue-400', 'dark:border-blue-600', 'bg-blue-100', 'dark:bg-blue-900/40');
                uploadArea.classList.remove('border-gray-300', 'dark:border-gray-600');
            }

            function unhighlight() {
                uploadArea.classList.remove('border-blue-400', 'dark:border-blue-600', 'bg-blue-100', 'dark:bg-blue-900/40');
                uploadArea.classList.add('border-gray-300', 'dark:border-gray-600');
            }

            uploadArea.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;

                if (files.length > 0) {
                    fileInput.files = files;
                    const event = new Event('change');
                    fileInput.dispatchEvent(event);
                }
            }
        });

        function clearFile() {
            const fileInput = document.getElementById('assignment_file');
            const uploadArea = document.getElementById('uploadArea');
            const uploadContent = document.getElementById('uploadContent');
            const filePreview = document.getElementById('filePreview');

            fileInput.value = '';
            uploadContent.style.display = 'block';
            filePreview.classList.add('hidden');
            uploadArea.classList.remove('border-green-200', 'dark:border-green-800', 'bg-green-50', 'dark:bg-green-900/20');
            uploadArea.classList.add('border-gray-300', 'dark:border-gray-600', 'bg-gray-50', 'dark:bg-gray-700/30');
        }
    </script>
@endsection
