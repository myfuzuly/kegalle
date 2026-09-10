@if($listings->count())
    <div class="k-grid-4 k-grid-tight">
        @foreach($listings as $listing)
            @include('frontend.listings.card',['listing'=>$listing,'cardIndex'=>$loop->index])
        @endforeach
    </div>
    {{ $listings->links('vendor.pagination.k-theme') }}

    @if($listings->total() <= 5 && isset($filterCategoryName) && $filterCategoryName)
    <div class="k-sparse-nudge">
        <div class="k-sparse-nudge-icon">🌱</div>
        <div>
            <strong>Only {{ $listings->total() }} {{ Str::plural('ad', $listings->total()) }} in {{ $filterCategoryName }} right now</strong>
            <p>Be the first to post one — it's free and takes under 2 minutes.</p>
        </div>
        <a href="{{ auth()->check() ? '/dashboard/listings/create' : '/login?redirect=/dashboard/listings/create' }}" class="k-btn k-btn-primary">Post Free Ad</a>
    </div>
    @endif
@else
    <div class="k-empty-state">
        @if(isset($filterCategoryName) && $filterCategoryName)
            <div class="k-empty-state-icon">📭</div>
            <div class="k-empty-state-title">No ads in {{ $filterCategoryName }} yet</div>
            <div class="k-empty-state-sub">Be the first seller in this category — buyers are already looking.</div>
            <div class="k-empty-state-cta">
                <a href="{{ auth()->check() ? '/dashboard/listings/create' : '/login?redirect=/dashboard/listings/create' }}" class="k-btn k-btn-primary">Post the First Ad</a>
            </div>
        @else
            <div class="k-empty-state-icon">🔍</div>
            <div class="k-empty-state-title">No listings found</div>
            <div class="k-empty-state-sub">Try a different keyword or remove some filters.</div>
            <div class="k-empty-state-cta">
                <a href="/listings" class="k-btn k-btn-outline">Clear filters</a>
            </div>
        @endif
    </div>
@endif
