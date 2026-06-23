@extends('layouts.app')

@php
    $logo = !empty($store->logo) ? asset('storage/'.ltrim($store->logo,'/')) : null;
    $banner = !empty($store->banner) ? asset('storage/'.ltrim($store->banner,'/')) : (!empty($store->cover_image) ? asset('storage/'.ltrim($store->cover_image,'/')) : null);
    $storeDesc = \Illuminate\Support\Str::limit(strip_tags($store->description ?? ''), 155) ?: ($store->name.' — verified store on Kegalle Marketplace with '.($store->listings_count ?? 0).'+ products. Browse and contact directly.');
@endphp

@section('title', $store->name.' — Verified Store in '.($store->city ?? 'Kegalle').' · Kegalle Marketplace')
@section('meta_description', $storeDesc)
@if($logo)
@section('og_image', $logo)
@endif

@section('content')

<div class="container"><div class="k-breadcrumb"><a href="/">Home</a><span>›</span><a href="/stores">Stores</a><span>›</span><span class="current">{{ $store->name }}</span></div></div>

<div class="k-store-banner" style="margin:0">
    @if($banner)
        <img src="{{ $banner }}" alt="{{ $store->name }}" style="width:100%;height:100%;object-fit:cover">
    @else
        <div style="font-size:64px;opacity:.3">🖥️</div>
    @endif
    <div style="position:absolute;bottom:0;left:0;right:0;height:80px;background:linear-gradient(to top,rgba(0,0,0,.5),transparent)"></div>
</div>

<div class="k-store-profile">
    <div class="k-store-profile-card">
        <div class="k-store-profile-logo">
            @if($logo)
                <img src="{{ $logo }}" alt="{{ $store->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit">
            @else
                {{ strtoupper(substr($store->name,0,1)) }}
            @endif
        </div>
        <div class="k-store-profile-info">
            <h1 class="k-store-profile-name">
                {{ $store->name }}
                <span class="k-verified-badge">✓ Verified Store</span>
            </h1>
            <div class="k-stars">★★★★★ (4.8 · 128 reviews)</div>
            <div class="k-store-profile-meta">
                <div class="k-store-profile-meta-item">⊛ {{ $store->listings_count ?? 0 }}+ Products</div>
                <div class="k-store-profile-meta-item">🗓 Joined {{ $store->created_at?->format('M Y') ?? '—' }}</div>
                <div class="k-store-profile-meta-item">
                    <span class="k-store-open open" style="font-size:13px"><span class="k-store-open-dot"></span> Open Now</span>
                    <span style="color:var(--k-text-muted);font-size:12px">9:00 AM – 8:00 PM</span>
                </div>
                <div class="k-store-profile-meta-item">📍 {{ $store->city ?? $store->address ?? 'Kegalle, Kegalle' }}</div>
            </div>
        </div>
        <div class="k-store-actions">
            <a href="#" class="k-btn k-btn-outline">🤍 Follow Store</a>
            <a href="#" class="k-btn k-btn-primary">💬 Message</a>
        </div>
    </div>
</div>

<div class="k-store-nav-tabs mt-20">
    <div class="k-store-nav-tabs-inner">
        <a href="#" class="k-store-nav-tab active">Overview</a>
        <a href="#products" class="k-store-nav-tab">Products <span class="cnt">{{ $store->listings_count ?? 0 }}+</span></a>
        <a href="#reviews" class="k-store-nav-tab">Reviews <span class="cnt">128</span></a>
        <a href="#" class="k-store-nav-tab">Store Policies</a>
        <a href="#" class="k-store-nav-tab">About</a>
    </div>
</div>

<div class="container" style="padding-top:24px;padding-bottom:40px">
    <div class="k-store-detail-grid">
        <!-- Main -->
        <div>
            <div class="k-section">
                <h2 class="k-section-title mb-16">Featured Categories</h2>
                <div style="display:flex;gap:12px;overflow-x:auto;padding-bottom:4px">
                    @foreach(($categories ?? collect())->take(5) as $category)
                        <a href="/store/{{ $store->slug }}?category={{ $category->slug }}" class="k-cat-pill" style="min-width:120px">
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
                        <div style="padding:30px;text-align:center;color:var(--k-text-tertiary)">No products found. This store has no approved products yet.</div>
                    @endforelse
                </div>
                {{ $products->links('vendor.pagination.k-theme') }}
            </div>

            <div id="reviews" class="k-section">
                <div class="k-section-header">
                    <h2 class="k-section-title">What Customers Say</h2>
                    <a href="#" class="k-section-link">View All Reviews →</a>
                </div>
                <div class="k-info-card">
                    <div class="k-review">
                        <div class="k-review-header"><div class="k-reviewer-avatar">N</div><div><div class="k-review-name">Nimal Perera</div><div class="k-stars" style="font-size:12px">★★★★★</div></div><div class="k-review-date" style="margin-left:auto">2 weeks ago</div></div>
                        <div class="k-review-text">Excellent customer service and genuine products.</div>
                    </div>
                    <div class="k-review">
                        <div class="k-review-header"><div class="k-reviewer-avatar" style="background:#1565C0">S</div><div><div class="k-review-name">Samanthi De Silva</div><div class="k-stars" style="font-size:12px">★★★★★</div></div><div class="k-review-date" style="margin-left:auto">1 month ago</div></div>
                        <div class="k-review-text">Bought a product and it was exactly as described.</div>
                    </div>
                    <div class="k-review">
                        <div class="k-review-header"><div class="k-reviewer-avatar" style="background:#E65100">R</div><div><div class="k-review-name">Ruwan Jayasinghe</div><div class="k-stars" style="font-size:12px">★★★★★</div></div><div class="k-review-date" style="margin-left:auto">2 months ago</div></div>
                        <div class="k-review-text">Fast delivery and good after-sales support.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <div class="k-info-card mb-16">
                <h3>Store Information</h3>
                <div class="k-info-row"><div class="k-info-icon">📍</div><div class="k-info-val">{{ $store->city ?? $store->address ?? 'Kegalle, Kegalle' }}</div></div>
                <div class="k-info-row"><div class="k-info-icon">🕐</div><div class="k-info-val"><span style="color:var(--k-primary);font-weight:600">Open Now</span><br><span style="font-size:11px">9:00 AM – 8:00 PM</span></div></div>
                <div class="k-info-row"><div class="k-info-icon">📞</div><div class="k-info-val"><a href="tel:{{ $store->phone ?? '+94771234567' }}">{{ $store->phone ?? '+94 77 123 4567' }}</a></div></div>
                <div class="k-info-row"><div class="k-info-icon">✉</div><div class="k-info-val"><a href="mailto:{{ $store->email ?? 'info@kegalle.lk' }}">{{ $store->email ?? 'info@kegalle.lk' }}</a></div></div>
                <div class="k-info-row"><div class="k-info-icon">🌐</div><div class="k-info-val"><a href="#">{{ $store->website ?? 'www.kegalle.lk' }}</a></div></div>
            </div>

            <div class="k-info-card mb-16">
                <h3>Store Statistics</h3>
                <div class="k-store-stats-grid">
                    <div class="k-store-stat-box"><strong>{{ $store->listings_count ?? 0 }}+</strong><span>Products</span></div>
                    <div class="k-store-stat-box"><strong>128</strong><span>Reviews</span></div>
                    <div class="k-store-stat-box"><strong>2+</strong><span>Years in Business</span></div>
                    <div class="k-store-stat-box"><strong>98%</strong><span>Positive Rating</span></div>
                </div>
                <div class="k-why-shop">
                    <div style="font-size:13px;font-weight:700;margin-bottom:10px;margin-top:14px">Why Shop With Us?</div>
                    <div class="k-why-item">100% Original Products</div>
                    <div class="k-why-item">Official Warranty</div>
                    <div class="k-why-item">7 Days Return Policy</div>
                    <div class="k-why-item">Secure Payments</div>
                    <div class="k-why-item">Dedicated Customer Support</div>
                </div>
                <div class="k-map-placeholder">🗺️</div>
                <div style="display:flex;justify-content:space-between;margin-top:8px">
                    <span style="font-size:12px;color:var(--k-text-secondary)">📍 {{ $store->city ?? 'Kegalle' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": {!! json_encode($store->name) !!},
    "description": {!! json_encode($storeDesc) !!},
    @if($logo)
    "image": {!! json_encode($logo) !!},
    @endif
    "address": {
        "@type": "PostalAddress",
        "addressLocality": {!! json_encode($store->city ?? 'Kegalle') !!},
        "addressRegion": "Sabaragamuwa",
        "addressCountry": "LK"
    },
    @if($store->phone)
    "telephone": {!! json_encode($store->phone) !!},
    @endif
    "url": {!! json_encode(url()->current()) !!}
}
</script>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {"@type": "ListItem", "position": 1, "name": "Home", "item": {!! json_encode(url('/')) !!}},
        {"@type": "ListItem", "position": 2, "name": "Stores", "item": {!! json_encode(url('/stores')) !!}},
        {"@type": "ListItem", "position": 3, "name": {!! json_encode($store->name) !!}, "item": {!! json_encode(url()->current()) !!}}
    ]
}
</script>
@endpush
@endsection
