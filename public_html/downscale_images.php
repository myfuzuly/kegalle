<?php
// One-time: downscale existing oversized images (listings max 1400px, logos 500px, banners 1600px).
$lock = __DIR__ . '/downscale_images.lock';
if (file_exists($lock)) die('Already run.');
file_put_contents($lock, date('Y-m-d H:i:s'));

set_time_limit(600);

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\DB;

$done = 0;

function shrink(string $rel, int $maxDim): void
{
    global $done;
    $abs = storage_path('app/public/' . $rel);
    if (!file_exists($abs)) return;
    $before = filesize($abs);
    $img = ImageHelper::loadImage($abs);
    if (!$img) return;
    $longest = max(imagesx($img), imagesy($img));
    imagedestroy($img);
    if ($longest <= $maxDim) return;

    if (ImageHelper::resizeDown($abs, $maxDim)) {
        clearstatcache(true, $abs);
        $after = filesize($abs);
        $done++;
        echo basename($rel) . ': ' . round($before/1024) . 'KB -> ' . round($after/1024) . "KB<br>";
    }
}

foreach (DB::table('listing_images')->get() as $img) {
    shrink($img->path, 1400);
}
foreach (DB::table('stores')->get() as $store) {
    if (!empty($store->logo)) shrink($store->logo, 500);
    if (!empty($store->banner)) shrink($store->banner, 1600);
}
// Ad banners
if (\Illuminate\Support\Facades\Schema::hasTable('ad_banners')) {
    foreach (DB::table('ad_banners')->get() as $b) {
        if (!empty($b->image)) shrink($b->image, 1600);
    }
}

echo "<br><b>Done. {$done} images downscaled.</b>";
