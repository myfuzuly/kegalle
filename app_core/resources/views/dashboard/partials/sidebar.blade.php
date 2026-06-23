<aside class="kd-sidebar">
<div class="kd-brand">
    <div class="kd-logo">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
    <div><strong>{{ auth()->user()->name ?? 'My Account' }}</strong><small>User Panel</small></div>
</div>
<nav class="kd-nav">
    <span>Overview</span>
    <a href="/dashboard" class="{{ request()->is('dashboard') ? 'active' : '' }}">📊 Dashboard</a>

    <span>My Ads</span>
    <a href="/dashboard/listings" class="{{ request()->is('dashboard/listings') ? 'active' : '' }}">📦 My Listings</a>
    <a href="/dashboard/listings/create" class="{{ request()->is('dashboard/listings/create') ? 'active' : '' }}">➕ Post New Ad</a>
    <a href="/dashboard/favorites" class="{{ request()->is('dashboard/favorites') ? 'active' : '' }}">🤍 Favorites</a>

    <span>My Store</span>
    <a href="/dashboard/stores" class="{{ request()->is('dashboard/stores') ? 'active' : '' }}">🏪 My Stores</a>
    <a href="/dashboard/stores/create" class="{{ request()->is('dashboard/stores/create') ? 'active' : '' }}">➕ Create Store</a>

    <span>Account</span>
    <a href="/dashboard/profile" class="{{ request()->is('dashboard/profile') ? 'active' : '' }}">👤 Profile Settings</a>
    <a href="/dashboard/membership" class="{{ request()->is('dashboard/membership') ? 'active' : '' }}">💳 Membership</a>
    <a href="/dashboard/payments" class="{{ request()->is('dashboard/payments') ? 'active' : '' }}">💰 Payments</a>
    <a href="/dashboard/chat" class="{{ request()->is('dashboard/chat') ? 'active' : '' }}">💬 Messages</a>
    <a href="/dashboard/reviews" class="{{ request()->is('dashboard/reviews') ? 'active' : '' }}">⭐ Reviews</a>
</nav>
</aside>
