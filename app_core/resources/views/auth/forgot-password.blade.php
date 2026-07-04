@extends('layouts.app')
@section('title','Forgot Password · Kegalle Marketplace')
@section('meta_description','Reset your Kegalle Marketplace account password.')
@section('content')
<div class="k-auth-page">
    <div class="k-auth-main">
        <div class="k-auth-container">
            <div class="k-auth-left">
                <div>
                    <h2>Reset your <span>Kegalle</span> password</h2>
                    <p>Enter your email address and we'll send you a link to reset your password.</p>
                    <div class="k-auth-feature">
                        <div class="k-auth-feature-icon">🔒</div>
                        <div class="k-auth-feature-text"><strong>Secure Reset</strong><span>We'll send a one-time link to your registered email.</span></div>
                    </div>
                    <div class="k-auth-feature">
                        <div class="k-auth-feature-icon">⏱️</div>
                        <div class="k-auth-feature-text"><strong>Quick Process</strong><span>Reset your password in under a minute.</span></div>
                    </div>
                </div>
                <div class="k-auth-visual">🔑📧✅</div>
            </div>
            <div class="k-auth-right">
                <div style="text-align:center;margin-bottom:24px">
                    <div style="width:56px;height:56px;background:var(--k-primary-xlight);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 14px">🔑</div>
                    <h3>Forgot Password?</h3>
                    <p class="subtitle">No worries, we'll send you reset instructions.</p>
                </div>

                @if(session('success'))
                    <div class="k-alert" style="background:var(--k-primary-xlight);color:var(--k-primary-hover);padding:12px 16px;border-radius:var(--k-radius);margin-bottom:16px;font-size:13px">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="k-alert" style="background:var(--k-red-light);color:var(--k-red);padding:12px 16px;border-radius:var(--k-radius);margin-bottom:16px;font-size:13px">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="k-form-group">
                        <label class="k-form-label" for="reset-email">Email Address</label>
                        <div class="k-form-icon-wrap">
                            <span class="k-form-icon">✉</span>
                            <input id="reset-email" type="email" name="email" value="{{ old('email') }}" class="k-form-control with-icon" placeholder="Enter your registered email" required autofocus>
                        </div>
                    </div>
                    <button type="submit" class="k-btn k-btn-primary k-btn-lg w-full" style="justify-content:center;margin-bottom:16px;border:none;cursor:pointer">Send Reset Link</button>
                </form>

                <p style="text-align:center;font-size:13px;color:var(--k-text-secondary)"><a href="/login" style="color:var(--k-primary);font-weight:700;text-decoration:none">← Back to Login</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
