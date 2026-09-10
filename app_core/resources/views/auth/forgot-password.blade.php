@extends('layouts.app')
@section('title','Forgot Password · Kegalle Marketplace')
@section('meta_description','Reset your Kegalle Marketplace account password. Enter your email and we will send you a secure reset link.')
@section('content')
<div class="k-auth-page">
    <div class="k-auth-main">
        <div class="k-auth-container">
            <div class="k-auth-left">
                <div>
                    <div class="k-auth-brand">
                        <img src="/images/kegalle-logo.png" alt="Kegalle Marketplace" class="k-auth-logo">
                    </div>
                    <h2>Reset your <span>Kegalle</span> password</h2>
                    <p>Enter your email address and we'll send you a secure link to choose a new password.</p>
                    <div class="k-auth-features">
                        <div class="k-auth-feature">
                            <div class="k-auth-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </div>
                            <div class="k-auth-feature-text"><strong>Secure Reset Link</strong><span>We send a one-time link valid for 60 minutes — no one else can use it.</span></div>
                        </div>
                        <div class="k-auth-feature">
                            <div class="k-auth-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            </div>
                            <div class="k-auth-feature-text"><strong>Check Your Inbox</strong><span>The reset email arrives within a minute. Check spam if you don't see it.</span></div>
                        </div>
                        <div class="k-auth-feature">
                            <div class="k-auth-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            </div>
                            <div class="k-auth-feature-text"><strong>Back in Seconds</strong><span>Reset your password and get back to buying and selling in Kegalle.</span></div>
                        </div>
                    </div>
                </div>
                <div class="k-auth-visual">
                    <svg viewBox="0 0 260 160" fill="none" xmlns="http://www.w3.org/2000/svg" class="k-auth-visual-svg">
                        <rect x="60" y="20" width="140" height="100" rx="12" fill="rgba(255,255,255,0.10)" stroke="rgba(255,255,255,0.20)" stroke-width="1.5"/>
                        <rect x="80" y="40" width="100" height="8" rx="4" fill="rgba(255,255,255,0.4)"/>
                        <rect x="80" y="56" width="70" height="6" rx="3" fill="rgba(255,255,255,0.2)"/>
                        <rect x="80" y="74" width="100" height="28" rx="6" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.18)" stroke-width="1"/>
                        <circle cx="96" cy="88" r="7" fill="rgba(0,200,83,0.35)" stroke="rgba(0,200,83,0.6)" stroke-width="1.2"/>
                        <path d="M93 88l2 2 4-4" stroke="#00C853" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <rect x="108" y="85" width="58" height="6" rx="3" fill="rgba(255,255,255,0.25)"/>
                        <rect x="80" y="112" width="100" height="0" rx="0"/>
                        <rect x="100" y="128" width="60" height="14" rx="7" fill="rgba(0,200,83,0.7)"/>
                        <rect x="110" y="133" width="40" height="4" rx="2" fill="rgba(255,255,255,0.8)"/>
                    </svg>
                    <div class="k-auth-visual-label">Kegalle's #1 local marketplace</div>
                </div>
            </div>
            <div class="k-auth-right">
                <div class="k-auth-right-header">
                    <div class="k-auth-right-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <h3>Forgot Password?</h3>
                    <p class="subtitle">No worries — we'll send you reset instructions.</p>
                </div>

                @if(session('success'))
                    <div class="k-alert k-alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="k-alert k-alert-error">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="k-form-group">
                        <label class="k-form-label" for="reset-email">Email Address</label>
                        <div class="k-form-icon-wrap">
                            <span class="k-form-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
                            <input id="reset-email" type="email" name="email" value="{{ old('email') }}" class="k-form-control with-icon" placeholder="your@email.com" required autofocus>
                        </div>
                    </div>
                    <button type="submit" class="k-btn k-btn-primary k-btn-lg w-full k-auth-submit">Send Reset Link</button>
                </form>

                <p class="k-auth-login-link"><a href="/login" class="k-link k-link-bold">← Back to Login</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
