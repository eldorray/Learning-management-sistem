<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LandingPageContent
{
    public const SOURCE_HASH = 'c8e06b90397ac246baf0ab6f32f5f6b570acc6fe03c7009f711b579fb72d9f49';

    public static function schema(): array
    {
        return json_decode(file_get_contents(resource_path('threeui/content-schema.json')), true, flags: JSON_THROW_ON_ERROR) + [
            'background_mode' => ['type' => 'select', 'section' => 'Latar gedung sekolah', 'label' => 'Jenis latar utama', 'default' => '3d', 'options' => ['3d' => 'Animasi 3D Kage asli', 'photo' => 'Foto gedung sekolah']],
            'background_image' => ['type' => 'image', 'section' => 'Latar gedung sekolah', 'label' => 'Foto gedung sekolah', 'default' => ''],
            'background_position' => ['type' => 'select', 'section' => 'Latar gedung sekolah', 'label' => 'Posisi foto', 'default' => 'center center', 'options' => ['center center' => 'Tengah', 'center top' => 'Atas', 'left center' => 'Kiri', 'right center' => 'Kanan']],
        ];
    }

    public static function values(): array
    {
        $saved = json_decode(Setting::get('landing_page_content', '{}'), true) ?: [];
        $values = [];
        foreach (self::schema() as $key => $field) {
            $values[$key] = $saved[$key] ?? $field['default'];
        }

        return $values;
    }

    public static function save(array $values): void
    {
        $schema = self::schema();
        $rules = [];
        foreach ($schema as $key => $field) {
            $rules[$key] = ['sometimes', 'nullable', 'string', 'max:2000'];
            if ($field['type'] === 'select') {
                $rules[$key] = ['sometimes', 'required', Rule::in(array_keys($field['options']))];
            } elseif ($key === 'js_wordmark') {
                $rules[$key] = ['sometimes', 'required', 'string', 'max:12'];
            } elseif ($key === 'language') {
                $rules[$key] = ['sometimes', 'required', 'regex:/^[a-z]{2,3}(-[A-Za-z]{2,4})?$/'];
            } elseif ($field['type'] === 'link') {
                $allowed = ['#top', '#hero', '#gate', '#pathways', '#lessons', '#eternity'];
                // The authored nav uses querySelector(href); only non-nav links may leave the scene.
                if (! in_array($key, ['link_02', 'link_03', 'link_04', 'link_05'], true)) {
                    $allowed = [...$allowed, '/login', '/register'];
                }
                $rules[$key] = ['sometimes', 'required', Rule::in($allowed)];
            } elseif ($field['type'] === 'image') {
                $rules[$key][] = function ($attribute, $value, $fail) use ($field) {
                    if ($value !== $field['default'] && ! preg_match('~^/storage/landing/[A-Za-z0-9_-]+\.(?:png|jpe?g|webp)$~D', $value ?? '')) {
                        $fail('Gambar harus berasal dari unggahan landing page.');
                    }
                };
            }
        }
        $validated = Validator::make($values, $rules)->validate();
        $merged = array_replace(self::values(), array_map(fn ($value) => $value ?? '', $validated));
        if ($merged['background_mode'] === 'photo' && $merged['background_image'] === '') {
            throw ValidationException::withMessages(['background_image' => 'Unggah foto gedung sekolah sebelum mengaktifkan mode foto.']);
        }
        Setting::set('landing_page_content', json_encode($merged, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    }

    private static function replaceArtwork(string $source, string $url): string
    {
        // Retain the original SVG element so authored sizing, hover and layout selectors still apply.
        preg_match('/^(<svg\b[^>]*>)/', $source, $opening);
        preg_match('/viewBox="([^"]+)"/', $opening[1], $viewBox);
        [$x, $y, $width, $height] = explode(' ', $viewBox[1]);

        return $opening[1].'<image href="'.e($url).'" x="'.$x.'" y="'.$y.'" width="'.$width.'" height="'.$height.'" preserveAspectRatio="xMidYMid meet" /></svg>';
    }

    public static function render(): string
    {
        $html = file_get_contents(resource_path('threeui/public/landing-pages/kage.html'));
        if (hash('sha256', $html) !== self::SOURCE_HASH) {
            throw new \RuntimeException('Canonical Kage source checksum mismatch.');
        }
        $values = self::values();
        $replacements = [];
        foreach (self::schema() as $key => $field) {
            if (! isset($field['offset']) || $values[$key] === $field['default']) {
                continue;
            }
            $value = $values[$key];
            $replacement = match ($field['encoding']) {
                'js' => json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR),
                'svg-image' => $value === '' ? $field['source'] : self::replaceArtwork($field['source'], $value),
                'url' => $value === '' ? $field['source'] : e($value),
                default => e($value),
            };
            if (substr($html, $field['offset'], $field['length']) !== $field['source']) {
                throw new \RuntimeException('Kage content anchor mismatch: '.$key);
            }
            $replacements[] = [$field['offset'], $field['length'], $replacement];
        }
        usort($replacements, fn ($a, $b) => $b[0] <=> $a[0]);
        foreach ($replacements as [$offset, $length, $replacement]) {
            $html = substr_replace($html, $replacement, $offset, $length);
        }
        if ($values['background_mode'] === 'photo' && $values['background_image'] !== '') {
            $background = view('landing-school-background', [
                'image' => $values['background_image'],
                'position' => $values['background_position'],
                'wordmark' => $values['js_wordmark'],
            ])->render();
            $html = preg_replace('/<body\b([^>]*)>/', '<body class="school-photo"$1>', $html, 1);
            $html = str_replace('<canvas id="gl"', $background.'<canvas id="gl"', $html);
            $bootAnchor = 'function boot() {';
            if (substr_count($html, $bootAnchor) !== 1) {
                throw new \RuntimeException('Kage photo boot anchor mismatch.');
            }
            $photoBoot = file_get_contents(resource_path('js/landing-photo-boot.js'));
            $html = str_replace($bootAnchor, $photoBoot."\n".$bootAnchor."\n  return startSchoolPhoto();", $html);
        }
        // Relative assets keep their authored paths because this response lives under /landing-pages/.
        // Authenticate outside the sandbox instead of rendering the LMS inside the scene iframe.
        $bridge = '<script>document.addEventListener("click",function(event){const a=event.target.closest("a");if(a&&["/login","/register"].includes(a.getAttribute("href"))){event.preventDefault();parent.postMessage({type:"arrahmah-navigate",path:a.getAttribute("href")},location.origin);}},true);</script>';

        return str_replace('</body>', $bridge.'</body>', $html);
    }
}
