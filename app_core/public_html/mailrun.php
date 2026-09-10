<?php
define('LARAVEL_START', microtime(true));
require '/home/kegalle/app_core/vendor/autoload.php';
$app = require_once '/home/kegalle/app_core/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    Illuminate\Support\Facades\Mail::raw('SMTP test from kurulla.com - ' . date('Y-m-d H:i:s'), function ($m) {
        $m->to('fuzooly@gmail.com')->subject('kegalle SMTP Test');
    });
    echo "MAIL_SENT_OK\n";
} catch (Exception $e) {
    echo 'FAILED: ' . $e->getMessage() . "\n";
}
