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

<div style="background:var(--k-surface);border-bottom:1px solid var(--k-border)">
    <div class="container" style="padding-top:14px;padding-bottom:14px">
        <form class="" method="GET" style="display:flex;gap:12px;align-items:center">
            <div style="flex:1;position:relative">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:15px;color:var(--k-text-tertiary)">🔍</span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search stores..." class="k-form-control" style="padding-left:36px">
            </div>
            <select name="location" class="k-sort-select" style="min-width:160px">
                <option value="">All Locations</option>
                @foreach(($locations ?? []) as $location)
                    <option value="{{ $location->name }}" @selected(request('location')==$location->name)>{{ $location->name }}</option>
                @endforeach
            </select>
            <span style="font-size:13px;color:var(--k-text-secondary)">Sort By:</span>
            <select name="sort" class="k-sort-select">
                <option value="">Popular</option>
                <option value="newest" @selected(request('sort')==='newest')>Newest</option>
                <option value="products" @selected(request('sort')==='products')>Most Products</option>
            </select>
            <button type="submit" class="k-btn k-btn-primary">Search</button>
        </form>
    </div>
</div>

<div class="container" style="padding-top:28px;padding-bottom:40px">
    <div class="k-store-list-page-grid">
        <!-- Sidebar filters -->
        <div class="k-filter-sidebar">
            <div class="k-filter-header"><span class="k-filter-title">Filter Stores</span><a href="/stores" class="k-filter-clear">Clear All</a></div>
            <div class="k-filter-section">
                <h4>Categories</h4>
                <div class="k-filter-option"><input type="checkbox" checked disabled><label style="color:var(--k-primary);font-weight:600">All Categories</label></div>
                @foreach(($categories ?? collect())->take(8) as $category)
                    <div class="k-filter-option">
                        <a href="/stores?category={{ $category->slug }}" style="display:flex;justify-content:space-between;flex:1;color:var(--k-text-secondary);text-decoration:none">
                            <span>{{ $category->icon ?? '🛒' }} {{ $category->name }}</span><span class="cnt">{{ $category->listings_count ?? 0 }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Store list -->
        <div>
            <div style="font-size:14px;color:var(--k-text-secondary);margin-bottom:16px">Showing <strong style="color:var(--k-text-primary)">{{ $stores->firstItem() ?? 0 }}–{{ $stores->lastItem() ?? 0 }}</strong> of <strong style="color:var(--k-text-primary)">{{ $stores->total() }} stores</strong></div>
            <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:24px">
                @forelse($stores as $store)
                    @php $logo = !empty($store->logo) ? asset('storage/'.ltrim($store->logo,'/')) : null; @endphp
                    <a href="/store/{{ $store->slug }}" class="k-store-list-card">
                        <div class="k-store-list-logo">
                            @if($logo)
                                <img src="{{ $logo }}" alt="{{ $store->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit">
                            @else
                                {{ strtoupper(substr($store->name,0,2)) }}
                            @endif
                        </div>
                        <div>
                            <div class="k-store-list-name">{{ $store->name }} <span class="k-verified-badge" style="display:inline-flex;font-size:12px">✓ Verified Store</span></div>
                            <div class="k-store-list-cat">{{ optional($store->category)->name ?? 'General' }}</div>
                            <div class="k-stars">★★★★★ <span>(4.8 · 128 reviews)</span></div>
                            <div class="k-store-list-desc mt-8">{{ \Illuminate\Support\Str::limit($store->description ?? 'Trusted local seller in Kegalle.', 90) }}</div>
                            <div style="margin-top:8px;display:flex;gap:12px">
                                <div class="k-store-open open"><span class="k-store-open-dot"></span> Open now</div>
                                <span style="font-size:12px;color:var(--k-text-tertiary)">📍 {{ $store->city ?? $store->address ?? 'Kegalle' }}</span>
                            </div>
                        </div>
                        <div class="k-store-list-stats"><strong>{{ $store->listings_count ?? 0 }}+</strong><span>Products</span></div>
                        <span class="k-btn k-btn-outline">View Store</span>
                    </a>
                @empty
                    <div style="padding:40px;text-align:center;color:var(--k-text-tertiary)">No stores found. Try another search.</div>
                @endforelse
            </div>
            {{ $stores->links('vendor.pagination.k-theme') }}

            <!-- Stats footer bar -->
            <div class="k-stats-bar" style="margin:0">
                <div class="k-stat-item"><div class="k-stat-number">300+</div><div class="k-stat-label">Trusted Stores</div></div>
                <div class="k-stat-item"><div class="k-stat-number">10K+</div><div class="k-stat-label">Happy Customers</div></div>
                <div class="k-stat-item"><div class="k-stat-number">50K+</div><div class="k-stat-label">Products Listed</div></div>
                <div class="k-stat-item"><div class="k-stat-number">100%</div><div class="k-stat-label">Secure & Safe</div></div>
            </div>
        </div>
    </div>
</div>
@endsection
