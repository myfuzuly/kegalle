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
        <x-admin.row-check form="klBulkForm" :value="$listing->id" :label="'Select '.$listing->title" />
        @if($thumb)<img src="{{ asset('storage/'.ltrim($thumb,'/')) }}" alt="{{ $listing->title }}" class="thumb-sm">@else<span class="icon-box-44">🛍️</span>@endif
    </td>
    <td class="max-w-200">
        <b class="text-truncate text-truncate-190">{{ $listing->title }}</b>
        <small>{{ $listing->type ?? 'product' }}</small>
    </td>
    <td>{{ $listing->locationModel->name ?? $listing->location ?? '-' }}</td>
    <td>{{ $listing->store->name ?? $listing->user->name ?? 'Seller' }}</td>
    <td>LKR {{ number_format($listing->price ?? 0) }}</td>
    <td><span class="ka-pill" style="{{ $statusStyle }}">{{ $markChar }} {{ ucfirst($listing->status) }}</span></td>
    <td>@if($listing->is_featured)<span class="status-amber">★ Featured</span>@else<span class="status-gray">— No</span>@endif</td>
    <td class="sa-actions-inline">
        <a href="/listings/{{ $listing->slug }}" target="_blank">View</a>
        <a href="/admin/listings/{{ $listing->id }}/edit">Edit</a>
        <form method="post" action="/admin/listings/{{ $listing->id }}/reject" data-confirm="Reject this listing?" >@csrf<button class="danger">Reject</button></form>
        <form method="post" action="/admin/listings/{{ $listing->id }}/feature">@csrf<button>Feature</button></form>
        <form method="post" action="/admin/listings/{{ $listing->id }}/top">@csrf<button>Top</button></form>
    </td>
</tr>
@empty
<x-admin.table-empty colspan="8" message="No listings found." hint="Try another status or type filter, or clear the search." />
@endforelse
