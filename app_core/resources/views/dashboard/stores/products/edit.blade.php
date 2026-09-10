@extends('layouts.store-dashboard')

@section('title', 'Edit Product — ' . ($store->name ?? 'Store'))
@section('eyebrow', 'Products')
@section('heading', 'Edit Product')

@section('actions')
<a href="/dashboard/stores/{{ $store->id }}/products" class="kdl-tb-btn kdl-tb-btn-light">
  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
  All Products
</a>
@endsection

@push('styles')
<link href="/css/quill.snow.css" rel="stylesheet">

@endpush

@section('content')

@php
    $status = $listing->status ?? 'pending';
    $bannerCls  = match($status) { 'approved','active' => 'alc-banner-approved', 'rejected' => 'alc-banner-rejected', default => 'alc-banner-pending' };
    $bannerIcon = match($status) { 'approved','active' => '✅', 'rejected' => '❌', default => '⏳' };
    $bannerColor = match($status) { 'approved','active' => '#065f46', 'rejected' => '#991b1b', default => '#854d0e' };
    $bannerMsg  = match($status) {
        'approved','active' => 'This product is live and visible to buyers. Saving changes will resubmit for review.',
        'rejected'  => 'This product was rejected. Update and save to resubmit for review.',
        default     => 'This product is pending approval. Saving will update and resubmit it.',
    };
    $plVal    = old('cf_price_label', $listing->cf_price_label ?? 'fixed');
    $plMap    = ['fixed'=>'Fixed Price','negotiable'=>'Negotiable','free'=>'Free / No Price','per_month'=>'Per Month','per_year'=>'Per Year'];

    // Location preselection
    $selLocId    = old('location_id', $listing->location_id ?? '');
    $selLocLabel = '';
    if ($selLocId) {
        $autoLoc = $locations->firstWhere('id', $selLocId);
        if ($autoLoc) {
            $selLocParent = $autoLoc->parent_id ? $locations->firstWhere('id', $autoLoc->parent_id) : null;
            $selLocLabel  = $selLocParent ? $selLocParent->name . ' — ' . $autoLoc->name : $autoLoc->name . ' (All)';
        }
    }
    if (!$selLocId && !empty($store->city)) {
        $autoLoc = $locations->first(fn($l) => strcasecmp($l->name, $store->city) === 0);
        if (!$autoLoc) $autoLoc = $locations->first(fn($l) => stripos($l->name, $store->city) !== false);
        if ($autoLoc) {
            $selLocId    = $autoLoc->id;
            $selLocParent = $autoLoc->parent_id ? $locations->firstWhere('id', $autoLoc->parent_id) : null;
            $selLocLabel  = $selLocParent ? $selLocParent->name . ' — ' . $autoLoc->name : $autoLoc->name . ' (All)';
        }
    }
@endphp

@if($errors->any())
<div class="alert-error">
    <ul class="list-pl">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

{{-- Status banner --}}
<div class="alc-status-banner {{ $bannerCls }}">
    <div class="fs-22">{{ $bannerIcon }}</div>
    <div>
        <div style="font-weight:700;font-size:13.5px;color:{{ $bannerColor }}">{{ ucfirst($status) }}</div>
        <div style="font-size:12px;color:{{ $bannerColor }};opacity:.8;margin-top:2px">{{ $bannerMsg }}</div>
    </div>
    @if(in_array($status, ['approved','active']))
    <a class="mla-btn-green" href="/listings/{{ $listing->slug }}" target="_blank">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        View Live
    </a>
    @endif
</div>

{{-- Store banner --}}
<div class="alc-store-banner">
    <div class="fs-26">🏪</div>
    <div>
        <div class="text-14-emerald">{{ $store->name }}</div>
        <div class="text-green-sm">📍 {{ $store->city ?? 'Kegalle' }} · Product listed under your store</div>
    </div>
</div>

<form method="POST" action="/dashboard/stores/{{ $store->id }}/products/{{ $listing->id }}" enctype="multipart/form-data" id="prdForm">
@csrf
@method('PUT')

@php
    // reuse same location / price vars computed above
@endphp

<div class="alc-shell">

{{-- ── LEFT MAIN ─────────────────────────────────────────── --}}
<div>

    {{-- Photos → AI Suggest → Title --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon green">📝</div>
            <div>
                <p class="alc-card-title">Product Details</p>
                <p class="alc-card-sub">Photos, Auto Suggestion, and title</p>
            </div>
        </div>
        <div class="alc-field mb-14">
            <label class="alc-label">Photos <span class="text-hint">(up to 6 · main photo first)</span></label>
            @include('dashboard.partials.photo-uploader', ['maxSlots' => 6, 'existingImages' => $listing->images])
        </div>
        <div class="mb-16">
            <button class="btn-purple-dis" type="button" id="prdAiBtn" disabled>
                ✨ <span id="prdAiLabel">Auto Suggestion</span>
            </button>
            <p class="note-inline">
                <span class="badge-amber">📌 NOTE</span>
                <span class="text-subtle">Upload or change photos above, then click to auto-fill title &amp; description</span>
            </p>
        </div>
        <div class="alc-field">
            <label class="alc-label">Product Title <span class="alc-req">*</span></label>
            <input name="title" required class="alc-input" value="{{ old('title', $listing->title) }}" id="prdTitleField" maxlength="190" placeholder="e.g. Premium Oud Perfume 100ml — Long-lasting">
            @error('title')<div class="alc-err">{{ $message }}</div>@enderror
        </div>
    </div>

    {{-- Category + Brand --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon purple">🏷️</div>
            <div>
                <p class="alc-card-title">Category &amp; Brand</p>
                <p class="alc-card-sub">Browse or search the hierarchy</p>
            </div>
        </div>
        <div class="alc-cat-brand">
            <div class="alc-field">
                <label class="alc-label">Category</label>
                @include('partials.kcp-category-picker', [
                    'kcpNs'         => 'prdKcp',
                    'kcpHiddenId'   => 'prd-cat-val',
                    'kcpOldKey'     => 'category_id',
                    'kcpCategories' => $categories,
                    'kcpSelectedId' => old('category_id', $listing->category_id ?? ''),
                    'kcpOnChange'   => 'var f=document.getElementById("prd-cf-category-select");if(f){f.value=id;f.dispatchEvent(new Event("change"));}if(window.prdLoadBrands)prdLoadBrands(id);',
                ])
            </div>
            <div class="alc-field">
                <label class="alc-label">Brand</label>
                <input type="hidden" name="cf_brand_id" id="prd-brand-val" value="{{ old('cf_brand_id', $listing->cf_brand_id ?? '') }}">
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger {{ ($listing->cf_brand_id ?? '') ? 'ksd-has-value' : '' }}" id="prdBrandTrigger">
                        <span class="ksd-trigger-text {{ ($listing->cf_brand_id ?? '') ? '' : 'placeholder' }}" id="prdBrandLabel">{{ ($listing->cf_brand_id ?? '') ? 'Loading…' : 'Select category first…' }}</span>
                        <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="prdBrandDropdown">
                        <div class="ksd-search-row"><input type="text" class="ksd-search" id="prdBrandSearch" placeholder="Search brand…" autocomplete="off"></div>
                        <div class="ksd-list" id="prdBrandList">
                            <div class="ksd-empty" id="prdBrandEmpty">Select a category first…</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="prd-category-fields-container" class="mt-14"></div>
    </div>

    {{-- Location --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon amber">📍</div>
            <div>
                <p class="alc-card-title">Location</p>
                <p class="alc-card-sub">Where the item is based</p>
            </div>
        </div>
        <div class="alc-grid">
            <div class="alc-field">
                <label class="alc-label">Location</label>
                <input type="hidden" name="location_id" id="prd-loc-val" value="{{ $selLocId }}">
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger {{ $selLocId ? 'ksd-has-value' : '' }}" id="prdLocTrigger">
                        <span class="ksd-trigger-text {{ $selLocId ? '' : 'placeholder' }}" id="prdLocLabel">{{ $selLocLabel ?: '— No Location —' }}</span>
                        <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="prdLocDropdown" class="hidden">
                        <div class="ksd-search-row"><input type="text" class="ksd-search" id="prdLocSearch" placeholder="Type to search…" autocomplete="off"></div>
                        <div class="ksd-list" id="prdLocList">
                            <div class="ksd-item {{ !$selLocId ? 'ksd-selected' : '' }}" data-value="" data-label="— No Location —" data-search="" data-group="">— No Location —</div>
                            @foreach($locations->whereNull('parent_id') as $parentLoc)
                                <div class="ksd-group-label" data-group="{{ $parentLoc->id }}">{{ strtoupper($parentLoc->name) }}</div>
                                <div class="ksd-item ksd-indent {{ $selLocId==$parentLoc->id ? 'ksd-selected' : '' }}" data-value="{{ $parentLoc->id }}" data-label="{{ $parentLoc->name }} (All)" data-search="{{ strtolower($parentLoc->name) }}" data-group="{{ $parentLoc->id }}">📍 {{ $parentLoc->name }} (All)</div>
                                @foreach($locations->where('parent_id',$parentLoc->id) as $loc)
                                    <div class="ksd-item ksd-indent {{ $selLocId==$loc->id ? 'ksd-selected' : '' }}" data-value="{{ $loc->id }}" data-label="{{ $parentLoc->name }} — {{ $loc->name }}" data-search="{{ strtolower($parentLoc->name.' '.$loc->name) }}" data-group="{{ $parentLoc->id }}">{{ $loc->name }}</div>
                                @endforeach
                            @endforeach
                            <div class="ksd-empty" id="prdLocEmpty" class="hidden">No locations match</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alc-field">
                <label class="alc-label">Manual Location Text</label>
                <input name="location" class="alc-input" value="{{ old('location', $listing->location ?? '') }}" placeholder="e.g. Kegalle Town">
            </div>
        </div>
    </div>

    {{-- Description --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon slate">📄</div>
            <div>
                <p class="alc-card-title">Description</p>
                <p class="alc-card-sub">English · Sinhala · Tamil</p>
            </div>
        </div>

        <div class="alc-field mb-20">
            <label class="alc-label">🇬🇧 English Description</label>
            <div id="prdDescEditor"></div>
            <input type="hidden" name="description" id="prdDescHidden" value="{{ old('description', $listing->description ?? '') }}" class="hidden">
        </div>

        <div class="divider-top-sm">
            <div class="flex-between-g10">
                <button class="btn-add-dashed" type="button" id="siToggleBtn">
                    <span id="siArrow">▶</span> 🇱🇰 Add Sinhala Description <span class="fs-11 text-muted">(optional)</span>
                </button>
                <button class="btn-action-blue-hidden" type="button" id="translateSiBtn">
                    🌐 <span id="siLabel">Translate to Sinhala</span>
                </button>
            </div>
            <div class="hidden mt-10" id="siPanel">
                <label class="alc-label mb6-block">සිංහල විස්තරය</label>
                <div id="prdDescSiEditor"></div>
                <input type="hidden" name="description_si" id="prdDescSiHidden" value="{{ old('description_si', $listing->description_si ?? '') }}" class="hidden">
            </div>
        </div>

        <div class="divider-top-md">
            <div class="flex-between-g10">
                <button class="btn-add-dashed" type="button" id="taToggleBtn">
                    <span id="taArrow">▶</span> 🇱🇰 Add Tamil Description <span class="fs-11 text-muted">(optional)</span>
                </button>
                <button class="btn-action-amber-hidden" type="button" id="translateTaBtn">
                    🌐 <span id="taLabel">Translate to Tamil</span>
                </button>
            </div>
            <div class="hidden mt-10" id="taPanel">
                <label class="alc-label mb6-block">தமிழ் விளக்கம்</label>
                <div id="prdDescTaEditor"></div>
                <input type="hidden" name="description_ta" id="prdDescTaHidden" value="{{ old('description_ta', $listing->description_ta ?? '') }}" class="hidden">
            </div>
        </div>
    </div>

    {{-- Pricing --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon amber">💰</div>
            <div>
                <p class="alc-card-title">Pricing</p>
                <p class="alc-card-sub">Set price type and amount</p>
            </div>
        </div>
        <div class="alc-grid">
            <div class="alc-field" id="prdPriceWrap" @class(['hidden' => $plVal === 'free'])>
                <label class="alc-label" id="prdPriceLabel">{{ $plVal==='per_month'?'Price / Month (LKR)':($plVal==='per_year'?'Price / Year (LKR)':'Price (LKR)') }}</label>
                <input name="price" type="number" step="1" min="0" class="alc-input" id="prdPriceInput" value="{{ old('price', $listing->price ? (int)$listing->price : '') }}" placeholder="0">
            </div>
            <div class="alc-field">
                <label class="alc-label">Price Type</label>
                <input type="hidden" name="cf_price_label" id="prdPlVal" value="{{ $plVal }}">
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger ksd-has-value" id="prdPlTrigger">
                        <span class="ksd-trigger-text" id="prdPlLabel">{{ $plMap[$plVal]??'Fixed Price' }}</span>
                        <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="prdPlDropdown" class="hidden">
                        <div class="ksd-search-row"><input type="text" class="ksd-search" id="prdPlSearch" placeholder="Search…" autocomplete="off"></div>
                        <div class="ksd-list" id="prdPlList">
                            @foreach($plMap as $v=>$l)
                            <div class="ksd-item {{ $plVal===$v?'ksd-selected':'' }}" data-value="{{ $v }}" data-label="{{ $l }}" data-search="{{ strtolower($l) }}">{{ $l }}</div>
                            @endforeach
                            <div class="ksd-empty" id="prdPlEmpty" class="hidden">No match</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ad Type --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon blue">🏷️</div>
            <div>
                <p class="alc-card-title">Ad Type</p>
                <p class="alc-card-sub">What kind of listing is this?</p>
            </div>
        </div>
        <div class="alc-field">
            <label class="alc-label">Ad Type</label>
            <select name="ad_type" class="alc-select">
                <option value="sale"     {{ old('ad_type', $listing->ad_type ?? 'sale')==='sale'     ? 'selected':'' }}>For Sale</option>
                <option value="rent"     {{ old('ad_type', $listing->ad_type ?? '')==='rent'     ? 'selected':'' }}>For Rent</option>
                <option value="wanted"   {{ old('ad_type', $listing->ad_type ?? '')==='wanted'   ? 'selected':'' }}>Wanted</option>
                <option value="free"     {{ old('ad_type', $listing->ad_type ?? '')==='free'     ? 'selected':'' }}>Free</option>
                <option value="exchange" {{ old('ad_type', $listing->ad_type ?? '')==='exchange' ? 'selected':'' }}>Exchange</option>
            </select>
        </div>
    </div>

    {{-- Bottom Submit --}}
    <div class="flex-gap10-mt8">
        <button type="submit" class="alc-publish-btn mw-280" id="prdSubmitBtn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Save &amp; Resubmit for Approval
        </button>
        <a href="/dashboard/stores/{{ $store->id }}/products" class="alc-cancel-btn mw120-mt0">Cancel</a>
    </div>

</div>

{{-- ── RIGHT SIDEBAR ─────────────────────────────────────── --}}
<div>
    {{-- Submit --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon green">💾</div>
            <div>
                <p class="alc-card-title">Save Changes</p>
                <p class="alc-card-sub">Will resubmit for admin review</p>
            </div>
        </div>
        <button type="submit" class="alc-publish-btn" form="prdForm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Save &amp; Resubmit
        </button>
        <a href="/dashboard/stores/{{ $store->id }}/products" class="alc-cancel-btn">Cancel</a>
    </div>

    {{-- Product Info --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon slate">📦</div>
            <div>
                <p class="alc-card-title">Product Info</p>
                <p class="alc-card-sub">Current stats</p>
            </div>
        </div>
        <div class="alc-meta-row"><span>Status</span><span class="alc-meta-val">{{ ucfirst($listing->status ?? 'pending') }}</span></div>
        <div class="alc-meta-row"><span>Views</span><span class="alc-meta-val">{{ number_format($listing->views ?? 0) }}</span></div>
        <div class="alc-meta-row"><span>Added</span><span class="alc-meta-val">{{ $listing->created_at?->format('M d, Y') ?? '—' }}</span></div>
        <div class="alc-meta-row"><span>Updated</span><span class="alc-meta-val">{{ $listing->updated_at?->format('M d, Y') ?? '—' }}</span></div>
        <div class="alc-meta-row"><span>Photos</span><span class="alc-meta-val">{{ $listing->images->count() }} / 6</span></div>
    </div>

    {{-- Tips --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon amber">✅</div>
            <div>
                <p class="alc-card-title">Tips for a better listing</p>
            </div>
        </div>
        <div class="alc-tip-item"><div class="alc-tip-dot">1</div><div>Use a clear, specific title — include brand, model and condition.</div></div>
        <div class="alc-tip-item"><div class="alc-tip-dot">2</div><div>Add at least 3 photos in good lighting.</div></div>
        <div class="alc-tip-item"><div class="alc-tip-dot">3</div><div>Set a realistic price. Mention if it's negotiable.</div></div>
        <div class="alc-tip-item"><div class="alc-tip-dot">4</div><div>Describe material, size, warranty, and delivery options.</div></div>
    </div>

    <div class="alc-card bg-orange50-border">
        <p class="fs13-fw7-orange">⚠️ Note</p>
        <p class="fs125-orange-lh">Saving changes will set your product back to <strong>Pending</strong> for admin review.</p>
    </div>
</div>

</div>
</form>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
// ── Brand loader ──────────────────────────────────────────────────────────
(function(){
    var trigger=document.getElementById('prdBrandTrigger'), dropdown=document.getElementById('prdBrandDropdown');
    var search=document.getElementById('prdBrandSearch'), list=document.getElementById('prdBrandList');
    var hidden=document.getElementById('prd-brand-val'), label=document.getElementById('prdBrandLabel');
    var emptyEl=document.getElementById('prdBrandEmpty');
    var open=false;
    if(!trigger) return;
    function openB(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); search.value=''; filterB(''); search.focus(); open=true; }
    function closeB(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeB():openB(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeB(); });
    search.addEventListener('input',function(){ filterB(this.value.toLowerCase()); });
    function filterB(q){ var items=list.querySelectorAll('.ksd-item'); var any=false; items.forEach(function(i){ var m=!q||i.dataset.search.indexOf(q)!==-1; i.style.display=m?'':'none'; if(m)any=true; }); emptyEl.style.display=any?'none':''; }
    function renderBrands(brands, preselect){
        list.querySelectorAll('.ksd-item').forEach(function(i){i.remove();});
        brands.forEach(function(b){
            var item=document.createElement('div'); item.className='ksd-item';
            item.dataset.value=b.id; item.dataset.search=b.name.toLowerCase(); item.textContent=b.name;
            if(preselect && String(b.id)===String(preselect)){ item.classList.add('ksd-selected'); label.textContent=b.name; label.classList.remove('placeholder'); trigger.classList.add('ksd-has-value'); }
            item.addEventListener('click',function(){
                hidden.value=this.dataset.value; label.textContent=this.textContent;
                label.classList.remove('placeholder'); trigger.classList.add('ksd-has-value');
                list.querySelectorAll('.ksd-item').forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected'); closeB();
                var cfBrand=document.getElementById('cf-brand-select');
                if(cfBrand){ cfBrand.value=this.dataset.value; cfBrand.dispatchEvent(new Event('change')); }
            });
            list.insertBefore(item,emptyEl);
        });
        emptyEl.style.display=brands.length?'none':''; emptyEl.textContent='No brands found';
    }
    window.prdLoadBrands=function(catId, preselect){
        if(!catId){ emptyEl.textContent='Select a category first…'; emptyEl.style.display=''; list.querySelectorAll('.ksd-item').forEach(function(i){i.remove();}); return; }
        if(!preselect){ hidden.value=''; label.textContent='Loading…'; label.classList.add('placeholder'); trigger.classList.remove('ksd-has-value'); }
        fetch('/api/brands/by-category/'+catId).then(function(r){return r.json();}).then(function(brands){renderBrands(brands, preselect);}).catch(function(){ emptyEl.textContent='Failed to load'; emptyEl.style.display=''; });
    };
    // Pre-load brands if category is already selected
    var initCat = document.getElementById('prd-cat-val');
    var initBrand = hidden ? hidden.value : '';
    if(initCat && initCat.value) window.prdLoadBrands(initCat.value, initBrand);
})();

// ── Location ksd ─────────────────────────────────────────────────────────
(function(){
    var trigger=document.getElementById('prdLocTrigger'), dropdown=document.getElementById('prdLocDropdown');
    var search=document.getElementById('prdLocSearch'), list=document.getElementById('prdLocList');
    var hidden=document.getElementById('prd-loc-val'), label=document.getElementById('prdLocLabel');
    var emptyEl=document.getElementById('prdLocEmpty');
    var items=list.querySelectorAll('.ksd-item'), groups=list.querySelectorAll('.ksd-group-label');
    var open=false;
    if(!trigger) return;
    function openD(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); search.value=''; filterItems(''); search.focus(); open=true; }
    function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    search.addEventListener('input',function(){ filterItems(this.value.toLowerCase()); });
    items.forEach(function(item){ item.addEventListener('click',function(){ var val=this.dataset.value, lbl=this.dataset.label; hidden.value=val; label.textContent=lbl||'— No Location —'; label.classList.toggle('placeholder',!val); trigger.classList.toggle('ksd-has-value',!!val); items.forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected'); closeD(); }); });
    function filterItems(q){ var vis={}; items.forEach(function(i){ var m=!q||i.dataset.search.indexOf(q)!==-1||(i.dataset.label||'').toLowerCase().indexOf(q)!==-1; i.style.display=m?'':'none'; if(m&&i.dataset.group) vis[i.dataset.group]=true; }); groups.forEach(function(g){ g.style.display=(!q||vis[g.dataset.group])?'':'none'; }); if(emptyEl) emptyEl.style.display=Array.from(items).some(function(i){return i.style.display!=='none';})?'none':''; }
    var cur=hidden.value; if(cur){ var pre=Array.from(items).find(function(i){return i.dataset.value===cur;}); if(pre){label.textContent=pre.dataset.label;label.classList.remove('placeholder');trigger.classList.add('ksd-has-value');} }
})();

// ── Fake category select for category-fields.js ───────────────────────────
(function(){
    var f=document.createElement('select'); f.id='prd-cf-category-select'; f.className='no-select2'; f.style.display='none';
    var container=document.getElementById('prd-category-fields-container');
    (container||document.body).appendChild(f);
    var initCat=document.getElementById('prd-cat-val');
    if(initCat && initCat.value) setTimeout(function(){ f.value=initCat.value; f.dispatchEvent(new Event('change')); }, 300);
})();

// ── Price Type ksd ───────────────────────────────────────────────────────
(function(){
    var lblMap={'fixed':'Price (LKR)','negotiable':'Price (LKR)','free':'','per_month':'Price / Month (LKR)','per_year':'Price / Year (LKR)'};
    var t=document.getElementById('prdPlTrigger'),d=document.getElementById('prdPlDropdown'),s=document.getElementById('prdPlSearch');
    var l=document.getElementById('prdPlList'),h=document.getElementById('prdPlVal'),lb=document.getElementById('prdPlLabel');
    var em=document.getElementById('prdPlEmpty'),wrap=document.getElementById('prdPriceWrap'),pl=document.getElementById('prdPriceLabel'),inp=document.getElementById('prdPriceInput');
    if(!t) return;
    var open=false;
    function applyType(v){if(v==='free'){wrap.style.display='none';if(inp)inp.value='';}else{wrap.style.display='';if(pl)pl.textContent=lblMap[v]||'Price (LKR)';}}
    function openD(){d.style.display='block';t.classList.add('ksd-open');s.value='';filter('');s.focus();open=true;}
    function closeD(){d.style.display='none';t.classList.remove('ksd-open');open=false;}
    t.addEventListener('click',function(e){e.stopPropagation();open?closeD():openD();});
    document.addEventListener('click',function(e){if(open&&!t.contains(e.target)&&!d.contains(e.target))closeD();});
    s.addEventListener('input',function(){filter(this.value.toLowerCase());});
    l.querySelectorAll('.ksd-item').forEach(function(item){item.addEventListener('click',function(){
        h.value=this.dataset.value;lb.textContent=this.dataset.label;t.classList.add('ksd-has-value');
        l.querySelectorAll('.ksd-item').forEach(function(i){i.classList.remove('ksd-selected');});
        this.classList.add('ksd-selected');closeD();applyType(h.value);
    });});
    function filter(q){var any=false;l.querySelectorAll('.ksd-item').forEach(function(i){var m=!q||(i.dataset.search||'').indexOf(q)!==-1;i.style.display=m?'':'none';if(m)any=true;});em.style.display=any?'none':'';}
    applyType(h.value);
})();

// ── AI Suggest ───────────────────────────────────────────────────────────
(function(){
    var btn=document.getElementById('prdAiBtn'),lbl=document.getElementById('prdAiLabel');
    if(!btn) return;
    function getToken(){ return (document.querySelector('meta[name="csrf-token"]')||{content:''}).content||((document.querySelector('input[name="_token"]')||{value:''}).value); }
    function filesBase64(files,cb){ var out=[],done=0,total=Math.min(files.length,4); if(!total){cb([]);return;} for(var i=0;i<total;i++){(function(f){ var r=new FileReader(); r.onload=function(e){out.push({data:e.target.result.split(',')[1],type:f.type});if(++done===total)cb(out);}; r.readAsDataURL(f); })(files[i]);} }
    function hasFiles(){ var inp=document.getElementById('kapInput'); return inp&&inp.files&&inp.files.length>0; }
    function updateBtn(){ var ok=hasFiles(); btn.disabled=!ok; btn.style.opacity=ok?'1':'.4'; }
    var grid=document.getElementById('kapGrid');
    if(grid) new MutationObserver(updateBtn).observe(grid,{childList:true,subtree:true});
    updateBtn();
    btn.addEventListener('click',function(){
        if(btn.disabled) return;
        var inp=document.getElementById('kapInput');
        if(!inp||!inp.files.length){alert('Please select photos first.');return;}
        btn.disabled=true; lbl.textContent='Thinking…'; btn.style.opacity='.65';
        filesBase64(Array.from(inp.files),function(imgs){
            fetch('/dashboard/listings/ai-suggest-images',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':getToken()},body:JSON.stringify({images:imgs})})
            .then(function(r){return r.json();})
            .then(function(d){
                if(d.error){var _t=document.createElement('div');_t.textContent='AI suggestion unavailable: '+d.error;_t.className='ka-toast';document.body.appendChild(_t);setTimeout(function(){_t.remove();},3500);return;}
                var tf=document.getElementById('prdTitleField');
                if(tf&&d.title){tf.value=d.title;tf.style.background='#f0fdf4';setTimeout(function(){tf.style.background='';},1800);}
                if(d.description&&window._prdQuillDesc){window._prdQuillDesc.root.innerHTML=d.description;window._prdQuillDesc.root.style.background='#f0fdf4';setTimeout(function(){window._prdQuillDesc.root.style.background='';},1800);}
            })
            .catch(function(e){alert('Auto Suggestion failed: '+e.message);})
            .finally(function(){updateBtn();lbl.textContent='Auto Suggestion';});
        });
    });
})();

// ── Dirty guard ───────────────────────────────────────────────────────────
(function(){
    var dirty=false, form=document.getElementById('prdForm');
    if(form){
        form.addEventListener('input',function(){dirty=true;});
        form.addEventListener('change',function(){dirty=true;});
        form.addEventListener('submit',function(){dirty=false; var btn=document.getElementById('prdSubmitBtn'); if(btn){btn.disabled=true;btn.textContent='Saving… please wait';} });
        window.addEventListener('beforeunload',function(e){if(!dirty)return;e.preventDefault();e.returnValue='You have unsaved changes. Leave?';});
    }
})();
</script>
<script src="/js/category-fields.js?v=18"></script>
<script src="/js/quill.min.js"></script>
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded',function(){
    if(typeof Quill==='undefined') return;
    function getToken(){ return (document.querySelector('meta[name="csrf-token"]')||{content:''}).content||((document.querySelector('input[name="_token"]')||{value:''}).value); }
    var toolbar=[['bold','italic','underline'],[{'list':'bullet'}],['link'],['clean']];

    var hidden=document.getElementById('prdDescHidden');
    var quill=new Quill('#prdDescEditor',{theme:'snow',placeholder:'Describe condition, size, colour, material, warranty…',modules:{toolbar:toolbar}});
    window._prdQuillDesc=quill;
    if(hidden&&hidden.value.trim()) quill.root.innerHTML=hidden.value;

    var hiddenSi=document.getElementById('prdDescSiHidden');
    var hiddenTa=document.getElementById('prdDescTaHidden');
    var quillSi=null, quillTa=null;

    var form=document.getElementById('prdForm');
    if(form) form.addEventListener('submit',function(){
        if(hidden) hidden.value=quill.root.innerHTML==='<p><br></p>'?'':quill.root.innerHTML;
        if(hiddenSi&&quillSi) hiddenSi.value=quillSi.root.innerHTML==='<p><br></p>'?'':quillSi.root.innerHTML;
        if(hiddenTa&&quillTa) hiddenTa.value=quillTa.root.innerHTML==='<p><br></p>'?'':quillTa.root.innerHTML;
    },true);

    window.prdToggleLang=function(lang){
        var panel=document.getElementById(lang+'Panel');
        var arrow=document.getElementById(lang+'Arrow');
        var toggleBtn=document.getElementById(lang+'ToggleBtn');
        var translateBtn=document.getElementById('translate'+lang.charAt(0).toUpperCase()+lang.slice(1)+'Btn');
        var open=panel.style.display==='none'||panel.style.display==='';
        panel.style.display=open?'block':'none';
        translateBtn.style.display=open?'inline-flex':'none';
        arrow.textContent=open?'▼':'▶';
        toggleBtn.style.borderStyle=open?'solid':'dashed';
        toggleBtn.style.color=open?'#374151':'#64748b';
        if(open){
            if(lang==='si'&&!quillSi){ quillSi=new Quill('#prdDescSiEditor',{theme:'snow',placeholder:'සිංහල විස්තරය…',modules:{toolbar:toolbar}}); window._prdQuillSi=quillSi; if(hiddenSi&&hiddenSi.value.trim()) quillSi.clipboard.dangerouslyPasteHTML(hiddenSi.value); }
            if(lang==='ta'&&!quillTa){ quillTa=new Quill('#prdDescTaEditor',{theme:'snow',placeholder:'தமிழ் விளக்கம்…',modules:{toolbar:toolbar}}); window._prdQuillTa=quillTa; if(hiddenTa&&hiddenTa.value.trim()) quillTa.clipboard.dangerouslyPasteHTML(hiddenTa.value); }
        }
    };
    var siTgl=document.getElementById('siToggleBtn'); if(siTgl) siTgl.addEventListener('click',function(){ prdToggleLang('si'); });
    var taTgl=document.getElementById('taToggleBtn'); if(taTgl) taTgl.addEventListener('click',function(){ prdToggleLang('ta'); });

    (function(){
        var overlay=document.createElement('div');
        overlay.id='prdTranslateModal';
        overlay.style.cssText='display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;padding:16px';
        overlay.innerHTML='<div class="modal-card"><div class="modal-header"><div class="flex-row flex-row-8"><span class="fs-18">🇱🇰</span><span class="modal-title">Review Translation</span></div><button class="modal-close-btn" id="prdTmClose" type="button">&times;</button></div><div class="modal-body"><p class="form-label-sm">Translation Preview</p><div class="note-box" id="prdTmPreview"></div></div><div class="modal-footer"><button class="btn-cancel" id="prdTmReject" type="button">✗ Discard</button><button class="btn-submit-green" id="prdTmAccept" type="button">✓ Use This Translation</button></div></div>';
        document.body.appendChild(overlay);
        var pendingCallback=null;
        document.getElementById('prdTmAccept').addEventListener('click',function(){if(pendingCallback)pendingCallback();overlay.style.display='none';pendingCallback=null;});
        function closeModal(){overlay.style.display='none';pendingCallback=null;}
        document.getElementById('prdTmReject').addEventListener('click',closeModal);
        document.getElementById('prdTmClose').addEventListener('click',closeModal);
        overlay.addEventListener('click',function(e){if(e.target===overlay)closeModal();});
        window.prdShowTranslateConfirm=function(html,onAccept){ document.getElementById('prdTmPreview').innerHTML=html; pendingCallback=onAccept; overlay.style.display='flex'; };
    })();

    function doTranslate(language,btn,lbl,targetQuill){
        var langShort=language==='sinhala'?'si':'ta';
        var panel=document.getElementById(langShort+'Panel');
        if(panel&&(panel.style.display==='none'||panel.style.display==='')) prdToggleLang(langShort);
        var plain=quill.root.innerText.trim();
        if(!plain){alert('Please write the English description first.');return;}
        btn.disabled=true;lbl.textContent='Translating…';btn.style.opacity='.65';
        fetch('/dashboard/ai/translate-text',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':getToken()},body:JSON.stringify({text:plain,language:language})})
        .then(function(r){return r.json();})
        .then(function(d){
            if(d.error){var _t=document.createElement('div');_t.textContent='Translation unavailable: '+d.error;_t.className='ka-toast';document.body.appendChild(_t);setTimeout(function(){_t.remove();},3500);return;}
            prdShowTranslateConfirm(d.translated,function(){ targetQuill.clipboard.dangerouslyPasteHTML(d.translated); targetQuill.root.style.background='#f0fdf4'; setTimeout(function(){targetQuill.root.style.background='';},1800); });
        })
        .catch(function(e){alert('Translation error: '+e.message);})
        .finally(function(){btn.disabled=false;lbl.textContent=language==='sinhala'?'Translate to Sinhala':'Translate to Tamil';btn.style.opacity='1';});
    }

    var siBtn=document.getElementById('translateSiBtn'),siLbl=document.getElementById('siLabel');
    var taBtn=document.getElementById('translateTaBtn'),taLbl=document.getElementById('taLabel');
    if(siBtn) siBtn.addEventListener('click',function(){if(quillSi)doTranslate('sinhala',siBtn,siLbl,quillSi);});
    if(taBtn) taBtn.addEventListener('click',function(){if(quillTa)doTranslate('tamil',taBtn,taLbl,quillTa);});
});
</script>
@endpush
