@extends('layouts.admin')
@section('title','Reviews')
@section('page','Reviews')
@section('heading','Reviews')
@section('subheading','Moderate customer reviews left on stores and listings')
@section('content')
<section class="sa-card"><div class="sa-card-head"><h2>Reviews from Database</h2><span>{{ $reviews->total() }} reviews</span></div>
<div class="sa-table-wrap"><table class="sa-table"><thead><tr><th>Reviewer</th><th>Target</th><th>Rating</th><th>Comment</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($reviews as $review)<tr>
    <td><b>{{ $review->user->name ?? 'Unknown' }}</b><small>#{{ $review->id }}</small></td>
    <td>{{ $review->store->name ?? $review->listing->title ?? '—' }}</td>
    <td>{{ str_repeat('★', (int) $review->rating).str_repeat('☆', 5 - (int) $review->rating) }}</td>
    <td style="max-width:280px">{{ \Illuminate\Support\Str::limit($review->comment, 100) }}</td>
    <td><span class="sa-status {{ $review->status === 'approved' ? 'active' : ($review->status === 'rejected' ? 'suspended' : '') }}">{{ ucfirst($review->status ?? 'pending') }}</span></td>
    <td class="sa-actions-inline">
        <form method="post" action="/admin/reviews/{{ $review->id }}/approve">@csrf<button title="Approve">Approve</button></form>
        <form method="post" action="/admin/reviews/{{ $review->id }}/reject">@csrf<button title="Reject">Reject</button></form>
        <form method="post" action="/admin/reviews/{{ $review->id }}" onsubmit="return confirm('Delete this review?')">@csrf @method('DELETE')<button class="danger" title="Delete">Delete</button></form>
    </td>
</tr>@empty<tr><td colspan="6">No reviews found.</td></tr>@endforelse
</tbody></table></div>{{ $reviews->links() }}</section>
@endsection
