<?php
if (($_GET['token'] ?? '') !== 'fvt_mX5nZqW8pCj2rTkH6aLsD9yEbNu4') {
    http_response_code(403); exit('Forbidden');
}
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$exitCode = Artisan::call('migrate', ['--force' => true, '--path' => 'database/migrations/2026_08_22_000001_create_hero_slides_table.php']);
echo '<pre>Exit: '.$exitCode."\n".Artisan::output().'</pre>';
