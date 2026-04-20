<?php

namespace App\Livewire\Student;

use App\Models\Enrollment;
use App\Support\AcademicYear;
use Livewire\Component;

class FocusMode extends Component
{
    public bool $active = false;
    public int $duration = 25; // minutes (Pomodoro default)
    public ?int $selectedCourseId = null;
    public string $sessionNote = '';

    public function getActiveEnrollmentsProperty()
    {
        $taId = AcademicYear::aktifId();
        return Enrollment::with('course')
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->when($taId, fn($q) => $q->where('tahun_ajaran_id', $taId))
            ->orderBy('progress_percentage', 'asc')
            ->limit(10)
            ->get();
    }

    public function start(): void
    {
        $this->validate([
            'duration'         => 'required|integer|min:5|max:120',
            'selectedCourseId' => 'nullable|exists:courses,id',
        ]);

        $this->active = true;
    }

    public function stop(): void
    {
        $this->active = false;
        $this->sessionNote = '';
    }

    public function render()
    {
        return view('livewire.student.focus-mode');
    }
}
