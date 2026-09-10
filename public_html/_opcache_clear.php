<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared.";
} else {
    echo "⚠️ OPcache not enabled or not accessible.";
}
// Also clear Laravel bootstrap cache
$files = [
    __DIR__.'/../app_core/bootstrap/cache/config.php',
    __DIR__.'/../app_core/bootstrap/cache/routes-v7.php',
    __DIR__.'/../app_core/bootstrap/cache/services.php',
    __DIR__.'/../app_core/bootstrap/cache/packages.php',
];
$cleared = 0;
foreach ($files as $f) { if (file_exists($f)) { @unlink($f); $cleared++; } }
echo " Bootstrap cache cleared ($cleared files).";
@unlink(__FILE__);
