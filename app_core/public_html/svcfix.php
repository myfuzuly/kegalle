<?php
$php = '/opt/alt/php83/usr/bin/php';
$a   = '/home/kegalle/app_core/artisan';
echo shell_exec("cd /home/kegalle/app_core && $php $a route:clear 2>&1");
echo shell_exec("cd /home/kegalle/app_core && $php $a cache:clear 2>&1");
$dir = '/home/kegalle/app_core/storage/framework/pagecache';
$del = 0;
foreach (glob($dir . '/*.html') ?: [] as $f) { @unlink($f); $del++; }
echo "Cleared $del pagecache files.\n";
if (function_exists('opcache_reset')) { opcache_reset(); echo "OPcache reset.\n"; }
unlink(__FILE__);
