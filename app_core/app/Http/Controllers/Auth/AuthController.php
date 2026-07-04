<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyAccountMail;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        $locations = Location::query()
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('auth.register', compact('locations'));
    }

    public function doLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (! auth()->user()->email_verified_at) {
                return redirect('/email/verify-notice');
            }

            return redirect()->intended('/dashboard');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Invalid login details.']);
    }

    public function doRegister(Request $request)
    {
        if (filled($request->input('website'))) {
            return back()->withInput($request->only('email'))->withErrors(['email' => 'Invalid login details.']);
        }

        $data = $request->validate([
            'account_type' => ['required', 'in:user,store'],
            'store_name' => ['nullable', 'string', 'max:190'],
            'name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'location_id' => ['required', 'exists:locations,id'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $token = Str::random(64);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'location_id' => $data['location_id'],
            'account_type' => $data['account_type'],
            'role' => 'user',
            'status' => 'inactive',
            'verification_token' => $token,
            'password' => Hash::make($data['password']),
        ]);

        try {
            Mail::to($user->email)->send(new VerifyAccountMail($user));
        } catch (\Throwable $e) {
            \Log::error('Verification email failed: '.$e->getMessage());
        }

        if ($data['account_type'] === 'store' && filled($data['store_name'] ?? null)) {
            session(['pending_store_name' => $data['store_name']]);
        }

        Auth::login($user);

        return redirect('/email/verify-notice');
    }

    public function verifyNotice()
    {
        return view('auth.verify-notice');
    }

    public function verifyEmail(string $token)
    {
        $user = User::where('verification_token', $token)->firstOrFail();

        $user->update([
            'email_verified_at' => now(),
            'verification_token' => null,
        ]);

        Auth::login($user);

        if ($user->account_type === 'store') {
            return redirect('/dashboard/stores/create')->with('success', 'Email verified. Create your store profile.');
        }

        return redirect('/dashboard')->with('success', 'Email verified successfully.');
    }

    public function resendVerification()
    {
        $user = auth()->user();

        if (! $user) {
            return redirect('/login');
        }

        if ($user->email_verified_at) {
            return redirect('/dashboard');
        }

        $user->update([
            'verification_token' => Str::random(64),
        ]);

        try {
            Mail::to($user->email)->send(new VerifyAccountMail($user));
        } catch (\Throwable $e) {
            \Log::error('Resend verification email failed: '.$e->getMessage());
            return back()->with('success', 'There was an issue sending the email. Please try again later.');
        }

        return back()->with('success', 'Verification email sent again.');
    }

    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'No account found with that email address.']);
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $resetUrl = url('/reset-password/'.$token.'?email='.urlencode($request->email));

        try {
            Mail::to($request->email)->send(new \App\Mail\PasswordResetMail($resetUrl));
        } catch (\Throwable $e) {
            \Log::error('Password reset email failed: '.$e->getMessage());
            return back()->withErrors(['email' => 'Failed to send reset email. Please try again later.']);
        }

        return back()->with('success', 'Password reset link sent to your email.');
    }

    public function resetPassword(string $token, Request $request)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function doResetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $record || ! Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Invalid or expired reset link.']);
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Reset link has expired. Please request a new one.']);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'No account found with that email address.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Password reset successfully.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
