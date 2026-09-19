@php $can = fn (string $p) => auth()->user()->hasPermission($p); @endphp
<aside class="ka-sidebar" id="kaSidebar">
    <div class="ka-brand">
        <a href="/admin" class="ka-brand-logo">KG</a>
        <div>
            <strong>Kegalle</strong>
            <small>{{ auth()->user()->role === 'super_admin' ? 'Super Admin' : (optional(auth()->user()->roleModel)->name ?? 'Admin') }}</small>
        </div>
    </div>

    <nav class="ka-nav-group">

        {{-- ── Overview ──────────────────────────────────────── --}}
        <button class="ka-nav-title-btn" type="button" data-group="overview">Overview <span class="ntb-arrow">▾</span></button>
        <div class="ka-nav-section">
        @if($can('dashboard'))
        <a class="ka-nav {{ request()->is('admin') ? 'active' : '' }}" href="/admin">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></span> Dashboard
        </a>
        @endif
        @if($can('approvals'))
        @php
            $approvalsPending = 0;
            try {
                $approvalsPending = \App\Models\Listing::where('status', 'pending')->count()
                    + \App\Models\Store::where('status', 'pending')->count()
                    + \App\Models\Deal::where('status', 'pending')->count()
                    + \App\Models\Service::where('status', 'pending')->count();
            } catch (\Throwable $e) {}
        @endphp
        <a class="ka-nav {{ request()->is('admin/approvals*') ? 'active' : '' }} flex-center" href="/admin/approvals">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span> Approval Center
            @if($approvalsPending)<span class="ka-notif-badge ml-auto-static">{{ $approvalsPending }}</span>@endif
        </a>
        @endif
        @if($can('notifications'))
        @php
            $sidebarUnreadCount = 0;
            try {
                $sidebarUnreadCount = \App\Models\AdminNotification::where('is_read', false)->count();
            } catch (\Throwable $e) {}
        @endphp
        <a class="ka-nav {{ request()->is('admin/notifications*') ? 'active' : '' }} flex-center" href="/admin/notifications">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></span> Notifications
            @if($sidebarUnreadCount)<span class="ka-notif-badge ml-auto-static">{{ $sidebarUnreadCount }}</span>@endif
        </a>
        @endif

        </div>{{-- /overview section --}}

        {{-- ── Content ────────────────────────────────────────── --}}
        @if($can('listings') || $can('classifieds') || $can('deals') || $can('events') || $can('posts') || $can('explore-items'))
        <button class="ka-nav-title-btn" type="button" data-group="content">Content <span class="ntb-arrow">▾</span></button>
        <div class="ka-nav-section">
        @endif
        @if($can('listings'))
        <a class="ka-nav {{ request()->is('admin/listings*') ? 'active' : '' }}" href="/admin/listings">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg></span> Products / Ads
        </a>
        @endif
        @if($can('classifieds'))
        <a class="ka-nav {{ request()->is('admin/classifieds*') ? 'active' : '' }}" href="/admin/classifieds">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></span> Classifieds
        </a>
        @endif
        @if($can('deals'))
        @php $dealsPending = 0; try { $dealsPending = \App\Models\Deal::where('status','pending')->count(); } catch(\Throwable $e){} @endphp
        <a class="ka-nav {{ request()->is('admin/deals*') ? 'active' : '' }} flex-center" href="/admin/deals">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></span> Deals
            @if($dealsPending)<span class="ka-notif-badge ml-auto-static">{{ $dealsPending }}</span>@endif
        </a>
        @endif
        @if($can('events'))
        @php $eventsPending = 0; try { $eventsPending = \App\Models\Event::where('status','pending')->count(); } catch(\Throwable $e){} @endphp
        <a class="ka-nav {{ request()->is('admin/events*') ? 'active' : '' }} flex-center" href="/admin/events">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span> Events
            @if($eventsPending)<span class="ka-notif-badge ml-auto-static">{{ $eventsPending }}</span>@endif
        </a>
        @endif
        @if($can('posts'))
        <a class="ka-nav {{ request()->is('admin/posts*') ? 'active' : '' }}" href="/admin/posts">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2z"/><path d="M4 6H2v16a2 2 0 0 0 2 2h16"/><line x1="12" y1="8" x2="16" y2="8"/><line x1="12" y1="12" x2="16" y2="12"/><line x1="12" y1="16" x2="16" y2="16"/><line x1="8" y1="8" x2="9" y2="8"/><line x1="8" y1="12" x2="9" y2="12"/><line x1="8" y1="16" x2="9" y2="16"/></svg></span> Blog
        </a>
        @endif
        @php $servicesPending = 0; try { $servicesPending = \App\Models\Service::where('status','pending')->count(); } catch(\Throwable $e){} @endphp
        <a class="ka-nav {{ request()->is('admin/services*') ? 'active' : '' }} flex-center" href="/admin/services">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></span> Services
            @if($servicesPending)<span class="ka-notif-badge ml-auto-static">{{ $servicesPending }}</span>@endif
        </a>
        @if($can('explore-items'))
        <a class="ka-nav {{ request()->is('admin/explore-items*') ? 'active' : '' }}" href="/admin/explore-items">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg></span> Explore Kegalle
        </a>
        @endif

        @if($can('listings') || $can('classifieds') || $can('deals') || $can('events') || $can('posts') || $can('explore-items'))
        </div>{{-- /content section --}}
        @endif

        {{-- ── People & Stores ────────────────────────────────── --}}
        @if($can('users') || $can('stores') || $can('reviews'))
        <button class="ka-nav-title-btn" type="button" data-group="people">People &amp; Stores <span class="ntb-arrow">▾</span></button>
        <div class="ka-nav-section">
        @endif
        @if($can('users'))
        <a class="ka-nav {{ request()->is('admin/users*') ? 'active' : '' }}" href="/admin/users">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span> Users
        </a>
        @endif
        @if($can('stores'))
        <a class="ka-nav {{ request()->is('admin/stores*') ? 'active' : '' }}" href="/admin/stores">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span> Stores
        </a>
        @endif
        @if($can('reviews'))
        <a class="ka-nav {{ request()->is('admin/reviews*') ? 'active' : '' }}" href="/admin/reviews">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></span> Reviews
        </a>
        @endif

        @if($can('users') || $can('stores') || $can('reviews'))
        </div>{{-- /people section --}}
        @endif

        {{-- ── Catalogue ───────────────────────────────────────── --}}
        @if($can('categories') || $can('brands') || $can('listing-fields') || $can('locations') || $can('government-services'))
        <button class="ka-nav-title-btn" type="button" data-group="catalogue">Catalogue <span class="ntb-arrow">▾</span></button>
        <div class="ka-nav-section">
        @endif
        @if($can('categories'))
        <a class="ka-nav {{ request()->is('admin/categories*') ? 'active' : '' }}" href="/admin/categories">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg></span> Categories
        </a>
        @endif
        @if($can('brands'))
        <a class="ka-nav {{ request()->is('admin/brands*') ? 'active' : '' }}" href="/admin/brands">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg></span> Brands
        </a>
        @endif
        @if($can('listing-fields'))
        <a class="ka-nav {{ request()->is('admin/listing-fields*') ? 'active' : '' }}" href="/admin/listing-fields">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg></span> Listing Fields
        </a>
        @endif
        @if($can('locations'))
        <a class="ka-nav {{ request()->is('admin/locations*') ? 'active' : '' }}" href="/admin/locations">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></span> Locations
        </a>
        @endif
        @if($can('government-services'))
        <a class="ka-nav {{ request()->is('admin/government-services*') ? 'active' : '' }}" href="/admin/government-services">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><line x1="3" y1="22" x2="21" y2="22"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7 12 2"/></svg></span> Gov Services
        </a>
        @endif

        @if($can('categories') || $can('brands') || $can('listing-fields') || $can('locations') || $can('government-services'))
        </div>{{-- /catalogue section --}}
        @endif

        {{-- ── Monetization ────────────────────────────────────── --}}
        @if($can('memberships') || $can('payments') || $can('ad-banners'))
        <button class="ka-nav-title-btn" type="button" data-group="monetization">Monetization <span class="ntb-arrow">▾</span></button>
        <div class="ka-nav-section">
        @endif
        @if($can('memberships'))
        <a class="ka-nav {{ request()->is('admin/memberships*') ? 'active' : '' }}" href="/admin/memberships">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></span> Memberships
        </a>
        @endif
        @if($can('payments'))
        <a class="ka-nav {{ request()->is('admin/payments*') ? 'active' : '' }}" href="/admin/payments">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span> Payments
        </a>
        @endif
        <a class="ka-nav {{ request()->is('admin/reports*') ? 'active' : '' }}" href="/admin/reports">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span> Reports
        </a>
        <a class="ka-nav {{ request()->is('admin/chats*') ? 'active' : '' }}" href="/admin/chats">
            <span class="kaa-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg></span> Chats
        </a>
        @if($can('ad-banners'))
        <a class="ka-nav {{ request()->is('admin/ad-banners*') ? 'active' : '' }}" href="/admin/ad-banners">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></span> Ad Spaces
        </a>
        @endif
        <a class="ka-nav {{ request()->is('admin/hero-slides*') ? 'active' : '' }}" href="/admin/hero-slides">
            <span class="kaa-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><polyline points="8 21 12 17 16 21"/></svg></span> Hero Slides
        </a>

        @if($can('memberships') || $can('payments') || $can('ad-banners'))
        </div>{{-- /monetization section --}}
        @endif

        {{-- ── System ──────────────────────────────────────────── --}}
        @if($can('roles') || $can('settings') || $can('inactive'))
        <button class="ka-nav-title-btn" type="button" data-group="system">System <span class="ntb-arrow">▾</span></button>
        <div class="ka-nav-section">
        @endif
        @if($can('roles'))
        <a class="ka-nav {{ request()->is('admin/roles*') ? 'active' : '' }}" href="/admin/roles">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span> Roles &amp; Permissions
        </a>
        @endif
        @if($can('settings'))
        <a class="ka-nav {{ request()->is('admin/settings*') ? 'active' : '' }}" href="/admin/settings">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></span> Settings
        </a>
        @endif
        @if($can('inactive'))
        <a class="ka-nav {{ request()->is('admin/inactive*') ? 'active' : '' }}" href="/admin/inactive">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></span> Danger Zone
        </a>
        @endif

        @if($can('roles') || $can('settings') || $can('inactive'))
        </div>{{-- /system section --}}
        @endif

    </nav>

    <div class="ka-sidebar-footer">
        <a href="/" target="_blank">
            <span class="kaa-ic"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></span>
            Open Marketplace
        </a>
    </div>
</aside>
