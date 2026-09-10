@extends('layouts.dashboard')
@section('banner_sub', 'Stay updated on your listings, offers, and activity.')
@section('title','Notifications')
@section('eyebrow','Account')
@section('heading','Notifications')
@section('subheading','Updates on your offers, listings, and activity.')

@section('actions')
@if($notifications->isNotEmpty())
<form class="csp5-311" method="POST" action="/dashboard/notifications/read-all">
  @csrf
  <button type="submit" class="kdl-tb-btn kdl-tb-btn-light">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    Mark all read
  </button>
</form>
@endif
@endsection

@section('content')

@php
  $unreadCount = $notifications->where('read_at', null)->count();
  $totalCount  = $notifications->total();
@endphp

<div class="ntf-topbar">
  <div class="ntf-stat">
    <div>
      <div class="ntf-stat-n" id="ntfTotal">{{ $totalCount }}</div>
      <div class="ntf-stat-l">Total</div>
    </div>
  </div>
  <div class="ntf-stat border-green200-bg">
    <div class="dot-8-green"></div>
    <div>
      <div class="ntf-stat-n" id="ntfUnread" class="text-green">{{ $unreadCount }}</div>
      <div class="ntf-stat-l text-green700">Unread</div>
    </div>
  </div>
</div>

{{-- Filter pills --}}
<div class="flex-fw-g8-mb16">
  @foreach([''=>'All', 'unread'=>'Unread', 'read'=>'Read'] as $val => $label)
  <button type="button" class="ntf-pill-btn lp-filter-tab tab-btn" data-value="{{ $val }}">
      {{ $label }}
  </button>
  @endforeach
</div>

<div class="ntf-card">
  <div id="ntfRows">@include('dashboard.notifications._rows')</div>
  <div id="ntfPagination">
    @if($notifications->hasPages())
      {{ $notifications->links('vendor.pagination.dashboard') }}
    @endif
  </div>
</div>

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
  var activeStatus = '';

  function updatePills(){
    document.querySelectorAll('.ntf-pill-btn').forEach(function(btn){
      var a = btn.dataset.value===activeStatus;
      btn.style.background = a ? '#1b5e20' : '#f1f5f9';
      btn.style.color      = a ? '#fff'    : '#64748b';
    });
  }

  function doFetch(page){
    var p = new URLSearchParams({status:activeStatus});
    if(page>1) p.set('page',page);
    fetch('/dashboard/notifications?'+p, {headers:{'X-Requested-With':'XMLHttpRequest'}})
      .then(function(r){return r.json();})
      .then(function(d){
        document.getElementById('ntfRows').innerHTML = d.rows;
        document.getElementById('ntfPagination').innerHTML = d.pagination;
        var t=document.getElementById('ntfTotal');
        if(t) t.textContent=d.total;
        bindPages();
      });
  }

  function bindPages(){
    document.getElementById('ntfPagination').querySelectorAll('a[href]').forEach(function(a){
      a.addEventListener('click',function(e){
        e.preventDefault();
        doFetch(new URL(a.href).searchParams.get('page')||1);
        window.scrollTo({top:0,behavior:'smooth'});
      });
    });
  }

  document.querySelectorAll('.ntf-pill-btn').forEach(function(btn){
    btn.addEventListener('click',function(){
      activeStatus=btn.dataset.value; updatePills(); doFetch(1);
    });
  });

  updatePills();
  bindPages();
})();
</script>
@endpush

@endsection
