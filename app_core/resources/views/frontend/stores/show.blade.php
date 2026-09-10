@extends('layouts.app')

@php
    $v      = $store->updated_at?->timestamp ?? time();
    $logo   = !empty($store->logo) ? asset('storage/'.ltrim($store->logo,'/')).('?v='.$v) : null;
    $banner = !empty($store->banner) ? asset('storage/'.ltrim($store->banner,'/')).('?v='.$v) : (!empty($store->cover_image) ? asset('storage/'.ltrim($store->cover_image,'/')).('?v='.$v) : null);
    $storeDesc = \Illuminate\Support\Str::limit(strip_tags($store->description ?? ''), 155) ?: ($store->name.' — verified store on Kegalle Marketplace with '.($store->listings_count ?? 0).'+ products. Browse and contact directly.');
    $avgRating = ($reviews ?? collect())->count() ? round(($reviews ?? collect())->avg('rating'), 1) : 0;
    $reviewCount = ($reviews ?? collect())->count();
    $rank = $store->rank;
    $rankLabel = $store->rank_label;
    $rankColor = $store->rank_color;

    /* ── SVG icon helpers ── */
    $iPin  = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>';
    $iPhone= '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';
    $iMail = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>';
    $iGlobe= '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>';
    $iCal  = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>';
    $iChat = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>';
    $iBox  = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>';
    $iLock = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>';
    $iMap  = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>';
    $iMobile = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>';
    $iCheck= '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
    $iFlame= '<svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M13.5.67s.74 2.65.74 4.8c0 2.06-1.35 3.73-3.41 3.73-2.07 0-3.63-1.67-3.63-3.73l.03-.36C5.21 7.51 4 10.62 4 14c0 4.42 3.58 8 8 8s8-3.58 8-8C20 8.61 17.41 3.8 13.5.67zm-1.79 19c-1.78 0-3.22-1.4-3.22-3.14 0-1.62 1.05-2.76 2.81-3.12 1.77-.36 3.6-1.21 4.62-2.58.39 1.29.59 2.65.59 4.04 0 2.65-2.15 4.8-4.8 4.8z"/></svg>';
    $iClip = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>';
    $iStar = '<svg width="13" height="13" viewBox="0 0 24 24" fill="#f59e0b"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
    $iWa   = '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>';

    $rankSvg = match($rank) {
        'platinum' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#818cf8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15 9 22 9 17 14 19 21 12 17 5 21 7 14 2 9 9 9"/></svg>',
        'gold'     => '<svg width="32" height="32" viewBox="0 0 24 24" fill="#f59e0b"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>',
        'silver'   => '<svg width="32" height="32" viewBox="0 0 24 24" fill="#94a3b8"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>',
        default    => '<svg width="32" height="32" viewBox="0 0 24 24" fill="#cd7c3b"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>',
    };
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

@push('styles')
<link rel="stylesheet" href="{{ asset('css/kegalle-store-profile.css') }}?v=7">
@endpush

@section('content')

@php $kspMonths = $store->created_at ? (int)$store->created_at->diffInMonths(now()) : 0; $kspDuration = $kspMonths >= 12 ? (int)floor($kspMonths/12).'yr' : max(1,$kspMonths).'mo'; @endphp

{{-- ══ LIGHTBOX ══ --}}
<div id="kspLightbox" class="ksp-hidden">
  <img id="kspLbImg" src="" alt="" data-default-alt="Store image">
</div>

{{-- ══ HERO ══ --}}
<div class="ksp-hero{{ $banner ? '' : ' no-banner' }}">
  @if($banner)<img src="{{ $banner }}" alt="{{ $store->name }}" class="ksp-hero-img ksp-zoomable" loading="eager">@endif
  <div class="ksp-hero-grad"></div>
  <div class="container ksp-hero-inner">
    <nav class="ksp-bc"><a href="/">Home</a><span class="sep">›</span><a href="/stores">Stores</a><span class="sep">›</span>{{ $store->name }}</nav>
  </div>
</div>

{{-- ══ PROFILE CARD ══ --}}
<div class="ksp-profile-outer">
  <div class="ksp-profile-wrap">
    <div class="ksp-card">
      <div class="ksp-top">
        <div class="ksp-logo">
          @if($logo)<img src="{{ $logo }}" alt="{{ $store->name }}" class="ksp-zoomable">@else{{ strtoupper(substr($store->name,0,1)) }}@endif
        </div>
        <div class="ksp-info">
          <h1 class="ksp-name">{{ $store->name }}</h1>
          <div class="ksp-badges">
            @if($store->is_verified)<span class="ksp-badge ksp-badge-v">{!! $iCheck !!} Verified Store</span>@endif
            @if($store->user?->phone_verified_at)<span class="ksp-badge ksp-badge-p">{!! $iMobile !!} Phone Verified</span>@endif
            <span class="ksp-badge ksp-badge-r" style="background:{{ $rankColor }}">{{ $rankLabel }}</span>
          </div>
          <div class="ksp-meta">
            <span class="ksp-mi">{!! $iBox !!} {{ $store->listings_count??0 }} {{ \Illuminate\Support\Str::plural('Product',$store->listings_count??0) }}</span>
            <span class="ksp-mi">{!! $iCal !!} Since {{ $store->created_at?->format('M Y')?? '—' }}</span>
            <span class="ksp-mi">{!! $iPin !!} {{ $store->city??$store->address?? 'Kegalle' }}</span>
            @if($reviewCount>0)<span class="ksp-mi">★ {{ $avgRating }} ({{ $reviewCount }} {{ \Illuminate\Support\Str::plural('review',$reviewCount) }})</span>@endif
          </div>
        </div>
        <div class="ksp-actions">
          @auth
            <a href="/dashboard/chat?to={{ $store->user_id }}" class="ksp-btn-msg">{!! $iChat !!} Message</a>
          @else
            <a href="/login?redirect={{ urlencode('/store/'.$store->slug) }}" class="ksp-btn-msg">{!! $iChat !!} Message</a>
          @endauth
          @if($store->whatsapp??$store->phone)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/','', $store->whatsapp??$store->phone) }}?text={{ urlencode('Hi, I found your store on Kegalle.com : '.$store->name) }}" class="ksp-btn-wa" target="_blank" rel="noopener">{!! $iWa !!} WhatsApp</a>
          @endif
        </div>
      </div>
      {{-- Dark green stats bar --}}
      <div class="ksp-stats">
        <div class="ksp-stat"><div class="ksp-stat-n">{{ $store->listings_count??0 }}</div><div class="ksp-stat-l">Products</div></div>
        <div class="ksp-stat"><div class="ksp-stat-n">{{ $reviewCount }}</div><div class="ksp-stat-l">Reviews</div></div>
        <div class="ksp-stat"><div class="ksp-stat-n">{{ $reviewCount ? $avgRating : '0' }}</div><div class="ksp-stat-l">Avg Rating</div></div>
        <div class="ksp-stat"><div class="ksp-stat-n">{{ $kspDuration }}</div><div class="ksp-stat-l">Active</div></div>
      </div>
    </div>
  </div>
</div>

{{-- ══ TABS ══ --}}
<div class="ksp-tabs">
  <div class="ksp-tabs-wrap">
    <div class="ksp-tabs-inner">
      <a href="javascript:void(0)" class="ksp-tab active" data-store-tab="overview">Overview</a>
      <a href="#products" class="ksp-tab" data-store-tab="products">Products <span class="cnt" data-count="{{ $store->listings_count??0 }}">{{ $store->listings_count??0 }}</span></a>
      @if(($storeDeals??collect())->count())
      <a href="#deals" class="ksp-tab ksp-deals-tab" data-store-tab="deals">{!! $iFlame !!} Deals <span class="cnt ksp-cnt-red">{{ ($storeDeals??collect())->count() }}</span></a>
      @endif
      <a href="#reviews" class="ksp-tab" data-store-tab="reviews">Reviews <span class="cnt" data-count="{{ $reviewCount }}">{{ $reviewCount }}</span></a>
      <a href="javascript:void(0)" class="ksp-tab" data-store-tab="about">About</a>
    </div>
  </div>
</div>

{{-- ══ MAIN CONTENT ══ --}}
<div class="ksp-main-wrap" id="ksp-main-content">

  {{-- LEFT COLUMN --}}
  <div>

    {{-- Featured Categories --}}
    <div class="ksp-section" id="overview-section">
      <div class="ksp-sec-hdr"><h2 class="ksp-sec-title">Featured Categories</h2></div>
      <div class="ksp-cat-scroll">
        @forelse(($categories??collect())->take(6) as $cat)
          <a href="/store/{{ $store->slug }}?category={{ $cat->slug }}" class="ksp-cat-pill">
            <span class="icon">{{ $cat->icon ?? '' }}</span>
            <span class="label">{{ $cat->name }}</span>
            <span class="count">{{ $cat->listings_count??0 }}</span>
          </a>
        @empty
          <span class="ksp-rev-count ksp-cat-empty">No categories yet.</span>
        @endforelse
      </div>
    </div>

    {{-- Products --}}
    <div id="products" class="ksp-section">
      <div class="ksp-sec-hdr">
        <h2 class="ksp-sec-title">All Products</h2>
        @php
          $kspSortOpts=[''=>'Newest First','price_low'=>'Price: Low to High','price_high'=>'Price: High to Low','popular'=>'Most Popular'];
          $kspCurSort=request('sort','');
          $kspCurLabel=$kspSortOpts[$kspCurSort]??'Newest First';
        @endphp
        <input type="hidden" id="kspSortVal" value="{{ $kspCurSort }}">
        <div class="kpd-wrap" id="kspSortWrap">
          <div class="kpd-trigger" id="kspSortTrigger">
            <span id="kspSortLabel">{{ $kspCurLabel }}</span>
            <svg class="kpd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
          <div class="kpd-panel" id="kspSortPanel" style="display:none">
            <div class="kpd-list">
              @foreach($kspSortOpts as $val=>$lbl)
              <div class="kpd-item{{ $kspCurSort===$val?' selected':'' }}" data-value="{{ $val }}" data-label="{{ $lbl }}">
                <span class="kpd-dot"></span>{{ $lbl }}
              </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
      <div class="k-grid-4">
        @forelse($products as $listing)
          @include('frontend.listings.card',['listing'=>$listing])
        @empty
          <div class="ksp-prod-empty">
            <div class="ksp-prod-empty-icon">{!! $iBox !!}</div>
            <p class="ksp-prod-empty-title">No products yet</p>
            <p class="ksp-prod-empty-sub">This store hasn't listed any products yet. Check back soon!</p>
          </div>
        @endforelse
      </div>
      {{ $products->links('vendor.pagination.k-theme') }}
    </div>

    {{-- Reviews --}}
    <div id="reviews" class="ksp-section ksp-hidden">
      <h2 class="ksp-sec-title ksp-sec-title-mb">Customer Reviews</h2>
      @if($reviewCount>0)
        <div class="ksp-rev-summary">
          <div class="ksp-rev-score-col">
            <div class="ksp-rev-big-num">{{ $avgRating }}</div>
            <div class="ksp-rev-stars-lg">@for($s=1;$s<=5;$s++)<span style="color:{{ $s<=round($avgRating)?'#f59e0b':'#e5e7eb' }}">★</span>@endfor</div>
            <div class="ksp-rev-count">{{ $reviewCount }} {{ \Illuminate\Support\Str::plural('review',$reviewCount) }}</div>
          </div>
          <div class="ksp-rev-bars">
            @for($r=5;$r>=1;$r--)
              @php $cnt=($reviews??collect())->where('rating',$r)->count();$pct=$reviewCount?round($cnt/$reviewCount*100):0; @endphp
              <div class="ksp-rev-bar-row">
                <span class="ksp-rev-bar-lbl">{{ $r }} ★</span>
                <div class="ksp-rev-bar-track"><div class="ksp-rev-bar-fill" style="width:{{ $pct }}%"></div></div>
                <span class="ksp-rev-bar-cnt">{{ $cnt }}</span>
              </div>
            @endfor
          </div>
        </div>
      @else
        <div class="ksp-rev-no-reviews">No reviews yet — be the first to share your experience!</div>
      @endif

      @auth
        @if(!($reviews??collect())->where('user_id',auth()->id())->count())
          <div class="ksp-rev-form">
            <h4 class="ksp-rev-form-title">Write a Review</h4>
            <form method="POST" action="/store/{{ $store->id }}/review">
              @csrf
              <div class="k-star-input ksp-rev-star-row" id="storeStarInput">
                @for($s=1;$s<=5;$s++)<span class="k-star-pick" data-val="{{ $s }}">★</span>@endfor
                <input type="hidden" name="rating" id="storeRatingInput" value="0" required>
              </div>
              <textarea name="comment" rows="3" placeholder="Share your experience at {{ $store->name }}..." required minlength="5" maxlength="1000" class="ksp-rev-textarea"></textarea>
              <button type="submit" class="ksp-btn-msg ksp-rev-submit-wrap">Submit Review</button>
            </form>
          </div>
        @else
          <p class="ksp-rev-reviewed">You have already reviewed this store.</p>
        @endif
      @else
        <p class="ksp-rev-login-cta"><a href="/login">Log in</a> to write a review.</p>
      @endauth

      @foreach(($reviews??collect()) as $review)
        <div class="ksp-rev-card">
          <div class="ksp-rev-hdr">
            <div class="ksp-rev-avatar">{{ strtoupper(substr(optional($review->user)->name??'U',0,1)) }}</div>
            <div class="ksp-rev-meta">
              <div class="ksp-rev-name">{{ optional($review->user)->name??'User' }}</div>
              <div class="ksp-rev-date">{{ $review->created_at?->diffForHumans() }}</div>
            </div>
            <div class="ksp-rev-rating">@for($s=1;$s<=5;$s++)<span style="color:{{ $s<=$review->rating?'#f59e0b':'#e5e7eb' }}">★</span>@endfor</div>
          </div>
          <p class="ksp-rev-comment">{{ $review->comment }}</p>
          @if(!empty($review->reply))
          <div class="ksp-rev-reply">
            <div class="ksp-rev-reply-lbl">Owner reply &middot; {{ $review->replied_at?->format('M d, Y') }}</div>
            <div class="ksp-rev-reply-text">{{ $review->reply }}</div>
          </div>
          @endif
        </div>
      @endforeach
    </div>

    {{-- Deals --}}
    @if(($storeDeals??collect())->count())
    <div id="deals" class="ksp-section ksp-hidden">
      <div class="ksp-sec-hdr">
        <h2 class="ksp-sec-title">{!! $iFlame !!} Active Deals</h2>
      </div>
      <p class="ksp-rev-comment ksp-deals-sub">Limited-time discounts from {{ $store->name }}. Prices updated live.</p>
      <div class="ksp-deals-grid">
        @foreach($storeDeals as $deal)
          @if($deal->listing)
          @php
            $dImg = optional($deal->listing->images->first())->path ?? $deal->listing->image ?? null;
            $dImgUrl = $dImg ? asset('storage/'.ltrim($dImg,'/')) : null;
            $dSavings = $deal->original_price - $deal->deal_price;
          @endphp
          <a href="/listings/{{ $deal->listing->slug }}" class="ksp-deal-card">
            <div class="ksp-deal-img">
              @if($dImgUrl)
                <img src="{{ $dImgUrl }}" alt="{{ $deal->listing->title }}" loading="lazy">
              @else
                <div class="ksp-deal-img-ph">{!! $iBox !!}</div>
              @endif
              <div class="ksp-deal-badge">-{{ number_format($deal->discount_percent, 0) }}%</div>
              @if($deal->is_flash)<div class="ksp-deal-flash">{!! $iFlame !!} Flash</div>@endif
            </div>
            <div class="ksp-deal-body">
              <div class="ksp-deal-title">{{ \Illuminate\Support\Str::limit($deal->listing->title, 55) }}</div>
              <div class="ksp-deal-prices">
                <span class="ksp-deal-price">LKR {{ number_format($deal->deal_price) }}</span>
                <span class="ksp-deal-orig">LKR {{ number_format($deal->original_price) }}</span>
              </div>
              <div class="ksp-deal-save">You save LKR {{ number_format($dSavings) }}</div>
              <div class="ksp-deal-timer" data-end="{{ $deal->ends_at->toIso8601String() }}">
                <span class="ksp-deal-timer-label">Ends in:</span>
                <span class="ksp-deal-cd"></span>
              </div>
            </div>
          </a>
          @endif
        @endforeach
      </div>
    </div>
    @endif

  </div>

  {{-- ══ SIDEBAR ══ --}}
  <div>

    {{-- Rank & Rating --}}
    <div class="ksp-scard">
      <div class="ksp-rank-wrap">
        <span class="ksp-rank-icon">{!! $rankSvg !!}</span>
        <div class="ksp-rank-lbl" style="color:{{ $rankColor }}">{{ $rankLabel }}</div>
        @if($reviewCount>0)
          <span class="ksp-stars">@for($s=1;$s<=5;$s++)<span style="color:{{ $s<=round($avgRating)?'#f59e0b':'#e5e7eb' }}">★</span>@endfor</span>
          <span class="ksp-stars-sub">{{ $avgRating }} / 5 &nbsp;·&nbsp; {{ $reviewCount }} {{ \Illuminate\Support\Str::plural('review',$reviewCount) }}</span>
        @else
          <span class="ksp-stars-sub ksp-stars-sub-top">No reviews yet</span>
        @endif
      </div>
    </div>

    {{-- Store Information --}}
    <div class="ksp-scard">
      <div class="ksp-scard-title">Store Information</div>
      <div class="ksp-irow"><div class="ksp-iicon">{!! $iPin !!}</div><div class="ksp-ival">{{ $store->city??$store->address?? 'Kegalle' }}</div></div>
      @if(!empty($store->phone))
        <div class="ksp-irow"><div class="ksp-iicon">{!! $iPhone !!}</div><div class="ksp-ival">
          @auth<a href="tel:{{ \App\Helpers\PhoneHelper::tel($store->phone) }}">{{ \App\Helpers\PhoneHelper::format($store->phone) }}</a>@else<a href="/login?redirect={{ urlencode('/store/'.$store->slug) }}">{!! $iLock !!} Login to view</a>@endauth
        </div></div>
      @endif
      @if(!empty($store->email))
        <div class="ksp-irow"><div class="ksp-iicon">{!! $iMail !!}</div><div class="ksp-ival">
          @auth<a href="mailto:{{ $store->email }}">{{ $store->email }}</a>@else<a href="/login?redirect={{ urlencode('/store/'.$store->slug) }}">{!! $iLock !!} Login to view</a>@endauth
        </div></div>
      @endif
      @if(!empty($store->website))
        <div class="ksp-irow"><div class="ksp-iicon">{!! $iGlobe !!}</div><div class="ksp-ival"><a href="{{ \App\Helpers\HtmlSanitizer::safeUrl($store->website) }}" target="_blank" rel="noopener noreferrer">{{ $store->website }}</a></div></div>
      @endif
      <div class="ksp-irow"><div class="ksp-iicon">{!! $iCal !!}</div><div class="ksp-ival">Member since {{ $store->created_at?->format('M Y')?? '—' }}</div></div>
    </div>

    {{-- Statistics --}}
    <div class="ksp-scard">
      <div class="ksp-scard-title">Store Statistics</div>
      <div class="ksp-sstat-grid">
        <div class="ksp-sstat"><strong>{{ $store->listings_count??0 }}</strong><span>Products</span></div>
        <div class="ksp-sstat"><strong>{{ $reviewCount }}</strong><span>Reviews</span></div>
        <div class="ksp-sstat"><strong>{{ $reviewCount ? $avgRating : '0' }}</strong><span>Rating</span></div>
        <div class="ksp-sstat"><strong>{{ $kspDuration }}</strong><span>Active</span></div>
      </div>
      @if($store->is_verified||$reviewCount>0||($store->listings_count??0)>=5)
        <div class="ksp-trust-title">Trust Signals</div>
        @if($store->is_verified)<div class="ksp-trust-item"><span>{!! $iCheck !!}</span> Verified Store</div>@endif
        @if($store->user?->phone_verified_at)<div class="ksp-trust-item"><span>{!! $iMobile !!}</span> Phone Verified Seller</div>@endif
        @if($reviewCount>0)<div class="ksp-trust-item"><span>{!! $iStar !!}</span> {{ $avgRating }}/5 from {{ $reviewCount }} {{ \Illuminate\Support\Str::plural('review',$reviewCount) }}</div>@endif
        @if(($store->listings_count??0)>=5)<div class="ksp-trust-item"><span>{!! $iBox !!}</span> {{ $store->listings_count }}+ products</div>@endif
        @if($store->created_at&&$store->created_at->diffInMonths(now())>=3)<div class="ksp-trust-item"><span>{!! $iCal !!}</span> Active since {{ $store->created_at->format('M Y') }}</div>@endif
      @endif

      @if($store->latitude&&$store->longitude)
        <div id="storeLocationMap"></div>
        <div class="ksp-map-footer">
          <span class="ksp-map-loc">{!! $iPin !!} {{ $store->city?? 'Kegalle' }}</span>
          <a href="https://www.google.com/maps/dir/?api=1&destination={{ $store->latitude }},{{ $store->longitude }}" target="_blank" rel="noopener" class="ksp-map-dir">Directions →</a>
        </div>
      @else
        <div class="ksp-map-ph">{!! $iMap !!}</div>
        <div class="ksp-map-ph-loc">{!! $iPin !!} {{ $store->city?? 'Kegalle' }}</div>
      @endif
    </div>

    {{-- Share --}}
    <div class="ksp-scard ksp-share-card">
      <div class="ksp-scard-title">Share This Store</div>
      <div class="ksp-share-row">
        <a href="https://wa.me/?text={{ urlencode($store->name.' — '.url('/store/'.$store->slug)) }}" target="_blank" rel="noopener" class="ksp-btn-wa ksp-btn-sm">{!! $iWa !!} WhatsApp</a>
        <button type="button" class="ksp-btn-copy ksp-btn-sm" id="kspCopyBtn">{!! $iClip !!} Copy Link</button>
      </div>
    </div>

  </div>
</div>

{{-- About panel (tab-switched) --}}
<div id="ksp-about-panel" class="ksp-hidden">
  <div class="ksp-about-grid">

    {{-- Left: description --}}
    <div class="ksp-section">
      <h2 class="ksp-sec-title ksp-sec-title-mb">About {{ $store->name }}</h2>
      @if(!empty($store->description))
        <div class="ksp-about-desc">{!! nl2br(e($store->description)) !!}</div>
      @else
        <p class="ksp-about-empty">This store hasn't added a description yet.</p>
      @endif
    </div>

    {{-- Right: contact card --}}
    <div class="ksp-scard ksp-about-sidebar">
      <div class="ksp-scard-title">Contact {{ $store->name }}</div>

      {{-- WhatsApp + Message buttons --}}
      <div class="ksp-about-btns">
        @if(!empty($store->whatsapp ?? $store->phone))
          @php $waNum = preg_replace('/[^0-9]/', '', $store->whatsapp ?? $store->phone); @endphp
          <a href="https://wa.me/{{ $waNum }}?text={{ urlencode('Hi '.$store->name.', I found your store on Kegalle Marketplace. '.url('/store/'.$store->slug)) }}"
             target="_blank" rel="noopener noreferrer"
             class="ksp-btn-wa">
            {!! $iWa !!} WhatsApp
          </a>
        @endif
        <a href="/store/{{ $store->slug }}/message" class="ksp-btn-msg">{!! $iMail !!} Send Message</a>
      </div>

      {{-- Info rows --}}
      <div class="ksp-about-info">
        <div class="ksp-irow"><div class="ksp-iicon">{!! $iPin !!}</div><div class="ksp-ival">{{ $store->city ?? $store->address ?? 'Kegalle' }}</div></div>

        @if(!empty($store->phone))
          <div class="ksp-irow">
            <div class="ksp-iicon">{!! $iPhone !!}</div>
            <div class="ksp-ival">
              @auth
                <a href="tel:{{ \App\Helpers\PhoneHelper::tel($store->phone) }}" class="ksp-info-link-grn">{{ \App\Helpers\PhoneHelper::format($store->phone) }}</a>
              @else
                <a href="/login?redirect={{ urlencode('/store/'.$store->slug) }}" class="ksp-info-muted">{!! $iLock !!} Login to view</a>
              @endauth
            </div>
          </div>
        @endif

        @if(!empty($store->email))
          <div class="ksp-irow">
            <div class="ksp-iicon">{!! $iMail !!}</div>
            <div class="ksp-ival">
              @auth
                <a href="mailto:{{ $store->email }}" class="ksp-info-link-grn">{{ $store->email }}</a>
              @else
                <a href="/login?redirect={{ urlencode('/store/'.$store->slug) }}" class="ksp-info-muted">{!! $iLock !!} Login to view</a>
              @endauth
            </div>
          </div>
        @endif

        @if(!empty($store->website))
          <div class="ksp-irow">
            <div class="ksp-iicon">{!! $iGlobe !!}</div>
            <div class="ksp-ival"><a href="{{ $store->website }}" target="_blank" rel="noopener noreferrer" class="ksp-info-link-grn">{{ $store->website }}</a></div>
          </div>
        @endif

        @if(!empty($store->address))
          <div class="ksp-irow"><div class="ksp-iicon">{!! $iMap !!}</div><div class="ksp-ival ksp-info-muted">{{ $store->address }}</div></div>
        @endif

        <div class="ksp-irow"><div class="ksp-iicon">{!! $iCal !!}</div><div class="ksp-ival ksp-info-muted">Member since {{ $store->created_at?->format('M Y') ?? '—' }}</div></div>
      </div>

      {{-- Verified badges --}}
      @if($store->is_verified || $store->is_kurilla_verified)
        <div class="ksp-info-trust">
          @if($store->is_verified)
            <div class="ksp-verified-row"><span class="ksp-verified-tick">{!! $iCheck !!}</span> Verified Store</div>
          @endif
          @if($store->is_kurilla_verified)
            <div class="ksp-kurilla-row"><span>{!! $iStar !!}</span> Kurilla Verified</div>
          @endif
        </div>
      @endif
    </div>

  </div>
</div>

@if(session('success'))<div class="k-flash k-flash-success" id="flashMsg">{{ session('success') }}</div>@endif
@if(session('error'))<div class="k-flash k-flash-error" id="flashMsg">{{ session('error') }}</div>@endif

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
  var tabs = document.querySelectorAll('.ksp-tab[data-store-tab]');
  var mainWrap = document.getElementById('ksp-main-content');
  var aboutPanel = document.getElementById('ksp-about-panel');
  var overviewSec = document.getElementById('overview-section');
  var productsSec = document.getElementById('products');
  var reviewsSec = document.getElementById('reviews');
  var dealsSec   = document.getElementById('deals');

  function showTab(t) {
    var isAbout=(t==='about');
    if(mainWrap) mainWrap.classList.toggle('ksp-hidden',isAbout);
    if(aboutPanel) aboutPanel.classList.toggle('ksp-hidden',!isAbout);
    if(!isAbout){
      var showOv=(t==='overview'||t==='products');
      if(overviewSec) overviewSec.classList.toggle('ksp-hidden',!showOv);
      if(productsSec) productsSec.classList.toggle('ksp-hidden',!showOv);
      if(reviewsSec) reviewsSec.classList.toggle('ksp-hidden',t!=='reviews');
      if(dealsSec)   dealsSec.classList.toggle('ksp-hidden',t!=='deals');
    }
  }

  /* ── Deal countdown timers ── */
  function kDealCountdown() {
    document.querySelectorAll('.ksp-deal-timer[data-end]').forEach(function(el) {
      var end = new Date(el.dataset.end).getTime();
      var cd  = el.querySelector('.ksp-deal-cd');
      if (!cd) return;
      var diff = end - Date.now();
      if (diff <= 0) { cd.textContent = 'Expired'; return; }
      var d = Math.floor(diff/86400000);
      var h = Math.floor((diff%86400000)/3600000);
      var m = Math.floor((diff%3600000)/60000);
      var s = Math.floor((diff%60000)/1000);
      cd.textContent = (d>0?d+'d ':'')+String(h).padStart(2,'0')+':'+String(m).padStart(2,'0')+':'+String(s).padStart(2,'0');
    });
  }
  kDealCountdown();
  setInterval(kDealCountdown, 1000);

  tabs.forEach(function(tab){
    tab.addEventListener('click',function(e){
      e.preventDefault();
      tabs.forEach(function(x){x.classList.remove('active')});
      this.classList.add('active');
      showTab(this.dataset.storeTab);
    });
  });

  var stars = document.querySelectorAll('#storeStarInput .k-star-pick');
  stars.forEach(function(star){
    star.addEventListener('click',function(){
      var val=parseInt(this.dataset.val);
      document.getElementById('storeRatingInput').value=val;
      stars.forEach(function(s){s.style.color=parseInt(s.dataset.val)<=val?'#f59e0b':'#e5e7eb';});
    });
    star.addEventListener('mouseenter',function(){
      var val=parseInt(this.dataset.val);
      stars.forEach(function(s){s.style.color=parseInt(s.dataset.val)<=val?'#fbbf24':'#e5e7eb';});
    });
    star.addEventListener('mouseleave',function(){
      var cur=parseInt(document.getElementById('storeRatingInput').value)||0;
      stars.forEach(function(s){s.style.color=parseInt(s.dataset.val)<=cur?'#f59e0b':'#e5e7eb';});
    });
  });

  if(window.location.hash==='#reviews'){
    var rt=document.querySelector('[data-store-tab="reviews"]');
    if(rt) rt.click();
  }

  var flash=document.getElementById('flashMsg');
  if(flash) setTimeout(function(){flash.style.opacity='0';setTimeout(function(){flash.remove();},300);},4000);

  /* ── Copy link button ── */
  var copyBtn = document.getElementById('kspCopyBtn');
  if(copyBtn) {
    copyBtn.addEventListener('click', function() {
      var btn = this;
      navigator.clipboard.writeText(window.location.href).then(function(){
        btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Copied!';
        setTimeout(function(){ btn.innerHTML = '{{ $iClip }} Copy Link'; }, 2000);
      });
    });
  }
})();
</script>
@if($store->latitude && $store->longitude)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
  var el=document.getElementById('storeLocationMap');
  if(!el||typeof L==='undefined') return;
  var lat={{ (float)$store->latitude }},lng={{ (float)$store->longitude }};
  var map=L.map('storeLocationMap',{scrollWheelZoom:false}).setView([lat,lng],15);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap',maxZoom:19}).addTo(map);
  L.marker([lat,lng]).addTo(map).bindPopup({!! json_encode($store->name, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) !!});
  setTimeout(function(){map.invalidateSize();},300);
})();
</script>
@endif
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
  var lb=document.getElementById('kspLightbox');
  var lbImg=document.getElementById('kspLbImg');
  function openLb(src,alt){if(!lb||!lbImg)return;lbImg.src=src;lbImg.alt=alt||lbImg.dataset.defaultAlt||'';lb.style.display='flex';}
  if(lb) lb.addEventListener('click',function(){lb.style.display='none';});
  document.querySelectorAll('.ksp-zoomable').forEach(function(el){el.addEventListener('click',function(){openLb(this.src,this.alt);});});
  document.addEventListener('keydown',function(e){if(e.key==='Escape'&&lb)lb.style.display='none';});
  (function(){
    var wrap=document.getElementById('kspSortWrap'),trigger=document.getElementById('kspSortTrigger'),panel=document.getElementById('kspSortPanel'),label=document.getElementById('kspSortLabel'),valEl=document.getElementById('kspSortVal');
    if(!wrap)return;
    trigger.addEventListener('click',function(e){e.stopPropagation();var open=panel.style.display==='block';panel.style.display=open?'none':'block';trigger.classList.toggle('open',!open);});
    document.addEventListener('click',function(){panel.style.display='none';trigger.classList.remove('open');});
    panel.addEventListener('click',function(e){var item=e.target.closest('.kpd-item');if(!item)return;var val=item.dataset.value,lbl=item.dataset.label;label.textContent=lbl;wrap.querySelectorAll('.kpd-item').forEach(function(i){i.classList.toggle('selected',i===item);});panel.style.display='none';trigger.classList.remove('open');var u=new URL(window.location.href);if(val)u.searchParams.set('sort',val);else u.searchParams.delete('sort');window.location.href=u.toString();});
  })();
})();
</script>
@endpush

@endsection
