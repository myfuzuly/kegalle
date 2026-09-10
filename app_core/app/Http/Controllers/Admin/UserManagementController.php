<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->q, fn ($q) => $q->where(function ($qq) use ($request) {
                $qq->where('name', 'like', '%'.$request->q.'%')
                   ->orWhere('email', 'like', '%'.$request->q.'%')
                   ->orWhere('phone', 'like', '%'.$request->q.'%');
            }))
            ->when($request->role && $request->role !== '', function ($q) use ($request) {
                if ($request->role === 'admins') {
                    $q->whereIn('role', ['admin', 'super_admin']);
                } else {
                    $q->where('role', $request->role);
                }
            })
            ->when($request->status && $request->status !== '', fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $allRoles = cache()->remember('admin_all_roles', 300, fn () =>
            \App\Models\Role::orderBy('sort_order')->get()
        );

        $stats = cache()->remember('admin_user_stats', 60, fn () => [
            'total'      => User::count(),
            'active'     => User::where('status', 'active')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
            'suspended'  => User::where('status', 'suspended')->count(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'total'      => $users->total(),
                'rows'       => view('admin.users._rows', compact('users', 'allRoles'))->render(),
                'pagination' => (string) $users->links('vendor.pagination.ka-admin'),
            ]);
        }

        return view('admin.users.index', compact('users', 'allRoles', 'stats'));
    }

    public function exportCsv(Request $request)
    {
        $filename = 'users-' . now()->format('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($request) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Phone', 'Role', 'Status', 'Email Verified', 'Store Limit', 'Registered']);
            User::when($request->q, fn ($q) => $q->where(function ($qq) use ($request) {
                    $qq->where('name', 'like', '%'.$request->q.'%')
                       ->orWhere('email', 'like', '%'.$request->q.'%');
                }))
                ->when($request->role && $request->role !== '', fn ($q) => $q->where('role', $request->role))
                ->when($request->status && $request->status !== '', fn ($q) => $q->where('status', $request->status))
                ->orderByDesc('id')
                ->chunk(500, function ($users) use ($handle) {
                    foreach ($users as $u) {
                        fputcsv($handle, [
                            $u->id,
                            $u->name,
                            $u->email,
                            $u->phone,
                            $u->role ?? 'user',
                            $u->status ?? 'active',
                            $u->email_verified_at ? 'Yes' : 'No',
                            $u->store_limit ?? 1,
                            $u->created_at?->format('Y-m-d'),
                        ]);
                    }
                });
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(User $user)
    {
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $listings  = $user->listings()->latest()->limit(10)->get();
        $stores    = $user->stores()->latest()->get();
        return view('admin.users.show', compact('user', 'locations', 'listings', 'stores'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:191',
            'email'       => ['required','email','max:180',\Illuminate\Validation\Rule::unique('users','email')->ignore($user->id)],
            'phone'       => 'nullable|string|max:30',
            'location_id' => 'nullable|exists:locations,id',
            'role'        => ['required',\Illuminate\Validation\Rule::in(['user','seller','admin','super_admin'])],
            'status'      => 'required|in:active,pending,suspended,inactive',
            'avatar'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'password'    => 'nullable|string|min:8|confirmed',
        ]);

        unset($data['avatar']);
        if ($request->hasFile('avatar')) {
            if ($user->avatar && \Illuminate\Support\Str::startsWith($user->avatar, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
            }
            $data['avatar'] = '/storage/' . $request->file('avatar')->store('avatars', 'public');
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'User updated successfully.');
    }

    public function create()
    {
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:191',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:30',
            'password' => 'required|string|min:8|confirmed',
            'role'     => ['required', \Illuminate\Validation\Rule::in(
                auth()->user()->role === 'super_admin'
                    ? ['user', 'seller', 'admin', 'super_admin']
                    : ['user', 'seller', 'admin']
            )],
            'status'   => 'required|in:active,pending,suspended,inactive',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect('/admin/users')->with('success', 'User created successfully.');
    }

    public function makeAdmin(User $user)
    {
        abort_if(auth()->user()->role !== 'super_admin', 403, 'Only super admins can promote users to admin.');

        $user->role = 'admin';
        $user->status = $user->status ?: 'active';
        $user->save();

        return back()->with('success', 'User promoted to Admin.');
    }

    public function makeSuperAdmin(User $user)
    {
        abort_if(auth()->user()->role !== 'super_admin', 403, 'Only super admins can promote to super admin.');

        $user->role = 'super_admin';
        $user->status = $user->status ?: 'active';
        $user->save();

        return back()->with('success', 'User promoted to Super Admin.');
    }

    public function toggleVerified(User $user)
    {
        try {
            $user->is_verified = !(bool) $user->is_verified;
            $user->save();
            $state = $user->is_verified ? 'verified' : 'unverified';
            return back()->with('success', "{$user->name} marked as {$state}.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not update verification status: column may not exist yet.');
        }
    }

    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate(['role' => 'required|in:user,seller,admin,super_admin']);
        if ($user->role === 'super_admin' && auth()->id() !== $user->id) {
            return back()->with('error', 'Cannot change super admin role.');
        }
        $user->role = $data['role'];
        $user->save();
        return back()->with('success', "{$user->name}'s role updated.");
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
            return back()->with('error', 'You cannot delete your own account.');
        }

        if (in_array($user->role, ['admin', 'super_admin'])) {
            return back()->with('error', 'Admin accounts cannot be deleted here. Remove their admin role first.');
        }

        // Cascade delete listings and stores
        $user->listings()->delete();
        foreach ($user->stores as $store) {
            $store->listings()->delete();
            $store->delete();
        }

        $name = $user->name;
        $user->delete();

        return redirect('/admin/users')->with('success', "User \"{$name}\" and all their data deleted permanently.");
    }
}
