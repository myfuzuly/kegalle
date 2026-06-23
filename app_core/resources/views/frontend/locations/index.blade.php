@extends('layouts.app')

@section('title','All Locations · Kegalle Marketplace')

@section('content')
@php
    $palette = ['#E3F2FD','#E8F5E9','#FFF8E1','#F3E5F5','#FFF3E0','#E0F2F1','#FFEBEE','#EDE7F6'];
    $totalAds = $locations->sum('listings_count');
@endphp

<!-- Hero -->
<section class="cats-hero">
    <div class="cats-hero-inner">
        <h1>Browse All Locations</h1>
        <p>Find listings near you across every town in the Kegalle district.</p>
        <form class="cats-hero-search" action="/listings" method="GET">
            <input type="text" name="q" placeholder="Search by town or area...">
            <button type="submit">Search</button>
        </form>
    </div>
</section>

<div class="container" style="padding-top:32px;padding-bottom:48px">
    <div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><span class="current">All Locations</span></div>

    <!-- Popular quick strip -->
    <div class="k-section">
        <div class="k-section-header"><h2 class="k-section-title">Popular Locations</h2></div>
        <div class="cat-popular-strip">
            @foreach($locations->sortByDesc('listings_count')->take(8) as $location)
                <a href="/listings?location={{ $location->name }}" class="cat-pill {{ $loop->first ? 'active' : '' }}">
                    <span class="icon">📍</span>
                    <span class="label">{{ $location->name }}</span>
                    <span class="ads">{{ $location->listings_count }} ads</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Full mega location grid -->
    <div class="k-section-header"><h2 class="k-section-title">All Locations</h2><span style="font-size:13px;color:var(--k-text-tertiary)">{{ $locations->count() }} towns · {{ number_format($totalAds) }} active ads</span></div>
    <div class="k-grid-3" style="gap:20px">
        @foreach($locations as $location)
            @php $bg = $palette[$loop->index % count($palette)]; @endphp
            <a href="/listings?location={{ $location->name }}" class="cat-mega-card" style="text-decoration:none;color:inherit">
                <div class="cat-mega-header">
                    <div class="cat-mega-icon" style="background:{{ $bg }}">📍</div>
                    <div>
                        <div class="cat-mega-name">{{ $location->name }}</div>
                        <div class="cat-mega-count">{{ number_format($location->listings_count) }} active ads</div>
                    </div>
                </div>
                <div class="cat-mega-footer"><span class="cat-view-all">View all in {{ $location->name }} →</span></div>
            </a>
        @endforeach
    </div>
</div>
@endsection
