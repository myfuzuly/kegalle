<?php
$lock = __DIR__ . '/add_store_limit.lock';
if (file_exists($lock)) die('Already run.');
file_put_contents($lock, date('Y-m-d H:i:s'));

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasColumn('users', 'store_limit')) {
    Schema::table('users', function (Blueprint $table) {
        $table->unsignedInteger('store_limit')->default(1)->after('status');
    });
    echo "Added store_limit column (default 1).<br>";
} else {
    echo "Column already exists.<br>";
}

// Users previously granted multi-store get a generous limit
if (Schema::hasColumn('users', 'allow_multiple_stores')) {
    $n = DB::table('users')->where('allow_multiple_stores', 1)->update(['store_limit' => 99]);
    echo "Migrated {$n} multi-store user(s) to limit 99.<br>";
}

// Users who already own more stores than their limit keep what they have
foreach (DB::table('stores')->select('user_id', DB::raw('count(*) as c'))->groupBy('user_id')->get() as $row) {
    DB::table('users')->where('id', $row->user_id)->where('store_limit', '<', $row->c)->update(['store_limit' => $row->c]);
}
echo "Adjusted limits for users already over quota.<br>";

echo "<br><b>Done.</b>";
