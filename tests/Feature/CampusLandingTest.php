<?php

use App\Livewire\Admin\LandingPageSettings;
use App\Models\Setting;
use App\Models\User;
use App\Support\LandingPageContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Setting::clearCache();
    $this->actingAs(User::factory()->create(['role' => 'admin']));
});

test('administrator selects the campus scene without requiring a photograph', function () {
    Livewire::test(LandingPageSettings::class)
        ->assertSee('Kampus Ar-Rahmah 3D')
        ->set('texts.background_mode', 'campus_3d')
        ->set('texts.campus_sign', 'SEKOLAH AR-RAHMAH')
        ->call('save')->assertHasNoErrors();
    $this->get('/landing-pages/kage.html')->assertOk()
        ->assertSee('class="campus-scene"', false)
        ->assertSee('return startCampus();', false)
        ->assertSee('SEKOLAH AR-RAHMAH')
        ->assertSee('campus-reference.webp');
    Livewire::test(LandingPageSettings::class)->assertSet('texts.background_mode', 'campus_3d');
});

test('campus signage is escaped and mode switching preserves original source', function () {
    LandingPageContent::save(['background_mode' => 'campus_3d', 'campus_sign' => '</script><script>alert(1)</script>']);
    expect(LandingPageContent::render())->not->toContain('</script><script>alert(1)</script>');
    LandingPageContent::save(['background_mode' => '3d']);
    expect(LandingPageContent::render())->not->toContain('return startCampus();');
    expect(hash_file('sha256', resource_path('threeui/public/landing-pages/kage.html')))->toBe(LandingPageContent::SOURCE_HASH);
});

test('removing an optional fallback image does not disable campus mode', function () {
    LandingPageContent::save(['background_mode' => 'campus_3d', 'background_image' => '/storage/landing/school.jpg']);
    Livewire::test(LandingPageSettings::class)->call('resetImage', 'background_image')->assertHasNoErrors();
    expect(LandingPageContent::values()['background_mode'])->toBe('campus_3d');
});
