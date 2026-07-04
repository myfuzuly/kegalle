@extends('layouts.admin')
@section('title','Chats')
@section('page','Chats')
@section('heading','Chats')
@section('subheading','View and moderate buyer-seller conversations')
@section('content')
<section class="sa-card"><div class="sa-card-head"><h2>Conversations from Database</h2><span>{{ $threads->total() }} threads</span></div>
<div class="sa-table-wrap"><table class="sa-table sa-table-chats"><thead><tr><th>Buyer</th><th>Seller</th><th>About</th><th>Status</th><th>Started</th><th>Actions</th></tr></thead><tbody>
@forelse($threads as $thread)<tr>
    <td>{{ $thread->buyer->name ?? 'Unknown' }}</td>
    <td>{{ $thread->seller->name ?? 'Unknown' }}</td>
    <td>{{ $thread->listing->title ?? '—' }}</td>
    <td><span class="sa-status {{ $thread->status === 'closed' ? 'suspended' : 'active' }}">{{ ucfirst($thread->status ?? 'open') }}</span></td>
    <td>{{ $thread->created_at?->format('Y-m-d') }}</td>
    <td class="sa-actions-inline">
        <a href="/admin/chats/{{ $thread->id }}" title="View">View</a>
        <form method="post" action="/admin/chats/{{ $thread->id }}/close">@csrf<button title="Close">Close</button></form>
        <form method="post" action="/admin/chats/{{ $thread->id }}" onsubmit="return confirm('Delete this conversation?')">@csrf @method('DELETE')<button class="danger" title="Delete">Delete</button></form>
    </td>
</tr>@empty<tr><td colspan="6">No conversations found.</td></tr>@endforelse
</tbody></table></div>{{ $threads->links() }}</section>
@endsection
