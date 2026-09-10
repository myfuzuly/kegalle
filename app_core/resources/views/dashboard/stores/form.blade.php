@extends('layouts.dashboard')
@section('title', $mode === 'edit' ? 'Edit Store' : 'Create Store')
@section('banner_sub', $mode === 'edit' ? 'Update your store profile, branding and contact details.' : 'Set up your business store on Kegalle Marketplace.')
@section('heading', $mode === 'edit' ? 'Edit Store' : 'Create Store')
@section('subheading', $mode === 'edit' ? 'Update your store profile, contact details and images.' : 'Tell buyers about your business. Your store will be reviewed before going live.')

@section('actions')
<a href="{{ $mode === 'edit' ? '/dashboard/stores/'.$store->id : '/dashboard/stores' }}" class="kdl-tb-btn kdl-tb-btn-light">
  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
  Back
</a>
@endsection

@section('content')

@if(session('success'))
<div class="sf-alert-success">✓ {{ session('success') }}</div>
@endif
@if($errors->any())
<div class="sf-alert-error">
  @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
</div>
@endif

<form method="POST" action="{{ $mode === 'edit' ? '/dashboard/stores/'.$store->id : '/dashboard/stores' }}" enctype="multipart/form-data" id="storeForm">
@csrf
@if($mode === 'edit') @method('PUT') @endif

<div class="sf-layout">
  <div>
    {{-- ── SECTION 1: Branding ── --}}
    <div class="sf-card">
      <div class="sf-card-head"><h2>Store Branding</h2><span>Logo &amp; cover photo</span></div>
      <div class="sf-card-body">

        {{-- Logo --}}
        <div class="mb-24">
          <div class="sf-label">Store Logo</div>
          <div class="flex-ac-g16-fw">
            <div class="upload-80" id="logoPreviewWrap" style="cursor:pointer">
              @if($mode === 'edit' && $store->logo)
                <img id="logoPreviewImg" src="{{ asset('storage/'.$store->logo) }}" alt="" class="img-cover">
              @else
                <img class="img-cover-hidden" id="logoPreviewImg" src="">
                <span class="fs-28" id="logoPlaceholder">🏪</span>
              @endif
            </div>
            <div>
              <input type="file" id="logoFileInput" name="logo" accept="image/jpeg,image/png,image/webp" class="hidden">
              <button class="btn-outline-38h" type="button" id="logoUploadBtn">Upload Logo</button>
              <div class="fs12-muted-lh">Square · JPG/PNG/WEBP · max 2 MB<br>Recommended: 400×400 px</div>
              @if($mode === 'edit' && $store->logo)
              <label class="flex-g6-rose-mt6">
                <input type="checkbox" name="remove_logo" value="1"> Remove current logo
              </label>
              @endif
            </div>
          </div>
        </div>

        {{-- Cover Banner --}}
        <div>
          <div class="sf-label">
            Store Cover Photo
            <span class="fw4-fs12-muted"> · JPG/PNG/WEBP · max 3 MB · recommended: 960×355 px (Facebook cover size)</span>
          </div>
          <div class="banner-preview" id="kBannerFrame">
            @if($mode === 'edit' && $store->banner)
              <img class="img-full-cover" id="kBannerPreview" src="{{ asset('storage/'.$store->banner) }}" alt="Current banner">
            @else
              <img class="img-full-hidden" id="kBannerPreview" src="" alt="">
              <div class="abs-center-overlay" id="kBannerPlaceholder">
                <svg width="44" height="44" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                <span class="fw6-14">Click to upload cover photo</span>
                <span class="fs-12">Preferred size: 1640 × 624 px</span>
              </div>
            @endif
            <div class="img-label" id="kBannerDragHint">⟵ Drag to reposition ⟶</div>
            <div class="abs-btm-actions">
              <button class="overlay-btn-dark" type="button" id="kBannerUploadBtn">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Upload Cover
              </button>
              @if($mode === 'edit' && $store->banner)
              <label class="overlay-btn-red">
                <input type="checkbox" name="remove_banner" value="1" id="kRemoveBanner" class="hidden"> Remove
              </label>
              @endif
            </div>
          </div>
          <input type="file" name="banner" id="kBannerFileInput" accept="image/jpeg,image/png,image/webp" class="hidden">
          <input type="hidden" name="banner_position_y" id="kBannerPositionY" value="50">
        </div>
      </div>
    </div>

    {{-- ── SECTION 2: Contact Info ── --}}
    <div class="sf-card">
      <div class="sf-card-head"><h2>Store Information</h2><span>Contact &amp; location</span></div>
      <div class="sf-card-body">
        <div class="mb-16">
          <label class="sf-label">Store Name *</label>
          <input name="name" required value="{{ old('name', $store->name ?? '') }}" placeholder="e.g. Kegalle Electronics" class="sf-input">
        </div>
        <div class="sf-field-2">
          <div>
            <label class="sf-label">Phone</label>
            <input name="phone" id="phoneInput" value="{{ old('phone', $store->phone ?? '') }}" placeholder="07X XXX XXXX" class="sf-input">
          </div>
          <div>
            <label class="sf-label">WhatsApp</label>
            <label class="flex-g7-ptr-mb6">
              <input class="accent-green" type="checkbox" name="whatsapp_same" id="whatsappSame" value="1" {{ old('whatsapp_same') ? 'checked' : '' }}>
              Same as phone
            </label>
            <input name="whatsapp" id="whatsappInput" value="{{ old('whatsapp', $store->whatsapp ?? '') }}" placeholder="07X XXX XXXX" class="sf-input">
          </div>
          <div>
            <label class="sf-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $store->email ?? '') }}" placeholder="store@example.com" class="sf-input">
          </div>
          <div>
            <label class="sf-label">City / Location</label>
            @php
              $sfCity = old('city', $store->city ?? '');
              $sfLoc = $locations->first(fn($l) => $l->name === $sfCity);
              $sfLocParent = $sfLoc && $sfLoc->parent_id ? $locations->firstWhere('id', $sfLoc->parent_id) : null;
              $sfLocLabel = $sfLoc ? ($sfLocParent ? $sfLocParent->name . ' — ' . $sfLoc->name : $sfLoc->name . ' (All)') : '';
            @endphp
            <input type="hidden" name="city" id="sfCityVal" value="{{ $sfCity }}">
            <div class="ksd-wrap">
              <button type="button" class="ksd-trigger {{ $sfCity ? 'ksd-has-value' : '' }}" id="sfLocTrigger">
                <span class="ksd-trigger-text {{ $sfCity ? '' : 'placeholder' }}" id="sfLocLabel">{{ $sfLocLabel ?: 'Select location…' }}</span>
                <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
              </button>
              <div class="ksd-dropdown" id="sfLocDropdown">
                <div class="ksd-search-row"><input type="text" class="ksd-search" id="sfLocSearch" placeholder="Type to search…" autocomplete="off"></div>
                <div class="ksd-list" id="sfLocList">
                  <div class="ksd-item {{ !$sfCity ? 'ksd-selected' : '' }}" data-value="" data-label="" data-search="">— No Location —</div>
                  @foreach($locations->whereNull('parent_id') as $parentLoc)
                    <div class="ksd-group-label" data-group="{{ $parentLoc->id }}">{{ strtoupper($parentLoc->name) }}</div>
                    <div class="ksd-item ksd-indent {{ $sfCity === $parentLoc->name ? 'ksd-selected' : '' }}"
                         data-value="{{ $parentLoc->name }}"
                         data-label="{{ $parentLoc->name }} (All)"
                         data-search="{{ strtolower($parentLoc->name) }}"
                         data-group="{{ $parentLoc->id }}">📍 {{ $parentLoc->name }} (All)</div>
                    @foreach($locations->where('parent_id', $parentLoc->id) as $loc)
                      <div class="ksd-item ksd-indent {{ $sfCity === $loc->name ? 'ksd-selected' : '' }}"
                           data-value="{{ $loc->name }}"
                           data-label="{{ $parentLoc->name }} — {{ $loc->name }}"
                           data-search="{{ strtolower($parentLoc->name . ' ' . $loc->name) }}"
                           data-group="{{ $parentLoc->id }}">{{ $loc->name }}</div>
                    @endforeach
                  @endforeach
                  <div class="ksd-empty" id="sfLocEmpty" style="display:none">No locations match</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="mb-16">
          <label class="sf-label">Address</label>
          <textarea name="address" placeholder="Street, town, district" rows="2" class="sf-textarea">{{ old('address', $store->address ?? '') }}</textarea>
        </div>
        <div>
          <label class="sf-label">Business Description</label>
          <textarea name="description" placeholder="What your store sells, opening hours, delivery options…" rows="4" class="sf-textarea">{{ old('description', $store->description ?? '') }}</textarea>
        </div>
      </div>
    </div>

    {{-- ── SECTION 3: Categories ── --}}
    <div class="sf-card">
      <div class="sf-card-head"><h2>Store Categories</h2><span>Select all that apply</span></div>
      <div class="sf-card-body">
        @php $selectedCats = old('categories', ($mode === 'edit' && $store->exists) ? $store->categories->pluck('id')->toArray() : []); $pickerId = 'storeCategoryPicker'; @endphp
        @include('admin.partials.category-picker')
      </div>
    </div>

    <input type="hidden" name="latitude"  value="{{ old('latitude',  $store->latitude  ?? '') }}">
    <input type="hidden" name="longitude" value="{{ old('longitude', $store->longitude ?? '') }}">

    <button type="submit" id="storeSubmitBtn" class="sf-submit">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      {{ $mode === 'edit' ? 'Save Changes' : 'Submit Store for Approval' }}
    </button>
    @if($mode === 'create')
    <p class="sf-hint">Your store will be reviewed by admin before appearing publicly.</p>
    @endif
  </div>

  {{-- Sidebar --}}
  <div class="sf-sidebar">
    <div class="sf-tip-card border-green100">
      <div class="sf-tip-title">
        <span class="fs-18">💡</span>
        Tips for a great store
      </div>
      <ul class="sf-tip-list">
        <li>Use a clear, square logo — at least 400×400 px.</li>
        <li>Add a cover photo at 1640×624 px for best quality.</li>
        <li>Write a detailed description of what you sell.</li>
        <li>Include your WhatsApp number for direct buyer contact.</li>
        <li>Select all relevant categories to appear in more searches.</li>
      </ul>
    </div>

    <div class="sf-tip-card border-sky100">
      <div class="sf-tip-title text-sky700">
        <span class="fs-18">⏳</span>
        Approval timeline
      </div>
      <ul class="sf-tip-list">
        <li>Stores go live after admin review — usually within 24 hours.</li>
        <li>You'll be notified by email once approved.</li>
        <li>You can edit your store at any time.</li>
      </ul>
    </div>

    @if($mode === 'create')
    <div class="sf-tip-card bg-green50-border">
      <div class="fs13-green-lh">
        <strong class="block-mb4">📦 After approval</strong>
        You can start posting listings under your new store and your business profile will appear publicly on the marketplace.
      </div>
    </div>
    @endif
  </div>
</div>
</form>

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded', function(){

    // ── Logo preview ─────────────────────────────────────────
    var logoInput = document.getElementById('logoFileInput');
    var logoImg   = document.getElementById('logoPreviewImg');
    var logoPh    = document.getElementById('logoPlaceholder');
    if(logoInput){
        logoInput.addEventListener('change', function(){
            var f = this.files[0];
            if(!f) return;
            logoImg.src = URL.createObjectURL(f);
            logoImg.style.display = 'block';
            if(logoPh) logoPh.style.display = 'none';
        });
    }
    var logoWrap = document.getElementById('logoPreviewWrap');
    var logoBtn  = document.getElementById('logoUploadBtn');
    if(logoWrap) logoWrap.addEventListener('click', function(){ if(logoInput) logoInput.click(); });
    if(logoBtn)  logoBtn.addEventListener('click',  function(){ if(logoInput) logoInput.click(); });
    if(logoBtn){
        logoBtn.addEventListener('mouseenter', function(){ this.style.background='#f1f5f9'; });
        logoBtn.addEventListener('mouseleave', function(){ this.style.background=''; });
    }

    // ── Banner editor ─────────────────────────────────────────
    var frame     = document.getElementById('kBannerFrame');
    var preview   = document.getElementById('kBannerPreview');
    var fileInput = document.getElementById('kBannerFileInput');
    var uploadBtn = document.getElementById('kBannerUploadBtn');
    var placeholder = document.getElementById('kBannerPlaceholder');
    var hint      = document.getElementById('kBannerDragHint');
    var posYInput = document.getElementById('kBannerPositionY');
    var removeCb  = document.getElementById('kRemoveBanner');

    if(frame && preview && fileInput){
        var posY = 50, isDragging = false, startY = 0, startPosY = 0;

        function applyPos(y){
            posY = Math.max(0, Math.min(100, y));
            preview.style.objectPosition = 'center ' + posY + '%';
            if(posYInput) posYInput.value = Math.round(posY);
        }

        if(uploadBtn) uploadBtn.addEventListener('click', function(){ fileInput.click(); });
        if(placeholder) placeholder.addEventListener('click', function(){ fileInput.click(); });

        fileInput.addEventListener('change', function(){
            var f = this.files[0]; if(!f) return;
            preview.src = URL.createObjectURL(f);
            preview.style.display = '';
            if(placeholder) placeholder.style.display = 'none';
            if(removeCb) removeCb.checked = false;
            applyPos(30);
            preview.onload = function(){
                if(hint){ hint.style.display = 'block'; setTimeout(function(){ hint.style.display = 'none'; }, 2500); }
            };
        });

        frame.addEventListener('mousedown', function(e){ if(e.button===0 && preview.src){ isDragging=true; startY=e.clientY; startPosY=posY; frame.style.cursor='grabbing'; }});
        window.addEventListener('mousemove', function(e){
            if(!isDragging) return;
            var fh = frame.getBoundingClientRect().height;
            var iw = frame.getBoundingClientRect().width;
            var ih = preview.naturalHeight ? (iw/preview.naturalWidth)*preview.naturalHeight : fh*2;
            var pxPer = (ih-fh)/100;
            applyPos(startPosY + (pxPer>0 ? -(e.clientY-startY)/pxPer : 0));
        });
        window.addEventListener('mouseup', function(){ isDragging=false; frame.style.cursor='grab'; });
        frame.addEventListener('touchstart', function(e){ if(preview.src){ isDragging=true; startY=e.touches[0].clientY; startPosY=posY; }}, {passive:true});
        window.addEventListener('touchmove', function(e){
            if(!isDragging) return;
            var fh = frame.getBoundingClientRect().height;
            var iw = frame.getBoundingClientRect().width;
            var ih = preview.naturalHeight ? (iw/preview.naturalWidth)*preview.naturalHeight : fh*2;
            var pxPer = (ih-fh)/100;
            applyPos(startPosY + (pxPer>0 ? -(e.touches[0].clientY-startY)/pxPer : 0));
        }, {passive:true});
        window.addEventListener('touchend', function(){ isDragging=false; });

        if(removeCb){
            removeCb.addEventListener('change', function(){
                if(removeCb.checked){ preview.style.display='none'; if(placeholder) placeholder.style.display='flex'; }
                else { preview.style.display=''; if(placeholder) placeholder.style.display='none'; }
            });
        }
    }

    // ── WhatsApp same-as-phone ────────────────────────────────
    var phoneInput = document.getElementById('phoneInput');
    var waInput    = document.getElementById('whatsappInput');
    var waSame     = document.getElementById('whatsappSame');

    function normLK(v, plus){
        var d = v.replace(/\D/g,'');
        if(!d) return '';
        if(d.charAt(0)==='0') d = '94'+d.slice(1);
        if(d.slice(0,2)!=='94') d = '94'+d;
        return (plus?'+':'')+d;
    }
    if(phoneInput) phoneInput.addEventListener('blur', function(){ this.value = normLK(this.value, true); });
    if(waInput)    waInput.addEventListener('blur',    function(){ if(!waSame||!waSame.checked) this.value = normLK(this.value, false); });
    if(waSame){
        function toggleWaSame(){
            if(waSame.checked){ waInput.value = phoneInput?phoneInput.value:''; waInput.disabled=true; waInput.style.opacity='.5'; }
            else { waInput.disabled=false; waInput.style.opacity='1'; }
        }
        waSame.addEventListener('change', toggleWaSame);
        if(waSame.checked) toggleWaSame();
    }

    // ── Location ksd picker ───────────────────────────────────
    (function(){
        var trigger=document.getElementById('sfLocTrigger'), dropdown=document.getElementById('sfLocDropdown');
        var search=document.getElementById('sfLocSearch'), list=document.getElementById('sfLocList');
        var hidden=document.getElementById('sfCityVal'), label=document.getElementById('sfLocLabel');
        var emptyEl=document.getElementById('sfLocEmpty');
        if(!trigger) return;
        var items=list.querySelectorAll('.ksd-item'), groups=list.querySelectorAll('.ksd-group-label');
        var open=false;
        function openD(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); search.value=''; filterItems(''); search.focus(); open=true; }
        function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
        trigger.addEventListener('click', function(e){ e.stopPropagation(); open?closeD():openD(); });
        document.addEventListener('click', function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
        search.addEventListener('input', function(){ filterItems(this.value.toLowerCase()); });
        items.forEach(function(item){
            item.addEventListener('click', function(){
                hidden.value=this.dataset.value;
                label.textContent=this.dataset.label||'Select location…';
                label.classList.toggle('placeholder',!this.dataset.value);
                trigger.classList.toggle('ksd-has-value',!!this.dataset.value);
                items.forEach(function(i){ i.classList.remove('ksd-selected'); });
                this.classList.add('ksd-selected'); closeD();
            });
        });
        function filterItems(q){
            var visG={};
            items.forEach(function(i){ var m=!q||(i.dataset.search||'').indexOf(q)!==-1||(i.dataset.label||'').toLowerCase().indexOf(q)!==-1; i.style.display=m?'':'none'; if(m&&i.dataset.group) visG[i.dataset.group]=true; });
            groups.forEach(function(g){ g.style.display=(!q||visG[g.dataset.group])?'':'none'; });
            if(emptyEl) emptyEl.style.display=Array.from(items).some(function(i){ return i.style.display!=='none'; })?'none':'';
        }
    })();

    // Category picker handled inline by category-picker.blade.php

    // ── Submit guard ──────────────────────────────────────────
    var btn = document.getElementById('storeSubmitBtn');
    document.getElementById('storeForm').addEventListener('submit', function(){
        if(btn){ btn.disabled=true; btn.style.opacity='.7'; }
    });

});
</script>
@endpush
