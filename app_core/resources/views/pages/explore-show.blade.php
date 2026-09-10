@extends('layouts.app')
@section('title', $explore->title . ' · Explore Kegalle · Kegalle Marketplace')
@section('meta_description', $explore->description ?: 'Explore '.$explore->title.' in Kegalle district — places, activities and more.')
@section('content')

<div class="k-help-hero">
    <div class="k-help-hero-inner">
        <div class="k-help-hero-badge">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Explore Kegalle
        </div>
        <h1>{{ $explore->title }}</h1>
        @if($explore->description)<p>{{ $explore->description }}</p>@endif
    </div>
</div>

<div class="k-guide-wrap">
    <div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><a href="/explore">Explore Kegalle</a><span class="sep">›</span><span class="current">{{ $explore->title }}</span></div>

    @if($explore->content)
    <div class="k-policy-body" style="margin-bottom:32px">{!! \App\Helpers\HtmlSanitizer::clean($explore->content) !!}</div>
    @endif

    @if($items->count())
    <div class="k-gov-items-grid">
    @foreach($items as $item)
        <div class="k-gov-item-card">
            @if($item->image)
                <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}" class="k-gov-item-img">
            @endif
            <div class="k-gov-item-body">
                <h3>{{ $item->name }}</h3>
                @if($item->description)<p class="k-gov-item-desc">{{ $item->description }}</p>@endif
                <div class="k-gov-item-details">
                    @if($item->phone)
                    <div class="k-gov-item-row">
                        <span class="k-gov-item-icon-wrap"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 19.79 19.79 0 01.014 1.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/></svg></span>
                        <a href="tel:{{ \App\Helpers\PhoneHelper::tel($item->phone) }}" class="k-gov-item-link">{{ \App\Helpers\PhoneHelper::format($item->phone) }}</a>
                    </div>
                    @endif
                    @if($item->email)
                    <div class="k-gov-item-row">
                        <span class="k-gov-item-icon-wrap"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
                        <a href="mailto:{{ $item->email }}" class="k-gov-item-link-primary">{{ $item->email }}</a>
                    </div>
                    @endif
                    @if($item->address)
                    <div class="k-gov-item-row">
                        <span class="k-gov-item-icon-wrap"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg></span>
                        <span>{{ $item->address }}</span>
                    </div>
                    @endif
                </div>
                @if($item->map_url)
                <div style="margin-top:12px;border-radius:var(--k-radius);overflow:hidden">
                    @php $safeMap = \App\Helpers\HtmlSanitizer::safeMapUrl($item->map_url); @endphp
                    @if($safeMap)<iframe src="{{ $safeMap }}" width="100%" height="150" style="border:0" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>@endif
                </div>
                @endif
            </div>
        </div>
    @endforeach
    </div>
    @endif

    <div class="k-explore-back"><a href="/explore">← Back to Explore Kegalle</a></div>
</div>
@endsection
