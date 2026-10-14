@php
    $uid = auth()->id();
    $me = auth()->user();
    try {
        $sidebarStores = cache()->remember("sidebar_stores_{$uid}", 60, fn () =>
            \App\Models\Store::where('user_id', $uid)->select('id', 'name', 'slug', 'status')->latest()->get()
        );
    } catch (\Throwable) { $sidebarStores = collect(); }

    $inStore = isset($store) && $store && $store->id;
    $sid = $inStore ? $store->id : null;

    $badges = cache()->remember("seller_nav_badges_{$uid}", 30, function () use ($uid) {
        $c = static function (callable $q): int { try { return (int) $q(); } catch (\Throwable) { return 0; } };
        return [
            'notifications' => $c(fn () => \App\Models\UserNotification::where('user_id', $uid)->whereNull('read_at')->count()),
            'offers'        => $c(fn () => \App\Models\Offer::whereHas('listing', fn ($q) => $q->where('user_id', $uid))->where('status', 'pending')->count()),
        ];
    });

    $ic = [
        'grid'    => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
        'edit'    => '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4z"/>',
        'box'     => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="M3.3 7L12 12l8.7-5M12 22V12"/>',
        'plus'    => '<circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>',
        'upload'  => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M17 8l-5-5-5 5M12 3v12"/>',
        'chat'    => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
        'gift'    => '<path d="M20 12v10H4V12M2 7h20v5H2zM12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7zM12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>',
        'chart'   => '<path d="M18 20V10M12 20V4M6 20v-6"/>',
        'star'    => '<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/>',
        'zap'     => '<path d="M13 2L3 14h9l-1 8 10-12h-9z"/>',
        'card'    => '<rect x="1" y="4" width="22" height="16" rx="2"/><path d="M1 10h22"/>',
        'store'   => '<path d="M3 9l1.5-5h15L21 9"/><path d="M4 9v11h16V9"/><path d="M9 20v-6h6v6"/>',
        'ext'     => '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3"/>',
        'file'    => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/>',
        'heart'   => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"/>',
        'tool'    => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94z"/>',
        'send'    => '<path d="M22 2L11 13M22 2l-7 20-4-9-9-4z"/>',
        'inbox'   => '<path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/>',
        'bell'    => '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
        'user'    => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'receipt' => '<path d="M4 2v20l3-2 3 2 3-2 3 2 3-2 1 .67V2l-1 .67L16 2l-3 2-3-2-3 2-3-2z"/><path d="M8 8h8M8 12h8"/>',
        'swap'    => '<path d="M17 1l4 4-4 4M3 11V9a4 4 0 0 1 4-4h14M7 23l-4-4 4-4M21 13v2a4 4 0 0 1-4 4H3"/>',
        'phone'   => '<rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/>',
        'help'    => '<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3M12 17h.01"/>',
        'globe'   => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
        'shield'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
    ];

    // [href, label, icon, active?, badge]
    if ($inStore) {
        $groups = [
            'Store' => [
                ["/dashboard/stores/$sid", 'Overview', 'grid', request()->is("dashboard/stores/$sid"), 0],
                ["/dashboard/stores/$sid/edit", 'Store profile', 'edit', request()->is('dashboard/stores/*/edit'), 0],
            ],
            'Products' => [
                ["/dashboard/stores/$sid/products", 'All products', 'box', request()->is('dashboard/stores/*/products'), 0],
                ["/dashboard/stores/$sid/products/create", 'Add product', 'plus', request()->is('dashboard/stores/*/products/create'), 0],
                ["/dashboard/stores/$sid/import", 'Bulk import', 'upload', request()->is('dashboard/stores/*/import'), 0],
            ],
            'Buyers' => [
                ['/dashboard/chat', 'Chats', 'chat', request()->is('dashboard/chat*'), 0],
                ['/dashboard/offers/received', 'Price offers', 'gift', request()->is('dashboard/offers/received'), $badges['offers']],
            ],
            'Insights' => [
                ["/dashboard/stores/$sid/analytics", 'Analytics', 'chart', request()->is('dashboard/stores/*/analytics'), 0],
                ["/dashboard/stores/$sid/reviews", 'Reviews', 'star', request()->is('dashboard/stores/*/reviews'), 0],
            ],
            'Promotions' => [
                ['/dashboard/deals', 'My deals', 'zap', request()->is('dashboard/deals') , 0],
                ['/dashboard/deals/create', 'Post a deal', 'plus', request()->is('dashboard/deals/create'), 0],
                ['/dashboard/membership', 'Membership', 'card', request()->is('dashboard/membership*'), 0],
            ],
            'More' => array_values(array_filter([
                $sidebarStores->count() > 1 ? ['/dashboard/stores', 'All my stores', 'store', false, 0] : null,
                ['/store/'.($store->slug ?? ''), 'View public store', 'ext', false, 0, true],
                ['/dashboard/notifications', 'Notifications', 'bell', request()->is('dashboard/notifications'), $badges['notifications']],
            ])),
        ];
    } else {
        $groups = [
            'Overview' => [
                ['/dashboard', 'Dashboard', 'grid', request()->is('dashboard'), 0],
            ],
            'Selling' => [
                ['/dashboard/listings', 'My listings', 'file', request()->is('dashboard/listings'), 0],
                ['/dashboard/listings/create', 'Post new ad', 'plus', request()->is('dashboard/listings/create'), 0],
                ['/dashboard/stores', 'My stores', 'store', request()->is('dashboard/stores'), 0],
                ['/dashboard/services', 'My services', 'tool', request()->is('dashboard/services'), 0],
                ['/dashboard/deals', 'My deals', 'zap', request()->is('dashboard/deals'), 0],
            ],
            'Buyers & offers' => [
                ['/dashboard/chat', 'Messages', 'chat', request()->is('dashboard/chat*'), 0],
                ['/dashboard/offers/received', 'Offers received', 'inbox', request()->is('dashboard/offers/received'), $badges['offers']],
                ['/dashboard/offers', 'Offers I made', 'send', request()->is('dashboard/offers'), 0],
                ['/dashboard/reviews', 'Reviews', 'star', request()->is('dashboard/reviews'), 0],
                ['/dashboard/favorites', 'Saved favourites', 'heart', request()->is('dashboard/favorites'), 0],
            ],
            'Account' => [
                ['/dashboard/notifications', 'Notifications', 'bell', request()->is('dashboard/notifications'), $badges['notifications']],
                ['/dashboard/profile', 'Profile settings', 'user', request()->is('dashboard/profile'), 0],
                ['/dashboard/membership', 'Membership', 'card', request()->is('dashboard/membership*'), 0],
                ['/dashboard/payments', 'Payments', 'receipt', request()->is('dashboard/payments'), 0],
                ['/dashboard/transactions', 'Transactions', 'swap', request()->is('dashboard/transactions'), 0],
                ['/dashboard/contact-admin', 'Contact support', 'help', request()->is('dashboard/contact-admin'), 0],
            ],
        ];
    }
    $primary = $inStore
        ? ["/dashboard/stores/$sid/products/create", 'Add product']
        : ['/dashboard/listings/create', 'Post new ad'];
@endphp
<aside class="kx-sidebar" id="kxSidebar" aria-label="Dashboard navigation">
    <div class="kx-brand">
        <a href="/dashboard" class="kx-brand-mark" aria-label="Dashboard home">K</a>
        <div class="kx-brand-text">
            <strong>Kegalle</strong>
            <small>{{ $inStore ? 'Store panel' : 'Seller dashboard' }}</small>
        </div>
        <button type="button" class="kx-icon-btn kx-drawer-close" id="kxDrawerClose" aria-label="Close menu">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Context switcher: personal account ↔ each store --}}
    @if($sidebarStores->isNotEmpty())
    <div class="kx-pop kx-ctx">
        <button type="button" class="kx-ctx-btn" aria-haspopup="true" aria-expanded="false">
            <span class="kx-ctx-ic {{ $inStore ? 'is-store' : '' }}" aria-hidden="true">
                <svg viewBox="0 0 24 24">{!! $inStore ? $ic['store'] : $ic['user'] !!}</svg>
            </span>
            <span class="kx-ctx-text">
                <strong>{{ $inStore ? $store->name : 'Personal account' }}</strong>
                <small>{{ $inStore ? ucfirst($store->status ?? 'pending').' store' : 'Listings, offers, messages' }}</small>
            </span>
            <svg class="kx-caret" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 15l5 5 5-5M7 9l5-5 5 5"/></svg>
        </button>
        <div class="kx-pop-panel kx-ctx-panel" hidden>
            <a href="/dashboard" class="kx-menu-item {{ !$inStore ? 'is-current' : '' }}">Personal account</a>
            <p class="kx-menu-label">Your stores</p>
            @foreach($sidebarStores as $ss)
                <a href="/dashboard/stores/{{ $ss->id }}" class="kx-menu-item {{ $sid == $ss->id ? 'is-current' : '' }}">
                    {{ $ss->name }} <small>{{ ucfirst($ss->status ?? 'pending') }}</small>
                </a>
            @endforeach
            <a href="/dashboard/stores/create" class="kx-menu-item kx-menu-add">+ Create a new store</a>
        </div>
    </div>
    @endif

    <div class="kx-side-cta">
        <a href="{{ $primary[0] }}" class="ka-btn ka-btn-primary">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            <span class="kx-nav-text">{{ $primary[1] }}</span>
        </a>
    </div>

    <nav class="kx-nav" id="kxNav">
        @foreach($groups as $label => $items)
            <div class="kx-nav-group">
                <p class="kx-nav-label">{{ $label }}</p>
                <ul role="list">
                    @foreach($items as $it)
                        @php [$href, $text, $icon, $active, $n] = $it; $ext = $it[5] ?? false; @endphp
                        <li>
                            <a href="{{ $href }}" class="kx-nav-link {{ $active ? 'is-active' : '' }}" @if($active) aria-current="page" @endif @if($ext) target="_blank" rel="noopener" @endif data-label="{{ strtolower($text) }}" title="{{ $text }}">
                                <svg class="kx-nav-ic" viewBox="0 0 24 24" aria-hidden="true">{!! $ic[$icon] !!}</svg>
                                <span class="kx-nav-text">{{ $text }}</span>
                                @if($n)<span class="kx-badge" aria-label="{{ $n }} new">{{ $n > 99 ? '99+' : $n }}</span>@endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach

        @if(!$me->phone_verified_at)
            <a href="/phone/verify" class="kx-nav-link kx-nav-warn" title="Verify your phone">
                <svg class="kx-nav-ic" viewBox="0 0 24 24" aria-hidden="true">{!! $ic['phone'] !!}</svg>
                <span class="kx-nav-text">Verify your phone</span>
            </a>
        @endif
    </nav>

    <div class="kx-sidebar-foot">
        @if(in_array($me->role ?? '', ['admin', 'super_admin']))
        <a href="/admin" class="kx-nav-link" title="Admin panel">
            <svg class="kx-nav-ic" viewBox="0 0 24 24" aria-hidden="true">{!! $ic['shield'] !!}</svg>
            <span class="kx-nav-text">Admin panel</span>
        </a>
        @endif
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
