<?php

namespace App\Providers;

use App\Models\Listing;
use App\Models\Store;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // N+1 guard: outside production, log every lazy-loaded relation on a model that came from a list.
        if (! $this->app->isProduction()) {
            \Illuminate\Database\Eloquent\Model::preventLazyLoading();
            \Illuminate\Database\Eloquent\Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
                $where = collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 40))
                    ->first(fn ($f) => isset($f['file']) && ! str_contains($f['file'], DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR));
                @file_put_contents(storage_path('logs/n1.log'), get_class($model) . '::' . $relation . ' @ '
                    . ($where ? str_replace(base_path() . DIRECTORY_SEPARATOR, '', $where['file']) . ':' . $where['line'] : '?') . "
", FILE_APPEND);
            });
        }

        $override = env('MAIL_OVERRIDE_TO');
        if ($override) {
            Mail::alwaysTo($override);
        }

        // Bust homepage cache when listings or stores change
        $bustHome = function () {
            Cache::forget('home_featured_listings');
            Cache::forget('home_latest_listings_40');
            Cache::forget('home_featured_classified');
            Cache::forget('home_latest_classified');
            Cache::forget('home_deals');
            Cache::forget('home_featured_stores_v2');
        };

        Listing::saved($bustHome);
        Listing::deleted($bustHome);
        Store::saved(function () {
            Cache::forget('home_featured_stores_v2');
        });
    }
}
