@extends('layouts.admin')
@section('title','Add User')
@section('page','Users')
@section('heading','Add User')
@section('subheading','Create a new user account manually')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/users">← Back</a>@endsection

@push('styles')

@endpush

@section('content')
@if($errors->any())
<div class="alert-error">
    <ul class="list-pl">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="post" action="/admin/users" enctype="multipart/form-data" id="aucForm">
@csrf

<div class="auc-shell">

{{-- ── LEFT MAIN ────────────────────────────────────────── --}}
<div>

    {{-- Profile --}}
    <div class="auc-card">
        <div class="auc-card-header">
            <div class="auc-card-icon blue">👤</div>
            <div>
                <p class="auc-card-title">Profile</p>
                <p class="auc-card-sub">Basic account information</p>
            </div>
        </div>
        <div class="auc-avatar-row" class="mb-18">
            <input type="file" name="avatar" id="aucAvatarInput" accept="image/jpeg,image/png,image/webp" class="hidden">
            <div class="auc-avatar-zone" id="aucAvatarZone">
                <div class="auc-zone-icon">🙂</div>
                <div class="auc-zone-label">Photo</div>
            </div>
            <div class="auc-avatar-meta">
                <span class="auc-hint">Optional profile photo<br>JPG/PNG · max 2MB</span>
                <button class="btn-outline-sm" type="button" data-trigger-click="aucAvatarInput">
                    📷 Upload Photo
                </button>
            </div>
        </div>
        <div class="auc-grid">
            <div class="auc-field auc-full">
                <label class="auc-label">Full Name <span class="auc-req">*</span></label>
                <input name="name" required class="auc-input" value="{{ old('name') }}" placeholder="e.g. Kamal Perera">
            </div>
            <div class="auc-field">
                <label class="auc-label">Email <span class="auc-req">*</span></label>
                <input type="email" name="email" required class="auc-input" value="{{ old('email') }}" placeholder="user@example.com">
            </div>
            <div class="auc-field">
                <label class="auc-label">Phone</label>
                <input name="phone" id="aucPhone" class="auc-input" value="{{ old('phone') }}" placeholder="077 1234567">
            </div>
        </div>
    </div>

    {{-- Location --}}
    <div class="auc-card">
        <div class="auc-card-header">
            <div class="auc-card-icon amber">📍</div>
            <div>
                <p class="auc-card-title">Location</p>
                <p class="auc-card-sub">User's city or district</p>
            </div>
        </div>
        <div class="auc-field">
            <label class="auc-label">Location</label>
            <input type="hidden" name="location_id" id="aucLocVal" value="{{ old('location_id') }}">
            <div class="ksd-wrap">
                <button type="button" class="ksd-trigger {{ old('location_id') ? 'ksd-has-value':'' }}" id="aucLocTrigger">
                    <span class="ksd-trigger-text {{ old('location_id') ? '':'placeholder' }}" id="aucLocLabel">Select location…</span>
                    <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="ksd-dropdown" id="aucLocDropdown">
                    <div class="ksd-search-row"><input type="text" class="ksd-search" id="aucLocSearch" placeholder="Type to search…" autocomplete="off"></div>
                    <div class="ksd-list" id="aucLocList">
                        <div class="ksd-item" data-value="" data-label="No Location" data-search="">— No Location —</div>
                        @foreach($locations->whereNull('parent_id') as $parent)
                            <div class="ksd-item {{ old('location_id')==$parent->id ? 'ksd-selected':'' }}"
                                 data-value="{{ $parent->id }}"
                                 data-label="{{ $parent->name }}"
                                 data-search="{{ strtolower($parent->name) }}">📍 {{ $parent->name }}</div>
                            @foreach($locations->where('parent_id',$parent->id) as $loc)
                            <div class="ksd-item {{ old('location_id')==$loc->id ? 'ksd-selected':'' }} pl-28"
                                 data-value="{{ $loc->id }}"
                                 data-label="{{ $parent->name }} — {{ $loc->name }}"
                                 data-search="{{ strtolower($parent->name.' '.$loc->name) }}">{{ $loc->name }}</div>
                            @endforeach
                        @endforeach
                        <div class="ksd-empty" id="aucLocEmpty" class="hidden">No locations match</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Password --}}
    <div class="auc-card">
        <div class="auc-card-header">
            <div class="auc-card-icon purple">🔒</div>
            <div>
                <p class="auc-card-title">Password</p>
                <p class="auc-card-sub">Set initial account password</p>
            </div>
        </div>
        <div class="auc-grid">
            <div class="auc-field">
                <label class="auc-label">Password <span class="auc-req">*</span></label>
                <input type="password" name="password" id="aucPwd" required class="auc-input" placeholder="Min 8 characters">
                <div class="auc-strength"><div class="auc-strength-bar" id="aucPwdBar"></div></div>
                <div class="auc-strength-txt" id="aucPwdTxt"></div>
            </div>
            <div class="auc-field">
                <label class="auc-label">Confirm Password <span class="auc-req">*</span></label>
                <input type="password" name="password_confirmation" id="aucPwdConf" required class="auc-input" placeholder="Repeat password">
                <div class="auc-strength-txt text-rose600" id="aucPwdMatch"></div>
            </div>
        </div>
        <p class="auc-hint" class="mt-8">The user can change this after logging in. You should share it securely.</p>
    </div>

</div>

{{-- ── RIGHT SIDEBAR ──────────────────────────────────── --}}
<div>

    {{-- Publish --}}
    <div class="auc-card">
        <div class="auc-card-header">
            <div class="auc-card-icon green">✅</div>
            <div>
                <p class="auc-card-title">Create Account</p>
                <p class="auc-card-sub">Save user to the system</p>
            </div>
        </div>
        <button type="submit" class="auc-publish-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
            Create User
        </button>
        <a href="/admin/users" class="auc-cancel-btn">Cancel</a>
    </div>

    {{-- Role --}}
    <div class="auc-card">
        <div class="auc-card-header">
            <div class="auc-card-icon purple">🛡️</div>
            <div>
                <p class="auc-card-title">Role</p>
                <p class="auc-card-sub">Account permission level</p>
            </div>
        </div>
        @php $oldRole = old('role','user'); @endphp
        <div class="auc-field">
            <input type="hidden" name="role" id="aucRoleVal" value="{{ $oldRole }}">
            <div class="ksd-wrap">
                <button type="button" class="ksd-trigger ksd-has-value" id="aucRoleTrigger">
                    <span class="ksd-trigger-text" id="aucRoleLabel">
                        @if($oldRole==='user') 👤 User
                        @elseif($oldRole==='seller') 🏪 Seller
                        @elseif($oldRole==='admin') 🔧 Admin
                        @else ⭐ Super Admin @endif
                    </span>
                    <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="ksd-dropdown" id="aucRoleDropdown">
                    <div class="ksd-list">
                        <div class="ksd-item {{ $oldRole==='user' ? 'ksd-selected':'' }}" data-value="user" data-label="👤 User">👤 User</div>
                        <div class="ksd-item {{ $oldRole==='seller' ? 'ksd-selected':'' }}" data-value="seller" data-label="🏪 Seller">🏪 Seller</div>
                        <div class="ksd-item {{ $oldRole==='admin' ? 'ksd-selected':'' }}" data-value="admin" data-label="🔧 Admin">🔧 Admin</div>
                        <div class="ksd-item {{ $oldRole==='super_admin' ? 'ksd-selected':'' }}" data-value="super_admin" data-label="⭐ Super Admin">⭐ Super Admin</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status --}}
    <div class="auc-card">
        <div class="auc-card-header">
            <div class="auc-card-icon slate">⚙️</div>
            <div>
                <p class="auc-card-title">Status</p>
                <p class="auc-card-sub">Account access state</p>
            </div>
        </div>
        @php $oldStatus = old('status','active'); @endphp
        <div class="auc-field">
            <input type="hidden" name="status" id="aucStatusVal" value="{{ $oldStatus }}">
            <div class="ksd-wrap">
                <button type="button" class="ksd-trigger ksd-has-value" id="aucStatusTrigger">
                    <span class="ksd-trigger-text" id="aucStatusLabel">
                        @if($oldStatus==='active') ✅ Active
                        @elseif($oldStatus==='pending') ⏳ Pending
                        @elseif($oldStatus==='suspended') 🚫 Suspended
                        @else ⬜ Inactive @endif
                    </span>
                    <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="ksd-dropdown" id="aucStatusDropdown">
                    <div class="ksd-list">
                        <div class="ksd-item {{ $oldStatus==='active' ? 'ksd-selected':'' }}" data-value="active" data-label="✅ Active">✅ Active</div>
                        <div class="ksd-item {{ $oldStatus==='pending' ? 'ksd-selected':'' }}" data-value="pending" data-label="⏳ Pending">⏳ Pending</div>
                        <div class="ksd-item {{ $oldStatus==='suspended' ? 'ksd-selected':'' }}" data-value="suspended" data-label="🚫 Suspended">🚫 Suspended</div>
                        <div class="ksd-item {{ $oldStatus==='inactive' ? 'ksd-selected':'' }}" data-value="inactive" data-label="⬜ Inactive">⬜ Inactive</div>
                    </div>
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
// ── Trigger-click buttons ──────────────────────────────────────────────────
document.querySelectorAll('[data-trigger-click]').forEach(function(btn){
    btn.addEventListener('click',function(){ var t=document.getElementById(btn.dataset.triggerClick); if(t) t.click(); });
});

// ── Avatar zone ────────────────────────────────────────────────────────────
(function(){
    var zone=document.getElementById('aucAvatarZone'), input=document.getElementById('aucAvatarInput');
    if(!zone) return;
    zone.addEventListener('click',function(){ if(!zone.querySelector('img')) input.click(); });
    input.addEventListener('change',function(){ if(this.files[0]) setPreview(this.files[0]); });
    function setPreview(file){
        var old=zone.querySelector('img'),rm=zone.querySelector('.auc-zone-rm');
        if(old)old.remove();if(rm)rm.remove();
        zone.querySelector('.auc-zone-icon').style.display='none';
        zone.querySelector('.auc-zone-label').style.display='none';
        var img=document.createElement('img');img.className='auc-zone-preview';img.src=URL.createObjectURL(file);zone.appendChild(img);
        var r=document.createElement('button');r.type='button';r.className='auc-zone-rm';r.textContent='×';
        r.addEventListener('click',function(e){e.stopPropagation();img.remove();r.remove();input.value='';zone.querySelector('.auc-zone-icon').style.display='';zone.querySelector('.auc-zone-label').style.display='';});
        zone.appendChild(r);
    }
})();

// ── Generic ksd (no search) ────────────────────────────────────────────────
function simpleKsd(triggerId, dropdownId, hiddenId, labelId){
    var trigger=document.getElementById(triggerId), dropdown=document.getElementById(dropdownId);
    var hidden=document.getElementById(hiddenId), label=document.getElementById(labelId);
    var items=dropdown.querySelectorAll('.ksd-item'), open=false;
    function openD(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); open=true; }
    function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    items.forEach(function(item){
        item.addEventListener('click',function(){
            hidden.value=this.dataset.value; label.textContent=this.dataset.label||this.textContent.trim();
            label.classList.remove('placeholder'); trigger.classList.add('ksd-has-value');
            items.forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected'); closeD();
        });
    });
}
simpleKsd('aucRoleTrigger','aucRoleDropdown','aucRoleVal','aucRoleLabel');
simpleKsd('aucStatusTrigger','aucStatusDropdown','aucStatusVal','aucStatusLabel');

// ── Location ksd (with search) ─────────────────────────────────────────────
(function(){
    var trigger=document.getElementById('aucLocTrigger'), dropdown=document.getElementById('aucLocDropdown');
    var hidden=document.getElementById('aucLocVal'), label=document.getElementById('aucLocLabel');
    var searchEl=document.getElementById('aucLocSearch'), list=document.getElementById('aucLocList');
    var emptyEl=document.getElementById('aucLocEmpty');
    var items=list.querySelectorAll('.ksd-item'), open=false;
    function openD(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); searchEl.value=''; filterItems(''); searchEl.focus(); open=true; }
    function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    searchEl.addEventListener('input',function(){ filterItems(this.value.toLowerCase()); });
    items.forEach(function(item){
        item.addEventListener('click',function(){
            hidden.value=this.dataset.value; label.textContent=this.dataset.label||this.textContent.trim();
            label.classList.toggle('placeholder',!this.dataset.value); trigger.classList.toggle('ksd-has-value',!!this.dataset.value);
            items.forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected'); closeD();
        });
    });
    function filterItems(q){
        var any=false;
        items.forEach(function(i){ var m=!q||(i.dataset.search||'').indexOf(q)!==-1; i.style.display=m?'':'none'; if(m)any=true; });
        if(emptyEl) emptyEl.style.display=any?'none':'';
    }
    var cur=hidden.value;
    if(cur){ var pre=Array.from(items).find(function(i){return i.dataset.value===cur;}); if(pre){label.textContent=pre.dataset.label||pre.textContent.trim();label.classList.remove('placeholder');trigger.classList.add('ksd-has-value');} }
})();

// ── Phone normalize ────────────────────────────────────────────────────────
(function(){
    var ph=document.getElementById('aucPhone');
    if(!ph) return;
    ph.addEventListener('blur',function(){
        var d=this.value.replace(/\D/g,'');
        if(!d)return;
        if(d.charAt(0)==='0') d='94'+d.slice(1);
        if(d.slice(0,2)!=='94') d='94'+d;
        this.value='+'+d;
    });
})();

// ── Password strength ──────────────────────────────────────────────────────
(function(){
    var pwd=document.getElementById('aucPwd'), conf=document.getElementById('aucPwdConf');
    var bar=document.getElementById('aucPwdBar'), txt=document.getElementById('aucPwdTxt'), matchTxt=document.getElementById('aucPwdMatch');
    if(!pwd) return;
    pwd.addEventListener('input',function(){
        var v=this.value, score=0;
        if(v.length>=8) score++;
        if(/[A-Z]/.test(v)) score++;
        if(/[0-9]/.test(v)) score++;
        if(/[^A-Za-z0-9]/.test(v)) score++;
        var pct=[0,30,55,80,100][score];
        var clr=['','#ef4444','#f97316','#eab308','#22c55e'][score];
        var lbl=['','Weak','Fair','Good','Strong'][score];
        bar.style.width=pct+'%'; bar.style.background=clr; txt.textContent=v?lbl:''; txt.style.color=clr;
        checkMatch();
    });
    conf.addEventListener('input',checkMatch);
    function checkMatch(){
        if(!conf.value){matchTxt.textContent='';return;}
        matchTxt.textContent=conf.value===pwd.value?'':'Passwords do not match';
    }
})();
</script>
@endpush
