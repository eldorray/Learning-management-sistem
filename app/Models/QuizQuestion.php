<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    protected $fillable = [
        'lesson_id', 'question', 'type', 'explanation', 'points', 'order',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class)->orderBy('order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }

    public function getCorrectOptionAttribute(): ?QuizOption
    {
        return $this->options->firstWhere('is_correct', true);
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'multiple_choice' => 'Pilihan Ganda',
            'true_false' => 'Benar/Salah',
            'essay' => 'Esai',
            default => ucfirst($this->type),
        };
    }
}
