<?php

namespace App\Support;

use GdImage;
use Illuminate\Support\Facades\Storage;

/**
 * Turns an ordinary photo into a sketchbook spread the authored page can turn.
 *
 * The page expects every plate to be a 1760×1240 transparent PNG of an open book
 * (paper edges at 5.1%/94.9% × 21.8%/78.2%, gutter in the middle). An uploaded
 * photo is laid onto the paper of the authored Merlion spread instead of replacing it.
 */
class SketchbookSpread
{
    private const WIDTH = 1760;

    private const HEIGHT = 1240;

    private const BASE = 'landing-pages/meng-to-sketchbook/merlion.png';

    /** Each page of the base spread, slightly past its edges; transparent corners are restored afterwards. */
    private const PAGES = [[96, 280, 848, 962], [916, 280, 1690, 962]];

    /** A blank stretch of paper on the base spread, used to wipe its drawing. */
    private const BLANK = [150, 295, 690, 340];

    /** Where the photo sits: across both pages, inside a paper margin. */
    private const PHOTO = [150, 322, 1610, 930];

    private const GUTTER_X = 880;

    /** Bump to regenerate every cached spread after changing the composition. */
    private const VERSION = 'v1';

    /**
     * URL of the spread for a stored plate upload, composed once and cached beside it.
     * The original upload is kept, so any photo saved earlier also fits the book.
     */
    public static function url(string $upload): string
    {
        $disk = Storage::disk('public');
        $source = 'landing/'.basename($upload);
        $spread = 'landing/spread-'.self::VERSION.'-'.pathinfo($source, PATHINFO_FILENAME).'.png';
        if (! $disk->exists($spread)) {
            if (! $disk->exists($source)) {
                return $upload;
            }
            try {
                $disk->put($spread, self::fromUpload($disk->path($source)));
            } catch (\Throwable $e) {
                report($e);

                return $upload;
            }
        }

        return '/storage/'.$spread;
    }

    /** PNG bytes for an uploaded image: as-is if already a spread, otherwise composed. */
    public static function fromUpload(string $path): string
    {
        // ponytail: GD holds decoded pixels in memory (~4 bytes/px); a 48 MP phone photo needs ~200 MB.
        if (self::bytes(ini_get('memory_limit')) < 512 * 1024 * 1024) {
            ini_set('memory_limit', '512M');
        }
        $source = imagecreatefromstring(file_get_contents($path));
        if ($source === false) {
            throw new \RuntimeException('Gambar tidak dapat dibaca.');
        }
        imagepalettetotruecolor($source);

        return self::png(self::isSpread($source) ? $source : self::compose($source));
    }

    private static function isSpread(GdImage $image): bool
    {
        $ratio = imagesx($image) / imagesy($image);
        $cornerAlpha = (imagecolorat($image, 0, 0) >> 24) & 127;

        return abs($ratio - self::WIDTH / self::HEIGHT) < 0.04 && $cornerAlpha > 100;
    }

    private static function compose(GdImage $photo): GdImage
    {
        $original = self::base();
        $book = self::base();

        // Wipe the authored drawing with the book's own blank paper, mirrored to hide tile seams.
        [$bx, $by, $bw, $bh] = self::BLANK;
        foreach (self::PAGES as [$x0, $y0, $x1, $y1]) {
            $patch = imagecreatetruecolor($x1 - $x0, $y1 - $y0);
            for ($y = 0, $row = 0; $y < $y1 - $y0; $y += $bh, $row++) {
                for ($x = 0, $col = 0; $x < $x1 - $x0; $x += $bw, $col++) {
                    $piece = imagecreatetruecolor($bw, $bh);
                    imagecopy($piece, $original, 0, 0, $bx, $by, $bw, $bh);
                    if ($col % 2) {
                        imageflip($piece, IMG_FLIP_HORIZONTAL);
                    }
                    if ($row % 2) {
                        imageflip($piece, IMG_FLIP_VERTICAL);
                    }
                    imagecopy($patch, $piece, $x, $y, 0, 0, $bw, $bh);
                }
            }
            self::fadeEdges($patch, 0, 18, 0);
            imagecopy($book, $patch, $x0, $y0, 0, 0, imagesx($patch), imagesy($patch));
        }
        // The patches overshoot the page outline; put the rounded corners and edges back.
        imagealphablending($book, false);
        foreach (self::PAGES as [$x0, $y0, $x1, $y1]) {
            for ($y = $y0; $y < $y1; $y++) {
                for ($x = $x0; $x < $x1; $x++) {
                    $pixel = imagecolorat($original, $x, $y);
                    if (($pixel >> 24) & 127) {
                        imagesetpixel($book, $x, $y, $pixel);
                    }
                }
            }
        }
        imagealphablending($book, true);

        [$px0, $py0, $px1, $py1] = self::PHOTO;
        $print = self::inkOnPaper(self::cover($photo, $px1 - $px0, $py1 - $py0), self::GUTTER_X - $px0);
        imagelayereffect($book, IMG_EFFECT_MULTIPLY);
        imagecopy($book, $print, $px0, $py0, 0, 0, imagesx($print), imagesy($print));
        imagelayereffect($book, IMG_EFFECT_NORMAL);

        return $book;
    }

    private static function base(): GdImage
    {
        $book = imagecreatefrompng(public_path(self::BASE));
        imagepalettetotruecolor($book);
        imagealphablending($book, true);
        imagesavealpha($book, true);

        return $book;
    }

    /** Centre-crop to fill the target box. */
    private static function cover(GdImage $photo, int $width, int $height): GdImage
    {
        $sw = imagesx($photo);
        $sh = imagesy($photo);
        $scale = max($width / $sw, $height / $sh);
        $cw = (int) round($width / $scale);
        $ch = (int) round($height / $scale);
        $out = imagecreatetruecolor($width, $height);
        imagecopyresampled($out, $photo, 0, 0, intdiv($sw - $cw, 2), intdiv($sh - $ch, 2), $width, $height, $cw, $ch);

        return $out;
    }

    /** Soften the photo like pigment on paper, shade the fold and dissolve the edges. */
    private static function inkOnPaper(GdImage $print, int $gutter): GdImage
    {
        $w = imagesx($print);
        $h = imagesy($print);

        $grey = imagecreatetruecolor($w, $h);
        imagecopy($grey, $print, 0, 0, 0, 0, $w, $h);
        imagefilter($grey, IMG_FILTER_GRAYSCALE);
        imagecopymerge($print, $grey, 0, 0, 0, 0, $w, $h, 22);
        imagefilter($print, IMG_FILTER_CONTRAST, 8);
        imagefilter($print, IMG_FILTER_COLORIZE, 10, 4, -8);

        // The page bends into the gutter, so the photo darkens there as the paper does.
        for ($x = max(0, $gutter - 56); $x < min($w, $gutter + 56); $x++) {
            $shade = (int) round(127 * 0.3 * (1 - abs($x - $gutter) / 56) ** 2);
            imageline($print, $x, 0, $x, $h - 1, imagecolorallocatealpha($print, 0, 0, 0, 127 - $shade));
        }

        self::fadeEdges($print, 16, 34, 1);

        return $print;
    }

    /**
     * Fade an image's border to transparent. $inset is fully clear, $feather is the ramp,
     * and $wobble > 0 bends the edge like a hand-laid wash instead of a straight crop.
     */
    private static function fadeEdges(GdImage $image, int $inset, int $feather, int $wobble): void
    {
        $w = imagesx($image);
        $h = imagesy($image);
        $wave = function (int $i, float $phase) use ($wobble): float {
            return $wobble * (9 * sin($i / 37 + $phase) + 5 * sin($i / 13 + $phase * 2) + 2 * sin($i / 5 + $phase * 3));
        };
        imagealphablending($image, false);
        imagesavealpha($image, true);
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $edge = min($y - $wave($x, 0.7), $h - 1 - $y - $wave($x, 2.9), $x - $wave($y, 4.1), $w - 1 - $x - $wave($y, 5.3));
                if ($edge >= $inset + $feather) {
                    continue;
                }
                $rgba = imagecolorat($image, $x, $y);
                $alpha = ($rgba >> 24) & 127;
                $visible = max(0.0, min(1.0, ($edge - $inset) / $feather));
                imagesetpixel($image, $x, $y, ($rgba & 0xFFFFFF) | ((int) round(127 - (127 - $alpha) * $visible) << 24));
            }
        }
    }

    private static function bytes(string $limit): int
    {
        return $limit === '-1' ? PHP_INT_MAX : (int) $limit * (['k' => 1024, 'm' => 1024 ** 2, 'g' => 1024 ** 3][strtolower(substr($limit, -1))] ?? 1);
    }

    private static function png(GdImage $image): string
    {
        imagesavealpha($image, true);
        ob_start();
        imagepng($image, null, 6);

        return (string) ob_get_clean();
    }
}
