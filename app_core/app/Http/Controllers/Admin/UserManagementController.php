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
}
