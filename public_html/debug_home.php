<?php
// Emergency debug - check home page error
$token = $_GET['token'] ?? '';
if ($token !== 'fvt_mX5nZqW8pCj2rTkH6aLsD9yEbNu4') { die('403'); }

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $ctrl = new App\Http\Controllers\Frontend\HomeController();
    $request = Illuminate\Http\Request::capture();
    $response = $ctrl->index();
    echo "Controller OK - returned: " . get_class($response);
} catch (\Throwable $e) {
    echo "ERROR: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . substr($e->getTraceAsString(), 0, 1500);
}
