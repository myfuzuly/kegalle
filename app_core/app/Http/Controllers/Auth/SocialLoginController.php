<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function redirect(string $provider)
    {
        abort_unless(in_array($provider, ['google', 'facebook']), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        abort_unless(in_array($provider, ['google', 'facebook']), 404);

        $socialUser = Socialite::driver($provider)->user();

        $user = User::where('social_provider', $provider)
            ->where('social_provider_id', $socialUser->getId())
            ->first();

        if (! $user && $socialUser->getEmail()) {
            $user = User::where('email', $socialUser->getEmail())->first();
        }

        if (! $user) {
            $user = User::create([
                'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: 'Kegalle User',
                'email' => $socialUser->getEmail(),
                'email_verified_at' => now(),
                'social_provider' => $provider,
                'social_provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'account_type' => 'user',
                'role' => 'user',
                'status' => 'active',
                'password' => Hash::make(Str::random(32)),
            ]);
        } else {
            $user->update([
                'email_verified_at' => $user->email_verified_at ?: now(),
                'social_provider' => $provider,
                'social_provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
            ]);
        }

        Auth::login($user, true);

        return redirect('/dashboard');
    }
}
