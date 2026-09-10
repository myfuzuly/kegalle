@extends('layouts.admin')
@section('title','Post Ad')
@section('page','Products / Ads')
@section('heading','Post Ad as Super Admin')
@section('subheading','Create a listing, assign user, category, location and status')
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

<form method="post" action="/admin/listings" enctype="multipart/form-data" id="alcForm">
@csrf

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
        <div class="alc-field mb-6">
            <label class="alc-label">Photos <span class="text-hint">(up to 6 — main photo first)</span></label>
        </div>
        <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" id="alcImgInput" class="hidden">
        <div class="alc-slots" id="alcSlots">
            @for($i=0;$i<6;$i++)
            <div class="alc-slot" data-slot="{{ $i }}">
                <div class="alc-slot-icon">+</div>
                <div class="alc-slot-label">{{ $i===0 ? 'Main' : 'Photo '.($i+1) }}</div>
            </div>
            @endfor
        </div>
        <div class="alc-hint mt8-mb14">JPG, PNG, WEBP — max 4 MB each</div>
        <div class="mb-16">
            <button class="btn-purple-sm-dis" type="button" id="aiSuggestBtn" disabled>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5z"/></svg> <span id="aiSuggestLabel">Auto Suggestion</span>
            </button>
            <p class="alc-hint mt-5">Upload photos above, then click to auto-fill title &amp; description</p>
        </div>

        @php $oldUserId = old('user_id',''); $oldStoreId = old('store_id',''); @endphp
        <div class="alc-grid mb-14">
            {{-- Post As User ksd --}}
            <div class="alc-field">
                <label class="alc-label">Post As User <span class="alc-req">*</span></label>
                <input type="hidden" name="user_id" id="alcUserVal" value="{{ $oldUserId }}" required>
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger {{ $oldUserId ? 'ksd-has-value' : '' }}" id="alcUserTrigger">
                        <span class="ksd-trigger-text {{ $oldUserId ? '' : 'placeholder' }}" id="alcUserLabel">Select user…</span>
                        <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="alcUserDropdown">
                        <div class="ksd-search-row"><input type="text" class="ksd-search" id="alcUserSearch" placeholder="Search user�" autocomplete="off"></div>
                        <div class="ksd-list" id="alcUserList">
                            <div class="ksd-item {{ !$oldUserId ? 'ksd-selected' : '' }}" data-value="" data-label="Select user…" data-search="">— No User —</div>
                            @foreach($users as $user)
                            <div class="ksd-item {{ $oldUserId==$user->id ? 'ksd-selected' : '' }}"
                                 data-value="{{ $user->id }}"
                                 data-label="{{ $user->name }} � {{ $user->email }}"
                                 data-search="{{ strtolower($user->name.' '.$user->email) }}">{{ $user->name }} � {{ $user->email }}</div>
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
                    <button type="button" class="ksd-trigger {{ $oldStoreId ? 'ksd-has-value' : '' }}" id="alcStoreTrigger">
                        <span class="ksd-trigger-text {{ $oldStoreId ? '' : 'placeholder' }}" id="alcStoreLabel">No Store</span>
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
            <input name="title" required class="alc-input" value="{{ old('title') }}" id="aiTitleField" placeholder="e.g. Honda CB125 2019 — Excellent Condition">
        </div>

        <div class="alc-field alc-full">
            <label class="alc-label">Slug URL</label>
            <input name="slug" class="alc-input" value="{{ old('slug') }}" placeholder="auto-generated-if-empty">
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
                    'kcpOldKey'     => 'category_id',
                    'kcpCategories' => $categories,
                    'kcpSelectedId' => old('category_id', ''),
                    'kcpOnChange'   => 'var f=document.getElementById("cf-category-select");if(f){f.value=id;f.dispatchEvent(new Event("change"));}if(window.adminLoadBrands)adminLoadBrands(id);',
                ])
            </div>
            <div class="alc-field">
                <label class="alc-label">Brand</label>
                <input type="hidden" name="brand_id" id="admin-brand-val" value="{{ old('brand_id') }}">
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger" id="adminBrandTrigger">
                        <span class="ksd-trigger-text placeholder" id="adminBrandLabel">Select category first�</span>
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
        @php $selLocId = old('location_id', ''); @endphp
        <div class="alc-grid">
            <div class="alc-field">
                <label class="alc-label">Location</label>
                <input type="hidden" name="location_id" id="ksd-loc-val" value="{{ $selLocId }}">
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger {{ $selLocId ? 'ksd-has-value' : '' }}" id="ksdLocTrigger">
                        <span class="ksd-trigger-text {{ $selLocId ? '' : 'placeholder' }}" id="ksdLocLabel">� No Location �</span>
                        <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="ksdLocDropdown" class="hidden">
                        <div class="ksd-search-row"><input type="text" class="ksd-search" id="ksdLocSearch" placeholder="Type to search�" autocomplete="off"></div>
                        <div class="ksd-list" id="ksdLocList">
                            <div class="ksd-item {{ !$selLocId ? 'ksd-selected' : '' }}" data-value="" data-label="� No Location �" data-search="" data-group="">� No Location �</div>
                            @foreach($locations->whereNull('parent_id') as $parentLoc)
                                <div class="ksd-group-label" data-group="{{ $parentLoc->id }}">{{ strtoupper($parentLoc->name) }}</div>
                                <div class="ksd-item ksd-indent {{ $selLocId==$parentLoc->id ? 'ksd-selected' : '' }}" data-value="{{ $parentLoc->id }}" data-label="{{ $parentLoc->name }} (All)" data-search="{{ strtolower($parentLoc->name) }}" data-group="{{ $parentLoc->id }}">📍 {{ $parentLoc->name }} (All)</div>
                                @foreach($locations->where('parent_id',$parentLoc->id) as $loc)
                                    <div class="ksd-item ksd-indent {{ $selLocId==$loc->id ? 'ksd-selected' : '' }}" data-value="{{ $loc->id }}" data-label="{{ $parentLoc->name }} � {{ $loc->name }}" data-search="{{ strtolower($parentLoc->name.' '.$loc->name) }}" data-group="{{ $parentLoc->id }}">{{ $loc->name }}</div>
                                @endforeach
                            @endforeach
                            <div class="ksd-empty" id="ksdLocEmpty" class="hidden">No locations match</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alc-field">
                <label class="alc-label">Manual Location Text</label>
                <input name="location" class="alc-input" value="{{ old('location') }}" placeholder="e.g. Kegalle Town">
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
            <input type="hidden" name="description" id="descHidden" value="{{ old('description','') }}" class="hidden">
        </div>
    </div>

    {{-- Pricing --}}
    @php $plVal=old('cf_price_label','fixed'); $plMap=['fixed'=>'Fixed Price','negotiable'=>'Negotiable','free'=>'Free / No Price','per_month'=>'Per Month','per_year'=>'Per Year']; @endphp
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
                <input name="price" type="number" step="1" min="0" class="alc-input" id="alcPriceInput" value="{{ old('price') }}" placeholder="0">
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
            <div class="alc-card-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></div>
            <div>
                <p class="alc-card-title">Publish</p>
                <p class="alc-card-sub">Save listing to marketplace</p>
            </div>
        </div>
        <button type="submit" class="alc-publish-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
            Post Ad
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
            @php $alcTypeVal=old('type','product'); $alcTypeMap=['product'=>'Product','classified'=>'Classified','buy'=>'Wanted / Buy','exchange'=>'Exchange','job'=>'Job','to-let'=>'To-Let']; @endphp
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
            @php $alcStatVal=old('status','approved'); $alcStatMap=['approved'=>'Approved','pending'=>'Pending','rejected'=>'Rejected','suspended'=>'Suspended']; @endphp
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

    {{-- Flags --}}
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
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured'))>
                ★ Featured Ad
            </label>
            <label class="alc-check">
                <input type="checkbox" name="is_top" value="1" @checked(old('is_top'))>
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
// -- Brand loader (preloaded, instant) ------------------------------------
(function(){
    var allBrands = @json($brands);
    var trigger=document.getElementById('adminBrandTrigger'), dropdown=document.getElementById('adminBrandDropdown');
    var search=document.getElementById('adminBrandSearch'), list=document.getElementById('adminBrandList');
    var hidden=document.getElementById('admin-brand-val'), label=document.getElementById('adminBrandLabel');
    var emptyEl=document.getElementById('adminBrandEmpty');
    var open=false;
    if(!trigger) return;
    function openB(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); search.value=''; filterB(''); search.focus(); open=true; }
    function closeB(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeB():openB(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeB(); });
    search.addEventListener('input',function(){ filterB(this.value.toLowerCase()); });
    function filterB(q){
        var items=list.querySelectorAll('.ksd-item'); var any=false;
        items.forEach(function(i){ var m=!q||i.dataset.search.indexOf(q)!==-1; i.style.display=m?'':'none'; if(m)any=true; });
        emptyEl.style.display=any?'none':''; if(!any) emptyEl.textContent='No brands match';
    }
    function renderBrands(brands){
        list.querySelectorAll('.ksd-item').forEach(function(i){i.remove();});
        brands.forEach(function(b){
            var item=document.createElement('div'); item.className='ksd-item';
            item.dataset.value=b.id; item.dataset.search=b.name.toLowerCase(); item.textContent=b.name;
            item.addEventListener('click',function(){
                hidden.value=this.dataset.value; label.textContent=this.textContent;
                label.classList.remove('placeholder'); trigger.classList.add('ksd-has-value');
                list.querySelectorAll('.ksd-item').forEach(function(i){i.classList.remove('ksd-selected');});
                this.classList.add('ksd-selected'); closeB();
                var cfBrand=document.getElementById('cf-brand-select');
                if(cfBrand){ cfBrand.value=this.dataset.value; cfBrand.dispatchEvent(new Event('change')); }
            });
            list.insertBefore(item,emptyEl);
        });
        emptyEl.style.display=brands.length?'none':''; emptyEl.textContent='No brands found';
    }
    window.adminLoadBrands=function(catId){
        if(!catId){ emptyEl.textContent='Select a category first�'; emptyEl.style.display=''; list.querySelectorAll('.ksd-item').forEach(function(i){i.remove();}); return; }
        hidden.value=''; label.textContent='Select Brand�'; label.classList.add('placeholder'); trigger.classList.remove('ksd-has-value');
        fetch('/api/brands/by-category/'+catId).then(function(r){return r.json();}).then(renderBrands).catch(function(){ emptyEl.textContent='Failed to load'; emptyEl.style.display=''; });
    };
})();

// -- Location ksd picker ---------------------------------------------------
(function(){
    var trigger=document.getElementById('ksdLocTrigger'), dropdown=document.getElementById('ksdLocDropdown');
    var search=document.getElementById('ksdLocSearch'), list=document.getElementById('ksdLocList');
    var hidden=document.getElementById('ksd-loc-val'), label=document.getElementById('ksdLocLabel');
    var emptyEl=document.getElementById('ksdLocEmpty');
    var items=list.querySelectorAll('.ksd-item'), groups=list.querySelectorAll('.ksd-group-label');
    var open=false;
    if(!trigger) return;
    function openD(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); search.value=''; filterItems(''); search.focus(); open=true; }
    function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    search.addEventListener('input',function(){ filterItems(this.value.toLowerCase()); });
    items.forEach(function(item){
        item.addEventListener('click',function(){
            var val=this.dataset.value, lbl=this.dataset.label;
            hidden.value=val; label.textContent=lbl||'� No Location �';
            label.classList.toggle('placeholder',!val); trigger.classList.toggle('ksd-has-value',!!val);
            items.forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected'); closeD();
        });
    });
    function filterItems(q){
        var vis={};
        items.forEach(function(i){ var m=!q||i.dataset.search.indexOf(q)!==-1||(i.dataset.label||'').toLowerCase().indexOf(q)!==-1; i.style.display=m?'':'none'; if(m&&i.dataset.group) vis[i.dataset.group]=true; });
        groups.forEach(function(g){ g.style.display=(!q||vis[g.dataset.group])?'':'none'; });
        if(emptyEl) emptyEl.style.display=Array.from(items).some(function(i){return i.style.display!=='none';})?'none':'';
    }
    var cur=hidden.value;
    if(cur){ var pre=Array.from(items).find(function(i){return i.dataset.value===cur;}); if(pre){label.textContent=pre.dataset.label;label.classList.remove('placeholder');trigger.classList.add('ksd-has-value');} }
})();

// -- Photo slots -----------------------------------------------------------
(function(){
    var MAX=6, fileInput=document.getElementById('alcImgInput'), slots=[];
    for(var s=0;s<MAX;s++) slots.push(null);
    var allSlots=document.querySelectorAll('.alc-slot');
    function syncInput(){ var dt=new DataTransfer(); slots.forEach(function(f){if(f)dt.items.add(f);}); fileInput.files=dt.files; }
    function renderSlots(){
        allSlots.forEach(function(slot,i){
            var icon=slot.querySelector('.alc-slot-icon'), lbl=slot.querySelector('.alc-slot-label');
            var oldImg=slot.querySelector('img'); if(oldImg)oldImg.remove();
            var oldRm=slot.querySelector('.alc-slot-rm'); if(oldRm)oldRm.remove();
            if(slots[i]){
                slot.classList.add('alc-slot-has-img');
                
                var img=document.createElement('img'); img.src=URL.createObjectURL(slots[i]);
                img.className='alc-slot-img'; slot.appendChild(img);
                var rm=document.createElement('button'); rm.type='button'; rm.className='alc-slot-rm';
                rm.className='alc-slot-rm';
                rm.textContent='�';
                rm.addEventListener('click',function(e){ e.stopPropagation(); slots[i]=null; slots=slots.filter(Boolean); while(slots.length<MAX)slots.push(null); syncInput();renderSlots(); });
                slot.appendChild(rm);
            } else {
                slot.classList.remove('alc-slot-has-img');
                
            }
        });
    }
    function addFiles(files){ files.forEach(function(f){ if(!f.type.match(/^image\//))return; for(var i=0;i<MAX;i++){if(!slots[i]){slots[i]=f;break;}} }); syncInput();renderSlots(); }
    allSlots.forEach(function(slot){
        slot.addEventListener('click',function(){ var i=parseInt(slot.dataset.slot); if(slots[i])return; var tmp=document.createElement('input'); tmp.type='file'; tmp.accept='image/jpeg,image/png,image/webp'; tmp.multiple=true; tmp.addEventListener('change',function(){addFiles(Array.from(tmp.files));}); tmp.click(); });
        slot.addEventListener('dragover',function(e){e.preventDefault();slot.style.borderColor='#1b5e20';slot.style.background='#f0fdf4';});
        slot.addEventListener('dragleave',function(){renderSlots();});
        slot.addEventListener('drop',function(e){e.preventDefault();addFiles(Array.from(e.dataTransfer.files));});
    });
    if(fileInput)fileInput.addEventListener('change',function(){addFiles(Array.from(fileInput.files));});
})();

// -- User ksd picker -------------------------------------------------------
(function(){
    var allStores = @json($stores->map(fn($s)=>['id'=>$s->id,'name'=>$s->name,'user_id'=>$s->user_id]));
    var userTrigger=document.getElementById('alcUserTrigger'), userDropdown=document.getElementById('alcUserDropdown');
    var userSearch=document.getElementById('alcUserSearch'), userList=document.getElementById('alcUserList');
    var userHidden=document.getElementById('alcUserVal'), userLabel=document.getElementById('alcUserLabel');
    var userEmpty=document.getElementById('alcUserEmpty');
    var userItems=userList.querySelectorAll('.ksd-item');
    var userOpen=false;
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
    function filterUser(q){
        var any=false;
        userItems.forEach(function(i){ var m=!q||i.dataset.search.indexOf(q)!==-1; i.style.display=m?'':'none'; if(m)any=true; });
        userEmpty.style.display=any?'none':'';
    }
    // pre-fill label if old value
    var initUser=userHidden.value;
    if(initUser){ var pre=Array.from(userItems).find(function(i){return i.dataset.value===initUser;}); if(pre){userLabel.textContent=pre.dataset.label;userLabel.classList.remove('placeholder');userTrigger.classList.add('ksd-has-value');} }

    // -- Store ksd picker (filtered by selected user) ----------------------
    var storeTrigger=document.getElementById('alcStoreTrigger'), storeDropdown=document.getElementById('alcStoreDropdown');
    var storeSearch=document.getElementById('alcStoreSearch'), storeList=document.getElementById('alcStoreList');
    var storeHidden=document.getElementById('alcStoreVal'), storeLabel=document.getElementById('alcStoreLabel');
    var storeEmpty=document.getElementById('alcStoreEmpty');
    var storeOpen=false;
    function openStore(){ storeDropdown.style.display='block'; storeTrigger.classList.add('ksd-open'); storeSearch.value=''; filterStore(''); storeSearch.focus(); storeOpen=true; }
    function closeStore(){ storeDropdown.style.display='none'; storeTrigger.classList.remove('ksd-open'); storeOpen=false; }
    storeTrigger.addEventListener('click',function(e){ e.stopPropagation(); storeOpen?closeStore():openStore(); });
    document.addEventListener('click',function(e){ if(storeOpen&&!storeTrigger.contains(e.target)&&!storeDropdown.contains(e.target)) closeStore(); });
    storeSearch.addEventListener('input',function(){ filterStore(this.value.toLowerCase()); });
    storeList.addEventListener('click',function(e){
        var item=e.target.closest('.ksd-item'); if(!item||item===storeEmpty) return;
        var val=item.dataset.value, lbl=item.dataset.label||item.textContent;
        storeHidden.value=val; storeLabel.textContent=val?lbl:'No Store';
        storeLabel.classList.toggle('placeholder',!val); storeTrigger.classList.toggle('ksd-has-value',!!val);
        storeList.querySelectorAll('.ksd-item').forEach(function(i){i.classList.remove('ksd-selected');}); item.classList.add('ksd-selected');
        closeStore();
    });
    function filterStore(q){
        var items=storeList.querySelectorAll('.ksd-item'); var any=false;
        items.forEach(function(i){ var m=!q||(i.dataset.search||'').indexOf(q)!==-1; i.style.display=m?'':'none'; if(m)any=true; });
        storeEmpty.style.display=any?'none':'';
    }
    function renderStores(userId){
        storeList.querySelectorAll('.ksd-item').forEach(function(i){i.remove();});
        storeHidden.value=''; storeLabel.textContent='No Store'; storeLabel.classList.add('placeholder'); storeTrigger.classList.remove('ksd-has-value');
        var filtered=userId?allStores.filter(function(s){return String(s.user_id)===String(userId);}):[];
        if(!filtered.length){ storeEmpty.textContent=userId?'No stores for this user':'Select a user first�'; storeEmpty.style.display=''; return; }
        storeEmpty.textContent='No stores match'; storeEmpty.style.display='none';
        // Auto-select if only one store
        if(filtered.length===1){
            storeHidden.value=String(filtered[0].id); storeLabel.textContent=filtered[0].name;
            storeLabel.classList.remove('placeholder'); storeTrigger.classList.add('ksd-has-value');
        }
        // No store option
        var none=document.createElement('div'); none.className='ksd-item'+(filtered.length!==1?' ksd-selected':''); none.dataset.value=''; none.dataset.label='No Store'; none.dataset.search=''; none.textContent='� No Store �';
        storeList.insertBefore(none,storeEmpty);
        filtered.forEach(function(s){
            var item=document.createElement('div'); item.className='ksd-item'+(filtered.length===1&&String(s.id)===storeHidden.value?' ksd-selected':'');
            item.dataset.value=String(s.id); item.dataset.label=s.name; item.dataset.search=s.name.toLowerCase(); item.textContent=s.name;
            storeList.insertBefore(item,storeEmpty);
        });
    }
    // init stores if old user is set
    if(initUser) renderStores(initUser);
})();

// -- Fake select for category-fields.js -----------------------------------
(function(){
    var fake=document.createElement('select'); fake.id='cf-category-select'; fake.className='no-select2'; fake.style.display='none';
    var cur=document.getElementById('ksd-cat-val'); if(cur) fake.value=cur.value;
    document.body.appendChild(fake);
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
        l.querySelectorAll('.ksd-item').forEach(function(item){
            item.addEventListener('click',function(){
                h.value=this.dataset.value; lb.textContent=this.dataset.label;
                lb.classList.remove('placeholder'); t.classList.add('ksd-has-value');
                l.querySelectorAll('.ksd-item').forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected');
                closeD();
            });
        });
        function filter(q){ var any=false; l.querySelectorAll('.ksd-item').forEach(function(i){ var m=!q||(i.dataset.search||'').indexOf(q)!==-1; i.style.display=m?'':'none'; if(m)any=true; }); em.style.display=any?'none':''; }
    }
    simpleKsd('alcTypeTrigger','alcTypeDropdown','alcTypeSearch','alcTypeList','alcTypeVal','alcTypeLabel','alcTypeEmpty');
    simpleKsd('alcStatTrigger','alcStatDropdown','alcStatSearch','alcStatList','alcStatVal','alcStatLabel','alcStatEmpty');
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

// Auto Suggestion create page (listings)
(function(){
    var btn=document.getElementById('aiSuggestBtn'),lbl=document.getElementById('aiSuggestLabel');
    if(!btn) return;
    function getToken(){ return (document.querySelector('meta[name="csrf-token"]')||{content:''}).content||((document.querySelector('input[name="_token"]')||{value:''}).value); }
    function filesBase64(files,cb){
        var out=[],done=0,total=Math.min(files.length,4);
        if(!total){cb([]);return;}
        for(var i=0;i<total;i++){(function(f){ var r=new FileReader(); r.onload=function(e){out.push({data:e.target.result.split(',')[1],type:f.type});if(++done===total)cb(out);}; r.readAsDataURL(f); })(files[i]);}
    }
    function hasFiles(){ var inp=document.getElementById('alcImgInput'); return inp&&inp.files&&inp.files.length>0; }
    function updateBtn(){ btn.disabled=!hasFiles(); btn.style.opacity=hasFiles()?'1':'.45'; }
    var grid=document.getElementById('alcSlots');
    if(grid) new MutationObserver(updateBtn).observe(grid,{childList:true,subtree:true});
    updateBtn();
    btn.addEventListener('click',function(){
        if(btn.disabled) return;
        var inp=document.getElementById('alcImgInput');
        if(!inp||!inp.files.length){alert('Please select photos first.');return;}
        btn.disabled=true; lbl.textContent='Thinking…'; btn.style.opacity='.65';
        filesBase64(Array.from(inp.files),function(imgs){
            fetch('/admin/ai/suggest-listing',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':getToken()},body:JSON.stringify({images:imgs})})
            .then(function(r){return r.json();})
            .then(function(d){
                if(d.error){var _t=document.createElement('div');_t.textContent='AI suggestion unavailable: '+d.error;_t.className='ka-toast';document.body.appendChild(_t);setTimeout(function(){_t.remove();},3500);return;}
                var tf=document.getElementById('aiTitleField');
                if(tf&&d.title){tf.value=d.title;tf.style.background='#f0fdf4';setTimeout(function(){tf.style.background='';},1800);}
                if(d.description&&window._quillDesc){window._quillDesc.root.innerHTML=d.description;window._quillDesc.root.style.background='#f0fdf4';setTimeout(function(){window._quillDesc.root.style.background='';},1800);}
            })
            .catch(function(e){alert('Auto Suggestion failed: '+e.message);})
            .finally(function(){updateBtn();lbl.textContent='Auto Suggestion';});
        });
    });
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
    if(hidden&&hidden.value.trim()) quill.root.innerHTML=hidden.value;
    var form=document.getElementById('alcForm');
    if(form) form.addEventListener('submit',function(){if(hidden) hidden.value=quill.root.innerHTML==='<p><br></p>'?'':quill.root.innerHTML;},true);
});
</script>
@endpush

