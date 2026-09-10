<?php
header('Content-Type: text/plain; charset=UTF-8');
// Find any TTF fonts on the server
$found = [];
$dirs = ['/usr/share/fonts', '/usr/local/share/fonts', '/opt/fonts', '/home/kegalle/app_core/resources', '/var/www'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($iter as $file) {
        if (strtolower($file->getExtension()) === 'ttf') {
            $found[] = $file->getPathname();
        }
    }
}
if ($found) {
    echo implode("\n", $found);
} else {
    echo "No TTF fonts found in standard locations.\n";
}
unlink(__FILE__);
