<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $data = Cache::remember('admin_dashboard', 120, function () {
            $stats = [
                'users'             => User::count(),
                'active_users'      => User::where('status', 'active')->orWhereNull('status')->count(),
                'stores'            => Store::count(),
                'approved_stores'   => Store::whereIn('status', ['approved', 'active', 'published'])->count(),
                'pending_stores'    => Store::where('status', 'pending')->count(),
                'listings'          => Listing::count(),
                'approved_listings' => Listing::whereIn('status', ['approved', 'active', 'published'])->count(),
                'pending_listings'  => Listing::where('status', 'pending')->count(),
                'products'          => Listing::where('type', 'product')->count(),
                'classified'        => Listing::where('type', 'classified')->count(),
                'categories'        => Category::count(),
                'total_value'       => Listing::whereIn('status', ['approved', 'active', 'published'])->sum('price'),
            ];

            $approvalQueue = Listing::with(['category', 'store', 'user'])
                ->where('status', 'pending')
                ->latest()
                ->take(8)
                ->get();

            $pendingStores = Store::with('user')
                ->where('status', 'pending')
                ->latest()
                ->take(6)
                ->get();

            $topCategories = Category::with('parent')->withCount('listings')
                ->orderByDesc('listings_count')
                ->take(8)
                ->get();

            $categoryStats = [
                'total'  => Category::count(),
                'main'   => Category::whereNull('parent_id')->count(),
                'active' => Category::where('is_active', true)->count(),
            ];

            $latestListings = Listing::with(['category', 'store', 'user'])
                ->latest()
                ->take(8)
                ->get();

            // ── 7-day charts ──────────────────────────────────────────────
            $days   = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));
            $labels = $days->map(fn($d) => date('D', strtotime($d)));

            $listingsByDay = DB::table('listings')
                ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
                ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                ->groupBy('day')->get()->keyBy('day');

            $usersByDay = DB::table('users')
                ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
                ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                ->groupBy('day')->get()->keyBy('day');

            $revenueByDay = DB::table('listings')
                ->selectRaw('DATE(created_at) as day, COALESCE(SUM(price),0) as total')
                ->whereIn('status', ['approved', 'active', 'published'])
                ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                ->groupBy('day')->get()->keyBy('day');

            $chartData = [
                'labels'   => $labels->values(),
                'listings' => $days->map(fn($d) => (int)   ($listingsByDay[$d]->total ?? 0))->values(),
                'users'    => $days->map(fn($d) => (int)   ($usersByDay[$d]->total    ?? 0))->values(),
                'revenue'  => $days->map(fn($d) => (float) ($revenueByDay[$d]->total  ?? 0))->values(),
            ];

            return compact('stats', 'approvalQueue', 'pendingStores', 'topCategories', 'latestListings', 'chartData', 'categoryStats');
        });

        extract($data);

        return view('admin.index', compact(
            'stats',
            'approvalQueue',
            'pendingStores',
            'topCategories',
            'latestListings',
            'chartData',
            'categoryStats'
        ));
    }
}
