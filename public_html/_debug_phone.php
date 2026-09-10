<?php
// Show last 50 lines of Laravel log
$log = __DIR__ . '/../app_core/storage/logs/laravel.log';
if (!file_exists($log)) {
    echo "Log not found at: $log";
    exit;
}
$lines = file($log);
$last = array_slice($lines, -80);
echo "<pre style='font-size:12px;white-space:pre-wrap'>";
echo htmlspecialchars(implode('', $last));
echo "</pre>";
