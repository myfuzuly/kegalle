<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use App\Models\Listing;
use App\Models\Location;
use App\Models\Post;
use App\Models\Store;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemaps = [
            ['loc' => url('/sitemap-pages.xml'), 'lastmod' => now()->toDateString()],
            ['loc' => url('/sitemap-listings.xml'), 'lastmod' => now()->toDateString()],
            ['loc' => url('/sitemap-stores.xml'), 'lastmod' => now()->toDateString()],
            ['loc' => url('/sitemap-events.xml'), 'lastmod' => now()->toDateString()],
            ['loc' => url('/sitemap-blog.xml'), 'lastmod' => now()->toDateString()],
            ['loc' => url('/sitemap-towns.xml'), 'lastmod' => now()->toDateString()],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($sitemaps as $s) {
            $xml .= "  <sitemap>\n    <loc>{$s['loc']}</loc>\n    <lastmod>{$s['lastmod']}</lastmod>\n  </sitemap>\n";
        }
        $xml .= '</sitemapindex>';

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function pages()
    {
        $xml = cache()->remember('sitemap_pages', 86400, function () {
        $urls = [];
        $static = [
            ['url' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => '/listings', 'priority' => '0.9', 'changefreq' => 'hourly'],
            ['url' => '/stores', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['url' => '/classified', 'priority' => '0.8', 'changefreq' => 'hourly'],
            ['url' => '/events', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['url' => '/categories', 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['url' => '/locations', 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['url' => '/blog', 'priority' => '0.6', 'changefreq' => 'weekly'],
            ['url' => '/deals', 'priority' => '0.7', 'changefreq' => 'daily'],
            ['url' => '/government-services', 'priority' => '0.6', 'changefreq' => 'weekly'],
            ['url' => '/about-us', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['url' => '/contact-us', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['url' => '/help-center', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['url' => '/how-to-buy', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['url' => '/how-to-sell', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['url' => '/safety-tips', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['url' => '/faq', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['url' => '/terms-and-conditions', 'priority' => '0.2', 'changefreq' => 'yearly'],
            ['url' => '/privacy-policy', 'priority' => '0.2', 'changefreq' => 'yearly'],
            ['url' => '/cookie-policy', 'priority' => '0.2', 'changefreq' => 'yearly'],
        ];

        foreach ($static as $page) {
            $urls[] = [
                'loc' => url($page['url']),
                'lastmod' => '2026-08-01',
                'changefreq' => $page['changefreq'],
                'priority' => $page['priority'],
            ];
        }

        Category::where('is_active', 1)->select('slug')->chunk(200, function ($categories) use (&$urls) {
            foreach ($categories as $category) {
                $urls[] = [
                    'loc'        => url('/listings?categories[]=' . $category->slug),
                    'lastmod'    => '2026-08-01',
                    'changefreq' => 'weekly',
                    'priority'   => '0.5',
                ];
            }
        });

        return view('sitemap.index', compact('urls'))->render();
        });

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function listings()
    {
        $xml = cache()->remember('sitemap_listings', 3600, function () {
            $urls = [];
            Listing::published()->select(['slug', 'updated_at'])->orderByDesc('updated_at')->chunk(200, function ($listings) use (&$urls) {
                foreach ($listings as $listing) {
                    $urls[] = [
                        'loc' => url('/listings/' . $listing->slug),
                        'lastmod' => $listing->updated_at?->toDateString() ?? now()->toDateString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.7',
                    ];
                }
            });
            return view('sitemap.index', compact('urls'))->render();
        });

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function stores()
    {
        $xml = cache()->remember('sitemap_stores', 3600, function () {
            $urls = [];
            Store::whereIn('status', ['approved', 'active', 'published'])->select(['slug', 'updated_at'])->chunk(200, function ($stores) use (&$urls) {
                foreach ($stores as $store) {
                    $urls[] = [
                        'loc' => url('/store/' . $store->slug),
                        'lastmod' => $store->updated_at?->toDateString() ?? now()->toDateString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.6',
                    ];
                }
            });
            return view('sitemap.index', compact('urls'))->render();
        });

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function events()
    {
        $xml = cache()->remember('sitemap_events', 3600, function () {
            $urls = [];
            Event::published()->select(['slug', 'updated_at'])->orderByDesc('updated_at')->chunk(200, function ($events) use (&$urls) {
                foreach ($events as $event) {
                    $urls[] = [
                        'loc' => url('/events/' . $event->slug),
                        'lastmod' => $event->updated_at?->toDateString() ?? now()->toDateString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.6',
                    ];
                }
            });
            return view('sitemap.index', compact('urls'))->render();
        });

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function blog()
    {
        $xml = cache()->remember('sitemap_blog', 3600, function () {
            $urls = [];
            Post::published()->select(['slug', 'updated_at'])->chunk(200, function ($posts) use (&$urls) {
                foreach ($posts as $post) {
                    $urls[] = [
                        'loc'        => url('/blog/' . $post->slug),
                        'lastmod'    => $post->updated_at?->toDateString() ?? now()->toDateString(),
                        'changefreq' => 'monthly',
                        'priority'   => '0.5',
                    ];
                }
            });
            return view('sitemap.index', compact('urls'))->render();
        });

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function towns()
    {
        $xml = cache()->remember('sitemap_towns', 86400, function () {
            $urls = [];
            $towns = Location::where('is_active', 1)->select(['slug', 'updated_at'])->get();
            foreach ($towns as $town) {
                $urls[] = [
                    'loc' => url('/town/' . $town->slug),
                    'lastmod' => $town->updated_at?->toDateString() ?? now()->toDateString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                ];
            }
            return view('sitemap.index', compact('urls'))->render();
        });

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }

    private function renderUrlset(array $urls)
    {
        $xml = view('sitemap.index', compact('urls'))->render();
        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
