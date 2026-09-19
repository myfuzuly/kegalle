<?php

namespace App\Console\Commands;

use App\Models\WholesalePrice;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RollForwardWholesalePrices extends Command
{
    protected $signature   = 'prices:rollforward {--date= : Target date (Y-m-d), defaults to today}';
    protected $description = 'Copy previous day\'s wholesale prices to the target date if none exist yet';

    public function handle(): int
    {
        $today = $this->option('date')
            ? Carbon::parse($this->option('date'))->toDateString()
            : Carbon::now('Asia/Colombo')->toDateString();

        // Skip if today already has entries
        $existing = WholesalePrice::where('price_date', $today)->count();
        if ($existing > 0) {
            $this->info("Already have {$existing} price(s) for {$today}. Skipping.");
            Log::channel('daily')->info("prices:rollforward skipped — {$existing} rows already exist for {$today}");
            return 0;
        }

        // Find the most recent date with data
        $prevDate = WholesalePrice::where('price_date', '<', $today)
            ->where('is_active', true)
            ->max('price_date');

        if (! $prevDate) {
            $this->warn('No previous price data found. Nothing to roll forward.');
            Log::channel('daily')->warning('prices:rollforward — no previous price data found');
            return 0;
        }

        $rows = WholesalePrice::where('price_date', $prevDate)
            ->where('is_active', true)
            ->get();

        $now = now();
        $inserts = $rows->map(fn($r) => [
            'commodity'  => $r->commodity,
            'category'   => $r->category,
            'unit'       => $r->unit,
            'min_price'  => $r->min_price,
            'max_price'  => $r->max_price,
            'avg_price'  => $r->avg_price,
            'market'     => $r->market,
            'price_date' => $today,
            'is_active'  => true,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        DB::table('wholesale_prices')->insert($inserts);

        $count = count($inserts);
        $this->info("Rolled forward {$count} price(s) from {$prevDate} → {$today}.");
        Log::channel('daily')->info("prices:rollforward — {$count} rows from {$prevDate} → {$today}");

        return 0;
    }
}
