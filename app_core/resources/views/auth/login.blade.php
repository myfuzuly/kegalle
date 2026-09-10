@extends('layouts.app')
@section('title','Login · Kegalle Marketplace')
@section('meta_description','Log in to your Kegalle Marketplace account to manage your listings, stores, and messages.')
@section('content')
<div class="k-auth-page">
    <div class="k-auth-main">
        <div class="k-auth-container">
            <div class="k-auth-left">
                <div>
                    <div class="k-auth-brand">
                        <img src="/images/kegalle-logo.png" alt="Kegalle Marketplace" class="k-auth-logo">
                    </div>
                    <h2>Welcome Back to <span>Kegalle</span> Marketplace</h2>
                    <p>Login to your account and continue exploring thousands of great deals from trusted sellers.</p>
                    <div class="k-auth-features">
                        <div class="k-auth-feature">
                            <div class="k-auth-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            </div>
                            <div class="k-auth-feature-text"><strong>Kegalle District Only</strong><span>Every seller and buyer is based right here — no nationwide noise.</span></div>
                        </div>
                        <div class="k-auth-feature">
                            <div class="k-auth-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </div>
                            <div class="k-auth-feature-text"><strong>Meet in Person</strong><span>Inspect before you pay — real deals happen face to face.</span></div>
                        </div>
                        <div class="k-auth-feature">
                            <div class="k-auth-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                            </div>
                            <div class="k-auth-feature-text"><strong>Free to Post & Browse</strong><span>List your ad or find what you need at no cost.</span></div>
                        </div>
                    </div>
                </div>
                <div class="k-auth-visual">
                    <svg viewBox="0 0 260 160" fill="none" xmlns="http://www.w3.org/2000/svg" class="k-auth-visual-svg">
                        <rect x="10" y="30" width="100" height="70" rx="10" fill="rgba(255,255,255,0.10)" stroke="rgba(255,255,255,0.20)" stroke-width="1.5"/>
                        <rect x="150" y="20" width="100" height="80" rx="10" fill="rgba(255,255,255,0.10)" stroke="rgba(255,255,255,0.20)" stroke-width="1.5"/>
                        <rect x="70" y="95" width="120" height="55" rx="10" fill="rgba(0,200,83,0.18)" stroke="rgba(0,200,83,0.35)" stroke-width="1.5"/>
                        <circle cx="30" cy="50" r="10" fill="rgba(0,200,83,0.3)"/>
                        <path d="M26 50l3 3 5-5" stroke="#00C853" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <rect x="46" y="45" width="50" height="5" rx="2.5" fill="rgba(255,255,255,0.5)"/>
                        <rect x="46" y="56" width="34" height="4" rx="2" fill="rgba(255,255,255,0.25)"/>
                        <rect x="14" y="72" width="40" height="14" rx="6" fill="rgba(0,200,83,0.7)"/>
                        <circle cx="170" cy="44" r="10" fill="rgba(0,200,83,0.3)"/>
                        <path d="M167 44l2 2 4-4" stroke="#00C853" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <rect x="186" y="39" width="50" height="5" rx="2.5" fill="rgba(255,255,255,0.5)"/>
                        <rect x="186" y="50" width="34" height="4" rx="2" fill="rgba(255,255,255,0.25)"/>
                        <rect x="154" y="66" width="48" height="14" rx="6" fill="rgba(255,255,255,0.15)"/>
                        <path d="M86 118h88" stroke="rgba(0,200,83,0.6)" stroke-width="1.5" stroke-dasharray="4 3"/>
                        <circle cx="130" cy="122" r="14" fill="rgba(0,200,83,0.25)" stroke="rgba(0,200,83,0.5)" stroke-width="1.5"/>
                        <path d="M127 122l2 2 4-4" stroke="#00C853" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <rect x="86" y="108" width="18" height="5" rx="2.5" fill="rgba(255,255,255,0.35)"/>
                        <rect x="156" y="108" width="18" height="5" rx="2.5" fill="rgba(255,255,255,0.35)"/>
                    </svg>
                    <div class="k-auth-visual-label">Kegalle's #1 local marketplace</div>
                </div>
            </div>
            <div class="k-auth-right">
                <div class="k-auth-right-header">
                    <div class="k-auth-right-icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <h3>Login to Your Account</h3>
                    <p class="subtitle">Glad to see you again!</p>
                </div>

                @if(session('success'))
                    <div class="k-alert k-alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="k-alert k-alert-error">{{ $errors->first() }}</div>
                @endif

                {{-- Login method tabs --}}
                <div role="tablist" aria-label="Login method" class="k-login-tabs">
                    <button type="button" id="tabEmail" role="tab" aria-selected="true" aria-controls="panelEmail" class="k-login-tab active">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        Email & Password
                    </button>
                    <button type="button" id="tabPhone" role="tab" aria-selected="false" aria-controls="panelPhone" class="k-login-tab">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                        Phone OTP
                    </button>
                </div>

                {{-- Email/password form --}}
                <div id="panelEmail" role="tabpanel" aria-labelledby="tabEmail">
                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    <div class="k-form-group">
                        <label class="k-form-label" for="login-email">Email Address</label>
                        <div class="k-form-icon-wrap">
                            <span class="k-form-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
                            <input id="login-email" type="email" name="email" value="{{ old('email') }}" class="k-form-control with-icon" placeholder="your@email.com" required>
                        </div>
                    </div>
                    <div class="k-form-group">
                        <label class="k-form-label" for="login-password">Password</label>
                        <div class="k-form-icon-wrap">
                            <span class="k-form-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                            <input id="login-password" type="password" name="password" class="k-form-control with-icon k-pw-input" placeholder="Your password" required>
                            <button type="button" class="k-pw-toggle" id="login-pw-toggle" aria-label="Show password" tabindex="-1">
                                <svg class="k-eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                <svg class="k-eye-on" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="k-remember-row">
                        <label class="k-remember-label">
                            <input type="checkbox" name="remember" class="k-checkbox"> Remember me
                        </label>
                        <a href="/forgot-password" class="k-link">Forgot Password?</a>
                    </div>
                    <button type="submit" class="k-btn k-btn-primary k-btn-lg w-full k-auth-submit">Login</button>
                </form>
                </div>

                {{-- Phone OTP panel --}}
                <div id="panelPhone" role="tabpanel" aria-labelledby="tabPhone" class="k-hidden" aria-hidden="true">
                    <div id="otpStep1">
                        <div class="k-form-group">
                            <label class="k-form-label">Mobile Number</label>
                            <div class="k-form-icon-wrap">
                                <span class="k-form-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg></span>
                                <input id="otpPhone" type="tel" class="k-form-control with-icon" placeholder="e.g. 0771234567" maxlength="15">
                            </div>
                        </div>
                        <div id="otpStep1Error" class="k-otp-error"></div>
                        <button type="button" id="otpSendBtn" class="k-btn k-btn-primary k-btn-lg w-full k-auth-submit">Send OTP</button>
                        <p class="k-otp-hint">We'll send a 6-digit code to your registered mobile number.</p>
                    </div>
                    <div id="otpStep2" class="k-hidden" aria-hidden="true">
                        <div class="k-otp-step2-hd">
                            <div class="k-auth-right-icon k-otp-icon-center">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.56 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.18 6.18l1.28-1.28a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </div>
                            <p class="k-otp-sent-title">Code sent!</p>
                            <p class="k-otp-sent-sub">Enter the 6-digit code sent to <strong id="otpPhoneDisplay"></strong></p>
                        </div>
                        <form method="POST" action="{{ route('phone.login-otp.verify') }}">
                            @csrf
                            <input type="hidden" name="phone" id="otpPhoneHidden">
                            <div class="k-form-group">
                                <label class="k-form-label">6-Digit Code</label>
                                <input type="text" name="code" class="k-form-control k-otp-input" placeholder="000000" maxlength="6" inputmode="numeric" pattern="\d{6}" autocomplete="one-time-code" required>
                            </div>
                            @error('code')<p class="k-otp-code-err">{{ $message }}</p>@enderror
                            <button type="submit" class="k-btn k-btn-primary k-btn-lg w-full k-auth-submit">Verify &amp; Login</button>
                        </form>
                        <div class="k-otp-actions">
                            <button type="button" id="otpBackBtn" class="k-btn k-btn-outline k-btn-sm">← Change Number</button>
                            <button type="button" id="otpResendBtn" class="k-btn k-btn-outline k-btn-sm k-otp-resend" disabled>Resend (60s)</button>
                        </div>
                    </div>
                </div>

                <div class="k-divider"><span>or continue with</span></div>
                <div class="k-social-col">
                    <div id="g_id_onload"
                         data-client_id="{{ config('services.google.client_id') }}"
                         data-callback="onGoogleLogin"
                         data-auto_prompt="false"
                         data-use_fedcm_for_prompt="false">
                    </div>
                    <div class="g_id_signin"
                         data-type="standard"
                         data-theme="outline"
                         data-size="large"
                         data-text="continue_with"
                         data-shape="rectangular"
                         data-width="340">
                    </div>
                </div>
                <form id="gsi-form" method="POST" action="/auth/google/token" class="k-form-hidden">
                    @csrf
                    <input type="hidden" name="credential" id="gsi-credential">
                    <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                </form>
                <script src="https://accounts.google.com/gsi/client" async defer></script>
                <script nonce="{{ $cspNonce ?? '' }}">
                function onGoogleLogin(resp) {
                    document.getElementById('gsi-credential').value = resp.credential;
                    document.getElementById('gsi-form').submit();
                }
                </script>
                <p class="k-auth-login-link">Don't have an account? <a href="/register" class="k-link k-link-bold">Sign up free</a></p>
            </div>
        </div>
    </div>
    <div class="k-auth-footer-bar">
        <div class="k-auth-trust">
            <div class="k-auth-trust-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
            <div class="k-auth-trust-text"><strong>100% Secure</strong><span>Your data is protected with top security</span></div>
        </div>
        <div class="k-auth-trust">
            <div class="k-auth-trust-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
            <div class="k-auth-trust-text"><strong>Local Support</strong><span>Our Kegalle team is here to help whenever you need us.</span></div>
        </div>
        <div class="k-auth-trust">
            <div class="k-auth-trust-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg></div>
            <div class="k-auth-trust-text"><strong>Best Deals</strong><span>Find amazing deals every day in Kegalle</span></div>
        </div>
        <div class="k-auth-trust">
            <div class="k-auth-trust-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
            <div class="k-auth-trust-text"><strong>Trusted Platform</strong><span>Verified sellers and safe transactions</span></div>
        </div>
    </div>
</div>
<script nonce="{{ $cspNonce ?? '' }}">
function kPwToggle(inputId, btn) {
    var i = document.getElementById(inputId);
    var show = i.type === 'password';
    i.type = show ? 'text' : 'password';
    btn.querySelector('.k-eye-off').classList.toggle('k-hidden', show);
    btn.querySelector('.k-eye-on').classList.toggle('k-hidden', !show);
    btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
}

function switchLoginTab(tab) {
    var isEmail = tab === 'email';
    var pE = document.getElementById('panelEmail');
    var pP = document.getElementById('panelPhone');
    pE.classList.toggle('k-hidden', !isEmail);
    pP.classList.toggle('k-hidden', isEmail);
    pE.setAttribute('aria-hidden', isEmail ? 'false' : 'true');
    pP.setAttribute('aria-hidden', isEmail ? 'true' : 'false');
    document.querySelectorAll('.k-login-tab').forEach(function(btn) { btn.classList.remove('active'); btn.setAttribute('aria-selected','false'); });
    var activeTab = document.getElementById(isEmail ? 'tabEmail' : 'tabPhone');
    if (activeTab) { activeTab.classList.add('active'); activeTab.setAttribute('aria-selected','true'); }
}

function otpStartCooldown(sec) {
    var btn = document.getElementById('otpResendBtn');
    btn.disabled = true;
    var t = setInterval(function() {
        sec--;
        if (sec <= 0) { clearInterval(t); btn.textContent = 'Resend OTP'; btn.disabled = false; }
        else { btn.textContent = 'Resend (' + sec + 's)'; }
    }, 1000);
}

document.addEventListener('DOMContentLoaded', function() {
    var pwToggle = document.getElementById('login-pw-toggle');
    if (pwToggle) pwToggle.addEventListener('click', function() { kPwToggle('login-password', pwToggle); });

    var tabEmail = document.getElementById('tabEmail');
    var tabPhone = document.getElementById('tabPhone');
    if (tabEmail) tabEmail.addEventListener('click', function() { switchLoginTab('email'); });
    if (tabPhone) tabPhone.addEventListener('click', function() { switchLoginTab('phone'); });

    var otpSendBtn = document.getElementById('otpSendBtn');
    if (otpSendBtn) otpSendBtn.addEventListener('click', function() { otpSend(); });

    var otpBackBtn = document.getElementById('otpBackBtn');
    if (otpBackBtn) otpBackBtn.addEventListener('click', function() { otpBack(); });

    var otpResendBtn = document.getElementById('otpResendBtn');
    if (otpResendBtn) otpResendBtn.addEventListener('click', function() { otpResend(); });
});

function otpSend() {
    var phone = document.getElementById('otpPhone').value.trim();
    var errEl = document.getElementById('otpStep1Error');
    var btn   = document.getElementById('otpSendBtn');
    if (!phone) { errEl.textContent = 'Please enter your mobile number.'; errEl.classList.add('visible'); return; }
    errEl.classList.remove('visible');
    btn.disabled = true; btn.textContent = 'Sending…';
    fetch('/phone/login-otp', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
        body: JSON.stringify({ phone: phone })
    }).then(function(r) {
        if (r.status === 429) { errEl.textContent = 'Too many attempts. Please wait 10 minutes.'; errEl.classList.add('visible'); btn.disabled = false; btn.textContent = 'Send OTP'; return; }
        return r.json().then(function(d) {
            if (d.error) { errEl.textContent = d.error; errEl.classList.add('visible'); btn.disabled = false; btn.textContent = 'Send OTP'; return; }
            document.getElementById('otpPhoneHidden').value = phone;
            document.getElementById('otpPhoneDisplay').textContent = phone;
            document.getElementById('otpStep1').classList.add('k-hidden');
            document.getElementById('otpStep2').classList.remove('k-hidden');
            otpStartCooldown(60);
        });
    }).catch(function() { errEl.textContent = 'Something went wrong. Please try again.'; errEl.classList.add('visible'); btn.disabled = false; btn.textContent = 'Send OTP'; });
}

function otpResend() {
    var btn = document.getElementById('otpResendBtn');
    var phone = document.getElementById('otpPhoneHidden').value;
    if (!phone || btn.disabled) return;
    btn.disabled = true; btn.textContent = 'Sending…';
    fetch('/phone/login-otp', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
        body: JSON.stringify({ phone: phone })
    }).then(function(r) {
        if (r.status === 429) { otpStartCooldown(60); return; }
        return r.json().then(function(d) {
            if (d.error) { btn.textContent = 'Resend OTP'; btn.disabled = false; }
            else { btn.textContent = '✓ Sent!'; setTimeout(function() { otpStartCooldown(60); }, 800); }
        });
    }).catch(function() { btn.disabled = false; btn.textContent = 'Resend OTP'; });
}

function otpBack() {
    document.getElementById('otpStep1').classList.remove('k-hidden');
    document.getElementById('otpStep2').classList.add('k-hidden');
    document.getElementById('otpSendBtn').disabled = false;
    document.getElementById('otpSendBtn').textContent = 'Send OTP';
}

@if($errors->has('code'))
document.addEventListener('DOMContentLoaded', function() {
    switchLoginTab('phone');
    document.getElementById('otpStep2').classList.remove('k-hidden');
    document.getElementById('otpStep1').classList.add('k-hidden');
});
@endif
</script>
@endsection
