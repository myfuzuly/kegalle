@extends('layouts.admin')
@section('title','Deal Management')
@section('page','Deals')
@section('heading','Deal Management')
@section('subheading','Approve, reject and manage seller deal submissions')
@section('actions')<a class="ka-btn ka-btn-primary" href="/admin/deals/create">+ Create Deal</a>@endsection

@push('styles')

@endpush

@section('content')
<section class="sa-card">

    {{-- Header --}}
    <div class="flex-row justify-between ka-card-head-row">
        <div>
            <h2 class="ka-section-title-row">Deal Management</h2>
            <p class="ka-section-sub-row">{{ $pendingCount }} pending approval</p>
        </div>
        <span id="kdBadge" class="badge-green">{{ $deals->total() }} deals</span>
    </div>

    {{-- Flash --}}
    @if(session('success'))<div class="alert-green-inner">{{ session('success') }}</div>@endif

    {{-- Filter bar --}}
    <div class="ka-card-border-bottom">
        <div class="pos-rel-mb12">
            <svg class="search-icon-abs" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input class="search-input-field" type="text" id="kdQ" placeholder="Search by listing title…" value="{{ request('q') }}" autocomplete="off">
            <div class="spinner-kd" id="kdSpinner"></div>
        </div>
        <div class="flex-wrap-gap8">
            <span class="section-label">Status</span>
            @foreach([''=>'All', 'pending'=>'Pending', 'approved'=>'Approved', 'rejected'=>'Rejected', 'expired'=>'Expired'] as $val => $label)
            <button type="button" class="kd-pill filter-pill" data-filter="status" data-value="{{ $val }}">
                {{ $label }}
            </button>
            @endforeach
            <span class="ml-auto-hint" id="kdCount">{{ $deals->total() }} results</span>
        </div>
    </div>

    {{-- Table --}}
    <div class="sa-table-wrap">
        <table class="sa-table sa-table-deals">
            <thead><tr>
                <th>Image</th><th>Listing</th><th>Seller / Store</th><th>Price</th><th>Discount</th><th>Period</th><th>Type</th><th>Status</th><th>Actions</th>
            </tr></thead>
            <tbody id="kdTableBody">
                @include('admin.deals._rows')
            </tbody>
        </table>
    </div>
    <div class="p12-20" id="kdPagination">{{ $deals->links('vendor.pagination.ka-admin') }}</div>
</section>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var timer,
        activeStatus = '{{ request('status','') }}',
        spinner = document.getElementById('kdSpinner'),
        tbody   = document.getElementById('kdTableBody'),
        paging  = document.getElementById('kdPagination'),
        count   = document.getElementById('kdCount'),
        badge   = document.getElementById('kdBadge');

    function updatePills(){
        document.querySelectorAll('.kd-pill').forEach(function(btn){
            var active = btn.dataset.filter==='status' && btn.dataset.value===activeStatus;
            btn.style.background  = active ? '#1b5e20' : '#f8fafc';
            btn.style.color       = active ? '#fff'    : '#475569';
            btn.style.borderColor = active ? '#1b5e20' : '#e2e8f0';
        });
    }

    function doFetch(page){
        var q = document.getElementById('kdQ').value.trim();
        var p = new URLSearchParams({q:q, status:activeStatus});
        if(page>1) p.set('page',page);
        spinner.style.display='block';
        fetch('/admin/deals?'+p, {headers:{'X-Requested-With':'XMLHttpRequest'}})
            .then(function(r){return r.json();})
            .then(function(d){
                tbody.innerHTML  = d.rows;
                paging.innerHTML = d.pagination;
                var t=d.total;
                count.textContent = t+' result'+(t!==1?'s':'');
                badge.textContent = t+' deal'+(t!==1?'s':'');
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

    document.querySelectorAll('.kd-pill').forEach(function(btn){
        btn.addEventListener('click',function(){
            if(btn.dataset.filter==='status') activeStatus=btn.dataset.value;
            updatePills(); doFetch(1);
        });
    });

    var qEl = document.getElementById('kdQ');
    qEl.addEventListener('input', function(){ clearTimeout(timer); timer=setTimeout(function(){doFetch(1);},320); });
    qEl.addEventListener('focus', function(){ this.style.borderColor='#1b5e20';this.style.background='#fff';this.style.boxShadow='0 0 0 3px rgba(27,94,32,.1)'; });
    qEl.addEventListener('blur',  function(){ this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none'; });

    updatePills();
    bindPages();
})();
</script>
@endpush
