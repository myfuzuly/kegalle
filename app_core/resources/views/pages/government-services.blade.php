@extends('layouts.app')
@section('title','Government Services · Kegalle Marketplace')
@section('meta_description','Find local government services in Kegalle district — DS Office, Municipal Council, Police, Hospitals, Schools, Public Services, Waste Collection, Road Closures and Announcements.')
@section('content')
<section class="cats-hero"><div class="cats-hero-inner"><h1>Government Services</h1><p>Essential public services in the Kegalle district — all in one place.</p></div></section>
<div class="container" style="padding-top:32px;padding-bottom:48px">
<div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><span class="current">Government Services</span></div>

<div class="k-gov-grid" style="margin-top:28px">
@forelse($services as $service)
    <a href="/government-services/{{ $service->slug }}" class="k-gov-card">
        <div class="k-gov-icon" style="background:linear-gradient(135deg,{{ $service->icon_bg_start }},{{ $service->icon_bg_end }})">
            @if($service->image)
                <img src="{{ asset('storage/'.$service->image) }}" alt="{{ $service->title }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit">
            @else
                <span style="font-size:24px">{{ $service->icon }}</span>
            @endif
        </div>
        <h3>{{ $service->title }}</h3>
        @if($service->description)<p>{{ $service->description }}</p>@endif
        <span class="k-gov-arrow">→</span>
    </a>
@empty
    <p style="padding:30px;text-align:center;color:var(--k-text-tertiary);grid-column:1/-1">No government services listed yet — check back soon.</p>
@endforelse
</div>

<div style="margin-top:40px;padding:24px;background:var(--k-primary-xlight);border-radius:var(--k-radius-lg)">
    <h3 style="font-family:var(--font-display);font-size:17px;font-weight:700;color:var(--k-text-primary);margin-bottom:8px">Need help finding a service?</h3>
    <p style="font-size:14px;color:var(--k-text-secondary);margin-bottom:14px">If you can't find what you're looking for, get in touch and we'll point you in the right direction.</p>
    <a href="/contact-us" class="k-btn k-btn-primary k-btn-sm" style="text-decoration:none">Contact Us</a>
</div>
</div>
@endsection
