@extends('layouts.admin')
@section('title','Reviews')
@section('page','Reviews')
@section('heading','Review Management')
@section('subheading','Moderate customer reviews on stores and listings')

@section('actions')
<div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
    <form method="get" style="display:flex;gap:8px;flex-wrap:wrap">
        <select name="status" style="height:40px;border:1px solid var(--ka-border);border-radius:10px;padding:0 10px">
            <option value="">All Status</option>
            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
            <option value="approved" @selected(request('status') === 'approved')>Approved</option>
            <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
        </select>
        <select name="rating" style="height:40px;border:1px solid var(--ka-border);border-radius:10px;padding:0 10px">
            <option value="">All Ratings</option>
            @for($r = 5; $r >= 1; $r--)
            <option value="{{ $r }}" @selected(request('rating') == $r)>{{ $r }} Star{{ $r > 1 ? 's' : '' }}</option>
            @endfor
        </select>
        <button class="ka-btn ka-btn-light">Filter</button>
        @if(request()->hasAny(['status','rating']))
            <a href="/admin/reviews" class="ka-btn ka-btn-light" style="color:#d32f2f">Clear</a>
        @endif
    </form>
</div>
@endsection

@section('content')

@if(session('success'))
<div style="background:#E8F5E9;color:#2E7D32;padding:12px 18px;border-radius:12px;margin-bottom:18px;font-weight:600;font-size:13px">{{ session('success') }}</div>
@endif

<!-- Stats -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:20px">
    <div style="background:#fff;border:1.5px solid var(--ka-border,#e5e8ef);border-radius:12px;padding:16px 20px;text-align:center">
        <div style="font-size:28px;font-weight:800;color:var(--ka-primary,#1b5e20)">{{ $reviews->total() }}</div>
        <div style="font-size:12px;color:#667085;font-weight:600">Total Reviews</div>
    </div>
    <div style="background:#fff;border:1.5px solid var(--ka-border,#e5e8ef);border-radius:12px;padding:16px 20px;text-align:center">
        <div style="font-size:28px;font-weight:800;color:#f59e0b">{{ round($reviews->avg('rating') ?? 0, 1) }}</div>
        <div style="font-size:12px;color:#667085;font-weight:600">Avg Rating</div>
    </div>
    <div style="background:#fff;border:1.5px solid var(--ka-border,#e5e8ef);border-radius:12px;padding:16px 20px;text-align:center">
        <div style="font-size:28px;font-weight:800;color:#e65100">{{ $reviews->where('status', 'pending')->count() }}</div>
        <div style="font-size:12px;color:#667085;font-weight:600">Pending</div>
    </div>
    <div style="background:#fff;border:1.5px solid var(--ka-border,#e5e8ef);border-radius:12px;padding:16px 20px;text-align:center">
        <div style="font-size:28px;font-weight:800;color:#2e7d32">{{ $reviews->where('status', 'approved')->count() }}</div>
        <div style="font-size:12px;color:#667085;font-weight:600">Approved</div>
    </div>
</div>

<section class="sa-card">
<div class="sa-card-head"><h2>All Reviews</h2><span>{{ $reviews->total() }} reviews</span></div>
<div class="sa-table-wrap">
<table class="sa-table">
<thead>
<tr>
    <th>Reviewer</th>
    <th>Target</th>
    <th style="text-align:center">Rating</th>
    <th style="max-width:280px">Comment</th>
    <th style="text-align:center">Status</th>
    <th>Date</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>
@forelse($reviews as $review)
<tr>
    <td>
        <b>{{ optional($review->user)->name ?? 'Unknown' }}</b>
        <br><small style="color:#94a3b8">{{ optional($review->user)->email ?? '' }}</small>
    </td>
    <td>
        @if($review->store)
            <span style="font-size:11px;background:#e3f2fd;color:#1565c0;padding:2px 6px;border-radius:4px;font-weight:600">STORE</span>
            <br>{{ $review->store->name }}
        @elseif($review->listing)
            <span style="font-size:11px;background:#f3e5f5;color:#7b1fa2;padding:2px 6px;border-radius:4px;font-weight:600">LISTING</span>
            <br>{{ \Illuminate\Support\Str::limit($review->listing->title, 30) }}
        @else
            —
        @endif
    </td>
    <td style="text-align:center">
        <span style="color:#f59e0b;font-size:15px">{{ str_repeat('★', (int) $review->rating) }}</span><span style="color:#e5e7eb;font-size:15px">{{ str_repeat('★', 5 - (int) $review->rating) }}</span>
    </td>
    <td style="max-width:280px;font-size:13px">{{ \Illuminate\Support\Str::limit($review->comment, 100) }}</td>
    <td style="text-align:center">
        <span class="sa-status {{ $review->status === 'approved' ? 'active' : ($review->status === 'rejected' ? 'suspended' : '') }}">{{ ucfirst($review->status ?? 'pending') }}</span>
    </td>
    <td><small style="color:#94a3b8">{{ $review->created_at?->format('M d, Y') }}</small></td>
    <td class="sa-actions-inline">
        @if($review->status !== 'approved')
        <form method="post" action="/admin/reviews/{{ $review->id }}/approve" style="display:inline">@csrf<button style="color:#2e7d32">Approve</button></form>
        @endif
        @if($review->status !== 'rejected')
        <form method="post" action="/admin/reviews/{{ $review->id }}/reject" style="display:inline">@csrf<button style="color:#e65100">Reject</button></form>
        @endif
        <form method="post" action="/admin/reviews/{{ $review->id }}" style="display:inline" onsubmit="return confirm('Delete this review?')">@csrf @method('DELETE')<button class="danger">Delete</button></form>
    </td>
</tr>
@empty
<tr><td colspan="7" style="text-align:center;padding:40px;color:#94a3b8">No reviews found.</td></tr>
@endforelse
</tbody>
</table>
</div>
<div style="padding:16px 20px">{{ $reviews->links() }}</div>
</section>
@endsection
