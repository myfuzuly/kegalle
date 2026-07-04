<?php
$lock = __DIR__ . '/add_store_coords.lock';
if (file_exists($lock)) die('Already run.');
file_put_contents($lock, date('Y-m-d H:i:s'));

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasColumn('stores', 'latitude')) {
    Schema::table('stores', function (Blueprint $table) {
        $table->decimal('latitude', 10, 7)->nullable()->after('city');
        $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
    });
    echo "Added latitude/longitude columns to stores table.<br>";
} else {
    echo "Columns already exist.<br>";
}
echo "Done.";
