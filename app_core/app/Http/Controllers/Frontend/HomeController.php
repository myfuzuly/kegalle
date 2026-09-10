<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ExploreItem;
use App\Models\Setting;
use App\Models\Listing;
use App\Models\Post;
use App\Models\Deal;
use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use App\Models\GovernmentService;
use App\Models\Service;
use App\Models\HeroSlide;
use App\Models\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        // Cache slow/stable queries for 5 minutes; bust on listing/store/category save via model observers or manual Cache::forget('home_*')
        try {
            $heroSlides = Cache::remember('hero_slides', 300, fn () => HeroSlide::active());
        } catch (\Throwable $e) {
            $heroSlides = collect();
        }

        $homeCatCount = (int) Setting::getValue('home_categories_count', '12');
        $categories = Cache::remember('home_categories_v3_' . $homeCatCount, 300, fn () =>
            Category::query()
                ->where('is_active', 1)
                ->whereNull('parent_id')
                ->with([
                    'children' => fn ($q) => $q->where('is_active', 1)
                        ->withCount(['listings' => fn ($q2) => $q2->published()])
                        ->with(['children' => fn ($q2) => $q2->where('is_active', 1)
                            ->withCount(['listings' => fn ($q3) => $q3->published()])
                        ])
                ])
                ->withCount(['listings' => fn ($q) => $q->published()])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->each(function ($cat) {
                    $cat->listings_count = ($cat->listings_count ?? 0)
                        + $cat->children->sum('listings_count')
                        + $cat->children->sum(fn ($s) => $s->children->sum('listings_count'));
                })
                ->take($homeCatCount)
        );

        $totalCatCount = Cache::remember('home_total_cat_count_root', 300, fn () =>
            Category::whereNull('parent_id')->count()
        );

        $totalListings = Cache::remember('home_stat_listings', 300, fn () =>
            Listing::published()->count()
        );

        $totalStores = Cache::remember('home_stat_stores', 300, fn () =>
            Store::whereIn('status', ['approved', 'active', 'published'])->count()
        );

        $totalUsers = Cache::remember('home_stat_users', 300, fn () =>
            User::count()
        );

        $featuredStores = Cache::remember('home_featured_stores_v2', 300, function () {
            $publishedOnly = fn ($q) => $q->where('status', 'approved');
            $stores = Store::query()
                ->withCount(['listings as listings_count' => $publishedOnly])
                ->whereIn('status', ['approved', 'active', 'published'])
                ->where('is_featured', 1)
                ->orderByDesc('listings_count')
                ->orderBy('name')
                ->take(10)->get();
            if ($stores->isEmpty()) {
                $stores = Store::query()
                    ->withCount(['listings as listings_count' => $publishedOnly])
                    ->whereIn('status', ['approved', 'active', 'published'])
                    ->orderByDesc('listings_count')
                    ->orderBy('name')
                    ->take(10)->get();
            }
            return $stores;
        });

        $govServices = Cache::remember('home_gov_services', 600, fn () =>
            GovernmentService::active()->withCount('items')->orderBy('sort_order')->take(8)->get()
        );

        try {
            $blogs = Cache::remember('home_blogs', 300, fn () =>
                Post::published()->latest('published_at')->take(5)->get()
            );
        } catch (\Throwable $e) {
            $blogs = collect();
        }

        // Time-sensitive — shorter TTL or uncached
        $featuredListings = Cache::remember('home_featured_listings', 120, fn () =>
            Listing::published()->with(['images', 'store'])->where('is_featured', 1)->latest()->take(10)->get()
        );

        $latestListings = Cache::remember('home_latest_listings_8', 60, fn () =>
            Listing::published()->with(['images', 'store'])->latest()->take(8)->get()
        );

        $randomAds = collect();
        try {
            $excludeIds = $latestListings->pluck('id')->all();
            $bucket    = (int) floor(time() / 1800);
            $offset    = ($bucket * 13) % 40;
            $cacheDir  = storage_path('framework/searchcache');
            $cacheFile = $cacheDir . '/home_random_ads_' . $bucket . '.json';
            if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 1800) {
                $ids = json_decode(file_get_contents($cacheFile), true) ?: [];
            } else {
                $ids = Listing::published()->latest()->skip($offset)->take(40)->pluck('id')->all();
                @file_put_contents($cacheFile, json_encode($ids), LOCK_EX);
            }
            $ids = array_values(array_diff($ids, $excludeIds));
            if ($ids) {
                $randomAds = Listing::published()->with(['images', 'store'])->whereIn('id', $ids)->get()->sortBy(fn($l) => array_search($l->id, $ids))->values();
            }
        } catch (\Throwable $e) { Log::error('HomeController randomAds failed: ' . $e->getMessage()); }

        $featuredClassified = Cache::remember('home_featured_classified', 120, fn () =>
            Listing::published()->with('images')->where('type', 'classified')->where('is_featured', 1)->latest()->take(5)->get()
        );

        $latestClassified = Cache::remember('home_latest_classified', 60, fn () =>
            Listing::published()->with('images')->where('type', 'classified')->latest()->take(5)->get()
        );

        $homeDeals = collect();
        $hasRealDeals = false;
        try {
            $homeDeals = Cache::remember('home_deals', 120, fn () =>
                Deal::active()
                    ->whereHas('listing', fn ($q) => $q->published())
                    ->with(['listing.images', 'listing.store', 'listing.category'])
                    ->latest()->take(20)->get()
                    ->filter(fn ($d) => $d->listing !== null)
                    ->unique('listing_id')->take(10)->values()
            );
            $hasRealDeals = $homeDeals->isNotEmpty();
        } catch (\Throwable $e) { Log::error('HomeController deals query failed: '.$e->getMessage()); }

        $upcomingEvents = collect();
        try {
            $upcomingEvents = Cache::remember('home_events', 300, fn () =>
                Event::upcoming()->where('is_active', 1)->orderBy('event_date')->take(6)->get()
            );
        } catch (\Throwable $e) { Log::error('HomeController events query failed: '.$e->getMessage()); }

        $exploreItems = collect();
        try {
            $exploreItems = Cache::remember('home_explore_items', 600, fn () =>
                ExploreItem::where('is_active', 1)->orderBy('sort_order')->get()
            );
        } catch (\Throwable $e) { Log::error('HomeController exploreItems query failed: '.$e->getMessage()); }

        $featuredServices = collect();
        try {
            $featuredServices = Cache::remember('home_featured_services', 300, fn () =>
                Service::with('user')->where('status', 'approved')->latest()->take(8)->get()
            );
        } catch (\Throwable $e) { Log::error('HomeController featuredServices query failed: '.$e->getMessage()); }

        $siteReviews = collect();
        try {
            $siteReviews = Cache::remember('home_site_reviews', 600, fn () =>
                Review::with('user')->where('status', 'approved')
                    ->whereNotNull('comment')->where('comment', '!=', '')
                    ->where('rating', '>=', 4)->latest()->take(3)->get()
            );
        } catch (\Throwable $e) { Log::error('HomeController siteReviews query failed: '.$e->getMessage()); }

        $host = request()->getHost();
        if ($host === 'kegalle.com' || $host === 'www.kegalle.com') {
            return view('frontend.landing', compact(
                'totalListings', 'totalStores', 'totalUsers',
                'categories', 'siteReviews', 'featuredServices',
                'featuredListings', 'featuredStores'
            ));
        }

        return view('frontend.home', compact(
            'totalListings',
            'totalStores',
            'totalUsers',
            'heroSlides',
            'randomAds',
            'featuredListings',
            'latestListings',
            'featuredClassified',
            'latestClassified',
            'featuredStores',
            'categories',
            'totalCatCount',
            'blogs',
            'homeDeals',
            'hasRealDeals',
            'govServices',
            'upcomingEvents',
            'exploreItems',
            'siteReviews',
            'featuredServices'
        ));
    }
}
