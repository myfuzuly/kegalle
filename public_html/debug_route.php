<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../app_core/vendor/autoload.php';
$app = require_once __DIR__.'/../app_core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Simulate the index method logic step by step
    $request = Illuminate\Http\Request::create('/listings', 'GET');
    app()->instance('request', $request);

    // Test the location block
    $allLocs = \App\Models\Location::where('is_active', 1)->orderBy('sort_order')->orderBy('name')->get();
    echo "Locations count: " . $allLocs->count() . "\n";

    $idCounts = \App\Models\Listing::published()->whereNotNull('location_id')
        ->selectRaw('location_id, COUNT(*) as cnt')->groupBy('location_id')
        ->get()->pluck('cnt', 'location_id');
    echo "idCounts: " . json_encode($idCounts) . "\n";

    $textCounts = \App\Models\Listing::published()
        ->selectRaw('location, COUNT(*) as cnt')->groupBy('location')
        ->get()->pluck('cnt', 'location');
    echo "textCounts: " . json_encode($textCounts) . "\n";

    foreach ($allLocs as $loc) {
        $byId   = (int) ($idCounts[$loc->id] ?? 0);
        $byText = (int) ($textCounts[$loc->name] ?? 0);
        $loc->listings_count = max($byId, $byText);
        echo "  {$loc->name}: byId={$byId}, byText={$byText}, final={$loc->listings_count}\n";
    }

    // Test paginate
    $query = \App\Models\Listing::published()->with(['store', 'user', 'category', 'images', 'locationModel']);
    $listings = $query->paginate(20);
    echo "Listings total: " . $listings->total() . "\n";

    echo "ALL OK\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
