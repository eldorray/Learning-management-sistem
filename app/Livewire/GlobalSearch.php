<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';
    public bool $open = false;

    public function updatedQuery(): void
    {
        $this->open = strlen(trim($this->query)) >= 2;
    }

    public function clear(): void
    {
        $this->query = '';
        $this->open = false;
    }

    public function getResultsProperty(): array
    {
        $q = trim($this->query);
        if (strlen($q) < 2) {
            return [];
        }

        $user = Auth::user();
        $results = [];

        if ($user->isAdmin()) {
            // Courses
            $courses = Course::where('title', 'like', "%{$q}%")
                ->orWhere('category', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%")
                ->limit(5)->get();
            foreach ($courses as $c) {
                $results[] = [
                    'type'  => 'course',
                    'label' => $c->title,
                    'sub'   => $c->category ?? 'Kursus',
                    'icon'  => 'library_books',
                    'url'   => route('admin.courses.builder', $c->id),
                ];
            }

            // Students
            $students = User::where('role', 'student')
                ->where(fn($sq) => $sq
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('nis', 'like', "%{$q}%")
                    ->orWhere('nisn', 'like', "%{$q}%")
                    ->orWhere('class_group', 'like', "%{$q}%"))
                ->limit(4)->get();
            foreach ($students as $s) {
                $results[] = [
                    'type'  => 'student',
                    'label' => $s->name,
                    'sub'   => $s->class_group ? "Kelas {$s->class_group}" : $s->email,
                    'icon'  => 'person',
                    'url'   => route('admin.students'),
                ];
            }

            // Instructors
            $instructors = User::where('role', 'instructor')
                ->where(fn($sq) => $sq
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('specialization', 'like', "%{$q}%"))
                ->limit(3)->get();
            foreach ($instructors as $i) {
                $results[] = [
                    'type'  => 'instructor',
                    'label' => $i->name,
                    'sub'   => $i->specialization ?? 'Instruktur',
                    'icon'  => 'school',
                    'url'   => route('admin.instructors'),
                ];
            }

        } elseif ($user->isInstructor()) {
            // Instructor's own courses
            $courses = Course::where('instructor_id', $user->id)
                ->where(fn($sq) => $sq
                    ->where('title', 'like', "%{$q}%")
                    ->orWhere('category', 'like', "%{$q}%"))
                ->limit(6)->get();
            foreach ($courses as $c) {
                $results[] = [
                    'type'  => 'course',
                    'label' => $c->title,
                    'sub'   => $c->category ?? 'Kursus',
                    'icon'  => 'library_books',
                    'url'   => route('admin.courses.builder', $c->id),
                ];
            }

            // Students enrolled in instructor's courses
            $courseIds = Course::where('instructor_id', $user->id)->pluck('id');
            $students = User::where('role', 'student')
                ->whereHas('enrollments', fn($eq) => $eq->whereIn('course_id', $courseIds))
                ->where(fn($sq) => $sq
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('nisn', 'like', "%{$q}%")
                    ->orWhere('class_group', 'like', "%{$q}%"))
                ->limit(5)->get();
            foreach ($students as $s) {
                $results[] = [
                    'type'  => 'student',
                    'label' => $s->name,
                    'sub'   => $s->class_group ? "Kelas {$s->class_group}" : $s->email,
                    'icon'  => 'person',
                    'url'   => route('admin.students'),
                ];
            }

        } else {
            // Student: search published courses
            $enrolledIds = $user->enrollments()->pluck('course_id');
            $courses = Course::where('is_published', true)
                ->where(fn($sq) => $sq
                    ->where('title', 'like', "%{$q}%")
                    ->orWhere('category', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%"))
                ->limit(8)->get();
            foreach ($courses as $c) {
                $enrolled = $enrolledIds->contains($c->id);
                $results[] = [
                    'type'  => 'course',
                    'label' => $c->title,
                    'sub'   => ($c->category ?? 'Kursus') . ($enrolled ? ' · Terdaftar' : ''),
                    'icon'  => $enrolled ? 'play_circle' : 'explore',
                    'url'   => $enrolled
                        ? route('student.learn', $c->slug)
                        : route('student.catalog'),
                ];
            }
        }

        return $results;
    }

    public function render()
    {
        return view('livewire.global-search', [
            'results' => $this->results,
        ]);
    }
}
