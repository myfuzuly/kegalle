@extends('layouts.admin')
@section('title','Edit Location')
@section('page','Locations')
@section('heading','Edit Location')
@section('subheading','Update location hierarchy, type, slug URL and visibility')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/locations">← Back</a>@endsection

@push('styles')

@endpush

@section('content')

@if($errors->any())
<div class="alert-error">
    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
</div>
@endif

<div class="loc-shell">

<div>
<div class="loc-card">
    <div class="loc-card-head">
        <div class="loc-card-icon">✎</div>
        <span class="loc-card-title">Edit: <b>{{ $location->name }}</b></span>
    </div>
    <form method="post" action="/admin/locations/{{ $location->id }}">
    @csrf @method('PUT')
    {{-- hidden input for parent_id (avoids Select2 global init) --}}
    <input type="hidden" name="parent_id" id="locParentNative" value="{{ old('parent_id',$location->parent_id) }}">
    <div class="loc-body">
        <div class="loc-field">
            <label class="loc-label">Parent Location</label>
            <div class="ksd-wrap">
                <button type="button" class="ksd-trigger {{ old('parent_id',$location->parent_id) ? 'ksd-has-value':'' }}" id="locParentTrigger">
                    <span class="ksd-trigger-text {{ old('parent_id',$location->parent_id) ? '':'placeholder' }}" id="locParentLabel">
                        {{ optional($location->parent)->name ?? 'No Parent (top-level)' }}
                    </span>
                    <svg class="ksd-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="ksd-dropdown" id="locParentDropdown">
                    <div class="ksd-search-row"><input type="text" class="ksd-search" id="locParentSearch" placeholder="Search…" autocomplete="off"></div>
                    <div class="ksd-list" id="locParentList">
                        <div class="ksd-item {{ !$location->parent_id ? 'ksd-selected':'' }}" data-value="" data-label="No Parent (top-level)" data-search="">— No Parent (top-level) —</div>
                        @foreach($parents as $parent)
                        <div class="ksd-item {{ old('parent_id',$location->parent_id)==$parent->id ? 'ksd-selected':'' }}" data-value="{{ $parent->id }}" data-label="{{ $parent->name }}" data-search="{{ strtolower($parent->name) }}">
                            {{ $parent->name }} <span class="fs-11 text-muted">({{ ucfirst($parent->type) }})</span>
                        </div>
                        @endforeach
                        <div class="ksd-empty" id="locParentEmpty" class="hidden">No results</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="loc-grid">
            <div class="loc-field">
                <label class="loc-label">Name <span class="loc-req">*</span></label>
                <input name="name" value="{{ old('name',$location->name) }}" class="loc-input" required>
            </div>
            <div class="loc-field">
                <label class="loc-label">Slug URL</label>
                <input name="slug" value="{{ old('slug',$location->slug) }}" class="loc-input" required>
            </div>
        </div>
        <div class="flex-end-gap12">
            <div class="loc-field flex1-mb0">
                <label class="loc-label">Type <span class="loc-req">*</span></label>
                <div class="loc-type-group">
                    @foreach(['province','district','city','town'] as $t)
                    <label class="loc-type-pill {{ old('type',$location->type)===$t ? 'checked':'' }}">
                        <input type="radio" name="type" value="{{ $t }}" {{ old('type',$location->type)===$t ? 'checked':'' }}>
                        {{ ucfirst($t) }}
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="loc-field w100-ns-mb0">
                <label class="loc-label">Sort Order</label>
                <input name="sort_order" type="number" class="loc-input" value="{{ old('sort_order',$location->sort_order??0) }}">
            </div>
            <div class="loc-field ns-mb0">
                <label class="loc-label">&nbsp;</label>
                <label class="loc-check">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active',$location->is_active))>
                    Active
                </label>
            </div>
        </div>
    </div>
    <div class="loc-foot">
        <button type="submit" class="loc-btn-primary">💾 Update Location</button>
        <a href="/admin/locations" class="loc-btn-light">Cancel</a>
    </div>
    </form>
</div>
</div>

{{-- Sidebar: info --}}
<div>
<div class="loc-card">
    <div class="loc-card-head grad-green-pale">
        <div class="loc-card-icon grad-green-lt">📍</div>
        <div>
            <div class="loc-card-title">{{ $location->name }}</div>
            <div class="fs115-slate-mt2">ID #{{ $location->id }}</div>
        </div>
    </div>
    <div class="loc-body">
        <div class="flex-col-g10-fs13">
            <div class="flex-jsb"><span class="text-subtle">Type</span><span class="fw7-capitalize">{{ $location->type }}</span></div>
            <div class="flex-jsb"><span class="text-subtle">Slug</span><code class="tag-gray-xs">{{ $location->slug }}</code></div>
            <div class="flex-jsb"><span class="text-subtle">Parent</span><span>{{ optional($location->parent)->name ?? '—' }}</span></div>
            <div class="flex-jsb"><span class="text-subtle">Sort</span><span>{{ $location->sort_order }}</span></div>
            <div class="flex-jsb"><span class="text-subtle">Status</span>
                <span class="sa-status {{ $location->is_active ? 'active':'suspended' }}" class="fs-11">{{ $location->is_active ? 'Active':'Inactive' }}</span>
            </div>
        </div>
    </div>
</div>
<form class="mt-0" method="post" action="/admin/locations/{{ $location->id }}" onsubmit="return confirm('Delete this location permanently?')">
    @csrf @method('DELETE')
    <button class="btn-rose-full" type="submit" onmouseover="this.style.background='#fecdd3'" onmouseout="this.style.background='#fff1f2'">🗑 Delete Location</button>
</form>
</div>

</div>

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.querySelectorAll('.loc-type-pill').forEach(function(pill){
    pill.addEventListener('click', function(){
        document.querySelectorAll('.loc-type-pill').forEach(function(p){ p.classList.remove('checked'); });
        this.classList.add('checked');
    });
});

(function(){
    var trigger=document.getElementById('locParentTrigger');
    var dropdown=document.getElementById('locParentDropdown');
    var hidden=document.getElementById('locParentNative');
    var labelEl=document.getElementById('locParentLabel');
    var searchEl=document.getElementById('locParentSearch');
    var list=document.getElementById('locParentList');
    var emptyEl=document.getElementById('locParentEmpty');
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
        trigger.classList.toggle('ksd-has-value',!!item.dataset.value);
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
