<?php
require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $count = DB::table('events')->count();
    echo "Events count: $count\n";
    $events = DB::table('events')->select('id','title','status','event_date')->orderBy('event_date','desc')->take(6)->get();
    foreach ($events as $e) {
        echo "ID:{$e->id} | {$e->title} | status:{$e->status} | date:{$e->event_date}\n";
    }
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage();
}
