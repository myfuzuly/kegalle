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
