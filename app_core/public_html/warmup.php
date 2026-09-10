<?php
// Clear all pagecache so fresh HTML is written with new layout
$dir = '/home/kegalle/app_core/storage/framework/pagecache';
$del = 0;
foreach (glob($dir . '/*.html') ?: [] as $f) { @unlink($f); $del++; }
echo "Cleared $del pagecache files\n";

// Verify randomAds fix
$cacheDir = '/home/kegalle/app_core/storage/framework/searchcache';
echo "searchcache writable: " . (is_writable($cacheDir) ? 'YES' : 'NO') . "\n";

unlink(__FILE__);
