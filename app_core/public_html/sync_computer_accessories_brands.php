<?php
header('Content-Type: text/plain; charset=UTF-8');
require_once '/home/kegalle/app_core/vendor/autoload.php';
$app = require_once '/home/kegalle/app_core/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

$brandNames = [
    'A4Tech','Acer','Acer Predator','ADATA','Anker','Antec','Apple','Arctic',
    'ASRock','ASUS','ASUS ROG','ASUS TUF Gaming','AOC','Audio-Technica','Baseus',
    'Belkin','BenQ','Bloody','Bose','Brother','Canon','Case Logic','Cooler Master',
    'Corsair','Creative','Crucial','D-Link','Dareu','Dell','DeepCool','Edifier',
    'Epson','Ergotron','Essager','EvoFox','Fantech','Frontech','Genius','Gigabyte',
    'Glorious','Goliathus','Havit','Hoco','HP','HyperX','iMICE','JBL','Joyroom',
    'Kensington','Kingston','Kioxia','Kyocera','LaCie','Lian Li','Lexar','LG',
    'Lenovo','Lenovo Legion','Logitech','Logitech G','Microsoft','Micro','MSI',
    'Meetion','NZXT','Noctua','North Bayou','Orico','Pantum','Portronics','Puma',
    'Razer','Rapoo','Redragon','Remax','Ricoh','RivaCase','Samsung','SanDisk',
    'Samsonite','Seagate','Sennheiser','Silicon Power','Sony','SteelSeries',
    'SwissGear','Targus','TeamGroup','Thermaltake','Toshiba','TP-Link','Transcend',
    'Tomtoc','UAG','UGREEN','Uniqlo','Verbatim','Vention','ViewSonic','WD',
    'Western Digital','WIWU','Xerox','Zebronics','Zowie',
];

// Find Computer Accessories sub-category
$cat = Category::where('name', 'like', '%Computer Accessories%')->whereNotNull('parent_id')->first();
if (!$cat) {
    // Try without parent_id constraint
    $cat = Category::where('name', 'like', '%Computer Accessories%')->first();
}
if (!$cat) {
    echo "ERROR: Could not find 'Computer Accessories' category.\n";
    echo "All sub-categories:\n";
    Category::whereNotNull('parent_id')->orderBy('name')->get()->each(fn($c) => print("  [{$c->id}] {$c->name}\n"));
    unlink(__FILE__);
    exit;
}

echo "Found category: [{$cat->id}] {$cat->name} (parent_id={$cat->parent_id})\n\n";

$created = 0;
$existing = 0;
$brandIds = [];

foreach ($brandNames as $name) {
    $brand = Brand::where('name', $name)->first();
    if (!$brand) {
        $brand = Brand::create([
            'name'           => $name,
            'slug'           => Str::slug($name),
            'category_group' => '',
            'sort_order'     => 0,
            'is_active'      => true,
        ]);
        echo "CREATED: {$name} (id={$brand->id})\n";
        $created++;
    } else {
        echo "EXISTS:  {$name} (id={$brand->id})\n";
        $existing++;
    }
    $brandIds[] = $brand->id;
}

echo "\n--- Summary ---\n";
echo "Created: {$created} brands\n";
echo "Already existed: {$existing} brands\n";
echo "Total to assign: " . count($brandIds) . " brands\n\n";

// Get current brand_category rows for this category
$currentIds = DB::table('brand_category')->where('category_id', $cat->id)->pluck('brand_id')->toArray();
echo "Currently assigned to category: " . count($currentIds) . " brands\n";

// Add only missing ones (don't remove existing)
$toAdd = array_diff($brandIds, $currentIds);
if ($toAdd) {
    $rows = array_map(fn($bid) => ['brand_id' => $bid, 'category_id' => $cat->id], $toAdd);
    DB::table('brand_category')->insert($rows);
    echo "Newly assigned: " . count($toAdd) . " brands\n";
} else {
    echo "All brands already assigned.\n";
}

$finalCount = DB::table('brand_category')->where('category_id', $cat->id)->count();
echo "Total brands now assigned to '{$cat->name}': {$finalCount}\n";
echo "\nDone.\n";
unlink(__FILE__);
