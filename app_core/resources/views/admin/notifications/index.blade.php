@extends('layouts.admin')
@section('title','Notification Center')
@section('page','Notification Center')
@section('heading','Notification Center')
@section('subheading','New users, listings, classifieds and stores added to the system')
@section('actions')
<form method="post" action="/admin/notifications/read-all">@csrf<button class="ka-btn ka-btn-light">Mark all as read</button></form>
@endsection
@section('content')
<section class="sa-card">
<div class="sa-head">
    <div><p>Super Admin</p><h1>Notification Center @if($unreadCount)<span class="sa-status pending" style="margin-left:8px">{{ $unreadCount }} unread</span>@endif</h1></div>
    <form class="sa-search" method="get">
        <select name="type">
            <option value="">All Types</option>
            @foreach($types as $type)
                <option value="{{ $type }}" @selected(request('type') === $type)>{{ str_replace('_',' ', ucfirst($type)) }}</option>
            @endforeach
        </select>
        <select name="status">
            <option value="">All Status</option>
            <option value="unread" @selected(request('status') === 'unread')>Unread</option>
            <option value="read" @selected(request('status') === 'read')>Read</option>
        </select>
        <button>Filter</button>
    </form>
</div>
<div class="sa-table-wrap"><table class="sa-table sa-table-notifications"><thead><tr><th>Type</th><th>Title</th><th>Details</th><th>When</th><th>Status</th><th>Action</th></tr></thead><tbody>
@forelse($notifications as $n)
<tr style="{{ $n->is_read ? '' : 'background:var(--ka-green-soft,#E8F5E9)' }}">
    <td>{{ str_replace('_',' ', ucfirst($n->type)) }}</td>
    <td><b>{{ $n->title }}</b></td>
    <td><small>{{ $n->message }}</small></td>
    <td><small>{{ $n->created_at?->diffForHumans() }}</small></td>
    <td><span class="sa-status {{ $n->is_read ? 'active' : 'pending' }}">{{ $n->is_read ? 'Read' : 'Unread' }}</span></td>
    <td class="sa-actions-inline">
        @if($n->link)
            <form method="post" action="/admin/notifications/{{ $n->id }}/read"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button title="View">View</button></form>
        @elseif(!$n->is_read)
            <form method="post" action="/admin/notifications/{{ $n->id }}/read"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button title="Mark read">Mark read</button></form>
        @endif
    </td>
</tr>
@empty
<tr><td colspan="6">No notifications yet.</td></tr>
@endforelse
</tbody></table></div>{{ $notifications->links() }}</section>
@endsection
