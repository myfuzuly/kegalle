@extends('layouts.admin')
@section('title','Site Settings')
@section('page','Settings')
@section('heading','Site Settings')
@section('subheading','Manage marketplace branding, approvals, SEO and support information')

@push('styles')

@endpush

@section('content')

@if(session('success'))
<div class="alert-success">✓ Settings saved successfully.</div>
@endif

<form method="post" action="/admin/settings" id="setForm">
@csrf

{{-- Brand & Contact --}}
<div class="set-card">
    <div class="set-card-head">
        <div class="set-card-icon blue">🏷️</div>
        <span class="set-card-title">Brand &amp; Contact</span>
    </div>
    <div class="set-body">
        <div class="set-grid">
            <div class="set-field">
                <label class="set-label">Site Name</label>
                <input name="site_name" class="set-input" value="{{ old('site_name', $settings['site_name'] ?? 'Kegalle') }}" required>
            </div>
            <div class="set-field">
                <label class="set-label">Site Tagline</label>
                <input name="site_tagline" class="set-input" value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}" placeholder="Your marketplace tagline">
            </div>
            <div class="set-field">
                <label class="set-label">Support Email</label>
                <input name="support_email" type="email" class="set-input" value="{{ old('support_email', $settings['support_email'] ?? '') }}" placeholder="support@example.com">
            </div>
            <div class="set-field">
                <label class="set-label">Support Phone</label>
                <input name="support_phone" class="set-input" value="{{ old('support_phone', $settings['support_phone'] ?? '') }}" placeholder="077 1234567">
            </div>
            <div class="set-field">
                <label class="set-label">WhatsApp Number</label>
                <input name="whatsapp_number" class="set-input" value="{{ old('whatsapp_number', $settings['whatsapp_number'] ?? '') }}" placeholder="94771234567 (international format)">
            </div>
            <div class="set-field">
                <label class="set-label">Default Currency</label>
                <input name="default_currency" class="set-input" value="{{ old('default_currency', $settings['default_currency'] ?? 'LKR') }}" required>
            </div>
        </div>
    </div>
</div>

{{-- Marketplace Rules --}}
<div class="set-card">
    <div class="set-card-head">
        <div class="set-card-icon amber">⚙️</div>
        <span class="set-card-title">Marketplace Rules</span>
    </div>
    <div class="set-body">
        {{-- hidden inputs for approval mode ksd --}}
        <input type="hidden" name="listing_approval_mode" id="setListApprNative" value="{{ old('listing_approval_mode',$settings['listing_approval_mode']??'manual') }}">
        <input type="hidden" name="store_approval_mode" id="setStoreApprNative" value="{{ old('store_approval_mode',$settings['store_approval_mode']??'manual') }}">
        <div class="set-grid">
            <div class="set-field">
                <label class="set-label">Listing Approval</label>
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger ksd-has-value" id="setListApprTrigger">
                        <span class="ksd-trigger-text" id="setListApprLabel">{{ (($settings['listing_approval_mode']??'manual')==='auto') ? 'Auto Approval' : 'Manual Approval' }}</span>
                        <svg class="ksd-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="setListApprDropdown">
                        <div class="ksd-list">
                            <div class="ksd-item {{ (($settings['listing_approval_mode']??'manual')==='manual') ? 'ksd-selected':'' }}" data-value="manual" data-label="Manual Approval">🔒 Manual Approval</div>
                            <div class="ksd-item {{ (($settings['listing_approval_mode']??'')==='auto') ? 'ksd-selected':'' }}" data-value="auto" data-label="Auto Approval">⚡ Auto Approval</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="set-field">
                <label class="set-label">Store Approval</label>
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger ksd-has-value" id="setStoreApprTrigger">
                        <span class="ksd-trigger-text" id="setStoreApprLabel">{{ (($settings['store_approval_mode']??'manual')==='auto') ? 'Auto Approval' : 'Manual Approval' }}</span>
                        <svg class="ksd-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="setStoreApprDropdown">
                        <div class="ksd-list">
                            <div class="ksd-item {{ (($settings['store_approval_mode']??'manual')==='manual') ? 'ksd-selected':'' }}" data-value="manual" data-label="Manual Approval">🔒 Manual Approval</div>
                            <div class="ksd-item {{ (($settings['store_approval_mode']??'')==='auto') ? 'ksd-selected':'' }}" data-value="auto" data-label="Auto Approval">⚡ Auto Approval</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="set-field">
                <label class="set-label">Max Images Per Listing</label>
                <input name="max_images_per_listing" type="number" class="set-input" value="{{ old('max_images_per_listing', $settings['max_images_per_listing'] ?? 10) }}">
            </div>
            <div class="set-field justify-end">
                <label class="set-check danger">
                    <input type="checkbox" name="maintenance_mode" value="1" @checked(($settings['maintenance_mode']??'0')==='1')>
                    🚧 Maintenance Mode
                </label>
                <span class="set-hint mt-4px">When enabled, the public site shows a maintenance message.</span>
            </div>
        </div>
    </div>
</div>

{{-- Home Page Display --}}
<div class="set-card">
    <div class="set-card-head">
        <div class="set-card-icon green">🏠</div>
        <span class="set-card-title">Home Page Display</span>
    </div>
    <div class="set-body">
        <div class="set-field mw-280">
            <label class="set-label">Categories shown on home page</label>
            <input name="home_categories_count" type="number" min="6" max="60" step="6" class="set-input" value="{{ old('home_categories_count', $settings['home_categories_count'] ?? 12) }}">
            <span class="set-hint">Each row shows 6 categories. 12 = 2 rows, 18 = 3 rows, 30 = all categories.</span>
        </div>
    </div>
</div>

{{-- SEO Defaults --}}
<div class="set-card">
    <div class="set-card-head">
        <div class="set-card-icon purple">🔍</div>
        <span class="set-card-title">SEO Defaults</span>
    </div>
    <div class="set-body">
        <div class="set-grid">
            <div class="set-field">
                <label class="set-label">Meta Title</label>
                <input name="meta_title" class="set-input" value="{{ old('meta_title', $settings['meta_title'] ?? '') }}" placeholder="Site-wide meta title">
            </div>
            <div class="set-field set-full">
                <label class="set-label">Meta Description</label>
                <textarea name="meta_description" class="set-textarea" rows="3" placeholder="Site-wide meta description shown in search results">{{ old('meta_description', $settings['meta_description'] ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="set-card">
    <div class="set-card-head">
        <div class="set-card-icon slate">💾</div>
        <span class="set-card-title">Save</span>
    </div>
    <div class="set-foot">
        <button type="submit" class="set-btn-primary">💾 Save Settings</button>
    </div>
</div>

</form>

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
function simpleKsd(triggerId, dropdownId, hiddenId, labelId) {
    var trigger=document.getElementById(triggerId);
    var dropdown=document.getElementById(dropdownId);
    var hidden=document.getElementById(hiddenId);
    var labelEl=document.getElementById(labelId);
    if(!trigger) return;
    var open=false;
    function openD(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); open=true; }
    function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    dropdown.querySelectorAll('.ksd-item').forEach(function(item){
        item.addEventListener('click',function(){
            hidden.value=this.dataset.value;
            labelEl.textContent=this.dataset.label;
            trigger.classList.add('ksd-has-value');
            dropdown.querySelectorAll('.ksd-item').forEach(function(i){ i.classList.remove('ksd-selected'); });
            this.classList.add('ksd-selected');
            closeD();
        });
    });
}
simpleKsd('setListApprTrigger','setListApprDropdown','setListApprNative','setListApprLabel');
simpleKsd('setStoreApprTrigger','setStoreApprDropdown','setStoreApprNative','setStoreApprLabel');
</script>
@endpush
