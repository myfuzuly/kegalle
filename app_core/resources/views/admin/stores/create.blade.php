@extends('layouts.admin')
@section('title','Create Store')
@section('page','Stores')
@section('heading','Create Store')
@section('subheading','Create a store for a customer — you can hand it over later')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/stores">← Back</a>@endsection

@push('styles')

@endpush

@section('content')
@if($errors->any())
<div class="alert-error">
    <ul class="list-pl">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

@php
$oldUserId = old('user_id','');
$oldCity   = old('city','');
$oldStatus = old('status','approved');
@endphp

<form method="post" action="/admin/stores" enctype="multipart/form-data" id="ascForm">
@csrf
<input type="hidden" name="latitude" value="{{ old('latitude') }}">
<input type="hidden" name="longitude" value="{{ old('longitude') }}">

<div class="asc-shell">

{{-- ── LEFT MAIN ──────────────────────────────────────── --}}
<div>

    {{-- Media --}}
    <div class="asc-card">
        <div class="asc-card-header">
            <div class="asc-card-icon blue">🖼️</div>
            <div>
                <p class="asc-card-title">Store Media</p>
                <p class="asc-card-sub">Logo and banner images</p>
            </div>
        </div>
        <div class="asc-media-row">
            {{-- Logo --}}
            <div class="asc-field">
                <label class="asc-label">Logo</label>
                <input type="file" name="logo" id="ascLogoInput" accept="image/jpeg,image/png,image/webp" class="hidden">
                <div class="asc-logo-zone" id="ascLogoZone">
                    <div class="asc-zone-icon">🏪</div>
                    <div class="asc-zone-label">Click to upload</div>
                </div>
                <span class="asc-hint">Square · JPG/PNG/WEBP · max 2MB</span>
            </div>
            {{-- Banner --}}
            <div class="asc-field">
                <label class="asc-label">Banner</label>
                <input type="file" name="banner" id="ascBannerInput" accept="image/jpeg,image/png,image/webp" class="hidden">
                <div class="asc-banner-zone" id="ascBannerZone">
                    <div class="asc-zone-icon">🖼️</div>
                    <div class="asc-zone-label">Click to upload banner</div>
                </div>
                <span class="asc-hint">1640×624 px recommended · max 3MB</span>
            </div>
        </div>
    </div>

    {{-- Owner --}}
    <div class="asc-card">
        <div class="asc-card-header">
            <div class="asc-card-icon purple">👤</div>
            <div>
                <p class="asc-card-title">Store Owner</p>
                <p class="asc-card-sub">Assign to a registered user</p>
            </div>
        </div>
        <div class="asc-field">
            <label class="asc-label">User <span class="asc-req">*</span></label>
            <input type="hidden" name="user_id" id="ascUserVal" value="{{ $oldUserId }}" required>
            <div class="ksd-wrap">
                <button type="button" class="ksd-trigger {{ $oldUserId ? 'ksd-has-value' : '' }}" id="ascUserTrigger">
                    <span class="ksd-trigger-text {{ $oldUserId ? '' : 'placeholder' }}" id="ascUserLabel">Select a user…</span>
                    <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="ksd-dropdown" id="ascUserDropdown">
                    <div class="ksd-search-row"><input type="text" class="ksd-search" id="ascUserSearch" placeholder="Search user…" autocomplete="off"></div>
                    <div class="ksd-list" id="ascUserList">
                        @foreach($users as $user)
                        <div class="ksd-item {{ $oldUserId==$user->id ? 'ksd-selected':'' }}"
                             data-value="{{ $user->id }}"
                             data-label="{{ $user->name }}"
                             data-search="{{ strtolower($user->name.' '.$user->email) }}">{{ $user->name }} — {{ $user->email }}</div>
                        @endforeach
                        <div class="ksd-empty" id="ascUserEmpty" class="hidden">No users match</div>
                    </div>
                </div>
            </div>
            <span class="asc-hint">This user will own and manage the store. You can change this later.</span>
        </div>
    </div>

    {{-- Store Details --}}
    <div class="asc-card">
        <div class="asc-card-header">
            <div class="asc-card-icon green">🏪</div>
            <div>
                <p class="asc-card-title">Store Details</p>
                <p class="asc-card-sub">Name and contact information</p>
            </div>
        </div>
        <div class="asc-field asc-full" class="mb-14">
            <label class="asc-label">Store Name <span class="asc-req">*</span></label>
            <input name="name" required class="asc-input" value="{{ old('name') }}" placeholder="e.g. Kegalle Auto Parts">
        </div>
        <div class="asc-grid">
            <div class="asc-field">
                <label class="asc-label">Phone</label>
                <input name="phone" id="ascPhone" class="asc-input" value="{{ old('phone') }}" placeholder="077 1234567">
            </div>
            <div class="asc-field">
                <label class="asc-label">Email</label>
                <input type="email" name="email" class="asc-input" value="{{ old('email') }}" placeholder="store@example.com">
            </div>
            <div class="asc-field asc-full">
                <label class="asc-label">WhatsApp</label>
                <label class="asc-wa-same">
                    <input type="checkbox" name="whatsapp_same" id="whatsappSame" value="1" {{ old('whatsapp_same') ? 'checked' : '' }}>
                    Same as phone number
                </label>
                <input name="whatsapp" id="whatsappInput" class="asc-input" value="{{ old('whatsapp') }}" placeholder="94771234567">
            </div>
        </div>
    </div>

    {{-- Location --}}
    <div class="asc-card">
        <div class="asc-card-header">
            <div class="asc-card-icon amber">📍</div>
            <div>
                <p class="asc-card-title">Location</p>
                <p class="asc-card-sub">City and address</p>
            </div>
        </div>
        <div class="asc-grid">
            <div class="asc-field">
                <label class="asc-label">City / Location</label>
                <input type="hidden" name="city" id="ascCityVal" value="{{ $oldCity }}">
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger {{ $oldCity ? 'ksd-has-value' : '' }}" id="ascCityTrigger">
                        <span class="ksd-trigger-text {{ $oldCity ? '' : 'placeholder' }}" id="ascCityLabel">Select location…</span>
                        <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="ascCityDropdown">
                        <div class="ksd-search-row"><input type="text" class="ksd-search" id="ascCitySearch" placeholder="Type to search…" autocomplete="off"></div>
                        <div class="ksd-list" id="ascCityList">
                            <div class="ksd-item {{ !$oldCity ? 'ksd-selected':'' }}" data-value="" data-label="Select location…" data-search="">— No Location —</div>
                            @foreach($locations->whereNull('parent_id') as $parentLoc)
                                <div class="ksd-group-label" data-group="{{ $parentLoc->id }}">{{ strtoupper($parentLoc->name) }}</div>
                                <div class="ksd-item ksd-indent {{ $oldCity===$parentLoc->name ? 'ksd-selected':'' }}"
                                     data-value="{{ $parentLoc->name }}"
                                     data-label="{{ $parentLoc->name }} (All)"
                                     data-search="{{ strtolower($parentLoc->name) }}"
                                     data-group="{{ $parentLoc->id }}">📍 {{ $parentLoc->name }} (All)</div>
                                @foreach($locations->where('parent_id',$parentLoc->id) as $loc)
                                <div class="ksd-item ksd-indent {{ $oldCity===$loc->name ? 'ksd-selected':'' }}"
                                     data-value="{{ $loc->name }}"
                                     data-label="{{ $parentLoc->name }} — {{ $loc->name }}"
                                     data-search="{{ strtolower($parentLoc->name.' '.$loc->name) }}"
                                     data-group="{{ $parentLoc->id }}">{{ $loc->name }}</div>
                                @endforeach
                            @endforeach
                            <div class="ksd-empty" id="ascCityEmpty" class="hidden">No locations match</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="asc-field">
                <label class="asc-label">Address</label>
                <input name="address" class="asc-input" value="{{ old('address') }}" placeholder="Full address…">
            </div>
        </div>
    </div>

    {{-- Description --}}
    <div class="asc-card">
        <div class="asc-card-header">
            <div class="asc-card-icon slate">📄</div>
            <div>
                <p class="asc-card-title">Description</p>
                <p class="asc-card-sub">What this store sells</p>
            </div>
        </div>
        <div class="asc-field">
            <textarea name="description" class="asc-textarea" placeholder="Brief description of what this store sells…">{{ old('description') }}</textarea>
        </div>
    </div>

    {{-- Categories --}}
    <div class="asc-card">
        <div class="asc-card-header">
            <div class="asc-card-icon purple">📂</div>
            <div>
                <p class="asc-card-title">Store Categories</p>
                <p class="asc-card-sub">Select all that apply</p>
            </div>
        </div>
        @php $selectedCats = old('categories', []); @endphp
        @include('admin.partials.category-picker')
    </div>

</div>

{{-- ── RIGHT SIDEBAR ──────────────────────────────────── --}}
<div>

    {{-- Publish --}}
    <div class="asc-card">
        <div class="asc-card-header">
            <div class="asc-card-icon green">🚀</div>
            <div>
                <p class="asc-card-title">Publish</p>
                <p class="asc-card-sub">Save store to marketplace</p>
            </div>
        </div>
        <button type="submit" class="asc-publish-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
            Create Store
        </button>
        <a href="/admin/stores" class="asc-cancel-btn">Cancel</a>
    </div>

    {{-- Status --}}
    <div class="asc-card">
        <div class="asc-card-header">
            <div class="asc-card-icon slate">⚙️</div>
            <div>
                <p class="asc-card-title">Status</p>
                <p class="asc-card-sub">Visibility on marketplace</p>
            </div>
        </div>
        <div class="asc-field">
            <input type="hidden" name="status" id="ascStatusVal" value="{{ $oldStatus }}">
            <div class="ksd-wrap">
                <button type="button" class="ksd-trigger ksd-has-value" id="ascStatusTrigger">
                    <span class="ksd-trigger-text" id="ascStatusLabel">{{ $oldStatus==='approved' ? '✅ Approved' : '⏳ Pending' }}</span>
                    <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="ksd-dropdown" id="ascStatusDropdown">
                    <div class="ksd-list">
                        <div class="ksd-item {{ $oldStatus==='approved' ? 'ksd-selected':'' }}" data-value="approved" data-label="✅ Approved">✅ Approved</div>
                        <div class="ksd-item {{ $oldStatus==='pending' ? 'ksd-selected':'' }}" data-value="pending" data-label="⏳ Pending">⏳ Pending</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Boost --}}
    <div class="asc-card">
        <div class="asc-card-header">
            <div class="asc-card-icon amber">⭐</div>
            <div>
                <p class="asc-card-title">Boost</p>
                <p class="asc-card-sub">Visibility options</p>
            </div>
        </div>
        <div class="asc-checks">
            <label class="asc-check">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured'))>
                ⭐ Featured Store
            </label>
        </div>
    </div>

</div>
</div>
</form>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
// ── Media upload zones ─────────────────────────────────────────────────────
function makeZone(zoneId, inputId, rmClass){
    var zone=document.getElementById(zoneId), input=document.getElementById(inputId);
    if(!zone||!input) return;
    zone.addEventListener('click',function(){ if(!zone.querySelector('img')) input.click(); });
    zone.addEventListener('dragover',function(e){e.preventDefault();zone.style.borderColor='#1b5e20';zone.style.background='#f0fdf4';});
    zone.addEventListener('dragleave',function(){zone.style.borderColor='';zone.style.background='';});
    zone.addEventListener('drop',function(e){e.preventDefault();zone.style.borderColor='';zone.style.background='';if(e.dataTransfer.files[0]) setPreview(e.dataTransfer.files[0]);});
    input.addEventListener('change',function(){if(this.files[0]) setPreview(this.files[0]);});
    function setPreview(file){
        var oldImg=zone.querySelector('img'), oldRm=zone.querySelector('.asc-zone-rm');
        if(oldImg)oldImg.remove(); if(oldRm)oldRm.remove();
        zone.querySelector('.asc-zone-icon').style.display='none';
        zone.querySelector('.asc-zone-label').style.display='none';
        var img=document.createElement('img'); img.className='asc-zone-preview'; img.src=URL.createObjectURL(file); zone.appendChild(img);
        var rm=document.createElement('button'); rm.type='button'; rm.className='asc-zone-rm'; rm.textContent='×';
        rm.addEventListener('click',function(e){
            e.stopPropagation(); img.remove(); rm.remove();
            input.value='';
            zone.querySelector('.asc-zone-icon').style.display=''; zone.querySelector('.asc-zone-label').style.display='';
        });
        zone.appendChild(rm);
    }
}
makeZone('ascLogoZone','ascLogoInput');
makeZone('ascBannerZone','ascBannerInput');

// ── ksd helper ─────────────────────────────────────────────────────────────
function makeKsd(triggerId, dropdownId, searchId, listId, emptyId, hiddenId, labelId, groups){
    var trigger=document.getElementById(triggerId), dropdown=document.getElementById(dropdownId);
    var hidden=document.getElementById(hiddenId), label=document.getElementById(labelId);
    var emptyEl=emptyId?document.getElementById(emptyId):null;
    var open=false;
    var searchEl=searchId?document.getElementById(searchId):null;
    var list=document.getElementById(listId);
    var items=list.querySelectorAll('.ksd-item');
    var grps=list.querySelectorAll('.ksd-group-label');
    function openD(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); if(searchEl){searchEl.value='';filterItems('');searchEl.focus();} open=true; }
    function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    if(searchEl) searchEl.addEventListener('input',function(){ filterItems(this.value.toLowerCase()); });
    items.forEach(function(item){
        item.addEventListener('click',function(){
            var val=this.dataset.value, lbl=this.dataset.label||this.textContent.trim();
            hidden.value=val; label.textContent=lbl; label.classList.toggle('placeholder',!val); trigger.classList.toggle('ksd-has-value',!!val);
            items.forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected'); closeD();
        });
    });
    function filterItems(q){
        var vis={};
        items.forEach(function(i){ var m=!q||(i.dataset.search||'').indexOf(q)!==-1||(i.dataset.label||'').toLowerCase().indexOf(q)!==-1; i.style.display=m?'':'none'; if(m&&i.dataset.group)vis[i.dataset.group]=true; });
        grps.forEach(function(g){ g.style.display=(!q||vis[g.dataset.group])?'':'none'; });
        if(emptyEl) emptyEl.style.display=Array.from(items).some(function(i){return i.style.display!=='none';})?'none':'';
    }
    var cur=hidden.value;
    if(cur){ var pre=Array.from(items).find(function(i){return i.dataset.value===cur;}); if(pre){label.textContent=pre.dataset.label||pre.textContent.trim();label.classList.remove('placeholder');trigger.classList.add('ksd-has-value');} }
}

makeKsd('ascUserTrigger','ascUserDropdown','ascUserSearch','ascUserList','ascUserEmpty','ascUserVal','ascUserLabel');
makeKsd('ascCityTrigger','ascCityDropdown','ascCitySearch','ascCityList','ascCityEmpty','ascCityVal','ascCityLabel');

// Status (no search)
(function(){
    var trigger=document.getElementById('ascStatusTrigger'), dropdown=document.getElementById('ascStatusDropdown');
    var hidden=document.getElementById('ascStatusVal'), label=document.getElementById('ascStatusLabel');
    var items=dropdown.querySelectorAll('.ksd-item'), open=false;
    function openD(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); open=true; }
    function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    items.forEach(function(item){
        item.addEventListener('click',function(){
            hidden.value=this.dataset.value; label.textContent=this.dataset.label||this.textContent.trim();
            items.forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected'); closeD();
        });
    });
})();

// ── Phone normalise ────────────────────────────────────────────────────────
function normLK(v,plus){ var d=v.replace(/\D/g,''); if(!d)return ''; if(d.charAt(0)==='0')d='94'+d.slice(1); if(d.slice(0,2)!=='94')d='94'+d; return (plus?'+':'')+d; }
var phoneInput=document.getElementById('ascPhone');
if(phoneInput) phoneInput.addEventListener('blur',function(){this.value=normLK(this.value,true);});
var waInput=document.getElementById('whatsappInput');
if(waInput) waInput.addEventListener('blur',function(){if(!document.getElementById('whatsappSame').checked)this.value=normLK(this.value,false);});

// ── WhatsApp same as phone ─────────────────────────────────────────────────
var waSame=document.getElementById('whatsappSame');
if(waSame){
    function toggleWaSame(){
        if(waSame.checked){waInput.value=phoneInput?phoneInput.value:'';waInput.disabled=true;}
        else{waInput.disabled=false;}
    }
    waSame.addEventListener('change',toggleWaSame);
    if(phoneInput) phoneInput.addEventListener('input',function(){if(waSame.checked)waInput.value=this.value;});
    if(waSame.checked) toggleWaSame();
}
</script>
@endpush
