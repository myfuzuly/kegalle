@extends('layouts.app')
@section('title','Reset Password · Kegalle Marketplace')
@section('content')
<div class="k-auth-page">
    <div class="k-auth-main">
        <div class="k-auth-container">
            <div class="k-auth-left">
                <div>
                    <h2>Set your new <span>Kegalle</span> password</h2>
                    <p>Choose a strong password to keep your account secure.</p>
                    <div class="k-auth-feature">
                        <div class="k-auth-feature-icon">🔒</div>
                        <div class="k-auth-feature-text"><strong>Strong Password</strong><span>Use at least 8 characters with mixed case and numbers.</span></div>
                    </div>
                </div>
                <div class="k-auth-visual">🔐✅🎉</div>
            </div>
            <div class="k-auth-right">
                <div style="text-align:center;margin-bottom:24px">
                    <div style="width:56px;height:56px;background:var(--k-primary-xlight);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 14px">🔐</div>
                    <h3>Set New Password</h3>
                    <p class="subtitle">Enter your new password below.</p>
                </div>

                @if($errors->any())
                    <div class="k-alert" style="background:var(--k-red-light);color:var(--k-red);padding:12px 16px;border-radius:var(--k-radius);margin-bottom:16px;font-size:13px">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">
                    <div class="k-form-group">
                        <label class="k-form-label" for="new-password">New Password</label>
                        <div class="k-form-icon-wrap">
                            <span class="k-form-icon">🔒</span>
                            <input id="new-password" type="password" name="password" class="k-form-control with-icon" placeholder="Enter new password" required>
                        </div>
                    </div>
                    <div class="k-form-group">
                        <label class="k-form-label" for="confirm-password">Confirm Password</label>
                        <div class="k-form-icon-wrap">
                            <span class="k-form-icon">🔒</span>
                            <input id="confirm-password" type="password" name="password_confirmation" class="k-form-control with-icon" placeholder="Confirm new password" required>
                        </div>
                    </div>
                    <button type="submit" class="k-btn k-btn-primary k-btn-lg w-full" style="justify-content:center;margin-bottom:16px;border:none;cursor:pointer">Reset Password</button>
                </form>

                <p style="text-align:center;font-size:13px;color:var(--k-text-secondary)"><a href="/login" style="color:var(--k-primary);font-weight:700;text-decoration:none">← Back to Login</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
