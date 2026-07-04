@php
    $banner = \App\Models\AdBanner::activeForLocation($location)->first();
@endphp
@if($banner)
    <a href="{{ route('ads.click', $banner) }}" target="_blank" rel="sponsored noopener" class="k-ad-dynamic k-ad-dynamic-{{ $style ?? 'box' }}" title="{{ $banner->title }}">
        @if($banner->image)
            <img src="{{ asset('storage/'.$banner->image) }}" alt="{{ $banner->title }}" loading="lazy">
        @else
            <span class="k-ad-dynamic-title">{{ $banner->title }}</span>
        @endif
    </a>
@else
    @if(($style ?? 'box') === 'top')
        <div class="k-ad-banner mt-20">
            <div class="k-ad-text">
                <strong>Grow your business with premium advertising</strong>
                <span>Reach thousands of local buyers in Kegalle</span>
            </div>
            <a href="/dashboard/membership" class="k-btn k-btn-primary k-btn-sm k-flex-shrink-0">Advertise Now</a>
        </div>
    @else
        <div class="k-ad-placeholder">
            <div class="k-ad-placeholder-title">Premium Ad Space</div>
            <div class="k-ad-placeholder-sub">Your ad could be here</div>
            <a href="/dashboard/membership" class="k-btn k-btn-sm k-btn-gold">Advertise Here</a>
        </div>
    @endif
@endif
