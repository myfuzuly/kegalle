<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Deal;
use App\Models\Listing;
use App\Models\Location;
use App\Models\Review;
use App\Models\Store;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Store::query()
            ->whereIn('status', ['approved', 'published', 'active'])
            ->withCount(['listings' => fn ($q) => $q->published()])
            ->withCount(['approvedReviews'])
            ->withAvg('approvedReviews', 'rating')
            ->with('user:id,phone_verified_at')
            ->having('listings_count', '>', 0);

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('city', 'like', "%{$keyword}%")
                    ->orWhere('address', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('location')) {
            $query->where(function ($q) use ($request) {
                $q->where('city', 'like', '%'.trim($request->location).'%')
                    ->orWhere('address', 'like', '%'.trim($request->location).'%');
            });
        }

        match ($request->get('sort')) {
            'newest' => $query->latest(),
            'products' => $query->orderByDesc('listings_count'),
            default => $query->orderByDesc('is_featured')->orderByDesc('listings_count')->latest(),
        };

        $categories = cache()->remember('stores.index.categories', 300, function () {
            return Category::where('is_active', 1)
                ->whereNull('parent_id')
                ->with(['children' => fn ($q) => $q->where('is_active', 1)
                    ->withCount(['listings' => fn ($q2) => $q2->published()])
                ])
                ->withCount(['listings' => fn ($q) => $q->published()])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });

        $locations = cache()->remember('stores_top_locations', 600, fn() =>
            class_exists(Location::class) && Schema::hasTable('locations')
                ? Location::where('is_active', 1)->whereNull('parent_id')->orderBy('sort_order')->orderBy('name')->get()
                : collect()
        );

        return view('frontend.stores.index', [
            'stores' => $query->paginate(12)->withQueryString(),
            'categories' => $categories,
            'locations' => $locations,
        ]);
    }

    public function show($slug)
    {
        $store = Store::query()
            ->where('slug', $slug)
            ->whereIn('status', ['approved', 'published', 'active'])
            ->withCount(['listings' => fn ($q) => $q->published()])
            ->firstOrFail();

        $productsQuery = Listing::published()
            ->where('store_id', $store->id)
            ->with(['category', 'images', 'locationModel']);

        if (request()->filled('q')) {
            $keyword = trim(request('q'));
            $productsQuery->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if (request()->filled('category')) {
            $productsQuery->whereHas('category', fn ($q) => $q->where('slug', request('category')));
        }

        match (request('sort')) {
            'price_low' => $productsQuery->orderBy('price'),
            'price_high' => $productsQuery->orderByDesc('price'),
            'popular' => $productsQuery->orderByDesc('views'),
            default => $productsQuery->latest(),
        };

        $products = $productsQuery->paginate(12)->withQueryString();

        $categories = Category::whereHas('listings', fn ($q) => $q->where('store_id', $store->id)->published())
            ->where('is_active', 1)
            ->withCount(['listings' => fn ($q) => $q->where('store_id', $store->id)->published()])
            ->orderBy('name')
            ->get();

        $reviews = Review::where('store_id', $store->id)
            ->where('status', 'approved')
            ->with('user')
            ->latest()
            ->take(20)
            ->get();

        $storeDeals = Deal::active()
            ->where('store_id', $store->id)
            ->with(['listing' => fn ($q) => $q->with(['images', 'category', 'locationModel'])])
            ->orderByDesc('is_flash')
            ->orderByDesc('discount_percent')
            ->get();

        return view('frontend.stores.show', compact('store', 'products', 'categories', 'reviews', 'storeDeals'));
    }

    public function storeReview(Request $request, Store $store)
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:1000',
        ]);

        if ((int) $store->user_id === (int) auth()->id()) {
            return back()->with('error', 'You cannot review your own store.');
        }

        $existing = Review::where('user_id', auth()->id())
            ->where('store_id', $store->id)
            ->whereNull('listing_id')
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already reviewed this store.');
        }

        Review::create([
            'user_id'    => auth()->id(),
            'store_id'   => $store->id,
            'listing_id' => null,
            'rating'     => $data['rating'],
            'comment'    => $data['comment'],
            'status'     => 'pending',
        ]);

        if ($store->user_id) {
            UserNotification::send(
                $store->user_id,
                'new_review',
                'New review on "' . \Illuminate\Support\Str::limit($store->name, 40) . '"',
                auth()->user()->name . ' left a ' . $data['rating'] . '★ review.',
                '/store/' . $store->slug,
                $store->id
            );
        }

        return back()->with('success', 'Thank you! Your review has been posted.');
    }
}
