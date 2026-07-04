@extends('layouts.admin')
@section('title','Listing Fields')
@section('page','Listing Fields')
@section('heading','Listing Fields')
@section('subheading','Manage custom fields and assign them to categories')
@section('content')

@if(session('success'))
<div style="background:#E8F5E9;color:#2E7D32;padding:12px 18px;border-radius:12px;margin-bottom:18px;font-weight:600;font-size:13px">{{ session('success') }}</div>
@endif

<div class="lfm-tabs" style="display:flex;gap:6px;margin-bottom:20px;flex-wrap:wrap">
    <a href="?tab=fields" class="ka-btn {{ $tab==='fields'?'ka-btn-primary':'ka-btn-light' }}" style="font-size:13px">Custom Fields</a>
    <a href="?tab=assign" class="ka-btn {{ $tab==='assign'?'ka-btn-primary':'ka-btn-light' }}" style="font-size:13px">Category Assignments</a>
    <a href="/admin/brands" class="ka-btn ka-btn-light" style="font-size:13px">Brands & Models →</a>
</div>

{{-- ═══ TAB: Custom Fields ═══ --}}
@if($tab === 'fields')
<section class="sa-card">
<div class="sa-card-head"><h2>Add Custom Field</h2></div>
<form class="ka-premium-form" method="post" action="/admin/listing-fields/fields">
@csrf
<div class="ka-form-grid">
    <div class="ka-field"><label>Label</label><input name="label" required placeholder="e.g. Condition"></div>
    <div class="ka-field"><label>Type</label>
        <select name="type" required>
            <option value="select">Select (Dropdown)</option>
            <option value="text">Text</option>
            <option value="number">Number</option>
            <option value="checkbox_group">Checkbox Group</option>
            <option value="brand_select">Brand Select</option>
            <option value="model_select">Model Select</option>
        </select>
    </div>
    <div class="ka-field ka-span-2"><label>Options (comma separated, for Select/Checkbox)</label><input name="options" placeholder="New, Used, Reconditioned, Antique"></div>
    <div class="ka-field"><label>Placeholder</label><input name="placeholder" placeholder="Select condition..."></div>
    <div class="ka-field"><label>Sort Order</label><input name="sort_order" type="number" value="0"></div>
    <label class="ka-check"><input type="checkbox" name="is_required" value="1"> Required</label>
    <label class="ka-check"><input type="checkbox" name="is_searchable" value="1" checked> Searchable</label>
</div>
<div class="ka-form-actions"><button class="ka-btn ka-btn-primary">Add Field</button></div>
</form>
</section>

<section class="sa-card">
<div class="sa-card-head"><h2>All Custom Fields</h2><span>{{ $fields->count() }} fields</span></div>
<div class="sa-table-wrap"><table class="sa-table"><thead><tr><th>Label</th><th>Name</th><th>Type</th><th>Options</th><th>Categories</th><th>Sort</th><th>Action</th></tr></thead><tbody>
@forelse($fields as $field)
<tr id="field-{{ $field->id }}">
    <td><b>{{ $field->label }}</b></td>
    <td><small>{{ $field->name }}</small></td>
    <td><span class="sa-status active" style="font-size:11px">{{ $field->type }}</span></td>
    <td><small>{{ $field->options ? implode(', ', $field->options) : '—' }}</small></td>
    <td>{{ $field->categories_count }}</td>
    <td>{{ $field->sort_order }}</td>
    <td class="sa-actions-inline">
        <a href="javascript:void(0)" onclick="editField({{ $field->id }})" title="Edit">Edit</a>
        <form method="post" action="/admin/listing-fields/fields/{{ $field->id }}" onsubmit="return confirm('Delete this field?')" style="display:inline">@csrf @method('DELETE')<button class="danger" title="Delete">Delete</button></form>
    </td>
</tr>
<tr id="edit-field-{{ $field->id }}" style="display:none">
    <td colspan="7">
        <form class="ka-premium-form" method="post" action="/admin/listing-fields/fields/{{ $field->id }}" style="padding:12px 0">
            @csrf @method('PUT')
            <div class="ka-form-grid" style="gap:10px">
                <div class="ka-field"><label>Label</label><input name="label" value="{{ $field->label }}" required></div>
                <div class="ka-field"><label>Type</label>
                    <select name="type" required>
                        <option value="select" @selected($field->type==='select')>Select</option>
                        <option value="text" @selected($field->type==='text')>Text</option>
                        <option value="number" @selected($field->type==='number')>Number</option>
                        <option value="checkbox_group" @selected($field->type==='checkbox_group')>Checkbox Group</option>
                        <option value="brand_select" @selected($field->type==='brand_select')>Brand Select</option>
                        <option value="model_select" @selected($field->type==='model_select')>Model Select</option>
                    </select>
                </div>
                <div class="ka-field ka-span-2"><label>Options</label><input name="options" value="{{ $field->options ? implode(', ', $field->options) : '' }}"></div>
                <div class="ka-field"><label>Placeholder</label><input name="placeholder" value="{{ $field->placeholder }}"></div>
                <div class="ka-field"><label>Sort</label><input name="sort_order" type="number" value="{{ $field->sort_order }}"></div>
                <label class="ka-check"><input type="checkbox" name="is_required" value="1" @checked($field->is_required)> Required</label>
                <label class="ka-check"><input type="checkbox" name="is_searchable" value="1" @checked($field->is_searchable)> Searchable</label>
            </div>
            <div style="margin-top:10px;display:flex;gap:8px"><button class="ka-btn ka-btn-primary" style="font-size:12px">Save</button><button type="button" class="ka-btn ka-btn-light" style="font-size:12px" onclick="document.getElementById('edit-field-{{ $field->id }}').style.display='none'">Cancel</button></div>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="7">No custom fields yet.</td></tr>
@endforelse
</tbody></table></div>
</section>
@endif

{{-- ═══ TAB: Category Assignments ═══ --}}
@if($tab === 'assign')
<section class="sa-card">
<div class="sa-card-head"><h2>Assign Fields to Category</h2><span>Select a subcategory, then pick which fields appear</span></div>
<form class="ka-premium-form" method="post" action="/admin/listing-fields/assign">
@csrf
<div class="ka-form-grid">
    <div class="ka-field ka-span-2">
        <label>Subcategory</label>
        <select name="category_id" id="assign-category" required>
            <option value="">Select subcategory...</option>
            @foreach($parents as $parent)
                <optgroup label="{{ $parent->icon }} {{ $parent->name }}">
                    @foreach($categories->where('parent_id', $parent->id) as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
    </div>
    <div class="ka-field ka-span-2">
        <label>Assigned Fields (check all that apply)</label>
        <div id="field-checkboxes" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:8px;padding:10px 0">
            @foreach($fields as $field)
            <label style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:500;cursor:pointer;padding:8px 12px;background:var(--ka-bg,#f5f7fb);border-radius:10px">
                <input type="checkbox" name="field_ids[]" value="{{ $field->id }}" class="assign-cb">
                {{ $field->label }} <small style="color:#667085">({{ $field->type }})</small>
            </label>
            @endforeach
        </div>
    </div>
</div>
<div class="ka-form-actions"><button class="ka-btn ka-btn-primary">Save Assignment</button></div>
</form>
</section>

<section class="sa-card">
<div class="sa-card-head"><h2>Current Assignments</h2></div>
<div class="sa-table-wrap"><table class="sa-table"><thead><tr><th>Category</th><th>Parent</th><th>Assigned Fields</th><th>Action</th></tr></thead><tbody>
@foreach($categories as $sub)
@php $subFields = $sub->customFields; @endphp
@if($subFields->count())
<tr>
    <td><b>{{ $sub->name }}</b></td>
    <td>{{ optional($sub->parent)->name }}</td>
    <td>
        @foreach($subFields as $sf)
        <span style="display:inline-block;background:#E3F2FD;color:#1565C0;padding:3px 10px;border-radius:8px;font-size:11px;font-weight:600;margin:2px">{{ $sf->label }}</span>
        @endforeach
    </td>
    <td class="sa-actions-inline"><a href="?tab=assign&cat={{ $sub->id }}" title="Edit">Edit</a></td>
</tr>
@endif
@endforeach
</tbody></table></div>
</section>
@endif


@endsection

@push('scripts')
<script>
function editField(id) {
    var row = document.getElementById('edit-field-' + id);
    row.style.display = row.style.display === 'none' ? '' : 'none';
}
// Category assignment: load current fields when category changes
var assignCat = document.getElementById('assign-category');
if (assignCat) {
    var urlCat = new URLSearchParams(window.location.search).get('cat');
    if (urlCat) { assignCat.value = urlCat; loadAssigned(urlCat); }

    assignCat.addEventListener('change', function() {
        if (this.value) loadAssigned(this.value);
        else document.querySelectorAll('.assign-cb').forEach(function(cb) { cb.checked = false; });
    });
}

function loadAssigned(catId) {
    fetch('/admin/listing-fields/category-fields/' + catId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            document.querySelectorAll('.assign-cb').forEach(function(cb) {
                cb.checked = data.assigned.indexOf(parseInt(cb.value)) !== -1;
            });
        });
}
</script>
@endpush
