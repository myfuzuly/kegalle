<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    private function ensureSuperAdmin(): void
    {
        abort_if(auth()->user()->role !== 'super_admin', 403, 'Only the Super Admin can manage roles.');
    }

    public function index()
    {
        $this->ensureSuperAdmin();
        $roles = Role::withCount('users')->orderBy('sort_order')->orderBy('name')->get();
        $permissions = Role::PERMISSIONS;

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $this->ensureSuperAdmin();
        $data = $request->validate([
            'name' => 'required|string|max:60|unique:roles,name',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys(Role::PERMISSIONS)),
        ]);

        Role::create([
            'key' => Str::slug($data['name'], '_'),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_admin_level' => true,
            'is_super' => false,
            'is_protected' => false,
            'sort_order' => 10,
            'permissions' => array_values($data['permissions'] ?? []),
        ]);

        return back()->with('success', "Role \"{$data['name']}\" created.");
    }

    public function update(Request $request, Role $role)
    {
        $this->ensureSuperAdmin();

        if ($role->is_super) {
            return back()->with('success', 'The Super Admin role always has full access and cannot be edited.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:60|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys(Role::PERMISSIONS)),
        ]);

        $role->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'permissions' => array_values($data['permissions'] ?? []),
        ]);

        return back()->with('success', "Role \"{$role->name}\" updated.");
    }

    public function destroy(Role $role)
    {
        $this->ensureSuperAdmin();

        if ($role->is_protected) {
            return back()->with('success', 'Built-in roles cannot be deleted.');
        }

        $inUse = User::where('role', $role->key)->count();
        if ($inUse > 0) {
            return back()->with('success', "Cannot delete: {$inUse} user(s) still have this role. Reassign them first.");
        }

        $name = $role->name;
        $role->delete();

        return back()->with('success', "Role \"{$name}\" deleted.");
    }

    public function assignToUser(Request $request, User $user)
    {
        $this->ensureSuperAdmin();
        $data = $request->validate(['role_key' => 'required|exists:roles,key']);

        if ($user->id === auth()->id() && $data['role_key'] !== 'super_admin') {
            return back()->with('success', 'You cannot downgrade your own account.');
        }

        $user->role = $data['role_key'];
        $user->save();

        $roleName = Role::where('key', $data['role_key'])->value('name');
        return back()->with('success', "{$user->name} is now: {$roleName}.");
    }
}
