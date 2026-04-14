<?php

namespace App\Observers;

use App\Models\Lesson;

class LessonObserver
{
    public function created(Lesson $lesson): void
    {
        $this->syncCount($lesson);
    }

    public function deleted(Lesson $lesson): void
    {
        $this->syncCount($lesson);
    }

    private function syncCount(Lesson $lesson): void
    {
        $lesson->course?->syncLessonCount();
    }
}
