<?php
// Quick debug - bootstrap Laravel and test the location count query
define('LARAVEL_START', microtime(true));
require __DIR__.'/../app_core/vendor/autoload.php';
$app = require_once __DIR__.'/../app_core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $textCounts = \App\Models\Listing::published()
        ->selectRaw('location, COUNT(*) as cnt')
        ->groupBy('location')
        ->get()
        ->pluck('cnt', 'location');
    echo "textCounts OK: " . json_encode($textCounts) . "\n";
} catch (\Throwable $e) {
    echo "textCounts ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

try {
    $idCounts = \App\Models\Listing::published()
        ->whereNotNull('location_id')
        ->selectRaw('location_id, COUNT(*) as cnt')
        ->groupBy('location_id')
        ->get()
        ->pluck('cnt', 'location_id');
    echo "idCounts OK: " . json_encode($idCounts) . "\n";
} catch (\Throwable $e) {
    echo "idCounts ERROR: " . $e->getMessage() . "\n";
}
