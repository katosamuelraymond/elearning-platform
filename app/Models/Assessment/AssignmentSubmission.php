<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'assignment_id',
        'student_id',
        'submission_file',
        'original_filename',
        'submission_notes',
        'submitted_at',
        'status',
        'points_obtained',
        'feedback',
        'graded_by',
        'graded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'points_obtained' => 'integer',
    ];

    /**
     * Get the assignment that the submission belongs to.
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Get the student that made the submission.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'student_id');
    }

    /**
     * Get the teacher who graded the submission.
     */
    public function grader(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'graded_by');
    }

    /**
     * Scope a query to only include submitted assignments.
     */
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    /**
     * Scope a query to only include graded assignments.
     */
    public function scopeGraded($query)
    {
        return $query->where('status', 'graded');
    }

    /**
     * Scope a query to only include late submissions.
     */
    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    /**
     * Check if the submission is graded.
     */
    public function getIsGradedAttribute(): bool
    {
        return $this->status === 'graded';
    }

    /**
     * Check if the submission is late.
     */
    public function getIsLateAttribute(): bool
    {
        return $this->status === 'late';
    }

    /**
     * Get the grade percentage.
     */
    public function getGradePercentageAttribute(): ?float
    {
        if (!$this->points_obtained || !$this->assignment) {
            return null;
        }

        return ($this->points_obtained / $this->assignment->max_points) * 100;
    }

    /**
     * Get the formatted grade.
     */
    public function getFormattedGradeAttribute(): string
    {
        if (!$this->points_obtained || !$this->assignment) {
            return 'Not graded';
        }

        return "{$this->points_obtained}/{$this->assignment->max_points}";
    }
}
