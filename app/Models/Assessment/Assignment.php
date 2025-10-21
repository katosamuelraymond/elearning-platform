<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Assignment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'title',
        'instructions',
        'description',
        'due_date',
        'max_points',
        'allowed_formats',
        'max_file_size',
        'assignment_file',
        'original_filename',
        'file_size',
        'is_published',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'datetime',
        'allowed_formats' => 'array',
        'is_published' => 'boolean',
        'max_points' => 'integer',
        'max_file_size' => 'integer',
        'file_size' => 'integer', // ADDED: Cast file_size to integer
    ];

    /**
     * Get the teacher that created the assignment.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'teacher_id');
    }

    /**
     * Get the class that the assignment belongs to.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\SchoolClass::class, 'class_id');
    }

    /**
     * Get the subject that the assignment belongs to.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\Subject::class, 'subject_id');
    }

    /**
     * Get the submissions for the assignment.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(\App\Models\Assessment\AssignmentSubmission::class);
    }

    /**
     * Scope a query to only include published assignments.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope a query to only include draft assignments.
     */
    public function scopeDraft($query)
    {
        return $query->where('is_published', false);
    }

    /**
     * Scope a query to only include upcoming assignments.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('due_date', '>', now());
    }

    /**
     * Scope a query to only include overdue assignments.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now());
    }

    /**
     * Check if the assignment is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date->isPast();
    }

    /**
     * Check if the assignment is upcoming.
     */
    public function getIsUpcomingAttribute(): bool
    {
        return $this->due_date->isFuture();
    }

    /**
     * Get the submission count for the assignment.
     */
    public function getSubmissionCountAttribute(): int
    {
        return $this->submissions()->count();
    }

    /**
     * Get the formatted due date.
     */
    public function getFormattedDueDateAttribute(): string
    {
        return $this->due_date->format('F j, Y \a\t g:i A');
    }

    /**
     * Get the remaining time until due date.
     */
    public function getRemainingTimeAttribute(): string
    {
        return $this->due_date->diffForHumans();
    }

    // ==================== FILE HANDLING METHODS ====================

    /**
     * Check if assignment has a file attached.
     */
    public function getHasFileAttribute(): bool
    {
        return !empty($this->assignment_file);
    }

    /**
     * Get the formatted file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return '0 KB';
        }

        if ($this->file_size >= 1048576) {
            return number_format($this->file_size / 1048576, 2) . ' MB';
        } elseif ($this->file_size >= 1024) {
            return number_format($this->file_size / 1024, 2) . ' KB';
        } else {
            return $this->file_size . ' bytes';
        }
    }

    /**
     * Get the file extension.
     */
    public function getFileExtensionAttribute(): string
    {
        if (!$this->assignment_file) {
            return '';
        }

        return pathinfo($this->assignment_file, PATHINFO_EXTENSION);
    }

    /**
     * Get the file icon based on extension.
     */
    public function getFileIconAttribute(): string
    {
        $extension = $this->file_extension;

        $icons = [
            'pdf' => 'fas fa-file-pdf',
            'doc' => 'fas fa-file-word',
            'docx' => 'fas fa-file-word',
            'txt' => 'fas fa-file-alt',
            'jpg' => 'fas fa-file-image',
            'jpeg' => 'fas fa-file-image',
            'png' => 'fas fa-file-image',
            'ppt' => 'fas fa-file-powerpoint',
            'pptx' => 'fas fa-file-powerpoint',
            'xls' => 'fas fa-file-excel',
            'xlsx' => 'fas fa-file-excel',
            'zip' => 'fas fa-file-archive',
            'rar' => 'fas fa-file-archive',
        ];

        return $icons[$extension] ?? 'fas fa-file';
    }

    /**
     * Get the file icon color based on extension.
     */
    public function getFileIconColorAttribute(): string
    {
        $extension = $this->file_extension;

        $colors = [
            'pdf' => 'text-red-500',
            'doc' => 'text-blue-500',
            'docx' => 'text-blue-500',
            'txt' => 'text-gray-500',
            'jpg' => 'text-green-500',
            'jpeg' => 'text-green-500',
            'png' => 'text-green-500',
            'ppt' => 'text-orange-500',
            'pptx' => 'text-orange-500',
            'xls' => 'text-green-600',
            'xlsx' => 'text-green-600',
            'zip' => 'text-yellow-500',
            'rar' => 'text-yellow-500',
        ];

        return $colors[$extension] ?? 'text-gray-500';
    }

    /**
     * Check if the assignment file exists in storage.
     */
    public function getFileExistsAttribute(): bool
    {
        return $this->assignment_file && Storage::disk('public')->exists($this->assignment_file);
    }

    // ==================== SUBMISSION METHODS ====================

    /**
     * Get the submission for a specific student.
     */
    public function getSubmissionForStudent($studentId)
    {
        return $this->submissions()->where('student_id', $studentId)->first();
    }

    /**
     * Check if a specific student has submitted.
     */
    public function hasStudentSubmitted($studentId): bool
    {
        return $this->submissions()->where('student_id', $studentId)->exists();
    }

    /**
     * Get the graded submissions count.
     */
    public function getGradedSubmissionsCountAttribute(): int
    {
        return $this->submissions()->where('status', 'graded')->count();
    }

    /**
     * Get the pending submissions count.
     */
    public function getPendingSubmissionsCountAttribute(): int
    {
        return $this->submissions()->whereIn('status', ['submitted', 'late'])->count();
    }

    /**
     * Get the late submissions count.
     */
    public function getLateSubmissionsCountAttribute(): int
    {
        return $this->submissions()->where('status', 'late')->count();
    }

    /**
     * Get the missing submissions count.
     */
    public function getMissingSubmissionsCountAttribute(): int
    {
        return $this->submissions()->where('status', 'missing')->count();
    }

    /**
     * Get the average grade for the assignment.
     */
    public function getAverageGradeAttribute(): ?float
    {
        $gradedSubmissions = $this->submissions()->where('status', 'graded')->get();

        if ($gradedSubmissions->isEmpty()) {
            return null;
        }

        $totalPoints = $gradedSubmissions->sum('points_obtained');
        $average = $totalPoints / $gradedSubmissions->count();

        return round($average, 2);
    }

    /**
     * Get the average grade percentage.
     */
    public function getAverageGradePercentageAttribute(): ?float
    {
        $averageGrade = $this->average_grade;

        if ($averageGrade === null) {
            return null;
        }

        return round(($averageGrade / $this->max_points) * 100, 2);
    }

    /**
     * Get the submission rate percentage.
     */
    public function getSubmissionRateAttribute(): float
    {
        // You'll need to implement this based on your student count logic
        $totalStudents = 0; // Replace with actual student count logic
        if ($totalStudents === 0) {
            return 0;
        }

        $submittedCount = $this->submissions()->count();
        return round(($submittedCount / $totalStudents) * 100, 2);
    }

    // ==================== VALIDATION METHODS ====================

    /**
     * Check if a file format is allowed for this assignment.
     */
    public function isFormatAllowed($extension): bool
    {
        return in_array(strtolower($extension), $this->allowed_formats ?? []);
    }

    /**
     * Get the allowed formats as a string for display.
     */
    public function getAllowedFormatsStringAttribute(): string
    {
        if (empty($this->allowed_formats)) {
            return 'No formats specified';
        }

        return implode(', ', array_map('strtoupper', $this->allowed_formats));
    }

    /**
     * Get the allowed formats for HTML accept attribute.
     */
    public function getAllowedFormatsAcceptAttribute(): string
    {
        if (empty($this->allowed_formats)) {
            return '';
        }

        $mimeTypes = [
            'pdf' => '.pdf',
            'doc' => '.doc',
            'docx' => '.docx',
            'txt' => '.txt',
            'jpg' => '.jpg,.jpeg',
            'jpeg' => '.jpg,.jpeg',
            'png' => '.png',
            'ppt' => '.ppt',
            'pptx' => '.pptx',
            'xls' => '.xls',
            'xlsx' => '.xlsx',
            'zip' => '.zip',
            'rar' => '.rar',
        ];

        $accept = [];
        foreach ($this->allowed_formats as $format) {
            if (isset($mimeTypes[$format])) {
                $accept[] = $mimeTypes[$format];
            }
        }

        return implode(',', $accept);
    }

    // ==================== MODEL EVENTS ====================

    /**
     * Boot method for model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Delete associated file when assignment is deleted
        static::deleting(function ($assignment) {
            if ($assignment->assignment_file && Storage::disk('public')->exists($assignment->assignment_file)) {
                Storage::disk('public')->delete($assignment->assignment_file);
            }

            // Also delete all submission files
            foreach ($assignment->submissions as $submission) {
                if ($submission->submission_file && Storage::disk('public')->exists($submission->submission_file)) {
                    Storage::disk('public')->delete($submission->submission_file);
                }
            }
        });
    }
}
