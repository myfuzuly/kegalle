<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title','Kegalle')</title>
<meta name="description" content="Kegalle marketplace for stores, products and classified ads">

<link rel="stylesheet" href="/css/app.css?v=99999">
<link rel="stylesheet" href="/css/kegalle-market.css?v=10">
<link rel="stylesheet" href="/css/kegalle-polish.css?v=10">
<link rel="stylesheet" href="/css/kegalle-product-detail.css?v=10">
<link rel="stylesheet" href="/css/kegalle-all-ads.css?v=10">
<link rel="stylesheet" href="/css/kegalle-super-admin-pro.css?v=10">

<!-- Load this last -->
<link rel="stylesheet" href="/css/kegalle-enterprise-premium.css?v=1">

<script defer src="/js/app.js"></script>
<script defer src="/js/kegalle-enterprise-premium.js?v=1"></script>
</head>
<body>
<header class="kg-header">
  <div class="kg-container kg-topnav">
    <a class="kg-brand" href="/">
      <span class="kg-pin">⌖</span>
      <span><b>Kegalle</b><small>Buy, Sell & Discover</small></span>
    </a>

    <form class="kg-mini-search" action="/listings" method="get">
      <select name="type">
        <option value="">All Listings</option>
        <option value="product">Products</option>
        <option value="classified">Classified</option>
      </select>
      <input name="q" placeholder="Search products, stores, suppliers">
      <button type="submit">Search</button>
    </form>

    <nav class="kg-actions">
      <a href="/stores">Stores</a>
      <a href="/classified">Classified</a>
      <a href="/listings?type=product">Products</a>

      @auth
        @if(auth()->user()->role === 'super_admin')
          <a href="/admin">Admin</a>
        @else
          <a href="/dashboard">Dashboard</a>
        @endif

        <form method="POST" action="/logout">
          @csrf
          <button type="submit" class="kg-text-btn">Logout</button>
        </form>
      @else
        <a href="/login">Login</a>
        <a class="kg-post" href="/register">+ Post Free Ad</a>
      @endauth
    </nav>
  </div>

  <div class="kg-greenbar">
    <div class="kg-container">
      <a href="/">⌂ Home</a>
      <a href="/listings">▦ All Ads</a>
      <a href="/stores">▣ Stores</a>
      <a href="/listings?type=product">◈ Products</a>
      <a href="/classified">◇ Classified</a>
      <span>Trusted local marketplace for Kegalle</span>
    </div>
  </div>
</header>

<main>
  @if(session('success'))
    <div class="kg-container"><div class="kg-alert">{{ session('success') }}</div></div>
  @endif

  @yield('content')
</main>

<footer class="kg-footer">
  <div class="kg-container kg-footer-grid">
    <div>
      <a class="kg-brand kg-brand-light" href="/"><span class="kg-pin">⌖</span><span><b>Kegalle</b><small>Buy, Sell & Discover</small></span></a>
      <p>Kegalle is your trusted local marketplace to discover stores, compare products and post classified ads.</p>
    </div>
    <div><h4>Quick Links</h4><a href="/">Home</a><a href="/stores">Stores</a><a href="/listings">All Listings</a><a href="/classified">Classified</a></div>
    <div><h4>Information</h4><a href="#">About Us</a><a href="#">How It Works</a><a href="#">Safety Tips</a><a href="#">Terms & Conditions</a></div>
    <div><h4>Support</h4><p>support@kurulla.com</p><p>+94 77 123 4567</p></div>
  </div>
  <div class="kg-container kg-copy">© {{ date('Y') }} Kegalle. Made for Sri Lankan local commerce.</div>
</footer>

<nav class="ep-mobile-bar">
  <a href="/">Home</a>
  <a href="/listings">Search</a>
  <a href="/register">Post</a>
  <a href="/stores">Stores</a>
  @auth
    @if(auth()->user()->role === 'super_admin')
      <a href="/admin">Admin</a>
    @else
      <a href="/dashboard">Account</a>
    @endif
  @else
    <a href="/login">Login</a>
  @endauth
</nav>
</body>
</html>
