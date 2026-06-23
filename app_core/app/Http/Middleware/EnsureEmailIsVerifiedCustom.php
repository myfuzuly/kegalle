<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureEmailIsVerifiedCustom
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && ! auth()->user()->email_verified_at) {
            return redirect('/email/verify-notice');
        }

        return $next($request);
    }
}
