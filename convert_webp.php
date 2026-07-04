<?php
/**
 * One-time WebP converter for existing listing images.
 * Upload to public_html/, run in browser, then DELETE this file.
 */

$lockFile = __DIR__ . '/convert_webp.lock';
$guard = 'K3GALL3_WEBP_2026';

if (($_GET['key'] ?? '') !== $guard) {
    http_response_code(403);
    die('Forbidden');
}

if (file_exists($lockFile)) {
    die('Already executed. Delete convert_webp.lock to re-run.');
}

set_time_limit(300);

$storageDir = dirname(__DIR__) . '/app_core/storage/app/public/listings';

if (!is_dir($storageDir)) {
    die('Directory not found: ' . $storageDir);
}

if (!function_exists('imagecreatefromjpeg')) {
    die('GD library not available.');
}

$quality = 80;
$converted = 0;
$skipped = 0;
$errors = 0;

echo "<pre>WebP Converter\n==============\n\n";

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($storageDir, RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($files as $file) {
    $path = $file->getPathname();
    $ext = strtolower($file->getExtension());

    if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
        continue;
    }

    $webpPath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);

    if (file_exists($webpPath)) {
        $skipped++;
        continue;
    }

    $image = match ($ext) {
        'jpg', 'jpeg' => @imagecreatefromjpeg($path),
        'png' => @imagecreatefrompng($path),
        default => null,
    };

    if (!$image) {
        echo "ERROR: Could not read $path\n";
        $errors++;
        continue;
    }

    if ($ext === 'png') {
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
    }

    if (imagewebp($image, $webpPath, $quality)) {
        $origSize = filesize($path);
        $webpSize = filesize($webpPath);
        $saved = round((1 - $webpSize / max($origSize, 1)) * 100);
        echo "OK: " . basename($path) . " → .webp ({$saved}% smaller)\n";
        $converted++;
    } else {
        echo "ERROR: Failed to write $webpPath\n";
        $errors++;
    }

    imagedestroy($image);
}

echo "\nDone! Converted: $converted | Skipped: $skipped | Errors: $errors\n";

file_put_contents($lockFile, date('Y-m-d H:i:s'));
echo "\n⚠️ DELETE convert_webp.php and convert_webp.lock from the server now!\n</pre>";
