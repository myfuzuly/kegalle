<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->q, function ($q) use ($request) {
                $q->where(function ($qq) use ($request) {
                    $qq->where('name', 'like', '%'.$request->q.'%')
                        ->orWhere('email', 'like', '%'.$request->q.'%');
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function makeAdmin(User $user)
    {
        $user->role = 'admin';
        $user->status = $user->status ?: 'active';
        $user->save();

        return back()->with('success', 'User promoted to Admin.');
    }

    public function makeSuperAdmin(User $user)
    {
        $user->role = 'super_admin';
        $user->status = $user->status ?: 'active';
        $user->save();

        return back()->with('success', 'User promoted to Super Admin.');
    }

    public function suspend(User $user)
    {
        $user->status = 'suspended';
        $user->save();

        return back()->with('success', 'User suspended.');
    }

    public function activate(User $user)
    {
        $user->status = 'active';
        $user->save();

        return back()->with('success', 'User activated.');
    }

    public function setStoreLimit(Request $request, User $user)
    {
        $data = $request->validate(['store_limit' => 'required|integer|min:1|max:999']);

        $owned = $user->stores()->count();
        if ($data['store_limit'] < $owned) {
            return back()->with('success', "{$user->name} already owns {$owned} store(s) — limit must be at least {$owned}.");
        }

        $user->store_limit = $data['store_limit'];
        $user->save();

        return back()->with('success', "Store limit for {$user->name} set to {$data['store_limit']}.");
    }

    public function toggleMultipleStores(User $user)
    {
        $user->allow_multiple_stores = ! (bool) $user->allow_multiple_stores;
        $user->save();

        $state = $user->allow_multiple_stores ? 'enabled' : 'disabled';
        return back()->with('success', "Multiple store creation {$state} for {$user->name}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('success', 'You cannot delete your own account.');
        }

        if (in_array($user->role, ['admin', 'super_admin'])) {
            return back()->with('success', 'Admin accounts cannot be deleted here. Remove their admin role first.');
        }

        if ($user->listings()->count() > 0 || $user->stores()->count() > 0) {
            return back()->with('success', 'This user has listings or stores attached and cannot be deleted. Suspend the account instead.');
        }

        $user->delete();

        return back()->with('success', 'User deleted permanently.');
    }
}
