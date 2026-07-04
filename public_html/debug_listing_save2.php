<?php
// Check error log for listing update errors
require '/home/kurulla/app_core/vendor/autoload.php';
$app = require '/home/kurulla/app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    Illuminate\Http\Request::capture()
);

$logPath = '/home/kurulla/app_core/storage/logs/laravel.log';
if (!file_exists($logPath)) { die('No log file.'); }

$content = file_get_contents($logPath);
// Find lines mentioning listing, update, or moderation errors
$lines = explode("\n", $content);
$relevant = [];
foreach ($lines as $i => $line) {
    if (stripos($line, 'updateListing') !== false
        || stripos($line, 'ModerationController') !== false
        || (stripos($line, 'admin/listings') !== false && stripos($line, 'PUT') !== false)
        || (stripos($line, 'SQLSTATE') !== false)
        || (stripos($line, 'QueryException') !== false)
    ) {
        // Grab this line + next 5 for context
        for ($j = max(0, $i-1); $j <= min(count($lines)-1, $i+5); $j++) {
            $relevant[$j] = $lines[$j];
        }
    }
}

echo "<h3>Relevant Error Log Lines</h3>";
if (empty($relevant)) {
    echo "<p>No errors found related to listing updates or SQL in the log.</p>";

    // Show last 100 lines for any errors
    echo "<h3>Last 100 lines of log</h3>";
    $last = array_slice($lines, -100);
    echo "<pre style='font-size:11px;max-height:600px;overflow:auto'>" . htmlspecialchars(implode("\n", $last)) . "</pre>";
} else {
    ksort($relevant);
    echo "<pre style='font-size:11px;max-height:600px;overflow:auto'>" . htmlspecialchars(implode("\n", $relevant)) . "</pre>";
}

// Also check: does the listing_images table have timestamps?
echo "<h3>listing_images has timestamps?</h3>";
$cols = \Illuminate\Support\Facades\Schema::getColumnListing('listing_images');
echo in_array('created_at', $cols) ? "YES" : "NO - this could cause save errors!";
echo "<br>Columns: " . implode(', ', $cols);

// Check ListingImage model for timestamps setting
echo "<h3>ListingImage model check</h3>";
$model = new \App\Models\ListingImage();
echo "timestamps enabled: " . ($model->usesTimestamps() ? 'YES' : 'NO');
