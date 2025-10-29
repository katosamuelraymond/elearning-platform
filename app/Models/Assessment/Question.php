<?php

namespace App\Models\Assessment;

use App\Models\Academic\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        // New fields to support controller logic and updated schema
        'details',
        'correct_answer',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'points' => 'float',
        'details' => 'array', // Crucial: Casts the JSON 'details' column to a PHP array/object
    ];

    /**
     * A Question belongs to a Subject.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * A Question was created by a User (Teacher/Admin).
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * A Question can have many options (for MCQ).
     */
    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order');
    }
}
