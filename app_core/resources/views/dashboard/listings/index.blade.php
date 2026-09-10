@extends('layouts.dashboard')
@section('banner_sub', 'View and manage all your classified ads.')
@section('title','My Listings')
@section('eyebrow','Listings')
@section('heading','My Listings')

@section('actions')
<a href="/dashboard/listings/create" class="kdl-tb-btn kdl-tb-btn-primary">
  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
  New Listing
</a>
@endsection

@section('content')

@php
  $user = auth()->user();
  $items = $listings;
  $total = $items->total() ?? $items->count();
  $statusFilter = request('status','');

  // compute counts — guard if paginator doesn't have all items loaded
  try {
      $allItems = \App\Models\Listing::where('user_id',$user->id);
      $countAll      = $allItems->count();
      $countApproved = (clone $allItems)->where('status','approved')->count();
      $countPending  = (clone $allItems)->where('status','pending')->count();
      $countRejected = (clone $allItems)->where('status','rejected')->count();
      $countSold     = (clone $allItems)->where('status','sold')->count();
  } catch(\Throwable) {
      $countAll = $countApproved = $countPending = $countRejected = $countSold = 0;
  }
@endphp

{{-- Submission success --}}
@if(session('listing_submitted'))
<div class="lp-submitted">
  <div class="fs-42">🎉</div>
  <h2>"{{ session('listing_submitted') }}" has been submitted!</h2>
  <p>Our team will review your ad within a few hours. Once approved it goes live and buyers can contact you directly.</p>
  <div class="lp-submitted-btns">
    <a href="/dashboard/listings/create" class="lp-submitted-btn-p">+ Post Another Ad</a>
    <a href="/listings" class="lp-submitted-btn-s">Browse Marketplace</a>
  </div>
</div>
@endif

{{-- Stats bar --}}
<div class="lp-stats">
  <div class="lp-stat">
    <div class="lp-stat-dot lp-stat-dot-all"></div>
    <div><div class="lp-stat-n">{{ $countAll }}</div><div class="lp-stat-l">Total Ads</div></div>
  </div>
  <div class="lp-stat">
    <div class="lp-stat-dot lp-stat-dot-live"></div>
    <div><div class="lp-stat-n">{{ $countApproved }}</div><div class="lp-stat-l">Live</div></div>
  </div>
  <div class="lp-stat">
    <div class="lp-stat-dot lp-stat-dot-pending"></div>
    <div><div class="lp-stat-n">{{ $countPending }}</div><div class="lp-stat-l">Pending</div></div>
  </div>
  <div class="lp-stat">
    <div class="lp-stat-dot lp-stat-dot-rejected"></div>
    <div><div class="lp-stat-n">{{ $countRejected }}</div><div class="lp-stat-l">Rejected</div></div>
  </div>
  <div class="lp-stat">
    <div class="lp-stat-dot lp-stat-dot-sold"></div>
    <div><div class="lp-stat-n">{{ $countSold }}</div><div class="lp-stat-l">Sold</div></div>
  </div>
</div>

{{-- Filter toolbar --}}
<div class="lp-toolbar">
  <div class="lp-filter-tabs" id="lpPillBar">
    @foreach([''=>'All', 'approved'=>'Live', 'pending'=>'Pending', 'rejected'=>'Rejected', 'sold'=>'Sold'] as $val => $label)
    <button type="button" class="lp-filter-tab lp-pill-btn btn-bare" data-value="{{ $val }}">{{ $label }}</button>
    @endforeach
  </div>
  <input type="text" class="lp-search" placeholder="Search listings…" id="lpQ">
</div>

{{-- Table --}}
<div class="lp-card">
  <div class="lp-card-head">
    <h2 id="lpHeading">All Listings</h2>
    <span id="lpBadge">{{ $listings->total() }} listing{{ $listings->total()===1?'':'s' }}</span>
  </div>

  <div id="lpRows">
    @include('dashboard.listings._rows')
  </div>
  <div id="lpPagination" class="lp-pages">
    @if(method_exists($listings,'links') && $listings->lastPage() > 1)
      {{ $listings->links('vendor.pagination.dashboard') }}
    @endif
  </div>
</div>

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
  var timer,
      activeStatus = '',
      headings = {'':'All','approved':'Live','pending':'Pending','rejected':'Rejected','sold':'Sold'};

  function updatePills(){
    document.querySelectorAll('.lp-pill-btn').forEach(function(btn){
      var active = btn.dataset.value === activeStatus;
      btn.classList.toggle('active', active);
    });
    var h = document.getElementById('lpHeading');
    if(h) h.textContent = (headings[activeStatus]||'All')+' Listings';
  }

  function doFetch(page){
    var q = document.getElementById('lpQ').value.trim();
    var p = new URLSearchParams({q:q, status:activeStatus});
    if(page>1) p.set('page',page);
    fetch('/dashboard/listings?'+p, {headers:{'X-Requested-With':'XMLHttpRequest'}})
      .then(function(r){return r.json();})
      .then(function(d){
        document.getElementById('lpRows').innerHTML = d.rows;
        document.getElementById('lpPagination').innerHTML = d.pagination;
        var b = document.getElementById('lpBadge');
        if(b) b.textContent = d.total+' listing'+(d.total!==1?'s':'');
        bindPages();
      });
  }

  function bindPages(){
    document.getElementById('lpPagination').querySelectorAll('a[href]').forEach(function(a){
      a.addEventListener('click',function(e){
        e.preventDefault();
        doFetch(new URL(a.href).searchParams.get('page')||1);
        window.scrollTo({top:0,behavior:'smooth'});
      });
    });
  }

  document.querySelectorAll('.lp-pill-btn').forEach(function(btn){
    btn.addEventListener('click',function(){
      activeStatus = btn.dataset.value;
      updatePills(); doFetch(1);
    });
  });

  document.getElementById('lpQ').addEventListener('input', function(){
    clearTimeout(timer); timer=setTimeout(function(){doFetch(1);},320);
  });

  updatePills();
  bindPages();

  // ── Inline stock editor ────────────────────────────────────────
  document.getElementById('lpRows').addEventListener('click', function(e){
    var badge = e.target.closest('.lp-stock-badge');
    if(!badge) return;
    var cell = badge.closest('.lp-stock-cell');
    var input = cell.querySelector('.lp-stock-input');
    badge.style.display = 'none';
    input.value = badge.dataset.stock;
    input.style.display = 'block';
    input.focus(); input.select();
  });

  function saveStock(input){
    var id = input.dataset.id, csrf = input.dataset.csrf;
    var val = input.value === '' ? null : parseInt(input.value, 10);
    var cell = input.closest('.lp-stock-cell');
    var badge = cell.querySelector('.lp-stock-badge');
    input.style.display = 'none';
    badge.style.display = '';

    fetch('/dashboard/listings/'+id+'/stock', {
      method: 'PATCH',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
      body: JSON.stringify({stock: val})
    }).then(function(r){ return r.json(); }).then(function(d){
      var s = d.stock;
      badge.dataset.stock = s === null ? '' : s;
      badge.textContent = s === null ? '—' : (s === 0 ? 'Out' : s);
      badge.className = 'lp-stock-badge ' + (s === null ? 'lp-stock-na' : s === 0 ? 'lp-stock-out' : s <= 5 ? 'lp-stock-low' : 'lp-stock-ok');
    }).catch(function(){ badge.textContent = '!'; });
  }

  document.getElementById('lpRows').addEventListener('keydown', function(e){
    if(!e.target.classList.contains('lp-stock-input')) return;
    if(e.key === 'Enter'){ e.preventDefault(); saveStock(e.target); }
    if(e.key === 'Escape'){ e.target.style.display='none'; e.target.closest('.lp-stock-cell').querySelector('.lp-stock-badge').style.display=''; }
  });
  document.getElementById('lpRows').addEventListener('blur', function(e){
    if(e.target.classList.contains('lp-stock-input')) saveStock(e.target);
  }, true);
})();
</script>
@endpush
@endsection
