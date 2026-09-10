@php
    $firstImage = optional($listing->images->first())->path ?? $listing->image ?? null;
    $hasImage = (bool) $firstImage;
    $imgUrl = $hasImage ? asset('storage/'.ltrim($firstImage,'/')) : null;
    $webpUrl = $hasImage ? asset('storage/'.ltrim(\App\Helpers\ImageHelper::webpPath($firstImage),'/')) : null;
    $location = optional($listing->locationModel)->name ?? $listing->location ?? 'Kegalle';
    $priceIsSet = ($listing->price ?? 0) > 0;
    $price = $priceIsSet ? 'LKR '.number_format($listing->price) : 'Price on Request';
    $tagType = strtolower($listing->ad_type ?? $listing->type ?? 'sale');
    $tagSlug = match(true) {
        str_contains($tagType, 'rent') => 'rent',
        str_contains($tagType, 'want') => 'wanted',
        default => 'sale',
    };
    $tagLabel = match(true) {
        str_contains($tagType, 'rent') => 'For Rent',
        str_contains($tagType, 'want') => 'Wanted',
        str_contains($tagType, 'sale') => 'For Sale',
        default => ucfirst($listing->ad_type ?? $listing->type ?? 'Sale'),
    };
    $cardWaNumber = preg_replace('/[^0-9]/', '', optional($listing->store)->whatsapp ?? optional($listing->store)->phone ?? $listing->poster_whatsapp ?? $listing->poster_phone ?? optional($listing->user)->phone ?? '');
    $cardViews = $listing->views ?? 0;
    $cardListingUrl = url('/listings/'.($listing->slug ?? $listing->id));
    $cardWaText = urlencode('Hi, I\'m interested in: '.($listing->title ?? 'this listing').' — '.$cardListingUrl);
    $catIcon = match(strtolower($listing->category?->name ?? '')) {
        'vehicles', 'cars', 'motors' => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 17H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1l3-5h8l3 5h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>',
        'electronics', 'phones', 'mobile' => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>',
        'property', 'real estate', 'houses' => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
        'fashion', 'clothing', 'clothes' => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46L16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.57a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.57a2 2 0 0 0-1.34-2.23z"/></svg>',
        'furniture', 'home & garden' => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="1"/><path d="M3 11V8a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/><path d="M15 11V8a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/><line x1="3" y1="21" x2="3" y2="21"/><line x1="21" y1="21" x2="21" y2="21"/></svg>',
        'jobs', 'services' => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
        'sports', 'fitness' => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M4.93 4.93l4.24 4.24"/><path d="M14.83 9.17l4.24-4.24"/><path d="M14.83 14.83l4.24 4.24"/><path d="M9.17 14.83l-4.24 4.24"/><circle cx="12" cy="12" r="4"/></svg>',
        'pets', 'animals' => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 5.172C10 3.782 8.423 2.678 6.5 3c-2.823.47-4.113 6.006-4 7 .08.703 1.725 1.722 3.656 1 1.261-.472 1.96-1.45 2.344-2.5"/><path d="M14.267 5.172c0-1.39 1.577-2.494 3.5-2.172 2.823.47 4.113 6.006 4 7-.08.703-1.725 1.722-3.656 1-1.261-.472-1.96-1.45-2.344-2.5"/><path d="M8 14v.5"/><path d="M16 14v.5"/><path d="M11.25 16.25h1.5L12 17l-.75-.75z"/><path d="M4.42 11.247A13.152 13.152 0 0 0 4 14.556C4 18.728 7.582 21 12 21s8-2.272 8-6.444c0-1.061-.162-2.2-.493-3.309m-9.243-6.082A8.801 8.801 0 0 1 12 5c.78 0 1.5.108 2.161.306"/></svg>',
        'books', 'education' => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>',
        'food', 'grocery' => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3zm0 0v7"/></svg>',
        default => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>',
    };
    $storeRank = $listing->store->rank ?? 'bronze';
@endphp
@php
$_svgPin  = '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>';
$_svgEye  = '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
$_svgShop = '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l1-6h16l1 6"/><path d="M3 9a2 2 0 0 0 2 2 2 2 0 0 0 2-2 2 2 0 0 0 2 2 2 2 0 0 0 2-2 2 2 0 0 0 2 2 2 2 0 0 0 2-2"/><path d="M5 22V11M19 22V11M9 22v-6h6v6"/></svg>';
$_svgUser = '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
$_svgImg  = '<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>';
$_rankDot = match($storeRank){ 'platinum'=>'<span class="kpc-rank-dot kpc-rank-plat"></span>','gold'=>'<span class="kpc-rank-dot kpc-rank-gold"></span>','silver'=>'<span class="kpc-rank-dot kpc-rank-silv"></span>',default=>'' };
@endphp
<a class="kpc k-listing-card{{ !empty($listing->is_featured) ? ' kpc-featured' : '' }}{{ ($listing->status ?? '') === 'sold' ? ' kpc-sold' : '' }}" href="/listings/{{ $listing->slug ?? '#' }}" aria-label="{{ $listing->title ?? 'Listing' }} — {{ $price }}" tabindex="0" data-location="{{ $location }}" data-price="{{ $listing->price ?? 0 }}">
    <div class="kpc-img{{ $hasImage ? '' : ' kpc-no-img' }}">
        @if($hasImage)
            <picture>
                <source type="image/webp" srcset="{{ $webpUrl }}">
                <img loading="{{ ($cardIndex ?? 99) < 4 ? 'eager' : 'lazy' }}" fetchpriority="{{ ($cardIndex ?? 99) === 0 ? 'high' : 'auto' }}" decoding="{{ ($cardIndex ?? 99) < 2 ? 'sync' : 'async' }}" src="{{ $imgUrl }}" alt="{{ $listing->title ?? 'Listing' }}" class="kpc-photo" onerror="var w=this.closest('.kpc-img');w.classList.add('kpc-no-photo');var p=w.querySelector('.kpc-ph-fb');if(p)p.style.display='flex';this.remove()">
            </picture>
            <div class="kpc-ph-fb kpc-placeholder" style="display:none">{!! $_svgImg !!}</div>
        @else
            <div class="kpc-placeholder">{!! $_svgImg !!}</div>
        @endif
        <div class="kpc-overlay"></div>
        <div class="kpc-badges">
            @if(!empty($listing->is_featured))<span class="kpc-badge kpc-feat">Featured</span>@endif
            <span class="kpc-badge kpc-{{ $tagSlug }}">{{ $tagLabel }}</span>
        </div>
        <button class="kpc-heart" type="button" aria-label="Save to wishlist" data-id="{{ $listing->id }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        </button>
        @if($cardViews > 0)<span class="kpc-views">{!! $_svgEye !!} {{ $cardViews }}</span>@endif
        @php $imageCount = $listing->images->count(); @endphp
        @if($imageCount > 1)<span class="kpc-img-count">📷 {{ $imageCount }}</span>@endif
    </div>
    <div class="kpc-body">
        <div class="kpc-title k-listing-title">{{ $listing->title ?? 'Untitled Listing' }}</div>
        <div class="kpc-meta">
            <span class="kpc-meta-pin">{!! $_svgPin !!} {{ $location }}</span>
            <span class="kpc-dot">·</span>
            <span>{{ $listing->created_at?->diffForHumans() ?? 'Recently' }}</span>
        </div>
        <div class="kpc-seller">
            @if(strtolower($listing->type ?? '') === 'classified')
                <span class="kpc-tag-classified">Classified</span>
            @elseif($listing->store)
                <span class="kpc-store-name">{!! $_svgShop !!} {{ $listing->store->name }}</span>
                @if($listing->store->is_kurilla_verified || $listing->store->is_verified)
                    <span class="kpc-verified">✓ Verified</span>
                @endif
                {!! $_rankDot !!}
            @else
                <span class="kpc-user">{!! $_svgUser !!} {{ optional($listing->user)->name ?? 'Seller' }}</span>
                @if(optional($listing->user)->is_verified)
                    <span class="kpc-verified">✓</span>
                @endif
            @endif
        </div>
        <div class="kpc-footer">
            <span class="kpc-price{{ $priceIsSet ? '' : ' kpc-por' }}">{{ $price }}</span>
            @if($cardWaNumber)
                <span class="kpc-wa" role="button" tabindex="0" aria-label="Chat on WhatsApp"
                    data-wa="/c/wa/{{ $listing->id }}">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                </span>
            @else
                <span class="kpc-view" role="button" tabindex="0" data-href="/listings/{{ $listing->slug ?? '#' }}">View &rarr;</span>
            @endif
        </div>
    </div>
</a>
@once
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    document.addEventListener('click', function(e){
        var heart = e.target.closest('.kpc-heart[data-id]');
        if(heart){ e.preventDefault(); e.stopPropagation(); kpcHeart(heart); return; }
        var wa = e.target.closest('[data-wa]');
        if(wa){ e.preventDefault(); e.stopPropagation(); window.open(wa.dataset.wa,'_blank','noopener'); return; }
        var vw = e.target.closest('[data-href]');
        if(vw){ e.preventDefault(); e.stopPropagation(); window.location = vw.dataset.href; }
    });
    document.addEventListener('keydown', function(e){
        if(e.key!=='Enter'&&e.key!==' ') return;
        var wa = e.target.closest('[data-wa]');
        if(wa){ e.preventDefault(); e.stopPropagation(); window.open(wa.dataset.wa,'_blank','noopener'); return; }
        var vw = e.target.closest('[data-href]');
        if(vw){ e.preventDefault(); e.stopPropagation(); window.location = vw.dataset.href; }
    });
    // Mark already-saved hearts on load
    var saved = JSON.parse(localStorage.getItem('k_saved')||'[]');
    document.querySelectorAll('.kpc-heart[data-id]').forEach(function(btn){
        if(saved.indexOf(btn.dataset.id)>-1) btn.classList.add('kpc-heart-active');
    });
})();
window.kpcHeart = function(btn){
    var id = btn.dataset.id;
    var saved = JSON.parse(localStorage.getItem('k_saved')||'[]');
    var idx = saved.indexOf(id);
    if(idx>-1){ saved.splice(idx,1); btn.classList.remove('kpc-heart-active'); }
    else { saved.unshift(id); btn.classList.add('kpc-heart-active'); }
    localStorage.setItem('k_saved', JSON.stringify(saved));
    // Update nav badges
    var b1=document.getElementById('kNavSavedBadge'), b2=document.getElementById('kDrawerSavedBadge');
    if(b1){ b1.textContent=saved.length; b1.style.display=saved.length?'':'none'; }
    if(b2){ b2.textContent=saved.length; b2.style.display=saved.length?'inline':'none'; }
};
</script>
@endonce
