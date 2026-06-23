<a class="km-listing-card" href="/listing/{{$listing->slug}}">
    <div class="km-card-img">
        <span class="km-ad-tag">{{$listing->ad_type ? 'For '.ucfirst($listing->ad_type) : ($listing->store_id ? 'Product' : 'Classified')}}</span>
        @if($listing->is_featured)<span class="km-featured-tag">Featured</span>@endif
        <span class="km-demo-icon">{{ $listing->type === 'product' ? '🛍️' : '📌' }}</span>
        <button type="button">♡</button>
    </div>
    <div class="km-card-body">
        <h3>{{$listing->title}}</h3>
        <p>📍 {{$listing->location ?: 'Kegalle'}}</p>
        <strong>{{$listing->currency ?? 'LKR'}} {{number_format($listing->price ?? 0)}}</strong>
        <small>{{$listing->created_at?->diffForHumans()}}</small>
    </div>
</a>
