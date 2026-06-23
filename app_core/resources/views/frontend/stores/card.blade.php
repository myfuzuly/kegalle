<a class="kg-store-card" href="/store/{{ $store->slug }}">
    <div class="kg-store-cover">
        @if(!empty($store->cover_image))<img loading="lazy" src="/storage/{{ $store->cover_image }}" alt="{{ $store->name }} cover">@endif
    </div>
    <div class="kg-store-logo">
        @if($store->logo)<img loading="lazy" src="/storage/{{ $store->logo }}" alt="{{ $store->name }}">@else{{ strtoupper(substr($store->name,0,1)) }}@endif
    </div>
    <h3>{{ $store->name }}</h3>
    <p>{{ $store->city ?? $store->address ?? 'Kegalle' }}</p>
    <div class="kg-trust-badges"><span>✓ Verified</span><span>{{ $store->listings_count ?? 0 }} Products</span><span>Fast Response</span></div>
    <b>Visit Store</b>
</a>
