<?php
require '/home/kurulla/app_core/vendor/autoload.php';
$app = require '/home/kurulla/app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Get the FULL error log tail — last 200 lines, no filtering
$logPath = '/home/kurulla/app_core/storage/logs/laravel.log';
$lines = file($logPath);
$last = array_slice($lines, -200);

// Filter for actual errors only
$errors = [];
foreach ($last as $i => $line) {
    if (preg_match('/\[(\d{4}-\d{2}-\d{2}.*?)\] \w+\.(ERROR|CRITICAL)/', $line)) {
        $errors[] = $line;
        // grab next 10 lines for stack trace
        for ($j = $i+1; $j < min($i+10, count($last)); $j++) {
            if (isset($last[$j])) $errors[] = $last[$j];
        }
        $errors[] = "---\n";
    }
}

echo "<h3>Recent Errors (ERROR/CRITICAL level)</h3>";
if (empty($errors)) {
    echo "<p>No ERROR/CRITICAL entries in last 200 lines.</p>";
} else {
    echo "<pre style='font-size:11px;max-height:800px;overflow:auto;white-space:pre-wrap'>" . htmlspecialchars(implode('', $errors)) . "</pre>";
}

// Check listing_field_values structure
echo "<h3>listing_field_values details</h3>";
$create = DB::select("SHOW CREATE TABLE listing_field_values");
echo "<pre>" . htmlspecialchars($create[0]->{'Create Table'}) . "</pre>";

// Check if ListingFieldValue has timestamps
$m = new \App\Models\ListingFieldValue();
echo "<p>timestamps: " . ($m->usesTimestamps() ? 'YES' : 'NO') . "</p>";

// Check listing_field_values has created_at/updated_at
$hasCols = Schema::getColumnListing('listing_field_values');
echo "<p>Columns: " . implode(', ', $hasCols) . "</p>";
