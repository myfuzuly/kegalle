@php
    $firstImage = optional($listing->images->first())->path ?? $listing->image ?? null;
    $hasImage = (bool) $firstImage;
    $imgUrl = $hasImage ? asset('storage/'.ltrim($firstImage,'/')) : null;
    $webpUrl = $hasImage ? asset('storage/'.ltrim(\App\Helpers\ImageHelper::webpPath($firstImage),'/')) : null;
    $location = optional($listing->locationModel)->name ?? $listing->location ?? 'Kegalle';
    $price = ($listing->price ?? 0) > 0 ? 'LKR '.number_format($listing->price) : 'Contact Seller';
    $placeholderImg = '/images/kegalle-placeholder.png?v=2';
    $tagType = strtolower($listing->ad_type ?? $listing->type ?? 'sale');
    $tagClass = match(true) {
        str_contains($tagType, 'rent') => 'k-tag-rent',
        str_contains($tagType, 'want') => 'k-tag-wanted',
        default => 'k-tag-new',
    };
    $tagLabel = match(true) {
        str_contains($tagType, 'rent') => 'For Rent',
        str_contains($tagType, 'want') => 'Wanted',
        str_contains($tagType, 'sale') => 'For Sale',
        default => ucfirst($listing->ad_type ?? $listing->type ?? 'Sale'),
    };
@endphp
<a class="k-listing-card" href="/listings/{{ $listing->slug ?? '#' }}">
    <div class="k-listing-img">
        @if($hasImage)
            <img loading="lazy" decoding="async" src="{{ $imgUrl }}" alt="{{ $listing->title ?? 'Listing' }}" class="k-cover-img" onerror="this.closest('.k-listing-img').classList.add('k-img-failed');this.remove()">
        @else
            @php
                $catIcon = match(strtolower($listing->category?->name ?? '')) {
                    'vehicles', 'cars', 'motors' => '🚗',
                    'electronics', 'phones', 'mobile' => '📱',
                    'property', 'real estate', 'houses' => '🏠',
                    'fashion', 'clothing', 'clothes' => '👗',
                    'furniture', 'home & garden' => '🪑',
                    'jobs', 'services' => '💼',
                    'sports', 'fitness' => '⚽',
                    'pets', 'animals' => '🐾',
                    'books', 'education' => '📚',
                    'food', 'grocery' => '🍎',
                    default => '📦',
                };
            @endphp
            <div class="k-placeholder-icon"><span>{{ $catIcon }}</span><span class="k-ph-label">No Photo</span></div>
        @endif
        <div class="k-listing-badge">
            @if(!empty($listing->is_featured))<span class="k-tag k-tag-featured">FEATURED</span>@endif
            <span class="k-tag {{ $tagClass }}">{{ $tagLabel }}</span>
        </div>
    </div>
    <div class="k-listing-body">
        <div class="k-listing-title">{{ $listing->title ?? 'Untitled Listing' }}</div>
        <div class="k-listing-meta">
            <span class="k-listing-loc">📍 {{ $location }}</span>
            <span class="k-listing-time">🕐 {{ $listing->created_at?->diffForHumans() ?? 'Recently' }}</span>
        </div>
        <div class="k-listing-seller">
            @if(strtolower($listing->type ?? '') === 'classified')
                <span class="k-seller-tag k-seller-tag-classified">Classified</span>
            @elseif($listing->store)
                <span class="k-seller-tag k-seller-tag-store">🏪 {{ $listing->store->name }}</span>
            @else
                <span class="k-seller-tag">👤 {{ optional($listing->user)->name ?? 'Seller' }}</span>
            @endif
        </div>
        <div class="k-listing-footer">
            <span class="k-price k-price-sm">{{ $price }}</span>
        </div>
    </div>
</a>
