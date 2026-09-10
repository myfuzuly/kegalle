@extends('layouts.app')

@section('title', 'Verify Your Phone — kegalle')

@section('content')
<div style="min-height:70vh;display:flex;align-items:center;justify-content:center;padding:40px 16px">
<div style="width:100%;max-width:420px">

    <div style="text-align:center;margin-bottom:32px">
        <div style="font-size:48px;margin-bottom:12px">📱</div>
        <h1 style="font-size:24px;font-weight:800;margin-bottom:6px">Verify Your Phone</h1>
        @if(auth()->user()->phone)
        <p style="color:var(--k-text-secondary);font-size:15px">
            We'll send a 6-digit code to <strong>{{ auth()->user()->phone }}</strong>
        </p>
        @else
        <p style="color:#dc2626;font-size:14px;font-weight:600">
            No phone number on your account.<br>
            <a href="/dashboard/profile" style="color:#2563eb;text-decoration:underline">Add your phone in Profile Settings →</a>
        </p>
        @endif
    </div>

    @if(session('warning'))
        <div style="background:#fff3e0;color:#e65100;padding:14px 18px;border-radius:10px;margin-bottom:16px;font-weight:600;font-size:14px">
            ⚠ {{ session('warning') }}
        </div>
    @endif
    @if($errors->any())
        <div class="k-alert k-alert-error" style="margin-bottom:20px">
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('otp_sent') && !$errors->has('code'))
        <div class="k-alert k-alert-success" style="margin-bottom:20px">
            ✓ Code sent to {{ auth()->user()->phone }}. Check your messages.
        </div>
    @endif

    {{-- Step 1: Send OTP --}}
    @if(!session('otp_sent') && !$errors->has('code'))
    <div class="k-card" style="padding:28px">
        <form method="POST" action="/phone/send-otp">
            @csrf
            <p style="font-size:14px;color:var(--k-text-secondary);margin-bottom:20px">
                Click below to receive a verification code via SMS. Standard SMS rates may apply.
            </p>
            <button type="submit" class="k-btn k-btn-primary" style="width:100%;justify-content:center;font-size:15px;padding:13px">
                Send Verification Code
            </button>
        </form>
    </div>
    @endif

    {{-- Step 2: Enter OTP --}}
    @if(session('otp_sent') || $errors->has('code'))
    <div class="k-card" style="padding:28px">
        <form method="POST" action="/phone/verify-otp" id="otpForm">
            @csrf
            <div style="margin-bottom:20px">
                <label for="otpInput" style="display:block;font-weight:600;margin-bottom:8px;font-size:14px">Enter 6-digit code</label>
                <input
                    type="text"
                    name="code"
                    id="otpInput"
                    maxlength="6"
                    pattern="\d{6}"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    placeholder="• • • • • •"
                    style="width:100%;text-align:center;font-size:28px;font-weight:700;letter-spacing:10px;padding:16px;border:2px solid var(--k-border);border-radius:var(--k-radius);background:var(--k-surface);color:var(--k-text);box-sizing:border-box"
                    autofocus
                    required>
                <div style="font-size:12px;color:var(--k-text-muted);margin-top:8px;text-align:center">
                    Code expires in <span id="otpCountdown">10:00</span>
                </div>
            </div>
            <button type="submit" class="k-btn k-btn-primary" style="width:100%;justify-content:center;font-size:15px;padding:13px">
                Verify Phone Number
            </button>
        </form>

        <div style="text-align:center;margin-top:16px">
            <form method="POST" action="/phone/send-otp" style="display:inline">
                @csrf
                <button type="submit" style="background:none;border:none;color:var(--k-primary);font-size:13px;cursor:pointer;text-decoration:underline;padding:0">
                    Resend code
                </button>
            </form>
        </div>
    </div>
    @endif

    <div style="text-align:center;margin-top:20px">
        <a href="/dashboard" style="font-size:13px;color:var(--k-text-muted)">Skip for now →</a>
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
