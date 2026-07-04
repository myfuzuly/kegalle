@extends('layouts.app')

@section('title', $town->name . ' — Buy, Sell & Discover in ' . $town->name . ', Kegalle District · Kegalle Marketplace')
@section('meta_description', 'Browse products, stores, events and classified ads in ' . $town->name . ', Kegalle District, Sri Lanka. Find local businesses and deals near you on Kegalle Marketplace.')

@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Place',
    'name' => $town->name,
    'address' => [
        '@type' => 'PostalAddress',
        'addressLocality' => $town->name,
        'addressRegion' => 'Sabaragamuwa',
        'addressCountry' => 'LK',
    ],
    'url' => url('/town/'.$town->slug),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Locations', 'item' => url('/locations')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $town->name],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')

<div class="container k-breadcrumb-wrap">
    <div class="k-breadcrumb">
        <a href="/">Home</a><span>›</span>
        <a href="/locations">Locations</a><span>›</span>
        <span class="current">{{ $town->name }}</span>
    </div>
</div>

{{-- Hero --}}
<section class="k-page-hero k-page-hero--narrow">
    <div class="container k-text-center">
        <h1 class="k-page-hero-title">{{ $town->name }}</h1>
        <p class="k-page-hero-desc">Buy, sell and discover products, stores and events in {{ $town->name }}, Kegalle District</p>
    </div>
</section>

{{-- Listings --}}
<section class="container mb-48">
    <div class="k-section-header-flex">
        <h2 class="k-section-title">Products & Ads in {{ $town->name }}</h2>
        <a href="/listings?location={{ $town->slug }}" class="k-link-primary k-text-sm">View All →</a>
    </div>

    @if($listings->count())
    <div class="k-grid-4">
        @foreach($listings as $listing)
            @include('frontend.listings.card', ['listing' => $listing])
        @endforeach
    </div>
    <div class="mt-24">{{ $listings->links('vendor.pagination.k-theme') }}</div>
    @else
    <div class="k-empty-state">
        <div class="k-empty-state-icon">📦</div>
        <div class="k-empty-state-title">No listings yet in {{ $town->name }}</div>
        <div class="k-empty-state-text">Be the first to post an ad here, or <a href="/listings" class="k-link-primary">browse all listings →</a></div>
    </div>
    @endif
</section>

{{-- Stores --}}
@if($stores->count())
<section class="k-section-alt mb-48">
    <div class="container">
        <h2 class="k-section-title mb-20">Stores in {{ $town->name }}</h2>
        <div class="k-grid k-grid-4">
            @foreach($stores as $store)
            @php $sLogo = !empty($store->logo) ? asset('storage/'.ltrim($store->logo,'/')) : null; @endphp
            <a href="/store/{{ $store->slug }}" class="k-card k-town-store-card">
                <div class="k-town-store-icon">
                    @if($sLogo)<img src="{{ $sLogo }}" alt="{{ $store->name }}" class="k-cover-img">@else{{ strtoupper(substr($store->name,0,1)) }}@endif
                </div>
                <h3 class="k-town-store-name">{{ $store->name }}</h3>
                <div class="k-town-store-count">{{ $store->listings_count ?? 0 }} products</div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Events --}}
@if($events->count())
<section class="container mb-48">
    <h2 class="k-section-title mb-20">Upcoming Events in {{ $town->name }}</h2>
    <div class="k-grid k-grid-3">
        @foreach($events as $event)
        <a href="/events/{{ $event->slug }}" class="k-card k-town-event-card">
            <div class="k-town-event-date">{{ $event->event_date->format('d M Y') }}</div>
            <h3 class="k-town-event-title">{{ $event->title }}</h3>
            <div class="k-town-event-venue">📍 {{ $event->venue ?? $event->location }}</div>
        </a>
        @endforeach
    </div>
</section>
@endif

{{-- Other Towns --}}
<section class="k-section-alt">
    <div class="container">
        <h2 class="k-section-title mb-20">Explore More Towns</h2>
        <div class="k-town-pill-wrap">
            @foreach($allTowns as $t)
            <a href="/town/{{ $t->slug }}" class="k-town-pill">{{ $t->name }}</a>
            @endforeach
        </div>
    </div>
</section>

@endsection
