<?php
$lock = __DIR__ . '/drop_brand_group.lock';
if (file_exists($lock)) die('Already run.');
file_put_contents($lock, date('Y-m-d H:i:s'));

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;

if (Schema::hasTable('brand_category_group')) {
    Schema::drop('brand_category_group');
    echo "Dropped brand_category_group table.<br>";
} else {
    echo "Table does not exist.<br>";
}
echo "Done.";
