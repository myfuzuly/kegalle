<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Post;
use App\Models\Store;

class HomeController extends Controller
{
    public function index()
    {
        $featuredListings = Listing::published()
            ->with(['images', 'store'])
            ->where('is_featured', 1)
            ->latest()
            ->take(5)
            ->get();

        $latestListings = Listing::published()
            ->with(['images', 'store'])
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
            ->take(5)
            ->get();

        $categories = Category::query()
            ->where('is_active', 1)
            ->withCount('listings')
            ->take(8)
            ->get();

        $blogs = Post::published()->latest('published_at')->take(5)->get();

        return view('frontend.home', compact(
            'featuredListings',
            'latestListings',
            'latestClassified',
            'featuredStores',
            'categories',
            'blogs'
        ));
    }
}
