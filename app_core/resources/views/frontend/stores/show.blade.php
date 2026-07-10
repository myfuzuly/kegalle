@extends('layouts.app')

@php
    $logo = !empty($store->logo) ? asset('storage/'.ltrim($store->logo,'/')) : null;
    $banner = !empty($store->banner) ? asset('storage/'.ltrim($store->banner,'/')) : (!empty($store->cover_image) ? asset('storage/'.ltrim($store->cover_image,'/')) : null);
    $storeDesc = \Illuminate\Support\Str::limit(strip_tags($store->description ?? ''), 155) ?: ($store->name.' — verified store on Kegalle Marketplace with '.($store->listings_count ?? 0).'+ products. Browse and contact directly.');
    $avgRating = ($reviews ?? collect())->count() ? round(($reviews ?? collect())->avg('rating'), 1) : 0;
    $reviewCount = ($reviews ?? collect())->count();
    $rank = $store->rank;
    $rankLabel = $store->rank_label;
    $rankColor = $store->rank_color;
@endphp

@section('title', $store->name.' — Verified Store in '.($store->city ?? 'Kegalle').' · Kegalle Marketplace')
@section('meta_description', $storeDesc)
@if($logo)
@section('og_image', $logo)
@endif

@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'LocalBusiness',
    'name' => $store->name,
    'description' => \Illuminate\Support\Str::limit(strip_tags($store->description ?? ''), 300),
    'image' => $logo ?: asset('images/kegalle-placeholder.png'),
    'url' => url('/store/'.$store->slug),
    'telephone' => $store->phone ?? null,
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => $store->address ?? null,
        'addressLocality' => $store->city ?? 'Kegalle',
        'addressCountry' => 'LK',
    ],
    'aggregateRating' => $reviewCount > 0 ? [
        '@type' => 'AggregateRating',
        'ratingValue' => $avgRating,
        'reviewCount' => $reviewCount,
        'bestRating' => 5,
    ] : null,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Stores', 'item' => url('/stores')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $store->name],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')

<div class="container"><div class="k-breadcrumb"><a href="/">Home</a><span>›</span><a href="/stores">Stores</a><span>›</span><span class="current">{{ $store->name }}</span></div></div>

<div class="k-store-banner">
    @if($banner)
        <img src="{{ $banner }}" alt="{{ $store->name }}" class="k-cover-img">
    @else
        <div class="k-banner-placeholder">🖥️</div>
    @endif
    <div class="k-banner-gradient"></div>
</div>

<div class="k-store-profile">
    <div class="k-store-profile-card">
        <div class="k-store-profile-logo">
            @if($logo)
                <img src="{{ $logo }}" alt="{{ $store->name }}" class="k-cover-img k-radius-inherit">
            @else
                {{ strtoupper(substr($store->name,0,1)) }}
            @endif
        </div>
        <div class="k-store-profile-info">
            <h1 class="k-store-profile-name">
                {{ $store->name }}
                @if($store->is_verified)<span class="k-verified-badge">✓ Verified Store</span>@endif
                <span class="k-rank-badge" style="background:{{ $rankColor }};color:#fff;font-size:11px;padding:3px 10px;border-radius:20px;font-weight:700;margin-left:6px">{{ $rankLabel }}</span>
            </h1>
            <div class="k-store-profile-meta">
                <div class="k-store-profile-meta-item">⊛ {{ $store->listings_count ?? 0 }}+ Products</div>
                <div class="k-store-profile-meta-item">🗓 Joined {{ $store->created_at?->format('M Y') ?? '—' }}</div>
                <div class="k-store-profile-meta-item">📍 {{ $store->city ?? $store->address ?? 'Kegalle' }}</div>
                @if($reviewCount > 0)
                <div class="k-store-profile-meta-item">★ {{ $avgRating }} ({{ $reviewCount }} {{ \Illuminate\Support\Str::plural('review', $reviewCount) }})</div>
                @endif
            </div>
        </div>
        <div class="k-store-actions">
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $store->whatsapp ?? $store->phone ?? '94706930930') }}?text={{ urlencode('Hi, I found your store on Kegalle.com : '.$store->name) }}" class="k-btn k-btn-primary" target="_blank">💬 Message</a>
        </div>
    </div>
</div>

<div class="k-store-nav-tabs mt-20">
    <div class="k-store-nav-tabs-inner">
        <a href="#" class="k-store-nav-tab active" data-store-tab="overview">Overview</a>
        <a href="#products" class="k-store-nav-tab" data-store-tab="products">Products <span class="cnt">{{ $store->listings_count ?? 0 }}+</span></a>
        <a href="#reviews" class="k-store-nav-tab" data-store-tab="reviews">Reviews <span class="cnt">{{ $reviewCount }}</span></a>
        @if(!empty($store->description))
        <a href="#" class="k-store-nav-tab" data-store-tab="about">About</a>
        @endif
    </div>
</div>

<div class="container k-store-content">
    <div class="k-store-detail-grid">
        <!-- Main -->
        <div>
            <div class="k-section" id="overview-section">
                <h2 class="k-section-title mb-16">Featured Categories</h2>
                <div class="k-cat-scroll">
                    @foreach(($categories ?? collect())->take(5) as $category)
                        <a href="/store/{{ $store->slug }}?category={{ $category->slug }}" class="k-cat-pill k-cat-pill-min">
                            <span class="icon">{{ $category->icon ?? '🛒' }}</span><span class="label">{{ $category->name }}</span><span class="count">{{ $category->listings_count ?? 0 }} Products</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div id="products" class="k-section">
                <div class="k-section-header">
                    <h2 class="k-section-title">All Products</h2>
                    <form method="GET">
                        <select name="sort" class="k-sort-select" onchange="this.form.submit()">
                            <option value="">Newest First</option>
                            <option value="price_low" @selected(request('sort')==='price_low')>Price Low</option>
                            <option value="price_high" @selected(request('sort')==='price_high')>Price High</option>
                            <option value="popular" @selected(request('sort')==='popular')>Popular</option>
                        </select>
                    </form>
                </div>
                <div class="k-grid-4">
                    @forelse($products as $listing)
                        @include('frontend.listings.card',['listing'=>$listing])
                    @empty
                        <div class="k-empty-state">
                            <p style="font-size:32px;margin:0 0 8px">📦</p>
                            <p style="font-weight:700;margin:0 0 4px">No products yet</p>
                            <p style="color:var(--k-text-secondary);font-size:13px;margin:0">This store hasn't added any products. Check back soon!</p>
                        </div>
                    @endforelse
                </div>
                {{ $products->links('vendor.pagination.k-theme') }}
            </div>

            <!-- Reviews Section -->
            <div id="reviews" class="k-section" style="display:none">
                <h2 class="k-section-title mb-16">Customer Reviews</h2>

                <!-- Rating Summary -->
                <div style="display:flex;gap:30px;align-items:flex-start;margin-bottom:24px;flex-wrap:wrap">
                    <div style="text-align:center;min-width:120px">
                        <div style="font-size:48px;font-weight:800;color:var(--k-text-primary)">{{ $avgRating ?: '—' }}</div>
                        <div style="margin:4px 0">
                            @for($s = 1; $s <= 5; $s++)
                                <span style="color:{{ $s <= round($avgRating) ? '#f59e0b' : '#e5e7eb' }};font-size:18px">★</span>
                            @endfor
                        </div>
                        <div style="font-size:13px;color:var(--k-text-muted)">{{ $reviewCount }} {{ \Illuminate\Support\Str::plural('review', $reviewCount) }}</div>
                    </div>
                    <div style="flex:1;min-width:200px">
                        @for($r = 5; $r >= 1; $r--)
                            @php $cnt = ($reviews ?? collect())->where('rating', $r)->count(); $pct = $reviewCount ? round($cnt / $reviewCount * 100) : 0; @endphp
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                                <span style="font-size:12px;font-weight:600;width:30px;color:var(--k-text-secondary)">{{ $r }} ★</span>
                                <div style="flex:1;height:8px;background:#f1f5f9;border-radius:4px;overflow:hidden"><div style="height:100%;background:#f59e0b;border-radius:4px;width:{{ $pct }}%"></div></div>
                                <span style="font-size:12px;color:var(--k-text-muted);width:24px;text-align:right">{{ $cnt }}</span>
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- Review Form -->
                @auth
                    @if(!($reviews ?? collect())->where('user_id', auth()->id())->count())
                    <div style="background:var(--k-bg-secondary,#f8fafc);border-radius:12px;padding:20px;margin-bottom:24px">
                        <h4 style="margin:0 0 12px;font-size:15px">Write a Review for {{ $store->name }}</h4>
                        <form method="POST" action="/store/{{ $store->id }}/review">
                            @csrf
                            <div class="k-star-input" id="storeStarInput" style="margin-bottom:12px">
                                @for($s = 1; $s <= 5; $s++)
                                <span class="k-star-pick" data-val="{{ $s }}" style="font-size:28px;cursor:pointer;color:#e5e7eb;transition:color .15s">★</span>
                                @endfor
                                <input type="hidden" name="rating" id="storeRatingInput" value="0" required>
                            </div>
                            <textarea name="comment" rows="3" placeholder="Share your experience shopping at this store..." required minlength="5" maxlength="1000" style="width:100%;padding:12px;border:1.5px solid var(--k-border,#e5e8ef);border-radius:10px;font-size:14px;resize:vertical;font-family:inherit"></textarea>
                            <button type="submit" class="k-btn k-btn-primary" style="margin-top:10px">Submit Review</button>
                        </form>
                    </div>
                    @else
                    <p style="font-size:13px;color:var(--k-text-muted);margin-bottom:16px">You have already reviewed this store.</p>
                    @endif
                @else
                    <p style="font-size:13px;color:var(--k-text-muted);margin-bottom:16px"><a href="/login" style="color:var(--k-primary);font-weight:600">Log in</a> to write a review.</p>
                @endauth

                <!-- Review List -->
                @forelse(($reviews ?? collect()) as $review)
                    <div style="border-bottom:1px solid var(--k-border,#f1f5f9);padding:16px 0">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                            <div style="width:36px;height:36px;border-radius:50%;background:var(--k-primary,#1b5e20);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px">{{ strtoupper(substr(optional($review->user)->name ?? 'U', 0, 1)) }}</div>
                            <div style="flex:1">
                                <div style="font-weight:600;font-size:14px">{{ optional($review->user)->name ?? 'User' }}</div>
                                <div style="font-size:11px;color:var(--k-text-muted)">{{ $review->created_at?->diffForHumans() }}</div>
                            </div>
                            <div>
                                @for($s = 1; $s <= 5; $s++)
                                    <span style="color:{{ $s <= $review->rating ? '#f59e0b' : '#e5e7eb' }};font-size:14px">★</span>
                                @endfor
                            </div>
                        </div>
                        <p style="font-size:14px;color:var(--k-text-secondary);line-height:1.6;margin:0">{{ $review->comment }}</p>
                    </div>
                @empty
                    <div style="text-align:center;padding:30px;color:var(--k-text-muted)">
                        <span style="font-size:36px;display:block;margin-bottom:8px">📝</span>
                        <p>No reviews yet. Be the first to review this store!</p>
                    </div>
                @endforelse
            </div>

        </div>

        <!-- Sidebar -->
        <div>
            <!-- Rank & Rating Card -->
            <div class="k-info-card mb-16" style="text-align:center;padding:20px">
                <div style="font-size:36px;margin-bottom:4px">
                    @switch($rank)
                        @case('platinum') 💎 @break
                        @case('gold') 🥇 @break
                        @case('silver') 🥈 @break
                        @default 🥉
                    @endswitch
                </div>
                <div style="font-weight:800;font-size:16px;color:{{ $rankColor }}">{{ $rankLabel }}</div>
                @if($reviewCount > 0)
                <div style="margin-top:8px">
                    @for($s = 1; $s <= 5; $s++)
                        <span style="color:{{ $s <= round($avgRating) ? '#f59e0b' : '#e5e7eb' }};font-size:20px">★</span>
                    @endfor
                    <div style="font-size:13px;color:var(--k-text-muted);margin-top:2px">{{ $avgRating }} out of 5 ({{ $reviewCount }} reviews)</div>
                </div>
                @else
                <div style="font-size:12px;color:var(--k-text-muted);margin-top:6px">No reviews yet</div>
                @endif
            </div>

            <div class="k-info-card mb-16">
                <h3>Store Information</h3>
                <div class="k-info-row"><div class="k-info-icon">📍</div><div class="k-info-val">{{ $store->city ?? $store->address ?? 'Kegalle' }}</div></div>
                @if(!empty($store->phone))
                <div class="k-info-row"><div class="k-info-icon">📞</div><div class="k-info-val"><a href="tel:{{ $store->phone }}">{{ $store->phone }}</a></div></div>
                @endif
                @if(!empty($store->email))
                <div class="k-info-row"><div class="k-info-icon">✉</div><div class="k-info-val"><a href="mailto:{{ $store->email }}">{{ $store->email }}</a></div></div>
                @endif
                @if(!empty($store->website))
                <div class="k-info-row"><div class="k-info-icon">🌐</div><div class="k-info-val"><a href="{{ $store->website }}" target="_blank" rel="noopener">{{ $store->website }}</a></div></div>
                @endif
                <div class="k-info-row"><div class="k-info-icon">🗓</div><div class="k-info-val">Joined {{ $store->created_at?->format('M Y') ?? '—' }}</div></div>
            </div>

            <div class="k-info-card mb-16">
                <h3>Store Statistics</h3>
                <div class="k-store-stats-grid">
                    <div class="k-store-stat-box"><strong>{{ $store->listings_count ?? 0 }}+</strong><span>Products</span></div>
                    <div class="k-store-stat-box"><strong>{{ $reviewCount }}</strong><span>Reviews</span></div>
                    <div class="k-store-stat-box"><strong>{{ $avgRating ?: '—' }}</strong><span>Rating</span></div>
                    <div class="k-store-stat-box"><strong>{{ $store->created_at ? (int) $store->created_at->diffInYears(now()) + 1 : '1' }}+</strong><span>Years</span></div>
                </div>
                <div class="k-why-shop">
                    <div class="k-why-shop-title">Why Shop With Us?</div>
                    <div class="k-why-item">100% Original Products</div>
                    <div class="k-why-item">Official Warranty</div>
                    <div class="k-why-item">7 Days Return Policy</div>
                    <div class="k-why-item">Verified Seller</div>
                    <div class="k-why-item">Dedicated Customer Support</div>
                </div>
                @if($store->latitude && $store->longitude)
                    <div id="storeLocationMap" style="height:220px;border-radius:12px;border:1px solid #e5e8ef;margin-top:14px;z-index:1"></div>
                    <div class="k-map-footer" style="display:flex;justify-content:space-between;align-items:center;gap:8px">
                        <span class="k-text-xs k-text-secondary">📍 {{ $store->city ?? 'Kegalle' }}</span>
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $store->latitude }},{{ $store->longitude }}" target="_blank" rel="noopener" style="font-size:12px;font-weight:700;color:var(--k-primary,#1b5e20);text-decoration:none">Get Directions →</a>
                    </div>
                @else
                    <div class="k-map-placeholder">🗺️</div>
                    <div class="k-map-footer">
                        <span class="k-text-xs k-text-secondary">📍 {{ $store->city ?? 'Kegalle' }}</span>
                    </div>
                @endif
            <div class="k-info-card mb-16" style="text-align:center">
                <h3 style="font-size:14px">Share This Store</h3>
                <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap">
                    <a href="https://wa.me/?text={{ urlencode($store->name . ' — ' . url('/store/' . $store->slug)) }}" target="_blank" rel="noopener" class="k-btn k-btn-outline" style="font-size:12px;padding:8px 14px">WhatsApp</a>
                    <button type="button" class="k-btn k-btn-outline" style="font-size:12px;padding:8px 14px" onclick="navigator.clipboard.writeText(window.location.href).then(function(){this.textContent='Copied!'}.bind(this))">📋 Copy Link</button>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

@if(!empty($store->description))
<div class="container k-store-about-panel" id="store-about" style="display:none">
    <div class="k-section">
        <h2 class="k-section-title mb-16">About {{ $store->name }}</h2>
        <div class="k-prose">{!! nl2br(e($store->description)) !!}</div>
    </div>
</div>
@endif

@if(session('success'))
<div class="k-flash k-flash-success" id="flashMsg">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="k-flash k-flash-error" id="flashMsg">{{ session('error') }}</div>
@endif

@push('scripts')
<script>
(function(){
    var tabs = document.querySelectorAll('.k-store-nav-tab[data-store-tab]');
    var mainContent = document.querySelector('.k-store-content');
    var aboutPanel = document.getElementById('store-about');
    var overviewSection = document.getElementById('overview-section');
    var productsSection = document.getElementById('products');
    var reviewsSection = document.getElementById('reviews');

    function showTab(t) {
        tabs.forEach(function(x){x.classList.remove('active')});
        if(t === 'about'){
            if(mainContent) mainContent.style.display = 'none';
            if(aboutPanel) aboutPanel.style.display = '';
        } else {
            if(mainContent) mainContent.style.display = '';
            if(aboutPanel) aboutPanel.style.display = 'none';
            if(overviewSection) overviewSection.style.display = (t === 'overview' || t === 'products') ? '' : 'none';
            if(productsSection) productsSection.style.display = (t === 'overview' || t === 'products') ? '' : 'none';
            if(reviewsSection) reviewsSection.style.display = (t === 'reviews') ? '' : 'none';
        }
    }

    tabs.forEach(function(tab){
        tab.addEventListener('click', function(e){
            e.preventDefault();
            var t = this.dataset.storeTab;
            tabs.forEach(function(x){x.classList.remove('active')});
            this.classList.add('active');
            showTab(t);
        });
    });

    // Store star rating input
    var stars = document.querySelectorAll('#storeStarInput .k-star-pick');
    stars.forEach(function(star) {
        star.addEventListener('click', function() {
            var val = parseInt(this.dataset.val);
            document.getElementById('storeRatingInput').value = val;
            stars.forEach(function(s) {
                s.style.color = parseInt(s.dataset.val) <= val ? '#f59e0b' : '#e5e7eb';
            });
        });
        star.addEventListener('mouseenter', function() {
            var val = parseInt(this.dataset.val);
            stars.forEach(function(s) {
                s.style.color = parseInt(s.dataset.val) <= val ? '#fbbf24' : '#e5e7eb';
            });
        });
        star.addEventListener('mouseleave', function() {
            var current = parseInt(document.getElementById('storeRatingInput').value) || 0;
            stars.forEach(function(s) {
                s.style.color = parseInt(s.dataset.val) <= current ? '#f59e0b' : '#e5e7eb';
            });
        });
    });

    // If URL has #reviews, show reviews tab
    if(window.location.hash === '#reviews') {
        var reviewTab = document.querySelector('[data-store-tab="reviews"]');
        if(reviewTab) reviewTab.click();
    }

    // Flash message auto-hide
    var flash = document.getElementById('flashMsg');
    if (flash) setTimeout(function() { flash.style.opacity = '0'; setTimeout(function() { flash.remove(); }, 300); }, 4000);
})();
</script>
@if($store->latitude && $store->longitude)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function(){
    var el = document.getElementById('storeLocationMap');
    if (!el || typeof L === 'undefined') return;
    var lat = {{ (float) $store->latitude }}, lng = {{ (float) $store->longitude }};
    var map = L.map('storeLocationMap', { scrollWheelZoom: false }).setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap', maxZoom: 19 }).addTo(map);
    L.marker([lat, lng]).addTo(map).bindPopup({!! json_encode($store->name) !!});
    setTimeout(function(){ map.invalidateSize(); }, 300);
})();
</script>
@endif
@endpush

@endsection
