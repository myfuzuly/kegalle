<?php
require '/home/kurulla/app_core/vendor/autoload.php';
$app = require '/home/kurulla/app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

echo "<h3>Custom fields with 'brand' in name</h3>";
$fields = DB::table('custom_fields')->where('name', 'like', '%brand%')->get();
foreach ($fields as $f) {
    echo "<pre>" . json_encode($f, JSON_PRETTY_PRINT) . "</pre>";
}

echo "<h3>Brands table sample</h3>";
$brands = DB::table('brands')->where('is_active', 1)->limit(10)->get();
foreach ($brands as $b) {
    echo "<p>ID={$b->id}, name={$b->name}, slug={$b->slug}, group={$b->category_group}</p>";
}

echo "<h3>Listing field values with brand_id field</h3>";
$brandField = DB::table('custom_fields')->where('name', 'brand_id')->first();
if ($brandField) {
    $vals = DB::table('listing_field_values')->where('custom_field_id', $brandField->id)->limit(10)->get();
    foreach ($vals as $v) {
        $brand = DB::table('brands')->find($v->value);
        echo "<p>listing_id={$v->listing_id}, brand_id={$v->value}, brand_name=" . ($brand->name ?? 'NOT FOUND') . "</p>";
    }
} else {
    echo "<p>No brand_id custom field found</p>";
}
