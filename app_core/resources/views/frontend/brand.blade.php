@extends('layouts.app')

@section('title', $brand->name . ' — All ' . $brand->name . ' Listings · Kegalle Marketplace')
@section('meta_description', 'Browse all ' . $brand->name . ' products and listings in Kegalle. Find the best deals on ' . $brand->name . ' items from verified sellers.')
@section('canonical', url('/brand/' . $brand->slug))

@section('content')
<div class="k-brand-hero">
    <div class="container">
        <nav class="k-breadcrumb"><a href="/">Home</a> › <a href="/listings">All Ads</a> › {{ $brand->name }}</nav>
        <h1>{{ $brand->name }}</h1>
        <p>{{ $totalCount }} listing{{ $totalCount !== 1 ? 's' : '' }} available from verified sellers in Kegalle</p>
    </div>
</div>

<div class="container k-content-section">
    <div class="k-layout-sidebar">
        <div class="k-filter-sidebar">
            <div class="k-filter-header"><span class="k-filter-title">Filter by Category</span><a href="/brand/{{ $brand->slug }}" class="k-filter-clear">Clear</a></div>
            <div class="k-filter-section">
                <div class="k-filter-option">
                    <a href="/brand/{{ $brand->slug }}" class="k-filter-cat-link {{ !request('category') ? 'k-filter-label-active' : '' }}"><span>All Categories</span><span class="cnt">{{ $totalCount }}</span></a>
                </div>
                @foreach($categories as $category)
                    <div class="k-filter-option">
                        <a href="/brand/{{ $brand->slug }}?category={{ $category->slug }}" class="k-filter-cat-link {{ request('category') === $category->slug ? 'k-filter-label-active' : '' }}">
                            <span>{{ $category->icon ?? '🏷️' }} {{ $category->name }}</span><span class="cnt">{{ $category->listings_count }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <div>
            <div class="k-listings-toolbar">
                <span class="k-listings-count">Showing <strong>{{ $listings->firstItem() ?? 0 }}–{{ $listings->lastItem() ?? 0 }}</strong> of <strong>{{ $listings->total() }} listings</strong></span>
                <form method="GET" class="k-sort-row">
                    @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                    <span class="k-sort-label">Sort:</span>
                    <select name="sort" class="k-form-select k-sort-select-sm" onchange="this.form.submit()">
                        <option value="">Newest First</option>
                        <option value="price_low" @selected(request('sort')=='price_low')>Price: Low → High</option>
                        <option value="price_high" @selected(request('sort')=='price_high')>Price: High → Low</option>
                        <option value="oldest" @selected(request('sort')=='oldest')>Oldest First</option>
                    </select>
                </form>
            </div>

            <div class="k-grid-4 k-grid-tight">
                @forelse($listings as $listing)
                    @include('frontend.listings.card', ['listing' => $listing])
                @empty
                    <div class="k-empty-state-box k-text-tertiary" style="grid-column:1/-1">
                        <h3 class="k-empty-state-heading">No {{ $brand->name }} listings found</h3>
                        <p class="k-text-secondary">Try selecting a different category or check back later.</p>
                    </div>
                @endforelse
            </div>

            {{ $listings->links('vendor.pagination.k-theme') }}
        </div>
    </div>
</div>
@endsection
