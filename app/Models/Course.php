<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'short_description',
        'thumbnail', 'level', 'category', 'duration_minutes',
        'total_lessons', 'price', 'is_free', 'is_published', 'instructor_id',
        'enrollment_code',
    ];

    protected static function booted(): void
    {
        static::creating(function (Course $course) {
            if (!$course->enrollment_code) {
                $course->enrollment_code = static::generateUniqueCode();
            }
        });
    }

    /**
     * Generate a unique 6-character uppercase enrollment code.
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        } while (static::where('enrollment_code', $code)->exists());

        return $code;
    }

    protected $casts = [
        'is_free' => 'boolean',
        'is_published' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot(['progress_percentage', 'status', 'enrolled_at', 'completed_at'])
            ->withTimestamps();
    }

    public function getLevelBadgeAttribute(): string
    {
        return match ($this->level) {
            'beginner' => 'Pemula',
            'intermediate' => 'Menengah',
            'advanced' => 'Mahir',
            default => ucfirst($this->level),
        };
    }

    public function getFormattedDurationAttribute(): string
    {
        $hours = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;
        if ($hours > 0) {
            return "{$hours}j {$minutes}m";
        }
        return "{$minutes}m";
    }

    /**
     * Get real-time lesson count from DB (not cached field).
     */
    public function getLessonCount(): int
    {
        return Lesson::where('course_id', $this->id)->count();
    }

    /**
     * Sync the cached total_lessons field with actual count.
     */
    public function syncLessonCount(): void
    {
        $count = $this->getLessonCount();
        if ($this->total_lessons !== $count) {
            $this->update(['total_lessons' => $count]);
        }
    }

    /**
     * Recalculate progress for a specific student enrollment.
     * Uses real-time lesson count to avoid stale data.
     */
    public function recalculateProgressFor(int $userId): void
    {
        $totalLessons = $this->getLessonCount();

        $completedLessons = \App\Models\LessonProgress::where('user_id', $userId)
            ->where('course_id', $this->id)
            ->where('is_completed', true)
            ->count();

        $progress = $totalLessons > 0
            ? min(100, round(($completedLessons / $totalLessons) * 100))
            : 0;

        $enrollment = Enrollment::where('user_id', $userId)
            ->where('course_id', $this->id)
            ->first();

        if (!$enrollment) return;

        $wasCompleted = $enrollment->status === 'completed';

        $enrollment->update([
            'progress_percentage' => $progress,
            'status' => $progress >= 100 ? 'completed' : 'active',
            'completed_at' => $progress >= 100 ? ($enrollment->completed_at ?? now()) : null,
        ]);

        // Award completion XP only once (when first reaching 100%)
        if ($progress >= 100 && !$wasCompleted) {
            User::find($userId)?->increment('xp_points', 100);
        }
    }
}
