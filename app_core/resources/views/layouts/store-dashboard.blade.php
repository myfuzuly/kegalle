<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title','Store Dashboard') · Kegalle</title>
<meta name="robots" content="noindex,nofollow">
<link rel="stylesheet" href="/css/kegalle-dashboard-panel.css?v=1">
<script defer src="/js/kegalle-dashboard-panel.js?v=1"></script>
@stack('styles')
</head>
<body class="kd-body kd-store-body">
<div class="kd-shell">
@include('dashboard.stores.partials.sidebar')
<section class="kd-workspace">
<header class="kd-topbar">
<div><span>@yield('eyebrow','Store Panel')</span><h1>@yield('heading','Store Dashboard')</h1><p>@yield('subheading','Manage your store profile, products and enquiries.')</p></div>
<div class="kd-actions">@yield('actions')<a href="/" target="_blank" class="kd-btn kd-btn-light">View Site</a><form method="POST" action="/logout">@csrf<button class="kd-btn kd-btn-danger" type="submit">Logout</button></form></div>
</header>
@if(session('success'))<div class="kd-alert">{{ session('success') }}</div>@endif
@if($errors->any())<div class="kd-alert kd-alert-danger">{{ $errors->first() }}</div>@endif
<main class="kd-content">@yield('content')</main>
</section>
</div>
@stack('scripts')
</body>
</html>
