@extends('layouts.admin')
@section('title','Products & Ads')
@section('page','Products / Ads')
@section('heading','Product / Ads Management')
@section('subheading','Post, edit, approve and promote store products and other listings')
@section('actions')<a href="/admin/listings/create" class="ka-btn ka-btn-primary">+ Post Ad</a>@endsection

@push('styles')

@endpush

@section('content')
<section class="sa-card">

    {{-- Header --}}
    <div class="flex-row justify-between ka-card-head-row">
        <div>
            <h2 class="ka-section-title-row">Product / Ads Management</h2>
            <p class="ka-section-sub-row">All listings except classifieds</p>
        </div>
        <span id="klBadge" class="badge-green">{{ $listings->total() }} listings</span>
    </div>

    {{-- Filter bar --}}
    <div class="ka-card-border-bottom">
        <div class="pos-rel-mb12">
            <svg class="search-icon-abs" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input class="search-input-field" type="text" id="klQ" placeholder="Search by title…" value="{{ request('q') }}" autocomplete="off">
            <div class="spinner-kl" id="klSpinner"></div>
        </div>
        <div class="flex-wrap-gap8">
            <span class="section-label">Status</span>
            @foreach([''=>'Approved', 'approved'=>'Approved', 'pending'=>'Pending', 'rejected'=>'Rejected', 'suspended'=>'Suspended'] as $val => $label)
            @if($val !== '' || true)
            <button type="button" class="kl-pill filter-pill" data-filter="status" data-value="{{ $val }}">
                {{ $val === '' ? 'Approved Only' : $label }}
            </button>
            @endif
            @endforeach

            <div class="divider-vert"></div>

            <span class="section-label">Type</span>
            @foreach([''=>'All', 'product'=>'Product', 'buy'=>'Buy', 'sell'=>'Sell', 'exchange'=>'Exchange', 'job'=>'Job', 'to-let'=>'To-Let'] as $val => $label)
            <button type="button" class="kl-pill filter-pill" data-filter="type" data-value="{{ $val }}">
                {{ $label }}
            </button>
            @endforeach

            <span class="ml-auto-hint" id="klCount">{{ $listings->total() }} results</span>
        </div>
    </div>

    {{-- Table --}}
    <div class="sa-table-wrap">
        <table class="sa-table sa-table-with-thumb sa-table-listings">
            <thead><tr>
                <th class="w-60">Image</th>
                <th class="w-200">Name</th>
                <th>Location</th>
                <th>Seller</th>
                <th class="w-110">Price</th>
                <th class="w-110">Status</th>
                <th class="w-90">Featured</th>
                <th class="w-180">Action</th>
            </tr></thead>
            <tbody id="klTableBody">
                @include('admin.listings._rows')
            </tbody>
        </table>
    </div>
    <div class="p12-20" id="klPagination">{{ $listings->links('vendor.pagination.ka-admin') }}</div>
</section>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var timer,
        activeStatus = '',
        activeType   = '',
        spinner = document.getElementById('klSpinner'),
        tbody   = document.getElementById('klTableBody'),
        paging  = document.getElementById('klPagination'),
        count   = document.getElementById('klCount'),
        badge   = document.getElementById('klBadge');

    function updatePills(){
        document.querySelectorAll('.kl-pill').forEach(function(btn){
            var active = (btn.dataset.filter==='status' && btn.dataset.value===activeStatus) ||
                         (btn.dataset.filter==='type'   && btn.dataset.value===activeType);
            btn.style.background  = active ? '#1b5e20' : '#f8fafc';
            btn.style.color       = active ? '#fff'    : '#475569';
            btn.style.borderColor = active ? '#1b5e20' : '#e2e8f0';
        });
    }

    function doFetch(page){
        var q = document.getElementById('klQ').value.trim();
        var p = new URLSearchParams({q:q, status:activeStatus, type:activeType});
        if(page>1) p.set('page',page);
        spinner.style.display='block';
        fetch('/admin/listings?'+p, {headers:{'X-Requested-With':'XMLHttpRequest'}})
            .then(function(r){return r.json();})
            .then(function(d){
                tbody.innerHTML  = d.rows;
                paging.innerHTML = d.pagination;
                var t=d.total;
                count.textContent = t+' result'+(t!==1?'s':'');
                badge.textContent = t+' listing'+(t!==1?'s':'');
                spinner.style.display='none';
                bindPages();
            })
            .catch(function(){ spinner.style.display='none'; });
    }

    function bindPages(){
        paging.querySelectorAll('a[href]').forEach(function(a){
            a.addEventListener('click',function(e){
                e.preventDefault();
                doFetch(new URL(a.href).searchParams.get('page')||1);
                window.scrollTo({top:0,behavior:'smooth'});
            });
        });
    }

    document.querySelectorAll('.kl-pill').forEach(function(btn){
        btn.addEventListener('click',function(){
            if(btn.dataset.filter==='status') activeStatus=btn.dataset.value;
            if(btn.dataset.filter==='type')   activeType=btn.dataset.value;
            updatePills(); doFetch(1);
        });
    });

    var qEl = document.getElementById('klQ');
    qEl.addEventListener('input', function(){ clearTimeout(timer); timer=setTimeout(function(){doFetch(1);},320); });
    qEl.addEventListener('focus', function(){ this.style.borderColor='#1b5e20';this.style.background='#fff';this.style.boxShadow='0 0 0 3px rgba(27,94,32,.1)'; });
    qEl.addEventListener('blur',  function(){ this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none'; });

    updatePills();
    bindPages();
})();
</script>
@endpush
