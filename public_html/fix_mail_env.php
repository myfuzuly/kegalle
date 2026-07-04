<?php
// One-time: correct MAIL_PASSWORD in .env and re-test sending. No secrets printed.
$lock = __DIR__ . '/fix_mail_env.lock';
if (file_exists($lock)) die('Already run.');
file_put_contents($lock, date('Y-m-d H:i:s'));

$envPath = __DIR__ . '/../app_core/.env';
$env = file_get_contents($envPath);
if ($env === false) die('Cannot read .env');

$newPassword = '7rb6StGjaWDS';
if (preg_match('/^MAIL_PASSWORD=.*$/m', $env)) {
    $env = preg_replace('/^MAIL_PASSWORD=.*$/m', 'MAIL_PASSWORD="' . $newPassword . '"', $env);
    echo "MAIL_PASSWORD updated.<br>";
} else {
    $env .= "\nMAIL_PASSWORD=\"" . $newPassword . "\"\n";
    echo "MAIL_PASSWORD added.<br>";
}
if (!preg_match('/^MAIL_ENCRYPTION=tls/m', $env)) {
    if (preg_match('/^MAIL_ENCRYPTION=.*$/m', $env)) {
        $env = preg_replace('/^MAIL_ENCRYPTION=.*$/m', 'MAIL_ENCRYPTION=tls', $env);
    } else {
        $env .= "\nMAIL_ENCRYPTION=tls\n";
    }
    echo "MAIL_ENCRYPTION set to tls.<br>";
}
file_put_contents($envPath, $env);

// Clear cached config if present
$cached = __DIR__ . '/../app_core/bootstrap/cache/config.php';
if (file_exists($cached)) { unlink($cached); echo "Config cache cleared.<br>"; }

// Re-test
require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    \Illuminate\Support\Facades\Mail::raw('Contact form delivery test after password fix.', function ($m) {
        $m->to(config('mail.from.address'))->subject('Kurulla mail test — password fix');
    });
    echo '<b>TEST SEND: SUCCESS</b>';
} catch (\Throwable $e) {
    echo '<b>TEST SEND FAILED:</b> ' . get_class($e) . '<br>' . htmlspecialchars(substr($e->getMessage(), 0, 300));
}
