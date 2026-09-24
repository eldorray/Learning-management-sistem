<?php

use App\Support\LandingPageContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

test('public landing serves the React host and local authored document', function () {
    $this->get('/')->assertOk()->assertSee('kage-root', false);
    $this->get('/landing-pages/kage.html')->assertOk()->assertSee('id="gl"', false);
    $this->get('/landing-preview')->assertOk()->assertSee('kage-root', false);
});

test('every editable source slot is unique and substitutions preserve the document', function () {
    $schema = LandingPageContent::schema();
    $values = [];
    foreach ($schema as $key => $field) {
        if ($field['type'] === 'text' && $key !== 'language') {
            $values[$key] = 'X'.substr($key, -8);
        }
    }
    LandingPageContent::save($values);
    $html = LandingPageContent::render();
    foreach ($values as $key => $value) {
        if (isset($schema[$key]['offset'])) {
            expect($html)->toContain($value);
        }
    }
    expect($html)->toContain('id="gl"')->toContain('function wireNav()');
});

test('uploaded artwork retains every authored svg container and viewbox', function () {
    $artwork = array_filter(LandingPageContent::schema(), fn ($field) => ($field['encoding'] ?? '') === 'svg-image');
    LandingPageContent::save(array_fill_keys(array_keys($artwork), '/storage/landing/replacement.webp'));
    $html = LandingPageContent::render();
    foreach ($artwork as $field) {
        preg_match('/^(<svg\b[^>]*>)/', $field['source'], $opening);
        preg_match('/viewBox="([^"]+)"/', $opening[1], $viewBox);
        [$x, $y, $width, $height] = explode(' ', $viewBox[1]);
        expect($html)->toContain($opening[1].'<image href="/storage/landing/replacement.webp" x="'.$x.'" y="'.$y.'" width="'.$width.'" height="'.$height.'" preserveAspectRatio="xMidYMid meet" /></svg>');
    }
});

test('landing rejects unsafe links images and oversized canvas text', function () {
    foreach ([['link_02' => '/login'], ['link_01' => 'javascript:alert(1)'], ['image_01' => 'https://evil.test/a.webp'], ['js_wordmark' => str_repeat('x', 13)]] as $values) {
        expect(fn () => LandingPageContent::save($values))->toThrow(ValidationException::class);
    }
});

test('landing content is rendered from the verified canonical source with escaped editable text', function () {
    $schema = LandingPageContent::schema();
    $key = array_key_first(array_filter($schema, fn ($field) => $field['default'] === 'Where stillness'));
    expect($key)->not->toBeNull();
    LandingPageContent::save([$key => '<script>alert("x")</script>']);
    $html = LandingPageContent::render();
    expect($html)->toContain('&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;')
        ->not->toContain('<script>alert("x")</script>')
        ->toContain('function buildWordmark(')
        ->toContain('secret-pathways-assets/three.min.js');
    expect(hash_file('sha256', resource_path('threeui/public/landing-pages/kage.html')))
        ->toBe('c8e06b90397ac246baf0ab6f32f5f6b570acc6fe03c7009f711b579fb72d9f49');
});
