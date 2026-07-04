<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Location;
use App\Models\Review;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $query = Listing::published()->with(['store', 'user', 'category', 'images', 'locationModel']);

        if ($request->route('type') === 'classified' && ! $request->filled('type')) {
            $query->where('type', 'classified');
        }

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('location', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('categories')) {
            $selectedSlugs = (array) $request->categories;
            $parentIds = Category::whereIn('slug', $selectedSlugs)->pluck('id');
            $childSlugs = Category::whereIn('parent_id', $parentIds)->pluck('slug')->toArray();
            $allSlugs = array_unique(array_merge($selectedSlugs, $childSlugs));
            $query->whereHas('category', fn ($q) => $q->whereIn('slug', $allSlugs));
        }

        if ($request->filled('store')) {
            $query->whereHas('store', fn ($q) => $q->where('slug', $request->store));
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%'.trim($request->location).'%');
        }

        if ($request->filled('locations') && Schema::hasColumn('listings', 'location_id')) {
            $query->whereIn('location_id', (array) $request->locations);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', 1);
        }

        match ($request->get('sort')) {
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'popular' => Schema::hasColumn('listings', 'views') ? $query->orderByDesc('views') : $query->latest(),
            default => $query->latest(),
        };

        $categories = Category::query()
            ->where('is_active', 1)
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->where('is_active', 1)
                ->withCount(['listings' => fn ($q2) => $q2->published()])
            ])
            ->withCount(['listings' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $locations = class_exists(Location::class)
            ? Location::query()
                ->where('is_active', 1)
                ->withCount(['listings' => fn ($q) => $q->published()])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
            : collect();

        $stores = Store::query()
            ->whereIn('status', ['approved', 'published', 'active'])
            ->orderBy('name')
            ->take(50)
            ->get();

        $isClassified = $request->route('type') === 'classified' && ! $request->filled('type');

        $listings = $query->paginate(20)->withQueryString();

        $data = [
            'listings' => $listings,
            'categories' => $categories,
            'locations' => $locations,
            'stores' => $stores,
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('frontend.listings.grid-partial', compact('listings'))->render(),
                'count' => $listings->total(),
                'from' => $listings->firstItem() ?? 0,
                'to' => $listings->lastItem() ?? 0,
            ]);
        }

        return $isClassified
            ? view('frontend.classified.index', $data)
            : view('frontend.listings.index', $data);
    }

    public function show($slug)
    {
        $listing = Listing::with(['images', 'category', 'store', 'user', 'values.field', 'reviews' => fn($q) => $q->where('status', 'approved')->with('user')->latest()])
            ->where('slug', $slug)
            ->firstOrFail();

        $store = $listing->store; // could be null for classified ads

        if (Schema::hasColumn('listings', 'views')) {
            $listing->increment('views');
        }

        $activeDeal = null;
        try {
            $activeDeal = \App\Models\Deal::where('listing_id', $listing->id)
                ->where('status', 'approved')
                ->where('starts_at', '<=', now())
                ->where('ends_at', '>=', now())
                ->first();
        } catch (\Throwable $e) {}

        $related = collect();
        $similarDeals = collect();

        if ($activeDeal) {
            try {
                $similarDeals = \App\Models\Deal::where('status', 'approved')
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>=', now())
                    ->where('listing_id', '!=', $listing->id)
                    ->with(['listing.images', 'listing.store', 'listing.category'])
                    ->latest()
                    ->take(6)
                    ->get();
            } catch (\Throwable $e) {}
        }

        if (!$activeDeal || $similarDeals->isEmpty()) {
            $related = Listing::published()
                ->with(['store', 'user', 'category', 'images', 'locationModel'])
                ->where('id', '!=', $listing->id)
                ->when($listing->category_id, fn ($q) => $q->where('category_id', $listing->category_id))
                ->latest()
                ->take(6)
                ->get();
        }

        return view('frontend.listings.show', compact('listing', 'store', 'related', 'activeDeal', 'similarDeals'));
    }

    public function storeReview(Request $request, Listing $listing)
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:1000',
        ]);

        $existing = Review::where('user_id', auth()->id())
            ->where('listing_id', $listing->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already reviewed this listing.');
        }

        Review::create([
            'user_id' => auth()->id(),
            'listing_id' => $listing->id,
            'store_id' => $listing->store_id,
            'rating' => $data['rating'],
            'comment' => $data['comment'],
            'status' => 'approved',
        ]);

        return back()->with('success', 'Review submitted successfully!');
    }

    public function brand($slug)
    {
        $brand = \App\Models\Brand::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $brandField = \App\Models\CustomField::where('name', 'brand_id')->first();
        if (!$brandField) {
            abort(404);
        }

        $allBrandIds = \App\Models\Brand::where('slug', $slug)->where('is_active', true)->pluck('id');

        $listingIds = \App\Models\ListingFieldValue::where('custom_field_id', $brandField->id)
            ->whereIn('value', $allBrandIds)
            ->pluck('listing_id');

        $query = Listing::published()
            ->whereIn('id', $listingIds)
            ->with(['category', 'images', 'locationModel', 'store', 'user']);

        if (request()->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', request('category')));
        }

        match (request('sort')) {
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };

        $listings = $query->paginate(12)->withQueryString();

        $categories = Category::whereHas('listings', fn ($q) => $q->published()->whereIn('id', $listingIds))
            ->where('is_active', 1)
            ->withCount(['listings' => fn ($q) => $q->published()->whereIn('id', $listingIds)])
            ->orderBy('name')
            ->get();

        $totalCount = Listing::published()->whereIn('id', $listingIds)->count();

        return view('frontend.brand', compact('brand', 'listings', 'categories', 'totalCount'));
    }
}
