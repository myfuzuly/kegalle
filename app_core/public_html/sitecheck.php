<?php
$php = '/opt/alt/php83/usr/bin/php';
$a   = '/home/kegalle/app_core/artisan';

echo "=== SCHEDULER LOG (last 20 lines) ===\n";
$log = '/home/kegalle/app_core/storage/logs/scheduler.log';
if (file_exists($log)) {
    $lines = file($log);
    echo implode('', array_slice($lines, -20));
} else {
    echo "No scheduler log yet\n";
}

echo "\n=== CRON ENTRY ===\n";
echo shell_exec("crontab -l 2>&1 | grep artisan");

echo "\n=== BACKUPS ===\n";
$backups = glob('/home/kegalle/backups/kegalle-*.sql.gz') ?: [];
if ($backups) {
    foreach ($backups as $b) {
        echo basename($b) . " — " . round(filesize($b)/1048576,2) . " MB — " . date('Y-m-d H:i', filemtime($b)) . "\n";
    }
} else {
    echo "No backups found\n";
}

echo "\n=== PAGECACHE FILES ===\n";
$pc = glob('/home/kegalle/app_core/storage/framework/pagecache/*.html') ?: [];
echo count($pc) . " files\n";
foreach ($pc as $f) {
    echo basename($f)." ".round(filesize($f)/1024)."KB ".date('H:i:s', filemtime($f))."\n";
}

echo "\n=== SEARCHCACHE FILES ===\n";
$sc = glob('/home/kegalle/app_core/storage/framework/searchcache/*.json') ?: [];
echo count($sc) . " files\n";

echo "\n=== LARAVEL LOG (errors last 15 lines) ===\n";
$laravelLog = '/home/kegalle/app_core/storage/logs/laravel.log';
if (file_exists($laravelLog)) {
    $lines = file($laravelLog);
    $errors = array_filter($lines, fn($l) => str_contains($l, 'ERROR') || str_contains($l, 'CRITICAL'));
    $recent = array_slice($errors, -15);
    echo $recent ? implode('', $recent) : "No errors\n";
} else {
    echo "No laravel log\n";
}

unlink(__FILE__);
