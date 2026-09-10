@extends('layouts.admin')
@section('title','Add Ad Banner')
@section('page','Ad Spaces')
@section('heading','Add Ad Banner')
@section('subheading','Create a new sponsored banner and assign it to a placement location')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/ad-banners">← Back</a>@endsection

@push('styles')

@endpush

@section('content')

@if($errors->any())
<div class="alert-error">
    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
</div>
@endif

<form method="post" action="/admin/ad-banners" enctype="multipart/form-data">
@csrf
{{-- hidden input for location ksd --}}
<input type="hidden" name="location" id="adbLocNative" value="{{ old('location','') }}" required>

<div class="adb-shell">

<div>
    <div class="adb-card">
        <div class="adb-card-head">
            <div class="adb-card-icon blue">📢</div>
            <span class="adb-card-title">Banner Details</span>
        </div>
        <div class="adb-body">
            <div class="adb-field">
                <label class="adb-label">Title / Advertiser Name <span class="adb-req">*</span></label>
                <input name="title" class="adb-input" value="{{ old('title') }}" required placeholder="e.g. ABC Motors Kegalle">
            </div>
            <div class="adb-field">
                <label class="adb-label">Placement Location <span class="adb-req">*</span></label>
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger {{ old('location') ? 'ksd-has-value':'' }}" id="adbLocTrigger">
                        <span class="ksd-trigger-text {{ old('location') ? '':'placeholder' }}" id="adbLocLabel">
                            {{ old('location') ? ($locations[old('location')] ?? old('location')) : 'Select placement location…' }}
                        </span>
                        <svg class="ksd-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="adbLocDropdown">
                        <div class="ksd-search-row"><input type="text" class="ksd-search" id="adbLocSearch" placeholder="Search placements…" autocomplete="off"></div>
                        <div class="ksd-list" id="adbLocList">
                            @foreach($locations as $key => $label)
                            <div class="ksd-item {{ old('location')===$key ? 'ksd-selected':'' }}" data-value="{{ $key }}" data-label="{{ $label }}" data-search="{{ strtolower($label) }}">{{ $label }}</div>
                            @endforeach
                            <div class="ksd-empty" id="adbLocEmpty" class="hidden">No placements match</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="adb-field">
                <label class="adb-label">Click-through Link <span class="adb-hint" class="fw-400">(optional)</span></label>
                <input type="url" name="link_url" class="adb-input" value="{{ old('link_url') }}" placeholder="https://...">
            </div>
            <div class="adb-field">
                <label class="adb-label">Sort Order</label>
                <input name="sort_order" type="number" class="adb-input" value="{{ old('sort_order',0) }}">
                <span class="adb-hint">Lower numbers show first when multiple banners share a location.</span>
            </div>
        </div>
    </div>
    <div class="adb-card">
        <div class="adb-card-head">
            <div class="adb-card-icon blue">🖼️</div>
            <span class="adb-card-title">Banner Image</span>
        </div>
        <div class="adb-body">
            <div class="adb-field">
                <label class="adb-label">Upload Image</label>
                <input type="file" name="image" class="adb-input p8-13" accept="image/jpeg,image/png,image/webp">
                <span class="adb-hint">JPG/PNG/WEBP, max 3MB. Use dimensions matching the selected location's recommended size.</span>
            </div>
        </div>
    </div>
</div>

<div>
    <div class="adb-card">
        <div class="adb-card-head">
            <div class="adb-card-icon amber">📅</div>
            <span class="adb-card-title">Schedule</span>
        </div>
        <div class="adb-body">
            <div class="adb-field">
                <label class="adb-label">Starts On <span class="adb-hint" class="fw-400">(optional)</span></label>
                <input type="date" name="starts_at" class="adb-input" value="{{ old('starts_at') }}">
            </div>
            <div class="adb-field">
                <label class="adb-label">Ends On <span class="adb-hint" class="fw-400">(optional)</span></label>
                <input type="date" name="ends_at" class="adb-input" value="{{ old('ends_at') }}">
                <span class="adb-hint">Leave both dates empty to run indefinitely.</span>
            </div>
        </div>
    </div>
    <div class="adb-card">
        <div class="adb-card-head">
            <div class="adb-card-icon green">⚙️</div>
            <span class="adb-card-title">Status</span>
        </div>
        <div class="adb-body">
            <label class="adb-check-wrap">
                <input type="checkbox" name="is_active" value="1" checked>
                Active (visible on site)
            </label>
        </div>
        <div class="adb-foot">
            <button type="submit" class="adb-btn-primary">✚ Create Banner</button>
        </div>
    </div>
</div>

</div>
</form>

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var trigger=document.getElementById('adbLocTrigger');
    var dropdown=document.getElementById('adbLocDropdown');
    var hidden=document.getElementById('adbLocNative');
    var labelEl=document.getElementById('adbLocLabel');
    var searchEl=document.getElementById('adbLocSearch');
    var list=document.getElementById('adbLocList');
    var emptyEl=document.getElementById('adbLocEmpty');
    var open=false;
    function getItems(){ return list.querySelectorAll('.ksd-item'); }
    function openD(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); searchEl.value=''; filterItems(''); searchEl.focus(); open=true; }
    function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    searchEl.addEventListener('input',function(){ filterItems(this.value.toLowerCase()); });
    list.addEventListener('click',function(e){
        var item=e.target.closest('.ksd-item'); if(!item) return;
        hidden.value=item.dataset.value;
        labelEl.textContent=item.dataset.label;
        labelEl.classList.remove('placeholder');
        trigger.classList.add('ksd-has-value');
        getItems().forEach(function(i){ i.classList.remove('ksd-selected'); });
        item.classList.add('ksd-selected');
        closeD();
    });
    function filterItems(q){
        var any=false;
        getItems().forEach(function(i){
            var m=!q||(i.dataset.search||'').indexOf(q)!==-1;
            i.style.display=m?'':'none';
            if(m) any=true;
        });
        emptyEl.style.display=any?'none':'';
    }
})();
</script>
@endpush
