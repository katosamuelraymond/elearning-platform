<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'created_by',
        'type',
        'question_text',
        'explanation',
        'difficulty',
        'points',
        'is_active',
        'details',
        'correct_answer'
    ];

    protected $casts = [
        'details' => 'array',
        'points' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function subject(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\Subject::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_question')
            ->withPivot('order', 'points')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helpers
    public function isMcq(): bool
    {
        return $this->type === 'mcq';
    }

    public function getFormattedPointsAttribute()
    {
        return number_format($this->points, 2);
    }
}
