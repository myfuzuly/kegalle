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
<title>@yield('title','Dashboard') · Kegalle Marketplace</title>
<meta name="robots" content="noindex,nofollow">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#ffffff">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
<script nonce="{{ $cspNonce ?? '' }}">try{if(localStorage.getItem('kx_sidebar')==='collapsed')document.documentElement.classList.add('kx-collapsed')}catch(e){}</script>
<link rel="stylesheet" href="/css/kegalle-dashboard.css?v=28">
<link rel="stylesheet" href="/css/kegalle-admin-ds.css?v=5">
@stack('styles')
</head>
@php
    $kxUser = auth()->user();
    $kxUnread = 0;
    try { $kxUnread = cache()->remember('sidebar_unread_notif_'.$kxUser->id, 30, fn () => \App\Models\UserNotification::where('user_id', $kxUser->id)->whereNull('read_at')->count()); } catch (\Throwable) {}
@endphp
<body class="kd-body kx-body kx-seller">
@include('partials.csp-hidden')
<a href="#kxMain" class="kx-skip">Skip to content</a>

<div class="kx-shell">
    @include('dashboard.partials.sidebar')
    <div class="kx-overlay" id="kxOverlay" hidden></div>

    <div class="kx-workspace">
        <header class="kx-topbar">
            <button type="button" class="kx-icon-btn kx-menu-btn" id="kxMenuBtn" aria-label="Open menu" aria-controls="kxSidebar" aria-expanded="false">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            </button>

            <nav class="kx-crumbs" aria-label="Breadcrumb">
                <ol>
                    <li><a href="/dashboard">Dashboard</a></li>
                    @if(trim($__env->yieldContent('eyebrow')) && trim($__env->yieldContent('eyebrow')) !== 'Dashboard' && trim($__env->yieldContent('eyebrow')) !== trim($__env->yieldContent('heading')))
                        <li>@yield('eyebrow')</li>
                    @endif
                    @if(!request()->is('dashboard'))
                        <li aria-current="page">@yield('heading', trim($__env->yieldContent('title')))</li>
                    @endif
                </ol>
            </nav>

            <div class="kx-top-actions">
                <div class="kx-pop" id="kxNew">
                    <button type="button" class="ka-btn ka-btn-primary kx-new-btn" aria-haspopup="true" aria-expanded="false">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg><span>New</span>
                    </button>
                    <div class="kx-pop-panel kx-user-panel" hidden>
                        <a href="/dashboard/listings/create" class="kx-menu-item">Post an ad</a>
                        <a href="/dashboard/deals/create" class="kx-menu-item">Post a deal</a>
                        <a href="/dashboard/services/create" class="kx-menu-item">Post a service</a>
                        <a href="/dashboard/stores/create" class="kx-menu-item">Create a store</a>
                    </div>
                </div>

                <a href="/dashboard/notifications" class="kx-icon-btn" aria-label="Notifications{{ $kxUnread ? ', '.$kxUnread.' unread' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    @if($kxUnread)<span class="kx-dot">{{ $kxUnread > 99 ? '99+' : $kxUnread }}</span>@endif
                </a>

                <div class="kx-pop" id="kxUser">
                    <button type="button" class="kx-user-btn" aria-haspopup="true" aria-expanded="false">
                        <span class="kx-avatar" aria-hidden="true">{{ strtoupper(substr($kxUser->name ?? 'U', 0, 1)) }}</span>
                        <span class="kx-user-name">{{ $kxUser->name ?? 'My account' }}</span>
                        <svg class="kx-caret" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="kx-pop-panel kx-user-panel" hidden>
                        <div class="kx-pop-head kx-user-head">
                            <strong>{{ $kxUser->name }}</strong>
                            <small>{{ $kxUser->email }}</small>
                        </div>
                        <a href="/dashboard/profile" class="kx-menu-item">Profile settings</a>
                        <a href="/dashboard/membership" class="kx-menu-item">Membership</a>
                        <a href="/" target="_blank" rel="noopener" class="kx-menu-item">View marketplace</a>
                        @if(in_array($kxUser->role ?? '', ['admin', 'super_admin']))<a href="/admin" class="kx-menu-item">Admin panel</a>@endif
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

        <main class="kdl-content kx-content" id="kxMain" tabindex="-1">
            <div class="kx-page-head">
                <div class="kx-page-titles">
                    <h1>
                        @if(request()->is('dashboard'))Hello, {{ \Illuminate\Support\Str::of($kxUser->name ?? 'there')->before(' ') }}
                        @else @yield('heading', trim($__env->yieldContent('title')) ?: 'Dashboard') @endif
                    </h1>
                    @if(trim($__env->yieldContent('subheading')))<p>@yield('subheading')</p>
                    @elseif(trim($__env->yieldContent('banner_sub')))<p>@yield('banner_sub')</p>
                    @elseif(request()->is('dashboard'))<p>Here's what's happening with your listings and buyers.</p>@endif
                </div>
                @hasSection('actions')
                <div class="kx-page-actions">@yield('actions')</div>
                @endif
            </div>

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

<script defer src="/js/kegalle-dashboard-panel.js?v=1"></script>
<script defer src="/js/kegalle-admin-ui.js?v=3"></script>
@stack('scripts')
</body>
</html>
