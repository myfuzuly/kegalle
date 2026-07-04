@extends('layouts.admin')
@section('title','Event Management')
@section('page','Events')
@section('heading','Event Management')
@section('subheading','Create, approve, feature and manage all events in the marketplace')
@section('actions')<a class="ka-btn ka-btn-primary" href="/admin/events/create">+ Create Event</a>@endsection
@section('content')
<section class="sa-card">
<div class="sa-head"><div><p>{{ $pendingCount }} pending approval</p><h1>📅 Event Management</h1></div>
<form class="sa-search" method="get"><input name="q" value="{{ request('q') }}" placeholder="Search events"><select name="status"><option value="">All Status</option><option value="pending" @selected(request('status')==='pending')>Pending</option><option value="published" @selected(request('status')==='published')>Published</option><option value="rejected" @selected(request('status')==='rejected')>Rejected</option><option value="draft" @selected(request('status')==='draft')>Draft</option><option value="cancelled" @selected(request('status')==='cancelled')>Cancelled</option></select><button>Filter</button></form></div>

@if(session('success'))<div style="background:#E8F5E9;color:#1B5E20;padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:14px">{{ session('success') }}</div>@endif

<div class="sa-table-wrap"><table class="sa-table"><thead><tr><th>Title</th><th>Date</th><th>Location</th><th>Organizer</th><th>Price</th><th>Type</th><th>Featured</th><th>Status</th><th>Views</th><th>Actions</th></tr></thead><tbody>
@forelse($events as $event)
@php
    $statusYes = $event->status === 'published';
    $statusPending = $event->status === 'pending';
    $markClass = $statusYes ? 'is-yes' : ($statusPending ? 'is-pending' : 'is-no');
    $markChar = $statusYes ? '✓' : ($statusPending ? '…' : '✕');
    $isPast = $event->event_date && $event->event_date->isPast();
@endphp
<tr>
    <td><b>{{ \Illuminate\Support\Str::limit($event->title, 30) }}</b><br><small style="color:#888">{{ $event->slug }}</small></td>
    <td><small>{{ $event->event_date ? $event->event_date->format('d M Y') : '—' }}</small>
        @if($isPast)<br><span style="color:#D32F2F;font-size:10px">Past</span>@else<br><span style="color:#1B5E20;font-size:10px">● Upcoming</span>@endif</td>
    <td><small>{{ $event->venue ?? '—' }}</small><br><small style="color:#888">{{ $event->location ?? '' }}</small></td>
    <td><small>{{ $event->organizer_name ?? ($event->user->name ?? '—') }}</small></td>
    <td>@if($event->is_free)<span style="color:#1B5E20;font-weight:700">Free</span>@else<b>LKR {{ number_format($event->price ?? 0) }}</b>@endif</td>
    <td><small>{{ ucfirst($event->event_type ?? 'offline') }}</small></td>
    <td>@if($event->is_featured)<span style="background:#1B5E20;color:#fff;font-size:10px;padding:1px 6px;border-radius:4px">★ Featured</span>@else<small style="color:#888">No</small>@endif</td>
    <td><span class="sa-status-mark {{ $markClass }}">{{ $markChar }}</span></td>
    <td><small>{{ number_format($event->views ?? 0) }}</small></td>
    <td class="sa-actions-inline">
        <a href="/events/{{ $event->slug }}" target="_blank">View</a>
        <a href="/admin/events/{{ $event->id }}/edit">Edit</a>
        @if($event->status === 'pending')
            <form method="post" action="/admin/events/{{ $event->id }}/approve">@csrf<button title="Approve">Approve</button></form>
            <form method="post" action="/admin/events/{{ $event->id }}/reject">@csrf<button class="danger" title="Reject">Reject</button></form>
        @endif
        <form method="post" action="/admin/events/{{ $event->id }}/feature">@csrf<button title="Toggle Featured">{{ $event->is_featured ? 'Unfeature' : 'Feature' }}</button></form>
        <form method="post" action="/admin/events/{{ $event->id }}" style="display:inline">@csrf @method('DELETE')<button class="danger" title="Delete" onclick="return confirm('Delete this event?')">Del</button></form>
    </td>
</tr>
@empty <tr><td colspan="10">No events found.</td></tr>@endforelse
</tbody></table></div>{{ $events->links() }}</section>
@endsection
