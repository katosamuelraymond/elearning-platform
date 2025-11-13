<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;

class SchoolClass extends Model
{
    protected $fillable = [
        'name',
        'level',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the streams for the class.
     */
    public function streams(): HasMany
    {
        return $this->hasMany(Stream::class, 'class_id');
    }

    /**
     * Get the subjects for the class.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'class_id', 'subject_id')
            ->withPivot('periods_per_week')
            ->withTimestamps();
    }

    /**
     * Get the student assignments for the class.
     */
    public function studentAssignments(): HasMany
    {
        return $this->hasMany(StudentClassAssignment::class, 'class_id');
    }

    /**
     * Get the teacher assignments for the class.
     */
    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(TeacherAssignment::class, 'class_id');
    }

    /**
     * Get the students assigned to this class through class assignments.
     */
    public function students()
    {
        return $this->hasManyThrough(
            User::class,
            StudentClassAssignment::class,
            'class_id', // Foreign key on student_class_assignments table
            'id', // Foreign key on users table
            'id', // Local key on school_classes table
            'student_id' // Local key on student_class_assignments table
        )->where('student_class_assignments.status', 'active');
    }

    /**
     * Get the exams for this class.
     */
    public function exams()
    {
        return $this->hasMany(\App\Models\Assessment\Exam::class, 'class_id');
    }

    /**
     * Scope for O-Level classes.
     */
    public function scopeOLevel($query)
    {
        return $query->where('level', 'O-Level');
    }

    /**
     * Scope for A-Level classes.
     */
    public function scopeALevel($query)
    {
        return $query->where('level', 'A-Level');
    }

    /**
     * Scope for active classes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
