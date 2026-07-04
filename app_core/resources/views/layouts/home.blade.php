@extends('layouts.app')

@section('title','Kegalle - Buy, Sell & Discover')

@section('content')

<section class="km-hero">
    <div class="km-container">
        <p class="km-eyebrow">Kegalle's local marketplace</p>
        <h1>Find products, stores and deals <span>near you.</span></h1>
        <p class="km-hero-text">Search trusted local stores, business suppliers, everyday products and classified ads from verified sellers in Kegalle.</p>

        <form class="km-searchbar" action="/listings" method="GET">
            <select name="category" class="km-field">
                <option value="">All Categories</option>
                @foreach($categories ?? [] as $category)
                    <option value="{{ $category->slug }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <input class="km-field" name="q" placeholder="Search products, stores or ads">

            <input class="km-field" name="location" placeholder="Location">

            <button class="km-search-btn">Search</button>
        </form>

        <div class="km-popular">
            <b>Popular:</b>
            <a href="#">Mobiles</a>
            <a href="#">Vehicles</a>
            <a href="#">Property</a>
            <a href="#">Jobs</a>
            <a href="#">Stores</a>
        </div>
    </div>
</section>

<section class="km-container km-section">
    @if(($categories ?? collect())->count())
        <div class="km-category-grid">
            @foreach($categories as $category)
                <a href="/listings?category={{ $category->slug }}" class="km-category-card">
                    <div class="km-category-icon">▦</div>
                    <strong>{{ $category->name }}</strong>
                    <span>{{ $category->listings_count ?? 0 }} Ads</span>
                </a>
            @endforeach
        </div>
    @else
        <div class="km-empty">No categories yet. Add categories from admin panel.</div>
    @endif
</section>

<section class="km-container km-section">
    <div class="km-section-head">
        <div>
            <p>Fresh marketplace picks</p>
            <h2>Featured Products & Ads</h2>
        </div>
        <a href="/listings">View All →</a>
    </div>

    @if(($featuredListings ?? collect())->count())
        <div class="km-listing-grid">
            @foreach($featuredListings as $listing)
                <a class="km-listing-card" href="/listings/{{ $listing->slug }}">
                    <div class="km-listing-image">
                        <span class="km-badge">{{ ucfirst($listing->type ?? 'Listing') }}</span>
                        <span class="km-badge km-badge-yellow">Featured</span>
                        <div class="km-image-placeholder">{{ strtoupper(substr($listing->title,0,1)) }}</div>
                    </div>

                    <div class="km-listing-body">
                        <small>{{ $listing->category->name ?? 'General' }}</small>
                        <h3>{{ $listing->title }}</h3>
                        <strong class="km-price">LKR {{ number_format($listing->price ?? 0) }}</strong>
                        <p>📍 {{ $listing->location ?? 'Kegalle' }} · {{ $listing->created_at?->diffForHumans() }}</p>
                        <p>🏬 {{ $listing->store->name ?? $listing->user->name ?? 'Seller' }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="km-empty">No approved products or ads found.</div>
    @endif
</section>

<section class="km-container km-section">
    <div class="km-section-head">
        <div>
            <p>Trusted vendors</p>
            <h2>Featured Stores</h2>
        </div>
        <a href="/stores">View Stores →</a>
    </div>

    @if(($stores ?? collect())->count())
        <div class="km-store-grid">
            @foreach($stores as $store)
                <a class="km-store-card" href="/store/{{ $store->slug }}">
                    <div class="km-store-logo">{{ strtoupper(substr($store->name,0,1)) }}</div>
                    <h3>{{ $store->name }}</h3>
                    <p>{{ $store->address ?? 'Kegalle' }}</p>
                    <span>Verified Store@if(($store->products_count ?? 0) > 0) · {{ $store->products_count }} {{ $store->products_count == 1 ? 'Product' : 'Products' }}@endif</span>
                    <b>Visit Store</b>
                </a>
            @endforeach
        </div>
    @else
        <div class="km-empty">No approved stores found.</div>
    @endif
</section>

<section class="km-container km-how">
    <h2>How Kegalle Works</h2>
    <div class="km-how-grid">
        <div><b>1</b><h3>Search</h3><p>Find local products, stores and ads.</p></div>
        <div><b>2</b><h3>Compare</h3><p>Check seller details, pricing and location.</p></div>
        <div><b>3</b><h3>Contact</h3><p>Call, WhatsApp or message the seller.</p></div>
        <div><b>4</b><h3>Deal</h3><p>Buy and sell safely in your area.</p></div>
    </div>
</section>

<section class="km-container km-cta">
    <span>For sellers and stores</span>
    <h2>Turn your store into a simple online catalogue.</h2>
    <p>Show your store profile, products, contact details and location with one public URL.</p>
    <a href="/register">Create Store / Post Ad</a>
</section>

@endsection