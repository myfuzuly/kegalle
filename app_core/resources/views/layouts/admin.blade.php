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
<link rel="stylesheet" href="/css/kegalle-admin-shell.css?v=26">

<!-- ADMIN PREMIUM UI -->
<link rel="stylesheet" href="/css/kegalle-admin-store-ui-polish.css?v=22">
<link rel="stylesheet" href="/css/kegalle-admin-premium-components-select2.css?v=22">
<link rel="stylesheet" href="/css/kegalle-admin-enterprise-ux-crud-v4.css?v=26">

@stack('styles')

</head>

<body class="ka-admin-body">

<div class="ka-shell">

<div class="ka-sidebar-overlay" id="kaSidebarOverlay"></div>
@include('admin.partials.sidebar')

<section class="ka-workspace">

<header class="ka-admin-topbar">

<div style="display:flex;align-items:center;gap:10px">
<button type="button" class="ka-menu-toggle" id="kaMenuToggle" aria-label="Toggle menu">☰</button>
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