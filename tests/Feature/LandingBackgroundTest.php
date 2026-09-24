<?php

use App\Livewire\Admin\LandingPageSettings;
use App\Models\Setting;
use App\Models\User;
use App\Support\LandingPageContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Setting::clearCache();
    Storage::fake('public');
    $this->actingAs(User::factory()->create(['role' => 'admin']));
});

test('admin can upload a school photograph and activate it instead of the 3d scene', function () {
    Livewire::test(LandingPageSettings::class)
        ->assertSee('Latar gedung sekolah')
        ->set('texts.background_mode', 'photo')
        ->set('texts.background_position', 'center top')
        ->set('uploads.background_image', UploadedFile::fake()->image('sekolah.jpg', 1600, 900))
        ->call('save')
        ->assertHasNoErrors();

    $values = LandingPageContent::values();
    expect($values['background_mode'])->toBe('photo');
    Storage::disk('public')->assertExists(substr($values['background_image'], strlen('/storage/')));
    $html = LandingPageContent::render();
    expect($html)->toContain('class="school-photo"')
        ->toContain('id="school-background"')
        ->toContain('src="'.$values['background_image'].'"')
        ->toContain('object-position: center top')
        ->toContain("mode: 'photo'");
    Livewire::test(LandingPageSettings::class)->assertSet('texts.background_mode', 'photo');
});

test('photo mode requires a photograph before it can be saved', function () {
    Livewire::test(LandingPageSettings::class)
        ->set('texts.background_mode', 'photo')
        ->call('save')->assertHasErrors('uploads.background_image');
    expect(Setting::get('landing_page_content'))->toBeNull();
});

test('resetting the school photo returns to the original 3d mode', function () {
    LandingPageContent::save(['background_mode' => 'photo', 'background_image' => '/storage/landing/school.jpg']);
    Livewire::test(LandingPageSettings::class)->call('resetImage', 'background_image')
        ->assertHasNoErrors()->assertSet('texts.background_mode', '3d');
    expect(LandingPageContent::values()['background_mode'])->toBe('3d')
        ->and(LandingPageContent::render())->not->toContain('id="school-background"');
});

test('switching back to 3d retains the uploaded photo for reuse', function () {
    LandingPageContent::save(['background_mode' => 'photo', 'background_image' => '/storage/landing/school.jpg']);
    Livewire::test(LandingPageSettings::class)->set('texts.background_mode', '3d')->call('save')->assertHasNoErrors();
    expect(LandingPageContent::values()['background_image'])->toBe('/storage/landing/school.jpg')
        ->and(LandingPageContent::render())->not->toContain('return startSchoolPhoto();');
});

test('background choices and uploads reject unsafe values', function () {
    Livewire::test(LandingPageSettings::class)->set('texts.background_mode', 'invalid')->call('save')->assertHasErrors('texts.background_mode');
    Livewire::test(LandingPageSettings::class)->set('texts.background_position', 'center; color:red')->call('save')->assertHasErrors('texts.background_position');
    Livewire::test(LandingPageSettings::class)->set('uploads.background_image', UploadedFile::fake()->createWithContent('bad.svg', '<svg onload="alert(1)"/>'))->call('save')->assertHasErrors('uploads.background_image');
    expect(Setting::get('landing_page_content'))->toBeNull();
});
