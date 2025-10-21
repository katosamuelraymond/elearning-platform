<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Stream;
use App\Models\Academic\Subject;
use App\Models\User;

class TeacherAssignment extends Model
{
    protected $fillable = [
        'teacher_id',
        'class_id',
        'stream_id',
        'subject_id',
        'academic_year',
        'role',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the teacher for the assignment.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the class for the assignment.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get the stream for the assignment.
     */
    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class, 'stream_id');
    }

    /**
     * Get the subject for the assignment.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Scope for class teacher assignments.
     */
    public function scopeClassTeachers($query)
    {
        return $query->where('role', 'class_teacher');
    }

    /**
     * Scope for subject teacher assignments.
     */
    public function scopeSubjectTeachers($query)
    {
        return $query->where('role', 'subject_teacher');
    }

    /**
     * Scope for head teacher assignments.
     */
    public function scopeHeadTeachers($query)
    {
        return $query->where('role', 'head_teacher');
    }

    /**
     * Scope for active assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for assignments in a specific academic year.
     */
    public function scopeForAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }
}
