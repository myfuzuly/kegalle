<?php
$php = '/opt/alt/php83/usr/bin/php';
$a   = '/home/kegalle/app_core/artisan';

// Clear pagecache files
$dir = '/home/kegalle/app_core/storage/framework/pagecache';
$del = 0;
foreach (glob($dir . '/*.html') ?: [] as $f) { @unlink($f); $del++; }
echo "Deleted $del pagecache files\n";

// Clear route cache and config cache
echo shell_exec("cd /home/kegalle/app_core && $php $a route:clear 2>&1");
echo shell_exec("cd /home/kegalle/app_core && $php $a config:clear 2>&1");
echo "Done\n";
unlink(__FILE__);
