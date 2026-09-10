<?php
/**
 * Called by CI/CD after deploy to clear Blade + config caches.
 * Protected by a secret token — set CACHE_CLEAR_TOKEN in server .env.
 */
$token = $_GET['token'] ?? '';
$expected = getenv('CACHE_CLEAR_TOKEN') ?: '';

if ($expected && $token !== $expected) {
    http_response_code(403);
    exit('Forbidden');
}

define('LARAVEL_START', microtime(true));
require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$results = [];

// Clear view cache
$viewPath = storage_path('framework/views');
$cleared  = 0;
foreach (glob($viewPath . '/*.php') ?: [] as $f) {
    @unlink($f);
    $cleared++;
}
$results[] = "✓ Cleared {$cleared} compiled views";

// Clear config cache
if (file_exists(bootstrap_path('cache/config.php'))) {
    @unlink(bootstrap_path('cache/config.php'));
    $results[] = "✓ Config cache cleared";
}

// Clear route cache
if (file_exists(bootstrap_path('cache/routes-v7.php'))) {
    @unlink(bootstrap_path('cache/routes-v7.php'));
    $results[] = "✓ Route cache cleared";
}

http_response_code(200);
header('Content-Type: text/plain');
echo implode("\n", $results) . "\nDone at " . date('Y-m-d H:i:s T');
