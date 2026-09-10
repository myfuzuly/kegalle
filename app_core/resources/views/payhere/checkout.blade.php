@extends('layouts.app')
@section('title', 'Complete Payment · Kegalle Marketplace')

@section('content')
<section style="min-height:55vh;display:flex;align-items:center;justify-content:center;padding:40px 20px">
<div style="width:100%;max-width:420px">

<div style="text-align:center;margin-bottom:28px">
    <div style="font-size:48px;margin-bottom:10px">💳</div>
    <h1 style="font-size:22px;font-weight:800;margin-bottom:6px">{{ $label }}</h1>
    <div style="font-size:28px;font-weight:800;color:var(--k-primary,#1b5e20)">LKR {{ number_format($amount, 2) }}</div>
</div>

<div style="background:var(--k-surface);border:1px solid var(--k-border);border-radius:16px;padding:24px;margin-bottom:20px">
    <div style="display:flex;justify-content:space-between;font-size:14px;padding:8px 0;border-bottom:1px solid var(--k-border)">
        <span style="color:var(--k-text-secondary)">Plan</span><strong>{{ $label }}</strong>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:14px;padding:8px 0;border-bottom:1px solid var(--k-border)">
        <span style="color:var(--k-text-secondary)">Billing</span><strong>Monthly</strong>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:14px;padding:8px 0;border-bottom:1px solid var(--k-border)">
        <span style="color:var(--k-text-secondary)">Order ID</span><strong style="font-family:monospace;font-size:12px">{{ $orderId }}</strong>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:16px;padding:12px 0;font-weight:700">
        <span>Total</span><span style="color:var(--k-primary,#1b5e20)">LKR {{ number_format($amount, 2) }}</span>
    </div>
</div>

<form method="POST" action="{{ app(\App\Services\PayHereService::class)->checkoutUrl() }}" id="payhere-form">
    <input type="hidden" name="merchant_id"  value="{{ app(\App\Services\PayHereService::class)->merchantId() }}">
    <input type="hidden" name="return_url"   value="{{ url('/payhere/return') }}">
    <input type="hidden" name="cancel_url"   value="{{ url('/payhere/cancel') }}">
    <input type="hidden" name="notify_url"   value="{{ url('/payhere/notify') }}">
    <input type="hidden" name="order_id"     value="{{ $orderId }}">
    <input type="hidden" name="items"        value="{{ $label }}">
    <input type="hidden" name="currency"     value="LKR">
    <input type="hidden" name="amount"       value="{{ number_format($amount, 2, '.', '') }}">
    <input type="hidden" name="hash"         value="{{ $hash }}">
    <input type="hidden" name="first_name"   value="{{ explode(' ', $user->name ?? 'User')[0] }}">
    <input type="hidden" name="last_name"    value="{{ implode(' ', array_slice(explode(' ', $user->name ?? ''), 1)) ?: '-' }}">
    <input type="hidden" name="email"        value="{{ $user->email ?? '' }}">
    <input type="hidden" name="phone"        value="{{ $user->phone ?? '0700000000' }}">
    <input type="hidden" name="address"      value="Kegalle">
    <input type="hidden" name="city"         value="Kegalle">
    <input type="hidden" name="country"      value="Sri Lanka">

    <button type="submit" class="k-btn k-btn-primary" style="width:100%;padding:16px;font-size:16px;font-weight:700;border-radius:12px">
        Pay LKR {{ number_format($amount, 2) }} with PayHere →
    </button>
</form>

<p style="text-align:center;font-size:12px;color:var(--k-text-tertiary);margin-top:14px">
    Secured by <strong>PayHere</strong> — Sri Lanka's trusted payment gateway.<br>
    <a href="/dashboard/membership" style="color:var(--k-text-secondary)">← Back to plans</a>
</p>

</div>
</section>
@endsection
