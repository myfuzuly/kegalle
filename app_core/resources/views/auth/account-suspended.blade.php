@extends('layouts.app')

@section('title','Account Suspended · Kegalle Marketplace')

@section('content')

<section class="kg-auth-page">
    <div class="kg-auth-shell">
        <div class="kg-auth-left">
            <span class="kg-auth-tag">Account Status</span>
            <h1>Your account has been suspended.</h1>
            <p>Your dashboard access has been temporarily suspended by our team.</p>
        </div>

        <div class="kg-auth-card">
            <div class="kg-auth-head">
                <h2>Need help?</h2>
                <p>If you believe this is a mistake, please get in touch with our support team.</p>
            </div>

            <a class="kg-btn kg-btn-primary kg-auth-btn" href="/contact-us">Contact Support</a>
        </div>
    </div>
</section>

@endsection
