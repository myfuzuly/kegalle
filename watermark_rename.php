<?php
/**
 * One-time script: add watermark + rename existing listing images to SEO-friendly names.
 * Upload to public_html/, run in browser, then DELETE this file.
 */

$lockFile = __DIR__ . '/watermark_rename.lock';
$guard = 'K3GALL3_WM_2026';

if (($_GET['key'] ?? '') !== $guard) {
    http_response_code(403);
    die('Forbidden');
}

if (file_exists($lockFile)) {
    die('Already executed. Delete watermark_rename.lock to re-run.');
}

set_time_limit(600);

$appRoot = dirname(__DIR__) . '/app_core';
$storageDir = $appRoot . '/storage/app/public/listings';
$dbPath = $appRoot . '/database/database.sqlite';

if (!is_dir($storageDir)) {
    die('Listings directory not found: ' . $storageDir);
}

// Try to connect to database
$db = null;
$dbType = null;

// Try MySQL first via Laravel's .env
$envFile = $appRoot . '/.env';
$env = [];
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with($line, '#')) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $env[trim($parts[0])] = trim($parts[1], " \t\n\r\0\x0B\"'");
        }
    }
}

try {
    if (($env['DB_CONNECTION'] ?? '') === 'mysql') {
        $dsn = 'mysql:host=' . ($env['DB_HOST'] ?? '127.0.0.1') . ';port=' . ($env['DB_PORT'] ?? '3306') . ';dbname=' . ($env['DB_DATABASE'] ?? 'forge');
        $db = new PDO($dsn, $env['DB_USERNAME'] ?? 'forge', $env['DB_PASSWORD'] ?? '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbType = 'mysql';
    } elseif (file_exists($dbPath)) {
        $db = new PDO('sqlite:' . $dbPath);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbType = 'sqlite';
    }
} catch (Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}

if (!$db) {
    die('Could not connect to database.');
}

echo "<pre>Watermark & Rename Tool\n=======================\n\n";

// Get all listing images with their listing slug
$stmt = $db->query("
    SELECT li.id, li.listing_id, li.path, li.sort_order, l.slug
    FROM listing_images li
    JOIN listings l ON l.id = li.listing_id
    ORDER BY li.listing_id, li.sort_order
");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($rows) . " image records\n\n";

$watermarkText = 'kurulla.com';
$renamed = 0;
$watermarked = 0;
$errors = 0;

// Track used filenames to avoid collisions
$usedNames = [];

function loadImage($path) {
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    return match ($ext) {
        'jpg', 'jpeg' => @imagecreatefromjpeg($path),
        'png' => @imagecreatefrompng($path),
        'webp' => @imagecreatefromwebp($path),
        default => null,
    };
}

function addWatermark($absolutePath, $text) {
    $image = loadImage($absolutePath);
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

        $bgColor = imagecolorallocatealpha($image, 0, 0, 0, 60);
        imagefilledrectangle($image,
            $w - $textW - $padX*2 - $margin,
            $h - $textH - $padY*2 - $margin,
            $w - $margin,
            $h - $margin,
            $bgColor
        );

        $white = imagecolorallocate($image, 255, 255, 255);
        imagettftext($image, $fontSize, 0,
            $w - $textW - $padX - $margin,
            $h - $padY - $margin,
            $white, $fontFile, $text
        );
    } else {
        $font = ($w > 600) ? 5 : (($w > 300) ? 4 : 3);
        $charW = imagefontwidth($font);
        $charH = imagefontheight($font);
        $textW = $charW * strlen($text);
        $padX = 8; $padY = 4; $margin = 8;

        $bgColor = imagecolorallocatealpha($image, 0, 0, 0, 60);
        imagefilledrectangle($image,
            $w - $textW - $padX*2 - $margin,
            $h - $charH - $padY*2 - $margin,
            $w - $margin,
            $h - $margin,
            $bgColor
        );

        $white = imagecolorallocate($image, 255, 255, 255);
        imagestring($image, $font,
            $w - $textW - $padX - $margin,
            $h - $charH - $padY - $margin,
            $text, $white
        );
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

foreach ($rows as $row) {
    $oldPath = $row['path'];
    $slug = $row['slug'];
    $ext = strtolower(pathinfo($oldPath, PATHINFO_EXTENSION));
    $sortOrder = (int)$row['sort_order'];

    $oldAbsolute = $storageDir . '/' . basename($oldPath);

    if (!file_exists($oldAbsolute)) {
        echo "SKIP (not found): $oldPath\n";
        $errors++;
        continue;
    }

    // Generate SEO filename
    $suffix = $sortOrder > 0 ? '-' . ($sortOrder + 1) : '';
    $newBasename = $slug . $suffix . '-in-kegalle.' . $ext;

    // Handle collisions
    $counter = 2;
    while (isset($usedNames[$newBasename]) && $usedNames[$newBasename] !== $row['id']) {
        $newBasename = $slug . $suffix . '-in-kegalle-' . $counter . '.' . $ext;
        $counter++;
    }
    $usedNames[$newBasename] = $row['id'];

    $newPath = 'listings/' . $newBasename;
    $newAbsolute = $storageDir . '/' . $newBasename;

    // Add watermark to original
    if (addWatermark($oldAbsolute, $watermarkText)) {
        echo "WATERMARK: " . basename($oldPath) . "\n";
        $watermarked++;
    } else {
        echo "WATERMARK FAIL: " . basename($oldPath) . "\n";
        $errors++;
    }

    // Rename file
    if ($oldAbsolute !== $newAbsolute) {
        if (rename($oldAbsolute, $newAbsolute)) {
            echo "RENAME: " . basename($oldPath) . " → $newBasename\n";
            $renamed++;

            // Update DB
            $upd = $db->prepare("UPDATE listing_images SET path = ? WHERE id = ?");
            $upd->execute([$newPath, $row['id']]);
        } else {
            echo "RENAME FAIL: " . basename($oldPath) . "\n";
            $errors++;
        }
    }

    // Handle existing .webp copy
    $oldWebp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $oldAbsolute);
    $newWebp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $newAbsolute);
    if (file_exists($oldWebp)) {
        // Delete old webp — we'll regenerate with watermark
        @unlink($oldWebp);
    }

    // Regenerate WebP from watermarked original
    $srcForWebp = file_exists($newAbsolute) ? $newAbsolute : $oldAbsolute;
    $img = loadImage($srcForWebp);
    if ($img) {
        imagewebp($img, $newWebp, 80);
        imagedestroy($img);
        echo "WEBP: " . basename($newWebp) . "\n";
    }

    echo "\n";
}

echo "======================\n";
echo "Watermarked: $watermarked | Renamed: $renamed | Errors: $errors\n";

file_put_contents($lockFile, date('Y-m-d H:i:s'));
echo "\n⚠️ DELETE watermark_rename.php and watermark_rename.lock from the server now!\n</pre>";
