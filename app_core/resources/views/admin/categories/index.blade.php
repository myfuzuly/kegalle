@extends('layouts.admin')
@section('title','Categories')
@section('page','Categories')
@section('eyebrow','Marketplace')
@section('page_heading','Category Management')
@php $__catSubhead = $stats['total'].' categories · '.$stats['main'].' main · '.$stats['other'].' sub/leaf'; @endphp
@section('subheading', $__catSubhead)
@section('actions')
<a class="ka-btn ka-btn-primary" href="#add-category" data-scroll-to="add-category">+ Add Category</a>
<a class="ka-btn ka-btn-light" href="/admin/categories">All Main</a>
@endsection

@push('styles')

@endpush

@section('content')

{{-- Flash messages --}}
@if(session('success'))
<div class="sa-alert sa-alert-success" class="mb-16">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="sa-alert sa-alert-danger" class="mb-16">{{ session('error') }}</div>
@endif

{{-- Stat cards --}}
<div class="grid-3-g12-mb20">
    <a href="/admin/categories" class="ka-stat-card ka-stat-card-green no-dec-p16-20-center">
        <div class="fs26-fw8-green">{{ $stats['main'] }}</div>
        <div class="meta-label">Main Categories</div>
    </a>
    <div class="ka-stat-card ka-stat-card-blue p16-20-center">
        <div class="fs26-fw8-blue">{{ $stats['other'] }}</div>
        <div class="meta-label">Sub &amp; Leaf</div>
    </div>
    <div class="ka-stat-card ka-stat-card-amber p16-20-center">
        <div class="fs26-fw8-dark">{{ $stats['total'] }}</div>
        <div class="meta-label">Total</div>
    </div>
</div>

{{-- Toolbar: Breadcrumb + Search --}}
<div class="kaa-toolbar">
    <nav class="kaa-breadcrumb">
        <a href="/admin/categories">All Categories</a>
        @foreach($breadcrumb as $crumb)
            <span class="kaa-breadcrumb-sep">›</span>
            <a href="/admin/categories?parent_id={{ $crumb['id'] }}">{{ $crumb['name'] }}</a>
        @endforeach
        @if($currentParent && empty($search))
            <span class="kaa-breadcrumb-note">— showing children</span>
        @endif
        @if($search)
            <span class="kaa-breadcrumb-note">— results for "{{ $search }}"</span>
        @endif
    </nav>
    <form method="GET" action="/admin/categories" class="kaa-search-form">
        <input class="kaa-search-input" name="search" value="{{ $search }}" placeholder="Search categories…">
        <button type="submit" class="ka-btn ka-btn-primary p7-14-sm">Search</button>
        @if($search)<a href="/admin/categories" class="ka-btn ka-btn-light p7-14-sm">Clear</a>@endif
    </form>
</div>

{{-- Category Table --}}
<section class="sa-card" class="mb-24">
<div class="sa-card-head">
    <h2>
        @if($search) Search Results for "{{ $search }}"
        @elseif($currentParent) {{ $currentParent->parent_id ? 'Leaf' : 'Sub' }} Categories under "{{ $currentParent->name }}"
        @else Main Categories
        @endif
    </h2>
    <span>{{ $categories->total() }} found</span>
</div>
<div class="sa-table-wrap">
<table class="sa-table sa-table-categories">
<thead><tr>
    <th class="w-56">Icon</th>
    <th class="w-200">Name</th>
    @if($search)<th>Level</th>@endif
    @if(!$currentParent || $search)<th>Parent</th>@endif
    <th class="w-60">Lists</th>
    @if(!$currentParent || $currentParent->parent_id === null)<th class="w-80">Children</th>@endif

    <th class="w-60">Sort</th>
    <th class="w-160">Actions</th>
</tr></thead>
<tbody>
@forelse($categories as $cat)
<tr>
    {{-- Icon --}}
    <td>
        <div class="kaa-cat-icon">
            @if($cat->image)
                <img src="{{ asset('storage/'.$cat->image) }}" alt="{{ $cat->name }}">
            @elseif($cat->icon)
                <span class="kaa-cat-icon-emoji">{{ $cat->icon }}</span>
            @else
                <span class="kaa-cat-icon-placeholder">{{ strtoupper(substr($cat->name,0,2)) }}</span>
            @endif
        </div>
    </td>

    {{-- Name --}}
    <td>
        <div class="kaa-cat-name">
            <div class="kaa-cat-name-row">
                <span class="kaa-cat-name-text" title="{{ $cat->name }}">{{ $cat->name }}</span>
                @if($cat->children_count > 0)
                <a href="/admin/categories?parent_id={{ $cat->id }}" class="kaa-drill">↳ {{ $cat->children_count }}</a>
                @endif
            </div>
            <span class="kaa-cat-id">#{{ $cat->id }}</span>
        </div>
    </td>

    {{-- Level (search only) --}}
    @if($search)
    <td>
        @if(!$cat->parent_id)
            <span class="kaa-level kaa-level-main">Main</span>
        @elseif($cat->children_count > 0)
            <span class="kaa-level kaa-level-sub">Sub</span>
        @else
            <span class="kaa-level kaa-level-leaf">Leaf</span>
        @endif
    </td>
    @endif

    {{-- Parent --}}
    @if(!$currentParent || $search)
    <td>
        @if($cat->parent_id)
            <a class="fs13-primary-lnk" href="/admin/categories?parent_id={{ $cat->parent_id }}">{{ $cat->parent->name ?? '—' }}</a>
        @else
            <span class="slate300-fs13">—</span>
        @endif
    </td>
    @endif

    {{-- Listings --}}
    <td class="kaa-num">
        @if($cat->listings_count > 0)
            <span class="text-ka">{{ $cat->listings_count }}</span>
        @else
            <span class="kaa-num-zero">0</span>
        @endif
    </td>

    {{-- Children --}}
    @if(!$currentParent || $currentParent->parent_id === null)
    <td class="kaa-num">
        @if($cat->children_count > 0)
            <a href="/admin/categories?parent_id={{ $cat->id }}" class="kaa-num-link">{{ $cat->children_count }}</a>
        @else
            <span class="kaa-num-zero">0</span>
        @endif
    </td>
    @endif

    {{-- Sort --}}
    <td class="kaa-num" class="text-muted">{{ $cat->sort_order ?? 0 }}</td>

    {{-- Actions --}}
    <td>
        <div class="kaa-actions">
            <a href="/admin/categories/{{ $cat->id }}/edit" class="kaa-act kaa-act-edit">Edit</a>
            <form method="post" action="/admin/categories/{{ $cat->id }}/toggle" class="d-contents">@csrf
                <button class="kaa-act kaa-act-toggle">{{ $cat->is_active ? 'Deactivate' : 'Activate' }}</button>
            </form>
            <form method="post" action="/admin/categories/{{ $cat->id }}" onsubmit="return confirm('Delete \'{{ addslashes($cat->name) }}\'? This cannot be undone.')" class="d-contents">@csrf @method('DELETE')
                <button class="kaa-act kaa-act-del">Delete</button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="10">
        <div class="kaa-empty">
            <span class="kaa-empty-icon">▦</span>
            <span class="kaa-empty-text">No categories found</span>
        </div>
    </td>
</tr>
@endforelse
</tbody>
</table>
</div>
@if($categories->hasPages())
<div class="p16-20">{{ $categories->links('vendor.pagination.ka-admin') }}</div>
@endif
</section>

{{-- Add Category Form --}}
<section class="sa-card kaa-add-cat-card" id="add-category">
<div class="sa-card-head">
    <h2>
        @if($currentParent && !$search)
            Add {{ $currentParent->parent_id ? 'Leaf' : 'Sub' }} Category under "{{ $currentParent->name }}"
        @else
            Add Category
        @endif
    </h2>
</div>
<form method="post" action="/admin/categories" enctype="multipart/form-data" class="kaa-cat-form">
@csrf

{{-- Section: Hierarchy --}}
<div class="kaa-form-section">
    <div class="kaa-form-section-label">Category Hierarchy</div>
    <div class="kaa-form-row">
        <div class="kaa-field">
            <label for="mainCatSelect">Main Category</label>
            <select name="_main_id" id="mainCatSelect" onchange="loadSubs(this.value)">
                <option value="">— None (creating a Main Category) —</option>
                @foreach($mainCategories as $m)
                <option value="{{ $m->id }}" @selected($currentParent && ($currentParent->id === $m->id || $currentParent->parent_id === $m->id))>{{ $m->name }}</option>
                @endforeach
            </select>
            <span class="kaa-field-hint">Leave blank to create a top-level main category</span>
        </div>
        <div class="kaa-field" id="subCatWrap" class="hidden">
            <label for="subCatSelect">Sub Category <span class="kaa-label-opt">optional — select to add a Leaf</span></label>
            <select name="parent_id" id="subCatSelect">
                <option value="">— None (add Sub directly under Main) —</option>
            </select>
            <span class="kaa-field-hint">Select a sub to place the new category as a leaf node</span>
        </div>
    </div>
</div>

{{-- Section: Details --}}
<div class="kaa-form-section">
    <div class="kaa-form-section-label">Category Details</div>
    <div class="kaa-form-row">
        <div class="kaa-field">
            <label for="catName">Name <span class="kaa-required">*</span></label>
            <input id="catName" name="name" placeholder="e.g. Smartphones" required>
        </div>
        <div class="kaa-field">
            <label for="catSlug">Slug URL <span class="kaa-label-opt">auto-generated if left blank</span></label>
            <input id="catSlug" name="slug" placeholder="smartphones">
        </div>
    </div>
    <div class="kaa-form-row kaa-form-row-3">
        <div class="kaa-field">
            <label for="catIcon">Icon <span class="kaa-label-opt">emoji</span></label>
            <input id="catIcon" name="icon" placeholder="📱" maxlength="8">
        </div>
        <div class="kaa-field">
            <label for="catSort">Sort Order</label>
            <input id="catSort" name="sort_order" type="number" value="{{ $currentParent ? 0 : ($stats['main'] + 1) }}">
        </div>
        <div class="kaa-field kaa-field-status">
            <label>Status</label>
            <label class="kaa-toggle">
                <input type="checkbox" name="is_active" value="1" checked>
                <span class="kaa-toggle-track"><span class="kaa-toggle-thumb"></span></span>
                <span class="kaa-toggle-label">Active</span>
            </label>
        </div>
    </div>
</div>

{{-- Section: Media --}}
<div class="kaa-form-section kaa-form-section-last">
    <div class="kaa-form-section-label">Media</div>
    <div class="kaa-field">
        <label>Category Image <span class="kaa-label-opt">JPG / PNG / WEBP · max 2 MB</span></label>
        <label class="kaa-file-drop" id="kaaFileDrop">
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" id="kaaFileInput" class="hidden">
            <span class="kaa-file-icon">↑</span>
            <span class="kaa-file-text" id="kaaFileText">Click to upload or drag & drop</span>
        </label>
    </div>
</div>

<div class="kaa-form-footer">
    <button type="submit" class="ka-btn ka-btn-primary btn-min160">Add Category</button>
</div>
</form>
</section>

<script nonce="{{ $cspNonce ?? '' }}">
function loadSubs(mainId) {
    const wrap = document.getElementById('subCatWrap');
    const sel  = document.getElementById('subCatSelect');
    if (!mainId) { wrap.style.display = 'none'; return; }
    wrap.style.display = '';
    sel.innerHTML = '<option value="">Loading…</option>';
    fetch('/api/subcategories/' + mainId)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">— None (add Sub directly under Main) —</option>';
            data.forEach(s => {
                sel.innerHTML += `<option value="${s.id}">${s.name}</option>`;
            });
            const urlPid = '{{ $currentParent?->id ?? "" }}';
            if (urlPid) sel.value = urlPid;
        });
}

document.addEventListener('DOMContentLoaded', function () {
    // Destroy Select2 on category hierarchy selects — they have dynamic options via JS
    setTimeout(function() {
        if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
            ['mainCatSelect','subCatSelect'].forEach(function(id) {
                var $el = jQuery('#' + id);
                if ($el.hasClass('select2-hidden-accessible')) $el.select2('destroy');
            });
        }
    }, 80);

    // File input label
    var fileInput = document.getElementById('kaaFileInput');
    var fileText  = document.getElementById('kaaFileText');
    var fileDrop  = document.getElementById('kaaFileDrop');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            fileText.textContent = this.files[0] ? this.files[0].name : 'Click to upload or drag & drop';
        });
        fileDrop.addEventListener('dragover', function(e){ e.preventDefault(); this.classList.add('drag-over'); });
        fileDrop.addEventListener('dragleave', function(){ this.classList.remove('drag-over'); });
        fileDrop.addEventListener('drop', function(e){
            e.preventDefault(); this.classList.remove('drag-over');
            if (e.dataTransfer.files[0]) {
                fileInput.files = e.dataTransfer.files;
                fileText.textContent = e.dataTransfer.files[0].name;
            }
        });
    }

    @if($currentParent)
        @if($currentParent->parent_id)
            document.getElementById('mainCatSelect').value = '{{ $currentParent->parent_id }}';
            loadSubs('{{ $currentParent->parent_id }}');
        @else
            document.getElementById('mainCatSelect').value = '{{ $currentParent->id }}';
            loadSubs('{{ $currentParent->id }}');
        @endif
    @endif
});

// Delegated scroll-to handler
document.addEventListener('click',function(e){
    var el=e.target.closest('[data-scroll-to]');
    if(!el) return;
    e.preventDefault();
    var target=document.getElementById(el.dataset.scrollTo);
    if(target) target.scrollIntoView({behavior:'smooth'});
});
</script>
@endsection
