<?php
// One-time: refresh 2025-dated demo content to 2026 (titles/descriptions only, slugs untouched).
$lock = __DIR__ . '/refresh_demo_dates.lock';
if (file_exists($lock)) die('Already run.');
file_put_contents($lock, date('Y-m-d H:i:s'));

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$updated = 0;

// Events: fix "2025" in title/description
foreach (DB::table('events')->get() as $e) {
    $changes = [];
    if (strpos($e->title ?? '', '2025') !== false) $changes['title'] = str_replace('2025', '2026', $e->title);
    if (isset($e->description) && strpos($e->description ?? '', '2025') !== false) $changes['description'] = str_replace('2025', '2026', $e->description);
    if ($changes) {
        DB::table('events')->where('id', $e->id)->update($changes);
        $updated++;
        echo "Event #{$e->id}: " . ($changes['title'] ?? $e->title) . "<br>";
    }
}

// Blog posts: fix "2025" in title only (slug untouched to keep URLs working)
foreach (DB::table('posts')->get() as $p) {
    if (strpos($p->title ?? '', '2025') !== false) {
        DB::table('posts')->where('id', $p->id)->update(['title' => str_replace('2025', '2026', $p->title)]);
        $updated++;
        echo "Post #{$p->id}: " . str_replace('2025', '2026', $p->title) . "<br>";
    }
}

// Any events with a past date: move to same day/month next occurrence in the future
if (Schema::hasColumn('events', 'event_date')) {
    foreach (DB::table('events')->whereDate('event_date', '<', now()->toDateString())->get() as $e) {
        $d = new DateTime($e->event_date);
        while ($d < new DateTime('today')) { $d->modify('+1 year'); }
        DB::table('events')->where('id', $e->id)->update(['event_date' => $d->format('Y-m-d')]);
        $updated++;
        echo "Event #{$e->id} date moved to " . $d->format('Y-m-d') . "<br>";
    }
}

echo "<br><b>Done. {$updated} records updated.</b>";
