@extends('layouts.app')
@section('title','Login · Kegalle Marketplace')
@section('content')
<div class="k-auth-page">
    <div class="k-auth-main">
        <div class="k-auth-container">
            <div class="k-auth-left">
                <div>
                    <h2>Welcome Back to <span>Kegalle</span> Marketplace</h2>
                    <p>Login to your account and continue exploring thousands of great deals from trusted sellers.</p>
                    <div class="k-auth-feature">
                        <div class="k-auth-feature-icon">🛍️</div>
                        <div class="k-auth-feature-text"><strong>Discover Amazing Deals</strong><span>Find the best products and services in your area.</span></div>
                    </div>
                    <div class="k-auth-feature">
                        <div class="k-auth-feature-icon">🔒</div>
                        <div class="k-auth-feature-text"><strong>Safe & Secure</strong><span>We ensure a secure experience for all our users.</span></div>
                    </div>
                    <div class="k-auth-feature">
                        <div class="k-auth-feature-icon">👥</div>
                        <div class="k-auth-feature-text"><strong>Trusted Community</strong><span>Connect with verified sellers and happy buyers.</span></div>
                    </div>
                </div>
                <div class="k-auth-visual">🛋️📱🏠</div>
            </div>
            <div class="k-auth-right">
                <div style="text-align:center;margin-bottom:24px">
                    <div style="width:56px;height:56px;background:var(--k-primary-xlight);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 14px">🔐</div>
                    <h3>Login to Your Account</h3>
                    <p class="subtitle">Glad to see you again!</p>
                </div>

                @if(session('success'))
                    <div class="k-alert" style="background:var(--k-primary-xlight);color:var(--k-primary-hover);padding:12px 16px;border-radius:var(--k-radius);margin-bottom:16px;font-size:13px">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="k-alert" style="background:var(--k-red-light);color:var(--k-red);padding:12px 16px;border-radius:var(--k-radius);margin-bottom:16px;font-size:13px">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    <div class="k-form-group">
                        <label class="k-form-label" for="login-email">Email Address</label>
                        <div class="k-form-icon-wrap">
                            <span class="k-form-icon">✉</span>
                            <input id="login-email" type="email" name="email" value="{{ old('email') }}" class="k-form-control with-icon" placeholder="Enter your email" required>
                        </div>
                    </div>
                    <div class="k-form-group">
                        <label class="k-form-label" for="login-password">Password</label>
                        <div class="k-form-icon-wrap">
                            <span class="k-form-icon">🔒</span>
                            <input id="login-password" type="password" name="password" class="k-form-control with-icon" placeholder="Enter your password" required>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
                        <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer">
                            <input type="checkbox" name="remember" style="accent-color:var(--k-primary)"> Remember me
                        </label>
                        <a href="#" style="font-size:13px;color:var(--k-primary);font-weight:600;text-decoration:none">Forgot Password?</a>
                    </div>
                    <button type="submit" class="k-btn k-btn-primary k-btn-lg w-full" style="justify-content:center;margin-bottom:16px;border:none;cursor:pointer">Login</button>
                </form>

                <div class="k-divider"><span>or continue with</span></div>
                <div class="k-social-grid">
                    <a href="/auth/google/redirect" class="k-social-btn" style="text-decoration:none"><span>G</span> Continue with Google</a>
                    <a href="/auth/facebook/redirect" class="k-social-btn" style="text-decoration:none"><span>f</span> Continue with Facebook</a>
                </div>
                <p style="text-align:center;font-size:13px;color:var(--k-text-secondary)">Don't have an account? <a href="/register" style="color:var(--k-primary);font-weight:700;text-decoration:none">Sign up</a></p>
            </div>
        </div>
    </div>
    <div class="k-auth-footer-bar">
        <div class="k-auth-trust"><div class="k-auth-trust-icon">🔒</div><div class="k-auth-trust-text"><strong>100% Secure</strong><span>Your data is protected with top security</span></div></div>
        <div class="k-auth-trust"><div class="k-auth-trust-icon">🎧</div><div class="k-auth-trust-text"><strong>24/7 Support</strong><span>We're here to help you anytime</span></div></div>
        <div class="k-auth-trust"><div class="k-auth-trust-icon">🏷️</div><div class="k-auth-trust-text"><strong>Best Deals</strong><span>Find amazing deals every day</span></div></div>
        <div class="k-auth-trust"><div class="k-auth-trust-icon">⭐</div><div class="k-auth-trust-text"><strong>Trusted Platform</strong><span>Verified sellers and safe transactions</span></div></div>
    </div>
</div>
@endsection
