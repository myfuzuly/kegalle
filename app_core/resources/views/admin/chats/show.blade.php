@extends('layouts.admin')

@section('title','View Conversation')
@section('page','Chats')
@section('heading','Conversation')
@section('subheading', ($thread->buyer->name ?? 'Buyer').' ↔ '.($thread->seller->name ?? 'Seller').' — '.($thread->listing->title ?? 'No listing'))

@section('actions')
<a class="ka-btn ka-btn-light" href="/admin/chats">Back</a>
@endsection

@section('content')
<section class="sa-card">
    <div style="display:flex;flex-direction:column;gap:14px;max-width:720px">
        @forelse($thread->messages as $message)
            <div style="display:flex;flex-direction:column;align-items:{{ $message->sender_id === $thread->buyer_id ? 'flex-start' : 'flex-end' }}">
                <div style="background:{{ $message->sender_id === $thread->buyer_id ? '#F1F5F9' : '#E8F5E9' }};border-radius:14px;padding:10px 14px;max-width:80%">
                    <div style="font-size:12px;font-weight:700;margin-bottom:4px;color:#667085">{{ $message->sender->name ?? 'Unknown' }}</div>
                    <div style="font-size:14px;color:#101828">{{ $message->message }}</div>
                </div>
                <small style="color:#98a2b3;margin-top:4px">{{ $message->created_at?->format('M d, Y · h:i A') }}</small>
            </div>
        @empty
            <p style="color:#667085">No messages in this conversation yet.</p>
        @endforelse
    </div>
</section>
@endsection
