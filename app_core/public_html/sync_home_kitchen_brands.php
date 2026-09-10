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
    '3M','Aashirvaad','Aarke','Ariete','Arzum','Bajaj','Beko','Black+Decker',
    'Borosil','Bormioli Rocco','Brita','Butterfly','Calphalon','Cambro',
    'Chef\'s Choice','Clikon','Conair','Corelle','Cuisinart','Dawlance',
    'De\'Longhi','Dorsch','Duralex','Ecolite','Electrolux','Elica','Ember',
    'Faber','Fagor','Fissler','Glen','Hario','Havells','Hettich','Hoffman',
    'Honeywell','IKEA','Inalsa','Ingco','Joseph Joseph','Kenwood','Karcher',
    'Kitchenaid','Korkmaz','Kuvings','Lakeland','Laica','Luminarc','Maharaja',
    'Milton','Morphy Richards','Moulinex','Midea','Nespresso','Ninja','Nilfisk',
    'Nitori','Nova','Oster','Panasonic','Prestige','Philips','Pyrex','Rinnai',
    'Russell Hobbs','Sakura','Samsung','Scotch-Brite','Sharp','Singer','Sistema',
    'Smeg','Sokany','Solis','Sunbeam','Tefal','Thermos','Toshiba','Tupperware',
    'Usha','Usha Cook','V-Guard','Vinod','Wahl','Westinghouse','Wonderchef',
    'Yamada','Zojirushi',
];

$cat = Category::where('name', 'like', '%Home%Kitchen%')->whereNotNull('parent_id')->first();
if (!$cat) $cat = Category::where('name', 'like', '%Home%Kitchen%')->first();
if (!$cat) {
    echo "ERROR: Could not find 'Home & Kitchen Accessories' category.\n";
    echo "Sub-categories:\n";
    Category::whereNotNull('parent_id')->orderBy('name')->get()->each(fn($c) => print("  [{$c->id}] {$c->name}\n"));
    unlink(__FILE__); exit;
}
echo "Found category: [{$cat->id}] {$cat->name} (parent_id={$cat->parent_id})\n\n";

$created = 0; $existing = 0; $brandIds = [];
foreach ($brandNames as $name) {
    $brand = Brand::where('name', $name)->first();
    if (!$brand) {
        $brand = Brand::create([
            'name' => $name, 'slug' => Str::slug($name),
            'category_group' => '', 'sort_order' => 0, 'is_active' => true,
        ]);
        echo "CREATED: {$name} (id={$brand->id})\n"; $created++;
    } else {
        echo "EXISTS:  {$name} (id={$brand->id})\n"; $existing++;
    }
    $brandIds[] = $brand->id;
}

echo "\n--- Summary ---\n";
echo "Created: {$created} | Existed: {$existing} | Total: " . count($brandIds) . "\n\n";

$currentIds = DB::table('brand_category')->where('category_id', $cat->id)->pluck('brand_id')->toArray();
echo "Currently assigned: " . count($currentIds) . "\n";
$toAdd = array_diff($brandIds, $currentIds);
if ($toAdd) {
    DB::table('brand_category')->insert(array_map(fn($bid) => ['brand_id' => $bid, 'category_id' => $cat->id], $toAdd));
    echo "Newly assigned: " . count($toAdd) . "\n";
} else {
    echo "All already assigned.\n";
}
echo "Total brands now in '{$cat->name}': " . DB::table('brand_category')->where('category_id', $cat->id)->count() . "\n";
echo "\nDone.\n";
unlink(__FILE__);
