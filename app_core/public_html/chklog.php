<?php
// Get the actual exception message lines from the log (not the stack trace)
$log = '/home/kegalle/app_core/storage/logs/laravel.log';
$content = file_get_contents($log);
// Match all exception message lines (start of each log entry)
preg_match_all('/\[\d{4}-\d{2}-\d{2}[^\]]+\] \w+\.\w+: [^\{]+/m', $content, $m);
$last = array_slice($m[0], -8);
foreach ($last as $line) echo trim($line) . "\n";
unlink(__FILE__);
