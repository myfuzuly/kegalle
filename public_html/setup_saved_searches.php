<?php
/**
 * One-time setup: creates the saved_searches table.
 * Run once via browser: https://kurulla.com/setup_saved_searches.php
 * Delete this file immediately after running.
 */

$guard = __DIR__ . '/setup_saved_searches.lock';
if (file_exists($guard)) {
    die('Already run. Delete the .lock file to re-run (not recommended).');
}

require_once __DIR__ . '/../app_core/vendor/autoload.php';
$app = require_once __DIR__ . '/../app_core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    if (Schema::hasTable('saved_searches')) {
        echo '<p style="color:orange">Table <code>saved_searches</code> already exists — nothing to do.</p>';
    } else {
        Schema::create('saved_searches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('label')->nullable();
            $table->text('params');
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        echo '<p style="color:green">✓ Table <code>saved_searches</code> created successfully.</p>';
    }

    file_put_contents($guard, date('Y-m-d H:i:s'));
    echo '<p>Lock file written. <strong>Delete this PHP file from the server now.</strong></p>';
} catch (Throwable $e) {
    echo '<p style="color:red">Error: '.$e->getMessage().'</p>';
}
