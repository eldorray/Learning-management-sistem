<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Validator;

class SketchbookContent
{
    public const SOURCE_HASH = 'e0330548b1ac905cf1b81698163ffa29f8a3a8c39b8d39f9b71ba5b9255b6dd1';

    public const LINK_PATTERN = '~\A(?:\#(?:sketchbook|about|plates|contact)|/(?:login|register)|https://[^\s"\'<>]+|mailto:[^\s"\'<>@]+@[^\s"\'<>]+)\z~';

    private const SETTING = 'landing_sketchbook_content';

    /** Authored spread files, in page order, with default captions for this LMS. */
    private const PLATES = [
        ['marina-bay-sands.png', 'Tahfidz Al-Qur\'an', 'Halaqoh harian'],
        ['gardens-by-the-bay.png', 'Kelas Digital', 'Materi & kuis'],
        ['merlion.png', 'Setoran Hafalan', 'Catatan ustadz'],
        ['buddha-tooth.png', 'Tahun Ajaran', 'Kalender akademik'],
        ['joo-chiat.png', 'Portal Orang Tua', 'Pantau dari rumah'],
        ['lau-pa-sat.png', 'Notifikasi Langsung', 'Kabar terbaru'],
        ['marina-bay-skyline.png', 'Gaya Belajar', 'Profil santri'],
        ['singapore-river.png', 'Mode Fokus', 'Belajar tanpa gangguan'],
        ['botanic-gardens.png', 'Aplikasi Ponsel', 'Pasang sekali, buka kapan saja'],
    ];

    private const SOCIALS = ['social_x' => 'X', 'social_linkedin' => 'LinkedIn', 'social_instagram' => 'Instagram'];

    public static function schema(): array
    {
        $schema = [
            'title' => ['type' => 'text', 'section' => 'Umum', 'label' => 'Judul tab browser', 'default' => 'LMS Ar-Rahmah', 'required' => true],
            'description' => ['type' => 'text', 'section' => 'Umum', 'label' => 'Deskripsi untuk mesin pencari', 'default' => 'Platform pembelajaran digital Modern Tahfidz Ar-Rahmah Boarding School: kelas, tahfidz, dan portal orang tua.'],
            'name' => ['type' => 'text', 'section' => 'Bagian atas', 'label' => 'Nama di bagian atas', 'default' => 'LMS Ar-Rahmah', 'required' => true],
            'nav_plates' => ['type' => 'text', 'section' => 'Bagian atas', 'label' => 'Menu ke daftar program', 'default' => 'Program', 'required' => true],
            'nav_about' => ['type' => 'text', 'section' => 'Bagian atas', 'label' => 'Menu ke bagian tentang', 'default' => 'Tentang', 'required' => true],
            'nav_contact' => ['type' => 'text', 'section' => 'Bagian atas', 'label' => 'Menu ke kontak', 'default' => 'Kontak', 'required' => true],
            'social_x' => ['type' => 'social', 'section' => 'Bagian atas', 'label' => 'Tautan X (kosongkan untuk menyembunyikan ikon)', 'default' => ''],
            'social_linkedin' => ['type' => 'social', 'section' => 'Bagian atas', 'label' => 'Tautan LinkedIn (kosongkan untuk menyembunyikan ikon)', 'default' => ''],
            'social_instagram' => ['type' => 'social', 'section' => 'Bagian atas', 'label' => 'Tautan Instagram (kosongkan untuk menyembunyikan ikon)', 'default' => ''],
            'kicker' => ['type' => 'text', 'section' => 'Buku sketsa', 'label' => 'Tulisan kecil di atas buku (singkat, agar muat di ponsel)', 'default' => 'Tahfidz / Kelas Digital / Portal Orang Tua'],
            'hint' => ['type' => 'text', 'section' => 'Buku sketsa', 'label' => 'Petunjuk di bawah buku', 'default' => 'Seret halaman untuk membalik · Seret kaca pembesar di atasnya'],
            'about_label' => ['type' => 'text', 'section' => 'Tentang', 'label' => 'Label bagian', 'default' => 'Tentang'],
            'bio_1' => ['type' => 'text', 'section' => 'Tentang', 'label' => 'Paragraf — bagian 1', 'default' => 'LMS Ar-Rahmah adalah ruang belajar digital Modern Tahfidz Ar-Rahmah Boarding School. Santri mengikuti kelas dan materi, mengerjakan kuis, serta melihat kemajuan hafalannya sendiri; ustadz dan ustadzah membimbing halaqoh tahfidz dan mencatat setiap setoran; orang tua memantau perkembangan anak dari rumah. Santri dan pengajar dapat '],
            'bio_link_1_label' => ['type' => 'text', 'section' => 'Tentang', 'label' => 'Tautan pertama — teks', 'default' => 'masuk dengan akun sekolah'],
            'bio_link_1_url' => ['type' => 'link', 'section' => 'Tentang', 'label' => 'Tautan pertama — alamat', 'default' => '/login'],
            'bio_2' => ['type' => 'text', 'section' => 'Tentang', 'label' => 'Paragraf — bagian 2', 'default' => '; akun dibuat oleh sekolah, jadi wali santri yang belum memiliki akun cukup '],
            'bio_link_2_label' => ['type' => 'text', 'section' => 'Tentang', 'label' => 'Tautan kedua — teks', 'default' => 'menghubungi kami'],
            'bio_link_2_url' => ['type' => 'link', 'section' => 'Tentang', 'label' => 'Tautan kedua — alamat', 'default' => '#contact'],
            'bio_3' => ['type' => 'text', 'section' => 'Tentang', 'label' => 'Paragraf — bagian 3', 'default' => '. Buku sketsa di atas merangkum keseharian belajar kami, satu halaman untuk setiap program — dibuka pelan-pelan, seperti membaca catatan di serambi pondok.'],
            'plates_label' => ['type' => 'text', 'section' => 'Halaman buku', 'label' => 'Label daftar halaman', 'default' => 'Program'],
        ];
        foreach (self::PLATES as $i => [$file, $title, $place]) {
            $n = $i + 1;
            $schema["plate_{$n}_title"] = ['type' => 'text', 'section' => 'Halaman buku', 'label' => "Halaman $n — judul", 'default' => $title, 'required' => true];
            $schema["plate_{$n}_place"] = ['type' => 'text', 'section' => 'Halaman buku', 'label' => "Halaman $n — keterangan", 'default' => $place];
            $schema["plate_{$n}_image"] = ['type' => 'image', 'section' => 'Halaman buku', 'label' => "Halaman $n — ilustrasi", 'default' => '', 'file' => $file];
        }

        return $schema + [
            'foot_text' => ['type' => 'text', 'section' => 'Kontak', 'label' => 'Tulisan kaki halaman', 'default' => 'Modern Tahfidz Ar-Rahmah Boarding School · LMS · '],
            'foot_link_label' => ['type' => 'text', 'section' => 'Kontak', 'label' => 'Tautan kontak — teks', 'default' => 'Masuk ke LMS'],
            'foot_link_url' => ['type' => 'link', 'section' => 'Kontak', 'label' => 'Tautan kontak — alamat (mis. mailto:info@sekolah.sch.id)', 'default' => '/login'],
            'login_label' => ['type' => 'text', 'section' => 'Akses LMS', 'label' => 'Tombol masuk (di bawah buku)', 'default' => 'Masuk ke LMS', 'required' => true],
            'install_label' => ['type' => 'text', 'section' => 'Akses LMS', 'label' => 'Tombol pasang aplikasi', 'default' => 'Pasang aplikasi', 'required' => true],
            'install_later' => ['type' => 'text', 'section' => 'Akses LMS', 'label' => 'Tombol tunda pemasangan', 'default' => 'Nanti', 'required' => true],
        ];
    }

    /** Validation rules for stored values, keyed by field. */
    public static function rules(): array
    {
        $rules = [];
        foreach (self::schema() as $key => $field) {
            $rules[$key] = match ($field['type']) {
                'link' => ['required', 'string', 'max:500', 'regex:'.self::LINK_PATTERN],
                'social' => ['nullable', 'string', 'max:500', 'url:https'],
                'image' => ['nullable', 'string', 'regex:~\A/storage/landing/[A-Za-z0-9_-]+\.(?:png|jpe?g|webp)\z~'],
                default => [! empty($field['required']) ? 'required' : 'nullable', 'string', 'max:2000'],
            };
        }

        return $rules;
    }

    public static function values(): array
    {
        $saved = json_decode(Setting::get(self::SETTING, '{}'), true) ?: [];
        $values = [];
        foreach (self::schema() as $key => $field) {
            $values[$key] = $saved[$key] ?? $field['default'];
        }

        return $values;
    }

    public static function save(array $values): void
    {
        $rules = array_map(fn ($rules) => ['sometimes', ...$rules], self::rules());
        $validated = Validator::make($values, $rules)->validate();
        $merged = array_replace(self::values(), array_map(fn ($value) => $value ?? '', $validated));
        Setting::set(self::SETTING, json_encode($merged, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    }

    /** The canonical document with saved content swapped into its authored anchors. */
    public static function render(): string
    {
        $html = file_get_contents(resource_path('threeui/public/landing-pages/meng-to-sketchbook.html'));
        if (hash('sha256', $html) !== self::SOURCE_HASH) {
            throw new \RuntimeException('Canonical sketchbook source checksum mismatch.');
        }
        $v = self::values();

        $pages = [];
        foreach (self::PLATES as $i => [$file]) {
            $n = $i + 1;
            $pages[] = [
                'url' => $v["plate_{$n}_image"] ? SketchbookSpread::url($v["plate_{$n}_image"]) : 'meng-to-sketchbook/'.$file,
                'title' => $v["plate_{$n}_title"],
                'place' => $v["plate_{$n}_place"],
            ];
        }

        $tools = self::span($html, '<div class="sb-tools"', '</div>');
        $replacements = [
            '<html lang="en">' => '<html lang="id">',
            '<title>Meng To</title>' => '<title>'.e($v['title']).'</title>',
            '<meta name="description" content="designer, creator, AI educator — Singapore">' => '<meta name="description" content="'.e($v['description']).'">',
            '<a class="name" href="#">Meng To</a>' => '<a class="name" href="#">'.e($v['name']).'</a>',
            '<a href="#plates">Journal</a>' => '<a href="#plates">'.e($v['nav_plates']).'</a>',
            '<a href="#about">About</a>' => '<a href="#about">'.e($v['nav_about']).'</a>',
            '<a href="#contact">Contact</a>' => '<a href="#contact">'.e($v['nav_contact']).'</a>',
            '<p class="hero-kicker">Designer / Creator / AI Educator / Founder @ Singapore</p>' => '<p class="hero-kicker">'.e($v['kicker']).'</p>',
            '<p class="sb-hint" id="sbHint">Drag the page to turn · Drag the glass across it</p>' => '<p class="sb-hint" id="sbHint">'.e($v['hint']).'</p>',
            // The LMS entry sits beside the view controls under the book, drawn with the page's own tokens.
            $tools => '<div class="sb-actions">'.strtr($tools, [
                'aria-label="view controls"' => 'aria-label="kontrol tampilan"',
                'aria-label="zoom out"' => 'aria-label="perkecil"',
                'aria-label="zoom in"' => 'aria-label="perbesar"',
                'aria-label="magnifier"' => 'aria-label="kaca pembesar"',
            ])
                .'<a class="sb-login" href="/login">'.e($v['login_label']).'<svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 10h11M11 5.5 15.5 10 11 14.5"/></svg></a></div>',
            '</style>' => '.sb-actions{display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:12px}'
                .'.sb-login{display:inline-flex;align-items:center;gap:10px;height:40px;box-sizing:border-box;padding:0 18px 1px 22px;'
                .'border:1px solid var(--hairline);border-radius:999px;background:rgba(250,246,238,.62);'
                .'-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);color:var(--ink);'
                .'font-family:var(--display);font-size:19px;line-height:1;letter-spacing:.02em;'
                .'box-shadow:0 1px 2px rgba(58,44,26,.10),0 10px 22px rgba(58,44,26,.08);'
                .'transition:background-color .2s ease,border-color .2s ease,color .2s ease}'
                .'.sb-login svg{width:16px;height:16px;flex:none;transition:transform .25s ease}'
                .'.sb-login:hover{background:rgba(255,252,244,.92);border-color:rgba(154,106,62,.45);color:var(--earth)}'
                .'.sb-login:hover svg{transform:translateX(3px)}'
                .'.sb-login:focus-visible{outline:2px solid var(--earth);outline-offset:3px}'
                .'@media (prefers-reduced-motion:reduce){.sb-login svg{transition:none}}'
                .'@media (max-width:640px){.sb-login{font-size:17px;height:38px;padding:0 15px 1px 19px}}</style>',
            '<p class="section-label">About</p>' => '<p class="section-label">'.e($v['about_label']).'</p>',
            '<p class="section-label">Plates</p>' => '<p class="section-label">'.e($v['plates_label']).'</p>',
            self::span($html, '<p class="bio">', '</p>') => '<p class="bio">'.e($v['bio_1']).self::link($v['bio_link_1_label'], $v['bio_link_1_url'])
                .e($v['bio_2']).self::link($v['bio_link_2_label'], $v['bio_link_2_url']).e($v['bio_3']).'</p>',
            self::span($html, '<p class="foot" id="contact">', '</p>') => '<p class="foot" id="contact">'.e($v['foot_text']).self::link($v['foot_link_label'], $v['foot_link_url']).'</p>',
            self::span($html, 'const PAGES=[', 'PAGES.forEach(p=>p.url=DIR+p.file);') => 'const PAGES='.json_encode($pages, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR).';',
            // Accessibility labels, translated with the rest of the page.
            'id="sbLeft" aria-label="previous page"' => 'id="sbLeft" aria-label="halaman sebelumnya"',
            'id="sbRight" aria-label="next page"' => 'id="sbRight" aria-label="halaman berikutnya"',
            "a.setAttribute('aria-label','previous page');b.setAttribute('aria-label','next page');" => "a.setAttribute('aria-label','halaman sebelumnya');b.setAttribute('aria-label','halaman berikutnya');",
            'aria-label="scroll to about"' => 'aria-label="gulir ke bagian tentang"',
        ];
        foreach (self::SOCIALS as $key => $label) {
            $element = self::span($html, '<a href="#" class="icon-btn" aria-label="'.$label.'">', '</a>');
            $replacements[$element] = $v[$key] === '' ? '' : str_replace('href="#"', 'href="'.e($v[$key]).'" target="_blank" rel="noopener noreferrer"', $element);
        }
        foreach (array_keys($replacements) as $source) {
            if (substr_count($html, $source) !== 1) {
                throw new \RuntimeException('Sketchbook content anchor mismatch: '.$source);
            }
        }
        // strtr replaces in one pass over the original, so saved text can never be matched as an anchor.
        $html = strtr($html, $replacements);

        // Authenticate outside the sandbox instead of rendering the LMS inside the page iframe.
        $bridge = '<script>document.addEventListener("click",function(event){const a=event.target.closest("a");if(a&&["/login","/register"].includes(a.getAttribute("href"))){event.preventDefault();parent.postMessage({type:"arrahmah-navigate",path:a.getAttribute("href")},location.origin);}},true);</script>';

        return str_replace('</body>', $bridge.'</body>', $html);
    }

    private static function link(string $label, string $url): string
    {
        if ($label === '') {
            return '';
        }
        $external = str_starts_with($url, 'https://') || str_starts_with($url, 'mailto:') ? ' target="_blank" rel="noopener noreferrer"' : '';

        return '<a class="bio-link" href="'.e($url).'"'.$external.'>'.e($label).'</a>';
    }

    /** The authored text from $open through the first $close after it; $open must be unique. */
    private static function span(string $html, string $open, string $close): string
    {
        $start = strpos($html, $open);
        $end = $start === false ? false : strpos($html, $close, $start);
        if ($end === false || substr_count($html, $open) !== 1) {
            throw new \RuntimeException('Sketchbook content anchor mismatch: '.$open);
        }

        return substr($html, $start, $end + strlen($close) - $start);
    }
}
