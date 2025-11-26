<?php

namespace App\Models\Teaching;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends Model
{
    protected $fillable = [
        'subject_id',
        'title',
        'description',
        'learning_objectives',
        'order',
        'duration_weeks',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_weeks' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the subject that owns the topic.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\Subject::class);
    }

    /**
     * Get the resources for the topic.
     */
    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    /**
     * Get the active resources for the topic.
     */
    public function activeResources(): HasMany
    {
        return $this->hasMany(Resource::class)->where('is_active', true);
    }

    /**
     * Scope for active topics
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for topics ordered by their order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('title');
    }

    /**
     * Scope for topics by subject
     */
    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * Get active resources count for the topic
     */
    public function getActiveResourcesCountAttribute(): int
    {
        return $this->activeResources()->count();
    }

    /**
     * Get estimated duration in hours
     */
    public function getDurationHoursAttribute(): int
    {
        // Assuming 5 hours per week
        return $this->duration_weeks * 5;
    }

    /**
     * Check if topic can be deleted
     */
    public function getCanDeleteAttribute(): bool
    {
        return $this->resources()->count() === 0;
    }

    /**
     * Get next topic in sequence
     */
    public function nextTopic()
    {
        return self::where('subject_id', $this->subject_id)
            ->where('order', '>', $this->order)
            ->active()
            ->ordered()
            ->first();
    }

    /**
     * Get previous topic in sequence
     */
    public function previousTopic()
    {
        return self::where('subject_id', $this->subject_id)
            ->where('order', '<', $this->order)
            ->active()
            ->ordered()
            ->first();
    }
}
