@extends('layouts.app')

@section('title', 'Create Assignment - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Button at the Top -->
            <div class="mb-8">
                <a href="{{ auth()->user()->isAdmin() ? route('admin.assignments.index') : route('teacher.assignments.index') }}"
                   class="inline-flex items-center px-5 py-3 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 hover:scale-105 hover:shadow-lg">
                    <i class="fas fa-arrow-left mr-3 text-blue-500"></i>
                    <span class="font-medium">Back to Assignments</span>
                </a>
            </div>



            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-lg mb-6 animate-pulse" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-400">
                                Please fix the following issues:
                            </h3>
                            <div class="mt-2 text-sm text-red-700 dark:text-red-300">
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

            <form action="{{
                auth()->user()->isAdmin() ? route('admin.assignments.store') : route('teacher.assignments.store')
            }}" method="POST" enctype="multipart/form-data" id="assignmentForm">
                @csrf

                <input type="hidden" name="is_published" value="1">

                <!-- Assignment Details Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 mb-8 border border-gray-100 dark:border-gray-700 transform hover:shadow-2xl transition-all duration-300">
                    <div class="flex items-center mb-8">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-file-circle-plus text-xl text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Assignment Details</h2>
                            <p class="text-gray-500 dark:text-gray-400">Basic information about your assignment</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Title -->
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <i class="fas fa-heading text-blue-500 mr-2 text-sm"></i>
                                Assignment Title *
                            </label>
                            <input type="text" name="title" id="title"
                                   class="w-full px-4 py-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-300 placeholder-gray-400"
                                   placeholder="Enter a compelling assignment title..."
                                   value="{{ old('title') }}" required>
                        </div>

                        <!-- Class and Subject -->
                        <div class="space-y-2">
                            <label for="class_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <i class="fas fa-users text-green-500 mr-2 text-sm"></i>
                                Class *
                            </label>
                            <select name="class_id" id="class_id"
                                    class="w-full px-4 py-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-all duration-300 appearance-none bg-gradient-to-b from-white to-gray-50 dark:from-gray-700 dark:to-gray-800" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="subject_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <i class="fas fa-book text-purple-500 mr-2 text-sm"></i>
                                Subject *
                            </label>
                            <select name="subject_id" id="subject_id"
                                    class="w-full px-4 py-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white transition-all duration-300 appearance-none bg-gradient-to-b from-white to-gray-50 dark:from-gray-700 dark:to-gray-800" required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Due Date and Points -->
                        <div class="space-y-2">
                            <label for="due_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <i class="fas fa-calendar-alt text-orange-500 mr-2 text-sm"></i>
                                Due Date *
                            </label>
                            <input type="datetime-local" name="due_date" id="due_date"
                                   class="w-full px-4 py-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white transition-all duration-300 bg-gradient-to-b from-white to-gray-50 dark:from-gray-700 dark:to-gray-800"
                                   value="{{ old('due_date') }}" required>
                        </div>

                        <div class="space-y-2">
                            <label for="max_points" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <i class="fas fa-star text-yellow-500 mr-2 text-sm"></i>
                                Max Points *
                            </label>
                            <input type="number" name="max_points" id="max_points" min="1"
                                   class="w-full px-4 py-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:text-white transition-all duration-300 bg-gradient-to-b from-white to-gray-50 dark:from-gray-700 dark:to-gray-800"
                                   placeholder="e.g., 100"
                                   value="{{ old('max_points', 100) }}" required>
                        </div>

                        <!-- Instructions -->
                        <div class="md:col-span-2 space-y-2">
                            <label for="instructions" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <i class="fas fa-list-check text-indigo-500 mr-2 text-sm"></i>
                                Instructions
                            </label>
                            <textarea name="instructions" id="instructions" rows="4"
                                      class="w-full px-4 py-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-all duration-300 resize-none bg-gradient-to-b from-white to-gray-50 dark:from-gray-700 dark:to-gray-800"
                                      placeholder="Add clear and detailed instructions for your students...">{{ old('instructions') }}</textarea>
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2 space-y-2">
                            <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <i class="fas fa-align-left text-gray-500 mr-2 text-sm"></i>
                                Description (Optional)
                            </label>
                            <textarea name="description" id="description" rows="3"
                                      class="w-full px-4 py-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-700 dark:text-white transition-all duration-300 resize-none bg-gradient-to-b from-white to-gray-50 dark:from-gray-700 dark:to-gray-800"
                                      placeholder="Add a brief description or context for this assignment...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- File & Submission Settings Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 mb-8 border border-gray-100 dark:border-gray-700 transform hover:shadow-2xl transition-all duration-300">
                    <div class="flex items-center mb-8">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-cog text-xl text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Submission Settings</h2>
                            <p class="text-gray-500 dark:text-gray-400">Configure file upload and format settings</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- File Upload Section -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center">
                                <i class="fas fa-paperclip text-blue-500 mr-2 text-sm"></i>
                                Assignment File (Optional)
                            </label>

                            <!-- File Upload Area -->
                            <div class="relative">
                                <input type="file" name="assignment_file" id="assignment_file"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                       accept=".pdf,.doc,.docx,.txt,.jpg,.png,.zip,.rar">

                                <div id="uploadArea" class="border-3 border-dashed border-blue-200 dark:border-blue-800 rounded-2xl p-8 text-center bg-blue-50 dark:bg-blue-900/20 transition-all duration-300 hover:border-blue-300 dark:hover:border-blue-700 hover:bg-blue-100 dark:hover:bg-blue-900/30">
                                    <div id="uploadContent">
                                        <div class="w-20 h-20 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-cloud-upload-alt text-3xl text-blue-500 dark:text-blue-400"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                                            Upload Assignment File
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-400 mb-4 text-lg">
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
                                </div>
                            </div>

                            <!-- File Preview Section - ADDED THIS SECTION -->
                            <div id="filePreviewSection" class="mt-4 hidden">
                                <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-4 border border-green-200 dark:border-green-700">
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

                        <!-- Max File Size -->
                        <div class="space-y-2">
                            <label for="max_file_size" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <i class="fas fa-weight-hanging text-orange-500 mr-2 text-sm"></i>
                                Max File Size (MB) *
                            </label>
                            <input type="number" name="max_file_size" id="max_file_size" min="1"
                                   class="w-full px-4 py-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white transition-all duration-300 bg-gradient-to-b from-white to-gray-50 dark:from-gray-700 dark:to-gray-800"
                                   placeholder="e.g., 10"
                                   value="{{ old('max_file_size', 10) }}" required>
                        </div>

                        <!-- Allowed Formats -->
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <i class="fas fa-file-code text-purple-500 mr-2 text-sm"></i>
                                Allowed File Formats
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                                @php
                                    $formats = [
                                        'pdf' => ['PDF', 'fas fa-file-pdf text-red-500'],
                                        'doc' => ['DOC', 'fas fa-file-word text-blue-500'],
                                        'docx' => ['DOCX', 'fas fa-file-word text-blue-500'],
                                        'txt' => ['TXT', 'fas fa-file-alt text-gray-500'],
                                        'jpg' => ['JPG', 'fas fa-file-image text-green-500'],
                                        'png' => ['PNG', 'fas fa-file-image text-green-500'],
                                        'zip' => ['ZIP', 'fas fa-file-archive text-yellow-500']
                                    ];
                                    $old_formats = old('allowed_formats', ['pdf', 'doc', 'docx']);
                                @endphp
                                @foreach($formats as $format => [$label, $icon])
                                    <label class="relative flex flex-col items-center p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl cursor-pointer transition-all duration-300 hover:border-purple-300 dark:hover:border-purple-700 hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:scale-105 has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50 dark:has-[:checked]:bg-purple-900/30 has-[:checked]:ring-2 has-[:checked]:ring-purple-200 dark:has-[:checked]:ring-purple-800">
                                        <input type="checkbox" name="allowed_formats[]" value="{{ $format }}"
                                               class="absolute top-3 right-3 rounded border-gray-300 text-purple-600 shadow-sm focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600"
                                               @if(in_array($format, $old_formats)) checked @endif>
                                        <i class="{{ $icon }} text-xl mb-2"></i>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <button type="submit" name="draft" value="1"
                                class="flex items-center justify-center px-8 py-3 text-white bg-gradient-to-r from-gray-600 to-gray-700 rounded-xl hover:from-gray-700 hover:to-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-300 hover:scale-105 hover:shadow-lg transform">
                            <i class="fas fa-save mr-2"></i>
                            Save as Draft
                        </button>

                        <button type="submit"
                                class="flex items-center justify-center px-8 py-3 text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 hover:scale-105 hover:shadow-lg transform shadow-blue-500/25">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Publish Assignment
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
            const filePreviewSection = document.getElementById('filePreviewSection');
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

                    // Show preview section
                    filePreviewSection.classList.remove('hidden');

                    // Update upload area styling
                    uploadArea.classList.remove('border-blue-200', 'dark:border-blue-800', 'bg-blue-50', 'dark:bg-blue-900/20');
                    uploadArea.classList.add('border-green-200', 'dark:border-green-800', 'bg-green-50', 'dark:bg-green-900/20');

                    console.log('File selected:', {
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
                uploadArea.classList.remove('border-blue-200', 'dark:border-blue-800');
            }

            function unhighlight() {
                uploadArea.classList.remove('border-blue-400', 'dark:border-blue-600', 'bg-blue-100', 'dark:bg-blue-900/40');
                uploadArea.classList.add('border-blue-200', 'dark:border-blue-800');
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

            // Set minimum datetime for due date
            const now = new Date();
            const timezoneOffset = now.getTimezoneOffset() * 60000;
            const localISOTime = new Date(now - timezoneOffset).toISOString().slice(0, 16);
            const dueDateInput = document.querySelector('input[name="due_date"]');
            if (dueDateInput) {
                dueDateInput.min = localISOTime;
            }
        });

        function clearFile() {
            const fileInput = document.getElementById('assignment_file');
            const uploadArea = document.getElementById('uploadArea');
            const filePreviewSection = document.getElementById('filePreviewSection');

            fileInput.value = '';
            filePreviewSection.classList.add('hidden');
            uploadArea.classList.remove('border-green-200', 'dark:border-green-800', 'bg-green-50', 'dark:bg-green-900/20');
            uploadArea.classList.add('border-blue-200', 'dark:border-blue-800', 'bg-blue-50', 'dark:bg-blue-900/20');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    </script>

    <style>
        .has-\[\:\checked\]\:border-purple-500:has(:checked) {
            border-color: rgb(168, 85, 247);
        }
        .has-\[\:\checked\]\:bg-purple-50:has(:checked) {
            background-color: rgb(250, 245, 255);
        }
        .dark .has-\[\:\checked\]\:bg-purple-900\/30:has(:checked) {
            background-color: rgb(76, 29, 149, 0.3);
        }
        .has-\[\:\checked\]\:ring-2:has(:checked) {
            ring-width: 2px;
        }
        .has-\[\:\checked\]\:ring-purple-200:has(:checked) {
            --tw-ring-color: rgb(233, 213, 255);
        }
        .dark .has-\[\:\checked\]\:ring-purple-800:has(:checked) {
            --tw-ring-color: rgb(107, 33, 168);
        }
    </style>
@endsection
