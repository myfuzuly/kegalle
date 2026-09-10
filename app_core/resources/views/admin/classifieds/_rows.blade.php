@forelse($listings as $listing)
@php
    $thumb = optional($listing->images->first())->path ?? $listing->image ?? null;
    $isApproved = $listing->status === 'approved';
    $statusStyle = $isApproved ? 'background:#E8F5E9;color:#2E7D32' : 'background:#FFF3E0;color:#E65100';
    $markChar = $isApproved ? '✓' : '…';
@endphp
<tr>
    <td class="w64-pr0">
        @if($thumb)
            <img src="{{ asset('storage/'.ltrim($thumb,'/')) }}" alt="{{ $listing->title }}" class="thumb-sm">
        @else
            <span class="icon-box-44">📋</span>
        @endif
    </td>
    <td class="max-w-200">
        <b class="text-truncate text-truncate-190">{{ $listing->title }}</b>
        <small class="text-muted">{{ $listing->category->name ?? '—' }}</small>
    </td>
    <td>{{ $listing->locationModel->name ?? $listing->location ?? '-' }}</td>
    <td>
        @if($listing->user_id)
            {{ optional($listing->store)->name ?? optional($listing->user)->name ?? 'Seller' }}
        @else
            <b>{{ $listing->poster_name ?? '—' }}</b>
            @if($listing->poster_phone)<br><a class="fs12-sky" href="tel:{{ $listing->poster_phone }}">{{ $listing->poster_phone }}</a>@endif
            <br><small class="text-hint-xs">No account</small>
        @endif
    </td>
    <td>LKR {{ number_format($listing->price ?? 0) }}</td>
    <td><span style="display:inline-flex;align-items:center;gap:5px;padding:4px 11px;border-radius:20px;font-size:11.5px;font-weight:700;white-space:nowrap;{{ $statusStyle }}">{{ $markChar }} {{ ucfirst($listing->status) }}</span></td>
    <td class="sa-actions-inline">
        <a href="/listings/{{ $listing->slug }}" target="_blank">View</a>
        <a href="/admin/classifieds/{{ $listing->id }}/edit">Edit</a>
        <form method="post" action="/admin/listings/{{ $listing->id }}/reject" onsubmit="return confirm('Reject this ad?')">@csrf<button class="danger">Reject</button></form>
        <form method="post" action="/admin/listings/{{ $listing->id }}/feature">@csrf<button>★ Feature</button></form>
        <form method="post" action="/admin/listings/{{ $listing->id }}/top">@csrf<button>▲ Top</button></form>
    </td>
</tr>
@empty
<tr><td colspan="7" class="td-empty">No classified ads found.</td></tr>
@endforelse
