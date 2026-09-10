<?php
header('Content-Type: text/plain; charset=UTF-8');
$logFile = '/home/kegalle/app_core/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    // Get last error block
    $parts = explode('[20', $content);
    $last3 = array_slice($parts, -3);
    echo '[20' . implode('[20', $last3);
}
unlink(__FILE__);
