@extends('layouts.dashboard')
@section('banner_sub', 'Buyers interested in your listings — accept, reject, or start a chat.')
@section('title','Received Offers')
@section('heading','Received Offers')
@section('subheading','Buyers interested in your listings — accept, reject, or start a chat.')

@section('content')

@if(session('success'))
<div class="or-success">✓ {{ session('success') }}</div>
@endif

<div class="or-info">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="flex-shrink-0"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
  <div>
    <strong>How offers work</strong>
    Accept to confirm the deal and open chat. Reject to decline — the buyer is notified either way.
  </div>
</div>

@if($offers->isEmpty())
<div class="or-empty">
  <div class="emoji-48b">📬</div>
  <strong class="heading-sm">No offers yet</strong>
  <p class="desc-text">When buyers make offers on your listings, they'll appear here for you to review.</p>
</div>
@else
@foreach($offers as $offer)
@php
  $st = $offer->status ?? 'pending';
  $cardCls  = match($st) { 'accepted' => 'or-card-accepted', 'rejected' => 'or-card-rejected', default => 'or-card-pending' };
  $pillCls  = match($st) { 'accepted' => 'or-pill-accepted', 'rejected' => 'or-pill-rejected', default => 'or-pill-pending' };
  $dotColor = match($st) { 'accepted' => '#22c55e', 'rejected' => '#f43f5e', default => '#f59e0b' };
@endphp
<div class="or-card {{ $cardCls }}">
  <div class="or-head">
    <div>
      <div class="or-listing-label">Listing</div>
      <a href="/listings/{{ $offer->listing->slug ?? $offer->listing_id }}" target="_blank" class="or-listing-link">
        {{ $offer->listing->title ?? 'Listing #'.$offer->listing_id }}
      </a>
    </div>
    <span class="or-pill {{ $pillCls }}">
      <span class="or-pill-dot" style="background:{{ $dotColor }}"></span>
      {{ ucfirst($st) }}
    </span>
  </div>

  <div class="or-meta">
    <div class="or-meta-cell">
      <div class="or-meta-label">Buyer</div>
      <div class="or-meta-val">{{ $offer->buyer->name ?? 'Unknown' }}</div>
    </div>
    <div class="or-meta-cell">
      <div class="or-meta-label">Offered Price</div>
      <div class="or-meta-price">LKR {{ number_format($offer->offered_price) }}</div>
      @if($offer->offered_price != $offer->listing?->price)
      <div class="or-meta-sub">asking LKR {{ number_format($offer->listing?->price ?? 0) }}</div>
      @endif
    </div>
    <div class="or-meta-cell">
      <div class="or-meta-label">Payment</div>
      <div class="or-meta-val fw6-fs135">{{ $offer->payment_icon ?? '' }} {{ $offer->payment_label ?? ucfirst($offer->payment_method ?? '—') }}</div>
    </div>
    <div class="or-meta-cell">
      <div class="or-meta-label">Received</div>
      <div class="or-meta-val text-13-mid">{{ $offer->created_at->diffForHumans() }}</div>
    </div>
  </div>

  @if($offer->message)
  <div class="or-msg">
    <div class="or-msg-label">Message from buyer</div>
    <p class="or-msg-text">{{ $offer->message }}</p>
  </div>
  @endif

  @if($st === 'pending')
  <div class="or-actions">
    <form method="POST" action="/offers/{{ $offer->id }}" class="d-contents">
      @csrf @method('PATCH')
      <input type="hidden" name="status" value="accepted">
      <div class="flex1-mw220">
        <textarea name="seller_note" class="or-textarea" placeholder="Optional note to buyer (shown when you respond)…" rows="2"></textarea>
        <button type="submit" class="or-btn or-btn-accept">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          Accept Offer
        </button>
      </div>
    </form>
    <div class="flex-col-g8-pt50">
      <form class="csp5-311" method="POST" action="/offers/{{ $offer->id }}">
        @csrf @method('PATCH')
        <input type="hidden" name="status" value="rejected">
        <button type="submit" class="or-btn or-btn-reject" data-confirm="Reject this offer?">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          Reject
        </button>
      </form>
      <a href="/dashboard/chat" class="or-btn or-btn-chat">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        Chat
      </a>
    </div>
  </div>
  @else
  <div class="or-actions-done">
    @if($offer->seller_note)
    <div class="flex1-mw200">
      <div class="or-msg-label">Your note to buyer</div>
      <p class="or-msg-text">{{ $offer->seller_note }}</p>
    </div>
    @endif
    <a href="/dashboard/chat" class="or-btn or-btn-chat">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      Chat with Buyer
    </a>
  </div>
  @endif
</div>
@endforeach

@if(method_exists($offers,'links') && $offers->hasPages())
<div class="mt-12">{{ $offers->links('vendor.pagination.dashboard') }}</div>
@endif
@endif

@endsection
