@extends('layouts.admin')

@section('title','Store Management')
@section('page','Stores')
@section('heading','Store Management')
@section('subheading','Approve, suspend and feature stores across the marketplace')

@section('actions')
<a href="/admin/stores/create" class="ka-btn ka-btn-primary">+ Create Store</a>
@endsection

@push('styles')

@endpush

@section('content')
<section class="sa-card">

    {{-- Header --}}
    <div class="flex-row justify-between ka-card-head-row">
        <div>
            <h2 class="ka-section-title-row">Store Management</h2>
            <p class="ka-section-sub-row">Approve, suspend and feature stores</p>
        </div>
        <span id="storeBadge" class="badge-green">{{ $stores->total() }} stores</span>
    </div>

    {{-- Filter bar — no <select>, no Select2 conflict --}}
    <div class="ka-card-border-bottom">

        {{-- Search row --}}
        <div class="pos-rel-mb12">
            <svg class="search-icon-abs" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input class="search-input-field" type="text" id="ksQ"
                placeholder="Search by name, owner, phone or email…"
                value="{{ request('q') }}"
                autocomplete="off">
            <div class="spinner-ks" id="ksSpinner"></div>
        </div>

        {{-- Pill filters row --}}
        <div class="flex-wrap-gap8">

            {{-- Status pills --}}
            <span class="section-label">Status</span>
            @foreach([''=>'All', 'approved'=>'Approved', 'pending'=>'Pending', 'suspended'=>'Suspended', 'rejected'=>'Rejected'] as $val => $label)
            <button type="button" class="ks-pill filter-pill" data-filter="status" data-value="{{ $val }}">
                {{ $label }}
            </button>
            @endforeach

            <div class="divider-vert"></div>

            {{-- Featured pills --}}
            <span class="section-label">Featured</span>
            @foreach([''=>'All', '1'=>'★ Featured', '0'=>'Not Featured'] as $val => $label)
            <button type="button" class="ks-pill filter-pill" data-filter="featured" data-value="{{ $val }}">
                {{ $label }}
            </button>
            @endforeach

            <span class="ml-auto-hint" id="ksCount">{{ $stores->total() }} results</span>
        </div>
    </div>

    {{-- Table --}}
    <div class="sa-table-wrap">
        <table class="sa-table sa-table-stores-mgmt sa-table-stores">
            <thead>
                <tr>
                    <th class="w-64">Logo</th>
                    <th class="w-180">Store</th>
                    <th>Owner</th>
                    <th>Contact</th>
                    <th class="w-80">Listings</th>
                    <th class="w-90">Limit</th>
                    <th class="w-110">Status</th>
                    <th class="w-110">Verified</th>
                    <th class="w-110">Featured</th>
                    <th class="w-210">Actions</th>
                </tr>
            </thead>
            <tbody id="ksTableBody">
                @include('admin.stores._rows')
            </tbody>
        </table>
    </div>
    <div class="p12-20" id="ksPagination">{{ $stores->links('vendor.pagination.ka-admin') }}</div>
</section>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var timer,
        activeStatus   = '{{ request('status','') }}',
        activeFeatured = '{{ request('featured','') }}',
        spinner = document.getElementById('ksSpinner'),
        tbody   = document.getElementById('ksTableBody'),
        paging  = document.getElementById('ksPagination'),
        count   = document.getElementById('ksCount'),
        badge   = document.getElementById('storeBadge');

    // Style active pill
    function updatePills(){
        document.querySelectorAll('.ks-pill').forEach(function(btn){
            var isActive = (btn.dataset.filter === 'status'   && btn.dataset.value === activeStatus) ||
                           (btn.dataset.filter === 'featured' && btn.dataset.value === activeFeatured);
            btn.style.background    = isActive ? '#1b5e20' : '#f8fafc';
            btn.style.color         = isActive ? '#fff'    : '#475569';
            btn.style.borderColor   = isActive ? '#1b5e20' : '#e2e8f0';
        });
    }

    function doFetch(page){
        var q = document.getElementById('ksQ').value.trim();
        var p = new URLSearchParams({q:q, status:activeStatus, featured:activeFeatured});
        if(page > 1) p.set('page', page);
        spinner.style.display = 'block';
        fetch('/admin/stores?' + p, {headers:{'X-Requested-With':'XMLHttpRequest'}})
            .then(function(r){return r.json();})
            .then(function(d){
                tbody.innerHTML  = d.rows;
                paging.innerHTML = d.pagination;
                var t = d.total;
                count.textContent = t + ' result' + (t!==1?'s':'');
                badge.textContent = t + ' store' + (t!==1?'s':'');
                spinner.style.display = 'none';
                bindPages();
            })
            .catch(function(){ spinner.style.display='none'; });
    }

    function debounce(){ clearTimeout(timer); timer=setTimeout(function(){ doFetch(1); },320); }

    function bindPages(){
        paging.querySelectorAll('a[href]').forEach(function(a){
            a.addEventListener('click',function(e){
                e.preventDefault();
                doFetch(new URL(a.href).searchParams.get('page')||1);
                window.scrollTo({top:0,behavior:'smooth'});
            });
        });
    }

    // Pill click handlers
    document.querySelectorAll('.ks-pill').forEach(function(btn){
        btn.addEventListener('click', function(){
            if(btn.dataset.filter === 'status')   activeStatus   = btn.dataset.value;
            if(btn.dataset.filter === 'featured') activeFeatured = btn.dataset.value;
            updatePills();
            doFetch(1);
        });
    });

    document.getElementById('ksQ').addEventListener('input', debounce);

    // Focus/blur styles for search
    var qInput = document.getElementById('ksQ');
    qInput.addEventListener('focus', function(){ this.style.borderColor='#1b5e20';this.style.background='#fff';this.style.boxShadow='0 0 0 3px rgba(27,94,32,.1)'; });
    qInput.addEventListener('blur',  function(){ this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none'; });

    updatePills();
    bindPages();
})();
</script>
@endpush
