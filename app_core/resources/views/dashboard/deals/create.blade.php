@extends('layouts.dashboard')
@section('title','Submit Deal')
@section('eyebrow','Deals')
@section('heading','Submit a Deal')

@section('actions')
<a href="/dashboard/deals" class="kdl-tb-btn kdl-tb-btn-light">
  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m7-7l-7 7 7 7"/></svg>
  My Deals
</a>
@endsection

@section('content')

<div class="dc-shell">
<div>

{{-- Hero: new product as deal --}}
<a href="/dashboard/deals/create-with-product" class="dc-hero">
  <div class="dc-hero-icon">🔥</div>
  <div class="dc-hero-body">
    <strong>Don't have a listing? Create product + deal in one step</strong>
    <span>Add a new product and set your deal price without extra steps.</span>
  </div>
  <div class="dc-hero-cta">Get Started →</div>
</a>

@if($listings->isEmpty())
<div class="empty-card">
  <div class="fs42-mb14">📋</div>
  <strong class="block-fs17-fw7">No approved listings yet</strong>
  <p class="desc-text-mb20">You need at least one approved listing to submit a deal from an existing product.</p>
  <a class="btn-primary-green" href="/dashboard/listings/create">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    Post a Listing First
  </a>
</div>
@else

<form method="POST" action="{{ route('dashboard.deals.store') }}" id="dealCreateForm">
@csrf

{{-- Section 1: Listing select --}}
<div class="dc-card">
  <div class="dc-card-title"><span>📋</span> Select Listing</div>
  <label class="dc-label">Choose from your approved listings <span class="dc-req">*</span></label>
  <input type="hidden" name="listing_id" id="dealListingId" value="{{ old('listing_id') }}">
  <div class="dcd-wrap" id="dcdWrap">
    <div class="dcd-trigger" id="dcdTrigger" tabindex="0">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
      <span id="dcdLabel" class="dcd-placeholder">Search and choose a listing…</span>
      <svg class="dcd-chevron" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
    </div>
    <div class="dcd-dropdown" id="dcdDropdown" class="hidden">
      <div class="dcd-search-row">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" class="flex-shrink-0"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" class="dcd-search-input" id="dcdSearch" placeholder="Search by title or store…" autocomplete="off">
      </div>
      <div class="dcd-list" id="dcdList">
        @foreach($listings as $listing)
        <div class="dcd-item"
             data-id="{{ $listing->id }}"
             data-price="{{ $listing->price ?? 0 }}"
             data-search="{{ strtolower($listing->title) }} {{ strtolower($listing->store->name ?? '') }}"
             data-label="{{ $listing->title }}{{ $listing->store ? ' — '.$listing->store->name : '' }}">
          <div class="flex-grow-min">
            <div class="dcd-item-name">{{ $listing->title }}</div>
            @if($listing->store)<div class="dcd-item-store">🏪 {{ $listing->store->name }}</div>@endif
          </div>
          <div class="dcd-item-price">LKR {{ number_format($listing->price ?? 0) }}</div>
        </div>
        @endforeach
        <div class="dcd-empty" id="dcdEmpty" class="hidden">No listings match your search</div>
      </div>
    </div>
  </div>
  <div class="dc-hint" class="mt-8">Only approved listings are shown. <a class="text-green-fw6" href="/dashboard/listings/create">Post a new listing →</a></div>
</div>

{{-- Section 2: Pricing --}}
<div class="dc-card">
  <div class="dc-card-title"><span>💰</span> Deal Pricing</div>
  <div class="dc-grid" class="mb-18">
    <div>
      <label class="dc-label">Original Price</label>
      <input type="text" id="dealOrigPrice" readonly class="dc-input" placeholder="Select a listing first">
    </div>
    <div>
      <label class="dc-label">Deal Price (LKR) <span class="dc-req">*</span></label>
      <input type="number" name="deal_price" required min="1" id="dealPrice" value="{{ old('deal_price') }}" placeholder="Enter special price" class="dc-input">
    </div>
  </div>
  {{-- Discount preview --}}
  <div class="dc-discount-preview" id="dealDiscountPreview" class="hidden">
    <div class="dc-discount-badge" id="dealDiscountPct">0%</div>
    <div class="dc-discount-text">OFF — buyers save <strong id="dealSaveAmt">LKR 0</strong></div>
  </div>
</div>

{{-- Section 3: Dates --}}
<div class="dc-card">
  <div class="dc-card-title"><span>📅</span> Deal Duration</div>
  <div class="dc-grid">
    <div>
      <label class="dc-label">Start Date <span class="dc-req">*</span></label>
      <input type="date" name="starts_at" required value="{{ old('starts_at', date('Y-m-d')) }}" class="dc-input">
    </div>
    <div>
      <label class="dc-label">End Date <span class="dc-req">*</span></label>
      <input type="date" name="ends_at" required value="{{ old('ends_at', date('Y-m-d', strtotime('+7 days'))) }}" class="dc-input">
    </div>
  </div>
  <div class="dc-hint" class="mt-10">Deal won't show after the end date even if still approved.</div>
</div>

{{-- Section 4: Options --}}
<div class="dc-card">
  <div class="dc-card-title"><span>⚙️</span> Options</div>
  <div class="mb-16">
    <label class="dc-label">Stock Quantity (optional)</label>
    <input type="number" name="stock_qty" min="1" value="{{ old('stock_qty') }}" placeholder="Leave empty for unlimited" class="dc-input">
    <div class="dc-hint">Buyers will see "Only X left!" when stock is low.</div>
  </div>
  <label class="dc-flash-label">
    <input type="checkbox" name="is_flash" value="1" {{ old('is_flash') ? 'checked' : '' }}>
    <div class="dc-flash-body">
      <strong>⚡ Request as Flash Deal</strong>
      <span>Shown with a countdown timer for extra urgency. Admin decides final approval.</span>
    </div>
  </label>
</div>

{{-- Approval note --}}
<div class="notice-row-green2">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 8 12 12 14 14"/></svg>
  <div>Your deal will be <strong>reviewed by admin</strong> before appearing on the Deals page. You'll see the status update in your deals list.</div>
</div>

<button type="submit" class="dc-submit" id="dcSubmitBtn">
  <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  Submit Deal for Approval
</button>

</form>
@endif

</div>

{{-- Right tips --}}
<div class="dc-right">
  <div class="dc-tip-card">
    <h4>🔥 What makes a great deal?</h4>
    <div class="dc-tip-row"><div class="dc-tip-dot">1</div><div>Offer at least 15–20% off — deals under 10% rarely get approved.</div></div>
    <div class="dc-tip-row"><div class="dc-tip-dot">2</div><div>Make sure your listing has clear photos and a full description.</div></div>
    <div class="dc-tip-row"><div class="dc-tip-dot">3</div><div>Set realistic start/end dates — 3–14 days is the sweet spot.</div></div>
    <div class="dc-tip-row"><div class="dc-tip-dot">4</div><div>Flash deals with countdowns convert 3× better than static ones.</div></div>
  </div>
  <div class="dc-tip-card border-sky100">
    <h4>⏱ Approval timeline</h4>
    <div class="dc-tip-row"><div class="dc-tip-dot badge-blue">1</div><div>Submit your deal.</div></div>
    <div class="dc-tip-row"><div class="dc-tip-dot badge-blue">2</div><div>Admin reviews within a few hours.</div></div>
    <div class="dc-tip-row"><div class="dc-tip-dot badge-blue">3</div><div>Approved deals go live on the Deals page instantly.</div></div>
    <div class="dc-tip-row"><div class="dc-tip-dot badge-blue">4</div><div>You'll see status change in your Deals list.</div></div>
  </div>
</div>

</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
  var wrap=document.getElementById('dcdWrap'), trigger=document.getElementById('dcdTrigger'),
      dropdown=document.getElementById('dcdDropdown'), search=document.getElementById('dcdSearch'),
      list=document.getElementById('dcdList'), empty=document.getElementById('dcdEmpty'),
      label=document.getElementById('dcdLabel'), hiddenId=document.getElementById('dealListingId');
  if(!wrap) return;
  var items=Array.from(list.querySelectorAll('.dcd-item'));
  var selectedPrice=0;

  function open(){ dropdown.style.display=''; trigger.classList.add('dcd-open'); search.value=''; filterItems(''); setTimeout(function(){search.focus();},50); }
  function close(){ dropdown.style.display='none'; trigger.classList.remove('dcd-open'); }

  function selectItem(el){
    hiddenId.value=el.dataset.id;
    selectedPrice=parseFloat(el.dataset.price||0);
    label.textContent=el.dataset.label;
    label.className='dcd-selected-text';
    trigger.classList.add('dcd-selected');
    items.forEach(function(i){i.classList.toggle('dcd-active',i===el);});
    document.getElementById('dealOrigPrice').value=selectedPrice>0?'LKR '+selectedPrice.toLocaleString():'Free';
    updatePreview();
    close();
  }
  function filterItems(q){
    q=q.toLowerCase().trim(); var n=0;
    items.forEach(function(it){ var m=!q||it.dataset.search.includes(q); it.style.display=m?'':'none'; if(m)n++; });
    empty.style.display=n?'none':'';
  }
  function updatePreview(){
    var dp=parseFloat(document.getElementById('dealPrice').value||0);
    var prev=document.getElementById('dealDiscountPreview');
    if(selectedPrice>0&&dp>0&&dp<selectedPrice){
      document.getElementById('dealDiscountPct').textContent=((selectedPrice-dp)/selectedPrice*100).toFixed(0)+'%';
      document.getElementById('dealSaveAmt').textContent='LKR '+(selectedPrice-dp).toLocaleString();
      prev.style.display='flex';
    } else { prev.style.display='none'; }
  }

  trigger.addEventListener('click',function(){dropdown.style.display==='none'?open():close();});
  trigger.addEventListener('keydown',function(e){if(e.key==='Enter'||e.key===' '){e.preventDefault();open();}});
  search.addEventListener('input',function(){filterItems(this.value);});
  items.forEach(function(it){it.addEventListener('click',function(){selectItem(it);});});
  document.addEventListener('mousedown',function(e){if(wrap&&!wrap.contains(e.target))close();});

  var priceInput=document.getElementById('dealPrice');
  if(priceInput)priceInput.addEventListener('input',updatePreview);

  // Re-select old value on validation failure
  var oldId='{{ old("listing_id") }}';
  if(oldId){ var oldEl=list.querySelector('[data-id="'+oldId+'"]'); if(oldEl)selectItem(oldEl); }

  // Submit lock
  var form=document.getElementById('dealCreateForm');
  if(form){ form.addEventListener('submit',function(){var b=document.getElementById('dcSubmitBtn');if(b){b.disabled=true;b.textContent='Submitting…';}}); }
})();
</script>
@endpush
