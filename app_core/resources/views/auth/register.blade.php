@extends('layouts.app')
@section('title','Create Account · Kegalle Marketplace')
@section('meta_description','Create a free Kegalle Marketplace account to start buying, selling, or listing your store in the Kegalle district today.')
@section('content')
<div class="k-auth-page">
    <div class="k-auth-main">
        <div class="k-auth-container" style="max-width:1020px">
            <div class="k-auth-left">
                <div>
                    <h2>Join Kegalle <span>Marketplace</span></h2>
                    <p>Create your account and unlock a world of great deals, trusted sellers and endless opportunities.</p>
                    <div class="k-auth-feature">
                        <div class="k-auth-feature-icon">✅</div>
                        <div class="k-auth-feature-text"><strong>Buy with Confidence</strong><span>Shop from verified sellers and quality products.</span></div>
                    </div>
                    <div class="k-auth-feature">
                        <div class="k-auth-feature-icon">📢</div>
                        <div class="k-auth-feature-text"><strong>Sell or Promote Easily</strong><span>List your products or services and reach thousands.</span></div>
                    </div>
                    <div class="k-auth-feature">
                        <div class="k-auth-feature-icon">🔒</div>
                        <div class="k-auth-feature-text"><strong>Secure & Safe</strong><span>Your privacy and security are our top priority.</span></div>
                    </div>
                    <div class="k-auth-feature">
                        <div class="k-auth-feature-icon">🎧</div>
                        <div class="k-auth-feature-text"><strong>24/7 Support</strong><span>We're here to help you anytime, anywhere.</span></div>
                    </div>
                </div>
                <div class="k-auth-visual">🛒💻📱</div>
            </div>
            <div class="k-auth-right">
                <h3>Create Your Account</h3>
                <p class="subtitle">It's quick and easy.</p>

                @if($errors->any())
                    <div class="k-alert" style="background:var(--k-red-light);color:var(--k-red);padding:12px 16px;border-radius:var(--k-radius);margin-bottom:16px;font-size:13px">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('register.submit') }}">
                    @csrf
                    <div style="margin-bottom:16px">
                        <label class="k-form-label">I want to join as</label>
                        <div class="k-account-types">
                            <label class="k-account-type {{ old('account_type', request('account_type','store')) === 'store' ? 'selected' : '' }}" style="cursor:pointer;display:block">
                                <input type="radio" name="account_type" value="store" style="display:none" {{ old('account_type', request('account_type','store')) === 'store' ? 'checked' : '' }}>
                                <div class="k-account-type-radio"></div>
                                <div class="k-account-type-icon">🏪</div>
                                <div class="k-account-type-name">Store / Business</div>
                                <div class="k-account-type-desc">Sell products or services with your store</div>
                            </label>
                            <label class="k-account-type {{ old('account_type', request('account_type')) === 'user' ? 'selected' : '' }}" style="cursor:pointer;display:block">
                                <input type="radio" name="account_type" value="user" style="display:none" {{ old('account_type', request('account_type')) === 'user' ? 'checked' : '' }}>
                                <div class="k-account-type-radio"></div>
                                <div class="k-account-type-icon">👤</div>
                                <div class="k-account-type-name">Personal / Classified</div>
                                <div class="k-account-type-desc">Post ads for personal use or services</div>
                            </label>
                        </div>
                    </div>

                    <div class="k-divider"><span>or continue with</span></div>
                    <div class="k-social-grid" style="margin-bottom:16px">
                        <a href="/auth/google/redirect" class="k-social-btn" style="text-decoration:none">G Continue with Google</a>
                        <a href="/auth/facebook/redirect" class="k-social-btn" style="text-decoration:none">f Continue with Facebook</a>
                    </div>

                    <div class="k-form-grid">
                        <div class="k-form-group">
                            <label class="k-form-label" for="register-name">Full Name</label>
                            <div class="k-form-icon-wrap">
                                <span class="k-form-icon">👤</span>
                                <input id="register-name" type="text" name="name" value="{{ old('name') }}" class="k-form-control with-icon" placeholder="Enter your full name" required>
                            </div>
                        </div>
                        <div class="k-form-group">
                            <label class="k-form-label" for="register-email">Email Address</label>
                            <div class="k-form-icon-wrap">
                                <span class="k-form-icon">✉</span>
                                <input id="register-email" type="email" name="email" value="{{ old('email') }}" class="k-form-control with-icon" placeholder="Enter your email" required>
                            </div>
                        </div>
                        <div class="k-form-group">
                            <label class="k-form-label" for="register-phone">Phone Number</label>
                            <div class="k-form-icon-wrap">
                                <span class="k-form-icon">📞</span>
                                <input id="register-phone" type="text" name="phone" value="{{ old('phone') }}" class="k-form-control with-icon" placeholder="Enter your phone number" required>
                            </div>
                        </div>
                        <div class="k-form-group">
                            <label class="k-form-label" for="register-location">Location</label>
                            <select id="register-location" name="location_id" class="k-form-control" required>
                                <option value="">Select location</option>
                                @foreach(($locations ?? []) as $location)
                                    <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="k-form-group">
                            <label class="k-form-label" for="register-password">Password</label>
                            <div class="k-form-icon-wrap">
                                <span class="k-form-icon">🔒</span>
                                <input id="register-password" type="password" name="password" class="k-form-control with-icon" placeholder="Create a password" required>
                            </div>
                        </div>
                        <div class="k-form-group">
                            <label class="k-form-label" for="register-password-confirmation">Confirm Password</label>
                            <div class="k-form-icon-wrap">
                                <span class="k-form-icon">🔒</span>
                                <input id="register-password-confirmation" type="password" name="password_confirmation" class="k-form-control with-icon" placeholder="Confirm your password" required>
                            </div>
                        </div>
                    </div>

                    <div style="display:flex;align-items:flex-start;gap:8px;margin:16px 0">
                        <input id="register-terms" type="checkbox" required style="accent-color:var(--k-primary);margin-top:2px">
                        <label for="register-terms" style="font-size:13px;color:var(--k-text-secondary);cursor:pointer">I agree to the <a href="/terms-and-conditions" style="color:var(--k-primary);font-weight:600;text-decoration:none">Terms & Conditions</a> and <a href="/privacy-policy" style="color:var(--k-primary);font-weight:600;text-decoration:none">Privacy Policy</a></label>
                    </div>
                    <button type="submit" class="k-btn k-btn-primary k-btn-lg w-full" style="justify-content:center;margin-bottom:12px;border:none;cursor:pointer">Create Account</button>
                </form>
                <p style="text-align:center;font-size:13px;color:var(--k-text-secondary)">Already have an account? <a href="/login" style="color:var(--k-primary);font-weight:700;text-decoration:none">Login</a></p>
            </div>
        </div>
    </div>
    <div class="k-auth-footer-bar">
        <div class="k-auth-trust"><div class="k-auth-trust-icon">🛡️</div><div class="k-auth-trust-text"><strong>Trusted Community</strong><span>Join a community of verified users</span></div></div>
        <div class="k-auth-trust"><div class="k-auth-trust-icon">🏷️</div><div class="k-auth-trust-text"><strong>Best Deals</strong><span>Find amazing deals every day</span></div></div>
        <div class="k-auth-trust"><div class="k-auth-trust-icon">📦</div><div class="k-auth-trust-text"><strong>Wide Categories</strong><span>Explore thousands of products & services</span></div></div>
        <div class="k-auth-trust"><div class="k-auth-trust-icon">🔒</div><div class="k-auth-trust-text"><strong>Secure Payments</strong><span>Safe and secure transactions</span></div></div>
    </div>
</div>

<script>
document.querySelectorAll('.k-account-type input[type=radio]').forEach(function(r){
    r.addEventListener('change', function(){
        document.querySelectorAll('.k-account-type').forEach(function(el){ el.classList.remove('selected'); });
        r.closest('.k-account-type').classList.add('selected');
    });
});
</script>
@endsection
