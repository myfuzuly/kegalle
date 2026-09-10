<?php
header('Content-Type: text/plain; charset=UTF-8');
// Delete the stale compiled view directly
$compiled = '/home/kegalle/app_core/storage/framework/views/d3ef7fdcd79172103b72dbeaeb9777ff.php';
if (file_exists($compiled)) {
    unlink($compiled);
    echo "Deleted compiled view\n";
} else {
    echo "Compiled view not found\n";
}
// Show the source file
$src = '/home/kegalle/app_core/resources/views/admin/brands/index.blade.php';
echo "Source size: " . filesize($src) . " bytes\n";
echo "Last 500 chars:\n";
echo substr(file_get_contents($src), -500);
unlink(__FILE__);
