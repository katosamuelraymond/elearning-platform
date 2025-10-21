<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'student_id',
        'teacher_id',
        'employee_id',
        'admission_year',
        'student_type',
        'education_level',
        'admission_number',
        'date_of_birth',
        'phone',
        'address',
        'parent_name',
        'parent_phone',
        'parent_email',
        'qualification',
        'specialization',
        'employment_date',
        'emergency_contact',
        'emergency_phone',
        'notes'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'employment_date' => 'date'
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if profile belongs to a student.
     */
    public function isStudent(): bool
    {
        return !is_null($this->student_id);
    }

    /**
     * Check if profile belongs to a teacher.
     */
    public function isTeacher(): bool
    {
        return !is_null($this->teacher_id);
    }

    /**
     * Get formatted education level.
     */
    public function getFormattedEducationLevelAttribute(): string
    {
        return $this->education_level === 'O' ? 'O-Level' : 'A-Level';
    }

    /**
     * Get formatted student type.
     */
    public function getFormattedStudentTypeAttribute(): string
    {
        return $this->student_type === 'U' ? 'Ugandan' : 'Foreign';
    }

    /**
     * Get age from date of birth.
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }

        return Carbon::parse($this->date_of_birth)->age;
    }

    /**
     * Scope for student profiles.
     */
    public function scopeStudents($query)
    {
        return $query->whereNotNull('student_id');
    }

    /**
     * Scope for teacher profiles.
     */
    public function scopeTeachers($query)
    {
        return $query->whereNotNull('teacher_id');
    }

    /**
     * Scope for O-Level students.
     */
    public function scopeOLevel($query)
    {
        return $query->where('education_level', 'O');
    }

    /**
     * Scope for A-Level students.
     */
    public function scopeALevel($query)
    {
        return $query->where('education_level', 'A');
    }

    /**
     * Get full emergency contact information.
     */
    public function getEmergencyContactInfoAttribute(): string
    {
        if ($this->emergency_contact && $this->emergency_phone) {
            return "{$this->emergency_contact} - {$this->emergency_phone}";
        }

        return $this->emergency_contact ?? $this->emergency_phone ?? 'Not set';
    }
}
