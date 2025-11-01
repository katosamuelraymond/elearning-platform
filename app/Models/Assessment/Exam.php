<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'title',
        'instructions',
        'description',
        'type',
        'duration',
        'total_marks',
        'passing_marks',
        'start_time',
        'end_time',
        'max_attempts',
        'randomize_questions',
        'require_fullscreen',
        'show_results',
        'is_published'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'randomize_questions' => 'boolean',
        'require_fullscreen' => 'boolean',
        'show_results' => 'boolean',
        'is_published' => 'boolean',
        'duration' => 'integer',
        'total_marks' => 'integer',
        'passing_marks' => 'integer',
        'max_attempts' => 'integer'
    ];

    // Relationships
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'teacher_id');
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\SchoolClass::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\Subject::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'exam_question')
            ->withPivot('order', 'points')
            ->orderBy('exam_question.order')
            ->withTimestamps();
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now());
    }

    public function scopeActive($query)
    {
        return $query->where('start_time', '<=', now())
            ->where('end_time', '>=', now());
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->start_time <= now() && $this->end_time >= now();
    }

    public function isUpcoming(): bool
    {
        return $this->start_time > now();
    }

    public function isPast(): bool
    {
        return $this->end_time < now();
    }

    public function calculateTotalMarks(): float
    {
        return $this->questions->sum('pivot.points');
    }

    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }
}
