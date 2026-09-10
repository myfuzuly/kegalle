@extends('layouts.admin')
@section('title','Create Deal')
@section('page','Deals')
@section('heading','Create Deal')
@section('subheading','Post a deal on behalf of a registered seller or an independent advertiser')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/deals">← Back</a>@endsection

@push('styles')

@endpush

@section('content')
@if($errors->any())
<div class="alert-error">
    <ul class="list-pl">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

@php
$oldPosterType = old('poster_type','user');
$oldUserId  = old('user_id','');
$oldStoreId = old('store_id','');
$oldListingId = old('listing_id','');
$oldStatus  = old('status','approved');
@endphp

{{-- Preload data for JS --}}
@php
$adcStoresJson = $stores->map(fn($s)=>['id'=>$s->id,'name'=>$s->name,'user_id'=>$s->user_id]);
$adcListingsJson = $listings->map(fn($l)=>['id'=>$l->id,'title'=>$l->title,'price'=>(float)$l->price,'cat'=>optional($l->category)->name??'']);
@endphp
<script nonce="{{ $cspNonce ?? '' }}">
var adcAllStores = @json($adcStoresJson);
var adcAllListings = @json($adcListingsJson);
</script>

<form method="post" action="/admin/deals" id="adcForm">
@csrf
<input type="hidden" name="poster_type" id="adcPosterType" value="{{ $oldPosterType }}">

<div class="adc-shell">

{{-- ── LEFT MAIN ────────────────────────────────────────── --}}
<div>

    {{-- Posted By --}}
    <div class="adc-card">
        <div class="adc-card-header">
            <div class="adc-card-icon purple">👤</div>
            <div>
                <p class="adc-card-title">Posted By</p>
                <p class="adc-card-sub">Seller or independent advertiser</p>
            </div>
        </div>

        <div class="adc-poster-toggle">
            <button type="button" class="adc-poster-btn {{ $oldPosterType==='user' ? 'active':'' }}" id="adcBtnUser" data-poster-type="user">👤 Registered Seller</button>
            <button type="button" class="adc-poster-btn {{ $oldPosterType==='independent' ? 'active':'' }}" id="adcBtnIndep" data-poster-type="independent">🎤 Independent Advertiser</button>
        </div>

        {{-- Registered user section --}}
        <div class="adc-poster-section {{ $oldPosterType==='user' ? 'visible':'' }}" id="adcSectionUser">
            <div class="adc-grid">
                <div class="adc-field">
                    <label class="adc-label">User</label>
                    <input type="hidden" name="user_id" id="adcUserVal" value="{{ $oldUserId }}">
                    <div class="ksd-wrap">
                        <button type="button" class="ksd-trigger {{ $oldUserId ? 'ksd-has-value':'' }}" id="adcUserTrigger">
                            <span class="ksd-trigger-text {{ $oldUserId ? '':'placeholder' }}" id="adcUserLabel">Select user…</span>
                            <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div class="ksd-dropdown" id="adcUserDropdown">
                            <div class="ksd-search-row"><input type="text" class="ksd-search" id="adcUserSearch" placeholder="Search user…" autocomplete="off"></div>
                            <div class="ksd-list" id="adcUserList">
                                <div class="ksd-item" data-value="" data-label="No User" data-search="">— No User —</div>
                                @foreach($users as $user)
                                <div class="ksd-item {{ $oldUserId==$user->id ? 'ksd-selected':'' }}"
                                     data-value="{{ $user->id }}" data-label="{{ $user->name }}"
                                     data-search="{{ strtolower($user->name.' '.$user->email) }}">{{ $user->name }} — {{ $user->email }}</div>
                                @endforeach
                                <div class="ksd-empty" id="adcUserEmpty" class="hidden">No users match</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="adc-field">
                    <label class="adc-label">Store</label>
                    <input type="hidden" name="store_id" id="adcStoreVal" value="{{ $oldStoreId }}">
                    <div class="ksd-wrap">
                        <button type="button" class="ksd-trigger {{ $oldStoreId ? 'ksd-has-value':'' }}" id="adcStoreTrigger">
                            <span class="ksd-trigger-text {{ $oldStoreId ? '':'placeholder' }}" id="adcStoreLabel">Select user first…</span>
                            <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div class="ksd-dropdown" id="adcStoreDropdown">
                            <div class="ksd-search-row"><input type="text" class="ksd-search" id="adcStoreSearch" placeholder="Search store…" autocomplete="off"></div>
                            <div class="ksd-list" id="adcStoreList">
                                <div class="ksd-empty" id="adcStoreEmpty" class="hidden">No stores match</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Independent section --}}
        <div class="adc-poster-section {{ $oldPosterType==='independent' ? 'visible':'' }}" id="adcSectionIndep">
            <div class="adc-grid">
                <div class="adc-field adc-full">
                    <label class="adc-label">Advertiser Name <span class="adc-req">*</span></label>
                    <input name="organizer_name" class="adc-input" value="{{ old('organizer_name') }}" placeholder="Full name or business name">
                </div>
                <div class="adc-field">
                    <label class="adc-label">Phone</label>
                    <input name="organizer_phone" class="adc-input" value="{{ old('organizer_phone') }}" placeholder="077 1234567">
                </div>
                <div class="adc-field">
                    <label class="adc-label">Email</label>
                    <input type="email" name="organizer_email" class="adc-input" value="{{ old('organizer_email') }}" placeholder="email@example.com">
                </div>
            </div>
        </div>
    </div>

    {{-- Product --}}
    <div class="adc-card">
        <div class="adc-card-header">
            <div class="adc-card-icon blue">🛍️</div>
            <div>
                <p class="adc-card-title">Product</p>
                <p class="adc-card-sub">Link a listing or enter a manual title</p>
            </div>
        </div>
        <div class="adc-field" class="mb-14">
            <label class="adc-label">Link to Existing Listing <span class="fs-115-hint">(optional — fills original price automatically)</span></label>
            <input type="hidden" name="listing_id" id="adcListingVal" value="{{ $oldListingId }}">
            <div class="ksd-wrap">
                <button type="button" class="ksd-trigger {{ $oldListingId ? 'ksd-has-value':'' }}" id="adcListingTrigger">
                    <span class="ksd-trigger-text {{ $oldListingId ? '':'placeholder' }}" id="adcListingLabel">Search listings…</span>
                    <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="ksd-dropdown" id="adcListingDropdown">
                    <div class="ksd-search-row"><input type="text" class="ksd-search" id="adcListingSearch" placeholder="Type to search listings…" autocomplete="off"></div>
                    <div class="ksd-list" id="adcListingList">
                        <div class="ksd-item" data-value="" data-label="" data-price="0" data-search="">— No Listing (manual) —</div>
                        @foreach($listings as $l)
                        <div class="ksd-item {{ $oldListingId==$l->id ? 'ksd-selected':'' }}"
                             data-value="{{ $l->id }}"
                             data-label="{{ $l->title }}"
                             data-price="{{ (float)$l->price }}"
                             data-search="{{ strtolower($l->title.' '.optional($l->category)->name) }}">
                            {{ $l->title }}@if($l->category) <span class="muted-fs115"> · {{ $l->category->name }}</span>@endif
                            <span class="green-fw7-ml4">LKR {{ number_format($l->price) }}</span>
                        </div>
                        @endforeach
                        <div class="ksd-empty" id="adcListingEmpty" class="hidden">No listings match</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="adc-grid">
            <div class="adc-field adc-full">
                <label class="adc-label">Manual Product Title <span class="fs-115-hint">(if no listing linked)</span></label>
                <input name="title" class="adc-input" id="adcTitleInput" value="{{ old('title') }}" placeholder="e.g. iPhone 14 Pro Max 256GB Space Black">
            </div>
            <div class="adc-field adc-full">
                <textarea name="description" class="adc-textarea" placeholder="Optional short description…">{{ old('description') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Deal Pricing --}}
    <div class="adc-card">
        <div class="adc-card-header">
            <div class="adc-card-icon red">💰</div>
            <div>
                <p class="adc-card-title">Deal Pricing</p>
                <p class="adc-card-sub">Original price and discounted deal price</p>
            </div>
        </div>
        <div class="adc-grid" class="mb-14">
            <div class="adc-field">
                <label class="adc-label">Original Price (LKR) <span class="adc-req">*</span></label>
                <input name="original_price" id="adcOrigPrice" type="number" step="1" min="0" class="adc-input" value="{{ old('original_price') }}" placeholder="0">
            </div>
            <div class="adc-field">
                <label class="adc-label">Deal Price (LKR) <span class="adc-req">*</span></label>
                <input name="deal_price" id="adcDealPrice" type="number" step="1" min="1" required class="adc-input" value="{{ old('deal_price') }}" placeholder="0">
            </div>
        </div>
        <div class="flex-row gap-10">
            <div class="adc-discount-badge">
                <span>Discount:</span>
                <span id="adcDiscountPct">—</span>
            </div>
            <span class="adc-hint">Auto-calculated from the two prices above</span>
        </div>
    </div>

    {{-- Schedule --}}
    <div class="adc-card">
        <div class="adc-card-header">
            <div class="adc-card-icon amber">📅</div>
            <div>
                <p class="adc-card-title">Schedule</p>
                <p class="adc-card-sub">When this deal is live</p>
            </div>
        </div>
        <div class="adc-grid-3">
            <div class="adc-field">
                <label class="adc-label">Starts <span class="adc-req">*</span></label>
                <input type="date" name="starts_at" required class="adc-input" value="{{ old('starts_at', date('Y-m-d')) }}">
            </div>
            <div class="adc-field">
                <label class="adc-label">Ends <span class="adc-req">*</span></label>
                <input type="date" name="ends_at" required class="adc-input" value="{{ old('ends_at', date('Y-m-d', strtotime('+7 days'))) }}">
            </div>
            <div class="adc-field">
                <label class="adc-label">Stock Qty</label>
                <input type="number" name="stock_qty" min="1" class="adc-input" value="{{ old('stock_qty') }}" placeholder="Unlimited">
            </div>
        </div>
    </div>

    {{-- Admin Note --}}
    <div class="adc-card">
        <div class="adc-card-header">
            <div class="adc-card-icon slate">🗒️</div>
            <div>
                <p class="adc-card-title">Admin Note</p>
                <p class="adc-card-sub">Internal note (visible to seller)</p>
            </div>
        </div>
        <div class="adc-field">
            <input name="admin_note" class="adc-input" value="{{ old('admin_note') }}" placeholder="Optional note about this deal…">
        </div>
    </div>

</div>

{{-- ── RIGHT SIDEBAR ──────────────────────────────────── --}}
<div>

    {{-- Publish --}}
    <div class="adc-card">
        <div class="adc-card-header">
            <div class="adc-card-icon red">🔥</div>
            <div>
                <p class="adc-card-title">Publish Deal</p>
                <p class="adc-card-sub">Create and go live</p>
            </div>
        </div>
        <button type="submit" class="adc-publish-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
            Create Deal
        </button>
        <a href="/admin/deals" class="adc-cancel-btn">Cancel</a>
    </div>

    {{-- Status --}}
    <div class="adc-card">
        <div class="adc-card-header">
            <div class="adc-card-icon slate">⚙️</div>
            <div>
                <p class="adc-card-title">Status</p>
                <p class="adc-card-sub">Deal approval state</p>
            </div>
        </div>
        <div class="adc-field">
            <input type="hidden" name="status" id="adcStatusVal" value="{{ $oldStatus }}">
            <div class="ksd-wrap">
                <button type="button" class="ksd-trigger ksd-has-value" id="adcStatusTrigger">
                    <span class="ksd-trigger-text" id="adcStatusLabel">
                        @if($oldStatus==='approved') ✅ Approved @elseif($oldStatus==='pending') ⏳ Pending @elseif($oldStatus==='rejected') ❌ Rejected @else 🕛 Expired @endif
                    </span>
                    <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="ksd-dropdown" id="adcStatusDropdown">
                    <div class="ksd-list">
                        <div class="ksd-item {{ $oldStatus==='approved' ? 'ksd-selected':'' }}" data-value="approved" data-label="✅ Approved">✅ Approved</div>
                        <div class="ksd-item {{ $oldStatus==='pending' ? 'ksd-selected':'' }}" data-value="pending" data-label="⏳ Pending">⏳ Pending</div>
                        <div class="ksd-item {{ $oldStatus==='rejected' ? 'ksd-selected':'' }}" data-value="rejected" data-label="❌ Rejected">❌ Rejected</div>
                        <div class="ksd-item {{ $oldStatus==='expired' ? 'ksd-selected':'' }}" data-value="expired" data-label="🕛 Expired">🕛 Expired</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Boost --}}
    <div class="adc-card">
        <div class="adc-card-header">
            <div class="adc-card-icon amber">⚡</div>
            <div>
                <p class="adc-card-title">Boost</p>
                <p class="adc-card-sub">Visibility upgrades</p>
            </div>
        </div>
        <div class="adc-checks">
            <label class="adc-check">
                <input type="checkbox" name="is_flash" value="1" @checked(old('is_flash'))>
                ⚡ Flash Deal
            </label>
            <label class="adc-check">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured'))>
                ⭐ Featured Deal
            </label>
        </div>
    </div>

</div>
</div>
</form>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
// ── Poster type toggle — wired via data-poster-type ────────────────────────
document.querySelectorAll('[data-poster-type]').forEach(function(btn){
    btn.addEventListener('click',function(){ setPosterType(btn.dataset.posterType); });
});
function setPosterType(type) {
    document.getElementById('adcPosterType').value = type;
    document.getElementById('adcBtnUser').classList.toggle('active', type==='user');
    document.getElementById('adcBtnIndep').classList.toggle('active', type==='independent');
    document.getElementById('adcSectionUser').classList.toggle('visible', type==='user');
    document.getElementById('adcSectionIndep').classList.toggle('visible', type==='independent');
}

// ── Generic ksd helper ─────────────────────────────────────────────────────
function makeKsd(cfg) {
    var trigger  = document.getElementById(cfg.triggerId);
    var dropdown = document.getElementById(cfg.dropdownId);
    var hidden   = document.getElementById(cfg.hiddenId);
    var label    = document.getElementById(cfg.labelId);
    var searchEl = cfg.searchId ? document.getElementById(cfg.searchId) : null;
    var list     = document.getElementById(cfg.listId);
    var emptyEl  = cfg.emptyId ? document.getElementById(cfg.emptyId) : null;
    var open = false;

    function getItems() { return list.querySelectorAll('.ksd-item'); }

    function openD() {
        dropdown.style.display = 'block';
        trigger.classList.add('ksd-open');
        if (searchEl) { searchEl.value = ''; filterItems(''); searchEl.focus(); }
        open = true;
    }
    function closeD() { dropdown.style.display = 'none'; trigger.classList.remove('ksd-open'); open = false; }

    trigger.addEventListener('click', function(e) { e.stopPropagation(); open ? closeD() : openD(); });
    document.addEventListener('click', function(e) {
        if (open && !trigger.contains(e.target) && !dropdown.contains(e.target)) closeD();
    });

    if (searchEl) searchEl.addEventListener('input', function() { filterItems(this.value.toLowerCase()); });

    list.addEventListener('click', function(e) {
        var item = e.target.closest('.ksd-item');
        if (!item) return;
        var val = item.dataset.value;
        var lbl = item.dataset.label || item.textContent.trim();
        hidden.value = val;
        label.textContent = lbl;
        label.classList.toggle('placeholder', !val);
        trigger.classList.toggle('ksd-has-value', !!val);
        getItems().forEach(function(i) { i.classList.remove('ksd-selected'); });
        item.classList.add('ksd-selected');
        closeD();
        if (cfg.onSelect) cfg.onSelect(val, item);
    });

    function filterItems(q) {
        var any = false;
        getItems().forEach(function(i) {
            var m = !q || (i.dataset.search || '').indexOf(q) !== -1 || (i.dataset.label || '').toLowerCase().indexOf(q) !== -1;
            i.style.display = m ? '' : 'none';
            if (m) any = true;
        });
        if (emptyEl) emptyEl.style.display = any ? 'none' : '';
    }

    // Pre-select from hidden value
    var cur = hidden.value;
    if (cur) {
        var pre = Array.from(getItems()).find(function(i) { return i.dataset.value === cur; });
        if (pre) { label.textContent = pre.dataset.label || pre.textContent.trim(); label.classList.remove('placeholder'); trigger.classList.add('ksd-has-value'); }
    }

    return { setItems: function(items) {
        list.innerHTML = '';
        items.forEach(function(item) {
            var d = document.createElement('div');
            d.className = 'ksd-item';
            d.dataset.value  = item.value;
            d.dataset.label  = item.label;
            d.dataset.search = item.search || item.label.toLowerCase();
            d.textContent = item.label;
            list.appendChild(d);
        });
        if (emptyEl) list.appendChild(emptyEl);
    }};
}

// ── User ksd ──────────────────────────────────────────────────────────────
var userKsd = makeKsd({
    triggerId: 'adcUserTrigger', dropdownId: 'adcUserDropdown',
    searchId: 'adcUserSearch', listId: 'adcUserList', emptyId: 'adcUserEmpty',
    hiddenId: 'adcUserVal', labelId: 'adcUserLabel',
    onSelect: function(userId) { renderStores(userId); }
});

// ── Store ksd (dynamic) ───────────────────────────────────────────────────
var storeKsd = makeKsd({
    triggerId: 'adcStoreTrigger', dropdownId: 'adcStoreDropdown',
    searchId: 'adcStoreSearch', listId: 'adcStoreList', emptyId: 'adcStoreEmpty',
    hiddenId: 'adcStoreVal', labelId: 'adcStoreLabel',
});

function renderStores(userId) {
    var storeHidden = document.getElementById('adcStoreVal');
    var storeLabel  = document.getElementById('adcStoreLabel');
    var storeTrigger = document.getElementById('adcStoreTrigger');
    var filtered = userId ? adcAllStores.filter(function(s) { return String(s.user_id) === String(userId); }) : adcAllStores;
    storeKsd.setItems(filtered.map(function(s) { return { value: s.id, label: s.name, search: s.name.toLowerCase() }; }));
    storeHidden.value = '';
    storeLabel.textContent = filtered.length ? 'Select store…' : 'No stores for this user';
    storeLabel.classList.add('placeholder');
    storeTrigger.classList.remove('ksd-has-value');
    if (filtered.length === 1) {
        storeHidden.value = String(filtered[0].id);
        storeLabel.textContent = filtered[0].name;
        storeLabel.classList.remove('placeholder');
        storeTrigger.classList.add('ksd-has-value');
    }
}
renderStores('{{ $oldUserId }}');

// ── Listing ksd ───────────────────────────────────────────────────────────
makeKsd({
    triggerId: 'adcListingTrigger', dropdownId: 'adcListingDropdown',
    searchId: 'adcListingSearch', listId: 'adcListingList', emptyId: 'adcListingEmpty',
    hiddenId: 'adcListingVal', labelId: 'adcListingLabel',
    onSelect: function(val, item) {
        var price = parseFloat(item.dataset.price || 0);
        if (price > 0) {
            document.getElementById('adcOrigPrice').value = Math.round(price);
            calcDiscount();
        }
        if (item.dataset.label) {
            var titleInput = document.getElementById('adcTitleInput');
            if (!titleInput.value) titleInput.value = item.dataset.label;
        }
    }
});

// ── Status ksd ────────────────────────────────────────────────────────────
makeKsd({
    triggerId: 'adcStatusTrigger', dropdownId: 'adcStatusDropdown',
    listId: 'adcStatusDropdown', hiddenId: 'adcStatusVal', labelId: 'adcStatusLabel',
});
// Simple (no search) status dropdown
(function(){
    var trigger=document.getElementById('adcStatusTrigger'), dropdown=document.getElementById('adcStatusDropdown');
    var hidden=document.getElementById('adcStatusVal'), label=document.getElementById('adcStatusLabel');
    var open=false;
    function openD(){ dropdown.style.display='block'; trigger.classList.add('ksd-open'); open=true; }
    function closeD(){ dropdown.style.display='none'; trigger.classList.remove('ksd-open'); open=false; }
    trigger.addEventListener('click',function(e){ e.stopPropagation(); open?closeD():openD(); });
    document.addEventListener('click',function(e){ if(open&&!trigger.contains(e.target)&&!dropdown.contains(e.target)) closeD(); });
    dropdown.querySelectorAll('.ksd-item').forEach(function(item){
        item.addEventListener('click',function(){
            hidden.value=this.dataset.value; label.textContent=this.dataset.label||this.textContent.trim();
            dropdown.querySelectorAll('.ksd-item').forEach(function(i){i.classList.remove('ksd-selected');}); this.classList.add('ksd-selected'); closeD();
        });
    });
})();

// ── Discount calculator ───────────────────────────────────────────────────
function calcDiscount() {
    var orig = parseFloat(document.getElementById('adcOrigPrice').value) || 0;
    var deal = parseFloat(document.getElementById('adcDealPrice').value) || 0;
    var el = document.getElementById('adcDiscountPct');
    if (orig > 0 && deal > 0 && deal < orig) {
        el.textContent = '-' + Math.round((orig - deal) / orig * 100) + '%';
    } else {
        el.textContent = '—';
    }
}
document.getElementById('adcOrigPrice').addEventListener('input', calcDiscount);
document.getElementById('adcDealPrice').addEventListener('input', calcDiscount);
calcDiscount();
</script>
@endpush
