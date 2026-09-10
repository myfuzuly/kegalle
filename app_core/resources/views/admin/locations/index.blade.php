@extends('layouts.admin')
@section('title','Locations')
@section('page','Locations')
@section('heading','Location Management')
@section('subheading','Manage province, district, city and town hierarchy with editable slug URLs')

@push('styles')

@endpush

@section('content')

@if(session('success'))
<div class="alert-success">✓ {{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert-error">
    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
</div>
@endif

<div class="loc-shell">

{{-- LEFT: Add form --}}
<div>
    <div class="loc-card">
        <div class="loc-card-head">
            <div class="loc-card-icon green">✚</div>
            <span class="loc-card-title">Add New Location</span>
        </div>
        <form method="POST" action="{{ route('admin.locations.store') }}">
        @csrf
        {{-- hidden input for parent_id (avoids Select2 global init) --}}
        <input type="hidden" name="parent_id" id="locParentNative" value="{{ old('parent_id','') }}">

        <div class="loc-body">
            <div class="loc-field">
                <label class="loc-label">Parent Location</label>
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger" id="locParentTrigger">
                        <span class="ksd-trigger-text placeholder" id="locParentLabel">No Parent (top-level)</span>
                        <svg class="ksd-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="locParentDropdown">
                        <div class="ksd-search-row"><input type="text" class="ksd-search" id="locParentSearch" placeholder="Search…" autocomplete="off"></div>
                        <div class="ksd-list" id="locParentList">
                            <div class="ksd-item ksd-selected" data-value="" data-label="No Parent (top-level)" data-search="">— No Parent (top-level) —</div>
                            @foreach($parents as $parent)
                            <div class="ksd-item" data-value="{{ $parent->id }}" data-label="{{ $parent->parent ? $parent->parent->name.' / ' : '' }}{{ $parent->name }}" data-search="{{ strtolower($parent->name) }}">
                                @if($parent->parent)<small class="text-muted">{{ $parent->parent->name }} /</small> @endif{{ $parent->name }}
                                <span class="fs11-muted-ml4">({{ ucfirst($parent->type) }})</span>
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
                    <input name="name" value="{{ old('name') }}" class="loc-input" placeholder="Location name" required>
                </div>
                <div class="loc-field">
                    <label class="loc-label">Slug URL</label>
                    <input name="slug" value="{{ old('slug') }}" class="loc-input" placeholder="auto-generate if empty">
                </div>
            </div>
            <div class="flex-end-gap12">
                <div class="loc-field" class="flex-1">
                    <label class="loc-label">Type <span class="loc-req">*</span></label>
                    <div class="loc-type-group" id="locAddTypeGroup">
                        @foreach(['province','district','city','town'] as $t)
                        <label class="loc-type-pill {{ old('type','city')===$t ? 'checked':'' }}">
                            <input type="radio" name="type" value="{{ $t }}" {{ old('type','city')===$t ? 'required checked' : '' }}>
                            {{ ucfirst($t) }}
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="loc-field w100-ns">
                    <label class="loc-label">Sort Order</label>
                    <input name="sort_order" type="number" class="loc-input" value="{{ old('sort_order',0) }}">
                </div>
                <div class="loc-field" class="flex-shrink-0">
                    <label class="loc-label">&nbsp;</label>
                    <label class="loc-check">
                        <input type="checkbox" name="is_active" value="1" checked>
                        Active
                    </label>
                </div>
            </div>
        </div>
        <div class="loc-foot">
            <button type="submit" class="loc-btn-primary">+ Add Location</button>
        </div>
        </form>
    </div>
</div>

{{-- RIGHT: Summary --}}
<div>
    <div class="loc-card flex-col">
        <div class="loc-card-head">
            <div class="loc-card-icon blue">📍</div>
            <span class="loc-card-title">Summary</span>
        </div>
        <div class="loc-body flex1-col-g0">
            @php
            $typeData = [
                'province' => ['cls'=>'province','icon'=>'🏔️','label'=>'Provinces'],
                'district' => ['cls'=>'district','icon'=>'🗺️','label'=>'Districts'],
                'city'     => ['cls'=>'city',    'icon'=>'🏙️','label'=>'Cities'],
                'town'     => ['cls'=>'town',    'icon'=>'🏘️','label'=>'Towns'],
            ];
            @endphp
            @foreach($typeData as $t => $td)
            <div class="flex-g12-p14-bb">
                <span class="loc-type-tag {{ $td['cls'] }} p5-12-fs12">{{ $td['icon'] }} {{ $td['label'] }}</span>
                <span class="mla-stat-num">{{ $locations->getCollection()->where('type',$t)->count() }}</span>
            </div>
            @endforeach
            <div class="flex-1"></div>
            <div class="flex-jsb-bt-mt8">
                <span class="fs13-fw7-gray">Total Locations</span>
                <span class="fs26-fw8-green-tnum">{{ $locations->total() }}</span>
            </div>
        </div>
    </div>
</div>

</div>

{{-- Locations table --}}
<div class="loc-card">
    <div class="loc-card-head">
        <div class="loc-card-icon blue">≡</div>
        <span class="loc-card-title">All Locations</span>
        <span class="loc-card-meta">{{ $locations->total() }} locations</span>
    </div>
    <form method="get" class="loc-filter-bar">
        <input name="q" value="{{ request('q') }}" class="loc-filter-input" placeholder="🔍 Search location or slug…">
        <button type="submit" class="loc-filter-btn">Filter</button>
        @if(request()->hasAny(['q','type']))<a href="/admin/locations" class="loc-filter-clear">✕ Clear</a>@endif
        <div class="flex-1"></div>
        @foreach(['province','district','city','town'] as $t)
        <a href="?type={{ $t }}" class="loc-type-tag {{ $t }}" style="text-decoration:none;cursor:pointer;{{ request('type')===$t ? 'outline:2px solid currentColor;outline-offset:1px' : '' }}">{{ ucfirst($t) }}</a>
        @endforeach
    </form>
    <div class="loc-table-wrap">
    <table class="loc-table">
    <thead><tr>
        <th>Location</th><th>Parent</th><th>Type</th><th>Slug</th><th class="text-center">Sort</th><th class="text-center">Status</th><th>Actions</th>
    </tr></thead>
    <tbody>
    @forelse($locations as $location)
    <tr>
        <td>
            <span class="fw-700">{{ $location->name }}</span>
            <small class="muted-ml4b">#{{ $location->id }}</small>
        </td>
        <td><span class="text-subtle">{{ $location->parent->name ?? '—' }}</span></td>
        <td><span class="loc-type-tag {{ $location->type }}">{{ ucfirst($location->type) }}</span></td>
        <td><code class="code-tag">{{ $location->slug }}</code></td>
        <td class="center-muted">{{ $location->sort_order }}</td>
        <td class="text-center">
            <span class="sa-status {{ $location->is_active ? 'active':'suspended' }}">{{ $location->is_active ? 'Active':'Inactive' }}</span>
        </td>
        <td>
            <div class="loc-actions">
                <a href="/admin/locations/{{ $location->id }}/edit" class="loc-act">Edit</a>
                <form method="post" action="/admin/locations/{{ $location->id }}/toggle" class="d-contents">@csrf
                    <button class="loc-act loc-toggle">{{ $location->is_active ? 'Deactivate':'Activate' }}</button>
                </form>
                <form method="post" action="/admin/locations/{{ $location->id }}" onsubmit="return confirm('Delete \'{{ addslashes($location->name) }}\'?')" class="d-contents">@csrf @method('DELETE')
                    <button class="loc-act danger">Delete</button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr><td class="empty-state-lg" colspan="7">No locations found.</td></tr>
    @endforelse
    </tbody>
    </table>
    </div>
    <div class="p14-22">{{ $locations->links() }}</div>
</div>

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
// Type pill toggle
document.querySelectorAll('.loc-type-pill').forEach(function(pill){
    pill.addEventListener('click', function(){
        document.querySelectorAll('.loc-type-pill').forEach(function(p){ p.classList.remove('checked'); });
        this.classList.add('checked');
    });
});

// Parent ksd
(function(){
    var trigger  = document.getElementById('locParentTrigger');
    var dropdown = document.getElementById('locParentDropdown');
    var hidden   = document.getElementById('locParentNative');
    var labelEl  = document.getElementById('locParentLabel');
    var searchEl = document.getElementById('locParentSearch');
    var list     = document.getElementById('locParentList');
    var emptyEl  = document.getElementById('locParentEmpty');
    var open = false;
    function getItems(){ return list.querySelectorAll('.ksd-item'); }
    function openD(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); searchEl.value=''; filterItems(''); searchEl.focus(); open=true; }
    function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open ? closeD() : openD(); });
    document.addEventListener('click',function(e){ if(open && !trigger.contains(e.target) && !dropdown.contains(e.target)) closeD(); });
    searchEl.addEventListener('input',function(){ filterItems(this.value.toLowerCase()); });
    list.addEventListener('click',function(e){
        var item=e.target.closest('.ksd-item'); if(!item) return;
        hidden.value=item.dataset.value;
        labelEl.textContent=item.dataset.label;
        labelEl.classList.remove('placeholder');
        trigger.classList.toggle('ksd-has-value', !!item.dataset.value);
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
