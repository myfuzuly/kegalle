<?php
header('Content-Type: text/plain; charset=UTF-8');
require_once '/home/kegalle/app_core/vendor/autoload.php';
$app = require_once '/home/kegalle/app_core/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Brand; use App\Models\Category;
use Illuminate\Support\Str; use Illuminate\Support\Facades\DB;

$brandNames = [
    'American Tourister','Antler','Arctic Hunter','Away','Baggallini','Baseus',
    'Bellroy','Benetton','Briggs & Riley','Cabeau','Case Logic','Cocoon','Delsey',
    'Deuter','Delsey Paris','Eagle Creek','Ebags','Fjallraven','Fossil','Go Travel',
    'Herschel','High Sierra','Kipling','Kono','Lewis N Clark','Lojel','Lowe Alpine',
    'Mark Ryden','Master Lock','Montblanc','Moshi','Mountain Warehouse',
    'National Geographic','Nomatic','Osprey','Pacsafe','Piquadro','Roncato',
    'Samsonite','Sea To Summit','Skybags','Swiss Gear','Swiss Military',
    'The North Face','Thule','Timbuk2','Toshiba','Targus','Travelpro','Tumi',
    'Victorinox','Wildcraft','Wenger','WiWU','Xiaomi','Yesido','Zomake',
];

$cat = Category::where('name','like','%Travel%Accessories%')->whereNotNull('parent_id')->first();
if (!$cat) $cat = Category::where('name','like','%Travel%')->whereNotNull('parent_id')->first();
if (!$cat) $cat = Category::where('name','like','%Travel%')->first();
if (!$cat) { echo "ERROR: Not found.\n"; Category::whereNotNull('parent_id')->orderBy('name')->get()->each(fn($c)=>print("  [{$c->id}] {$c->name}\n")); unlink(__FILE__); exit; }
echo "Found: [{$cat->id}] {$cat->name}\n\n";

$created=0; $existing=0; $brandIds=[];
foreach ($brandNames as $name) {
    $brand = Brand::where('name',$name)->first();
    if (!$brand) { $brand=Brand::create(['name'=>$name,'slug'=>Str::slug($name),'category_group'=>'','sort_order'=>0,'is_active'=>true]); echo "CREATED: {$name}\n"; $created++; }
    else { echo "EXISTS:  {$name} (id={$brand->id})\n"; $existing++; }
    $brandIds[]=$brand->id;
}
echo "\nCreated: {$created} | Existed: {$existing} | Total: ".count($brandIds)."\n";
$currentIds=DB::table('brand_category')->where('category_id',$cat->id)->pluck('brand_id')->toArray();
$toAdd=array_diff($brandIds,$currentIds);
if ($toAdd) { DB::table('brand_category')->insert(array_map(fn($bid)=>['brand_id'=>$bid,'category_id'=>$cat->id],$toAdd)); echo "Newly assigned: ".count($toAdd)."\n"; } else echo "All already assigned.\n";
echo "Total in '{$cat->name}': ".DB::table('brand_category')->where('category_id',$cat->id)->count()."\nDone.\n";
unlink(__FILE__);
