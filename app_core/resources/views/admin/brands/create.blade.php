@extends('layouts.admin')
@section('title','Add Brand')
@section('page','Brands')
@section('eyebrow','Marketplace')
@section('page_heading','Add New Brand')
@section('subheading','Create a new brand and assign it to categories')
@section('actions')
<a href="/admin/brands" class="ka-btn ka-btn-light">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"  class="icon-inline"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
    Back to Brands
</a>
@endsection

@push('styles')

@endpush

@section('content')
@if(session('success'))<div class="alert-green-sm">{{ session('success') }}</div>@endif

<form method="post" action="/admin/brands">
@csrf

{{-- Top row: Brand Details (left) + Publish/Tips (right) --}}
<div class="abr-top-row">

    {{-- Brand Details --}}
    <div class="abr-card">
        <div class="abr-card-header">
            <div class="abr-card-icon green">🏷️</div>
            <div>
                <p class="abr-card-title">Brand Details</p>
                <p class="abr-card-sub">Name and display order</p>
            </div>
        </div>

        <div class="abr-field">
            <label class="abr-label">Brand Name <span class="abr-req">*</span></label>
            <input class="abr-input" name="name" value="{{ old('name') }}" required
                   placeholder="e.g. Toyota, Samsung, Apple" autocomplete="off">
            @error('name')<span class="abr-error">{{ $message }}</span>@enderror
        </div>

        <div class="abr-field">
            <label class="abr-label">Sort Order</label>
            <input class="abr-input abr-input-sm" type="number" name="sort_order"
                   value="{{ old('sort_order', 0) }}" min="0">
            <span class="abr-hint">Lower numbers appear first. Use 0 for automatic alphabetical sorting.</span>
        </div>
    </div>

    {{-- Publish --}}
    <div class="abr-card h100-bbox">
        <div class="abr-card-header">
            <div class="abr-card-icon blue">🚀</div>
            <div>
                <p class="abr-card-title">Publish</p>
                <p class="abr-card-sub">Save to brand library</p>
            </div>
        </div>
        <div class="flex-col gap-8">
            <button type="submit" class="abr-publish-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
                Create Brand
            </button>
            <a href="/admin/brands" class="abr-cancel-btn">Cancel</a>
        </div>
    </div>

    {{-- Tips --}}
    <div class="abr-card grad-amber-card">
        <div class="abr-card-header mb14-pb12-amber">
            <div class="abr-card-icon amber">💡</div>
            <div>
                <p class="abr-card-title text-amber800b">Tips</p>
                <p class="abr-card-sub text-amber700">Best practices</p>
            </div>
        </div>
        <div>
            <div class="abr-tip-item border-amber">
                <div class="abr-tip-dot bg-amber600"></div>
                <span class="abr-tip-text text-amber800">Use title case — e.g. <em>Samsung</em>, not <em>samsung</em>.</span>
            </div>
            <div class="abr-tip-item border-amber">
                <div class="abr-tip-dot bg-amber600"></div>
                <span class="abr-tip-text text-amber800">Select at least one category so brands appear in listing forms.</span>
            </div>
            <div class="abr-tip-item border-amber">
                <div class="abr-tip-dot bg-amber600"></div>
                <span class="abr-tip-text text-amber800">Sort order <strong>0</strong> lets the system sort alphabetically.</span>
            </div>
        </div>
    </div>

</div>

{{-- Categories — full width --}}
<div class="abr-cat-section">
    <div class="abr-cat-toolbar">
        <div class="flex-row gap-10">
            <div class="abr-card-icon green sq28-fs13">📂</div>
            <div>
                <p class="abr-card-title" class="fs-13">Categories</p>
                <p class="abr-card-sub">Select all that apply — brands appear in all selected categories</p>
            </div>
        </div>
        <div class="flex-row flex-row-8">
            <span class="abr-cat-count" id="abrSelCount">0 selected</span>
            <button type="button" class="abr-select-all" id="abrSelectAll">Select All</button>
        </div>
    </div>

    <div class="abr-cat-grid" id="abrCatGrid">
        @foreach($categories as $cat)
        @php $checked = in_array($cat->id, (array) old('category_ids', [])); @endphp
        <label class="abr-cat-pill {{ $checked ? 'selected' : '' }}" id="pill-{{ $cat->id }}">
            <input class="abs-hidden" type="checkbox" name="category_ids[]" value="{{ $cat->id }}"
                   {{ $checked ? 'checked' : '' }}>
            <span class="abr-cat-icon">{{ $cat->icon ?: '📦' }}</span>
            <span class="abr-cat-name">{{ $cat->name }}</span>
            <span class="abr-cat-check">
                <svg width="9" height="9" viewBox="0 0 10 10" fill="none"><polyline points="1.5,5 4,7.5 8.5,2.5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        </label>
        @endforeach
    </div>
    @error('category_ids')<span class="abr-error" class="mt-12">{{ $message }}</span>@enderror
</div>

</form>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var pills = document.querySelectorAll('#abrCatGrid .abr-cat-pill');
    var countEl = document.getElementById('abrSelCount');
    var selectAllBtn = document.getElementById('abrSelectAll');
    var allSelected = false;

    function updateCount(){
        var n = document.querySelectorAll('#abrCatGrid .abr-cat-pill.selected').length;
        countEl.textContent = n + ' selected';
        allSelected = (n === pills.length);
        selectAllBtn.textContent = allSelected ? 'Clear All' : 'Select All';
    }

    pills.forEach(function(pill){
        pill.addEventListener('click', function(e){
            e.preventDefault();
            var cb = pill.querySelector('input[type=checkbox]');
            cb.checked = !cb.checked;
            pill.classList.toggle('selected', cb.checked);
            updateCount();
        });
    });

    selectAllBtn.addEventListener('click', function(){
        allSelected = !allSelected;
        pills.forEach(function(pill){
            var cb = pill.querySelector('input[type=checkbox]');
            cb.checked = allSelected;
            pill.classList.toggle('selected', allSelected);
        });
        updateCount();
    });

    updateCount();
})();
</script>
@endpush
