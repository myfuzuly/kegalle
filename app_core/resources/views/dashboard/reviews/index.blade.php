@extends('layouts.dashboard')
@section('banner_sub', 'Reviews left by customers on your listings and stores.')
@section('title','My Reviews')
@section('heading','My Reviews')
@section('subheading','Reviews from customers on your stores and listings.')

@section('content')

<div class="rv-stats">
  <div class="rv-stat">
    <div class="rv-stat-n" class="text-green">{{ $totalReviews ?? 0 }}</div>
    <div class="rv-stat-l">Total Reviews</div>
  </div>
  <div class="rv-stat">
    <div class="rv-stat-n text-amber500">{{ $avgRating ? round($avgRating, 1) : '—' }}</div>
    <div class="rv-stat-l">Avg Rating</div>
  </div>
  <div class="rv-stat">
    <div class="rv-stars">
      @for($s = 1; $s <= 5; $s++)
        <span style="color:{{ $s <= round($avgRating ?? 0) ? '#f59e0b' : '#e5e7eb' }};font-size:18px">★</span>
      @endfor
    </div>
    <div class="rv-stat-l">Star Rating</div>
  </div>
</div>

{{-- Client-side filter bar --}}
<div class="flex-fw-g10-mb16">
  <div class="tab-group">
    @foreach(['0'=>'All', '5'=>'★★★★★', '4'=>'★★★★', '3'=>'★★★', '1'=>'1-2★'] as $val => $label)
    <button type="button" class="rv-star-pill lp-filter-tab {{ $val==='0'?'active':'' }} btn-bare" data-stars="{{ $val }}">{{ $label }}</button>
    @endforeach
  </div>
  <input class="filter-input" type="text" id="rvSearch" placeholder="Search reviews…"
>
</div>

<div class="rv-card">
  @forelse($reviews as $review)
  <div class="rv-row" data-rating="{{ $review->rating }}" data-text="{{ strtolower($review->comment ?? '') }}">
    <div class="rv-avatar">{{ strtoupper(substr(optional($review->user)->name ?? 'U', 0, 1)) }}</div>
    <div class="rv-body">
      <div class="rv-meta-row">
        <div>
          <span class="rv-reviewer">{{ optional($review->user)->name ?? 'User' }}</span>
          <span class="rv-time">{{ $review->created_at?->diffForHumans() }}</span>
        </div>
        <div class="rv-right">
          <div class="rv-star-row">
            @for($s = 1; $s <= 5; $s++)
            <span class="rv-star" style="color:{{ $s <= $review->rating ? '#f59e0b' : '#e5e7eb' }}">★</span>
            @endfor
          </div>
          @php
            $stCls = match($review->status ?? 'pending') { 'approved' => 'rv-status-approved', 'rejected' => 'rv-status-rejected', default => 'rv-status-pending' };
          @endphp
          <span class="rv-status {{ $stCls }}">{{ ucfirst($review->status ?? 'pending') }}</span>
        </div>
      </div>
      <p class="rv-comment">{{ $review->comment }}</p>
      @if($review->listing)
      <div class="rv-source">on listing <a href="/listings/{{ $review->listing->slug }}">{{ $review->listing->title }}</a></div>
      @elseif($review->store)
      <div class="rv-source">on store <a href="/store/{{ $review->store->slug }}">{{ $review->store->name }}</a></div>
      @endif
    </div>
  </div>
  @empty
  <div class="rv-empty">
    <span class="emoji-48">📝</span>
    <strong class="heading-sm">No reviews yet</strong>
    <p class="desc-text">Reviews from customers on your listings and stores will appear here.</p>
  </div>
  @endforelse
</div>

@if($reviews->hasPages())
<div class="mt-16">{{ $reviews->links('vendor.pagination.dashboard') }}</div>
@endif

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
  var activeStars='0';
  function applyFilters(){
    var q=document.getElementById('rvSearch').value.toLowerCase().trim();
    document.querySelectorAll('.rv-row').forEach(function(r){
      var rating=parseInt(r.dataset.rating||0);
      var matchStar = activeStars==='0' || (activeStars==='1' ? rating<=2 : rating>=parseInt(activeStars));
      var matchQ    = !q || r.dataset.text.includes(q);
      r.style.display=(matchStar&&matchQ)?'':'none';
    });
  }
  document.querySelectorAll('.rv-star-pill').forEach(function(btn){
    btn.addEventListener('click',function(){
      activeStars=btn.dataset.stars;
      document.querySelectorAll('.rv-star-pill').forEach(function(b){b.classList.remove('active');});
      btn.classList.add('active');
      applyFilters();
    });
  });
  var si=document.getElementById('rvSearch');
  if(si) si.addEventListener('input',applyFilters);
})();
</script>
@endpush

@endsection
