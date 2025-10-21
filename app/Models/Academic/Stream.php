<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\TeacherAssignment;
use App\Models\StudentClassAssignment;

class Stream extends Model
{
    protected $fillable = [
        'class_id',
        'name',
        'description',
        'capacity',
        'is_active'
    ];

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * Get the class that owns the stream.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get the student assignments for the stream.
     */
    public function studentAssignments()
    {
        return $this->hasMany(StudentClassAssignment::class, 'stream_id');
    }

    /**
     * Get the teacher assignments for the stream.
     */
    public function teacherAssignments()
    {
        return $this->hasMany(TeacherAssignment::class, 'stream_id');
    }
}
