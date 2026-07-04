@extends('layouts.admin')
@section('title','Create Store')
@section('page','Stores')
@section('heading','Create Store')
@section('subheading','Create a store for a customer — you can hand it over later')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/stores">Back</a>@endsection
@section('content')
<section class="sa-card">
<form class="ka-premium-form" method="post" action="/admin/stores" enctype="multipart/form-data">
@csrf
<div class="ka-form-grid">
    <div class="ka-field">
        <label>Store Logo</label>
        <input type="file" name="logo" accept="image/jpeg,image/png,image/webp">
        <small>Square image recommended. JPG/PNG/WEBP, max 2MB.</small>
    </div>
    <div class="ka-field">
        <label>Store Banner</label>
        <input type="file" name="banner" accept="image/jpeg,image/png,image/webp">
        <small>Wide image recommended. JPG/PNG/WEBP, max 3MB.</small>
    </div>
    <div class="ka-field ka-span-2">
        <label>Assign to User (Owner)</label>
        <select name="user_id" required>
            <option value="">Select a user...</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }} — {{ $user->email }}</option>
            @endforeach
        </select>
        <small>This user will own and manage the store. You can change this later.</small>
    </div>
    <div class="ka-field ka-span-2">
        <label>Store Name</label>
        <input name="name" value="{{ old('name') }}" required placeholder="e.g. Kegalle Auto Parts">
    </div>
    <div class="ka-field">
        <label>Phone</label>
        <input name="phone" value="{{ old('phone') }}" placeholder="077 1234567">
    </div>
    <div class="ka-field">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="store@example.com">
    </div>
    <div class="ka-field">
        <label>WhatsApp</label>
        <input name="whatsapp" value="{{ old('whatsapp') }}" placeholder="94771234567">
    </div>
    <div class="ka-field">
        <label>City / Location</label>
        <select name="city">
            <option value="">Select location...</option>
            @foreach($locations as $loc)
                <option value="{{ $loc->name }}" @selected(old('city') === $loc->name)>{{ $loc->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="ka-field ka-span-2">
        <label>Address</label>
        <input name="address" value="{{ old('address') }}" placeholder="Full address...">
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
                <input type="text" name="latitude" id="latInput" value="{{ old('latitude') }}" readonly style="width:100%;padding:8px 10px;border:1px solid #e5e8ef;border-radius:8px;font-size:12px;background:#f8fafc;color:#667085">
            </div>
            <div style="flex:1">
                <label style="font-size:11px;font-weight:600;color:#667085">Longitude</label>
                <input type="text" name="longitude" id="lngInput" value="{{ old('longitude') }}" readonly style="width:100%;padding:8px 10px;border:1px solid #e5e8ef;border-radius:8px;font-size:12px;background:#f8fafc;color:#667085">
            </div>
            <button type="button" onclick="clearLocation()" style="align-self:flex-end;padding:8px 12px;background:#ffebee;color:#d32f2f;border:none;border-radius:8px;font-size:12px;cursor:pointer;font-weight:600">Clear</button>
        </div>
    </div>

    <div class="ka-field ka-span-2">
        <label>Description</label>
        <textarea name="description" placeholder="Brief description of what this store sells...">{{ old('description') }}</textarea>
    </div>
    <div class="ka-field">
        <label>Status</label>
        <select name="status">
            <option value="approved" @selected(old('status', 'approved') === 'approved')>Approved</option>
            <option value="pending" @selected(old('status') === 'pending')>Pending</option>
        </select>
    </div>
    <label class="ka-check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured'))> Featured Store</label>
</div>
<div class="ka-form-actions">
    <button class="ka-btn ka-btn-primary">Create Store</button>
    <a class="ka-btn ka-btn-light" href="/admin/stores">Cancel</a>
</div>
</form>
</section>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
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
            } else {
                alert('Location not found. Try a different search or click on the map.');
            }
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
