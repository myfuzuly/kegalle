<?php
$lock = __DIR__ . '/add_multi_store_flag.lock';
if (file_exists($lock)) die('Already run.');
file_put_contents($lock, date('Y-m-d H:i:s'));

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasColumn('users', 'allow_multiple_stores')) {
    Schema::table('users', function (Blueprint $table) {
        $table->boolean('allow_multiple_stores')->default(false)->after('status');
    });
    echo "Added allow_multiple_stores column to users table.<br>";
} else {
    echo "Column already exists.<br>";
}
echo "Done.";
