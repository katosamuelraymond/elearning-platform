<?php

namespace App\Models\Teaching;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resource extends Model
{
    protected $fillable = [
        'topic_id',
        'uploaded_by',
        'class_id',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'access_level',
        'is_active',
    ];

    protected $casts = [
        'file_size' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the topic that owns the resource.
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get the subject through topic
     */
    public function subject()
    {
        return $this->hasOneThrough(
            \App\Models\Academic\Subject::class,
            Topic::class,
            'id', // Foreign key on topics table
            'id', // Foreign key on subjects table
            'topic_id', // Local key on resources table
            'subject_id' // Local key on topics table
        );
    }

    /**
     * Get the user who uploaded the resource.
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }

    /**
     * Get the class that can access this resource (for class_only access)
     */
    public function accessibleClass(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\SchoolClass::class, 'class_id');
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if ($this->file_size >= 1024) {
            return number_format($this->file_size / 1024, 2) . ' GB';
        }
        return number_format($this->file_size, 2) . ' MB';
    }

    /**
     * Get file icon based on file type
     */
    public function getFileIconAttribute(): string
    {
        return match($this->file_type) {
            'pdf' => 'fas fa-file-pdf text-red-500',
            'doc', 'docx' => 'fas fa-file-word text-blue-500',
            'ppt', 'pptx' => 'fas fa-file-powerpoint text-orange-500',
            'xls', 'xlsx' => 'fas fa-file-excel text-green-500',
            'mp4', 'avi', 'mov' => 'fas fa-file-video text-purple-500',
            'mp3', 'wav' => 'fas fa-file-audio text-yellow-500',
            'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image text-pink-500',
            'zip', 'rar' => 'fas fa-file-archive text-gray-500',
            default => 'fas fa-file text-gray-400'
        };
    }

    /**
     * Get file type category
     */
    public function getFileCategoryAttribute(): string
    {
        return match($this->file_type) {
            'pdf' => 'Document',
            'doc', 'docx' => 'Document',
            'ppt', 'pptx' => 'Presentation',
            'xls', 'xlsx' => 'Spreadsheet',
            'mp4', 'avi', 'mov' => 'Video',
            'mp3', 'wav' => 'Audio',
            'jpg', 'jpeg', 'png', 'gif' => 'Image',
            'zip', 'rar' => 'Archive',
            default => 'File'
        };
    }

    /**
     * Check if resource is accessible by user
     */
    public function isAccessibleBy($user): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return match($this->access_level) {
            'public' => true,
            'class_only' => $user->currentClassAssignment && $user->currentClassAssignment->class_id == $this->class_id,
            'teacher_only' => $user->isTeacher() || $user->isAdmin(),
            default => false
        };
    }

    /**
     * Scope for active resources
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for resources by file type
     */
    public function scopeByFileType($query, $fileType)
    {
        return $query->where('file_type', $fileType);
    }

    /**
     * Scope for resources by access level
     */
    public function scopeByAccessLevel($query, $accessLevel)
    {
        return $query->where('access_level', $accessLevel);
    }

    /**
     * Scope for resources accessible by class
     */
    public function scopeAccessibleByClass($query, $classId)
    {
        return $query->where(function($q) use ($classId) {
            $q->where('access_level', 'public')
                ->orWhere(function($q2) use ($classId) {
                    $q2->where('access_level', 'class_only')
                        ->where('class_id', $classId);
                });
        });
    }

    /**
     * Scope for resources by subject
     */
    public function scopeBySubject($query, $subjectId)
    {
        return $query->whereHas('topic', function($q) use ($subjectId) {
            $q->where('subject_id', $subjectId);
        });
    }

    /**
     * Scope for resources by topic
     */
    public function scopeByTopic($query, $topicId)
    {
        return $query->where('topic_id', $topicId);
    }
}
