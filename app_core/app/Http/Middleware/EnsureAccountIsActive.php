<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && in_array($user->status, ['inactive', 'suspended', 'banned'])) {
            $route = $user->status === 'inactive' ? '/account-pending' : '/account-suspended';
            return redirect($route);
        }

        return $next($request);
    }
}
