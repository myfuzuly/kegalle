<?php
$routeFile = __DIR__ . '/app_core/routes/web.php';
$content = file_get_contents($routeFile);

// Check if /saved route exists
if (strpos($content, "'/saved'") !== false) {
    echo "Route /saved FOUND in web.php\n";
} else {
    echo "Route /saved NOT FOUND in web.php\n";
}

// Check bootstrap cache
$cacheDir = __DIR__ . '/app_core/bootstrap/cache/';
$cacheFiles = glob($cacheDir . '*.php');
echo "Cache files: " . (count($cacheFiles) ? implode(', ', array_map('basename', $cacheFiles)) : 'none') . "\n";

// Try clearing opcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache cleared.\n";
} else {
    echo "OPcache not available.\n";
}

// Show the line with /saved
foreach (explode("\n", $content) as $i => $line) {
    if (strpos($line, 'saved') !== false) {
        echo "Line " . ($i+1) . ": " . trim($line) . "\n";
    }
}
