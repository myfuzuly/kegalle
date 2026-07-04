<?php
$lock = __DIR__ . '/.clear_route_cache.lock';
if (file_exists($lock)) { die('Already run.'); }
file_put_contents($lock, date('Y-m-d H:i:s'));

$cacheDir = __DIR__ . '/app_core/bootstrap/cache/';
$cleared = [];
foreach (glob($cacheDir . 'routes-*.php') as $f) {
    unlink($f);
    $cleared[] = basename($f);
}

// Also clear any compiled config cache
foreach (glob($cacheDir . 'config.php') as $f) {
    unlink($f);
    $cleared[] = basename($f);
}

echo "Cleared: " . (count($cleared) ? implode(', ', $cleared) : 'no cache files found') . "\n";
echo "Done.";
