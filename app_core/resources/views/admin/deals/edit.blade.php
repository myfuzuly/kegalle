@extends('layouts.admin')
@section('title','Edit Deal')
@section('page','Deals')
@section('heading','Edit Deal #{{ $deal->id }}')
@section('subheading','Adjust pricing, schedule and status for this deal')
@section('actions')<a class="ka-btn ka-btn-light" href="/admin/deals">Back</a>@endsection
@section('content')

@if(session('error'))
<div style="background:#FFEBEE;color:#C62828;padding:12px 18px;border-radius:12px;margin-bottom:18px;font-weight:600;font-size:13px">{{ session('error') }}</div>
@endif

<section class="sa-card">
<div class="sa-card-head">
    <h2>{{ $deal->listing->title ?? 'Deleted listing' }}</h2>
    <span>Original price: LKR {{ number_format($deal->original_price ?? 0) }} · Seller: {{ $deal->user->name ?? '—' }}</span>
</div>
<form class="ka-premium-form" method="post" action="/admin/deals/{{ $deal->id }}">
@csrf
@method('PUT')
<div class="ka-form-grid">
    <div class="ka-field">
        <label>Deal Price (LKR)</label>
        <input name="deal_price" type="number" step="0.01" min="1" value="{{ old('deal_price', $deal->deal_price) }}" required>
        <small>Must be lower than the original price (LKR {{ number_format($deal->original_price ?? 0) }}).</small>
    </div>
    <div class="ka-field">
        <label>Stock Quantity</label>
        <input name="stock_qty" type="number" min="1" value="{{ old('stock_qty', $deal->stock_qty) }}" placeholder="Unlimited if empty">
    </div>
    <div class="ka-field">
        <label>Starts At</label>
        <input name="starts_at" type="date" value="{{ old('starts_at', optional($deal->starts_at)->format('Y-m-d')) }}" required>
    </div>
    <div class="ka-field">
        <label>Ends At</label>
        <input name="ends_at" type="date" value="{{ old('ends_at', optional($deal->ends_at)->format('Y-m-d')) }}" required>
    </div>
    <div class="ka-field">
        <label>Status</label>
        <select name="status" required>
            <option value="pending" @selected(old('status', $deal->status)==='pending')>Pending</option>
            <option value="approved" @selected(old('status', $deal->status)==='approved')>Approved</option>
            <option value="rejected" @selected(old('status', $deal->status)==='rejected')>Rejected</option>
            <option value="expired" @selected(old('status', $deal->status)==='expired')>Expired</option>
        </select>
    </div>
    <div class="ka-field">
        <label>Admin Note</label>
        <input name="admin_note" value="{{ old('admin_note', $deal->admin_note) }}" placeholder="Visible to the seller">
    </div>
    <label class="ka-check"><input type="checkbox" name="is_flash" value="1" @checked(old('is_flash', $deal->is_flash))> ⚡ Flash Deal</label>
    <label class="ka-check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $deal->is_featured))> ⭐ Featured Deal</label>
</div>
<div class="ka-form-actions">
    <button class="ka-btn ka-btn-primary">Save Changes</button>
    <a class="ka-btn ka-btn-light" href="/admin/deals">Cancel</a>
</div>
</form>
</section>
@endsection
