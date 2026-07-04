@extends('layouts.dashboard')

@section('title','Submit Deal')
@section('heading','🔥 Submit a Deal')
@section('subheading','Choose a listing and set a special discounted price. Deals require admin approval before appearing on the site.')

@section('actions')
<a href="/dashboard/deals" class="kd-btn kd-btn-light">← Back to Deals</a>
@endsection

@section('content')
@if($listings->isEmpty())
    <div class="kd-empty">
        <strong>No approved listings</strong>
        <p>You need at least one approved listing to submit a deal.</p>
        <a href="/dashboard/listings/create" class="kd-btn kd-btn-primary">Post Listing First</a>
    </div>
@else
<section class="kd-card" style="max-width:680px">
    <div class="kd-card-head"><h2>Deal Details</h2></div>

    <form method="POST" action="{{ route('dashboard.deals.store') }}">
        @csrf

        <div style="margin-bottom:20px">
            <label style="display:block;font-weight:800;margin-bottom:6px;font-size:14px">Select Listing *</label>
            <select name="listing_id" required id="dealListingSelect" style="width:100%;padding:12px 14px;border:1px solid var(--kd-line);border-radius:14px;font-size:14px;background:#fff">
                <option value="">— Choose a listing —</option>
                @foreach($listings as $listing)
                    <option value="{{ $listing->id }}" data-price="{{ $listing->price ?? 0 }}" {{ old('listing_id') == $listing->id ? 'selected' : '' }}>
                        {{ $listing->title }} — LKR {{ number_format($listing->price ?? 0) }}
                        @if($listing->store) ({{ $listing->store->name }}) @endif
                    </option>
                @endforeach
            </select>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
            <div>
                <label style="display:block;font-weight:800;margin-bottom:6px;font-size:14px">Original Price</label>
                <input type="text" id="dealOrigPrice" readonly style="width:100%;padding:12px 14px;border:1px solid var(--kd-line);border-radius:14px;font-size:14px;background:var(--kd-bg)">
            </div>
            <div>
                <label style="display:block;font-weight:800;margin-bottom:6px;font-size:14px">Deal Price (LKR) *</label>
                <input type="number" name="deal_price" required min="1" value="{{ old('deal_price') }}" id="dealPrice" placeholder="Enter special price" style="width:100%;padding:12px 14px;border:1px solid var(--kd-line);border-radius:14px;font-size:14px">
            </div>
        </div>

        <div id="dealDiscountPreview" style="display:none;margin-bottom:20px">
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:14px;padding:12px 18px;font-size:14px">
                Discount: <strong id="dealDiscountPct" style="color:#dc2626">0%</strong> off
                — Customers save <strong id="dealSaveAmt">LKR 0</strong>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
            <div>
                <label style="display:block;font-weight:800;margin-bottom:6px;font-size:14px">Start Date *</label>
                <input type="date" name="starts_at" required value="{{ old('starts_at', date('Y-m-d')) }}" style="width:100%;padding:12px 14px;border:1px solid var(--kd-line);border-radius:14px;font-size:14px">
            </div>
            <div>
                <label style="display:block;font-weight:800;margin-bottom:6px;font-size:14px">End Date *</label>
                <input type="date" name="ends_at" required value="{{ old('ends_at', date('Y-m-d', strtotime('+7 days'))) }}" style="width:100%;padding:12px 14px;border:1px solid var(--kd-line);border-radius:14px;font-size:14px">
            </div>
        </div>

        <div style="margin-bottom:20px">
            <label style="display:block;font-weight:800;margin-bottom:6px;font-size:14px">Stock Quantity (optional)</label>
            <input type="number" name="stock_qty" min="1" value="{{ old('stock_qty') }}" placeholder="Leave empty for unlimited" style="width:100%;padding:12px 14px;border:1px solid var(--kd-line);border-radius:14px;font-size:14px">
        </div>

        <div style="margin-bottom:20px">
            <label style="display:flex;align-items:center;gap:10px;font-weight:700;font-size:14px;cursor:pointer">
                <input type="checkbox" name="is_flash" value="1" {{ old('is_flash') ? 'checked' : '' }}>
                ⚡ Request as Flash Deal (limited time, shown with countdown timer)
            </label>
        </div>

        <div style="background:#ecfdf5;border-radius:14px;padding:14px 18px;font-size:13px;color:#047857;margin-bottom:24px;font-weight:600">
            ℹ️ Your deal will be reviewed by admin before appearing on the Deals page. You'll see the status in your deals list.
        </div>

        <button type="submit" class="kd-btn kd-btn-primary" style="width:100%;min-height:52px;font-size:16px;border-radius:16px">Submit Deal for Approval</button>
    </form>
</section>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    var sel = document.getElementById('dealListingSelect');
    var orig = document.getElementById('dealOrigPrice');
    var price = document.getElementById('dealPrice');
    var preview = document.getElementById('dealDiscountPreview');
    var pctEl = document.getElementById('dealDiscountPct');
    var saveEl = document.getElementById('dealSaveAmt');

    function updatePreview(){
        var opt = sel.options[sel.selectedIndex];
        var op = parseFloat(opt?.dataset?.price || 0);
        orig.value = op > 0 ? 'LKR ' + op.toLocaleString() : '';
        var dp = parseFloat(price.value || 0);
        if(op > 0 && dp > 0 && dp < op){
            var pct = ((op - dp) / op * 100).toFixed(0);
            var save = op - dp;
            pctEl.textContent = pct + '%';
            saveEl.textContent = 'LKR ' + save.toLocaleString();
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    }

    sel.addEventListener('change', updatePreview);
    price.addEventListener('input', updatePreview);
    updatePreview();
});
</script>
@endpush
