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
        <input name="whatsapp" value="{{ old('whatsapp', $store->whatsapp) }}">
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

    {{-- Google Map Location --}}
    <div class="ka-field ka-span-2">
        <label>Store Location on Map</label>
        <small style="display:block;color:#667085;margin-bottom:8px">Click on the map to set the store location, or search for an address. You can also paste a Google Maps link.</small>
        <div style="display:flex;gap:8px;margin-bottom:8px">
            <input type="text" id="mapSearch" placeholder="Search address or paste Google Maps link..." style="flex:1;padding:10px 14px;border:1.5px solid #e5e8ef;border-radius:10px;font-size:13px">
            <button type="button" onclick="searchAddress()" style="padding:8px 16px;background:var(--ka-primary,#1b5e20);color:#fff;border:none;border-radius:10px;font-size:13px;cursor:pointer;font-weight:600">Search</button>
        </div>
        <div id="storeMap" style="height:300px;border-radius:12px;border:1.5px solid #e5e8ef;z-index:1"></div>
        <div style="display:flex;gap:12px;margin-top:8px">
            <div style="flex:1">
                <label style="font-size:11px;font-weight:600;color:#667085">Latitude</label>
                <input type="text" name="latitude" id="latInput" value="{{ old('latitude', $store->latitude) }}" readonly style="width:100%;padding:8px 10px;border:1px solid #e5e8ef;border-radius:8px;font-size:12px;background:#f8fafc;color:#667085">
            </div>
            <div style="flex:1">
                <label style="font-size:11px;font-weight:600;color:#667085">Longitude</label>
                <input type="text" name="longitude" id="lngInput" value="{{ old('longitude', $store->longitude) }}" readonly style="width:100%;padding:8px 10px;border:1px solid #e5e8ef;border-radius:8px;font-size:12px;background:#f8fafc;color:#667085">
            </div>
            <button type="button" onclick="clearLocation()" style="align-self:flex-end;padding:8px 12px;background:#ffebee;color:#d32f2f;border:none;border-radius:8px;font-size:12px;cursor:pointer;font-weight:600">Clear</button>
        </div>
    </div>

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

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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

var defaultLat = 7.2513, defaultLng = 80.3464;
var initLat = document.getElementById('latInput').value || defaultLat;
var initLng = document.getElementById('lngInput').value || defaultLng;
var hasPin = !!(document.getElementById('latInput').value);

var map = L.map('storeMap').setView([initLat, initLng], hasPin ? 15 : 12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap', maxZoom: 19
}).addTo(map);

var marker = null;
if (hasPin) {
    marker = L.marker([initLat, initLng], {draggable: true}).addTo(map);
    marker.on('dragend', function(e) { updateCoords(e.target.getLatLng()); });
}

map.on('click', function(e) { placeMarker(e.latlng); });

function placeMarker(latlng) {
    if (marker) { marker.setLatLng(latlng); }
    else { marker = L.marker(latlng, {draggable: true}).addTo(map); marker.on('dragend', function(e) { updateCoords(e.target.getLatLng()); }); }
    updateCoords(latlng);
}

function updateCoords(latlng) {
    document.getElementById('latInput').value = latlng.lat.toFixed(7);
    document.getElementById('lngInput').value = latlng.lng.toFixed(7);
}

function clearLocation() {
    if (marker) { map.removeLayer(marker); marker = null; }
    document.getElementById('latInput').value = '';
    document.getElementById('lngInput').value = '';
}

function searchAddress() {
    var q = document.getElementById('mapSearch').value.trim();
    if (!q) return;
    var gmapMatch = q.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);
    if (!gmapMatch) gmapMatch = q.match(/q=(-?\d+\.\d+),(-?\d+\.\d+)/);
    if (!gmapMatch) gmapMatch = q.match(/(-?\d+\.\d{4,}),\s*(-?\d+\.\d{4,})/);
    if (gmapMatch) {
        var lat = parseFloat(gmapMatch[1]), lng = parseFloat(gmapMatch[2]);
        map.setView([lat, lng], 16);
        placeMarker({lat: lat, lng: lng});
        return;
    }
    fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q + ', Sri Lanka') + '&limit=1')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.length) {
                var lat = parseFloat(data[0].lat), lng = parseFloat(data[0].lon);
                map.setView([lat, lng], 16);
                placeMarker({lat: lat, lng: lng});
            } else { alert('Location not found. Try a different search or click on the map.'); }
        });
}

document.getElementById('mapSearch').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); searchAddress(); }
});

setTimeout(function() { map.invalidateSize(); }, 300);

// Auto-format Sri Lankan numbers: phone -> +94..., whatsapp -> 94...
function normLK(v, plus) {
    var d = v.replace(/\D/g, '');
    if (!d) return '';
    if (d.charAt(0) === '0') d = '94' + d.slice(1);
    if (d.slice(0, 2) !== '94') d = '94' + d;
    return (plus ? '+' : '') + d;
}
var phoneInput = document.querySelector('[name="phone"]');
if (phoneInput) phoneInput.addEventListener('blur', function(){ this.value = normLK(this.value, true); });
var waInput = document.querySelector('[name="whatsapp"]');
if (waInput) waInput.addEventListener('blur', function(){ this.value = normLK(this.value, false); });
</script>
@endsection
