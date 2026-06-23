<a class="kg-listing-card" href="/listings/{{ $listing->slug }}">
    <div class="kg-listing-image">
        @if(isset($listing->images) && $listing->images->count())
            <img src="/storage/{{ $listing->images->first()->path }}" alt="{{ $listing->title }}">
        @else
            <div class="kg-image-placeholder">{{ strtoupper(substr($listing->title,0,1)) }}</div>
        @endif

        <div class="kg-card-badges">
            <span class="kg-badge">{{ ucfirst($listing->type ?? 'Sell') }}</span>
            @if($listing->is_featured ?? false)
                <span class="kg-badge kg-badge-yellow">Featured</span>
            @endif
        </div>
    </div>

    <div class="kg-listing-body">
        <small>{{ $listing->category->name ?? 'General' }}</small>
        <h3>{{ $listing->title }}</h3>

        <strong class="kg-price">LKR {{ number_format($listing->price ?? 0) }}</strong>

        <p>📍 {{ $listing->location ?? 'Kegalle' }} · {{ $listing->created_at?->diffForHumans() }}</p>
        <p>🏬 {{ $listing->store->name ?? $listing->user->name ?? 'Seller' }}</p>
    </div>
</a>
