<?php
/**
 * One-time setup: create push tables + generate VAPID keys.
 * Visit: https://kurulla.com/setup_webpush.php
 * Copy the VAPID keys shown into your .env, then delete this file.
 */

$guard = __DIR__ . '/setup_webpush.lock';
if (file_exists($guard)) { die('Already run. Delete .lock to re-run.'); }

require_once __DIR__ . '/../app_core/vendor/autoload.php';
$app    = require_once __DIR__ . '/../app_core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

$log = [];

try {
    // 1. push_subscriptions
    if (!Schema::hasTable('push_subscriptions')) {
        Schema::create('push_subscriptions', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id');
            $t->text('endpoint');
            $t->text('p256dh');
            $t->string('auth_token', 50);
            $t->timestamps();
            $t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $t->index('user_id');
        });
        $log[] = '<span style="color:green">✓ Created push_subscriptions table</span>';
    } else {
        $log[] = '<span style="color:orange">⚠ push_subscriptions already exists</span>';
    }

    // 2. push_notifications
    if (!Schema::hasTable('push_notifications')) {
        Schema::create('push_notifications', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id');
            $t->string('title');
            $t->string('body')->nullable();
            $t->string('url')->nullable();
            $t->timestamp('shown_at')->nullable();
            $t->timestamps();
            $t->index(['user_id', 'shown_at']);
        });
        $log[] = '<span style="color:green">✓ Created push_notifications table</span>';
    } else {
        $log[] = '<span style="color:orange">⚠ push_notifications already exists</span>';
    }

    // 3. Generate VAPID keys
    $vapidKeys = app(\App\Services\VapidService::class)->generateKeys();

    $log[] = '<span style="color:green">✓ VAPID keys generated</span>';

    file_put_contents($guard, date('Y-m-d H:i:s'));

} catch (Throwable $e) {
    $log[] = '<span style="color:red">✗ ' . htmlspecialchars($e->getMessage()) . '</span>';
    $vapidKeys = null;
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Web Push Setup</title>
<style>body{font-family:monospace;padding:30px;max-width:800px}
pre{background:#1a1a2e;color:#0f3;padding:20px;border-radius:8px;white-space:pre-wrap;word-break:break-all}
.copy-btn{background:#00C853;color:#fff;border:none;padding:8px 16px;border-radius:6px;cursor:pointer;font-size:13px;margin-top:8px}
</style></head>
<body>
<h2>🔔 Web Push Setup</h2>
<ul><?= implode('', array_map(fn($l) => "<li>$l</li>", $log)) ?></ul>

<?php if ($vapidKeys): ?>
<h3>Add these to your <code>.env</code> on the server:</h3>
<pre id="envBlock">WEBPUSH_PUBLIC_KEY=<?= $vapidKeys['public_key'] ?>

WEBPUSH_PRIVATE_KEY="<?= str_replace(["\r", "\n"], ['', '\n'], $vapidKeys['private_pem']) ?>"

WEBPUSH_SUBJECT=mailto:admin@kurulla.com</pre>
<button class="copy-btn" onclick="navigator.clipboard.writeText(document.getElementById('envBlock').textContent).then(()=>this.textContent='Copied!')">Copy .env block</button>

<h3>Also add to <code>config/services.php</code> (already done if you deployed):</h3>
<pre>'webpush' => [
    'public_key'  => env('WEBPUSH_PUBLIC_KEY'),
    'private_key' => env('WEBPUSH_PRIVATE_KEY'),
    'subject'     => env('WEBPUSH_SUBJECT', 'mailto:admin@kurulla.com'),
],</pre>

<p style="color:#c00"><strong>⚠ Delete this file from the server after copying the keys!</strong></p>
<p>The private key above is in PEM format. You can paste it into .env with literal \n for newlines.</p>
<?php endif; ?>
</body>
</html>
