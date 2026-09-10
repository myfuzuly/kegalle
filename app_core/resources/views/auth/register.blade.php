@extends('layouts.app')
@section('title','Create Account · Kegalle Marketplace')
@section('meta_description','Create a free Kegalle Marketplace account to start buying, selling, or listing your store in the Kegalle district today.')
@section('content')
<div class="k-auth-page">
    <div class="k-auth-main">
        <div class="k-auth-container k-auth-container--wide">
            <div class="k-auth-left">
                <div>
                    <div class="k-auth-brand">
                        <img src="/images/kegalle-logo.png" alt="Kegalle Marketplace" class="k-auth-logo">
                    </div>
                    <h2>Join Kegalle <span>Marketplace</span></h2>
                    <p>Create your free account and connect with buyers and sellers right here in Kegalle.</p>
                    <div class="k-auth-features">
                        <div class="k-auth-feature">
                            <div class="k-auth-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            </div>
                            <div class="k-auth-feature-text"><strong>Buy with Confidence</strong><span>Shop from verified sellers and quality products.</span></div>
                        </div>
                        <div class="k-auth-feature">
                            <div class="k-auth-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9l20-7z"/></svg>
                            </div>
                            <div class="k-auth-feature-text"><strong>Sell or Promote Easily</strong><span>List your products and reach thousands locally.</span></div>
                        </div>
                        <div class="k-auth-feature">
                            <div class="k-auth-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </div>
                            <div class="k-auth-feature-text"><strong>Secure & Safe</strong><span>Your privacy and security are our top priority.</span></div>
                        </div>
                        <div class="k-auth-feature">
                            <div class="k-auth-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            </div>
                            <div class="k-auth-feature-text"><strong>Local Support</strong><span>Our Kegalle team is here to help whenever you need us.</span></div>
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
                <h3>Create Your Account</h3>
                <p class="subtitle">Free forever · Takes less than 2 minutes</p>

                @if($errors->any())
                    <div class="k-alert k-alert-error">
                        @if($errors->count() === 1)
                            {{ $errors->first() }}
                        @else
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif

                {{-- Social login forms --}}
                <form id="gsi-reg-form" method="POST" action="/auth/google/token" class="k-form-hidden">
                    @csrf
                    <input type="hidden" name="credential" id="gsi-reg-credential">
                    <input type="hidden" name="redirect" value="/dashboard/profile">
                </form>
                @if(config('services.facebook.client_id'))
                <form id="fb-reg-form" method="POST" action="/auth/facebook/token" class="k-form-hidden">
                    @csrf
                    <input type="hidden" name="access_token" id="fb-reg-access-token">
                    <input type="hidden" name="redirect" value="/dashboard/profile">
                </form>
                @endif

                <form method="POST" action="{{ route('register.submit') }}" id="register-form">
                    @csrf
                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                    <div class="k-honeypot" aria-hidden="true">
                        <input type="text" name="website" id="website" tabindex="-1" autocomplete="off" aria-hidden="true">
                    </div>

                    <div class="k-form-group">
                        <label class="k-form-label" id="account-type-label">I want to join as</label>
                        <div class="k-account-types" role="radiogroup" aria-labelledby="account-type-label">
                            <label class="k-account-type {{ old('account_type', request('account_type','store')) === 'store' ? 'selected' : '' }}">
                                <input type="radio" name="account_type" value="store" class="k-radio-hidden" {{ old('account_type', request('account_type','store')) === 'store' ? 'checked' : '' }}>
                                <div class="k-account-type-radio"></div>
                                <div class="k-account-type-icon">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                </div>
                                <div class="k-account-type-name">Store / Business / Service</div>
                                <div class="k-account-type-desc">Sell products, run a store, or offer services</div>
                            </label>
                            <label class="k-account-type {{ old('account_type', request('account_type')) === 'user' ? 'selected' : '' }}">
                                <input type="radio" name="account_type" value="user" class="k-radio-hidden" {{ old('account_type', request('account_type')) === 'user' ? 'checked' : '' }}>
                                <div class="k-account-type-radio"></div>
                                <div class="k-account-type-icon">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                </div>
                                <div class="k-account-type-name">Personal / Classified</div>
                                <div class="k-account-type-desc">Post ads for personal use or services</div>
                            </label>
                        </div>
                    </div>

                    <div class="k-form-grid">
                        <div class="k-form-group k-store-field {{ old('account_type', request('account_type','store')) === 'store' ? '' : 'k-hidden' }}">
                            <label class="k-form-label" for="register-store-name">Store Name</label>
                            <div class="k-form-icon-wrap">
                                <span class="k-form-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                                <input id="register-store-name" type="text" name="store_name" value="{{ old('store_name') }}" class="k-form-control with-icon" placeholder="Your store or business name">
                            </div>
                        </div>
                        <div class="k-form-group">
                            <label class="k-form-label" for="register-name">Full Name</label>
                            <div class="k-form-icon-wrap">
                                <span class="k-form-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                                <input id="register-name" type="text" name="name" value="{{ old('name') }}" class="k-form-control with-icon" placeholder="Your full name" required>
                            </div>
                        </div>
                        <div class="k-form-group">
                            <label class="k-form-label" for="register-email">Email Address</label>
                            <div class="k-form-icon-wrap">
                                <span class="k-form-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
                                <input id="register-email" type="email" name="email" value="{{ old('email') }}" class="k-form-control with-icon" placeholder="your@email.com" required>
                            </div>
                        </div>
                        <div class="k-form-group">
                            <label class="k-form-label" for="register-phone">Phone Number</label>
                            <div class="k-form-icon-wrap">
                                <span class="k-form-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.56 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.18 6.18l1.28-1.28a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg></span>
                                <input id="register-phone" type="tel" name="phone" value="{{ old('phone') }}" class="k-form-control with-icon" placeholder="0712 345 678" required>
                            </div>
                        </div>
                        <div class="k-form-group">
                            <label class="k-form-label">Location</label>
                            <input type="hidden" name="location_id" id="regLocVal" value="{{ old('location_id') }}" required>
                            <div class="ksd-wrap" id="regLocWrap">
                                <button type="button" class="ksd-trigger {{ old('location_id') ? 'ksd-has-value' : '' }}" id="regLocTrigger">
                                    <span class="ksd-trigger-text {{ old('location_id') ? '' : 'placeholder' }}" id="regLocLabel">
                                        @php $oldLoc = collect($locations ?? [])->firstWhere('id', old('location_id')); @endphp
                                        {{ $oldLoc ? $oldLoc->name : 'Select your city…' }}
                                    </span>
                                    <svg class="ksd-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                                </button>
                                <div class="ksd-dropdown" id="regLocDropdown">
                                    <div class="ksd-search-row"><input type="text" class="ksd-search" id="regLocSearch" placeholder="Type to search…" autocomplete="off"></div>
                                    <div class="ksd-list" id="regLocList">
                                        @foreach(($locations ?? []) as $city)
                                            <div class="ksd-item {{ old('location_id')==$city->id ? 'ksd-selected' : '' }}"
                                                data-value="{{ $city->id }}"
                                                data-label="{{ $city->name }}"
                                                data-search="{{ strtolower($city->name) }}">📍 {{ $city->name }}</div>
                                        @endforeach
                                        <div class="ksd-empty" id="regLocEmpty">No cities match</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="k-form-group">
                            <label class="k-form-label" for="register-password">Password</label>
                            <div class="k-form-icon-wrap">
                                <span class="k-form-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                                <input id="register-password" type="password" name="password" class="k-form-control with-icon k-pw-input" placeholder="Min 8 characters" required minlength="8">
                                <button type="button" class="k-pw-toggle" id="pw-toggle-1" aria-label="Show password" tabindex="-1">
                                    <svg class="k-eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                    <svg class="k-eye-on" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                            <div id="pw-strength" class="k-hidden">
                                <div class="pw-bar-row">
                                    <div class="pw-bar"></div>
                                    <div class="pw-bar"></div>
                                    <div class="pw-bar"></div>
                                    <div class="pw-bar"></div>
                                </div>
                                <small id="pw-label">Min 8 characters</small>
                            </div>
                        </div>
                        <div class="k-form-group">
                            <label class="k-form-label" for="register-password-confirmation">Confirm Password</label>
                            <div class="k-form-icon-wrap">
                                <span class="k-form-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                                <input id="register-password-confirmation" type="password" name="password_confirmation" class="k-form-control with-icon k-pw-input" placeholder="Repeat your password" required>
                                <button type="button" class="k-pw-toggle" id="pw-toggle-2" aria-label="Show password" tabindex="-1">
                                    <svg class="k-eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                    <svg class="k-eye-on" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="k-divider"><span>or sign up with</span></div>
                    <div class="k-social-col">
                        <div id="g_id_onload"
                             data-client_id="{{ config('services.google.client_id') }}"
                             data-callback="onGoogleRegister"
                             data-auto_prompt="false"
                             data-use_fedcm_for_prompt="false">
                        </div>
                        <div class="g_id_signin"
                             data-type="standard"
                             data-theme="outline"
                             data-size="large"
                             data-text="signup_with"
                             data-shape="rectangular"
                             data-width="340">
                        </div>
                        @if(config('services.facebook.client_id'))
                        <button type="button" id="fb-reg-btn" class="k-fb-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            Sign up with Facebook
                        </button>
                        @endif
                    </div>
                    <script src="https://accounts.google.com/gsi/client" async defer></script>
                    <script nonce="{{ $cspNonce ?? '' }}">
                    function onGoogleRegister(resp) {
                        document.getElementById('gsi-reg-credential').value = resp.credential;
                        document.getElementById('gsi-reg-form').submit();
                    }
                    @if(config('services.facebook.client_id'))
                    window.fbAsyncInit = function() {
                        FB.init({ appId: '{{ config('services.facebook.client_id') }}', cookie: true, xfbml: false, version: 'v19.0' });
                    };
                    (function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(d.getElementById(id))return;js=d.createElement(s);js.id=id;js.src='https://connect.facebook.net/en_US/sdk.js';fjs.parentNode.insertBefore(js,fjs);}(document,'script','facebook-jssdk'));
                    document.addEventListener('DOMContentLoaded', function() {
                        var btn = document.getElementById('fb-reg-btn');
                        if (!btn) return;
                        btn.addEventListener('click', function() {
                            btn.disabled = true; btn.textContent = 'Connecting…';
                            FB.login(function(response) {
                                if (response.authResponse && response.authResponse.accessToken) {
                                    document.getElementById('fb-reg-access-token').value = response.authResponse.accessToken;
                                    document.getElementById('fb-reg-form').submit();
                                } else {
                                    btn.disabled = false;
                                    btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg> Sign up with Facebook';
                                }
                            }, { scope: 'email,public_profile' });
                        });
                    });
                    @endif
                    </script>

                    <div class="k-terms-row">
                        <input id="register-terms" type="checkbox" required class="k-checkbox">
                        <label for="register-terms">I agree to the <a href="/terms-and-conditions" class="k-link">Terms & Conditions</a> and <a href="/privacy-policy" class="k-link">Privacy Policy</a></label>
                    </div>
                    <button type="submit" class="k-btn k-btn-primary k-btn-lg w-full k-auth-submit">Create Account</button>
                </form>
                <p class="k-auth-login-link">Already have an account? <a href="/login" class="k-link k-link-bold">Sign in</a></p>
            </div>
        </div>
    </div>
    <div class="k-auth-footer-bar">
        <div class="k-auth-trust">
            <div class="k-auth-trust-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
            <div class="k-auth-trust-text"><strong>Kegalle District Only</strong><span>Local buyers and sellers — no nationwide noise</span></div>
        </div>
        <div class="k-auth-trust">
            <div class="k-auth-trust-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
            <div class="k-auth-trust-text"><strong>Meet in Person</strong><span>Inspect before you pay — safe local deals</span></div>
        </div>
        <div class="k-auth-trust">
            <div class="k-auth-trust-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg></div>
            <div class="k-auth-trust-text"><strong>Free to Post & Browse</strong><span>List your ad today at no cost</span></div>
        </div>
        <div class="k-auth-trust">
            <div class="k-auth-trust-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
            <div class="k-auth-trust-text"><strong>Verified Local Stores</strong><span>Shop from trusted businesses in Kegalle</span></div>
        </div>
    </div>
</div>

<script nonce="{{ $cspNonce ?? '' }}">
/* ---- KSD location dropdown ---- */
(function() {
    var trigger  = document.getElementById('regLocTrigger');
    var dropdown = document.getElementById('regLocDropdown');
    var search   = document.getElementById('regLocSearch');
    var list     = document.getElementById('regLocList');
    var emptyEl  = document.getElementById('regLocEmpty');
    var valInput = document.getElementById('regLocVal');
    var label    = document.getElementById('regLocLabel');

    function open() {
        dropdown.classList.add('ksd-open');
        search.focus();
    }
    function close() {
        dropdown.classList.remove('ksd-open');
    }
    function toggle() {
        dropdown.classList.contains('ksd-open') ? close() : open();
    }
    function filter(q) {
        var items = list.querySelectorAll('.ksd-item');
        var any = false;
        items.forEach(function(el) {
            var m = el.dataset.search.indexOf(q.toLowerCase()) >= 0;
            el.classList.toggle('k-hidden', !m);
            if (m) any = true;
        });
        emptyEl.classList.toggle('visible', !any);
    }
    function select(el) {
        valInput.value = el.dataset.value;
        label.textContent = el.dataset.label;
        label.classList.remove('placeholder');
        trigger.classList.add('ksd-has-value');
        close();
        list.querySelectorAll('.ksd-item').forEach(function(i) { i.classList.remove('ksd-selected'); });
        el.classList.add('ksd-selected');
    }

    if (trigger) trigger.addEventListener('click', toggle);
    if (search)  search.addEventListener('input', function() { filter(this.value); });
    if (list)    list.addEventListener('click', function(e) {
        var item = e.target.closest('.ksd-item');
        if (item) select(item);
    });
    document.addEventListener('click', function(e) {
        if (dropdown && !dropdown.contains(e.target) && e.target !== trigger && !trigger.contains(e.target)) close();
    });
})();

/* ---- Password toggle ---- */
function kPwToggle(inputId, btn) {
    var i = document.getElementById(inputId);
    var show = i.type === 'password';
    i.type = show ? 'text' : 'password';
    btn.querySelector('.k-eye-off').classList.toggle('k-hidden', show);
    btn.querySelector('.k-eye-on').classList.toggle('k-hidden', !show);
    btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
}

document.addEventListener('DOMContentLoaded', function() {
    ['pw-toggle-1', 'pw-toggle-2'].forEach(function(id, i) {
        var btn = document.getElementById(id);
        var inputId = i === 0 ? 'register-password' : 'register-password-confirmation';
        if (btn) btn.addEventListener('click', function() { kPwToggle(inputId, btn); });
    });
});

/* ---- Field validation helpers ---- */
function kFieldError(el, msg) {
    el.classList.toggle('k-input-error', !!msg);
    if (!msg) el.classList.remove('k-input-error');
    var wrap = el.closest('.k-form-icon-wrap') || el.closest('.k-form-group');
    var id = el.id + '-err';
    var existing = document.getElementById(id);
    if (msg) {
        var errHtml = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>' + msg;
        if (!existing) {
            var e = document.createElement('div');
            e.id = id;
            e.className = 'k-field-err';
            e.innerHTML = errHtml;
            (wrap || el.parentNode).after(e);
        } else { existing.innerHTML = errHtml; }
    } else if (existing) { existing.remove(); }
}
function kFieldOk(el) {
    el.classList.add('k-input-ok');
    el.classList.remove('k-input-error');
    kFieldError(el, '');
}

/* ---- Password strength ---- */
(function() {
    var pw = document.getElementById('register-password');
    var box = document.getElementById('pw-strength');
    var bars = box ? box.querySelectorAll('.pw-bar') : [];
    var lbl = document.getElementById('pw-label');
    var colors = ['#ef4444','#f59e0b','#22c55e','#059669'];
    var labels = ['Weak','Fair','Good','Strong'];
    if (pw && box) {
        pw.addEventListener('input', function() {
            var v = pw.value, s = 0;
            if (!v) { box.classList.add('k-hidden'); kFieldError(pw, ''); pw.classList.remove('k-input-error','k-input-ok'); return; }
            box.classList.remove('k-hidden');
            if (v.length >= 8) s++;
            if (v.length >= 12) s++;
            if (/[A-Z]/.test(v) && /[a-z]/.test(v)) s++;
            if (/[0-9]/.test(v) || /[^a-zA-Z0-9]/.test(v)) s++;
            s = Math.min(s, 4);
            for (var i = 0; i < 4; i++) { bars[i].style.background = i < s ? colors[Math.min(s-1,3)] : '#e2e8f0'; }
            lbl.textContent = s === 0 ? 'Min 8 characters' : labels[s-1];
            lbl.style.color = colors[Math.min(s-1,3)];
            if (v.length < 8) kFieldError(pw, 'Password must be at least 8 characters');
            else { kFieldOk(pw); kFieldError(pw, ''); }
        });
        pw.addEventListener('blur', function() {
            if (pw.value && pw.value.length < 8) kFieldError(pw, 'Password must be at least 8 characters');
        });
    }

    var name = document.getElementById('register-name');
    if (name) name.addEventListener('blur', function() {
        if (!name.value.trim()) kFieldError(name, 'Full name is required');
        else if (name.value.trim().length < 2) kFieldError(name, 'Name must be at least 2 characters');
        else kFieldOk(name);
    });

    var email = document.getElementById('register-email');
    if (email) email.addEventListener('blur', function() {
        var v = email.value.trim();
        if (!v) kFieldError(email, 'Email is required');
        else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) kFieldError(email, 'Enter a valid email address');
        else kFieldOk(email);
    });

    var phone = document.getElementById('register-phone');
    if (phone) phone.addEventListener('blur', function() {
        var v = phone.value.trim().replace(/[\s\-()]/g, '');
        if (!v) kFieldError(phone, 'Phone number is required');
        else if (!/^(\+94|0094|0)7[0-9]{8}$/.test(v)) kFieldError(phone, 'Enter a valid Sri Lankan phone (e.g. 0712345678)');
        else kFieldOk(phone);
    });

    var loc = document.getElementById('regLocVal');
    var locTrigger = document.getElementById('regLocTrigger');
    var locWrap = document.getElementById('regLocWrap');

    var conf = document.getElementById('register-password-confirmation');
    if (conf && pw) conf.addEventListener('blur', function() {
        if (!conf.value) kFieldError(conf, 'Please confirm your password');
        else if (conf.value !== pw.value) kFieldError(conf, 'Passwords do not match');
        else kFieldOk(conf);
    });

    var sname = document.getElementById('register-store-name');
    if (sname) sname.addEventListener('blur', function() {
        var typeEl = document.querySelector('.k-account-type input[name="account_type"]:checked');
        if (typeEl && typeEl.value === 'store' && !sname.value.trim())
            kFieldError(sname, 'Store name is required for Store / Business accounts');
        else if (sname.value.trim()) kFieldOk(sname);
    });

    var form = document.getElementById('register-form');
    if (form) form.addEventListener('submit', function(e) {
        var invalid = false;
        if (name && !name.value.trim()) { kFieldError(name, 'Full name is required'); invalid = true; }
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) { kFieldError(email, 'Enter a valid email address'); invalid = true; }
        if (phone) { var v = phone.value.trim().replace(/[\s\-()]/g, ''); if (!/^(\+94|0094|0)7[0-9]{8}$/.test(v)) { kFieldError(phone, 'Enter a valid Sri Lankan phone'); invalid = true; } }
        if (loc && !loc.value) {
            if (locTrigger) locTrigger.classList.add('k-input-error');
            if (locWrap) locWrap.classList.add('k-loc-error');
            invalid = true;
        }
        if (pw && pw.value.length < 8) { kFieldError(pw, 'Password must be at least 8 characters'); invalid = true; }
        if (conf && conf.value !== pw.value) { kFieldError(conf, 'Passwords do not match'); invalid = true; }
        if (invalid) { e.preventDefault(); form.querySelector('[type=submit]').textContent = 'Please fix the errors above'; }
    });
})();

/* ---- Account type toggle ---- */
document.querySelectorAll('.k-account-type input[type=radio]').forEach(function(r) {
    r.addEventListener('change', function() {
        document.querySelectorAll('.k-account-type').forEach(function(el) { el.classList.remove('selected'); });
        r.closest('.k-account-type').classList.add('selected');
        document.querySelectorAll('.k-store-field').forEach(function(el) {
            el.classList.toggle('k-hidden', r.value !== 'store');
        });
    });
});
</script>
@if(config('services.recaptcha.site_key'))
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}" async defer></script>
<script nonce="{{ $cspNonce ?? '' }}">
document.getElementById('register-form').addEventListener('submit', function(e) {
    var siteKey = '{{ config('services.recaptcha.site_key') }}';
    if (!siteKey) return;
    e.preventDefault();
    var form = this;
    grecaptcha.ready(function() {
        grecaptcha.execute(siteKey, { action: 'register' }).then(function(token) {
            document.getElementById('g-recaptcha-response').value = token;
            form.submit();
        });
    });
}, true);
</script>
@endif
@endsection
