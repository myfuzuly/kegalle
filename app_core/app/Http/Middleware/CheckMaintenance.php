<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Setting::getValue('maintenance_mode', '0') !== '1') {
            return $next($request);
        }

        // Allow any logged-in user through
        if (auth()->check()) {
            return $next($request);
        }

        // Allow login/logout, Google OAuth, and admin panel routes through
        if ($request->is('admin/*') || $request->is('login') || $request->is('logout')
            || $request->is('g-return') || $request->is('auth/google/token')
            || $request->is('auth/facebook/callback') || $request->is('auth/facebook/token')) {
            return $next($request);
        }

        return response()->view('errors.maintenance', [], 503);
    }
}
