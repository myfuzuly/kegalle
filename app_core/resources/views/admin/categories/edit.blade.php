@extends('layouts.admin')
@section('title','Edit Category')
@section('page','Categories')
@section('heading','Edit Category')
@section('subheading','Update name, slug, parent, icon and visibility')

@section('actions')
@if($category->parent_id)
<a class="ka-btn ka-btn-light" href="/admin/categories?parent_id={{ $category->parent_id }}">Back</a>
@else
<a class="ka-btn ka-btn-light" href="/admin/categories">Back</a>
@endif
@endsection

@section('content')

{{-- Level indicator --}}
@php
    $level = 'Main';
    if ($category->parent_id) {
        $level = $category->children()->exists() ? 'Sub' : 'Leaf';
    }
    $levelColors = ['Main' => ['bg'=>'#e8f5e9','color'=>'#1b5e20'], 'Sub' => ['bg'=>'#e3f2fd','color'=>'#1565c0'], 'Leaf' => ['bg'=>'#f3e5f5','color'=>'#6a1b9a']];
    $lc = $levelColors[$level];
@endphp

<div class="flex-g10-mb20">
    <span style="font-size:12px;font-weight:700;padding:3px 10px;background:{{ $lc['bg'] }};color:{{ $lc['color'] }};border-radius:12px">{{ strtoupper($level) }}</span>
    @if($category->parent)
    <span class="fs13-muted-var">
        @if($category->parent->parent)
        <a href="/admin/categories" class="link-primary">All</a>
        › <a href="/admin/categories?parent_id={{ $category->parent->parent_id }}" class="link-primary">{{ $category->parent->parent->name }}</a>
        › <a href="/admin/categories?parent_id={{ $category->parent_id }}" class="link-primary">{{ $category->parent->name }}</a>
        @else
        <a href="/admin/categories" class="link-primary">All</a>
        › <a href="/admin/categories?parent_id={{ $category->parent_id }}" class="link-primary">{{ $category->parent->name }}</a>
        @endif
        › <strong>{{ $category->name }}</strong>
    </span>
    @endif
</div>

<section class="sa-card">
<form class="ka-premium-form" method="post" action="/admin/categories/{{ $category->id }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="ka-form-grid">

        {{-- Parent selection (hierarchical) --}}
        <div class="ka-field">
            <label>Main Category <small class="text-muted-light">(leave blank to keep as Main)</small></label>
            <select name="_main_id" id="editMainSel" onchange="editLoadSubs(this.value)">
                <option value="">— No Parent (Main Category) —</option>
                @foreach($mainCategories as $m)
                <option value="{{ $m->id }}"
                    @selected(
                        ($category->parent_id && !$category->parent?->parent_id && $category->parent_id == $m->id) ||
                        ($category->parent?->parent_id == $m->id)
                    )>{{ $m->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="ka-field" id="editSubWrap">
            <label>Sub Category <small class="text-muted-light">(select to place as a Leaf)</small></label>
            <select name="parent_id" id="editSubSel">
                <option value="">— None (will be placed under Main) —</option>
                @if($category->parent && $category->parent->parent_id)
                    {{-- current category is a leaf, pre-load siblings --}}
                    @foreach($subCategories as $sub)
                    <option value="{{ $sub->id }}" @selected($sub->id == $category->parent_id)>{{ $sub->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="ka-field">
            <label>Name <span class="text-red700">*</span></label>
            <input name="name" value="{{ old('name', $category->name) }}" required>
        </div>

        <div class="ka-field">
            <label>Slug URL</label>
            <input name="slug" value="{{ old('slug', $category->slug) }}" required>
        </div>

        <div class="ka-field">
            <label>Icon (emoji)</label>
            <input name="icon" value="{{ old('icon', $category->icon) }}" maxlength="8">
        </div>

        <div class="ka-field">
            <label>Sort Order</label>
            <input name="sort_order" type="number" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
        </div>

        {{-- Image --}}
        <div class="ka-field ka-span-2">
            <label>Category Image</label>
            @if($category->image)
            <div class="flex-row-14-mb10">
                <img class="thumb-80b" src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}">
                <label class="flex-g6-red-ptr">
                    <input type="checkbox" name="remove_image" value="1"> Remove current image
                </label>
            </div>
            @else
            <p class="fs13-muted-mb8">No image — emoji icon is used as fallback.</p>
            @endif
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
            <small>JPG/PNG/WEBP, max 2MB.</small>
        </div>

        <label class="ka-check">
            <input type="checkbox" name="is_active" value="1" @checked($category->is_active)> Active
        </label>
    </div>

    <div class="ka-form-actions">
        <button class="ka-btn ka-btn-primary">Update Category</button>
        <a class="ka-btn ka-btn-light" href="/admin/categories{{ $category->parent_id ? '?parent_id='.$category->parent_id : '' }}">Cancel</a>
    </div>
</form>
</section>

@if($category->children()->count() > 0)
<section class="sa-card" class="mt-20">
<div class="sa-card-head"><h2>Sub-Categories ({{ $category->children()->count() }})</h2>
<a href="/admin/categories?parent_id={{ $category->id }}" class="ka-btn ka-btn-light" class="fs-13">Manage →</a>
</div>
</section>
@endif

<script nonce="{{ $cspNonce ?? '' }}">
function editLoadSubs(mainId) {
    const wrap = document.getElementById('editSubWrap');
    const sel  = document.getElementById('editSubSel');
    if (!mainId) {
        sel.innerHTML = '<option value="">— None (will be placed under Main) —</option>';
        return;
    }
    sel.innerHTML = '<option value="">Loading…</option>';
    fetch('/api/subcategories/' + mainId)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">— None (place as Sub under Main) —</option>';
            data.forEach(s => {
                const selected = s.id == {{ $category->parent_id && $category->parent?->parent_id ? $category->parent_id : 'null' }} ? ' selected' : '';
                sel.innerHTML += `<option value="${s.id}"${selected}>${s.name}</option>`;
            });
        });
}

document.addEventListener('DOMContentLoaded', function () {
    const mainSel = document.getElementById('editMainSel');
    if (mainSel.value) {
        editLoadSubs(mainSel.value);
    }
});
</script>
@endsection
