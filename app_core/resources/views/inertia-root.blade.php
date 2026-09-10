<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@inertiaHead</title>
<meta name="robots" content="noindex,nofollow">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#0f172a">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="/css/kegalle-dashboard.css?v=21">
@vite(['resources/js/inertia.js'], 'vite-dist')
</head>
<body class="kd-body">
@inertia
</body>
</html>
