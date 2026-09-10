@extends('layouts.app')

@php
    $images = $listing->images ?? collect();
    $hasImages = $images->count() > 0;
    $mainImage = optional($images->first())->path ?? $listing->image ?? null;
    $mainImageUrl = $mainImage ? asset('storage/'.ltrim($mainImage,'/')) : null;
    $mainWebpUrl = $mainImage ? asset('storage/'.ltrim(\App\Helpers\ImageHelper::webpPath($mainImage),'/')) : null;
    $sellerName = optional($listing->store)->name ?? optional($listing->user)->name ?? $listing->poster_name ?? 'Seller';
    $location = optional($listing->locationModel)->name ?? $listing->location ?? 'Kegalle';
    $priceLabel = $listing->values->first(fn($v)=>optional($v->field)->name==='price_label')?->value ?? null;
    $priceLabelMap = ['fixed'=>'Fixed Price','negotiable'=>'Negotiable','free'=>'Free','per_month'=>'Per Month','per_year'=>'Per Year'];
    if($priceLabel === 'free') {
        $price = 'Free';
    } elseif(($listing->price ?? 0) > 0) {
        $price = 'LKR '.number_format($listing->price).($priceLabel==='per_month'?' / mo':($priceLabel==='per_year'?' / yr':''));
    } else {
        $price = 'Contact Seller';
    }
    $whatsappNumber = preg_replace('/[^0-9]/', '', optional($listing->store)->whatsapp ?? optional($listing->store)->phone ?? optional($listing->user)->phone ?? $listing->poster_phone ?? '');
    $callNumber = optional($listing->store)->phone ?? optional($listing->user)->phone ?? $listing->poster_phone ?? '';
    $callNumberFormatted = \App\Helpers\PhoneHelper::format($callNumber);
    $callNumberTel = \App\Helpers\PhoneHelper::tel($callNumber);
    $callNumberB64 = base64_encode($callNumberTel);
    $waNumberB64   = base64_encode($whatsappNumber);
    $waMsgB64      = base64_encode('Hi, I\'m interested in: '.$listing->title.' — '.url('/listings/'.$listing->slug));
    $seoAction = match(strtolower($listing->ad_type ?? 'sale')) {
        'rent' => 'for Rent',
        'wanted' => 'Wanted',
        'free' => 'Free',
        'exchange' => 'for Exchange',
        default => 'for Sale',
    };
    $rawDesc = \Illuminate\Support\Str::limit(strip_tags($listing->description ?? ''), 155);
    $seoDesc = mb_strlen($rawDesc) >= 50
        ? $rawDesc
        : ($listing->title.' '.$seoAction.' in '.$location.' — '.$price.'. Find this and more local listings on Kegalle Marketplace, Sri Lanka\'s Kegalle district marketplace.');

    $reviews = $listing->reviews ?? collect();
    $avgRating = $reviews->count() ? round($reviews->avg('rating'), 1) : 0;
    $fieldValues = $listing->values ?? collect();
    $specs = $fieldValues->filter(fn($v) => $v->field && $v->value && !in_array($v->field->name, ['brand_id', 'model_id']));
    // $listingBrand is passed from the controller (avoids N+1 DB call in view)
@endphp

@section('og_type', 'product')
@section('title', ($listing->title ?? 'Listing').' '.$seoAction.' in '.$location.' — '.$price.' · Kegalle Marketplace')
@section('meta_description', $seoDesc)
@if($mainImageUrl)
@section('og_image', $mainImageUrl)
@endif

@push('styles')
<link rel="stylesheet" href="{{ asset('css/kegalle-listing-detail.css') }}?v=3">
{{-- KEEP: no inline style block --}}
@endpush



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
        'availability' => $listing->status === 'sold' ? 'https://schema.org/OutOfStock' : 'https://schema.org/InStock',
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
        ['@type' => 'ListItem', 'position' => 3, 'name' => optional($listing->category)->name ?? 'General', 'item' => url('/listings/'.( optional($listing->category)->slug ?? 'all' ))],
        ['@type' => 'ListItem', 'position' => 4, 'name' => $listing->title],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')

<div class="container k-show-wrap">
    <div class="k-breadcrumb">
        <a href="/">Home</a><span>›</span>
        <a href="/listings">All Ads</a><span>›</span>
        <a href="/listings?categories[]={{ optional($listing->category)->slug }}">{{ optional($listing->category)->name ?? 'General' }}</a><span>›</span>
        <span class="current">{{ $listing->title }}</span>
        <span class="k-back-link-cell">
            <a id="kBackToResults" href="/listings?categories[]={{ optional($listing->category)->slug }}" class="k-back-link">← Back to results</a>
        </span>
    </div>
    <script nonce="{{ $cspNonce ?? '' }}">
    (function(){
        var back = document.getElementById('kBackToResults');
        if(!back) return;
        var ref = document.referrer;
        if(ref && ref.indexOf(location.hostname) !== -1 && (ref.indexOf('/listings') !== -1 || ref.indexOf('/classified') !== -1 || ref.indexOf('/deals') !== -1 || ref.indexOf('/search') !== -1)){
            back.href = ref;
        }
    })();
    </script>

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
            <div class="k-product-main-img k-gallery-zoomable" id="kMainImgWrap" title="Click to view full size" role="button" tabindex="0" aria-label="View full-size image" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click()}">
                @if($mainImageUrl)
                    <picture>
                        <source type="image/webp" srcset="{{ $mainWebpUrl }}">
                        <img id="kMainImg" src="{{ $mainImageUrl }}" alt="{{ $listing->title }}" class="k-main-img"
                             onerror="this.style.display='none';document.getElementById('kImgPlaceholder').style.display='flex'">
                    </picture>
                    <div id="kImgPlaceholder" class="k-img-placeholder-large" style="display:none">
                        <span class="k-img-ph-icon">🛍</span>
                        <span class="k-img-ph-text">No Image Available</span>
                    </div>
                    <span class="k-gallery-zoom-hint" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                        Zoom
                    </span>
                @else
                    <div class="k-img-placeholder-large">
                        <span class="k-img-ph-icon">🛍</span>
                        <span class="k-img-ph-text">No Image Available</span>
                    </div>
                @endif
                @if($listing->status === 'sold')
                    <div class="k-product-main-img-badge"><span class="k-tag k-sold-tag">SOLD</span></div>
                @elseif($listing->is_featured)
                    <div class="k-product-main-img-badge"><span class="k-tag k-tag-featured">Featured</span></div>
                @endif
                @if($images->count() > 1)
                    <div class="k-main-img-counter" id="kImgCounter">1 / {{ $images->count() }}</div>
                @endif
            </div>
            <div class="k-product-thumbs-wrap">
                <div class="k-thumb-arrow k-thumb-arrow-prev hidden" id="kThumbPrev">&#8249;</div>
                <div class="k-thumb-arrow k-thumb-arrow-next {{ $images->count() <= 5 ? 'hidden' : '' }}" id="kThumbNext">&#8250;</div>
                <div class="k-product-thumbs" id="kThumbStrip"><div id="kThumbInner" class="k-thumb-strip-inner">
                    @forelse($images as $i => $image)
                        @php $thumbUrl = asset('storage/'.ltrim($image->path,'/')); @endphp
                        <div class="k-product-thumb {{ $i === 0 ? 'active' : '' }}" data-img="{{ $thumbUrl }}" data-idx="{{ $i }}">
                            <img src="{{ $thumbUrl }}" alt="{{ $listing->title }}" loading="lazy">
                        </div>
                    @empty
                        <div class="k-product-thumb active">🛍</div>
                    @endforelse
                    </div>{{-- kThumbInner --}}
                </div>
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
                @php
                    $hasEn = !empty(trim($listing->description ?? ''));
                    $hasSi = !empty(trim($listing->description_si ?? ''));
                    $hasTa = !empty(trim($listing->description_ta ?? ''));
                    $langCount = ($hasEn?1:0) + ($hasSi?1:0) + ($hasTa?1:0);
                @endphp
                @if($hasEn || $hasSi || $hasTa)
                    @if($langCount > 1)
                    <div class="klp-lang-switcher">
                        @if($hasEn)<button class="klp-lang-btn active" data-lang="en">🇬🇧 English</button>@endif
                        @if($hasSi)<button class="klp-lang-btn{{ !$hasEn ? ' active' : '' }}" data-lang="si">🇱🇰 සිංහල</button>@endif
                        @if($hasTa)<button class="klp-lang-btn{{ (!$hasEn && !$hasSi) ? ' active' : '' }}" data-lang="ta">🇱🇰 தமிழ்</button>@endif
                    </div>
                    @endif
                    @if($hasEn)
                    <div class="klp-lang-content" id="klp-lang-en">
                        <div class="listing-description">{!! \App\Helpers\HtmlSanitizer::clean($listing->description) !!}</div>
                    </div>
                    @endif
                    @if($hasSi)
                    <div class="klp-lang-content{{ ($langCount > 1 && $hasEn) ? ' klp-lang-hidden' : '' }}" id="klp-lang-si">
                        <div class="listing-description">{!! \App\Helpers\HtmlSanitizer::clean($listing->description_si) !!}</div>
                    </div>
                    @endif
                    @if($hasTa)
                    <div class="klp-lang-content{{ ($langCount > 1 && ($hasEn || $hasSi)) ? ' klp-lang-hidden' : '' }}" id="klp-lang-ta">
                        <div class="listing-description">{!! \App\Helpers\HtmlSanitizer::clean($listing->description_ta) !!}</div>
                    </div>
                    @endif
                @else
                    <div class="k-no-desc-box">
                        <span class="k-no-desc-icon">📝</span>
                        <span>The seller hasn't added a description yet. Use <strong class="k-no-desc-cta">Chat with Seller</strong> to ask for more details.</span>
                    </div>
                @endif
                <div class="k-tags-row">
                    <span class="k-pill-tag">{{ optional($listing->category)->name ?? 'General' }}</span>
                    <span class="k-pill-tag">{{ ucfirst($listing->type ?? 'Listing') }}</span>
                    <span class="k-pill-tag">📍 {{ $location }}</span>
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
                    <div class="k-spec-row"><span class="k-spec-label">Ad Type</span><span class="k-spec-value">{{ ucfirst($listing->ad_type ?? 'Sale') }}</span></div>
                    @if(($listing->price ?? 0) > 0)
                    <div class="k-spec-row"><span class="k-spec-label">Price</span><span class="k-spec-value">LKR {{ number_format($listing->price) }}</span></div>
                    @endif
                    <div class="k-spec-row"><span class="k-spec-label">Location</span><span class="k-spec-value">{{ $location }}</span></div>
                    <div class="k-spec-row"><span class="k-spec-label">Posted</span><span class="k-spec-value">{{ $listing->created_at?->format('M d, Y') }}</span></div>
                    @foreach($specs as $fv)
                    @php
                        $fType  = $fv->field->type ?? 'text';
                        $fUnit  = $fv->field->unit ?? '';
                        $fMulti = (bool)($fv->field->multi ?? false);
                        $fVal   = $fv->value ?? '';
                    @endphp
                    <div class="k-spec-row{{ in_array($fType,['checkbox_group','pill_group']) ? ' k-spec-row-wide' : '' }}">
                        <span class="k-spec-label">{{ $fv->field->label }}</span>
                        <span class="k-spec-value">
                            @if($fType === 'checkbox_group' || ($fType === 'pill_group' && $fMulti))
                                {{-- Comma-separated list → badge chips --}}
                                @foreach(array_filter(array_map('trim', explode(',', $fVal))) as $chip)
                                    <span class="k-spec-chip">{{ $chip }}</span>
                                @endforeach
                            @elseif($fType === 'pill_group')
                                <span class="k-spec-chip k-spec-chip-single">{{ $fVal }}</span>
                            @elseif($fType === 'text_unit' && $fUnit)
                                {{ $fVal }} <span class="k-spec-unit">{{ $fUnit }}</span>
                            @else
                                {{ $fVal }}
                            @endif
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Tab: Reviews -->
            <div class="k-tab-panel" id="tab-reviews">
                <!-- Rating summary — only shown when there are reviews -->
                @if($reviews->count() > 0)
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
                @endif

                <!-- Review form -->
                @auth
                    @if(!$reviews->where('user_id', auth()->id())->count())
                    <div class="k-review-form-wrap">
                        <h4>Write a review</h4>
                        <form method="POST" action="/listings/{{ $listing->id }}/review" class="k-review-form" onsubmit="if(parseInt(document.getElementById('ratingInput').value)<1){var e=document.getElementById('star-err');if(!e){e=document.createElement('p');e.id='star-err';e.style.cssText='color:#ef4444;font-size:12px;margin:4px 0 0';e.textContent='Please select a star rating.';document.getElementById('starInput').after(e);}return false;}var r=document.getElementById('star-err');if(r)r.remove();">
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
                    <p class="k-review-note">You have already reviewed this listing.</p>
                    @endif
                @else
                    <p class="k-review-note"><a href="/login" class="k-review-login-link">Log in</a> to write a review.</p>
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
                        <span class="k-review-empty-icon">📝</span>
                        <p>No reviews yet. Be the first to share your experience!</p>
                    </div>
                @endforelse
            </div>

            <!-- Tab: Location -->
            <div class="k-tab-panel" id="tab-location">
                <div class="k-location-display">
                    <div class="k-location-map">
                        <iframe id="k-map-frame"
                            width="100%" height="300" frameborder="0" class="k-map-frame"
                            referrerpolicy="no-referrer-when-downgrade"
                            data-src="https://www.google.com/maps?q={{ urlencode($location . ', Sri Lanka') }}&output=embed"
                            src="">
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
                @if(($listing->views ?? 0) > 0)
                    <span class="k-text-sm k-text-secondary">{{ number_format($listing->views) }} views</span>
                    <span class="k-text-muted">·</span>
                @else
                    <span class="k-badge-new">New</span>
                    <span class="k-text-muted">·</span>
                @endif
                <span class="k-text-sm k-text-secondary">{{ $listing->created_at?->diffForHumans() }}</span>
            </div>
            <div class="k-product-price-row">
                @if(isset($activeDeal) && $activeDeal)
                    <span class="k-price k-price-lg k-text-red">LKR {{ number_format($activeDeal->deal_price) }}</span>
                    <span class="k-price-original">{{ $price }}</span>
                    <span class="k-discount-badge">-{{ number_format($activeDeal->discount_percent, 0) }}%</span>
                @else
                    <span class="k-price k-price-lg{{ $priceLabel==='free' ? ' k-price-free' : '' }}">{{ $price }}</span>
                @endif
                @if($priceLabel && $priceLabel !== 'fixed')
                    <span class="k-price-type-badge k-price-type-{{ $priceLabel }}">{{ $priceLabelMap[$priceLabel] ?? $priceLabel }}</span>
                @endif
                @auth
                    @if(($listing->price ?? 0) > 0)
                    <button type="button" class="k-price-alert-btn{{ isset($hasPriceAlert) && $hasPriceAlert ? ' active' : '' }}"
                        id="priceAlertBtn"
                        data-listing="{{ $listing->id }}"
                        data-url="{{ route('listings.price-alert', $listing->id) }}"
                        data-token="{{ csrf_token() }}"
                        class="k-price-alert-btn"
                        title="{{ isset($hasPriceAlert) && $hasPriceAlert ? 'Cancel price drop alert' : 'Alert me if price drops' }}">
                        🔔 {{ isset($hasPriceAlert) && $hasPriceAlert ? 'Alert set' : 'Alert me' }}
                    </button>
                    @endif
                @endauth
                @guest
                    @if(($listing->price ?? 0) > 0)
                    <a href="{{ route('login') }}" class="k-price-alert-btn" title="Sign in to get price drop alerts">🔔 Alert me</a>
                    @endif
                @endguest
            </div>
            <div class="k-product-location-row">
                <div class="k-product-location">📍 {{ $location }}</div>
                @if(!in_array($listing->type ?? 'product', ['classified','wanted','free','exchange']))
                <div class="k-product-stock instock"><span class="k-stock-dot"></span> In Stock</div>
                @endif
            </div>

            <div class="k-product-specs">
                <div class="k-product-spec"><span class="spec-label">Condition</span><span class="spec-val">{{ $listing->condition ?? 'Brand New' }}</span></div>
                @if($listingBrand)
                <div class="k-product-spec"><span class="spec-label">Brand</span><span class="spec-val"><a href="/brand/{{ $listingBrand->slug }}" class="k-brand-link">{{ $listingBrand->name }} →</a></span></div>
                @endif
                <div class="k-product-spec"><span class="spec-label">Category</span><span class="spec-val">{{ optional($listing->category)->name ?? 'General' }}</span></div>
                <div class="k-product-spec"><span class="spec-label">Type</span><span class="spec-val">{{ ucfirst($listing->type ?? 'Listing') }}</span></div>
                <div class="k-product-spec"><span class="spec-label">Ad Type</span><span class="spec-val">{{ ucfirst($listing->ad_type ?? 'Sale') }}</span></div>
                @foreach($specs as $fv)
                <div class="k-product-spec"><span class="spec-label">{{ $fv->field->label }}</span><span class="spec-val">{{ $fv->value }}</span></div>
                @endforeach
            </div>

            @if($listing->variants->count())
            <div class="k-size-wrap">
                <div class="k-size-label">Select Size</div>
                <div class="k-size-grid">
                    @foreach($listing->variants as $variant)
                        <button type="button" class="k-size-btn k-size-option" data-price="{{ $variant->price ?? $listing->price ?? 0 }}">
                            {{ $variant->name }}
                            @if($variant->price)<span class="k-size-price">LKR {{ number_format($variant->price) }}</span>@endif
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

            @php
                $payMethods = $listing->payment_methods
                    ?? optional($listing->store)->payment_methods
                    ?? ['cash_on_pickup', 'cod'];
                $allPayMethods = \App\Models\Offer::PAYMENT_METHODS;
            @endphp

            {{-- Payment method badges --}}
            @if(!empty($payMethods))
            <div class="k-pay-methods" aria-label="Accepted payment methods">
                <span class="k-pay-methods-label">Accepted payment:</span>
                @foreach($payMethods as $pm)
                    @if(isset($allPayMethods[$pm]))
                    <span class="k-pay-badge" title="{{ $allPayMethods[$pm]['desc'] }}">
                        {{ $allPayMethods[$pm]['icon'] }} {{ $allPayMethods[$pm]['label'] }}
                    </span>
                    @endif
                @endforeach
            </div>
            @endif

            @php
                $sellerStore   = $listing->store ?? null;
                $sellerAdsCount = $sellerStore
                    ? ($sellerStore->active_listings_count ?? 0)
                    : ($listing->user?->active_listings_count ?? 0);
                $sellerRating  = $sellerStore
                    ? ($sellerStore->average_rating ?? ($sellerStore->approvedReviews->avg('rating') ?? 0))
                    : ($avgRating ?? 0);
                $sellerRatingDisplay = $sellerRating > 0 ? number_format($sellerRating, 1).'★' : '—';
                $sellerSince   = $sellerStore
                    ? optional($sellerStore->created_at)->format('M Y')
                    : optional($listing->user?->created_at)->format('M Y');
            @endphp
            <div class="k-seller-stats">
                <a href="{{ $sellerStore ? '/stores/'.($sellerStore->slug ?? '#') : '#' }}" class="k-seller-stats-name">
                    @if($sellerStore)
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l1-6h16l1 6"/><path d="M3 9a2 2 0 0 0 2 2 2 2 0 0 0 2-2 2 2 0 0 0 2 2 2 2 0 0 0 2-2 2 2 0 0 0 2 2 2 2 0 0 0 2-2"/><path d="M5 22V11M19 22V11M9 22v-6h6v6"/></svg>
                        {{ $sellerStore->name }}
                        @if($sellerStore->is_verified ?? false)<span class="k-seller-stats-verified">✓ Verified</span>@endif
                    @else
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        {{ $sellerName }}
                    @endif
                </a>
                <div class="k-seller-stats-row">
                    <div class="k-seller-stat-item">
                        <strong>{{ $sellerAdsCount > 0 ? $sellerAdsCount.'+' : 'New' }}</strong>
                        <span>Total Ads</span>
                    </div>
                    <div class="k-seller-stat-item">
                        <strong>{{ $sellerRatingDisplay }}</strong>
                        <span>Rating</span>
                    </div>
                    <div class="k-seller-stat-item">
                        <strong>{{ $sellerSince ?? '—' }}</strong>
                        <span>Member Since</span>
                    </div>
                </div>
            </div>
            <div class="k-product-actions">
                @auth
                    @if(Auth::id() === $listing->user_id)
                    {{-- Seller viewing their own listing --}}
                    <div class="k-owner-notice">
                        <span class="k-owner-notice-icon">✏️</span>
                        <span>This is <strong>your listing</strong></span>
                    </div>
                    <div class="k-contact-row">
                        <a href="/dashboard/listings/{{ $listing->id }}/edit" class="k-btn k-btn-primary k-btn-lg k-btn-center">✏️ Edit Listing</a>
                        <a href="/dashboard/listings" class="k-btn k-btn-outline k-btn-lg k-btn-center">📋 My Listings</a>
                    </div>
                    @else
                    {{-- Buyer: platform chat + make offer --}}
                    @if($listing->status === 'sold')
                    <div class="k-sold-box">
                        <span class="k-sold-box-icon">🏷️</span>
                        <p class="k-sold-box-title">This item has been sold</p>
                        <p class="k-sold-box-note">The seller is no longer accepting offers</p>
                    </div>
                    @else
                    {{-- Row 1: Chat + Make Offer (only when listing has an account) --}}
                    @if($listing->user_id)
                    <div class="k-contact-row">
                        <a href="{{ route('dashboard.chat', ['listing' => $listing->id, 'seller' => $listing->user_id]) }}" class="k-btn k-btn-primary k-btn-lg k-btn-center">💬 Chat with Seller</a>
                        <button type="button" id="kOfferOpenBtn" class="k-btn k-btn-outline k-btn-lg k-btn-center" aria-haspopup="dialog">🤝 Make Offer</button>
                    </div>
                    @endif
                    {{-- Row 2: WhatsApp + Call (click-to-reveal) --}}
                    <div class="k-contact-row k-contact-row-secondary">
                        <button type="button" class="k-btn k-btn-whatsapp k-btn-lg k-btn-center k-reveal-wa" data-wa="{{ $waNumberB64 }}" data-msg="{{ $waMsgB64 }}">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="k-wa-icon"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>WhatsApp
                        </button>
                        <button type="button" class="k-btn k-btn-call k-btn-lg k-btn-center k-reveal-call" data-tel="{{ $callNumberB64 }}" data-fmt="{{ $callNumberFormatted }}">📞 Tap to Call</button>
                    </div>
                    @endif
                    @endif
                @else
                    {{-- Guest --}}
                    @if($listing->status === 'sold')
                    <div class="k-sold-box">
                        <span class="k-sold-box-icon">🏷️</span>
                        <p class="k-sold-box-title">This item has been sold</p>
                    </div>
                    @else
                    {{-- Row 1: Guest enquiry + login --}}
                    <div class="k-contact-row">
                        <button type="button" id="kGuestEnquiryOpenBtn" class="k-btn k-btn-primary k-btn-lg k-btn-center" aria-haspopup="dialog">💬 Send Enquiry</button>
                        <a href="{{ route('login') }}?redirect={{ urlencode(request()->url()) }}" class="k-btn k-btn-outline k-btn-lg k-btn-center">🔑 Login</a>
                    </div>
                    {{-- Row 2: WhatsApp + Call (public for store listings, click-to-reveal) --}}
                    @if($store && $store->id)
                    <div class="k-contact-row k-contact-row-secondary">
                        <button type="button" class="k-btn k-btn-whatsapp k-btn-lg k-btn-center k-reveal-wa" data-wa="{{ $waNumberB64 }}" data-msg="{{ $waMsgB64 }}">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="k-wa-icon"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>WhatsApp
                        </button>
                        <button type="button" class="k-btn k-btn-call k-btn-lg k-btn-center k-reveal-call" data-tel="{{ $callNumberB64 }}" data-fmt="{{ $callNumberFormatted }}">📞 Tap to Call</button>
                    </div>
                    @else
                    <a href="{{ route('login') }}?redirect={{ urlencode(request()->url()) }}" class="k-btn k-btn-outline k-btn-sm k-btn-center k-mt-4">🔒 Login to see contact details</a>
                    @endif
                    @endif
                @endauth
            </div>


            @auth
            @if(Auth::id() !== $listing->user_id)
            {{-- Make Offer modal --}}
            <div id="kOfferModal" class="k-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="kOfferModalTitle">
                <div class="k-modal-panel k-modal-panel-sm">
                    <button id="kOfferCloseBtn" class="k-modal-close" aria-label="Close offer modal">×</button>
                    <h2 id="kOfferModalTitle" class="k-modal-title">🤝 Make an Offer</h2>
                    <p class="k-modal-sub">{{ $listing->title }}</p>
                    @if(($listing->price ?? 0) > 0)
                    <div class="k-mf-asking-price">Asking price: <strong>LKR {{ number_format($listing->price) }}</strong></div>
                    @endif
                    <form method="POST" action="{{ route('offers.store', $listing) }}">
                        @csrf
                        <label class="k-mf-label">
                            <span class="k-mf-caption">Your offer price (LKR)</span>
                            <input type="number" name="offered_price" value="{{ $listing->price }}" min="0" step="0.01"
                                   class="k-mf-input" aria-label="Offer price in LKR">
                        </label>
                        <fieldset class="k-mf-fieldset">
                            <legend class="k-mf-legend">Payment Method</legend>
                            @foreach($allPayMethods as $key => $pm)
                            <label class="k-mf-radio-row">
                                <input type="radio" name="payment_method" value="{{ $key }}"
                                    {{ in_array($key, $payMethods ?? []) ? 'checked' : ($loop->first ? 'checked' : '') }}
                                    class="k-mf-radio">
                                <span>{{ $pm['icon'] }} {{ $pm['label'] }} <small class="k-mf-radio-desc">{{ $pm['desc'] }}</small></span>
                            </label>
                            @endforeach
                        </fieldset>
                        <label class="k-mf-label-lg">
                            <span class="k-mf-caption">Message (optional)</span>
                            <textarea name="message" rows="2" maxlength="500" placeholder="Any questions or special requests?" class="k-mf-textarea"></textarea>
                        </label>
                        <button type="submit" class="k-btn k-btn-primary k-mf-btn-full">Send Offer</button>
                    </form>
                </div>
            </div>
            @endif
            @endauth

            {{-- Report Ad modal (visible to all) --}}
            <div id="kReportModal" class="k-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="kReportModalTitle">
                <div class="k-modal-panel k-modal-panel-xs">
                    <button id="kReportCloseBtn" class="k-modal-close" aria-label="Close">×</button>
                    <h2 id="kReportModalTitle" class="k-modal-title">⚑ Report This Ad</h2>
                    <p class="k-modal-sub">Help us keep Kegalle Marketplace safe. Our team will review your report within 24 hours.</p>
                    <form id="kReportForm" onsubmit="submitReport(event)">
                        @csrf
                        <label class="k-mf-label">
                            <span class="k-mf-caption-md">Reason for reporting</span>
                            <select name="reason" required class="k-mf-select">
                                <option value="">Select a reason…</option>
                                <option value="scam">Looks like a scam or fraud</option>
                                <option value="duplicate">Duplicate or spam listing</option>
                                <option value="wrong_category">Wrong category</option>
                                <option value="inappropriate">Inappropriate or offensive content</option>
                                <option value="sold">Item already sold</option>
                                <option value="other">Other</option>
                            </select>
                        </label>
                        <label class="k-mf-label-lg">
                            <span class="k-mf-caption">Details (optional)</span>
                            <textarea name="details" rows="3" maxlength="500" placeholder="Any extra details that will help us investigate…" class="k-mf-textarea"></textarea>
                        </label>
                        <div id="kReportMsg" style="display:none;padding:8px 12px;border-radius:8px;font-size:13px;margin-bottom:8px"></div>
                        <div class="k-mf-btn-row">
                            <button type="submit" id="kReportSubmitBtn" class="k-btn k-btn-primary k-mf-btn-submit">Submit Report</button>
                            <button type="button" id="kReportCancelBtn" class="k-btn k-mf-btn-cancel">Cancel</button>
                        </div>
                    </form>
                    <script nonce="{{ $cspNonce ?? '' }}">
                    function submitReport(e){
                        e.preventDefault();
                        @guest
                        window.location='/login';return;
                        @endguest
                        var form=document.getElementById('kReportForm');
                        var btn=document.getElementById('kReportSubmitBtn');
                        var msg=document.getElementById('kReportMsg');
                        btn.disabled=true;btn.textContent='Submitting…';
                        fetch('/listings/{{ $listing->id }}/report',{
                            method:'POST',
                            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':form.querySelector('[name=_token]').value},
                            body:JSON.stringify({reason:form.querySelector('[name=reason]').value,details:form.querySelector('[name=details]').value})
                        }).then(function(r){return r.json().then(function(d){return{ok:r.ok,d:d};});})
                        .then(function(res){
                            msg.style.display='block';
                            msg.style.background=res.ok?'#e8f5e9':'#fef2f2';
                            msg.style.color=res.ok?'#1b5e20':'#dc2626';
                            msg.textContent=res.d.message||'Done.';
                            btn.textContent='Submitted';
                            if(res.ok)setTimeout(function(){document.getElementById('kReportModal').style.display='none';},1800);
                            else btn.disabled=false;
                        }).catch(function(){btn.disabled=false;btn.textContent='Submit Report';});
                    }
                    </script>
                </div>
            </div>
            <script nonce="{{ $cspNonce ?? '' }}">
            document.getElementById('kReportModal').addEventListener('click', function(e){
                if(e.target === this) this.style.display = 'none';
            });
            </script>

            <div class="k-product-action-links">
                <button class="k-product-action-link" id="saveBtn">🤍 Save</button>
                <a href="https://wa.me/?text={{ urlencode($listing->title.' — '.$price.' · '.url('/listings/'.$listing->slug)) }}" target="_blank" rel="noopener" class="k-product-action-link k-share-wa">🟢 WhatsApp</a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/listings/'.$listing->slug)) }}" target="_blank" rel="noopener" class="k-product-action-link k-share-fb">📘 Facebook</a>
                <button class="k-product-action-link" id="shareBtn">↗ Share</button>
                @auth
                <button type="button" id="kReportOpenBtn" class="k-product-action-link k-product-action-link--report">⚑ Report Ad</button>
                @else
                <a href="/login?redirect={{ urlencode(url()->current()) }}" class="k-product-action-link k-product-action-link--report">⚑ Report Ad</a>
                @endauth
            </div>
        </div>

        <!-- Right sidebar -->
        <div>
            <div class="k-seller-card mb-16">
                <div class="k-seller-card-top" @if(optional($listing->store)->banner) style="background-image:url('{{ asset('storage/'.ltrim($listing->store->banner,'/')) }}');background-size:cover;background-position:center;background-color:#1a5c1f;" @endif>
                    @if(optional($listing->store)->banner)<div style="position:absolute;inset:0;background:linear-gradient(145deg,rgba(15,40,18,0.72),rgba(20,60,22,0.58));z-index:0;"></div>@endif
                    <div class="k-seller-card-label" style="position:relative;z-index:1;">Seller Information</div>
                    <div class="k-seller-card-header" style="position:relative;z-index:1;">
                        <div class="k-seller-avatar">
                            @if(optional($listing->store)->logo)
                                <img src="{{ asset('storage/'.ltrim($listing->store->logo,'/')) }}" alt="{{ $sellerName }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
                            @else
                                {{ strtoupper(substr($sellerName,0,1)) }}
                            @endif
                        </div>
                        <div>
                            <div class="k-seller-name">{{ $sellerName }}</div>
                            @if(optional($listing->store)->is_kurilla_verified)
                                <div class="k-kurilla-verified-badge k-mt-4">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    kegalle Verified
                                </div>
                            @elseif(optional($listing->store)->id)
                                <div class="k-verified-badge k-mt-4">✓ Registered Store</div>
                            @endif
                            @if(optional($listing->store)->id)
                            @php $storeRank = $listing->store->rank_label; $storeRankColor = $listing->store->rank_color; @endphp
                            <span class="k-store-rank-badge k-mt-4" style="background:{{ $storeRankColor }}">{{ $storeRank }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="k-seller-card-body">
                    @if(optional($listing->store)->slug)
                    <div class="k-seller-btn-row">
                        <a href="/store/{{ $listing->store->slug }}" class="k-btn k-btn-outline k-btn-sm w-full k-btn-center">🏪 View Store</a>
                    </div>
                    @endif
                    <div class="k-seller-stat-grid">
                        <div class="k-seller-stat"><strong>{{ optional($listing->store)->created_at?->format('M Y') ?? optional($listing->user)->created_at?->format('M Y') ?? '—' }}</strong><span>Joined</span></div>
                        @if(optional($listing->store)->id)
                        @php $sellerAvg = $listing->store->average_rating; @endphp
                        <div class="k-seller-stat">
                            @if($sellerAvg)
                                <strong class="k-seller-rating-star">{{ $sellerAvg }} ★</strong>
                            @else
                                <strong class="k-seller-rating-none">No reviews</strong>
                            @endif
                            <span>Rating</span>
                        </div>
                        @endif
                        <div class="k-seller-stat"><strong>{{ $store ? ($store->active_listings_count ?? 0) : ($listing->user?->active_listings_count ?? 1) }}</strong><span>Active Ads</span></div>
                        <div class="k-seller-stat"><strong class="k-seller-stat-loc">{{ $location }}</strong><span>Location</span></div>
                    </div>
                    @auth
                    @if(auth()->id() !== $listing->user_id)
                    @php $isBlocked = \App\Models\UserBlock::where('blocker_id',auth()->id())->where('blocked_id',$listing->user_id)->exists(); @endphp
                    <div style="margin-top:10px;text-align:center">
                        <button id="kBlockBtn" data-uid="{{ $listing->user_id }}" style="font-size:11px;background:none;border:none;color:{{ $isBlocked?'#ef4444':'#94a3b8' }};cursor:pointer;text-decoration:underline;padding:0">
                            {{ $isBlocked ? '🚫 Unblock User' : '🚫 Block User' }}
                        </button>
                    </div>
                    <script nonce="{{ $cspNonce ?? '' }}">
                    function toggleBlock(uid){
                        var btn=document.getElementById('kBlockBtn');
                        var blocked=btn.textContent.trim().indexOf('Unblock')>-1;
                        fetch(blocked?'/users/'+uid+'/unblock':'/users/'+uid+'/block',{
                            method:blocked?'DELETE':'POST',
                            headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}
                        }).then(function(r){return r.json();}).then(function(d){
                            if(d.blocked){btn.textContent='🚫 Unblock User';btn.style.color='#ef4444';}
                            else{btn.textContent='🚫 Block User';btn.style.color='#94a3b8';}
                        }).catch(function(){});
                    }
                    </script>
                    @endif
                    @endauth
                    <div class="k-safety-card">
                        <div class="k-safety-title">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7v5c0 5.25 3.75 10.15 9 11.35C17.25 22.15 21 17.25 21 12V7L12 2z" fill="#d97706" opacity=".2"/><path d="M12 2L3 7v5c0 5.25 3.75 10.15 9 11.35C17.25 22.15 21 17.25 21 12V7L12 2z" stroke="#b45309" stroke-width="1.5" stroke-linejoin="round"/><path d="M9 12l2 2 4-4" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Safety Tips
                        </div>
                        <div class="k-safety-tip">
                            <span class="k-safety-tip-icon"><svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            <span class="k-safety-tip-text">Meet in a safe public place</span>
                        </div>
                        <div class="k-safety-tip">
                            <span class="k-safety-tip-icon"><svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            <span class="k-safety-tip-text">Check the item before you buy</span>
                        </div>
                        <div class="k-safety-tip">
                            <span class="k-safety-tip-icon"><svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            <span class="k-safety-tip-text">Pay only after inspection</span>
                        </div>
                        <div class="k-safety-tip">
                            <span class="k-safety-tip-icon"><svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                            <span class="k-safety-tip-text">Avoid advance payments</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="k-info-card">
                <h3>📍 Location</h3>
                <div class="k-map-preview">
                    <div class="k-map-preview-inner">
                        <span class="k-map-preview-icon">📍</span>
                        <div class="k-map-preview-city">{{ $location }}</div>
                        <div class="k-map-preview-district">Kegalle District, Sri Lanka</div>
                    </div>
                </div>
                <div class="k-map-footer">
                    <a href="https://www.google.com/maps/search/{{ urlencode($location . ' Kegalle Sri Lanka') }}" target="_blank" rel="noopener">
                        🗺 View on Google Maps →
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(isset($activeDeal) && $activeDeal && isset($similarDeals) && $similarDeals->count())
        <div class="k-section k-section--mt">
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
        <div class="k-section k-section--mt">
            <div class="k-section-header">
                <h2 class="k-section-title">{{ ($relatedIsGeneric ?? false) ? 'More Ads' : 'Similar Products' }}</h2>
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
    @elseif(isset($listing->category) && $listing->category)
    <div class="k-section k-section--mt" style="text-align:center;padding:2rem 1rem;">
        <p style="color:var(--k-muted);margin-bottom:1rem;">Browse more listings in <strong>{{ $listing->category->name }}</strong></p>
        <a href="/listings?category={{ $listing->category->slug ?? $listing->category_id }}" class="k-btn k-btn-secondary">View all {{ $listing->category->name }} ads →</a>
    </div>
    @endif

</div>

<script nonce="{{ $cspNonce ?? '' }}">
// Language switcher
(function(){
    var btns = document.querySelectorAll('.klp-lang-btn');
    if(!btns.length) return;
    btns.forEach(function(btn){
        btn.addEventListener('click', function(){
            var lang = btn.dataset.lang;
            btns.forEach(function(b){ b.classList.remove('active'); });
            btn.classList.add('active');
            document.querySelectorAll('.klp-lang-content').forEach(function(el){ el.classList.add('klp-lang-hidden'); });
            var panel = document.getElementById('klp-lang-' + lang);
            if(panel){ panel.classList.remove('klp-lang-hidden'); }
        });
    });
})();

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
<script nonce="{{ $cspNonce ?? '' }}">
/* ── Click-to-reveal phone / WhatsApp ── */
function kRevealCall(btn) {
    var tel = atob(btn.dataset.tel);
    var fmt = btn.dataset.fmt || tel;
    var a = document.createElement('a');
    a.href = 'tel:' + tel;
    a.className = btn.className.replace('k-reveal-call','').trim();
    a.innerHTML = '📞 ' + fmt;
    btn.replaceWith(a);
}
function kRevealWa(btn) {
    var num = atob(btn.dataset.wa);
    var msg = atob(btn.dataset.msg);
    var a = document.createElement('a');
    a.href = 'https://wa.me/' + num + '?text=' + encodeURIComponent(msg);
    a.className = btn.className.replace('k-reveal-wa','').trim();
    a.target = '_blank'; a.rel = 'noopener noreferrer';
    a.innerHTML = btn.innerHTML;
    btn.replaceWith(a);
    a.click();
}
function kRevealCallScb(btn) {
    var tel = atob(btn.dataset.tel);
    var fmt = btn.dataset.fmt || tel;
    var a = document.createElement('a');
    a.href = 'tel:' + tel;
    a.className = btn.className.replace('k-reveal-call','').trim();
    a.innerHTML = '📞 ' + fmt;
    btn.replaceWith(a);
    a.click();
}
function kRevealWaScb(btn) {
    var num = atob(btn.dataset.wa);
    var msg = atob(btn.dataset.msg);
    var a = document.createElement('a');
    a.href = 'https://wa.me/' + num + '?text=' + encodeURIComponent(msg);
    a.className = btn.className.replace('k-reveal-wa','').trim();
    a.target = '_blank'; a.rel = 'noopener noreferrer';
    a.innerHTML = btn.innerHTML;
    btn.replaceWith(a);
    a.click();
}
/* ── Wire up all CSP-blocked onclick handlers ── */
(function(){
    // WhatsApp reveal buttons
    document.querySelectorAll('.k-reveal-wa').forEach(function(btn){
        btn.addEventListener('click', function(){ kRevealWa(this); }, {once:true});
    });
    // Call reveal buttons
    document.querySelectorAll('.k-reveal-call').forEach(function(btn){
        btn.addEventListener('click', function(){ kRevealCall(this); }, {once:true});
    });
    // Make Offer modal open/close
    var offerModal = document.getElementById('kOfferModal');
    var offerOpenBtn = document.getElementById('kOfferOpenBtn');
    var offerCloseBtn = document.getElementById('kOfferCloseBtn');
    if (offerModal) {
        if (offerOpenBtn) offerOpenBtn.addEventListener('click', function(){ offerModal.style.display='flex'; });
        if (offerCloseBtn) offerCloseBtn.addEventListener('click', function(){ offerModal.style.display='none'; });
        offerModal.addEventListener('click', function(e){ if(e.target===offerModal) offerModal.style.display='none'; });
    }
    // Report modal open/close
    var reportModal = document.getElementById('kReportModal');
    var reportOpenBtn = document.getElementById('kReportOpenBtn');
    var reportCloseBtn = document.getElementById('kReportCloseBtn');
    var reportCancelBtn = document.getElementById('kReportCancelBtn');
    if (reportModal) {
        if (reportOpenBtn) reportOpenBtn.addEventListener('click', function(){ reportModal.style.display='flex'; });
        if (reportCloseBtn) reportCloseBtn.addEventListener('click', function(){ reportModal.style.display='none'; });
        if (reportCancelBtn) reportCancelBtn.addEventListener('click', function(){ reportModal.style.display='none'; });
        reportModal.addEventListener('click', function(e){ if(e.target===reportModal) reportModal.style.display='none'; });
    }
    // Guest Enquiry modal open/close
    var geqModal = document.getElementById('kGuestEnquiry');
    var geqOpenBtn = document.getElementById('kGuestEnquiryOpenBtn');
    var geqCloseBtn = document.getElementById('kGuestEnquiryCloseBtn');
    if (geqModal) {
        if (geqOpenBtn) geqOpenBtn.addEventListener('click', function(){ geqModal.style.display='flex'; });
        if (geqCloseBtn) geqCloseBtn.addEventListener('click', function(){ geqModal.style.display='none'; });
        geqModal.addEventListener('click', function(e){ if(e.target===geqModal) geqModal.style.display='none'; });
    }
    // Save button
    var saveBtn = document.getElementById('saveBtn');
    if (saveBtn) saveBtn.addEventListener('click', function(){ toggleSave(); });
    // Share button
    var shareBtn = document.getElementById('shareBtn');
    if (shareBtn) shareBtn.addEventListener('click', function(){ shareListing(); });
    // Block button
    var blockBtn = document.getElementById('kBlockBtn');
    if (blockBtn) blockBtn.addEventListener('click', function(){ toggleBlock(parseInt(blockBtn.dataset.uid||0)); });
    // Price alert button
    var priceAlertBtn = document.getElementById('priceAlertBtn');
    if (priceAlertBtn) priceAlertBtn.addEventListener('click', function(){ togglePriceAlert(this); });
})();
/* ─────────────────────────────────────── */
function togglePriceAlert(btn) {
    var url = btn.dataset.url;
    var token = btn.dataset.token;
    btn.disabled = true;
    fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
        body: JSON.stringify({})
    }).then(function(r){ return r.json(); }).then(function(data) {
        if (data.active) {
            btn.textContent = '🔔 Alert set';
            btn.classList.add('active');
            btn.title = 'Cancel price drop alert';
        } else {
            btn.textContent = '🔔 Alert me';
            btn.classList.remove('active');
            btn.title = 'Alert me if price drops';
        }
    }).catch(function(){ btn.textContent = '🔔 Alert me'; }).finally(function(){ btn.disabled = false; });
}
(function(){
    // ── Carousel thumb strip (5 visible, arrows page through) ────
    var mainImg     = document.getElementById('kMainImg');
    var thumbInner  = document.getElementById('kThumbInner');
    var thumbs      = document.querySelectorAll('.k-product-thumb[data-img]');
    var counter     = document.getElementById('kImgCounter');
    var btnPrev     = document.getElementById('kThumbPrev');
    var btnNext     = document.getElementById('kThumbNext');
    var THUMB_W     = 70 + 8; // thumb width + gap
    var PAGE        = 5;      // visible at once
    var totalThumbs = thumbs.length;
    var offset      = 0;      // current left-most visible index

    function updateArrows() {
        if (btnPrev) btnPrev.classList.toggle('hidden', offset <= 0);
        if (btnNext) btnNext.classList.toggle('hidden', offset + PAGE >= totalThumbs);
    }

    function slideTo(newOffset) {
        offset = Math.max(0, Math.min(newOffset, totalThumbs - PAGE));
        if (thumbInner) thumbInner.style.transform = 'translateX(-' + (offset * THUMB_W) + 'px)';
        updateArrows();
    }

    function setActive(idx) {
        if (!mainImg) return;
        thumbs.forEach(function(t) { t.classList.remove('active'); });
        var active = thumbs[idx];
        if (!active) return;
        active.classList.add('active');
        // slide strip so active thumb is visible
        if (idx < offset) { slideTo(idx); }
        else if (idx >= offset + PAGE) { slideTo(idx - PAGE + 1); }
        if (counter) counter.textContent = (idx + 1) + ' / ' + totalThumbs;
        mainImg.style.opacity = '0';
        setTimeout(function() {
            mainImg.src = active.dataset.img;
            mainImg.style.opacity = '1';
        }, 120);
    }

    thumbs.forEach(function(thumb, idx) {
        thumb.addEventListener('click', function() { setActive(idx); });
    });

    if (btnPrev) btnPrev.addEventListener('click', function() { slideTo(offset - PAGE); });
    if (btnNext) btnNext.addEventListener('click', function() { slideTo(offset + PAGE); });
    updateArrows();

    var tabs = document.querySelectorAll('.k-product-tab[data-tab]');
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            tabs.forEach(function(t) { t.classList.remove('active'); });
            document.querySelectorAll('.k-tab-panel').forEach(function(p) { p.classList.remove('active'); });
            tab.classList.add('active');
            var panel = document.getElementById('tab-' + tab.dataset.tab);
            if (panel) panel.classList.add('active');
            // Lazy-load Google Maps when Location tab is first clicked
            if (tab.dataset.tab === 'location') {
                var frame = document.getElementById('k-map-frame');
                if (frame && frame.dataset.src && !frame.src) {
                    frame.src = frame.dataset.src;
                }
            }
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
    var isAuth = {{ auth()->check() ? 'true' : 'false' }};
    var isFavored = {{ auth()->check() && auth()->user()->favorites()->where('listing_id',$listing->id)->exists() ? 'true' : 'false' }};
    var toggleUrl = '/dashboard/favorites/{{ $listing->id }}/toggle';
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // Initialise button state
    (function(){
        var btn = document.getElementById('saveBtn');
        if (!btn) return;
        var localSaved = JSON.parse(localStorage.getItem('k_saved') || '[]');
        var active = isAuth ? isFavored : localSaved.indexOf(listingId) !== -1;
        if (active) { btn.textContent = '❤ Saved'; btn.classList.add('active'); }
    })();

    window.toggleSave = function() {
        var btn = document.getElementById('saveBtn');
        if (isAuth) {
            // DB-backed for logged-in users
            fetch(toggleUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            }).then(function(r){ return r.json(); }).then(function(data){
                if (data.favorited) {
                    btn.textContent = '❤ Saved'; btn.classList.add('active');
                    if (window.kToast) window.kToast('Saved to your favourites', 'success');
                } else {
                    btn.textContent = '🤍 Save'; btn.classList.remove('active');
                    if (window.kToast) window.kToast('Removed from favourites', 'success');
                }
            }).catch(function(){
                if (window.kToast) window.kToast('Something went wrong', 'error');
            });
        } else {
            // localStorage for guests
            var saved = JSON.parse(localStorage.getItem('k_saved') || '[]');
            var idx = saved.indexOf(listingId);
            if (idx === -1) {
                saved.push(listingId);
                btn.textContent = '❤ Saved'; btn.classList.add('active');
                if (window.kToast) window.kToast('Saved to your favourites', 'success');
            } else {
                saved.splice(idx, 1);
                btn.textContent = '🤍 Save'; btn.classList.remove('active');
                if (window.kToast) window.kToast('Removed from favourites', 'success');
            }
            localStorage.setItem('k_saved', JSON.stringify(saved));
            var b1 = document.getElementById('kNavSavedBadge'), b2 = document.getElementById('kDrawerSavedBadge');
            var n = saved.length;
            if (b1) { b1.textContent = n; b1.style.display = n ? '' : 'none'; }
            if (b2) { b2.textContent = n; b2.style.display = n ? 'inline' : 'none'; }
        }
    };

    window.shareListing = function() {
        var data = { title: @json($listing->title), url: window.location.href };
        if (navigator.share) {
            navigator.share(data).catch(function(){});
        } else {
            var btn = document.querySelector('[onclick*="shareListing"]');
            var orig = btn ? btn.textContent : '';
            try {
                navigator.clipboard.writeText(window.location.href).then(function() {
                    if (btn) { btn.textContent = '✓ Link Copied!'; setTimeout(function() { btn.textContent = orig; }, 2000); }
                }).catch(function() {
                    if (btn) { btn.textContent = '✓ Link Copied!'; setTimeout(function() { btn.textContent = orig; }, 2000); }
                    prompt('Copy this link:', window.location.href);
                });
            } catch(e) {
                prompt('Copy this link:', window.location.href);
            }
        }
    };
})();
</script>
@endpush

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var item = {
        id: {{ $listing->id }},
        title: {!! json_encode(\Illuminate\Support\Str::limit($listing->title, 40), JSON_HEX_TAG) !!},
        url: '/listing/' + {!! json_encode($listing->slug, JSON_HEX_TAG) !!},
        img: {!! json_encode($mainImageUrl ?? '', JSON_HEX_TAG) !!},
        price: {!! json_encode($price, JSON_HEX_TAG) !!},
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
<script nonce="{{ $cspNonce ?? '' }}">
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

{{-- ── Lightbox overlay ──────────────────────────────────── --}}
<div id="kLightbox" class="k-lightbox" role="dialog" aria-modal="true" aria-label="Image viewer">
    <button class="k-lightbox-close" id="kLbClose" aria-label="Close">&times;</button>
    <button class="k-lightbox-arrow k-lightbox-prev" id="kLbPrev" aria-label="Previous">&#8249;</button>
    <div class="k-lightbox-img-wrap">
        <img id="kLbImg" src="" alt="{{ $listing->title }}" class="k-lightbox-img">
        <div class="k-lightbox-counter" id="kLbCounter"></div>
    </div>
    <button class="k-lightbox-arrow k-lightbox-next" id="kLbNext" aria-label="Next">&#8250;</button>
</div>

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
// ── Lightbox ───────────────────────────────────────────────
(function(){
    @if($images->count() > 0)
    var imgs = {!! $images->map(fn($i) => asset('storage/'.ltrim($i->path,'/')))->toJson() !!};
    var idx  = 0;
    var lb   = document.getElementById('kLightbox');
    var lbImg= document.getElementById('kLbImg');
    var lbCnt= document.getElementById('kLbCounter');

    function open(i){
        idx = ((i % imgs.length) + imgs.length) % imgs.length;
        lbImg.src = imgs[idx];
        lbCnt.textContent = (idx+1) + ' / ' + imgs.length;
        lb.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function close(){
        lb.classList.remove('open');
        document.body.style.overflow = '';
    }

    // Open on main image click
    var wrap = document.getElementById('kMainImgWrap');
    if(wrap) wrap.addEventListener('click', function(){
        var cur = document.querySelector('.k-product-thumb.active');
        open(cur ? parseInt(cur.dataset.idx||0) : 0);
    });

    // Thumb click → sync lightbox index
    document.querySelectorAll('.k-product-thumb[data-idx]').forEach(function(t){
        t.addEventListener('dblclick', function(){ open(parseInt(t.dataset.idx)); });
    });

    document.getElementById('kLbClose').onclick = close;
    document.getElementById('kLbPrev').onclick  = function(){ open(idx - 1); };
    document.getElementById('kLbNext').onclick  = function(){ open(idx + 1); };

    lb.addEventListener('click', function(e){ if(e.target === lb) close(); });
    document.addEventListener('keydown', function(e){
        if(!lb.classList.contains('open')) return;
        if(e.key === 'Escape') close();
        if(e.key === 'ArrowLeft')  { open(idx - 1); }
        if(e.key === 'ArrowRight') { open(idx + 1); }
    });
    @endif
})();
</script>
@endpush

{{-- Guest Enquiry Modal --}}
@guest
<div id="kGuestEnquiry" class="k-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="kGeqTitle" style="display:none">
    <div class="k-modal-panel k-modal-panel-sm">
        <button type="button" id="kGuestEnquiryCloseBtn" class="k-modal-close" aria-label="Close enquiry form">×</button>
        <h2 id="kGeqTitle" class="k-modal-title">💬 Send Enquiry</h2>
        <p class="k-modal-sub" style="margin-bottom:16px">{{ Str::limit($listing->title, 60) }}</p>
        <form id="kGeqForm" onsubmit="kGeqSubmit(event)">
            @csrf
            <label class="k-mf-label" style="display:block;margin-bottom:12px">
                <span class="k-mf-caption">Your Name *</span>
                <input type="text" id="kGeqName" name="name" required class="k-mf-input" maxlength="80" autocomplete="name">
            </label>
            <label class="k-mf-label" style="display:block;margin-bottom:12px">
                <span class="k-mf-caption">Phone or Email *</span>
                <input type="text" id="kGeqContact" name="contact" required class="k-mf-input" maxlength="100" placeholder="+94 7X XXX XXXX or your@email.com">
            </label>
            <label class="k-mf-label" style="display:block;margin-bottom:16px">
                <span class="k-mf-caption">Message *</span>
                <textarea id="kGeqMsg" name="message" required class="k-mf-input" rows="3" maxlength="500" placeholder="Hi, is this item still available?"></textarea>
            </label>
            <button type="submit" id="kGeqBtn" class="k-btn k-btn-primary k-btn-full">Send Message</button>
        </form>
        <div id="kGeqSuccess" style="display:none;text-align:center;padding:24px 0">
            <div style="font-size:40px">✅</div>
            <p style="font-weight:700;font-size:16px;margin-top:12px">Message Sent!</p>
            <p style="color:#64748b;font-size:14px">The seller will get in touch with you directly.</p>
        </div>
    </div>
</div>
@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
function kGeqSubmit(e){
    e.preventDefault();
    var btn=document.getElementById('kGeqBtn');
    btn.disabled=true; btn.textContent='Sending…';
    var token=document.querySelector('meta[name=csrf-token]').content;
    fetch('/listings/{{ $listing->id }}/enquire',{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token},
        body:JSON.stringify({
            name:document.getElementById('kGeqName').value,
            contact:document.getElementById('kGeqContact').value,
            message:document.getElementById('kGeqMsg').value
        })
    }).then(function(r){return r.json();}).then(function(d){
        if(d.ok){
            document.getElementById('kGeqForm').style.display='none';
            document.getElementById('kGeqSuccess').style.display='';
        } else {
            btn.disabled=false; btn.textContent='Send Message';
            alert(d.error||'Failed to send. Please try again.');
        }
    }).catch(function(){btn.disabled=false; btn.textContent='Send Message'; alert('Network error. Please try again.');});
}
</script>
@endpush
@endguest

@endsection
