@extends('layouts.admin')
@section('title','Edit Listing')
@section('page','Products / Ads')
@section('heading','Edit Listing')
@section('subheading','Update listing details, assign user, category, location and status')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/listings">&#8592; Back</a>@endsection

@push('styles')
<link href="/css/quill.snow.css" rel="stylesheet">

@endpush

@section('content')

@if($errors->any())
<div class="alert-error">
    <ul class="list-pl">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="post" action="/admin/listings/{{ $listing->id }}" enctype="multipart/form-data" id="alcForm">
@csrf
@method('PUT')

<div class="alc-shell">

{{-- -- LEFT MAIN ---------------------------------------------- --}}
<div>

    {{-- Ad Details: Photos ? Auto Suggestion ? fields --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg></div>
            <div>
                <p class="alc-card-title">Ad Details</p>
                <p class="alc-card-sub">Photos, Auto Suggestion, and listing info</p>
            </div>
        </div>
        <div class="alc-field mb-14">
            <label class="alc-label">Photos <span class="text-hint">(up to 6 — main photo first)</span></label>
            @include('admin.partials.photo-uploader', ['maxSlots' => 6, 'existingImages' => $listing->images])
        </div>
        <div class="mb-16">
            <button class="btn-purple-sm" type="button" id="aiSuggestBtn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5z"/></svg> <span id="aiSuggestLabel">Auto Suggestion</span>
            </button>
            <p class="alc-hint mt-5">Click to auto-fill title &amp; description from the photos above</p>
        </div>

        @php $oldUserId=old('user_id',$listing->user_id??''); $oldStoreId=old('store_id',$listing->store_id??''); @endphp
        <div class="alc-grid mb-14">
            {{-- Post As User ksd --}}
            <div class="alc-field">
                <label class="alc-label">Post As User <span class="alc-req">*</span></label>
                <input type="hidden" name="user_id" id="alcUserVal" value="{{ $oldUserId }}" required>
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger {{ $oldUserId ? 'ksd-has-value':'' }}" id="alcUserTrigger">
                        <span class="ksd-trigger-text {{ $oldUserId ? '':'placeholder' }}" id="alcUserLabel">Select user…</span>
                        <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="alcUserDropdown">
                        <div class="ksd-search-row"><input type="text" class="ksd-search" id="alcUserSearch" placeholder="Search user�" autocomplete="off"></div>
                        <div class="ksd-list" id="alcUserList">
                            <div class="ksd-item {{ !$oldUserId?'ksd-selected':'' }}" data-value="" data-label="Select user…" data-search="">— No User —</div>
                            @foreach($users as $user)
                            <div class="ksd-item {{ $oldUserId==$user->id?'ksd-selected':'' }}" data-value="{{ $user->id }}" data-label="{{ $user->name }} � {{ $user->email }}" data-search="{{ strtolower($user->name.' '.$user->email) }}">{{ $user->name }} � {{ $user->email }}</div>
                            @endforeach
                            <div class="ksd-empty" id="alcUserEmpty" class="hidden">No users match</div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Store ksd --}}
            <div class="alc-field">
                <label class="alc-label">Store</label>
                <input type="hidden" name="store_id" id="alcStoreVal" value="{{ $oldStoreId }}">
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger {{ $oldStoreId?'ksd-has-value':'' }}" id="alcStoreTrigger">
                        <span class="ksd-trigger-text {{ $oldStoreId?'':'placeholder' }}" id="alcStoreLabel">No Store</span>
                        <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="alcStoreDropdown">
                        <div class="ksd-search-row"><input type="text" class="ksd-search" id="alcStoreSearch" placeholder="Search store�" autocomplete="off"></div>
                        <div class="ksd-list" id="alcStoreList">
                            <div class="ksd-empty" id="alcStoreEmpty">Select a user first�</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="alc-field alc-full mb-14">
            <label class="alc-label">Listing Title <span class="alc-req">*</span></label>
            <input name="title" id="aiTitleField" required class="alc-input" value="{{ old('title',$listing->title??'') }}" placeholder="e.g. Honda CB125 2019 — Excellent Condition">
        </div>

        <div class="alc-field alc-full">
            <label class="alc-label">Slug URL</label>
            <input name="slug" class="alc-input" value="{{ old('slug',$listing->slug??'') }}" placeholder="auto-generated-if-empty">
            <span class="alc-hint">Leave blank to auto-generate from title.</span>
        </div>
    </div>

    {{-- Category + Brand --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg></div>
            <div>
                <p class="alc-card-title">Category &amp; Brand</p>
                <p class="alc-card-sub">Browse or search the hierarchy</p>
            </div>
        </div>

        <div class="alc-cat-brand">
            <div class="alc-field">
                <label class="alc-label">Category</label>
                @include('partials.kcp-category-picker', [
                    'kcpNs'         => 'kcp',
                    'kcpHiddenId'   => 'ksd-cat-val',
                    'kcpCategories' => $categories,
                    'kcpSelectedId' => old('category_id',$listing->category_id??''),
                    'kcpOnChange'   => 'var fs=document.getElementById("cf-category-select");if(fs){fs.value=id;fs.dispatchEvent(new Event("change"));}if(window.loadKsdBrands)loadKsdBrands(id);',
                ])
            </div>
            <div class="alc-field">
                <label class="alc-label">Brand</label>
                @php $existBrandId=$listing->values->first(fn($v)=>$v->field&&$v->field->name==='brand_id')?->value??''; @endphp
                <input type="hidden" name="cf_brand_id" id="ksd-brand-val" value="{{ $existBrandId }}">
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger {{ $existBrandId?'ksd-has-value':'' }}" id="ksdBrandTrigger">
                        <span class="ksd-trigger-text {{ $existBrandId?'':'placeholder' }}" id="ksdBrandLabel">{{ $existBrandId?'Loading�':'Select Brand�' }}</span>
                        <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="ksdBrandDropdown">
                        <div class="ksd-search-row"><input type="text" class="ksd-search" id="ksdBrandSearch" placeholder="Search brand�" autocomplete="off"></div>
                        <div class="ksd-list csp5-315" id="ksdBrandList">
                            <div class="ksd-empty" id="ksdBrandEmpty">Select a category first�</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="category-fields-container" class="mt-14"></div>
    </div>

    {{-- Location --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
            <div>
                <p class="alc-card-title">Location</p>
                <p class="alc-card-sub">Where the item is based</p>
            </div>
        </div>
        @php
            $selLocId=old('location_id',$listing->location_id??'');
            $selLocLabel='';
            if($selLocId){ $selLoc=$locations->firstWhere('id',$selLocId); if($selLoc){ if($selLoc->parent_id){ $selLocParent=$locations->firstWhere('id',$selLoc->parent_id); $selLocLabel=($selLocParent?$selLocParent->name.' � ':'').$selLoc->name; } else { $selLocLabel=$selLoc->name.' (All)'; } } }
        @endphp
        <div class="alc-grid">
            <div class="alc-field">
                <label class="alc-label">Location</label>
                <input type="hidden" name="location_id" id="ksd-loc-val" value="{{ $selLocId }}">
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger {{ $selLocLabel?'ksd-has-value':'' }}" id="ksdLocTrigger">
                        <span class="ksd-trigger-text {{ $selLocLabel?'':'placeholder' }}" id="ksdLocLabel">{{ $selLocLabel?:'� No Location �' }}</span>
                        <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="ksdLocDropdown" class="hidden">
                        <div class="ksd-search-row"><input type="text" class="ksd-search" id="ksdLocSearch" placeholder="Type to search�" autocomplete="off"></div>
                        <div class="ksd-list" id="ksdLocList">
                            <div class="ksd-item {{ !$selLocId?'ksd-selected':'' }}" data-value="" data-label="� No Location �" data-search="" data-group="">� No Location �</div>
                            @foreach($locations->whereNull('parent_id') as $parentLoc)
                                <div class="ksd-group-label" data-group="{{ $parentLoc->id }}">{{ strtoupper($parentLoc->name) }}</div>
                                <div class="ksd-item ksd-indent {{ $selLocId==$parentLoc->id?'ksd-selected':'' }}" data-value="{{ $parentLoc->id }}" data-label="{{ $parentLoc->name }} (All)" data-search="{{ strtolower($parentLoc->name) }}" data-group="{{ $parentLoc->id }}">📍 {{ $parentLoc->name }} (All)</div>
                                @foreach($locations->where('parent_id',$parentLoc->id) as $loc)
                                    <div class="ksd-item ksd-indent {{ $selLocId==$loc->id?'ksd-selected':'' }}" data-value="{{ $loc->id }}" data-label="{{ $parentLoc->name }} � {{ $loc->name }}" data-search="{{ strtolower($parentLoc->name.' '.$loc->name) }}" data-group="{{ $parentLoc->id }}">{{ $loc->name }}</div>
                                @endforeach
                            @endforeach
                            <div class="ksd-empty" id="ksdLocEmpty" class="hidden">No locations match</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alc-field">
                <label class="alc-label">Manual Location Text</label>
                <input name="location" class="alc-input" value="{{ old('location',$listing->location??'') }}" placeholder="e.g. Kegalle Town">
            </div>
        </div>
    </div>

    {{-- Description --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon slate"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
            <div>
                <p class="alc-card-title">Description</p>
                <p class="alc-card-sub">Describe the item in detail</p>
            </div>
        </div>
        <div class="alc-field">
            <div id="descEditor"></div>
            <input type="hidden" name="description" id="descHidden" value="{{ old('description',$listing->description??'') }}" class="hidden">
        </div>
    </div>

    {{-- Pricing --}}
    @php $plVal=old('cf_price_label',$listing->values->first(fn($v)=>optional($v->field)->name==='price_label')?->value??'fixed'); $plMap=['fixed'=>'Fixed Price','negotiable'=>'Negotiable','free'=>'Free / No Price','per_month'=>'Per Month','per_year'=>'Per Year']; @endphp
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
            <div>
                <p class="alc-card-title">Pricing</p>
                <p class="alc-card-sub">Set price type and amount</p>
            </div>
        </div>
        <div class="alc-grid">
            <div class="alc-field" id="alcPriceWrap" @class(['hidden' => $plVal === 'free'])>
                <label class="alc-label" id="alcPriceLabel">{{ $plVal==='per_month'?'Price / Month (LKR)':($plVal==='per_year'?'Price / Year (LKR)':'Price (LKR)') }}</label>
                <input name="price" type="number" step="1" min="0" class="alc-input" id="alcPriceInput" value="{{ old('price',isset($listing->price)&&$listing->price?(int)$listing->price:'') }}" placeholder="0">
            </div>
            <div class="alc-field">
                    <label class="alc-label">Price Type</label>
                    <input type="hidden" name="cf_price_label" id="alcPlVal" value="{{ $plVal }}">
                    <div class="ksd-wrap">
                        <button type="button" class="ksd-trigger ksd-has-value" id="alcPlTrigger">
                            <span class="ksd-trigger-text" id="alcPlLabel">{{ $plMap[$plVal]??'Fixed Price' }}</span>
                            <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div class="ksd-dropdown" id="alcPlDropdown" class="hidden">
                            <div class="ksd-search-row"><input type="text" class="ksd-search" id="alcPlSearch" placeholder="Search…" autocomplete="off"></div>
                            <div class="ksd-list" id="alcPlList">
                                @foreach($plMap as $v=>$l)
                                <div class="ksd-item {{ $plVal===$v?'ksd-selected':'' }}" data-value="{{ $v }}" data-label="{{ $l }}" data-search="{{ strtolower($l) }}">{{ $l }}</div>
                                @endforeach
                                <div class="ksd-empty" id="alcPlEmpty" class="hidden">No match</div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

</div>

{{-- -- RIGHT SIDEBAR ------------------------------------------ --}}
<div>

    {{-- Publish --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon green">??</div>
            <div>
                <p class="alc-card-title">Save Changes</p>
                <p class="alc-card-sub">Update listing on marketplace</p>
            </div>
        </div>
        <button type="submit" class="alc-publish-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Save Changes
        </button>
        <a href="/admin/listings" class="alc-cancel-btn">Cancel</a>
    </div>

    {{-- Type & Status --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon purple"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg></div>
            <div>
                <p class="alc-card-title">Type &amp; Status</p>
                <p class="alc-card-sub">Listing classification</p>
            </div>
        </div>
        <div class="alc-field mb-12">
            <label class="alc-label">Type <span class="alc-req">*</span></label>
            @php $alcTypeVal=old('type',$listing->type??'product'); $alcTypeMap=['product'=>'Product','classified'=>'Classified','buy'=>'Wanted / Buy','exchange'=>'Exchange','job'=>'Job','to-let'=>'To-Let']; @endphp
            <input type="hidden" name="type" id="alcTypeVal" value="{{ $alcTypeVal }}" required>
            <div class="ksd-wrap">
                <button type="button" class="ksd-trigger ksd-has-value" id="alcTypeTrigger">
                    <span class="ksd-trigger-text" id="alcTypeLabel">{{ $alcTypeMap[$alcTypeVal]??'Product' }}</span>
                    <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="ksd-dropdown" id="alcTypeDropdown">
                    <div class="ksd-search-row"><input type="text" class="ksd-search" id="alcTypeSearch" placeholder="Search type…" autocomplete="off"></div>
                    <div class="ksd-list" id="alcTypeList">
                        @foreach($alcTypeMap as $v=>$l)
                        <div class="ksd-item {{ $alcTypeVal===$v?'ksd-selected':'' }}" data-value="{{ $v }}" data-label="{{ $l }}" data-search="{{ strtolower($l) }}">{{ $l }}</div>
                        @endforeach
                        <div class="ksd-empty" id="alcTypeEmpty" class="hidden">No match</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="alc-field">
            <label class="alc-label">Status <span class="alc-req">*</span></label>
            @php $alcStatVal=old('status',$listing->status??'approved'); $alcStatMap=['approved'=>'Approved','pending'=>'Pending','rejected'=>'Rejected','suspended'=>'Suspended']; @endphp
            <input type="hidden" name="status" id="alcStatVal" value="{{ $alcStatVal }}" required>
            <div class="ksd-wrap">
                <button type="button" class="ksd-trigger ksd-has-value" id="alcStatTrigger">
                    <span class="ksd-trigger-text" id="alcStatLabel">{{ $alcStatMap[$alcStatVal]??'? Approved' }}</span>
                    <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="ksd-dropdown" id="alcStatDropdown">
                    <div class="ksd-search-row"><input type="text" class="ksd-search" id="alcStatSearch" placeholder="Search status…" autocomplete="off"></div>
                    <div class="ksd-list" id="alcStatList">
                        @foreach($alcStatMap as $v=>$l)
                        <div class="ksd-item {{ $alcStatVal===$v?'ksd-selected':'' }}" data-value="{{ $v }}" data-label="{{ $l }}" data-search="{{ strtolower($v) }}">{{ $l }}</div>
                        @endforeach
                        <div class="ksd-empty" id="alcStatEmpty" class="hidden">No match</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Boost Flags --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div>
            <div>
                <p class="alc-card-title">Boost Flags</p>
                <p class="alc-card-sub">Visibility options</p>
            </div>
        </div>
        <div class="alc-checks">
            <label class="alc-check">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured',$listing->is_featured??false))>
                ★ Featured Ad
            </label>
            <label class="alc-check">
                <input type="checkbox" name="is_top" value="1" @checked(old('is_top',$listing->is_top??false))>
                ⬆ Top Ad
            </label>
        </div>
    </div>

</div>
</div>
</form>

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
window.listingVariants = {!! json_encode($listing->variants->mapWithKeys(fn($v)=>[$v->name=>$v->price])->toArray(),JSON_HEX_TAG|JSON_HEX_APOS) !!};
window.cfExistingValues = {!! json_encode(
    array_merge(
        $listing->condition?['cf_condition'=>$listing->condition]:[],
        $listing->values->filter(fn($v)=>$v->field!==null)->mapWithKeys(fn($v)=>['cf_'.$v->field->name=>$v->value])->toArray()
    ),JSON_HEX_TAG|JSON_HEX_APOS
) !!};

// -- Generic ksd initializer ------------------------------------------------
function initKsd(opts) {
    var trigger=document.getElementById(opts.triggerId), dropdown=document.getElementById(opts.dropdownId);
    var search=document.getElementById(opts.searchId), list=document.getElementById(opts.listId);
    var hidden=document.getElementById(opts.hiddenId), label=document.getElementById(opts.labelId);
    var emptyEl=document.getElementById(opts.emptyId);
    var items=list.querySelectorAll('.ksd-item'), groups=list.querySelectorAll('.ksd-group-label'), open=false;
    function openDropdown(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); search.value=''; filterItems(''); search.focus(); open=true; }
    function closeDropdown(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeDropdown():openDropdown(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeDropdown(); });
    search.addEventListener('input',function(){ filterItems(this.value.toLowerCase()); });
    items.forEach(function(item){
        item.addEventListener('click',function(){
            var val=this.dataset.value, lbl=this.dataset.label;
            hidden.value=val; label.textContent=lbl||opts.placeholder;
            label.classList.toggle('placeholder',!val); trigger.classList.toggle('ksd-has-value',!!val);
            items.forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected');
            closeDropdown(); if(opts.onChange) opts.onChange(val);
        });
    });
    function filterItems(q){
        var vis={};
        items.forEach(function(i){ var m=!q||i.dataset.search.indexOf(q)!==-1||(i.dataset.label||'').toLowerCase().indexOf(q)!==-1; i.style.display=m?'':'none'; if(m&&i.dataset.group) vis[i.dataset.group]=true; });
        groups.forEach(function(g){ g.style.display=(!q||vis[g.dataset.group])?'':'none'; });
        var any=Array.from(items).some(function(i){return i.style.display!=='none';}); if(emptyEl) emptyEl.style.display=any?'none':'';
    }
    // Pre-fill label
    var cur=hidden.value;
    if(cur){ var pre=Array.from(items).find(function(i){return i.dataset.value===cur;}); if(pre){label.textContent=pre.dataset.label||pre.textContent;label.classList.remove('placeholder');trigger.classList.add('ksd-has-value');} }
}

// -- Brand ksd dropdown (dynamic API) -------------------------------------
(function(){
    var trigger=document.getElementById('ksdBrandTrigger'), dropdown=document.getElementById('ksdBrandDropdown');
    var search=document.getElementById('ksdBrandSearch'), list=document.getElementById('ksdBrandList');
    var hidden=document.getElementById('ksd-brand-val'), label=document.getElementById('ksdBrandLabel');
    var emptyEl=document.getElementById('ksdBrandEmpty'), open=false;
    function openBrand(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); search.value=''; filterBrands(''); search.focus(); open=true; }
    function closeBrand(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeBrand():openBrand(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeBrand(); });
    search.addEventListener('input',function(){ filterBrands(this.value.toLowerCase()); });
    function filterBrands(q){ var items=list.querySelectorAll('.ksd-item'); var any=false; items.forEach(function(i){ var m=!q||i.dataset.search.indexOf(q)!==-1; i.style.display=m?'':'none'; if(m)any=true; }); emptyEl.style.display=any?'none':''; emptyEl.textContent=any?'':(q?'No brands match':'Select a category first�'); }
    window.loadKsdBrands=function(catId,preselect){
        if(!catId) return;
        emptyEl.textContent='Loading�'; emptyEl.style.display='';
        list.querySelectorAll('.ksd-item').forEach(function(i){i.remove();});
        fetch('/api/brands/by-category/'+catId)
            .then(function(r){return r.json();})
            .then(function(brands){
                list.querySelectorAll('.ksd-item').forEach(function(i){i.remove();});
                brands.forEach(function(b){
                    var item=document.createElement('div'); item.className='ksd-item'+(String(b.id)===String(preselect!==undefined?preselect:hidden.value)?' ksd-selected':'');
                    item.dataset.value=b.id; item.dataset.search=b.name.toLowerCase(); item.textContent=b.name;
                    item.addEventListener('click',function(){ hidden.value=this.dataset.value; label.textContent=this.textContent; label.classList.remove('placeholder'); trigger.classList.add('ksd-has-value'); list.querySelectorAll('.ksd-item').forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected'); closeBrand(); var cfBrand=document.getElementById('cf-brand-select'); if(cfBrand){ cfBrand.value=this.dataset.value; cfBrand.dispatchEvent(new Event('change')); } });
                    list.insertBefore(item,emptyEl);
                });
                emptyEl.style.display=brands.length?'none':''; emptyEl.textContent='No brands found';
                var pre=preselect!==undefined?preselect:hidden.value;
                if(pre){ var match=list.querySelector('.ksd-item[data-value="'+pre+'"]'); if(match){label.textContent=match.textContent;label.classList.remove('placeholder');trigger.classList.add('ksd-has-value');} }
            })
            .catch(function(){ emptyEl.textContent='Failed to load brands'; emptyEl.style.display=''; });
    };
})();

// -- User & Store ksd ------------------------------------------------------
(function(){
    var allStores = @json($stores->map(fn($s)=>['id'=>$s->id,'name'=>$s->name,'user_id'=>$s->user_id]));
    var userTrigger=document.getElementById('alcUserTrigger'), userDropdown=document.getElementById('alcUserDropdown');
    var userSearch=document.getElementById('alcUserSearch'), userList=document.getElementById('alcUserList');
    var userHidden=document.getElementById('alcUserVal'), userLabel=document.getElementById('alcUserLabel');
    var userEmpty=document.getElementById('alcUserEmpty'), userItems=userList.querySelectorAll('.ksd-item'), userOpen=false;
    function openUser(){ userDropdown.style.display='block'; userTrigger.classList.add('ksd-open'); userSearch.value=''; filterUser(''); userSearch.focus(); userOpen=true; }
    function closeUser(){ userDropdown.style.display='none'; userTrigger.classList.remove('ksd-open'); userOpen=false; }
    userTrigger.addEventListener('click',function(e){ e.stopPropagation(); userOpen?closeUser():openUser(); });
    document.addEventListener('click',function(e){ if(userOpen&&!userTrigger.contains(e.target)&&!userDropdown.contains(e.target)) closeUser(); });
    userSearch.addEventListener('input',function(){ filterUser(this.value.toLowerCase()); });
    userItems.forEach(function(item){
        item.addEventListener('click',function(){
            var val=this.dataset.value, lbl=this.dataset.label||this.textContent;
            userHidden.value=val; userLabel.textContent=val?lbl:'Select user�';
            userLabel.classList.toggle('placeholder',!val); userTrigger.classList.toggle('ksd-has-value',!!val);
            userItems.forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected');
            closeUser(); renderStores(val);
        });
    });
    function filterUser(q){ var any=false; userItems.forEach(function(i){ var m=!q||i.dataset.search.indexOf(q)!==-1; i.style.display=m?'':'none'; if(m)any=true; }); userEmpty.style.display=any?'none':''; }
    var initUser=userHidden.value;
    if(initUser){ var pre=Array.from(userItems).find(function(i){return i.dataset.value===initUser;}); if(pre){userLabel.textContent=pre.dataset.label;userLabel.classList.remove('placeholder');userTrigger.classList.add('ksd-has-value');} }

    var storeTrigger=document.getElementById('alcStoreTrigger'), storeDropdown=document.getElementById('alcStoreDropdown');
    var storeSearch=document.getElementById('alcStoreSearch'), storeList=document.getElementById('alcStoreList');
    var storeHidden=document.getElementById('alcStoreVal'), storeLabel=document.getElementById('alcStoreLabel');
    var storeEmpty=document.getElementById('alcStoreEmpty'), storeOpen=false;
    function openStore(){ storeDropdown.style.display='block'; storeTrigger.classList.add('ksd-open'); storeSearch.value=''; filterStore(''); storeSearch.focus(); storeOpen=true; }
    function closeStore(){ storeDropdown.style.display='none'; storeTrigger.classList.remove('ksd-open'); storeOpen=false; }
    storeTrigger.addEventListener('click',function(e){ e.stopPropagation(); storeOpen?closeStore():openStore(); });
    document.addEventListener('click',function(e){ if(storeOpen&&!storeTrigger.contains(e.target)&&!storeDropdown.contains(e.target)) closeStore(); });
    storeSearch.addEventListener('input',function(){ filterStore(this.value.toLowerCase()); });
    storeList.addEventListener('click',function(e){ var item=e.target.closest('.ksd-item'); if(!item||item===storeEmpty) return; var val=item.dataset.value,lbl=item.dataset.label||item.textContent; storeHidden.value=val; storeLabel.textContent=val?lbl:'No Store'; storeLabel.classList.toggle('placeholder',!val); storeTrigger.classList.toggle('ksd-has-value',!!val); storeList.querySelectorAll('.ksd-item').forEach(function(i){i.classList.remove('ksd-selected');}); item.classList.add('ksd-selected'); closeStore(); });
    function filterStore(q){ var items=storeList.querySelectorAll('.ksd-item'); var any=false; items.forEach(function(i){ var m=!q||(i.dataset.search||'').indexOf(q)!==-1; i.style.display=m?'':'none'; if(m)any=true; }); storeEmpty.style.display=any?'none':''; }
    function renderStores(userId){
        storeList.querySelectorAll('.ksd-item').forEach(function(i){i.remove();}); storeHidden.value=''; storeLabel.textContent='No Store'; storeLabel.classList.add('placeholder'); storeTrigger.classList.remove('ksd-has-value');
        var filtered=userId?allStores.filter(function(s){return String(s.user_id)===String(userId);}):[];
        if(!filtered.length){ storeEmpty.textContent=userId?'No stores for this user':'Select a user first�'; storeEmpty.style.display=''; return; }
        storeEmpty.textContent='No stores match'; storeEmpty.style.display='none';
        var none=document.createElement('div'); none.className='ksd-item'; none.dataset.value=''; none.dataset.label='No Store'; none.dataset.search=''; none.textContent='� No Store �'; storeList.insertBefore(none,storeEmpty);
        filtered.forEach(function(s){ var item=document.createElement('div'); item.className='ksd-item'; item.dataset.value=String(s.id); item.dataset.label=s.name; item.dataset.search=s.name.toLowerCase(); item.textContent=s.name; storeList.insertBefore(item,storeEmpty); });
        // Pre-select current store
        var curStore=storeHidden.value; if(curStore){ var sm=storeList.querySelector('.ksd-item[data-value="'+curStore+'"]'); if(sm){sm.classList.add('ksd-selected');storeLabel.textContent=sm.dataset.label;storeLabel.classList.remove('placeholder');storeTrigger.classList.add('ksd-has-value');} }
    }
    if(initUser) renderStores(initUser);
})();

// -- Type & Status simple ksd ----------------------------------------------
(function(){
    function simpleKsd(tid,did,sid,lid,hid,lbid,eid){
        var t=document.getElementById(tid),d=document.getElementById(did),s=document.getElementById(sid);
        var l=document.getElementById(lid),h=document.getElementById(hid),lb=document.getElementById(lbid);
        var em=document.getElementById(eid),open=false;
        if(!t) return;
        function openD(){ d.style.display='block'; t.classList.add('ksd-open'); s.value=''; filter(''); s.focus(); open=true; }
        function closeD(){ d.style.display='none'; t.classList.remove('ksd-open'); open=false; }
        t.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
        document.addEventListener('click',function(e){ if(open&&!t.contains(e.target)&&!d.contains(e.target)) closeD(); });
        s.addEventListener('input',function(){ filter(this.value.toLowerCase()); });
        l.querySelectorAll('.ksd-item').forEach(function(item){ item.addEventListener('click',function(){ h.value=this.dataset.value; lb.textContent=this.dataset.label; lb.classList.remove('placeholder'); t.classList.add('ksd-has-value'); l.querySelectorAll('.ksd-item').forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected'); closeD(); }); });
        function filter(q){ var any=false; l.querySelectorAll('.ksd-item').forEach(function(i){ var m=!q||(i.dataset.search||'').indexOf(q)!==-1; i.style.display=m?'':'none'; if(m)any=true; }); em.style.display=any?'none':''; }
    }
    simpleKsd('alcTypeTrigger','alcTypeDropdown','alcTypeSearch','alcTypeList','alcTypeVal','alcTypeLabel','alcTypeEmpty');
    simpleKsd('alcStatTrigger','alcStatDropdown','alcStatSearch','alcStatList','alcStatVal','alcStatLabel','alcStatEmpty');
})();

// -- Location ksd ---------------------------------------------------------
initKsd({ triggerId:'ksdLocTrigger', dropdownId:'ksdLocDropdown', searchId:'ksdLocSearch', listId:'ksdLocList', hiddenId:'ksd-loc-val', labelId:'ksdLocLabel', emptyId:'ksdLocEmpty', placeholder:'� No Location �' });

// -- Category-fields integration -------------------------------------------
(function(){
    if(document.getElementById('cf-category-select')) return;
    var fake=document.createElement('select'); fake.id='cf-category-select'; fake.className='no-select2'; fake.style.display='none';
    fake.value=document.getElementById('ksd-cat-val').value; document.body.appendChild(fake);
})();

(function(){
    var catVal=document.getElementById('ksd-cat-val').value;
    var existBrand=document.getElementById('ksd-brand-val').value;
    if(catVal){ var fake=document.getElementById('cf-category-select'); if(fake){fake.value=catVal;fake.dispatchEvent(new Event('change'));} }
    if(catVal&&window.loadKsdBrands) loadKsdBrands(catVal,existBrand);
    var observer=new MutationObserver(function(){ document.querySelectorAll('#category-fields-container .cf-field-wrap').forEach(function(wrap){ if(wrap.querySelector('#cf-brand-select')) wrap.style.display='none'; }); });
    var container=document.getElementById('category-fields-container'); if(container) observer.observe(container,{childList:true,subtree:true});
})();

// -- Auto Suggestion ------------------------------------------------------------
function aiSuggestListing(listingId){
    var btn=document.getElementById('aiSuggestBtn'),lbl=document.getElementById('aiSuggestLabel');
    if(!btn||btn.disabled) return;
    btn.disabled=true; lbl.textContent='Thinking…'; btn.style.opacity='.65';
    fetch('/admin/ai/suggest-listing',{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':(document.querySelector('meta[name="csrf-token"]')||{content:''}).content||document.querySelector('input[name="_token"]').value},
        body:JSON.stringify({listing_id:listingId})
    })
    .then(function(r){return r.json();})
    .then(function(d){
        if(d.error){var _t=document.createElement('div');_t.textContent='AI suggestion unavailable: '+d.error;_t.className='ka-toast';document.body.appendChild(_t);setTimeout(function(){_t.remove();},3500);return;}
        var tf=document.getElementById('aiTitleField');
        if(tf&&d.title){tf.value=d.title;tf.style.background='#f0fdf4';setTimeout(function(){tf.style.background='';},1800);}
        if(d.description&&window._quillDesc){window._quillDesc.root.innerHTML=d.description;window._quillDesc.root.style.background='#f0fdf4';setTimeout(function(){window._quillDesc.root.style.background='';},1800);}
    })
    .catch(function(e){alert('Auto Suggestion failed: '+e.message);})
    .finally(function(){btn.disabled=false;lbl.textContent='Auto Suggestion';btn.style.opacity='1';});
}

// -- Price Type ksd -------------------------------------------------------
(function(){
    var lblMap={'fixed':'Price (LKR)','negotiable':'Price (LKR)','free':'','per_month':'Price / Month (LKR)','per_year':'Price / Year (LKR)'};
    var t=document.getElementById('alcPlTrigger'),d=document.getElementById('alcPlDropdown'),s=document.getElementById('alcPlSearch');
    var l=document.getElementById('alcPlList'),h=document.getElementById('alcPlVal'),lb=document.getElementById('alcPlLabel');
    var em=document.getElementById('alcPlEmpty'),wrap=document.getElementById('alcPriceWrap'),pl=document.getElementById('alcPriceLabel'),inp=document.getElementById('alcPriceInput');
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
</script>
<script src="/js/category-fields.js?v=18"></script>
<script src="/js/quill.min.js"></script>
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded',function(){
    if(typeof Quill==='undefined'||!document.getElementById('descEditor')) return;
    var hidden=document.getElementById('descHidden');
    var quill=new Quill('#descEditor',{theme:'snow',placeholder:'Describe condition, size, colour, reason for selling�',modules:{toolbar:[['bold','italic','underline'],['link'],['clean']]}});
    window._quillDesc=quill;
    if(hidden&&hidden.value.trim()) quill.clipboard.dangerouslyPasteHTML(hidden.value);
    var form=document.getElementById('alcForm');
    if(form) form.addEventListener('submit',function(){if(hidden) hidden.value=quill.root.innerHTML==='<p><br></p>'?'':quill.root.innerHTML;},true);
});
</script>
@endpush

