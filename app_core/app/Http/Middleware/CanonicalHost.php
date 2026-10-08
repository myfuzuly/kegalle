<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanonicalHost
{
    private const HOST = 'kegalle.com';

    public function handle(Request $request, Closure $next): Response
    {
        $host = strtolower($request->getHost());

        if (!app()->environment('production') || ($host !== self::HOST && $host !== 'www.' . self::HOST)) {
            return $next($request);
        }

        if ($host === 'www.' . self::HOST || $this->scheme($request) === 'http') {
            return redirect()->to('https://' . self::HOST . $request->getRequestUri(), 301);
        }

        return $next($request);
    }

    // The app sits behind nginx, so trust only explicit signals; unknown means "don't redirect".
    private function scheme(Request $request): ?string
    {
        $fwd = strtolower((string) $request->server('HTTP_X_FORWARDED_PROTO'));
        if ($fwd === 'https' || $fwd === 'http') {
            return $fwd;
        }
        $https = strtolower((string) $request->server('HTTPS'));
        if ($https !== '' && $https !== 'off') {
            return 'https';
        }
        $port = (string) $request->server('SERVER_PORT');
        if ($port === '443') {
            return 'https';
        }
        if ($port === '80') {
            return 'http';
        }
        return null;
    }
}
