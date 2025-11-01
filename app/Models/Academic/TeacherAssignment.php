<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Stream;
use App\Models\Academic\Subject;

class TeacherAssignment extends Model
{
    protected $fillable = [
        'teacher_id',
        'subject_id',
        'class_id',
        'stream_id',
        'academic_year',
        'role',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the teacher that owns the assignment.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the subject that owns the assignment.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Get the class that owns the assignment.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get the stream that owns the assignment.
     */
    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class, 'stream_id');
    }

    /**
     * Scope for active assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific academic year.
     */
    public function scopeForAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }
}
