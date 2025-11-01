@extends('layouts.app')

@section('title', 'View Question - Lincoln eLearning')

@section('content')
    @php
        $isAdmin = auth()->user()->isAdmin();
        $isTeacher = auth()->user()->isTeacher();

        // Determine routes based on user role
        $indexRoute = $isAdmin ? route('admin.questions.index') : route('teacher.questions.index');
        $editRoute = $isAdmin ? route('admin.questions.edit', $question) : route('teacher.questions.edit', $question);
        $destroyRoute = $isAdmin ? route('admin.questions.destroy', $question) : route('teacher.questions.destroy', $question);

        // Check if teacher can edit this question (only their own)
        $canEdit = $isAdmin || $question->created_by == auth()->id();
    @endphp

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Question Details</h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-2">
                        View question information and usage
                    </p>
                    @if($isTeacher)
                        <div class="mt-1 text-sm text-blue-600 dark:text-blue-400">
                            @if($question->created_by == auth()->id())
                                <i class="fas fa-user-check mr-1"></i> Created by you
                            @else
                                <i class="fas fa-users mr-1"></i> Shared question
                            @endif
                        </div>
                    @endif
                </div>
                <div class="flex space-x-3">
                    @if($canEdit)
                        <a href="{{ $editRoute }}"
                           class="ajax-link bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Question
                        </a>
                    @endif
                    <a href="{{ $indexRoute }}"
                       class="ajax-link bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Question Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
                <!-- Basic Information -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                            <p class="text-gray-900 dark:text-white">{{ $question->subject->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Question Type</label>
                            <p class="text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $question->type)) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Difficulty</label>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $question->difficulty == 'easy' ? 'bg-green-100 text-green-800' :
                                   ($question->difficulty == 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($question->difficulty) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Points</label>
                            <p class="text-gray-900 dark:text-white">{{ $question->points }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $question->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                {{ $question->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ $isAdmin ? 'Created By' : 'Author' }}
                            </label>
                            <p class="text-gray-900 dark:text-white">
                                @if($isAdmin)
                                    {{ $question->creator->name }}
                                @else
                                    {{ $question->created_by == auth()->id() ? 'You' : $question->creator->name }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Question Text -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Question</h2>
                    <p class="text-gray-900 dark:text-white text-lg">{{ $question->question_text }}</p>
                </div>

                <!-- Explanation -->
                @if($question->explanation)
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Explanation</h2>
                        <p class="text-gray-900 dark:text-white">{{ $question->explanation }}</p>
                    </div>
                @endif

                <!-- Type-Specific Details -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Answer Details</h2>

                    @switch($question->type)
                        @case('mcq')
                            <div class="space-y-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Options</label>
                                <div class="space-y-2">
                                    @foreach($question->options as $index => $option)
                                        <div class="flex items-center space-x-3 p-3 rounded-lg
                                            {{ $option->is_correct ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-700' }}">
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-300 w-6">
                                                {{ $index + 1 }}.
                                            </span>
                                            <span class="flex-1 text-gray-900 dark:text-white">
                                                {{ $option->option_text }}
                                            </span>
                                            @if($option->is_correct)
                                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                                    Correct
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @break

                        @case('true_false')
                            <div class="flex items-center space-x-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Correct Answer:
                                </span>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full text-sm font-medium">
                                    {{ ucfirst($question->correct_answer) }}
                                </span>
                            </div>
                            @break

                        @case('short_answer')
                            @if($question->details && $question->details['expected_answer'])
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expected Answer</label>
                                    <p class="text-gray-900 dark:text-white p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        {{ $question->details['expected_answer'] }}
                                    </p>
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400 italic">No expected answer provided</p>
                            @endif
                            @break

                        @case('essay')
                            @if($question->details && $question->details['grading_rubric'])
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Grading Rubric</label>
                                    <p class="text-gray-900 dark:text-white p-3 bg-gray-50 dark:bg-gray-700 rounded-lg whitespace-pre-wrap">
                                        {{ $question->details['grading_rubric'] }}
                                    </p>
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400 italic">No grading rubric provided</p>
                            @endif
                            @break

                        @case('fill_blank')
                            <div class="space-y-4">
                                @if($question->details && $question->details['blank_question'])
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Question with Blanks</label>
                                        <p class="text-gray-900 dark:text-white p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            {{ $question->details['blank_question'] }}
                                        </p>
                                    </div>
                                @endif

                                @if($question->details && $question->details['blank_answers'])
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correct Answers</label>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($question->details['blank_answers'] as $answer)
                                                <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full text-sm">
                                                    {{ $answer }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @break
                    @endswitch
                </div>

                <!-- Usage Statistics -->
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Usage Statistics</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $question->exams_count ?? 0 }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Exams Using This Question</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $question->created_at->format('M j, Y') }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Created Date</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $question->updated_at->format('M j, Y') }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Last Updated</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone (Only show for users who can edit/delete) -->
            @if($canEdit)
                <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow border border-red-200 dark:border-red-800">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-red-700 dark:text-red-400 mb-4">Danger Zone</h2>
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Delete Question</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    Once deleted, this question cannot be recovered.
                                    @if($question->exams_count > 0)
                                        <span class="text-red-600 dark:text-red-400 font-medium">
                                            This question is being used in {{ $question->exams_count }} exam(s) and cannot be deleted.
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                @if($question->exams_count == 0)
                                    <form action="{{ $destroyRoute }}" method="POST" class="ajax-form"
                                          onsubmit="return confirm('Are you sure you want to delete this question? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                                            <i class="fas fa-trash mr-2"></i>
                                            Delete Question
                                        </button>
                                    </form>
                                @else
                                    <button disabled
                                            class="bg-red-400 text-white px-6 py-3 rounded-lg font-medium cursor-not-allowed flex items-center opacity-50">
                                        <i class="fas fa-trash mr-2"></i>
                                        Cannot Delete
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize AJAX links
            initializeAjaxLinks();

            // Initialize AJAX forms
            initializeAjaxForms();
        });

        function initializeAjaxLinks() {
            document.querySelectorAll('.ajax-link').forEach(link => {
                link.removeEventListener('click', handleAjaxLinkClick);
                link.addEventListener('click', handleAjaxLinkClick);
            });
        }

        function handleAjaxLinkClick(e) {
            e.preventDefault();
            e.stopPropagation();

            const url = this.href;
            loadContent(url);
        }

        function initializeAjaxForms() {
            document.querySelectorAll('.ajax-form').forEach(form => {
                form.removeEventListener('submit', handleAjaxFormSubmit);
                form.addEventListener('submit', handleAjaxFormSubmit);
            });
        }

        function handleAjaxFormSubmit(e) {
            e.preventDefault();

            const form = e.target;
            const url = form.action;
            const method = form.method;
            const formData = new FormData(form);

            // Show loading
            showLoading();

            fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // For successful deletions, redirect to index
                        if (method === 'DELETE') {
                            const indexUrl = '{{ $indexRoute }}';
                            loadContent(indexUrl);
                        } else {
                            // For other form submissions, reload current page
                            window.location.reload();
                        }
                    } else {
                        throw new Error(data.error || 'Form submission failed');
                    }
                })
                .catch(error => {
                    console.error('Form submission failed:', error);
                    alert('Error: ' + error.message);
                    window.location.reload();
                });
        }

        function loadContent(url) {
            // Show loading indicator
            showLoading();

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.text())
                .then(html => {
                    // Update main content area
                    document.querySelector('main').innerHTML = html;

                    // Update browser history
                    history.pushState(null, null, url);

                    // Re-initialize AJAX handlers
                    initializeAjaxLinks();
                    initializeAjaxForms();
                })
                .catch(error => {
                    console.error('AJAX loading failed:', error);
                    window.location.href = url;
                });
        }

        function showLoading() {
            document.querySelector('main').innerHTML = `
                <div class="flex justify-center items-center h-64">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                </div>
            `;
        }

        // Handle browser back/forward buttons
        window.addEventListener('popstate', function() {
            loadContent(window.location.href);
        });
    </script>
@endpush
