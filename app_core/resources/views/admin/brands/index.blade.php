@extends('layouts.admin')
@section('title','Brand Management')
@section('page','Brands')
@section('eyebrow','Marketplace')
@section('page_heading','Brand Management')
@section('subheading','Add, edit and organise all brands in the marketplace')
@section('actions')
<a href="/admin/brands/by-category" class="ka-btn ka-btn-light mr-6">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"  class="icon-inline"><path d="M4 6h16M4 10h16M4 14h8M4 18h8"/></svg>
    By Category
</a>
<a href="/admin/brands/create" class="ka-btn ka-btn-primary">+ Add Brand</a>
@endsection

@section('content')

@if(session('success'))<div class="kaa-flash-success">{{ session('success') }}</div>@endif

<section class="sa-card">
    {{-- Header --}}
    <div class="flex-row justify-between ka-card-head-row">
        <div>
            <h2 class="ka-section-title-row">All Brands</h2>
            <p class="ka-section-sub-row">Total in system: {{ $totalBrands }}</p>
        </div>
        <span id="kbBadge" class="badge-green">{{ $brands->total() }} brands</span>
    </div>

    {{-- Single-line filter bar --}}
    <div class="ka-card-border-bottom">
        <div class="flex-g8-ac">

            {{-- Search --}}
            <div class="rel-flex1">
                <svg class="search-icon-abs" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input class="search-input-38h" type="text" id="kbQ" placeholder="Search brands…" value="{{ request('q') }}" autocomplete="off">
                <div class="spinner-kb" id="kbSpinner"></div>
            </div>

            {{-- Custom category dropdown (div-based — avoids Select2 conflict) --}}
            <div class="rel-ns" id="kbCatWrap">
                <button class="custom-select-38h" type="button" id="kbCatBtn">
                    <span id="kbCatLabel">All Categories</span>
                    <svg class="abs-right-center" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <input type="hidden" id="kbCatVal" value="{{ request('category','') }}">
                <div class="dropdown-panel" id="kbCatMenu">
                    <button type="button" class="kbCatOpt dropdown-item" data-val="" data-label="All Categories">All Categories</button>
                    @foreach($categories as $cat)
                    <button type="button" class="kbCatOpt dropdown-item-hd" data-val="{{ $cat->id }}" data-label="{{ $cat->name }}">
                        {{ $cat->icon ?? '' }} {{ $cat->name }}
                    </button>
                    @foreach($cat->children as $sub)
                    <button type="button" class="kbCatOpt dropdown-item-sm" data-val="{{ $sub->id }}" data-label="{{ $sub->name }}">
                        <span class="dot-5-green"></span>{{ $sub->name }}
                    </button>
                    @endforeach
                    @endforeach
                </div>
            </div>

            {{-- Divider --}}
            <div class="divider-v"></div>

            {{-- Status pills --}}
            <span class="label-11-fw7">Status</span>
            @foreach([''=>'All', 'active'=>'Active', 'inactive'=>'Inactive'] as $val => $label)
            <button type="button" class="kb-pill pill-btn" data-filter="status" data-value="{{ $val }}">
                {{ $label }}
            </button>
            @endforeach

            {{-- Count --}}
            <span class="mla-muted-nw" id="kbCount">{{ $brands->total() }} results</span>
        </div>
    </div>

    {{-- Table --}}
    <div class="sa-table-wrap">
        <table class="sa-table">
            <thead><tr>
                <th>Brand</th>
                <th class="w-220">Categories</th>
                <th class="w-90">Status</th>
                <th class="center-w70">Order</th>
                <th class="w-200">Actions</th>
            </tr></thead>
            <tbody id="kbTableBody">
                @include('admin.brands._rows')
            </tbody>
        </table>
    </div>
    <div id="kbPagination" class="kaa-pagination">{{ $brands->links('vendor.pagination.ka-admin') }}</div>
</section>

@push('styles')

@endpush
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var timer,
        activeStatus = '{{ request('status','') }}',
        activeCat    = '{{ request('category','') }}',
        spinner = document.getElementById('kbSpinner'),
        tbody   = document.getElementById('kbTableBody'),
        paging  = document.getElementById('kbPagination'),
        count   = document.getElementById('kbCount'),
        badge   = document.getElementById('kbBadge');

    /* ── Custom category dropdown ───────────── */
    var catBtn  = document.getElementById('kbCatBtn');
    var catMenu = document.getElementById('kbCatMenu');
    var catVal  = document.getElementById('kbCatVal');
    var catLabel= document.getElementById('kbCatLabel');

    catBtn.addEventListener('click', function(e){
        e.stopPropagation();
        var open = catMenu.style.display !== 'none';
        catMenu.style.display = open ? 'none' : 'block';
        catBtn.classList.toggle('open', !open);
    });

    document.querySelectorAll('.kbCatOpt').forEach(function(opt){
        opt.addEventListener('click', function(){
            activeCat = opt.dataset.val;
            catVal.value = activeCat;
            catLabel.textContent = opt.dataset.label;
            catMenu.style.display = 'none';
            catBtn.classList.remove('open');
            updateCatOpts();
            doFetch(1);
        });
    });

    document.addEventListener('click', function(){ catMenu.style.display='none'; catBtn.classList.remove('open'); });

    function updateCatOpts(){
        document.querySelectorAll('.kbCatOpt').forEach(function(o){
            o.classList.toggle('active', o.dataset.val === activeCat);
        });
    }

    /* ── Status pills ───────────────────────── */
    function updatePills(){
        document.querySelectorAll('.kb-pill').forEach(function(btn){
            var active = btn.dataset.filter==='status' && btn.dataset.value===activeStatus;
            btn.style.background  = active ? '#1b5e20' : '#f8fafc';
            btn.style.color       = active ? '#fff'    : '#475569';
            btn.style.borderColor = active ? '#1b5e20' : '#e2e8f0';
        });
    }

    /* ── AJAX fetch ─────────────────────────── */
    function doFetch(page){
        var q = document.getElementById('kbQ').value.trim();
        var p = new URLSearchParams({q:q, status:activeStatus, category:activeCat});
        if(page>1) p.set('page',page);
        spinner.style.display='block';
        fetch('/admin/brands?'+p, {headers:{'X-Requested-With':'XMLHttpRequest'}})
            .then(function(r){return r.json();})
            .then(function(d){
                tbody.innerHTML  = d.rows;
                paging.innerHTML = d.pagination;
                var t=d.total;
                count.textContent = t+' result'+(t!==1?'s':'');
                badge.textContent = t+' brand'+(t!==1?'s':'');
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

    document.querySelectorAll('.kb-pill').forEach(function(btn){
        btn.addEventListener('click',function(){
            if(btn.dataset.filter==='status') activeStatus=btn.dataset.value;
            updatePills(); doFetch(1);
        });
    });

    var qEl = document.getElementById('kbQ');
    qEl.addEventListener('input', function(){ clearTimeout(timer); timer=setTimeout(function(){doFetch(1);},320); });
    qEl.addEventListener('focus', function(){ this.style.borderColor='#1b5e20';this.style.background='#fff';this.style.boxShadow='0 0 0 3px rgba(27,94,32,.1)'; });
    qEl.addEventListener('blur',  function(){ this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none'; });

    // Set initial state
    if(activeCat){
        var init = document.querySelector('.kbCatOpt[data-val="'+activeCat+'"]');
        if(init) catLabel.textContent = init.dataset.label;
    }
    updatePills();
    updateCatOpts();
    bindPages();
})();
</script>
@endpush
