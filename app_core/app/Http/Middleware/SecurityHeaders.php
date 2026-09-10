<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    private static ?string $nonce = null;

    public static function nonce(): string
    {
        if (self::$nonce === null) {
            self::$nonce = base64_encode(random_bytes(16));
        }
        return self::$nonce;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $nonce = self::nonce();
        \Illuminate\Support\Facades\View::share('cspNonce', $nonce);

        $response = $next($request);

        // Skip for binary / stream responses
        $ct = $response->headers->get('Content-Type', '');
        if (!str_contains($ct, 'text/html') && !empty($ct)) {
            return $response;
        }

        $ga  = config('services.analytics.ga_id')    ? 'https://www.googletagmanager.com https://www.google-analytics.com' : '';
        $px  = config('services.analytics.pixel_id') ? 'https://connect.facebook.net https://www.facebook.com' : '';
        $img = config('services.analytics.pixel_id') ? 'https://www.facebook.com' : '';

        $csp = implode('; ', array_filter([
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' https://cdn.jsdelivr.net https://code.jquery.com https://accounts.google.com https://unpkg.com https://www.google.com https://www.gstatic.com {$ga} {$px}",
            "style-src 'self' 'nonce-{$nonce}' https://fonts.googleapis.com https://accounts.google.com https://unpkg.com https://cdn.jsdelivr.net",
            "font-src 'self' https://fonts.gstatic.com data:",
            "img-src 'self' data: blob: https: {$img}",
            "connect-src 'self' https://accounts.google.com https://www.google-analytics.com https://sessions.bugsnag.com https://notify.bugsnag.com",
            "frame-src 'self' https://accounts.google.com https://www.google.com",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "object-src 'none'",
            "upgrade-insecure-requests",
        ]));

        $response->headers->set('Content-Security-Policy',              $csp);
        $response->headers->set('X-Content-Type-Options',               'nosniff');
        $response->headers->set('X-Frame-Options',                      'SAMEORIGIN');
        $response->headers->set('Referrer-Policy',                      'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy',                   'camera=(), microphone=(), geolocation=(self), payment=()');
        $response->headers->set('Strict-Transport-Security',            'max-age=31536000; includeSubDomains; preload');
        $response->headers->set('X-Permitted-Cross-Domain-Policies',    'none');
        $response->headers->set('Cross-Origin-Opener-Policy',           'same-origin-allow-popups');
        $response->headers->set('Cross-Origin-Resource-Policy',         'same-site');
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
