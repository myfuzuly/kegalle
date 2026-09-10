@extends('layouts.admin')
@section('title','Event Management')
@section('page','Events')
@section('eyebrow','Marketplace')
@section('page_heading','Event Management')
@section('subheading','Create, approve, feature and manage all events in the marketplace')
@section('actions')<a class="ka-btn ka-btn-primary" href="/admin/events/create">+ Create Event</a>@endsection

@push('styles')

@endpush

@section('content')

@if(session('success'))<div class="alert-green-fw6">{{ session('success') }}</div>@endif
@if($pendingCount > 0)
<div class="alert-orange-sm">
    ⏳ {{ $pendingCount }} event{{ $pendingCount > 1 ? 's' : '' }} pending approval
</div>
@endif

<section class="sa-card">
    {{-- Header --}}
    <div class="flex-row justify-between ka-card-head-row">
        <div>
            <h2 class="ka-section-title-row">All Events</h2>
            <p class="ka-section-sub-row">Manage, approve and feature events</p>
        </div>
        <span id="keBadge" class="badge-green">{{ $events->total() }} events</span>
    </div>

    {{-- Filter bar --}}
    <div class="ka-card-border-bottom">
        <div class="pos-rel-mb12">
            <svg class="search-icon-abs" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input class="search-input-field" type="text" id="keQ" placeholder="Search by title, venue, location…" value="{{ request('q') }}" autocomplete="off">
            <div class="spinner-ke" id="keSpinner"></div>
        </div>
        <div class="flex-wrap-gap8">
            <span class="section-label">Status</span>
            @foreach([''=>'All', 'pending'=>'Pending', 'published'=>'Published', 'rejected'=>'Rejected', 'draft'=>'Draft', 'cancelled'=>'Cancelled'] as $val => $label)
            <button type="button" class="ke-pill filter-pill" data-filter="status" data-value="{{ $val }}">
                {{ $label }}
            </button>
            @endforeach
            <span class="ml-auto-hint" id="keCount">{{ $events->total() }} results</span>
        </div>
    </div>

    {{-- Table --}}
    <div class="sa-table-wrap">
        <table class="sa-table sa-table-events">
            <thead><tr>
                <th class="w-200">Title</th>
                <th class="w-110">Date</th>
                <th>Location</th>
                <th>Organizer</th>
                <th class="w-90">Price</th>
                <th class="w-80">Type</th>
                <th class="w-90">Featured</th>
                <th class="w-90">Status</th>
                <th class="w-70">Views</th>
                <th class="w-200">Actions</th>
            </tr></thead>
            <tbody id="keTableBody">
                @include('admin.events._rows')
            </tbody>
        </table>
    </div>
    <div class="p12-20" id="kePagination">{{ $events->links('vendor.pagination.ka-admin') }}</div>
</section>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var timer,
        activeStatus = '{{ request('status','') }}',
        spinner = document.getElementById('keSpinner'),
        tbody   = document.getElementById('keTableBody'),
        paging  = document.getElementById('kePagination'),
        count   = document.getElementById('keCount'),
        badge   = document.getElementById('keBadge');

    function updatePills(){
        document.querySelectorAll('.ke-pill').forEach(function(btn){
            var active = btn.dataset.filter==='status' && btn.dataset.value===activeStatus;
            btn.style.background  = active ? '#1b5e20' : '#f8fafc';
            btn.style.color       = active ? '#fff'    : '#475569';
            btn.style.borderColor = active ? '#1b5e20' : '#e2e8f0';
        });
    }

    function doFetch(page){
        var q = document.getElementById('keQ').value.trim();
        var p = new URLSearchParams({q:q, status:activeStatus});
        if(page>1) p.set('page',page);
        spinner.style.display='block';
        fetch('/admin/events?'+p, {headers:{'X-Requested-With':'XMLHttpRequest'}})
            .then(function(r){return r.json();})
            .then(function(d){
                tbody.innerHTML  = d.rows;
                paging.innerHTML = d.pagination;
                var t=d.total;
                count.textContent = t+' result'+(t!==1?'s':'');
                badge.textContent = t+' event'+(t!==1?'s':'');
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

    document.querySelectorAll('.ke-pill').forEach(function(btn){
        btn.addEventListener('click',function(){
            if(btn.dataset.filter==='status') activeStatus=btn.dataset.value;
            updatePills(); doFetch(1);
        });
    });

    var qEl = document.getElementById('keQ');
    qEl.addEventListener('input', function(){ clearTimeout(timer); timer=setTimeout(function(){doFetch(1);},320); });
    qEl.addEventListener('focus', function(){ this.style.borderColor='#1b5e20';this.style.background='#fff';this.style.boxShadow='0 0 0 3px rgba(27,94,32,.1)'; });
    qEl.addEventListener('blur',  function(){ this.style.borderColor='#e2e8f0';this.style.background='#f8fafc';this.style.boxShadow='none'; });

    updatePills();
    bindPages();
})();
</script>
@endpush
