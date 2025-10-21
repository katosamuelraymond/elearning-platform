<?php

namespace App\Http\Controllers\Modules\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use App\Models\Role;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Stream;
use App\Models\Academic\StudentClassAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;

class AdminUsersController extends Controller
{
public function index()
{
    $query = User::with(['roles', 'profile'])->latest();

    // Search functionality - only by name and email
    if (request()->has('search') && !empty(request('search'))) {
        $search = request('search');
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Role filter
    if (request()->has('role') && !empty(request('role'))) {
        $role = request('role');
        $query->whereHas('roles', function($q) use ($role) {
            $q->where('name', $role);
        });
    }

    // Status filter
    if (request()->has('status') && !empty(request('status'))) {
        $status = request('status');
        $query->where('is_active', $status === 'active');
    }

    $users = $query->paginate(10);

    $viewData = [
        'users' => $users
    ];

    return $this->renderView('modules.users.index', $viewData);
}

    public function create()
{
    $roles = Role::where('is_active', true)->get();
    $classes = SchoolClass::where('is_active', true)->get();
    $streams = Stream::where('is_active', true)->get();

    return $this->renderView('modules.users.create', [
        'roles' => $roles,
        'classes' => $classes,
        'streams' => $streams,
        'showNavbar' => true,
        'showSidebar' => true,
        'showFooter' => true
    ]);
}


  public function store(Request $request)
{
    \Log::info('=== USER CREATION START ===');
    \Log::info('Form data:', $request->all());

    DB::beginTransaction();

    try {
        // Get the student role ID for conditional validation
        $studentRoleId = Role::where('name', 'student')->first()->id;

        $validationRules = [
            'name' => 'required|string|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'required|exists:roles,id',
            'profile_image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',

            // Profile fields
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',

            // Student specific - make conditional
            'admission_year' => "required_if:role_id,{$studentRoleId}|nullable|string",
            'student_type' => "required_if:role_id,{$studentRoleId}|nullable|in:U,F",
            'education_level' => "required_if:role_id,{$studentRoleId}|nullable|in:O,A",
            'admission_number' => 'nullable|string',
            'gender' => "required_if:role_id,{$studentRoleId}|nullable|in:M,F",

            // Parent info
            'parent_name' => 'nullable|string',
            'parent_phone' => 'nullable|string',
            'parent_email' => 'nullable|email',

            // Teacher specific
            'qualification' => 'nullable|string',
            'specialization' => 'nullable|string',
            'employment_date' => 'nullable|date',

            // Class assignment (for students)
            'class_id' => 'nullable|exists:school_classes,id',
            'stream_id' => 'nullable|exists:streams,id',
            'academic_year' => 'nullable|string',
        ];

        \Log::info('Validation rules:', $validationRules);

        $validator = Validator::make($request->all(), $validationRules, [
            'gender.required_if' => 'The gender field is required for students.',
            'admission_year.required_if' => 'The admission year field is required for students.',
            'student_type.required_if' => 'The student type field is required for students.',
            'education_level.required_if' => 'The education level field is required for students.',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        \Log::info('Validation passed');

        // Get selected role
        $role = Role::findOrFail($request->role_id);
        $isStudent = $role->name === 'student';
        $isTeacher = $role->name === 'teacher';

        \Log::info('Role detected:', ['role' => $role->name, 'isStudent' => $isStudent, 'isTeacher' => $isTeacher]);

        // Generate email and IDs based on role
        $email = null;
        $studentId = null;
        $teacherId = null;
        $employeeId = null;
        $admissionNumber = $request->admission_number;

        if ($isStudent) {
            \Log::info('Processing student user');

            // Validate required student fields
            if (!$request->admission_year || !$request->education_level || !$request->gender) {
                \Log::error('Missing required student fields');
                return back()->with('error', 'Admission year, education level, and gender are required for students.')->withInput();
            }

            if (!$admissionNumber) {
                $admissionNumber = User::generateAdmissionNumber(
                    $request->admission_year,
                    $request->student_type,
                    $request->education_level
                );
                \Log::info('Generated admission number:', ['number' => $admissionNumber]);
            }

            // Generate student ID in format: 23/A/0001/F
            $admissionYearShort = substr($request->admission_year, -2);
            $educationLevel = $request->education_level; // O or A
            $gender = $request->gender; // M or F

            $studentId = sprintf('%s/%s/%s/%s',
                $admissionYearShort,
                $educationLevel,
                str_pad($admissionNumber, 4, '0', STR_PAD_LEFT),
                $gender
            );

            // Generate email: 23/A/0001/F@lhs.ac.ug
            $email = $studentId . '@lhs.ac.ug';

            \Log::info('Generated student credentials:', ['student_id' => $studentId, 'email' => $email]);

        } elseif ($isTeacher) {
            \Log::info('Processing teacher user');
            $teacherId = 'TCH-' . strtoupper(uniqid());
            $email = $request->email;
            \Log::info('Teacher email will be:', ['email' => $email]);
        } else {
            \Log::info('Processing staff user');
            $employeeId = 'EMP-' . strtoupper(uniqid());
            $email = $request->email;
        }

        // For students, ensure email doesn't already exist
        if ($isStudent) {
            $existingUser = User::where('email', $email)->first();
            if ($existingUser) {
                \Log::error('Duplicate student email found:', ['email' => $email]);
                return back()->with('error', 'A user with this generated email already exists.')->withInput();
            }
        }

        \Log::info('Creating user with email:', ['email' => $email]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make($request->password),
            'is_active' => $request->is_active ?? true,

        ]);

        \Log::info('User created:', ['user_id' => $user->id]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile-images', 'public');
            $user->profile_image = $path;
            $user->save();
            \Log::info('Profile image uploaded:', ['path' => $path]);
        }

        // Create profile
        $profileData = [
            'user_id' => $user->id,
            'student_id' => $studentId,
            'teacher_id' => $teacherId,
            'employee_id' => $employeeId,
            'admission_year' => $request->admission_year,
            'student_type' => $request->student_type,
            'education_level' => $request->education_level,
            'admission_number' => $admissionNumber,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'address' => $request->address,
            'parent_name' => $request->parent_name,
            'parent_phone' => $request->parent_phone,
            'parent_email' => $request->parent_email,
            'qualification' => $request->qualification,
            'specialization' => $request->specialization,
            'employment_date' => $request->employment_date,
            'emergency_contact' => $request->emergency_contact,
            'emergency_phone' => $request->emergency_phone,
            'notes' => $request->notes,
        ];

        \Log::info('Creating profile with data:', $profileData);

        Profile::create($profileData);

        // Assign role
        $user->roles()->attach($request->role_id);
        \Log::info('Role assigned:', ['role_id' => $request->role_id]);

        // Handle class assignment for students
        if ($isStudent && $request->class_id && $request->stream_id && $request->academic_year) {
            StudentClassAssignment::create([
                'student_id' => $user->id,
                'class_id' => $request->class_id,
                'stream_id' => $request->stream_id,
                'academic_year' => $request->academic_year,
                'status' => 'active'
            ]);
            \Log::info('Class assignment created');
        }

        DB::commit();
        \Log::info('=== USER CREATION SUCCESS ===');

        return redirect()->route('admin.users.index')->with('success', 'User created successfully. ' . ($isStudent ? 'Student ID: ' . $studentId . ' Email: ' . $email : ''));

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('User creation error:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        return back()->with('error', 'Error creating user: ' . $e->getMessage())->withInput();
    }
}
    public function edit(User $user)
    {
        $roles = Role::where('is_active', true)->get();
        $classes = SchoolClass::where('is_active', true)->get();
        $streams = Stream::where('is_active', true)->get();
        $user->load(['profile', 'roles', 'studentClassAssignments']);

        return view('modules.users.edit', compact('user', 'roles', 'classes', 'streams'));
    }
     public function update(Request $request, User $user)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|confirmed|' . Rules\Password::defaults(),
                'role_id' => 'required|exists:roles,id',
                'profile_image' => 'nullable|image|max:2048',
                'is_active' => 'boolean',

                // Profile fields
                'date_of_birth' => 'nullable|date',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',

                // Student specific
                'admission_year' => 'nullable|string',
                'student_type' => 'nullable|in:U,F',
                'education_level' => 'nullable|in:O,A',
                'admission_number' => 'nullable|string',

                // Parent info
                'parent_name' => 'nullable|string',
                'parent_phone' => 'nullable|string',
                'parent_email' => 'nullable|email',

                // Teacher specific
                'qualification' => 'nullable|string',
                'specialization' => 'nullable|string',
                'employment_date' => 'nullable|date',

                // Class assignment (for students)
                'class_id' => 'nullable|exists:school_classes,id',
                'stream_id' => 'nullable|exists:streams,id',
                'academic_year' => 'nullable|string',
            ]);

            // Update user
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'is_active' => $request->is_active ?? $user->is_active,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                $path = $request->file('profile_image')->store('profile-images', 'public');
                $user->profile_image = $path;
                $user->save();
            }

            // Update profile
            $profileData = [
                'admission_year' => $request->admission_year,
                'student_type' => $request->student_type,
                'education_level' => $request->education_level,
                'admission_number' => $request->admission_number,
                'date_of_birth' => $request->date_of_birth,
                'phone' => $request->phone,
                'address' => $request->address,
                'parent_name' => $request->parent_name,
                'parent_phone' => $request->parent_phone,
                'parent_email' => $request->parent_email,
                'qualification' => $request->qualification,
                'specialization' => $request->specialization,
                'employment_date' => $request->employment_date,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'notes' => $request->notes,
            ];

            if ($user->profile) {
                $user->profile->update($profileData);
            } else {
                $profileData['user_id'] = $user->id;
                Profile::create($profileData);
            }

            // Update role
            $user->roles()->sync([$request->role_id]);

            // Handle class assignment for students
            if ($request->role_id == Role::where('name', 'student')->first()->id &&
                $request->class_id && $request->stream_id && $request->academic_year) {

                $classAssignment = $user->studentClassAssignments()->first();
                if ($classAssignment) {
                    $classAssignment->update([
                        'class_id' => $request->class_id,
                        'stream_id' => $request->stream_id,
                        'academic_year' => $request->academic_year,
                    ]);
                } else {
                    StudentClassAssignment::create([
                        'student_id' => $user->id,
                        'class_id' => $request->class_id,
                        'stream_id' => $request->stream_id,
                        'academic_year' => $request->academic_year,
                        'status' => 'active'
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating user: ' . $e->getMessage())->withInput();
        }
    }

   public function destroy(User $user)
{
    // Prevent users from deleting themselves
    if ($user->id === auth()->id()) {
        if (request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 422);
        }
        return back()->with('error', 'You cannot delete your own account.');
    }

    DB::beginTransaction();

    try {
        // Optional: Store user data for notification or logging
        $userName = $user->name;
        $userEmail = $user->email;

        // Delete the user
        $user->delete();

        DB::commit();

        // Optional: Add logging
        \Log::info("User deleted: {$userName} ({$userEmail}) by " . auth()->user()->name);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');

    } catch (\Exception $e) {
        DB::rollBack();

        // Log the error
        \Log::error('Error deleting user: ' . $e->getMessage(), [
            'user_id' => $user->id,
            'deleted_by' => auth()->id()
        ]);

        $errorMessage = 'Error deleting user. Please try again.';

        // Don't expose detailed errors to users in production
        if (app()->environment('local')) {
            $errorMessage = 'Error deleting user: ' . $e->getMessage();
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }

        return back()->with('error', $errorMessage);
    }
}
}
