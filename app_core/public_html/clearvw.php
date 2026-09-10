<?php
$php = '/opt/alt/php83/usr/bin/php';
$a   = '/home/kegalle/app_core/artisan';

// Clear compiled views
echo shell_exec("cd /home/kegalle/app_core && $php $a view:clear 2>&1");
// Recompile
echo shell_exec("cd /home/kegalle/app_core && $php $a view:cache 2>&1");

// Also clear pagecache
$dir = '/home/kegalle/app_core/storage/framework/pagecache';
$del = 0;
foreach (glob($dir . '/*.html') ?: [] as $f) { @unlink($f); $del++; }
echo "Cleared $del pagecache files\n";

unlink(__FILE__);
