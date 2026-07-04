@extends('layouts.app')
@section('title', $service->title . ' · Government Services · Kegalle Marketplace')
@section('meta_description', $service->description ?: 'Government service information for '.$service->title.' in Kegalle district.')
@section('content')
<section class="cats-hero"><div class="cats-hero-inner"><h1>{{ $service->title }}</h1><p>{{ $service->description }}</p></div></section>
<div class="container" style="padding-top:32px;padding-bottom:48px">
<div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><a href="/government-services">Government Services</a><span class="sep">›</span><span class="current">{{ $service->title }}</span></div>

@if($service->content)
<div class="blog-content" style="margin-top:24px;margin-bottom:32px">{!! $service->content !!}</div>
@endif

@if($items->count())
<div class="k-gov-items-grid" style="margin-top:24px">
@foreach($items as $item)
    <div class="k-gov-item-card">
        @if($item->image)
            <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}" style="width:100%;height:140px;object-fit:cover;border-radius:var(--k-radius) var(--k-radius) 0 0">
        @endif
        <div class="k-gov-item-body">
            <h3>{{ $item->name }}</h3>
            @if($item->description)<p class="k-gov-item-desc">{{ $item->description }}</p>@endif
            <div class="k-gov-item-details">
                @if($item->phone)
                <div class="k-gov-item-row"><span class="k-gov-item-icon">📞</span><span>{{ $item->phone }}</span></div>
                @endif
                @if($item->email)
                <div class="k-gov-item-row"><span class="k-gov-item-icon">✉️</span><a href="mailto:{{ $item->email }}" style="color:var(--k-primary);text-decoration:none">{{ $item->email }}</a></div>
                @endif
                @if($item->address)
                <div class="k-gov-item-row"><span class="k-gov-item-icon">📍</span><span>{{ $item->address }}</span></div>
                @endif
            </div>
            @if($item->map_url)
            <div style="margin-top:12px;border-radius:var(--k-radius);overflow:hidden">
                <iframe src="{{ $item->map_url }}" width="100%" height="150" style="border:0" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            @endif
        </div>
    </div>
@endforeach
</div>
@endif

@if($service->phone || $service->email || $service->address)
<div style="margin-top:36px;padding:24px;background:var(--k-surface);border:1px solid var(--k-border);border-radius:var(--k-radius-lg)">
    <h3 style="font-family:var(--font-display);font-size:17px;font-weight:700;margin-bottom:14px">General Contact</h3>
    <div style="display:flex;flex-wrap:wrap;gap:24px">
        @if($service->phone)<div><strong style="font-size:12px;color:var(--k-text-tertiary);display:block;margin-bottom:2px">Phone</strong><span style="font-size:14px">{{ $service->phone }}</span></div>@endif
        @if($service->email)<div><strong style="font-size:12px;color:var(--k-text-tertiary);display:block;margin-bottom:2px">Email</strong><a href="mailto:{{ $service->email }}" style="font-size:14px;color:var(--k-primary);text-decoration:none">{{ $service->email }}</a></div>@endif
        @if($service->address)<div><strong style="font-size:12px;color:var(--k-text-tertiary);display:block;margin-bottom:2px">Address</strong><span style="font-size:14px">{{ $service->address }}</span></div>@endif
    </div>
</div>
@endif

<div style="margin-top:24px;text-align:center">
    <a href="/government-services" style="font-size:14px;color:var(--k-primary);font-weight:600;text-decoration:none">← Back to All Government Services</a>
</div>
</div>
@endsection
