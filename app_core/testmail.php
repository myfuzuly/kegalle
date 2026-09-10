<?php
/**
 * One-shot SMTP diagnostic — delete after use.
 * Place at: /app_core/testmail.php
 * Hit: https://kurulla.com/testmail.php
 */

// Bootstrap Laravel to use its config/mail setup
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$app->instance('request', Illuminate\Http\Request::capture());
$app->boot();

header('Content-Type: text/html; charset=utf-8');
echo '<pre style="font-family:monospace;padding:20px;font-size:13px">';
echo "=== kegalle SMTP Diagnostic ===\n\n";

$to      = $_GET['to'] ?? 'fuzooly@gmail.com';
$mailer  = config('mail.default');
$host    = config('mail.mailers.smtp.host');
$port    = config('mail.mailers.smtp.port');
$enc     = config('mail.mailers.smtp.encryption');
$from    = config('mail.from.address');
$fromName= config('mail.from.name');

echo "MAIL_MAILER   : $mailer\n";
echo "MAIL_HOST     : $host\n";
echo "MAIL_PORT     : $port\n";
echo "MAIL_ENCRYPTION: $enc\n";
echo "MAIL_FROM     : $from ($fromName)\n";
echo "Sending to    : $to\n\n";

// Test 1: Basic connectivity
echo "--- Test 1: SMTP socket connect ---\n";
$fp = @fsockopen(($enc === 'ssl' ? 'ssl://' : '').$host, $port, $errno, $errstr, 10);
if ($fp) {
    echo "Socket open: OK (".fgets($fp, 256).")\n";
    fclose($fp);
} else {
    echo "Socket FAILED: $errno $errstr\n";
}

// Test 2: Send via Laravel Mail
echo "\n--- Test 2: Send via Laravel Mail::raw() ---\n";
try {
    Illuminate\Support\Facades\Mail::raw(
        "This is a test email from kegalle SMTP diagnostic.\n\nSent at: ".date('Y-m-d H:i:s T')."\nServer: ".$_SERVER['SERVER_NAME']."\n",
        function ($msg) use ($to, $from, $fromName) {
            $msg->to($to)
                ->from($from, $fromName)
                ->subject('[kegalle Test] SMTP delivery check – '.date('H:i:s'));
        }
    );
    echo "Mail::raw() -> OK (no exception)\n";
} catch (\Throwable $e) {
    echo "Mail::raw() FAILED:\n  ".get_class($e).": ".$e->getMessage()."\n";
    if (str_contains($e->getMessage(), 'certificate')) {
        echo "  HINT: SSL cert mismatch — try MAIL_VERIFY_PEER=false in .env\n";
    }
}

// Test 3: Check queue driver
$queueDriver = config('queue.default');
echo "\n--- Test 3: Queue driver = $queueDriver ---\n";
if ($queueDriver === 'sync') {
    echo "Queue is sync — Mail::queue() sends immediately. Good.\n";
} else {
    echo "Queue is '$queueDriver' — Mail::queue() goes to queue table/redis. Make sure a worker is running!\n";
    echo "For shared hosting, use QUEUE_CONNECTION=sync in .env to send emails inline.\n";
}

echo "\n=== Done. Delete this file from /app_core/testmail.php when finished. ===\n";
echo '</pre>';
