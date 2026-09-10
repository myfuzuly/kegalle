@extends('layouts.dashboard')
@section('banner_sub', 'Submit listings as deals for discounted promotions.')
@section('title','My Deals')
@section('eyebrow','Deals')
@section('heading','My Deals')

@section('actions')
<a href="/dashboard/deals/create" class="kdl-tb-btn kdl-tb-btn-primary">
  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
  Submit Deal
</a>
@endsection

@section('content')

@php
  try {
    $countAll      = $deals->total();
    $countApproved = \App\Models\Deal::where('user_id', auth()->id())->where('status','approved')->count();
    $countPending  = \App\Models\Deal::where('user_id', auth()->id())->where('status','pending')->count();
    $countRejected = \App\Models\Deal::where('user_id', auth()->id())->where('status','rejected')->count();
  } catch(\Throwable) {
    $countAll = $countApproved = $countPending = $countRejected = 0;
  }
@endphp

{{-- Info banner --}}
<div class="di-info-banner">
  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
  <div>
    <strong>How deals work</strong>
    Submit a listing as a deal with a discounted price. Admin reviews and approves before it appears on the public Deals page.
  </div>
</div>

{{-- Stats --}}
<div class="di-stats">
  <div class="di-stat"><div class="di-stat-dot bg-slate400"></div><div><div class="di-stat-n">{{ $countAll }}</div><div class="di-stat-l">Total</div></div></div>
  <div class="di-stat"><div class="di-stat-dot bg-green400"></div><div><div class="di-stat-n">{{ $countApproved }}</div><div class="di-stat-l">Live</div></div></div>
  <div class="di-stat"><div class="di-stat-dot bg-amber400"></div><div><div class="di-stat-n">{{ $countPending }}</div><div class="di-stat-l">Pending</div></div></div>
  <div class="di-stat"><div class="di-stat-dot bg-rose400"></div><div><div class="di-stat-n">{{ $countRejected }}</div><div class="di-stat-l">Rejected</div></div></div>
</div>

{{-- Filter toolbar --}}
<div class="flex-fw-g10-mb18b">
  <div class="tab-group">
    @foreach([''=>'All', 'approved'=>'Live', 'pending'=>'Pending', 'rejected'=>'Rejected'] as $val => $label)
    <button type="button" class="di-pill-btn lp-filter-tab btn-bare" data-value="{{ $val }}">{{ $label }}</button>
    @endforeach
  </div>
  <input class="filter-input-b" type="text" id="diQ" placeholder="Search deals…"
>
</div>

{{-- Table --}}
<div class="di-card">
  <div class="di-card-head">
    <h2>🔥 Deal Submissions</h2>
    <span id="diBadge">{{ $deals->total() }} deal{{ $deals->total()===1?'':'s' }}</span>
  </div>

  <div id="diRows">@include('dashboard.deals._rows')</div>
  <div id="diPagination">
    @if(method_exists($deals,'links') && $deals->lastPage() > 1)
      {{ $deals->links('vendor.pagination.dashboard') }}
    @endif
  </div>
</div>

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
  var timer, activeStatus='';

  function updatePills(){
    document.querySelectorAll('.di-pill-btn').forEach(function(btn){
      btn.classList.toggle('active', btn.dataset.value===activeStatus);
    });
  }

  function doFetch(page){
    var q = document.getElementById('diQ').value.trim();
    var p = new URLSearchParams({q:q, status:activeStatus});
    if(page>1) p.set('page',page);
    fetch('/dashboard/deals?'+p, {headers:{'X-Requested-With':'XMLHttpRequest'}})
      .then(function(r){return r.json();})
      .then(function(d){
        document.getElementById('diRows').innerHTML = d.rows;
        document.getElementById('diPagination').innerHTML = d.pagination;
        var b=document.getElementById('diBadge');
        if(b) b.textContent=d.total+' deal'+(d.total!==1?'s':'');
        bindPages();
      });
  }

  function bindPages(){
    document.getElementById('diPagination').querySelectorAll('a[href]').forEach(function(a){
      a.addEventListener('click',function(e){
        e.preventDefault();
        doFetch(new URL(a.href).searchParams.get('page')||1);
        window.scrollTo({top:0,behavior:'smooth'});
      });
    });
  }

  document.querySelectorAll('.di-pill-btn').forEach(function(btn){
    btn.addEventListener('click',function(){
      activeStatus=btn.dataset.value; updatePills(); doFetch(1);
    });
  });
  document.getElementById('diQ').addEventListener('input', function(){
    clearTimeout(timer); timer=setTimeout(function(){doFetch(1);},320);
  });

  updatePills();
  bindPages();
})();
</script>
@endpush

@endsection
