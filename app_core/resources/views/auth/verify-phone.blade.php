@extends('layouts.app')

@section('title', 'Verify Your Phone — kegalle')

@section('content')
<div class="ks-040a28">
<div class="ks-f0da59">

    <div class="ks-1a49a5">
        <div class="ks-199bf1">📱</div>
        <h1 class="ks-cb7633">Verify Your Phone</h1>
        @if(auth()->user()->phone)
        <p class="ks-e340e6">
            We'll send a 6-digit code to <strong>{{ auth()->user()->phone }}</strong>
        </p>
        @else
        <p class="ks-d37fee">
            No phone number on your account.<br>
            <a href="/dashboard/profile" class="ks-2f7daa">Add your phone in Profile Settings →</a>
        </p>
        @endif
    </div>

    @if(session('warning'))
        <div class="ks-3c2b9f">
            ⚠ {{ session('warning') }}
        </div>
    @endif
    @if($errors->any())
        <div class="k-alert k-alert-error ks-301969">
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('otp_sent') && !$errors->has('code'))
        <div class="k-alert k-alert-success ks-301969">
            ✓ Code sent to {{ auth()->user()->phone }}. Check your messages.
        </div>
    @endif

    {{-- Step 1: Send OTP --}}
    @if(!session('otp_sent') && !$errors->has('code'))
    <div class="k-card ks-b3b25b">
        <form method="POST" action="/phone/send-otp">
            @csrf
            <p class="ks-cfe6e3">
                Click below to receive a verification code via SMS. Standard SMS rates may apply.
            </p>
            <button type="submit" class="k-btn k-btn-primary ks-f8c54c">
                Send Verification Code
            </button>
        </form>
    </div>
    @endif

    {{-- Step 2: Enter OTP --}}
    @if(session('otp_sent') || $errors->has('code'))
    <div class="k-card ks-b3b25b">
        <form method="POST" action="/phone/verify-otp" id="otpForm">
            @csrf
            <div class="ks-301969">
                <label for="otpInput" class="ks-96c847">Enter 6-digit code</label>
                <input
                    type="text"
                    name="code"
                    id="otpInput"
                    maxlength="6"
                    pattern="\d{6}"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    placeholder="• • • • • •"
                    class="ks-43eb4f"
                    autofocus
                    required>
                <div class="ks-f9ee92">
                    Code expires in <span id="otpCountdown">10:00</span>
                </div>
            </div>
            <button type="submit" class="k-btn k-btn-primary ks-f8c54c">
                Verify Phone Number
            </button>
        </form>

        <div class="ks-87cd1c">
            <form method="POST" action="/phone/send-otp" class="ks-5677b9">
                @csrf
                <button type="submit" class="ks-246eb5">
                    Resend code
                </button>
            </form>
        </div>
    </div>
    @endif

    <div class="ks-80237d">
        <a href="/dashboard" class="ks-e478a8">Skip for now →</a>
    </div>

</div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
// Auto-submit when 6 digits typed
var inp = document.getElementById('otpInput');
if (inp) {
    inp.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g,'');
        if (this.value.length === 6) {
            document.getElementById('otpForm').submit();
        }
    });
}

// Countdown timer
@if(session('otp_sent') || $errors->has('code'))
(function(){
    var s = 600, el = document.getElementById('otpCountdown');
    if (!el) return;
    var iv = setInterval(function(){
        s--;
        if (s <= 0) { clearInterval(iv); el.textContent = 'Expired'; el.style.color='#ef4444'; return; }
        var m = Math.floor(s/60), sec = s%60;
        el.textContent = m+':'+(sec<10?'0':'')+sec;
    }, 1000);
})();
@endif
</script>
@endpush
