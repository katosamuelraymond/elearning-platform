<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $fillable = [
        'year',
        'start_date',
        'end_date',
        'is_current'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean'
    ];

    /**
     * Get the terms for the academic year.
     */
    public function terms(): HasMany
    {
        return $this->hasMany(Term::class, 'academic_year_id');
    }

    /**
     * Scope for current academic year.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }
}
