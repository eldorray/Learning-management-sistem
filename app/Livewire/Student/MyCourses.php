<?php

namespace App\Livewire\Student;

use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

class MyCourses extends Component
{
    #[Url]
    public string $tab = 'aktif';

    public function render()
    {
        // Every enrolment across school years, so finished and past courses stay reachable.
        $all = Enrollment::where('user_id', Auth::id())
            ->whereHas('course', fn ($q) => $q->where('is_published', true))
            ->with(['course.instructor', 'tahunAjaran'])
            ->latest('enrolled_at')
            ->get();

        [$completed, $active] = $all->partition(fn ($e) => $e->status === 'completed');
        $tab = $this->tab === 'selesai' ? 'selesai' : 'aktif';

        return view('livewire.student.my-courses', [
            'enrollments' => $tab === 'selesai' ? $completed : $active,
            'activeCount' => $active->count(),
            'completedCount' => $completed->count(),
            'currentTab' => $tab,
        ])->layout('layouts.student', ['title' => 'Kursus Saya']);
    }
}
