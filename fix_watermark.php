<?php
/**
 * Re-apply watermark with "Kegalle.com" on all listing images.
 * Upload to public_html/, run in browser, then DELETE.
 */

$lockFile = __DIR__ . '/fix_watermark.lock';
$guard = 'K3GALL3_FW_2026';

if (($_GET['key'] ?? '') !== $guard) { http_response_code(403); die('Forbidden'); }
if (file_exists($lockFile)) { die('Already executed. Delete fix_watermark.lock to re-run.'); }

set_time_limit(600);

$storageDir = dirname(__DIR__) . '/app_core/storage/app/public/listings';
if (!is_dir($storageDir)) { die('Directory not found: ' . $storageDir); }

$text = 'Kegalle.com';
$done = 0;
$errors = 0;

echo "<pre>Fix Watermark → Kegalle.com\n===========================\n\n";

function loadImg($path) {
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    return match ($ext) {
        'jpg', 'jpeg' => @imagecreatefromjpeg($path),
        'png' => @imagecreatefrompng($path),
        'webp' => @imagecreatefromwebp($path),
        default => null,
    };
}

function applyWatermark($absolutePath, $text) {
    $image = loadImg($absolutePath);
    if (!$image) return false;

    $w = imagesx($image);
    $h = imagesy($image);

    $fontFile = null;
    foreach ([
        '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
        '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
        '/usr/share/fonts/truetype/freefont/FreeSansBold.ttf',
    ] as $f) {
        if (file_exists($f)) { $fontFile = $f; break; }
    }

    if ($fontFile) {
        $fontSize = max(12, (int)($w * 0.03));
        $bbox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textW = abs($bbox[2] - $bbox[0]);
        $textH = abs($bbox[7] - $bbox[1]);
        $padX = (int)($fontSize * 0.8);
        $padY = (int)($fontSize * 0.5);
        $margin = (int)($fontSize * 0.6);

        // Cover old watermark area (slightly larger to be safe)
        $coverX1 = $w - $textW - $padX*2 - $margin - 40;
        $coverY1 = $h - $textH - $padY*2 - $margin - 10;
        $coverX2 = $w;
        $coverY2 = $h;

        // Sample background color from nearby area
        $sampleX = max(0, $coverX1 - 5);
        $sampleY = max(0, $coverY1 - 5);
        $sampleColor = imagecolorat($image, $sampleX, $sampleY);

        // First paint over old watermark with sampled color
        imagefilledrectangle($image, $coverX1, $coverY1, $coverX2, $coverY2, $sampleColor);

        // Now apply new watermark
        $bgX1 = $w - $textW - $padX*2 - $margin;
        $bgY1 = $h - $textH - $padY*2 - $margin;

        $bgColor = imagecolorallocatealpha($image, 0, 0, 0, 60);
        imagefilledrectangle($image, $bgX1, $bgY1, $w - $margin, $h - $margin, $bgColor);

        $white = imagecolorallocate($image, 255, 255, 255);
        imagettftext($image, $fontSize, 0, $bgX1 + $padX, $h - $margin - $padY, $white, $fontFile, $text);
    } else {
        $font = ($w > 600) ? 5 : (($w > 300) ? 4 : 3);
        $charW = imagefontwidth($font);
        $charH = imagefontheight($font);
        $textW = $charW * strlen($text);
        $padX = 8; $padY = 4; $margin = 8;

        $coverX1 = $w - $textW - $padX*2 - $margin - 40;
        $coverY1 = $h - $charH - $padY*2 - $margin - 10;
        $sampleX = max(0, $coverX1 - 5);
        $sampleY = max(0, $coverY1 - 5);
        $sampleColor = imagecolorat($image, $sampleX, $sampleY);
        imagefilledrectangle($image, $coverX1, $coverY1, $w, $h, $sampleColor);

        $bgX1 = $w - $textW - $padX*2 - $margin;
        $bgY1 = $h - $charH - $padY*2 - $margin;

        $bgColor = imagecolorallocatealpha($image, 0, 0, 0, 60);
        imagefilledrectangle($image, $bgX1, $bgY1, $w - $margin, $h - $margin, $bgColor);

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

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($storageDir, RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($files as $file) {
    $path = $file->getPathname();
    $ext = strtolower($file->getExtension());
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) continue;

    if (applyWatermark($path, $text)) {
        echo "OK: " . basename($path) . "\n";
        $done++;
    } else {
        echo "FAIL: " . basename($path) . "\n";
        $errors++;
    }
}

echo "\nDone! Updated: $done | Errors: $errors\n";
file_put_contents($lockFile, date('Y-m-d H:i:s'));
echo "\n⚠️ DELETE fix_watermark.php and fix_watermark.lock now!\n</pre>";
