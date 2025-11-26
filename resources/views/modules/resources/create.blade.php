@extends('layouts.app')

@section('title', 'Upload Resource - Lincoln eLearning')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Upload Resource</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">Add new educational resources to the system</p>
            </div>

            <!-- Upload Form -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                <form action="{{ route('admin.resources.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Basic Information</h3>
                        </div>

                        <!-- Title -->
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Resource Title *
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Enter resource title" required>
                            @error('title')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                      placeholder="Brief description of the resource">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subject and Topic -->
                        <div>
                            <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Subject *
                            </label>
                            <select name="subject_id" id="subject_id"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    required>
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}"
                                        {{ old('subject_id', $preSelectedTopic->subject_id ?? '') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="topic_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Topic *
                            </label>
                            <select name="topic_id" id="topic_id"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    required>
                                <option value="">Select Topic</option>
                                <!-- Load topics based on pre-selected subject or old subject_id -->
                                @php
                                    $currentSubjectId = old('subject_id', $preSelectedTopic->subject_id ?? '');
                                    $currentTopicId = old('topic_id', $preSelectedTopic->id ?? '');

                                    if ($currentSubjectId) {
                                        $topics = \App\Models\Teaching\Topic::where('subject_id', $currentSubjectId)
                                            ->where('is_active', true)
                                            ->ordered()
                                            ->get();
                                    } else {
                                        $topics = collect();
                                    }
                                @endphp

                                @foreach($topics as $topic)
                                    <option value="{{ $topic->id }}"
                                        {{ $currentTopicId == $topic->id ? 'selected' : '' }}>
                                        {{ $topic->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('topic_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- File Upload -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">File Upload</h3>

                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                                <input type="file" name="file" id="file"
                                       class="hidden"
                                       accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.mp4,.avi,.mov,.mp3,.wav,.jpg,.jpeg,.png,.gif,.zip,.rar"
                                       required>

                                <div id="file-upload-area" class="cursor-pointer">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                    <p class="text-lg font-medium text-gray-900 dark:text-white mb-1">
                                        Click to upload or drag and drop
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        PDF, DOC, PPT, XLS, MP4, MP3, JPG, PNG, ZIP, RAR (Max: 100MB)
                                    </p>
                                </div>

                                <div id="file-preview" class="hidden mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <i id="file-icon" class="fas fa-file text-xl text-gray-400 mr-3"></i>
                                            <div>
                                                <p id="file-name" class="text-sm font-medium text-gray-900 dark:text-white"></p>
                                                <p id="file-size" class="text-sm text-gray-500 dark:text-gray-400"></p>
                                            </div>
                                        </div>
                                        <button type="button" id="remove-file" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @error('file')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Access Settings -->
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Access Settings</h3>
                        </div>

                        <!-- Access Level -->
                        <div>
                            <label for="access_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Access Level *
                            </label>
                            <select name="access_level" id="access_level"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    required>
                                <option value="public" {{ old('access_level') == 'public' ? 'selected' : '' }}>Public - All users</option>
                                <option value="class_only" {{ old('access_level') == 'class_only' ? 'selected' : '' }}>Class Only - Specific class</option>
                                <option value="teacher_only" {{ old('access_level') == 'teacher_only' ? 'selected' : '' }}>Teacher Only - Teachers and admins</option>
                            </select>
                            @error('access_level')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Class Selection (only for class_only access) -->
                        <div id="class-selection" class="{{ old('access_level') != 'class_only' ? 'hidden' : '' }}">
                            <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Select Class *
                            </label>
                            <select name="class_id" id="class_id"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="md:col-span-2">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                    Make this resource active immediately
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.resources.index') }}"
                           class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                            Upload Resource
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // File upload handling
            const fileInput = document.getElementById('file');
            const fileUploadArea = document.getElementById('file-upload-area');
            const filePreview = document.getElementById('file-preview');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');
            const fileIcon = document.getElementById('file-icon');
            const removeFileBtn = document.getElementById('remove-file');

            // Click to upload
            fileUploadArea.addEventListener('click', () => fileInput.click());

            // Drag and drop
            fileUploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                fileUploadArea.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
            });

            fileUploadArea.addEventListener('dragleave', () => {
                fileUploadArea.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
            });

            fileUploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                fileUploadArea.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');

                if (e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    handleFileSelect(e.dataTransfer.files[0]);
                }
            });

            // File input change
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length) {
                    handleFileSelect(e.target.files[0]);
                }
            });

            // Remove file
            removeFileBtn.addEventListener('click', (e) => {
                e.preventDefault();
                fileInput.value = '';
                filePreview.classList.add('hidden');
                fileUploadArea.classList.remove('hidden');
            });

            function handleFileSelect(file) {
                const validTypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'mp4', 'avi', 'mov', 'mp3', 'wav', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar'];
                const fileExtension = file.name.split('.').pop().toLowerCase();

                if (!validTypes.includes(fileExtension)) {
                    alert('Invalid file type. Please select a supported file.');
                    return;
                }

                if (file.size > 100 * 1024 * 1024) { // 100MB
                    alert('File size exceeds 100MB limit.');
                    return;
                }

                // Update preview
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);

                // Set file icon
                const iconClass = getFileIconClass(fileExtension);
                fileIcon.className = `${iconClass} text-xl mr-3`;

                // Show preview
                filePreview.classList.remove('hidden');
                fileUploadArea.classList.add('hidden');
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            function getFileIconClass(extension) {
                const iconMap = {
                    'pdf': 'fas fa-file-pdf text-red-500',
                    'doc': 'fas fa-file-word text-blue-500',
                    'docx': 'fas fa-file-word text-blue-500',
                    'ppt': 'fas fa-file-powerpoint text-orange-500',
                    'pptx': 'fas fa-file-powerpoint text-orange-500',
                    'xls': 'fas fa-file-excel text-green-500',
                    'xlsx': 'fas fa-file-excel text-green-500',
                    'mp4': 'fas fa-file-video text-purple-500',
                    'avi': 'fas fa-file-video text-purple-500',
                    'mov': 'fas fa-file-video text-purple-500',
                    'mp3': 'fas fa-file-audio text-yellow-500',
                    'wav': 'fas fa-file-audio text-yellow-500',
                    'jpg': 'fas fa-file-image text-pink-500',
                    'jpeg': 'fas fa-file-image text-pink-500',
                    'png': 'fas fa-file-image text-pink-500',
                    'gif': 'fas fa-file-image text-pink-500',
                    'zip': 'fas fa-file-archive text-gray-500',
                    'rar': 'fas fa-file-archive text-gray-500'
                };
                return iconMap[extension] || 'fas fa-file text-gray-400';
            }

            // Dynamic topic loading based on subject
            const subjectSelect = document.getElementById('subject_id');
            const topicSelect = document.getElementById('topic_id');

            subjectSelect.addEventListener('change', function() {
                const subjectId = this.value;

                if (!subjectId) {
                    topicSelect.innerHTML = '<option value="">Select Topic</option>';
                    return;
                }

                // Show loading
                topicSelect.innerHTML = '<option value="">Loading topics...</option>';
                topicSelect.disabled = true;

                fetch(`{{ route('admin.resources.topics.by-subject') }}?subject_id=${subjectId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            topicSelect.innerHTML = '<option value="">Select Topic</option>';
                            data.topics.forEach(topic => {
                                const option = new Option(topic.title, topic.id);
                                topicSelect.add(option);
                            });
                            topicSelect.disabled = false;
                        } else {
                            topicSelect.innerHTML = '<option value="">No topics found</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading topics:', error);
                        topicSelect.innerHTML = '<option value="">Error loading topics</option>';
                    });
            });

            // Show/hide class selection based on access level
            const accessLevelSelect = document.getElementById('access_level');
            const classSelection = document.getElementById('class-selection');

            accessLevelSelect.addEventListener('change', function() {
                if (this.value === 'class_only') {
                    classSelection.classList.remove('hidden');
                    // Make class selection required
                    document.getElementById('class_id').required = true;
                } else {
                    classSelection.classList.add('hidden');
                    // Make class selection not required
                    document.getElementById('class_id').required = false;
                }
            });

            // If we have a pre-selected topic, ensure the topics are loaded
            @if($preSelectedTopic)
            // Trigger the subject change to load topics if subject is already selected
            if (document.getElementById('subject_id').value) {
                const event = new Event('change');
                document.getElementById('subject_id').dispatchEvent(event);
            }
            @endif
        });
    </script>
