<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\MobitelSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PhoneOtpController extends Controller
{
    public function __construct(private MobitelSmsService $sms) {}

    // ── Show the verify-phone page ──────────────────────────────────────────
    public function showVerify()
    {
        if (!auth()->check()) return redirect('/login');
        if (auth()->user()->phone_verified_at) return redirect('/dashboard');

        return view('auth.verify-phone');
    }

    // ── Send (or resend) OTP to the logged-in user's phone ─────────────────
    public function sendOtp(Request $request)
    {
        $user = auth()->user();
        if (!$user) return redirect('/login');

        if (empty($user->phone)) {
            return back()->withErrors(['otp' => 'Please add a phone number to your profile first before verifying.']);
        }

        // Rate-limit: max 3 sends per 10 minutes
        $recent = DB::table('phone_otps')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count();

        if ($recent >= 3) {
            return back()->withErrors(['otp' => 'Too many attempts. Please wait 10 minutes before requesting another code.']);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('phone_otps')->insert([
            'user_id'    => $user->id,
            'phone'      => $user->phone,
            'code'       => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sent = $this->sms->send($user->phone, "Your kegalle verification code is: $code. Valid for 10 minutes. Do not share this code.");

        if (!$sent) {
            return back()->withErrors(['otp' => 'Could not send SMS. Please try again or contact support.']);
        }

        return back()->with('otp_sent', true);
    }

    // ── Verify the code the user typed ─────────────────────────────────────
    public function verifyOtp(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $user = auth()->user();
        if (!$user) return redirect('/login');

        $record = DB::table('phone_otps')
            ->where('user_id', $user->id)
            ->where('used',    false)
            ->where('expires_at', '>=', now())
            ->latest('created_at')
            ->first();

        if (!$record || !Hash::check($request->input('code'), $record->code)) {
            return back()
                ->withErrors(['code' => !$record
                    ? 'Code expired. Please request a new one.'
                    : 'Incorrect code. Please try again or resend.'])
                ->with('otp_sent', true);
        }

        // Mark used
        DB::table('phone_otps')->where('id', $record->id)->update(['used' => true, 'updated_at' => now()]);

        // Mark phone verified on user
        $user->update(['phone_verified_at' => now()]);

        return redirect('/dashboard')->with('success', 'Phone number verified successfully!');
    }

    // ── API endpoint: send OTP for login-by-phone (unauthenticated) ────────
    public function sendLoginOtp(Request $request)
    {
        $request->validate(['phone' => 'required|string']);

        $phone  = $request->input('phone');
        $digits = preg_replace('/\D/', '', $phone);
        // Normalise to local 10-digit format (07xxxxxxxx) for comparison
        $local  = $digits;
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) $local = substr($digits, 1); // 0094… → strip leading 0
        if (strlen($digits) === 11 && str_starts_with($digits, '94')) $local = '0'.substr($digits, 2);
        if (strlen($digits) === 12 && str_starts_with($digits, '094')) $local = '0'.substr($digits, 3);
        $variants = array_unique([$phone, $digits, $local, '0'.$local, '+94'.ltrim($local,'0'), '94'.ltrim($local,'0')]);
        $user  = \App\Models\User::whereIn('phone', $variants)->first();

        if (!$user) {
            return response()->json(['error' => 'No account found with this phone number.'], 404);
        }

        $recent = DB::table('phone_otps')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count();

        if ($recent >= 3) {
            return response()->json(['error' => 'Too many attempts. Wait 10 minutes.'], 429);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('phone_otps')->insert([
            'user_id'    => $user->id,
            'phone'      => $user->phone,
            'code'       => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->sms->send($user->phone, "Your kegalle login code is: $code. Valid for 10 minutes.");

        return response()->json(['sent' => true]);
    }

    // ── Verify login OTP and log user in (unauthenticated) ─────────────────
    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code'  => 'required|digits:6',
        ]);

        $phone  = $request->input('phone');
        $digits = preg_replace('/\D/', '', $phone);
        $local  = $digits;
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) $local = substr($digits, 1);
        if (strlen($digits) === 11 && str_starts_with($digits, '94')) $local = '0'.substr($digits, 2);
        if (strlen($digits) === 12 && str_starts_with($digits, '094')) $local = '0'.substr($digits, 3);
        $variants = array_unique([$phone, $digits, $local, '0'.$local, '+94'.ltrim($local,'0'), '94'.ltrim($local,'0')]);
        $user = \App\Models\User::whereIn('phone', $variants)->first();

        if (!$user) {
            return back()->withErrors(['code' => 'Invalid phone or code.']);
        }

        $record = DB::table('phone_otps')
            ->where('user_id', $user->id)
            ->where('used',    false)
            ->where('expires_at', '>=', now())
            ->latest('created_at')
            ->first();

        if (!$record || !Hash::check($request->input('code'), $record->code)) {
            return back()->withErrors(['code' => 'Invalid or expired code.']);
        }

        DB::table('phone_otps')->where('id', $record->id)->update(['used' => true, 'updated_at' => now()]);
        if (! $user->phone_verified_at) {
            $user->forceFill(['phone_verified_at' => now()])->save();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect('/dashboard')->with('success', 'Logged in via SMS OTP.');
    }
}
