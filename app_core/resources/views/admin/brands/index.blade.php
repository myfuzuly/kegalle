@extends('layouts.admin')
@section('title','Brands')
@section('page','Brands')
@section('heading','Brand Management')
@section('subheading','Manage all brands — add, edit, search and organize')

@section('actions')
<div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
    <a href="/admin/brands/create" class="ka-btn ka-btn-primary">+ Add Brand</a>
    <form method="get" style="display:flex;gap:8px;flex-wrap:wrap">
        <input name="q" value="{{ request('q') }}" placeholder="Search brand name..." style="height:40px;border:1px solid var(--ka-border);border-radius:10px;padding:0 14px;min-width:180px">
        <select name="status" style="height:40px;border:1px solid var(--ka-border);border-radius:10px;padding:0 10px">
            <option value="">All Status</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>
        <button class="ka-btn ka-btn-light">Filter</button>
        @if(request()->hasAny(['q','status']))
            <a href="/admin/brands" class="ka-btn ka-btn-light" style="color:#d32f2f">Clear</a>
        @endif
    </form>
</div>
@endsection

@section('content')

@if(session('success'))
<div style="background:#E8F5E9;color:#2E7D32;padding:12px 18px;border-radius:12px;margin-bottom:18px;font-weight:600;font-size:13px">{{ session('success') }}</div>
@endif

<section class="sa-card">
<div class="sa-card-head">
    <h2>All Brands</h2>
    <span>{{ $brands->total() }} of {{ $totalBrands }} brands</span>
</div>
<div class="sa-table-wrap">
<table class="sa-table">
<thead>
    <tr>
        <th style="width:30%">Brand</th>
        <th style="text-align:center">Models</th>
        <th style="text-align:center">Status</th>
        <th style="text-align:center">Order</th>
        <th style="width:200px">Actions</th>
    </tr>
</thead>
<tbody>
@forelse($brands as $brand)
<tr>
    <td>
        <b>{{ $brand->name }}</b>
        <br><small style="color:#94a3b8">/brand/{{ $brand->slug }}</small>
    </td>
    <td style="text-align:center"><b>{{ $brand->models_count }}</b></td>
    <td style="text-align:center">
        <span class="sa-status {{ $brand->is_active ? 'active' : 'suspended' }}">{{ $brand->is_active ? 'Active' : 'Inactive' }}</span>
    </td>
    <td style="text-align:center">{{ $brand->sort_order }}</td>
    <td class="sa-actions-inline">
        <a href="/admin/brands/{{ $brand->id }}/edit">Edit</a>
        <a href="/brand/{{ $brand->slug }}" target="_blank">View</a>
        <form method="post" action="/admin/brands/{{ $brand->id }}/toggle" style="display:inline">@csrf
            <button style="color:{{ $brand->is_active ? '#d32f2f' : '#2E7D32' }}">{{ $brand->is_active ? 'Deactivate' : 'Activate' }}</button>
        </form>
        <form method="post" action="/admin/brands/{{ $brand->id }}" style="display:inline" onsubmit="return confirm('Delete {{ addslashes($brand->name) }} and all its models?')">@csrf @method('DELETE')
            <button class="danger">Delete</button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="5" style="text-align:center;padding:40px;color:#94a3b8">
    @if(request()->hasAny(['q','status']))
        No brands match your search. <a href="/admin/brands">Clear filters</a>
    @else
        No brands yet. <a href="/admin/brands/create">Add your first brand</a>
    @endif
</td></tr>
@endforelse
</tbody>
</table>
</div>
<div style="padding:16px 20px">
    {{ $brands->links() }}
</div>
</section>
@endsection
