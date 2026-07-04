<?php
$guardKey = 'ev_create_2025_kx9m';
if (!isset($_GET['key']) || $_GET['key'] !== $guardKey) { die('Unauthorized'); }
$lockFile = __DIR__ . '/create_events_table.lock';
if (file_exists($lockFile)) { die('Already executed'); }

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    \Illuminate\Support\Facades\Schema::create('events', function ($table) {
        $table->id();
        $table->string('title');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->date('event_date');
        $table->datetime('starts_at')->nullable();
        $table->datetime('ends_at')->nullable();
        $table->string('location')->nullable();
        $table->string('venue')->nullable();
        $table->decimal('price', 10, 2)->default(0);
        $table->boolean('is_free')->default(false);
        $table->boolean('is_featured')->default(false);
        $table->string('image')->nullable();
        $table->unsignedBigInteger('category_id')->nullable();
        $table->unsignedBigInteger('store_id')->nullable();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->string('status')->default('draft');
        $table->unsignedInteger('views')->default(0);
        $table->string('event_type')->default('offline');
        $table->string('organizer_name')->nullable();
        $table->timestamps();

        $table->index('event_date');
        $table->index('status');
        $table->index('category_id');
    });
    file_put_contents($lockFile, date('Y-m-d H:i:s'));
    echo 'SUCCESS: events table created';
} catch (\Throwable $e) {
    echo 'ERROR: ' . $e->getMessage();
}
