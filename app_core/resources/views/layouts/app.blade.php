<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $seoTitle = trim($__env->yieldContent('title')) ?: 'Kegalle Marketplace — Buy, Sell & Discover Locally';
    $seoDescription = trim($__env->yieldContent('meta_description')) ?: 'Kegalle Marketplace is the local online marketplace for the Kegalle district — buy, sell and discover products, services, stores and classified ads near you.';
    $seoImage = trim($__env->yieldContent('og_image')) ?: asset('images/kegalle-hero-tower.jpg');
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
<meta property="og:type" content="website">
<meta property="og:site_name" content="Kegalle Marketplace">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:locale" content="en_US">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
<noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"></noscript>
<link rel="icon" href="/images/kegalle-placeholder.png">
<link rel="stylesheet" href="/css/kurulla-main.css?v=125">
<link rel="stylesheet" href="/css/kurulla-events.css?v=5">
@stack('schema')
@stack('styles')
</head>
<body>

<nav class="k-navbar">
    <div class="k-navbar-inner">
        <a href="/" class="k-logo">
            <div class="k-logo-icon">K</div>
            <div class="k-logo-text">
                <strong>Kegalle</strong>
                <small>Buy · Sell · Discover</small>
            </div>
        </a>
        <form class="k-search-wrap" action="/listings" method="GET" autocomplete="off">
            <span class="k-search-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
            <input class="k-search-input" type="text" name="q" id="kSearchInput" value="{{ request('q') }}" placeholder="Search for anything in Kegalle...">
            <button class="k-search-btn" type="submit">Search</button>
            <div class="k-search-dropdown" id="kSearchDropdown"></div>
        </form>
        <a href="/register" class="k-mobile-post-btn">+ Post Free Ad</a>
        <button class="k-menu-btn" type="button" aria-label="Open menu" aria-expanded="false" id="kMenuBtn">☰</button>
        <div class="k-nav-links" id="kNavLinks">
            <a href="/blog" class="k-nav-link">Blog</a>
            <a href="/saved" class="k-nav-link" id="kNavSaved" style="position:relative">❤ Saved<span id="kNavSavedBadge" style="display:none;position:absolute;top:-4px;right:-10px;background:var(--k-red);color:#fff;font-size:9px;padding:1px 5px;border-radius:8px;font-weight:700"></span></a>
            @auth
                <a href="/dashboard" class="k-nav-link">Dashboard</a>
                @if(in_array(auth()->user()->role ?? '', ['super_admin','admin']))
                    <a href="/admin" class="k-nav-link">Admin</a>
                @endif
                <form method="POST" action="/logout" style="display:inline">@csrf<button type="submit" class="k-nav-link" style="background:none;border:none;cursor:pointer">Logout</button></form>
            @else
                <a href="/login" class="k-nav-link">Login</a>
                <a href="/register" class="k-btn k-btn-primary k-btn-sm">+ Post Free Ad</a>
            @endauth
        </div>

<!-- Mobile Slide-out Drawer -->
<div class="k-drawer-overlay" id="kDrawerOverlay"></div>
<aside class="k-drawer" id="kDrawer">
    <div class="k-drawer-header">
        <a href="/" class="k-logo" style="text-decoration:none">
            <div class="k-logo-icon">K</div>
            <div class="k-logo-text"><strong>Kegalle</strong><small>Buy · Sell · Discover</small></div>
        </a>
        <button class="k-drawer-close" id="kDrawerClose" aria-label="Close menu">✕</button>
    </div>
    <div class="k-drawer-body">
        <div class="k-drawer-section">
            <a href="/" class="k-drawer-link {{ request()->is('/') ? 'active' : '' }}">🏠 Home</a>
            <a href="/listings" class="k-drawer-link {{ request()->is('listings*') ? 'active' : '' }}">📋 All Ads</a>
            <a href="/stores" class="k-drawer-link {{ request()->is('stores*') || request()->is('store/*') ? 'active' : '' }}">🏪 Stores</a>
            <a href="/explore" class="k-drawer-link {{ request()->is('explore*') ? 'active' : '' }}">🗺 Explore Kegalle</a>
            <a href="/classified" class="k-drawer-link {{ request()->is('classified*') ? 'active' : '' }}">📰 Classified</a>
            <a href="/deals" class="k-drawer-link {{ request()->is('deals*') ? 'active' : '' }}">🔥 Deals</a>
            <a href="/saved" class="k-drawer-link {{ request()->is('saved') ? 'active' : '' }}" id="kDrawerSaved">❤ Saved <span id="kDrawerSavedBadge" style="display:none;background:var(--k-red);color:#fff;font-size:10px;padding:1px 6px;border-radius:8px;font-weight:700;margin-left:4px"></span></a>
            <a href="/events" class="k-drawer-link {{ request()->is('events*') ? 'active' : '' }}">🎉 Events</a>
        </div>
        <div class="k-drawer-divider"></div>
        <div class="k-drawer-section">
            <a href="/government-services" class="k-drawer-link {{ request()->is('government-services*') ? 'active' : '' }}">🏛 Gov Services</a>
            <a href="/categories" class="k-drawer-link {{ request()->is('categories*') ? 'active' : '' }}">📂 Categories</a>
            <a href="/locations" class="k-drawer-link {{ request()->is('locations*') ? 'active' : '' }}">📍 Locations</a>
            <a href="/blog" class="k-drawer-link {{ request()->is('blog*') ? 'active' : '' }}">✍ Blog</a>
        </div>
        <div class="k-drawer-divider"></div>
        <div class="k-drawer-section">
            @auth
                <a href="/dashboard" class="k-drawer-link">📊 Dashboard</a>
                @if(in_array(auth()->user()->role ?? '', ['super_admin','admin']))
                    <a href="/admin" class="k-drawer-link">⚙ Admin Panel</a>
                @endif
                <form method="POST" action="/logout">@csrf<button type="submit" class="k-drawer-link" style="width:100%;text-align:left;border:none;background:none;cursor:pointer;font:inherit;color:inherit;padding:inherit">🚪 Logout</button></form>
            @else
                <a href="/login" class="k-drawer-link">🔑 Login</a>
                <a href="/register" class="k-drawer-link">📝 Register</a>
            @endauth
        </div>
    </div>
    <div class="k-drawer-footer">
        <a href="/register" class="k-btn k-btn-primary" style="width:100%;justify-content:center;font-size:15px;height:48px">+ Post Free Ad</a>
    </div>
</aside>
    </div>
</nav>
<nav class="k-subnav">
    <div class="k-subnav-inner">
        <button class="k-subnav-menu-btn" type="button" aria-label="Open categories menu" aria-expanded="false" id="kSubnavMenuBtn">☰ Browse</button>
        <div class="k-subnav-links" id="kSubnavLinks">
            <a href="/" class="k-subnav-link {{ request()->is('/') ? 'active' : '' }}">Home</a>
            <a href="/listings" class="k-subnav-link {{ request()->is('listings*') ? 'active' : '' }}">All Ads</a>
            <a href="/stores" class="k-subnav-link {{ request()->is('stores*') || request()->is('store/*') ? 'active' : '' }}">Stores</a>
            <a href="/explore" class="k-subnav-link {{ request()->is('explore*') ? 'active' : '' }}">Explore Kegalle</a>
            <a href="/classified" class="k-subnav-link {{ request()->is('classified*') ? 'active' : '' }}">Classified</a>
            <a href="/deals" class="k-subnav-link {{ request()->is('deals*') ? 'active' : '' }}" style="position:relative">🔥 Deals<span style="position:absolute;top:-6px;right:-14px;background:var(--k-red);color:#fff;font-size:9px;padding:1px 4px;border-radius:6px;font-weight:700">Hot</span></a>
            <a href="/events" class="k-subnav-link {{ request()->is('events*') ? 'active' : '' }}">Events</a>
            <a href="/government-services" class="k-subnav-link {{ request()->is('government-services*') ? 'active' : '' }}">Gov Services</a>
            <a href="/categories" class="k-subnav-link {{ request()->is('categories*') ? 'active' : '' }}">Categories</a>
            <a href="/locations" class="k-subnav-link {{ request()->is('locations*') ? 'active' : '' }}">Locations</a>
            <a href="/blog" class="k-subnav-link {{ request()->is('blog*') ? 'active' : '' }}">Blog</a>
        </div>
        <div class="k-subnav-trust">✓ Trusted local marketplace</div>
    </div>
</nav>

<main>
@if(session('success'))
    <div class="container" style="padding-top:18px"><div class="k-alert" style="background:var(--k-primary-xlight);color:var(--k-primary-hover);padding:14px 18px;border-radius:var(--k-radius);font-weight:600">{{ session('success') }}</div></div>
@endif
@if($errors->any())
    <div class="container" style="padding-top:18px"><div class="k-alert" style="background:var(--k-red-light);color:var(--k-red);padding:14px 18px;border-radius:var(--k-radius);font-weight:600">{{ $errors->first() }}</div></div>
@endif
@yield('content')
</main>

<footer class="k-footer">
    <div class="k-footer-inner">
        <div class="k-footer-grid">
            <div class="k-footer-brand">
                <strong>Kegalle</strong>
                <p>Discover great deals, trusted sellers and local stores across Kegalle.</p>
                <div class="k-footer-socials">
                    <a href="#" class="k-footer-social">f</a>
                    <a href="#" class="k-footer-social">in</a>
                    <a href="#" class="k-footer-social">yt</a>
                    <a href="#" class="k-footer-social">t</a>
                </div>
            </div>
            <div class="k-footer-col">
                <h4>Marketplace</h4>
                <a href="/">Home</a><a href="/listings">All Ads</a><a href="/stores">Stores / Business</a><a href="/listings?type=product">Products</a><a href="/classified">Classified</a><a href="/categories">Categories</a><a href="/locations">Locations</a><a href="/blog">Blog</a>
            </div>
            <div class="k-footer-col">
                <h4>Company</h4>
                <a href="/about-us">About Us</a><a href="/terms-and-conditions">Terms & Conditions</a><a href="/privacy-policy">Privacy Policy</a><a href="/contact-us">Contact Us</a>
            </div>
            <div class="k-footer-col">
                <h4>Support</h4>
                <a href="/help-center">Help Center</a><a href="/how-to-buy">How to Buy</a><a href="/how-to-sell">How to Sell</a><a href="/safety-tips">Safety Tips</a><a href="/faq">FAQ</a>
            </div>
            <div class="k-footer-col">
                <h4>Newsletter</h4>
                <p style="font-size:13px;margin-bottom:12px">Get the latest updates and best deals straight to your inbox.</p>
                <form style="display:flex;gap:8px" method="POST" action="#">
                    @csrf
                    <input type="email" name="email" placeholder="Enter your email" class="k-footer-input">
                    <button class="k-btn k-btn-primary k-btn-sm" type="submit">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="k-footer-bottom">
            <span>© {{ date('Y') }} Kegalle Marketplace. All rights reserved.</span>
            <div class="k-footer-links-inline">
                <a href="/safety-tips">Safety Tips</a>
                <a href="/how-to-buy">How to Buy</a>
                <a href="/faq">FAQ</a>
            </div>
        </div>
    </div>
</footer>

{{-- Mobile bottom navigation --}}
<nav class="k-mobile-nav" aria-label="Mobile navigation">
    <a href="/" class="k-mnav-item {{ request()->is('/') ? 'active' : '' }}"><span class="k-mnav-icon">🏠</span><span>Home</span></a>
    <a href="/listings" class="k-mnav-item {{ request()->is('listings*') ? 'active' : '' }}"><span class="k-mnav-icon">🔍</span><span>Browse</span></a>
    <a href="{{ auth()->check() ? '/dashboard/listings/create' : '/login' }}" class="k-mnav-post" aria-label="Post an ad"><span>+</span></a>
    <a href="{{ auth()->check() ? '/dashboard/chat' : '/login' }}" class="k-mnav-item {{ request()->is('dashboard/chat*') ? 'active' : '' }}"><span class="k-mnav-icon">💬</span><span>Chats</span></a>
    <a href="{{ auth()->check() ? '/dashboard' : '/login' }}" class="k-mnav-item {{ request()->is('dashboard') ? 'active' : '' }}"><span class="k-mnav-icon">👤</span><span>Account</span></a>
</nav>

<button type="button" class="k-back-top" id="kBackTop" aria-label="Back to top"><svg viewBox="0 0 24 24"><polyline points="18 15 12 9 6 15"/></svg></button>
<a href="https://wa.me/94706930930?text={{ urlencode('Message from Kegalle.com') }}" target="_blank" rel="noopener" class="k-whatsapp-btn" aria-label="Chat with us on WhatsApp"><svg viewBox="0 0 24 24" width="22" height="22" fill="#fff"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg><span class="k-wa-label">WhatsApp</span></a>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "Kegalle Marketplace",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('images/kegalle-placeholder.png') }}",
    "telephone": "+94766930930",
    "sameAs": []
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

<script>
(function () {
    var btn = document.getElementById('kMenuBtn');
    var nav = document.getElementById('kNavLinks');
    var drawer = document.getElementById('kDrawer');
    var drawerOverlay = document.getElementById('kDrawerOverlay');
    var drawerClose = document.getElementById('kDrawerClose');

    function openDrawer() { drawer.classList.add('open'); drawerOverlay.classList.add('open'); document.body.style.overflow = 'hidden'; btn.setAttribute('aria-expanded', 'true'); }
    function closeDrawer() { drawer.classList.remove('open'); drawerOverlay.classList.remove('open'); document.body.style.overflow = ''; btn.setAttribute('aria-expanded', 'false'); }

    if (btn) btn.addEventListener('click', openDrawer);
    if (drawerClose) drawerClose.addEventListener('click', closeDrawer);
    if (drawerOverlay) drawerOverlay.addEventListener('click', closeDrawer);

    // Desktop nav dropdown fallback
    if (btn && nav) {
        btn.addEventListener('click', function () {
            var isOpen = nav.classList.toggle('open');
            btn.textContent = isOpen ? '✕' : '☰';
        });
    }

    var subnavBtn = document.getElementById('kSubnavMenuBtn');
    var subnavLinks = document.getElementById('kSubnavLinks');
    if (subnavBtn && subnavLinks) {
        subnavBtn.addEventListener('click', function () {
            var isOpen = subnavLinks.classList.toggle('open');
            subnavBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            subnavBtn.textContent = isOpen ? '✕ Browse' : '☰ Browse';
        });
    }

    document.addEventListener('click', function (e) {
        var prevBtn = e.target.closest('[data-carousel-prev]');
        var nextBtn = e.target.closest('[data-carousel-next]');
        var trigger = prevBtn || nextBtn;
        if (!trigger) return;

        var track = document.getElementById(trigger.getAttribute(prevBtn ? 'data-carousel-prev' : 'data-carousel-next'));
        if (!track) return;

        var scrollAmount = track.clientWidth;
        track.scrollBy({ left: prevBtn ? -scrollAmount : scrollAmount, behavior: 'smooth' });
        pauseCarouselAutoplay(track);
    });

    var autoplayTimers = {};
    var autoplayPauseTimers = {};

    function pauseCarouselAutoplay(track) {
        if (autoplayTimers[track.id]) {
            clearInterval(autoplayTimers[track.id]);
            autoplayTimers[track.id] = null;
        }
        if (autoplayPauseTimers[track.id]) {
            clearTimeout(autoplayPauseTimers[track.id]);
        }
        autoplayPauseTimers[track.id] = setTimeout(function () {
            startCarouselAutoplay(track);
        }, 6000);
    }

    function startCarouselAutoplay(track) {
        if (autoplayTimers[track.id]) return;
        var item = track.querySelector('.k-carousel-item');
        if (!item) return;
        var itemWidth = item.offsetWidth + 16;

        autoplayTimers[track.id] = setInterval(function () {
            var maxScroll = track.scrollWidth - track.clientWidth;
            if (maxScroll <= 4) return;
            if (track.scrollLeft >= maxScroll - 4) {
                track.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                track.scrollBy({ left: itemWidth, behavior: 'smooth' });
            }
        }, 6000);
    }

    document.querySelectorAll('.k-carousel[id]').forEach(function (track) {
        startCarouselAutoplay(track);
        track.addEventListener('mouseenter', function () { pauseCarouselAutoplay(track); });
        track.addEventListener('mouseleave', function () { startCarouselAutoplay(track); });
        track.addEventListener('touchstart', function () { pauseCarouselAutoplay(track); }, { passive: true });
        track.addEventListener('focusin', function () { pauseCarouselAutoplay(track); });
        track.addEventListener('focusout', function () { startCarouselAutoplay(track); });
    });

    // Scroll fade-in
    var fadeEls = document.querySelectorAll('.k-cta-banner, .k-explore-card, .k-cat-pill, .k-blog-card, .k-listing-card, .k-store-card-v2, .k-deal-card-home');
    if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        fadeEls.forEach(function (el, i) { el.classList.add('k-fade-in'); el.style.transitionDelay = (i % 6) * 80 + 'ms'; });
        var fadeObs = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) { e.target.classList.add('k-visible'); fadeObs.unobserve(e.target); }
            });
        }, { threshold: 0, rootMargin: '0px 0px 60px 0px' });
        fadeEls.forEach(function (el) { fadeObs.observe(el); });
    }

    // Stop shimmer when images load + fallback for stuck shimmer
    document.querySelectorAll('.k-listing-img').forEach(function(el){
        var img = el.querySelector('img');
        if(img){
            if(img.complete && img.naturalWidth > 0){
                el.style.animation = 'none';
            } else {
                img.addEventListener('load', function(){ el.style.animation = 'none'; });
            }
        }
    });
    setTimeout(function(){
        document.querySelectorAll('.k-listing-img').forEach(function(el){
            el.style.animation = 'none';
            var img = el.querySelector('img');
            if(!img || (img.complete && img.naturalWidth === 0)){
                el.classList.add('k-img-failed');
                if(img) img.remove();
            }
        });
        document.querySelectorAll('.k-deal-card-img').forEach(function(el){
            var img = el.querySelector('img');
            if(!img || (img.complete && img.naturalWidth === 0)){
                el.classList.add('k-img-failed');
                if(img) img.remove();
            }
        });
    }, 15000);

    // Back to top
    var btt = document.getElementById('kBackTop');
    if (btt) {
        var bttVisible = false;
        window.addEventListener('scroll', function () {
            var show = window.scrollY > 400;
            if (show !== bttVisible) { bttVisible = show; btt.classList.toggle('k-visible', show); }
        }, { passive: true });
        btt.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
    }
    // Search autocomplete
    var sInput = document.getElementById('kSearchInput');
    var sDrop = document.getElementById('kSearchDropdown');
    var sTimer = null;
    if (sInput && sDrop) {
        sInput.addEventListener('input', function () {
            clearTimeout(sTimer);
            var q = sInput.value.trim();
            if (q.length < 2) { sDrop.classList.remove('open'); return; }
            sTimer = setTimeout(function () {
                fetch('/api/search-suggestions?q=' + encodeURIComponent(q))
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (!data.length) { sDrop.classList.remove('open'); return; }
                        sDrop.innerHTML = data.map(function (item) {
                            return '<a class="k-search-item" href="' + item.url + '">' +
                                '<span class="k-search-item-title">' + item.title + '</span>' +
                                (item.category ? '<span class="k-search-item-cat">' + item.category + '</span>' : '') +
                                '</a>';
                        }).join('');
                        sDrop.classList.add('open');
                    }).catch(function () { sDrop.classList.remove('open'); });
            }, 300);
        });
        sInput.addEventListener('focus', function () {
            if (sDrop.children.length && sInput.value.trim().length >= 2) sDrop.classList.add('open');
        });
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.k-search-wrap')) sDrop.classList.remove('open');
        });
    }

    // Deal card countdowns
    var dealTimers = document.querySelectorAll('.k-deal-card-timer[data-deal-end]');
    if (dealTimers.length) {
        function pad2(n){return n<10?'0'+n:n}
        function updateDealTimers(){
            var now = new Date();
            dealTimers.forEach(function(el){
                var end = new Date(el.dataset.dealEnd);
                var diff = Math.max(0, end - now);
                var cd = el.querySelector('.k-deal-card-countdown');
                if(!cd) return;
                if(diff <= 0){ cd.textContent = 'Expired'; cd.classList.add('k-deal-urgent'); return; }
                var d = Math.floor(diff/864e5);
                var h = Math.floor(diff%864e5/36e5);
                var m = Math.floor(diff%36e5/6e4);
                var s = Math.floor(diff%6e4/1e3);
                if(d > 0) cd.textContent = d+'d '+pad2(h)+'h '+pad2(m)+'m '+pad2(s)+'s';
                else cd.textContent = pad2(h)+'h '+pad2(m)+'m '+pad2(s)+'s';
                if(d === 0 && h < 6) cd.classList.add('k-deal-urgent');
                else cd.classList.remove('k-deal-urgent');
            });
        }
        updateDealTimers();
        setInterval(updateDealTimers, 1000);
    }
})();

// Reveal card images once loaded — works for initial page AND AJAX-injected content
window.kMarkLoadedImages = function() {
    document.querySelectorAll('.k-listing-img img, .k-store-card-v2-logo img').forEach(function(img) {
        if (img.complete && img.naturalWidth > 0) { img.classList.add('k-img-loaded'); }
    });
};
// Capture-phase listener fires for every image load, including ones added later via AJAX
document.addEventListener('load', function(e) {
    var t = e.target;
    if (t.tagName === 'IMG' && (t.closest('.k-listing-img') || t.closest('.k-store-card-v2-logo'))) {
        t.classList.add('k-img-loaded');
    }
}, true);

document.addEventListener('DOMContentLoaded', function() {
    window.kMarkLoadedImages();

    var sp = document.querySelector('.k-scroll-progress');
    if (!sp) { sp = document.createElement('div'); sp.className = 'k-scroll-progress'; document.body.appendChild(sp); }
    window.addEventListener('scroll', function() {
        var h = document.documentElement.scrollHeight - window.innerHeight;
        sp.style.width = h > 0 ? (window.scrollY / h * 100) + '%' : '0';
    }, {passive: true});
});
</script>
@stack('scripts')
<script>
(function(){var s=JSON.parse(localStorage.getItem('k_saved')||'[]'),c=s.length;if(!c)return;
var b1=document.getElementById('kNavSavedBadge'),b2=document.getElementById('kDrawerSavedBadge');
if(b1){b1.textContent=c;b1.style.display='';}
if(b2){b2.textContent=c;b2.style.display='inline';}
})();
</script>
</body>
</html>
