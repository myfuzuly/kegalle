<a class="kg-store-card" href="/store/{{ $store->slug }}">
    <div class="kg-store-cover">
        @if(!empty($store->banner))
            <img loading="lazy" src="{{ asset('storage/'.ltrim($store->banner,'/')) }}" alt="{{ $store->name }} cover">
        @elseif(!empty($store->cover_image))
            {{-- legacy: cover_image was renamed to banner --}}
            <img loading="lazy" src="{{ asset('storage/'.ltrim($store->cover_image,'/')) }}" alt="{{ $store->name }} cover">
        @endif
    </div>
    <div class="kg-store-logo">
        <span class="kg-store-logo-init">{{ strtoupper(substr($store->name,0,2)) }}</span>
        @if($store->logo)
            <img loading="lazy" src="{{ asset('storage/'.ltrim($store->logo,'/')) }}" alt="{{ $store->name }}" class="kg-store-logo-img">
        @endif
    </div>
    <div class="kg-store-body">
        <h3 class="kg-store-name">{{ $store->name }}</h3>
        <p class="kg-store-city">{{ $store->city ?? $store->address ?? 'Kegalle' }}</p>
        @php $storeAvg = $store->approved_reviews_count > 0 ? round($store->approved_reviews_avg_rating ?? 0, 1) : 0; @endphp
        @if($storeAvg > 0)
        <div class="kg-store-rating">
            <span class="kg-store-stars">★ {{ $storeAvg }}</span>
            <span class="kg-store-review-count">({{ $store->approved_reviews_count }})</span>
        </div>
        @endif
        <div class="kg-trust-badges">
            @if($store->rank_label)
                <span class="kg-rank-badge kg-rank-{{ $store->rank ?? 'bronze' }}">{{ $store->rank_label }}</span>
            @endif
            <span class="kg-product-count">{{ $store->listings_count ?? 0 }} Products</span>
            @if($store->user?->phone_verified_at)
                <span class="kg-verified-badge"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="k-badge-svg"><polyline points="20 6 9 17 4 12"/></svg>Verified</span>
            @endif
        </div>
        <span class="kg-visit-btn">Visit Store ›</span>
    </div>
</a>
