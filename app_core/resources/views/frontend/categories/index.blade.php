@extends('layouts.app')

@section('title','Browse All Categories · Kegalle Marketplace')
@section('meta_description','Explore listings by category in Kegalle — electronics, vehicles, property, fashion, jobs, services and more. Find exactly what you need, fast.')
@section('canonical', url('/categories'))

@section('content')
@php
    $totalAds = $categories->sum('listings_count');
    $topCats = $categories->whereNull('parent_id')->sortByDesc(function($c) use ($childMap) {
        return $c->listings_count + ($childMap->get($c->id, collect()))->sum('listings_count');
    })->take(8);
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

<div class="container k-content-section">
    <div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><span class="current">All Categories</span></div>

    <!-- Popular quick strip -->
    <div class="k-section">
        <div class="k-section-header"><h2 class="k-section-title">Popular Categories</h2></div>
        <div class="cat-popular-strip">
            @foreach($topCats as $tc)
                @php $tcAds = $tc->listings_count + ($childMap->get($tc->id, collect()))->sum('listings_count'); @endphp
                <a href="/listings?categories[]={{ $tc->slug }}" class="cat-pill {{ $loop->first ? 'active' : '' }}">
                    @if(!empty($tc->image))
                        <img src="{{ asset('storage/'.$tc->image) }}" alt="" class="icon-img">
                    @else
                        <span class="icon">{{ $tc->icon ?: '🛒' }}</span>
                    @endif
                    <span class="label">{{ $tc->name }}</span>
                    <span class="ads">{{ $tcAds }} ads</span>
                </a>
            @endforeach
        </div>
    </div>

    @include('frontend.partials.ad-banner', ['location' => 'category_page', 'style' => 'top'])

    <!-- All Categories -->
    <div class="cat-section-head">
        <h2>All Categories</h2>
        <span class="cat-section-meta">{{ $parents->count() }} categories · {{ number_format($totalAds) }} active ads</span>
    </div>

    <div class="cat-grid">
        @foreach($parents as $parent)
            @php
                $children = $childMap->get($parent->id, collect());
                $parentAdCount = $parent->listings_count + $children->sum('listings_count');
                $visibleCount = 6;
                $hasMore = $children->count() > $visibleCount;
            @endphp
            <div class="cat-card">
                <a href="/listings?categories[]={{ $parent->slug }}" class="cat-card-head">
                    <div class="cat-card-icon">
                        @if(!empty($parent->image))
                            <img src="{{ asset('storage/'.$parent->image) }}" alt="">
                        @else
                            <span>{{ $parent->icon ?: '🛒' }}</span>
                        @endif
                    </div>
                    <div class="cat-card-info">
                        <h3>{{ $parent->name }}</h3>
                        <p>{{ number_format($parentAdCount) }} ads</p>
                    </div>
                    <svg class="cat-card-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                </a>
                @if($children->count())
                    <div class="cat-card-subs" id="subs-{{ $parent->id }}">
                        @foreach($children->take($visibleCount) as $child)
                            <a href="/listings?categories[]={{ $child->slug }}" class="cat-sub-row">
                                <span class="cat-sub-name">{{ $child->name }}</span>
                                @if($child->listings_count > 0)
                                    <span class="cat-sub-badge">{{ $child->listings_count }}</span>
                                @endif
                            </a>
                        @endforeach
                        @if($hasMore)
                            <div class="cat-sub-more-wrap" id="more-{{ $parent->id }}" style="display:none">
                                @foreach($children->slice($visibleCount) as $child)
                                    <a href="/listings?categories[]={{ $child->slug }}" class="cat-sub-row">
                                        <span class="cat-sub-name">{{ $child->name }}</span>
                                        @if($child->listings_count > 0)
                                            <span class="cat-sub-badge">{{ $child->listings_count }}</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                            <button class="cat-show-more" onclick="toggleSubs({{ $parent->id }}, this)">
                                <span>+{{ $children->count() - $visibleCount }} more</span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                            </button>
                        @endif
                    </div>
                @endif
                <a href="/listings?categories[]={{ $parent->slug }}" class="cat-card-foot">
                    <span>View all {{ $parent->name }}</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>
        @endforeach
    </div>
</div>

<script>
function toggleSubs(id, btn) {
    var el = document.getElementById('more-' + id);
    if (el.style.display === 'none') {
        el.style.display = 'block';
        btn.querySelector('span').textContent = 'Show less';
        btn.querySelector('svg').style.transform = 'rotate(180deg)';
    } else {
        el.style.display = 'none';
        var total = el.querySelectorAll('.cat-sub-row').length;
        btn.querySelector('span').textContent = '+' + total + ' more';
        btn.querySelector('svg').style.transform = '';
    }
}
</script>
@endsection
