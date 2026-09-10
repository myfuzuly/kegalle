<?php
header('Content-Type: text/plain; charset=UTF-8');
require_once '/home/kegalle/app_core/vendor/autoload.php';
$app = require_once '/home/kegalle/app_core/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $tables = \Illuminate\Support\Facades\DB::select("SHOW TABLES LIKE 'brand%'");
    echo "Tables: " . json_encode($tables) . "\n";
    $brands = \App\Models\Brand::withCount('models')->orderBy('name')->paginate(30);
    echo "Brands count: " . $brands->total() . "\n";
    echo "OK\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
unlink(__FILE__);
