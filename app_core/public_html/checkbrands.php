<?php
header('Content-Type: text/plain; charset=UTF-8');
require_once '/home/kegalle/app_core/vendor/autoload.php';
$app = require_once '/home/kegalle/app_core/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    $brands = \App\Models\Brand::withCount('models')->orderBy('name')->paginate(30);
    $totalBrands = \App\Models\Brand::count();
    $html = view('admin.brands.index', compact('brands', 'totalBrands'))->render();
    echo "View rendered OK, length=" . strlen($html) . "\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getFile() . ":" . $e->getLine() . "\n";
}
unlink(__FILE__);
