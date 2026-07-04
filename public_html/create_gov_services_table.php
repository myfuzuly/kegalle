<?php
$lockFile = __DIR__ . '/create_gov_services_table.lock';
if (file_exists($lockFile)) { die('Already executed.'); }

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (Schema::hasTable('government_services')) {
    file_put_contents($lockFile, date('Y-m-d H:i:s'));
    die('Table already exists.');
}

Schema::create('government_services', function (Blueprint $table) {
    $table->id();
    $table->string('title', 120);
    $table->string('slug', 140)->unique();
    $table->string('description', 500)->nullable();
    $table->string('icon', 10)->default('🏛️');
    $table->string('icon_bg_start', 9)->default('#1e6b3a');
    $table->string('icon_bg_end', 9)->default('#2e9b5a');
    $table->string('image')->nullable();
    $table->text('content')->nullable();
    $table->string('phone', 100)->nullable();
    $table->string('email', 190)->nullable();
    $table->string('address', 500)->nullable();
    $table->string('map_url', 500)->nullable();
    $table->integer('sort_order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

file_put_contents($lockFile, date('Y-m-d H:i:s'));
echo 'government_services table created successfully.';
