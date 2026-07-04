<?php

namespace App\Helpers;

class ImageHelper
{
    public static function webpPath(string $originalPath): string
    {
        return preg_replace('/\.(jpe?g|png)$/i', '.webp', $originalPath);
    }

    public static function loadImage(string $absolutePath): ?\GdImage
    {
        if (!file_exists($absolutePath)) {
            return null;
        }

        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        $image = match ($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($absolutePath),
            'png' => @imagecreatefrompng($absolutePath),
            'webp' => @imageCreateFromWebp($absolutePath),
            default => null,
        };

        if ($image && $ext === 'png') {
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        }

        return $image ?: null;
    }

    public static function addWatermark(string $absolutePath, string $text = 'Kegalle.com'): bool
    {
        $image = self::loadImage($absolutePath);
        if (!$image) {
            return false;
        }

        $w = imagesx($image);
        $h = imagesy($image);

        $fontSize = max(12, (int) ($w * 0.03));

        $fontFile = null;
        $possibleFonts = [
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
            '/usr/share/fonts/truetype/freefont/FreeSansBold.ttf',
        ];
        foreach ($possibleFonts as $f) {
            if (file_exists($f)) {
                $fontFile = $f;
                break;
            }
        }

        if ($fontFile) {
            $bbox = imagettfbbox($fontSize, 0, $fontFile, $text);
            $textW = abs($bbox[2] - $bbox[0]);
            $textH = abs($bbox[7] - $bbox[1]);

            $padX = (int) ($fontSize * 0.8);
            $padY = (int) ($fontSize * 0.5);
            $margin = (int) ($fontSize * 0.6);

            $bgX1 = $w - $textW - $padX * 2 - $margin;
            $bgY1 = $h - $textH - $padY * 2 - $margin;
            $bgX2 = $w - $margin;
            $bgY2 = $h - $margin;

            $bgColor = imagecolorallocatealpha($image, 0, 0, 0, 60);
            imagefilledrectangle($image, $bgX1, $bgY1, $bgX2, $bgY2, $bgColor);

            $white = imagecolorallocate($image, 255, 255, 255);
            $textX = $bgX1 + $padX;
            $textY = $bgY2 - $padY;
            imagettftext($image, $fontSize, 0, $textX, $textY, $white, $fontFile, $text);
        } else {
            $builtinFontSize = ($w > 600) ? 5 : (($w > 300) ? 4 : 3);
            $font = $builtinFontSize;
            $charW = imagefontwidth($font);
            $charH = imagefontheight($font);
            $textW = $charW * strlen($text);

            $padX = 8;
            $padY = 4;
            $margin = 8;

            $bgX1 = $w - $textW - $padX * 2 - $margin;
            $bgY1 = $h - $charH - $padY * 2 - $margin;
            $bgX2 = $w - $margin;
            $bgY2 = $h - $margin;

            $bgColor = imagecolorallocatealpha($image, 0, 0, 0, 60);
            imagefilledrectangle($image, $bgX1, $bgY1, $bgX2, $bgY2, $bgColor);

            $white = imagecolorallocate($image, 255, 255, 255);
            imagestring($image, $font, $bgX1 + $padX, $bgY1 + $padY, $text, $white);
        }

        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        $result = match ($ext) {
            'jpg', 'jpeg' => imagejpeg($image, $absolutePath, 90),
            'png' => imagepng($image, $absolutePath, 8),
            'webp' => imagewebp($image, $absolutePath, 80),
            default => false,
        };

        imagedestroy($image);
        return $result;
    }

    public static function convertToWebp(string $absolutePath, int $quality = 80): ?string
    {
        $image = self::loadImage($absolutePath);
        if (!$image) {
            return null;
        }

        $webpPath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $absolutePath);
        $success = imagewebp($image, $webpPath, $quality);
        imagedestroy($image);

        return $success ? $webpPath : null;
    }

    /**
     * Downscale an image in place so its longest side is at most $maxDim px.
     */
    public static function resizeDown(string $absolutePath, int $maxDim = 1400): bool
    {
        $image = self::loadImage($absolutePath);
        if (!$image) {
            return false;
        }

        $w = imagesx($image);
        $h = imagesy($image);
        $longest = max($w, $h);
        if ($longest <= $maxDim) {
            imagedestroy($image);
            return true;
        }

        $scale = $maxDim / $longest;
        $newW = (int) round($w * $scale);
        $newH = (int) round($h * $scale);
        $resized = imagecreatetruecolor($newW, $newH);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newW, $newH, $w, $h);
        imagedestroy($image);

        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        $result = match ($ext) {
            'jpg', 'jpeg' => imagejpeg($resized, $absolutePath, 88),
            'png' => imagepng($resized, $absolutePath, 8),
            'webp' => imagewebp($resized, $absolutePath, 80),
            default => false,
        };
        imagedestroy($resized);

        return (bool) $result;
    }

    /**
     * Watermark + convert an uploaded image to WebP, delete the original,
     * and return the relative path that should be stored in the database.
     */
    public static function finalize(string $relativePath, bool $watermark = true, int $maxDim = 1400): string
    {
        $absolutePath = storage_path('app/public/' . $relativePath);
        if (!file_exists($absolutePath)) {
            return $relativePath;
        }

        self::resizeDown($absolutePath, $maxDim);

        if ($watermark) {
            self::addWatermark($absolutePath);
        }

        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $webpAbsolute = self::convertToWebp($absolutePath);
            if ($webpAbsolute) {
                @unlink($absolutePath);
                return self::webpPath($relativePath);
            }
        }

        return $relativePath;
    }

    public static function seoFilename(string $slug, int $index, string $extension): string
    {
        $suffix = $index > 0 ? '-' . ($index + 1) : '';
        return $slug . $suffix . '-in-kegalle.' . $extension;
    }
}
