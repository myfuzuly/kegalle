@extends('layouts.admin')
@section('title','Edit Classified Ad')
@section('page','Classifieds')
@section('heading','Edit Classified Ad')
@section('subheading','Update contact details, content or photos for this no-account classified ad')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/classifieds">? Back to Classifieds</a>@endsection

@push('styles')
<link href="/css/quill.snow.css" rel="stylesheet">

@endpush

@section('content')

@if($errors->any())
<div class="alert-error">
    <ul class="list-pl">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="post" action="/admin/classifieds/{{ $listing->id }}" enctype="multipart/form-data" id="alcForm">
@csrf
@method('PUT')

<div class="alc-shell">

{{-- -- LEFT MAIN ---------------------------------------------- --}}
<div>

    {{-- Contact Details --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon teal">??</div>
            <div>
                <p class="alc-card-title">Contact Details</p>
                <p class="alc-card-sub">Shown to buyers on the listing page</p>
            </div>
        </div>

        <div class="alc-grid mb-14">
            <div class="alc-field">
                <label class="alc-label">Poster Name <span class="alc-req">*</span></label>
                <input name="poster_name" class="alc-input" value="{{ old('poster_name',$listing->poster_name) }}" required placeholder="e.g. Kamal Perera" maxlength="120">
            </div>
            <div class="alc-field">
                <label class="alc-label">Contact Number <span class="alc-req">*</span></label>
                <input name="poster_phone" id="posterPhone" class="alc-input" value="{{ old('poster_phone',$listing->poster_phone) }}" required placeholder="e.g. 0771234567" maxlength="30">
            </div>
        </div>

        <div class="alc-field">
            <label class="alc-label">
                <svg class="va-m2-mr4" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                WhatsApp Number
            </label>
            <div class="alc-wa-row">
                <span class="alc-hint flex-1">If set, a WhatsApp button will appear on the listing page.</span>
                <label class="alc-wa-same"><input type="checkbox" id="waSameAsPhone"> Same as phone</label>
            </div>
            <input name="poster_whatsapp" id="waInput" class="alc-input" value="{{ old('poster_whatsapp',$listing->poster_whatsapp) }}" placeholder="e.g. 0771234567 (optional)" maxlength="30">
        </div>
    </div>

    {{-- Ad Details: Photos ? Auto Suggestion ? Title --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg></div>
            <div>
                <p class="alc-card-title">Ad Details</p>
                <p class="alc-card-sub">Photos, Auto Suggestion, and title</p>
            </div>
        </div>
        <div class="alc-field mb-14">
            <label class="alc-label">Photos <span class="text-hint">(up to 6 — main photo first)</span></label>
            @include('admin.partials.photo-uploader', ['maxSlots' => 6, 'existingImages' => $listing->images])
        </div>
        <div class="mb-16">
            <button class="btn-purple-md" type="button" id="aiSuggestBtn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5z"/></svg> <span id="aiSuggestLabel">Auto Suggestion</span>
            </button>
            <p class="note-inline">
                <span class="badge-amber">?? NOTE</span>
                <span class="text-subtle">Upload or change photos above, then click to auto-fill title &amp; description from existing or new photos</span>
            </p>
        </div>
        <div class="alc-field">
            <label class="alc-label">Ad Title <span class="alc-req">*</span></label>
            <input name="title" id="aiTitleField" required class="alc-input" value="{{ old('title',$listing->title) }}" maxlength="180" placeholder="e.g. Honda CB125 2019 � Good Condition">
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
                    'kcpOnChange'   => 'var f=document.getElementById("cf-category-select");if(f){f.value=id;f.dispatchEvent(new Event("change"));}if(window.adminLoadBrands)adminLoadBrands(id);',
                ])
            </div>
            <div class="alc-field">
                <label class="alc-label">Brand</label>
                <input type="hidden" name="brand_id" id="admin-brand-val" value="{{ old('brand_id',$currentBrandId??'') }}">
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger {{ ($currentBrandId??'')?'ksd-has-value':'' }}" id="adminBrandTrigger">
                        <span class="ksd-trigger-text {{ ($currentBrandId??'')?'':'placeholder' }}" id="adminBrandLabel">{{ ($currentBrandId??'')?'Loading�':'Select category first�' }}</span>
                        <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="adminBrandDropdown">
                        <div class="ksd-search-row"><input type="text" class="ksd-search" id="adminBrandSearch" placeholder="Search brand�" autocomplete="off"></div>
                        <div class="ksd-list" id="adminBrandList">
                            <div class="ksd-empty" id="adminBrandEmpty">Select a category first�</div>
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
                <input name="location" class="alc-input" value="{{ old('location',$listing->location) }}" placeholder="e.g. Kegalle Town">
            </div>
        </div>
    </div>

    {{-- Description --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon slate"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
            <div>
                <p class="alc-card-title">Description</p>
                <p class="alc-card-sub">English � Sinhala � Tamil</p>
            </div>
        </div>

        {{-- English --}}
        <div class="alc-field mb-20">
            <label class="alc-label">???? English Description</label>
            <div id="descEditor"></div>
            <input type="hidden" name="description" id="descHidden" value="{{ old('description',$listing->description??'') }}" class="hidden">
        </div>

        {{-- Sinhala (collapsed by default unless value exists) --}}
        <div class="divider-top-sm">
            <div class="flex-between-g10">
                <button class="btn-add-dashed" type="button" id="siToggleBtn" data-lang="si">
                    <span id="siArrow">?</span> ???? Add Sinhala Description <span class="fs-11 text-muted">(optional)</span>
                </button>
                <button class="btn-action-blue-hidden" type="button" id="translateSiBtn">
                    ?? <span id="siLabel">Translate to Sinhala</span>
                </button>
            </div>
            <div class="hidden mt-10" id="siPanel">
                <label class="alc-label mb6-block">????? ???????</label>
                <div id="descSiEditor"></div>
                <input type="hidden" name="description_si" id="descSiHidden" value="{{ old('description_si',$listing->description_si??'') }}" class="hidden">
            </div>
        </div>

        {{-- Tamil (collapsed by default unless value exists) --}}
        <div class="divider-top-md">
            <div class="flex-between-g10">
                <button class="btn-add-dashed" type="button" id="taToggleBtn" data-lang="ta">
                    <span id="taArrow">?</span> ???? Add Tamil Description <span class="fs-11 text-muted">(optional)</span>
                </button>
                <button class="btn-action-amber-hidden" type="button" id="translateTaBtn">
                    ?? <span id="taLabel">Translate to Tamil</span>
                </button>
            </div>
            <div class="hidden mt-10" id="taPanel">
                <label class="alc-label mb6-block">????? ????????</label>
                <div id="descTaEditor"></div>
                <input type="hidden" name="description_ta" id="descTaHidden" value="{{ old('description_ta',$listing->description_ta??'') }}" class="hidden">
            </div>
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
                <input name="price" type="number" step="1" min="0" class="alc-input" id="alcPriceInput" value="{{ old('price',$listing->price?(int)$listing->price:'') }}" placeholder="0">
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

    {{-- Bottom Save --}}
    <div class="flex-gap10-mt8">
        <button type="submit" class="alc-publish-btn mw-260">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Save Changes
        </button>
        <a href="/admin/classifieds" class="alc-cancel-btn mw120-mt0">Cancel</a>
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
                <p class="alc-card-sub">Update this classified ad</p>
            </div>
        </div>
        <button type="submit" class="alc-publish-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Save Changes
        </button>
        <a href="/admin/classifieds" class="alc-cancel-btn">Cancel</a>
    </div>

    {{-- Status --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon purple">??</div>
            <div>
                <p class="alc-card-title">Status</p>
                <p class="alc-card-sub">Listing visibility</p>
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
window.cfExistingValues = {!! json_encode(['brand_id'=>$currentBrandId??null]+($existingFieldValues??[]),JSON_HEX_TAG|JSON_HEX_APOS) !!};

// -- WhatsApp same-as-phone ------------------------------------------------
(function(){
    var phone=document.getElementById('posterPhone'), wa=document.getElementById('waInput'), chk=document.getElementById('waSameAsPhone');
    if(!phone||!wa||!chk) return;
    if(wa.value&&phone.value&&wa.value===phone.value){chk.checked=true;wa.disabled=true;}
    chk.addEventListener('change',function(){ if(chk.checked){wa.value=phone.value;wa.disabled=true;}else{wa.disabled=false;} });
    phone.addEventListener('input',function(){ if(chk.checked) wa.value=phone.value; });
})();

// -- Brand loader ----------------------------------------------------------
(function(){
    var trigger=document.getElementById('adminBrandTrigger'), dropdown=document.getElementById('adminBrandDropdown');
    var search=document.getElementById('adminBrandSearch'), list=document.getElementById('adminBrandList');
    var hidden=document.getElementById('admin-brand-val'), label=document.getElementById('adminBrandLabel');
    var emptyEl=document.getElementById('adminBrandEmpty'), open=false;
    if(!trigger) return;
    function openB(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); search.value=''; filterB(''); search.focus(); open=true; }
    function closeB(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeB():openB(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeB(); });
    search.addEventListener('input',function(){ filterB(this.value.toLowerCase()); });
    function filterB(q){ var items=list.querySelectorAll('.ksd-item'); var any=false; items.forEach(function(i){ var m=!q||i.dataset.search.indexOf(q)!==-1; i.style.display=m?'':'none'; if(m)any=true; }); emptyEl.style.display=any?'none':''; if(!any) emptyEl.textContent='No brands match'; }
    function renderBrands(brands){
        list.querySelectorAll('.ksd-item').forEach(function(i){i.remove();});
        brands.forEach(function(b){ var item=document.createElement('div'); item.className='ksd-item'; item.dataset.value=b.id; item.dataset.search=b.name.toLowerCase(); item.textContent=b.name; item.addEventListener('click',function(){ hidden.value=this.dataset.value; label.textContent=this.textContent; label.classList.remove('placeholder'); trigger.classList.add('ksd-has-value'); list.querySelectorAll('.ksd-item').forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected'); closeB(); var cfBrand=document.getElementById('cf-brand-select'); if(cfBrand){ cfBrand.value=this.dataset.value; cfBrand.dispatchEvent(new Event('change')); } }); list.insertBefore(item,emptyEl); });
        emptyEl.style.display=brands.length?'none':''; emptyEl.textContent='No brands found';
        // Pre-select if brand already set
        var cur=hidden.value; if(cur){ var m=list.querySelector('.ksd-item[data-value="'+cur+'"]'); if(m){m.classList.add('ksd-selected');label.textContent=m.textContent;label.classList.remove('placeholder');trigger.classList.add('ksd-has-value');} }
    }
    window.adminLoadBrands=function(catId){
        if(!catId){ emptyEl.textContent='Select a category first�'; emptyEl.style.display=''; list.querySelectorAll('.ksd-item').forEach(function(i){i.remove();}); return; }
        hidden.value=''; label.textContent='Select Brand�'; label.classList.add('placeholder'); trigger.classList.remove('ksd-has-value');
        fetch('/api/brands/by-category/'+catId).then(function(r){return r.json();}).then(renderBrands).catch(function(){emptyEl.textContent='Failed to load'; emptyEl.style.display='';});
    };
    // Auto-load brands for current category
    var catVal=document.getElementById('ksd-cat-val').value;
    if(catVal) adminLoadBrands(catVal);
})();

// -- Status simple ksd -----------------------------------------------------
(function(){
    var t=document.getElementById('alcStatTrigger'),d=document.getElementById('alcStatDropdown'),s=document.getElementById('alcStatSearch');
    var l=document.getElementById('alcStatList'),h=document.getElementById('alcStatVal'),lb=document.getElementById('alcStatLabel');
    var em=document.getElementById('alcStatEmpty'),open=false;
    if(!t) return;
    function openD(){ d.style.display='block'; t.classList.add('ksd-open'); s.value=''; filter(''); s.focus(); open=true; }
    function closeD(){ d.style.display='none'; t.classList.remove('ksd-open'); open=false; }
    t.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!t.contains(e.target)&&!d.contains(e.target)) closeD(); });
    s.addEventListener('input',function(){ filter(this.value.toLowerCase()); });
    l.querySelectorAll('.ksd-item').forEach(function(item){ item.addEventListener('click',function(){ h.value=this.dataset.value; lb.textContent=this.dataset.label; lb.classList.remove('placeholder'); t.classList.add('ksd-has-value'); l.querySelectorAll('.ksd-item').forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected'); closeD(); }); });
    function filter(q){ var any=false; l.querySelectorAll('.ksd-item').forEach(function(i){ var m=!q||(i.dataset.search||'').indexOf(q)!==-1; i.style.display=m?'':'none'; if(m)any=true; }); em.style.display=any?'none':''; }
})();

// -- Location ksd picker ---------------------------------------------------
(function(){
    var trigger=document.getElementById('ksdLocTrigger'), dropdown=document.getElementById('ksdLocDropdown');
    var search=document.getElementById('ksdLocSearch'), list=document.getElementById('ksdLocList');
    var hidden=document.getElementById('ksd-loc-val'), label=document.getElementById('ksdLocLabel');
    var emptyEl=document.getElementById('ksdLocEmpty');
    var items=list.querySelectorAll('.ksd-item'), groups=list.querySelectorAll('.ksd-group-label'), open=false;
    if(!trigger) return;
    function openD(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); search.value=''; filterItems(''); search.focus(); open=true; }
    function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    search.addEventListener('input',function(){ filterItems(this.value.toLowerCase()); });
    items.forEach(function(item){ item.addEventListener('click',function(){ var val=this.dataset.value,lbl=this.dataset.label; hidden.value=val; label.textContent=lbl||'� No Location �'; label.classList.toggle('placeholder',!val); trigger.classList.toggle('ksd-has-value',!!val); items.forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected'); closeD(); }); });
    function filterItems(q){ var vis={}; items.forEach(function(i){ var m=!q||i.dataset.search.indexOf(q)!==-1||(i.dataset.label||'').toLowerCase().indexOf(q)!==-1; i.style.display=m?'':'none'; if(m&&i.dataset.group) vis[i.dataset.group]=true; }); groups.forEach(function(g){ g.style.display=(!q||vis[g.dataset.group])?'':'none'; }); if(emptyEl) emptyEl.style.display=Array.from(items).some(function(i){return i.style.display!=='none';})?'none':''; }
})();

// -- Fake select for category-fields.js -----------------------------------
(function(){
    var f=document.createElement('select'); f.id='cf-category-select'; f.style.display='none';
    var cur=document.getElementById('ksd-cat-val'); if(cur) f.value=cur.value;
    var container=document.getElementById('category-fields-container');
    (container||document.body).appendChild(f);
    var catVal=cur?cur.value:'';
    if(catVal){ f.value=catVal; f.dispatchEvent(new Event('change')); }
})();

// -- Auto Suggestion ----------------------------------------------------------
(function(){
    var btn=document.getElementById('aiSuggestBtn'),lbl=document.getElementById('aiSuggestLabel');
    if(!btn) return;
    var listingId={{ $listing->id }};
    function getToken(){ return (document.querySelector('meta[name="csrf-token"]')||{content:''}).content||((document.querySelector('input[name="_token"]')||{value:''}).value); }
    function filesBase64(files,cb){ var out=[],done=0,total=Math.min(files.length,4); if(!total){cb([]);return;} for(var i=0;i<total;i++){(function(f){ var r=new FileReader(); r.onload=function(e){out.push({data:e.target.result.split(',')[1],type:f.type});if(++done===total)cb(out);}; r.readAsDataURL(f); })(files[i]);} }
    btn.addEventListener('click',function(){
        if(btn.disabled) return;
        btn.disabled=true; lbl.textContent='Thinking…'; btn.style.opacity='.65';
        var inp=document.getElementById('kapInput');
        var hasNew=inp&&inp.files&&inp.files.length>0;
        function applyResult(d){
            if(d.error){var _t=document.createElement('div');_t.textContent='AI suggestion unavailable: '+d.error;_t.className='ka-toast';document.body.appendChild(_t);setTimeout(function(){_t.remove();},3500);return;}
            var tf=document.getElementById('aiTitleField');
            if(tf&&d.title){tf.value=d.title;tf.style.background='#f0fdf4';setTimeout(function(){tf.style.background='';},1800);}
            if(d.description&&window._quillDesc){window._quillDesc.root.innerHTML=d.description;window._quillDesc.root.style.background='#f0fdf4';setTimeout(function(){window._quillDesc.root.style.background='';},1800);}
        }
        function done(){ btn.disabled=false; lbl.textContent='Auto Suggestion'; btn.style.opacity='1'; }
        if(hasNew){
            filesBase64(Array.from(inp.files),function(imgs){
                fetch('/admin/ai/suggest-listing',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':getToken()},body:JSON.stringify({images:imgs})})
                .then(function(r){return r.json();}).then(applyResult).catch(function(e){alert('Auto Suggestion failed: '+e.message);}).finally(done);
            });
        } else {
            fetch('/admin/ai/suggest-listing',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':getToken()},body:JSON.stringify({listing_id:listingId})})
            .then(function(r){return r.json();}).then(applyResult).catch(function(e){alert('Auto Suggestion failed: '+e.message);}).finally(done);
        }
    });
})();

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
    function getToken(){ return (document.querySelector('meta[name="csrf-token"]')||{content:''}).content||((document.querySelector('input[name="_token"]')||{value:''}).value); }
    var toolbar=[['bold','italic','underline'],[{'list':'bullet'}],['link'],['clean']];

    // English
    var hidden=document.getElementById('descHidden');
    var quill=new Quill('#descEditor',{theme:'snow',placeholder:'Describe condition, size, colour, reason for selling�',modules:{toolbar:toolbar}});
    window._quillDesc=quill;
    if(hidden&&hidden.value.trim()) quill.clipboard.dangerouslyPasteHTML(hidden.value);

    // Sinhala & Tamil � lazy init
    var hiddenSi=document.getElementById('descSiHidden');
    var hiddenTa=document.getElementById('descTaHidden');
    var quillSi=null, quillTa=null;

    // Auto-expand if existing value
    if(hiddenSi&&hiddenSi.value.trim()) toggleLang('si');
    if(hiddenTa&&hiddenTa.value.trim()) toggleLang('ta');

    var form=document.getElementById('alcForm');
    if(form) form.addEventListener('submit',function(){
        if(hidden) hidden.value=quill.root.innerHTML==='<p><br></p>'?'':quill.root.innerHTML;
        if(hiddenSi&&quillSi) hiddenSi.value=quillSi.root.innerHTML==='<p><br></p>'?'':quillSi.root.innerHTML;
        if(hiddenTa&&quillTa) hiddenTa.value=quillTa.root.innerHTML==='<p><br></p>'?'':quillTa.root.innerHTML;
    },true);

    // Wire data-lang buttons
    document.querySelectorAll('[data-lang]').forEach(function(btn){
        btn.addEventListener('click',function(){ window.toggleLang(btn.dataset.lang); });
    });

    window.toggleLang=function(lang){
        var panel=document.getElementById(lang+'Panel');
        var arrow=document.getElementById(lang+'Arrow');
        var toggleBtn=document.getElementById(lang+'ToggleBtn');
        var translateBtn=document.getElementById('translate'+lang.charAt(0).toUpperCase()+lang.slice(1)+'Btn');
        var open=panel.style.display==='none'||panel.style.display==='';
        panel.style.display=open?'block':'none';
        translateBtn.style.display=open?'inline-flex':'none';
        arrow.textContent=open?'?':'?';
        toggleBtn.style.borderStyle=open?'solid':'dashed';
        toggleBtn.style.color=open?'#374151':'#64748b';
        if(open){
            if(lang==='si'&&!quillSi){
                quillSi=new Quill('#descSiEditor',{theme:'snow',placeholder:'????? ???????�',modules:{toolbar:toolbar}});
                window._quillSi=quillSi;
                if(hiddenSi&&hiddenSi.value.trim()) quillSi.clipboard.dangerouslyPasteHTML(hiddenSi.value);
            }
            if(lang==='ta'&&!quillTa){
                quillTa=new Quill('#descTaEditor',{theme:'snow',placeholder:'????? ????????�',modules:{toolbar:toolbar}});
                window._quillTa=quillTa;
                if(hiddenTa&&hiddenTa.value.trim()) quillTa.clipboard.dangerouslyPasteHTML(hiddenTa.value);
            }
        }
    };

    // Translation confirmation modal
    (function(){
        var overlay=document.createElement('div');
        overlay.id='translateModal';
        overlay.style.cssText='display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;padding:16px';
        overlay.innerHTML='<div class="modal-card"><div class="modal-header"><div class="flex-row flex-row-8"><span id="tmModalIcon" class="fs-18"></span><span class="modal-title">Review Translation</span></div><button class="modal-close-btn" id="tmClose" type="button">&times;</button></div><div class="modal-body"><p class="form-label-sm">Translation Preview</p><div class="note-box" id="tmPreview"></div></div><div class="modal-footer"><button class="btn-cancel" id="tmReject" type="button">? Discard</button><button class="btn-submit-green" id="tmAccept" type="button">? Use This Translation</button></div></div>';
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
        if(panel&&(panel.style.display==='none'||panel.style.display==='')) toggleLang(langShort);
        var plain=quill.root.innerText.trim();
        if(!plain){alert('Please write the English description first.');return;}
        btn.disabled=true;lbl.textContent='Translating�';btn.style.opacity='.65';
        fetch('/admin/ai/translate-text',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':getToken()},body:JSON.stringify({text:plain,language:language})})
        .then(function(r){return r.json();})
        .then(function(d){
            if(d.error){var _t=document.createElement('div');_t.textContent='Translation unavailable: '+d.error;_t.className='ka-toast';document.body.appendChild(_t);setTimeout(function(){_t.remove();},3500);return;}
            showTranslateConfirm(d.translated,language==='sinhala'?'Sinhala':'Tamil','????',function(){
                targetQuill.clipboard.dangerouslyPasteHTML(d.translated);
                targetQuill.root.style.background='#f0fdf4';
                setTimeout(function(){targetQuill.root.style.background='';},1800);
            });
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

