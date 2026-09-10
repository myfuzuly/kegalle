@extends('layouts.admin')
@section('title','Edit Brand — ' . $brand->name)
@section('page','Brands')
@section('eyebrow','Marketplace')
@section('page_heading', $brand->name)
@section('subheading','Edit brand details and manage categories')
@section('actions')
<div class="flex-g8">
    <a class="ka-btn ka-btn-light" href="/admin/brands">← All Brands</a>
    <a class="ka-btn ka-btn-light" href="/brand/{{ $brand->slug }}" target="_blank">View on Site ↗</a>
</div>
@endsection

@section('content')

@if(session('success'))
<div class="be-flash">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- Stats strip --}}
<div class="be-stats">
    <div class="be-stat">
        <span class="be-stat-val">{{ $brand->categories->count() }}</span>
        <span class="be-stat-key">Categories</span>
    </div>
    <div class="be-stat">
        <span class="be-stat-val">#{{ $brand->sort_order }}</span>
        <span class="be-stat-key">Sort Order</span>
    </div>
    <div class="be-stat">
        <span class="be-stat-val fs14-pt4">
            <span class="sa-status {{ $brand->is_active ? 'active' : 'suspended' }}" class="fs-12">{{ $brand->is_active ? 'Active' : 'Inactive' }}</span>
        </span>
        <span class="be-stat-key">Status</span>
    </div>
</div>

{{-- Brand Details --}}
<div class="be-section">
    <div class="be-section-head">
        <div class="flex-row gap-14">
            <div class="be-brand-avatar">{{ strtoupper(substr($brand->name,0,1)) }}</div>
            <div>
                <div class="be-section-title">{{ $brand->name }}</div>
                <div class="be-section-sub">ID #{{ $brand->id }}{{ $brand->created_at ? ' · created ' . $brand->created_at->format('M j, Y') : '' }}</div>
            </div>
        </div>
        <button class="be-slug-badge" type="button" data-action="copy-slug" title="Copy slug">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            <span id="slugText">/brand/{{ $brand->slug }}</span>
        </button>
    </div>

    <form method="post" action="/admin/brands/{{ $brand->id }}" id="brandForm">
    @csrf @method('PUT')

    <div class="be-section-body">
        <div class="grid-2col-160-mb24">
            <div class="be-field">
                <label class="be-label" for="brandName">Brand Name</label>
                <input class="be-input" id="brandName" name="name" value="{{ old('name', $brand->name) }}" required placeholder="e.g. Toyota, Samsung…">
            </div>
            <div class="be-field">
                <label class="be-label" for="sortOrder">Sort Order</label>
                <input class="be-input" id="sortOrder" name="sort_order" type="number" value="{{ old('sort_order', $brand->sort_order) }}" min="0">
            </div>
        </div>

        {{-- Status toggle --}}
        <div class="flex-jsb-card-mb24">
            <div>
                <div class="fs13-fw7-dark">Active Status</div>
                <div class="fs12-muted-mt2">Inactive brands are hidden from listing forms and brand pages</div>
            </div>
            <label class="be-toggle">
                <input type="checkbox" name="is_active" value="1" @checked($brand->is_active)>
                <span class="be-toggle-track"></span>
                <span class="be-toggle-label" id="toggleLabel">{{ $brand->is_active ? 'Active' : 'Inactive' }}</span>
            </label>
        </div>

        {{-- Category chips --}}
        <div>
            <div class="flex-baseline-g8">
                <span class="be-label">Top-Level Categories</span>
                <span class="fs-11 text-muted">Select all that apply</span>
            </div>
            @php $selectedIds = old('category_ids', $brand->categories->pluck('id')->toArray()); @endphp
            <div class="flex-wrap-g8">
                @foreach($categories as $cat)
                <label class="be-cat-chip">
                    <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}"
                        {{ in_array($cat->id, (array) $selectedIds) ? 'checked' : '' }}>
                    <span class="be-cat-chip-check">
                        <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                    <span class="be-cat-chip-text">{{ $cat->name }}</span>
                </label>
                @endforeach
            </div>
        </div>
    </div>

    <div class="be-save-bar">
        <button type="submit" class="be-btn be-btn-primary btn-42h-md">
            Save Changes
        </button>
        <a href="/admin/brands" class="be-btn be-btn-light btn-42h-sm">Cancel</a>
        <span class="mla-muted-hidden" id="saveHint">Unsaved changes</span>
    </div>
    </form>
</div>

{{-- Danger Zone --}}
<div class="be-danger-section">
    <div class="be-danger-head">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2.5" stroke-linecap="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <span class="fs14-fw7-rose">Danger Zone</span>
    </div>
    <div class="p20-24-flex-jsb">
        <div>
            <div class="fs13-fw7-dark-mb4">Delete "{{ $brand->name }}"</div>
            <div class="fs12-slate">Permanently deletes this brand and all its models. This action cannot be undone.</div>
        </div>
        <form method="post" action="/admin/brands/{{ $brand->id }}" onsubmit="return confirm('Permanently delete {{ addslashes($brand->name) }}? This cannot be undone.')">
            @csrf @method('DELETE')
            <button type="submit" class="be-btn btn-red-40h">Delete Brand</button>
        </form>
    </div>
</div>

@endsection

@push('styles')

@endpush

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    // Toggle status label
    var toggleInput = document.querySelector('.be-toggle input');
    if(toggleInput){
        toggleInput.addEventListener('change', function(){
            document.getElementById('toggleLabel').textContent = this.checked ? 'Active' : 'Inactive';
        });
    }

    // Category chips — label natively toggles the checkbox; just sync the visual class
    document.querySelectorAll('.be-cat-chip').forEach(function(chip){
        var cb = chip.querySelector('input[type=checkbox]');
        function sync(){ chip.classList.toggle('is-checked', cb.checked); }
        sync(); // initial state
        cb.addEventListener('change', sync);
    });

    // Unsaved changes hint
    var dirty = false;
    var form  = document.getElementById('brandForm');
    var hint  = document.getElementById('saveHint');
    if(form && hint){
        form.addEventListener('input',  function(){ if(!dirty){ dirty=true; hint.style.display='block'; } });
        form.addEventListener('change', function(){ if(!dirty){ dirty=true; hint.style.display='block'; } });
        form.addEventListener('submit', function(){ dirty=false; });
    }
})();

// Copy slug to clipboard — wired via data-action="copy-slug"
document.querySelectorAll('[data-action="copy-slug"]').forEach(function(btn){
    btn.addEventListener('click', copySlug);
});
function copySlug(){
    var text = document.getElementById('slugText').textContent;
    navigator.clipboard.writeText('https://kegalle.com'+text).then(function(){
        var el = document.getElementById('slugText');
        var orig = el.textContent;
        el.textContent = 'Copied!';
        setTimeout(function(){ el.textContent = orig; }, 1500);
    });
}
</script>
@endpush
