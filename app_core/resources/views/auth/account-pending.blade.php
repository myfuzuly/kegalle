@extends('layouts.app')

@section('title','Account Pending Approval · Kegalle Marketplace')

@section('content')

<section class="kg-auth-page">
    <div class="kg-auth-shell">
        <div class="kg-auth-left">
            <span class="kg-auth-tag">Account Status</span>
            <h1>Your account is pending approval.</h1>
            <p>Thanks for joining Kegalle Marketplace. Our team reviews every new account before activating dashboard access — this usually doesn't take long.</p>
        </div>

        <div class="kg-auth-card">
            <div class="kg-auth-head">
                <h2>Almost there</h2>
                <p>You're logged in, but your account hasn't been activated by our team yet. You'll be able to post ads and access your dashboard once it's approved.</p>
            </div>

            <a class="kg-btn kg-btn-primary kg-auth-btn" href="/">Back to Homepage</a>
        </div>
    </div>
</section>

@endsection
