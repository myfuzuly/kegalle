@extends('layouts.app')

@section('title','All Stores — Verified Local Businesses in Kegalle · Kegalle Marketplace')
@section('meta_description','Discover trusted, verified stores and businesses across Kegalle, Mawanella, Ruwanwella and beyond. Browse local shops and contact sellers directly.')
@section('canonical', url('/stores'))

@section('content')
<div class="k-stores-header">
    <div class="k-stores-header-inner">
        <div>
            <h1>All Stores</h1>
            <p>Discover trusted stores in Kegalle. Buy with confidence from verified sellers.</p>
        </div>
        <a href="/register?account_type=store" class="k-btn k-btn-white k-btn-lg">+ Add Your Store</a>
    </div>
</div>

<div class="k-search-bar-wrap">
    <div class="container k-search-bar-inner">
        <form method="GET" class="k-search-bar-form">
            <div class="k-search-input-wrap">
                <span class="k-search-icon">🔍</span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search stores..." class="k-form-control k-form-control-icon">
            </div>
            <select name="location" class="k-sort-select k-sort-select-wide">
                <option value="">All Locations</option>
                @foreach(($locations ?? []) as $location)
                    <option value="{{ $location->name }}" @selected(request('location')==$location->name)>{{ $location->name }}</option>
                @endforeach
            </select>
            <span class="k-sort-label">Sort By:</span>
            <select name="sort" class="k-sort-select">
                <option value="">Popular</option>
                <option value="newest" @selected(request('sort')==='newest')>Newest</option>
                <option value="products" @selected(request('sort')==='products')>Most Products</option>
            </select>
            <button type="submit" class="k-btn k-btn-primary">Search</button>
        </form>
    </div>
</div>

<div class="container k-content-section">
    <div class="k-store-list-page-grid">
        <!-- Sidebar filters -->
        <div class="k-filter-sidebar">
            <div class="k-filter-header"><span class="k-filter-title">Filter Stores</span><a href="/stores" class="k-filter-clear">Clear All</a></div>
            <div class="k-filter-section">
                <h4>Categories</h4>
                <div class="k-filter-option"><a href="/stores" class="k-filter-cat-link {{ !request('category') ? 'k-filter-label-active' : '' }}"><span>All Categories</span></a></div>
                @foreach(($categories ?? collect()) as $category)
                    @php
                        $hasChildren = $category->children->isNotEmpty();
                        $isActive = request('category') === $category->slug || $category->children->pluck('slug')->contains(request('category'));
                        $totalCount = $category->listings_count + $category->children->sum('listings_count');
                    @endphp
                    <div class="k-cat-tree-item">
                        <div class="k-filter-option k-cat-parent">
                            @if($hasChildren)
                                <button type="button" class="k-cat-toggle {{ $isActive ? 'open' : '' }}" onclick="this.classList.toggle('open');this.closest('.k-cat-tree-item').querySelector('.k-cat-children').classList.toggle('k-cat-children-open')">▸</button>
                            @endif
                            <a href="/stores?category={{ $category->slug }}" class="k-filter-cat-link {{ request('category') === $category->slug ? 'k-filter-label-active' : '' }}">
                                <span>{{ $category->icon ?? '🛒' }} {{ $category->name }}</span><span class="cnt">{{ $totalCount }}</span>
                            </a>
                        </div>
                        @if($hasChildren)
                            <div class="k-cat-children {{ $isActive ? 'k-cat-children-open' : '' }}">
                                @foreach($category->children as $child)
                                    <div class="k-filter-option k-cat-child">
                                        <a href="/stores?category={{ $child->slug }}" class="k-filter-cat-link {{ request('category') === $child->slug ? 'k-filter-label-active' : '' }}">
                                            <span>{{ $child->name }}</span><span class="cnt">{{ $child->listings_count ?? 0 }}</span>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Store list -->
        <div>
            <div class="k-listings-count">Showing <strong>{{ $stores->firstItem() ?? 0 }}–{{ $stores->lastItem() ?? 0 }}</strong> of <strong>{{ $stores->total() }} stores</strong></div>
            <div id="k-store-skeleton" class="k-sidebar-skeleton k-store-skeleton">
                @for($i = 0; $i < 4; $i++)
                <div class="k-skeleton-card k-skeleton-card-row">
                    <div class="k-skeleton-thumb k-skeleton-thumb-lg"></div>
                    <div class="k-skeleton-body-col"><div class="k-skeleton-line k-skeleton-line-long"></div><div class="k-skeleton-line k-skeleton-line-short"></div><div class="k-skeleton-line k-skeleton-line-75"></div></div>
                </div>
                @endfor
            </div>
            <div class="k-store-list-col" id="k-store-list">
                @forelse($stores as $store)
                    @php $logo = !empty($store->logo) ? asset('storage/'.ltrim($store->logo,'/')) : null; @endphp
                    <a href="/store/{{ $store->slug }}" class="k-store-list-card">
                        <div class="k-store-list-logo">
                            @if($logo)
                                <img src="{{ $logo }}" alt="{{ $store->name }}" class="k-cover-img k-radius-inherit">
                            @else
                                {{ strtoupper(substr($store->name,0,2)) }}
                            @endif
                        </div>
                        <div>
                            <div class="k-store-list-name">{{ $store->name }} <span class="k-verified-badge k-verified-badge-sm">✓ Verified Store</span></div>
                            <div class="k-store-list-cat" style="display:flex;align-items:center;gap:8px">
                                <span style="background:{{ $store->rank_color }}1a;color:{{ $store->rank_color }};padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700">{{ $store->rank_label }}</span>
                                @if(($store->approved_reviews_count ?? 0) > 0)
                                    <span style="color:#f59e0b;font-size:12px;font-weight:600">★ {{ number_format($store->approved_reviews_avg_rating ?? 0, 1) }} <span style="color:#98a2b3;font-weight:400">({{ $store->approved_reviews_count }})</span></span>
                                @endif
                            </div>
                            <div class="k-store-list-desc mt-8">{{ \Illuminate\Support\Str::limit($store->description ?? 'Trusted local seller in Kegalle.', 90) }}</div>
                            <div class="k-store-list-meta">
                                <span class="k-text-xs k-text-tertiary">📍 {{ $store->city ?? $store->address ?? 'Kegalle' }}</span>
                                <span class="k-text-xs k-text-muted">🗓 Joined {{ $store->created_at?->format('M Y') ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="k-store-list-stats"><strong>{{ $store->listings_count ?? 0 }}</strong><span>{{ \Illuminate\Support\Str::plural('Product', $store->listings_count ?? 0) }}</span></div>
                        <span class="k-btn k-btn-outline">View Store</span>
                    </a>
                @empty
                    <div class="k-empty-state-box k-text-tertiary">No stores found. Try another search.</div>
                @endforelse
            </div>
            {{ $stores->links('vendor.pagination.k-theme') }}

            <!-- Stats footer bar -->
            @php
                $statStores = \App\Models\Store::approved()->count();
                $statProducts = \App\Models\Listing::where('status', 'approved')->count();
                $statReviews = \App\Models\Review::where('status', 'approved')->count();
                $statLocations = \App\Models\Location::where('is_active', 1)->count();
            @endphp
            <div class="k-stats-bar mb-0">
                <div class="k-stat-item"><div class="k-stat-number">{{ $statStores }}</div><div class="k-stat-label">Verified Stores</div></div>
                <div class="k-stat-item"><div class="k-stat-number">{{ $statProducts }}</div><div class="k-stat-label">Active Listings</div></div>
                <div class="k-stat-item"><div class="k-stat-number">{{ $statReviews }}</div><div class="k-stat-label">Customer Reviews</div></div>
                <div class="k-stat-item"><div class="k-stat-number">{{ $statLocations }}</div><div class="k-stat-label">Towns Covered</div></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    var sk = document.getElementById('k-store-skeleton');
    if(sk) sk.style.display='none';
});
</script>
@endpush
