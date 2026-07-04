@if($listings->count())
    <div class="k-grid-4 k-grid-tight">
        @foreach($listings as $listing)
            @include('frontend.listings.card',['listing'=>$listing])
        @endforeach
    </div>
    {{ $listings->links('vendor.pagination.k-theme') }}
@else
    <div class="k-empty-state-box">
        <h3 class="k-empty-state-heading">No listings found</h3>
        <p class="k-text-secondary">Try changing filters or search keyword.</p>
    </div>
@endif
