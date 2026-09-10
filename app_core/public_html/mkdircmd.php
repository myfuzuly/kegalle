<?php
$dir = '/home/kegalle/app_core/app/Console/Commands';
echo "Directory exists: " . (is_dir($dir) ? 'YES' : 'NO') . "\n";
echo "Directory writable: " . (is_writable($dir) ? 'YES' : 'NO') . "\n";
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
    echo "Created: $dir\n";
}
// Write a marker file to confirm write access
file_put_contents($dir . '/.write_test', 'ok');
echo "Write test: " . (file_exists($dir . '/.write_test') ? 'OK' : 'FAILED') . "\n";
@unlink($dir . '/.write_test');
unlink(__FILE__);
