<?php
$lock = __DIR__ . '/dedup_brands.lock';
if (file_exists($lock)) die('Already run.');
file_put_contents($lock, date('Y-m-d H:i:s'));

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$brands = DB::table('brands')->orderBy('id')->get();

$seen = [];
$merged = 0;
$deleted = 0;

foreach ($brands as $brand) {
    $key = strtolower(trim($brand->name));
    if (!isset($seen[$key])) {
        $seen[$key] = $brand->id;
        continue;
    }

    $keepId = $seen[$key];

    // Move models from duplicate to the kept brand
    $moved = DB::table('brand_models')
        ->where('brand_id', $brand->id)
        ->update(['brand_id' => $keepId]);
    $merged += $moved;

    // Update listing_field_values referencing this brand
    DB::table('listing_field_values')
        ->where('value', (string)$brand->id)
        ->whereIn('custom_field_id', function($q) {
            $q->select('id')->from('custom_fields')->where('type', 'brand_select');
        })
        ->update(['value' => (string)$keepId]);

    // Clean up junction table
    DB::table('brand_category_group')->where('brand_id', $brand->id)->delete();

    // Delete duplicate
    DB::table('brands')->where('id', $brand->id)->delete();
    $deleted++;
}

// Remove duplicate models within same brand (same name)
$dupModels = DB::select("
    SELECT MIN(id) as keep_id, brand_id, LOWER(TRIM(name)) as norm_name, COUNT(*) as cnt
    FROM brand_models
    GROUP BY brand_id, LOWER(TRIM(name))
    HAVING COUNT(*) > 1
");
$deletedModels = 0;
foreach ($dupModels as $d) {
    $del = DB::table('brand_models')
        ->where('brand_id', $d->brand_id)
        ->whereRaw('LOWER(TRIM(name)) = ?', [$d->norm_name])
        ->where('id', '!=', $d->keep_id)
        ->delete();
    $deletedModels += $del;
}

echo "Deduplication complete.<br>";
echo "Brands removed: {$deleted}<br>";
echo "Models reassigned: {$merged}<br>";
echo "Duplicate models removed: {$deletedModels}<br>";
echo "Unique brands remaining: " . DB::table('brands')->count() . "<br>";
