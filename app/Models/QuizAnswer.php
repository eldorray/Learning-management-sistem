<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAnswer extends Model
{
    protected $fillable = [
        'user_id', 'lesson_id', 'quiz_question_id', 'quiz_option_id',
        'essay_answer', 'is_correct', 'points_earned',
    ];

    protected $casts = ['is_correct' => 'boolean'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function lesson(): BelongsTo { return $this->belongsTo(Lesson::class); }
    public function question(): BelongsTo { return $this->belongsTo(QuizQuestion::class, 'quiz_question_id'); }
    public function option(): BelongsTo { return $this->belongsTo(QuizOption::class, 'quiz_option_id'); }
}
