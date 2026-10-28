<?php

namespace App\Services;

use App\Models\Passage;

// Draws the 1200x630 preview image for a passage with PHP's GD extension (needs FreeType).
// Images are cached in storage/app/cards and re-drawn automatically if the passage text,
// voice, or badge changes.
class ShareCard
{
    private const W = 1200;
    private const H = 630;
    private const BODY_X = 60;
    private const BODY_Y = 190;
    private const BODY_W = 1080;
    private const BODY_H = 360;
    private const MAX_SIZE = 46;
    private const MIN_SIZE = 24;

    public static function available(): bool
    {
        return extension_loaded('gd')
            && function_exists('imagettftext')
            && is_file(self::font())
            && is_file(self::font(true));
    }

    private static function font(bool $bold = false): string
    {
        return resource_path('fonts/' . ($bold ? 'DejaVuSerif-Bold.ttf' : 'DejaVuSerif.ttf'));
    }

    // Path to the PNG for this passage, drawing it first if needed.
    public function path(Passage $p): string
    {
        $dir = storage_path('app/cards');

        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $attr = $p->attribution;
        $who = $attr->person;
        $key = md5(implode('|', [$who->name, $who->handle, $who->kind, $p->reference, $p->body, $attr->basis, config('app.name')]));
        $file = "{$dir}/{$p->id}-{$key}.png";

        if (! is_file($file)) {
            $this->render($p, $file);
        }

        return $file;
    }

    private function render(Passage $p, string $file): void
    {
        $attr = $p->attribution;
        $who = $attr->person;
        $regular = self::font();
        $bold = self::font(true);

        $img = imagecreatetruecolor(self::W, self::H);

        $bg = $this->color($img, '#0f1419');
        $fg = $this->color($img, '#e7e9ea');
        $muted = $this->color($img, '#71767b');
        $kindColor = ['author' => '#2f6f4f', 'speaker' => '#8a5a1f', 'anonymous' => '#6b7a8f'][$who->kind] ?? '#6b7a8f';
        $accent = $this->color($img, $kindColor);

        imagefilledrectangle($img, 0, 0, self::W, self::H, $bg);
        imagefilledrectangle($img, 0, 0, 12, self::H, $accent);

        // Avatar with the voice's initial.
        imagefilledellipse($img, 100, 100, 88, 88, $accent);
        $this->centered($img, mb_strtoupper(mb_substr($who->name, 0, 1)), 40, $bold, 100, 100, $this->color($img, '#ffffff'));

        // Name, handle, and reference.
        imagettftext($img, 34, 0, 170, 96, $fg, $bold, $who->name);
        imagettftext($img, 24, 0, 170, 138, $muted, $regular, $who->handle);

        $ref = $p->reference;
        $refWidth = $this->width($ref, 28, $regular);
        imagettftext($img, 28, 0, self::W - 60 - $refWidth, 96, $muted, $regular, $ref);

        // Body text, as large as fits.
        $body = trim($p->body);
        $size = self::MIN_SIZE;
        $lines = [];
        $fits = false;

        foreach (range(self::MAX_SIZE, self::MIN_SIZE, -2) as $try) {
            $size = $try;
            $lines = $this->wrap($body, $size, $regular, self::BODY_W);

            if (count($lines) * $size * 1.4 <= self::BODY_H) {
                $fits = true;
                break;
            }
        }

        if (! $fits) {
            $max = max(1, (int) floor(self::BODY_H / ($size * 1.4)));
            $lines = array_slice($lines, 0, $max);
            $lines[$max - 1] = rtrim($lines[$max - 1], " ,.;:") . '…';
        }

        foreach ($lines as $i => $line) {
            $y = (int) round(self::BODY_Y + $size + $i * $size * 1.4);
            imagettftext($img, $size, 0, self::BODY_X, $y, $fg, $regular, $line);
        }

        // Footer: site name and the attribution badge.
        imagettftext($img, 24, 0, self::BODY_X, 595, $muted, $regular, (string) config('app.name'));
        $badge = $attr->basisLabel();
        $badgeWidth = $this->width($badge, 22, $regular);
        imagettftext($img, 22, 0, self::W - 60 - $badgeWidth, 595, $muted, $regular, $badge);

        imagepng($img, $file);
    }

    private function color($img, string $hex): int
    {
        [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');

        return imagecolorallocate($img, $r, $g, $b);
    }

    private function width(string $text, int $size, string $font): int
    {
        $box = imagettfbbox($size, 0, $font, $text);

        return (int) abs($box[2] - $box[0]);
    }

    private function centered($img, string $text, int $size, string $font, int $cx, int $cy, int $color): void
    {
        $b = imagettfbbox($size, 0, $font, $text);
        $x = (int) round($cx - ($b[2] + $b[0]) / 2);
        $y = (int) round($cy - ($b[1] + $b[7]) / 2);

        imagettftext($img, $size, 0, $x, $y, $color, $font, $text);
    }

    private function wrap(string $text, int $size, string $font, int $maxWidth): array
    {
        $lines = [];
        $line = '';

        foreach (preg_split('/\s+/u', $text) as $word) {
            $try = $line === '' ? $word : "{$line} {$word}";

            if ($line === '' || $this->width($try, $size, $font) <= $maxWidth) {
                $line = $try;
            } else {
                $lines[] = $line;
                $line = $word;
            }
        }

        if ($line !== '') {
            $lines[] = $line;
        }

        return $lines;
    }
}
