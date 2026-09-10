@extends('layouts.admin')
@section('title','User Management')
@section('page','Users')
@section('heading','User Management')
@section('subheading','Manage roles, status and access for all marketplace users')
@section('actions')<a class="ka-btn ka-btn-primary" href="/admin/users/create">+ Add User</a>@endsection

@if(session('success'))
<div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;font-weight:600;">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;font-weight:600;">✕ {{ session('error') }}</div>
@endif



@section('content')
<div class="um-wrap">

    {{-- Stat cards --}}
    <div class="um-stats">
        <div class="um-stat">
            <div class="um-stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div class="um-stat-val">{{ $stats['total'] ?? $users->total() }}</div>
                <div class="um-stat-lbl">Total Users</div>
            </div>
        </div>
        <div class="um-stat">
            <div class="um-stat-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div>
                <div class="um-stat-val">{{ $stats['active'] ?? '—' }}</div>
                <div class="um-stat-lbl">Active</div>
            </div>
        </div>
        <div class="um-stat">
            <div class="um-stat-icon amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div>
                <div class="um-stat-val">{{ $stats['unverified'] ?? '—' }}</div>
                <div class="um-stat-lbl">Unverified Email</div>
            </div>
        </div>
        <div class="um-stat">
            <div class="um-stat-icon red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
            </div>
            <div>
                <div class="um-stat-val">{{ $stats['suspended'] ?? '—' }}</div>
                <div class="um-stat-lbl">Suspended</div>
            </div>
        </div>
    </div>

    {{-- Filter panel --}}
    <div class="um-panel">
        <div class="um-search-row">
            <div class="um-search-wrap">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="kuQ" placeholder="Search by name, email or phone…" value="{{ request('q') }}" autocomplete="off">
                <div class="um-spinner" id="kuSpinner"></div>
            </div>
        </div>
        <div class="um-filters">
            <span class="um-filter-label">Role</span>
            @foreach([''=>'All', 'admins'=>'Admins', 'super_admin'=>'Super Admin', 'admin'=>'Site Admin', 'seller'=>'Seller', 'user'=>'User'] as $val => $label)
            <button type="button" class="um-pill filter-pill {{ request('role','') === $val ? 'active' : '' }}" data-filter="role" data-value="{{ $val }}">{{ $label }}</button>
            @endforeach
            <div class="um-sep"></div>
            <span class="um-filter-label">Status</span>
            @foreach([''=>'All', 'active'=>'Active', 'suspended'=>'Suspended', 'inactive'=>'Inactive'] as $val => $label)
            <button type="button" class="um-pill filter-pill {{ request('status','') === $val ? 'active' : '' }}" data-filter="status" data-value="{{ $val }}">{{ $label }}</button>
            @endforeach
            <span class="um-count" id="kuCount">{{ $users->total() }} results</span>
        </div>
    </div>

    {{-- Table --}}
    <div class="um-table-wrap">
        <div style="overflow-x:auto">
        <table class="um-table">
            <thead><tr>
                <th style="width:180px">User</th>
                <th>Email</th>
                <th style="width:85px">Role</th>
                <th style="width:90px">Status</th>
                <th style="width:110px">Store Limit</th>
                <th style="width:100px">Email</th>
                <th style="width:95px">Joined</th>
                <th>Actions</th>
            </tr></thead>
            <tbody id="kuTableBody">
                @include('admin.users._rows')
            </tbody>
        </table>
        </div>
        <div class="um-pagination" id="kuPagination">{{ $users->links('vendor.pagination.ka-admin') }}</div>
    </div>

</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var timer,
        activeRole   = '{{ request('role','') }}',
        activeStatus = '{{ request('status','') }}',
        spinner = document.getElementById('kuSpinner'),
        tbody   = document.getElementById('kuTableBody'),
        paging  = document.getElementById('kuPagination'),
        count   = document.getElementById('kuCount');

    function updatePills(){
        document.querySelectorAll('.um-pill[data-filter]').forEach(function(btn){
            var active = (btn.dataset.filter==='role'   && btn.dataset.value===activeRole) ||
                         (btn.dataset.filter==='status' && btn.dataset.value===activeStatus);
            btn.classList.toggle('active', active);
        });
    }

    function doFetch(page){
        var q = document.getElementById('kuQ').value.trim();
        var p = new URLSearchParams({q:q, role:activeRole, status:activeStatus});
        if(page>1) p.set('page',page);
        spinner.style.display='block';
        fetch('/admin/users?'+p, {headers:{'X-Requested-With':'XMLHttpRequest'}})
            .then(function(r){return r.json();})
            .then(function(d){
                tbody.innerHTML  = d.rows;
                paging.innerHTML = d.pagination;
                var t=d.total;
                count.textContent = t+' result'+(t!==1?'s':'');
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

    document.querySelectorAll('.um-pill[data-filter]').forEach(function(btn){
        btn.addEventListener('click',function(){
            if(btn.dataset.filter==='role')   activeRole   = btn.dataset.value;
            if(btn.dataset.filter==='status') activeStatus = btn.dataset.value;
            updatePills();
            doFetch(1);
        });
    });

    var qInput = document.getElementById('kuQ');
    qInput.addEventListener('input', function(){ clearTimeout(timer); timer=setTimeout(function(){ doFetch(1); },320); });

    updatePills();
    bindPages();

    // Role select delegation — handles both static and AJAX-refreshed rows
    document.getElementById('kuTableBody').addEventListener('change', function(e){
        var sel = e.target.closest('select[name="role"]');
        if(!sel) return;
        sel.closest('form').submit();
    });

    // Delete confirm delegation (CSP-safe — no inline onsubmit)
    document.addEventListener('click', function(e){
        var btn = e.target.closest('button[data-confirm]');
        if(!btn) return;
        if(!window.confirm(btn.dataset.confirm)) e.preventDefault();
    });
})();
</script>
@endpush
