@forelse($events as $event)
@php
    $statusYes = $event->status === 'published';
    $statusPending = $event->status === 'pending';
    $markChar = $statusYes ? '✓' : ($statusPending ? '…' : '✕');
    $statusStyle = $statusYes ? 'background:#E8F5E9;color:#2E7D32' : ($statusPending ? 'background:#FFF3E0;color:#E65100' : 'background:#FFEBEE;color:#C62828');
    $isPast = $event->event_date && $event->event_date->isPast();
@endphp
<tr>
    <td class="w-200">
        <b class="text-truncate text-truncate-190">{{ $event->title }}</b>
        <small class="text-muted">{{ $event->event_type ? ucfirst($event->event_type) : 'Event' }}</small>
    </td>
    <td>
        <span class="fs125-nw">{{ $event->event_date ? $event->event_date->format('d M Y') : '—' }}</span>
        @if($isPast)<span class="block-red-xs">Past</span>@else<span class="block-green-xs">● Upcoming</span>@endif
    </td>
    <td>
        <span class="fs-12h">{{ $event->venue ?? '—' }}</span>
        @if($event->location)<small class="block-muted">{{ $event->location }}</small>@endif
    </td>
    <td class="fs-13">{{ $event->organizer_name ?? ($event->user->name ?? '—') }}</td>
    <td>@if($event->is_free)<span class="green-fw7-fs13">Free</span>@else<b class="fs-13">LKR {{ number_format($event->price ?? 0) }}</b>@endif</td>
    <td><small>{{ ucfirst($event->event_type ?? 'offline') }}</small></td>
    <td>@if($event->is_featured)<span class="status-amber-sm">★ Featured</span>@else<span class="text-115-muted">— No</span>@endif</td>
    <td><span style="display:inline-flex;align-items:center;gap:5px;padding:4px 11px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap;{{ $statusStyle }}">{{ $markChar }} {{ ucfirst($event->status) }}</span></td>
    <td class="fs13-tnum">{{ number_format($event->views ?? 0) }}</td>
    <td class="sa-actions-inline">
        <a href="/events/{{ $event->slug }}" target="_blank">View</a>
        <a href="/admin/events/{{ $event->id }}/edit">Edit</a>
        @if($event->status === 'pending')
            <form method="post" action="/admin/events/{{ $event->id }}/approve">@csrf<button>Approve</button></form>
            <form method="post" action="/admin/events/{{ $event->id }}/reject">@csrf<button class="danger">Reject</button></form>
        @endif
        <form method="post" action="/admin/events/{{ $event->id }}/feature">@csrf<button>{{ $event->is_featured ? 'Unfeature' : 'Feature' }}</button></form>
        <form method="post" action="/admin/events/{{ $event->id }}" onsubmit="return confirm('Delete this event?')">@csrf @method('DELETE')<button class="danger">Del</button></form>
    </td>
</tr>
@empty
<tr><td colspan="10" class="td-empty">No events found.</td></tr>
@endforelse
