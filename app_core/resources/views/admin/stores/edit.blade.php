@extends('layouts.admin')
@section('title','Edit Store')
@section('page','Stores')
@section('heading','Edit Store')
@section('subheading','Update store profile, contact details and images')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/stores">Back</a>@endsection
@section('content')

@if(session('success'))
<div class="alert-success">{{ session('success') }}</div>
@endif

{{-- Store Details --}}
<section class="sa-card" class="mb-24">
<div class="sa-card-head"><h2>Store Details</h2></div>
<form class="ka-premium-form" method="post" action="/admin/stores/{{ $store->id }}" enctype="multipart/form-data">
@csrf
@method('PUT')
<div class="ka-form-grid">
    <div class="ka-field">
        <label>Store Logo</label>
        @if($store->logo)
            <div class="flex-row-14-mb10">
                <img class="thumb-80" src="{{ asset('storage/'.$store->logo) }}" alt="{{ $store->name }}">
                <label class="text-del-action">
                    <input type="checkbox" name="remove_logo" value="1"> Remove logo
                </label>
            </div>
        @else
            <p class="hint-text">No logo uploaded yet.</p>
        @endif
        <input type="file" name="logo" accept="image/jpeg,image/png,image/webp">
        <small>Square image recommended. JPG/PNG/WEBP, max 2MB.</small>
    </div>

    <div class="ka-field">
        <label>Store Banner</label>
        @if($store->banner)
            <div class="flex-row-14-mb10">
                <img class="thumb-140x64" src="{{ asset('storage/'.$store->banner) }}" alt="{{ $store->name }}">
                <label class="text-del-action">
                    <input type="checkbox" name="remove_banner" value="1"> Remove banner
                </label>
            </div>
        @else
            <p class="hint-text">No banner uploaded yet.</p>
        @endif
        <input type="file" name="banner" accept="image/jpeg,image/png,image/webp">
        <small>Preferred size: 1640×624 px. JPG/PNG/WEBP, max 3MB.</small>
    </div>

    <div class="ka-field ka-span-2">
        <label>Store Name</label>
        <input name="name" value="{{ old('name', $store->name) }}" required>
    </div>
    <div class="ka-field">
        <label>Phone</label>
        <input name="phone" value="{{ old('phone', $store->phone) }}">
    </div>
    <div class="ka-field">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $store->email) }}">
    </div>
    <div class="ka-field">
        <label>WhatsApp</label>
        <div class="flex-g6-mb6">
            <input class="chk-14" type="checkbox" name="whatsapp_same" id="whatsappSame" value="1" {{ old('whatsapp_same') ? 'checked' : '' }}>
            <label class="fs13-fw5-ptr" for="whatsappSame">Same as phone number</label>
        </div>
        <input name="whatsapp" id="whatsappInput" value="{{ old('whatsapp', $store->whatsapp) }}">
    </div>
    <div class="ka-field">
        <label>City / Location</label>
        <select name="city">
            <option value="">Select location...</option>
            @foreach($locations as $loc)
                <option value="{{ $loc->name }}" @selected(old('city', $store->city) === $loc->name)>{{ $loc->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="ka-field ka-span-2">
        <label>Address</label>
        <input name="address" value="{{ old('address', $store->address) }}">
    </div>

    <div class="ka-field ka-span-2">
        <label>Store Categories <small class="text-light-gray">(select all that apply)</small></label>
        @php $selectedCats = old('categories', $store->categories->pluck('id')->toArray()); @endphp
        @include('admin.partials.category-picker')
    </div>

    <input type="hidden" name="latitude" value="{{ old('latitude', $store->latitude) }}">
    <input type="hidden" name="longitude" value="{{ old('longitude', $store->longitude) }}">

    <div class="ka-field ka-span-2">
        <label>Description</label>
        <textarea name="description">{{ old('description', $store->description) }}</textarea>
    </div>
</div>
<div class="ka-form-actions">
    <button class="ka-btn ka-btn-primary">Update Store</button>
    <a class="ka-btn ka-btn-light" href="/admin/stores">Cancel</a>
</div>
</form>
</section>

{{-- Store Creation Limit --}}
@php
    $owner     = $store->user;
    $ownerStores = $owner ? $owner->stores->count() : 0;
    $ownerLimit  = $owner ? (int)($owner->store_limit ?? 1) : 1;
@endphp
@if($owner)
<section class="sa-card border-green50-mb24">
<div class="sa-card-head"><h2 class="text-green">Store Creation Limit</h2><span>Owner: {{ $owner->name }}</span></div>
<div class="p-20">
    <div class="flex-ac-g20-fw-mb20">
        <div class="stat-card">
            <div class="csp5-259">{{ $ownerStores }}</div>
            <div class="caption-mt2">Stores Created</div>
        </div>
        <div class="stat-card">
            <div style="font-size:28px;font-weight:800;color:{{ $ownerStores >= $ownerLimit ? '#c62828' : '#1b5e20' }}">{{ $ownerLimit }}</div>
            <div class="caption-mt2">Current Limit</div>
        </div>
        <div style="background:{{ $ownerStores >= $ownerLimit ? '#ffebee' : '#e8f5e9' }};border-radius:10px;padding:10px 16px;font-size:13px;font-weight:700;color:{{ $ownerStores >= $ownerLimit ? '#c62828' : '#2e7d32' }}">
            {{ $ownerStores >= $ownerLimit ? '🔒 Limit reached — Create Store is disabled for this seller' : '✅ Seller can still create ' . ($ownerLimit - $ownerStores) . ' more store(s)' }}
        </div>
    </div>
    <form class="flex-end-g12-fw" method="post" action="/admin/users/{{ $owner->id }}/store-limit">
        @csrf
        <div>
            <label class="block-fw6-mb6">Set New Store Limit</label>
            <input class="qty-input" type="number" name="store_limit" min="1" max="999" value="{{ $ownerLimit }}">
        </div>
        <button class="ka-btn ka-btn-primary" type="submit">Save Limit</button>
    </form>
    <p class="fs12-muted-mt10">Default is 1. Increase to allow this seller to create multiple stores. The "Create Store" button on their dashboard is automatically hidden when they reach the limit.</p>
</div>
</section>
@endif

{{-- Ownership Transfer --}}
<section class="sa-card border-blue50-mb24">
<div class="sa-card-head"><h2 class="text-blue900">Ownership Transfer</h2></div>
<div class="p-20">
    <div class="info-row-gray">
        <div class="avatar-42-green">
            {{ strtoupper(substr($store->user->name ?? 'N', 0, 1)) }}
        </div>
        <div>
            <div class="fw7-fs14">Current Owner: {{ $store->user->name ?? 'No owner' }}</div>
            <div class="fs13-gray">{{ $store->user->email ?? '—' }}</div>
        </div>
    </div>

    <form method="post" action="/admin/stores/{{ $store->id }}/transfer" id="transferForm">
        @csrf
        <div class="mb-14">
            <label class="fw6-fs13-block">Transfer to User</label>
            <select class="input-500" name="new_owner_id" id="newOwnerSelect">
                <option value="">— Select a user —</option>
                @foreach($users as $user)
                    @if($user->id !== $store->user_id)
                        <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
                    @endif
                @endforeach
            </select>
            @error('new_owner_id')<small class="red-block-mt4">{{ $message }}</small>@enderror
        </div>
        <div class="alert-orange-md">
            <b>Warning:</b> This will transfer the store and all its listings to the selected user. The new owner will have full control over this store from their dashboard. This action can only be done by an admin.
        </div>
        <button type="button" id="transferOwnershipBtn" class="ka-btn bg-blue700-btn">Transfer Ownership</button>
    </form>
</div>
</section>

{{-- ── DANGER ZONE ─────────────────────────────────────────────────────── --}}
<section style="border:2px solid #fca5a5;border-radius:16px;padding:28px 32px;margin-top:24px;background:#fff5f5;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <span style="font-size:16px;font-weight:800;color:#dc2626;">Danger Zone</span>
    </div>
    <p style="font-size:13px;color:#6b7280;margin-bottom:20px;">Permanently delete this store and all its listings. This action cannot be undone.</p>
    <button type="button" id="dzStoreDeleteBtn" style="background:#dc2626;color:#fff;border:none;border-radius:8px;padding:10px 20px;font-size:13px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
        Delete Store & All Listings
    </button>
</section>

{{-- Store Delete Confirmation Modal --}}
<div id="dzStoreModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.5);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:20px;padding:36px 40px;max-width:440px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.25);">
        <div style="text-align:center;margin-bottom:20px;">
            <div style="width:56px;height:56px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
            </div>
            <h3 style="font-size:18px;font-weight:800;color:#111;margin-bottom:8px;">Delete "{{ $store->name }}"?</h3>
            <p style="font-size:13px;color:#6b7280;line-height:1.6;">All listings belonging to this store will also be deleted. <strong style="color:#dc2626;">This cannot be undone.</strong></p>
        </div>
        <p style="font-size:13px;color:#374151;margin-bottom:8px;">Type <strong>DELETE</strong> to confirm:</p>
        <input id="dzStoreInput" type="text" placeholder="Type DELETE here" style="width:100%;border:2px solid #e5e7eb;border-radius:8px;padding:10px 14px;font-size:14px;margin-bottom:16px;outline:none;box-sizing:border-box;">
        <div style="display:flex;gap:10px;">
            <button type="button" id="dzStoreCancelBtn" style="flex:1;padding:12px;border:1.5px solid #e5e7eb;border-radius:8px;background:#fff;font-size:14px;font-weight:600;cursor:pointer;color:#374151;">Cancel</button>
            <form method="POST" action="/admin/stores/{{ $store->id }}" style="flex:1;">
                @csrf @method('DELETE')
                <button type="submit" id="dzStoreConfirmBtn" style="width:100%;padding:12px;border:none;border-radius:8px;background:#dc2626;color:#fff;font-size:14px;font-weight:700;cursor:pointer;opacity:.4;pointer-events:none;">Delete Permanently</button>
            </form>
        </div>
    </div>
</div>

<script nonce="{{ $cspNonce ?? '' }}">
// ── Transfer ownership ─────────────────────────────────────────────────────
document.getElementById('transferOwnershipBtn')?.addEventListener('click', function(){ confirmTransfer(); });
function confirmTransfer() {
    var select = document.getElementById('newOwnerSelect');
    if (!select.value) {
        alert('Please select a user to transfer to.');
        return;
    }
    var userName = select.options[select.selectedIndex].text;
    if (confirm('Are you sure you want to transfer "{{ addslashes($store->name) }}" to ' + userName + '?\n\nAll listings under this store will also be transferred to the new owner.')) {
        document.getElementById('transferForm').submit();
    }
}

function normLK(v, plus) {
    var d = v.replace(/\D/g, '');
    if (!d) return '';
    if (d.charAt(0) === '0') d = '94' + d.slice(1);
    if (d.slice(0, 2) !== '94') d = '94' + d;
    return (plus ? '+' : '') + d;
}
var phoneInput = document.querySelector('[name="phone"]');
if (phoneInput) phoneInput.addEventListener('blur', function(){ this.value = normLK(this.value, true); });
var waInput = document.getElementById('whatsappInput');
if (waInput) waInput.addEventListener('blur', function(){ if (!document.getElementById('whatsappSame').checked) this.value = normLK(this.value, false); });

var waSame = document.getElementById('whatsappSame');
if (waSame) {
    function toggleWaSame() {
        var wa = document.getElementById('whatsappInput');
        if (waSame.checked) {
            wa.value = phoneInput ? phoneInput.value : '';
            wa.disabled = true;
            wa.style.opacity = '0.5';
        } else {
            wa.disabled = false;
            wa.style.opacity = '1';
        }
    }
    waSame.addEventListener('change', toggleWaSame);
    if (waSame.checked) toggleWaSame();
}

// ── Danger Zone — Store Delete Modal ─────────────────────────────────────
(function(){
    var btn = document.getElementById('dzStoreDeleteBtn');
    var modal = document.getElementById('dzStoreModal');
    var cancelBtn = document.getElementById('dzStoreCancelBtn');
    var input = document.getElementById('dzStoreInput');
    var confirmBtn = document.getElementById('dzStoreConfirmBtn');
    if(!btn || !modal) return;
    btn.addEventListener('click', function(){ modal.style.display='flex'; input.value=''; confirmBtn.style.opacity='.4'; confirmBtn.style.pointerEvents='none'; input.focus(); });
    cancelBtn.addEventListener('click', function(){ modal.style.display='none'; });
    modal.addEventListener('click', function(e){ if(e.target===modal) modal.style.display='none'; });
    input.addEventListener('input', function(){ var ok=this.value==='DELETE'; confirmBtn.style.opacity=ok?'1':'.4'; confirmBtn.style.pointerEvents=ok?'auto':'none'; });
})();

</script>
@endsection
