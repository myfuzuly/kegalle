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

    public static function addWatermark(string $absolutePath, string $text = 'KEGALLE.COM'): bool
    {
        $image = self::loadImage($absolutePath);
        if (!$image) {
            return false;
        }

        $w = imagesx($image);
        $h = imagesy($image);

        imagealphablending($image, true);

        $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
        $wmFile = $docRoot . '/images/watermark2.png';
        if (file_exists($wmFile)) {
            $wm   = @imagecreatefrompng($wmFile);
            if ($wm) {
                $wmW  = imagesx($wm);
                $wmH  = imagesy($wm);
                // Scale watermark to 35% of the base image width
                $dstW = (int) round($w * 0.35);
                $dstH = (int) round($wmH * ($dstW / $wmW));
                $dstX = (int) (($w - $dstW) / 2);
                $dstY = (int) (($h - $dstH) / 2);
                $scaled = imagecreatetruecolor($dstW, $dstH);
                imagealphablending($scaled, false);
                imagesavealpha($scaled, true);
                imagecopyresampled($scaled, $wm, 0, 0, 0, 0, $dstW, $dstH, $wmW, $wmH);
                imagedestroy($wm);
                imagealphablending($image, true);
                imagecopy($image, $scaled, $dstX, $dstY, 0, 0, $dstW, $dstH);
                imagedestroy($scaled);
            }
        } else {
            // Fallback: text watermark
            $fontFile = null;
            foreach ([
                '/usr/share/fonts/google-droid/DroidSans-Bold.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
                '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
                '/usr/share/fonts/truetype/freefont/FreeSansBold.ttf',
            ] as $f) {
                if (file_exists($f)) { $fontFile = $f; break; }
            }
            if ($fontFile) {
                $fontSize = max(18, (int) round($w * 0.048));
                $bbox  = imagettfbbox($fontSize, 0, $fontFile, $text);
                $textW = abs($bbox[2] - $bbox[0]);
                $textH = abs($bbox[7] - $bbox[1]);
                $x     = (int) (($w - $textW) / 2);
                $y     = (int) (($h + $textH) / 2);
                $shadow = imagecolorallocatealpha($image, 0, 0, 0, 70);
                imagettftext($image, $fontSize, 0, $x + 2, $y + 2, $shadow, $fontFile, $text);
                $white = imagecolorallocatealpha($image, 255, 255, 255, 64);
                imagettftext($image, $fontSize, 0, $x, $y, $white, $fontFile, $text);
            }
        }

        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        $result = match ($ext) {
            'jpg', 'jpeg' => imagejpeg($image, $absolutePath, 90),
            'png'         => imagepng($image, $absolutePath, 8),
            'webp'        => imagewebp($image, $absolutePath, 80),
            default       => false,
        };
        imagedestroy($image);
        return $result;
    }

    public static function addBannerWatermark(string $absolutePath, string $text = 'kegalle.com'): bool
    {
        $image = self::loadImage($absolutePath);
        if (!$image) {
            return false;
        }

        $w      = imagesx($image);
        $h      = imagesy($image);
        $margin = max(8, (int) round($w * 0.012));

        imagealphablending($image, true);

        $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
        $wmFile = $docRoot . '/images/watermark2.png';
        if (file_exists($wmFile)) {
            $wm = @imagecreatefrompng($wmFile);
            if ($wm) {
                $wmW  = imagesx($wm);
                $wmH  = imagesy($wm);
                // Scale watermark to 20% of base image width, bottom-right
                $dstW = (int) round($w * 0.20);
                $dstH = (int) round($wmH * ($dstW / $wmW));
                $dstX = $w - $dstW - $margin;
                $dstY = $h - $dstH - $margin;
                $scaled = imagecreatetruecolor($dstW, $dstH);
                imagealphablending($scaled, false);
                imagesavealpha($scaled, true);
                imagecopyresampled($scaled, $wm, 0, 0, 0, 0, $dstW, $dstH, $wmW, $wmH);
                imagedestroy($wm);
                imagealphablending($image, true);
                imagecopy($image, $scaled, $dstX, $dstY, 0, 0, $dstW, $dstH);
                imagedestroy($scaled);
            }
        } else {
            // Fallback: text watermark
            $fontFile = null;
            foreach ([
                '/usr/share/fonts/google-droid/DroidSans-Bold.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
                '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
                '/usr/share/fonts/truetype/freefont/FreeSansBold.ttf',
            ] as $f) {
                if (file_exists($f)) { $fontFile = $f; break; }
            }
            if ($fontFile) {
                $fontSize = max(11, (int) round($w * 0.018));
                $bbox  = imagettfbbox($fontSize, 0, $fontFile, $text);
                $textW = abs($bbox[2] - $bbox[0]);
                $x     = $w - $textW - $margin;
                $y     = $h - $margin;
                $shadow = imagecolorallocatealpha($image, 0, 0, 0, 90);
                imagettftext($image, $fontSize, 0, $x + 1, $y + 1, $shadow, $fontFile, $text);
                $white = imagecolorallocatealpha($image, 255, 255, 255, 55);
                imagettftext($image, $fontSize, 0, $x, $y, $white, $fontFile, $text);
            }
        }

        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        $result = match ($ext) {
            'jpg', 'jpeg' => imagejpeg($image, $absolutePath, 90),
            'png'         => imagepng($image, $absolutePath, 8),
            'webp'        => imagewebp($image, $absolutePath, 80),
            default       => false,
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
     * Crop image to a fixed aspect ratio at the given vertical offset (0–100%).
     * Used for store banners so the user-dragged position is baked in.
     */
    public static function cropToAspect(string $absolutePath, int $targetW, int $targetH, int $posYPct = 50): bool
    {
        $image = self::loadImage($absolutePath);
        if (!$image) return false;

        $srcW = imagesx($image);
        $srcH = imagesy($image);

        // Determine crop box that fills targetW×targetH from the source
        $srcAspect = $srcW / $srcH;
        $tgtAspect = $targetW / $targetH;

        if ($srcAspect > $tgtAspect) {
            // Source is wider — crop width
            $cropH = $srcH;
            $cropW = (int) round($srcH * $tgtAspect);
            $cropX = (int) round(($srcW - $cropW) / 2);
            $cropY = 0;
        } else {
            // Source is taller — crop height at posYPct
            $cropW = $srcW;
            $cropH = (int) round($srcW / $tgtAspect);
            $cropX = 0;
            $maxY  = $srcH - $cropH;
            $cropY = (int) round($maxY * $posYPct / 100);
        }

        $out = imagecreatetruecolor($targetW, $targetH);
        imagecopyresampled($out, $image, 0, 0, $cropX, $cropY, $targetW, $targetH, $cropW, $cropH);
        imagedestroy($image);

        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        $result = match ($ext) {
            'jpg', 'jpeg' => imagejpeg($out, $absolutePath, 90),
            'png'         => imagepng($out, $absolutePath, 8),
            'webp'        => imagewebp($out, $absolutePath, 85),
            default       => false,
        };
        imagedestroy($out);
        return (bool) $result;
    }

    /**
     * Watermark + convert an uploaded image to WebP, delete the original,
     * and return the relative path that should be stored in the database.
     */
    public static function finalize(string $relativePath, bool|string $watermark = true, int $maxDim = 1400): string
    {
        $absolutePath = storage_path('app/public/' . $relativePath);
        if (!file_exists($absolutePath)) {
            return $relativePath;
        }

        self::resizeDown($absolutePath, $maxDim);

        if ($watermark === 'banner') {
            self::addBannerWatermark($absolutePath);
        } elseif ($watermark) {
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
