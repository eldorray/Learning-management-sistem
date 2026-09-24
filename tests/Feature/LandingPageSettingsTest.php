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

function landingFieldKey(string $type): string
{
    return collect(LandingPageContent::schema())->filter(fn ($field, $key) => $field['type'] === $type && ! in_array($key, ['js_wordmark', 'language'], true))->keys()->first();
}

beforeEach(function () {
    Setting::clearCache();
    Storage::fake('public');
});

test('landing editor rejects non administrators on mount', function (?string $role) {
    if ($role !== null) {
        $this->actingAs(User::factory()->create(['role' => $role]));
    }

    Livewire::test(LandingPageSettings::class)->assertForbidden();
})->with([null, 'student', 'instructor', 'parent']);

test('administrator sees the grouped landing editor and preview link', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $editor = Livewire::test(LandingPageSettings::class)
        ->assertOk()
        ->assertSee('Pengaturan Landing Page')
        ->assertSeeHtml('href="/landing-preview"');

    foreach (LandingPageContent::schema() as $key => $field) {
        $editor->assertSee($field['section'])->assertSee($field['label']);
        if ($field['type'] !== 'image') {
            $editor->assertSet("texts.$key", LandingPageContent::values()[$key]);
        }
    }
});

test('administrator saves plain text without accepting image URLs or unknown keys', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $text = landingFieldKey('text');
    $image = landingFieldKey('image');
    $original = LandingPageContent::values()[$image];
    $payload = '<script>alert("xss")</script>';

    Livewire::test(LandingPageSettings::class)
        ->set("texts.$text", $payload)
        ->set("texts.$image", 'https://attacker.invalid/image.svg')
        ->set('texts.unknown_key', 'not allowed')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Perubahan landing page berhasil disimpan.');

    $saved = json_decode(Setting::get('landing_page_content'), true);
    expect($saved[$text])->toBe($payload)
        ->and($saved[$image])->toBe($original)
        ->and($saved)->not->toHaveKey('unknown_key');
    expect(LandingPageContent::render())->not->toContain($payload);
    Livewire::test(LandingPageSettings::class)->assertSet("texts.$text", $payload);
});

test('landing editor reports field validation before persistence', function (string $kind, string $value) {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $key = in_array($kind, ['js_wordmark', 'language'], true) ? $kind : landingFieldKey($kind);

    Livewire::test(LandingPageSettings::class)
        ->set("texts.$key", $value)
        ->call('save')
        ->assertHasErrors("texts.$key");

    expect(Setting::get('landing_page_content'))->toBeNull();
})->with([
    'long text' => ['text', str_repeat('a', 2001)],
    'long wordmark' => ['js_wordmark', 'ABCDEFGHIJKLM'],
    'invalid language' => ['language', '<script>'],
    'javascript' => ['link', 'javascript:alert(1)'],
    'external URL' => ['link', 'https://example.com'],
    'unknown anchor' => ['link', '#does-not-exist'],
]);

test('administrator uploads a randomized landing image with a safe pending preview', function (string $extension) {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $key = landingFieldKey('image');
    $file = UploadedFile::fake()->image('original.'.$extension);

    Livewire::test(LandingPageSettings::class)
        ->set("uploads.$key", $file)
        ->assertSeeHtml('alt="Pratinjau unggahan"')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('uploads', []);

    $path = LandingPageContent::values()[$key];
    expect($path)->toMatch('~^/storage/landing/[A-Za-z0-9]+\.(png|jpg|jpeg|webp)$~')
        ->not->toContain('original');
    Storage::disk('public')->assertExists(substr($path, strlen('/storage/')));
    Livewire::test(LandingPageSettings::class)->assertSeeHtml('src="'.$path.'"');
})->with(['png', 'jpg', 'webp']);

test('administrator resets only the requested image and retains old files', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $key = landingFieldKey('image');
    $text = landingFieldKey('text');
    Storage::disk('public')->put('landing/previous.png', 'previous');
    LandingPageContent::save([$key => '/storage/landing/previous.png', $text => 'Persisted text']);

    Livewire::test(LandingPageSettings::class)
        ->set("texts.$text", 'Unsaved draft')
        ->call('resetImage', $key)
        ->assertHasNoErrors()
        ->assertSet("texts.$text", 'Unsaved draft');

    expect(LandingPageContent::values()[$key])->toBe(LandingPageContent::schema()[$key]['default'])
        ->and(LandingPageContent::values()[$text])->toBe('Persisted text');
    Storage::disk('public')->assertExists('landing/previous.png');
});

test('image reset rejects unknown and non image slots', function (string $slot) {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    Livewire::test(LandingPageSettings::class)->call('resetImage', $slot)->assertNotFound();
    expect(Setting::get('landing_page_content'))->toBeNull();
})->with(['unknown_key', 'js_wordmark', '../../settings']);

test('landing actions recheck authorization after role revocation', function (string $action) {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);
    $editor = Livewire::test(LandingPageSettings::class);
    User::whereKey($admin->id)->update(['role' => 'instructor']);

    $editor->call($action, ...($action === 'resetImage' ? [landingFieldKey('image')] : []))->assertForbidden();
    expect(Setting::get('landing_page_content'))->toBeNull();
})->with(['save', 'resetImage']);

test('invalid image uploads never persist or preview as images', function (string $kind) {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $key = landingFieldKey('image');
    $upload = match ($kind) {
        'oversized' => UploadedFile::fake()->image('large.png')->size(4097),
        'svg' => UploadedFile::fake()->createWithContent('attack.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'),
        'gif' => UploadedFile::fake()->image('animation.gif'),
        'disguised' => UploadedFile::fake()->createWithContent('photo.png', '<?php echo 1;'),
        'url' => 'https://attacker.invalid/photo.png',
    };

    Livewire::test(LandingPageSettings::class)
        ->set("uploads.$key", $upload)
        ->assertDontSeeHtml('alt="Pratinjau unggahan"')
        ->call('save')
        ->assertHasErrors("uploads.$key");
    expect(Setting::get('landing_page_content'))->toBeNull()
        ->and(Storage::disk('public')->allFiles('landing'))->toBe([]);
})->with(['oversized', 'svg', 'gif', 'disguised', 'url']);

test('unknown upload slots cannot write files or settings', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    Livewire::test(LandingPageSettings::class)
        ->set('uploads.unknown', UploadedFile::fake()->image('unknown.png'))
        ->set('uploads.js_wordmark', UploadedFile::fake()->image('text.png'))
        ->call('save')
        ->assertHasNoErrors();

    expect(Storage::disk('public')->allFiles('landing'))->toBe([])
        ->and(json_decode(Setting::get('landing_page_content'), true))->not->toHaveKey('unknown');
});

test('text saves retain trusted images persisted after the editor was opened', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $key = landingFieldKey('image');
    $editor = Livewire::test(LandingPageSettings::class);
    LandingPageContent::save([$key => '/storage/landing/trusted.png']);

    $editor->set("texts.$key", '/storage/landing/forged.png')->call('save')->assertHasNoErrors();
    expect(LandingPageContent::values()[$key])->toBe('/storage/landing/trusted.png');
});

test('landing editor accepts the exact image and text size limits', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $key = landingFieldKey('image');
    $text = landingFieldKey('text');
    Livewire::test(LandingPageSettings::class)
        ->set("texts.$text", str_repeat('a', 2000))
        ->set('texts.js_wordmark', 'ABCDEFGHIJKL')
        ->set("uploads.$key", UploadedFile::fake()->image('limit.png')->size(4096))
        ->call('save')
        ->assertHasNoErrors();

    expect(LandingPageContent::values()[$text])->toBe(str_repeat('a', 2000))
        ->and(LandingPageContent::values()['js_wordmark'])->toBe('ABCDEFGHIJKL');
    Storage::disk('public')->assertExists(substr(LandingPageContent::values()[$key], strlen('/storage/')));
});
