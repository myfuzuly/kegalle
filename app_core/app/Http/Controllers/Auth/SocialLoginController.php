<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function redirect(string $provider)
    {
        abort_unless(in_array($provider, ['google', 'facebook']), 404);

        // Store intended redirect URL in session so callback can use it
        if (request()->filled('redirect')) {
            $redir = request('redirect');
            if (str_starts_with($redir, '/') && !str_starts_with($redir, '//')) {
                session(['social_redirect' => $redir]);
            }
        }

        $driver = Socialite::driver($provider);
        if ($provider === 'google') {
            $driver->with(['response_mode' => 'form_post']);
        }
        return $driver->redirect();
    }

    /**
     * Verify a Google ID token (from GSI popup) and log the user in.
     * Avoids OAuth redirect/callback entirely — no WAF-blocked URLs.
     */
    public function googleToken(\Illuminate\Http\Request $request)
    {
        $idToken = $request->input('credential');
        abort_if(empty($idToken), 422, 'Missing credential');

        // Verify with Google's tokeninfo endpoint
        $response = \Illuminate\Support\Facades\Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken,
        ]);

        abort_unless($response->successful(), 401, 'Invalid Google token');

        $payload = $response->json();

        // Confirm token is for our app
        $clientId = config('services.google.client_id');
        abort_unless(($payload['aud'] ?? '') === $clientId, 401, 'Token audience mismatch');
        abort_unless(in_array($payload['iss'] ?? '', ['accounts.google.com', 'https://accounts.google.com']), 401, 'Invalid issuer');

        $email    = $payload['email'] ?? null;
        $googleId = $payload['sub']   ?? null;
        abort_if(empty($email) || empty($googleId), 401, 'Incomplete Google profile');

        $user = User::where('social_provider', 'google')->where('social_provider_id', $googleId)->first()
            ?? ($email ? User::where('email', $email)->first() : null);

        if (! $user) {
            $user = User::create([
                'name'               => trim(($payload['given_name'] ?? '') . ' ' . ($payload['family_name'] ?? '')) ?: 'Kegalle User',
                'email'              => $email,
                'account_type'       => 'user',
                'avatar'             => $payload['picture'] ?? null,
                'social_provider'    => 'google',
                'social_provider_id' => $googleId,
                'password'           => Hash::make(Str::random(32)),
            ]);
            $user->forceFill(['role' => 'user', 'status' => 'active', 'email_verified_at' => now()])->save();
        } else {
            $user->forceFill([
                'email_verified_at'  => $user->email_verified_at ?: now(),
                'social_provider'    => 'google',
                'social_provider_id' => $googleId,
                'avatar'             => $payload['picture'] ?? $user->avatar,
            ])->save();
        }

        Auth::login($user, true);
        $isFirst = $this->trackLogin($user);

        // Only redirect to profile on very first login
        if ($isFirst && (empty($user->phone) || empty($user->location_id))) {
            return redirect()->route('dashboard.profile')
                ->with('warning', 'Welcome! Please add your phone number and location to complete your profile before posting ads.');
        }

        $redirectTo = $request->input('redirect') ?: '/dashboard';
        $parsed  = parse_url($redirectTo);
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        if (! empty($parsed['host']) && $parsed['host'] !== $appHost) {
            $redirectTo = '/dashboard';
        }

        return redirect($redirectTo);
    }

    /**
     * Verify a Facebook access token (from JS SDK popup) and log the user in.
     * Avoids OAuth redirect/callback entirely — no WAF-blocked URLs.
     */
    public function facebookToken(\Illuminate\Http\Request $request)
    {
        $accessToken = $request->input('access_token');
        abort_if(empty($accessToken), 422, 'Missing access_token');

        $appId     = config('services.facebook.client_id');
        $appSecret = config('services.facebook.client_secret');

        // Verify token with Facebook
        $debug = Http::get('https://graph.facebook.com/debug_token', [
            'input_token'  => $accessToken,
            'access_token' => $appId . '|' . $appSecret,
        ]);
        abort_unless($debug->successful() && ($debug->json('data.is_valid') === true), 401, 'Invalid Facebook token');
        abort_unless((string) ($debug->json('data.app_id') ?? '') === (string) $appId, 401, 'Token app mismatch');

        // Fetch user profile
        $profile = Http::get('https://graph.facebook.com/me', [
            'fields'       => 'id,name,email,picture.width(200)',
            'access_token' => $accessToken,
        ]);
        abort_unless($profile->successful(), 401, 'Could not fetch Facebook profile');

        $data     = $profile->json();
        $fbId     = $data['id']    ?? null;
        $email    = $data['email'] ?? null;
        $name     = $data['name']  ?? 'Kegalle User';
        $avatar   = $data['picture']['data']['url'] ?? null;

        abort_if(empty($fbId), 401, 'Incomplete Facebook profile');

        $user = User::where('social_provider', 'facebook')->where('social_provider_id', $fbId)->first()
            ?? ($email ? User::where('email', $email)->first() : null);

        if (! $user) {
            abort_if(empty($email), 422, 'Facebook did not provide an email address. Please register with email.');
            $user = User::create([
                'name'               => $name,
                'email'              => $email,
                'account_type'       => 'user',
                'avatar'             => $avatar,
                'social_provider'    => 'facebook',
                'social_provider_id' => $fbId,
                'password'           => Hash::make(Str::random(32)),
            ]);
            $user->forceFill(['role' => 'user', 'status' => 'active', 'email_verified_at' => now()])->save();
        } else {
            $user->forceFill([
                'email_verified_at'  => $user->email_verified_at ?: now(),
                'social_provider'    => 'facebook',
                'social_provider_id' => $fbId,
                'avatar'             => $avatar ?? $user->avatar,
            ])->save();
        }

        Auth::login($user, true);
        $this->trackLogin($user);

        if (empty($user->phone) || empty($user->location_id)) {
            return redirect()->route('dashboard.profile')
                ->with('warning', 'Welcome! Please add your phone number and location to complete your profile before posting ads.');
        }

        $redirectTo = $request->input('redirect') ?: '/dashboard';
        $parsed  = parse_url($redirectTo);
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        if (! empty($parsed['host']) && $parsed['host'] !== $appHost) {
            $redirectTo = '/dashboard';
        }

        return redirect($redirectTo);
    }

    private function trackLogin(User $user): bool
    {
        $isFirst = is_null($user->last_login_at);
        $user->forceFill(['last_login_at' => now()])->save();
        if ($isFirst) {
            session()->flash('first_login', true);
        }
        return $isFirst;
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
                'name'               => $socialUser->getName() ?: $socialUser->getNickname() ?: 'Kegalle User',
                'email'              => $socialUser->getEmail(),
                'account_type'       => 'user',
                'avatar'             => $socialUser->getAvatar(),
                'social_provider'    => $provider,
                'social_provider_id' => $socialUser->getId(),
                'password'           => Hash::make(Str::random(32)),
            ]);
            $user->forceFill(['role' => 'user', 'status' => 'active', 'email_verified_at' => now()])->save();
        } else {
            $user->forceFill([
                'email_verified_at'  => $user->email_verified_at ?: now(),
                'social_provider'    => $provider,
                'social_provider_id' => $socialUser->getId(),
                'avatar'             => $socialUser->getAvatar(),
            ])->save();
        }

        Auth::login($user, true);
        $this->trackLogin($user);

        if (empty($user->phone) || empty($user->location_id)) {
            return redirect()->route('dashboard.profile')
                ->with('warning', 'Welcome! Please add your phone number and location to complete your profile before posting ads.');
        }

        $redirectTo = session()->pull('social_redirect', '/dashboard');
        $parsed  = parse_url($redirectTo);
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        if (! empty($parsed['host']) && $parsed['host'] !== $appHost) {
            $redirectTo = '/dashboard';
        }

        return redirect($redirectTo);
    }
}
