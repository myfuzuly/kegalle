<?php
header('Content-Type: text/plain; charset=UTF-8');
require_once '/home/kegalle/app_core/vendor/autoload.php';
$app = require_once '/home/kegalle/app_core/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Brand; use App\Models\Category;
use Illuminate\Support\Str; use Illuminate\Support\Facades\DB;

$brandNames = [
    '3M','Abro','Armor All','Autoglym','Bburago','Bosch','Bridgestone','Castrol',
    'Chevron','Continental','CRC','Denso','Energizer','Exide','Febreze','Focal',
    'Gates','Goodyear','GS Yuasa','Hella','Honda','Hyundai','JBL','Karcher','K&N',
    'Kenwood','Liqui Moly','Lucas','Mahle','Mann Filter','Meguiar\'s','Michelin',
    'Mobil','Motul','NGK','Nilfisk','Osram','Panasonic','Philips','Pioneer',
    'Prestone','Quaker State','Rain-X','Shell','Sonax','STP','Turtle Wax',
    'Valeo','Varta','WD-40','Wurth','Yokohama',
];

$cat = Category::where('name','like','%Vehicle%Accessories%')->whereNotNull('parent_id')->first();
if (!$cat) $cat = Category::where('name','like','%Vehicle%Accessories%')->first();
if (!$cat) $cat = Category::where('name','like','%Vehicle%')->whereNotNull('parent_id')->first();
if (!$cat) {
    echo "ERROR: Not found.\nSub-categories:\n";
    Category::whereNotNull('parent_id')->orderBy('name')->get()->each(fn($c)=>print("  [{$c->id}] {$c->name}\n"));
    unlink(__FILE__); exit;
}
echo "Found: [{$cat->id}] {$cat->name} (parent_id={$cat->parent_id})\n\n";

$created=0; $existing=0; $brandIds=[];
foreach ($brandNames as $name) {
    $brand = Brand::where('name',$name)->first();
    if (!$brand) {
        $brand=Brand::create(['name'=>$name,'slug'=>Str::slug($name),'category_group'=>'','sort_order'=>0,'is_active'=>true]);
        echo "CREATED: {$name} (id={$brand->id})\n"; $created++;
    } else { echo "EXISTS:  {$name} (id={$brand->id})\n"; $existing++; }
    $brandIds[]=$brand->id;
}
echo "\nCreated: {$created} | Existed: {$existing} | Total: ".count($brandIds)."\n";
$currentIds=DB::table('brand_category')->where('category_id',$cat->id)->pluck('brand_id')->toArray();
$toAdd=array_diff($brandIds,$currentIds);
if ($toAdd) { DB::table('brand_category')->insert(array_map(fn($bid)=>['brand_id'=>$bid,'category_id'=>$cat->id],$toAdd)); echo "Newly assigned: ".count($toAdd)."\n"; }
else echo "All already assigned.\n";
echo "Total in '{$cat->name}': ".DB::table('brand_category')->where('category_id',$cat->id)->count()."\nDone.\n";
unlink(__FILE__);
