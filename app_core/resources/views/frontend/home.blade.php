@extends('layouts.app')

@section('title','Kegalle Marketplace — Buy, Sell & Discover Locally in Kegalle')
@section('meta_description','Buy and sell products, vehicles, property, electronics and more in Kegalle, Sri Lanka. Browse trusted local stores, classified ads, and verified sellers — all in one place.')

@push('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "Kegalle Marketplace",
  "url": "https://kurulla.com",
  "description": "Buy and sell products, vehicles, property, electronics and more in Kegalle, Sri Lanka.",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "https://kurulla.com/listings?q={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "Kegalle Marketplace",
  "url": "https://kurulla.com",
  "description": "The local online marketplace for the Kegalle district — buy, sell and discover products, services, stores and classified ads.",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "Kegalle",
    "addressRegion": "Sabaragamuwa",
    "addressCountry": "LK"
  },
  "areaServed": {
    "@type": "Place",
    "name": "Kegalle District, Sri Lanka"
  },
  "sameAs": []
}
</script>
@endpush

@section('content')
@php
    $featuredClassifiedItems = ($featuredClassified ?? collect())->take(5);
    $latestClassifiedItems = ($latestClassified ?? collect())->take(5);
    $blogItems = ($blogs ?? collect())->take(5);
    $palette = ['#E8F5E9','#E3F2FD','#FCE4EC','#FFF8E1','#F3E8FD','#E0F2F1'];
@endphp

<!-- Hero -->
<section class="home-hero">
    <div class="home-hero-photo"></div>
    <div class="home-hero-inner">
        <div>
            <h1>Buy, Sell & Discover<br>the best in <span>Kegalle</span></h1>
            <p>Find great deals on products, vehicles, properties and more from trusted sellers and local stores.</p>
            <div class="hero-cta-group">
                <a href="/listings" class="k-btn k-btn-primary k-btn-lg">Browse Listings</a>
                <a href="/register" class="k-btn k-btn-outline k-btn-lg">Post Free Ad</a>
            </div>
        </div>
        <div class="hero-actions-card">
            <h2>What would you like to do?</h2>
            <div class="hero-action-grid">
                <a href="/register?account_type=store" class="hero-action-btn selected">
                    <div class="hero-action-icon">🏪</div>
                    <strong>Store / Business</strong>
                    <span>Sell products or services with your store</span>
                </a>
                <a href="/register?account_type=user" class="hero-action-btn">
                    <div class="hero-action-icon">👤</div>
                    <strong>Personal / Classified</strong>
                    <span>Post ads for personal use or services</span>
                </a>
            </div>
        </div>
    </div>
</section>

<div class="k-main">
    <div class="k-layout">
        <!-- Main Content -->
        <div>
            <!-- Ad Banner -->
            @include('frontend.partials.ad-banner', ['location' => 'home_top', 'style' => 'top'])

            <!-- Featured Stores -->
            <div class="k-section">
                <div class="k-section-header">
                    <h2 class="k-section-title">Featured Stores</h2>
                    <a href="/stores" class="k-section-link">View all →</a>
                </div>
                <div class="k-carousel-wrap">
                    @if($featuredStores->count() > 4)
                    <button type="button" class="k-carousel-side-btn k-carousel-side-prev" data-carousel-prev="stores-carousel" aria-label="Previous">‹</button>
                    @endif
                <div class="k-carousel" id="stores-carousel">
                    @forelse($featuredStores as $store)
                        @php $storeBg = $palette[$loop->index % count($palette)]; @endphp
                        <div class="k-carousel-item">
                            <a href="/store/{{ $store->slug }}" class="k-store-card-v2">
                                <div class="k-store-card-v2-logo" style="background:{{ $storeBg }}">
                                    @if($store->logo)
                                        <img loading="lazy" src="{{ asset('storage/'.ltrim($store->logo,'/')) }}" alt="{{ $store->name }}">
                                    @else
                                        <span>{{ strtoupper(substr($store->name,0,1)) }}</span>
                                    @endif
                                </div>
                                <div class="k-store-card-v2-name">{{ $store->name }}@if($store->is_verified)<span class="k-verified-tick" title="Verified Store">✓</span>@endif</div>
                                <div class="k-store-card-v2-meta">@if(($store->listings_count ?? 0) > 0){{ $store->listings_count }} {{ $store->listings_count == 1 ? 'Product' : 'Products' }} · @endif{{ $store->city ?? 'Kegalle' }}</div>
                            </a>
                        </div>
                    @empty
                        <div class="k-empty-state">No stores found.</div>
                    @endforelse
                    <div class="k-carousel-item">
                        <a href="/register?account_type=store" class="k-store-card-v2 k-store-card-v2-add">
                            <div class="k-store-card-v2-add-icon">+</div>
                            <div class="k-store-card-v2-name">Add Your Store</div>
                            <div class="k-store-card-v2-meta">Start selling today</div>
                        </a>
                    </div>
                </div>
                    @if($featuredStores->count() > 4)
                    <button type="button" class="k-carousel-side-btn k-carousel-side-next" data-carousel-next="stores-carousel" aria-label="Next">›</button>
                    @endif
                </div>
            </div>

            <!-- Featured Ads -->
            <div class="k-section">
                <div class="k-section-header">
                    <h2 class="k-section-title">Featured Ads</h2>
                    <a href="/listings?featured=1" class="k-section-link">View all →</a>
                </div>
                @if($featuredListings->count())
                    <div class="k-carousel-wrap">
                        @if($featuredListings->count() > 4)
                        <button type="button" class="k-carousel-side-btn k-carousel-side-prev" data-carousel-prev="featured-carousel" aria-label="Previous">‹</button>
                        @endif
                        <div class="k-carousel" id="featured-carousel">
                            @foreach($featuredListings as $listing)
                                <div class="k-carousel-item">
                                    @include('frontend.listings.card',['listing'=>$listing])
                                </div>
                            @endforeach
                        </div>
                        @if($featuredListings->count() > 4)
                        <button type="button" class="k-carousel-side-btn k-carousel-side-next" data-carousel-next="featured-carousel" aria-label="Next">›</button>
                        @endif
                    </div>
                @else
                    <div class="k-empty-state">No featured ads found.</div>
                @endif
            </div>

            <!-- Latest Ads -->
            <div class="k-section">
                <div class="k-section-header">
                    <h2 class="k-section-title">Latest Ads</h2>
                    <a href="/listings" class="k-section-link">View all →</a>
                </div>
                <div class="k-grid-4">
                    @forelse($latestListings->take(4) as $listing)
                        @include('frontend.listings.card',['listing'=>$listing])
                    @empty
                        <div class="k-empty-state">No latest ads found.</div>
                    @endforelse
                </div>
            </div>

            <!-- Recently Viewed -->
            <div class="k-section k-recently-viewed-section" id="kRecentlyViewed" style="display:none">
                <div class="k-section-header">
                    <h2 class="k-section-title">Recently Viewed</h2>
                    <a href="#" class="k-section-link" onclick="localStorage.removeItem('k_recently_viewed');document.getElementById('kRecentlyViewed').style.display='none';return false">Clear</a>
                </div>
                <div class="k-grid-4" id="kRecentlyViewedGrid"></div>
            </div>

            <!-- CTA Banner -->
            <div class="k-cta-banner mb-20">
                <div>
                    <h3>Stand Out with Featured Ads</h3>
                    <p>Get more views and sell faster with premium placement.</p>
                </div>
                <a href="/dashboard/membership" class="k-btn k-btn-white k-btn-lg">Feature Your Ad</a>
            </div>

            <!-- Deals -->
            @if(isset($hasRealDeals) && $hasRealDeals && $homeDeals->count())
            <div class="k-section">
                <div class="k-section-header">
                    <h2 class="k-section-title">🔥 Hot Deals</h2>
                    <a href="/deals" class="k-section-link">View all →</a>
                </div>
                <div class="k-carousel-wrap">
                    @if($homeDeals->count() > 4)
                    <button type="button" class="k-carousel-side-btn k-carousel-side-prev" data-carousel-prev="deals-carousel" aria-label="Previous">‹</button>
                    @endif
                    <div class="k-carousel" id="deals-carousel">
                        @foreach($homeDeals as $deal)
                            @php
                                $dListing = $deal->listing;
                                if(!$dListing) continue;
                                $dImg = optional($dListing->images->first())->path ?? $dListing->image ?? null;
                                $dHasImg = (bool) $dImg;
                                $dImgUrl = $dHasImg ? asset('storage/'.ltrim($dImg,'/')) : null;
                                $dWebp = $dHasImg ? asset('storage/'.ltrim(\App\Helpers\ImageHelper::webpPath($dImg),'/')) : null;
                                $dLocation = optional($dListing->locationModel)->name ?? $dListing->location ?? 'Kegalle';
                            @endphp
                            <div class="k-carousel-item">
                                <a class="k-deal-card-home" href="/listings/{{ $dListing->slug ?? '#' }}">
                                    <div class="k-deal-card-img">
                                        @if($dHasImg)
                                            <img loading="lazy" decoding="async" src="{{ $dImgUrl }}" alt="{{ $dListing->title }}" onerror="this.closest('.k-deal-card-img').classList.add('k-img-failed');this.remove()">
                                        @else
                                            <div class="k-deal-card-ph">🛍️</div>
                                        @endif
                                        <div class="k-deal-card-pct">-{{ number_format($deal->discount_percent, 0) }}%</div>
                                        @if($deal->is_flash)<div class="k-deal-card-flash">⚡ Flash</div>@endif
                                    </div>
                                    <div class="k-deal-card-body">
                                        <div class="k-deal-card-title">{{ $dListing->title }}</div>
                                        <div class="k-deal-card-loc">📍 {{ $dLocation }}</div>
                                        <div class="k-deal-card-prices">
                                            <span class="k-deal-card-sale">LKR {{ number_format($deal->deal_price) }}</span>
                                            <span class="k-deal-card-orig">LKR {{ number_format($deal->original_price) }}</span>
                                        </div>
                                        <div class="k-deal-card-save">Save LKR {{ number_format($deal->original_price - $deal->deal_price) }}</div>
                                        <div class="k-deal-card-timer" data-deal-end="{{ $deal->ends_at->toIso8601String() }}">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                            <span class="k-deal-card-countdown">Loading...</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    @if($homeDeals->count() > 4)
                    <button type="button" class="k-carousel-side-btn k-carousel-side-next" data-carousel-next="deals-carousel" aria-label="Next">›</button>
                    @endif
                </div>
            </div>
            @endif

            <!-- Browse Categories -->
            <div class="k-section">
                <div class="k-section-header">
                    <h2 class="k-section-title">Browse Categories</h2>
                    <a href="/categories" class="k-section-link">View all →</a>
                </div>
                <div class="k-cat-grid">
                    @foreach($categories->take(8) as $category)
                        <a href="/listings?categories[]={{ $category->slug }}" class="k-cat-card">
                            <div class="k-cat-card-icon">
                                @if(!empty($category->image))
                                    <img loading="lazy" src="{{ asset('storage/'.$category->image) }}" alt="">
                                @else
                                    <span>{{ $category->icon ?: '🛒' }}</span>
                                @endif
                            </div>
                            <div class="k-cat-card-name">{{ $category->name }}</div>
                            <div class="k-cat-card-count">{{ $category->listings_count ?? 0 }} {{ \Illuminate\Support\Str::plural('ad', $category->listings_count ?? 0) }}</div>
                        </a>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Right Column (Sidebar + Blog) -->
        <div class="k-home-right mt-20">
        <div class="k-home-sidebar">
            <!-- Classified Section -->
            <div class="k-classified-sidebar mb-20">
                <div class="k-classified-sidebar-header">
                    <span class="k-classified-sidebar-title">Classified Section <span class="k-tag k-tag-new k-tag-inline">New</span></span>
                    <a href="/classified" class="k-section-link k-link-sm">View all →</a>
                </div>
                <div class="k-classified-intro">
                    <p class="k-classified-intro-text">Post personal ads or find what you need in your area.</p>
                    <a href="/classified" class="k-btn k-btn-primary w-full k-btn-center">Explore Classifieds</a>
                </div>
                <div class="k-classified-sidebar-header pt-14">
                    <span class="k-sidebar-label">Featured Classified Ads</span>
                    <a href="/classified?featured=1" class="k-section-link k-link-xs">View all →</a>
                </div>
                <div class="k-sidebar-skeleton" id="k-sidebar-skeleton-1">
                    @for($i = 0; $i < 3; $i++)
                    <div class="k-skeleton-item"><div class="k-skeleton-thumb"></div><div class="k-skeleton-body"><div class="k-skeleton-line k-skeleton-line-long"></div><div class="k-skeleton-line k-skeleton-line-short"></div></div></div>
                    @endfor
                </div>
                @forelse($featuredClassifiedItems as $item)
                    @php
                        $img = optional($item->images->first())->path ?? $item->image ?? null;
                        $imgUrl = $img ? asset('storage/'.ltrim($img,'/')) : null;
                        $webpUrl = $img ? asset('storage/'.ltrim(\App\Helpers\ImageHelper::webpPath($img),'/')) : null;
                        $miniBg = $palette[($item->id ?? 0) % count($palette)];
                    @endphp
                    <a href="/listings/{{ $item->slug }}" class="k-classified-item">
                        @if($imgUrl)
                            <div class="k-classified-thumb k-overflow-hidden"><img loading="lazy" src="{{ $imgUrl }}" alt="{{ $item->title }}" class="k-cover-img"></div>
                        @else
                            <div class="k-classified-thumb" style="background:{{ $miniBg }}"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="k-classified-thumb-icon"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg></div>
                        @endif
                        <div class="k-classified-info">
                            <h4>{{ $item->title }}</h4>
                            <div class="loc">📍 {{ optional($item->locationModel)->name ?? $item->location ?? 'Kegalle' }}</div>
                            <div class="k-classified-price">{{ ($item->price ?? 0) > 0 ? 'LKR '.number_format($item->price) : 'Contact Seller' }}</div>
                        </div>
                        <div class="k-classified-time">{{ $item->created_at?->diffForHumans() ?? '' }}</div>
                    </a>
                @empty
                    <p class="k-sidebar-empty">No featured classifieds yet.</p>
                @endforelse
            </div>

            <!-- Premium Ad Space -->
            @include('frontend.partials.ad-banner', ['location' => 'home_sidebar', 'style' => 'box'])

            <!-- Latest Classified -->
            <div class="k-classified-sidebar mb-20">
                <div class="k-classified-sidebar-header">
                    <span class="k-sidebar-label">Latest Classified Ads</span>
                    <a href="/classified" class="k-section-link k-link-xs">View all →</a>
                </div>
                <div class="k-sidebar-skeleton" id="k-sidebar-skeleton-2">
                    @for($i = 0; $i < 3; $i++)
                    <div class="k-skeleton-item"><div class="k-skeleton-thumb"></div><div class="k-skeleton-body"><div class="k-skeleton-line k-skeleton-line-long"></div><div class="k-skeleton-line k-skeleton-line-short"></div></div></div>
                    @endfor
                </div>
                @forelse($latestClassifiedItems as $item)
                    @php
                        $img = optional($item->images->first())->path ?? $item->image ?? null;
                        $imgUrl = $img ? asset('storage/'.ltrim($img,'/')) : null;
                        $webpUrl = $img ? asset('storage/'.ltrim(\App\Helpers\ImageHelper::webpPath($img),'/')) : null;
                        $miniBg = $palette[($item->id ?? 0) % count($palette)];
                    @endphp
                    <a href="/listings/{{ $item->slug }}" class="k-classified-item">
                        @if($imgUrl)
                            <div class="k-classified-thumb k-overflow-hidden"><img loading="lazy" src="{{ $imgUrl }}" alt="{{ $item->title }}" class="k-cover-img"></div>
                        @else
                            <div class="k-classified-thumb" style="background:{{ $miniBg }}"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="k-classified-thumb-icon"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg></div>
                        @endif
                        <div class="k-classified-info">
                            <h4>{{ $item->title }}</h4>
                            <div class="loc">📍 {{ optional($item->locationModel)->name ?? $item->location ?? 'Kegalle' }}</div>
                            <div class="k-classified-price">{{ ($item->price ?? 0) > 0 ? 'LKR '.number_format($item->price) : 'Contact Seller' }}</div>
                        </div>
                        <div class="k-classified-time">{{ $item->created_at?->diffForHumans() ?? '' }}</div>
                    </a>
                @empty
                    <p class="k-sidebar-empty">No classifieds yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Latest Blog -->
        <div class="k-home-blog">
            <div class="k-section-header">
                <h3 class="k-sidebar-title">Latest Blog</h3>
                <a href="/blog" class="k-section-link k-link-sm">View all →</a>
            </div>
            <div class="k-blog-list">
                @forelse($blogItems as $post)
                    <a href="/blog/{{ $post->slug }}" class="k-blog-card">
                        @if($post->image)
                            <div class="k-blog-thumb k-overflow-hidden"><img loading="lazy" src="{{ asset('storage/'.ltrim($post->image,'/')) }}" alt="{{ $post->title }}" class="k-cover-img"></div>
                        @else
                            <div class="k-blog-thumb"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 002-2V4a2 2 0 00-2-2H8a2 2 0 00-2 2v16a2 2 0 01-2 2zm0 0a2 2 0 01-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8z"/></svg></div>
                        @endif
                        <div>
                            <div class="k-blog-title">{{ $post->title }}</div>
                            <div class="k-blog-date">{{ optional($post->published_at ?? $post->created_at)->format('M d, Y') }}</div>
                        </div>
                    </a>
                @empty
                    @foreach(['Top 10 Places to Visit in Kegalle' => 'May 10, 2025','Best Local Restaurants in Kegalle' => 'May 5, 2025','Kegalle Travel Guide 2025' => 'April 28, 2025'] as $title => $date)
                        <a href="#" class="k-blog-card">
                            <div class="k-blog-thumb"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 002-2V4a2 2 0 00-2-2H8a2 2 0 00-2 2v16a2 2 0 01-2 2zm0 0a2 2 0 01-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8z"/></svg></div>
                            <div><div class="k-blog-title">{{ $title }}</div><div class="k-blog-date">{{ $date }}</div></div>
                        </a>
                    @endforeach
                @endforelse
            </div>
        </div>
        </div><!-- end k-home-right -->

    </div>

    <!-- Government Services Section -->
    @if(($govServices ?? collect())->count())
    <div class="k-section k-home-gov">
        <div class="k-section-header">
            <div>
                <h2 class="k-section-title">Government Services in <span>Kegalle</span></h2>
                <p class="text-secondary text-sm mt-8">Access essential government services, offices and departments in the Kegalle district.</p>
            </div>
            <a href="/government-services" class="k-section-link">View all →</a>
        </div>
        <div class="k-home-gov-grid">
            @foreach($govServices as $gov)
                <a href="/government-services/{{ $gov->slug }}" class="k-home-gov-card">
                    <div class="k-home-gov-icon" style="background:linear-gradient(135deg,{{ $gov->icon_bg_start ?: '#1B6B3A' }},{{ $gov->icon_bg_end ?: '#145A2E' }})">
                        <span>{{ $gov->icon ?: '🏛️' }}</span>
                    </div>
                    <div class="k-home-gov-info">
                        <h4>{{ $gov->title }}</h4>
                        @if($gov->items_count)<span class="k-home-gov-count">{{ $gov->items_count }} {{ Str::plural('service', $gov->items_count) }}</span>@endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Upcoming Events Section -->
    @if(($upcomingEvents ?? collect())->count())
    <div class="k-section k-home-events">
        <div class="k-section-header">
            <div>
                <h2 class="k-section-title">Upcoming Events in <span>Kegalle</span></h2>
                <p class="text-secondary text-sm mt-8">Stay updated with the latest events, festivals and happenings in the Kegalle district.</p>
            </div>
            <a href="/events" class="k-section-link">View all →</a>
        </div>
        <div class="k-home-events-grid">
            @foreach($upcomingEvents as $event)
                <a href="/events/{{ $event->slug }}" class="k-home-event-card">
                    <div class="k-home-event-date-badge">
                        <span class="k-home-event-month">{{ $event->event_date->format('M') }}</span>
                        <span class="k-home-event-day">{{ $event->event_date->format('d') }}</span>
                    </div>
                    <div class="k-home-event-info">
                        <h4>{{ $event->title }}</h4>
                        <div class="k-home-event-meta">
                            @if($event->venue)<span>📍 {{ $event->venue }}</span>@endif
                            @if($event->starts_at)<span>🕐 {{ $event->starts_at->format('g:i A') }}</span>@endif
                        </div>
                        @if($event->is_free)
                            <span class="k-home-event-tag k-home-event-free">Free</span>
                        @elseif($event->price)
                            <span class="k-home-event-tag">LKR {{ number_format($event->price) }}</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Explore Section -->
    @php
        $exploreItems = \App\Models\ExploreItem::where('is_active', 1)->orderBy('sort_order')->get();
    @endphp
    <div class="k-section k-home-explore">
        <div class="k-section-header">
            <div>
                <h2 class="k-section-title">Explore in <span>Kegalle</span></h2>
                <p class="text-secondary text-sm mt-8">Discover the best of Kegalle – culture, nature, history and more.</p>
            </div>
            <a href="/explore" class="k-section-link">View all →</a>
        </div>
        <div class="k-explore-grid">
            @if($exploreItems->count())
                @foreach($exploreItems as $explore)
                    <a href="{{ $explore->link_url ?: '#' }}" class="k-explore-card k-no-underline">
                        <div class="k-explore-placeholder" style="background:{{ $explore->image ? 'center/cover no-repeat url(\''.asset('storage/'.$explore->image).'\')' : 'linear-gradient(135deg,'.$explore->gradient_start.','.$explore->gradient_end.')' }}">
                            @unless($explore->image)<div class="k-explore-icon-text">{{ $explore->icon }}</div>@endunless
                        </div>
                        <div class="k-explore-overlay">
                            <h4>{{ $explore->title }}</h4>
                            @if($explore->item_list)
                                <ul>@foreach($explore->item_list as $point)<li>{{ $point }}</li>@endforeach</ul>
                            @endif
                            @if($explore->link_label)
                                <span class="k-explore-link">{{ $explore->link_label }}</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            @else
                <div class="k-explore-card">
                    <div class="k-explore-placeholder" style="background:linear-gradient(135deg,#388E3C,#1B5E20)"><div class="k-explore-icon-text">🚶</div></div>
                    <div class="k-explore-overlay">
                        <h4>Activities</h4>
                        <ul><li>Hiking & Trekking</li><li>Water Activities</li><li>Camping & Outdoor</li></ul>
                        <a href="#" class="k-explore-link">Explore Activities →</a>
                    </div>
                </div>
                <div class="k-explore-card">
                    <div class="k-explore-placeholder" style="background:linear-gradient(135deg,#0288D1,#01579B)"><div class="k-explore-icon-text">🐘</div></div>
                    <div class="k-explore-overlay">
                        <h4>Tourist Places</h4>
                        <ul><li>Pinnawala Elephant Orphanage</li><li>Bopath Ella</li><li>Bellena Cave</li></ul>
                        <a href="#" class="k-explore-link">Explore Places →</a>
                    </div>
                </div>
                <div class="k-explore-card">
                    <div class="k-explore-placeholder" style="background:linear-gradient(135deg,#2E7D32,#558B2F)"><div class="k-explore-icon-text">🌊</div></div>
                    <div class="k-explore-overlay">
                        <h4>Natural Resources</h4>
                        <ul><li>Samanala Reserve Forest</li><li>Kegalle Reservoir</li><li>Rivers & Waterfalls</li></ul>
                        <a href="#" class="k-explore-link">Explore Nature →</a>
                    </div>
                </div>
                <div class="k-explore-card">
                    <div class="k-explore-placeholder" style="background:linear-gradient(135deg,#5D4037,#4E342E)"><div class="k-explore-icon-text">🏛️</div></div>
                    <div class="k-explore-overlay">
                        <h4>Historic Places</h4>
                        <ul><li>Old Kegalle Dutch Fort</li><li>Warakapola</li><li>Archaeological Sites</li></ul>
                        <a href="#" class="k-explore-link">Explore History →</a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Testimonials — real approved reviews only -->
    @php
        $siteReviews = \App\Models\Review::with('user')
            ->where('status', 'approved')
            ->whereNotNull('comment')
            ->where('comment', '!=', '')
            ->where('rating', '>=', 4)
            ->latest()->take(3)->get();
    @endphp
    @if($siteReviews->count())
    <div class="k-section">
        <div class="k-section-header">
            <h2 class="k-section-title">What Our Users Say</h2>
        </div>
        <div class="k-testimonials-grid">
            @foreach($siteReviews as $rev)
            <div class="k-testimonial-card">
                <div class="k-testimonial-stars">{{ str_repeat('★', (int) $rev->rating) }}{{ str_repeat('☆', 5 - (int) $rev->rating) }}</div>
                <p>"{{ \Illuminate\Support\Str::limit($rev->comment, 160) }}"</p>
                <div class="k-testimonial-author">
                    <div class="k-testimonial-avatar">{{ strtoupper(substr($rev->user->name ?? 'U', 0, 1)) }}</div>
                    <div><strong>{{ $rev->user->name ?? 'Marketplace User' }}</strong><span>Verified Review</span></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif


</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.k-sidebar-skeleton').forEach(function(el){ el.style.display='none'; });

    var rv = [];
    try { rv = JSON.parse(localStorage.getItem('k_recently_viewed') || '[]'); } catch(e){}
    if (rv.length > 0) {
        var section = document.getElementById('kRecentlyViewed');
        var grid = document.getElementById('kRecentlyViewedGrid');
        if (section && grid) {
            section.style.display = '';
            grid.innerHTML = rv.slice(0, 4).map(function(item) {
                var imgHtml = item.image
                    ? '<div class="k-listing-img"><img src="' + item.image + '" alt="" class="k-img-loaded" loading="lazy"></div>'
                    : '<div class="k-listing-img"><div class="k-listing-img-placeholder">🛍️</div></div>';
                return '<a href="/listings/' + item.slug + '" class="k-listing-card">' +
                    imgHtml +
                    '<div class="k-listing-body"><h3 class="k-listing-title">' + item.title + '</h3>' +
                    '<div class="k-listing-price">' + item.price + '</div></div></a>';
            }).join('');
        }
    }
});
</script>
@endpush
