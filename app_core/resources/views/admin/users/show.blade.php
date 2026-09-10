@extends('layouts.admin')
@section('title','User #'.$user->id)
@section('page','Users')
@section('heading',$user->name)
@section('subheading','User profile · ID #'.$user->id)
@section('actions')
<a class="ka-btn ka-btn-light" href="/admin/users">← All Users</a>
@endsection

@section('content')

@if(session('success'))
<div class="alert-success mb-16">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert-error mb-16"><ul class="list-pl">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form method="POST" action="/admin/users/{{ $user->id }}" enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="auc-shell">

{{-- ── LEFT ──────────────────────────────────────────────────── --}}
<div>

    {{-- Profile --}}
    <div class="auc-card">
        <div class="auc-card-header">
            <div class="auc-card-icon blue">👤</div>
            <div>
                <p class="auc-card-title">Profile</p>
                <p class="auc-card-sub">Joined {{ $user->created_at?->format('d M Y') }} · Last login {{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</p>
            </div>
        </div>

        {{-- Avatar --}}
        <div class="auc-avatar-row" style="margin-bottom:18px">
            <input type="file" name="avatar" id="aucAvatarInput" accept="image/jpeg,image/png,image/webp" class="hidden">
            <div class="auc-avatar-zone" id="aucAvatarZone" style="position:relative">
                @if($user->avatar)
                    <img class="auc-zone-preview" src="{{ $user->avatar }}" alt="">
                @else
                    <div class="auc-zone-icon">{{ strtoupper(substr($user->name,0,1)) }}</div>
                    <div class="auc-zone-label">Photo</div>
                @endif
            </div>
            <div class="auc-avatar-meta">
                <span class="auc-hint">Profile photo<br>JPG/PNG/WEBP · max 2MB</span>
                <button class="btn-outline-sm" type="button" id="aucAvatarBtn">📷 Change Photo</button>
            </div>
        </div>

        <div class="auc-grid">
            <div class="auc-field auc-full">
                <label class="auc-label">Full Name <span class="auc-req">*</span></label>
                <input name="name" required class="auc-input" value="{{ old('name',$user->name) }}">
            </div>
            <div class="auc-field">
                <label class="auc-label">Email <span class="auc-req">*</span></label>
                <input type="email" name="email" required class="auc-input" value="{{ old('email',$user->email) }}">
            </div>
            <div class="auc-field">
                <label class="auc-label">Phone</label>
                <input name="phone" id="aucPhone" class="auc-input" value="{{ old('phone',$user->phone) }}" placeholder="077 1234567">
            </div>
        </div>
    </div>

    {{-- Location --}}
    <div class="auc-card">
        <div class="auc-card-header">
            <div class="auc-card-icon amber">📍</div>
            <div><p class="auc-card-title">Location</p><p class="auc-card-sub">User's city or district</p></div>
        </div>
        <div class="auc-field">
            <input type="hidden" name="location_id" id="aucLocVal" value="{{ old('location_id',$user->location_id) }}">
            <div class="ksd-wrap">
                <button type="button" class="ksd-trigger {{ $user->location_id ? 'ksd-has-value':'' }}" id="aucLocTrigger">
                    <span class="ksd-trigger-text {{ $user->location_id ? '':'placeholder' }}" id="aucLocLabel">
                        {{ $user->location?->name ?? 'Select location…' }}
                    </span>
                    <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="ksd-dropdown" id="aucLocDropdown">
                    <div class="ksd-search-row"><input type="text" class="ksd-search" id="aucLocSearch" placeholder="Type to search…" autocomplete="off"></div>
                    <div class="ksd-list" id="aucLocList">
                        <div class="ksd-item" data-value="" data-label="No Location" data-search="">— No Location —</div>
                        @foreach($locations->whereNull('parent_id') as $parent)
                            <div class="ksd-item {{ old('location_id',$user->location_id)==$parent->id ? 'ksd-selected':'' }}"
                                 data-value="{{ $parent->id }}" data-label="{{ $parent->name }}" data-search="{{ strtolower($parent->name) }}">📍 {{ $parent->name }}</div>
                            @foreach($locations->where('parent_id',$parent->id) as $loc)
                            <div class="ksd-item {{ old('location_id',$user->location_id)==$loc->id ? 'ksd-selected':'' }} pl-28"
                                 data-value="{{ $loc->id }}" data-label="{{ $parent->name }} — {{ $loc->name }}" data-search="{{ strtolower($parent->name.' '.$loc->name) }}">{{ $loc->name }}</div>
                            @endforeach
                        @endforeach
                        <div class="ksd-empty" id="aucLocEmpty" class="hidden">No locations match</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reset Password --}}
    <div class="auc-card">
        <div class="auc-card-header">
            <div class="auc-card-icon purple">🔒</div>
            <div><p class="auc-card-title">Reset Password</p><p class="auc-card-sub">Leave blank to keep current password</p></div>
        </div>
        <div class="auc-grid">
            <div class="auc-field">
                <label class="auc-label">New Password</label>
                <input type="password" name="password" id="aucPwd" class="auc-input" placeholder="Min 8 characters">
            </div>
            <div class="auc-field">
                <label class="auc-label">Confirm Password</label>
                <input type="password" name="password_confirmation" id="aucPwdConf" class="auc-input" placeholder="Repeat password">
                <div class="auc-strength-txt text-rose600 mt-4" id="aucPwdMatch"></div>
            </div>
        </div>
    </div>

    {{-- Recent Listings --}}
    @if($listings->count())
    <div class="auc-card">
        <div class="auc-card-header">
            <div class="auc-card-icon green">📋</div>
            <div><p class="auc-card-title">Recent Listings</p><p class="auc-card-sub">Last {{ $listings->count() }} ads</p></div>
        </div>
        <div style="display:flex;flex-direction:column;gap:8px">
            @foreach($listings as $l)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 10px;background:var(--ka-bg-alt,#f8fafc);border-radius:8px;font-size:13px">
                <a href="/admin/listings/{{ $l->id }}" style="color:var(--ka-accent,#16a34a);font-weight:500;text-decoration:none">{{ Str::limit($l->title,50) }}</a>
                <span style="color:var(--ka-text-muted,#6b7280)">{{ $l->created_at?->format('d M Y') }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Stores --}}
    @if($stores->count())
    <div class="auc-card">
        <div class="auc-card-header">
            <div class="auc-card-icon amber">🏪</div>
            <div><p class="auc-card-title">Stores</p><p class="auc-card-sub">{{ $stores->count() }} store(s)</p></div>
        </div>
        <div style="display:flex;flex-direction:column;gap:8px">
            @foreach($stores as $s)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 10px;background:var(--ka-bg-alt,#f8fafc);border-radius:8px;font-size:13px">
                <a href="/admin/stores/{{ $s->id }}" style="color:var(--ka-accent,#16a34a);font-weight:500;text-decoration:none">{{ $s->name }}</a>
                <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;background:{{ $s->is_approved ? '#dcfce7' : '#fef9c3' }};color:{{ $s->is_approved ? '#15803d' : '#92400e' }}">{{ $s->is_approved ? 'Approved' : 'Pending' }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- ── RIGHT SIDEBAR ──────────────────────────────────── --}}
<div>

    {{-- Save --}}
    <div class="auc-card">
        <div class="auc-card-header">
            <div class="auc-card-icon green">✅</div>
            <div><p class="auc-card-title">Save Changes</p><p class="auc-card-sub">Update user account</p></div>
        </div>
        <button type="submit" class="auc-publish-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
            Save Changes
        </button>
        <a href="/admin/users" class="auc-cancel-btn">Back to Users</a>
    </div>

    {{-- Account Info --}}
    <div class="auc-card">
        <div class="auc-card-header">
            <div class="auc-card-icon slate">ℹ️</div>
            <div><p class="auc-card-title">Account Info</p><p class="auc-card-sub">Read-only details</p></div>
        </div>
        <div style="display:flex;flex-direction:column;gap:10px;font-size:13px">
            <div style="display:flex;justify-content:space-between"><span style="color:var(--ka-text-muted,#6b7280)">ID</span><strong>#{{ $user->id }}</strong></div>
            <div style="display:flex;justify-content:space-between"><span style="color:var(--ka-text-muted,#6b7280)">Registered</span><strong>{{ $user->created_at?->format('d M Y') }}</strong></div>
            <div style="display:flex;justify-content:space-between"><span style="color:var(--ka-text-muted,#6b7280)">Email Verified</span>
                <strong style="color:{{ $user->email_verified_at ? '#16a34a':'#ef4444' }}">{{ $user->email_verified_at ? 'Yes' : 'No' }}</strong></div>
            <div style="display:flex;justify-content:space-between"><span style="color:var(--ka-text-muted,#6b7280)">Listings</span><strong>{{ $user->listings()->count() }}</strong></div>
            <div style="display:flex;justify-content:space-between"><span style="color:var(--ka-text-muted,#6b7280)">Stores</span><strong>{{ $stores->count() }}</strong></div>
        </div>
    </div>

    {{-- Role --}}
    <div class="auc-card">
        <div class="auc-card-header">
            <div class="auc-card-icon purple">🛡️</div>
            <div><p class="auc-card-title">Role</p><p class="auc-card-sub">Account permission level</p></div>
        </div>
        @php $curRole = old('role',$user->role ?? 'user'); @endphp
        <div class="auc-field">
            <input type="hidden" name="role" id="aucRoleVal" value="{{ $curRole }}">
            <div class="ksd-wrap">
                <button type="button" class="ksd-trigger ksd-has-value" id="aucRoleTrigger">
                    <span class="ksd-trigger-text" id="aucRoleLabel">
                        @if($curRole==='user') 👤 User
                        @elseif($curRole==='seller') 🏪 Seller
                        @elseif($curRole==='admin') 🔧 Admin
                        @else ⭐ Super Admin @endif
                    </span>
                    <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="ksd-dropdown" id="aucRoleDropdown">
                    <div class="ksd-list">
                        <div class="ksd-item {{ $curRole==='user'?'ksd-selected':'' }}" data-value="user" data-label="👤 User">👤 User</div>
                        <div class="ksd-item {{ $curRole==='seller'?'ksd-selected':'' }}" data-value="seller" data-label="🏪 Seller">🏪 Seller</div>
                        <div class="ksd-item {{ $curRole==='admin'?'ksd-selected':'' }}" data-value="admin" data-label="🔧 Admin">🔧 Admin</div>
                        <div class="ksd-item {{ $curRole==='super_admin'?'ksd-selected':'' }}" data-value="super_admin" data-label="⭐ Super Admin">⭐ Super Admin</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status --}}
    <div class="auc-card">
        <div class="auc-card-header">
            <div class="auc-card-icon slate">⚙️</div>
            <div><p class="auc-card-title">Status</p><p class="auc-card-sub">Account access state</p></div>
        </div>
        @php $curStatus = old('status',$user->status ?? 'active'); @endphp
        <div class="auc-field">
            <input type="hidden" name="status" id="aucStatusVal" value="{{ $curStatus }}">
            <div class="ksd-wrap">
                <button type="button" class="ksd-trigger ksd-has-value" id="aucStatusTrigger">
                    <span class="ksd-trigger-text" id="aucStatusLabel">
                        @if($curStatus==='active') ✅ Active
                        @elseif($curStatus==='pending') ⏳ Pending
                        @elseif($curStatus==='suspended') 🚫 Suspended
                        @else ⬜ Inactive @endif
                    </span>
                    <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="ksd-dropdown" id="aucStatusDropdown">
                    <div class="ksd-list">
                        <div class="ksd-item {{ $curStatus==='active'?'ksd-selected':'' }}" data-value="active" data-label="✅ Active">✅ Active</div>
                        <div class="ksd-item {{ $curStatus==='pending'?'ksd-selected':'' }}" data-value="pending" data-label="⏳ Pending">⏳ Pending</div>
                        <div class="ksd-item {{ $curStatus==='suspended'?'ksd-selected':'' }}" data-value="suspended" data-label="🚫 Suspended">🚫 Suspended</div>
                        <div class="ksd-item {{ $curStatus==='inactive'?'ksd-selected':'' }}" data-value="inactive" data-label="⬜ Inactive">⬜ Inactive</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Store Limit --}}
    <div class="auc-card">
        <div class="auc-card-header">
            <div class="auc-card-icon green">🏬</div>
            <div><p class="auc-card-title">Store Limit</p><p class="auc-card-sub">Max stores this user can own</p></div>
        </div>
        <form method="POST" action="/admin/users/{{ $user->id }}/store-limit">
            @csrf
            <div style="display:flex;gap:8px;align-items:center">
                <input type="number" name="store_limit" value="{{ $user->store_limit ?? 1 }}" min="1" max="999" class="auc-input" style="width:80px">
                <button type="submit" class="ka-btn ka-btn-primary" style="white-space:nowrap">Set Limit</button>
            </div>
        </form>
    </div>

    {{-- Danger Zone --}}
    @if($user->id !== auth()->id())
    <div class="auc-card" style="border-color:#fca5a5">
        <div class="auc-card-header">
            <div class="auc-card-icon" style="background:#fee2e2">⚠️</div>
            <div><p class="auc-card-title" style="color:#dc2626">Danger Zone</p><p class="auc-card-sub">Irreversible actions</p></div>
        </div>
        <form method="POST" action="/admin/users/{{ $user->id }}" onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
            @csrf @method('DELETE')
            <button type="submit" class="ka-btn" style="background:#dc2626;color:#fff;width:100%">🗑 Delete User</button>
        </form>
    </div>
    @endif

</div>
</div>
</form>

{{-- ── DANGER ZONE ─────────────────────────────────────────────────────── --}}
@if($user->id !== auth()->id() && !in_array($user->role, ['super_admin']))
<div class="dz-box" style="border:2px solid #fca5a5;border-radius:16px;padding:28px 32px;margin-top:32px;background:#fff5f5;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <span style="font-size:16px;font-weight:800;color:#dc2626;letter-spacing:-.01em;">Danger Zone</span>
    </div>
    <p style="font-size:13px;color:#6b7280;margin-bottom:20px;">These actions are permanent and cannot be undone. All listings, stores and data belonging to this user will be deleted.</p>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <button type="button" id="dzDeleteBtn" class="ka-btn" style="background:#dc2626;color:#fff;border-color:#dc2626;font-weight:700;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:5px"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            Delete User & All Data
        </button>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="dzModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.5);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:20px;padding:36px 40px;max-width:440px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.25);">
        <div style="text-align:center;margin-bottom:20px;">
            <div style="width:56px;height:56px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
            </div>
            <h3 style="font-size:18px;font-weight:800;color:#111;margin-bottom:8px;">Delete "{{ $user->name }}"?</h3>
            <p style="font-size:13px;color:#6b7280;line-height:1.6;">This will permanently delete the user account and all associated listings, stores, and data. <strong style="color:#dc2626;">This cannot be undone.</strong></p>
        </div>
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:12px;color:#991b1b;">
            <strong>Will be deleted:</strong> Account · {{ $user->listings()->count() }} listing(s) · {{ $user->stores()->count() }} store(s)
        </div>
        <p style="font-size:13px;color:#374151;margin-bottom:8px;">Type <strong>DELETE</strong> to confirm:</p>
        <input id="dzConfirmInput" type="text" placeholder="Type DELETE here" style="width:100%;border:2px solid #e5e7eb;border-radius:8px;padding:10px 14px;font-size:14px;margin-bottom:16px;outline:none;box-sizing:border-box;">
        <div style="display:flex;gap:10px;">
            <button type="button" id="dzCancelBtn" style="flex:1;padding:12px;border:1.5px solid #e5e7eb;border-radius:8px;background:#fff;font-size:14px;font-weight:600;cursor:pointer;color:#374151;">Cancel</button>
            <form method="POST" action="/admin/users/{{ $user->id }}" id="dzDeleteForm" style="flex:1;">
                @csrf @method('DELETE')
                <button type="submit" id="dzConfirmBtn" style="width:100%;padding:12px;border:none;border-radius:8px;background:#dc2626;color:#fff;font-size:14px;font-weight:700;cursor:pointer;opacity:.4;pointer-events:none;">Delete Permanently</button>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
// ── Avatar ─────────────────────────────────────────────────────────────────
(function(){
    var zone=document.getElementById('aucAvatarZone'), input=document.getElementById('aucAvatarInput'), btn=document.getElementById('aucAvatarBtn');
    if(!zone) return;
    function openPicker(){ input.click(); }
    zone.addEventListener('click', openPicker);
    if(btn) btn.addEventListener('click', openPicker);
    input.addEventListener('change',function(){
        if(!this.files[0]) return;
        var old=zone.querySelector('img');
        if(old) old.remove();
        zone.querySelector('.auc-zone-icon') && (zone.querySelector('.auc-zone-icon').style.display='none');
        zone.querySelector('.auc-zone-label') && (zone.querySelector('.auc-zone-label').style.display='none');
        var img=document.createElement('img'); img.className='auc-zone-preview'; img.src=URL.createObjectURL(this.files[0]); zone.appendChild(img);
    });
})();

// ── ksd dropdowns ──────────────────────────────────────────────────────────
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
})();

// ── Danger Zone modal ──────────────────────────────────────────────────────
(function(){
    var deleteBtn = document.getElementById('dzDeleteBtn');
    var modal = document.getElementById('dzModal');
    var cancelBtn = document.getElementById('dzCancelBtn');
    var input = document.getElementById('dzConfirmInput');
    var confirmBtn = document.getElementById('dzConfirmBtn');
    if(!deleteBtn || !modal) return;

    deleteBtn.addEventListener('click', function(){
        modal.style.display = 'flex';
        if(input) { input.value = ''; input.focus(); }
        if(confirmBtn) { confirmBtn.style.opacity = '.4'; confirmBtn.style.pointerEvents = 'none'; }
    });
    cancelBtn.addEventListener('click', function(){ modal.style.display = 'none'; });
    modal.addEventListener('click', function(e){ if(e.target === modal) modal.style.display = 'none'; });
    if(input) input.addEventListener('input', function(){
        var ok = this.value === 'DELETE';
        confirmBtn.style.opacity = ok ? '1' : '.4';
        confirmBtn.style.pointerEvents = ok ? 'auto' : 'none';
    });
})();

// ── Password match ─────────────────────────────────────────────────────────
(function(){
    var pwd=document.getElementById('aucPwd'), conf=document.getElementById('aucPwdConf'), matchTxt=document.getElementById('aucPwdMatch');
    if(!pwd) return;
    function checkMatch(){ if(!conf.value){matchTxt.textContent='';return;} matchTxt.textContent=conf.value===pwd.value?'':'Passwords do not match'; }
    pwd.addEventListener('input',checkMatch); conf.addEventListener('input',checkMatch);
})();
</script>
@endpush
