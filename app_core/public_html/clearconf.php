<?php
$php = '/opt/alt/php83/usr/bin/php';
$a   = '/home/kegalle/app_core/artisan';
echo shell_exec("cd /home/kegalle/app_core && $php $a config:clear 2>&1") . "\n";
echo shell_exec("cd /home/kegalle/app_core && $php $a route:clear 2>&1") . "\n";
// Test middleware is registered
echo "bootstrap/app.php SecurityHeaders: ";
$content = file_get_contents('/home/kegalle/app_core/bootstrap/app.php');
echo (str_contains($content, 'SecurityHeaders') ? 'FOUND' : 'MISSING') . "\n";
unlink(__FILE__);
