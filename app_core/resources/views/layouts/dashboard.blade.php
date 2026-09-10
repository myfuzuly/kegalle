<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title','Dashboard') · kegalle</title>
<meta name="robots" content="noindex,nofollow">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#0f172a">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" href="/css/kegalle-dashboard.css?v=26" as="style">
<link rel="stylesheet" href="/css/kegalle-dashboard.css?v=26">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&display=swap"></noscript>
<script defer src="/js/kegalle-dashboard-panel.js?v=1"></script>
{{-- @vite(['resources/css/dashboard.css', 'resources/js/dashboard.js'], 'vite-dist') --}}
@stack('styles')

</head>
<body class="kd-body">
{{-- Mobile sidebar overlay --}}
<div class="kdl-sidebar-overlay" id="kdlSidebarOverlay"></div>

<div class="kdl-shell">

  {{-- Sidebar --}}
  @include('dashboard.partials.sidebar')

  {{-- Main area --}}
  <div class="kdl-main">

    {{-- Topbar --}}
    <header class="kdl-topbar">
      <div class="kdl-topbar-left" style="display:flex;align-items:center">
        <button type="button" class="kdl-hamburger" id="kdlHamburger" aria-label="Open menu">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <div style="min-width:0">
          <div class="kdl-breadcrumb">
            <span>kegalle</span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span>@yield('eyebrow','Dashboard')</span>
          </div>
          <div class="kdl-topbar-title">@yield('heading','Dashboard')</div>
        </div>
      </div>
      <div class="kdl-topbar-right">
        @yield('actions')
        <a href="/dashboard/notifications" class="kdl-tb-btn-icon" title="Notifications">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
        </a>
        <div class="kdl-userchip">
          <div class="kdl-userchip-av">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
          <span class="kdl-userchip-name">{{ auth()->user()->name ?? 'User' }}</span>
        </div>
        <form method="POST" action="/logout" style="margin:0">
          @csrf
          <button type="submit" class="kdl-tb-btn kdl-tb-btn-logout">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/></svg>
            <span class="kdl-signout-label">Sign Out</span>
          </button>
        </form>
      </div>
    </header>

    {{-- Hello banner --}}
    <div class="kdl-welcome-banner">
      <div class="kdl-wb-blob kdl-wb-blob-1"></div>
      <div class="kdl-wb-blob kdl-wb-blob-2"></div>
      <div class="kdl-wb-inner">
        <div class="kdl-wb-title">Hello, {{ auth()->user()->name ?? 'there' }} 👋</div>
        <div class="kdl-wb-sub">@yield('banner_sub', 'Here\'s a summary of your kegalle Marketplace activity.')</div>
      </div>
      <div class="kdl-wb-actions">
        <a href="/dashboard/listings/create" class="kdl-wb-btn-primary">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
          Post New Ad
        </a>
        <a href="/dashboard/stores/create" class="kdl-wb-btn-ghost">🏪 Create Store</a>
        @if(in_array(auth()->user()->role ?? '', ['admin','super_admin']))
        <a href="/admin" class="kdl-wb-btn-admin">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
          Admin Panel
        </a>
        @endif
      </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="kdl-alert kdl-alert-success">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="kdl-alert kdl-alert-danger">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      {{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div class="kdl-alert kdl-alert-danger">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      {{ $errors->first() }}
    </div>
    @endif

    {{-- Page content --}}
    <main class="kdl-content">@yield('content')</main>

  </div>
</div>
@stack('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
  var btn=document.getElementById('kdlHamburger');
  var sidebar=document.querySelector('.ksp');
  var overlay=document.getElementById('kdlSidebarOverlay');
  function openSidebar(){sidebar&&sidebar.classList.add('open');overlay&&overlay.classList.add('open');document.body.style.overflow='hidden';}
  function closeSidebar(){sidebar&&sidebar.classList.remove('open');overlay&&overlay.classList.remove('open');document.body.style.overflow='';}
  btn&&btn.addEventListener('click',openSidebar);
  overlay&&overlay.addEventListener('click',closeSidebar);

  // Global data-confirm handler for delete/action forms
  document.addEventListener('submit',function(e){
    var f=e.target;
    var msg=f.getAttribute('data-confirm');
    if(!msg) return;
    if(!window.confirm(msg)) e.preventDefault();
  },true);

  // Global data-confirm handler for buttons (non-form)
  document.addEventListener('click',function(e){
    var btn=e.target.closest('[data-confirm]');
    if(!btn||btn.tagName==='FORM') return;
    if(!window.confirm(btn.getAttribute('data-confirm'))) e.preventDefault();
  },true);
})();
</script>
</body>
</html>
