@extends('layouts.dashboard')
@section('title','Edit Deal')
@section('heading','✏️ Edit Deal')
@section('subheading','Update your deal — changes will be reviewed by admin before going live again.')
@section('actions')
<a href="/dashboard/deals" class="kd-btn kd-btn-light">← Back to Deals</a>
@endsection

@section('content')

@if(session('success'))
<div class="alert-green-md">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-error">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="alert-error-lg2">
    @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
</div>
@endif

<section class="kd-card mw-680">
    <div class="kd-card-head"><h2>Deal Details</h2></div>

    {{-- Listing preview --}}
    @php $img = optional(optional($deal->listing)->images->first())->path; @endphp
    <div class="notice-row-var">
        <div class="icon-box-56c">
            @if($img)<img src="{{ asset('storage/'.ltrim($img,'/')) }}" alt="" class="img-cover">@else🛍️@endif
        </div>
        <div>
            <b class="fs14-block-mb2">{{ $deal->listing->title ?? 'Deleted listing' }}</b>
            <div class="fs-12h text-gray">
                Original price: <b>LKR {{ number_format($deal->original_price ?? 0) }}</b>
                &nbsp;·&nbsp;
                Status: <span style="font-weight:700;color:{{ $deal->status === 'approved' ? '#1b5e20' : ($deal->status === 'rejected' ? '#c62828' : '#e65100') }}">{{ ucfirst($deal->status) }}</span>
            </div>
            @if($deal->admin_note)
            <div class="fs12-orange-mt4">Admin note: {{ $deal->admin_note }}</div>
            @endif
        </div>
    </div>

    <form method="POST" action="/dashboard/deals/{{ $deal->id }}">
        @csrf
        @method('PUT')

        <div class="grid-2col mb-20">
            <div>
                <label class="label-strong">Original Price</label>
                <input class="kd-input-disabled" type="text" readonly value="LKR {{ number_format($deal->original_price ?? 0) }}">
            </div>
            <div>
                <label class="label-strong">Deal Price (LKR) *</label>
                <input class="form-input-std" name="deal_price" type="number" step="0.01" min="1" value="{{ old('deal_price', $deal->deal_price) }}" required id="editDealPrice">
                <small class="help-text">Must be lower than LKR {{ number_format($deal->original_price ?? 0) }}</small>
            </div>
        </div>

        <div class="none-mb20" id="editDiscountPreview">
            <div class="notice-red-sm">
                Discount: <strong id="editDiscountPct" class="text-danger">0%</strong> off
                — Customers save <strong id="editSaveAmt">LKR 0</strong>
            </div>
        </div>

        <div class="grid-2col mb-20">
            <div>
                <label class="label-strong">Start Date *</label>
                <input class="form-input-std" name="starts_at" type="date" value="{{ old('starts_at', optional($deal->starts_at)->format('Y-m-d')) }}" required>
            </div>
            <div>
                <label class="label-strong">End Date *</label>
                <input class="form-input-std" name="ends_at" type="date" value="{{ old('ends_at', optional($deal->ends_at)->format('Y-m-d')) }}" required>
            </div>
        </div>

        <div class="mb-20">
            <label class="label-strong">Stock Quantity (optional)</label>
            <input class="form-input-std" name="stock_qty" type="number" min="1" value="{{ old('stock_qty', $deal->stock_qty) }}" placeholder="Leave empty for unlimited">
        </div>

        <div class="mb-24">
            <label class="flex-g10-fw7-ptr">
                <input type="checkbox" name="is_flash" value="1" @checked(old('is_flash', $deal->is_flash))>
                ⚡ Request Flash Deal placement (limited time, shown with countdown timer)
            </label>
        </div>

        <div class="notice-orange">
            ⚠️ Saving changes will send this deal back for admin approval before it appears publicly again.
        </div>

        <button type="submit" class="kd-btn kd-btn-primary input-52h">Save Changes</button>
    </form>
</section>

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded', function(){
    var origPrice = {{ (float)($deal->original_price ?? 0) }};
    var priceInput = document.getElementById('editDealPrice');
    var preview = document.getElementById('editDiscountPreview');
    var pctEl = document.getElementById('editDiscountPct');
    var saveEl = document.getElementById('editSaveAmt');

    function updatePreview(){
        var dp = parseFloat(priceInput.value || 0);
        if(origPrice > 0 && dp > 0 && dp < origPrice){
            pctEl.textContent = ((origPrice - dp) / origPrice * 100).toFixed(0) + '%';
            saveEl.textContent = 'LKR ' + (origPrice - dp).toLocaleString();
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    }
    priceInput.addEventListener('input', updatePreview);
    updatePreview();
});
</script>
@endpush
