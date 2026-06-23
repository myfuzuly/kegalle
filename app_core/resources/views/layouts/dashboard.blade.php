<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title','User Dashboard') · Kegalle</title>
<meta name="robots" content="noindex,nofollow">
<link rel="stylesheet" href="/css/kegalle-dashboard-panel.css?v=2">
<script defer src="/js/kegalle-dashboard-panel.js?v=1"></script>
@stack('styles')
</head>
<body class="kd-body">
<div class="kd-shell">
@include('dashboard.partials.sidebar')
<section class="kd-workspace">
<header class="kd-topbar">
<div><span>@yield('eyebrow','User Panel')</span><h1>@yield('heading','Dashboard')</h1><p>@yield('subheading','Manage your personal ads and marketplace activity.')</p></div>
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
