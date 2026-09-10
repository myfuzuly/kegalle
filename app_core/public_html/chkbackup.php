<?php
$dir = '/home/kegalle/app_core/storage/framework/pagecache';
$files = glob($dir . '/*.html') ?: [];
echo count($files) . " pagecache files:\n";
foreach ($files as $f) {
    echo basename($f) . " " . round(filesize($f)/1024) . "KB " . date('H:i:s', filemtime($f)) . "\n";
}
// Compute expected key for /listings?q=phone
$expected = 'pagecache_' . sha1('listings' . '?' . 'q=phone');
echo "\nExpected key for /listings?q=phone: $expected\n";
echo "File exists: " . (file_exists($dir.'/'.$expected.'.html') ? 'YES' : 'NO') . "\n";
// Also check what index.php would compute
$rawPath = '/listings';
$path = $rawPath === '/' ? '/' : ltrim($rawPath, '/');
$qs = 'q=phone';
$idxKey = 'pagecache_' . sha1($path . '?' . $qs);
echo "index.php key: $idxKey\n";
echo "Match: " . ($expected === $idxKey ? 'YES' : 'NO') . "\n";
unlink(__FILE__);
