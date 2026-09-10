@extends('layouts.admin')
@section('title','Classified Ads')
@section('page','Classifieds')
@section('heading','Classified Ads Management')
@section('subheading','Approve, edit and promote personal classified ads')
@section('actions')
<a href="/admin/classifieds/create" class="ka-btn ka-btn-primary">+ Post Classified</a>
<a href="/admin/listings/create" class="ka-btn ka-btn-light ml-8">+ Post as User</a>
@endsection

@push('styles')

@endpush

@section('content')

{{-- ── Active / Approved + Pending ── --}}
<section class="sa-card" class="mb-28">

    {{-- Header --}}
    <div class="flex-row justify-between ka-card-head-row">
        <div>
            <h2 class="ka-section-title-row">Active Classified Ads</h2>
            <p class="ka-section-sub-row">Approved and pending classified submissions</p>
        </div>
        <span id="kcBadge" class="badge-green">{{ $listings->total() }} ads</span>
    </div>

    {{-- Filter bar --}}
    <div class="ka-card-border-bottom">
        <div class="pos-rel-mb12">
            <svg class="search-icon-abs" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input class="search-input-field" type="text" id="kcQ" placeholder="Search by title, poster name…" value="{{ request('q') }}" autocomplete="off">
            <div class="spinner-kc" id="kcSpinner"></div>
        </div>
        <div class="flex-wrap-gap8">
            <span class="section-label">Status</span>
            @foreach([''=>'All', 'approved'=>'Approved', 'pending'=>'Pending'] as $val => $label)
            <button type="button" class="kc-pill filter-pill" data-filter="status" data-value="{{ $val }}">
                {{ $label }}
            </button>
            @endforeach
            <span class="ml-auto-hint" id="kcCount">{{ $listings->total() }} results</span>
        </div>
    </div>

    {{-- Table --}}
    <div class="sa-table-wrap">
        <table class="sa-table sa-table-with-thumb sa-table-classifieds">
            <thead><tr>
                <th class="w-64">Image</th>
                <th class="w-200">Title</th>
                <th>Location</th>
                <th>Seller / Contact</th>
                <th class="w-110">Price</th>
                <th class="w-110">Status</th>
                <th class="w-200">Action</th>
            </tr></thead>
            <tbody id="kcTableBody">
                @include('admin.classifieds._rows')
            </tbody>
        </table>
    </div>
    <div class="p12-20" id="kcPagination">{{ $listings->links('vendor.pagination.ka-admin') }}</div>
</section>

{{-- ── Danger Zone: Rejected / Suspended / Expired ── --}}
@if($dangerListings->count())
<section class="sa-card border-red200-r14">
<div class="sa-card-head modal-danger-head">
    <span class="fs-20">⚠️</span>
    <div>
        <h2 class="m0-red-fs16">Danger Zone — Rejected / Suspended / Expired</h2>
        <p class="m0-red-fs12">{{ $dangerListings->count() }} ad(s) need attention. Not visible to the public.</p>
    </div>
</div>
<div class="sa-table-wrap"><table class="sa-table sa-table-with-thumb sa-table-classifieds">
<thead><tr>
    <th class="w-64">Image</th>
    <th class="w-200">Title</th>
    <th>Location</th>
    <th>Seller / Contact</th>
    <th class="w-110">Price</th>
    <th class="w-130">Status</th>
    <th class="w-220">Action</th>
</tr></thead>
<tbody>
@foreach($dangerListings as $listing)
@php $thumb = optional($listing->images->first())->path ?? $listing->image ?? null; @endphp
<tr class="bg-snow">
    <td class="w64-pr0">
        @if($thumb)<img class="csp5-392" src="{{ asset('storage/'.ltrim($thumb,'/')) }}" alt="{{ $listing->title }}">
        @else<span class="icon-box-44-red">📋</span>@endif
    </td>
    <td class="max-w-200">
        <b class="trunc-190-gray">{{ $listing->title }}</b>
        <small class="text-muted">{{ $listing->category->name ?? '—' }}</small>
    </td>
    <td class="text-gray400">{{ $listing->locationModel->name ?? $listing->location ?? '-' }}</td>
    <td>
        @if($listing->user_id)
            <span class="text-gray400">{{ optional($listing->store)->name ?? optional($listing->user)->name ?? 'Seller' }}</span>
        @else
            <b class="text-gray400">{{ $listing->poster_name ?? '—' }}</b>
            @if($listing->poster_phone)<br><span class="fs12-gray400">{{ $listing->poster_phone }}</span>@endif
        @endif
    </td>
    <td class="text-gray400">LKR {{ number_format($listing->price ?? 0) }}</td>
    <td><span class="pill-red">✕ {{ ucfirst($listing->status) }}</span></td>
    <td class="sa-actions-inline">
        <a href="/admin/classifieds/{{ $listing->id }}/edit">Edit</a>
        <form method="post" action="/admin/listings/{{ $listing->id }}/approve" onsubmit="return confirm('Re-approve this ad?')">@csrf<button class="text-green600">✓ Approve</button></form>
        <form method="post" action="/admin/listings/{{ $listing->id }}" onsubmit="return confirm('Permanently delete this ad?')" class="d-inline">@csrf @method('DELETE')<button class="danger">Delete</button></form>
    </td>
</tr>
@endforeach
</tbody></table></div>
</section>
@endif

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var timer,
        activeStatus = '{{ request('status','') }}',
        spinner = document.getElementById('kcSpinner'),
        tbody   = document.getElementById('kcTableBody'),
        paging  = document.getElementById('kcPagination'),
        count   = document.getElementById('kcCount'),
        badge   = document.getElementById('kcBadge');

    function updatePills(){
        document.querySelectorAll('.kc-pill').forEach(function(btn){
            var active = btn.dataset.filter==='status' && btn.dataset.value===activeStatus;
            btn.style.background  = active ? '#1b5e20' : '#f8fafc';
            btn.style.color       = active ? '#fff'    : '#475569';
            btn.style.borderColor = active ? '#1b5e20' : '#e2e8f0';
        });
    }

    function doFetch(page){
        var q = document.getElementById('kcQ').value.trim();
        var p = new URLSearchParams({q:q, status:activeStatus});
        if(page>1) p.set('page',page);
        spinner.style.display='block';
        fetch('/admin/classifieds?'+p, {headers:{'X-Requested-With':'XMLHttpRequest'}})
            .then(function(r){return r.json();})
            .then(function(d){
                tbody.innerHTML  = d.rows;
                paging.innerHTML = d.pagination;
                var t=d.total;
                count.textContent = t+' result'+(t!==1?'s':'');
                badge.textContent = t+' ad'+(t!==1?'s':'');
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

    document.querySelectorAll('.kc-pill').forEach(function(btn){
        btn.addEventListener('click',function(){
            if(btn.dataset.filter==='status') activeStatus=btn.dataset.value;
            updatePills(); doFetch(1);
        });
    });

    var qEl = document.getElementById('kcQ');
    qEl.addEventListener('input', function(){ clearTimeout(timer); timer=setTimeout(function(){doFetch(1);},320); });
    qEl.addEventListener('focus', function(){ this.style.borderColor='#1b5e20';this.style.background='#fff';this.style.boxShadow='0 0 0 3px rgba(27,94,32,.1)'; });
    qEl.addEventListener('blur',  function(){ this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none'; });

    updatePills();
    bindPages();
})();
</script>
@endpush
