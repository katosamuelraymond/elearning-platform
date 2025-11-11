<?php

namespace App\Models;
use App\Models\Academic\StudentClassAssignment;

use App\Models\Academic\Subject;
use App\Models\Academic\TeacherAssignment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
        'is_active',
        'created_by'
        // REMOVED: 'role' - we use roles relationship instead
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get the user's profile.
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get the roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Get the user who created this user.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ========== ROLE METHODS ==========

    public function hasRole($roleName)
    {
        return $this->roles->contains('name', $roleName);
    }

    public function hasAnyRole(array $roles)
    {
        return $this->roles->whereIn('name', $roles)->isNotEmpty();
    }

    public function getPrimaryRoleAttribute()
    {
        return $this->roles->first()->name ?? null;
    }

    // UPDATED: Use only roles relationship, no role column
    public function isStudent()
    {
        return $this->hasRole('student');
    }

    public function isTeacher()
    {
        return $this->hasRole('teacher');
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    // ========== PROFILE DATA ACCESSORS ==========

    public function getStudentIdAttribute()
    {
        return $this->profile->student_id ?? null;
    }

    public function getDateOfBirthAttribute()
    {
        return $this->profile->date_of_birth ?? null;
    }

    public function getAddressAttribute()
    {
        return $this->profile->address ?? null;
    }

    public function getAdmissionYearAttribute()
    {
        return $this->profile->admission_year ?? null;
    }

    public function getStudentTypeAttribute()
    {
        return $this->profile->student_type ?? null;
    }

    public function getEducationLevelAttribute()
    {
        return $this->profile->education_level ?? null;
    }

    public function getAdmissionNumberAttribute()
    {
        return $this->profile->admission_number ?? null;
    }

    public function getParentNameAttribute()
    {
        return $this->profile->parent_name ?? null;
    }

    public function getParentPhoneAttribute()
    {
        return $this->profile->parent_phone ?? null;
    }

    public function getParentEmailAttribute()
    {
        return $this->profile->parent_email ?? null;
    }

    public function getTeacherIdAttribute()
    {
        return $this->profile->teacher_id ?? null;
    }

    public function getQualificationAttribute()
    {
        return $this->profile->qualification ?? null;
    }

    public function getSpecializationAttribute()
    {
        return $this->profile->specialization ?? null;
    }

    public function getEmploymentDateAttribute()
    {
        return $this->profile->employment_date ?? null;
    }

    public function getPhoneAttribute()
    {
        return $this->profile->phone ?? null;
    }

    public function getEmergencyContactAttribute()
    {
        return $this->profile->emergency_contact ?? null;
    }

    public function getEmergencyPhoneAttribute()
    {
        return $this->profile->emergency_phone ?? null;
    }

    public function getNotesAttribute()
    {
        return $this->profile->notes ?? null;
    }

    // ========== SCOPES ==========

    // UPDATED: Use roles relationship instead of role column
    public function scopeStudents($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'student');
        });
    }

    public function scopeTeachers($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'teacher');
        });
    }

    public function scopeAdmins($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'admin');
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOLevel($query)
    {
        return $query->whereHas('profile', function ($q) {
            $q->where('education_level', 'O');
        });
    }

    public function scopeALevel($query)
    {
        return $query->whereHas('profile', function ($q) {
            $q->where('education_level', 'A');
        });
    }

    public function scopeByAdmissionYear($query, $year)
    {
        return $query->whereHas('profile', function ($q) use ($year) {
            $q->where('admission_year', $year);
        });
    }

    // ========== BUSINESS LOGIC ==========

    public function isOLevel()
    {
        return $this->education_level === 'O';
    }

    public function isALevel()
    {
        return $this->education_level === 'A';
    }


/**
 * Get the primary role name for route generation
 */
public function getRouteRoleAttribute()
{
    $primaryRole = $this->primaryRole;

    // Map role names to route prefixes if needed
    $roleMap = [
        'admin' => 'admin',
        'teacher' => 'teacher',
        'student' => 'student'
    ];

    return $roleMap[$primaryRole] ?? $primaryRole;
}
  /**
 * Generate student ID in format: 23/A/0001/F
 */
public function generateStudentId()
{
    // Make sure user is a student and has a profile
    if (!method_exists($this, 'isStudent') || !$this->isStudent() || !$this->profile) {
        return null;
    }

    // Safely get data from profile
    $admissionYear = $this->profile->admission_year
        ? substr($this->profile->admission_year, -2)
        : '00';

    $educationLevel = $this->profile->education_level ?? 'O'; // default to 'O' level
    $admissionNumber = $this->profile->admission_number ?? '0';

    // Ensure it's always 4 digits
    $admissionNumber = str_pad($admissionNumber, 4, '0', STR_PAD_LEFT);

    $gender = $this->profile->gender ?? 'M';

    return sprintf('%s/%s/%s/%s',
        $admissionYear,
        strtoupper($educationLevel),
        $admissionNumber,
        strtoupper($gender)
    );
}


/**
 * Generate school email from student ID: 23/A/0001/F@lhs.ac.ug
 */
public function generateSchoolEmail()
{
    if (!$this->isStudent()) return null;

    $studentId = $this->generateStudentId();
    return $studentId ? $studentId . '@lhs.ac.ug' : null;
}

/**
 * Generate admission number with 4-digit format
 */
/**
 * Generate admission number with 4-digit format
 */
public static function generateAdmissionNumber($admissionYear, $studentType, $educationLevel)
{
    $lastStudent = static::whereHas('profile', function ($query) use ($admissionYear, $studentType, $educationLevel) {
        $query->where('admission_year', $admissionYear)
              ->where('student_type', $studentType)
              ->where('education_level', $educationLevel);
    })->with('profile') // Add this to eager load the profile
      ->get()
      ->sortByDesc(function ($user) {
          return (int) $user->profile->admission_number; // Access through the relationship
      })
      ->first();

    $lastNumber = $lastStudent ? (int)$lastStudent->profile->admission_number : 0;
    return str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
}
    public static function createALevelStudent(User $oLevelStudent, $newAdmissionYear)
    {
        $newAdmissionNumber = static::generateAdmissionNumber($newAdmissionYear, $oLevelStudent->student_type, 'A');

        $newStudent = static::create([
            'name' => $oLevelStudent->name,
            'email' => null,
            'password' => $oLevelStudent->password,
            'profile_image' => $oLevelStudent->profile_image,
            'is_active' => true,
            'created_by' => auth()->id() ?? $oLevelStudent->created_by,
        ]);

        // Attach student role
        $newStudent->roles()->attach(Role::where('name', 'student')->first()->id);

        Profile::create([
            'user_id' => $newStudent->id,
            'admission_year' => $newAdmissionYear,
            'student_type' => $oLevelStudent->student_type,
            'education_level' => 'A',
            'admission_number' => $newAdmissionNumber,
            'date_of_birth' => $oLevelStudent->date_of_birth,
            'address' => $oLevelStudent->address,
            'parent_name' => $oLevelStudent->parent_name,
            'parent_phone' => $oLevelStudent->parent_phone,
            'parent_email' => $oLevelStudent->parent_email,
            'phone' => $oLevelStudent->phone,
            'emergency_contact' => $oLevelStudent->emergency_contact,
            'emergency_phone' => $oLevelStudent->emergency_phone,
            'notes' => "Promoted from O-Level: {$oLevelStudent->student_id}"
        ]);

        return $newStudent;
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            if ($user->isStudent() && $user->profile) {
                $user->profile->update([
                    'student_id' => $user->generateStudentId()
                ]);
            }
        });
    }

    public function getDisplayIdAttribute()
    {
        return $this->student_id ?? $this->teacher_id ?? $this->id;
    }

    public function getFormattedLevelAttribute()
    {
        if (!$this->isStudent()) return null;
        return $this->education_level === 'O' ? 'O-Level' : 'A-Level';
    }
    // In User model, add these relationships:

/**
 * Get the teacher assignments for the user.
 */
public function teacherAssignments()
{
    return $this->hasMany(TeacherAssignment::class, 'teacher_id');
}

/**
 * Get the student class assignments for the user.
 */
public function studentClassAssignments()
{
    return $this->hasMany(StudentClassAssignment::class, 'student_id');
}

/**
 * Get the classes taught by the teacher.
 */
public function taughtClasses()
{
    return $this->hasManyThrough(
        Academic\SchoolClass::class,
        TeacherAssignment::class,
        'teacher_id',
        'id',
        'id',
        'class_id'
    )->distinct();
}

/**
 * Get the subjects taught by the teacher.
 */
public function taughtSubjects()
{
    return $this->hasManyThrough(
        Academic\Subject::class,
        TeacherAssignment::class,
        'teacher_id',
        'id',
        'id',
        'subject_id'
    )->distinct();
}

    /**
     * Get the subjects taught by the teacher through assignments
     */
    public function subjects()
    {
        return $this->hasManyThrough(
            Subject::class,
            TeacherAssignment::class,
            'teacher_id', // Foreign key on TeacherAssignment table
            'id', // Foreign key on Subject table
            'id', // Local key on User table
            'subject_id' // Local key on TeacherAssignment table
        )->distinct();
    }

    public function class()
    {
        return $this->belongsTo(\App\Models\Academic\SchoolClass::class, 'class_id');
    }


}
