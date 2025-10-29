<?php

namespace App\Models\Assessment;

use App\Models\Academic\SchoolClass;
use App\Models\Academic\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'randomize_questions' => 'boolean',
        'require_fullscreen' => 'boolean',
        'show_results' => 'boolean',
        'is_published' => 'boolean',
    ];

    /**
     * An Exam belongs to a Teacher (User).
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * An Exam belongs to a School Class.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * An Exam belongs to a Subject.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * An Exam has many Questions through the exam_question pivot table.
     */
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'exam_question')
            ->withPivot('order', 'points')
            ->orderBy('pivot_order')
            ->withTimestamps();
    }

    /**
     * An Exam has many attempts.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }
}
