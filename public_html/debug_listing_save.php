<?php
// One-time diagnostic — check admin listing update issues
$lockFile = __DIR__ . '/debug_listing_save.lock';
if (file_exists($lockFile)) { die('Already run.'); }

require '/home/kegalle/app_core/vendor/autoload.php';
$app = require '/home/kegalle/app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "<h3>Listing Table Column Check</h3>";
$cols = Schema::getColumnListing('listings');
echo "<pre>Columns: " . implode(', ', $cols) . "</pre>";

// Check if location_id column exists
echo "<p>Has location_id: " . (Schema::hasColumn('listings', 'location_id') ? 'YES' : 'NO') . "</p>";
echo "<p>Has is_top: " . (Schema::hasColumn('listings', 'is_top') ? 'YES' : 'NO') . "</p>";
echo "<p>Has is_featured: " . (Schema::hasColumn('listings', 'is_featured') ? 'YES' : 'NO') . "</p>";

// Check listing_images table
echo "<h3>listing_images Table</h3>";
if (Schema::hasTable('listing_images')) {
    $imgCols = Schema::getColumnListing('listing_images');
    echo "<pre>Columns: " . implode(', ', $imgCols) . "</pre>";
} else {
    echo "<p style='color:red'>Table listing_images does NOT exist!</p>";
}

// Check listing_field_values table
echo "<h3>listing_field_values Table</h3>";
if (Schema::hasTable('listing_field_values')) {
    $lfvCols = Schema::getColumnListing('listing_field_values');
    echo "<pre>Columns: " . implode(', ', $lfvCols) . "</pre>";
} else {
    echo "<p style='color:red'>Table listing_field_values does NOT exist!</p>";
}

// Check custom_fields table
echo "<h3>custom_fields Table</h3>";
if (Schema::hasTable('custom_fields')) {
    $cfCols = Schema::getColumnListing('custom_fields');
    echo "<pre>Columns: " . implode(', ', $cfCols) . "</pre>";
} else {
    echo "<p style='color:red'>Table custom_fields does NOT exist!</p>";
}

// Check for orphaned field values
echo "<h3>Orphaned ListingFieldValues (field deleted)</h3>";
if (Schema::hasTable('listing_field_values') && Schema::hasTable('custom_fields')) {
    $orphans = DB::table('listing_field_values')
        ->leftJoin('custom_fields', 'listing_field_values.custom_field_id', '=', 'custom_fields.id')
        ->whereNull('custom_fields.id')
        ->count();
    echo "<p>Orphaned records: $orphans</p>";
}

// Test: try to get a listing and check values relationship
echo "<h3>Sample Listing Check</h3>";
$listing = \App\Models\Listing::with(['images', 'values.field'])->first();
if ($listing) {
    echo "<p>Listing #{$listing->id}: {$listing->title}</p>";
    echo "<p>Images count: {$listing->images->count()}</p>";
    echo "<p>Values count: {$listing->values->count()}</p>";
    foreach ($listing->values as $v) {
        echo "<p>Value #{$v->id}: field=" . ($v->field ? $v->field->name : '<span style="color:red">NULL (orphaned!)</span>') . ", value={$v->value}</p>";
    }
} else {
    echo "<p>No listings found.</p>";
}

// Check Laravel error log for recent listing update errors
echo "<h3>Recent Error Log (last 30 lines)</h3>";
$logPath = '/home/kegalle/app_core/storage/logs/laravel.log';
if (file_exists($logPath)) {
    $lines = file($logPath);
    $last = array_slice($lines, -30);
    echo "<pre style='font-size:11px;max-height:400px;overflow:auto'>" . htmlspecialchars(implode('', $last)) . "</pre>";
} else {
    echo "<p>Log file not found.</p>";
}

file_put_contents($lockFile, date('c'));
echo "<p style='color:green'>Done.</p>";
