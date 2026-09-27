<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\InstructorDirectory;
use App\Livewire\Admin\TahfidzManagement;
use App\Livewire\Student\CourseLearning;
use App\Livewire\Student\MyCourses;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Module;
use App\Models\TahfidzGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function courseWithLessons(int $count = 3): Course
{
    $course = Course::factory()->create();
    $module = Module::create(['course_id' => $course->id, 'title' => 'Bab 1', 'order' => 1]);
    foreach (range(1, $count) as $i) {
        Lesson::create(['module_id' => $module->id, 'course_id' => $course->id, 'title' => "Materi $i", 'content' => 'Isi', 'type' => 'text', 'order' => $i]);
    }

    return $course->fresh(['modules.lessons']);
}

function completeLesson(User $user, Lesson $lesson): void
{
    LessonProgress::create(['user_id' => $user->id, 'lesson_id' => $lesson->id, 'course_id' => $lesson->course_id, 'is_completed' => true, 'completed_at' => now()]);
}

test('completed lessons stay open and previous/next step through every lesson', function () {
    $student = User::factory()->create(['role' => 'student']);
    $course = courseWithLessons();
    [$first, $second, $third] = $course->modules->first()->lessons->all();
    Enrollment::create(['user_id' => $student->id, 'course_id' => $course->id, 'enrolled_at' => now(), 'status' => 'active']);
    completeLesson($student, $first);

    $this->actingAs($student);
    Livewire::test(CourseLearning::class, ['slug' => $course->slug])
        ->assertSet('currentLesson.id', $second->id)          // resumes at the first unfinished lesson
        ->call('previousLesson')->assertSet('currentLesson.id', $first->id)  // a finished lesson can be reviewed
        ->assertSee('Selesai · buka lagi')
        ->call('nextLesson')->assertSet('currentLesson.id', $second->id)
        ->call('nextLesson')->assertSet('currentLesson.id', $third->id);
});

test('a finished course opens at its first lesson for review', function () {
    $student = User::factory()->create(['role' => 'student']);
    $course = courseWithLessons(2);
    Enrollment::create(['user_id' => $student->id, 'course_id' => $course->id, 'enrolled_at' => now(), 'status' => 'completed', 'progress_percentage' => 100]);
    $course->modules->first()->lessons->each(fn ($l) => completeLesson($student, $l));

    $this->actingAs($student);
    Livewire::test(CourseLearning::class, ['slug' => $course->slug])
        ->assertSet('currentLesson.id', $course->modules->first()->lessons->first()->id);
});

test('lessons of another course cannot be opened from a course page', function () {
    $student = User::factory()->create(['role' => 'student']);
    $course = courseWithLessons(1);
    $foreign = courseWithLessons(1)->modules->first()->lessons->first();
    Enrollment::create(['user_id' => $student->id, 'course_id' => $course->id, 'enrolled_at' => now(), 'status' => 'active']);

    $this->actingAs($student);
    $page = Livewire::test(CourseLearning::class, ['slug' => $course->slug]);
    expect(fn () => $page->call('selectLesson', $foreign->id))
        ->toThrow(ModelNotFoundException::class);
});

test('my courses lists active and finished courses separately', function () {
    $student = User::factory()->create(['role' => 'student']);
    $active = Course::factory()->create(['title' => 'Kursus Aktif']);
    $done = Course::factory()->create(['title' => 'Kursus Tamat']);
    Enrollment::create(['user_id' => $student->id, 'course_id' => $active->id, 'enrolled_at' => now(), 'status' => 'active']);
    Enrollment::create(['user_id' => $student->id, 'course_id' => $done->id, 'enrolled_at' => now(), 'status' => 'completed', 'progress_percentage' => 100]);

    $this->actingAs($student);
    $this->get('/my-courses')->assertOk()->assertSee('Kursus Aktif')->assertDontSee('Kursus Tamat');
    Livewire::test(MyCourses::class)->set('tab', 'selesai')
        ->assertSee('Kursus Tamat')
        ->assertSeeHtml('href="'.route('student.learn', $done->slug).'"');
});

test('new course link opens the create form', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $this->get('/admin/courses/create')->assertOk()->assertSee('wire:submit="save"', false);
    $this->get('/admin/courses')->assertOk()->assertDontSee('wire:submit="save"', false);
    $this->get('/admin/dashboard')->assertOk()->assertDontSee('>Export<', false);
});

test('completed this month counts only the instructor courses in this month', function () {
    $guru = User::factory()->create(['role' => 'instructor']);
    $mine = Course::factory()->create(['instructor_id' => $guru->id]);
    $other = Course::factory()->create();
    $done = ['status' => 'completed', 'progress_percentage' => 100, 'enrolled_at' => now()];
    Enrollment::create(['user_id' => User::factory()->create()->id, 'course_id' => $mine->id, 'completed_at' => now()] + $done);
    Enrollment::create(['user_id' => User::factory()->create()->id, 'course_id' => $mine->id, 'completed_at' => now()->subYear()] + $done);
    Enrollment::create(['user_id' => User::factory()->create()->id, 'course_id' => $other->id, 'completed_at' => now()] + $done);

    $this->actingAs($guru);
    Livewire::test(Dashboard::class)->assertViewHas('completedThisMonth', 1);
});

test('deleting a teacher states the courses and enrollments that go with them', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $guru = User::factory()->create(['role' => 'instructor', 'name' => 'Ustadz Hamid']);
    $course = Course::factory()->create(['instructor_id' => $guru->id]);
    Enrollment::create(['user_id' => User::factory()->create()->id, 'course_id' => $course->id, 'enrolled_at' => now(), 'status' => 'active']);

    Livewire::test(InstructorDirectory::class)->call('confirmDelete', $guru->id)
        ->assertSee('Ustadz Hamid akan dihapus bersama 1 kursus yang diampunya dan 1 pendaftaran siswa');
});

test('plotting keeps the join date of students already in the halaqoh', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $group = TahfidzGroup::create(['instruktur_id' => User::factory()->create(['role' => 'instructor'])->id, 'nama_halaqoh' => 'Al-Fatih', 'is_active' => true]);
    [$lama, $baru, $keluar] = User::factory()->count(3)->create(['role' => 'student'])->all();
    $group->students()->attach([$lama->id => ['joined_at' => '2025-07-14'], $keluar->id => ['joined_at' => '2025-07-14']]);

    Livewire::test(TahfidzManagement::class)
        ->call('openPlottingModal', $group->id)
        ->set('selectedStudents', [(string) $lama->id, (string) $baru->id])
        ->call('savePlotting');

    $members = $group->students()->get()->keyBy('id');
    expect($members->keys()->sort()->values()->all())->toBe(collect([$lama->id, $baru->id])->sort()->values()->all())
        ->and(substr((string) $members[$lama->id]->pivot->joined_at, 0, 10))->toBe('2025-07-14')
        ->and(substr((string) $members[$baru->id]->pivot->joined_at, 0, 10))->toBe(now()->format('Y-m-d'));
});

test('student pages no longer ask for notification permission on load', function () {
    $this->actingAs(User::factory()->create(['role' => 'student']));
    $html = $this->get('/dashboard')->assertOk()->getContent();
    // The only permission request lives inside the "Mulai Fokus" handler.
    expect(substr_count($html, 'Notification.requestPermission'))->toBe(1)
        ->and($html)->toContain('async begin()');
});
