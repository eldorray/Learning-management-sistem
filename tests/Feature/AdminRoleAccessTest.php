<?php

use App\Livewire\Instructor\TahfidzHalaqoh;
use App\Models\Course;
use App\Models\TahfidzGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('admin can open every student page', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    foreach (['/dashboard', '/my-courses', '/catalog', '/tahfidz', '/profile'] as $path) {
        $this->get($path)->assertOk();
    }
});

test('admin can open a course without being enrolled', function () {
    $course = Course::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'admin']))
        ->get('/learn/'.$course->slug)->assertOk();
});

test('student area stays closed to instructors and parents', function (string $role) {
    $this->actingAs(User::factory()->create(['role' => $role]))->get('/dashboard')->assertForbidden();
})->with(['instructor', 'parent']);

test('admin sees every instructor halaqoh, instructors only their own', function () {
    $ustadz = User::factory()->create(['role' => 'instructor', 'name' => 'Ustadz Hamid']);
    $other = User::factory()->create(['role' => 'instructor']);
    $group = TahfidzGroup::create(['instruktur_id' => $ustadz->id, 'nama_halaqoh' => 'Halaqoh Al-Fatih', 'is_active' => true]);

    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $this->get('/admin/tahfidz/halaqoh')->assertOk()->assertSee('Halaqoh Guru');
    Livewire::test(TahfidzHalaqoh::class)
        ->assertSet('selectedGroup', $group->id)
        ->assertSee('Halaqoh Al-Fatih')
        ->assertSee('Pengajar: Ustadz Hamid');

    $this->actingAs($other);
    $editor = Livewire::test(TahfidzHalaqoh::class)->assertSet('selectedGroup', null)->assertDontSee('Halaqoh Al-Fatih');
    // Selecting someone else's halaqoh by id reveals nothing.
    $editor->set('selectedGroup', $group->id)->assertDontSee('Halaqoh Al-Fatih');
});
