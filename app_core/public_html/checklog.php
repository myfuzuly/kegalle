<?php
header('Content-Type: text/plain; charset=UTF-8');
$log = '/home/kegalle/app_core/storage/logs/laravel.log';
$content = file_get_contents($log);
// Get last exception block
preg_match_all('/\{"message":"([^"]+)","exception":"([^"]+)"/', $content, $m);
$last = count($m[1]) - 1;
if ($last >= 0) {
    echo "Message: " . $m[1][$last] . "\n";
    echo "Exception: " . $m[2][$last] . "\n";
}
// Also get last 5 messages
$msgs = array_slice($m[1], -5);
echo "\nLast 5 errors:\n";
foreach ($msgs as $msg) echo "- $msg\n";
unlink(__FILE__);
