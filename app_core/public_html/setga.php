<?php
$php  = '/opt/alt/php83/usr/bin/php';
$a    = '/home/kegalle/app_core/artisan';
$env  = '/home/kegalle/app_core/.env';

$contents = file_get_contents($env);

// Update or append GA_MEASUREMENT_ID
if (preg_match('/^GA_MEASUREMENT_ID=.*/m', $contents)) {
    $contents = preg_replace('/^GA_MEASUREMENT_ID=.*/m', 'GA_MEASUREMENT_ID=G-7TD5JH5933', $contents);
} else {
    $contents .= "\nGA_MEASUREMENT_ID=G-7TD5JH5933\n";
}

file_put_contents($env, $contents);
echo "GA_MEASUREMENT_ID set\n";

// Clear and rebuild config cache
echo shell_exec("cd /home/kegalle/app_core && $php $a config:clear 2>&1");
echo shell_exec("cd /home/kegalle/app_core && $php $a config:cache 2>&1");

// Verify
$env2 = file_get_contents($env);
preg_match('/^GA_MEASUREMENT_ID=(.+)/m', $env2, $m);
echo "Verified in .env: " . trim($m[1] ?? 'NOT FOUND') . "\n";

unlink(__FILE__);
