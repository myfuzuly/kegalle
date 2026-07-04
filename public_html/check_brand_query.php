<?php
require '/home/kurulla/app_core/vendor/autoload.php';
$app = require '/home/kurulla/app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

// Find all Honda brands
$hondas = DB::table('brands')->where('name', 'like', '%Honda%')->get();
echo "<h3>All Honda brands</h3>";
foreach ($hondas as $h) {
    echo "<p>id={$h->id}, name={$h->name}, slug={$h->slug}, group={$h->category_group}, active={$h->is_active}</p>";
}

// Check brand 81
$b81 = DB::table('brands')->find(81);
echo "<h3>Brand ID 81</h3>";
echo $b81 ? "<p>id={$b81->id}, name={$b81->name}, slug={$b81->slug}, group={$b81->category_group}</p>" : "<p>NOT FOUND</p>";
