@extends('layouts.dashboard')
@section('banner_sub', 'Track price offers you\'ve sent to sellers.')
@section('title','My Offers')
@section('heading','My Offers')
@section('subheading','Offers you\'ve sent to sellers.')

@section('actions')
<a href="/" class="kdl-tb-btn kdl-tb-btn-light">
  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
  Browse Listings
</a>
@endsection

@section('content')

@if(session('success'))
<div class="oi-success">✓ {{ session('success') }}</div>
@endif

{{-- Client-side filter bar --}}
<div class="flex-fw-g10-mb18">
  <div class="tab-group" id="oiPillBar">
    @foreach(['all'=>'All', 'pending'=>'Pending', 'accepted'=>'Accepted', 'rejected'=>'Rejected'] as $val => $label)
    <button type="button" class="oi-pill-filter lp-filter-tab {{ $val==='all'?'active':'' }} btn-bare" data-status="{{ $val }}">{{ $label }}</button>
    @endforeach
  </div>
  <input class="filter-input" type="text" id="oiSearch" placeholder="Search by listing title…"
>
</div>

@if($offers->isEmpty())
<div class="oi-empty">
  <div class="emoji-48b">🤝</div>
  <strong class="heading-sm">No offers sent yet</strong>
  <p class="desc-text-mb20">Browse listings and click <strong>Make Offer</strong> to negotiate directly with sellers.</p>
  <a class="btn-primary-green" href="/">Browse Listings</a>
</div>
@else
@foreach($offers as $offer)
@php
  $st = $offer->status ?? 'pending';
  $cardCls  = match($st) { 'accepted' => 'oi-card-accepted', 'rejected' => 'oi-card-rejected', default => 'oi-card-pending' };
  $pillCls  = match($st) { 'accepted' => 'oi-pill-accepted', 'rejected' => 'oi-pill-rejected', default => 'oi-pill-pending' };
  $dotColor = match($st) { 'accepted' => '#22c55e', 'rejected' => '#f43f5e', default => '#f59e0b' };
  $labels   = ['pending' => 'Pending', 'accepted' => 'Accepted', 'rejected' => 'Rejected'];
@endphp
<div class="oi-card {{ $cardCls }}" data-status="{{ $st }}" data-title="{{ strtolower($offer->listing->title ?? '') }}">
  <div class="oi-head">
    <div>
      <div class="oi-label">Listing</div>
      <a href="/listings/{{ $offer->listing->slug ?? $offer->listing_id }}" target="_blank" class="oi-listing-link">
        {{ $offer->listing->title ?? 'Listing #'.$offer->listing_id }}
      </a>
    </div>
    <span class="oi-pill {{ $pillCls }}">
      <span class="oi-pill-dot" style="background:{{ $dotColor }}"></span>
      {{ $labels[$st] ?? ucfirst($st) }}
    </span>
  </div>

  <div class="oi-meta">
    <div class="oi-meta-cell">
      <div class="oi-label">Your Offer</div>
      <div class="oi-meta-price">LKR {{ number_format($offer->offered_price) }}</div>
      @if($offer->offered_price != $offer->listing?->price)
      <div class="oi-meta-sub">asking LKR {{ number_format($offer->listing?->price ?? 0) }}</div>
      @endif
    </div>
    <div class="oi-meta-cell">
      <div class="oi-label">Payment</div>
      <div class="oi-meta-val" class="fs-13h">{{ $offer->payment_icon ?? '' }} {{ $offer->payment_label ?? ucfirst($offer->payment_method ?? '—') }}</div>
    </div>
    <div class="oi-meta-cell">
      <div class="oi-label">Sent</div>
      <div class="oi-meta-val text-13-mid">{{ $offer->created_at->diffForHumans() }}</div>
    </div>
  </div>

  @if($offer->message)
  <div class="oi-box bg-slate50">
    <div class="oi-label">Your message</div>
    <p class="fs135-dark-lh">{{ $offer->message }}</p>
  </div>
  @endif

  @if($offer->seller_note)
  <div class="oi-seller-box">
    <div class="oi-label text-green800">Seller's response</div>
    <p class="fs135-green-lh">{{ $offer->seller_note }}</p>
  </div>
  @endif

  @if($st === 'accepted')
  <div class="oi-next">
    <div class="fw7-green-mb8">🎉 Offer accepted — here's what to do next</div>
    <ol class="list-green">
      <li>Open chat with the seller to arrange meeting details.</li>
      <li>Agree on a safe, public meeting place.</li>
      <li>Inspect the item before paying — never pay in advance.</li>
      <li>Pay via <strong>{{ $offer->payment_label ?? ucfirst($offer->payment_method ?? 'cash') }}</strong> as agreed.</li>
      <li>Leave a review after the deal — helps future buyers!</li>
    </ol>
  </div>
  @elseif($st === 'rejected')
  <div class="oi-rejected">
    ❌ This offer was not accepted.
    <a class="rose800-fw7-ml6" href="/listings">Browse similar listings →</a>
  </div>
  @endif

  <div class="oi-footer">
    @if($st === 'accepted')
    <a href="/dashboard/chat" class="oi-btn oi-btn-primary">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      Chat with Seller
    </a>
    @else
    <a href="/dashboard/chat" class="oi-btn oi-btn-light">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      Open Chat
    </a>
    @endif
    <a href="/listings/{{ $offer->listing->slug ?? $offer->listing_id }}" class="oi-btn oi-btn-light" target="_blank">View Listing</a>
  </div>
</div>
@endforeach

@if(method_exists($offers,'links') && $offers->hasPages())
<div class="mt-12">{{ $offers->links('vendor.pagination.dashboard') }}</div>
@endif
@endif

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
  var activeStatus='all';
  function applyFilters(){
    var q=document.getElementById('oiSearch').value.toLowerCase().trim();
    document.querySelectorAll('.oi-card').forEach(function(c){
      var matchStatus = activeStatus==='all' || c.dataset.status===activeStatus;
      var matchQ      = !q || c.dataset.title.includes(q);
      c.style.display = (matchStatus && matchQ) ? '' : 'none';
    });
  }
  document.querySelectorAll('.oi-pill-filter').forEach(function(btn){
    btn.addEventListener('click',function(){
      activeStatus=btn.dataset.status;
      document.querySelectorAll('.oi-pill-filter').forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
      applyFilters();
    });
  });
  var si=document.getElementById('oiSearch');
  if(si) si.addEventListener('input', applyFilters);
})();
</script>
@endpush

@endsection
