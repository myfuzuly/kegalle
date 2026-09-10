<?php
header('Content-Type: text/plain; charset=UTF-8');
$compiled = '/home/kegalle/app_core/storage/framework/views/d3ef7fdcd79172103b72dbeaeb9777ff.php';
if (file_exists($compiled)) {
    echo file_get_contents($compiled);
} else {
    echo "File not found - not compiled yet\n";
    require_once '/home/kegalle/app_core/vendor/autoload.php';
    $app = require_once '/home/kegalle/app_core/bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    $src = '/home/kegalle/app_core/resources/views/admin/brands/index.blade.php';
    $compiler = app('blade.compiler');
    $compiler->compile($src);
    echo file_get_contents($compiled);
}
unlink(__FILE__);
