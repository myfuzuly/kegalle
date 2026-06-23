<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Location;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Store::query()
            ->whereIn('status', ['approved', 'published', 'active'])
            ->withCount(['listings' => fn ($q) => $q->published()]);

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

        $categories = Category::where('is_active', 1)
            ->withCount(['listings' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $locations = class_exists(Location::class) && Schema::hasTable('locations')
            ? Location::where('is_active', 1)->orderBy('sort_order')->orderBy('name')->get()
            : collect();

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
            'popular' => Schema::hasColumn('listings', 'views') ? $productsQuery->orderByDesc('views') : $productsQuery->latest(),
            default => $productsQuery->latest(),
        };

        $products = $productsQuery->paginate(12)->withQueryString();

        $categories = Category::whereHas('listings', fn ($q) => $q->where('store_id', $store->id)->published())
            ->where('is_active', 1)
            ->withCount(['listings' => fn ($q) => $q->where('store_id', $store->id)->published()])
            ->orderBy('name')
            ->get();

        return view('frontend.stores.show', compact('store', 'products', 'categories'));
    }
}
