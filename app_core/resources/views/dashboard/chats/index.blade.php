@extends('layouts.dashboard')
@section('banner_sub', 'Chat with buyers and sellers directly.')
@section('title','Messages')
@section('eyebrow','Account')
@section('heading','Messages')
@section('subheading','Your conversations with buyers and sellers.')

@section('content')

@if(session('error'))
<div class="ch-error">{{ session('error') }}</div>
@endif

@if(isset($newListing) && $newListing)
<div class="ch-new-card">
  <div class="ch-card-head">
    <div class="ch-card-head-icon">💬</div>
    <h2>Start Conversation</h2>
  </div>
  <div class="ch-new-form">
    <p>Send a message about <strong>{{ $newListing->title }}</strong></p>
    <form action="{{ route('dashboard.chat.store') }}" method="POST">
      @csrf
      <input type="hidden" name="listing_id" value="{{ $newListing->id }}">
      <input type="hidden" name="seller_id" value="{{ $newSellerId }}">
      <textarea name="message" class="ch-new-textarea" placeholder="Write your message…" required maxlength="2000">{{ old('message') }}</textarea>
      @error('message')<p class="rose800-fs125">{{ $message }}</p>@enderror
      <div class="ch-new-footer">
        <button type="submit" class="ch-btn ch-btn-primary">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          Send Message
        </button>
        <a href="{{ route('dashboard.chat') }}" class="ch-btn ch-btn-light">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endif

<div class="ch-inbox-card">
  <div class="ch-card-head">
    <div class="ch-card-head-icon">📥</div>
    <h2>Inbox</h2>
    @if($threads->count() > 0)
    <span class="fs12-muted-mla">{{ $threads->count() }} conversation{{ $threads->count()===1?'':'s' }}</span>
    @endif
  </div>
  @if($threads->count() > 0)
  <div class="p10-18-bb">
    <input class="input-36h" type="text" id="chSearch" placeholder="Search by name or listing…"
>
  </div>
  @endif
  @if($threads->count())
    @foreach($threads as $thread)
    @php
      $otherUser = $thread->buyer_id === Auth::id() ? $thread->seller : $thread->buyer;
      $lastMsg   = $thread->messages->first();
      $initial   = strtoupper(substr(optional($otherUser)->name ?? 'U', 0, 1));
      $hasUnread = ($thread->unread_count ?? 0) > 0;
    @endphp
    <a href="{{ route('dashboard.chat.show', $thread) }}" class="ch-thread {{ $hasUnread ? 'ch-thread-unread' : '' }}" data-name="{{ strtolower(optional($otherUser)->name ?? '') }}" data-listing="{{ strtolower(optional($thread->listing)->title ?? '') }}">
      <div class="ch-avatar">{{ $initial }}</div>
      <div class="ch-body">
        <div class="ch-name-row">
          <span class="ch-name">{{ optional($otherUser)->name ?? 'User' }}</span>
          @if($hasUnread)
          <span class="ch-unread-badge">{{ $thread->unread_count }}</span>
          @endif
        </div>
        <div class="ch-listing">{{ \Illuminate\Support\Str::limit(optional($thread->listing)->title, 50) }}</div>
        @if($lastMsg)
        <div class="ch-preview">{{ \Illuminate\Support\Str::limit($lastMsg->message, 80) }}</div>
        @endif
      </div>
      <div class="ch-meta">
        @if($lastMsg)
        <div class="ch-time">{{ $lastMsg->created_at->diffForHumans(null, true, true) }}</div>
        @endif
        <div class="ch-arrow">›</div>
      </div>
    </a>
    @endforeach
  @else
    <div class="ch-empty">
      <span class="ch-empty-icon">💬</span>
      <strong>No messages yet</strong>
      <p>When you contact a seller or receive an inquiry, conversations will appear here.</p>
      <a href="/listings" class="ch-empty-btn">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        Browse Listings
      </a>
    </div>
  @endif
</div>

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
var chSrch=document.getElementById('chSearch');
if(chSrch) chSrch.addEventListener('input',function(){
  var q=this.value.toLowerCase().trim();
  document.querySelectorAll('.ch-thread').forEach(function(t){
    t.style.display=(!q||t.dataset.name.includes(q)||t.dataset.listing.includes(q))?'':'none';
  });
});
</script>
@endpush
@endsection
