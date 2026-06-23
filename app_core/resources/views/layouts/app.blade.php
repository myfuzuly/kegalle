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
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="icon" href="/images/kegalle-placeholder.png">
<link rel="stylesheet" href="/css/kurulla-main.css?v=16">
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
        <form class="k-search-wrap" action="/listings" method="GET">
            <select name="category" class="k-search-cat" aria-label="Category">
                <option value="">All Categories</option>
            </select>
            <input class="k-search-input" type="text" name="q" value="{{ request('q') }}" placeholder="Search products, stores or ads...">
            <button class="k-search-btn" type="submit">Search</button>
        </form>
        <button class="k-menu-btn" type="button" aria-label="Open menu" aria-expanded="false" id="kMenuBtn">☰</button>
        <div class="k-nav-links" id="kNavLinks">
            <a href="/blog" class="k-nav-link">Blog</a>
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
    </div>
</nav>
<nav class="k-subnav">
    <div class="k-subnav-inner">
        <div class="k-subnav-links">
            <a href="/" class="k-subnav-link {{ request()->is('/') ? 'active' : '' }}">🏠 Home</a>
            <a href="/listings" class="k-subnav-link {{ request()->is('listings*') ? 'active' : '' }}">⊞ All Ads</a>
            <a href="/stores" class="k-subnav-link {{ request()->is('stores*') || request()->is('store/*') ? 'active' : '' }}">🏪 Stores / Business</a>
            <a href="/listings?type=product" class="k-subnav-link">📦 Products</a>
            <a href="/classified" class="k-subnav-link {{ request()->is('classified*') ? 'active' : '' }}">◎ Classified <span class="badge" style="font-size:9px">New</span></a>
            <a href="/categories" class="k-subnav-link {{ request()->is('categories*') ? 'active' : '' }}">▦ Categories</a>
            <a href="/locations" class="k-subnav-link {{ request()->is('locations*') ? 'active' : '' }}">📍 Locations</a>
            <a href="/blog" class="k-subnav-link {{ request()->is('blog*') ? 'active' : '' }}">📰 Blog</a>
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
                    <input type="email" name="email" placeholder="Enter your email" style="flex:1;background:#1e2d3d;border:1px solid #2a3f55;color:#fff;border-radius:var(--k-radius-sm);padding:8px 10px;font-size:13px;outline:none;">
                    <button class="k-btn k-btn-primary k-btn-sm" type="submit">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="k-footer-bottom">
            <span>© {{ date('Y') }} Kegalle Marketplace. All rights reserved.</span>
            <div style="display:flex;align-items:center;gap:8px">
                <span>We Accept</span>
                <span style="background:#1e2d3d;padding:3px 8px;border-radius:4px;font-size:11px;color:#9BB8D3">VISA</span>
                <span style="background:#1e2d3d;padding:3px 8px;border-radius:4px;font-size:11px;color:#9BB8D3">Mastercard</span>
                <span style="background:#1e2d3d;padding:3px 8px;border-radius:4px;font-size:11px;color:#9BB8D3">Amex</span>
            </div>
        </div>
    </div>
</footer>

<a href="https://wa.me/94771234567" target="_blank" style="position:fixed;right:22px;bottom:22px;z-index:1200;display:inline-flex;align-items:center;padding:0 18px;height:48px;border-radius:999px;background:#25D366;color:#fff;font-weight:700;text-decoration:none;box-shadow:0 16px 36px rgba(37,211,102,.3)">WhatsApp</a>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "Kegalle Marketplace",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('images/kegalle-placeholder.png') }}",
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
    if (btn && nav) {
        btn.addEventListener('click', function () {
            var isOpen = nav.classList.toggle('open');
            btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            btn.textContent = isOpen ? '✕' : '☰';
        });
    }

    document.addEventListener('click', function (e) {
        var fav = e.target.closest('.js-favorite-btn');
        if (!fav) return;
        e.preventDefault();
        e.stopPropagation();

        if (fav.dataset.authed !== '1') {
            window.location.href = '/login';
            return;
        }

        var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch('/dashboard/favorites/' + fav.dataset.listingId + '/toggle', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            fav.textContent = data.favorited ? '❤️' : '🤍';
            fav.classList.toggle('is-favorited', data.favorited);
            fav.setAttribute('aria-label', data.favorited ? 'Remove from favorites' : 'Save listing');
        })
        .catch(function () {});
    });
})();
</script>
@stack('scripts')
</body>
</html>
