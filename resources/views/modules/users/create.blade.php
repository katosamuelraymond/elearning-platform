@extends('layouts.app')

@section('title', 'Create User - Lincoln eLearning')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create New User</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Add new students, teachers, or staff members to the system</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-medium text-red-800 dark:text-red-400">Please fix the following errors:</h4>
                        <ul class="mt-1 text-sm text-red-600 dark:text-red-300 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Basic Information Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Enter full name">
                    </div>

                    <!-- Email Field (hidden for students) -->
                    <div id="email_field">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Enter email address">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password *</label>
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Enter password">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm Password *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Confirm password">
                    </div>

                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role *</label>
                        <select id="role_id" name="role_id" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white role-selector">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }} data-role="{{ $role->name }}">
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="profile_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Profile Image</label>
                        <input type="file" id="profile_image" name="profile_image"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            accept="image/*">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="ml-3 text-gray-700 dark:text-gray-300">Active User</span>
                    </label>
                </div>
            </div>

            <!-- Profile Information Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Profile Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Gender Field (shown only for students) -->
                    <div id="gender_field" style="display: none;">
                        <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gender *</label>
                        <select id="gender" name="gender" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Gender</option>
                            <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Male</option>
                            <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Enter phone number">
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                        <textarea id="address" name="address" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Enter full address">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Student Specific Information -->
            <div id="student_section" class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6" style="display: none;">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Student Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="admission_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Admission Year *</label>
                        <input type="text" id="admission_year" name="admission_year" value="{{ old('admission_year') }}" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="e.g., 2024">
                    </div>

                    <div>
                        <label for="student_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Student Type *</label>
                        <select id="student_type" name="student_type" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Type</option>
                            <option value="U" {{ old('student_type') == 'U' ? 'selected' : '' }}>Ugandan</option>
                            <option value="F" {{ old('student_type') == 'F' ? 'selected' : '' }}>Foreign</option>
                        </select>
                    </div>

                    <div>
                        <label for="education_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Education Level *</label>
                        <select id="education_level" name="education_level" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Level</option>
                            <option value="O" {{ old('education_level') == 'O' ? 'selected' : '' }}>O-Level</option>
                            <option value="A" {{ old('education_level') == 'A' ? 'selected' : '' }}>A-Level</option>
                        </select>
                    </div>

                    <div>
                        <label for="admission_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Admission Number</label>
                        <input type="text" id="admission_number" name="admission_number" value="{{ old('admission_number') }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Leave blank to auto-generate">
                        <p class="text-xs text-gray-500 mt-1">If left blank, system will generate automatically (4 digits: 0001)</p>
                    </div>

                    <!-- Class Assignment -->
                    <div>
                        <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Class *</label>
                        <select id="class_id" name="class_id" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} ({{ $class->level }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="stream_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stream *</label>
                        <select id="stream_id" name="stream_id" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Stream</option>
                            @foreach($streams as $stream)
                                <option value="{{ $stream->id }}" {{ old('stream_id') == $stream->id ? 'selected' : '' }}>
                                    {{ $stream->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="academic_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic Year *</label>
                        <input type="text" id="academic_year" name="academic_year" value="{{ old('academic_year') }}" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="e.g., 2024-2025">
                    </div>
                </div>

                <!-- Parent Information -->
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mt-6 mb-4">Parent/Guardian Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="parent_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Parent Name</label>
                        <input type="text" id="parent_name" name="parent_name" value="{{ old('parent_name') }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Enter parent/guardian name">
                    </div>

                    <div>
                        <label for="parent_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Parent Phone</label>
                        <input type="text" id="parent_phone" name="parent_phone" value="{{ old('parent_phone') }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Enter parent phone number">
                    </div>

                    <div>
                        <label for="parent_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Parent Email</label>
                        <input type="email" id="parent_email" name="parent_email" value="{{ old('parent_email') }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Enter parent email">
                    </div>
                </div>

                <!-- Auto-generated Email Preview -->
                <div id="email_preview" class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800" style="display: none;">
                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">Auto-generated Email Preview</h4>
                    <p class="text-sm text-blue-700 dark:text-blue-400">
                        Student email will be generated in format: <span id="email_format" class="font-mono">23/A/0001/F@lhs.ac.ug</span>
                    </p>
                    <p class="text-xs text-blue-600 dark:text-blue-500 mt-1">
                        Format: [Year]/[Level]/[Admission Number]/[Gender]@lhs.ac.ug
                    </p>
                </div>
            </div>

            <!-- Teacher Specific Information -->
            <div id="teacher_section" class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6" style="display: none;">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Teacher Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="qualification" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Qualification</label>
                        <input type="text" id="qualification" name="qualification" value="{{ old('qualification') }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="e.g., B.Sc. Education">
                    </div>

                    <div>
                        <label for="specialization" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Specialization</label>
                        <input type="text" id="specialization" name="specialization" value="{{ old('specialization') }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="e.g., Mathematics, Physics">
                    </div>

                    <div>
                        <label for="employment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Employment Date</label>
                        <input type="date" id="employment_date" name="employment_date" value="{{ old('employment_date') }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Emergency Contact</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="emergency_contact" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Contact Name</label>
                        <input type="text" id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact') }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Enter emergency contact name">
                    </div>

                    <div>
                        <label for="emergency_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Phone</label>
                        <input type="text" id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone') }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Enter emergency phone number">
                    </div>
                </div>

                <div class="mt-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Additional Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="Any additional notes or information">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.users.index') }}" class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role_id');
    const studentSection = document.getElementById('student_section');
    const teacherSection = document.getElementById('teacher_section');
    const genderField = document.getElementById('gender_field');
    const emailField = document.getElementById('email_field');
    const emailPreview = document.getElementById('email_preview');
    const emailInput = document.getElementById('email');

    console.log('Elements found:', {
        roleSelect: !!roleSelect,
        studentSection: !!studentSection,
        teacherSection: !!teacherSection,
        genderField: !!genderField,
        emailField: !!emailField,
        emailPreview: !!emailPreview,
        emailInput: !!emailInput
    });

    function toggleSections() {
        const selectedOption = roleSelect.options[roleSelect.selectedIndex];
        const roleName = selectedOption.getAttribute('data-role');

        console.log('Selected role:', roleName);

        // Hide all sections first
        if (studentSection) studentSection.style.display = 'none';
        if (teacherSection) teacherSection.style.display = 'none';
        if (genderField) genderField.style.display = 'none';
        if (emailPreview) emailPreview.style.display = 'none';

        // Show email field by default, make it required
        if (emailField) emailField.style.display = 'block';
        if (emailInput) emailInput.required = true;

        // Show relevant sections based on role
        if (roleName === 'student') {
            console.log('Showing student sections');
            if (studentSection) studentSection.style.display = 'block';
            if (genderField) genderField.style.display = 'block';
            if (emailField) emailField.style.display = 'none';
            if (emailInput) emailInput.required = false;
            if (emailPreview) emailPreview.style.display = 'block';
        } else if (roleName === 'teacher') {
            console.log('Showing teacher sections');
            if (teacherSection) teacherSection.style.display = 'block';
            if (emailField) emailField.style.display = 'block';
            if (emailInput) emailInput.required = true;
        } else {
            console.log('Showing admin/staff sections');
            if (emailField) emailField.style.display = 'block';
            if (emailInput) emailInput.required = true;
        }
    }

    // Update email preview when student fields change
    function updateEmailPreview() {
        const admissionYear = document.getElementById('admission_year');
        const educationLevel = document.getElementById('education_level');
        const admissionNumber = document.getElementById('admission_number');
        const gender = document.getElementById('gender');

        if (admissionYear && educationLevel && gender && admissionYear.value && educationLevel.value && gender.value) {
            const yearShort = admissionYear.value.slice(-2);
            const admissionNum = admissionNumber.value ? admissionNumber.value.padStart(4, '0') : '0001';
            const emailFormat = `${yearShort}/${educationLevel.value}/${admissionNum}/${gender.value}@lhs.ac.ug`;
            document.getElementById('email_format').textContent = emailFormat;
        }
    }

    // Add event listeners for student fields
    const admissionYear = document.getElementById('admission_year');
    const educationLevel = document.getElementById('education_level');
    const admissionNumber = document.getElementById('admission_number');
    const gender = document.getElementById('gender');

    if (admissionYear) admissionYear.addEventListener('input', updateEmailPreview);
    if (educationLevel) educationLevel.addEventListener('change', updateEmailPreview);
    if (admissionNumber) admissionNumber.addEventListener('input', updateEmailPreview);
    if (gender) gender.addEventListener('change', updateEmailPreview);

    if (roleSelect) {
        roleSelect.addEventListener('change', toggleSections);
    }

    // Initial call to set correct sections on page load
    toggleSections();
});
</script>
@endsection
