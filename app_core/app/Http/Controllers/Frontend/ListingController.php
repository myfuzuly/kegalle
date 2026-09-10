<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Location;
use App\Models\Review;
use App\Models\Store;
use App\Models\UserNotification;
use Illuminate\Http\Request;

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
                $q->whereFullText(['title', 'description'], $keyword)
                  ->orWhere('location', 'like', '%' . $keyword . '%');
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
            // Collect all descendant category IDs — single query fetching all categories once
            $rootIds = Category::whereIn('slug', $selectedSlugs)->pluck('id')->toArray();
            $all = Category::select('id', 'parent_id')->get()->keyBy('id');
            $allIds = [];
            $queue = $rootIds;
            while (!empty($queue)) {
                $next = [];
                foreach ($queue as $pid) {
                    if (!in_array($pid, $allIds)) {
                        $allIds[] = $pid;
                        foreach ($all as $cat) {
                            if ($cat->parent_id == $pid) $next[] = $cat->id;
                        }
                    }
                }
                $queue = $next;
            }
            $query->whereHas('category', fn ($q) => $q->whereIn('id', $allIds));
        }

        if ($request->filled('store')) {
            $query->whereHas('store', fn ($q) => $q->where('slug', $request->store));
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%'.trim($request->location).'%');
        }

        if ($request->filled('locations')) {
            $locIds = (array) $request->locations;
            $locNames = Location::whereIn('id', $locIds)->pluck('name')->toArray();
            $query->where(function ($q) use ($locIds, $locNames) {
                $q->whereIn('location_id', $locIds);
                foreach ($locNames as $name) {
                    $q->orWhere('location', 'like', '%' . $name . '%');
                }
            });
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

        if ($request->filled('condition')) {
            $cond = $request->condition;
            $query->where(function ($q) use ($cond) {
                if ($cond === 'new') {
                    $q->where('condition', 'like', '%new%')->orWhere('condition', 'like', '%brand%');
                } else {
                    $q->whereNotNull('condition')
                      ->where('condition', '!=', '')
                      ->where('condition', 'not like', '%new%')
                      ->where('condition', 'not like', '%brand%');
                }
            });
        }

        if ($request->filled('posted')) {
            $from = match($request->posted) {
                'today' => now()->startOfDay(),
                'week'  => now()->startOfWeek(),
                'month' => now()->startOfMonth(),
                default => null,
            };
            if ($from) $query->where('created_at', '>=', $from);
        }

        match ($request->get('sort')) {
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'popular' => $query->orderByDesc('views'),
            'alpha' => $query->orderBy('title'),
            default => $query->orderByDesc('is_featured')->orderByRaw('COALESCE(bumped_at, created_at) DESC'),
        };

        $categories = cache()->remember('listing_categories_tree', 600, function () {
            $cats = Category::query()
                ->where('is_active', 1)
                ->whereNull('parent_id')
                ->with(['children' => fn ($q) => $q->where('is_active', 1)
                    ->withCount(['listings' => fn ($q2) => $q2->published()])
                    ->with(['children' => fn ($q2) => $q2->where('is_active', 1)
                        ->withCount(['listings' => fn ($q3) => $q3->published()])
                    ])
                ])
                ->withCount(['listings' => fn ($q) => $q->published()])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            // Bubble up counts: grandchildren → children → parents
            foreach ($cats as $parent) {
                $parentExtra = 0;
                foreach ($parent->children as $child) {
                    $childExtra = $child->children->sum('listings_count');
                    $child->listings_count += $childExtra;
                    $parentExtra += $child->listings_count;
                }
                $parent->listings_count += $parentExtra;
            }

            return $cats;
        });

        if (class_exists(Location::class)) {
            $allLocs = Location::where('is_active', 1)->orderBy('sort_order')->orderBy('name')->get();
            [$idCounts, $textCounts] = cache()->remember('listing_location_counts', 300, function () {
                return [
                    Listing::published()->whereNotNull('location_id')
                        ->selectRaw('location_id, COUNT(*) as cnt')->groupBy('location_id')
                        ->get()->pluck('cnt', 'location_id'),
                    Listing::published()
                        ->selectRaw('location, COUNT(*) as cnt')->groupBy('location')
                        ->get()->pluck('cnt', 'location'),
                ];
            });
            foreach ($allLocs as $loc) {
                $loc->listings_count = max((int)($idCounts[$loc->id] ?? 0), (int)($textCounts[$loc->name] ?? 0));
            }
            $locations = $allLocs;
        } else {
            $locations = collect();
        }

        $stores = cache()->remember('listing_sidebar_stores', 600, fn() =>
            Store::query()
                ->whereIn('status', ['approved', 'published', 'active'])
                ->orderBy('name')
                ->take(50)
                ->get()
        );

        $isClassified = $request->route('type') === 'classified' && ! $request->filled('type');

        // Cache listing IDs for 30s using direct file cache (framework cache may lack permissions)
        $cacheKey = md5(serialize(array_merge(
            ['_route_type' => $request->route('type') ?? ''],
            $request->only([
                'q','type','category','categories','store','location','locations',
                'min_price','max_price','featured','condition','posted','sort','page',
            ])
        )));
        $page     = max(1, (int) $request->get('page', 1));
        $perPage  = 20;
        $cacheDir = storage_path('framework/searchcache');
        if (!is_dir($cacheDir)) { @mkdir($cacheDir, 0755, true); }
        $cacheFile = $cacheDir . '/' . $cacheKey . '.json';

        $cached = null;
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 30) {
            $cached = json_decode(file_get_contents($cacheFile), true);
        }
        if (!$cached) {
            $result = $query->paginate($perPage, ['id'], 'page', $page);
            $cached = ['ids' => $result->pluck('id')->all(), 'total' => $result->total()];
            @file_put_contents($cacheFile, json_encode($cached), LOCK_EX);
        }

        // Re-fetch full models by cached IDs (fast PK lookup + eager load)
        $idList = $cached['ids'];
        if ($idList) {
            $idOrder = implode(',', array_map('intval', $idList));
            $rows = Listing::with(['store', 'user', 'category', 'images', 'locationModel'])
                ->whereIn('id', $idList)
                ->orderByRaw("FIELD(id, {$idOrder})")
                ->get()
                ->keyBy('id');
            $items = collect($idList)->map(fn($id) => $rows[$id] ?? null)->filter()->values();
        } else {
            $items = collect();
        }
        $listings = new \Illuminate\Pagination\LengthAwarePaginator(
            $items, $cached['total'], $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $maxListingPrice = cache()->remember('listing_max_price', 3600, fn() =>
            (int) ceil((\App\Models\Listing::published()->max('price') ?: 500000) / 1000) * 1000
        );

        $totalCatCount = cache()->remember('home_total_cat_count_root', 300, fn() => Category::whereNull('parent_id')->count());

        $data = [
            'listings' => $listings,
            'categories' => $categories,
            'locations' => $locations,
            'stores' => $stores,
            'maxListingPrice' => $maxListingPrice,
            'totalCatCount' => $totalCatCount,
        ];

        // Resolve single selected category name for empty-state messaging
        $selectedSlugs = (array) $request->get('categories', []);
        $filterCategoryName = null;
        if (count($selectedSlugs) === 1) {
            $filterCategoryName = Category::where('slug', $selectedSlugs[0])->value('name');
        }

        if ($request->ajax() || $request->wantsJson() || $request->boolean('_ajax')) {
            return response()->json([
                'html' => view('frontend.listings.grid-partial', [
                    'listings' => $listings,
                    'filterCategoryName' => $filterCategoryName,
                ])->render(),
                'count' => $listings->total(),
                'from' => $listings->firstItem() ?? 0,
                'to' => $listings->lastItem() ?? 0,
            ]);
        }

        $data['filterCategoryName'] = $filterCategoryName;

        if ($isClassified) {
            // Cached ad-type counts for the classified index header (avoids 3 live queries per render)
            $classifiedCounts = cache()->remember('classified_ad_type_counts', 300, function () {
                return \App\Models\Listing::published()
                    ->where('type', 'classified')
                    ->selectRaw('ad_type, COUNT(*) as cnt')
                    ->groupBy('ad_type')
                    ->pluck('cnt', 'ad_type');
            });
            $classifiedNewThisWeek = cache()->remember('classified_new_this_week', 300, fn () =>
                \App\Models\Listing::published()
                    ->where('type', 'classified')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count()
            );

            // Single favorites query for the page instead of per-card exists() (N+1)
            $userFavoriteIds = auth()->check()
                ? \App\Models\Favorite::where('user_id', auth()->id())
                    ->whereIn('listing_id', $listings->pluck('id'))
                    ->pluck('listing_id')
                    ->flip()
                : collect();

            $data['classifiedCounts']      = $classifiedCounts;
            $data['classifiedNewThisWeek'] = $classifiedNewThisWeek;
            $data['userFavoriteIds']        = $userFavoriteIds;

            return view('frontend.classified.index', $data);
        }

        return view('frontend.listings.index', $data);
    }

    public function show($slug)
    {
        $listing = Listing::with(['images', 'category', 'store', 'user', 'values.field', 'reviews' => fn($q) => $q->where('status', 'approved')->with('user')->latest()])
            ->whereIn('status', \App\Models\Listing::LIVE_STATUSES)
            ->where('slug', $slug)
            ->firstOrFail();

        $store = $listing->store; // could be null for classified ads

        $listing->increment('views');

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
        $relatedIsGeneric = false;

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

        {
            // Same subcategory, prefer different store
            $related = Listing::published()
                ->with(['store', 'user', 'category', 'images', 'locationModel'])
                ->where('id', '!=', $listing->id)
                ->when($listing->category_id, fn ($q) => $q->where('category_id', $listing->category_id))
                ->when($listing->store_id, fn ($q) => $q->where('store_id', '!=', $listing->store_id))
                ->latest()
                ->take(6)
                ->get();

            // Not enough? Broaden to sibling subcategories under same parent
            if ($related->count() < 4 && $listing->category_id) {
                $parentId = optional($listing->category)->parent_id ?? $listing->category_id;
                $siblingIds = \App\Models\Category::where('parent_id', $parentId)
                    ->orWhere('id', $parentId)
                    ->pluck('id');
                $extra = Listing::published()
                    ->with(['store', 'user', 'category', 'images', 'locationModel'])
                    ->where('id', '!=', $listing->id)
                    ->whereNotIn('id', $related->pluck('id'))
                    ->whereIn('category_id', $siblingIds)
                    ->latest()
                    ->take(6 - $related->count())
                    ->get();
                $related = $related->concat($extra);
            }

            // Still not enough? Fill with any published listings; flag as generic
            $relatedIsGeneric = false;
            if ($related->count() < 4) {
                $relatedIsGeneric = true;
                $extra = Listing::published()
                    ->with(['store', 'user', 'category', 'images', 'locationModel'])
                    ->where('id', '!=', $listing->id)
                    ->whereNotIn('id', $related->pluck('id'))
                    ->latest()
                    ->take(6 - $related->count())
                    ->get();
                $related = $related->concat($extra);
            }
        }

        // Resolve brand from field values (avoids DB call in view)
        $brandFieldValue = ($listing->values ?? collect())->first(fn($v) => $v->field && $v->field->name === 'brand_id' && $v->value);
        $listingBrand = $brandFieldValue ? \App\Models\Brand::find($brandFieldValue->value) : null;

        // Eager-load store listing count and average rating to avoid N+1 in view
        if ($store) {
            $store->loadCount(['listings as active_listings_count' => fn($q) => $q->whereIn('status', \App\Models\Listing::LIVE_STATUSES)]);
            $store->load(['approvedReviews' => fn($q) => $q->select('id', 'store_id', 'rating')]);
        } elseif ($listing->user) {
            $listing->user->loadCount(['listings as active_listings_count' => fn($q) => $q->whereIn('status', \App\Models\Listing::LIVE_STATUSES)]);
        }

        $hasPriceAlert = false;
        if (auth()->check()) {
            try {
                $hasPriceAlert = \Illuminate\Support\Facades\DB::table('price_alerts')
                    ->where('user_id', auth()->id())
                    ->where('listing_id', $listing->id)
                    ->exists();
            } catch (\Throwable) {}
        }

        return view('frontend.listings.show', compact('listing', 'store', 'related', 'activeDeal', 'similarDeals', 'relatedIsGeneric', 'listingBrand', 'hasPriceAlert'));
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
            'user_id'    => auth()->id(),
            'listing_id' => $listing->id,
            'store_id'   => $listing->store_id,
            'rating'     => $data['rating'],
            'comment'    => $data['comment'],
            'status'     => 'pending',
        ]);

        // Notify the store owner
        if ($listing->store_id) {
            $store = $listing->store;
            if ($store?->user_id) {
                UserNotification::send(
                    $store->user_id,
                    'new_review',
                    'New review on "'.(\Illuminate\Support\Str::limit($listing->title, 40)).'"',
                    auth()->user()->name.' left a '.$data['rating'].'★ review.',
                    '/store/'.$store->slug,
                    $listing->id
                );
            }
        }

        return back()->with('success', 'Thank you! Your review has been posted.');
    }

    public function brands()
    {
        $brands = cache()->remember('brands_page_data', 600, function () {
            $brands = \App\Models\Brand::where('is_active', true)->orderBy('name')->get();

            $counts = collect();
            $brandField = \App\Models\CustomField::where('name', 'brand_id')->first();
            if ($brandField) {
                $counts = \App\Models\ListingFieldValue::where('custom_field_id', $brandField->id)
                    ->whereIn('listing_id', Listing::published()->select('id'))
                    ->selectRaw('value, count(*) as c')
                    ->groupBy('value')
                    ->pluck('c', 'value');
            }

            foreach ($brands as $brand) {
                $brand->listings_count = (int) ($counts[(string) $brand->id] ?? 0);
            }

            return $brands;
        });

        return view('frontend.brands', compact('brands'));
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
