<?php
$lockFile = __DIR__ . '/swap_gov_order.lock';
if (file_exists($lockFile)) { die('Already executed.'); }

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\GovernmentService;

$road = GovernmentService::where('slug', 'road-closures')->first();
$ann = GovernmentService::where('slug', 'announcements')->first();

if ($road && $ann) {
    $roadOrder = $road->sort_order;
    $annOrder = $ann->sort_order;
    $road->sort_order = $annOrder;
    $ann->sort_order = $roadOrder;
    $road->save();
    $ann->save();
    echo "Swapped: Road Closures (now sort_order=$annOrder) <-> Announcements (now sort_order=$roadOrder)";
} else {
    echo "Road: " . ($road ? "found (sort={$road->sort_order})" : "NOT FOUND") . "\n";
    echo "Ann: " . ($ann ? "found (sort={$ann->sort_order})" : "NOT FOUND") . "\n";
}

file_put_contents($lockFile, date('Y-m-d H:i:s'));
