@if(count($subjects) > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
            <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subject</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Classes & Streams</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>

            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($subjects as $subjectData)
                @php $subject = $subjectData['subject']; @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                <span class="text-blue-600 dark:text-blue-300 font-semibold text-sm">
                                    {{ substr($subject->name, 0, 2) }}
                                </span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $subject->name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $subject->description ?? 'No description' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            {{ $subject->code }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($subject->type === 'compulsory')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                Compulsory
                            </span>
                        @elseif($subject->type === 'elective')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                Elective
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                Optional
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        @if(count($subjectData['classes']) > 0)
                            <div class="flex flex-wrap gap-1">
                                @foreach($subjectData['classes'] as $classInfo)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        {{ $classInfo }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400 dark:text-gray-500">No classes assigned</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $roles = collect($subjectData['assignments'])->pluck('role')->unique();
                        @endphp
                        @foreach($roles as $role)
                            @if($role === 'class_teacher')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                    Class Teacher
                                </span>
                            @elseif($role === 'subject_teacher')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                    Subject Teacher
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                    Head Teacher
                                </span>
                            @endif
                        @endforeach
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination - Only show if assignments is a paginator and has pages -->
    @if($assignments instanceof \Illuminate\Pagination\LengthAwarePaginator && $assignments->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $assignments->links() }}
        </div>
    @endif
@else
    <div class="text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" opacity="0.5"/>
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No subjects assigned</h3>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            You haven't been assigned any subjects to teach for the current academic year.
        </p>
        <div class="mt-6">
            <a href="{{ route('teacher.dashboard') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Return to Dashboard
            </a>
        </div>
    </div>
@endif
