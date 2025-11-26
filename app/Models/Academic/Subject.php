<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the topics for the subject.
     */
    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }

    /**
     * Get the active topics for the subject.
     */
    public function activeTopics(): HasMany
    {
        return $this->hasMany(Topic::class)->where('is_active', true);
    }

    /**
     * Get the classes that study this subject.
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subject')
            ->withTimestamps();
    }

    /**
     * Get the resources for this subject.
     */
    public function resources(): HasMany
    {
        return $this->hasThrough(Resource::class, Topic::class);
    }

    /**
     * Scope for active subjects
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for compulsory subjects
     */
    public function scopeCompulsory($query)
    {
        return $query->where('type', 'compulsory');
    }

    /**
     * Scope for optional subjects
     */
    public function scopeOptional($query)
    {
        return $query->where('type', 'optional');
    }

    /**
     * Get subject with its active topics count
     */
    public function getActiveTopicsCountAttribute(): int
    {
        return $this->activeTopics()->count();
    }

    /**
     * Check if subject can be deleted
     */
    public function getCanDeleteAttribute(): bool
    {
        return $this->topics()->count() === 0 &&
            $this->classes()->count() === 0;
    }
}
