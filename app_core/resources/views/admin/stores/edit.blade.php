@extends('layouts.admin')
@section('title','Edit Store')
@section('page','Stores')
@section('heading','Edit Store')
@section('subheading','Update store profile, contact details and images')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/stores">Back</a>@endsection
@section('content')

@if(session('success'))
<div style="background:#E8F5E9;color:#2E7D32;padding:12px 18px;border-radius:12px;margin-bottom:18px;font-weight:600;font-size:13px">{{ session('success') }}</div>
@endif

{{-- Store Details --}}
<section class="sa-card" style="margin-bottom:24px">
<div class="sa-card-head"><h2>Store Details</h2></div>
<form class="ka-premium-form" method="post" action="/admin/stores/{{ $store->id }}" enctype="multipart/form-data">
@csrf
@method('PUT')
<div class="ka-form-grid">
    <div class="ka-field">
        <label>Store Logo</label>
        @if($store->logo)
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:10px">
                <img src="{{ asset('storage/'.$store->logo) }}" alt="{{ $store->name }}" style="width:80px;height:80px;border-radius:12px;object-fit:cover;border:1.5px solid var(--ka-border,#E5E8EF)">
                <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#D32F2F;cursor:pointer">
                    <input type="checkbox" name="remove_logo" value="1"> Remove logo
                </label>
            </div>
        @else
            <p style="font-size:13px;color:#667085;margin-bottom:8px">No logo uploaded yet.</p>
        @endif
        <input type="file" name="logo" accept="image/jpeg,image/png,image/webp">
        <small>Square image recommended. JPG/PNG/WEBP, max 2MB.</small>
    </div>

    <div class="ka-field">
        <label>Store Banner</label>
        @if($store->banner)
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:10px">
                <img src="{{ asset('storage/'.$store->banner) }}" alt="{{ $store->name }}" style="width:140px;height:64px;border-radius:10px;object-fit:cover;border:1.5px solid var(--ka-border,#E5E8EF)">
                <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#D32F2F;cursor:pointer">
                    <input type="checkbox" name="remove_banner" value="1"> Remove banner
                </label>
            </div>
        @else
            <p style="font-size:13px;color:#667085;margin-bottom:8px">No banner uploaded yet.</p>
        @endif
        <input type="file" name="banner" accept="image/jpeg,image/png,image/webp">
        <small>Wide image recommended. JPG/PNG/WEBP, max 3MB.</small>
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
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px">
            <input type="checkbox" name="whatsapp_same" id="whatsappSame" value="1" style="width:14px!important;height:14px!important;min-width:14px;margin:0!important;accent-color:#1b5e20" {{ old('whatsapp_same') ? 'checked' : '' }}>
            <label for="whatsappSame" style="font-size:13px;font-weight:500;margin:0;cursor:pointer">Same as phone number</label>
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
        <label>Store Categories <small style="font-weight:400;color:#667085">(select all that apply)</small></label>
        <style>
            #storeCategoryPicker input[type="checkbox"]{width:16px!important;height:16px!important;min-width:16px;margin:0!important;accent-color:#1b5e20;cursor:pointer}
            #storeCategoryPicker .k-cat-main{display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:10px;cursor:pointer;transition:all .15s}
        </style>
        <input type="text" id="catSearch" placeholder="Search categories..." style="margin-top:4px;margin-bottom:6px;padding:10px 14px;border:1.5px solid #e5e8ef;border-radius:10px;font-size:13px;width:100%;box-sizing:border-box">
        <div id="storeCategoryPicker" style="border:1.5px solid #e5e8ef;border-radius:10px;padding:12px;max-height:300px;overflow-y:auto">
            @php $selectedCats = old('categories', $store->categories->pluck('id')->toArray()); @endphp
            <div id="catNoResults" style="display:none;text-align:center;padding:16px;color:#94a3b8;font-size:13px">No categories found</div>
            @foreach($categories as $parent)
                <div class="k-cat-group" data-name="{{ strtolower($parent->name) }} {{ strtolower($parent->children->pluck('name')->join(' ')) }}" style="margin-bottom:8px">
                    <label class="k-cat-main" style="{{ in_array($parent->id, $selectedCats) ? 'background:#e8f5e9;border:1.5px solid #1b5e20' : 'background:#f8fafc;border:1.5px solid #e5e8ef' }}">
                        <input type="checkbox" name="categories[]" value="{{ $parent->id }}" {{ in_array($parent->id, $selectedCats) ? 'checked' : '' }}>
                        <span style="font-size:15px;line-height:1">{{ $parent->icon ?? '🏷' }}</span>
                        <span style="font-weight:700;font-size:14px;color:#1b5e20">{{ $parent->name }}</span>
                    </label>
                    @if($parent->children->isNotEmpty())
                        <div style="padding:4px 12px 2px 40px;font-size:11px;color:#94a3b8;line-height:1.6">
                            {{ $parent->children->pluck('name')->join(' · ') }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
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

{{-- Ownership Transfer --}}
<section class="sa-card" style="border:1.5px solid #e3f2fd;margin-bottom:24px">
<div class="sa-card-head"><h2 style="color:#1565c0">Ownership Transfer</h2></div>
<div style="padding:20px">
    <div style="background:#f5f5f5;border-radius:10px;padding:16px 20px;margin-bottom:18px;display:flex;align-items:center;gap:14px">
        <div style="width:42px;height:42px;border-radius:50%;background:#e8f5e9;display:flex;align-items:center;justify-content:center;font-weight:700;color:#2e7d32;font-size:16px">
            {{ strtoupper(substr($store->user->name ?? 'N', 0, 1)) }}
        </div>
        <div>
            <div style="font-weight:700;font-size:14px">Current Owner: {{ $store->user->name ?? 'No owner' }}</div>
            <div style="font-size:13px;color:#667085">{{ $store->user->email ?? '—' }}</div>
        </div>
    </div>

    <form method="post" action="/admin/stores/{{ $store->id }}/transfer" id="transferForm">
        @csrf
        <div style="margin-bottom:14px">
            <label style="font-weight:600;font-size:13px;display:block;margin-bottom:6px">Transfer to User</label>
            <select name="new_owner_id" id="newOwnerSelect" style="width:100%;max-width:500px;height:44px;border:1.5px solid #e5e8ef;border-radius:10px;padding:0 14px;font-size:14px">
                <option value="">— Select a user —</option>
                @foreach($users as $user)
                    @if($user->id !== $store->user_id)
                        <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
                    @endif
                @endforeach
            </select>
            @error('new_owner_id')<small style="color:#d32f2f;display:block;margin-top:4px">{{ $message }}</small>@enderror
        </div>
        <div style="background:#fff3e0;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#e65100">
            <b>Warning:</b> This will transfer the store and all its listings to the selected user. The new owner will have full control over this store from their dashboard. This action can only be done by an admin.
        </div>
        <button type="button" onclick="confirmTransfer()" class="ka-btn" style="background:#1565c0;color:#fff;border:none">Transfer Ownership</button>
    </form>
</div>
</section>

<script>
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

document.querySelectorAll('.k-cat-main input[type="checkbox"]').forEach(function(cb) {
    cb.addEventListener('change', function() {
        var lbl = this.closest('.k-cat-main');
        lbl.style.background = this.checked ? '#e8f5e9' : '#f8fafc';
        lbl.style.borderColor = this.checked ? '#1b5e20' : '#e5e8ef';
    });
});
var catSearch = document.getElementById('catSearch');
if (catSearch) {
    catSearch.addEventListener('input', function() {
        var q = this.value.toLowerCase().trim();
        var groups = document.querySelectorAll('.k-cat-group');
        var found = 0;
        groups.forEach(function(g) {
            var match = !q || g.getAttribute('data-name').indexOf(q) !== -1;
            g.style.display = match ? '' : 'none';
            if (match) found++;
        });
        document.getElementById('catNoResults').style.display = found ? 'none' : 'block';
    });
}
</script>
@endsection
