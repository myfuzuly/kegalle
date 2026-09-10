<?php
if (function_exists('opcache_reset')) { opcache_reset(); }
echo "Opcache reset.\n";

$base = dirname(__DIR__) . '/app_core';

// Clear view cache
$viewDir = $base . '/storage/framework/views';
$v = 0;
if (is_dir($viewDir)) {
    foreach (glob($viewDir . '/*.php') ?: [] as $f) { @unlink($f); $v++; }
}

// Clear app cache files
$cacheDir = $base . '/storage/framework/cache/data';
$m = 0;
if (is_dir($cacheDir)) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cacheDir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) { if ($f->isFile()) { @unlink($f->getPathname()); $m++; } }
}
echo "Cache and views cleared.\n";

// Wipe page cache
$pageDir = $base . '/storage/framework/pagecache';
$n = 0;
if (is_dir($pageDir)) {
    foreach (glob($pageDir . '/*') ?: [] as $f) { if (is_file($f)) { @unlink($f); $n++; } }
}
echo "Pagecache wiped: $n files.\n";
echo "App cache wiped: $m files.\n";

// Wipe search result file cache
$searchDir = $base . '/storage/framework/searchcache';
$s = 0;
if (is_dir($searchDir)) {
    foreach (glob($searchDir . '/*.json') ?: [] as $f) { @unlink($f); $s++; }
}
echo "Search cache wiped: $s files.\n";

// Clear compiled config/routes/services cache
$bootstrapCache = $base . '/bootstrap/cache';
$bc = 0;
foreach (['config.php','routes-v7.php','services.php','packages.php','events.php'] as $cf) {
    $p = $bootstrapCache . '/' . $cf;
    if (file_exists($p)) { @unlink($p); $bc++; }
}
echo "Bootstrap cache wiped: $bc files.\n";

echo "Done.\n";
