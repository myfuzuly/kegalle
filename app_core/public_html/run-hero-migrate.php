<?php
header('Content-Type: text/plain; charset=UTF-8');
$php = '/opt/alt/php83/usr/bin/php';
$art = '/home/kegalle/app_core/artisan';
echo shell_exec("cd /home/kegalle/app_core && {$php} {$art} migrate --force --path=database/migrations/2026_08_22_000001_create_hero_slides_table.php --no-ansi 2>&1");
echo "\n";
unlink(__FILE__);
