@extends('layouts.dashboard')
@section('title','Post New Ad')
@section('eyebrow','Listings')
@section('heading','Post New Ad')

@section('actions')
<a href="/dashboard/listings" class="kdl-tb-btn kdl-tb-btn-light">
  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m7-7l-7 7 7 7"/></svg>
  My Listings
</a>
@endsection

@push('styles')
<link href="/css/quill.snow.css" rel="stylesheet">

@endpush

@section('content')

@if($errors->any())
<div class="alc-form-errors">
    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

@php
    $hasStore    = ($stores ?? collect())->isNotEmpty();
    $authUser    = auth()->user();
    $isPremium   = (bool) ($authUser->is_premium ?? false);
    $autoPhone   = $store->phone    ?? $authUser->phone   ?? $authUser->mobile   ?? '';
    $autoWa      = $store->whatsapp ?? $authUser->whatsapp ?? $autoPhone;
    $autoEmail   = $authUser->email ?? '';
    $autoAddress = $store->address  ?? $authUser->address ?? '';
    // Auto-location: match store city to locations list
    $autoLocId   = '';
    $autoLocLabel= '';
    if (!empty($store->city)) {
        $autoLocMatch = $locations->first(fn($l) => strcasecmp($l->name, $store->city) === 0)
                     ?? $locations->first(fn($l) => stripos($l->name, $store->city) !== false);
        if ($autoLocMatch) {
            $autoLocId    = $autoLocMatch->id;
            $autoLocParent= $autoLocMatch->parent_id ? $locations->firstWhere('id', $autoLocMatch->parent_id) : null;
            $autoLocLabel = $autoLocParent ? $autoLocParent->name.' — '.$autoLocMatch->name : $autoLocMatch->name.' (All)';
        }
    }
@endphp

<form method="POST" action="/dashboard/listings" enctype="multipart/form-data" id="alcForm">
@csrf
@if($hasStore)
  <input type="hidden" name="type" value="product">
@else
  <input type="hidden" name="type" value="classified">
@endif

<div class="alc-shell">

{{-- ── LEFT MAIN ────────────────────────────────────────────── --}}
<div>

    {{-- Store selector --}}
    @if($hasStore)
      @if(($stores ?? collect())->count() > 1)
        <div class="alc-card">
            <div class="alc-card-header">
                <div class="alc-card-icon teal">🏪</div>
                <div>
                    <p class="alc-card-title">Select Store</p>
                    <p class="alc-card-sub">Choose which store to post under</p>
                </div>
            </div>
            <div class="alc-field">
                <label class="alc-label">Post to Store <span class="alc-req">*</span></label>
                <select name="store_id" required id="storeSelect" class="k-hidden">
                    <option value="">Select a store…</option>
                    @foreach($stores as $s)
                        <option value="{{ $s->id }}" data-city="{{ $s->city ?? 'Kegalle' }}">{{ $s->name }} · {{ $s->city ?? 'Kegalle' }}</option>
                    @endforeach
                </select>
                <div class="ssp-wrap" id="sspWrap">
                    <div class="ssp-trigger" id="sspTrigger" tabindex="0">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <span id="sspLabel" class="ssp-placeholder">Search and select a store…</span>
                        <svg class="ssp-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </div>
                    <div class="ssp-dropdown k-hidden" id="sspDropdown">
                        <div class="ssp-search-row">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                            <input type="text" id="sspSearch" class="ssp-search-input" placeholder="Search stores…" autocomplete="off">
                        </div>
                        <div class="ssp-list" id="sspList">
                            @foreach($stores as $s)
                            <div class="ssp-item" data-value="{{ $s->id }}" data-label="{{ $s->name }} · {{ $s->city ?? 'Kegalle' }}" data-city="{{ $s->city ?? 'Kegalle' }}">
                                <div class="ssp-store-icon">🏪</div>
                                <div>
                                    <div class="ssp-item-name">{{ $s->name }}</div>
                                    <div class="ssp-item-city">📍 {{ $s->city ?? 'Kegalle' }}</div>
                                </div>
                            </div>
                            @endforeach
                            <div class="ssp-empty" id="sspEmpty">No stores match</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      @else
        <input type="hidden" name="store_id" value="{{ $store->id }}">
        <div class="alc-store-banner">
            <div class="alc-store-banner-icon">🏪</div>
            <div>
                <div class="alc-store-banner-name">{{ $store->name }}</div>
                <div class="alc-store-banner-city">📍 {{ $store->city ?? 'Kegalle' }} · Listing posted under your store</div>
            </div>
        </div>
      @endif
    @else
        <div class="alc-classified-banner">
            <div class="alc-classified-banner-icon">📋</div>
            <div>Posting as a <strong>Personal Classified Ad</strong>. <a href="/dashboard/stores/create">Create a store</a> to sell as a business.</div>
        </div>
    @endif

    {{-- ── STEP 1: Product Information ──────────────────────────── --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-step-badge">1</div>
            <div>
                <p class="alc-card-title">Product Information</p>
                <p class="alc-card-sub">Category, brand and product-specific fields</p>
            </div>
        </div>

        {{-- Category (searchable) --}}
        <div class="alc-field alc-field--mb">
            <label class="alc-label">Category <span class="alc-req">*</span></label>
            @include('partials.kcp-category-picker', [
                'kcpNs'         => 'kcp',
                'kcpHiddenId'   => 'ksd-cat-val',
                'kcpOldKey'     => 'category_id',
                'kcpCategories' => $categories,
                'kcpSelectedId' => old('category_id', ''),
                'kcpOnChange'   => 'var f=document.getElementById("cf-category-select");if(f){f.dataset.categoryId=id;f.dispatchEvent(new Event("change"));}if(window.dashLoadBrands)dashLoadBrands(id);',
            ])
        </div>

        {{-- Brand (dynamic, loads on category select) --}}
        <div class="alc-cat-brand">
            <div class="alc-field">
                <label class="alc-label">Brand</label>
                <input type="hidden" name="cf_brand_id" id="dash-brand-val" value="{{ old('cf_brand_id') }}">
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger" id="dashBrandTrigger">
                        <span class="ksd-trigger-text placeholder" id="dashBrandLabel">Select category first…</span>
                        <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="dashBrandDropdown">
                        <div class="ksd-search-row"><input type="text" class="ksd-search" id="dashBrandSearch" placeholder="Search brand…" autocomplete="off"></div>
                        <div class="ksd-list" id="dashBrandList">
                            <div class="ksd-empty" id="dashBrandEmpty">Select a category first…</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4 dynamic dropboxes (category-specific fields: Model, Condition, Color, etc.) --}}
        <div id="category-fields-container" class="alc-cf-container">
            <p class="alc-cat-fields-hint">
                Select a category above to load product-specific fields (Model, Condition, Colour, etc.)
            </p>
        </div>
    </div>

    {{-- ── STEP 2: Photos (up to 6) ─────────────────────────────── --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-step-badge">2</div>
            <div>
                <p class="alc-card-title">Add Photos</p>
                <p class="alc-card-sub">Up to 6 photos · main photo first · clear photos get more responses</p>
            </div>
        </div>
        @include('admin.partials.photo-uploader', ['maxSlots' => 6, 'existingImages' => collect()])
    </div>

    {{-- ── STEP 3: Auto Suggestion + Title + Description ───────────── --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-step-badge">3</div>
            <div>
                <p class="alc-card-title">Title &amp; Description</p>
                <p class="alc-card-sub">Use Auto Suggestion after uploading photos</p>
            </div>
        </div>

        {{-- AI Suggest --}}
        <div class="alc-ai-box">
            <div>
                <div class="alc-ai-box-label">✨ Auto Suggestion</div>
                <div class="alc-ai-box-sub">Upload photos above, then click to auto-fill title &amp; description using AI</div>
            </div>
            <button type="button" id="aiSuggestBtn" class="alc-ai-btn" disabled>
                ✨ <span id="aiSuggestLabel">Auto Suggest</span>
            </button>
        </div>

        {{-- Title --}}
        <div class="alc-field alc-field--mb">
            <label class="alc-label">Ad Title <span class="alc-req">*</span></label>
            <input name="title" required class="alc-input" value="{{ old('title') }}" id="aiTitleField" maxlength="180" placeholder="e.g. Honda CB125 2019 — Good Condition">
        </div>

        {{-- English Description --}}
        <div class="alc-field alc-field--mb">
            <label class="alc-label">🇬🇧 Description <span class="alc-req">*</span></label>
            <div id="descEditor"></div>
            <input type="hidden" name="description" id="descHidden" value="{{ old('description','') }}" class="k-hidden">
        </div>

        {{-- Sinhala --}}
        <div class="alc-lang-sep">
            <div class="alc-lang-row">
                <button type="button" id="siToggleBtn" class="alc-lang-toggle">
                    <span id="siArrow">▶</span> 🇱🇰 Add Sinhala Description <span class="alc-label-opt">(optional)</span>
                </button>
                <button type="button" id="translateSiBtn" class="alc-lang-translate alc-lang-translate--si">
                    🌐 <span id="siLabel">Translate to Sinhala</span>
                </button>
            </div>
            <div id="siPanel" class="alc-lang-panel">
                <label class="alc-label alc-lang-label">සිංහල විස්තරය</label>
                <div id="descSiEditor"></div>
                <input type="hidden" name="description_si" id="descSiHidden" value="{{ old('description_si','') }}" class="k-hidden">
            </div>
        </div>

        {{-- Tamil --}}
        <div class="alc-lang-sep">
            <div class="alc-lang-row">
                <button type="button" id="taToggleBtn" class="alc-lang-toggle">
                    <span id="taArrow">▶</span> 🇱🇰 Add Tamil Description <span class="alc-label-opt">(optional)</span>
                </button>
                <button type="button" id="translateTaBtn" class="alc-lang-translate alc-lang-translate--ta">
                    🌐 <span id="taLabel">Translate to Tamil</span>
                </button>
            </div>
            <div id="taPanel" class="alc-lang-panel">
                <label class="alc-label alc-lang-label">தமிழ் விளக்கம்</label>
                <div id="descTaEditor"></div>
                <input type="hidden" name="description_ta" id="descTaHidden" value="{{ old('description_ta','') }}" class="k-hidden">
            </div>
        </div>
    </div>

    {{-- ── STEP 4: Price & Rules ─────────────────────────────────── --}}
    @php $plVal=old('cf_price_label','fixed'); $plMap=['fixed'=>'Fixed Price','negotiable'=>'Negotiable','free'=>'Free / No Price','per_month'=>'Per Month','per_year'=>'Per Year']; @endphp
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-step-badge">4</div>
            <div>
                <p class="alc-card-title">Price &amp; Rules</p>
                <p class="alc-card-sub">Set pricing, ad type and location</p>
            </div>
        </div>

        <div class="alc-grid">
            <div class="alc-field {{ $plVal==='free'?'k-hidden':'' }}" id="alcPriceWrap">
                <label class="alc-label" id="alcPriceLabel">{{ $plVal==='per_month'?'Price / Month (LKR)':($plVal==='per_year'?'Price / Year (LKR)':'Price (LKR)') }}</label>
                <input name="price" type="text" inputmode="numeric" class="alc-input" id="alcPriceInput" value="{{ old('price') ? number_format((int)old('price')) : '' }}" placeholder="0" autocomplete="off">
            </div>
            <div class="alc-field">
                <label class="alc-label">Price Type</label>
                <input type="hidden" name="cf_price_label" id="alcPlVal" value="{{ $plVal }}">
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger ksd-has-value" id="alcPlTrigger">
                        <span class="ksd-trigger-text" id="alcPlLabel">{{ $plMap[$plVal]??'Fixed Price' }}</span>
                        <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="alcPlDropdown">
                        <div class="ksd-search-row"><input type="text" class="ksd-search" id="alcPlSearch" placeholder="Search…" autocomplete="off"></div>
                        <div class="ksd-list" id="alcPlList">
                            @foreach($plMap as $v=>$l)
                            <div class="ksd-item {{ $plVal===$v?'ksd-selected':'' }}" data-value="{{ $v }}" data-label="{{ $l }}" data-search="{{ strtolower($l) }}">{{ $l }}</div>
                            @endforeach
                            <div class="ksd-empty" id="alcPlEmpty">No match</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="alc-grid alc-grid--mt">
            <div class="alc-field">
                <label class="alc-label">Ad Type</label>
                <select name="ad_type" class="alc-select">
                    <option value="sale"     {{ old('ad_type','sale')==='sale'     ? 'selected':'' }}>For Sale</option>
                    <option value="rent"     {{ old('ad_type')==='rent'     ? 'selected':'' }}>For Rent</option>
                    <option value="wanted"   {{ old('ad_type')==='wanted'   ? 'selected':'' }}>Wanted</option>
                    <option value="free"     {{ old('ad_type')==='free'     ? 'selected':'' }}>Free</option>
                    <option value="exchange" {{ old('ad_type')==='exchange' ? 'selected':'' }}>Exchange</option>
                </select>
            </div>
            <div class="alc-field">
                <label class="alc-label">Location <span class="alc-req">*</span></label>
                @php
                    $selLocId = old('location_id', '');
                    $selLocLabel = '';
                    if (!$selLocId && !empty($store->city)) {
                        $autoLoc = $locations->first(fn($l) => strcasecmp($l->name, $store->city) === 0);
                        if (!$autoLoc) $autoLoc = $locations->first(fn($l) => stripos($l->name, $store->city) !== false);
                        if ($autoLoc) {
                            $selLocId = $autoLoc->id;
                            $selLocParent = $autoLoc->parent_id ? $locations->firstWhere('id', $autoLoc->parent_id) : null;
                            $selLocLabel = $selLocParent ? $selLocParent->name . ' — ' . $autoLoc->name : $autoLoc->name . ' (All)';
                        }
                    }
                @endphp
                <input type="hidden" name="location_id" id="ksd-loc-val" value="{{ $selLocId }}">
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger {{ $selLocId ? 'ksd-has-value' : '' }}" id="ksdLocTrigger">
                        <span class="ksd-trigger-text {{ $selLocId ? '' : 'placeholder' }}" id="ksdLocLabel">{{ $selLocLabel ?: '— Select Location —' }}</span>
                        <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="ksdLocDropdown">
                        <div class="ksd-search-row"><input type="text" class="ksd-search" id="ksdLocSearch" placeholder="Type to search…" autocomplete="off"></div>
                        <div class="ksd-list" id="ksdLocList">
                            <div class="ksd-item {{ !$selLocId ? 'ksd-selected' : '' }}" data-value="" data-label="— No Location —" data-search="" data-group="">— No Location —</div>
                            @foreach($locations->whereNull('parent_id') as $parentLoc)
                                <div class="ksd-group-label" data-group="{{ $parentLoc->id }}">{{ strtoupper($parentLoc->name) }}</div>
                                <div class="ksd-item ksd-indent {{ $selLocId==$parentLoc->id ? 'ksd-selected' : '' }}" data-value="{{ $parentLoc->id }}" data-label="{{ $parentLoc->name }} (All)" data-search="{{ strtolower($parentLoc->name) }}" data-group="{{ $parentLoc->id }}">📍 {{ $parentLoc->name }} (All)</div>
                                @foreach($locations->where('parent_id',$parentLoc->id) as $loc)
                                    <div class="ksd-item ksd-indent {{ $selLocId==$loc->id ? 'ksd-selected' : '' }}" data-value="{{ $loc->id }}" data-label="{{ $parentLoc->name }} — {{ $loc->name }}" data-search="{{ strtolower($parentLoc->name.' '.$loc->name) }}" data-group="{{ $parentLoc->id }}">{{ $loc->name }}</div>
                                @endforeach
                            @endforeach
                            <div class="ksd-empty" id="ksdLocEmpty">No locations match</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="alc-field alc-field--mt">
            <label class="alc-label">Area / Street <span class="alc-label-opt">(optional)</span>
                @if($autoAddress && !old('location'))
                    <span class="alc-autofill-badge">auto-filled</span>
                @endif
            </label>
            <input name="location" class="alc-input" value="{{ old('location', $autoAddress) }}" placeholder="e.g. Kegalle Town, Main Street">
        </div>
    </div>

    {{-- ── STEP 5: Upload More Images (Premium) ─────────────────── --}}
    <div class="alc-card alc-premium-card">
        <div class="alc-card-header">
            <div class="alc-step-badge alc-step-badge--gold">5</div>
            <div>
                <p class="alc-card-title">Upload More Images</p>
                <p class="alc-card-sub">Add up to 15 photos for maximum visibility</p>
            </div>
        </div>

        {{-- Dummy blurred slots preview --}}
        <div class="alc-dummy-slots">
            @for($i=0;$i<8;$i++)
            <div class="alc-dummy-slot">🖼</div>
            @endfor
        </div>

        @if(!$isPremium)
        <div class="alc-premium-overlay">
            <div class="alc-premium-badge">⭐ PREMIUM FEATURE</div>
            <div class="alc-premium-title">Upload up to 15 Photos</div>
            <div class="alc-premium-sub">Premium listings get 3× more views. Upgrade to add more images and boost your ad to the top.</div>
            <a href="/dashboard/membership" class="alc-premium-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                Upgrade to Premium
            </a>
        </div>
        @else
        {{-- Premium users: show extra upload slots 7–15 --}}
        <div class="alc-premium-active">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Premium active — upload up to 15 photos total
        </div>
        @include('admin.partials.photo-uploader', ['maxSlots' => 9, 'existingImages' => collect(), 'uploaderNs' => 'extra'])
        @endif
    </div>

    {{-- ── STEP 6: Contact Information ──────────────────────────── --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-step-badge">6</div>
            <div>
                <p class="alc-card-title">Contact Information</p>
                <p class="alc-card-sub">Auto-filled from your account — edit if needed</p>
            </div>
        </div>

        <div class="alc-contact-row">
            <div class="alc-field">
                <label class="alc-label">Phone Number <span class="alc-req">*</span></label>
                <input type="tel" name="contact_phone" id="contactPhone" class="alc-input"
                    value="{{ old('contact_phone', $autoPhone) }}"
                    placeholder="+94 7XX XXX XXX">
                @if($autoPhone)
                <div class="alc-contact-autofill">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    Auto-filled from your {{ $hasStore ? 'store' : 'account' }}
                </div>
                @endif
            </div>
            <div class="alc-field">
                <label class="alc-label">WhatsApp Number <span class="text-hint">(optional)</span></label>
                <input type="tel" name="contact_whatsapp" id="contactWhatsapp" class="alc-input"
                    value="{{ old('contact_whatsapp', $autoWa) }}"
                    placeholder="+94 7XX XXX XXX">
                <div class="alc-wa-row">
                    <input type="checkbox" name="show_whatsapp" id="showWa" value="1" {{ old('show_whatsapp','1')==='1'?'checked':'' }} class="alc-wa-check">
                    <label for="showWa" class="alc-wa-label">Show WhatsApp button on listing</label>
                </div>
            </div>
        </div>

        <div class="alc-field alc-field--mt">
            <label class="alc-label">Email Address <span class="alc-label-opt">(optional — not shown publicly)</span></label>
            <input type="email" name="contact_email" class="alc-input"
                value="{{ old('contact_email', $autoEmail) }}"
                placeholder="you@example.com">
            @if($autoEmail)
            <div class="alc-contact-autofill">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Auto-filled from your account
            </div>
            @endif
        </div>
    </div>

    {{-- Bottom Submit --}}
    <div class="alc-submit-row">
        <button type="submit" class="alc-publish-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
            Post Ad
        </button>
        <a href="/dashboard/listings" class="alc-cancel-btn">Cancel</a>
    </div>

</div>

{{-- ── RIGHT SIDEBAR ────────────────────────────────────────── --}}
<div>
    <div class="alc-card alc-sidebar-card">
        <div class="alc-card-header">
            <div class="alc-card-icon green">🚀</div>
            <div>
                <p class="alc-card-title">Submit Ad</p>
                <p class="alc-card-sub">Sent for admin approval</p>
            </div>
        </div>
        <button type="submit" class="alc-publish-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
            Post Ad
        </button>
        <a href="/dashboard/listings" class="alc-cancel-btn">Cancel</a>

        {{-- Checklist --}}
        <div class="alc-checklist">
            <p class="alc-chk-header">Checklist</p>
            <div id="alcChecklist" class="alc-chk-list">
                <div class="alc-chk" id="chk-cat" data-done="0">
                    <span class="alc-chk-icon">○</span>
                    <span>Category selected</span>
                </div>
                <div class="alc-chk" id="chk-photo" data-done="0">
                    <span class="alc-chk-icon">○</span>
                    <span>At least 1 photo</span>
                </div>
                <div class="alc-chk" id="chk-title" data-done="0">
                    <span class="alc-chk-icon">○</span>
                    <span>Title filled in</span>
                </div>
                <div class="alc-chk" id="chk-desc" data-done="0">
                    <span class="alc-chk-icon">○</span>
                    <span>Description written</span>
                </div>
                <div class="alc-chk" id="chk-price" data-done="0">
                    <span class="alc-chk-icon">○</span>
                    <span>Price set</span>
                </div>
                <div class="alc-chk" id="chk-loc" data-done="0">
                    <span class="alc-chk-icon">○</span>
                    <span>Location selected</span>
                </div>
                <div class="alc-chk" id="chk-contact" data-done="{{ $autoPhone ? '1' : '0' }}">
                    <span class="alc-chk-icon">{{ $autoPhone ? '✓' : '○' }}</span>
                    <span>Contact number</span>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</form>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
// ── Checklist live update ─────────────────────────────────────────────────
(function(){
    function setChk(id, done){
        var el = document.getElementById(id); if(!el) return;
        el.dataset.done = done ? '1' : '0';
        el.querySelector('.alc-chk-icon').textContent = done ? '✓' : '○';
    }
    // Category
    var catVal = document.getElementById('ksd-cat-val');
    if(catVal) new MutationObserver(function(){ setChk('chk-cat', !!catVal.value); }).observe(catVal, {attributes:true,attributeFilter:['value']});
    // Photo
    var kapGrid = document.getElementById('kapGrid');
    if(kapGrid) new MutationObserver(function(){ var inp=document.getElementById('kapInput'); setChk('chk-photo', inp&&inp.files&&inp.files.length>0); }).observe(kapGrid, {childList:true,subtree:true});
    // Title
    var titleField = document.getElementById('aiTitleField');
    if(titleField) titleField.addEventListener('input', function(){ setChk('chk-title', this.value.trim().length > 3); });
    // Price
    var priceInput = document.getElementById('alcPriceInput');
    var plVal = document.getElementById('alcPlVal');
    function checkPrice(){ setChk('chk-price', (plVal&&plVal.value==='free') || (priceInput&&priceInput.value.replace(/,/g,'')>0)); }
    if(priceInput) priceInput.addEventListener('input', checkPrice);
    // Location
    var locHidden = document.getElementById('ksd-loc-val');
    if(locHidden) new MutationObserver(function(){ setChk('chk-loc', !!locHidden.value); }).observe(locHidden, {attributes:true,attributeFilter:['value']});
    // Contact
    var phoneInput = document.getElementById('contactPhone');
    if(phoneInput){ phoneInput.addEventListener('input', function(){ setChk('chk-contact', this.value.trim().length > 6); }); setChk('chk-contact', phoneInput.value.trim().length > 6); }
    // Description — watch after Quill init
    window._alcDescChkTimer = setInterval(function(){
        if(window._quillDesc){ clearInterval(window._alcDescChkTimer);
            window._quillDesc.on('text-change', function(){ setChk('chk-desc', window._quillDesc.getText().trim().length > 10); });
        }
    }, 300);
    // Initial cat check
    if(catVal && catVal.value) setChk('chk-cat', true);
})();

// ── Brand loader via API ──────────────────────────────────────────────────
(function(){
    var trigger=document.getElementById('dashBrandTrigger'), dropdown=document.getElementById('dashBrandDropdown');
    var search=document.getElementById('dashBrandSearch'), list=document.getElementById('dashBrandList');
    var hidden=document.getElementById('dash-brand-val'), label=document.getElementById('dashBrandLabel');
    var emptyEl=document.getElementById('dashBrandEmpty');
    var open=false;
    if(!trigger) return;
    function openB(){ dropdown.classList.add('ksd-open'); trigger.classList.add('ksd-open'); search.value=''; filterB(''); search.focus(); open=true; }
    function closeB(){ dropdown.classList.remove('ksd-open'); trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeB():openB(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeB(); });
    search.addEventListener('input',function(){ filterB(this.value.toLowerCase()); });
    function selectBrand(val, lbl){
        hidden.value=val; label.textContent=lbl;
        label.classList.remove('placeholder'); trigger.classList.add('ksd-has-value');
        list.querySelectorAll('.ksd-item').forEach(function(i){i.classList.remove('ksd-selected');});
        var sel=list.querySelector('.ksd-item[data-value="'+val+'"]');
        if(sel) sel.classList.add('ksd-selected');
        closeB();
        var cfBrand=document.getElementById('cf-brand-select');
        if(cfBrand){ cfBrand.value=val; cfBrand.dispatchEvent(new Event('change')); }
    }
    function filterB(q){
        var items=list.querySelectorAll('.ksd-item'); var any=false;
        items.forEach(function(i){
            if(i.dataset.value==='other'){i.classList.remove('k-hidden');return;}
            var m=!q||i.dataset.search.indexOf(q)!==-1; i.classList.toggle('k-hidden',!m); if(m)any=true;
        });
        if(q&&!any){ selectBrand('other','— Other / Not Listed'); }
        emptyEl.style.display='none';
    }
    function renderBrands(brands){
        list.querySelectorAll('.ksd-item').forEach(function(i){i.remove();});
        var ot=document.createElement('div'); ot.className='ksd-item ksd-item--other';
        ot.dataset.value='other'; ot.dataset.search='other';
        ot.textContent='— Other / Not Listed';
        ot.addEventListener('click',function(){ selectBrand('other','— Other / Not Listed'); });
        list.insertBefore(ot,emptyEl);
        brands.forEach(function(b){
            var item=document.createElement('div'); item.className='ksd-item';
            item.dataset.value=b.id; item.dataset.search=b.name.toLowerCase(); item.textContent=b.name;
            item.addEventListener('click',function(){ selectBrand(this.dataset.value, this.textContent); });
            list.insertBefore(item,emptyEl);
        });
        emptyEl.classList.add('k-hidden');
    }
    window.dashLoadBrands=function(catId){
        if(!catId){ emptyEl.textContent='Select a category first…'; emptyEl.classList.remove('k-hidden'); list.querySelectorAll('.ksd-item').forEach(function(i){i.remove();}); return; }
        hidden.value=''; label.textContent='Loading…'; label.classList.add('placeholder'); trigger.classList.remove('ksd-has-value');
        fetch('/api/brands/by-category/'+catId)
            .then(function(r){ return r.json(); })
            .then(function(brands){ renderBrands(brands); })
            .catch(function(){ emptyEl.textContent='Failed to load'; emptyEl.classList.remove('k-hidden'); });
    };
})();

// ── Location ksd picker ───────────────────────────────────────────────────
(function(){
    var trigger=document.getElementById('ksdLocTrigger'), dropdown=document.getElementById('ksdLocDropdown');
    var search=document.getElementById('ksdLocSearch'), list=document.getElementById('ksdLocList');
    var hidden=document.getElementById('ksd-loc-val'), label=document.getElementById('ksdLocLabel');
    var emptyEl=document.getElementById('ksdLocEmpty');
    var items=list.querySelectorAll('.ksd-item'), groups=list.querySelectorAll('.ksd-group-label');
    var open=false;
    if(!trigger) return;
    function openD(){ dropdown.classList.add('ksd-open'); trigger.classList.add('ksd-open'); search.value=''; filterItems(''); search.focus(); open=true; }
    function closeD(){ dropdown.classList.remove('ksd-open'); trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    search.addEventListener('input',function(){ filterItems(this.value.toLowerCase()); });
    items.forEach(function(item){
        item.addEventListener('click',function(){
            var val=this.dataset.value, lbl=this.dataset.label;
            hidden.value=val; label.textContent=lbl||'— No Location —';
            label.classList.toggle('placeholder',!val); trigger.classList.toggle('ksd-has-value',!!val);
            items.forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected'); closeD();
            // Update checklist
            var chkLoc=document.getElementById('chk-loc');
            if(chkLoc){chkLoc.dataset.done=val?'1':'0';chkLoc.querySelector('.alc-chk-icon').textContent=val?'✓':'○';}
        });
    });
    function filterItems(q){
        var vis={};
        items.forEach(function(i){ var m=!q||i.dataset.search.indexOf(q)!==-1||(i.dataset.label||'').toLowerCase().indexOf(q)!==-1; i.classList.toggle('k-hidden',!m); if(m&&i.dataset.group) vis[i.dataset.group]=true; });
        groups.forEach(function(g){ g.classList.toggle('k-hidden',!(!q||vis[g.dataset.group])); });
        if(emptyEl) emptyEl.classList.toggle('k-hidden',Array.from(items).some(function(i){return !i.classList.contains('k-hidden');}));
    }
    var cur=hidden.value;
    if(cur){ var pre=Array.from(items).find(function(i){return i.dataset.value===cur;}); if(pre){label.textContent=pre.dataset.label;label.classList.remove('placeholder');trigger.classList.add('ksd-has-value');} }
})();

// ── Fake select for category-fields.js ───────────────────────────────────
(function(){
    var f=document.createElement('select'); f.id='cf-category-select'; f.className='no-select2'; f.style.display='none';
    f.setAttribute('data-cf-signal','1');
    var cur=document.getElementById('ksd-cat-val'); if(cur) f.value=cur.value;
    document.body.appendChild(f);
})();

// ── Price Type ksd ───────────────────────────────────────────────────────
(function(){
    var lblMap={'fixed':'Price (LKR)','negotiable':'Price (LKR)','free':'','per_month':'Price / Month (LKR)','per_year':'Price / Year (LKR)'};
    var t=document.getElementById('alcPlTrigger'),d=document.getElementById('alcPlDropdown'),s=document.getElementById('alcPlSearch');
    var l=document.getElementById('alcPlList'),h=document.getElementById('alcPlVal'),lb=document.getElementById('alcPlLabel');
    var em=document.getElementById('alcPlEmpty'),wrap=document.getElementById('alcPriceWrap'),pl=document.getElementById('alcPriceLabel'),inp=document.getElementById('alcPriceInput');
    if(inp){inp.addEventListener('input',function(){var p=this.selectionStart,raw=this.value.replace(/[^0-9]/g,''),fmt=raw?Number(raw).toLocaleString('en-US'):'';var diff=fmt.length-this.value.length;this.value=fmt;try{this.setSelectionRange(p+diff,p+diff);}catch(e){}});if(inp.value){var rv=inp.value.replace(/[^0-9]/g,'');if(rv)inp.value=Number(rv).toLocaleString('en-US');}}
    if(!t) return;
    var open=false;
    function applyType(v){if(v==='free'){wrap.classList.add('k-hidden');if(inp)inp.value='';}else{wrap.classList.remove('k-hidden');if(pl)pl.textContent=lblMap[v]||'Price (LKR)';}}
    function openD(){d.classList.add('ksd-open');t.classList.add('ksd-open');s.value='';filter('');s.focus();open=true;}
    function closeD(){d.classList.remove('ksd-open');t.classList.remove('ksd-open');open=false;}
    t.addEventListener('click',function(e){e.stopPropagation();open?closeD():openD();});
    document.addEventListener('click',function(e){if(open&&!t.contains(e.target)&&!d.contains(e.target))closeD();});
    s.addEventListener('input',function(){filter(this.value.toLowerCase());});
    l.querySelectorAll('.ksd-item').forEach(function(item){item.addEventListener('click',function(){
        h.value=this.dataset.value;lb.textContent=this.dataset.label;t.classList.add('ksd-has-value');
        l.querySelectorAll('.ksd-item').forEach(function(i){i.classList.remove('ksd-selected');});
        this.classList.add('ksd-selected');closeD();applyType(h.value);
    });});
    function filter(q){var any=false;l.querySelectorAll('.ksd-item').forEach(function(i){var m=!q||(i.dataset.search||'').indexOf(q)!==-1;i.classList.toggle('k-hidden',!m);if(m)any=true;});em.classList.toggle('k-hidden',any);}
    applyType(h.value);
})();

// ── AI Suggest (image-based) ──────────────────────────────────────────────
(function(){
    var btn=document.getElementById('aiSuggestBtn'),lbl=document.getElementById('aiSuggestLabel');
    if(!btn) return;
    function getToken(){ return (document.querySelector('meta[name="csrf-token"]')||{content:''}).content||((document.querySelector('input[name="_token"]')||{value:''}).value); }
    function filesBase64(files,cb){
        var out=[],done=0,total=Math.min(files.length,4);
        if(!total){cb([]);return;}
        for(var i=0;i<total;i++){(function(f){ var r=new FileReader(); r.onload=function(e){out.push({data:e.target.result.split(',')[1],type:f.type});if(++done===total)cb(out);}; r.readAsDataURL(f); })(files[i]);}
    }
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
                var tf=document.getElementById('aiTitleField');
                if(tf&&d.title){tf.value=d.title;tf.style.background='#f0fdf4';setTimeout(function(){tf.style.background='';},1800);
                    // checklist update
                    var chk=document.getElementById('chk-title');if(chk){chk.dataset.done='1';chk.querySelector('.alc-chk-icon').textContent='✓';}
                }
                if(d.description&&window._quillDesc){window._quillDesc.root.innerHTML=d.description;window._quillDesc.root.style.background='#f0fdf4';setTimeout(function(){window._quillDesc.root.style.background='';},1800);}
            })
            .catch(function(e){alert('Auto Suggestion failed: '+e.message);})
            .finally(function(){updateBtn();lbl.textContent='Auto Suggest';});
        });
    });
})();

// ── Searchable Store Picker ───────────────────────────────────────────────
(function(){
    var wrap=document.getElementById('sspWrap');
    var trigger=document.getElementById('sspTrigger');
    var dropdown=document.getElementById('sspDropdown');
    var search=document.getElementById('sspSearch');
    var list=document.getElementById('sspList');
    var empty=document.getElementById('sspEmpty');
    var label=document.getElementById('sspLabel');
    var sel=document.getElementById('storeSelect');
    if(!wrap||!sel) return;
    var items=Array.from(list.querySelectorAll('.ssp-item'));
    function open(){dropdown.classList.remove('k-hidden');trigger.classList.add('ssp-open');search.value='';filterItems('');setTimeout(function(){search.focus();},60);}
    function close(){dropdown.classList.add('k-hidden');trigger.classList.remove('ssp-open');}
    function selectItem(value,labelText){sel.value=value;sel.dispatchEvent(new Event('change'));label.textContent=labelText;label.className='ssp-selected-label';trigger.classList.add('ssp-selected');items.forEach(function(it){it.classList.toggle('ssp-active',it.dataset.value==value);});close();}
    function filterItems(q){q=q.toLowerCase().trim();var visible=0;items.forEach(function(it){var match=!q||it.dataset.label.toLowerCase().includes(q);it.classList.toggle('k-hidden',!match);if(match)visible++;});empty.classList.toggle('k-hidden',visible>0);}
    trigger.addEventListener('click',function(){dropdown.classList.contains('k-hidden')?open():close();});
    trigger.addEventListener('keydown',function(e){if(e.key==='Enter'||e.key===' '){e.preventDefault();open();}});
    search.addEventListener('input',function(){filterItems(this.value);});
    items.forEach(function(it){it.addEventListener('click',function(){selectItem(it.dataset.value,it.dataset.label);});});
    document.addEventListener('mousedown',function(e){if(wrap&&!wrap.contains(e.target))close();});
})();
</script>
<script src="/js/category-fields.js?v=18"></script>
<script src="/js/quill.min.js"></script>
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded',function(){
    if(typeof Quill==='undefined') return;
    function getToken(){ return (document.querySelector('meta[name="csrf-token"]')||{content:''}).content||((document.querySelector('input[name="_token"]')||{value:''}).value); }
    var toolbar=[['bold','italic','underline'],[{'list':'bullet'}],['link'],['clean']];

    var hidden=document.getElementById('descHidden');
    var quill=new Quill('#descEditor',{theme:'snow',placeholder:'Describe condition, size, colour, reason for selling…',modules:{toolbar:toolbar}});
    window._quillDesc=quill;
    if(hidden&&hidden.value.trim()) quill.root.innerHTML=hidden.value;

    var hiddenSi=document.getElementById('descSiHidden');
    var hiddenTa=document.getElementById('descTaHidden');
    var quillSi=null, quillTa=null;

    var form=document.getElementById('alcForm');
    if(form) form.addEventListener('submit',function(){
        if(hidden) hidden.value=quill.root.innerHTML==='<p><br></p>'?'':quill.root.innerHTML;
        if(hiddenSi&&quillSi) hiddenSi.value=quillSi.root.innerHTML==='<p><br></p>'?'':quillSi.root.innerHTML;
        if(hiddenTa&&quillTa) hiddenTa.value=quillTa.root.innerHTML==='<p><br></p>'?'':quillTa.root.innerHTML;
    },true);

    function toggleLang(lang){
        var panel=document.getElementById(lang+'Panel');
        var arrow=document.getElementById(lang+'Arrow');
        var toggleBtn=document.getElementById(lang+'ToggleBtn');
        var translateBtn=document.getElementById('translate'+lang.charAt(0).toUpperCase()+lang.slice(1)+'Btn');
        var opening=!panel.classList.contains('open');
        panel.classList.toggle('open',opening);
        translateBtn.classList.toggle('open',opening);
        toggleBtn.classList.toggle('open',opening);
        arrow.textContent=opening?'▼':'▶';
        if(opening){
            if(lang==='si'&&!quillSi){
                quillSi=new Quill('#descSiEditor',{theme:'snow',placeholder:'සිංහල විස්තරය…',modules:{toolbar:toolbar}});
                window._quillSi=quillSi;
                if(hiddenSi&&hiddenSi.value.trim()) quillSi.clipboard.dangerouslyPasteHTML(hiddenSi.value);
            }
            if(lang==='ta'&&!quillTa){
                quillTa=new Quill('#descTaEditor',{theme:'snow',placeholder:'தமிழ் விளக்கம்…',modules:{toolbar:toolbar}});
                window._quillTa=quillTa;
                if(hiddenTa&&hiddenTa.value.trim()) quillTa.clipboard.dangerouslyPasteHTML(hiddenTa.value);
            }
        }
    };

    // Translation modal
    (function(){
        var overlay=document.createElement('div');
        overlay.id='translateModal';
        overlay.style.cssText='display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;padding:16px';
        overlay.innerHTML='<div class="modal-card"><div class="modal-header"><div class="flex-row flex-row-8"><span id="tmModalIcon" class="fs-18"></span><span class="modal-title">Review Translation</span></div><button class="modal-close-btn" id="tmClose" type="button">&times;</button></div><div class="modal-body"><p class="form-label-sm">Translation Preview</p><div class="note-box" id="tmPreview"></div></div><div class="modal-footer"><button class="btn-cancel" id="tmReject" type="button">✗ Discard</button><button class="btn-submit-green" id="tmAccept" type="button">✓ Use This Translation</button></div></div>';
        document.body.appendChild(overlay);
        var pendingCallback=null;
        document.getElementById('tmAccept').addEventListener('click',function(){if(pendingCallback)pendingCallback();overlay.style.display='none';pendingCallback=null;});
        function closeModal(){overlay.style.display='none';pendingCallback=null;}
        document.getElementById('tmReject').addEventListener('click',closeModal);
        document.getElementById('tmClose').addEventListener('click',closeModal);
        overlay.addEventListener('click',function(e){if(e.target===overlay)closeModal();});
        window.showTranslateConfirm=function(html,langName,icon,onAccept){
            document.getElementById('tmModalIcon').textContent=icon;
            document.getElementById('tmPreview').innerHTML=html;
            pendingCallback=onAccept;
            overlay.style.display='flex';
        };
    })();

    function doTranslate(language,btn,lbl,targetQuill){
        var langShort=language==='sinhala'?'si':'ta';
        var panel=document.getElementById(langShort+'Panel');
        if(panel&&!panel.classList.contains('open')) toggleLang(langShort);
        var plain=quill.root.innerText.trim();
        if(!plain){alert('Please write the English description first.');return;}
        btn.disabled=true;lbl.textContent='Translating…';btn.style.opacity='.65';
        fetch('/dashboard/ai/translate-text',{
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':getToken()},
            body:JSON.stringify({text:plain,language:language})
        })
        .then(function(r){return r.json();})
        .then(function(d){
            if(d.error){var _t=document.createElement('div');_t.textContent='Translation unavailable: '+d.error;_t.className='ka-toast';document.body.appendChild(_t);setTimeout(function(){_t.remove();},3500);return;}
            var langLabel=language==='sinhala'?'Sinhala':'Tamil';
            showTranslateConfirm(d.translated,langLabel,'🇱🇰',function(){
                targetQuill.clipboard.dangerouslyPasteHTML(d.translated);
                targetQuill.root.style.background='#f0fdf4';
                setTimeout(function(){targetQuill.root.style.background='';},1800);
            });
        })
        .catch(function(e){alert('Translation error: '+e.message);})
        .finally(function(){btn.disabled=false;lbl.textContent=language==='sinhala'?'Translate to Sinhala':'Translate to Tamil';btn.style.opacity='1';});
    }

    var siToggle=document.getElementById('siToggleBtn');
    var taToggle=document.getElementById('taToggleBtn');
    if(siToggle) siToggle.addEventListener('click',function(){toggleLang('si');});
    if(taToggle) taToggle.addEventListener('click',function(){toggleLang('ta');});

    var siBtn=document.getElementById('translateSiBtn'),siLbl=document.getElementById('siLabel');
    var taBtn=document.getElementById('translateTaBtn'),taLbl=document.getElementById('taLabel');
    if(siBtn) siBtn.addEventListener('click',function(){if(quillSi)doTranslate('sinhala',siBtn,siLbl,quillSi);});
    if(taBtn) taBtn.addEventListener('click',function(){if(quillTa)doTranslate('tamil',taBtn,taLbl,quillTa);});
});
</script>
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter') return;
    var t = e.target;
    if (t.tagName === 'INPUT' && t.type !== 'submit' && t.type !== 'button') {
        var allowed = ['aiTitleField', 'alcPriceInput', 'contactPhone', 'contactWhatsapp'];
        if (allowed.indexOf(t.id) === -1) { e.preventDefault(); }
    }
}, true);

(function(){
    var form = document.getElementById('alcForm');
    if (!form) return;
    function showErr(el, msg) {
        el.classList.add('k-input-error');
        var existing = el.parentElement.querySelector('.alc-inline-err');
        if (!existing) { var err = document.createElement('div'); err.className = 'alc-inline-err'; err.textContent = msg; el.parentElement.appendChild(err); }
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    function clearErr(el) {
        el.classList.remove('k-input-error');
        var err = el.parentElement.querySelector('.alc-inline-err'); if (err) err.remove();
    }
    form.addEventListener('submit', function(e) {
        var ok = true;
        var locHidden = document.getElementById('ksd-loc-val');
        var locTrigger = document.getElementById('ksdLocTrigger');
        if (locHidden && !locHidden.value.trim()) {
            e.preventDefault(); ok = false;
            if (locTrigger) showErr(locTrigger, 'Please select a location.');
        } else if (locTrigger) { clearErr(locTrigger); }
        var plVal = document.getElementById('alcPlVal');
        var priceInput = document.getElementById('alcPriceInput');
        if (priceInput) priceInput.value = priceInput.value.replace(/,/g, '');
        var priceType = plVal ? plVal.value : 'fixed';
        if (priceType !== 'free') {
            if (!priceInput || !priceInput.value || Number(priceInput.value) < 0) {
                if (!e.defaultPrevented) e.preventDefault();
                ok = false;
                if (priceInput) showErr(priceInput, 'Please enter a price (or set type to Free).');
            } else if (priceInput) { clearErr(priceInput); }
        }
        var phoneInput = document.getElementById('contactPhone');
        if (phoneInput && !phoneInput.value.trim()) {
            if (!e.defaultPrevented) e.preventDefault();
            ok = false;
            showErr(phoneInput, 'Please enter a contact phone number.');
        } else if (phoneInput) { clearErr(phoneInput); }
        // Condition/description mismatch warning
        var condField = document.querySelector('select[name="condition"], input[name="condition"]');
        var descText = (window._quillDesc ? window._quillDesc.getText() : (document.getElementById('descHidden') ? document.getElementById('descHidden').value : '')).toLowerCase();
        if (condField && /new|brand.?new/i.test(condField.value) && /\bused\b|\bsecond.?hand\b|\bpre.?owned\b|\bold\b/i.test(descText)) {
            var warn = document.getElementById('kCondWarn');
            if (!warn) {
                warn = document.createElement('p');
                warn.id = 'kCondWarn';
                warn.style.cssText = 'color:#b45309;background:#fffbeb;border:1px solid #fcd34d;border-radius:6px;padding:8px 12px;font-size:13px;margin-top:8px';
                warn.textContent = '⚠️ Your condition is set to "New" but the description mentions "used". Please review before submitting.';
                condField.closest('.alc-field') && condField.closest('.alc-field').appendChild(warn);
            }
            if (!e.defaultPrevented) e.preventDefault();
            ok = false;
        }
        return ok;
    }, false);
})();
</script>
@endpush
