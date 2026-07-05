@extends('layouts.app')
@section('title','Edit Deal - Kegalle')
@push('styles')
<link rel="stylesheet" href="/css/kegalle-dashboard-functional.css?v=2">
@endpush
@section('content')
<section class="kd-wrap">
    <div class="kd-container kd-form-container">
        <div class="kd-head">
            <div>
                <span>Seller Dashboard</span>
                <h1>Edit Deal</h1>
                <p>Update your deal — changes are reviewed by admin before going live again.</p>
            </div>
        </div>

        @if(session('error'))
        <div style="background:#FFEBEE;color:#C62828;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-weight:600;font-size:13.5px">{{ session('error') }}</div>
        @endif
        @if($errors->any())
        <div style="background:#FFEBEE;color:#C62828;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13.5px">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
        @endif

        <div style="display:flex;align-items:center;gap:14px;background:#f8fafc;border:1.5px solid #e5e8ef;border-radius:12px;padding:14px 16px;margin-bottom:18px">
            @php $img = optional(optional($deal->listing)->images->first())->path; @endphp
            <div style="width:56px;height:56px;border-radius:10px;overflow:hidden;background:#eef1f6;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:22px">
                @if($img)<img src="{{ asset('storage/'.$img) }}" alt="" style="width:100%;height:100%;object-fit:cover">@else 🛍️ @endif
            </div>
            <div>
                <b style="font-size:14px">{{ $deal->listing->title ?? 'Deleted listing' }}</b>
                <div style="font-size:12.5px;color:#667085">Original price: LKR {{ number_format($deal->original_price ?? 0) }} · Current status: {{ ucfirst($deal->status) }}</div>
            </div>
        </div>

        <form class="kd-form" method="post" action="/dashboard/deals/{{ $deal->id }}">
            @csrf
            @method('PUT')
            <div class="kd-form-grid">
                <label>Deal Price (LKR)
                    <input name="deal_price" type="number" step="0.01" min="1" inputmode="decimal" value="{{ old('deal_price', $deal->deal_price) }}" required>
                    <small>Must be lower than LKR {{ number_format($deal->original_price ?? 0) }}.</small>
                </label>
                <label>Stock Quantity (optional)
                    <input name="stock_qty" type="number" min="1" inputmode="numeric" value="{{ old('stock_qty', $deal->stock_qty) }}" placeholder="Unlimited if empty">
                </label>
                <label>Starts At
                    <input name="starts_at" type="date" value="{{ old('starts_at', optional($deal->starts_at)->format('Y-m-d')) }}" required>
                </label>
                <label>Ends At
                    <input name="ends_at" type="date" value="{{ old('ends_at', optional($deal->ends_at)->format('Y-m-d')) }}" required>
                </label>
            </div>

            <label style="display:flex;align-items:center;gap:8px;margin:12px 0;cursor:pointer">
                <input type="checkbox" name="is_flash" value="1" @checked(old('is_flash', $deal->is_flash))> ⚡ Request Flash Deal placement
            </label>

            <div style="background:#fff3e0;border-radius:10px;padding:12px 16px;margin:14px 0;font-size:13px;color:#e65100">
                Saving changes will send this deal back for admin approval before it appears publicly again.
            </div>

            <button class="kd-primary" type="submit">Save Changes</button>
        </form>
    </div>
</section>
@endsection
