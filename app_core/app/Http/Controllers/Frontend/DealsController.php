<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Deal;
use App\Models\Listing;
use Illuminate\Http\Request;

class DealsController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', 1)
            ->withCount('listings')
            ->take(8)
            ->get();

        $hasDealsTable = true;
        try {
            \Illuminate\Support\Facades\DB::select('SELECT 1 FROM deals LIMIT 1');
        } catch (\Throwable $e) {
            $hasDealsTable = false;
        }

        if ($hasDealsTable) {
            $flashDeals = Deal::active()
                ->flash()
                ->with(['listing.images', 'listing.category', 'store'])
                ->orderBy('ends_at')
                ->take(5)
                ->get();

            $query = Deal::active()
                ->with(['listing.images', 'listing.category', 'store']);

            if ($request->filled('q')) {
                $q = $request->q;
                $query->whereHas('listing', fn($qb) => $qb->where('title', 'like', "%$q%"));
            }

            if ($request->filled('discount')) {
                $query->where('discount_percent', '>=', (int)$request->discount);
            }

            if ($request->filled('location')) {
                $loc = $request->location;
                $query->whereHas('listing', fn($qb) => $qb->where('location', $loc));
            }

            if ($request->filled('min_price')) {
                $query->where('deal_price', '>=', (int)$request->min_price);
            }
            if ($request->filled('max_price')) {
                $query->where('deal_price', '<=', (int)$request->max_price);
            }

            $sort = $request->input('sort', 'newest');
            $query = match($sort) {
                'popular' => $query->orderByDesc('sold_count'),
                'discount' => $query->orderByDesc('discount_percent'),
                'price_low' => $query->orderBy('deal_price'),
                'price_high' => $query->orderByDesc('deal_price'),
                // Admin-featured deals always lead the grid
                default => $query->orderByDesc('is_featured')->latest(),
            };

            $featuredDeals = $query->paginate(12)->withQueryString();

            $useRealDeals = $flashDeals->isNotEmpty() || $featuredDeals->total() > 0;
        } else {
            $useRealDeals = false;
            $flashDeals = collect();
            $featuredDeals = collect();
        }

        if (!$useRealDeals) {
            $featuredListings = Listing::published()
                ->with(['images', 'store', 'category'])
                ->where('is_featured', 1)
                ->latest()
                ->take(8)
                ->get();

            $latestListings = Listing::published()
                ->with(['images', 'store', 'category'])
                ->latest()
                ->take(12)
                ->get();
        } else {
            $featuredListings = collect();
            $latestListings = collect();
        }

        return view('frontend.deals.index', compact(
            'categories',
            'flashDeals',
            'featuredDeals',
            'featuredListings',
            'latestListings',
            'useRealDeals'
        ));
    }
}
