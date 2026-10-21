<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="format-detection" content="telephone=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Kegalle">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<title>@yield('title','Admin') · Kegalle</title>
<meta name="robots" content="noindex,nofollow">
<meta name="theme-color" content="#ffffff">
<meta name="csrf-token" content="{{ csrf_token() }}">
{{-- Apply the saved sidebar state before first paint so the layout never jumps --}}
<script nonce="{{ $cspNonce ?? '' }}">try{if(localStorage.getItem('kx_sidebar')==='collapsed')document.documentElement.classList.add('kx-collapsed')}catch(e){}</script>
<link rel="stylesheet" href="/css/kegalle-admin.css?v=18">
<link rel="stylesheet" href="/css/kegalle-admin-ds.css?v=4">
@stack('styles')
</head>
@php
    $kxUser = auth()->user();
    $unreadNotifCount = 0;
    $latestNotifs = collect();
    try {
        $unreadNotifCount = cache()->remember('admin_unread_notif_count', 30, fn () => \App\Models\AdminNotification::where('is_read', false)->count());
        $latestNotifs = cache()->remember('admin_latest_notifs', 30, fn () => \App\Models\AdminNotification::latest()->take(6)->get());
    } catch (\Throwable $e) {}
    $searchScope = request()->is('admin/users*') ? 'users' : (request()->is('admin/stores*') ? 'stores' : 'listings');
    $searchValue = request('search', request('q'));
@endphp
<body class="ka-admin-body kx-body">
@include('partials.csp-hidden')
<a href="#kxMain" class="kx-skip">Skip to content</a>

<div class="kx-shell">
    @include('admin.partials.sidebar')
    <div class="kx-overlay" id="kxOverlay" hidden></div>

    <div class="kx-workspace">
        <header class="kx-topbar">
            <button type="button" class="kx-icon-btn kx-menu-btn" id="kxMenuBtn" aria-label="Open menu" aria-controls="kxSidebar" aria-expanded="false">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            </button>

            <nav class="kx-crumbs" aria-label="Breadcrumb">
                <ol>
                    <li><a href="/admin">Admin</a></li>
                    @hasSection('crumb_parent')
                        <li>@yield('crumb_parent')</li>
                    @endif
                    <li aria-current="page">@yield('page','Dashboard')</li>
                </ol>
            </nav>

            <form class="kx-search" action="/admin/{{ $searchScope }}" method="get" role="search" id="kxSearch">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                <label class="kx-sr" for="kxSearchInput">Search</label>
                <input id="kxSearchInput" type="search" name="{{ $searchScope === 'listings' ? 'search' : 'q' }}" value="{{ $searchValue }}" placeholder="Search…" autocomplete="off">
                <label class="kx-sr" for="kxSearchScope">Search in</label>
                <select id="kxSearchScope" class="no-select2" aria-label="Search in">
                    <option value="listings" @selected($searchScope === 'listings')>Listings</option>
                    @if($kxUser->hasPermission('users'))<option value="users" @selected($searchScope === 'users')>Users</option>@endif
                    @if($kxUser->hasPermission('stores'))<option value="stores" @selected($searchScope === 'stores')>Stores</option>@endif
                </select>
                <kbd aria-hidden="true">/</kbd>
            </form>

            <div class="kx-top-actions">
                <div class="kx-pop" id="kxNotif">
                    <button type="button" class="kx-icon-btn" aria-haspopup="true" aria-expanded="false" aria-label="Notifications{{ $unreadNotifCount ? ', '.$unreadNotifCount.' unread' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        @if($unreadNotifCount)<span class="kx-dot">{{ $unreadNotifCount > 99 ? '99+' : $unreadNotifCount }}</span>@endif
                    </button>
                    <div class="kx-pop-panel kx-notif-panel" hidden>
                        <div class="kx-pop-head">
                            <strong>Notifications</strong>
                            @if($unreadNotifCount)
                            <form method="post" action="/admin/notifications/read-all">@csrf<button type="submit" class="kx-link-btn">Mark all read</button></form>
                            @endif
                        </div>
                        @forelse($latestNotifs as $n)
                            <form method="post" action="/admin/notifications/{{ $n->id }}/read" class="kx-notif-item {{ $n->is_read ? '' : 'is-unread' }}">
                                @csrf
                                <button type="submit">
                                    <strong>{{ $n->title }}</strong>
                                    <span>{{ \Illuminate\Support\Str::limit($n->message, 90) }}</span>
                                    <small>{{ $n->created_at?->diffForHumans() }}</small>
                                </button>
                            </form>
                        @empty
                            <p class="kx-empty-sm">You're all caught up.</p>
                        @endforelse
                        <a href="/admin/notifications" class="kx-pop-foot">View all notifications</a>
                    </div>
                </div>

                <div class="kx-pop" id="kxUser">
                    <button type="button" class="kx-user-btn" aria-haspopup="true" aria-expanded="false">
                        <span class="kx-avatar" aria-hidden="true">{{ strtoupper(substr($kxUser->name ?? 'A', 0, 1)) }}</span>
                        <span class="kx-user-name">{{ $kxUser->name ?? 'Admin' }}</span>
                        <svg class="kx-caret" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="kx-pop-panel kx-user-panel" hidden>
                        <div class="kx-pop-head kx-user-head">
                            <strong>{{ $kxUser->name }}</strong>
                            <small>{{ $kxUser->email }}</small>
                        </div>
                        <a href="/" target="_blank" rel="noopener" class="kx-menu-item">View marketplace</a>
                        <a href="/dashboard" class="kx-menu-item">My seller dashboard</a>
                        <form method="POST" action="/logout">@csrf<button type="submit" class="kx-menu-item kx-menu-danger">Log out</button></form>
                    </div>
                </div>
            </div>
        </header>

        <div class="kx-flash" aria-live="polite">
            @if(session('success'))
                <div class="kx-alert kx-alert-success" role="status">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="kx-alert-x" aria-label="Dismiss">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div class="kx-alert kx-alert-danger" role="alert">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                    <span>{{ session('error') }}</span>
                    <button type="button" class="kx-alert-x" aria-label="Dismiss">&times;</button>
                </div>
            @endif
            @if($errors->any())
                <div class="kx-alert kx-alert-danger" role="alert">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                    <div>
                        <strong>Please fix {{ $errors->count() === 1 ? 'this problem' : 'these '.$errors->count().' problems' }}:</strong>
                        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                    <button type="button" class="kx-alert-x" aria-label="Dismiss">&times;</button>
                </div>
            @endif
        </div>

        <main class="ka-content kx-content" id="kxMain" tabindex="-1">
            @if(trim($__env->yieldContent('heading')) || trim($__env->yieldContent('actions')))
            <div class="kx-page-head">
                <div class="kx-page-titles">
                    <h1>@yield('heading', trim($__env->yieldContent('page')))</h1>
                    @hasSection('subheading')<p>@yield('subheading')</p>@endif
                </div>
                @hasSection('actions')
                <div class="kx-page-actions">@yield('actions')</div>
                @endif
            </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<div class="kx-dialog-backdrop" id="kxConfirm" hidden>
    <div class="kx-dialog" role="alertdialog" aria-modal="true" aria-labelledby="kxConfirmTitle" aria-describedby="kxConfirmMsg">
        <h2 id="kxConfirmTitle">Are you sure?</h2>
        <p id="kxConfirmMsg"></p>
        <div class="kx-dialog-actions">
            <button type="button" class="ka-btn ka-btn-light" data-kx-cancel>Cancel</button>
            <button type="button" class="ka-btn ka-btn-danger" data-kx-ok>Confirm</button>
        </div>
    </div>
</div>

<script defer src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script defer src="/js/select2.min.js"></script>
<script defer src="/js/kegalle-admin-ui.js?v=2"></script>
@stack('scripts')
</body>
</html>
