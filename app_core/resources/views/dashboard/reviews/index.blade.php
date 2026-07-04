@extends('layouts.dashboard')
@section('title','My Reviews')

@section('content')
<div class="container" style="padding:24px 0">
    <h1 style="font-size:22px;font-weight:800;margin-bottom:6px">My Reviews</h1>
    <p style="font-size:14px;color:var(--k-text-muted);margin-bottom:24px">Reviews from customers on your stores and listings</p>

    <!-- Stats -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:24px">
        <div style="background:#fff;border:1px solid var(--k-border,#e5e8ef);border-radius:12px;padding:16px;text-align:center">
            <div style="font-size:28px;font-weight:800;color:var(--k-primary,#1b5e20)">{{ $totalReviews ?? 0 }}</div>
            <div style="font-size:12px;color:var(--k-text-muted)">Total Reviews</div>
        </div>
        <div style="background:#fff;border:1px solid var(--k-border,#e5e8ef);border-radius:12px;padding:16px;text-align:center">
            <div style="font-size:28px;font-weight:800;color:#f59e0b">{{ $avgRating ? round($avgRating, 1) : '—' }}</div>
            <div style="font-size:12px;color:var(--k-text-muted)">Average Rating</div>
        </div>
        <div style="background:#fff;border:1px solid var(--k-border,#e5e8ef);border-radius:12px;padding:16px;text-align:center">
            <div style="font-size:28px;font-weight:800">
                @for($s = 1; $s <= 5; $s++)
                    <span style="color:{{ $s <= round($avgRating ?? 0) ? '#f59e0b' : '#e5e7eb' }};font-size:16px">★</span>
                @endfor
            </div>
            <div style="font-size:12px;color:var(--k-text-muted)">Star Rating</div>
        </div>
    </div>

    <!-- Review List -->
    <div style="background:#fff;border:1px solid var(--k-border,#e5e8ef);border-radius:12px;overflow:hidden">
        @forelse($reviews as $review)
        <div style="padding:16px 20px;border-bottom:1px solid var(--k-border,#f1f5f9);display:flex;gap:12px;align-items:flex-start">
            <div style="width:40px;height:40px;border-radius:50%;background:var(--k-primary,#1b5e20);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;flex-shrink:0">{{ strtoupper(substr(optional($review->user)->name ?? 'U', 0, 1)) }}</div>
            <div style="flex:1;min-width:0">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;flex-wrap:wrap;gap:4px">
                    <div>
                        <strong style="font-size:14px">{{ optional($review->user)->name ?? 'User' }}</strong>
                        <span style="font-size:12px;color:var(--k-text-muted);margin-left:8px">{{ $review->created_at?->diffForHumans() }}</span>
                    </div>
                    <div>
                        @for($s = 1; $s <= 5; $s++)
                            <span style="color:{{ $s <= $review->rating ? '#f59e0b' : '#e5e7eb' }};font-size:14px">★</span>
                        @endfor
                        <span style="font-size:11px;padding:2px 8px;border-radius:6px;margin-left:6px;font-weight:600;background:{{ $review->status === 'approved' ? '#e8f5e9' : ($review->status === 'rejected' ? '#ffebee' : '#fff3e0') }};color:{{ $review->status === 'approved' ? '#2e7d32' : ($review->status === 'rejected' ? '#d32f2f' : '#e65100') }}">{{ ucfirst($review->status) }}</span>
                    </div>
                </div>
                <p style="font-size:14px;color:var(--k-text-secondary);line-height:1.5;margin:0">{{ $review->comment }}</p>
                @if($review->listing)
                <div style="font-size:12px;color:var(--k-text-muted);margin-top:4px">on <a href="/listings/{{ $review->listing->slug }}" style="color:var(--k-primary)">{{ $review->listing->title }}</a></div>
                @elseif($review->store)
                <div style="font-size:12px;color:var(--k-text-muted);margin-top:4px">on store <a href="/store/{{ $review->store->slug }}" style="color:var(--k-primary)">{{ $review->store->name }}</a></div>
                @endif
            </div>
        </div>
        @empty
        <div style="padding:40px;text-align:center;color:var(--k-text-muted)">
            <span style="font-size:36px;display:block;margin-bottom:8px">📝</span>
            <p>No reviews yet. Reviews from your customers will appear here.</p>
        </div>
        @endforelse
    </div>

    @if($reviews->hasPages())
    <div style="margin-top:16px">{{ $reviews->links() }}</div>
    @endif
</div>
@endsection
