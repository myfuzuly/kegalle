@extends('layouts.dashboard')

@section('title','Messages')
@section('eyebrow','Account')
@section('heading','Messages')
@section('subheading','Your conversations with buyers and sellers.')

@push('styles')
<style>
.chat-thread-list{display:flex;flex-direction:column;gap:2px}
.chat-thread-item{display:flex;align-items:center;gap:14px;padding:14px 16px;border-bottom:1px solid var(--kd-border,#eee);text-decoration:none;color:inherit;transition:background .15s}
.chat-thread-item:hover{background:var(--kd-hover,#f8f9fa)}
.chat-thread-avatar{width:42px;height:42px;border-radius:50%;background:var(--kd-primary,#2563eb);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;flex-shrink:0}
.chat-thread-body{flex:1;min-width:0}
.chat-thread-name{font-weight:600;font-size:14px;display:flex;align-items:center;gap:8px}
.chat-thread-listing{font-size:12px;color:var(--kd-muted,#888);margin-top:1px}
.chat-thread-preview{font-size:13px;color:var(--kd-muted,#666);margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.chat-thread-meta{text-align:right;flex-shrink:0}
.chat-thread-time{font-size:11px;color:var(--kd-muted,#999)}
.chat-unread-badge{background:var(--kd-primary,#2563eb);color:#fff;font-size:11px;font-weight:700;padding:2px 7px;border-radius:10px;margin-left:4px}
.chat-new-form{margin-bottom:20px}
.chat-new-form textarea{width:100%;padding:10px 12px;border:1px solid var(--kd-border,#ddd);border-radius:8px;font-size:14px;resize:vertical;min-height:80px;font-family:inherit}
.chat-new-form textarea:focus{outline:none;border-color:var(--kd-primary,#2563eb)}
</style>
@endpush

@section('content')

@if(session('error'))
<div class="kd-alert" style="background:#fee;color:#c00;padding:12px 16px;border-radius:8px;margin-bottom:16px">{{ session('error') }}</div>
@endif

@if(isset($newListing) && $newListing)
<section class="kd-card" style="margin-bottom:20px">
    <div class="kd-card-head"><h2>Start Conversation</h2></div>
    <div style="padding:16px">
        <p style="margin:0 0 12px;font-size:14px;color:#666">Send a message about <strong>{{ $newListing->title }}</strong></p>
        <form action="{{ route('dashboard.chat.store') }}" method="POST" class="chat-new-form">
            @csrf
            <input type="hidden" name="listing_id" value="{{ $newListing->id }}">
            <input type="hidden" name="seller_id" value="{{ $newSellerId }}">
            <textarea name="message" placeholder="Write your message..." required maxlength="2000">{{ old('message') }}</textarea>
            @error('message')<p style="color:#c00;font-size:13px;margin:4px 0 0">{{ $message }}</p>@enderror
            <div style="margin-top:10px;display:flex;gap:10px">
                <button type="submit" class="kd-btn kd-btn-primary">Send Message</button>
                <a href="{{ route('dashboard.chat') }}" class="kd-btn">Cancel</a>
            </div>
        </form>
    </div>
</section>
@endif

<section class="kd-card">
    <div class="kd-card-head"><h2>Inbox</h2></div>
    @if($threads->count())
        <div class="chat-thread-list">
            @foreach($threads as $thread)
                @php
                    $otherUser = $thread->buyer_id === Auth::id() ? $thread->seller : $thread->buyer;
                    $lastMsg = $thread->messages->first();
                    $initial = strtoupper(substr(optional($otherUser)->name ?? 'U', 0, 1));
                @endphp
                <a href="{{ route('dashboard.chat.show', $thread) }}" class="chat-thread-item">
                    <div class="chat-thread-avatar">{{ $initial }}</div>
                    <div class="chat-thread-body">
                        <div class="chat-thread-name">
                            {{ optional($otherUser)->name ?? 'User' }}
                            @if($thread->unread_count > 0)
                                <span class="chat-unread-badge">{{ $thread->unread_count }}</span>
                            @endif
                        </div>
                        <div class="chat-thread-listing">{{ Str::limit(optional($thread->listing)->title, 50) }}</div>
                        @if($lastMsg)
                            <div class="chat-thread-preview">{{ Str::limit($lastMsg->message, 80) }}</div>
                        @endif
                    </div>
                    <div class="chat-thread-meta">
                        @if($lastMsg)
                            <div class="chat-thread-time">{{ $lastMsg->created_at->diffForHumans(null, true, true) }}</div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="kd-empty">
            <strong>No messages yet</strong>
            <p>When you contact a seller or receive an inquiry, it will appear here.</p>
            <a href="/listings" class="kd-btn kd-btn-primary">Browse Listings</a>
        </div>
    @endif
</section>
@endsection
