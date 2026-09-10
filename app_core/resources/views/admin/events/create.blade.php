@extends('layouts.admin')
@section('title','Create Event')
@section('page','Events')
@section('heading','Create New Event')
@section('subheading','Add a new event to the marketplace')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/events">← Back</a>@endsection

@push('styles')

@endpush

@section('content')
@if($errors->any())
<div class="alert-error">
    <ul class="list-pl">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

@php
$oldPosterType = old('poster_type', 'user');
$oldUserId     = old('user_id', '');
$oldStoreId    = old('store_id', '');
$oldLocId      = old('location', '');
$oldCatId      = old('category_id', '');
$oldStatus     = old('status', 'published');
$oldEventType  = old('event_type', 'offline');
@endphp

<form method="post" action="/admin/events" id="aevForm">
@csrf
<input type="hidden" name="poster_type" id="aevPosterType" value="{{ $oldPosterType }}">

<div class="aev-shell">

{{-- ── LEFT MAIN ──────────────────────────────────────── --}}
<div>

    {{-- Poster --}}
    <div class="aev-card">
        <div class="aev-card-header">
            <div class="aev-card-icon blue">👤</div>
            <div>
                <p class="aev-card-title">Posted By</p>
                <p class="aev-card-sub">Registered user or independent organizer</p>
            </div>
        </div>

        <div class="aev-poster-toggle">
            <button type="button" class="aev-poster-btn {{ $oldPosterType==='user' ? 'active' : '' }}" id="aevBtnUser">
                👤 Registered User
            </button>
            <button type="button" class="aev-poster-btn {{ $oldPosterType==='independent' ? 'active' : '' }}" id="aevBtnIndep">
                🎤 Independent Organizer
            </button>
        </div>

        {{-- Registered user section --}}
        <div class="aev-poster-section {{ $oldPosterType==='user' ? 'visible' : '' }}" id="aevSectionUser">
            <div class="aev-grid">
                <div class="aev-field">
                    <label class="aev-label">User</label>
                    <input type="hidden" name="user_id" id="aevUserVal" value="{{ $oldUserId }}">
                    <div class="ksd-wrap">
                        <button type="button" class="ksd-trigger {{ $oldUserId ? 'ksd-has-value' : '' }}" id="aevUserTrigger">
                            <span class="ksd-trigger-text {{ $oldUserId ? '' : 'placeholder' }}" id="aevUserLabel">Select user…</span>
                            <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div class="ksd-dropdown" id="aevUserDropdown">
                            <div class="ksd-search-row"><input type="text" class="ksd-search" id="aevUserSearch" placeholder="Search user…" autocomplete="off"></div>
                            <div class="ksd-list" id="aevUserList">
                                <div class="ksd-item {{ !$oldUserId ? 'ksd-selected' : '' }}" data-value="" data-label="No User" data-search="">— No User —</div>
                                @foreach($users as $user)
                                <div class="ksd-item {{ $oldUserId==$user->id ? 'ksd-selected' : '' }}"
                                     data-value="{{ $user->id }}"
                                     data-label="{{ $user->name }}"
                                     data-search="{{ strtolower($user->name.' '.$user->email) }}">{{ $user->name }} — {{ $user->email }}</div>
                                @endforeach
                                <div class="ksd-empty" id="aevUserEmpty" class="hidden">No users match</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="aev-field">
                    <label class="aev-label">Store</label>
                    <input type="hidden" name="store_id" id="aevStoreVal" value="{{ $oldStoreId }}">
                    <div class="ksd-wrap">
                        <button type="button" class="ksd-trigger {{ $oldStoreId ? 'ksd-has-value' : '' }}" id="aevStoreTrigger">
                            <span class="ksd-trigger-text {{ $oldStoreId ? '' : 'placeholder' }}" id="aevStoreLabel">No Store</span>
                            <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div class="ksd-dropdown" id="aevStoreDropdown">
                            <div class="ksd-search-row"><input type="text" class="ksd-search" id="aevStoreSearch" placeholder="Search store…" autocomplete="off"></div>
                            <div class="ksd-list" id="aevStoreList">
                                <div class="ksd-empty" id="aevStoreEmpty">Select a user first…</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Independent organizer section --}}
        <div class="aev-poster-section {{ $oldPosterType==='independent' ? 'visible' : '' }}" id="aevSectionIndep">
            <div class="aev-grid">
                <div class="aev-field">
                    <label class="aev-label">Organizer Name <span class="aev-req">*</span></label>
                    <input name="organizer_name" class="aev-input" value="{{ old('organizer_name') }}" placeholder="e.g. Kegalle Arts Club" autocomplete="off">
                </div>
                <div class="aev-field">
                    <label class="aev-label">Phone</label>
                    <input name="organizer_phone" class="aev-input" value="{{ old('organizer_phone') }}" placeholder="+94 77 000 0000">
                </div>
                <div class="aev-field aev-full">
                    <label class="aev-label">Email</label>
                    <input type="email" name="organizer_email" class="aev-input" value="{{ old('organizer_email') }}" placeholder="organizer@example.com">
                </div>
            </div>
        </div>
    </div>

    {{-- Event Details --}}
    <div class="aev-card">
        <div class="aev-card-header">
            <div class="aev-card-icon green">📝</div>
            <div>
                <p class="aev-card-title">Event Details</p>
                <p class="aev-card-sub">Title and description</p>
            </div>
        </div>
        <div class="aev-field aev-full" class="mb-14">
            <label class="aev-label">Event Title <span class="aev-req">*</span></label>
            <input name="title" required class="aev-input" value="{{ old('title') }}" placeholder="e.g. Kegalle Music Festival 2025">
        </div>
        <div class="aev-field aev-full">
            <label class="aev-label">Description</label>
            <textarea name="description" class="aev-textarea min-h-110" placeholder="Describe the event in detail…">{{ old('description') }}</textarea>
        </div>
    </div>

    {{-- Schedule --}}
    <div class="aev-card">
        <div class="aev-card-header">
            <div class="aev-card-icon purple">📅</div>
            <div>
                <p class="aev-card-title">Schedule</p>
                <p class="aev-card-sub">Date, time and format</p>
            </div>
        </div>
        <div class="aev-grid">
            <div class="aev-field">
                <label class="aev-label">Event Date <span class="aev-req">*</span></label>
                <input type="date" name="event_date" required class="aev-input" value="{{ old('event_date') }}">
            </div>
            <div class="aev-field">
                <label class="aev-label">Event Type</label>
                <input type="hidden" name="event_type" id="aevEtypeVal" value="{{ $oldEventType }}">
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger ksd-has-value" id="aevEtypeTrigger">
                        <span class="ksd-trigger-text" id="aevEtypeLabel">{{ ['offline'=>'Offline (In-Person)','online'=>'Online','hybrid'=>'Hybrid'][$oldEventType] ?? 'Offline (In-Person)' }}</span>
                        <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="aevEtypeDropdown">
                        <div class="ksd-list">
                            <div class="ksd-item {{ $oldEventType==='offline' ? 'ksd-selected':'' }}" data-value="offline" data-label="Offline (In-Person)">🏟️ Offline (In-Person)</div>
                            <div class="ksd-item {{ $oldEventType==='online' ? 'ksd-selected':'' }}" data-value="online" data-label="Online">💻 Online</div>
                            <div class="ksd-item {{ $oldEventType==='hybrid' ? 'ksd-selected':'' }}" data-value="hybrid" data-label="Hybrid">🔀 Hybrid</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="aev-field">
                <label class="aev-label">Start Time</label>
                <input type="datetime-local" name="starts_at" class="aev-input" value="{{ old('starts_at') }}">
            </div>
            <div class="aev-field">
                <label class="aev-label">End Time</label>
                <input type="datetime-local" name="ends_at" class="aev-input" value="{{ old('ends_at') }}">
            </div>
        </div>
    </div>

    {{-- Venue & Location --}}
    <div class="aev-card">
        <div class="aev-card-header">
            <div class="aev-card-icon amber">📍</div>
            <div>
                <p class="aev-card-title">Venue &amp; Location</p>
                <p class="aev-card-sub">Where the event takes place</p>
            </div>
        </div>
        <div class="aev-grid">
            <div class="aev-field">
                <label class="aev-label">Venue <span class="aev-req">*</span></label>
                <input name="venue" class="aev-input" value="{{ old('venue') }}" placeholder="e.g. Kegalle City Ground">
            </div>
            <div class="aev-field">
                <label class="aev-label">Location <span class="aev-req">*</span></label>
                <input type="hidden" name="location" id="aevLocVal" value="{{ $oldLocId }}" required>
                <div class="ksd-wrap">
                    <button type="button" class="ksd-trigger {{ $oldLocId ? 'ksd-has-value' : '' }}" id="aevLocTrigger">
                        <span class="ksd-trigger-text {{ $oldLocId ? '' : 'placeholder' }}" id="aevLocLabel">— Select Location —</span>
                        <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div class="ksd-dropdown" id="aevLocDropdown">
                        <div class="ksd-search-row"><input type="text" class="ksd-search" id="aevLocSearch" placeholder="Type to search…" autocomplete="off"></div>
                        <div class="ksd-list" id="aevLocList">
                            @foreach($locations->whereNull('parent_id') as $parentLoc)
                                <div class="ksd-group-label" data-group="{{ $parentLoc->id }}">{{ strtoupper($parentLoc->name) }}</div>
                                <div class="ksd-item ksd-indent {{ $oldLocId===$parentLoc->name ? 'ksd-selected':'' }}"
                                     data-value="{{ $parentLoc->name }}"
                                     data-label="{{ $parentLoc->name }} (All)"
                                     data-search="{{ strtolower($parentLoc->name) }}"
                                     data-group="{{ $parentLoc->id }}">📍 {{ $parentLoc->name }} (All)</div>
                                @foreach($locations->where('parent_id',$parentLoc->id) as $loc)
                                <div class="ksd-item ksd-indent {{ $oldLocId===$loc->name ? 'ksd-selected':'' }}"
                                     data-value="{{ $loc->name }}"
                                     data-label="{{ $parentLoc->name }} — {{ $loc->name }}"
                                     data-search="{{ strtolower($parentLoc->name.' '.$loc->name) }}"
                                     data-group="{{ $parentLoc->id }}">{{ $loc->name }}</div>
                                @endforeach
                            @endforeach
                            <div class="ksd-empty" id="aevLocEmpty" class="hidden">No locations match</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pricing --}}
    <div class="aev-card">
        <div class="aev-card-header">
            <div class="aev-card-icon rose">💰</div>
            <div>
                <p class="aev-card-title">Pricing &amp; Capacity</p>
                <p class="aev-card-sub">Ticket price and attendee limit</p>
            </div>
        </div>
        <div class="aev-grid">
            <div class="aev-field">
                <label class="aev-label">Price (LKR)</label>
                <input type="number" name="price" step="1" min="0" class="aev-input" value="{{ old('price', 0) }}" placeholder="0">
            </div>
            <div class="aev-field">
                <label class="aev-label">Capacity</label>
                <input type="number" name="capacity" class="aev-input" value="{{ old('capacity') }}" placeholder="Max attendees">
            </div>
        </div>
        <div class="aev-checks" class="mt-14">
            <label class="aev-check">
                <input type="checkbox" name="is_free" value="1" @checked(old('is_free'))>
                🎟️ Free Event
            </label>
        </div>
    </div>

</div>

{{-- ── RIGHT SIDEBAR ──────────────────────────────────── --}}
<div>

    {{-- Publish --}}
    <div class="aev-card">
        <div class="aev-card-header">
            <div class="aev-card-icon green">🚀</div>
            <div>
                <p class="aev-card-title">Publish</p>
                <p class="aev-card-sub">Save event to marketplace</p>
            </div>
        </div>
        <button type="submit" class="aev-publish-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
            Create Event
        </button>
        <a href="/admin/events" class="aev-cancel-btn">Cancel</a>
    </div>

    {{-- Status --}}
    <div class="aev-card">
        <div class="aev-card-header">
            <div class="aev-card-icon slate">⚙️</div>
            <div>
                <p class="aev-card-title">Status</p>
                <p class="aev-card-sub">Publication state</p>
            </div>
        </div>
        <div class="aev-field">
            <input type="hidden" name="status" id="aevStatusVal" value="{{ $oldStatus }}">
            <div class="ksd-wrap">
                <button type="button" class="ksd-trigger ksd-has-value" id="aevStatusTrigger">
                    <span class="ksd-trigger-text" id="aevStatusLabel">{{ ['published'=>'✅ Published','pending'=>'⏳ Pending','draft'=>'📝 Draft'][$oldStatus] ?? '✅ Published' }}</span>
                    <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="ksd-dropdown" id="aevStatusDropdown">
                    <div class="ksd-list">
                        <div class="ksd-item {{ $oldStatus==='published' ? 'ksd-selected':'' }}" data-value="published" data-label="✅ Published">✅ Published</div>
                        <div class="ksd-item {{ $oldStatus==='pending' ? 'ksd-selected':'' }}" data-value="pending" data-label="⏳ Pending">⏳ Pending</div>
                        <div class="ksd-item {{ $oldStatus==='draft' ? 'ksd-selected':'' }}" data-value="draft" data-label="📝 Draft">📝 Draft</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Category --}}
    <div class="aev-card">
        <div class="aev-card-header">
            <div class="aev-card-icon purple">📂</div>
            <div>
                <p class="aev-card-title">Category</p>
                <p class="aev-card-sub">Event classification</p>
            </div>
        </div>
        <div class="aev-field">
            <input type="hidden" name="category_id" id="aevCatVal" value="{{ $oldCatId }}">
            <div class="ksd-wrap">
                <button type="button" class="ksd-trigger {{ $oldCatId ? 'ksd-has-value' : '' }}" id="aevCatTrigger">
                    <span class="ksd-trigger-text {{ $oldCatId ? '' : 'placeholder' }}" id="aevCatLabel">No Category</span>
                    <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="ksd-dropdown" id="aevCatDropdown">
                    <div class="ksd-search-row"><input type="text" class="ksd-search" id="aevCatSearch" placeholder="Search…" autocomplete="off"></div>
                    <div class="ksd-list" id="aevCatList">
                        <div class="ksd-item {{ !$oldCatId ? 'ksd-selected':'' }}" data-value="" data-label="No Category" data-search="">— No Category —</div>
                        @foreach($categories as $cat)
                        <div class="ksd-item {{ $oldCatId==$cat->id ? 'ksd-selected':'' }}"
                             data-value="{{ $cat->id }}"
                             data-label="{{ $cat->name }}"
                             data-search="{{ strtolower($cat->name) }}">{{ $cat->icon ?? '📦' }} {{ $cat->name }}</div>
                        @endforeach
                        <div class="ksd-empty" id="aevCatEmpty" class="hidden">No categories match</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Boost --}}
    <div class="aev-card">
        <div class="aev-card-header">
            <div class="aev-card-icon amber">⭐</div>
            <div>
                <p class="aev-card-title">Boost</p>
                <p class="aev-card-sub">Visibility options</p>
            </div>
        </div>
        <div class="aev-checks">
            <label class="aev-check">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured'))>
                ⭐ Featured Event
            </label>
        </div>
    </div>

    {{-- Admin Note --}}
    <div class="aev-card">
        <div class="aev-card-header">
            <div class="aev-card-icon slate">📋</div>
            <div>
                <p class="aev-card-title">Admin Note</p>
                <p class="aev-card-sub">Internal only</p>
            </div>
        </div>
        <div class="aev-field">
            <input name="admin_note" class="aev-input" value="{{ old('admin_note') }}" placeholder="Internal note…">
        </div>
    </div>

</div>
</div>
</form>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
// ── Poster type toggle ──────────────────────────────────────────────────────
document.getElementById('aevBtnUser').addEventListener('click', function(){ setPosterType('user'); });
document.getElementById('aevBtnIndep').addEventListener('click', function(){ setPosterType('independent'); });
function setPosterType(type){
    document.getElementById('aevPosterType').value=type;
    document.getElementById('aevBtnUser').classList.toggle('active',type==='user');
    document.getElementById('aevBtnIndep').classList.toggle('active',type==='independent');
    document.getElementById('aevSectionUser').classList.toggle('visible',type==='user');
    document.getElementById('aevSectionIndep').classList.toggle('visible',type==='independent');
}

// ── Generic ksd factory ─────────────────────────────────────────────────────
function makeKsd(cfg){
    // cfg: {triggerId, dropdownId, searchId, listId, emptyId, hiddenId, labelId, groups, onSelect}
    var trigger=document.getElementById(cfg.triggerId);
    var dropdown=document.getElementById(cfg.dropdownId);
    var hidden=document.getElementById(cfg.hiddenId);
    var label=document.getElementById(cfg.labelId);
    var emptyEl=cfg.emptyId?document.getElementById(cfg.emptyId):null;
    var open=false;
    if(!trigger) return;

    var searchEl=cfg.searchId?document.getElementById(cfg.searchId):null;
    var list=document.getElementById(cfg.listId);
    var items=list.querySelectorAll('.ksd-item');
    var groups=list.querySelectorAll('.ksd-group-label');

    function openD(){
        dropdown.style.display='block'; trigger.classList.add('ksd-open');
        if(searchEl){searchEl.value='';filterItems('');searchEl.focus();}
        open=true;
    }
    function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    if(searchEl) searchEl.addEventListener('input',function(){ filterItems(this.value.toLowerCase()); });

    items.forEach(function(item){
        item.addEventListener('click',function(){
            var val=this.dataset.value, lbl=this.dataset.label||this.textContent.trim();
            hidden.value=val;
            label.textContent=lbl; label.classList.toggle('placeholder',!val);
            trigger.classList.toggle('ksd-has-value',!!(val));
            items.forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected');
            closeD();
            if(cfg.onSelect) cfg.onSelect(val, lbl);
        });
    });

    function filterItems(q){
        var vis={};
        items.forEach(function(i){
            var m=!q||(i.dataset.search||'').indexOf(q)!==-1||(i.dataset.label||'').toLowerCase().indexOf(q)!==-1;
            i.style.display=m?'':'none';
            if(m&&i.dataset.group) vis[i.dataset.group]=true;
        });
        groups.forEach(function(g){ g.style.display=(!q||vis[g.dataset.group])?'':'none'; });
        if(emptyEl) emptyEl.style.display=Array.from(items).some(function(i){return i.style.display!=='none';})?'none':'';
    }

    // pre-fill label
    var cur=hidden.value;
    if(cur){
        var pre=Array.from(items).find(function(i){return i.dataset.value===cur;});
        if(pre){label.textContent=pre.dataset.label||pre.textContent.trim();label.classList.remove('placeholder');trigger.classList.add('ksd-has-value');}
    }
    return {renderItems:function(newItems, emptyText){
        list.querySelectorAll('.ksd-item').forEach(function(i){i.remove();});
        hidden.value=''; label.textContent=cfg.placeholder||'Select…'; label.classList.add('placeholder'); trigger.classList.remove('ksd-has-value');
        if(!newItems.length){ if(emptyEl){emptyEl.textContent=emptyText||'No options';emptyEl.style.display='';} return; }
        if(emptyEl){emptyEl.textContent='No match';emptyEl.style.display='none';}
        newItems.forEach(function(s){
            var item=document.createElement('div'); item.className='ksd-item';
            item.dataset.value=String(s.id); item.dataset.label=s.name; item.dataset.search=s.name.toLowerCase();
            item.textContent=s.name;
            item.addEventListener('click',function(){
                hidden.value=this.dataset.value; label.textContent=this.dataset.label;
                label.classList.remove('placeholder'); trigger.classList.add('ksd-has-value');
                list.querySelectorAll('.ksd-item').forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected');
                closeD(); if(cfg.onSelect) cfg.onSelect(this.dataset.value, this.dataset.label);
            });
            list.insertBefore(item,emptyEl);
        });
        if(newItems.length===1){
            hidden.value=String(newItems[0].id); label.textContent=newItems[0].name;
            label.classList.remove('placeholder'); trigger.classList.add('ksd-has-value');
            list.querySelector('.ksd-item').classList.add('ksd-selected');
        }
    }};
}

// ── Init all ksd pickers ────────────────────────────────────────────────────
var allStores = @json($stores->map(fn($s)=>['id'=>$s->id,'name'=>$s->name,'user_id'=>$s->user_id]));

// Simple ksd for static lists (no search)
function makeSimpleKsd(triggerId, dropdownId, listId, hiddenId, labelId){
    var trigger=document.getElementById(triggerId), dropdown=document.getElementById(dropdownId);
    var hidden=document.getElementById(hiddenId), label=document.getElementById(labelId);
    var list=document.getElementById(listId), open=false;
    var items=list.querySelectorAll('.ksd-item');
    function openD(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); open=true; }
    function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    items.forEach(function(item){
        item.addEventListener('click',function(){
            hidden.value=this.dataset.value; label.textContent=this.dataset.label||this.textContent.trim();
            label.classList.remove('placeholder'); trigger.classList.add('ksd-has-value');
            items.forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected');
            closeD();
        });
    });
}

makeSimpleKsd('aevEtypeTrigger','aevEtypeDropdown','aevEtypeDropdown .ksd-list','aevEtypeVal','aevEtypeLabel');
// Fix: list is inside dropdown, need separate id
(function(){
    var trigger=document.getElementById('aevEtypeTrigger'), dropdown=document.getElementById('aevEtypeDropdown');
    var hidden=document.getElementById('aevEtypeVal'), label=document.getElementById('aevEtypeLabel');
    var items=dropdown.querySelectorAll('.ksd-item'), open=false;
    function openD(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); open=true; }
    function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    items.forEach(function(item){
        item.addEventListener('click',function(){
            hidden.value=this.dataset.value; label.textContent=this.dataset.label||this.textContent.trim();
            label.classList.remove('placeholder'); trigger.classList.add('ksd-has-value');
            items.forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected');
            closeD();
        });
    });
})();

(function(){
    var trigger=document.getElementById('aevStatusTrigger'), dropdown=document.getElementById('aevStatusDropdown');
    var hidden=document.getElementById('aevStatusVal'), label=document.getElementById('aevStatusLabel');
    var items=dropdown.querySelectorAll('.ksd-item'), open=false;
    function openD(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); open=true; }
    function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    items.forEach(function(item){
        item.addEventListener('click',function(){
            hidden.value=this.dataset.value; label.textContent=this.dataset.label||this.textContent.trim();
            label.classList.remove('placeholder'); trigger.classList.add('ksd-has-value');
            items.forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected');
            closeD();
        });
    });
})();

// Location ksd
(function(){
    var trigger=document.getElementById('aevLocTrigger'), dropdown=document.getElementById('aevLocDropdown');
    var search=document.getElementById('aevLocSearch'), list=document.getElementById('aevLocList');
    var hidden=document.getElementById('aevLocVal'), label=document.getElementById('aevLocLabel');
    var emptyEl=document.getElementById('aevLocEmpty');
    var items=list.querySelectorAll('.ksd-item'), groups=list.querySelectorAll('.ksd-group-label');
    var open=false;
    function openD(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); search.value=''; filterItems(''); search.focus(); open=true; }
    function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    search.addEventListener('input',function(){ filterItems(this.value.toLowerCase()); });
    items.forEach(function(item){
        item.addEventListener('click',function(){
            var val=this.dataset.value, lbl=this.dataset.label||this.textContent.trim();
            hidden.value=val; label.textContent=val?lbl:'— Select Location —';
            label.classList.toggle('placeholder',!val); trigger.classList.toggle('ksd-has-value',!!val);
            items.forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected');
            closeD();
        });
    });
    function filterItems(q){
        var vis={};
        items.forEach(function(i){ var m=!q||(i.dataset.search||'').indexOf(q)!==-1; i.style.display=m?'':'none'; if(m&&i.dataset.group)vis[i.dataset.group]=true; });
        groups.forEach(function(g){ g.style.display=(!q||vis[g.dataset.group])?'':'none'; });
        if(emptyEl) emptyEl.style.display=Array.from(items).some(function(i){return i.style.display!=='none';})?'none':'';
    }
    var cur=hidden.value;
    if(cur){ var pre=Array.from(items).find(function(i){return i.dataset.value===cur;}); if(pre){label.textContent=pre.dataset.label;label.classList.remove('placeholder');trigger.classList.add('ksd-has-value');} }
})();

// Category ksd
(function(){
    var trigger=document.getElementById('aevCatTrigger'), dropdown=document.getElementById('aevCatDropdown');
    var search=document.getElementById('aevCatSearch'), list=document.getElementById('aevCatList');
    var hidden=document.getElementById('aevCatVal'), label=document.getElementById('aevCatLabel');
    var emptyEl=document.getElementById('aevCatEmpty');
    var items=list.querySelectorAll('.ksd-item'), open=false;
    function openD(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); search.value=''; filterItems(''); search.focus(); open=true; }
    function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    search.addEventListener('input',function(){ filterItems(this.value.toLowerCase()); });
    items.forEach(function(item){
        item.addEventListener('click',function(){
            var val=this.dataset.value, lbl=this.dataset.label||this.textContent.trim();
            hidden.value=val; label.textContent=lbl;
            label.classList.toggle('placeholder',!val); trigger.classList.toggle('ksd-has-value',!!val);
            items.forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected');
            closeD();
        });
    });
    function filterItems(q){
        var any=false;
        items.forEach(function(i){ var m=!q||(i.dataset.search||'').indexOf(q)!==-1; i.style.display=m?'':'none'; if(m)any=true; });
        if(emptyEl) emptyEl.style.display=any?'none':'';
    }
    var cur=hidden.value;
    if(cur){ var pre=Array.from(items).find(function(i){return i.dataset.value===cur;}); if(pre){label.textContent=pre.dataset.label;label.classList.remove('placeholder');trigger.classList.add('ksd-has-value');} }
})();

// User ksd with store filtering
(function(){
    var trigger=document.getElementById('aevUserTrigger'), dropdown=document.getElementById('aevUserDropdown');
    var search=document.getElementById('aevUserSearch'), list=document.getElementById('aevUserList');
    var hidden=document.getElementById('aevUserVal'), label=document.getElementById('aevUserLabel');
    var emptyEl=document.getElementById('aevUserEmpty');
    var items=list.querySelectorAll('.ksd-item'), open=false;
    function openD(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); search.value=''; filterUser(''); search.focus(); open=true; }
    function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    search.addEventListener('input',function(){ filterUser(this.value.toLowerCase()); });
    items.forEach(function(item){
        item.addEventListener('click',function(){
            var val=this.dataset.value, lbl=this.dataset.label||this.textContent.trim();
            hidden.value=val; label.textContent=val?lbl:'Select user…';
            label.classList.toggle('placeholder',!val); trigger.classList.toggle('ksd-has-value',!!val);
            items.forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected');
            closeD(); renderStores(val);
        });
    });
    function filterUser(q){
        var any=false;
        items.forEach(function(i){ var m=!q||(i.dataset.search||'').indexOf(q)!==-1; i.style.display=m?'':'none'; if(m)any=true; });
        if(emptyEl) emptyEl.style.display=any?'none':'';
    }
    var initUser=hidden.value;
    if(initUser){ var pre=Array.from(items).find(function(i){return i.dataset.value===initUser;}); if(pre){label.textContent=pre.dataset.label;label.classList.remove('placeholder');trigger.classList.add('ksd-has-value');} }

    // Store ksd
    var stTrigger=document.getElementById('aevStoreTrigger'), stDropdown=document.getElementById('aevStoreDropdown');
    var stSearch=document.getElementById('aevStoreSearch'), stList=document.getElementById('aevStoreList');
    var stHidden=document.getElementById('aevStoreVal'), stLabel=document.getElementById('aevStoreLabel');
    var stEmpty=document.getElementById('aevStoreEmpty'), stOpen=false;
    function openSt(){ stDropdown.style.display='block'; stTrigger.classList.add('ksd-open'); stSearch.value=''; filterStore(''); stSearch.focus(); stOpen=true; }
    function closeSt(){ stDropdown.style.display='none'; stTrigger.classList.remove('ksd-open'); stOpen=false; }
    stTrigger.addEventListener('click',function(e){ e.stopPropagation(); stOpen?closeSt():openSt(); });
    document.addEventListener('click',function(e){ if(stOpen&&!stTrigger.contains(e.target)&&!stDropdown.contains(e.target)) closeSt(); });
    stSearch.addEventListener('input',function(){ filterStore(this.value.toLowerCase()); });
    stList.addEventListener('click',function(e){
        var item=e.target.closest('.ksd-item'); if(!item||item===stEmpty) return;
        var val=item.dataset.value, lbl=item.dataset.label||item.textContent.trim();
        stHidden.value=val; stLabel.textContent=val?lbl:'No Store';
        stLabel.classList.toggle('placeholder',!val); stTrigger.classList.toggle('ksd-has-value',!!val);
        stList.querySelectorAll('.ksd-item').forEach(function(i){i.classList.remove('ksd-selected');}); item.classList.add('ksd-selected');
        closeSt();
    });
    function filterStore(q){
        var stItems=stList.querySelectorAll('.ksd-item'); var any=false;
        stItems.forEach(function(i){ var m=!q||(i.dataset.search||'').indexOf(q)!==-1; i.style.display=m?'':'none'; if(m)any=true; });
        stEmpty.style.display=any?'none':'';
    }
    function renderStores(userId){
        stList.querySelectorAll('.ksd-item').forEach(function(i){i.remove();});
        stHidden.value=''; stLabel.textContent='No Store'; stLabel.classList.add('placeholder'); stTrigger.classList.remove('ksd-has-value');
        var filtered=userId?allStores.filter(function(s){return String(s.user_id)===String(userId);}):[];
        if(!filtered.length){ stEmpty.textContent=userId?'No stores for this user':'Select a user first…'; stEmpty.style.display=''; return; }
        stEmpty.textContent='No stores match'; stEmpty.style.display='none';
        if(filtered.length===1){
            stHidden.value=String(filtered[0].id); stLabel.textContent=filtered[0].name;
            stLabel.classList.remove('placeholder'); stTrigger.classList.add('ksd-has-value');
        }
        var none=document.createElement('div'); none.className='ksd-item'+(filtered.length!==1?' ksd-selected':'');
        none.dataset.value=''; none.dataset.label='No Store'; none.dataset.search=''; none.textContent='— No Store —';
        stList.insertBefore(none,stEmpty);
        filtered.forEach(function(s){
            var item=document.createElement('div'); item.className='ksd-item'+(filtered.length===1&&String(s.id)===stHidden.value?' ksd-selected':'');
            item.dataset.value=String(s.id); item.dataset.label=s.name; item.dataset.search=s.name.toLowerCase(); item.textContent=s.name;
            stList.insertBefore(item,stEmpty);
        });
    }
    if(initUser) renderStores(initUser);
})();
</script>
@endpush
