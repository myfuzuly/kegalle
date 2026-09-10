<?php
$dir = '/home/kegalle/public_html/css/';
$files = glob($dir . '*.css');
foreach ($files as $f) {
    echo basename($f) . ' (' . number_format(filesize($f)) . ' bytes)' . "\n";
}
unlink(__FILE__);
