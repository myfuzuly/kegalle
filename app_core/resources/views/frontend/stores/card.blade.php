<a class="kg-store-card" href="/store/{{ $store->slug }}">
    <div class="kg-store-cover">
        @if(!empty($store->cover_image))<img loading="lazy" src="/storage/{{ $store->cover_image }}" alt="{{ $store->name }} cover">@endif
    </div>
    <div class="kg-store-logo">
        @if($store->logo)<img loading="lazy" src="/storage/{{ $store->logo }}" alt="{{ $store->name }}">@else{{ strtoupper(substr($store->name,0,1)) }}@endif
    </div>
    <h3>{{ $store->name }}</h3>
    <p>{{ $store->city ?? $store->address ?? 'Kegalle' }}</p>
    @php $storeAvg = $store->approved_reviews_count > 0 ? round($store->approved_reviews_avg_rating ?? 0, 1) : 0; @endphp
    @if($storeAvg > 0)
    <div style="font-size:13px;margin:-4px 0 4px;color:#f59e0b;font-weight:600">★ {{ $storeAvg }} <span style="color:#94a3b8;font-weight:400">({{ $store->approved_reviews_count }})</span></div>
    @endif
    <div class="kg-trust-badges">
        <span style="background:{{ $store->rank_color }};color:#fff;border-radius:4px;padding:1px 6px">{{ $store->rank_label }}</span>
        <span>{{ $store->listings_count ?? 0 }} Products</span>
    </div>
    <b>Visit Store</b>
</a>
