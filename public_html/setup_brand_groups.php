<?php
$lockFile = __DIR__ . '/setup_brand_groups.lock';
if (file_exists($lockFile)) { die('Already executed.'); }

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Create pivot table
if (!Schema::hasTable('brand_category_group')) {
    DB::statement("CREATE TABLE brand_category_group (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        brand_id INT UNSIGNED NOT NULL,
        category_group VARCHAR(50) NOT NULL,
        INDEX idx_brand_id (brand_id),
        INDEX idx_category_group (category_group),
        UNIQUE KEY uq_brand_group (brand_id, category_group)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Created brand_category_group table.<br>";
}

// Migrate existing data: copy each brand's category_group into the pivot table
$brands = DB::table('brands')->get();
$inserted = 0;
foreach ($brands as $brand) {
    if (empty($brand->category_group)) continue;
    $exists = DB::table('brand_category_group')
        ->where('brand_id', $brand->id)
        ->where('category_group', $brand->category_group)
        ->exists();
    if (!$exists) {
        DB::table('brand_category_group')->insert([
            'brand_id' => $brand->id,
            'category_group' => $brand->category_group,
        ]);
        $inserted++;
    }
}
echo "Migrated $inserted brand-group associations.<br>";

file_put_contents($lockFile, date('Y-m-d H:i:s'));
echo "Done!";
