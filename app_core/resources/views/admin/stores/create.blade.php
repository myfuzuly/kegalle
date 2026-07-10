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
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px">
            <input type="checkbox" name="whatsapp_same" id="whatsappSame" value="1" style="width:14px!important;height:14px!important;min-width:14px;margin:0!important;accent-color:#1b5e20" {{ old('whatsapp_same') ? 'checked' : '' }}>
            <label for="whatsappSame" style="font-size:13px;font-weight:500;margin:0;cursor:pointer">Same as phone number</label>
        </div>
        <input name="whatsapp" id="whatsappInput" value="{{ old('whatsapp') }}" placeholder="94771234567">
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

    <div class="ka-field ka-span-2">
        <label>Store Categories <small style="font-weight:400;color:#667085">(select all that apply)</small></label>
        <style>
            #storeCategoryPicker input[type="checkbox"]{width:16px!important;height:16px!important;min-width:16px;margin:0!important;accent-color:#1b5e20;cursor:pointer}
            #storeCategoryPicker .k-cat-main{display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:10px;cursor:pointer;transition:all .15s}
        </style>
        <input type="text" id="catSearch" placeholder="Search categories..." style="margin-top:4px;margin-bottom:6px;padding:10px 14px;border:1.5px solid #e5e8ef;border-radius:10px;font-size:13px;width:100%;box-sizing:border-box">
        <div id="storeCategoryPicker" style="border:1.5px solid #e5e8ef;border-radius:10px;padding:12px;max-height:300px;overflow-y:auto">
            @php $selectedCats = old('categories', []); @endphp
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

    <input type="hidden" name="latitude" value="{{ old('latitude') }}">
    <input type="hidden" name="longitude" value="{{ old('longitude') }}">

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

<script>
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
