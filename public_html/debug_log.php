<?php
$log = file_get_contents(__DIR__.'/../app_core/storage/logs/laravel.log');
// Find the last log entry (starts with [YYYY-)
preg_match_all('/\[\d{4}-\d{2}-\d{2}/', $log, $matches, PREG_OFFSET_CAPTURE);
if (!empty($matches[0])) {
    $last = end($matches[0]);
    $pos = $last[1];
    echo '<pre>' . htmlspecialchars(substr($log, $pos, 1500)) . '</pre>';
} else {
    echo '<pre>' . htmlspecialchars(substr($log, -800)) . '</pre>';
}
