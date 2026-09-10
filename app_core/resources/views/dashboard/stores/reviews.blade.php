@extends('layouts.store-dashboard')

@section('title', 'Reviews — ' . ($store->name ?? 'Store'))
@section('eyebrow', 'Reviews')
@section('heading', 'Customer Reviews')

@section('actions')
<a href="/dashboard/stores/{{ $store->id }}" class="kdl-tb-btn kdl-tb-btn-light">
  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
  Back to Overview
</a>
@endsection

@section('content')

{{-- Stat cards --}}
<div class="dbi-grid-3">
    <div class="dbi-stat dbi-stat-green">
        <div class="dbi-ic dbi-ic-green">⭐</div>
        <div>
            <div class="dbi-num">{{ number_format($stats['avg'], 1) }}</div>
            <div class="dbi-lbl">Avg Rating</div>
        </div>
    </div>
    <div class="dbi-stat dbi-stat-blue">
        <div class="dbi-ic dbi-ic-blue">💬</div>
        <div>
            <div class="dbi-num">{{ $stats['total'] }}</div>
            <div class="dbi-lbl">Total Reviews</div>
        </div>
    </div>
    <div class="dbi-stat dbi-stat-amber">
        <div class="dbi-ic dbi-ic-amber">⏳</div>
        <div>
            <div class="dbi-num">{{ $stats['pending'] }}</div>
            <div class="dbi-lbl">Pending</div>
        </div>
    </div>
</div>

<div class="drv-cols">

    {{-- Reviews list --}}
    <section class="dbi-card">
        <div class="dbi-card-head">
            <h3>All Reviews <span class="text-13-light">({{ $reviews->total() }})</span></h3>
        </div>

        @forelse($reviews as $review)
        @php
            $stars = (int)($review->rating ?? 0);
            $pillCls = $review->status === 'approved' ? 'dbi-pill-green' : 'dbi-pill-amber';
        @endphp
        <div class="drv-item">
            <div class="drv-header">
                <div class="drv-user">
                    <div class="drv-avatar">{{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}</div>
                    <div>
                        <div class="drv-name">{{ $review->user->name ?? 'Anonymous' }}</div>
                        <div class="drv-date">{{ $review->created_at?->format('M d, Y') }}</div>
                    </div>
                </div>
                <div class="flex-g8-ns">
                    <div class="drv-stars">
                        @for($i=1;$i<=5;$i++)
                            <span style="color:{{ $i<=$stars ? '#f59e0b' : '#e2e8f0' }}">★</span>
                        @endfor
                    </div>
                    <span class="dbi-pill {{ $pillCls }}">{{ ucfirst($review->status ?? 'pending') }}</span>
                </div>
            </div>
            @if(!empty($review->comment))
                <div class="drv-comment">{{ $review->comment }}</div>
            @else
                <div class="drv-no-comment">No comment left.</div>
            @endif

            {{-- Owner reply --}}
            @if(!empty($review->reply))
                <div class="drv-reply">
                    <div class="drv-reply-label">Your reply &middot; {{ $review->replied_at?->format('M d, Y') }}</div>
                    <div class="drv-reply-text">{{ $review->reply }}</div>
                </div>
                <button type="button" class="drv-reply-toggle" data-open-label="Edit reply" data-close-label="Cancel">Edit reply</button>
                <form method="POST" action="{{ route('dashboard.stores.reviews.reply', [$store->id, $review->id]) }}" class="drv-reply-form" style="display:none">
                    @csrf
                    <textarea name="reply" rows="3" placeholder="Update your reply…" required minlength="2" maxlength="1000">{{ $review->reply }}</textarea>
                    <button type="submit" class="drv-reply-btn">Update Reply</button>
                </form>
            @else
                <button type="button" class="drv-reply-toggle" data-open-label="Reply" data-close-label="Cancel">Reply</button>
                <form method="POST" action="{{ route('dashboard.stores.reviews.reply', [$store->id, $review->id]) }}" class="drv-reply-form" style="display:none">
                    @csrf
                    <textarea name="reply" rows="3" placeholder="Write a public reply to this review…" required minlength="2" maxlength="1000"></textarea>
                    <button type="submit" class="drv-reply-btn">Post Reply</button>
                </form>
            @endif
        </div>
        @empty
        <div class="dbi-empty">
            <div class="emoji-40">⭐</div>
            <strong>No reviews yet</strong>
            <p>Reviews from your customers will appear here once they leave feedback.</p>
        </div>
        @endforelse

        @if($reviews->hasPages())
        <div class="dpt-pagination">
            <span class="dpt-pagination-info">Showing {{ $reviews->firstItem() }}–{{ $reviews->lastItem() }} of {{ $reviews->total() }}</span>
            <div class="dpt-pagination-links">
                @if($reviews->onFirstPage())
                    <span class="disabled">‹</span>
                @else
                    <a href="{{ $reviews->previousPageUrl() }}">‹</a>
                @endif
                @foreach($reviews->getUrlRange(1, $reviews->lastPage()) as $page => $url)
                    @if($page == $reviews->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
                @if($reviews->hasMorePages())
                    <a href="{{ $reviews->nextPageUrl() }}">›</a>
                @else
                    <span class="disabled">›</span>
                @endif
            </div>
        </div>
        @endif
    </section>

    {{-- Rating summary --}}
    <div class="flex-col gap-20">
        <section class="dbi-card" class="text-center">
            <div class="dbi-card-head justify-center"><h3>Rating Summary</h3></div>
            <div class="drv-avg-num">{{ number_format($stats['avg'], 1) }}</div>
            <div class="drv-avg-stars justify-center">
                @for($i=1;$i<=5;$i++)
                    <span style="color:{{ $i <= round($stats['avg']) ? '#f59e0b' : '#e2e8f0' }}">★</span>
                @endfor
            </div>
            <div class="drv-avg-lbl">Based on {{ $stats['approved'] }} approved review{{ $stats['approved'] != 1 ? 's' : '' }}</div>

            {{-- Star breakdown --}}
            @php
                $breakdown = [];
                for($r=5;$r>=1;$r--){
                    $breakdown[$r] = \App\Models\Review::where('store_id',$store->id)->where('status','approved')->where('rating',$r)->count();
                }
                $maxB = max(array_values($breakdown) ?: [1]);
            @endphp
            <div class="csp5-309">
                @foreach($breakdown as $star => $cnt)
                <div class="flex-g8-mb7b">
                    <span class="rank-num2">{{ $star }}</span>
                    <span class="amber-fs13-ns">★</span>
                    <div class="progress-bar">
                        <div style="height:100%;border-radius:99px;background:linear-gradient(90deg,#f59e0b,#fbbf24);width:{{ $maxB > 0 ? round($cnt/$maxB*100) : 0 }}%;transition:width .4s"></div>
                    </div>
                    <span class="rank-num">{{ $cnt }}</span>
                </div>
                @endforeach
            </div>
        </section>
    </div>
</div>

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.querySelectorAll('.drv-reply-toggle').forEach(function(btn){
  btn.addEventListener('click', function(){
    var form = btn.nextElementSibling;
    var open = form.style.display === 'none' || form.style.display === '';
    form.style.display = open ? 'block' : 'none';
    btn.textContent = open ? (btn.dataset.closeLabel || 'Cancel') : (btn.dataset.openLabel || btn.textContent);
  });
});
</script>
@endpush

@endsection
