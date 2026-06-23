<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Post;
use App\Models\Store;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [];

        // Static pages
        $static = [
            ['url' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => '/listings', 'priority' => '0.9', 'changefreq' => 'hourly'],
            ['url' => '/stores', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['url' => '/classified', 'priority' => '0.8', 'changefreq' => 'hourly'],
            ['url' => '/categories', 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['url' => '/locations', 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['url' => '/blog', 'priority' => '0.6', 'changefreq' => 'weekly'],
            ['url' => '/about-us', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['url' => '/contact-us', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['url' => '/help-center', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['url' => '/how-to-buy', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['url' => '/how-to-sell', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['url' => '/safety-tips', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['url' => '/faq', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['url' => '/terms-and-conditions', 'priority' => '0.2', 'changefreq' => 'yearly'],
            ['url' => '/privacy-policy', 'priority' => '0.2', 'changefreq' => 'yearly'],
        ];

        foreach ($static as $page) {
            $urls[] = [
                'loc' => url($page['url']),
                'lastmod' => now()->toDateString(),
                'changefreq' => $page['changefreq'],
                'priority' => $page['priority'],
            ];
        }

        // Listings
        Listing::published()->select(['slug', 'updated_at'])->orderByDesc('updated_at')->chunk(200, function ($listings) use (&$urls) {
            foreach ($listings as $listing) {
                $urls[] = [
                    'loc' => url('/listings/'.$listing->slug),
                    'lastmod' => $listing->updated_at?->toDateString() ?? now()->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            }
        });

        // Stores
        Store::whereIn('status', ['approved', 'active', 'published'])->select(['slug', 'updated_at'])->chunk(200, function ($stores) use (&$urls) {
            foreach ($stores as $store) {
                $urls[] = [
                    'loc' => url('/store/'.$store->slug),
                    'lastmod' => $store->updated_at?->toDateString() ?? now()->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                ];
            }
        });

        // Categories
        Category::where('is_active', 1)->select('slug')->get()->each(function ($category) use (&$urls) {
            $urls[] = [
                'loc' => url('/listings?categories[]='.$category->slug),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ];
        });

        // Blog posts
        Post::published()->select(['slug', 'updated_at'])->get()->each(function ($post) use (&$urls) {
            $urls[] = [
                'loc' => url('/blog/'.$post->slug),
                'lastmod' => $post->updated_at?->toDateString() ?? now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ];
        });

        $xml = view('sitemap.index', compact('urls'))->render();

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
