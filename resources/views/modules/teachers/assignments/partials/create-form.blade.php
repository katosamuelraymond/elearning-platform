<div class="p-6">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Create Teacher Assignment</h2>

    <form action="{{ route('admin.teacher-assignments.store') }}" method="POST" class="ajax-form space-y-6">
        @csrf

        <!-- Same form fields as in create.blade.php -->
        <div>
            <label for="teacher_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Teacher *
            </label>
            <select id="teacher_id" name="teacher_id" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">Select Teacher</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->name }} - {{ $teacher->email }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Include other form fields similarly -->

        <div class="flex justify-end space-x-3 pt-4">
            <button type="button" onclick="closeModal()"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                Cancel
            </button>
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                Create Assignment
            </button>
        </div>
    </form>
</div>
