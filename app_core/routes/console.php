<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Daily Database Backup — runs at 2 AM
|--------------------------------------------------------------------------
*/
Schedule::call(function () {
    $db   = config('database.connections.mysql');
    $dir  = '/home/kegalle/backups';
    if (!is_dir($dir)) { mkdir($dir, 0750, true); }

    $file = $dir . '/kegalle-' . now()->format('Y-m-d') . '.sql.gz';
    $cmd  = sprintf(
        '/usr/bin/mysqldump --host=%s --port=%s -u%s -p%s %s 2>/dev/null | gzip > %s',
        escapeshellarg($db['host'] ?? '127.0.0.1'),
        escapeshellarg($db['port'] ?? '3306'),
        escapeshellarg($db['username'] ?? ''),
        escapeshellarg($db['password'] ?? ''),
        escapeshellarg($db['database'] ?? ''),
        escapeshellarg($file)
    );
    exec($cmd, $output, $exitCode);

    // Keep only the last 7 backups
    $backups = glob($dir . '/kegalle-*.sql.gz');
    if (count($backups) > 7) {
        sort($backups);
        foreach (array_slice($backups, 0, count($backups) - 7) as $old) {
            @unlink($old);
        }
    }

    if ($exitCode !== 0 || !file_exists($file)) {
        Log::error("DB backup FAILED (exit {$exitCode}): {$file}");
        return;
    }
    $size = round(filesize($file) / 1048576, 2) . ' MB';
    Log::info("DB backup: {$file} ({$size})");
})->dailyAt('02:00')->name('db-backup')->withoutOverlapping();

/*
|--------------------------------------------------------------------------
| Listing Renewal Reminders — runs daily at 9 AM
|--------------------------------------------------------------------------
*/
Schedule::call(function () {
    $count = 0;
    \App\Models\Listing::with('user')
        ->whereNotNull('expires_at')
        ->whereDate('expires_at', now()->addDays(7)->toDateString())
        ->where('status', 'approved')
        ->chunk(100, function ($listings) use (&$count) {
            foreach ($listings as $listing) {
                $email = optional($listing->user)->email;
                if (!$email) continue;
                try {
                    Mail::to($email)->queue(new \App\Mail\ListingRenewalMail($listing));
                    $count++;
                } catch (\Throwable $e) {
                    Log::error('Renewal reminder failed for listing '.$listing->id.': '.$e->getMessage());
                }
            }
        });

    Log::info('Renewal reminders sent: '.$count);
})->dailyAt('09:00')->name('renewal-reminders')->withoutOverlapping();

/*
|--------------------------------------------------------------------------
| Saved Search Alerts — runs daily at 8 AM
|--------------------------------------------------------------------------
*/
Schedule::call(function () {
    if (!DB::getSchemaBuilder()->hasTable('saved_searches')) return;

    DB::table('saved_searches')
        ->join('users', 'saved_searches.user_id', '=', 'users.id')
        ->select('saved_searches.*', 'users.email', 'users.name')
        ->orderBy('saved_searches.id')
        ->lazy(100)
        ->each(function ($search) {
        $params = json_decode($search->params, true) ?: [];
        $query  = \App\Models\Listing::published()->with(['images', 'category', 'locationModel']);

        if (!empty($params['q'])) {
            $query->where('title', 'like', '%'.$params['q'].'%');
        }
        if (!empty($params['categories'])) {
            $query->whereHas('category', fn($q) => $q->whereIn('slug', (array)$params['categories']));
        }
        if (!empty($params['min_price'])) {
            $query->where('price', '>=', (float)$params['min_price']);
        }
        if (!empty($params['max_price'])) {
            $query->where('price', '<=', (float)$params['max_price']);
        }

        $cutoff = $search->last_sent_at ?? $search->created_at;
        $newListings = $query->where('created_at', '>', $cutoff)->latest()->take(10)->get();

        if ($newListings->isEmpty()) return;

        try {
            Mail::to($search->email)->queue(new \App\Mail\SavedSearchAlertMail($newListings, $params));
            DB::table('saved_searches')->where('id', $search->id)->update(['last_sent_at' => now()]);
        } catch (\Throwable $e) {
            Log::error('Saved search alert failed for user '.$search->user_id.': '.$e->getMessage());
        }
    });
})->dailyAt('08:00')->name('saved-search-alerts')->withoutOverlapping();

/*
|--------------------------------------------------------------------------
| Price Drop Alerts — runs daily at 10 AM
| Sources from price_alerts table (set via the 🔔 button on listing pages)
|--------------------------------------------------------------------------
*/
Schedule::call(function () {
    if (!DB::getSchemaBuilder()->hasTable('price_alerts')) return;

    $drops = DB::table('price_alerts')
        ->join('listings', 'price_alerts.listing_id', '=', 'listings.id')
        ->join('users',    'price_alerts.user_id',    '=', 'users.id')
        ->whereNotNull('price_alerts.price_when_set')
        ->whereRaw('listings.price < price_alerts.price_when_set')
        ->whereRaw('listings.price > 0')
        ->whereIn('listings.status', ['approved', 'active', 'published'])
        ->select(
            'users.id as user_id', 'users.email', 'users.name',
            'listings.id as listing_id', 'listings.title', 'listings.slug', 'listings.price as new_price',
            'price_alerts.price_when_set as old_price',
            'price_alerts.id as alert_id'
        )
        ->orderBy('users.id')
        ->lazy(200);

    $currentUserId = null;
    $currentDrops  = collect();
    $userCount     = 0;

    $sendForUser = function ($userId, $userDrops) use (&$userCount) {
        $email = $userDrops->first()->email;
        try {
            Mail::to($email)->queue(new \App\Mail\PriceDropMail($userDrops));
            foreach ($userDrops as $row) {
                DB::table('price_alerts')
                    ->where('id', $row->alert_id)
                    ->update(['price_when_set' => $row->new_price, 'updated_at' => now()]);
            }
            try {
                $first = $userDrops->first();
                $notifyUserId = $userId;
                $notifyTitle  = '"'.($first->title ?? 'A listing').'" dropped to LKR '.number_format($first->new_price).'!';
                $notifyUrl    = '/listings/'.($first->slug ?? '');
                dispatch(function () use ($notifyUserId, $notifyTitle, $notifyUrl) {
                    app(\App\Services\WebPushService::class)->notifyUser(
                        $notifyUserId, 'Price Drop Alert 🎉', $notifyTitle, $notifyUrl
                    );
                })->onQueue('default');
            } catch (\Throwable) {}
            $userCount++;
        } catch (\Throwable $e) {
            Log::error('Price drop alert failed for user '.$userId.': '.$e->getMessage());
        }
    };

    foreach ($drops as $row) {
        if ($currentUserId !== null && $row->user_id !== $currentUserId) {
            $sendForUser($currentUserId, $currentDrops);
            $currentDrops = collect();
        }
        $currentUserId = $row->user_id;
        $currentDrops->push($row);
    }
    if ($currentUserId !== null && $currentDrops->isNotEmpty()) {
        $sendForUser($currentUserId, $currentDrops);
    }

    Log::info('Price drop alerts sent to '.$userCount.' users.');
})->dailyAt('10:00')->name('price-drop-alerts')->withoutOverlapping();

/*
|--------------------------------------------------------------------------
| Deal Expiry Cleanup — runs daily at midnight
|--------------------------------------------------------------------------
*/
Schedule::call(function () {
    $expired = \App\Models\Deal::where('status', 'approved')
        ->where('ends_at', '<', now())
        ->update(['status' => 'expired']);

    if ($expired > 0) {
        Log::info("Deal expiry: {$expired} deal(s) marked as expired.");
    }
})->daily()->name('deal-expiry-cleanup')->withoutOverlapping();
