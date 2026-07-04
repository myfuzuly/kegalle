<?php
$routeFile = '/home/kurulla/app_core/routes/web.php';

// Try opcache invalidation
if (function_exists('opcache_invalidate')) {
    $r = opcache_invalidate($routeFile, true);
    echo "opcache_invalidate: " . ($r ? 'success' : 'failed') . "\n";
} else {
    echo "opcache_invalidate not available\n";
}

// Boot Laravel and list routes containing 'saved'
require '/home/kurulla/app_core/vendor/autoload.php';
$app = require '/home/kurulla/app_core/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$router = $app->make('router');
$routes = $router->getRoutes();
echo "\nRoutes containing 'saved':\n";
foreach ($routes as $route) {
    if (strpos($route->uri(), 'saved') !== false) {
        echo "  " . implode('|', $route->methods()) . " /" . $route->uri() . " -> " . ($route->getActionName()) . "\n";
    }
}
echo "\nTotal routes: " . count($routes) . "\n";

// Check cache files
$cacheDir = '/home/kurulla/app_core/bootstrap/cache/';
$cacheFiles = glob($cacheDir . '*.php');
echo "Cache files: " . (count($cacheFiles) ? implode(', ', array_map('basename', $cacheFiles)) : 'none') . "\n";
