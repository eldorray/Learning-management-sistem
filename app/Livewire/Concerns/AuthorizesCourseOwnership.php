<?php

namespace App\Livewire\Concerns;

use App\Models\Course;

/**
 * Shared ownership authorization for Livewire components that work with courses.
 * Admins always pass. Instructors must own the course.
 */
trait AuthorizesCourseOwnership
{
    protected function authorizeCourseOwnership(Course $course): void
    {
        if (!auth()->user()->isAdmin() && $course->instructor_id !== auth()->id()) {
            abort(403);
        }
    }
}
