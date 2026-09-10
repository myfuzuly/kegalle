<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $locations = Location::where('is_active', 1)->orderBy('name')->get();

        return view('dashboard.profile.edit', compact('user', 'locations'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'email'       => ['required', 'email', 'max:180', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'       => 'required|string|max:30',
            'location_id' => 'required|exists:locations,id',
            'avatar'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['phone'] = \App\Services\StoreService::normalizePhone($data['phone']);

        unset($data['avatar']); // remove file object; set path below
        if ($request->hasFile('avatar')) {
            if ($user->avatar && \Illuminate\Support\Str::startsWith($user->avatar, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = '/storage/' . $path;
        }

        $emailChanged = $data['email'] !== $user->email;

        $user->update($data);

        if ($emailChanged) {
            $user->forceFill(['email_verified_at' => null])->save();

            $plainToken = \Illuminate\Support\Str::random(64);
            $user->update(['verification_token' => hash('sha256', $plainToken)]);

            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\VerifyAccountMail($user, $plainToken));
            } catch (\Throwable $e) {
                \Log::error('Re-verification email failed: ' . $e->getMessage());
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->with('success', 'Email updated. Please check your new inbox to verify before logging in.');
        }

        return redirect('/dashboard')->with('success', 'Profile updated! Welcome to your dashboard.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        Auth::user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password updated successfully.');
    }
}
