<?php
$envFile = '/home/kegalle/app_core/.env';
$env = file_get_contents($envFile);
$env = preg_replace('/^SESSION_DRIVER=.*/m', 'SESSION_DRIVER=file', $env);
$env = preg_replace('/^CACHE_STORE=.*/m',   'CACHE_STORE=file',   $env);
file_put_contents($envFile, $env);
echo "Done. SESSION_DRIVER=file, CACHE_STORE=file\n";

// Clear all Laravel caches so they rebuild with the new driver
$php = '/opt/alt/php83/usr/bin/php';
$a   = '/home/kegalle/app_core/artisan';
echo shell_exec("cd /home/kegalle/app_core && $php $a config:cache 2>&1");
echo shell_exec("cd /home/kegalle/app_core && $php $a cache:clear 2>&1");
unlink(__FILE__);
