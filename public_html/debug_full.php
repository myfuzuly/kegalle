<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../app_core/vendor/autoload.php';
$app = require_once __DIR__.'/../app_core/bootstrap/app.php';

// Enable detailed error display
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    function ($app) {
        return new class($app) extends \App\Exceptions\Handler {
            public function render($request, \Throwable $e) {
                echo "<pre>ERROR: " . get_class($e) . ": " . $e->getMessage() . "\n";
                echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
                echo $e->getTraceAsString() . "</pre>";
                exit;
            }
        };
    }
);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
// Override the URI to listings
$request = Illuminate\Http\Request::create('/listings', 'GET', [], [], [], array_merge($_SERVER, [
    'REQUEST_URI' => '/listings',
    'PATH_INFO' => '/listings',
]));

try {
    $response = $kernel->handle($request);
    echo "HTTP Status: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() >= 500) {
        echo substr($response->getContent(), 0, 2000);
    } else {
        echo "OK - page rendered successfully\n";
    }
} catch (\Throwable $e) {
    echo "EXCEPTION: " . get_class($e) . ": " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
