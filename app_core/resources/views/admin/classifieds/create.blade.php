@extends('layouts.admin')
@section('title','Post Classified Ad')
@section('page','Classifieds')
@section('heading','Post Classified Ad')
@section('subheading','Post a no-account classified ad — goes live immediately as approved')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/classifieds">← Back to Classifieds</a>@endsection

@push('styles')
<link href="/css/quill.snow.css" rel="stylesheet">

@endpush

@section('content')

@if($errors->any())
<div class="alc-form-errors">
    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="post" action="/admin/classifieds" enctype="multipart/form-data" id="alcForm">
@csrf

<div class="alc-shell">

{{-- ── LEFT MAIN ────────────────────────────────────────────── --}}
<div>

    {{-- Contact Details --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon teal">👤</div>
            <div>
                <p class="alc-card-title">Contact Details</p>
                <p class="alc-card-sub">Shown to buyers on the listing page</p>
            </div>
        </div>

        <div class="alc-grid alc-grid--mb">
            <div class="alc-field">
                <label class="alc-label">Poster Name <span class="alc-req">*</span></label>
                <input name="poster_name" class="alc-input" value="{{ old('poster_name') }}" required placeholder="e.g. Kamal Perera" maxlength="120">
            </div>
            <div class="alc-field">
                <label class="alc-label">Contact Number <span class="alc-req">*</span></label>
                <input name="poster_phone" id="posterPhone" class="alc-input" value="{{ old('poster_phone') }}" required placeholder="e.g. 0771234567" maxlength="30">
            </div>
        </div>

        <div class="alc-field">
            <div class="alc-wa-row">
                <label class="alc-label alc-wa-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp Number
                </label>
                <label class="alc-wa-same"><input type="checkbox" id="waSameAsPhone"> Same as phone</label>
            </div>
            <span class="alc-hint">If set, a WhatsApp button will appear on the listing page.</span>
            <input name="poster_whatsapp" id="waInput" class="alc-input alc-input--mt" value="{{ old('poster_whatsapp') }}" placeholder="e.g. 0771234567 (optional)" maxlength="30">
        </div>
    </div>

    {{-- Ad Details: Images → AI Suggest → Title --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon green">📝</div>
            <div>
                <p class="alc-card-title">Ad Details</p>
                <p class="alc-card-sub">Photos, Auto Suggestion, and title</p>
            </div>
        </div>
        <div class="alc-field alc-field--mb">
            <label class="alc-label">Photos <span class="alc-label-opt">(up to 6 · main photo first)</span></label>
            @include('admin.partials.photo-uploader', ['maxSlots' => 6, 'existingImages' => collect()])
        </div>
        <div class="alc-ai-inline">
            <button type="button" id="aiSuggestBtn" class="alc-ai-btn" disabled>
                ✨ <span id="aiSuggestLabel">Auto Suggestion</span>
            </button>
            <p class="alc-ai-note">
                <span class="alc-ai-note-badge">📌 NOTE</span>
                <span>Upload photos above, then click to auto-fill title &amp; description</span>
            </p>
        </div>
        <div class="alc-field">
            <label class="alc-label">Ad Title <span class="alc-req">*</span></label>
            <input name="title" required class="alc-input" value="{{ old('title') }}" id="aiTitleField" maxlength="180" placeholder="e.g. Honda CB125 2019 — Good Condition">
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
                        <span class="ksd-trigger-text placeholder" id="adminBrandLabel">Select category first…</span>
                        <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="adminBrandDropdown">
                        <div class="ksd-search-row"><input type="text" class="ksd-search" id="adminBrandSearch" placeholder="Search brand…" autocomplete="off"></div>
                        <div class="ksd-list" id="adminBrandList">
                            <div class="ksd-empty" id="adminBrandEmpty">Select a category first…</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="category-fields-container" class="alc-cf-container"></div>
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
        @php $selLocId = old('location_id', ''); @endphp
        <div class="alc-grid">
            <div class="alc-field">
                <label class="alc-label">Location</label>
                <input type="hidden" name="location_id" id="ksd-loc-val" value="{{ $selLocId }}">
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger {{ $selLocId ? 'ksd-has-value' : '' }}" id="ksdLocTrigger">
                        <span class="ksd-trigger-text {{ $selLocId ? '' : 'placeholder' }}" id="ksdLocLabel">— No Location —</span>
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
                            <div class="ksd-empty k-hidden" id="ksdLocEmpty">No locations match</div>
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
            <div class="alc-card-icon slate">📄</div>
            <div>
                <p class="alc-card-title">Description</p>
                <p class="alc-card-sub">English · Sinhala · Tamil</p>
            </div>
        </div>

        {{-- English --}}
        <div class="alc-field alc-field--mb">
            <label class="alc-label">🇬🇧 English Description</label>
            <div id="descEditor"></div>
            <input type="hidden" name="description" id="descHidden" value="{{ old('description','') }}" class="k-hidden">
        </div>

        {{-- Sinhala (collapsed by default) --}}
        <div class="alc-lang-sep alc-lang-sep--first">
            <div class="alc-lang-row">
                <button type="button" id="siToggleBtn" class="alc-lang-toggle">
                    <span id="siArrow">▶</span> 🇱🇰 Add Sinhala Description <span class="alc-lang-opt">(optional)</span>
                </button>
                <button type="button" id="translateSiBtn" class="alc-lang-translate alc-lang-translate--si">
                    🌐 <span id="siLabel">Translate to Sinhala</span>
                </button>
            </div>
            <div id="siPanel" class="alc-lang-panel">
                <label class="alc-label alc-lang-panel-label">සිංහල විස්තරය</label>
                <div id="descSiEditor"></div>
                <input type="hidden" name="description_si" id="descSiHidden" value="{{ old('description_si','') }}" class="k-hidden">
            </div>
        </div>

        {{-- Tamil (collapsed by default) --}}
        <div class="alc-lang-sep">
            <div class="alc-lang-row">
                <button type="button" id="taToggleBtn" class="alc-lang-toggle">
                    <span id="taArrow">▶</span> 🇱🇰 Add Tamil Description <span class="alc-lang-opt">(optional)</span>
                </button>
                <button type="button" id="translateTaBtn" class="alc-lang-translate alc-lang-translate--ta">
                    🌐 <span id="taLabel">Translate to Tamil</span>
                </button>
            </div>
            <div id="taPanel" class="alc-lang-panel">
                <label class="alc-label alc-lang-panel-label">தமிழ் விளக்கம்</label>
                <div id="descTaEditor"></div>
                <input type="hidden" name="description_ta" id="descTaHidden" value="{{ old('description_ta','') }}" class="k-hidden">
            </div>
        </div>
    </div>

    {{-- Pricing --}}
    @php $plVal=old('cf_price_label','fixed'); $plMap=['fixed'=>'Fixed Price','negotiable'=>'Negotiable','free'=>'Free / No Price','per_month'=>'Per Month','per_year'=>'Per Year']; @endphp
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon amber">💰</div>
            <div>
                <p class="alc-card-title">Pricing</p>
                <p class="alc-card-sub">Set price type and amount</p>
            </div>
        </div>
        <div class="alc-grid">
            <div class="alc-field {{ $plVal==='free'?'k-hidden':'' }}" id="alcPriceWrap">
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
                        <div class="ksd-dropdown" id="alcPlDropdown">
                            <div class="ksd-search-row"><input type="text" class="ksd-search" id="alcPlSearch" placeholder="Search…" autocomplete="off"></div>
                            <div class="ksd-list" id="alcPlList">
                                @foreach($plMap as $v=>$l)
                                <div class="ksd-item {{ $plVal===$v?'ksd-selected':'' }}" data-value="{{ $v }}" data-label="{{ $l }}" data-search="{{ strtolower($l) }}">{{ $l }}</div>
                                @endforeach
                                <div class="ksd-empty k-hidden" id="alcPlEmpty">No match</div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    {{-- Bottom Publish --}}
    <div class="alc-submit-row">
        <button type="submit" class="alc-publish-btn alc-publish-btn--narrow">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
            Publish Ad
        </button>
        <a href="/admin/classifieds" class="alc-cancel-btn alc-cancel-btn--narrow">Cancel</a>
    </div>

</div>

{{-- ── RIGHT SIDEBAR ────────────────────────────────────────── --}}
<div>

    {{-- Publish --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon green">🚀</div>
            <div>
                <p class="alc-card-title">Publish</p>
                <p class="alc-card-sub">Goes live immediately as approved</p>
            </div>
        </div>
        <button type="submit" class="alc-publish-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
            Publish Ad
        </button>
        <a href="/admin/classifieds" class="alc-cancel-btn">Cancel</a>
    </div>

    {{-- Boost Flags --}}
    <div class="alc-card">
        <div class="alc-card-header">
            <div class="alc-card-icon blue">⭐</div>
            <div>
                <p class="alc-card-title">Boost Flags</p>
                <p class="alc-card-sub">Visibility options</p>
            </div>
        </div>
        <div class="alc-checks">
            <label class="alc-check">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured'))>
                ⭐ Featured Ad
            </label>
            <label class="alc-check">
                <input type="checkbox" name="is_top" value="1" @checked(old('is_top'))>
                🔝 Top Ad
            </label>
        </div>
    </div>

</div>
</div>
</form>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
// ── WhatsApp same-as-phone ────────────────────────────────────────────────
(function(){
    var phone=document.getElementById('posterPhone'), wa=document.getElementById('waInput'), chk=document.getElementById('waSameAsPhone');
    if(!phone||!wa||!chk) return;
    chk.addEventListener('change',function(){ if(chk.checked){wa.value=phone.value;wa.disabled=true;}else{wa.disabled=false;} });
    phone.addEventListener('input',function(){ if(chk.checked) wa.value=phone.value; });
})();

// ── Brand loader ──────────────────────────────────────────────────────────
(function(){
    var allBrands = @json($brands);
    var trigger=document.getElementById('adminBrandTrigger'), dropdown=document.getElementById('adminBrandDropdown');
    var search=document.getElementById('adminBrandSearch'), list=document.getElementById('adminBrandList');
    var hidden=document.getElementById('admin-brand-val'), label=document.getElementById('adminBrandLabel');
    var emptyEl=document.getElementById('adminBrandEmpty');
    var open=false;
    if(!trigger) return;
    function openB(){ dropdown.classList.add('ksd-open'); trigger.classList.add('ksd-open'); search.value=''; filterB(''); search.focus(); open=true; }
    function closeB(){ dropdown.classList.remove('ksd-open'); trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeB():openB(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeB(); });
    search.addEventListener('input',function(){ filterB(this.value.toLowerCase()); });
    function filterB(q){
        var items=list.querySelectorAll('.ksd-item'); var any=false;
        items.forEach(function(i){ var m=!q||i.dataset.search.indexOf(q)!==-1; i.classList.toggle('k-hidden',!m); if(m)any=true; });
        emptyEl.classList.toggle('k-hidden',any); if(!any) emptyEl.textContent='No brands match';
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
        emptyEl.classList.toggle('k-hidden',brands.length>0); emptyEl.textContent='No brands found';
    }
    window.adminLoadBrands=function(catId){
        if(!catId){ emptyEl.textContent='Select a category first…'; emptyEl.classList.remove('k-hidden'); list.querySelectorAll('.ksd-item').forEach(function(i){i.remove();}); return; }
        hidden.value=''; label.textContent='Select Brand…'; label.classList.add('placeholder'); trigger.classList.remove('ksd-has-value');
        fetch('/api/brands/by-category/'+catId).then(function(r){return r.json();}).then(renderBrands).catch(function(){ emptyEl.textContent='Failed to load'; emptyEl.classList.remove('k-hidden'); });
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
    var f=document.createElement('select'); f.id='cf-category-select'; f.className='k-hidden';
    var cur=document.getElementById('ksd-cat-val'); if(cur) f.value=cur.value;
    var container=document.getElementById('category-fields-container');
    (container||document.body).appendChild(f);
})();

// ── Price Type ksd ───────────────────────────────────────────────────────
(function(){
    var lblMap={'fixed':'Price (LKR)','negotiable':'Price (LKR)','free':'','per_month':'Price / Month (LKR)','per_year':'Price / Year (LKR)'};
    var t=document.getElementById('alcPlTrigger'),d=document.getElementById('alcPlDropdown'),s=document.getElementById('alcPlSearch');
    var l=document.getElementById('alcPlList'),h=document.getElementById('alcPlVal'),lb=document.getElementById('alcPlLabel');
    var em=document.getElementById('alcPlEmpty'),wrap=document.getElementById('alcPriceWrap'),pl=document.getElementById('alcPriceLabel'),inp=document.getElementById('alcPriceInput');
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

// ── AI Suggest ───────────────────────────────────────────────────────────
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

    var siToggle=document.getElementById('siToggleBtn');
    var taToggle=document.getElementById('taToggleBtn');
    if(siToggle) siToggle.addEventListener('click',function(){toggleLang('si');});
    if(taToggle) taToggle.addEventListener('click',function(){toggleLang('ta');});

    window.toggleLang = function(lang){
        var panel=document.getElementById(lang+'Panel');
        var arrow=document.getElementById(lang+'Arrow');
        var toggleBtn=document.getElementById(lang+'ToggleBtn');
        var translateBtn=document.getElementById('translate'+lang.charAt(0).toUpperCase()+lang.slice(1)+'Btn');
        var opening=!panel.classList.contains('open');
        panel.classList.toggle('open',opening);
        translateBtn.classList.toggle('open',opening);
        arrow.textContent=opening?'▼':'▶';
        toggleBtn.classList.toggle('open',opening);
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

    (function(){
        var overlay=document.createElement('div');
        overlay.id='translateModal';
        overlay.style.cssText='display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;padding:16px';
        overlay.innerHTML=[
            '<div class="modal-card">',
              '<div class="modal-header">',
                '<div class="flex-row flex-row-8">',
                  '<span id="tmModalIcon" class="fs-18"></span>',
                  '<span class="modal-title">Review Translation</span>',
                '</div>',
                '<button class="modal-close-btn" id="tmClose" type="button">&times;</button>',
              '</div>',
              '<div class="modal-body">',
                '<p class="form-label-sm">Translation Preview</p>',
                '<div class="note-box" id="tmPreview"></div>',
              '</div>',
              '<div class="modal-footer">',
                '<button class="btn-cancel" id="tmReject" type="button">✗ Discard</button>',
                '<button class="btn-submit-green" id="tmAccept" type="button">✓ Use This Translation</button>',
              '</div>',
            '</div>'
        ].join('');
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
        fetch('/admin/ai/translate-text',{
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':getToken()},
            body:JSON.stringify({text:plain,language:language})
        })
        .then(function(r){return r.json();})
        .then(function(d){
            if(d.error){var _t=document.createElement('div');_t.textContent='Translation unavailable: '+d.error;_t.className='ka-toast';document.body.appendChild(_t);setTimeout(function(){_t.remove();},3500);return;}
            showTranslateConfirm(d.translated,'','🇱🇰',function(){
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
