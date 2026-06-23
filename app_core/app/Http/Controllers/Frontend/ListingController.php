<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Location;
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
            $query->whereHas('category', fn ($q) => $q->whereIn('slug', (array) $request->categories));
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

        $data = [
            'listings' => $query->paginate(12)->withQueryString(),
            'categories' => $categories,
            'locations' => $locations,
            'stores' => $stores,
        ];

        return $isClassified
            ? view('frontend.classified.index', $data)
            : view('frontend.listings.index', $data);
    }

    public function show($slug)
    {
        $listing = Listing::with(['images', 'category', 'store', 'user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $store = $listing->store; // could be null for classified ads

        if (Schema::hasColumn('listings', 'views')) {
            $listing->increment('views');
        }

        $related = Listing::published()
            ->with(['store', 'user', 'category', 'images', 'locationModel'])
            ->where('id', '!=', $listing->id)
            ->when($listing->category_id, fn ($q) => $q->where('category_id', $listing->category_id))
            ->latest()
            ->take(6)
            ->get();

        return view('frontend.listings.show', compact('listing', 'store', 'related'));
    }
}
