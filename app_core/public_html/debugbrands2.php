<?php
header('Content-Type: text/plain; charset=UTF-8');
require_once '/home/kegalle/app_core/vendor/autoload.php';
$app = require_once '/home/kegalle/app_core/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check Laravel logs for recent errors
$logFile = '/home/kegalle/app_core/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $last = array_slice($lines, -60);
    echo implode('', $last);
} else {
    echo "No log file found\n";
}
unlink(__FILE__);
