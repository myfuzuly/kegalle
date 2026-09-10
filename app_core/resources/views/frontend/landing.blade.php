@extends('layouts.app')
@section('title', 'Kegalle Marketplace — Buy, Sell & Connect in Kegalle')
@section('meta_description', 'Sri Lanka\'s marketplace for Kegalle. Buy and sell electronics, vehicles, property, clothing and more. Post a free ad or open your online store today.')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/kegalle-landing.css') }}?v=1">
@endpush

@section('content')

{{-- ══ NAV ══════════════════════════════════════════════════════════ --}}
<nav class="lp-nav">
  <a href="/" class="lp-nav-logo">
    <div class="lp-nav-logo-mark">
      <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/></svg>
    </div>
    <span class="lp-nav-brand">Kegalle<span>.com</span></span>
  </a>
  <div class="lp-nav-links">
    <a href="/listings" class="lp-nav-link">Browse Ads</a>
    <a href="/stores" class="lp-nav-link">Stores</a>
    <a href="/services" class="lp-nav-link">Services</a>
    @auth
      <a href="/dashboard" class="lp-nav-btn-ghost">Dashboard</a>
    @else
      <a href="/login" class="lp-nav-btn-ghost">Sign In</a>
    @endauth
    <a href="/dashboard/listings/create" class="lp-nav-btn">Post Free Ad</a>
  </div>
</nav>

{{-- ══ HERO ═════════════════════════════════════════════════════════ --}}
<section class="lp-hero">
  <div class="lp-hero-dot-grid"></div>
  <div class="lp-hero-eyebrow">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
    Kegalle District, Sri Lanka
  </div>
  <h1 class="lp-hero-h1">
    Sri Lanka's Marketplace<br>for <span>Kegalle</span>
  </h1>
  <p class="lp-hero-sub">
    Buy, sell, and connect with local businesses. From electronics and vehicles to fresh produce and handmade crafts — everything Kegalle needs, in one place.
  </p>
  <div class="lp-hero-cta">
    <a href="/listings" class="lp-btn-primary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
      Browse Listings
    </a>
    <a href="/dashboard/listings/create" class="lp-btn-ghost">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14m-7-7h14"/></svg>
      Post a Free Ad
    </a>
  </div>
  <div class="lp-hero-stats">
    <div class="lp-stat">
      <span class="lp-stat-num">{{ number_format($totalListings) }}+</span>
      <span class="lp-stat-lbl">Active Listings</span>
    </div>
    <div class="lp-stat">
      <span class="lp-stat-num">{{ number_format($totalStores) }}+</span>
      <span class="lp-stat-lbl">Local Stores</span>
    </div>
    <div class="lp-stat">
      <span class="lp-stat-num">{{ number_format($totalUsers) }}+</span>
      <span class="lp-stat-lbl">Members</span>
    </div>
    <div class="lp-stat">
      <span class="lp-stat-num">Free</span>
      <span class="lp-stat-lbl">To Post Ads</span>
    </div>
  </div>
</section>

{{-- ══ HOW IT WORKS ════════════════════════════════════════════════ --}}
<div class="lp-how-bg">
<div class="lp-section">
  <div class="lp-section-eyebrow">Simple & Fast</div>
  <h2 class="lp-section-title">How Kegalle Marketplace Works</h2>
  <p class="lp-section-sub">No fees, no fuss. Post your ad in minutes and reach thousands of local buyers.</p>
  <div class="lp-steps">
    <div class="lp-step">
      <div class="lp-step-num">Step 01</div>
      <div class="lp-step-icon lp-step-icon-green">📝</div>
      <h3>Create Your Free Ad</h3>
      <p>Sign up in seconds. Choose your category, add photos, set your price, and your ad goes live immediately after a quick review.</p>
      <div class="lp-step-connector"></div>
    </div>
    <div class="lp-step">
      <div class="lp-step-num">Step 02</div>
      <div class="lp-step-icon lp-step-icon-blue">📱</div>
      <h3>Buyers Contact You Directly</h3>
      <p>Interested buyers reach out via WhatsApp or phone — no middleman, no commission. You deal directly and keep 100% of the sale.</p>
      <div class="lp-step-connector"></div>
    </div>
    <div class="lp-step">
      <div class="lp-step-num">Step 03</div>
      <div class="lp-step-icon lp-step-icon-gold">🤝</div>
      <h3>Meet, Deal, Done</h3>
      <p>Arrange a safe meeting point in Kegalle, complete the deal, and leave a review. It's that straightforward.</p>
    </div>
  </div>
</div>
</div>

{{-- ══ CATEGORIES ══════════════════════════════════════════════════ --}}
<div class="lp-section">
  <div class="lp-section-eyebrow">Browse by Category</div>
  <h2 class="lp-section-title">What Are You Looking For?</h2>
  <p class="lp-section-sub">From everyday essentials to rare finds — explore everything available in Kegalle right now.</p>
  @php
  $catEmojis = [
    'electronics'=>'📱','mobile'=>'📱','phones'=>'📱','vehicles'=>'🚗','cars'=>'🚗',
    'property'=>'🏠','real estate'=>'🏠','clothes'=>'👗','clothing'=>'👗','fashion'=>'👗',
    'furniture'=>'🛋️','home'=>'🏡','garden'=>'🌿','food'=>'🥦','agriculture'=>'🌾',
    'jobs'=>'💼','services'=>'🔧','animals'=>'🐾','pets'=>'🐾','sports'=>'⚽',
    'books'=>'📚','education'=>'📚','health'=>'💊','beauty'=>'💄','toys'=>'🧸',
    'computers'=>'💻','appliances'=>'🔌','tools'=>'🔧','jewellery'=>'💍','music'=>'🎵',
  ];
  function getCatEmoji($name, $emojis){
    $n = strtolower($name);
    foreach($emojis as $k=>$e){ if(str_contains($n,$k)) return $e; }
    return '🏷️';
  }
  @endphp
  <div class="lp-cat-grid">
    @foreach($categories->take(15) as $cat)
    <a href="/listings?category={{ $cat->id }}" class="lp-cat-card">
      <span class="lp-cat-emoji">{{ getCatEmoji($cat->name, $catEmojis) }}</span>
      <div class="lp-cat-name">{{ $cat->name }}</div>
      @if($cat->listings_count > 0)
      <div class="lp-cat-count">{{ number_format($cat->listings_count) }} ads</div>
      @endif
    </a>
    @endforeach
    <a href="/listings" class="lp-cat-card" style="border-style:dashed;border-color:var(--lp-green);background:#f0fdf4">
      <span class="lp-cat-emoji">🔍</span>
      <div class="lp-cat-name" style="color:var(--lp-green)">All Categories</div>
    </a>
  </div>
</div>

{{-- ══ WHY KEGALLE MARKETPLACE ═════════════════════════════════════ --}}
<div class="lp-how-bg">
<div class="lp-section">
  <div class="lp-section-eyebrow">Why Us</div>
  <h2 class="lp-section-title">Built for the Kegalle Community</h2>
  <p class="lp-section-sub">We're not a generic classifieds site. We're specifically built for buyers and sellers right here in Kegalle District.</p>
  <div class="lp-features">
    <div class="lp-feature">
      <div class="lp-feature-icon">📍</div>
      <div>
        <h4>Hyper-Local Focus</h4>
        <p>Every listing is from someone in Kegalle. No irrelevant ads from Colombo or other districts cluttering your search.</p>
      </div>
    </div>
    <div class="lp-feature">
      <div class="lp-feature-icon">💬</div>
      <div>
        <h4>WhatsApp-First Contact</h4>
        <p>All contact happens via WhatsApp or phone — the way people in Kegalle already communicate. No login needed to browse.</p>
      </div>
    </div>
    <div class="lp-feature">
      <div class="lp-feature-icon">🆓</div>
      <div>
        <h4>Free to Post, Always</h4>
        <p>Post your ad for free. No hidden charges, no listing fees. Upgrade to premium only if you want more visibility.</p>
      </div>
    </div>
    <div class="lp-feature">
      <div class="lp-feature-icon">🏪</div>
      <div>
        <h4>Open Your Own Store</h4>
        <p>Businesses can create a full online storefront with product catalogues, custom branding, and customer reviews.</p>
      </div>
    </div>
    <div class="lp-feature">
      <div class="lp-feature-icon">🔧</div>
      <div>
        <h4>Services Directory</h4>
        <p>Find plumbers, electricians, tutors, and more. Or list your own services and get discovered by local customers.</p>
      </div>
    </div>
    <div class="lp-feature">
      <div class="lp-feature-icon">🛡️</div>
      <div>
        <h4>Verified Listings</h4>
        <p>Every ad is reviewed before it goes live. Phone-verified accounts mean you're dealing with real people.</p>
      </div>
    </div>
  </div>
</div>
</div>

{{-- ══ SELLER CTA STRIP ════════════════════════════════════════════ --}}
<section class="lp-seller-strip">
  <div class="lp-seller-strip-inner">
    <h2>Have Something to Sell?</h2>
    <p>Your item deserves a local buyer. Post your ad for free in under 2 minutes — no account needed to browse, and buyers contact you directly.</p>
    <div class="lp-seller-btns">
      <a href="/dashboard/listings/create" class="lp-seller-btn-white">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14m-7-7h14"/></svg>
        Post a Free Ad
      </a>
      <a href="/dashboard/stores/create" class="lp-seller-btn-outline">
        🏪 Open a Store
      </a>
      <a href="/dashboard/services/create" class="lp-seller-btn-outline">
        🔧 List a Service
      </a>
    </div>
  </div>
</section>

{{-- ══ REVIEWS ═════════════════════════════════════════════════════ --}}
@if($siteReviews->isNotEmpty())
<div class="lp-reviews-bg">
<div class="lp-section">
  <div class="lp-section-eyebrow">Community Reviews</div>
  <h2 class="lp-section-title">Trusted by Kegalle</h2>
  <div class="lp-reviews-grid">
    @foreach($siteReviews as $review)
    <div class="lp-review-card">
      <div class="lp-review-stars">★★★★★</div>
      <p class="lp-review-text">"{{ Str::limit($review->comment, 160) }}"</p>
      <div class="lp-review-author">
        <div class="lp-review-av">{{ strtoupper(substr(optional($review->user)->name ?? 'U', 0, 1)) }}</div>
        <div>
          <div class="lp-review-name">{{ optional($review->user)->name ?? 'Member' }}</div>
          <div class="lp-review-loc">Kegalle, Sri Lanka</div>
        </div>
      </div>
    </div>
    @endforeach
  </div>
</div>
</div>
@endif

{{-- ══ STORE CTA ════════════════════════════════════════════════════ --}}
<div style="padding:64px 0;background:#fff">
<div class="lp-store-cta">
  <div class="lp-store-cta-text">
    <div class="lp-section-eyebrow">For Business Owners</div>
    <h2>Open Your Online Store — Free</h2>
    <p>Running a shop in Kegalle? Create your free online storefront and reach every customer in the district. No technical knowledge needed.</p>
    <div class="lp-store-features">
      <div class="lp-store-feature-item">Your own branded store page</div>
      <div class="lp-store-feature-item">Unlimited product listings</div>
      <div class="lp-store-feature-item">Customer enquiries via WhatsApp</div>
      <div class="lp-store-feature-item">Reviews and ratings from buyers</div>
    </div>
    <a href="/dashboard/stores/create" class="lp-btn-primary" style="display:inline-flex">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14m-7-7h14"/></svg>
      Create My Free Store
    </a>
  </div>
  <div class="lp-store-cta-img">🏪</div>
</div>
</div>

{{-- ══ FOOTER ═══════════════════════════════════════════════════════ --}}
<footer class="lp-footer">
  <div class="lp-footer-inner">
    <div class="lp-footer-brand">
      <div class="lp-nav-logo">
        <div class="lp-nav-logo-mark">
          <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/></svg>
        </div>
        <span class="lp-nav-brand">Kegalle<span>.com</span></span>
      </div>
      <p>Sri Lanka's marketplace for Kegalle District. Buy, sell, and connect with local businesses and services.</p>
    </div>
    <div class="lp-footer-col">
      <h4>Marketplace</h4>
      <a href="/listings">All Listings</a>
      <a href="/stores">Stores</a>
      <a href="/services">Services</a>
      <a href="/deals">Deals</a>
    </div>
    <div class="lp-footer-col">
      <h4>Sellers</h4>
      <a href="/dashboard/listings/create">Post Free Ad</a>
      <a href="/dashboard/stores/create">Open a Store</a>
      <a href="/dashboard/services/create">List a Service</a>
      <a href="/register">Sign Up Free</a>
    </div>
    <div class="lp-footer-col">
      <h4>Help</h4>
      <a href="/contact">Contact Us</a>
      <a href="/privacy">Privacy Policy</a>
      <a href="/terms">Terms of Use</a>
    </div>
  </div>
  <div class="lp-footer-bottom">
    <span>© {{ date('Y') }} Kegalle Marketplace. All rights reserved.</span>
    <span>Made with ❤️ for Kegalle, Sri Lanka</span>
  </div>
</footer>
@endsection
