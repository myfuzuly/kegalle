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
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                        <input type="checkbox" name="whatsapp_same" id="whatsappSame" value="1" style="width:auto;margin:0" {{ old('whatsapp_same') ? 'checked' : '' }}>
                        <label for="whatsappSame" style="font-size:13px;font-weight:500;margin:0;cursor:pointer">Same as phone number</label>
                    </div>
                    <input name="whatsapp" id="whatsappInput" value="{{ old('whatsapp', $store->whatsapp ?? '') }}" placeholder="07X XXX XXXX">
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

            <label>Store Categories <small style="font-weight:400;color:#667085">(select all that apply)</small>
                <div id="storeCategoryPicker" style="border:1.5px solid #e5e8ef;border-radius:10px;padding:12px;max-height:260px;overflow-y:auto;margin-top:4px">
                    @php $selectedCats = old('categories', ($mode === 'edit' && $store->exists) ? $store->categories->pluck('id')->toArray() : []); @endphp
                    @foreach($categories as $parent)
                        <div style="margin-bottom:10px" class="k-cat-group">
                            @if($parent->children->isNotEmpty())
                                <label style="display:inline-flex;align-items:center;gap:5px;font-size:13px;color:#1b5e20;cursor:pointer;font-weight:700" class="k-cat-parent">
                                    <input type="checkbox" class="k-parent-cb" style="width:auto;margin:0;accent-color:#1b5e20" data-parent="{{ $parent->id }}">
                                    {{ $parent->icon ?? '' }} {{ $parent->name }}
                                </label>
                            @else
                                <strong style="font-size:13px;color:#1b5e20">{{ $parent->icon ?? '' }} {{ $parent->name }}</strong>
                            @endif
                            <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:4px;padding-left:4px">
                                @foreach($parent->children as $child)
                                    <label style="display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:500;padding:4px 10px;border:1px solid #e5e8ef;border-radius:8px;cursor:pointer;{{ in_array($child->id, $selectedCats) ? 'background:#e8f5e9;border-color:#1b5e20' : '' }}" class="k-cat-chip" data-parent="{{ $parent->id }}">
                                        <input type="checkbox" name="categories[]" value="{{ $child->id }}" style="width:auto;margin:0" {{ in_array($child->id, $selectedCats) ? 'checked' : '' }}>
                                        {{ $child->name }}
                                    </label>
                                @endforeach
                                @if($parent->children->isEmpty())
                                    <label style="display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:500;padding:4px 10px;border:1px solid #e5e8ef;border-radius:8px;cursor:pointer;{{ in_array($parent->id, $selectedCats) ? 'background:#e8f5e9;border-color:#1b5e20' : '' }}" class="k-cat-chip">
                                        <input type="checkbox" name="categories[]" value="{{ $parent->id }}" style="width:auto;margin:0" {{ in_array($parent->id, $selectedCats) ? 'checked' : '' }}>
                                        {{ $parent->name }}
                                    </label>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </label>

            <label>Address
                <textarea name="address" placeholder="Street, town, district">{{ old('address', $store->address ?? '') }}</textarea>
            </label>

            {{-- Map hidden for now — will be implemented later --}}
            <input type="hidden" name="latitude" value="{{ old('latitude', $store->latitude ?? '') }}">
            <input type="hidden" name="longitude" value="{{ old('longitude', $store->longitude ?? '') }}">

            <label>Business Description
                <textarea name="description" placeholder="Describe what your store sells, opening hours, delivery options, etc.">{{ old('description', $store->description ?? '') }}</textarea>
            </label>

            <button class="kd-primary" type="submit">{{ $mode === 'edit' ? 'Update Store' : 'Submit for Approval' }}</button>
        </form>
    </div>
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

function styleChip(lbl, on) {
    lbl.style.background = on ? '#e8f5e9' : '';
    lbl.style.borderColor = on ? '#1b5e20' : '#e5e8ef';
}

function syncParentCb(parentId) {
    var chips = document.querySelectorAll('.k-cat-chip[data-parent="' + parentId + '"] input');
    var parentCb = document.querySelector('.k-parent-cb[data-parent="' + parentId + '"]');
    if (!parentCb || !chips.length) return;
    var all = true, none = true;
    chips.forEach(function(c) { if (c.checked) none = false; else all = false; });
    parentCb.checked = all;
    parentCb.indeterminate = !all && !none;
}

document.querySelectorAll('.k-cat-chip input[type="checkbox"]').forEach(function(cb) {
    cb.addEventListener('change', function() {
        styleChip(this.closest('.k-cat-chip'), this.checked);
        var parent = this.closest('.k-cat-chip').getAttribute('data-parent');
        if (parent) syncParentCb(parent);
    });
});

document.querySelectorAll('.k-parent-cb').forEach(function(pcb) {
    var pid = pcb.getAttribute('data-parent');
    syncParentCb(pid);
    pcb.addEventListener('change', function() {
        var on = this.checked;
        document.querySelectorAll('.k-cat-chip[data-parent="' + pid + '"]').forEach(function(lbl) {
            var cb = lbl.querySelector('input');
            cb.checked = on;
            styleChip(lbl, on);
        });
    });
});
</script>
@endsection
