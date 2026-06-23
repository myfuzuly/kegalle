@extends('layouts.app')

@section('title','All Ads — Browse Classifieds & Products in Kegalle · Kegalle Marketplace')
@section('meta_description','Browse all classified ads and products for sale in Kegalle and nearby towns. Filter by category, location and price to find exactly what you need.')
@section('canonical', url('/listings'))

@section('content')
<div class="container" style="padding-top:28px;padding-bottom:40px">
    <h1 style="font-family:var(--font-display);font-size:24px;font-weight:800;margin-bottom:4px">All Ads in Kegalle</h1>
    <p style="color:var(--k-text-secondary);font-size:14px;margin-bottom:20px">Browse classifieds and products from trusted local sellers across the Kegalle district.</p>
    <div class="k-listing-page-grid">
        <!-- Filter Sidebar -->
        <div class="k-filter-sidebar">
            <div class="k-filter-header">
                <span class="k-filter-title">🔽 Filter</span>
                <a href="/listings" class="k-filter-clear">Clear All</a>
            </div>
            <form method="GET" action="/listings">
                <div class="k-filter-section">
                    <h4>Type <span>−</span></h4>
                    <div class="k-filter-option"><input type="radio" name="type" value="" id="type-all" @checked(request('type')=='')><label for="type-all">All Types</label></div>
                    <div class="k-filter-option"><input type="radio" name="type" value="product" id="type-product" @checked(request('type')=='product')><label for="type-product">Product</label></div>
                    <div class="k-filter-option"><input type="radio" name="type" value="classified" id="type-classified" @checked(request('type')=='classified')><label for="type-classified">Classified</label></div>
                </div>

                <div class="k-filter-section">
                    <h4>Category <span>−</span></h4>
                    @foreach($categories as $category)
                        <div class="k-filter-option">
                            <input type="checkbox" name="categories[]" value="{{ $category->slug }}" id="cat-{{ $category->slug }}" @checked(in_array($category->slug, request('categories', [])))>
                            <label for="cat-{{ $category->slug }}">{{ $category->icon ?? '🛒' }} {{ $category->name }} <span class="cnt">{{ $category->listings_count ?? 0 }}</span></label>
                        </div>
                    @endforeach
                </div>

                <div class="k-filter-section">
                    <h4>Location <span>−</span></h4>
                    @foreach(($locations ?? []) as $location)
                        <div class="k-filter-option">
                            <input type="checkbox" name="locations[]" value="{{ $location->id }}" id="loc-{{ $location->id }}" @checked(in_array($location->id, request('locations', [])))>
                            <label for="loc-{{ $location->id }}">📍 {{ $location->name }} <span class="cnt">{{ $location->listings_count ?? 0 }}</span></label>
                        </div>
                    @endforeach
                </div>

                <div class="k-filter-section">
                    <h4>Price Range <span>−</span></h4>
                    <div class="k-price-range">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min Price">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max Price">
                    </div>
                </div>

                <button type="submit" class="k-btn k-btn-primary w-full" style="justify-content:center;margin-top:4px;border:none;cursor:pointer">▽ Apply Filter</button>
            </form>
        </div>

        <!-- Listings Main -->
        <div>
            <div class="k-listings-toolbar">
                <div class="k-listings-count">Showing <strong>{{ $listings->firstItem() ?? 0 }}–{{ $listings->lastItem() ?? 0 }}</strong> of <strong>{{ $listings->total() }} results</strong></div>
                <form method="GET" style="display:flex;align-items:center;gap:10px">
                    @foreach(request()->except('sort','page') as $k=>$v)
                        @if(is_array($v)) @foreach($v as $vv)<input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">@endforeach @else <input type="hidden" name="{{ $k }}" value="{{ $v }}"> @endif
                    @endforeach
                    <span style="font-size:13px;color:var(--k-text-secondary)">Sort By:</span>
                    <select name="sort" class="k-sort-select" onchange="this.form.submit()">
                        <option value="">Newest First</option>
                        <option value="popular" @selected(request('sort')=='popular')>Most Popular</option>
                        <option value="price_low" @selected(request('sort')=='price_low')>Price: Low to High</option>
                        <option value="price_high" @selected(request('sort')=='price_high')>Price: High to Low</option>
                    </select>
                </form>
            </div>

            @if($listings->count())
                <div class="k-grid-4" style="gap:14px">
                    @foreach($listings as $listing)
                        @include('frontend.listings.card',['listing'=>$listing])
                    @endforeach
                </div>
                {{ $listings->links('vendor.pagination.k-theme') }}
            @else
                <div style="padding:60px 20px;text-align:center;background:var(--k-surface);border:1px dashed var(--k-border);border-radius:var(--k-radius-lg)">
                    <h3 style="font-family:var(--font-display);margin-bottom:8px">No listings found</h3>
                    <p style="color:var(--k-text-secondary)">Try changing filters or search keyword.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<section class="k-main" style="padding-top:0">
    <div class="k-section">
        <div class="k-section-header">
            <h2 class="k-section-title">Popular Categories</h2>
            <a href="/listings" class="k-section-link">View All Categories →</a>
        </div>
        <div class="k-cats">
            @foreach($categories->take(8) as $category)
                <a href="/listings?categories[]={{ $category->slug }}" class="k-cat-pill">
                    <span class="icon">{{ $category->icon ?? '🛒' }}</span>
                    <span class="label">{{ $category->name }}</span>
                    <span class="count">{{ $category->listings_count ?? 0 }} ads</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
