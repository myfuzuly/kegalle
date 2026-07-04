<!doctype html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>@yield('title','Super Admin') · Kegalle</title>

<meta name="robots" content="noindex,nofollow">
<meta name="theme-color" content="#1B5E20">

<!-- SELECT2 -->
<link
href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
rel="stylesheet">

<!-- ADMIN CORE -->
<link rel="stylesheet" href="/css/kegalle-admin-shell.css?v=25">

<!-- ADMIN PREMIUM UI -->
<link rel="stylesheet" href="/css/kegalle-admin-store-ui-polish.css?v=22">
<link rel="stylesheet" href="/css/kegalle-admin-premium-components-select2.css?v=22">
<link rel="stylesheet" href="/css/kegalle-admin-enterprise-ux-crud-v4.css?v=26">

@stack('styles')

</head>

<body class="ka-admin-body">

<div class="ka-shell">

@include('admin.partials.sidebar')

<section class="ka-workspace">

<header class="ka-admin-topbar">

<div>

<div class="ka-breadcrumb">

<a href="/admin">
Super Admin
</a>

<span>/</span>

<strong>
@yield('page','Dashboard')
</strong>

</div>

<h1>
@yield('heading','Dashboard')
</h1>

<p>
@yield('subheading','Marketplace Control Center · Kegalle')
</p>

</div>

<div class="ka-top-actions">

@yield('actions')

@php
    $unreadNotifCount = 0;
    $latestNotifs = collect();
    try {
        $unreadNotifCount = \App\Models\AdminNotification::where('is_read', false)->count();
        $latestNotifs = \App\Models\AdminNotification::latest()->take(8)->get();
    } catch (\Throwable $e) {}
@endphp
<div class="ka-notif-bell" id="kaNotifBell">
    <button type="button" class="ka-btn ka-btn-light" id="kaNotifBtn">
        🔔 @if($unreadNotifCount) <span class="ka-notif-badge">{{ $unreadNotifCount }}</span>@endif
    </button>
    <div class="ka-notif-dropdown" id="kaNotifDropdown">
        <div class="ka-notif-dropdown-head">
            <strong>Notifications</strong>
            <form method="post" action="/admin/notifications/read-all"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button type="submit">Mark all read</button></form>
        </div>
        @forelse($latestNotifs as $n)
            <form method="post" action="/admin/notifications/{{ $n->id }}/read" class="ka-notif-item {{ $n->is_read ? '' : 'is-unread' }}">
                @csrf
                <button type="submit">
                    <strong>{{ $n->title }}</strong>
                    <span>{{ $n->message }}</span>
                    <small>{{ $n->created_at?->diffForHumans() }}</small>
                </button>
            </form>
        @empty
            <div class="ka-notif-empty">No notifications yet.</div>
        @endforelse
        <a href="/admin/notifications" class="ka-notif-viewall">View all notifications →</a>
    </div>
</div>

<a
href="/"
target="_blank"
class="ka-btn ka-btn-light">

View Site

</a>

<form
method="POST"
action="/logout">

@csrf

<button
type="submit"
class="ka-btn ka-btn-danger">

Logout

</button>

</form>

</div>

</header>


@if(session('success'))

<div class="ka-alert">

{{ session('success') }}

</div>

@endif


@if($errors->any())

<div class="ka-alert ka-alert-danger">

@foreach($errors->all() as $error)

<div>
{{ $error }}
</div>

@endforeach

</div>

@endif


<main class="ka-content">

@yield('content')

</main>

</section>

</div>


<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script
src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js">
</script>

<script src="/js/kegalle-admin-shell.js?v=22"></script>
<script src="/js/kegalle-admin-premium-components-select2.js?v=22"></script>
<script src="/js/kegalle-admin-enterprise-ux-crud-v4.js?v=22"></script>

@stack('scripts')

<script>
(function () {
    var btn = document.getElementById('kaNotifBtn');
    var dropdown = document.getElementById('kaNotifDropdown');
    if (!btn || !dropdown) return;
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.classList.toggle('open');
    });
    document.addEventListener('click', function (e) {
        if (!document.getElementById('kaNotifBell').contains(e.target)) {
            dropdown.classList.remove('open');
        }
    });
})();
</script>

</body>
</html>