<?php
// Install E2ETestRunner command on the server, then run it
$target = '/home/kegalle/app_core/app/Console/Commands/E2ETestRunner.php';
$src = base64_decode('PLACEHOLDER');
file_put_contents($target, $src);
echo "Installed: $target\n";
echo "Size: " . filesize($target) . " bytes\n";

$php = '/opt/alt/php83/usr/bin/php';
$art = '/home/kegalle/app_core/artisan';
header('Content-Type: text/plain; charset=UTF-8');
echo shell_exec("cd /home/kegalle/app_core && {$php} {$art} e2e:run --no-ansi 2>&1");
unlink(__FILE__);
