<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Post;
use App\Models\Deal;
use App\Models\Store;
use App\Models\GovernmentService;
use App\Models\Event;

class HomeController extends Controller
{
    public function index()
    {
        $homeDeals = collect();
        $hasRealDeals = false;
        try {
            $homeDeals = Deal::active()
                ->with(['listing.images', 'listing.store', 'listing.category'])
                ->latest()
                ->take(10)
                ->get();
            $hasRealDeals = $homeDeals->isNotEmpty();
        } catch (\Throwable $e) {}

        if (!$hasRealDeals) {
            $homeDeals = collect();
        }

        $featuredListings = Listing::published()
            ->with(['images', 'store'])
            ->where('is_featured', 1)
            ->latest()
            ->take(10)
            ->get();

        $latestListings = Listing::published()
            ->with(['images', 'store'])
            ->latest()
            ->take(10)
            ->get();

        $featuredClassified = Listing::published()
            ->with('images')
            ->where('type', 'classified')
            ->where('is_featured', 1)
            ->latest()
            ->take(5)
            ->get();

        $latestClassified = Listing::published()
            ->with('images')
            ->where('type', 'classified')
            ->latest()
            ->take(5)
            ->get();

        $featuredStores = Store::query()
            ->withCount('listings')
            ->whereIn('status', ['approved', 'active', 'published'])
            ->where('is_featured', 1)
            ->take(10)
            ->get();

        if ($featuredStores->isEmpty()) {
            $featuredStores = Store::query()
                ->withCount('listings')
                ->whereIn('status', ['approved', 'active', 'published'])
                ->take(10)
                ->get();
        }

        $categories = Category::query()
            ->where('is_active', 1)
            ->withCount('listings')
            ->take(8)
            ->get();

        $blogs = Post::published()->latest('published_at')->take(5)->get();

        $govServices = GovernmentService::active()->withCount('items')->orderBy('sort_order')->take(8)->get();

        $upcomingEvents = collect();
        try {
            $upcomingEvents = Event::upcoming()->orderBy('event_date')->take(6)->get();
        } catch (\Throwable $e) {}

        return view('frontend.home', compact(
            'featuredListings',
            'latestListings',
            'featuredClassified',
            'latestClassified',
            'featuredStores',
            'categories',
            'blogs',
            'homeDeals',
            'hasRealDeals',
            'govServices',
            'upcomingEvents'
        ));
    }
}
