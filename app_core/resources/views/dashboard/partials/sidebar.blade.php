@php
    $uid = auth()->id();
    try {
        $sidebarStores = cache()->remember("sidebar_stores_{$uid}", 60, fn() =>
            \App\Models\Store::where('user_id', $uid)->select('id','name','slug','status')->latest()->get()
        );
    } catch(\Throwable) { $sidebarStores = collect(); }

    // Determine if we're in store context
    $inStore = isset($store) && $store && $store->id;
    $currentStoreId = $inStore ? $store->id : null;
    $showCtxSwitcher = $sidebarStores->count() > 1 || (!$inStore && $sidebarStores->count() >= 1);
@endphp

<aside class="ksp">

  {{-- Brand --}}
  <div class="ksp-brand">
    <div class="ksp-logo">
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg>
    </div>
    <div>
      <div class="ksp-brand-name">kegalle</div>
      <div class="ksp-brand-sub">{{ $inStore ? 'Store Panel' : 'Marketplace' }}</div>
    </div>
  </div>

  {{-- Context Switcher (hidden for single-store users) --}}
  @if($showCtxSwitcher)
  <div class="ksp-ctx">
    <button type="button" class="ksp-ctx-btn" id="kspCtxBtn">
      <div class="ksp-ctx-icon {{ $inStore ? 'ksp-ctx-icon-store' : 'ksp-ctx-icon-personal' }}">
        {{ $inStore ? '🏪' : '👤' }}
      </div>
      <div class="ksp-ctx-body">
        <div class="ksp-ctx-label">{{ $inStore ? ($store->name ?? 'Store') : 'Personal Dashboard' }}</div>
        <div class="ksp-ctx-sub">{{ $inStore ? 'Store Panel' : 'My Account' }}</div>
      </div>
      <svg class="ksp-ctx-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
    </button>

    <div class="ksp-ctx-drop" id="kspCtxDrop">
      {{-- Personal --}}
      <a href="/dashboard" class="ksp-ctx-drop-item {{ !$inStore ? 'active' : '' }}">
        <div class="ksp-ctx-drop-icon grad-blue">👤</div>
        <div class="min-w-0">
          <div class="ksp-ctx-drop-name">Personal Dashboard</div>
          <div class="ksp-ctx-drop-meta">Listings, offers, messages</div>
        </div>
        @if(!$inStore)<svg class="ksp-ctx-drop-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>@endif
      </a>

      @if($sidebarStores->isNotEmpty())
      <div class="ksp-ctx-divider"></div>
      @foreach($sidebarStores as $ss)
      <a href="/dashboard/stores/{{ $ss->id }}" class="ksp-ctx-drop-item {{ ($inStore && $currentStoreId == $ss->id) ? 'active' : '' }}">
        <div class="ksp-ctx-drop-icon grad-green2">🏪</div>
        <div class="min-w-0">
          <div class="ksp-ctx-drop-name">{{ $ss->name }}</div>
          <div class="ksp-ctx-drop-meta">{{ ucfirst($ss->status ?? 'pending') }} · Store</div>
        </div>
        @if($inStore && $currentStoreId == $ss->id)<svg class="ksp-ctx-drop-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>@endif
      </a>
      @endforeach
      @endif

      <div class="ksp-ctx-divider"></div>
      <a href="/dashboard/stores/create" class="ksp-ctx-new">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Create New Store
      </a>
    </div>
  </div>
  @endif

  {{-- User card --}}
  <div class="ksp-user">
    <div class="ksp-avatar" style="background:{{ $inStore ? 'linear-gradient(135deg,#1b5e20,#4ade80)' : 'linear-gradient(135deg,#1e3a5f,#2563eb)' }}">
      {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
    </div>
    <div class="min-w-0">
      <div class="ksp-uname">{{ auth()->user()->name ?? 'My Account' }}</div>
      <div class="ksp-urole">{{ $inStore ? 'Store Owner' : 'Free Plan' }}</div>
    </div>
  </div>

  {{-- CTA --}}
  @if($inStore)
  <a href="/dashboard/stores/{{ $store->id }}/products/create" class="ksp-cta ksp-cta-store">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    Add Product
  </a>
  @else
  <a href="/dashboard/listings/create" class="ksp-cta ksp-cta-personal">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    Post New Ad
  </a>
  @endif

  {{-- ── Nav ── --}}
  <nav class="ksp-nav">

  @if($inStore)
  {{-- ═══ STORE NAV ═══ --}}
  <span class="ksp-group">Store</span>
  <a href="/dashboard/stores/{{ $store->id }}" class="ksp-item {{ request()->is('dashboard/stores/'.$store->id) ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg></span>
    <span class="ksp-label">Overview</span>
  </a>
  <a href="/dashboard/stores/{{ $store->id }}/edit" class="ksp-item {{ request()->is('dashboard/stores/*/edit') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></span>
    <span class="ksp-label">Store Profile</span>
  </a>

  <span class="ksp-group">Products</span>
  <a href="/dashboard/stores/{{ $store->id }}/products" class="ksp-item {{ request()->is('dashboard/stores/*/products') && !request()->is('dashboard/stores/*/products/create') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg></span>
    <span class="ksp-label">All Products</span>
  </a>
  <a href="/dashboard/stores/{{ $store->id }}/products/create" class="ksp-item {{ request()->is('dashboard/stores/*/products/create') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg></span>
    <span class="ksp-label">Add Product</span>
  </a>
  <a href="/dashboard/stores/{{ $store->id }}/import" class="ksp-item {{ request()->is('dashboard/stores/*/import') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></span>
    <span class="ksp-label">Bulk Import</span>
  </a>

  <span class="ksp-group">Buyers</span>
  <a href="/dashboard/chat" class="ksp-item {{ request()->is('dashboard/chat*') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></span>
    <span class="ksp-label">Chats</span>
  </a>
  <a href="/dashboard/offers/received" class="ksp-item {{ request()->is('dashboard/offers/received') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 010-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 000-5C13 2 12 7 12 7z"/></svg></span>
    <span class="ksp-label">Price Offers</span>
  </a>

  <span class="ksp-group">Insights</span>
  <a href="/dashboard/stores/{{ $store->id }}/analytics" class="ksp-item {{ request()->is('dashboard/stores/*/analytics') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
    <span class="ksp-label">Analytics</span>
  </a>
  <a href="/dashboard/stores/{{ $store->id }}/reviews" class="ksp-item {{ request()->is('dashboard/stores/*/reviews') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></span>
    <span class="ksp-label">Reviews</span>
  </a>

  <span class="ksp-group">Promotions</span>
  <a href="/dashboard/deals/create" class="ksp-item {{ request()->is('dashboard/deals/create') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 14l6-6"/><circle cx="9.5" cy="9.5" r="1.5"/><circle cx="14.5" cy="14.5" r="1.5"/><path d="M20.59 13.41a2 2 0 000-2.82L13.41 3.41a2 2 0 00-2.82 0L3.41 10.59a2 2 0 000 2.82l7.18 7.18a2 2 0 002.82 0l7.18-7.18z"/></svg></span>
    <span class="ksp-label">Post a Deal</span>
  </a>
  <a href="/dashboard/deals" class="ksp-item {{ request()->is('dashboard/deals') && !request()->is('dashboard/deals/create') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg></span>
    <span class="ksp-label">My Deals</span>
  </a>
  <a href="/dashboard/membership" class="ksp-item {{ request()->is('dashboard/membership*') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></span>
    <span class="ksp-label">Membership</span>
  </a>

  <div class="ksp-div"></div>
  @if($sidebarStores->count() > 1)
  <a href="/dashboard/stores" class="ksp-item">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
    <span class="ksp-label">All My Stores</span>
  </a>
  @endif
  <a href="/store/{{ $store->slug ?? '' }}" target="_blank" class="ksp-item">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></span>
    <span class="ksp-label">View Public Store</span>
  </a>

  @else
  {{-- ═══ PERSONAL NAV ═══ --}}
  <span class="ksp-group">Overview</span>
  <a href="/dashboard" class="ksp-item {{ request()->is('dashboard') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg></span>
    <span class="ksp-label">Dashboard</span>
  </a>

  <span class="ksp-group">My Ads</span>
  <a href="/dashboard/listings" class="ksp-item {{ request()->is('dashboard/listings') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
    <span class="ksp-label">My Listings</span>
  </a>
  <a href="/dashboard/listings/create" class="ksp-item {{ request()->is('dashboard/listings/create') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg></span>
    <span class="ksp-label">Post New Ad</span>
  </a>
  <a href="/dashboard/favorites" class="ksp-item {{ request()->is('dashboard/favorites') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg></span>
    <span class="ksp-label">Saved Favorites</span>
  </a>

  <span class="ksp-group">Deals</span>
  <a href="/dashboard/deals" class="ksp-item {{ request()->is('dashboard/deals') && !request()->is('dashboard/deals/create*') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg></span>
    <span class="ksp-label">My Deals</span>
  </a>
  <a href="/dashboard/deals/create" class="ksp-item {{ request()->is('dashboard/deals/create*') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg></span>
    <span class="ksp-label">Submit Deal</span>
  </a>

  <span class="ksp-group">Services</span>
  <a href="/dashboard/services" class="ksp-item {{ request()->is('dashboard/services') && !request()->is('dashboard/services/create') ? 'on' : '' }}">
    <span class="ksp-ic">🛠</span>
    <span class="ksp-label">My Services</span>
  </a>
  <a href="/dashboard/services/create" class="ksp-item {{ request()->is('dashboard/services/create') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg></span>
    <span class="ksp-label">Post Service</span>
  </a>

  <span class="ksp-group">My Stores</span>
  <a href="/dashboard/stores" class="ksp-item {{ request()->is('dashboard/stores') && !request()->is('dashboard/stores/create') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
    <span class="ksp-label">All Stores</span>
  </a>
  <a href="/dashboard/stores/create" class="ksp-item {{ request()->is('dashboard/stores/create') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg></span>
    <span class="ksp-label">Create Store</span>
  </a>

  <span class="ksp-group">Offers</span>
  <a href="/dashboard/offers" class="ksp-item {{ request()->is('dashboard/offers') && !request()->is('dashboard/offers/received*') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg></span>
    <span class="ksp-label">My Offers</span>
  </a>
  @php try { $pendingOffers = cache()->remember("sidebar_pending_offers_{$uid}", 60, fn() => \App\Models\Offer::whereHas('listing', fn($q)=>$q->where('user_id',$uid))->where('status','pending')->count()); } catch(\Throwable){$pendingOffers=0;} @endphp
  <a href="/dashboard/offers/received" class="ksp-item {{ request()->is('dashboard/offers/received') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
    <span class="ksp-label">Received Offers</span>
    @if($pendingOffers>0)<span class="ksp-badge ksp-badge-g">{{ $pendingOffers }}</span>@endif
  </a>

  <span class="ksp-group">Account</span>
  @php try{$unreadNotifCount=cache()->remember("sidebar_unread_notif_{$uid}", 30, fn()=>\App\Models\UserNotification::where('user_id',$uid)->whereNull('read_at')->count());}catch(\Throwable){$unreadNotifCount=0;} @endphp
  <a href="/dashboard/notifications" class="ksp-item {{ request()->is('dashboard/notifications') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg></span>
    <span class="ksp-label">Notifications</span>
    @if($unreadNotifCount>0)<span class="ksp-badge">{{ $unreadNotifCount>9?'9+':$unreadNotifCount }}</span>@endif
  </a>
  <a href="/dashboard/profile" class="ksp-item {{ request()->is('dashboard/profile') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
    <span class="ksp-label">Profile Settings</span>
  </a>
  <a href="/dashboard/membership" class="ksp-item {{ request()->is('dashboard/membership*') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></span>
    <span class="ksp-label">Membership</span>
  </a>
  <a href="/dashboard/payments" class="ksp-item {{ request()->is('dashboard/payments') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></span>
    <span class="ksp-label">Payments</span>
  </a>
  <a href="/dashboard/transactions" class="ksp-item {{ request()->is('dashboard/transactions') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 014-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 01-4 4H3"/></svg></span>
    <span class="ksp-label">Transactions</span>
  </a>
  @if(!auth()->user()->phone_verified_at)
  <a href="/phone/verify" class="ksp-item ksp-warn"><span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg></span><span class="ksp-label">Verify Phone ⚠️</span></a>
  @else
  <span class="ksp-item ksp-item-static"><span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg></span><span class="ksp-label">Phone Verified ✓</span></span>
  @endif
  <a href="/dashboard/chat" class="ksp-item {{ request()->is('dashboard/chat*') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></span>
    <span class="ksp-label">Messages</span>
  </a>
  <a href="/dashboard/reviews" class="ksp-item {{ request()->is('dashboard/reviews') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></span>
    <span class="ksp-label">Reviews</span>
  </a>
  <div class="ksp-div"></div>
  <a href="/dashboard/contact-admin" class="ksp-item ksp-accent {{ request()->is('dashboard/contact-admin') ? 'on' : '' }}">
    <span class="ksp-ic"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg></span>
    <span class="ksp-label">Contact Admin</span>
  </a>
  @endif

  </nav>

  {{-- Bottom --}}
  <div class="ksp-bottom">
    <a href="/" target="_blank" class="ksp-bottom-link">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
      View Live Site
    </a>
    <form class="csp5-311" method="POST" action="/logout">
      @csrf
      <button type="submit" class="ksp-bottom-link">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        Sign Out
      </button>
    </form>
  </div>

</aside>

<script nonce="{{ $cspNonce ?? '' }}">
document.getElementById('kspCtxBtn')?.addEventListener('click', kspToggleCtx);
function kspToggleCtx(){
  var btn=document.getElementById('kspCtxBtn');
  var drop=document.getElementById('kspCtxDrop');
  var open=drop.classList.toggle('open');
  btn.classList.toggle('open',open);
}
document.addEventListener('click',function(e){
  var wrap=document.getElementById('kspCtxBtn')?.closest('.ksp-ctx');
  if(wrap&&!wrap.contains(e.target)){
    document.getElementById('kspCtxDrop')?.classList.remove('open');
    document.getElementById('kspCtxBtn')?.classList.remove('open');
  }
});
</script>
