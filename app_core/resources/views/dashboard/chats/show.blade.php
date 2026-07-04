@extends('layouts.dashboard')

@section('title','Chat with ' . ($otherUser->name ?? 'User'))
@section('eyebrow','Messages')
@section('heading', $otherUser->name ?? 'User')
@section('subheading')
    Re: <a href="/listings/{{ $thread->listing->slug ?? $thread->listing_id }}" style="color:var(--kd-primary,#2563eb)">{{ optional($thread->listing)->title ?? 'Listing' }}</a>
@endsection

@push('styles')
<style>
.chat-messages{display:flex;flex-direction:column;gap:8px;padding:16px;max-height:500px;overflow-y:auto}
.chat-bubble-wrap{display:flex;flex-direction:column;max-width:75%}
.chat-bubble-wrap.is-mine{align-self:flex-end;align-items:flex-end}
.chat-bubble-wrap.is-other{align-self:flex-start;align-items:flex-start}
.chat-bubble{padding:10px 14px;border-radius:14px;font-size:14px;line-height:1.45;word-break:break-word}
.chat-bubble-wrap.is-mine .chat-bubble{background:var(--kd-primary,#2563eb);color:#fff;border-bottom-right-radius:4px}
.chat-bubble-wrap.is-other .chat-bubble{background:var(--kd-surface,#f1f3f5);color:#222;border-bottom-left-radius:4px}
.chat-bubble-meta{font-size:11px;color:var(--kd-muted,#999);margin-top:3px;display:flex;gap:6px;align-items:center}
.chat-reply-form{padding:16px;border-top:1px solid var(--kd-border,#eee);display:flex;gap:10px;align-items:flex-end}
.chat-reply-form textarea{flex:1;padding:10px 12px;border:1px solid var(--kd-border,#ddd);border-radius:10px;font-size:14px;resize:none;min-height:44px;max-height:120px;font-family:inherit}
.chat-reply-form textarea:focus{outline:none;border-color:var(--kd-primary,#2563eb)}
.chat-back{display:inline-flex;align-items:center;gap:5px;font-size:13px;color:var(--kd-muted,#666);text-decoration:none;margin-bottom:12px}
.chat-back:hover{color:var(--kd-primary,#2563eb)}
</style>
@endpush

@section('content')
<a href="{{ route('dashboard.chat') }}" class="chat-back">&larr; Back to Inbox</a>

<section class="kd-card">
    <div class="chat-messages" id="chatMessages">
        @forelse($messages as $msg)
            <div class="chat-bubble-wrap {{ $msg->sender_id === Auth::id() ? 'is-mine' : 'is-other' }}">
                <div class="chat-bubble">{!! nl2br(e($msg->message)) !!}</div>
                <div class="chat-bubble-meta">
                    <span>{{ $msg->created_at->diffForHumans() }}</span>
                    @if($msg->sender_id === Auth::id() && $msg->read_at)
                        <span title="Read {{ $msg->read_at->diffForHumans() }}">&#10003;&#10003;</span>
                    @endif
                </div>
            </div>
        @empty
            <p style="text-align:center;color:#999;padding:20px 0">No messages yet. Start the conversation below.</p>
        @endforelse
    </div>

    <form action="{{ route('dashboard.chat.reply', $thread) }}" method="POST" class="chat-reply-form">
        @csrf
        <textarea name="message" placeholder="Type a message..." required maxlength="2000" rows="1">{{ old('message') }}</textarea>
        <button type="submit" class="kd-btn kd-btn-primary">Send</button>
    </form>
    @error('message')<p style="color:#c00;font-size:13px;padding:0 16px 12px">{{ $message }}</p>@enderror
</section>

<script>
document.addEventListener('DOMContentLoaded',function(){
    var el=document.getElementById('chatMessages');
    if(el) el.scrollTop=el.scrollHeight;
});
</script>
@endsection
