<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;

class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (! $user || ! $user->isAdminLevel()) {
            abort(403, 'You do not have permission to access this area.');
        }

        // Map /admin/{section}/... to a permission key
        $section = $request->segment(2); // null on /admin itself
        $permission = $section === null ? 'dashboard' : $section;

        // Only enforce known permission keys (unknown sub-paths inherit their parent section)
        if (array_key_exists($permission, Role::PERMISSIONS) && ! $user->hasPermission($permission)) {
            abort(403, 'Your role does not include access to this section.');
        }

        return $next($request);
    }
}
