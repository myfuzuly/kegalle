@php
    $firstImage = optional($listing->images->first())->path ?? $listing->image ?? null;
    $hasImage = (bool) $firstImage;
    $imgUrl = $hasImage ? asset('storage/'.ltrim($firstImage,'/')) : null;
    $location = optional($listing->locationModel)->name ?? $listing->location ?? 'Kegalle';
    $price = ($listing->price ?? 0) > 0 ? 'LKR '.number_format($listing->price) : 'Contact Seller';
    $placeholderIcon = optional($listing->category)->icon ?: '🛍️';
    $palette = [
        'linear-gradient(135deg,#E8F5E9 0%,#C8E6C9 100%)',
        'linear-gradient(135deg,#E3F2FD 0%,#BBDEFB 100%)',
        'linear-gradient(135deg,#FCE4EC 0%,#F8BBD0 100%)',
        'linear-gradient(135deg,#FFF8E1 0%,#FFECB3 100%)',
        'linear-gradient(135deg,#F3E8FD 0%,#E1BEE7 100%)',
        'linear-gradient(135deg,#E0F2F1 0%,#B2DFDB 100%)',
    ];
    $phBg = $palette[($listing->id ?? 0) % count($palette)];
    $tagType = strtolower($listing->ad_type ?? $listing->type ?? 'sale');
    $tagClass = match(true) {
        str_contains($tagType, 'rent') => 'k-tag-rent',
        str_contains($tagType, 'want') => 'k-tag-wanted',
        default => 'k-tag-new',
    };
    $isFavorited = auth()->check() && \App\Models\Favorite::where('user_id', auth()->id())->where('listing_id', $listing->id)->exists();
@endphp
<a class="k-listing-card" href="/listings/{{ $listing->slug ?? '#' }}">
    <div class="k-listing-img">
        @if($hasImage)
            <img loading="lazy" decoding="async" src="{{ $imgUrl }}" alt="{{ $listing->title ?? 'Listing' }}" style="width:100%;height:100%;object-fit:cover">
        @else
            <div class="k-listing-img-placeholder" style="background:{{ $phBg }};filter:drop-shadow(0 4px 8px rgba(0,0,0,.08))">{{ $placeholderIcon }}</div>
        @endif
        <div class="k-listing-badge">
            @if(!empty($listing->is_featured))<span class="k-tag k-tag-featured">FEATURED</span>@endif
            <span class="k-tag {{ $tagClass }}">{{ ucfirst($listing->ad_type ?? $listing->type ?? 'Sale') }}</span>
        </div>
        <button class="k-listing-heart js-favorite-btn {{ $isFavorited ? 'is-favorited' : '' }}" type="button" data-listing-id="{{ $listing->id }}" data-authed="{{ auth()->check() ? '1' : '0' }}" aria-label="{{ $isFavorited ? 'Remove from favorites' : 'Save listing' }}">{{ $isFavorited ? '❤️' : '🤍' }}</button>
    </div>
    <div class="k-listing-body">
        <div class="k-listing-title">{{ $listing->title ?? 'Untitled Listing' }}</div>
        <div class="k-listing-meta">📍 {{ $location }} · 🕐 {{ $listing->created_at?->diffForHumans() ?? 'Recently' }}</div>
        <div class="k-listing-footer">
            <span class="k-price k-price-sm">{{ $price }}</span>
        </div>
    </div>
</a>
