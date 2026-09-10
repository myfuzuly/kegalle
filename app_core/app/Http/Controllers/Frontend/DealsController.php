<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Deal;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DealsController extends Controller
{
    public function index(Request $request)
    {
        // Cache table existence check for 1 hour
        $hasDealsTable = Cache::remember('deals_table_exists', 3600, function () {
            try {
                DB::select('SELECT 1 FROM deals LIMIT 1');
                return true;
            } catch (\Throwable $e) {
                return false;
            }
        });

        $isFiltered = $request->hasAny(['q', 'discount', 'location', 'min_price', 'max_price', 'sort']);

        // Cache categories for 10 minutes (only when no filters)
        $categories = collect();
        if ($hasDealsTable) {
            $categories = Cache::remember('deals_active_categories', 600, function () {
                try {
                    return Category::whereIn('id',
                        DB::table('deals')
                            ->join('listings', 'deals.listing_id', '=', 'listings.id')
                            ->where('deals.status', 'active')
                            ->where('deals.starts_at', '<=', now())
                            ->where('deals.ends_at', '>=', now())
                            ->whereNotNull('listings.category_id')
                            ->distinct()
                            ->pluck('listings.category_id')
                    )->where('is_active', 1)->withCount('listings')->get();
                } catch (\Throwable $e) {
                    return collect();
                }
            });
        }

        $flashDeals = collect();
        $featuredDeals = collect();

        if ($hasDealsTable) {
            // Cache flash deals for 5 minutes
            $flashDeals = Cache::remember('deals_flash', 300, function () {
                try {
                    return Deal::active()
                        ->flash()
                        ->with(['listing.images', 'listing.category', 'store'])
                        ->orderBy('ends_at')
                        ->take(5)
                        ->get();
                } catch (\Throwable $e) {
                    return collect();
                }
            });

            // Featured/filtered deals — cache only unfiltered requests
            try {
                $query = Deal::active()->with(['listing.images', 'listing.category', 'store']);

                if ($request->filled('q')) {
                    $q = $request->q;
                    $query->whereHas('listing', fn($qb) => $qb->where('title', 'like', "%$q%"));
                }
                if ($request->filled('discount')) {
                    $query->where('discount_percent', '>=', (int) $request->discount);
                }
                if ($request->filled('location')) {
                    $query->whereHas('listing', fn($qb) => $qb->where('location', $request->location));
                }
                if ($request->filled('min_price')) {
                    $query->where('deal_price', '>=', (int) $request->min_price);
                }
                if ($request->filled('max_price')) {
                    $query->where('deal_price', '<=', (int) $request->max_price);
                }

                $sort = $request->input('sort', 'newest');
                $query = match ($sort) {
                    'popular'    => $query->orderByDesc('sold_count'),
                    'discount'   => $query->orderByDesc('discount_percent'),
                    'price_low'  => $query->orderBy('deal_price'),
                    'price_high' => $query->orderByDesc('deal_price'),
                    default      => $query->orderByDesc('is_featured')->latest(),
                };

                if (!$isFiltered) {
                    // Cache first page of unfiltered results for 5 minutes
                    $page = $request->input('page', 1);
                    $featuredDeals = Cache::remember("deals_featured_p{$page}_{$sort}", 300, fn() => $query->paginate(12)->withQueryString());
                } else {
                    $featuredDeals = $query->paginate(12)->withQueryString();
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('DealsController main query: ' . $e->getMessage());
            }

            $useRealDeals = $flashDeals->isNotEmpty() || (method_exists($featuredDeals, 'total') ? $featuredDeals->total() > 0 : $featuredDeals->isNotEmpty());
        } else {
            $useRealDeals = false;
        }

        $featuredListings = collect();
        $latestListings   = collect();

        if (!$useRealDeals) {
            $featuredListings = Cache::remember('deals_featured_listings', 600, fn() =>
                Listing::published()->with(['images', 'store', 'category'])
                    ->where('is_featured', 1)->latest()->take(8)->get()
            );
            $latestListings = Cache::remember('deals_latest_listings', 600, fn() =>
                Listing::published()->with(['images', 'store', 'category'])
                    ->latest()->take(12)->get()
            );
        }

        return view('frontend.deals.index', compact(
            'categories', 'flashDeals', 'featuredDeals',
            'featuredListings', 'latestListings', 'useRealDeals'
        ));
    }
}
