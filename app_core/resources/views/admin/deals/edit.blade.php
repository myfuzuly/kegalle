@extends('layouts.admin')
@section('title','Edit Deal #'.$deal->id)
@section('page','Deals')
@section('heading','Edit Deal #{{ $deal->id }}')
@section('subheading','Adjust pricing, schedule and status')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/deals">← Back</a>@endsection

@push('styles')

@endpush

@section('content')
@if(session('error'))
<div class="alert-error">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="alert-error">
    <ul class="list-pl">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

@php
$origPrice = $deal->original_price ?: optional($deal->listing)->price ?: 0;
$oldStatus = old('status', $deal->status);
@endphp

<form method="post" action="/admin/deals/{{ $deal->id }}">
@csrf @method('PUT')

<div class="ade-shell">
<div>

    {{-- Deal info banner --}}
    <div class="ade-card">
        <div class="ade-card-header">
            <div class="ade-card-icon blue">🛍️</div>
            <div>
                <p class="ade-card-title">{{ $deal->listing->title ?? $deal->title ?? 'Deal #'.$deal->id }}</p>
                <p class="ade-card-sub">
                    @if($deal->poster_type === 'independent')
                    🎤 {{ $deal->organizer_name ?? 'Independent advertiser' }}
                    @else
                    {{ $deal->user->name ?? '—' }}@if($deal->store) · {{ $deal->store->name }}@endif
                    @endif
                </p>
            </div>
        </div>
        <div class="flex-wrap-g8">
            <span class="ade-meta-pill">📦 Original: <strong>LKR {{ number_format($origPrice) }}</strong></span>
            <span class="ade-meta-pill">🔖 Sold: {{ $deal->sold_count ?? 0 }}</span>
            @if($deal->listing)<span class="ade-meta-pill">🔗 <a href="/listings/{{ $deal->listing->slug }}" target="_blank" class="link-primary">View Listing ↗</a></span>@endif
        </div>
    </div>

    {{-- Pricing --}}
    <div class="ade-card">
        <div class="ade-card-header">
            <div class="ade-card-icon red">💰</div>
            <div>
                <p class="ade-card-title">Pricing</p>
                <p class="ade-card-sub">Deal price must be below LKR {{ number_format($origPrice) }}</p>
            </div>
        </div>
        <div class="ade-grid" class="mb-14">
            <div class="ade-field">
                <label class="ade-label">Deal Price (LKR) <span class="ade-req">*</span></label>
                <input name="deal_price" id="adeDealPrice" type="number" step="1" min="1" required class="ade-input" value="{{ old('deal_price', $deal->deal_price) }}">
            </div>
            <div class="ade-field">
                <label class="ade-label">Stock Quantity</label>
                <input name="stock_qty" type="number" min="1" class="ade-input" value="{{ old('stock_qty', $deal->stock_qty) }}" placeholder="Unlimited">
            </div>
        </div>
        <div class="flex-row gap-10">
            <div class="ade-discount-badge">
                <span>Discount:</span>
                <span id="adeDiscountPct">{{ $origPrice > 0 ? '-'.round((($origPrice - $deal->deal_price) / $origPrice) * 100).'%' : '—' }}</span>
            </div>
            <span class="ade-hint">Based on original price LKR {{ number_format($origPrice) }}</span>
        </div>
    </div>

    {{-- Schedule --}}
    <div class="ade-card">
        <div class="ade-card-header">
            <div class="ade-card-icon amber">📅</div>
            <div>
                <p class="ade-card-title">Schedule</p>
                <p class="ade-card-sub">When this deal is live</p>
            </div>
        </div>
        <div class="ade-grid">
            <div class="ade-field">
                <label class="ade-label">Starts <span class="ade-req">*</span></label>
                <input type="date" name="starts_at" required class="ade-input" value="{{ old('starts_at', optional($deal->starts_at)->format('Y-m-d')) }}">
            </div>
            <div class="ade-field">
                <label class="ade-label">Ends <span class="ade-req">*</span></label>
                <input type="date" name="ends_at" required class="ade-input" value="{{ old('ends_at', optional($deal->ends_at)->format('Y-m-d')) }}">
            </div>
        </div>
    </div>

    {{-- Admin Note --}}
    <div class="ade-card">
        <div class="ade-card-header">
            <div class="ade-card-icon slate">🗒️</div>
            <div>
                <p class="ade-card-title">Admin Note</p>
                <p class="ade-card-sub">Visible to the seller</p>
            </div>
        </div>
        <div class="ade-field">
            <input name="admin_note" class="ade-input" value="{{ old('admin_note', $deal->admin_note) }}" placeholder="Optional note for the seller…">
        </div>
    </div>

</div>

{{-- SIDEBAR --}}
<div>
    <div class="ade-card">
        <div class="ade-card-header">
            <div class="ade-card-icon green">💾</div>
            <div>
                <p class="ade-card-title">Save</p>
                <p class="ade-card-sub">Update this deal</p>
            </div>
        </div>
        <button type="submit" class="ade-save-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
            Save Changes
        </button>
        <a href="/admin/deals" class="ade-cancel-btn">Cancel</a>
    </div>

    <div class="ade-card">
        <div class="ade-card-header">
            <div class="ade-card-icon slate">⚙️</div>
            <div><p class="ade-card-title">Status</p></div>
        </div>
        <div class="ade-field">
            <input type="hidden" name="status" id="adeStatusVal" value="{{ $oldStatus }}">
            <div class="ksd-wrap">
                <button type="button" class="ksd-trigger ksd-has-value" id="adeStatusTrigger">
                    <span class="ksd-trigger-text" id="adeStatusLabel">
                        @if($oldStatus==='approved') ✅ Approved @elseif($oldStatus==='pending') ⏳ Pending @elseif($oldStatus==='rejected') ❌ Rejected @else 🕛 Expired @endif
                    </span>
                    <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="ksd-dropdown" id="adeStatusDropdown">
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

    <div class="ade-card">
        <div class="ade-card-header">
            <div class="ade-card-icon amber">⚡</div>
            <div><p class="ade-card-title">Boost</p></div>
        </div>
        <div class="ade-checks">
            <label class="ade-check">
                <input type="checkbox" name="is_flash" value="1" @checked(old('is_flash', $deal->is_flash))>
                ⚡ Flash Deal
            </label>
            <label class="ade-check">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $deal->is_featured))>
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
(function(){
    var trigger=document.getElementById('adeStatusTrigger'), dropdown=document.getElementById('adeStatusDropdown');
    var hidden=document.getElementById('adeStatusVal'), label=document.getElementById('adeStatusLabel');
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

(function(){
    var orig = {{ (float)($deal->original_price ?: optional($deal->listing)->price ?: 0) }};
    var dealEl = document.getElementById('adeDealPrice');
    var pctEl  = document.getElementById('adeDiscountPct');
    if(!dealEl||!pctEl) return;
    dealEl.addEventListener('input', function(){
        var d = parseFloat(this.value)||0;
        pctEl.textContent = orig>0&&d>0&&d<orig ? '-'+Math.round((orig-d)/orig*100)+'%' : '—';
    });
})();
</script>
@endpush
