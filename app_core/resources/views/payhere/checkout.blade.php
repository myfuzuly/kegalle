@extends('layouts.app')
@section('title', 'Complete Payment · Kegalle Marketplace')

@section('content')
<section class="ks-72a4ea">
<div class="ks-f0da59">

<div class="ks-d592e9">
    <div class="ks-078ee5">💳</div>
    <h1 class="ks-158a20">{{ $label }}</h1>
    <div class="ks-d42a1c">LKR {{ number_format($amount, 2) }}</div>
</div>

<div class="ks-e95d0e">
    <div class="ks-de4fa9">
        <span class="ks-eafb47">Plan</span><strong>{{ $label }}</strong>
    </div>
    <div class="ks-de4fa9">
        <span class="ks-eafb47">Billing</span><strong>Monthly</strong>
    </div>
    <div class="ks-de4fa9">
        <span class="ks-eafb47">Order ID</span><strong class="ks-293313">{{ $orderId }}</strong>
    </div>
    <div class="ks-c51ddd">
        <span>Total</span><span class="ks-7ad905">LKR {{ number_format($amount, 2) }}</span>
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

    <button type="submit" class="k-btn k-btn-primary ks-c34069">
        Pay LKR {{ number_format($amount, 2) }} with PayHere →
    </button>
</form>

<p class="ks-3a1b86">
    Secured by <strong>PayHere</strong> — Sri Lanka's trusted payment gateway.<br>
    <a href="/dashboard/membership" class="ks-eafb47">← Back to plans</a>
</p>

</div>
</section>
@endsection
