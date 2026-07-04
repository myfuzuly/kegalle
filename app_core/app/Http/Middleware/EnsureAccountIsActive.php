<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $user->status === 'inactive') {
            return redirect('/account-pending');
        }

        if ($user && $user->status === 'suspended') {
            return redirect('/account-suspended');
        }

        return $next($request);
    }
}
