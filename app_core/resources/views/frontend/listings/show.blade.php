@extends('layouts.app')

@php
    $images = $listing->images ?? collect();
    $hasImages = $images->count() > 0;
    $mainImage = optional($images->first())->path ?? $listing->image ?? null;
    $mainImageUrl = $mainImage ? asset('storage/'.ltrim($mainImage,'/')) : null;
    $mainWebpUrl = $mainImage ? asset('storage/'.ltrim(\App\Helpers\ImageHelper::webpPath($mainImage),'/')) : null;
    $sellerName = optional($listing->store)->name ?? optional($listing->user)->name ?? 'Seller';
    $location = optional($listing->locationModel)->name ?? $listing->location ?? 'Kegalle';
    $price = ($listing->price ?? 0) > 0 ? 'LKR '.number_format($listing->price) : 'Contact Seller';
    $whatsappNumber = preg_replace('/[^0-9]/', '', optional($listing->store)->whatsapp ?? optional($listing->store)->phone ?? '94706930930');
    $callNumber = optional($listing->store)->phone ?? optional($listing->user)->phone ?? '+94766930930';
    $seoAction = match(strtolower($listing->ad_type ?? 'sale')) {
        'rent' => 'for Rent',
        'wanted' => 'Wanted',
        'free' => 'Free',
        'exchange' => 'for Exchange',
        default => 'for Sale',
    };
    $seoDesc = \Illuminate\Support\Str::limit(strip_tags($listing->description ?? ''), 155) ?: ($listing->title.' '.$seoAction.' in '.$location.' — '.$price.'. Browse on Kegalle Marketplace.');

    $reviews = $listing->reviews ?? collect();
    $avgRating = $reviews->count() ? round($reviews->avg('rating'), 1) : 0;
    $fieldValues = $listing->values ?? collect();
    $specs = $fieldValues->filter(fn($v) => $v->field && $v->value && !in_array($v->field->name, ['brand_id', 'model_id']));
    $brandFieldValue = $fieldValues->first(fn($v) => $v->field && $v->field->name === 'brand_id' && $v->value);
    $listingBrand = $brandFieldValue ? \App\Models\Brand::find($brandFieldValue->value) : null;
@endphp

@section('title', ($listing->title ?? 'Listing').' '.$seoAction.' in '.$location.' — '.$price.' · Kegalle Marketplace')
@section('meta_description', $seoDesc)
@if($mainImageUrl)
@section('og_image', $mainImageUrl)
@endif

@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $listing->title,
    'description' => \Illuminate\Support\Str::limit(strip_tags($listing->description ?? ''), 300),
    'image' => $mainImageUrl ?: asset('images/kegalle-placeholder.png'),
    'url' => url('/listings/'.$listing->slug),
    'category' => optional($listing->category)->name,
    'offers' => [
        '@type' => 'Offer',
        'price' => ($listing->price ?? 0) > 0 ? number_format($listing->price, 2, '.', '') : '0',
        'priceCurrency' => 'LKR',
        'availability' => 'https://schema.org/InStock',
        'itemCondition' => strtolower($listing->condition ?? '') === 'used' ? 'https://schema.org/UsedCondition' : 'https://schema.org/NewCondition',
        'seller' => [
            '@type' => 'Organization',
            'name' => $sellerName,
        ],
        'url' => url('/listings/'.$listing->slug),
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Listings', 'item' => url('/listings')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => optional($listing->category)->name ?? 'General', 'item' => url('/listings?categories[]='.optional($listing->category)->slug)],
        ['@type' => 'ListItem', 'position' => 4, 'name' => $listing->title],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')

<div class="container" style="padding-top:12px;padding-bottom:40px">
    <div class="k-breadcrumb">
        <a href="/">Home</a><span>›</span>
        <a href="/listings">All Ads</a><span>›</span>
        <a href="/listings?categories[]={{ optional($listing->category)->slug }}">{{ optional($listing->category)->name ?? 'General' }}</a><span>›</span>
        <span class="current">{{ $listing->title }}</span>
    </div>

    @if(isset($activeDeal) && $activeDeal)
    <div class="k-deal-banner">
        <div class="k-deal-banner-left">
            <div class="k-deal-banner-top">
                <span class="k-deal-banner-badge">DEAL</span>
                @if($activeDeal->is_flash)<span class="k-deal-banner-flash">Flash Deal</span>@endif
                <span class="k-deal-banner-pct">-{{ number_format($activeDeal->discount_percent, 0) }}%</span>
            </div>
            <div class="k-deal-banner-prices">
                <span class="k-deal-banner-sale">LKR {{ number_format($activeDeal->deal_price) }}</span>
                <span class="k-deal-banner-orig">LKR {{ number_format($activeDeal->original_price) }}</span>
            </div>
            <div class="k-deal-banner-save">You save <strong>LKR {{ number_format($activeDeal->original_price - $activeDeal->deal_price) }}</strong></div>
        </div>
        <div class="k-deal-banner-right">
            <div class="k-deal-banner-timer-label">Ends in</div>
            <div class="k-deal-banner-countdown" id="dealBannerCountdown" data-end="{{ $activeDeal->ends_at->toIso8601String() }}">
                <div class="k-deal-cd-box"><span id="dealDays">00</span><small>Days</small></div>
                <span class="k-deal-cd-sep">:</span>
                <div class="k-deal-cd-box"><span id="dealHrs">00</span><small>Hrs</small></div>
                <span class="k-deal-cd-sep">:</span>
                <div class="k-deal-cd-box"><span id="dealMins">00</span><small>Mins</small></div>
                <span class="k-deal-cd-sep">:</span>
                <div class="k-deal-cd-box"><span id="dealSecs">00</span><small>Secs</small></div>
            </div>
        </div>
    </div>
    @endif

    <div class="k-product-page-grid">
        <!-- Gallery -->
        <div class="k-product-gallery">
            <div class="k-product-main-img">
                @if($mainImageUrl)
                    <img src="{{ $mainImageUrl }}" alt="{{ $listing->title }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit">
                @else
                    <div style="font-size:100px">🛍</div>
                @endif
                @if($listing->is_featured)
                    <div class="k-product-main-img-badge"><span class="k-tag k-tag-featured">Featured</span></div>
                @endif
            </div>
            <div class="k-product-thumbs">
                @forelse($images->take(4) as $i => $image)
                    <div class="k-product-thumb {{ $i === 0 ? 'active' : '' }}"><img src="{{ asset('storage/'.ltrim($image->path,'/')) }}" alt="{{ $listing->title }}" style="width:100%;height:100%;object-fit:cover"></div>
                @empty
                    <div class="k-product-thumb active">🛍</div>
                @endforelse
            </div>

            <!-- Tabs -->
            <div class="k-product-tabs mt-20">
                <button class="k-product-tab active" data-tab="description">Description</button>
                <button class="k-product-tab" data-tab="specifications">Specifications</button>
                <button class="k-product-tab" data-tab="reviews">Reviews ({{ $reviews->count() }})</button>
                <button class="k-product-tab" data-tab="location">Location</button>
            </div>

            <!-- Tab: Description -->
            <div class="k-tab-panel active" id="tab-description">
                <div style="font-size:14px;color:var(--k-text-secondary);line-height:1.7;margin-bottom:16px">
                    <p>{{ $listing->description ?? 'No description available.' }}</p>
                </div>
                <div style="display:flex;gap:6px;flex-wrap:wrap">
                    <span class="k-pill-tag">{{ optional($listing->category)->name ?? 'General' }}</span>
                    <span class="k-pill-tag">{{ ucfirst($listing->type ?? 'Listing') }}</span>
                    <span class="k-pill-tag">{{ $location }}</span>
                </div>
            </div>

            <!-- Tab: Specifications -->
            <div class="k-tab-panel" id="tab-specifications">
                <div class="k-specs-grid">
                    <div class="k-spec-row"><span class="k-spec-label">Condition</span><span class="k-spec-value">{{ $listing->condition ?? 'Brand New' }}</span></div>
                    @if($listingBrand)
                    <div class="k-spec-row"><span class="k-spec-label">Brand</span><span class="k-spec-value"><a href="/brand/{{ $listingBrand->slug }}" class="k-brand-link">{{ $listingBrand->name }} →</a></span></div>
                    @endif
                    <div class="k-spec-row"><span class="k-spec-label">Category</span><span class="k-spec-value">{{ optional($listing->category)->name ?? 'General' }}</span></div>
                    <div class="k-spec-row"><span class="k-spec-label">Type</span><span class="k-spec-value">{{ ucfirst($listing->type ?? 'Listing') }}</span></div>
                    <div class="k-spec-row"><span class="k-spec-label">Ad Type</span><span class="k-spec-value">{{ ucfirst($listing->ad_type ?? 'Sale') }}</span></div>
                    @if($listing->price > 0)
                    <div class="k-spec-row"><span class="k-spec-label">Price</span><span class="k-spec-value">LKR {{ number_format($listing->price) }}</span></div>
                    @endif
                    <div class="k-spec-row"><span class="k-spec-label">Location</span><span class="k-spec-value">{{ $location }}</span></div>
                    <div class="k-spec-row"><span class="k-spec-label">Posted</span><span class="k-spec-value">{{ $listing->created_at?->format('M d, Y') }}</span></div>
                    @foreach($specs as $fv)
                        <div class="k-spec-row"><span class="k-spec-label">{{ $fv->field->label }}</span><span class="k-spec-value">{{ $fv->value }}</span></div>
                    @endforeach
                </div>
            </div>

            <!-- Tab: Reviews -->
            <div class="k-tab-panel" id="tab-reviews">
                <!-- Rating summary -->
                <div class="k-review-summary">
                    <div class="k-review-avg">
                        <span class="k-review-avg-num">{{ $avgRating ?: '—' }}</span>
                        <div class="k-review-avg-stars">
                            @for($s = 1; $s <= 5; $s++)
                                <span class="k-star {{ $s <= round($avgRating) ? 'filled' : '' }}">★</span>
                            @endfor
                        </div>
                        <span class="k-review-avg-count">{{ $reviews->count() }} {{ \Illuminate\Support\Str::plural('review', $reviews->count()) }}</span>
                    </div>
                    <div class="k-review-bars">
                        @for($r = 5; $r >= 1; $r--)
                            @php $cnt = $reviews->where('rating', $r)->count(); $pct = $reviews->count() ? round($cnt / $reviews->count() * 100) : 0; @endphp
                            <div class="k-review-bar-row">
                                <span class="k-review-bar-label">{{ $r }} ★</span>
                                <div class="k-review-bar"><div class="k-review-bar-fill" style="width:{{ $pct }}%"></div></div>
                                <span class="k-review-bar-count">{{ $cnt }}</span>
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- Review form -->
                @auth
                    @if(!$reviews->where('user_id', auth()->id())->count())
                    <div class="k-review-form-wrap">
                        <h4>Write a review</h4>
                        <form method="POST" action="/listings/{{ $listing->id }}/review" class="k-review-form">
                            @csrf
                            <div class="k-star-input" id="starInput">
                                <span class="k-star-pick" data-val="1">★</span>
                                <span class="k-star-pick" data-val="2">★</span>
                                <span class="k-star-pick" data-val="3">★</span>
                                <span class="k-star-pick" data-val="4">★</span>
                                <span class="k-star-pick" data-val="5">★</span>
                                <input type="hidden" name="rating" id="ratingInput" value="0" required>
                            </div>
                            <textarea name="comment" rows="3" placeholder="Share your experience with this product..." required minlength="5" maxlength="1000" class="k-review-textarea"></textarea>
                            <button type="submit" class="k-btn k-btn-primary">Submit Review</button>
                        </form>
                    </div>
                    @else
                    <p style="font-size:13px;color:var(--k-text-muted);margin-bottom:16px">You have already reviewed this listing.</p>
                    @endif
                @else
                    <p style="font-size:13px;color:var(--k-text-muted);margin-bottom:16px"><a href="/login" style="color:var(--k-primary);font-weight:600">Log in</a> to write a review.</p>
                @endauth

                <!-- Review list -->
                @forelse($reviews as $review)
                    <div class="k-review-item">
                        <div class="k-review-item-head">
                            <div class="k-review-avatar">{{ strtoupper(substr(optional($review->user)->name ?? 'U', 0, 1)) }}</div>
                            <div>
                                <div class="k-review-author">{{ optional($review->user)->name ?? 'User' }}</div>
                                <div class="k-review-date">{{ $review->created_at?->diffForHumans() }}</div>
                            </div>
                            <div class="k-review-stars-sm">
                                @for($s = 1; $s <= 5; $s++)
                                    <span class="k-star-sm {{ $s <= $review->rating ? 'filled' : '' }}">★</span>
                                @endfor
                            </div>
                        </div>
                        <p class="k-review-text">{{ $review->comment }}</p>
                    </div>
                @empty
                    <div class="k-review-empty">
                        <span style="font-size:36px;display:block;margin-bottom:8px">📝</span>
                        <p>No reviews yet. Be the first to share your experience!</p>
                    </div>
                @endforelse
            </div>

            <!-- Tab: Location -->
            <div class="k-tab-panel" id="tab-location">
                <div class="k-location-display">
                    <div class="k-location-map">
                        <iframe
                            width="100%" height="300" frameborder="0" style="border:0;border-radius:var(--k-radius)"
                            loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                            src="https://www.google.com/maps?q={{ urlencode($location . ', Sri Lanka') }}&output=embed">
                        </iframe>
                    </div>
                    <div class="k-location-info">
                        <div class="k-location-row">
                            <span class="k-location-icon">📍</span>
                            <div>
                                <strong>{{ $location }}</strong>
                                <span>Kegalle District, Sri Lanka</span>
                            </div>
                        </div>
                        @if(optional($listing->store)->name)
                        <div class="k-location-row">
                            <span class="k-location-icon">🏪</span>
                            <div>
                                <strong>{{ $listing->store->name }}</strong>
                                <span>Verified Store</span>
                            </div>
                        </div>
                        @endif
                        <div class="k-location-row">
                            <span class="k-location-icon">🕐</span>
                            <div>
                                <strong>Posted {{ $listing->created_at?->format('M d, Y') }}</strong>
                                <span>{{ $listing->created_at?->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info -->
        <div>
            <div class="k-product-tag-row">
                <span class="k-tag k-tag-primary">{{ $listing->is_featured ? 'Featured' : ucfirst($listing->type ?? 'Listing') }}</span>
                @if($reviews->count())
                    <span class="k-text-sm k-text-secondary">★ {{ $avgRating }} ({{ $reviews->count() }})</span>
                @endif
            </div>
            <h1 class="k-product-title">{{ $listing->title }}</h1>
            <div class="k-product-meta-row">
                <span class="k-text-sm k-text-secondary">{{ $listing->views ?? 0 }} views</span>
                <span class="k-text-muted">·</span>
                <span class="k-text-sm k-text-secondary">{{ $listing->created_at?->diffForHumans() }}</span>
            </div>
            <div class="k-product-price-row">
                @if(isset($activeDeal) && $activeDeal)
                    <span class="k-price k-price-lg k-text-red">LKR {{ number_format($activeDeal->deal_price) }}</span>
                    <span class="k-price-original">{{ $price }}</span>
                    <span class="k-discount-badge">-{{ number_format($activeDeal->discount_percent, 0) }}%</span>
                @else
                    <span class="k-price k-price-lg">{{ $price }}</span>
                @endif
            </div>
            <div class="k-product-location-row">
                <div class="k-product-location">📍 {{ $location }}</div>
                <div class="k-product-stock instock"><span class="k-stock-dot"></span> In Stock</div>
            </div>

            <div class="k-product-specs">
                <div class="k-product-spec"><span class="spec-label">Condition</span><span class="spec-val">{{ $listing->condition ?? 'Brand New' }}</span></div>
                @if($listingBrand)
                <div class="k-product-spec"><span class="spec-label">Brand</span><span class="spec-val"><a href="/brand/{{ $listingBrand->slug }}" class="k-brand-link">{{ $listingBrand->name }} →</a></span></div>
                @endif
                <div class="k-product-spec"><span class="spec-label">Category</span><span class="spec-val">{{ optional($listing->category)->name ?? 'General' }}</span></div>
                <div class="k-product-spec"><span class="spec-label">Type</span><span class="spec-val">{{ ucfirst($listing->type ?? 'Listing') }}</span></div>
                <div class="k-product-spec"><span class="spec-label">Ad Type</span><span class="spec-val">{{ ucfirst($listing->ad_type ?? 'Sale') }}</span></div>
            </div>

            @if($listing->variants->count())
            <div style="margin:14px 0">
                <div style="font-weight:700;font-size:13.5px;margin-bottom:8px">Select Size</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px">
                    @foreach($listing->variants as $variant)
                        <button type="button" class="k-size-option" data-price="{{ $variant->price ?? $listing->price ?? 0 }}"
                            style="min-width:52px;padding:9px 14px;border-radius:10px;border:1.5px solid #e5e8ef;background:#fff;font-size:13px;font-weight:700;cursor:pointer;transition:all .15s">
                            {{ $variant->name }}
                            @if($variant->price)<span style="display:block;font-size:10.5px;font-weight:600;color:#667085">LKR {{ number_format($variant->price) }}</span>@endif
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="k-product-actions">
                <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Hi, I\'m interested in: '.$listing->title.' — '.url('/listings/'.$listing->slug)) }}" class="k-btn k-btn-primary k-btn-lg k-btn-center" target="_blank">💬 Chat with Seller</a>
                @auth
                    @if(Auth::id() !== $listing->user_id)
                    <a href="{{ route('dashboard.chat', ['listing' => $listing->id, 'seller' => $listing->user_id]) }}" class="k-btn k-btn-outline k-btn-lg k-btn-center">💬 Send Message</a>
                    @endif
                @else
                    <a href="{{ route('login') }}?redirect={{ urlencode(route('dashboard.chat', ['listing' => $listing->id, 'seller' => $listing->user_id])) }}" class="k-btn k-btn-outline k-btn-lg k-btn-center">💬 Send Message</a>
                @endauth
                <div class="k-product-actions-row">
                    <a href="tel:{{ $callNumber }}" class="k-btn k-btn-outline k-btn-lg k-btn-center">📞 Call Now</a>
                    <a href="https://wa.me/{{ $whatsappNumber }}" class="k-btn k-btn-lg k-btn-whatsapp k-btn-center" target="_blank">WhatsApp</a>
                </div>
            </div>

            <div class="k-product-action-links">
                <button class="k-product-action-link" id="saveBtn" onclick="toggleSave()">🤍 Save</button>
                <button class="k-product-action-link" onclick="shareListing()">↗ Share</button>
                <a href="mailto:support@kurulla.com?subject={{ urlencode('Report: '.$listing->title) }}&body={{ urlencode('I would like to report this listing: '.url('/listings/'.$listing->slug)."\n\nReason: ") }}" class="k-product-action-link k-text-red">⚑ Report Ad</a>
            </div>
        </div>

        <!-- Right sidebar -->
        <div>
            <div class="k-seller-card mb-16">
                <div class="k-seller-card-label">Seller Information</div>
                <div class="k-seller-card-header">
                    <div class="k-seller-avatar">{{ strtoupper(substr($sellerName,0,1)) }}</div>
                    <div>
                        <div class="k-seller-name">{{ $sellerName }}</div>
                        @if(optional($listing->store)->slug)<div class="k-verified-badge">✓ Verified Store</div>@endif
                        @if(optional($listing->store)->id)
                        @php $storeRank = $listing->store->rank_label; $storeRankColor = $listing->store->rank_color; @endphp
                        <span style="display:inline-block;background:{{ $storeRankColor }};color:#fff;font-size:10px;padding:2px 8px;border-radius:10px;font-weight:700;margin-top:2px">{{ $storeRank }}</span>
                        @endif
                    </div>
                </div>
                <div class="k-seller-btn-row">
                    @if(optional($listing->store)->slug)
                        <a href="/store/{{ $listing->store->slug }}" class="k-btn k-btn-outline k-btn-sm w-full k-btn-center">View Store</a>
                    @endif
                </div>
                <div class="k-seller-stat-grid">
                    <div class="k-seller-stat"><strong>{{ optional($listing->store)->created_at?->format('M Y') ?? '—' }}</strong><span>Joined</span></div>
                    @if(optional($listing->store)->id)
                    @php $sellerAvg = $listing->store->average_rating; @endphp
                    <div class="k-seller-stat"><strong style="color:#f59e0b">{{ $sellerAvg ?: '—' }} ★</strong><span>Rating</span></div>
                    @endif
                    <div class="k-seller-stat"><strong>{{ $listing->store ? $listing->store->listings()->where('status','approved')->count() : ($listing->user ? $listing->user->listings()->where('status','approved')->count() : 1) }}</strong><span>Active Ads</span></div>
                    <div class="k-seller-stat"><strong>{{ $location }}</strong><span>Location</span></div>
                </div>
                <div class="k-safety-card">
                    <div class="k-safety-title">🛡 Safety Tips for Buyers</div>
                    <div class="k-safety-tip">Meet in a safe public place</div>
                    <div class="k-safety-tip">Check the item before you buy</div>
                    <div class="k-safety-tip">Pay after inspection</div>
                    <div class="k-safety-tip">Avoid advance payments</div>
                </div>
            </div>

            <div class="k-info-card">
                <h3>Location</h3>
                <div class="k-map-placeholder">🗺</div>
                <div class="k-map-footer">
                    <span class="k-text-sm k-text-secondary">📍 {{ $location }}</span>
                </div>
            </div>
        </div>
    </div>

    @if(isset($activeDeal) && $activeDeal && isset($similarDeals) && $similarDeals->count())
        <div class="k-section" class="mt-40">
            <div class="k-section-header">
                <h2 class="k-section-title">Similar Deals</h2>
                <a href="/deals" class="k-section-link">View all →</a>
            </div>
            <div class="k-carousel-wrap">
                @if($similarDeals->count() > 4)
                <button type="button" class="k-carousel-side-btn k-carousel-side-prev" data-carousel-prev="similar-carousel" aria-label="Previous">‹</button>
                @endif
                <div class="k-carousel" id="similar-carousel">
                    @foreach($similarDeals->take(6) as $sDeal)
                        @php $sListing = $sDeal->listing; @endphp
                        @if($sListing)
                        <div class="k-carousel-item">
                            <a class="k-deal-card-home" href="/listings/{{ $sListing->slug ?? '#' }}">
                                @php
                                    $sdImg = optional($sListing->images->first())->path ?? $sListing->image ?? null;
                                    $sdHas = (bool) $sdImg;
                                    $sdUrl = $sdHas ? asset('storage/'.ltrim($sdImg,'/')) : null;
                                    $sdWebp = $sdHas ? asset('storage/'.ltrim(\App\Helpers\ImageHelper::webpPath($sdImg),'/')) : null;
                                    $sdLoc = optional($sListing->locationModel)->name ?? $sListing->location ?? 'Kegalle';
                                @endphp
                                <div class="k-deal-card-img">
                                    @if($sdHas)
                                        <img loading="lazy" decoding="async" src="{{ $sdUrl }}" alt="{{ $sListing->title }}" onerror="this.closest('.k-deal-card-img').classList.add('k-img-failed');this.remove()">
                                    @else
                                        <div class="k-deal-card-ph">🛍</div>
                                    @endif
                                    <div class="k-deal-card-pct">-{{ number_format($sDeal->discount_percent, 0) }}%</div>
                                    @if($sDeal->is_flash)<div class="k-deal-card-flash">Flash</div>@endif
                                </div>
                                <div class="k-deal-card-body">
                                    <div class="k-deal-card-title">{{ $sListing->title }}</div>
                                    <div class="k-deal-card-loc">📍 {{ $sdLoc }}</div>
                                    <div class="k-deal-card-prices">
                                        <span class="k-deal-card-sale">LKR {{ number_format($sDeal->deal_price) }}</span>
                                        <span class="k-deal-card-orig">LKR {{ number_format($sDeal->original_price) }}</span>
                                    </div>
                                    <div class="k-deal-card-save">Save LKR {{ number_format($sDeal->original_price - $sDeal->deal_price) }}</div>
                                    <div class="k-deal-card-timer" data-deal-end="{{ $sDeal->ends_at->toIso8601String() }}">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                        <span class="k-deal-card-countdown">Loading...</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endif
                    @endforeach
                </div>
                @if($similarDeals->count() > 4)
                <button type="button" class="k-carousel-side-btn k-carousel-side-next" data-carousel-next="similar-carousel" aria-label="Next">›</button>
                @endif
            </div>
        </div>
    @elseif(isset($related) && $related->count())
        <div class="k-section" class="mt-40">
            <div class="k-section-header">
                <h2 class="k-section-title">Similar Products</h2>
                <a href="/listings" class="k-section-link">View all →</a>
            </div>
            <div class="k-carousel-wrap">
                @if($related->count() > 4)
                <button type="button" class="k-carousel-side-btn k-carousel-side-prev" data-carousel-prev="similar-carousel" aria-label="Previous">‹</button>
                @endif
                <div class="k-carousel" id="similar-carousel">
                    @foreach($related->take(6) as $item)
                        <div class="k-carousel-item">
                            @include('frontend.listings.card',['listing'=>$item])
                        </div>
                    @endforeach
                </div>
                @if($related->count() > 4)
                <button type="button" class="k-carousel-side-btn k-carousel-side-next" data-carousel-next="similar-carousel" aria-label="Next">›</button>
                @endif
            </div>
        </div>
    @endif

    @if(session('success'))
    <div class="k-flash k-flash-success" id="flashMsg">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="k-flash k-flash-error" id="flashMsg">{{ session('error') }}</div>
    @endif
</div>

{{-- Mobile sticky contact bar --}}
<div class="k-sticky-contactbar">
    <a href="tel:{{ $callNumber }}" class="k-scb-call">📞 Call</a>
    <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Hi, I\'m interested in: '.$listing->title.' — '.url('/listings/'.$listing->slug)) }}" class="k-scb-wa" target="_blank" rel="noopener">WhatsApp</a>
    <a href="https://wa.me/{{ $whatsappNumber }}" class="k-scb-chat" target="_blank" rel="noopener">💬 Chat</a>
</div>
<script>document.body.classList.add('k-has-contactbar');
// Size variant price switching
(function(){
    var opts = document.querySelectorAll('.k-size-option');
    if (!opts.length) return;
    var priceEl = document.querySelector('.k-price-lg');
    opts.forEach(function(btn){
        btn.addEventListener('click', function(){
            opts.forEach(function(b){ b.style.borderColor = '#e5e8ef'; b.style.background = '#fff'; });
            btn.style.borderColor = '#1B5E20';
            btn.style.background = '#E8F5E9';
            var p = parseFloat(btn.dataset.price || 0);
            if (priceEl && p > 0) priceEl.textContent = 'LKR ' + p.toLocaleString();
        });
    });
})();
</script>

@push('scripts')
<script>
(function(){
    var tabs = document.querySelectorAll('.k-product-tab[data-tab]');
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            tabs.forEach(function(t) { t.classList.remove('active'); });
            document.querySelectorAll('.k-tab-panel').forEach(function(p) { p.classList.remove('active'); });
            tab.classList.add('active');
            var panel = document.getElementById('tab-' + tab.dataset.tab);
            if (panel) panel.classList.add('active');
        });
    });

    var stars = document.querySelectorAll('.k-star-pick');
    stars.forEach(function(star) {
        star.addEventListener('click', function() {
            var val = parseInt(this.dataset.val);
            document.getElementById('ratingInput').value = val;
            stars.forEach(function(s) {
                s.classList.toggle('selected', parseInt(s.dataset.val) <= val);
            });
        });
        star.addEventListener('mouseenter', function() {
            var val = parseInt(this.dataset.val);
            stars.forEach(function(s) {
                s.classList.toggle('hover', parseInt(s.dataset.val) <= val);
            });
        });
        star.addEventListener('mouseleave', function() {
            stars.forEach(function(s) { s.classList.remove('hover'); });
        });
    });

    var flash = document.getElementById('flashMsg');
    if (flash) setTimeout(function() { flash.style.opacity = '0'; setTimeout(function() { flash.remove(); }, 300); }, 4000);

    var listingId = '{{ $listing->id }}';
    var saved = JSON.parse(localStorage.getItem('k_saved') || '[]');
    if (saved.indexOf(listingId) !== -1) {
        var btn = document.getElementById('saveBtn');
        if (btn) { btn.textContent = '❤ Saved'; btn.classList.add('active'); }
    }

    window.toggleSave = function() {
        var saved = JSON.parse(localStorage.getItem('k_saved') || '[]');
        var btn = document.getElementById('saveBtn');
        var idx = saved.indexOf(listingId);
        if (idx === -1) {
            saved.push(listingId);
            btn.textContent = '❤ Saved';
            btn.classList.add('active');
        } else {
            saved.splice(idx, 1);
            btn.textContent = '🤍 Save';
            btn.classList.remove('active');
        }
        localStorage.setItem('k_saved', JSON.stringify(saved));
    };

    window.shareListing = function() {
        var data = { title: @json($listing->title), url: window.location.href };
        if (navigator.share) {
            navigator.share(data).catch(function(){});
        } else {
            navigator.clipboard.writeText(window.location.href).then(function() {
                var btn = document.querySelector('.k-product-action-link:nth-child(2)');
                var orig = btn.textContent;
                btn.textContent = '✓ Link Copied!';
                setTimeout(function() { btn.textContent = orig; }, 2000);
            });
        }
    };
})();
</script>
@endpush

@push('scripts')
<script>
(function(){
    var item = {
        id: {{ $listing->id }},
        title: {!! json_encode(\Illuminate\Support\Str::limit($listing->title, 40), JSON_HEX_TAG) !!},
        slug: {!! json_encode($listing->slug, JSON_HEX_TAG) !!},
        price: {!! json_encode($price, JSON_HEX_TAG) !!},
        image: {!! json_encode($mainImageUrl ?? '', JSON_HEX_TAG) !!},
        ts: Date.now()
    };
    var key = 'k_recently_viewed';
    var list = [];
    try { list = JSON.parse(localStorage.getItem(key) || '[]'); } catch(e) {}
    list = list.filter(function(x) { return x.id !== item.id; });
    list.unshift(item);
    if (list.length > 12) list = list.slice(0, 12);
    localStorage.setItem(key, JSON.stringify(list));
})();
</script>
@endpush

@if(isset($activeDeal) && $activeDeal)
@push('scripts')
<script>
(function(){
    var el = document.getElementById('dealBannerCountdown');
    if(!el) return;
    var end = new Date(el.dataset.end);
    function pad(n){return n<10?'0'+n:n}
    function tick(){
        var now=new Date(), d=Math.max(0, end-now);
        var days=Math.floor(d/864e5), h=Math.floor(d%864e5/36e5), m=Math.floor(d%36e5/6e4), s=Math.floor(d%6e4/1e3);
        document.getElementById('dealDays').textContent=pad(days);
        document.getElementById('dealHrs').textContent=pad(h);
        document.getElementById('dealMins').textContent=pad(m);
        document.getElementById('dealSecs').textContent=pad(s);
        if(d<=0) clearInterval(iv);
    }
    tick(); var iv=setInterval(tick,1000);
})();
</script>
@endpush
@endif
@endsection
