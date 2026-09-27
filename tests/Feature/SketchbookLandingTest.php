<?php

use App\Livewire\Admin\SketchbookSettings;
use App\Models\Setting;
use App\Models\User;
use App\Support\SketchbookContent;
use App\Support\SketchbookSpread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Setting::clearCache();
    Storage::fake('public');
});

test('public landing hosts the sketchbook with localised default content', function () {
    $this->get('/')->assertOk()->assertSee('landing-root', false)->assertSee('LMS Ar-Rahmah')->assertDontSee('href="'.route('register').'"', false);
    $this->get('/landing-pages/meng-to-sketchbook.html')->assertOk()
        ->assertHeader('Cache-Control', 'no-store, private')
        ->assertSee('<html lang="id">', false)
        ->assertSee('<a class="name" href="#">LMS Ar-Rahmah</a>', false)
        ->assertSee('Tahfidz Al-Qur\\u0027an', false)
        ->assertSee('"url":"meng-to-sketchbook\/marina-bay-sands.png"', false)
        ->assertSee('aria-label="halaman sebelumnya"', false)
        ->assertSee('aria-label="perbesar"', false)
        ->assertSee('aria-label="kontrol tampilan"', false)
        ->assertSee('<a class="sb-login" href="/login">Masuk ke LMS<svg', false)
        ->assertDontSee('>Meng To<', false)
        ->assertDontSee('aria-label="LinkedIn"', false);
    expect(hash_file('sha256', resource_path('threeui/public/landing-pages/meng-to-sketchbook.html')))->toBe(SketchbookContent::SOURCE_HASH)
        ->and(glob(public_path('landing-pages/meng-to-sketchbook/*')))->toHaveCount(17);
});

test('administrator edits text, links and a plate illustration', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    Livewire::test(SketchbookSettings::class)
        ->assertSee('Halaman 9 — ilustrasi')
        ->set('texts.name', 'Pondok Ar-Rahmah')
        ->set('texts.plate_3_title', 'Setoran Pekanan')
        ->set('texts.social_instagram', 'https://instagram.com/arrahmah')
        ->set('texts.foot_link_url', 'mailto:info@arrahmah.test')
        ->set('uploads.plate_3_image', UploadedFile::fake()->image('foto-sekolah.jpg', 1600, 1000))
        ->call('save')
        ->assertHasNoErrors();

    $image = SketchbookContent::values()['plate_3_image'];
    Storage::disk('public')->assertExists(substr($image, strlen('/storage/')));
    // The plain photo is served as a transparent 1760x1240 book spread, not as the raw upload.
    $spreadUrl = SketchbookSpread::url($image);
    expect($spreadUrl)->toStartWith('/storage/landing/spread-');
    $spread = imagecreatefromstring(Storage::disk('public')->get(substr($spreadUrl, strlen('/storage/'))));
    expect([imagesx($spread), imagesy($spread)])->toBe([1760, 1240])
        ->and((imagecolorat($spread, 0, 0) >> 24) & 127)->toBe(127)
        ->and((imagecolorat($spread, 880, 620) >> 24) & 127)->toBe(0);
    $this->get('/landing-pages/meng-to-sketchbook.html')->assertOk()
        ->assertSee('<a class="name" href="#">Pondok Ar-Rahmah</a>', false)
        ->assertSee('"url":'.json_encode($spreadUrl), false)
        ->assertSee('Setoran Pekanan')
        ->assertSee('href="https://instagram.com/arrahmah" target="_blank"', false)
        ->assertSee('href="mailto:info@arrahmah.test"', false);

    Livewire::test(SketchbookSettings::class)->call('resetImage', 'plate_3_image')->assertHasNoErrors();
    expect(SketchbookContent::values()['plate_3_image'])->toBe('');
});

test('a photo saved before composition existed is fitted to the book on render', function () {
    Storage::disk('public')->put('landing/lama.jpg', UploadedFile::fake()->image('lama.jpg', 1921, 1281)->getContent());
    SketchbookContent::save(['plate_1_image' => '/storage/landing/lama.jpg']);

    expect(SketchbookContent::render())->toContain('"url":"\\/storage\\/landing\\/spread-v1-lama.png"');
    Storage::disk('public')->assertExists('landing/spread-v1-lama.png');
});

test('an uploaded spread with a transparent book outline is kept as-is', function () {
    $spread = imagecreatetruecolor(1760, 1240);
    imagesavealpha($spread, true);
    imagealphablending($spread, false);
    imagefill($spread, 0, 0, imagecolorallocatealpha($spread, 0, 0, 0, 127));
    imagefilledrectangle($spread, 90, 270, 1670, 968, imagecolorallocate($spread, 10, 200, 30));
    $path = tempnam(sys_get_temp_dir(), 'spread');
    imagepng($spread, $path);

    $stored = imagecreatefromstring(SketchbookSpread::fromUpload($path));
    expect(imagecolorat($stored, 880, 620) & 0xFFFFFF)->toBe(0x0AC81E);
    unlink($path);
});

test('saved text is escaped and cannot be read as a source anchor', function () {
    SketchbookContent::save([
        'kicker' => '</p><script>alert(1)</script>',
        'plate_1_title' => '</script><script>alert(2)</script>',
        'bio_3' => 'const PAGES=[ aria-label="zoom in"',
    ]);
    $html = SketchbookContent::render();
    expect($html)->not->toContain('<script>alert(1)</script>')
        ->not->toContain('</script><script>alert(2)</script>')
        ->toContain('&lt;/p&gt;&lt;script&gt;alert(1)&lt;/script&gt;')
        ->toContain('const PAGES=[ aria-label=&quot;zoom in&quot;');
});

test('unsafe links, social urls and uploads are rejected', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    Livewire::test(SketchbookSettings::class)->set('texts.bio_link_1_url', 'javascript:alert(1)')->call('save')->assertHasErrors('texts.bio_link_1_url');
    Livewire::test(SketchbookSettings::class)->set('texts.social_x', 'http://x.com/a')->call('save')->assertHasErrors('texts.social_x');
    Livewire::test(SketchbookSettings::class)->set('texts.name', '')->call('save')->assertHasErrors('texts.name');
    Livewire::test(SketchbookSettings::class)->set('uploads.plate_1_image', UploadedFile::fake()->createWithContent('bad.svg', '<svg onload="alert(1)"/>'))->call('save')->assertHasErrors('uploads.plate_1_image');
    expect(Setting::get('landing_sketchbook_content'))->toBeNull();
    expect(fn () => SketchbookContent::save(['plate_1_image' => '/landing-pages/../.env']))->toThrow(ValidationException::class);
});

test('only administrators can open the landing editor', function (?string $role) {
    if ($role !== null) {
        $this->actingAs(User::factory()->create(['role' => $role]));
    }
    Livewire::test(SketchbookSettings::class)->assertForbidden();
})->with([null, 'student', 'instructor', 'parent']);
