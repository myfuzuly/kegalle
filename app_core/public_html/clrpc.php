<?php
$dir = '/home/kegalle/app_core/storage/framework/pagecache';
$del = 0;
foreach (glob($dir . '/*.html') ?: [] as $f) { @unlink($f); $del++; }
echo "Cleared $del pagecache files\n";
unlink(__FILE__);
