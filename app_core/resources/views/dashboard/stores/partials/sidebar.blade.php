<aside class="kd-sidebar">
<div class="kd-brand"><div class="kd-logo">🏪</div><div><strong>{{ $store->name ?? 'Store' }}</strong><small>Store Owner Panel</small></div></div>
<nav class="kd-nav">
<span>Store Control</span>
<a href="/dashboard/stores/{{ $store->id ?? '' }}" class="{{ request()->is('dashboard/stores/*') ? 'active' : '' }}">📊 Overview</a>
<a href="/dashboard/stores/{{ $store->id ?? '' }}/edit">🏪 Store Profile</a>
<a href="/dashboard/stores/{{ $store->id ?? '' }}/products">📦 Products</a>
<a href="/dashboard/stores/{{ $store->id ?? '' }}/products/create">➕ Add Product</a>
<span>Growth</span>
<a href="/dashboard/stores/{{ $store->id ?? '' }}/analytics">📈 Analytics</a>
<a href="/dashboard/stores/{{ $store->id ?? '' }}/reviews">⭐ Reviews</a>
<a href="/dashboard/membership">💳 Membership</a>
<span>Back</span>
<a href="/dashboard">← User Dashboard</a>
<a href="/dashboard/stores">All Stores</a>
</nav>
</aside>
