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
    <div class="flex-col-g14-mw720">
        @forelse($thread->messages as $message)
            <div class="chat-msg-row {{ $message->sender_id === $thread->buyer_id ? 'chat-msg-row--buyer' : 'chat-msg-row--seller' }}">
                <div class="chat-bubble {{ $message->sender_id === $thread->buyer_id ? 'chat-bubble-received' : 'chat-bubble-sent' }}">
                    <div class="fs12-fw7-gray">{{ $message->sender->name ?? 'Unknown' }}</div>
                    <div class="fs14-dark">{{ $message->message }}</div>
                </div>
                <small class="muted2-mt4">{{ $message->created_at?->format('M d, Y · h:i A') }}</small>
            </div>
        @empty
            <p class="text-gray">No messages in this conversation yet.</p>
        @endforelse
    </div>
</section>
@endsection
