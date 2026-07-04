<?php
// One-time mail diagnostic — prints config shape (no secrets) and attempts a test send.
$lock = __DIR__ . '/mail_diag.lock';
if (file_exists($lock)) die('Already run.');
file_put_contents($lock, date('Y-m-d H:i:s'));

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'mailer: ' . config('mail.default') . '<br>';
echo 'host: ' . config('mail.mailers.smtp.host') . '<br>';
echo 'port: ' . config('mail.mailers.smtp.port') . '<br>';
echo 'encryption: ' . (config('mail.mailers.smtp.encryption') ?? config('mail.mailers.smtp.scheme') ?? 'null') . '<br>';
echo 'username set: ' . (config('mail.mailers.smtp.username') ? 'yes' : 'NO') . '<br>';
echo 'password set: ' . (config('mail.mailers.smtp.password') ? 'yes' : 'NO') . '<br>';
echo 'from: ' . config('mail.from.address') . '<br><br>';

try {
    \Illuminate\Support\Facades\Mail::raw('Contact form delivery test from kurulla.com diagnostics.', function ($m) {
        $m->to(config('mail.from.address'))->subject('Kurulla mail diagnostic test');
    });
    echo '<b>TEST SEND: SUCCESS</b>';
} catch (\Throwable $e) {
    echo '<b>TEST SEND FAILED:</b> ' . get_class($e) . '<br>';
    echo htmlspecialchars(substr($e->getMessage(), 0, 500));
}
