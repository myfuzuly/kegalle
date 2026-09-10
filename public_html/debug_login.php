<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../app_core/vendor/autoload.php';
$app = require_once __DIR__.'/../app_core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/login', 'GET');
$response = $kernel->handle($request);
$html = $response->getContent();
$checks = [
  'k-social-google'   => strpos($html, 'k-social-google') !== false,
  'Google SVG red'    => strpos($html, 'EA4335') !== false,
  'Continue with Google' => strpos($html, 'Continue with Google') !== false,
  'auth/google/redirect' => strpos($html, 'auth/google/redirect') !== false,
  'k-social-grid gone'  => strpos($html, 'k-social-grid') === false,
];
foreach($checks as $k=>$v) echo ($v?'✅':'❌').' '.$k."\n";
