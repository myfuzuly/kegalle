<!doctype html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>@yield('title','Super Admin') · Kegalle</title>

<meta name="robots" content="noindex,nofollow">
<meta name="theme-color" content="#1B5E20">
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="/css/kegalle-admin.css?v=13">
{{-- @vite(['resources/css/admin.css', 'resources/js/admin.js'], 'vite-dist') --}}
@stack('styles')

</head>

<body class="ka-admin-body">

<div class="ka-shell">

<div class="ka-sidebar-overlay" id="kaSidebarOverlay"></div>
@include('admin.partials.sidebar')

<section class="ka-workspace">

<header class="ka-admin-topbar">

    {{-- Left: hamburger + breadcrumb --}}
    <div>
        <button type="button" class="ka-menu-toggle" id="kaMenuToggle" aria-label="Toggle menu">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="2" y1="5" x2="16" y2="5"/><line x1="2" y1="9" x2="16" y2="9"/><line x1="2" y1="13" x2="16" y2="13"/></svg>
        </button>
        <nav class="ka-breadcrumb">
            <a href="/admin">Super Admin</a>
            <span>/</span>
            <strong>@yield('page','Dashboard')</strong>
        </nav>
    </div>

    {{-- Search --}}
    <form class="ka-topbar-search" action="/admin/listings" method="get" role="search">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="search" placeholder="Search listings, users…" autocomplete="off" value="{{ request('search') }}">
    </form>

    {{-- Right: all actions in one flex row --}}
    <div class="ka-top-actions">

        {{-- Page-level action buttons --}}
        @yield('actions')

        {{-- Notification bell --}}
        @php
            $unreadNotifCount = 0;
            $latestNotifs = collect();
            try {
                $unreadNotifCount = cache()->remember('admin_unread_notif_count', 30, fn () =>
                    \App\Models\AdminNotification::where('is_read', false)->count()
                );
                $latestNotifs = cache()->remember('admin_latest_notifs', 30, fn () =>
                    \App\Models\AdminNotification::latest()->take(8)->get()
                );
            } catch (\Throwable $e) {}
        @endphp
        <div class="ka-notif-bell" id="kaNotifBell">
            <button type="button" class="ka-btn ka-btn-light ka-notif-btn-pad" id="kaNotifBtn" title="Notifications">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                @if($unreadNotifCount)<span class="ka-notif-badge">{{ $unreadNotifCount }}</span>@endif
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

        {{-- User chip --}}
        <div class="kaa-userchip" title="{{ auth()->user()->email ?? '' }}">
            <div class="kaa-userchip-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}</div>
            <span class="kaa-userchip-name">{{ auth()->user()->name ?? 'Admin' }}</span>
        </div>

        {{-- View Site --}}
        <a href="/" target="_blank" class="ka-btn ka-btn-light ka-topbar-btn-sm">View Site</a>

        {{-- Logout --}}
        <form method="POST" action="/logout" class="m-0-form">
            @csrf
            <button type="submit" class="ka-btn ka-btn-danger ka-topbar-btn-sm">Logout</button>
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

@if(trim($__env->yieldContent('heading')))
<div class="kap-page-head">
    <div>
        <h1 class="kap-heading">@yield('heading')</h1>
        @if(trim($__env->yieldContent('subheading')))
        <p class="kap-subheading">@yield('subheading')</p>
        @endif
    </div>
</div>
@endif

@yield('content')

</main>

</section>

</div>


<!-- JS — all deferred so they never block rendering -->
<script defer src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script defer src="/js/select2.min.js"></script>
<script defer src="/js/kegalle-admin-shell.js?v=23"></script>
<script defer src="/js/kegalle-admin-premium-components-select2.js?v=24"></script>
<script defer src="/js/kegalle-admin-enterprise-ux-crud-v4.js?v=23"></script>

@stack('scripts')

<script nonce="{{ $cspNonce ?? '' }}">
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

// Collapsible nav groups — with active-section auto-expand
(function(){
    document.querySelectorAll('.ka-nav-title-btn').forEach(function(btn){
        var section = btn.nextElementSibling;
        if(!section) return;
        var key = 'ka_nav_' + btn.dataset.group;
        var hasActive = section.querySelector('.ka-nav.active') !== null;
        // Force-open the section that contains the active nav link
        if(hasActive){
            btn.classList.remove('nt-collapsed');
            section.classList.remove('nt-collapsed');
            section.style.maxHeight = section.scrollHeight + 'px';
            localStorage.setItem(key, '0');
        } else if(localStorage.getItem(key) === '1'){
            btn.classList.add('nt-collapsed');
            section.classList.add('nt-collapsed');
            section.style.maxHeight = '0';
        } else {
            section.style.maxHeight = section.scrollHeight + 'px';
        }
        btn.addEventListener('click', function(){
            var collapsed = btn.classList.toggle('nt-collapsed');
            section.classList.toggle('nt-collapsed', collapsed);
            section.style.maxHeight = collapsed ? '0' : section.scrollHeight + 'px';
            localStorage.setItem(key, collapsed ? '1' : '0');
        });
    });
})();

// Keyboard shortcut: "/" focuses topbar search
(function(){
    var inp = document.querySelector('.ka-topbar-search input');
    if(!inp) return;
    document.addEventListener('keydown', function(e){
        if(e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA' && document.activeElement.tagName !== 'SELECT'){
            e.preventDefault();
            inp.focus();
            inp.select();
        }
        if(e.key === 'Escape' && document.activeElement === inp){
            inp.blur();
        }
    });
})();

// Approve / Reject button loading state on form submit
(function(){
    document.querySelectorAll('form').forEach(function(form){
        var btn = form.querySelector('.btn-approve-green, .btn-reject-red, .ka-btn-primary, .ka-btn-danger');
        if(!btn) return;
        form.addEventListener('submit', function(){
            btn.classList.add('ka-loading');
            btn.disabled = true;
        });
    });
})();

// Sidebar hamburger toggle
(function(){
    var tog=document.getElementById('kaMenuToggle'),
        sb=document.getElementById('kaSidebar'),
        ov=document.getElementById('kaSidebarOverlay');
    if(!tog||!sb)return;
    function open(){sb.classList.add('open');ov.classList.add('open');document.body.style.overflow='hidden';tog.textContent='✕';}
    function close(){sb.classList.remove('open');ov.classList.remove('open');document.body.style.overflow='';tog.textContent='☰';}
    tog.addEventListener('click',function(){sb.classList.contains('open')?close():open();});
    ov.addEventListener('click',close);
    sb.querySelectorAll('.ka-nav').forEach(function(a){a.addEventListener('click',close);});
})();

// Inline form validation
(function(){
    document.querySelectorAll('.ka-premium-form, .kd-form').forEach(function(form){
        form.setAttribute('novalidate','');
        form.addEventListener('submit',function(e){
            var ok=true;
            form.querySelectorAll('.ka-field-error').forEach(function(f){f.classList.remove('ka-field-error');});
            form.querySelectorAll('.ka-field-error-msg').forEach(function(m){m.remove();});
            form.querySelectorAll('[required]').forEach(function(inp){
                var val=(inp.value||'').trim();
                if(!val){
                    ok=false;
                    var wrap=inp.closest('.ka-field')||inp.closest('label')||inp.parentElement;
                    wrap.classList.add('ka-field-error');
                    var msg=document.createElement('small');
                    msg.className='ka-field-error-msg';
                    msg.textContent='This field is required';
                    inp.insertAdjacentElement('afterend',msg);
                }
            });
            form.querySelectorAll('[type="email"]').forEach(function(inp){
                var val=(inp.value||'').trim();
                if(val&&!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)){
                    ok=false;
                    var wrap=inp.closest('.ka-field')||inp.closest('label')||inp.parentElement;
                    wrap.classList.add('ka-field-error');
                    var msg=document.createElement('small');
                    msg.className='ka-field-error-msg';
                    msg.textContent='Please enter a valid email';
                    inp.insertAdjacentElement('afterend',msg);
                }
            });
            if(!ok){e.preventDefault();form.querySelector('.ka-field-error-msg').scrollIntoView({behavior:'smooth',block:'center'});}
        });
        form.addEventListener('input',function(e){
            var wrap=e.target.closest('.ka-field')||e.target.closest('label')||e.target.parentElement;
            if(wrap){wrap.classList.remove('ka-field-error');var m=wrap.querySelector('.ka-field-error-msg');if(m)m.remove();}
        });
    });
})();
</script>

</body>
</html>