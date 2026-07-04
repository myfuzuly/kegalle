@extends('layouts.admin')
@section('title','Role Management')
@section('page','Roles')
@section('heading','Role Management')
@section('subheading','Create custom admin roles and control exactly which sections each role can access')
@section('content')

@if(session('success'))
<div style="background:#E8F5E9;color:#2E7D32;padding:12px 18px;border-radius:12px;margin-bottom:18px;font-weight:600;font-size:13px">{{ session('success') }}</div>
@endif
@if($errors->any())
<div style="background:#FFEBEE;color:#C62828;padding:12px 18px;border-radius:12px;margin-bottom:18px;font-size:13px">
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif

@php $permGroups = collect($permissions)->groupBy(fn ($p) => $p['group']); @endphp

{{-- Create new role --}}
<section class="sa-card" style="margin-bottom:24px">
    <div class="sa-card-head"><h2>➕ Create New Role</h2></div>
    <form method="post" action="/admin/roles" style="padding:20px">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 2fr;gap:14px;margin-bottom:16px">
            <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:6px">Role Name</label>
                <input name="name" value="{{ old('name') }}" required placeholder="e.g. Content Moderator" style="width:100%;height:42px;border:1.5px solid #e5e8ef;border-radius:10px;padding:0 14px;font-size:14px">
            </div>
            <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:6px">Description</label>
                <input name="description" value="{{ old('description') }}" placeholder="What can this role do?" style="width:100%;height:42px;border:1.5px solid #e5e8ef;border-radius:10px;padding:0 14px;font-size:14px">
            </div>
        </div>
        <label style="font-weight:600;font-size:13px;display:block;margin-bottom:10px">Permissions</label>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;margin-bottom:18px">
            @foreach($permGroups as $group => $perms)
                <div style="background:#f8fafc;border-radius:12px;padding:14px 16px">
                    <div style="font-size:11px;font-weight:800;color:#98a2b3;text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px">{{ $group }}</div>
                    @foreach($perms as $key => $perm)
                        <label style="display:flex;align-items:center;gap:8px;font-size:13px;padding:4px 0;cursor:pointer">
                            <input type="checkbox" name="permissions[]" value="{{ $key }}"> {{ $perm['label'] }}
                        </label>
                    @endforeach
                </div>
            @endforeach
        </div>
        <button class="ka-btn ka-btn-primary">Create Role</button>
    </form>
</section>

{{-- Existing roles --}}
<section class="sa-card">
    <div class="sa-card-head"><h2>🎭 Existing Roles</h2><span>{{ $roles->count() }} roles</span></div>
    @foreach($roles as $role)
        <details style="border-bottom:1px solid #f0f2f7">
            <summary style="display:flex;align-items:center;gap:14px;padding:16px 20px;cursor:pointer;list-style:none">
                <div style="width:40px;height:40px;border-radius:10px;background:{{ $role->is_super ? '#f3e8ff' : ($role->is_admin_level ? '#e8f5e9' : '#f5f5f5') }};display:flex;align-items:center;justify-content:center;font-size:17px">
                    {{ $role->is_super ? '👑' : ($role->is_admin_level ? '🛡️' : '👤') }}
                </div>
                <div style="flex:1;min-width:0">
                    <b style="font-size:14px">{{ $role->name }}</b>
                    @if($role->is_protected)<span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;background:#eceff1;color:#607d8b;margin-left:6px">BUILT-IN</span>@endif
                    <div style="font-size:12.5px;color:#667085">{{ $role->description ?: '—' }} · {{ $role->users_count }} user(s)</div>
                </div>
                <span style="font-size:12px;color:#667085">
                    @if($role->is_super) Full access
                    @elseif(!$role->is_admin_level) No admin access
                    @else {{ count($role->permissions ?? []) }} permission(s)
                    @endif
                </span>
                <span style="color:#98a2b3">▾</span>
            </summary>

            @if($role->is_super)
                <div style="padding:0 20px 18px 74px;font-size:13px;color:#667085">The Super Admin role always has complete access to everything, including role management. It cannot be edited or deleted.</div>
            @elseif(!$role->is_admin_level)
                <div style="padding:0 20px 18px 74px;font-size:13px;color:#667085">Regular marketplace account — no admin panel access. This built-in role cannot be edited.</div>
            @else
                <form method="post" action="/admin/roles/{{ $role->id }}" style="padding:0 20px 20px 74px">
                    @csrf @method('PUT')
                    <div style="display:grid;grid-template-columns:1fr 2fr;gap:14px;margin-bottom:14px">
                        <input name="name" value="{{ $role->name }}" required {{ $role->is_protected ? 'readonly style=background:#f8fafc;' : '' }} style="height:40px;border:1.5px solid #e5e8ef;border-radius:10px;padding:0 14px;font-size:13.5px">
                        <input name="description" value="{{ $role->description }}" placeholder="Description" style="height:40px;border:1.5px solid #e5e8ef;border-radius:10px;padding:0 14px;font-size:13.5px">
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;margin-bottom:14px">
                        @foreach($permGroups as $group => $perms)
                            <div>
                                <div style="font-size:10.5px;font-weight:800;color:#98a2b3;text-transform:uppercase;margin-bottom:6px">{{ $group }}</div>
                                @foreach($perms as $key => $perm)
                                    <label style="display:flex;align-items:center;gap:7px;font-size:12.5px;padding:3px 0;cursor:pointer">
                                        <input type="checkbox" name="permissions[]" value="{{ $key }}" @checked(in_array($key, $role->permissions ?? []))> {{ $perm['label'] }}
                                    </label>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    <div style="display:flex;gap:10px">
                        <button class="ka-btn ka-btn-primary" style="font-size:13px">Save Changes</button>
                        @if(!$role->is_protected)
                            <button type="submit" form="delRole{{ $role->id }}" class="ka-btn" style="background:#ffebee;color:#c62828;border:none;font-size:13px;cursor:pointer">Delete Role</button>
                        @endif
                    </div>
                </form>
                @if(!$role->is_protected)
                    <form id="delRole{{ $role->id }}" method="post" action="/admin/roles/{{ $role->id }}" onsubmit="return confirm('Delete role \'{{ addslashes($role->name) }}\'?')">@csrf @method('DELETE')</form>
                @endif
            @endif
        </details>
    @endforeach
</section>

@endsection
