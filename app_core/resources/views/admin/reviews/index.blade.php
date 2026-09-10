@extends('layouts.admin')
@section('title','Reviews')
@section('page','Reviews')
@section('heading','Review Management')
@section('subheading','Moderate customer reviews on stores and listings')

@push('styles')

@endpush

@section('content')

@if(session('success'))
<div class="alert-success">✓ {{ session('success') }}</div>
@endif

{{-- Stats --}}
<div class="rev-stats">
    <div class="rev-stat blue">
        <div class="rev-stat-num">{{ $reviews->total() }}</div>
        <div class="rev-stat-label">Total Reviews</div>
    </div>
    <div class="rev-stat amber">
        <div class="rev-stat-num">{{ number_format($reviews->avg('rating') ?? 0, 1) }}</div>
        <div class="rev-stat-label">Avg Rating</div>
    </div>
    <div class="rev-stat orange">
        <div class="rev-stat-num">{{ $reviews->where('status','pending')->count() }}</div>
        <div class="rev-stat-label">Pending</div>
    </div>
    <div class="rev-stat green">
        <div class="rev-stat-num">{{ $reviews->where('status','approved')->count() }}</div>
        <div class="rev-stat-label">Approved</div>
    </div>
</div>

<div class="rev-card">
    <div class="rev-card-head">
        <div class="rev-card-icon">★</div>
        <span class="rev-card-title">All Reviews</span>
        <span class="rev-card-meta">{{ $reviews->total() }} total</span>
    </div>

    <form method="get" class="rev-filter">
        {{-- Status ksd --}}
        <input type="hidden" name="status" id="revStatusVal" value="{{ request('status','') }}">
        <div class="ksd-wrap">
            <button type="button" class="ksd-trigger {{ request('status') ? 'ksd-has-value':'' }}" id="revStatusTrigger">
                <span class="ksd-trigger-text {{ request('status') ? '':'placeholder' }}" id="revStatusLabel">{{ request('status') ? ucfirst(request('status')).' reviews' : 'All Status' }}</span>
                <svg class="ksd-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div class="ksd-dropdown" id="revStatusDropdown">
                <div class="ksd-list">
                    <div class="ksd-item {{ !request('status') ? 'ksd-selected':'' }}" data-value="" data-label="All Status">All Status</div>
                    <div class="ksd-item {{ request('status')==='pending' ? 'ksd-selected':'' }}" data-value="pending" data-label="Pending">⏳ Pending</div>
                    <div class="ksd-item {{ request('status')==='approved' ? 'ksd-selected':'' }}" data-value="approved" data-label="Approved">✅ Approved</div>
                    <div class="ksd-item {{ request('status')==='rejected' ? 'ksd-selected':'' }}" data-value="rejected" data-label="Rejected">❌ Rejected</div>
                </div>
            </div>
        </div>
        {{-- Rating ksd --}}
        <input type="hidden" name="rating" id="revRatingVal" value="{{ request('rating','') }}">
        <div class="ksd-wrap">
            <button type="button" class="ksd-trigger {{ request('rating') ? 'ksd-has-value':'' }}" id="revRatingTrigger">
                <span class="ksd-trigger-text {{ request('rating') ? '':'placeholder' }}" id="revRatingLabel">{{ request('rating') ? request('rating').' Star'.( request('rating')>1 ? 's':'' ) : 'All Ratings' }}</span>
                <svg class="ksd-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div class="ksd-dropdown" id="revRatingDropdown">
                <div class="ksd-list">
                    <div class="ksd-item {{ !request('rating') ? 'ksd-selected':'' }}" data-value="" data-label="All Ratings">All Ratings</div>
                    @for($r=5;$r>=1;$r--)
                    <div class="ksd-item {{ request('rating')==$r ? 'ksd-selected':'' }}" data-value="{{ $r }}" data-label="{{ $r }} Star{{ $r>1 ? 's':'' }}">{{ str_repeat('★',$r) }} {{ $r }} Star{{ $r>1 ? 's':'' }}</div>
                    @endfor
                </div>
            </div>
        </div>
        <button type="submit" class="rev-filter-btn">Filter</button>
        @if(request()->hasAny(['status','rating']))<a href="/admin/reviews" class="rev-filter-clear">✕ Clear</a>@endif
    </form>

    <div class="rev-table-wrap">
    <table class="rev-table">
    <thead><tr>
        <th>Reviewer</th>
        <th>Target</th>
        <th class="center-100">Rating</th>
        <th>Comment</th>
        <th class="center-100">Status</th>
        <th class="w-100">Date</th>
        <th>Actions</th>
    </tr></thead>
    <tbody>
    @forelse($reviews as $review)
    <tr>
        <td>
            <span class="fw7-trunc-150">{{ optional($review->user)->name ?? 'Unknown' }}</span>
            <small class="muted-trunc-150">{{ optional($review->user)->email ?? '' }}</small>
        </td>
        <td>
            @if($review->store)
                <span class="rev-target-badge store">STORE</span>
                <span class="block-13-mt2">{{ $review->store->name }}</span>
            @elseif($review->listing)
                <span class="rev-target-badge listing">LISTING</span>
                <span class="block-13-mt2">{{ \Illuminate\Support\Str::limit($review->listing->title,30) }}</span>
            @else —
            @endif
        </td>
        <td class="text-center">
            <div class="rev-stars">
                @for($i=1;$i<=5;$i++)
                <span style="color:{{ $i<=(int)$review->rating ? '#f59e0b':'#e5e7eb' }}">★</span>
                @endfor
            </div>
            <div class="fs-11 text-muted">{{ $review->rating }}/5</div>
        </td>
        <td class="csp5-317">{{ \Illuminate\Support\Str::limit($review->comment,100) }}</td>
        <td class="text-center">
            <span class="sa-status {{ $review->status==='approved' ? 'active':($review->status==='rejected' ? 'suspended':'') }}">{{ ucfirst($review->status ?? 'pending') }}</span>
        </td>
        <td class="nowrap"><small class="text-muted">{{ $review->created_at?->format('M d, Y') }}</small></td>
        <td>
            <div class="rev-actions">
                @if($review->status !== 'approved')
                <form method="post" action="/admin/reviews/{{ $review->id }}/approve" class="d-contents">@csrf<button class="rev-act">✓ Approve</button></form>
                @endif
                @if($review->status !== 'rejected')
                <form method="post" action="/admin/reviews/{{ $review->id }}/reject" class="d-contents">@csrf<button class="rev-act">✕ Reject</button></form>
                @endif
                <form method="post" action="/admin/reviews/{{ $review->id }}" onsubmit="return confirm('Delete this review?')" class="d-contents">@csrf @method('DELETE')
                    <button class="rev-act danger">🗑</button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr><td class="empty-state-lg" colspan="7">No reviews found.</td></tr>
    @endforelse
    </tbody>
    </table>
    </div>
    <div class="p14-22">{{ $reviews->links('vendor.pagination.ka-admin') }}</div>
</div>

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
function simpleKsd(triggerId, dropdownId, hiddenId, labelId) {
    var trigger=document.getElementById(triggerId);
    var dropdown=document.getElementById(dropdownId);
    var hidden=document.getElementById(hiddenId);
    var labelEl=document.getElementById(labelId);
    if(!trigger) return;
    var open=false;
    function openD(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); open=true; }
    function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    dropdown.querySelectorAll('.ksd-item').forEach(function(item){
        item.addEventListener('click',function(){
            hidden.value=this.dataset.value;
            labelEl.textContent=this.dataset.label;
            labelEl.classList.toggle('placeholder',!this.dataset.value);
            trigger.classList.toggle('ksd-has-value',!!this.dataset.value);
            dropdown.querySelectorAll('.ksd-item').forEach(function(i){i.classList.remove('ksd-selected');});
            this.classList.add('ksd-selected');
            closeD();
        });
    });
}
simpleKsd('revStatusTrigger','revStatusDropdown','revStatusVal','revStatusLabel');
simpleKsd('revRatingTrigger','revRatingDropdown','revRatingVal','revRatingLabel');
</script>
@endpush
