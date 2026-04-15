<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    protected $fillable = [
        'module_id', 'course_id', 'title', 'content',
        'video_url', 'document_path', 'document_name',
        'type', 'duration_minutes', 'order', 'is_preview',
    ];

    protected $casts = [
        'is_preview' => 'boolean',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function quizQuestions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function isCompletedBy(int $userId): bool
    {
        return $this->progress()->where('user_id', $userId)->where('is_completed', true)->exists();
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'video' => 'play_circle',
            'quiz' => 'quiz',
            'document' => 'description',
            default => 'article',
        };
    }

    public function getDocumentUrlAttribute(): ?string
    {
        if ($this->document_path) {
            return asset('storage/' . $this->document_path);
        }
        return null;
    }

    public function getDocumentExtensionAttribute(): ?string
    {
        if (!$this->document_name) return null;
        return strtolower(pathinfo($this->document_name, PATHINFO_EXTENSION));
    }

    /**
     * Convert any YouTube URL to embeddable format.
     * Supports: youtube.com/watch?v=, youtu.be/, youtube.com/shorts/, youtube.com/embed/
     */
    public function getEmbedUrlAttribute(): ?string
    {
        $url = $this->video_url;
        if (!$url) return null;

        $videoId = null;

        // youtube.com/watch?v=VIDEO_ID
        if (preg_match('/(?:youtube\.com\/watch\?v=)([\w-]+)/', $url, $m)) {
            $videoId = $m[1];
        }
        // youtu.be/VIDEO_ID
        elseif (preg_match('/(?:youtu\.be\/)([\w-]+)/', $url, $m)) {
            $videoId = $m[1];
        }
        // youtube.com/shorts/VIDEO_ID
        elseif (preg_match('/(?:youtube\.com\/shorts\/)([\w-]+)/', $url, $m)) {
            $videoId = $m[1];
        }
        // youtube.com/embed/VIDEO_ID (already embed)
        elseif (preg_match('/(?:youtube\.com\/embed\/)([\w-]+)/', $url, $m)) {
            $videoId = $m[1];
        }

        if ($videoId) {
            return "https://www.youtube.com/embed/{$videoId}?rel=0&modestbranding=1&disablekb=1";
        }

        // Not a YouTube URL — return as-is
        return $url;
    }
}
