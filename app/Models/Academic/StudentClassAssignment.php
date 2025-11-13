<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentClassAssignment extends Model
{
    protected $fillable = [
        'student_id',
        'class_id',
        'stream_id',
        'academic_year',
        'optional_subjects',
        'status',
        'combination_id'
    ];

    protected $casts = [
        'optional_subjects' => 'array'
    ];

    /**
     * Get the student for this assignment.
     */
    public function student()
    {
        return $this->belongsTo(\App\Models\User::class, 'student_id');
    }

    /**
     * Get the class for this assignment.
     */
    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get the stream for this assignment.
     */
    public function stream()
    {
        return $this->belongsTo(Stream::class, 'stream_id');
    }

    /**
     * Get the combination for this assignment.
     */
    public function combination()
    {
        return $this->belongsTo(Combination::class, 'combination_id');
    }

    /**
     * Scope for active assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for assignments by academic year.
     */
    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }
}
