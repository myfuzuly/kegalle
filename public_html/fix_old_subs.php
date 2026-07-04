<?php
$lockFile = __DIR__ . '/fix_old_subs.lock';
if (file_exists($lockFile)) { die('Already executed.'); }

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<pre>";

// Old subcategory slugs to deactivate (replaced by new ones)
$oldSlugs = [
    // Vehicles old subs
    'bikes', 'lorries', 'heavy-duty', 'tractor', 'boats', 'bicycle',
    'auto-parts-accessories', 'auto-services-rentals', 'maintenance-repair',
    // Electronics old subs
    'mobile-phones', 'mobile-accessories', 'mobile-spare-parts', 'smart-products',
    'computers-laptops-tablets', 'computer-accessories', 'tv', 'tv-accessories',
    'camera', 'audio-mp3', 'electronic-home-appliances',
    'video-games-other-electronics', 'aircon-fittings',
    // Property old subs
    'land', 'commercial-property', 'house', 'apartment',
    // Home old subs
    'bathrooms', 'kitchen-items', 'other-items',
    // Animals old subs
    'pets', 'farm-animals', 'animal-accessories', 'veterinary-services', 'other',
];

$deactivated = 0;
foreach ($oldSlugs as $slug) {
    $cat = DB::table('categories')->where('slug', $slug)->whereNotNull('parent_id')->first();
    if ($cat && $cat->is_active) {
        // Check if there's a newer subcategory under the same parent with a different slug
        DB::table('categories')->where('id', $cat->id)->update(['is_active' => false]);
        echo "Deactivated: {$cat->name} (slug={$slug}, parent_id={$cat->parent_id})\n";
        $deactivated++;
    }
}

echo "\n✅ Deactivated $deactivated old subcategories\n";
file_put_contents($lockFile, date('Y-m-d H:i:s'));
echo "</pre>";
