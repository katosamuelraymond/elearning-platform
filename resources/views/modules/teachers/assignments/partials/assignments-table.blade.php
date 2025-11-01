<div class="overflow-x-auto">
    <table class="w-full">
        <thead>
        <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Teacher
            </th>
            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Subject
            </th>
            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Class & Stream
            </th>
            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Role
            </th>
            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Academic Year
            </th>
            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Status
            </th>
            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Actions
            </th>
        </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        @forelse($assignments as $assignment)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            @if($assignment->teacher->profile_image)
                                <img class="h-10 w-10 rounded-full object-cover"
                                     src="{{ asset('storage/' . $assignment->teacher->profile_image) }}"
                                     alt="{{ $assignment->teacher->name }}">
                            @else
                                <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                        <span class="text-blue-600 dark:text-blue-400 font-medium text-sm">
                                            {{ substr($assignment->teacher->name, 0, 1) }}
                                        </span>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $assignment->teacher->name }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $assignment->teacher->email }}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $assignment->subject->name }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $assignment->subject->code }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $assignment->class->name }}
                    </div>
                    @if($assignment->stream)
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $assignment->stream->name }}
                        </div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            @if($assignment->role === 'class_teacher') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                            @elseif($assignment->role === 'subject_teacher') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @else bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                            @endif">
                            {{ $roles[$assignment->role] }}
                        </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ $assignment->academic_year }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <button type="button"
                            class="toggle-status-btn inline-flex px-3 py-1 text-xs font-semibold rounded-full transition-colors cursor-pointer
                                    @if($assignment->is_active)
                                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 hover:bg-green-200 dark:hover:bg-green-800
                                    @else
                                        bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 hover:bg-red-200 dark:hover:bg-red-800
                                    @endif"
                            data-assignment-id="{{ $assignment->id }}">
                        {{ $assignment->is_active ? 'Active' : 'Inactive' }}
                    </button>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('admin.teacher-assignments.edit', $assignment) }}"
                           class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                           title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button"
                                class="delete-assignment-btn text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                                data-assignment-id="{{ $assignment->id }}"
                                title="Delete">
                            <i class="fas fa-trash"></i>
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

<!-- Pagination -->
@if($assignments->hasPages())
    <div class="bg-white dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $assignments->links() }}
    </div>
@endif
