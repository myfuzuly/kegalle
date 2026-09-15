<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
@php $gsvCode = \App\Models\Setting::where('key','google_site_verification')->value('value'); @endphp
@if($gsvCode)<meta name="google-site-verification" content="{{ $gsvCode }}">@endif
<script nonce="{{ $cspNonce ?? '' }}">!function(){var t=localStorage.getItem('k_theme')||'light';document.documentElement.setAttribute('data-theme',t);}();</script>
@php
    $seoTitle = trim($__env->yieldContent('title')) ?: 'Kegalle Marketplace — Buy, Sell & Discover Locally';
    $seoDescription = trim($__env->yieldContent('meta_description')) ?: 'Kegalle Marketplace is the local online marketplace for the Kegalle district — buy, sell and discover products, services, stores and classified ads near you.';
    $seoImage = trim($__env->yieldContent('og_image')) ?: asset('images/kegalle-hero-clocktower-v3.png');
    $seoCanonical = trim($__env->yieldContent('canonical')) ?: request()->url();
    $seoNoindex = trim($__env->yieldContent('noindex')) === '1';
@endphp
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<link rel="canonical" href="{{ $seoCanonical }}">
@if($seoNoindex)
<meta name="robots" content="noindex,follow">
@endif

<!-- Open Graph -->
<meta property="og:type" content="{{ $__env->yieldContent('og_type', 'website') }}">
<meta property="og:site_name" content="Kegalle Marketplace">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:locale" content="en_LK">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@KegalleMarket">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">

<!-- Local SEO: Kegalle district, Sri Lanka -->
<meta name="geo.region" content="LK-52">
<meta name="geo.placename" content="Kegalle, Sri Lanka">
<meta name="geo.position" content="7.2513;80.3464">
<meta name="ICBM" content="7.2513, 80.3464">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"></noscript>
<link rel="preload" href="/fonts/inter-latin.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="/fonts/pjs-latin.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="/images/kegalle-town.webp" as="image" type="image/webp" fetchpriority="high">
<link rel="preload" href="/images/kegalle-town.png" as="image" fetchpriority="high">
<!-- Performance: DNS prefetch for key origins -->
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link rel="dns-prefetch" href="//kegalle.com">
@vite(['resources/css/app.css', 'resources/js/app.js'], 'vite-dist')

<link rel="preload" href="/css/kegalle-fonts.css?v=1" as="style">
<link rel="stylesheet" href="/css/kegalle-fonts.css?v=1" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="/css/kegalle-fonts.css?v=1"></noscript>
@if(request()->is('/'))
@endif
<link rel="shortcut icon" href="/favicon-v2.ico">
<link rel="icon" type="image/x-icon" href="/favicon-v2.ico">
<link rel="icon" type="image/png" href="/images/favicon.png?v=2">
<link rel="icon" type="image/png" sizes="192x192" href="/images/favicon.png?v=2">
<link rel="apple-touch-icon" href="/images/favicon.png?v=2">
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#1B5E20">
@stack('schema')
@stack('styles')
@if(config('services.analytics.ga_id'))
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.analytics.ga_id') }}"></script>
<script nonce="{{ $cspNonce ?? '' }}">window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ config('services.analytics.ga_id') }}');</script>
@endif
@if(config('services.analytics.pixel_id'))
<!-- Meta Pixel -->
<script nonce="{{ $cspNonce ?? '' }}">!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','{{ config('services.analytics.pixel_id') }}');fbq('track','PageView');</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ config('services.analytics.pixel_id') }}&ev=PageView&noscript=1"/></noscript>
@endif
</head>
<body>
<div id="k-preloader" aria-hidden="true">
  <div class="k-preloader-logo">
    <img src="/images/kegalle-logo.png" alt="Kegalle.com" class="k-preloader-logo-img" loading="eager" fetchpriority="high" decoding="sync">
  </div>
  <div class="k-preloader-bar"><div class="k-preloader-fill"></div></div>
</div>
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
  if(sessionStorage.getItem('k_visited')){
    document.getElementById('k-preloader').style.display='none';
    return;
  }
  sessionStorage.setItem('k_visited','1');
  var gone=false;
  function hide(){
    if(gone)return; gone=true;
    var el=document.getElementById('k-preloader');
    if(el){el.classList.add('k-preloader-hide');setTimeout(function(){el.style.display='none'},200);}
  }
  // Dismiss as soon as main CSS is applied — not waiting for images
  window.__kCssLoaded=hide;
  // Hard cap: dismiss after 400ms no matter what (CSS already cached, etc.)
  setTimeout(hide,400);
})();
</script>

{{-- Skip navigation for keyboard users --}}
<a href="#main-content" class="k-skip-link">Skip to main content</a>

<div class="k-sticky-header">
<header role="banner">
<nav class="k-navbar" role="navigation" aria-label="Main navigation">
    <div class="k-navbar-inner">
        <a href="/" class="k-logo">
            <img src="/images/kegalle-logo.png" alt="Kegalle.com" class="k-logo-img" width="180" height="60" fetchpriority="high" decoding="sync">
        </a>
        {{-- Vue search island --}}
        <div id="k-search-vue" data-q="{{ request('q') }}">
            {{-- No-JS fallback --}}
            <noscript>
                <form action="/listings" method="GET" class="ksa-form">
                    <input class="k-search-input" type="text" name="q" value="{{ request('q') }}" placeholder="Search for anything in Kegalle...">
                    <button class="k-search-btn" type="submit">Search</button>
                </form>
            </noscript>
        </div>
        @auth
        <a href="/dashboard/listings/create" class="k-mobile-post-btn">+ Post Free Ad</a>
        @else
        <a href="/register" class="k-mobile-post-btn">+ Post Free Ad</a>
        @endauth
        <button class="k-menu-btn" type="button" aria-label="Open menu" aria-expanded="false" id="kMenuBtn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <div class="k-nav-links" id="kNavLinks">
            <a href="/saved" class="k-nav-link" id="kNavSaved"><span aria-hidden="true">❤</span> Saved<span id="kNavSavedBadge" class="k-nav-badge k-hidden"></span></a>
            <button type="button" id="kDarkToggle" class="k-dark-toggle" aria-label="Toggle dark mode" title="Toggle dark mode">
                <svg id="kDarkIconMoon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                <svg id="kDarkIconSun"  viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
            </button>
            @auth
                <a href="/dashboard/chat" class="k-nav-link" title="Messages">
                    <span aria-hidden="true">💬</span> Chats
                    <span id="kChatBadge" class="k-nav-badge k-hidden"></span>
                </a>
                <a href="/dashboard" class="k-nav-link">Dashboard</a>
                {{-- <a href="/pricing" class="k-nav-link k-nav-premium-link"><span aria-hidden="true">⭐</span> Go Premium</a> --}}
                @if(in_array(auth()->user()->role ?? '', ['super_admin','admin']))
                    <a href="/admin" class="k-nav-link">Admin</a>
                @endif
                <form method="POST" action="/logout" class="k-nav-logout-form">@csrf<button type="submit" class="k-nav-link k-nav-logout-btn">Logout</button></form>
            @else
                <a href="/login" class="k-nav-link">Login</a>
                <a href="/register" class="k-btn k-btn-primary k-btn-sm">+ Post Free Ad</a>
            @endauth
            <a href="https://wa.me/94712930930?text={{ urlencode('Hi, I found you on Kegalle.com') }}" target="_blank" rel="noopener" class="k-nav-wa-btn" aria-label="WhatsApp us" title="Chat on WhatsApp"><svg viewBox="0 0 24 24" width="18" height="18" fill="#25D366" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></a>
        </div>

<!-- Mobile Slide-out Drawer -->
<div class="k-drawer-overlay" id="kDrawerOverlay"></div>
<aside class="k-drawer" id="kDrawer" role="dialog" aria-modal="true" aria-label="Navigation menu">
    <div class="k-drawer-header">
        <a href="/" class="k-logo">
            <img src="/images/kegalle-logo.png" alt="Kegalle.com" class="k-logo-img" width="180" height="60" loading="eager" decoding="sync">
        </a>
        <button class="k-drawer-close" id="kDrawerClose" aria-label="Close menu">✕</button>
    </div>
    <div class="k-drawer-body">
        <div class="k-drawer-section-label">Discover</div>
        <div class="k-drawer-section">
            <a href="/" class="k-drawer-link {{ request()->is('/') ? 'active' : '' }}"><span class="k-dlink-icon"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span><span>Home</span></a>
            <a href="/listings" class="k-drawer-link {{ request()->is('listings*') ? 'active' : '' }}"><span class="k-dlink-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg></span><span>All Ads</span></a>
            <a href="/stores" class="k-drawer-link {{ request()->is('stores*') || request()->is('store/*') ? 'active' : '' }}"><span class="k-dlink-icon"><svg viewBox="0 0 24 24"><path d="M3 3h18v3a3 3 0 01-3 3H6a3 3 0 01-3-3V3z"/><path d="M3 9v12h18V9"/><path d="M9 9v12"/><path d="M15 9v12"/></svg></span><span>Stores</span></a>
            <a href="/deals" class="k-drawer-link {{ request()->is('deals*') ? 'active' : '' }}"><span class="k-dlink-icon"><svg viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></span><span>Deals</span></a>
            <a href="/classified" class="k-drawer-link {{ request()->is('classified*') ? 'active' : '' }}"><span class="k-dlink-icon"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></span><span>Classified</span></a>
            <a href="/events" class="k-drawer-link {{ request()->is('events*') ? 'active' : '' }}"><span class="k-dlink-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span><span>Events</span></a>
            <a href="/saved" class="k-drawer-link {{ request()->is('saved') ? 'active' : '' }}" id="kDrawerSaved"><span class="k-dlink-icon"><svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg></span><span>Saved <span id="kDrawerSavedBadge" class="k-drawer-badge k-hidden"></span></span></a>
            <a href="/explore" class="k-drawer-link {{ request()->is('explore*') ? 'active' : '' }}"><span class="k-dlink-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg></span><span>Explore Kegalle</span></a>
        </div>
        <div class="k-drawer-divider"></div>
        <div class="k-drawer-section-label">More</div>
        <div class="k-drawer-section">
            <a href="/services" class="k-drawer-link {{ request()->is('services*') ? 'active' : '' }}"><span class="k-dlink-icon" aria-hidden="true">🛠</span><span>Services</span></a>
            <a href="/public-services" class="k-drawer-link {{ request()->is('public-services*') ? 'active' : '' }}"><span class="k-dlink-icon"><svg viewBox="0 0 24 24"><line x1="3" y1="22" x2="21" y2="22"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7"/></svg></span><span>Public Services</span></a>
            <a href="/categories" class="k-drawer-link {{ request()->is('categories*') ? 'active' : '' }}"><span class="k-dlink-icon"><svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg></span><span>Categories</span></a>
            <a href="/locations" class="k-drawer-link {{ request()->is('locations*') ? 'active' : '' }}"><span class="k-dlink-icon"><svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg></span><span>Locations</span></a>
            <a href="/blog" class="k-drawer-link {{ request()->is('blog*') ? 'active' : '' }}"><span class="k-dlink-icon"><svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></span><span>Blog</span></a>
            <a href="/contact-us" class="k-drawer-link {{ request()->is('contact-us') ? 'active' : '' }}"><span class="k-dlink-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></span><span>Contact Us</span></a>
        </div>
        <div class="k-drawer-divider"></div>
        <div class="k-drawer-section">
            @auth
                <a href="/dashboard" class="k-drawer-link"><span class="k-dlink-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg></span><span>Dashboard</span></a>
                @if(in_array(auth()->user()->role ?? '', ['super_admin','admin']))
                    <a href="/admin" class="k-drawer-link"><span class="k-dlink-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 10-16 0"/><circle cx="12" cy="8" r="2" fill="currentColor" stroke="none" style="opacity:.3"/></svg></span><span>Admin Panel</span></a>
                @endif
                <form method="POST" action="/logout">@csrf<button type="submit" class="k-drawer-link k-drawer-logout-btn"><span class="k-dlink-icon"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></span><span>Logout</span></button></form>
            @else
                <a href="/login" class="k-drawer-link"><span class="k-dlink-icon"><svg viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg></span><span>Login</span></a>
                <a href="/register" class="k-drawer-link"><span class="k-dlink-icon"><svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg></span><span>Register</span></a>
            @endauth
        </div>
    </div>
    <div class="k-drawer-footer">
        @auth
        <a href="/dashboard/listings/create" class="k-btn k-btn-primary">+ Post Free Ad</a>
        @else
        <a href="/register" class="k-btn k-btn-primary">+ Post Free Ad</a>
        @endauth
        <a href="/pricing" class="k-btn k-btn-premium-drawer"><span aria-hidden="true">⭐</span> Go Premium</a>
        <a href="https://wa.me/94712930930" target="_blank" rel="noopener" class="k-drawer-wa-link">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            +94 712 930 930
        </a>
    </div>
</aside>
    </div>
</nav>
</header>
<nav class="k-subnav" aria-label="Category navigation" aria-hidden="false" id="kSubnav">
    <div class="k-subnav-inner">
        <button class="k-subnav-menu-btn" type="button" aria-label="Open categories menu" aria-expanded="false" id="kSubnavMenuBtn">☰ Browse</button>
        <div class="k-subnav-links" id="kSubnavLinks">
            <a href="/" class="k-subnav-link {{ request()->is('/') ? 'active' : '' }}" tabindex="0">Home</a>
            <a href="/listings" class="k-subnav-link {{ request()->is('listings*') ? 'active' : '' }}">All Ads</a>
            <a href="/stores" class="k-subnav-link {{ request()->is('stores*') || request()->is('store/*') ? 'active' : '' }}">Stores</a>
            <a href="/classified" class="k-subnav-link {{ request()->is('classified*') ? 'active' : '' }}">Classified</a>
            <div class="k-subnav-more" id="kSubnavMore">
                <button type="button" class="k-subnav-link k-subnav-more-btn" id="kMoreBtn" aria-haspopup="true" aria-expanded="false">More <svg class="k-more-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg></button>
                <div class="k-subnav-dropdown" id="kSubnavDropdown" role="menu">
                    <a href="/deals" class="k-subnav-dd-link {{ request()->is('deals*') ? 'active' : '' }}" role="menuitem">🔥 Deals</a>
                    <a href="/blog" class="k-subnav-dd-link {{ request()->is('blog*') ? 'active' : '' }}" role="menuitem">📰 Blog</a>
                    <a href="/explore" class="k-subnav-dd-link {{ request()->is('explore*') ? 'active' : '' }}" role="menuitem">🗺 Explore Kegalle</a>
                    <a href="/events" class="k-subnav-dd-link {{ request()->is('events*') ? 'active' : '' }}" role="menuitem">🎉 Events</a>
                    <a href="/services" class="k-subnav-dd-link {{ request()->is('services*') ? 'active' : '' }}" role="menuitem">🛠 Services</a>
                    <a href="/public-services" class="k-subnav-dd-link {{ request()->is('public-services*') ? 'active' : '' }}" role="menuitem">🏛 Public Services</a>
                    <a href="/categories" class="k-subnav-dd-link {{ request()->is('categories*') ? 'active' : '' }}" role="menuitem">📂 Categories</a>
                    <a href="/locations" class="k-subnav-dd-link {{ request()->is('locations*') ? 'active' : '' }}" role="menuitem">📍 Locations</a>
                    <a href="/contact-us" class="k-subnav-dd-link {{ request()->is('contact-us') ? 'active' : '' }}" role="menuitem">📞 Contact Us</a>
                </div>
            </div>
        </div>
        <div class="k-subnav-right-group">
            <div class="k-subnav-trust">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                Trusted &amp; Secure
            </div>
            <a href="tel:+94713930930" class="k-subnav-hotline" aria-label="Call us at +94 713 930 930">
                <span class="k-hotline-icon-wrap" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#fff" aria-hidden="true"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"/></svg>
                </span>
                <span class="k-hotline-text">
                    <span class="k-hotline-label">Call Us Free</span>
                    <strong class="k-hotline-number">+94 713 930 930</strong>
                </span>
                <span class="k-hotline-pulse" aria-hidden="true"></span>
            </a>
        </div>
    </div>
</nav>
</div>{{-- end k-sticky-header --}}

<div id="k-toast-wrap" aria-live="polite" aria-atomic="true"></div>


<main id="main-content" tabindex="-1">
@yield('content')
</main>

<footer class="k-footer" role="contentinfo">
    {{-- Trust strip --}}
    <div class="k-footer-trust-strip">
        <div class="k-footer-trust-inner">
            <div class="k-footer-trust-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                <span>Verified Sellers</span>
            </div>
            <div class="k-footer-trust-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                <span>Safe &amp; Secure</span>
            </div>
            <div class="k-footer-trust-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span>100% Local — Kegalle</span>
            </div>
            <div class="k-footer-trust-item">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.06 1.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/></svg>
                <span>24/7 Support</span>
            </div>
        </div>
    </div>

    <div class="k-footer-inner">
        <div class="k-footer-grid">

            {{-- Brand column --}}
            <div class="k-footer-brand">
                <div class="k-footer-brand-logo">
                    <img src="/images/kegalle-logo.png" alt="Kegalle.com" class="k-footer-logo-img" loading="lazy" decoding="async">
                </div>
                <p class="k-footer-brand-desc">Sri Lanka's trusted local marketplace for the Kegalle district. Buy, sell and discover great deals from verified local sellers.</p>
                <div class="k-footer-socials">
                    <a href="https://wa.me/94712930930" target="_blank" rel="noopener" class="k-footer-social k-fsoc-wa" title="WhatsApp" aria-label="WhatsApp">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                    <a href="https://facebook.com/kegallecom" target="_blank" rel="noopener" class="k-footer-social k-fsoc-fb" title="Facebook" aria-label="Facebook">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.883v2.27h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>
                    </a>
                    <a href="https://instagram.com/kegalle.com" target="_blank" rel="noopener" class="k-footer-social k-fsoc-ig" title="Instagram" aria-label="Instagram">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                </div>
                <a href="tel:+94713930930" class="k-footer-phone">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"/></svg>
                    +94 713 930 930
                </a>
            </div>

            <div class="k-footer-col">
                <h3 class="k-footer-heading">Marketplace</h3>
                <a href="/listings">All Ads</a>
                <a href="/stores">Stores &amp; Business</a>
                <a href="/classified">Classified</a>
                <a href="/services">Services</a>
                <a href="/categories">Categories</a>
            </div>

            <div class="k-footer-col">
                <h3 class="k-footer-heading">Company</h3>
                <a href="/about-us">About Us</a>
                <a href="/terms-and-conditions">Terms &amp; Conditions</a>
                <a href="/privacy-policy">Privacy Policy</a>
                <a href="/cookie-policy">Cookie Policy</a>
                <a href="/contact-us">Contact Us</a>
            </div>

            <div class="k-footer-col">
                <h3 class="k-footer-heading">Support</h3>
                <a href="/blog">Blog</a>
                <a href="/help-center">Help Center</a>
                <a href="/how-to-buy">How to Buy &amp; Sell</a>
                <a href="/safety-tips">Safety Tips</a>
                <a href="/faq">FAQ</a>
            </div>

            <div class="k-footer-col">
                <h3 class="k-footer-heading">Newsletter</h3>
                <p class="k-footer-newsletter-note">Get the latest deals and local news straight to your inbox.</p>
                <form id="kNewsletterForm" class="k-footer-newsletter-form">
                    @csrf
                    <label for="kNewsletterEmail" class="sr-only">Email address</label>
                    <input id="kNewsletterEmail" type="email" name="email" placeholder="Your email address" class="k-footer-input" required aria-label="Email address for newsletter">
                    <button id="kNewsletterBtn" class="k-btn k-btn-primary k-btn-sm" type="submit">Subscribe</button>
                </form>
                <script nonce="{{ $cspNonce ?? '' }}">
                function kNewsletterSubmit(e){
                    e.preventDefault();
                    var btn=document.getElementById('kNewsletterBtn');
                    var email=document.getElementById('kNewsletterEmail').value;
                    var token=document.querySelector('#kNewsletterForm input[name=_token]').value;
                    btn.disabled=true; btn.textContent='…';
                    fetch('/newsletter/subscribe',{
                        method:'POST',
                        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token},
                        body:JSON.stringify({email:email})
                    }).then(function(r){return r.json();}).then(function(){
                        btn.textContent='✓ Done!';
                        document.getElementById('kNewsletterEmail').value='';
                        setTimeout(function(){btn.textContent='Subscribe';btn.disabled=false;},3000);
                    }).catch(function(){btn.textContent='Try again';btn.disabled=false;});
                }
                (function(){var f=document.getElementById('kNewsletterForm');if(f)f.addEventListener('submit',kNewsletterSubmit);})();
                </script>
                <p class="k-footer-newsletter-note k-footer-newsletter-privacy">🔒 No spam. Unsubscribe anytime.</p>
            </div>

        </div>

        <div class="k-footer-bottom">
            <span class="k-footer-copy">{{ date('Y') }} © Kegalle Marketplace. All rights reserved.</span>
            <span class="k-footer-made">Crafted with ❤ in Kegalle, Sri Lanka &nbsp;·&nbsp; By <a href="https://fidhaps.com" target="_blank" rel="noopener" class="k-footer-dev-link">FIDHAPS</a></span>
        </div>
    </div>
</footer>

{{-- Mobile bottom navigation --}}
<nav class="k-mobile-nav" aria-label="Mobile navigation">
    <a href="/" class="k-mnav-item {{ request()->is('/') ? 'active' : '' }}" {{ request()->is('/') ? 'aria-current="page"' : '' }}>
        <span class="k-mnav-icon"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
        <span>Home</span>
    </a>
    <a href="/listings" class="k-mnav-item {{ request()->is('listings*') ? 'active' : '' }}" {{ request()->is('listings*') ? 'aria-current="page"' : '' }}>
        <span class="k-mnav-icon"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
        <span>Browse</span>
    </a>
    <a href="{{ auth()->check() ? '/dashboard/listings/create' : '/register?intent=post' }}" class="k-mnav-post" aria-label="Post an ad"><span>+</span></a>
    <a href="{{ auth()->check() ? '/dashboard/chat' : '/login' }}" class="k-mnav-item {{ request()->is('dashboard/chat*') ? 'active' : '' }}">
        <span class="k-mnav-icon"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></span>
        <span>Chats</span>
        <span id="kMNavChatBadge" class="k-mnav-badge k-hidden"></span>
    </a>
    <a href="{{ auth()->check() ? '/dashboard' : '/login' }}" class="k-mnav-item {{ request()->is('dashboard') ? 'active' : '' }}" {{ request()->is('dashboard') ? 'aria-current="page"' : '' }}>
        <span class="k-mnav-icon"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
        <span>Account</span>
    </a>
</nav>

<button type="button" class="k-back-top" id="kBackTop" aria-label="Back to top"><svg viewBox="0 0 24 24"><polyline points="18 15 12 9 6 15"/></svg></button>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "Kegalle Marketplace",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('images/icon-192.png') }}",
    "sameAs": [
        "https://www.facebook.com/kegallecom",
        "https://wa.me/94712930930",
        "https://www.instagram.com/kegallecom"
    ]
}
</script>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "Kegalle Marketplace",
    "url": "{{ url('/') }}",
    "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ url('/listings') }}?q={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>

@stack('scripts')
{{-- Search autocomplete is now a Vue component (resources/js/components/SearchAutocomplete.vue) --}}
<script nonce="{{ $cspNonce ?? '' }}">
/* ── Dark Mode ── */
(function(){
    var html = document.documentElement;
    var btn  = document.getElementById('kDarkToggle');
    var moon = document.getElementById('kDarkIconMoon');
    var sun  = document.getElementById('kDarkIconSun');
    function apply(theme){
        html.setAttribute('data-theme', theme);
        if(moon) moon.style.display = theme === 'dark' ? 'none' : '';
        if(sun)  sun.style.display  = theme === 'dark' ? '' : 'none';
        localStorage.setItem('k_theme', theme);
    }
    var stored = localStorage.getItem('k_theme');
    if(stored){ apply(stored); }
    if(btn){ btn.addEventListener('click', function(){ apply(html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'); }); }
})();
</script>
<script nonce="{{ $cspNonce ?? '' }}">
(function(){var s=JSON.parse(localStorage.getItem('k_saved')||'[]'),c=s.length;if(!c)return;
var b1=document.getElementById('kNavSavedBadge'),b2=document.getElementById('kDrawerSavedBadge');
if(b1){b1.textContent=c;b1.style.display='';}
if(b2){b2.textContent=c;b2.style.display='inline';}
})();
</script>
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    function kToast(msg,type){
        var wrap=document.getElementById('k-toast-wrap');
        if(!wrap)return;
        var t=document.createElement('div');
        t.className='k-toast k-toast-'+type;
        t.setAttribute('role','alert');
        t.innerHTML='<span class="k-toast-msg">'+msg+'</span><button class="k-toast-close" aria-label="Close">✕</button>';
        t.querySelector('.k-toast-close').addEventListener('click',function(){t.remove();});
        wrap.appendChild(t);
        setTimeout(function(){t.classList.add('k-toast-show');},10);
        setTimeout(function(){t.classList.remove('k-toast-show');setTimeout(function(){t.remove();},300);},5000);
    }
    @if(session('success'))kToast(@json(session('success')),'success');@endif
    @if(session('error'))kToast(@json(session('error')),'error');@endif
    @if(session('warning'))kToast(@json(session('warning')),'warning');@endif
    @if($errors->any())kToast(@json($errors->first()),'error');@endif
    window.kToast=kToast;
})();
</script>
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var nav = document.getElementById('kSubnav');
    var links = nav ? nav.querySelectorAll('a') : [];
    function syncTabIndex(){
        var isMobile = window.innerWidth < 761;
        var isOpen = nav && nav.classList.contains('open');
        var hidden = isMobile && !isOpen;
        links.forEach(function(a){ a.tabIndex = hidden ? -1 : 0; });
        if(nav) nav.setAttribute('aria-hidden', hidden ? 'true' : 'false');
    }
    syncTabIndex();
    window.addEventListener('resize', syncTabIndex);
    var btn = document.getElementById('kSubnavMenuBtn');
    if(btn) btn.addEventListener('click', function(){ setTimeout(syncTabIndex, 50); });
})();
</script>
<!-- Navbar scroll shadow -->
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var nav = document.querySelector('.k-navbar');
    if(!nav) return;
    function onScroll(){ nav.classList.toggle('k-navbar--scrolled', window.scrollY > 8); }
    window.addEventListener('scroll', onScroll, {passive:true});
    onScroll();
})();
</script>
<!-- Footer accordion (mobile) -->
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    if(window.innerWidth > 760) return;
    document.querySelectorAll('.k-footer-col h3').forEach(function(h){
        h.setAttribute('aria-expanded', 'false');
        h.setAttribute('tabindex', '0');
        var col = h.parentElement;
        var links = col.querySelectorAll('a, form');
        var panelId = 'kFpanel-' + Math.random().toString(36).slice(2);
        var panel = document.createElement('div');
        panel.id = panelId;
        links.forEach(function(el){ panel.appendChild(el); });
        col.appendChild(panel);
        h.setAttribute('aria-controls', panelId);
        function toggle(){
            var open = col.classList.toggle('open');
            h.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
        h.addEventListener('click', toggle);
        h.addEventListener('keydown', function(e){ if(e.key==='Enter'||e.key===' '){ e.preventDefault(); toggle(); } });
    });
})();
</script>
<!-- Lazy-image blur-up fade-in -->
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    if(!('IntersectionObserver' in window))return;
    document.querySelectorAll('img[loading="lazy"]').forEach(function(img){
        img.classList.add('k-img-loading');
        if(img.complete && img.naturalWidth > 0){img.classList.remove('k-img-loading');img.classList.add('k-img-loaded');return;}
        img.addEventListener('load',function(){
            img.classList.remove('k-img-loading');
            img.classList.add('k-img-loaded');
        });
        img.addEventListener('error',function(){img.classList.remove('k-img-loading');});
    });
})();
</script>

@auth
<script nonce="{{ $cspNonce ?? '' }}">
/* ── Chat unread badge (navbar) ── */
(function(){
    function refreshBadge(){
        fetch('/api/chat/unread',{credentials:'same-origin'})
            .then(r=>r.ok?r.json():null)
            .then(d=>{
                if(!d) return;
                var n = d.unread || 0;
                var b = document.getElementById('kChatBadge');
                var mb = document.getElementById('kMNavChatBadge');
                if(b){  b.textContent=n>99?'99+':n; b.style.display=n?'':'none'; }
                if(mb){ mb.textContent=n>99?'99+':n; mb.style.display=n?'':'none'; }
            }).catch(function(){});
    }
    refreshBadge();
    var _badgeTimer = setInterval(refreshBadge, 60000);
    // Pause polling when tab is hidden; refresh immediately on return
    document.addEventListener('visibilitychange', function(){
        if(document.visibilityState === 'visible'){
            refreshBadge();
            if(!_badgeTimer) _badgeTimer = setInterval(refreshBadge, 60000);
        } else {
            clearInterval(_badgeTimer);
            _badgeTimer = null;
        }
    });
})();

</script>
@endauth
<script nonce="{{ $cspNonce ?? '' }}">
/* ── Web Push: register SW + subscribe (all users) ── */
(function(){
    var VAPID_PUBLIC_KEY = '{{ config("services.webpush.public_key","") }}';
    var IS_AUTH = {{ auth()->check() ? 'true' : 'false' }};

    if(!('serviceWorker' in navigator)) return;

    // Always register SW (needed for PWA + offline)
    navigator.serviceWorker.register('/sw.js', {scope:'/'}).then(function(reg){
        if(!VAPID_PUBLIC_KEY || !('PushManager' in window)) return;

        if(Notification.permission === 'granted'){
            if(IS_AUTH) subscribe(reg);
        } else if(Notification.permission === 'default'){
            showPushPrompt(reg);
        }
    }).catch(function(){});

    function urlB64ToUint8(s){
        var pad='='.repeat((4-s.length%4)%4),b64=(s+pad).replace(/-/g,'+').replace(/_/g,'/');
        var raw=atob(b64),out=new Uint8Array(raw.length);
        for(var i=0;i<raw.length;i++) out[i]=raw.charCodeAt(i);
        return out;
    }

    function subscribe(reg){
        reg.pushManager.getSubscription().then(function(sub){
            if(sub){ saveSub(sub); return; }
            reg.pushManager.subscribe({userVisibleOnly:true,applicationServerKey:urlB64ToUint8(VAPID_PUBLIC_KEY)})
                .then(saveSub).catch(function(){});
        });
    }

    function saveSub(sub){
        if(!IS_AUTH) return; // subscription saved on next login
        var json=sub.toJSON();
        fetch('/api/push/subscribe',{
            method:'POST',credentials:'same-origin',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||''},
            body:JSON.stringify({endpoint:json.endpoint,p256dh:json.keys.p256dh,auth:json.keys.auth}),
        }).catch(function(){});
    }

    function showPushPrompt(reg){
        if(localStorage.getItem('push_dismissed')) return;
        var msg = IS_AUTH
            ? 'Get notified when someone messages you or your listing is approved.'
            : 'Get alerts for price drops and new listings matching your searches.';
        var bar=document.createElement('div');
        bar.id='kPushBar';
        bar.className='k-push-bar';
        bar.setAttribute('role','dialog');
        bar.setAttribute('aria-label','Enable notifications');
        bar.innerHTML='<span class="k-push-bar__icon" aria-hidden="true">🔔</span>'
            +'<span class="k-push-bar__msg">'+msg+'</span>'
            +'<button id="kPushAllow" class="k-push-bar__allow">Allow</button>'
            +'<button id="kPushDismiss" class="k-push-bar__dismiss" aria-label="Dismiss">✕</button>';
        document.body.appendChild(bar);
        document.getElementById('kPushAllow').addEventListener('click',function(){
            Notification.requestPermission().then(function(p){
                bar.remove();
                if(p==='granted'){
                    if(IS_AUTH) subscribe(reg);
                    // Guest: permission granted, subscription saved after login
                }
            });
        });
        document.getElementById('kPushDismiss').addEventListener('click',function(){
            bar.remove();
            localStorage.setItem('push_dismissed','1');
        });
    }
})();
</script>
</body>
</html>
