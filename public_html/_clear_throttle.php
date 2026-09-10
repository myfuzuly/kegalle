<?php
// Clear all Laravel cache (includes throttle keys)
$cacheDir = __DIR__ . '/../app_core/storage/framework/cache/data';
$count = 0;
if (is_dir($cacheDir)) {
    $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cacheDir, FilesystemIterator::SKIP_DOTS));
    foreach ($iter as $file) {
        if ($file->isFile()) { @unlink($file->getPathname()); $count++; }
    }
}
echo "<pre>✅ Cleared $count cache file(s). Rate limit reset. Try /phone/verify again.</pre>";
@unlink(__FILE__);
