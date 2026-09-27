<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('student profile offers logout for phones where the sidebar is hidden', function () {
    $student = User::factory()->create(['role' => 'student']);

    $this->actingAs($student)->get('/profile')->assertOk()
        ->assertSee('action="'.route('logout').'"', false)
        ->assertSee('Keluar');

    $this->post(route('logout'))->assertRedirect();
    $this->assertGuest();
});
