@extends('layouts.dashboard')
@section('title','Add New Product as Deal')
@section('heading','🔥 Add New Product as Deal')
@section('subheading','Create a new product listing and a deal discount in one step. Both will be reviewed by admin.')

@section('actions')
<a href="/dashboard/deals/create" class="kd-btn kd-btn-light">← Back to Deals</a>
@endsection

@section('content')

@if(session('error'))
<div class="alert-error">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="alert-error-lg2">
    @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
</div>
@endif

<form method="POST" action="{{ route('dashboard.deals.storeWithProduct') }}" enctype="multipart/form-data" id="cwpForm">
@csrf

{{-- ═══ STEP 1 – PRODUCT INFO ═══ --}}
<section class="kd-card mw720-mb20">
    <div class="kd-card-head">
        <h2>Step 1 — Product Details</h2>
        <span class="caption-gray">Tell buyers about your product</span>
    </div>

    {{-- Store badge --}}
    @if($store)
    <div class="notice-row-green">
        <span class="fs-24">🏪</span>
        <div>
            <div class="fw7-fs135-emerald">{{ $store->name }}</div>
            <div class="fs12-emerald">Product will be posted under your store</div>
        </div>
    </div>
    @endif

    {{-- Photos --}}
    <div class="mb-20">
        <div class="fw7-fs14-mb8">
            Product Photos
            <span class="fw4-fs12-gray"> (up to 5 · JPG, PNG · max 4 MB each)</span>
        </div>
        <input type="file" name="images[]" id="cwpImages" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
        <div class="grid-5-g10" id="cwpSlots">
            @for($i=0;$i<5;$i++)
            <div class="cwp-slot upload-zone" data-slot="{{ $i }}">
                <span class="cwp-slot-icon fs24-muted">+</span>
                <span class="cwp-slot-lbl fs10-muted-mt3">{{ $i===0?'Main photo':'Photo '.($i+1) }}</span>
            </div>
            @endfor
        </div>
    </div>

    {{-- Title --}}
    <div class="mb-16">
        <label class="card-label">Product Title *</label>
        <input class="form-input-std" name="title" type="text" required value="{{ old('title') }}" placeholder="e.g. Fresh Ceylon Cinnamon Sticks 500g">
    </div>

    {{-- Category + Price --}}
    <div class="grid-2col">
        <div>
            <label class="card-label">Category *</label>
            <select class="kd-input" name="category_id" required id="cwpCategory">
                <option value="">— Select category —</option>
                @foreach($categories->whereNull('parent_id') as $parent)
                    @php $children = $categories->where('parent_id', $parent->id); @endphp
                    @if($children->isNotEmpty())
                        <optgroup label="{{ $parent->name }}">
                            @foreach($children as $child)
                                @php $leaves = $categories->where('parent_id', $child->id); @endphp
                                @if($leaves->isEmpty())
                                    <option value="{{ $child->id }}" {{ old('category_id') == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                                @else
                                    @foreach($leaves as $leaf)
                                        <option value="{{ $leaf->id }}" {{ old('category_id') == $leaf->id ? 'selected' : '' }}>{{ $child->name }} › {{ $leaf->name }}</option>
                                    @endforeach
                                @endif
                            @endforeach
                        </optgroup>
                    @endif
                @endforeach
            </select>
        </div>
        <div>
            <label class="card-label">Original Price (LKR) *</label>
            <input class="form-input-std" name="price" type="number" required min="1" step="0.01" id="cwpOrigPrice" value="{{ old('price') }}" placeholder="e.g. 1200">
            <small class="help-text">The regular selling price before the deal</small>
        </div>
    </div>

    {{-- Description --}}
    <div class="mb-4px">
        <label class="card-label">Product Description *</label>
        <textarea class="kd-textarea" name="description" required minlength="20" rows="4" placeholder="Describe your product — size, weight, condition, delivery area, contact preference…">{{ old('description') }}</textarea>
        <div class="flex-jsb-fs12-mt4">
            <span>Minimum 20 characters</span>
            <span id="cwpDescCount">0 chars</span>
        </div>
    </div>
</section>

{{-- ═══ STEP 2 – DEAL INFO ═══ --}}
<section class="kd-card mw720-mb20">
    <div class="kd-card-head">
        <h2>Step 2 — Deal Discount</h2>
        <span class="caption-gray">Set your special deal price and duration</span>
    </div>

    <div class="grid-2col">
        <div>
            <label class="card-label">Deal Price (LKR) *</label>
            <input class="form-input-std" name="deal_price" type="number" required min="1" step="0.01" id="cwpDealPrice" value="{{ old('deal_price') }}" placeholder="e.g. 900">
            <small class="help-text" id="cwpOrigHint">Must be lower than the original price</small>
        </div>
        <div>
            <label class="card-label">Stock Quantity (optional)</label>
            <input class="form-input-std" name="stock_qty" type="number" min="1" value="{{ old('stock_qty') }}" placeholder="Leave empty for unlimited">
        </div>
    </div>

    {{-- Discount live preview --}}
    <div class="none-mb16" id="cwpDiscountPreview">
        <div class="notice-red-flex">
            <span class="fs-26">🏷️</span>
            <span>Discount: <strong class="red600-fs18" id="cwpPct">0%</strong> off &nbsp;—&nbsp; Customers save <strong id="cwpSave">LKR 0</strong></span>
        </div>
    </div>

    <div class="grid-2col">
        <div>
            <label class="card-label">Deal Starts *</label>
            <input class="form-input-std" name="starts_at" type="date" required value="{{ old('starts_at', date('Y-m-d')) }}">
        </div>
        <div>
            <label class="card-label">Deal Ends *</label>
            <input class="form-input-std" name="ends_at" type="date" required value="{{ old('ends_at', date('Y-m-d', strtotime('+7 days'))) }}">
        </div>
    </div>

    <div class="mb-24">
        <label class="notice-row-amber">
            <input class="chk-18-amber" type="checkbox" name="is_flash" value="1" {{ old('is_flash') ? 'checked' : '' }}>
            <span>
                ⚡ Request Flash Deal
                <span class="fw4-fs125-amber">Limited-time offer shown with a countdown timer on the deals page</span>
            </span>
        </label>
    </div>

    <div class="notice-emerald">
        ℹ️ Both your product listing and deal will be reviewed by admin. Once approved, they go live automatically.
    </div>

    <button type="submit" id="cwpSubmitBtn" class="kd-btn kd-btn-primary input-54h">
        Submit Product &amp; Deal for Approval
    </button>
</section>

</form>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded', function(){

    // ── Image slots ─────────────────────────────────────────
    var MAX = 5;
    var slots = [];
    for(var s=0;s<MAX;s++) slots.push(null);
    var fileInput = document.getElementById('cwpImages');
    var slotEls   = document.querySelectorAll('.cwp-slot');

    function syncInput(){
        var dt = new DataTransfer();
        slots.forEach(function(f){ if(f) dt.items.add(f); });
        fileInput.files = dt.files;
    }

    function renderSlots(){
        slotEls.forEach(function(slot, i){
            var icon = slot.querySelector('.cwp-slot-icon');
            var lbl  = slot.querySelector('.cwp-slot-lbl');
            var oldImg = slot.querySelector('img');
            var oldRm  = slot.querySelector('.cwp-rm');
            if(oldImg) oldImg.remove();
            if(oldRm)  oldRm.remove();

            if(slots[i]){
                slot.style.borderColor = '#22C55E';
                slot.style.background  = '#F0FDF4';
                if(icon) icon.style.display = 'none';
                if(lbl)  lbl.style.display  = 'none';
                var img = document.createElement('img');
                img.src = URL.createObjectURL(slots[i]);
                img.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block';
                slot.appendChild(img);
                var rm = document.createElement('button');
                rm.type = 'button'; rm.className = 'cwp-rm';
                rm.style.cssText = 'position:absolute;top:4px;right:4px;width:20px;height:20px;border-radius:50%;background:rgba(0,0,0,.55);color:#fff;border:none;cursor:pointer;font-size:13px;line-height:1;z-index:2';
                rm.textContent = '×';
                rm.addEventListener('click', function(e){
                    e.stopPropagation();
                    slots[i] = null;
                    slots = slots.filter(Boolean);
                    while(slots.length < MAX) slots.push(null);
                    syncInput(); renderSlots();
                });
                slot.appendChild(rm);
            } else {
                slot.style.borderColor = '#CBD5E1';
                slot.style.background  = '#F8FAFC';
                if(icon) icon.style.display = '';
                if(lbl)  lbl.style.display  = '';
            }
        });
    }

    slotEls.forEach(function(slot){
        slot.addEventListener('click', function(){
            var i = parseInt(slot.dataset.slot);
            if(slots[i]) return;
            var tmp = document.createElement('input');
            tmp.type = 'file'; tmp.accept = 'image/jpeg,image/png,image/webp'; tmp.multiple = true;
            tmp.addEventListener('change', function(){
                Array.from(tmp.files).forEach(function(f){
                    if(!f.type.match(/^image\//)) return;
                    for(var j=0;j<MAX;j++){ if(!slots[j]){ slots[j]=f; break; } }
                });
                syncInput(); renderSlots();
            });
            tmp.click();
        });
        slot.addEventListener('dragover',  function(e){ e.preventDefault(); slot.style.borderColor='#3B82F6'; });
        slot.addEventListener('dragleave', function(){ renderSlots(); });
        slot.addEventListener('drop', function(e){
            e.preventDefault();
            Array.from(e.dataTransfer.files).forEach(function(f){
                if(!f.type.match(/^image\//)) return;
                for(var j=0;j<MAX;j++){ if(!slots[j]){ slots[j]=f; break; } }
            });
            syncInput(); renderSlots();
        });
    });

    // ── Discount preview ─────────────────────────────────────
    var origInput  = document.getElementById('cwpOrigPrice');
    var dealInput  = document.getElementById('cwpDealPrice');
    var preview    = document.getElementById('cwpDiscountPreview');
    var pctEl      = document.getElementById('cwpPct');
    var saveEl     = document.getElementById('cwpSave');
    var origHint   = document.getElementById('cwpOrigHint');

    function updateDiscount(){
        var op = parseFloat(origInput.value || 0);
        var dp = parseFloat(dealInput.value || 0);
        if(op > 0) origHint.textContent = 'Must be lower than LKR ' + op.toLocaleString();
        if(op > 0 && dp > 0 && dp < op){
            pctEl.textContent  = ((op - dp) / op * 100).toFixed(0) + '%';
            saveEl.textContent = 'LKR ' + (op - dp).toLocaleString();
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    }
    origInput.addEventListener('input', updateDiscount);
    dealInput.addEventListener('input', updateDiscount);
    updateDiscount();

    // ── Description counter ───────────────────────────────────
    var desc = document.querySelector('[name="description"]');
    var cnt  = document.getElementById('cwpDescCount');
    if(desc && cnt){
        function updateCount(){
            var n = desc.value.length;
            cnt.textContent = n + ' chars' + (n >= 20 ? ' ✓' : ' (min 20)');
            cnt.style.color = n >= 20 ? '#15803D' : '#D97706';
        }
        desc.addEventListener('input', updateCount);
        updateCount();
    }

    // ── Submit guard ──────────────────────────────────────────
    var btn = document.getElementById('cwpSubmitBtn');
    document.getElementById('cwpForm').addEventListener('submit', function(){
        if(btn){ btn.disabled = true; btn.textContent = 'Uploading… please wait'; btn.style.opacity = '.7'; }
    });

});
</script>
@endpush
