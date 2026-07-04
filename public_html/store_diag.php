<?php
$lock = __DIR__ . '/store_diag.lock';
if (file_exists($lock)) die('Already run.');
file_put_contents($lock, date('Y-m-d H:i:s'));

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

foreach (DB::table('stores')->get(['id', 'name', 'slug', 'status', 'user_id']) as $s) {
    echo "#{$s->id} | {$s->name} | slug={$s->slug} | status=[{$s->status}] | user={$s->user_id}<br>";
}
echo '<br>Frontend query count: ' . \App\Models\Store::query()->whereIn('status', ['approved', 'published', 'active'])->count();
