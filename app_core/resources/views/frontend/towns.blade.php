@extends('layouts.app')

@section('title', 'Towns in Kegalle District — Browse by Location · Kegalle Marketplace')
@section('meta_description', 'Explore all towns and cities in the Kegalle district. Browse products, stores and events by location — Kegalle, Mawanella, Rambukkana, Ruwanwella, Warakapola and more.')
@section('canonical', url('/towns'))

@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Towns'],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')

<div class="container k-breadcrumb-wrap">
    <div class="k-breadcrumb">
        <a href="/">Home</a><span>›</span>
        <span class="current">Towns</span>
    </div>
</div>

<section class="k-page-hero">
    <div class="container k-text-center">
        <h1 class="k-page-hero-title">Towns in Kegalle District</h1>
        <p class="k-page-hero-desc">Browse products, stores and events across every town in the Kegalle district</p>
    </div>
</section>

<section class="container k-section-bottom">
    <div class="k-town-grid">
        @foreach($towns as $town)
        <a href="/town/{{ $town->slug }}" class="k-card k-town-card">
            <div class="k-town-icon">📍</div>
            <h2 class="k-town-name">{{ $town->name }}</h2>
            <div class="k-town-count">{{ $town->listings_count }} {{ $town->listings_count === 1 ? 'ad' : 'ads' }}</div>
        </a>
        @endforeach
    </div>

    @if($towns->isEmpty())
    <div class="k-empty-state">
        <p class="k-text-md">No towns available yet.</p>
        <a href="/listings" class="k-link-primary">Browse all listings →</a>
    </div>
    @endif
</section>

@endsection
