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
        <div class="ka-nav-title">Overview</div>
        @if($can('dashboard'))
        <a class="ka-nav {{ request()->is('admin') ? 'active' : '' }}" href="/admin">
            <span>📊</span> Dashboard
        </a>
        @endif
        @if($can('notifications'))
        @php
            $sidebarUnreadCount = 0;
            try {
                $sidebarUnreadCount = \App\Models\AdminNotification::where('is_read', false)->count();
            } catch (\Throwable $e) {}
        @endphp
        <a class="ka-nav {{ request()->is('admin/notifications*') ? 'active' : '' }}" href="/admin/notifications" style="display:flex;align-items:center">
            <span>🔔</span> Notification Center
            @if($sidebarUnreadCount)<span class="ka-notif-badge" style="position:static;margin-left:auto;flex-shrink:0">{{ $sidebarUnreadCount }}</span>@endif
        </a>
        @endif
        @if($can('approvals'))
        @php
            $approvalsPending = 0;
            try {
                $approvalsPending = \App\Models\Listing::where('status', 'pending')->count()
                    + \App\Models\Store::where('status', 'pending')->count();
            } catch (\Throwable $e) {}
        @endphp
        <a class="ka-nav {{ request()->is('admin/approvals*') ? 'active' : '' }}" href="/admin/approvals" style="display:flex;align-items:center">
            <span>✅</span> Approval Center
            @if($approvalsPending)<span class="ka-notif-badge" style="position:static;margin-left:auto;flex-shrink:0">{{ $approvalsPending }}</span>@endif
        </a>
        @endif
        @if($can('inactive'))
        <a class="ka-nav {{ request()->is('admin/inactive*') ? 'active' : '' }}" href="/admin/inactive">
            <span>⚠️</span> Danger Zone
        </a>
        @endif

        <div class="ka-nav-title">Marketplace</div>
        @if($can('users'))
        <a class="ka-nav {{ request()->is('admin/users*') ? 'active' : '' }}" href="/admin/users">
            <span>👥</span> Users
        </a>
        @endif
        @if($can('stores'))
        <a class="ka-nav {{ request()->is('admin/stores*') ? 'active' : '' }}" href="/admin/stores">
            <span>🏬</span> Stores
        </a>
        @endif
        @if($can('listings'))
        <a class="ka-nav {{ request()->is('admin/listings*') ? 'active' : '' }}" href="/admin/listings">
            <span>📦</span> Products / Ads
        </a>
        @endif
        @if($can('classifieds'))
        <a class="ka-nav {{ request()->is('admin/classifieds*') ? 'active' : '' }}" href="/admin/classifieds">
            <span>📋</span> Classifieds
        </a>
        @endif
        @if($can('deals'))
        <a class="ka-nav {{ request()->is('admin/deals*') ? 'active' : '' }}" href="/admin/deals">
            <span>🔥</span> Deals
            @php $dealsPending = 0; try { $dealsPending = \App\Models\Deal::where('status','pending')->count(); } catch(\Throwable $e){} @endphp
            @if($dealsPending)<span class="ka-notif-badge" style="position:static;margin-left:auto;flex-shrink:0">{{ $dealsPending }}</span>@endif
        </a>
        @endif
        @if($can('events'))
        <a class="ka-nav {{ request()->is('admin/events*') ? 'active' : '' }}" href="/admin/events" style="display:flex;align-items:center">
            <span>📅</span> Events
            @php $eventsPending = 0; try { $eventsPending = \App\Models\Event::where('status','pending')->count(); } catch(\Throwable $e){} @endphp
            @if($eventsPending)<span class="ka-notif-badge" style="position:static;margin-left:auto;flex-shrink:0">{{ $eventsPending }}</span>@endif
        </a>
        @endif
        @if($can('categories'))
        <a class="ka-nav {{ request()->is('admin/categories*') ? 'active' : '' }}" href="/admin/categories">
            <span>🗂</span> Categories
        </a>
        @endif
        @if($can('brands'))
        <a class="ka-nav {{ request()->is('admin/brands*') ? 'active' : '' }}" href="/admin/brands">
            <span>🏷️</span> Brands
        </a>
        @endif
        @if($can('listing-fields'))
        <a class="ka-nav {{ request()->is('admin/listing-fields*') ? 'active' : '' }}" href="/admin/listing-fields">
            <span>🔧</span> Listing Fields
        </a>
        @endif
        @if($can('locations'))
        <a class="ka-nav {{ request()->is('admin/locations*') ? 'active' : '' }}" href="/admin/locations">
            <span>📍</span> Locations
        </a>
        @endif
        @if($can('posts'))
        <a class="ka-nav {{ request()->is('admin/posts*') ? 'active' : '' }}" href="/admin/posts">
            <span>📰</span> Blog
        </a>
        @endif
        @if($can('explore-items'))
        <a class="ka-nav {{ request()->is('admin/explore-items*') ? 'active' : '' }}" href="/admin/explore-items">
            <span>🗺️</span> Explore Kegalle
        </a>
        @endif
        @if($can('government-services'))
        <a class="ka-nav {{ request()->is('admin/government-services*') ? 'active' : '' }}" href="/admin/government-services">
            <span>🏛️</span> Gov Services
        </a>
        @endif

        @if($can('memberships') || $can('payments') || $can('ad-banners'))
        <div class="ka-nav-title">Monetization</div>
        @endif
        @if($can('memberships'))
        <a class="ka-nav {{ request()->is('admin/memberships*') ? 'active' : '' }}" href="/admin/memberships">
            <span>💳</span> Memberships
        </a>
        @endif
        @if($can('payments'))
        <a class="ka-nav {{ request()->is('admin/payments*') ? 'active' : '' }}" href="/admin/payments">
            <span>💰</span> Payments
        </a>
        @endif
        @if($can('ad-banners'))
        <a class="ka-nav {{ request()->is('admin/ad-banners*') ? 'active' : '' }}" href="/admin/ad-banners">
            <span>📢</span> Ad Spaces
        </a>
        @endif

        <div class="ka-nav-title">Operations</div>
        @if($can('reviews'))
        <a class="ka-nav {{ request()->is('admin/reviews*') ? 'active' : '' }}" href="/admin/reviews">
            <span>⭐</span> Reviews
        </a>
        @endif
        @if($can('roles'))
        <a class="ka-nav {{ request()->is('admin/roles*') ? 'active' : '' }}" href="/admin/roles">
            <span>🎭</span> Roles
        </a>
        @endif
        @if($can('settings'))
        <a class="ka-nav {{ request()->is('admin/settings*') ? 'active' : '' }}" href="/admin/settings">
            <span>⚙️</span> Settings
        </a>
        @endif
    </nav>

    <div class="ka-sidebar-footer">
        <a href="/" target="_blank">🌍 Open Marketplace</a>
    </div>
</aside>
