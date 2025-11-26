<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'title',
        'instructions',
        'type',
        'duration',
        'total_marks',
        'start_time',
        'end_time',
        'randomize_questions',
        'show_answers',
        'is_published',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'randomize_questions' => 'boolean',
        'show_answers' => 'boolean',
        'is_published' => 'boolean',
    ];

    /**
     * Get the teacher that owns the quiz.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'teacher_id');
    }

    /**
     * Get the class that owns the quiz.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\SchoolClass::class, 'class_id');
    }

    /**
     * Get the subject that owns the quiz.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\Subject::class);
    }

    /**
     * Get the questions for the quiz.
     */
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'quiz_question')
            ->withPivot('order', 'points')
            ->withTimestamps()
            ->orderBy('pivot_order');
    }

    /**
     * Get the attempts for the quiz.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Check if quiz is active
     */
    public function isActive(): bool
    {
        $now = now();
        return $this->is_published &&
            $this->start_time <= $now &&
            $this->end_time >= $now;
    }

    /**
     * Check if quiz is upcoming
     */
    public function isUpcoming(): bool
    {
        return $this->is_published && $this->start_time > now();
    }

    /**
     * Check if quiz is completed
     */
    public function isCompleted(): bool
    {
        return $this->end_time < now();
    }
}
