@extends('layouts.app')

@section('title','Kegalle Marketplace — Buy, Sell & Discover Locally in Kegalle')
@section('meta_description','Buy and sell products, vehicles, property, electronics and more in Kegalle, Sri Lanka. Browse trusted local stores, classified ads, and verified sellers — all in one place.')

@push('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "Kegalle Marketplace",
  "url": "https://kegalle.com",
  "description": "The local online marketplace for the Kegalle district — buy, sell and discover products, services, stores and classified ads.",
  "telephone": "+94712930930",
  "email": "support@kegalle.com",
  "logo": {
    "@type": "ImageObject",
    "url": "https://kegalle.com/images/kegalle-logo.jpg",
    "width": 300,
    "height": 100
  },
  "image": "https://kegalle.com/images/kegalle-hero-clocktower-v3.png",
  "priceRange": "LKR",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Kegalle Town",
    "addressLocality": "Kegalle",
    "addressRegion": "Sabaragamuwa",
    "postalCode": "71000",
    "addressCountry": "LK"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": 7.2513,
    "longitude": 80.3464
  },
  "areaServed": {
    "@type": "Place",
    "name": "Kegalle District, Sri Lanka"
  },
  "openingHoursSpecification": {
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],
    "opens": "00:00",
    "closes": "23:59"
  },
  "sameAs": [
    "https://www.facebook.com/kegallecom",
    "https://wa.me/94712930930",
    "https://www.instagram.com/kegallecom"
  ]
}
</script>
@endpush


@push('styles')
<style nonce="{{ $cspNonce ?? '' }}">
{{-- Dynamic hero slide backgrounds --}}
@php $hSlides = $heroSlides ?? collect(); @endphp
@foreach($hSlides as $hi => $hSlide)
.hs-slide-{{ ($hi%4)+1 }}{background-image:url('{{ addslashes($hSlide->image_url) }}')}
@endforeach
@if($hSlides->isEmpty())
.hs-slide-1{background-image:url('/images/kegalle-town.png')}
@endif
{{-- Dynamic rank badge colours --}}
@foreach($featuredStores ?? collect() as $hStore)
@if(!empty($hStore->rank_label))
.k-rank-{{ $hStore->id }}{background:{{ $hStore->rank_color ?? '#1b5e20' }}}
@endif
@endforeach
{{-- Dynamic classified colours --}}
@php $hPalette=['#E8F5E9','#E3F2FD','#FCE4EC','#FFF8E1','#F3E8FD','#E0F2F1'];
$hClassified=($featuredClassified ?? collect())->merge($latestClassified ?? collect())->unique('id'); @endphp
@foreach($hClassified as $hItem)
.k-cf-bg-{{ $hItem->id }}{background:{{ $hPalette[($hItem->id ?? 0) % count($hPalette)] }}}
@endforeach
{{-- Dynamic gov service gradients --}}
@foreach($govServices ?? collect() as $hGov)
.k-gov-bg-{{ $hGov->id }}{background:linear-gradient(135deg,{{ preg_match('/^#[0-9a-fA-F]{3,8}$/', $hGov->icon_bg_start ?? '') ? $hGov->icon_bg_start : '#1B6B3A' }},{{ preg_match('/^#[0-9a-fA-F]{3,8}$/', $hGov->icon_bg_end ?? '') ? $hGov->icon_bg_end : '#145A2E' }})}
@endforeach
{{-- Dynamic explore item backgrounds --}}
@foreach($exploreItems ?? collect() as $hExp)
@php $expGs = preg_match('/^#[0-9a-fA-F]{3,8}$/',$hExp->gradient_start??'') ? $hExp->gradient_start : '#1B6B3A'; $expGe = preg_match('/^#[0-9a-fA-F]{3,8}$/',$hExp->gradient_end??'') ? $hExp->gradient_end : '#145A2E'; @endphp
.k-exp-bg-{{ $hExp->id }}{background:{!! $hExp->image ? 'center/cover no-repeat url('.json_encode(asset('storage/'.$hExp->image), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT).')' : 'linear-gradient(135deg,'.$expGs.','.$expGe.')' !!}}
@endforeach
</style>
@endpush

@section('content')
@php
    $featuredClassifiedItems = ($featuredClassified ?? collect())->take(5);
    $latestClassifiedItems = ($latestClassified ?? collect())->take(5);
    $blogItems = ($blogs ?? collect())->take(5);
    $palette = ['#E8F5E9','#E3F2FD','#FCE4EC','#FFF8E1','#F3E8FD','#E0F2F1'];
@endphp

<!-- Hero -->
<section id="home-hero" class="home-hero">
    <div class="home-hero-photo">
        @php $slides = $heroSlides ?? collect(); $slideCount = max($slides->count(), 1); @endphp
        @foreach($slides as $i => $slide)
        <div class="hs-slide hs-slide-{{ ($i % 4) + 1 }}" aria-hidden="true"></div>
        @endforeach
        @if($slides->isEmpty())
        <div class="hs-slide hs-slide-1" aria-hidden="true"></div>
        @endif
        <div class="hs-overlay" aria-hidden="true"></div>
        <button class="hs-arrow hs-arrow-prev" aria-label="Previous slide">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <button class="hs-arrow hs-arrow-next" aria-label="Next slide">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
        <div class="hs-bottom-bar" aria-hidden="true">
            <div class="hs-dots"></div>
            <div class="hs-progress-track"><div class="hs-progress-bar"></div></div>
        </div>
    </div>
    <div class="home-hero-inner">
        <div class="hero-text-card">
            <h1>Buy, Sell & Discover <br>the best in <span>Kegalle</span></h1>
            <p>Find great deals on products, vehicles, properties and more from trusted sellers and local stores.</p>
            <form action="/listings" method="GET" class="hero-search-form" role="search">
                <div class="hero-search-wrap">
                    <label for="hero-search-input" class="sr-only">Search listings</label>
                    <input id="hero-search-input" type="search" name="q" placeholder="Search listings, stores, products…" value="{{ request('q') }}" class="hero-search-input" autocomplete="off">
                    <button type="submit" class="hero-search-btn" aria-label="Search">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        Search
                    </button>
                </div>
            </form>
            <div class="hero-cta-group">
                <a href="/listings" class="k-btn k-btn-primary k-btn-lg">Browse Listings</a>
                <a href="{{ auth()->check() ? '/dashboard/listings/create' : '/register' }}" class="k-btn k-btn-outline k-btn-lg">Post Free Ad</a>
            </div>
            <div class="hero-trust">
                <div class="hero-trust-item"><span class="hero-trust-dot"></span>Free to list</div>
                <div class="hero-trust-item"><span class="hero-trust-dot"></span>Verified sellers</div>
                <div class="hero-trust-item"><span class="hero-trust-dot"></span>Local &amp; trusted</div>
            </div>
        </div>
        <div class="hero-actions-card">
            <h2 class="hero-card-heading">Create an account in one step and post your free ad.</h2>
            <div class="hero-action-grid">
                @auth
                <a href="/dashboard/stores/create" class="hero-action-btn selected">
                    <div class="hero-action-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
                    <strong>Store / Business</strong>
                    <span>Manage your store and products</span>
                </a>
                <a href="/dashboard/listings/create" class="hero-action-btn">
                    <div class="hero-action-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><line x1="8" y1="8" x2="16" y2="8"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="8" y1="16" x2="12" y2="16"/></svg></div>
                    <strong>Post a Free Ad</strong>
                    <span>List an item or classified ad</span>
                </a>
                @else
                <a href="/register?account_type=store" class="hero-action-btn selected">
                    <div class="hero-action-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
                    <strong>Store / Business</strong>
                    <span>Sell products or services with your store</span>
                </a>
                <a href="/register?account_type=user" class="hero-action-btn">
                    <div class="hero-action-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><line x1="8" y1="8" x2="16" y2="8"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="8" y1="16" x2="12" y2="16"/></svg></div>
                    <strong>Post a Free Ad</strong>
                    <span>List an item, service or classified</span>
                </a>
                @endauth
            </div>
            <div class="hero-trending">
                <span class="hero-trending-label">Trending:</span>
                <a href="/listings?q=mobile+phones" class="hero-trend-tag">Phones</a>
                <a href="/listings?q=furniture" class="hero-trend-tag">Furniture</a>
                <a href="/listings?q=vehicles" class="hero-trend-tag">Vehicles</a>
                <a href="/listings?q=electronics" class="hero-trend-tag">Electronics</a>
            </div>
        </div>
    </div>
</section>

<div class="k-main">
    <div class="k-layout">
        <!-- Main Content -->
        <div>
            <!-- Featured Ads — curated, highest buyer intent -->
            @if($featuredListings->count())
            <div class="k-section">
                <div class="k-section-header">
                    <h2 class="k-section-title">Featured Ads</h2>
                    <a href="/listings?featured=1" class="k-section-link">View all →</a>
                </div>
                    <div class="k-carousel-wrap">
                        @if($featuredListings->count() > 4)
                        <button type="button" class="k-carousel-side-btn k-carousel-side-prev" data-carousel-prev="featured-carousel" aria-label="Previous">‹</button>
                        @endif
                        <div class="k-carousel" id="featured-carousel">
                            @foreach($featuredListings as $listing)
                                <div class="k-carousel-item">
                                    @include('frontend.listings.card',['listing'=>$listing,'cardIndex'=>$loop->index])
                                </div>
                            @endforeach
                        </div>
                        @if($featuredListings->count() > 4)
                        <button type="button" class="k-carousel-side-btn k-carousel-side-next" data-carousel-next="featured-carousel" aria-label="Next">›</button>
                        @endif
                    </div>
            </div>
            @endif

            <!-- Ad Banner -->
            @include('frontend.partials.ad-banner', ['location' => 'home_top', 'style' => 'top'])

            <!-- Featured Stores — premium card carousel -->
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
                        @php
                            $hv = $store->updated_at?->timestamp ?? time();
                            $hBanner = !empty($store->banner) ? asset('storage/'.ltrim($store->banner,'/')).('?v='.$hv) : (!empty($store->cover_image) ? asset('storage/'.ltrim($store->cover_image,'/')).('?v='.$hv) : null);
                            $hLogo   = $store->logo   ? asset('storage/'.ltrim($store->logo,'/'))   : null;
                            $hAvg    = $store->approved_reviews_avg_rating ? round($store->approved_reviews_avg_rating,1) : 0;
                        @endphp
                        <div class="k-carousel-item khfs-item">
                            <a href="/store/{{ $store->slug }}" class="khfs-card">
                                <div class="khfs-banner">
                                    @if($hBanner)<img src="{{ $hBanner }}" alt="{{ $store->name }}" loading="lazy">@endif
                                    <div class="khfs-logo-ring">
                                        @if($hLogo)
                                            <img src="{{ $hLogo }}" alt="{{ $store->name }}" loading="lazy">
                                        @else
                                            <div class="khfs-logo-init">{{ strtoupper(substr($store->name,0,1)) }}</div>
                                        @endif
                                    </div>
                                    @if(!empty($store->rank_label))
                                        <span class="khfs-rank-badge k-rank-{{ $store->id }}">🏅 {{ $store->rank_label }}</span>
                                    @endif
                                </div>
                                <div class="khfs-body">
                                    <div class="khfs-name">{{ $store->name }}</div>
                                    <div class="khfs-badges">
                                        @if($store->is_verified)<span class="khfs-badge khfs-badge-v">✓ Verified</span>@endif
                                        @if($store->user?->phone_verified_at)<span class="khfs-badge khfs-badge-p">📱 Phone</span>@endif
                                    </div>
                                    <div class="khfs-footer">
                                        <div class="khfs-count"><strong>{{ $store->listings_count ?? 0 }}</strong> {{ Str::plural('Product', $store->listings_count ?? 0) }}</div>
                                        <span class="khfs-visit">Visit <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @empty
                            <div class="k-empty-state">No stores found.</div>
                        @endforelse
                        <div class="k-carousel-item khfs-item">
                            <a href="/register?account_type=store" class="khfs-add-card">
                                <div class="khfs-add-icon">+</div>
                                <div class="khfs-add-name">Add Your Store</div>
                                <div class="khfs-add-meta">Start selling today</div>
                            </a>
                        </div>
                    </div>
                    @if($featuredStores->count() > 4)
                    <button type="button" class="k-carousel-side-btn k-carousel-side-next" data-carousel-next="stores-carousel" aria-label="Next">›</button>
                    @endif
                </div>
            </div>

            <!-- Featured Services -->
            @if(isset($featuredServices) && $featuredServices->count())
            <div class="k-section">
                <div class="k-section-header">
                    <h2 class="k-section-title">Services</h2>
                    <a href="/services" class="k-section-link">View all →</a>
                </div>
                <div class="k-carousel-wrap">
                    @if($featuredServices->count() > 4)
                    <button type="button" class="k-carousel-side-btn k-carousel-side-prev" data-carousel-prev="services-carousel" aria-label="Previous">‹</button>
                    @endif
                    <div class="k-carousel" id="services-carousel">
                        @foreach($featuredServices as $svc)
                        @php
                            $svcImg = $svc->image ? asset('storage/'.ltrim($svc->image,'/')) : null;
                        @endphp
                        <div class="k-carousel-item">
                            <a href="/services/{{ $svc->slug }}" class="k-listing-card">
                                <div class="k-listing-img">
                                    @if($svcImg)
                                        <img src="{{ $svcImg }}" alt="{{ $svc->title }}" loading="lazy">
                                    @else
                                        <div class="k-listing-ph" style="font-size:2rem;display:flex;align-items:center;justify-content:center;height:100%;background:var(--k-bg-2);">🛠️</div>
                                    @endif
                                    @if($svc->service_type)
                                        <span class="k-badge k-badge-blue" style="position:absolute;top:8px;left:8px;font-size:11px;">{{ ucwords(str_replace('_',' ',$svc->service_type)) }}</span>
                                    @endif
                                </div>
                                <div class="k-listing-body">
                                    <div class="k-listing-title">{{ $svc->title }}</div>
                                    @if($svc->price_label)
                                        <div class="k-listing-price">{{ $svc->price_label }}</div>
                                    @endif
                                    <div class="k-listing-meta">
                                        @if($svc->location)<span>📍 {{ $svc->location }}</span>@endif
                                        @if($svc->user)<span>{{ $svc->user->name }}</span>@endif
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @if($featuredServices->count() > 4)
                    <button type="button" class="k-carousel-side-btn k-carousel-side-next" data-carousel-next="services-carousel" aria-label="Next">›</button>
                    @endif
                </div>
            </div>
            @endif

            <!-- Advertise Banner -->
            <div class="k-advertise-strip">
                <div class="k-advertise-strip-left">
                    <div class="k-advertise-strip-tag">Advertise</div>
                    <h3 class="k-advertise-strip-title">Grow your business with premium advertising</h3>
                    <p class="k-advertise-strip-sub">Reach thousands of local buyers in Kegalle — feature your store, boost your ads, or place a banner.</p>
                </div>
                <a href="/contact?subject=advertise" class="k-btn k-btn-white k-btn-lg k-advertise-strip-cta">Get Started →</a>
            </div>

            <!-- Deals -->
            @php $validDeals = isset($homeDeals) ? $homeDeals->filter(fn($d) => $d->listing) : collect(); @endphp
            @if($validDeals->count())
            <div class="k-section">
                <div class="k-section-header">
                    <h2 class="k-section-title">🔥 Hot Deals</h2>
                    <a href="/deals" class="k-section-link">View all →</a>
                </div>
                <div class="k-carousel-wrap">
                    @if($validDeals->count() > 4)
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
                                            <span class="k-deal-card-countdown" data-ends="{{ optional($deal->ends_at)->toISOString() }}">Ends soon</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    @if($validDeals->count() > 4)
                    <button type="button" class="k-carousel-side-btn k-carousel-side-next" data-carousel-next="deals-carousel" aria-label="Next">›</button>
                    @endif
                </div>
            </div>
            @endif

            <!-- Recently Viewed (localStorage-driven, hidden until JS populates) -->
            <div class="k-section k-recently-viewed-section k-hidden" id="kRecentlyViewedSection">
                <div class="k-section-header">
                    <h2 class="k-section-title">Recently Viewed</h2>
                    <button class="k-section-link k-recently-clear-btn" id="kClearRecent" type="button">Clear history</button>
                </div>
                <div class="k-carousel-wrap">
                    <div class="k-carousel" id="kRecentCarousel"></div>
                </div>
            </div>

            <!-- Latest Ads -->
            @if($latestListings->count())
            <div class="k-section" id="k-latest-ads-section">
                <div class="k-section-header">
                    <h2 class="k-section-title">Latest Ads</h2>
                    <a href="/listings" class="k-section-link">View all →</a>
                </div>
                <div class="k-grid-4" id="k-latest-grid">
                    @forelse($latestListings as $listing)
                        <div class="k-lazy-item{{ $loop->index >= 8 ? ' k-lazy-hidden' : '' }}" data-lazy-index="{{ $loop->index }}">
                            @include('frontend.listings.card',['listing'=>$listing,'cardIndex'=>$loop->index])
                        </div>
                    @empty
                        <div class="k-empty-state">No latest ads found.</div>
                    @endforelse
                </div>
                <div id="k-lazy-sentinel"></div>
                <div id="k-lazy-loader" aria-live="polite">
                    <span>Loading more ads...</span>
                </div>
            </div>
            @endif
            <script nonce="{{ $cspNonce ?? '' }}">
            (function(){
                if(window.innerWidth > 760){
                    var s=document.getElementById('k-lazy-sentinel');
                    var l=document.getElementById('k-lazy-loader');
                    if(s) s.remove(); if(l) l.remove();
                    return;
                } // mobile only
                var grid    = document.getElementById('k-latest-grid');
                var sentinel= document.getElementById('k-lazy-sentinel');
                var loader  = document.getElementById('k-lazy-loader');
                if(!grid || !sentinel) return;
                var items   = grid.querySelectorAll('.k-lazy-item');
                var total   = items.length;
                var loaded  = 8;
                if(total <= loaded){ sentinel.remove(); return; }
                var obs = new IntersectionObserver(function(entries){
                    if(!entries[0].isIntersecting) return;
                    loader.style.display='block';loader.classList.add('k-lazy-loading');
                    setTimeout(function(){
                        var end = Math.min(loaded + 8, total);
                        for(var i = loaded; i < end; i++){
                            items[i].classList.remove('k-lazy-hidden');
                        }
                        loaded = end;
                        loader.classList.remove('k-lazy-loading');
                        if(loaded >= total){ obs.disconnect(); sentinel.remove(); loader.remove(); }
                    }, 300);
                }, { rootMargin: '200px' });
                obs.observe(sentinel);
            })();
            </script>

            <!-- Browse Categories -->
            @if($categories->count())
            <div class="k-section">
                <div class="k-section-header">
                    <h2 class="k-section-title">Browse by Category</h2>
                    <a href="/categories" class="k-section-link">All categories →</a>
                </div>
                <div class="k-cat-icon-grid">
                    @foreach($categories as $cat)
                    <a href="/listings?categories[]={{ $cat->slug }}" class="k-cat-icon-item">
                        <div class="k-cat-icon-wrap">
                            @if(!empty($cat->image))
                                <img src="{{ asset('storage/'.$cat->image) }}" alt="{{ $cat->name }}" loading="lazy">
                            @else
                                <span class="k-cat-icon-emoji">{{ $cat->icon ?: '🛒' }}</span>
                            @endif
                        </div>
                        <span class="k-cat-icon-name">{{ $cat->name }}</span>
                        @if(($cat->listings_count ?? 0) > 0)
                            <span class="k-cat-icon-count">{{ $cat->listings_count }}</span>
                        @endif
                    </a>
                    @endforeach
                </div>
                <div class="k-cat-cta-wrap">
                    <a href="/categories" class="k-cat-explore-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        Explore All {{ $totalCatCount ?? $categories->count() }} Categories
                    </a>
                </div>
            </div>
            @endif

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
                            <div class="k-classified-thumb k-cf-bg-{{ $item->id }}"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="k-classified-thumb-icon"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg></div>
                        @endif
                        <div class="k-classified-info">
                            <h4>{{ $item->title }}</h4>
                            <div class="loc">📍 {{ optional($item->locationModel)->name ?? $item->location ?? 'Kegalle' }}</div>
                            <div class="k-classified-price">{{ ($item->price ?? 0) > 0 ? 'LKR '.number_format($item->price) : 'Contact Seller' }}</div>
                        </div>
                        <div class="k-classified-time">{{ $item->created_at?->diffForHumans() ?? '' }}</div>
                    </a>
                @empty
                    <a href="/classified/create" class="k-sidebar-cta-link">+ Be the first to post a classified</a>
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
                            <div class="k-classified-thumb k-cf-bg-{{ $item->id }}"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="k-classified-thumb-icon"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg></div>
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
                    <a href="/blog" class="k-blog-card k-blog-empty-card">
                        <div class="k-blog-empty-text">Check back soon for local news &amp; guides.</div>
                    </a>
                @endforelse
            </div>
        </div>
        </div><!-- end k-home-right -->

    </div>

    <!-- Community Sections — separated visually from marketplace content -->
    <div class="k-community-band">
        <div class="k-community-band-header">
            <div class="k-community-band-label">🏘️ Kegalle Community</div>
            <p class="k-community-band-sub">Local services, events and places to explore in the Kegalle district</p>
        </div>
    </div>

    <!-- Public Services Section -->
    @if(($govServices ?? collect())->count())
    <div class="k-section k-home-gov">
        <div class="k-section-header">
            <div>
                <h2 class="k-section-title">Public Services in <span>Kegalle</span></h2>
                <p class="text-secondary text-sm mt-8">Access essential public services, offices and departments in the Kegalle district.</p>
            </div>
            <a href="/public-services" class="k-section-link">View all →</a>
        </div>
        <div class="k-home-gov-grid">
            @foreach($govServices as $gov)
                <a href="/public-services/{{ $gov->slug }}" class="k-home-gov-card">
                    <div class="k-home-gov-icon k-gov-bg-{{ $gov->id }}">
                        <span>{{ $gov->icon ?: '🏛️' }}</span>
                    </div>
                    <div class="k-home-gov-info">
                        <h3>{{ $gov->title }}</h3>
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

    <!-- Stats strip -->
    <div class="k-stats-strip">
        <div class="k-stats-inner">
            <div class="k-stat-item">
                <div class="k-stat-icon green" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg></div>
                <div>
                    <div class="k-stat-val">{{ number_format($totalListings) }}+</div>
                    <div class="k-stat-lbl">Active Listings</div>
                </div>
            </div>
            <div class="k-stat-item">
                <div class="k-stat-icon blue" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
                <div>
                    <div class="k-stat-val">{{ number_format($totalStores) }}+</div>
                    <div class="k-stat-lbl">Verified Stores</div>
                </div>
            </div>
            <div class="k-stat-item">
                <div class="k-stat-icon orange" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg></div>
                <div>
                    <div class="k-stat-val">{{ number_format($totalCatCount) }}+</div>
                    <div class="k-stat-lbl">Product Categories</div>
                </div>
            </div>
            <div class="k-stat-item">
                <div class="k-stat-icon purple" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                <div>
                    <div class="k-stat-val">{{ number_format($totalUsers) }}+</div>
                    <div class="k-stat-lbl">Community Members</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Explore Section -->
    <div class="k-section k-home-explore kex-section">
        <div class="kex-header">
            <div class="kex-header-left">
                <div class="kex-eyebrow">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    Discover Kegalle
                </div>
                <h2 class="kex-title">Explore in <span>Kegalle</span></h2>
                <p class="kex-sub">Culture, nature, history and hidden gems of the district</p>
            </div>
            <a href="/explore" class="kex-view-all">View all <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
        </div>
        <div class="kex-grid">
            @if($exploreItems->count())
                @foreach($exploreItems as $ei => $explore)
                    <a href="{{ $explore->link_url ?: '#' }}" class="kex-card k-no-underline{{ $ei === 0 ? ' kex-card--hero' : '' }}">
                        <div class="kex-card-bg k-exp-bg-{{ $explore->id }}">
                            @unless($explore->image)<div class="kex-card-icon">{{ $explore->icon }}</div>@endunless
                        </div>
                        <div class="kex-card-overlay">
                            <div class="kex-card-body">
                                <div class="kex-card-pill">{{ $explore->icon ?? '📍' }} {{ $explore->title }}</div>
                                <h4 class="kex-card-title">{{ $explore->title }}</h4>
                                @if($explore->item_list)
                                    <ul class="kex-card-list">@foreach($explore->item_list as $point)<li>{{ $point }}</li>@endforeach</ul>
                                @endif
                                <span class="kex-card-cta">{{ $explore->link_label ?: 'Explore →' }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            @else
                <a href="/listings?category=activities" class="kex-card kex-card--hero k-no-underline">
                    <div class="kex-card-bg kex-ph-activities" style="background:linear-gradient(135deg,rgba(27,94,32,.55),rgba(46,125,50,.4)),center/cover no-repeat url('/images/kegalle-hero-clocktower-v3.png')"></div>
                    <div class="kex-card-overlay"><div class="kex-card-body">
                        <div class="kex-card-pill">🚶 Activities</div>
                        <h4 class="kex-card-title">Activities</h4>
                        <ul class="kex-card-list"><li>Hiking &amp; Trekking</li><li>Water Activities</li><li>Camping &amp; Outdoor</li></ul>
                        <span class="kex-card-cta">Explore Activities →</span>
                    </div></div>
                </a>
                <a href="/listings?category=tourist" class="kex-card k-no-underline">
                    <div class="kex-card-bg kex-ph-tourist"><div class="kex-card-icon">🐘</div></div>
                    <div class="kex-card-overlay"><div class="kex-card-body">
                        <div class="kex-card-pill">🐘 Places</div>
                        <h4 class="kex-card-title">Tourist Places</h4>
                        <ul class="kex-card-list"><li>Pinnawala</li><li>Bopath Ella</li><li>Bellena Cave</li></ul>
                        <span class="kex-card-cta">Explore Places →</span>
                    </div></div>
                </a>
                <a href="/listings?category=nature" class="kex-card k-no-underline">
                    <div class="kex-card-bg kex-ph-nature"><div class="kex-card-icon">🌊</div></div>
                    <div class="kex-card-overlay"><div class="kex-card-body">
                        <div class="kex-card-pill">🌊 Nature</div>
                        <h4 class="kex-card-title">Natural Resources</h4>
                        <ul class="kex-card-list"><li>Samanala Reserve</li><li>Kegalle Reservoir</li><li>Rivers &amp; Waterfalls</li></ul>
                        <span class="kex-card-cta">Explore Nature →</span>
                    </div></div>
                </a>
                <a href="/listings?category=history" class="kex-card k-no-underline">
                    <div class="kex-card-bg" style="background:center/cover no-repeat url('/images/kegalle-hero-clocktower-v3.png')"></div>
                    <div class="kex-card-overlay"><div class="kex-card-body">
                        <div class="kex-card-pill">🏛️ History</div>
                        <h4 class="kex-card-title">Historic Places</h4>
                        <ul class="kex-card-list"><li>Dutch Fort</li><li>Warakapola</li><li>Archaeological Sites</li></ul>
                        <span class="kex-card-cta">Explore History →</span>
                    </div></div>
                </a>
            @endif
            {{-- Premium upgrade CTA card --}}
            <div class="kex-card kex-card--premium">
                <div class="kex-premium-bg"></div>
                <div class="kex-premium-body">
                    <div class="kex-premium-badge">⭐ Premium</div>
                    <h4 class="kex-premium-title">Feature Your Place</h4>
                    <p class="kex-premium-desc">Get your business, hotel, restaurant or attraction listed prominently across Kegalle Marketplace.</p>
                    <ul class="kex-premium-perks">
                        <li>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Top placement in searches
                        </li>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Featured on homepage
                        </li>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Verified badge &amp; analytics
                        </li>
                    </ul>
                    <a href="/pricing" class="kex-premium-btn">See Premium Plans →</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Random Ads — mobile only, only shown when distinct content is available -->
    @if($randomAds->count())
    <div class="k-section k-home-random-ads">
        <div class="k-section-header">
            <div>
                <h2 class="k-section-title">More Ads in <span>Kegalle</span></h2>
            </div>
            <a href="/listings" class="k-section-link">View all →</a>
        </div>
        <div class="k-grid-4" id="k-random-grid">
            @foreach($randomAds as $listing)
                <div class="k-lazy-item{{ $loop->index >= 8 ? ' k-lazy-hidden' : '' }}" data-lazy-index="{{ $loop->index }}">
                    @include('frontend.listings.card', ['listing' => $listing, 'cardIndex' => $loop->index])
                </div>
            @endforeach
        </div>
        <div id="k-random-sentinel"></div>
        <div id="k-random-loader"><span>Loading more ads…</span></div>
    </div>
    <script nonce="{{ $cspNonce ?? '' }}">
    (function(){
        if(window.innerWidth > 760) return;
        var grid=document.getElementById('k-random-grid');
        var sentinel=document.getElementById('k-random-sentinel');
        var loader=document.getElementById('k-random-loader');
        if(!grid||!sentinel) return;
        var items=grid.querySelectorAll('.k-lazy-item');
        var total=items.length, loaded=8;
        var obs=new IntersectionObserver(function(entries){
            if(!entries[0].isIntersecting) return;
            loader.style.display='block';
            setTimeout(function(){
                var end=Math.min(loaded+8,total);
                for(var i=loaded;i<end;i++) items[i].classList.remove('k-lazy-hidden');
                loaded=end;
                loader.style.display='none';
                if(loaded>=total){obs.disconnect();sentinel.remove();loader.remove();}
            },300);
        },{rootMargin:'200px'});
        obs.observe(sentinel);
    })();
    </script>
    @endif{{-- /randomAds --}}

    <!-- Testimonials — real approved reviews only -->
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




</div>{{-- close k-main --}}

<!-- Mobile sticky CTA bar -->
<div class="k-mobile-cta-bar" id="kMobileCta">
    <a href="/listings" class="k-btn k-btn-outline">Browse Ads</a>
    <a href="{{ auth()->check() ? '/dashboard/listings/create' : '/register' }}" class="k-btn k-btn-primary">Post Free Ad</a>
</div>

<!-- How It Works — full-width section outside k-main -->
<div class="k-hiw-strip">
    <div class="k-hiw-inner">

        <div class="k-hiw-head">
            <div class="k-hiw-eyebrow">Free · Fast · Local</div>
            <h2 class="k-hiw-title">How Kegalle Marketplace Works</h2>
            <p class="k-hiw-sub">Buy and sell locally in 3 simple steps — no fees, no middlemen</p>
        </div>

        <div class="k-hiw-steps">
            <div class="k-hiw-step-card">
                <div class="k-hiw-step-num">1</div>
                <div class="k-hiw-step-emoji"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg></div>
                <h3 class="k-hiw-step-title">Post a Free Ad</h3>
                <p class="k-hiw-step-desc">List your item or service in under 2 minutes — add photos, set a price, choose your location.</p>
            </div>
            <div class="k-hiw-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></div>
            <div class="k-hiw-step-card">
                <div class="k-hiw-step-num">2</div>
                <div class="k-hiw-step-emoji"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
                <h3 class="k-hiw-step-title">Buyer Messages You</h3>
                <p class="k-hiw-step-desc">Interested buyers chat through the platform. Negotiate and agree on a price — no middlemen.</p>
            </div>
            <div class="k-hiw-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></div>
            <div class="k-hiw-step-card">
                <div class="k-hiw-step-num">3</div>
                <div class="k-hiw-step-emoji"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></div>
                <h3 class="k-hiw-step-title">Meet &amp; Deal</h3>
                <p class="k-hiw-step-desc">Meet in a safe public place in Kegalle, inspect the item, pay cash on pickup — done.</p>
            </div>
        </div>

        <div class="k-hiw-cta">
            <a href="{{ auth()->check() ? '/dashboard/listings/create' : '/register' }}" class="k-btn k-btn-primary k-btn-lg">Post Your Free Ad Now</a>
            <a href="/listings" class="k-hiw-browse-link">Browse All Listings →</a>
        </div>

        <div class="k-hiw-divider">What's on Kegalle Marketplace?</div>

        <div class="k-hiw-types">
            <a href="/listings" class="k-hiw-type">
                <span class="k-hiw-type-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><line x1="8" y1="8" x2="16" y2="8"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="8" y1="16" x2="12" y2="16"/></svg></span>
                <strong>Ads &amp; Listings</strong>
                <span>Products from local sellers</span>
            </a>
            <a href="/classified" class="k-hiw-type">
                <span class="k-hiw-type-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v2"/><path d="M2 14l5 5 9-9"/></svg></span>
                <strong>Classifieds</strong>
                <span>Jobs, notices &amp; announcements</span>
            </a>
            <a href="/deals" class="k-hiw-type">
                <span class="k-hiw-type-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></span>
                <strong>Hot Deals</strong>
                <span>Time-limited local discounts</span>
            </a>
            <a href="/stores" class="k-hiw-type">
                <span class="k-hiw-type-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                <strong>Stores</strong>
                <span>Verified local seller pages</span>
            </a>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.k-sidebar-skeleton').forEach(function(el){ el.style.display='none'; });

    // ── Recently Viewed ──────────────────────────────────────
    var KEY = 'k_recently_viewed';
    var ids = JSON.parse(localStorage.getItem(KEY) || '[]').slice(0, 8);
    var section  = document.getElementById('kRecentlyViewedSection');
    var carousel = document.getElementById('kRecentCarousel');
    var clearBtn = document.getElementById('kClearRecent');

    if(ids.length >= 2 && section && carousel) {
        fetch('/api/listings/recently-viewed?ids=' + ids.join(','))
            .then(function(r){ return r.ok ? r.json() : []; })
            .then(function(items){
                if(!items || items.length < 2) return;
                items.forEach(function(l){
                    if(!l||!l.slug||!l.title)return;
                    var title=l.title||'';
                    var price=l.price||'';
                    var loc=l.location||'Kegalle';
                    var card = document.createElement('div');
                    card.className = 'k-carousel-item';
                    card.innerHTML =
                        '<a class="kpc k-listing-card" href="/listings/' + l.slug + '" aria-label="' + title.replace(/"/g,'&quot;') + '">' +
                        '<div class="kpc-img">' +
                            (l.image
                                ? '<img loading="lazy" src="' + l.image + '" alt="' + title.replace(/"/g,'&quot;') + '" class="kpc-photo">'
                                : '<div class="kpc-placeholder"><span>📦</span></div>') +
                        '</div>' +
                        '<div class="kpc-body">' +
                            '<div class="kpc-title">' + title + '</div>' +
                            '<div class="kpc-meta"><span>📍 ' + loc + '</span></div>' +
                            '<div class="kpc-footer"><span class="kpc-price">' + price + '</span></div>' +
                        '</div></a>';
                    carousel.appendChild(card);
                });
                section.classList.remove('k-hidden');
            }).catch(function(){});
    }

    if(clearBtn) {
        clearBtn.addEventListener('click', function(){
            localStorage.removeItem(KEY);
            if(section) section.classList.add('k-hidden');
        });
    }

    // ── Hero Search Autocomplete ──────────────────────────────
    var heroInput = document.getElementById('hero-search-input');
    if(heroInput) {
        var acBox = document.createElement('ul');
        acBox.id = 'hero-ac-list';
        acBox.setAttribute('role','listbox');
        acBox.style.cssText='position:absolute;top:100%;left:0;right:0;background:var(--k-bg,#fff);border:1px solid var(--k-border,#ddd);border-top:0;border-radius:0 0 8px 8px;list-style:none;margin:0;padding:0;z-index:999;max-height:260px;overflow-y:auto;box-shadow:0 4px 12px rgba(0,0,0,.12);display:none;';
        heroInput.parentElement.style.position='relative';
        heroInput.parentElement.appendChild(acBox);
        var acTimer=null, acIdx=-1;
        function acClear(){acBox.style.display='none';acBox.innerHTML='';acIdx=-1;}
        function acFetch(q){
            clearTimeout(acTimer);
            if(!q||q.length<2){acClear();return;}
            acTimer=setTimeout(function(){
                fetch('/api/search-suggestions?q='+encodeURIComponent(q))
                    .then(function(r){return r.ok?r.json():[];})
                    .then(function(items){
                        acBox.innerHTML='';
                        if(!items||!items.length){acBox.style.display='none';return;}
                        items.slice(0,8).forEach(function(it,i){
                            var li=document.createElement('li');
                            li.setAttribute('role','option');
                            li.setAttribute('id','hero-ac-'+i);
                            li.style.cssText='padding:10px 16px;cursor:pointer;font-size:14px;color:var(--k-text,#222);';
                            li.textContent=it.title||it;
                            li.addEventListener('mousedown',function(e){e.preventDefault();heroInput.value=it.title||it;acClear();heroInput.closest('form').submit();});
                            li.addEventListener('mouseover',function(){acIdx=i;acHighlight();});
                            acBox.appendChild(li);
                        });
                        acIdx=-1;
                        acBox.style.display='block';
                    }).catch(acClear);
            },220);
        }
        function acHighlight(){
            Array.from(acBox.children).forEach(function(li,i){
                li.style.background=i===acIdx?'var(--k-primary-light,#e8f5e9)':'';
            });
        }
        heroInput.addEventListener('input',function(){acFetch(this.value.trim());});
        heroInput.addEventListener('keydown',function(e){
            var items=acBox.children;
            if(!items.length||acBox.style.display==='none')return;
            if(e.key==='ArrowDown'){e.preventDefault();acIdx=Math.min(acIdx+1,items.length-1);acHighlight();}
            else if(e.key==='ArrowUp'){e.preventDefault();acIdx=Math.max(acIdx-1,-1);acHighlight();}
            else if(e.key==='Enter'&&acIdx>=0){e.preventDefault();heroInput.value=items[acIdx].textContent;acClear();heroInput.closest('form').submit();}
            else if(e.key==='Escape'){acClear();}
        });
        heroInput.setAttribute('aria-autocomplete','list');
        heroInput.setAttribute('aria-haspopup','listbox');
        document.addEventListener('click',function(e){if(!heroInput.contains(e.target)&&!acBox.contains(e.target))acClear();});
    }

    // ── Hero Slider — class-based crossfade, no inline styles ──────────────────
    (function(){
        var INTERVAL = 6000;
        var slides = Array.prototype.slice.call(
            document.querySelectorAll('.home-hero-photo .hs-slide')
        );
        if (!slides.length) return;

        var cur = 0;
        var transitioning = false;
        var timer = null;
        var dots = [];

        var dotsWrap = document.querySelector('.hs-dots');
        var btnPrev  = document.querySelector('.hs-arrow-prev');
        var btnNext  = document.querySelector('.hs-arrow-next');
        var progBar  = document.querySelector('.hs-progress-bar');
        var photo    = document.querySelector('.home-hero-photo');

        // ── Init: activate first slide ──
        slides[0].classList.add('hs-active');

        // ── Build dots ──
        if (dotsWrap && slides.length > 1) {
            slides.forEach(function(_, i) {
                var d = document.createElement('button');
                d.className = 'hs-dot' + (i === 0 ? ' hs-dot-active' : '');
                d.setAttribute('aria-label', 'Go to slide ' + (i + 1));
                d.addEventListener('click', function() {
                    if (!transitioning) { goTo(i); resetTimer(); }
                });
                dotsWrap.appendChild(d);
                dots.push(d);
            });
        }

        function updateDots(i) {
            dots.forEach(function(d, idx) {
                d.classList.toggle('hs-dot-active', idx === i);
            });
        }

        // ── Crossfade: incoming gets .hs-entering (z:3) then .hs-active (opacity:1) ──
        function goTo(next) {
            if (next === cur || transitioning) return;
            transitioning = true;

            var outgoing = slides[cur];
            var incoming = slides[next];

            // Stack incoming on top before it's visible
            incoming.classList.add('hs-entering');

            // Double rAF guarantees browser has painted .hs-entering before transition
            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    incoming.classList.add('hs-active');    // opacity 1 via CSS
                    outgoing.classList.remove('hs-active'); // opacity 0 via CSS

                    setTimeout(function() {
                        incoming.classList.remove('hs-entering');
                        cur = next;
                        transitioning = false;
                        updateDots(cur);
                        restartProgress();
                    }, 1150);
                });
            });
        }

        function goNext() { goTo((cur + 1) % slides.length); }
        function goPrev() { goTo((cur - 1 + slides.length) % slides.length); }

        // ── Progress bar (width driven by JS; only dynamic part) ──
        function restartProgress() {
            if (progBar) {
                progBar.style.transition = 'none';
                progBar.style.width = '0%';
            }
            clearTimeout(timer);
            if (slides.length <= 1) return;
            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    if (progBar) {
                        progBar.style.transition = 'width ' + INTERVAL + 'ms linear';
                        progBar.style.width = '100%';
                    }
                    timer = setTimeout(goNext, INTERVAL);
                });
            });
        }

        function resetTimer() { clearTimeout(timer); restartProgress(); }

        // ── Arrows ──
        if (btnPrev) btnPrev.addEventListener('click', function() { goPrev(); resetTimer(); });
        if (btnNext) btnNext.addEventListener('click', function() { goNext(); resetTimer(); });
        if (slides.length <= 1) {
            if (btnPrev) btnPrev.style.display = 'none';
            if (btnNext) btnNext.style.display = 'none';
        }

        // ── Pause on hover ──
        if (photo && slides.length > 1) {
            photo.addEventListener('mouseenter', function() { clearTimeout(timer); });
            photo.addEventListener('mouseleave', resetTimer);
        }

        // ── Keyboard ──
        document.addEventListener('keydown', function(e) {
            if (e.target && (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA')) return;
            if (e.key === 'ArrowLeft')  { goPrev(); resetTimer(); }
            if (e.key === 'ArrowRight') { goNext(); resetTimer(); }
        });

        // ── Touch swipe ──
        var touchStartX = 0;
        if (photo) {
            photo.addEventListener('touchstart', function(e) {
                touchStartX = e.touches[0].clientX;
            }, { passive: true });
            photo.addEventListener('touchend', function(e) {
                var dx = e.changedTouches[0].clientX - touchStartX;
                if (Math.abs(dx) > 40) { dx < 0 ? goNext() : goPrev(); resetTimer(); }
            }, { passive: true });
        }

        // ── Start autoplay ──
        updateDots(0);
        restartProgress();
    })();
});
</script>
@endpush
