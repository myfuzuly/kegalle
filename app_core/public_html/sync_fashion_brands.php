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
    'Adidas','Aldo','American Tourister','Armani Exchange','Arrow','Bata',
    'Beverly Hills Polo Club','Calvin Klein','Casio','Charles & Keith',
    'Charles Tyrwhitt','Clarks','Converse','Crocs','Daniel Wellington','Delsey',
    'Diesel','Dior','DKNY','Dolce & Gabbana','Dunhill','Ecco','Ellesse',
    'Emporio Armani','Fastrack','Fossil','Furla','G-Shock','Giordano','Guess',
    'Hanes','Havaianas','Herschel','Hugo Boss','Hush Puppies','Jockey','Jordan',
    'Kangol','Kipling','Lacoste','Lee','Levi\'s','Louis Vuitton','Mango',
    'Marc Jacobs','Marks & Spencer','Michael Kors','Mizuno','New Balance','Nike',
    'Nine West','Oakley','Paco Rabanne','Palladium','Puma','Ray-Ban','Reebok',
    'Ralph Lauren','Roxy','Samsonite','Skechers','Slazenger','Steve Madden',
    'Superdry','Swarovski','Swiss Military','Tag Heuer','The North Face',
    'Timberland','Titan','Tommy Hilfiger','Tory Burch','Tumi','Under Armour',
    'United Colors of Benetton','U.S. Polo Assn','Vans','Versace',
    'Victoria\'s Secret','Wildcraft','Wrangler','Yves Saint Laurent','Zara',
];

$cat = Category::where('name', 'like', '%Fashion%Personal%')->whereNotNull('parent_id')->first();
if (!$cat) $cat = Category::where('name', 'like', '%Fashion%')->whereNotNull('parent_id')->first();
if (!$cat) $cat = Category::where('name', 'like', '%Fashion%')->first();
if (!$cat) {
    echo "ERROR: Could not find 'Fashion & Personal Accessories' category.\n";
    echo "Sub-categories available:\n";
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
