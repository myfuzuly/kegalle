

<aside class="ksp">

  {{-- Brand --}}
  <div class="ksp-brand">
    <div class="ksp-logo">
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM4 5h16V4a1 1 0 00-1-1H5a1 1 0 00-1 1v1z"/></svg>
    </div>
    <div class="min-w-0">
      <div class="ksp-brand-name">{{ $store->name ?? 'Store' }}</div>
      <div class="ksp-brand-sub">Store Panel</div>
    </div>
  </div>

  {{-- Store avatar card --}}
  <div class="ksp-user">
    <div class="ksp-avatar">{{ strtoupper(substr($store->name ?? 'S', 0, 1)) }}</div>
    <div class="min-w-0">
      <div class="ksp-uname">{{ auth()->user()->name ?? 'Owner' }}</div>
      <div class="ksp-urole">Store Owner</div>
    </div>
  </div>

  <nav class="ksp-nav">

    {{-- Store --}}
    <span class="ksp-group">Store</span>
    <a href="/dashboard/stores/{{ $store->id ?? '' }}" class="ksp-item {{ request()->is('dashboard/stores/*') && !request()->is('dashboard/stores/*/edit') && !request()->is('dashboard/stores/*/products*') && !request()->is('dashboard/stores/*/analytics') && !request()->is('dashboard/stores/*/reviews') && !request()->is('dashboard/stores/*/import') ? 'on' : '' }}">
      <span class="ksp-ic">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
      </span>
      <span class="ksp-label">Overview</span>
    </a>
    <a href="/dashboard/stores/{{ $store->id ?? '' }}/edit" class="ksp-item {{ request()->is('dashboard/stores/*/edit') ? 'on' : '' }}">
      <span class="ksp-ic">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      </span>
      <span class="ksp-label">Store Profile</span>
    </a>

    {{-- Products --}}
    <span class="ksp-group">Products</span>
    <a href="/dashboard/stores/{{ $store->id ?? '' }}/products" class="ksp-item {{ request()->is('dashboard/stores/*/products') ? 'on' : '' }}">
      <span class="ksp-ic">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>
      </span>
      <span class="ksp-label">All Products</span>
    </a>
    <a href="/dashboard/stores/{{ $store->id ?? '' }}/products/create" class="ksp-item {{ request()->is('dashboard/stores/*/products/create') ? 'on' : '' }}">
      <span class="ksp-ic">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      </span>
      <span class="ksp-label">Add Product</span>
    </a>
    <a href="/dashboard/stores/{{ $store->id ?? '' }}/import" class="ksp-item {{ request()->is('dashboard/stores/*/import') ? 'on' : '' }}">
      <span class="ksp-ic">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      </span>
      <span class="ksp-label">Bulk Import</span>
    </a>

    {{-- Buyers --}}
    <span class="ksp-group">Buyers</span>
    <a href="/dashboard/chat" class="ksp-item {{ request()->is('dashboard/chat*') ? 'on' : '' }}">
      <span class="ksp-ic">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
      </span>
      <span class="ksp-label">Chats</span>
    </a>
    <a href="/dashboard/offers/received" class="ksp-item {{ request()->is('dashboard/offers/received') ? 'on' : '' }}">
      <span class="ksp-ic">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 010-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 000-5C13 2 12 7 12 7z"/></svg>
      </span>
      <span class="ksp-label">Price Offers</span>
    </a>

    {{-- Insights --}}
    <span class="ksp-group">Insights</span>
    <a href="/dashboard/stores/{{ $store->id ?? '' }}/analytics" class="ksp-item {{ request()->is('dashboard/stores/*/analytics') ? 'on' : '' }}">
      <span class="ksp-ic">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      </span>
      <span class="ksp-label">Analytics</span>
    </a>
    <a href="/dashboard/stores/{{ $store->id ?? '' }}/reviews" class="ksp-item {{ request()->is('dashboard/stores/*/reviews') ? 'on' : '' }}">
      <span class="ksp-ic">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      </span>
      <span class="ksp-label">Reviews</span>
    </a>

    {{-- Promotions --}}
    <span class="ksp-group">Promotions</span>
    <a href="/dashboard/listings/create" class="ksp-item {{ request()->is('dashboard/listings/create') ? 'on' : '' }}">
      <span class="ksp-ic">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      </span>
      <span class="ksp-label">Post Listing</span>
    </a>
    <a href="/dashboard/deals/create" class="ksp-item {{ request()->is('dashboard/deals/create') ? 'on' : '' }}">
      <span class="ksp-ic">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 14l6-6"/><circle cx="9.5" cy="9.5" r="1.5"/><circle cx="14.5" cy="14.5" r="1.5"/><path d="M20.59 13.41a2 2 0 000-2.82L13.41 3.41a2 2 0 00-2.82 0L3.41 10.59a2 2 0 000 2.82l7.18 7.18a2 2 0 002.82 0l7.18-7.18z"/></svg>
      </span>
      <span class="ksp-label">Post a Deal</span>
    </a>
    <a href="/dashboard/deals" class="ksp-item {{ request()->is('dashboard/deals') ? 'on' : '' }}">
      <span class="ksp-ic">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
      </span>
      <span class="ksp-label">My Deals</span>
    </a>
    <a href="/dashboard/membership" class="ksp-item {{ request()->is('dashboard/membership') ? 'on' : '' }}">
      <span class="ksp-ic">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
      </span>
      <span class="ksp-label">Membership</span>
    </a>

    {{-- Back --}}
    <div class="ksp-div"></div>
    <a href="/dashboard/stores" class="ksp-item">
      <span class="ksp-ic">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      </span>
      <span class="ksp-label">All My Stores</span>
    </a>
    <a href="/dashboard" class="ksp-item">
      <span class="ksp-ic">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      </span>
      <span class="ksp-label">Main Dashboard</span>
    </a>

  </nav>

  {{-- Bottom --}}
  <div class="ksp-bottom">
    <a href="/store/{{ $store->slug ?? '' }}" target="_blank" class="ksp-bottom-link">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
      View Public Store
    </a>
    <form class="csp5-311" method="POST" action="/logout">
      @csrf
      <button type="submit" class="ksp-bottom-link btn-reset">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        Sign Out
      </button>
    </form>
  </div>

</aside>
