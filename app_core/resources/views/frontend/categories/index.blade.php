@extends('layouts.app')

@section('title','All Categories · Kegalle Marketplace')

@section('content')
@php
    $palette = ['#E3F2FD','#E8F5E9','#FFF8E1','#F3E5F5','#FFF3E0','#E0F2F1','#FFEBEE','#EDE7F6'];
    $totalAds = $categories->sum('listings_count');
@endphp

<!-- Hero -->
<section class="cats-hero">
    <div class="cats-hero-inner">
        <h1>Browse All Categories</h1>
        <p>Explore thousands of listings across every category in Kegalle.</p>
        <form class="cats-hero-search" action="/listings" method="GET">
            <input type="text" name="q" placeholder="Search categories or items...">
            <button type="submit">Search</button>
        </form>
    </div>
</section>

<div class="container" style="padding-top:32px;padding-bottom:48px">
    <div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><span class="current">All Categories</span></div>

    <!-- Popular quick strip -->
    <div class="k-section">
        <div class="k-section-header"><h2 class="k-section-title">Popular Categories</h2></div>
        <div class="cat-popular-strip">
            @foreach($categories->sortByDesc('listings_count')->take(8) as $category)
                <a href="/listings?categories[]={{ $category->slug }}" class="cat-pill {{ $loop->first ? 'active' : '' }}">
                    <span class="icon">{{ $category->icon ?: '🛒' }}</span>
                    <span class="label">{{ $category->name }}</span>
                    <span class="ads">{{ $category->listings_count }} ads</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Full mega category grid -->
    <div class="k-section-header"><h2 class="k-section-title">All Categories</h2><span style="font-size:13px;color:var(--k-text-tertiary)">{{ $categories->count() }} categories · {{ number_format($totalAds) }} active ads</span></div>
    <div class="k-grid-3" style="gap:20px">
        @foreach($categories as $category)
            @php $bg = $palette[$loop->index % count($palette)]; @endphp
            <a href="/listings?categories[]={{ $category->slug }}" class="cat-mega-card" style="text-decoration:none;color:inherit">
                <div class="cat-mega-header">
                    <div class="cat-mega-icon" style="background:{{ $bg }}">{{ $category->icon ?: '🛒' }}</div>
                    <div>
                        <div class="cat-mega-name">{{ $category->name }}</div>
                        <div class="cat-mega-count">{{ number_format($category->listings_count) }} active ads</div>
                    </div>
                </div>
                <div class="cat-mega-footer"><span class="cat-view-all">View all {{ $category->name }} →</span></div>
            </a>
        @endforeach
    </div>
</div>
@endsection
