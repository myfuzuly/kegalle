@forelse($deals as $deal)
@php
    $thumb = $deal->listing?->images?->first()?->path ?? null;
    $statusYes = $deal->status === 'approved';
    $statusPending = $deal->status === 'pending';
    $isActive  = $deal->status === 'approved' && $deal->starts_at && $deal->ends_at && $deal->starts_at <= now() && $deal->ends_at >= now();
    $isExpired = $deal->ends_at && $deal->ends_at < now();
    $dealTitle = $deal->listing->title ?? $deal->title ?? 'Untitled Deal';
    $sellerName = $deal->poster_type === 'independent' ? ($deal->organizer_name ?? 'Independent') : ($deal->user->name ?? '—');
    $storeName  = $deal->poster_type === 'independent' ? '🎤 Indep.' : ($deal->store->name ?? 'Personal');
@endphp
<tr>
    <td class="w64-pr0">
        @if($thumb)
        <img src="{{ asset('storage/'.ltrim($thumb,'/')) }}" alt="{{ $dealTitle }}" class="thumb-sm">
        @else
        <span class="icon-box-44c">🛍️</span>
        @endif
    </td>
    <td class="max-w-200">
        <b class="text-truncate text-truncate-190">{{ $dealTitle }}</b>
        <small class="text-muted">{{ optional(optional($deal->listing)->category)->name ?? ($deal->poster_type === 'independent' ? 'Independent' : '') }}</small>
    </td>
    <td>
        <small class="fw-600">{{ $storeName }}</small><br>
        <small class="text-gray-lgt">{{ $sellerName }}</small>
    </td>
    <td>
        <b class="text-green">LKR {{ number_format($deal->deal_price) }}</b><br>
        <small><s>{{ number_format($deal->original_price) }}</s></small>
    </td>
    <td class="red-fw7">-{{ number_format($deal->discount_percent,0) }}%</td>
    <td>
        <small>{{ optional($deal->starts_at)->format('M d') ?? '—' }} — {{ optional($deal->ends_at)->format('M d') ?? '—' }}</small>
        @if($isExpired)<br><span class="red-fs10">Expired</span>
        @elseif($isActive)<br><span class="green-fs10">● Live</span>@endif
    </td>
    <td>
        @if($deal->is_flash)<span class="badge-amber-xs">⚡ Flash</span> @endif
        @if($deal->is_featured)<span class="badge-green-xs">★ Featured</span> @endif
        @if(!$deal->is_flash && !$deal->is_featured)<small class="text-gray-lgt">Standard</small>@endif
    </td>
    <td><span class="sa-status {{ $statusYes ? 'active' : ($statusPending ? 'pending' : 'suspended') }}" class="fs-11">{{ ucfirst($deal->status) }}</span></td>
    <td class="sa-actions-inline">
        @if($deal->listing)<a href="/listings/{{ $deal->listing->slug }}" target="_blank">View</a>@endif
        <a href="/admin/deals/{{ $deal->id }}/edit">Edit</a>
        @if($deal->status === 'pending')
            <form method="post" action="/admin/deals/{{ $deal->id }}/approve">@csrf<button>Approve</button></form>
            <form method="post" action="/admin/deals/{{ $deal->id }}/reject">@csrf<button class="danger">Reject</button></form>
        @endif
        <form method="post" action="/admin/deals/{{ $deal->id }}/feature">@csrf<button>{{ $deal->is_featured ? 'Unfeature' : 'Feature' }}</button></form>
        <form method="post" action="/admin/deals/{{ $deal->id }}/flash">@csrf<button>{{ $deal->is_flash ? 'Unflash' : '⚡ Flash' }}</button></form>
        <form method="post" action="/admin/deals/{{ $deal->id }}" class="d-inline">@csrf @method('DELETE')<button class="danger">Del</button></form>
    </td>
</tr>
@empty
<tr><td colspan="9" class="td-empty">No deals found.</td></tr>
@endforelse
