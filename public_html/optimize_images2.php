<?php
// One-time: tighter listing image downscale (1000px) + convert ad banners to WebP.
$lock = __DIR__ . '/optimize_images2.lock';
if (file_exists($lock)) die('Already run.');
file_put_contents($lock, date('Y-m-d H:i:s'));

set_time_limit(600);

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$done = 0;

// Listing images: max 1000px (cards show ~400px, detail ~800px)
foreach (DB::table('listing_images')->get() as $img) {
    $abs = storage_path('app/public/' . $img->path);
    if (!file_exists($abs)) continue;
    $gd = ImageHelper::loadImage($abs);
    if (!$gd) continue;
    $longest = max(imagesx($gd), imagesy($gd));
    imagedestroy($gd);
    if ($longest > 1000) {
        $before = filesize($abs);
        if (ImageHelper::resizeDown($abs, 1000)) {
            clearstatcache(true, $abs);
            $done++;
            echo basename($img->path) . ': ' . round($before/1024) . 'KB -> ' . round(filesize($abs)/1024) . "KB<br>";
        }
    }
}

// Ad banners: convert jpg/png to webp, downscale to 1600, update DB
if (Schema::hasTable('ad_banners')) {
    $cols = Schema::getColumnListing('ad_banners');
    $field = in_array('image', $cols) ? 'image' : (in_array('image_path', $cols) ? 'image_path' : null);
    if ($field) {
        foreach (DB::table('ad_banners')->get() as $b) {
            $rel = $b->$field ?? null;
            if (!$rel) continue;
            $abs = storage_path('app/public/' . $rel);
            $ext = strtolower(pathinfo($abs, PATHINFO_EXTENSION));
            if (!file_exists($abs) || !in_array($ext, ['jpg', 'jpeg', 'png'])) continue;
            $before = filesize($abs);
            ImageHelper::resizeDown($abs, 1600);
            $webpAbs = ImageHelper::convertToWebp($abs);
            if ($webpAbs) {
                @unlink($abs);
                $newRel = ImageHelper::webpPath($rel);
                DB::table('ad_banners')->where('id', $b->id)->update([$field => $newRel]);
                $done++;
                echo "Ad banner #{$b->id}: " . round($before/1024) . 'KB -> ' . round(filesize($webpAbs)/1024) . "KB (webp)<br>";
            }
        }
    } else {
        echo "ad_banners image column not found; skipped.<br>";
    }
}

echo "<br><b>Done. {$done} images optimized.</b>";
