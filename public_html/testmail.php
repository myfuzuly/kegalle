<?php
/**
 * One-shot SMTP diagnostic — delete after use.
 * Accessible at: https://kurulla.com/testmail.php
 */

define('LARAVEL_START', microtime(true));
require __DIR__.'/../app_core/vendor/autoload.php';
$app = require_once __DIR__.'/../app_core/bootstrap/app.php';
$app->instance('request', Illuminate\Http\Request::capture());
$app->boot();

header('Content-Type: text/html; charset=utf-8');
echo '<pre style="font-family:monospace;padding:20px;font-size:13px">';
echo "=== kegalle SMTP Diagnostic ===\n\n";

$to      = $_GET['to'] ?? 'fuzooly@gmail.com';
$host    = config('mail.mailers.smtp.host');
$port    = config('mail.mailers.smtp.port');
$enc     = config('mail.mailers.smtp.encryption');
$from    = config('mail.from.address');
$fromName= config('mail.from.name');

echo "MAIL_HOST     : $host\n";
echo "MAIL_PORT     : $port\n";
echo "MAIL_ENCRYPTION: $enc\n";
echo "MAIL_FROM     : $from ($fromName)\n";
echo "Sending to    : $to\n\n";

// Test 1: SMTP socket
echo "--- Test 1: TCP socket to SMTP host ---\n";
$addr = ($enc === 'ssl' ? 'ssl://' : '').$host;
$fp = @fsockopen($addr, (int)$port, $errno, $errstr, 10);
if ($fp) {
    $banner = fgets($fp, 256);
    echo "OK — server banner: $banner";
    fclose($fp);
} else {
    echo "FAILED: [$errno] $errstr\n";
}

// Test 2: Send via Laravel
echo "\n--- Test 2: Mail::raw() send ---\n";
try {
    Illuminate\Support\Facades\Mail::raw(
        "kegalle SMTP test\nSent: ".date('Y-m-d H:i:s T'),
        function ($m) use ($to, $from, $fromName) {
            $m->to($to)->from($from, $fromName)
              ->subject('[kegalle] SMTP test '.date('H:i:s'));
        }
    );
    echo "SUCCESS — check $to inbox\n";
} catch (\Throwable $e) {
    echo "FAILED: ".get_class($e)."\n".$e->getMessage()."\n";
}

// Test 3: Queue driver
$q = config('queue.default');
echo "\n--- Test 3: Queue driver = $q ---\n";
if ($q !== 'sync') {
    echo "WARNING: queue driver is '$q' — Mail::queue() sits in a queue table and won't\n";
    echo "send until a worker runs. Add QUEUE_CONNECTION=sync to .env for shared hosting.\n";
} else {
    echo "OK — sync queue, emails send immediately.\n";
}

echo "\n=== Done. Delete /public_html/testmail.php when finished. ===\n";
echo '</pre>';
