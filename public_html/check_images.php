<?php
$paths = [
    '/home/kegalle/public_html/storage',
    '/home/kegalle/public_html/storage/listings',
    '/home/kegalle/app_core/storage/app/public',
    '/home/kegalle/app_core/storage/app/public/listings',
];

foreach ($paths as $p) {
    echo "<p><b>$p</b>: ";
    if (is_link($p)) {
        echo "SYMLINK -> " . readlink($p);
    } elseif (is_dir($p)) {
        $files = scandir($p);
        echo "DIR (" . (count($files)-2) . " items): " . implode(', ', array_slice(array_diff($files, ['.', '..']), 0, 10));
    } elseif (file_exists($p)) {
        echo "FILE";
    } else {
        echo "<span style='color:red'>NOT FOUND</span>";
    }
    echo "</p>";
}

// Check specific file
$file = '/home/kegalle/app_core/storage/app/public/listings/aTBE0qlkEQpGyYYDbnEZbJbVRPTs2MF3538yWlvA.jpg';
echo "<p><b>Specific image</b>: " . (file_exists($file) ? "EXISTS (" . filesize($file) . " bytes)" : "<span style='color:red'>NOT FOUND</span>") . "</p>";

// Check via public_html/storage path
$file2 = '/home/kegalle/public_html/storage/listings/aTBE0qlkEQpGyYYDbnEZbJbVRPTs2MF3538yWlvA.jpg';
echo "<p><b>Via public symlink</b>: " . (file_exists($file2) ? "EXISTS (" . filesize($file2) . " bytes)" : "<span style='color:red'>NOT FOUND</span>") . "</p>";
