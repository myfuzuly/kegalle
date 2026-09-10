@extends('layouts.admin')
@section('title','Add Explore Card')
@section('page','Explore Kegalle')
@section('heading','Add Explore Card')
@section('subheading','Create a new card for the homepage "Explore in Kegalle" section')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/explore-items">← Back</a>@endsection

@push('styles')

@endpush

@section('content')

@if($errors->any())
<div class="alert-error">
    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
</div>
@endif

<form method="post" action="/admin/explore-items" enctype="multipart/form-data" id="expForm">
@csrf
<div class="exp-shell">

<div>
    <div class="exp-card">
        <div class="exp-card-head">
            <div class="exp-card-icon green">📍</div>
            <span class="exp-card-title">Card Details</span>
        </div>
        <div class="exp-body">
            <div class="exp-field">
                <label class="exp-label">Title <span class="exp-req">*</span></label>
                <input name="title" class="exp-input" value="{{ old('title') }}" required placeholder="e.g. Activities, Tourist Places">
            </div>
            <div class="exp-grid">
                <div class="exp-field">
                    <label class="exp-label">Icon <span class="exp-hint" class="fw-400">(emoji — used when no photo)</span></label>
                    <input name="icon" class="exp-input" value="{{ old('icon','📍') }}" maxlength="10" id="expIcon">
                </div>
                <div class="exp-field">
                    <label class="exp-label">Sort Order</label>
                    <input name="sort_order" type="number" class="exp-input" value="{{ old('sort_order',0) }}">
                </div>
            </div>
            <div class="exp-grid">
                <div class="exp-field">
                    <label class="exp-label">Link URL</label>
                    <input name="link_url" class="exp-input" value="{{ old('link_url') }}" placeholder="/listings?categories[]=activities">
                </div>
                <div class="exp-field">
                    <label class="exp-label">Link Label</label>
                    <input name="link_label" class="exp-input" value="{{ old('link_label') }}" placeholder="Explore →">
                </div>
            </div>
            <div class="exp-field">
                <label class="exp-label">Bullet Points <span class="exp-hint" class="fw-400">(one per line)</span></label>
                <textarea name="items" class="exp-textarea" placeholder="Hiking &amp; Trekking&#10;Water Activities&#10;Camping &amp; Outdoor">{{ old('items') }}</textarea>
            </div>
        </div>
    </div>

    <div class="exp-card">
        <div class="exp-card-head">
            <div class="exp-card-icon blue">🖼️</div>
            <span class="exp-card-title">Photo &amp; Appearance</span>
        </div>
        <div class="exp-body">
            <div class="exp-field">
                <label class="exp-label">Card Photo <span class="exp-hint" class="fw-400">(optional — JPG/PNG/WEBP, max 3MB)</span></label>
                <input type="file" name="image" class="exp-input p8-13" accept="image/jpeg,image/png,image/webp">
                <span class="exp-hint">If left empty, the icon + gradient below is used instead.</span>
            </div>
            <div class="exp-grid">
                <div class="exp-field">
                    <label class="exp-label">Gradient Start</label>
                    <div class="exp-color-row">
                        <input type="color" name="gradient_start" class="exp-color-input" value="{{ old('gradient_start','#1B5E20') }}" id="expGradStart">
                        <input class="exp-input" value="{{ old('gradient_start','#1B5E20') }}" class="flex-1" placeholder="#1B5E20" id="expGradStartText" readonly>
                    </div>
                </div>
                <div class="exp-field">
                    <label class="exp-label">Gradient End</label>
                    <div class="exp-color-row">
                        <input type="color" name="gradient_end" class="exp-color-input" value="{{ old('gradient_end','#388E3C') }}" id="expGradEnd">
                        <input class="exp-input" value="{{ old('gradient_end','#388E3C') }}" class="flex-1" placeholder="#388E3C" id="expGradEndText" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div>
    <div class="exp-card">
        <div class="exp-card-head">
            <div class="exp-card-icon amber">👁️</div>
            <span class="exp-card-title">Preview</span>
        </div>
        <div class="exp-body">
            <div class="exp-preview grad-green" id="expPreview">
                <div class="exp-preview-icon" id="expPreviewIcon">📍</div>
                <div id="expPreviewTitle">Card Title</div>
            </div>
        </div>
    </div>
    <div class="exp-card">
        <div class="exp-card-head">
            <div class="exp-card-icon green">⚙️</div>
            <span class="exp-card-title">Settings</span>
        </div>
        <div class="exp-body">
            <label class="exp-check-wrap">
                <input type="checkbox" name="is_active" value="1" checked>
                Active (visible on homepage)
            </label>
        </div>
        <div class="exp-foot">
            <button type="submit" class="exp-btn-primary">✚ Create Card</button>
        </div>
    </div>
</div>

</div>
</form>

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var preview = document.getElementById('expPreview');
    var previewIcon = document.getElementById('expPreviewIcon');
    var previewTitle = document.getElementById('expPreviewTitle');
    var gradStart = document.getElementById('expGradStart');
    var gradEnd = document.getElementById('expGradEnd');
    var iconInput = document.getElementById('expIcon');
    var titleInput = document.querySelector('input[name="title"]');
    function updatePreview(){
        preview.style.background = 'linear-gradient(135deg,' + gradStart.value + ',' + gradEnd.value + ')';
        previewIcon.textContent = iconInput.value || '📍';
        previewTitle.textContent = titleInput.value || 'Card Title';
    }
    gradStart.addEventListener('input',function(){ document.getElementById('expGradStartText').value=this.value; updatePreview(); });
    gradEnd.addEventListener('input',function(){ document.getElementById('expGradEndText').value=this.value; updatePreview(); });
    iconInput.addEventListener('input',updatePreview);
    titleInput.addEventListener('input',updatePreview);
    updatePreview();
})();
</script>
@endpush
