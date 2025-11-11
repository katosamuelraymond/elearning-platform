<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the classes for the subject.
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subject', 'subject_id', 'class_id')
                    ->withPivot('periods_per_week')
                    ->withTimestamps();
    }

    public function exams()
    {
        return $this->hasMany(\App\Models\Assessment\Exam::class);
    }

    /**
     * Scope for compulsory subjects.
     */
    public function scopeCompulsory($query)
    {
        return $query->where('type', 'compulsory');
    }

    /**
     * Scope for optional subjects.
     */
    public function scopeOptional($query)
    {
        return $query->where('type', 'optional');
    }

    /**
     * Scope for elective subjects.
     */
    public function scopeElective($query)
    {
        return $query->where('type', 'elective');
    }

    /**
     * Scope for active subjects.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
