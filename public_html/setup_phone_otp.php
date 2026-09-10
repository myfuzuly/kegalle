<?php
/**
 * One-time setup: creates phone_otps table + adds phone_verified_at to users.
 * Visit: https://kurulla.com/setup_phone_otp.php
 * Delete this file immediately after running.
 */

$guard = __DIR__ . '/setup_phone_otp.lock';
if (file_exists($guard)) {
    die('Already run. Delete the .lock file to re-run.');
}

require_once __DIR__ . '/../app_core/vendor/autoload.php';
$app = require_once __DIR__ . '/../app_core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

$log = [];

try {
    // 1. phone_otps table
    if (Schema::hasTable('phone_otps')) {
        $log[] = '<span style="color:orange">⚠ Table <code>phone_otps</code> already exists — skipped.</span>';
    } else {
        Schema::create('phone_otps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('phone', 30);
            $table->string('code', 6);
            $table->boolean('used')->default(false);
            $table->timestamp('expires_at');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'code', 'used']);
        });
        $log[] = '<span style="color:green">✓ Created <code>phone_otps</code> table.</span>';
    }

    // 2. phone_verified_at column on users
    if (Schema::hasColumn('users', 'phone_verified_at')) {
        $log[] = '<span style="color:orange">⚠ Column <code>users.phone_verified_at</code> already exists — skipped.</span>';
    } else {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
        });
        $log[] = '<span style="color:green">✓ Added <code>users.phone_verified_at</code> column.</span>';
    }

    file_put_contents($guard, date('Y-m-d H:i:s'));
    $log[] = '<p><strong>Lock file written. Delete this PHP file from the server now.</strong></p>';

} catch (Throwable $e) {
    $log[] = '<span style="color:red">✗ Error: '.$e->getMessage().'</span>';
}

echo '<html><body style="font-family:monospace;padding:30px">';
echo '<h2>Phone OTP Setup</h2>';
echo '<ul>'.implode('', array_map(fn($l) => "<li>$l</li>", $log)).'</ul>';
echo '</body></html>';
