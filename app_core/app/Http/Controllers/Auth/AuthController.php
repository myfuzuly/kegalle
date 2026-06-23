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
        $data = $request->validate([
            'account_type' => ['required', 'in:user,store'],
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
            'status' => 'active',
            'verification_token' => $token,
            'password' => Hash::make($data['password']),
        ]);

        Mail::to($user->email)->send(new VerifyAccountMail($user));

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

        Mail::to($user->email)->send(new VerifyAccountMail($user));

        return back()->with('success', 'Verification email sent again.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
