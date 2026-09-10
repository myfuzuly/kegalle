@forelse($listings as $listing)
@php
    $thumb = optional($listing->images->first())->path ?? $listing->image ?? null;
    $statusYes = $listing->status === 'approved';
    $statusPending = $listing->status === 'pending';
    $markChar = $statusYes ? '✓' : ($statusPending ? '…' : '✕');
    $statusStyle = $statusYes ? 'background:#E8F5E9;color:#2E7D32' : ($statusPending ? 'background:#FFF3E0;color:#E65100' : 'background:#FFEBEE;color:#C62828');
@endphp
<tr>
    <td class="w60-pr0">
        @if($thumb)<img src="{{ asset('storage/'.ltrim($thumb,'/')) }}" alt="{{ $listing->title }}" class="thumb-sm">@else<span class="icon-box-44">🛍️</span>@endif
    </td>
    <td class="max-w-200">
        <b class="text-truncate text-truncate-190">{{ $listing->title }}</b>
        <small>{{ $listing->type ?? 'product' }}</small>
    </td>
    <td>{{ $listing->locationModel->name ?? $listing->location ?? '-' }}</td>
    <td>{{ $listing->store->name ?? $listing->user->name ?? 'Seller' }}</td>
    <td>LKR {{ number_format($listing->price ?? 0) }}</td>
    <td><span style="display:inline-flex;align-items:center;gap:5px;padding:4px 11px;border-radius:20px;font-size:11.5px;font-weight:700;white-space:nowrap;{{ $statusStyle }}">{{ $markChar }} {{ ucfirst($listing->status) }}</span></td>
    <td>@if($listing->is_featured)<span class="status-amber">★ Featured</span>@else<span class="status-gray">— No</span>@endif</td>
    <td class="sa-actions-inline">
        <a href="/listings/{{ $listing->slug }}" target="_blank">View</a>
        <a href="/admin/listings/{{ $listing->id }}/edit">Edit</a>
        <form method="post" action="/admin/listings/{{ $listing->id }}/reject" onsubmit="return confirm('Reject this listing?')">@csrf<button class="danger">Reject</button></form>
        <form method="post" action="/admin/listings/{{ $listing->id }}/feature">@csrf<button>Feature</button></form>
        <form method="post" action="/admin/listings/{{ $listing->id }}/top">@csrf<button>Top</button></form>
    </td>
</tr>
@empty
<tr><td colspan="8" class="td-empty">No listings found.</td></tr>
@endforelse
