<aside class="ka-sidebar" id="kaSidebar">
    <div class="ka-brand">
        <a href="/admin" class="ka-brand-logo">KG</a>
        <div>
            <strong>Kegalle</strong>
            <small>Super Admin</small>
        </div>
    </div>

    <nav class="ka-nav-group">
        <div class="ka-nav-title">Overview</div>
        <a class="ka-nav {{ request()->is('admin') ? 'active' : '' }}" href="/admin">
            <span>📊</span> Dashboard
        </a>

        <div class="ka-nav-title">Marketplace</div>
        <a class="ka-nav {{ request()->is('admin/users*') ? 'active' : '' }}" href="/admin/users">
            <span>👥</span> Users
        </a>
        <a class="ka-nav {{ request()->is('admin/stores*') ? 'active' : '' }}" href="/admin/stores">
            <span>🏬</span> Stores
        </a>
        <a class="ka-nav {{ request()->is('admin/listings*') ? 'active' : '' }}" href="/admin/listings">
            <span>📦</span> Products / Ads
        </a>
        <a class="ka-nav {{ request()->is('admin/categories*') ? 'active' : '' }}" href="/admin/categories">
            <span>🗂</span> Categories
        </a>
        <a class="ka-nav {{ request()->is('admin/locations*') ? 'active' : '' }}" href="/admin/locations">
            <span>📍</span> Locations
        </a>
        <a class="ka-nav {{ request()->is('admin/posts*') ? 'active' : '' }}" href="/admin/posts">
            <span>📰</span> Blog
        </a>

        <div class="ka-nav-title">Monetization</div>
        <a class="ka-nav {{ request()->is('admin/memberships*') ? 'active' : '' }}" href="/admin/memberships">
            <span>💳</span> Memberships
        </a>
        <a class="ka-nav {{ request()->is('admin/payments*') ? 'active' : '' }}" href="/admin/payments">
            <span>💰</span> Payments
        </a>

        <div class="ka-nav-title">Operations</div>
        <a class="ka-nav {{ request()->is('admin/reviews*') ? 'active' : '' }}" href="/admin/reviews">
            <span>⭐</span> Reviews
        </a>
        <a class="ka-nav {{ request()->is('admin/settings*') ? 'active' : '' }}" href="/admin/settings">
            <span>⚙️</span> Settings
        </a>
    </nav>

    <div class="ka-sidebar-footer">
        <a href="/" target="_blank">🌍 Open Marketplace</a>
    </div>
</aside>
