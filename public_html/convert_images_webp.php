<?php
// One-time: convert existing listing images + store logos/banners to WebP and update DB paths.
$lock = __DIR__ . '/convert_images_webp.lock';
if (file_exists($lock)) die('Already run.');
file_put_contents($lock, date('Y-m-d H:i:s'));

set_time_limit(600);

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\DB;

function convertOne(string $relativePath): ?string
{
    $abs = storage_path('app/public/' . $relativePath);
    $ext = strtolower(pathinfo($abs, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png'])) return null; // already webp or missing ext
    if (!file_exists($abs)) return null;

    $webpAbs = ImageHelper::convertToWebp($abs);
    if (!$webpAbs) return null;

    @unlink($abs);
    return ImageHelper::webpPath($relativePath);
}

$converted = 0; $skipped = 0;

// Listing images
foreach (DB::table('listing_images')->get() as $img) {
    $newPath = convertOne($img->path);
    if ($newPath) {
        DB::table('listing_images')->where('id', $img->id)->update(['path' => $newPath]);
        $converted++;
        echo "Converted listing image #{$img->id}: {$newPath}<br>";
    } else {
        $skipped++;
    }
}

// Store logos & banners
foreach (DB::table('stores')->get() as $store) {
    foreach (['logo', 'banner'] as $field) {
        if (empty($store->$field)) continue;
        $newPath = convertOne($store->$field);
        if ($newPath) {
            DB::table('stores')->where('id', $store->id)->update([$field => $newPath]);
            $converted++;
            echo "Converted store #{$store->id} {$field}: {$newPath}<br>";
        } else {
            $skipped++;
        }
    }
}

echo "<br><b>Done. Converted: {$converted}, skipped (already WebP/missing): {$skipped}</b>";
