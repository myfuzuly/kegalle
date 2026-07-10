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
                    <div style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden" aria-hidden="true">
                        <input type="text" name="website" id="website" tabindex="-1" autocomplete="off" aria-label="Do not fill this field">
                    </div>
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
                        <div class="k-form-group k-store-field" style="{{ old('account_type', request('account_type','store')) === 'store' ? '' : 'display:none' }}">
                            <label class="k-form-label" for="register-store-name">Store Name</label>
                            <div class="k-form-icon-wrap">
                                <span class="k-form-icon">🏪</span>
                                <input id="register-store-name" type="text" name="store_name" value="{{ old('store_name') }}" class="k-form-control with-icon" placeholder="Enter your store or business name">
                            </div>
                        </div>
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
                                <input id="register-password" type="password" name="password" class="k-form-control with-icon" placeholder="Create a password" required minlength="8">
                            </div>
                            <div id="pw-strength" style="margin-top:6px;display:none">
                                <div style="display:flex;gap:3px;margin-bottom:4px">
                                    <div class="pw-bar" style="flex:1;height:4px;border-radius:2px;background:#e2e8f0"></div>
                                    <div class="pw-bar" style="flex:1;height:4px;border-radius:2px;background:#e2e8f0"></div>
                                    <div class="pw-bar" style="flex:1;height:4px;border-radius:2px;background:#e2e8f0"></div>
                                    <div class="pw-bar" style="flex:1;height:4px;border-radius:2px;background:#e2e8f0"></div>
                                </div>
                                <small id="pw-label" style="font-size:11px;color:#94a3b8">Min 8 characters</small>
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
        <div class="k-auth-trust"><div class="k-auth-trust-icon">🔒</div><div class="k-auth-trust-text"><strong>Secure & Private</strong><span>Your data is safe with us</span></div></div>
    </div>
</div>

<script>
(function(){
    var pw=document.getElementById('register-password'),box=document.getElementById('pw-strength'),bars=box?box.querySelectorAll('.pw-bar'):[],lbl=document.getElementById('pw-label');
    var colors=['#ef4444','#f59e0b','#22c55e','#059669'],labels=['Weak','Fair','Good','Strong'];
    if(pw&&box){pw.addEventListener('input',function(){
        var v=pw.value,s=0;
        if(!v){box.style.display='none';return;}
        box.style.display='block';
        if(v.length>=8)s++;if(v.length>=12)s++;if(/[A-Z]/.test(v)&&/[a-z]/.test(v))s++;if(/[0-9]/.test(v)||/[^a-zA-Z0-9]/.test(v))s++;
        s=Math.min(s,4);
        for(var i=0;i<4;i++){bars[i].style.background=i<s?colors[Math.min(s-1,3)]:'#e2e8f0';}
        lbl.textContent=s===0?'Min 8 characters':labels[s-1];lbl.style.color=colors[Math.min(s-1,3)];
    });}
})();
document.querySelectorAll('.k-account-type input[type=radio]').forEach(function(r){
    r.addEventListener('change', function(){
        document.querySelectorAll('.k-account-type').forEach(function(el){ el.classList.remove('selected'); });
        r.closest('.k-account-type').classList.add('selected');
        document.querySelectorAll('.k-store-field').forEach(function(el){
            el.style.display = r.value === 'store' ? '' : 'none';
        });
    });
});
</script>
@endsection
