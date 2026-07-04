@extends('layouts.app')
@section('title', $mode === 'edit' ? 'Edit Store - Kegalle' : 'Create Store - Kegalle')
@push('styles')
<link rel="stylesheet" href="/css/kegalle-dashboard-functional.css?v=2">
@endpush
@section('content')
<section class="kd-wrap">
    <div class="kd-container kd-form-container">
        <div class="kd-head">
            <div>
                <span>Seller Dashboard</span>
                <h1>{{ $mode === 'edit' ? 'Edit Store' : 'Create Store' }}</h1>
                <p>{{ $mode === 'edit' ? 'Update your store profile, contact details and images.' : 'Tell buyers about your business. Your store will be reviewed before going live.' }}</p>
            </div>
        </div>

        <form class="kd-form" method="post" action="{{ $mode === 'edit' ? '/dashboard/stores/'.$store->id : '/dashboard/stores' }}" enctype="multipart/form-data">
            @csrf
            @if($mode === 'edit')
                @method('PUT')
            @endif

            <label>Store Logo
                @if($mode === 'edit' && $store->logo)
                    <div style="display:flex;align-items:center;gap:14px;margin:8px 0">
                        <img src="{{ asset('storage/'.$store->logo) }}" alt="{{ $store->name }}" style="width:72px;height:72px;border-radius:12px;object-fit:cover;border:1px solid #E5E8EF">
                        <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#D32F2F;cursor:pointer">
                            <input type="checkbox" name="remove_logo" value="1"> Remove logo
                        </label>
                    </div>
                @endif
                <input type="file" name="logo" accept="image/jpeg,image/png,image/webp">
                <small>Square image recommended. JPG/PNG/WEBP, max 2MB.</small>
            </label>

            <label>Store Banner
                @if($mode === 'edit' && $store->banner)
                    <div style="display:flex;align-items:center;gap:14px;margin:8px 0">
                        <img src="{{ asset('storage/'.$store->banner) }}" alt="{{ $store->name }}" style="width:160px;height:70px;border-radius:10px;object-fit:cover;border:1px solid #E5E8EF">
                        <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#D32F2F;cursor:pointer">
                            <input type="checkbox" name="remove_banner" value="1"> Remove banner
                        </label>
                    </div>
                @endif
                <input type="file" name="banner" accept="image/jpeg,image/png,image/webp">
                <small>Wide image recommended. JPG/PNG/WEBP, max 3MB.</small>
            </label>

            <div class="kd-form-grid">
                <label>Store Name
                    <input name="name" required value="{{ old('name', $store->name ?? '') }}" placeholder="Example: Kegalle Electronics">
                </label>

                <label>Phone
                    <input name="phone" value="{{ old('phone', $store->phone ?? '') }}" placeholder="07X XXX XXXX">
                </label>

                <label>WhatsApp
                    <input name="whatsapp" value="{{ old('whatsapp', $store->whatsapp ?? '') }}" placeholder="07X XXX XXXX">
                </label>

                <label>Email
                    <input type="email" name="email" value="{{ old('email', $store->email ?? '') }}" placeholder="store@example.com">
                </label>

                <label>City / Location
                    <select name="city">
                        <option value="">Select location...</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->name }}" @selected(old('city', $store->city ?? '') === $loc->name)>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <label>Address
                <textarea name="address" placeholder="Street, town, district">{{ old('address', $store->address ?? '') }}</textarea>
            </label>

            {{-- Google Map Location --}}
            <div style="margin-bottom:16px">
                <label style="font-weight:600;margin-bottom:6px;display:block">Store Location on Map</label>
                <small style="display:block;color:#667085;margin-bottom:8px">Click on the map to set your store location, or search for an address. You can also paste a Google Maps link.</small>
                <div style="display:flex;gap:8px;margin-bottom:8px">
                    <input type="text" id="mapSearch" placeholder="Search address or paste Google Maps link..." style="flex:1;padding:10px 14px;border:1.5px solid #e5e8ef;border-radius:10px;font-size:13px">
                    <button type="button" onclick="searchAddress()" style="padding:8px 16px;background:var(--k-primary,#1b5e20);color:#fff;border:none;border-radius:10px;font-size:13px;cursor:pointer;font-weight:600">Search</button>
                </div>
                <div id="storeMap" style="height:300px;border-radius:12px;border:1.5px solid #e5e8ef;z-index:1"></div>
                <div style="display:flex;gap:12px;margin-top:8px">
                    <div style="flex:1">
                        <label style="font-size:11px;font-weight:600;color:#667085">Latitude</label>
                        <input type="text" name="latitude" id="latInput" value="{{ old('latitude', $store->latitude ?? '') }}" readonly style="width:100%;padding:8px 10px;border:1px solid #e5e8ef;border-radius:8px;font-size:12px;background:#f8fafc;color:#667085">
                    </div>
                    <div style="flex:1">
                        <label style="font-size:11px;font-weight:600;color:#667085">Longitude</label>
                        <input type="text" name="longitude" id="lngInput" value="{{ old('longitude', $store->longitude ?? '') }}" readonly style="width:100%;padding:8px 10px;border:1px solid #e5e8ef;border-radius:8px;font-size:12px;background:#f8fafc;color:#667085">
                    </div>
                    <button type="button" onclick="clearLocation()" style="align-self:flex-end;padding:8px 12px;background:#ffebee;color:#d32f2f;border:none;border-radius:8px;font-size:12px;cursor:pointer;font-weight:600">Clear</button>
                </div>
            </div>

            <label>Business Description
                <textarea name="description" placeholder="Describe what your store sells, opening hours, delivery options, etc.">{{ old('description', $store->description ?? '') }}</textarea>
            </label>

            <button class="kd-primary" type="submit">{{ $mode === 'edit' ? 'Update Store' : 'Submit for Approval' }}</button>
        </form>
    </div>
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
