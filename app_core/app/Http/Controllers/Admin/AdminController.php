<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Deal;
use App\Models\Event;
use App\Models\Listing;
use App\Models\Payment;
use App\Models\Report;
use App\Models\Review;
use App\Models\Service;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    private const LIVE = ['approved', 'active', 'published'];

    public function index()
    {
        $data = Cache::remember('admin_dashboard_v2', 120, function () {
            $count = static function (callable $q): int {
                try {
                    return (int) $q();
                } catch (\Throwable $e) {
                    return 0;
                }
            };

            $now = now();
            $weekAgo = $now->copy()->subDays(7);
            $twoWeeksAgo = $now->copy()->subDays(14);

            $stats = [
                'users'             => $count(fn () => User::count()),
                'stores'            => $count(fn () => Store::count()),
                'live_stores'       => $count(fn () => Store::whereIn('status', self::LIVE)->count()),
                'listings'          => $count(fn () => Listing::count()),
                'live_listings'     => $count(fn () => Listing::whereIn('status', self::LIVE)->count()),
                'products'          => $count(fn () => Listing::where('type', 'product')->count()),
                'classified'        => $count(fn () => Listing::where('type', 'classified')->count()),
                'users_7d'          => $count(fn () => User::where('created_at', '>=', $weekAgo)->count()),
                'users_prev_7d'     => $count(fn () => User::whereBetween('created_at', [$twoWeeksAgo, $weekAgo])->count()),
                'listings_7d'       => $count(fn () => Listing::where('created_at', '>=', $weekAgo)->count()),
                'listings_prev_7d'  => $count(fn () => Listing::whereBetween('created_at', [$twoWeeksAgo, $weekAgo])->count()),
                'revenue_30d'       => $count(fn () => Payment::where('status', 'paid')->where('created_at', '>=', $now->copy()->subDays(30))->sum('amount')),
                'revenue_prev_30d'  => $count(fn () => Payment::where('status', 'paid')->whereBetween('created_at', [$now->copy()->subDays(60), $now->copy()->subDays(30)])->sum('amount')),
                'paid_count_30d'    => $count(fn () => Payment::where('status', 'paid')->where('created_at', '>=', $now->copy()->subDays(30))->count()),
            ];

            // Work waiting for an admin — each links to the page where it is handled.
            $pending = [
                ['key' => 'listings', 'label' => 'Listings to review',  'count' => $count(fn () => Listing::where('status', 'pending')->count()), 'url' => '/admin/approvals'],
                ['key' => 'stores',   'label' => 'Stores to approve',   'count' => $count(fn () => Store::where('status', 'pending')->count()),   'url' => '/admin/stores?status=pending'],
                ['key' => 'deals',    'label' => 'Deals to review',     'count' => $count(fn () => Deal::where('status', 'pending')->count()),    'url' => '/admin/deals'],
                ['key' => 'services', 'label' => 'Services to review',  'count' => $count(fn () => Service::where('status', 'pending')->count()), 'url' => '/admin/services'],
                ['key' => 'events',   'label' => 'Events to review',    'count' => $count(fn () => Event::where('status', 'pending')->count()),   'url' => '/admin/events'],
                ['key' => 'reviews',  'label' => 'Reviews to moderate', 'count' => $count(fn () => Review::where('status', 'pending')->count()),  'url' => '/admin/reviews'],
                ['key' => 'reports',  'label' => 'Open reports',        'count' => $count(fn () => Report::where('status', 'pending')->count()),  'url' => '/admin/reports'],
                ['key' => 'payments', 'label' => 'Payments to confirm', 'count' => $count(fn () => Payment::where('status', 'pending')->count()), 'url' => '/admin/payments'],
            ];

            $approvalQueue = Listing::with(['category', 'store', 'user'])
                ->where('status', 'pending')->latest()->take(6)->get();

            $pendingStores = Store::with('user')
                ->where('status', 'pending')->latest()->take(5)->get();

            $latestListings = Listing::with(['category', 'store'])
                ->latest()->take(6)->get();

            $topCategories = Category::withCount('listings')
                ->orderByDesc('listings_count')->take(6)->get();

            // 14-day activity as plain integers; rendered as lightweight SVG bars (no chart library).
            $days = collect(range(13, 0))->map(fn ($i) => $now->copy()->subDays($i)->format('Y-m-d'));
            $byDay = static fn (string $table) => DB::table($table)
                ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
                ->where('created_at', '>=', $now->copy()->subDays(13)->startOfDay())
                ->groupBy('day')->pluck('total', 'day');
            $listingsByDay = $byDay('listings');
            $usersByDay = $byDay('users');
            $activity = $days->map(fn ($d) => [
                'date'     => $d,
                'listings' => (int) ($listingsByDay[$d] ?? 0),
                'users'    => (int) ($usersByDay[$d] ?? 0),
            ])->values()->all();

            return compact('stats', 'pending', 'approvalQueue', 'pendingStores', 'latestListings', 'topCategories', 'activity');
        });

        return view('admin.index', $data);
    }
}
