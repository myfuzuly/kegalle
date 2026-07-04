@extends('layouts.admin')
@section('title','Edit Brand — ' . $brand->name)
@section('page','Brands')
@section('heading', $brand->name)
@section('subheading','Edit brand details and manage models')
@section('actions')
<div style="display:flex;gap:8px">
    <a class="ka-btn ka-btn-light" href="/admin/brands">Back to Brands</a>
    <a class="ka-btn ka-btn-light" href="/brand/{{ $brand->slug }}" target="_blank">View on Site</a>
</div>
@endsection

@section('content')

@if(session('success'))
<div style="background:#E8F5E9;color:#2E7D32;padding:12px 18px;border-radius:12px;margin-bottom:18px;font-weight:600;font-size:13px">{{ session('success') }}</div>
@endif

<section class="sa-card" style="margin-bottom:24px">
<div class="sa-card-head"><h2>Brand Details</h2></div>
<form class="ka-premium-form" method="post" action="/admin/brands/{{ $brand->id }}">
@csrf @method('PUT')
<div class="ka-form-grid">
    <div class="ka-field">
        <label>Brand Name</label>
        <input name="name" value="{{ old('name', $brand->name) }}" required>
    </div>
    <div class="ka-field">
        <label>Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $brand->sort_order) }}">
    </div>
    <div class="ka-field">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
            <input type="checkbox" name="is_active" value="1" style="accent-color:#1b5e20" @checked($brand->is_active)>
            Active
        </label>
        <small>Inactive brands won't appear in listing forms or brand pages.</small>
    </div>
    <div class="ka-field">
        <small style="color:#94a3b8">Slug: /brand/{{ $brand->slug }}</small>
    </div>
</div>
<div class="ka-form-actions">
    <button class="ka-btn ka-btn-primary">Update Brand</button>
    <a href="/admin/brands" class="ka-btn ka-btn-light">Cancel</a>
</div>
</form>
</section>

<section class="sa-card" style="margin-bottom:24px">
<div class="sa-card-head">
    <h2>Models ({{ $models->count() }})</h2>
</div>
<div style="padding:20px;border-bottom:1px solid var(--ka-border,#e5e8ef)">
    <form method="post" action="/admin/brands/models" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap">
        @csrf
        <input type="hidden" name="brand_id" value="{{ $brand->id }}">
        <div style="flex:1;min-width:200px">
            <label style="font-size:12px;font-weight:600;color:#667085;margin-bottom:4px;display:block">Model Name</label>
            <input name="name" required placeholder="e.g. Corolla, Galaxy S24" style="width:100%;padding:10px 14px;border:1.5px solid #e5e8ef;border-radius:10px;font-size:14px">
        </div>
        <div style="width:100px">
            <label style="font-size:12px;font-weight:600;color:#667085;margin-bottom:4px;display:block">Order</label>
            <input type="number" name="sort_order" value="0" style="width:100%;padding:10px 14px;border:1.5px solid #e5e8ef;border-radius:10px;font-size:14px">
        </div>
        <button class="ka-btn ka-btn-primary" style="height:44px">Add Model</button>
    </form>
</div>
@if($models->count())
<div class="sa-table-wrap">
<table class="sa-table">
<thead><tr><th>Model Name</th><th>Slug</th><th style="text-align:center">Status</th><th style="text-align:center">Order</th><th>Actions</th></tr></thead>
<tbody>
@foreach($models as $model)
<tr id="model-row-{{ $model->id }}">
    <td><b>{{ $model->name }}</b></td>
    <td><small style="color:#94a3b8">{{ $model->slug }}</small></td>
    <td style="text-align:center"><span class="sa-status {{ $model->is_active ? 'active' : 'suspended' }}">{{ $model->is_active ? 'Active' : 'Inactive' }}</span></td>
    <td style="text-align:center">{{ $model->sort_order }}</td>
    <td class="sa-actions-inline">
        <button onclick="toggleEditModel({{ $model->id }})" style="background:none;border:none;color:var(--ka-primary,#1b5e20);cursor:pointer;font-weight:600;font-size:13px">Edit</button>
        <form method="post" action="/admin/brands/models/{{ $model->id }}" style="display:inline" onsubmit="return confirm('Delete model {{ addslashes($model->name) }}?')">@csrf @method('DELETE')
            <button class="danger">Delete</button>
        </form>
    </td>
</tr>
<tr id="edit-model-{{ $model->id }}" style="display:none">
    <td colspan="5" style="background:#f8fafc;padding:14px 20px">
        <form method="post" action="/admin/brands/models/{{ $model->id }}" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap">
            @csrf @method('PUT')
            <div style="flex:1;min-width:180px">
                <label style="font-size:12px;font-weight:600;color:#667085;margin-bottom:4px;display:block">Name</label>
                <input name="name" value="{{ $model->name }}" required style="width:100%;padding:8px 12px;border:1.5px solid #e5e8ef;border-radius:8px;font-size:13px">
            </div>
            <div style="width:80px">
                <label style="font-size:12px;font-weight:600;color:#667085;margin-bottom:4px;display:block">Order</label>
                <input type="number" name="sort_order" value="{{ $model->sort_order }}" style="width:100%;padding:8px 12px;border:1.5px solid #e5e8ef;border-radius:8px;font-size:13px">
            </div>
            <label style="display:flex;align-items:center;gap:4px;font-size:13px;cursor:pointer"><input type="checkbox" name="is_active" value="1" @checked($model->is_active)> Active</label>
            <button class="ka-btn ka-btn-primary" style="font-size:12px;height:36px">Save</button>
            <button type="button" onclick="toggleEditModel({{ $model->id }})" class="ka-btn ka-btn-light" style="font-size:12px;height:36px">Cancel</button>
        </form>
    </td>
</tr>
@endforeach
</tbody>
</table>
</div>
@else
<div style="padding:30px;text-align:center;color:#94a3b8">
    <p>No models yet. Add the first model above.</p>
</div>
@endif
</section>

<section class="sa-card" style="border:1.5px solid #ffcdd2">
<div class="sa-card-head"><h2 style="color:#d32f2f">Danger Zone</h2></div>
<div style="padding:20px;display:flex;align-items:center;justify-content:space-between">
    <div>
        <b>Delete this brand</b>
        <p style="font-size:13px;color:#667085;margin:4px 0 0">This will permanently delete {{ $brand->name }} and all its {{ $models->count() }} model(s).</p>
    </div>
    <form method="post" action="/admin/brands/{{ $brand->id }}" onsubmit="return confirm('Are you sure you want to delete {{ addslashes($brand->name) }} and ALL its models? This cannot be undone.')">
        @csrf @method('DELETE')
        <button class="ka-btn" style="background:#d32f2f;color:#fff;border:none">Delete Brand</button>
    </form>
</div>
</section>

<script>
function toggleEditModel(id) {
    var row = document.getElementById('edit-model-' + id);
    row.style.display = row.style.display === 'none' ? '' : 'none';
}
</script>
@endsection
