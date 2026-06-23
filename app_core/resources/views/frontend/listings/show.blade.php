@extends('layouts.app')

@php
    $images = $listing->images ?? collect();
    $hasImages = $images->count() > 0;
    $mainImage = optional($images->first())->path ?? $listing->image ?? null;
    $mainImageUrl = $mainImage ? asset('storage/'.ltrim($mainImage,'/')) : null;
    $sellerName = optional($listing->store)->name ?? optional($listing->user)->name ?? 'Seller';
    $location = optional($listing->locationModel)->name ?? $listing->location ?? 'Kegalle';
    $price = ($listing->price ?? 0) > 0 ? 'LKR '.number_format($listing->price) : 'Contact Seller';
    $whatsappNumber = preg_replace('/[^0-9]/', '', optional($listing->store)->whatsapp ?? optional($listing->store)->phone ?? '94771234567');
    $callNumber = optional($listing->store)->phone ?? optional($listing->user)->phone ?? '+94771234567';
    $seoAction = match(strtolower($listing->ad_type ?? 'sale')) {
        'rent' => 'for Rent',
        'wanted' => 'Wanted',
        'free' => 'Free',
        'exchange' => 'for Exchange',
        default => 'for Sale',
    };
    $seoDesc = \Illuminate\Support\Str::limit(strip_tags($listing->description ?? ''), 155) ?: ($listing->title.' '.$seoAction.' in '.$location.' — '.$price.'. Browse on Kegalle Marketplace.');
@endphp

@section('title', ($listing->title ?? 'Listing').' '.$seoAction.' in '.$location.' — '.$price.' · Kegalle Marketplace')
@section('meta_description', $seoDesc)
@if($mainImageUrl)
@section('og_image', $mainImageUrl)
@endif

@section('content')

<div class="container" style="padding-top:12px;padding-bottom:40px">
    <div class="k-breadcrumb">
        <a href="/">Home</a><span>›</span>
        <a href="/listings">All Ads</a><span>›</span>
        <a href="/listings?categories[]={{ optional($listing->category)->slug }}">{{ optional($listing->category)->name ?? 'General' }}</a><span>›</span>
        <span class="current">{{ $listing->title }}</span>
    </div>

    <div class="k-product-page-grid">
        <!-- Gallery -->
        <div class="k-product-gallery">
            <div class="k-product-main-img">
                @if($mainImageUrl)
                    <img src="{{ $mainImageUrl }}" alt="{{ $listing->title }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit">
                @else
                    <div style="font-size:100px">🛍️</div>
                @endif
                @if($listing->is_featured)
                    <div class="k-product-main-img-badge"><span class="k-tag k-tag-featured">Featured</span></div>
                @endif
            </div>
            <div class="k-product-thumbs">
                @forelse($images->take(4) as $i => $image)
                    <div class="k-product-thumb {{ $i === 0 ? 'active' : '' }}"><img src="{{ asset('storage/'.ltrim($image->path,'/')) }}" alt="{{ $listing->title }}" style="width:100%;height:100%;object-fit:cover"></div>
                @empty
                    <div class="k-product-thumb active">🛍️</div>
                @endforelse
            </div>
        </div>

        <!-- Info -->
        <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
                <span class="k-tag" style="background:var(--k-primary-xlight);color:var(--k-primary)">{{ $listing->is_featured ? 'Featured' : ucfirst($listing->type ?? 'Listing') }}</span>
            </div>
            <h1 style="font-family:var(--font-display);font-size:26px;font-weight:800;line-height:1.2;margin-bottom:12px">{{ $listing->title }}</h1>
            <div class="k-product-meta-row">
                <div class="k-stars">★★★★★ <span>(4.8 · 56 reviews)</span></div>
                <span style="color:var(--k-text-muted)">•</span>
                <span style="font-size:13px;color:var(--k-text-secondary)">👁 {{ $listing->views ?? 0 }} views</span>
                <span style="color:var(--k-text-muted)">•</span>
                <span style="font-size:13px;color:var(--k-text-secondary)">🕐 {{ $listing->created_at?->diffForHumans() }}</span>
            </div>
            <div class="k-product-price-row">
                <span class="k-price k-price-lg">{{ $price }}</span>
            </div>
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px">
                <div class="k-product-location">📍 {{ $location }}</div>
                <div class="k-product-stock instock"><span class="k-stock-dot"></span> In Stock</div>
            </div>

            <div class="k-product-specs">
                <div class="k-product-spec"><span class="spec-label">Condition</span><span class="spec-val">{{ $listing->condition ?? 'Used – Like New' }}</span></div>
                <div class="k-product-spec"><span class="spec-label">Category</span><span class="spec-val">{{ optional($listing->category)->name ?? 'General' }}</span></div>
                <div class="k-product-spec"><span class="spec-label">Type</span><span class="spec-val">{{ ucfirst($listing->type ?? 'Listing') }}</span></div>
                <div class="k-product-spec"><span class="spec-label">Ad Type</span><span class="spec-val">{{ ucfirst($listing->ad_type ?? 'Sale') }}</span></div>
            </div>

            <div class="k-product-actions">
                <a href="#" class="k-btn k-btn-primary k-btn-lg" style="justify-content:center">💬 Chat with Seller</a>
                <div class="k-product-actions-row">
                    <a href="tel:{{ $callNumber }}" class="k-btn k-btn-outline k-btn-lg" style="justify-content:center">📞 Call Now</a>
                    <a href="https://wa.me/{{ $whatsappNumber }}" class="k-btn k-btn-lg" style="background:#25D366;color:#fff;justify-content:center" target="_blank">WhatsApp</a>
                </div>
            </div>

            <div class="k-product-action-links">
                <button class="k-product-action-link">🤍 Save</button>
                <button class="k-product-action-link">↗ Share</button>
                <button class="k-product-action-link" style="color:var(--k-red)">⚑ Report Ad</button>
            </div>

            <div class="k-product-tabs mt-20">
                <button class="k-product-tab active">Description</button>
                <button class="k-product-tab">Specifications</button>
                <button class="k-product-tab">Reviews</button>
                <button class="k-product-tab">Location</button>
            </div>

            <div style="font-size:14px;color:var(--k-text-secondary);line-height:1.7;margin-bottom:16px">
                <p>{{ $listing->description ?? 'No description available.' }}</p>
            </div>

            <div style="display:flex;gap:6px;flex-wrap:wrap">
                <span style="background:var(--k-bg);border:1px solid var(--k-border);border-radius:20px;padding:3px 10px;font-size:12px">{{ optional($listing->category)->name ?? 'General' }}</span>
                <span style="background:var(--k-bg);border:1px solid var(--k-border);border-radius:20px;padding:3px 10px;font-size:12px">{{ ucfirst($listing->type ?? 'Listing') }}</span>
                <span style="background:var(--k-bg);border:1px solid var(--k-border);border-radius:20px;padding:3px 10px;font-size:12px">{{ $location }}</span>
            </div>
        </div>

        <!-- Right sidebar -->
        <div>
            <div class="k-seller-card mb-16">
                <div style="font-size:13px;font-weight:700;color:var(--k-text-secondary);margin-bottom:10px">Seller Information</div>
                <div class="k-seller-card-header">
                    <div class="k-seller-avatar">{{ strtoupper(substr($sellerName,0,1)) }}</div>
                    <div>
                        <div class="k-seller-name">{{ $sellerName }}</div>
                        @if(optional($listing->store)->slug)<div class="k-verified-badge">✓ Verified Store</div>@endif
                        <div class="k-stars" style="margin-top:3px">★★★★★ <span>(4.8 · 128 reviews)</span></div>
                    </div>
                </div>
                <div style="display:flex;gap:8px;margin-bottom:12px">
                    @if(optional($listing->store)->slug)
                        <a href="/store/{{ $listing->store->slug }}" class="k-btn k-btn-outline k-btn-sm w-full" style="justify-content:center">View Store</a>
                    @endif
                    <a href="#" class="k-btn k-btn-primary k-btn-sm w-full" style="justify-content:center">Follow Store</a>
                </div>
                <div class="k-seller-stat-grid">
                    <div class="k-seller-stat"><strong>{{ optional($listing->store)->created_at?->format('M Y') ?? '—' }}</strong><span>Joined</span></div>
                    <div class="k-seller-stat"><strong>98%</strong><span>Response Rate</span></div>
                    <div class="k-seller-stat"><strong>Few hrs</strong><span>Response Time</span></div>
                    <div class="k-seller-stat"><strong>{{ $location }}</strong><span>Location</span></div>
                </div>
                <div class="k-safety-card">
                    <div class="k-safety-title">🛡️ Safety Tips for Buyers</div>
                    <div class="k-safety-tip">Meet in a safe public place</div>
                    <div class="k-safety-tip">Check the item before you buy</div>
                    <div class="k-safety-tip">Pay after inspection</div>
                    <div class="k-safety-tip">Avoid advance payments</div>
                </div>
            </div>

            <div class="k-info-card">
                <h3>Location</h3>
                <div class="k-map-placeholder">🗺️</div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-top:10px">
                    <span style="font-size:13px;color:var(--k-text-secondary)">📍 {{ $location }}</span>
                </div>
            </div>
        </div>
    </div>

    @if(isset($related) && $related->count())
        <div class="k-section" style="margin-top:40px">
            <div class="k-section-header">
                <h2 class="k-section-title">Similar Products</h2>
                <a href="/listings" class="k-section-link">View all →</a>
            </div>
            <div class="k-grid-5">
                @foreach($related->take(6) as $item)
                    @include('frontend.listings.card',['listing'=>$item])
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": {!! json_encode($listing->title) !!},
    "description": {!! json_encode($seoDesc) !!},
    @if($mainImageUrl)
    "image": {!! json_encode($mainImageUrl) !!},
    @endif
    "offers": {
        "@type": "Offer",
        "priceCurrency": "LKR",
        "price": "{{ (float) ($listing->price ?? 0) }}",
        "availability": "https://schema.org/InStock",
        "url": {!! json_encode(url()->current()) !!}
    }
}
</script>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {"@type": "ListItem", "position": 1, "name": "Home", "item": {!! json_encode(url('/')) !!}},
        {"@type": "ListItem", "position": 2, "name": "All Ads", "item": {!! json_encode(url('/listings')) !!}},
        {"@type": "ListItem", "position": 3, "name": {!! json_encode($listing->title) !!}, "item": {!! json_encode(url()->current()) !!}}
    ]
}
</script>
@endpush
@endsection
