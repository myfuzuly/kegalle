<?php
header('Content-Type: text/plain; charset=UTF-8');
// Force a compile by touching the view
$src = '/home/kegalle/app_core/resources/views/admin/brands/index.blade.php';
touch($src);
// Trigger compilation via Blade compiler
require_once '/home/kegalle/app_core/vendor/autoload.php';
$app = require_once '/home/kegalle/app_core/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$compiler = app('blade.compiler');
$compiler->compile($src);
$compiled = $compiler->getCompiledPath($src);
echo "Compiled path: $compiled\n";
echo "---\n";
$lines = file($compiled);
// Show around line 83
$start = max(0, 75);
$end = min(count($lines), 90);
for ($i = $start; $i < $end; $i++) {
    echo ($i+1) . ": " . $lines[$i];
}
unlink(__FILE__);
