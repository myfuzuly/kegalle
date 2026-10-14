@php
    $u = auth()->user();
    // Mirrors EnsureIsAdmin: known sections use role permissions, unknown sections are super-admin only.
    $can = function (string $key) use ($u): bool {
        return array_key_exists($key, \App\Models\Role::PERMISSIONS)
            ? $u->hasPermission($key)
            : $u->role === 'super_admin';
    };

    $badges = cache()->remember('admin_nav_badges', 60, function () {
        $c = static function (callable $q): int { try { return (int) $q(); } catch (\Throwable $e) { return 0; } };
        return [
            'approvals'     => $c(fn () => \App\Models\Listing::where('status', 'pending')->count()
                                + \App\Models\Store::where('status', 'pending')->count()
                                + \App\Models\Deal::where('status', 'pending')->count()
                                + \App\Models\Service::where('status', 'pending')->count()),
            'notifications' => $c(fn () => \App\Models\AdminNotification::where('is_read', false)->count()),
            'reports'       => $c(fn () => \App\Models\Report::where('status', 'pending')->count()),
            'deals'         => $c(fn () => \App\Models\Deal::where('status', 'pending')->count()),
            'events'        => $c(fn () => \App\Models\Event::where('status', 'pending')->count()),
            'services'      => $c(fn () => \App\Models\Service::where('status', 'pending')->count()),
            'payments'      => $c(fn () => \App\Models\Payment::where('status', 'pending')->count()),
        ];
    });

    $ic = [
        'dashboard'  => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
        'approvals'  => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
        'bell'       => '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
        'flag'       => '<path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/>',
        'box'        => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="M3.3 7L12 12l8.7-5"/><path d="M12 22V12"/>',
        'file'       => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8M16 17H8"/>',
        'store'      => '<path d="M3 9l1.5-5h15L21 9"/><path d="M4 9v11h16V9"/><path d="M9 20v-6h6v6"/>',
        'zap'        => '<path d="M13 2L3 14h9l-1 8 10-12h-9z"/>',
        'tool'       => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94z"/>',
        'calendar'   => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
        'users'      => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
        'star'       => '<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/>',
        'chat'       => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
        'folder'     => '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>',
        'tag'        => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><circle cx="7" cy="7" r="1"/>',
        'sliders'    => '<path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/>',
        'pin'        => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
        'book'       => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V2H6.5A2.5 2.5 0 0 0 4 4.5z"/><path d="M6.5 17A2.5 2.5 0 0 0 4 19.5 2.5 2.5 0 0 0 6.5 22H20v-5"/>',
        'compass'    => '<circle cx="12" cy="12" r="10"/><path d="M16.24 7.76l-2.12 6.36-6.36 2.12 2.12-6.36z"/>',
        'landmark'   => '<path d="M3 22h18M6 18v-7M10 18v-7M14 18v-7M18 18v-7M12 2l8 5H4z"/>',
        'image'      => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/>',
        'megaphone'  => '<path d="M3 11v2a1 1 0 0 0 1 1h3l5 4V6L7 10H4a1 1 0 0 0-1 1z"/><path d="M16 8a5 5 0 0 1 0 8"/>',
        'card'       => '<rect x="1" y="4" width="22" height="16" rx="2"/><path d="M1 10h22"/>',
        'receipt'    => '<path d="M4 2v20l3-2 3 2 3-2 3 2 3-2 1 .67V2l-1 .67L16 2l-3 2-3-2-3 2-3-2z"/><path d="M8 8h8M8 12h8"/>',
        'shield'     => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        'settings'   => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-2.82 1.17V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 4.6 19.4l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 3 13.6H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 4.6l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 10.4 3V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 2.82 1.17l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 21 10.4h.09a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.6.6z"/>',
        'alert'      => '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><path d="M12 9v4M12 17h.01"/>',
        'globe'      => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
    ];

    // Grouped by the job an admin is doing, most frequent first.
    $groups = [
        ['id' => 'overview', 'label' => 'Overview', 'items' => [
            ['dashboard', '/admin', 'Dashboard', 'dashboard', 'admin', null],
            ['approvals', '/admin/approvals', 'Approval Center', 'approvals', 'admin/approvals*', 'approvals'],
            ['notifications', '/admin/notifications', 'Notifications', 'bell', 'admin/notifications*', 'notifications'],
            ['reports', '/admin/reports', 'Reports', 'flag', 'admin/reports*', 'reports'],
        ]],
        ['id' => 'marketplace', 'label' => 'Marketplace', 'items' => [
            ['listings', '/admin/listings', 'Products & Ads', 'box', 'admin/listings*', null],
            ['classifieds', '/admin/classifieds', 'Classifieds', 'file', 'admin/classifieds*', null],
            ['stores', '/admin/stores', 'Stores', 'store', 'admin/stores*', null],
            ['deals', '/admin/deals', 'Deals', 'zap', 'admin/deals*', 'deals'],
            ['services', '/admin/services', 'Services', 'tool', 'admin/services*', 'services'],
            ['events', '/admin/events', 'Events', 'calendar', 'admin/events*', 'events'],
        ]],
        ['id' => 'people', 'label' => 'People', 'items' => [
            ['users', '/admin/users', 'Users', 'users', 'admin/users*', null],
            ['reviews', '/admin/reviews', 'Reviews', 'star', 'admin/reviews*', null],
            ['chats', '/admin/chats', 'Chats', 'chat', 'admin/chats*', null],
        ]],
        ['id' => 'catalogue', 'label' => 'Catalogue', 'items' => [
            ['categories', '/admin/categories', 'Categories', 'folder', 'admin/categories*', null],
            ['brands', '/admin/brands', 'Brands', 'tag', 'admin/brands*', null],
            ['listing-fields', '/admin/listing-fields', 'Listing Fields', 'sliders', 'admin/listing-fields*', null],
            ['locations', '/admin/locations', 'Locations', 'pin', 'admin/locations*', null],
        ]],
        ['id' => 'content', 'label' => 'Site Content', 'items' => [
            ['posts', '/admin/posts', 'Blog', 'book', 'admin/posts*', null],
            ['explore-items', '/admin/explore-items', 'Explore Kegalle', 'compass', 'admin/explore-items*', null],
            ['government-services', '/admin/government-services', 'Gov Services', 'landmark', 'admin/government-services*', null],
            ['hero-slides', '/admin/hero-slides', 'Hero Slides', 'image', 'admin/hero-slides*', null],
            ['ad-banners', '/admin/ad-banners', 'Ad Spaces', 'megaphone', 'admin/ad-banners*', null],
        ]],
        ['id' => 'revenue', 'label' => 'Revenue', 'items' => [
            ['memberships', '/admin/memberships', 'Memberships', 'card', 'admin/memberships*', null],
            ['payments', '/admin/payments', 'Payments', 'receipt', 'admin/payments*', 'payments'],
        ]],
        ['id' => 'system', 'label' => 'System', 'items' => [
            ['roles', '/admin/roles', 'Roles & Permissions', 'shield', 'admin/roles*', null],
            ['settings', '/admin/settings', 'Settings', 'settings', 'admin/settings*', null],
            ['inactive', '/admin/inactive', 'Danger Zone', 'alert', 'admin/inactive*', null],
        ]],
    ];
@endphp
<aside class="kx-sidebar" id="kxSidebar" aria-label="Admin navigation">
    <div class="kx-brand">
        <a href="/admin" class="kx-brand-mark" aria-label="Admin dashboard">K</a>
        <div class="kx-brand-text">
            <strong>Kegalle Admin</strong>
            <small>{{ $u->role === 'super_admin' ? 'Super Admin' : (optional($u->roleModel)->name ?? 'Admin') }}</small>
        </div>
        <button type="button" class="kx-icon-btn kx-drawer-close" id="kxDrawerClose" aria-label="Close menu">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
    </div>

    <div class="kx-nav-search">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
        <input type="search" id="kxNavFilter" placeholder="Find a page…" aria-label="Find a page in the menu" autocomplete="off">
    </div>

    <nav class="kx-nav" id="kxNav">
        @foreach($groups as $g)
            @php $items = array_values(array_filter($g['items'], fn ($i) => $can($i[0]))); @endphp
            @continue(!$items)
            <div class="kx-nav-group" data-group="{{ $g['id'] }}">
                <p class="kx-nav-label">{{ $g['label'] }}</p>
                <ul role="list">
                    @foreach($items as [$key, $href, $label, $icon, $match, $badgeKey])
                        @php
                            $active = $key === 'dashboard' ? request()->is('admin') : request()->is($match);
                            $n = $badgeKey ? ($badges[$badgeKey] ?? 0) : 0;
                        @endphp
                        <li>
                            <a href="{{ $href }}" class="kx-nav-link {{ $active ? 'is-active' : '' }}" @if($active) aria-current="page" @endif data-label="{{ strtolower($label) }}" title="{{ $label }}">
                                <svg class="kx-nav-ic" viewBox="0 0 24 24" aria-hidden="true">{!! $ic[$icon] !!}</svg>
                                <span class="kx-nav-text">{{ $label }}</span>
                                @if($n)<span class="kx-badge" aria-label="{{ $n }} pending">{{ $n > 99 ? '99+' : $n }}</span>@endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
        <p class="kx-nav-empty" id="kxNavEmpty" hidden>No matching pages.</p>
    </nav>

    <div class="kx-sidebar-foot">
        <a href="/" target="_blank" rel="noopener" class="kx-nav-link" title="Open marketplace">
            <svg class="kx-nav-ic" viewBox="0 0 24 24" aria-hidden="true">{!! $ic['globe'] !!}</svg>
            <span class="kx-nav-text">Open marketplace</span>
        </a>
        <button type="button" class="kx-nav-link kx-collapse-btn" id="kxCollapse" aria-pressed="false" title="Collapse sidebar">
            <svg class="kx-nav-ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M11 17l-5-5 5-5M18 17l-5-5 5-5"/></svg>
            <span class="kx-nav-text">Collapse</span>
        </button>
    </div>
</aside>
