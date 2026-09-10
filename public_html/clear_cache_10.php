<?php
$lock = __DIR__.'/.clear_cache_10.lock';
if (file_exists($lock)) { echo 'Already ran.'; exit; }

$bootstrapPath = dirname(__DIR__).'/app_core/bootstrap/app.php';
$app = require $bootstrapPath;
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Illuminate\Support\Facades\Artisan::call('view:clear');
Illuminate\Support\Facades\Artisan::call('cache:clear');
Illuminate\Support\Facades\Artisan::call('config:clear');
Illuminate\Support\Facades\Artisan::call('route:clear');

file_put_contents($lock, date('c'));
echo 'Cache cleared. Views, config, route, and app cache flushed.';
