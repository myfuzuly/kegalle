@extends('layouts.admin')
@section('title','Add Government Service')
@section('page','Government Services')
@section('heading','Add Government Service')
@section('subheading','Create a new service for the Government Services page')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/government-services">← Back</a>@endsection

@push('styles')

@endpush

@section('content')

@if($errors->any())
<div class="alert-error">
    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
</div>
@endif

<form method="post" action="/admin/government-services" enctype="multipart/form-data" id="gsvForm">
@csrf
<div class="gsv-shell">

<div>
    {{-- Basic info --}}
    <div class="gsv-card">
        <div class="gsv-card-head">
            <div class="gsv-card-icon green">🏛️</div>
            <span class="gsv-card-title">Service Details</span>
        </div>
        <div class="gsv-body">
            <div class="gsv-field">
                <label class="gsv-label">Title <span class="gsv-req">*</span></label>
                <input name="title" class="gsv-input" value="{{ old('title') }}" required placeholder="e.g. DS Office, Municipal Council">
            </div>
            <div class="gsv-field">
                <label class="gsv-label">Short Description</label>
                <textarea name="description" class="gsv-textarea" rows="2" placeholder="Brief description shown on the card">{{ old('description') }}</textarea>
            </div>
            <div class="gsv-grid">
                <div class="gsv-field">
                    <label class="gsv-label">Icon <span class="gsv-hint" class="fw-400">(emoji, used when no image)</span></label>
                    <input name="icon" class="gsv-input" value="{{ old('icon','🏛️') }}" maxlength="10" id="gsvIcon">
                </div>
                <div class="gsv-field">
                    <label class="gsv-label">Sort Order</label>
                    <input name="sort_order" type="number" class="gsv-input" value="{{ old('sort_order',0) }}">
                </div>
            </div>
        </div>
    </div>

    {{-- Appearance --}}
    <div class="gsv-card">
        <div class="gsv-card-head">
            <div class="gsv-card-icon blue">🎨</div>
            <span class="gsv-card-title">Appearance &amp; Icon Image</span>
        </div>
        <div class="gsv-body">
            <div class="gsv-field">
                <label class="gsv-label">Service Icon Image <span class="gsv-hint" class="fw-400">(optional — JPG/PNG/WEBP, max 3MB)</span></label>
                <input type="file" name="image" class="gsv-input p8-13" accept="image/jpeg,image/png,image/webp">
                <span class="gsv-hint">If left empty, the icon emoji + gradient colors are used.</span>
            </div>
            <div class="gsv-grid">
                <div class="gsv-field">
                    <label class="gsv-label">Icon Background Start</label>
                    <div class="gsv-color-row">
                        <input type="color" name="icon_bg_start" class="gsv-color-input" value="{{ old('icon_bg_start','#1e6b3a') }}" id="gsvBgStart">
                        <input class="gsv-input" value="{{ old('icon_bg_start','#1e6b3a') }}" class="flex-1" readonly id="gsvBgStartText">
                    </div>
                </div>
                <div class="gsv-field">
                    <label class="gsv-label">Icon Background End</label>
                    <div class="gsv-color-row">
                        <input type="color" name="icon_bg_end" class="gsv-color-input" value="{{ old('icon_bg_end','#2e9b5a') }}" id="gsvBgEnd">
                        <input class="gsv-input" value="{{ old('icon_bg_end','#2e9b5a') }}" class="flex-1" readonly id="gsvBgEndText">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="gsv-card">
        <div class="gsv-card-head">
            <div class="gsv-card-icon purple">📝</div>
            <span class="gsv-card-title">Full Page Content</span>
        </div>
        <div class="gsv-body">
            <div class="gsv-field">
                <label class="gsv-label">Content <span class="gsv-hint" class="fw-400">(HTML supported)</span></label>
                <textarea name="content" class="gsv-textarea" rows="8" placeholder="Detailed information about this service — HTML is supported">{{ old('content') }}</textarea>
                <span class="gsv-hint">Shown on the individual service page (e.g. /government-services/ds-office).</span>
            </div>
        </div>
    </div>

    {{-- Contact --}}
    <div class="gsv-card">
        <div class="gsv-card-head">
            <div class="gsv-card-icon amber">📞</div>
            <span class="gsv-card-title">Contact &amp; Location</span>
        </div>
        <div class="gsv-body">
            <div class="gsv-grid">
                <div class="gsv-field">
                    <label class="gsv-label">Phone</label>
                    <input name="phone" class="gsv-input" value="{{ old('phone') }}" placeholder="037-2222222">
                </div>
                <div class="gsv-field">
                    <label class="gsv-label">Email</label>
                    <input type="email" name="email" class="gsv-input" value="{{ old('email') }}" placeholder="info@example.gov.lk">
                </div>
            </div>
            <div class="gsv-field">
                <label class="gsv-label">Address</label>
                <input name="address" class="gsv-input" value="{{ old('address') }}" placeholder="Main Street, Kegalle">
            </div>
            <div class="gsv-field">
                <label class="gsv-label">Google Maps Embed URL <span class="gsv-hint" class="fw-400">(optional)</span></label>
                <input name="map_url" class="gsv-input" value="{{ old('map_url') }}" placeholder="https://www.google.com/maps/embed?pb=...">
            </div>
        </div>
    </div>
</div>

<div>
    <div class="gsv-card">
        <div class="gsv-card-head">
            <div class="gsv-card-icon blue">👁️</div>
            <span class="gsv-card-title">Icon Preview</span>
        </div>
        <div class="gsv-body center-pt24">
            <div class="gsv-icon-preview grad-green3" id="gsvPreview">🏛️</div>
            <div class="fs-12 text-muted">Preview updates as you type</div>
        </div>
    </div>
    <div class="gsv-card">
        <div class="gsv-card-head">
            <div class="gsv-card-icon green">⚙️</div>
            <span class="gsv-card-title">Visibility</span>
        </div>
        <div class="gsv-body">
            <label class="gsv-check-wrap">
                <input type="checkbox" name="is_active" value="1" checked>
                Active (visible on site)
            </label>
        </div>
        <div class="gsv-foot">
            <button type="submit" class="gsv-btn-primary">✚ Create Service</button>
        </div>
    </div>
</div>

</div>
</form>

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var preview=document.getElementById('gsvPreview');
    var bgStart=document.getElementById('gsvBgStart');
    var bgEnd=document.getElementById('gsvBgEnd');
    var icon=document.getElementById('gsvIcon');
    function update(){
        preview.style.background='linear-gradient(135deg,'+bgStart.value+','+bgEnd.value+')';
        preview.textContent=icon.value||'🏛️';
    }
    bgStart.addEventListener('input',function(){ document.getElementById('gsvBgStartText').value=this.value; update(); });
    bgEnd.addEventListener('input',function(){ document.getElementById('gsvBgEndText').value=this.value; update(); });
    icon.addEventListener('input',update);
    update();
})();
</script>
@endpush
