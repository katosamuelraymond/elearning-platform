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
        'is_published',
        'is_archived'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'randomize_questions' => 'boolean',
        'require_fullscreen' => 'boolean',
        'show_results' => 'boolean',
        'is_published' => 'boolean',
        'is_archived' => 'boolean',
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
    // Scopes
    public function scopeAvailableForStudent($query, $student)
    {
        return $query->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->whereDoesntHave('attempts', function($q) use ($student) {
                $q->where('student_id', $student->id)
                    ->whereIn('status', ['submitted', 'graded']);
            });
    }

    public function scopeInProgressByStudent($query, $student)
    {
        return $query->whereHas('attempts', function($q) use ($student) {
            $q->where('student_id', $student->id)
                ->where('status', 'in_progress');
        });
    }

    public function scopeCompletedByStudent($query, $student)
    {
        return $query->whereHas('attempts', function($q) use ($student) {
            $q->where('student_id', $student->id)
                ->whereIn('status', ['submitted', 'graded']);
        });
    }

// Helper methods
    public function getStatusForStudent($student)
    {
        if ($this->attempts()->where('student_id', $student->id)->where('status', 'in_progress')->exists()) {
            return 'in_progress';
        }

        if ($this->attempts()->where('student_id', $student->id)->whereIn('status', ['submitted', 'graded'])->exists()) {
            return 'completed';
        }

        if (now()->between($this->start_time, $this->end_time)) {
            return 'available';
        }

        if (now()->lt($this->start_time)) {
            return 'upcoming';
        }

        return 'missed';
    }

    public function canStudentAttempt($student)
    {
        // Check if exam is published and not archived
        if (!$this->is_published || $this->is_archived) {
            return false;
        }

        // Check if within exam time window
        if (!now()->between($this->start_time, $this->end_time)) {
            return false;
        }

        // Check attempt limits
        $attemptCount = $this->attempts()->where('student_id', $student->id)->count();
        if ($attemptCount >= $this->max_attempts) {
            return false;
        }

        return true;
    }


    public function getStatusTextForStudent($student)
    {
        $status = $this->getStatusForStudent($student);

        $statusTexts = [
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'available' => 'Available',
            'upcoming' => 'Upcoming',
            'missed' => 'Missed'
        ];

        return $statusTexts[$status] ?? 'Unknown';
    }

    public function getStatusBadgeClassForStudent($student)
    {
        $status = $this->getStatusForStudent($student);

        $badgeClasses = [
            'in_progress' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200',
            'completed' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
            'available' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
            'upcoming' => 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200',
            'missed' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200'
        ];

        return $badgeClasses[$status] ?? 'bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200';
    }

    public function isAssignedToStudent($student)
    {
        return $this->class_id === $student->class_id && $this->is_published && !$this->is_archived;
    }

    public function isNextAttemptAvailable($student)
    {
        $attemptCount = $this->attempts()->where('student_id', $student->id)->count();
        return $attemptCount < $this->max_attempts;
    }

    public function canAutoGrade()
    {
        // Check if all questions are auto-gradable
        return $this->questions->every(function($question) {
            return in_array($question->type, ['mcq', 'true_false', 'fill_blank']);
        });
    }

    // Add this method to handle question randomization with seed
    public function getQuestionsForAttempt($attempt)
    {
        $questions = $this->questions()->with('options')->get();

        if ($this->randomize_questions) {
            // Use attempt ID as seed for consistent randomization per attempt
            $questions = $questions->shuffle($attempt->id);
        }

        return $questions;
    }
}
