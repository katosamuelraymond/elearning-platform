<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
        'order',
    ];

    protected $casts = [
        'is_correct' => 'boolean', // Ensures the database 0/1 is treated as true/false
    ];

    /**
     * A Question Option belongs to a Question.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
