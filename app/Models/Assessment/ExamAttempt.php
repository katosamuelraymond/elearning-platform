<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $table = 'exam_attempts';

    protected $fillable = [
        'exam_id',
        'student_id',
        'started_at',
        'submitted_at',
        'status', // e.g., in_progress, submitted, graded
        'score',
        'answers', // Stores student responses, typically JSON
        'time_spent', // in seconds
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'answers' => 'array', // Must be cast as array/json to store complex data
        'score' => 'decimal:2',
        'time_spent' => 'integer',
    ];

    /**
     * The exam this attempt belongs to.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * The student who made this attempt.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
