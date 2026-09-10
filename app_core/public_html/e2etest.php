<?php
header('Content-Type: text/plain; charset=UTF-8');
$php = '/opt/alt/php83/usr/bin/php';
$art = '/home/kegalle/app_core/artisan';
echo shell_exec("cd /home/kegalle/app_core && {$php} {$art} e2e:run --no-ansi 2>&1");
unlink(__FILE__);
