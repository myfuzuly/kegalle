<?php
$paths = [
    '/home/kurulla/app_core/routes/web.php',
    '/home/kurulla/public_html/app_core/routes/web.php',
];
foreach ($paths as $p) {
    echo "$p: " . (file_exists($p) ? 'EXISTS (' . filesize($p) . ' bytes)' : 'NOT FOUND') . "\n";
    if (file_exists($p) && strpos(file_get_contents($p), "'/saved'") !== false) {
        echo "  -> Contains /saved route\n";
    }
}

// Check if they're the same via symlink
echo "\nis /home/kurulla/public_html/app_core a symlink? " . (is_link('/home/kurulla/public_html/app_core') ? 'YES -> ' . readlink('/home/kurulla/public_html/app_core') : 'NO') . "\n";
echo "is /home/kurulla/public_html/app_core a dir? " . (is_dir('/home/kurulla/public_html/app_core') ? 'YES' : 'NO') . "\n";
