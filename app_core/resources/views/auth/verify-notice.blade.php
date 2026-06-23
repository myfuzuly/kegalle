@extends('layouts.app')

@section('title','Verify Email · Kegalle Marketplace')

@section('content')

<section class="kg-auth-page">
    <div class="kg-auth-shell">
        <div class="kg-auth-left">
            <span class="kg-auth-tag">Email Verification</span>
            <h1>Check your inbox.</h1>
            <p>We sent a verification link to your email address. Please verify before using your dashboard.</p>
        </div>

        <div class="kg-auth-card">
            <div class="kg-auth-head">
                <h2>Verify your email</h2>
                <p>Did not receive the email? Send it again.</p>
            </div>

            @if(session('success'))
                <div class="kg-alert">{{ session('success') }}</div>
            @endif

            <form method="POST" action="/email/resend">
                @csrf
                <button class="kg-btn kg-btn-primary kg-auth-btn" type="submit">Resend Verification Email</button>
            </form>
        </div>
    </div>
</section>

@endsection
