@extends('layouts.admin')
@section('title','Role Management')
@section('page','Roles')
@section('heading','Role Management')
@section('subheading','Create custom admin roles and control exactly which sections each role can access')

@push('styles')

@endpush

@section('content')

@if(session('success'))
<div class="alert-success">✓ {{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert-error">
    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
</div>
@endif

@php $permGroups = collect($permissions)->groupBy(fn($p) => $p['group']); @endphp

{{-- Create new role --}}
<div class="rol-card" class="mb-24">
    <div class="rol-card-head">
        <div class="rol-card-icon green">✚</div>
        <span class="rol-card-title">Create New Role</span>
    </div>
    <form method="post" action="/admin/roles">
    @csrf
    <div class="rol-form-body">
        <div class="rol-grid">
            <div class="rol-field">
                <label class="rol-label">Role Name</label>
                <input name="name" value="{{ old('name') }}" class="rol-input" required placeholder="e.g. Content Moderator">
            </div>
            <div class="rol-field">
                <label class="rol-label">Description</label>
                <input name="description" value="{{ old('description') }}" class="rol-input" placeholder="What can this role do?">
            </div>
        </div>
        <label class="rol-label">Permissions</label>
        <div class="rol-perm-grid">
            @foreach($permGroups as $group => $perms)
            <div class="rol-perm-group">
                <div class="rol-perm-group-title">{{ $group }}</div>
                @foreach($perms as $key => $perm)
                <label class="rol-perm-item">
                    <input type="checkbox" name="permissions[]" value="{{ $key }}">
                    {{ $perm['label'] }}
                </label>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
    <div class="rol-foot">
        <button type="submit" class="rol-btn-primary">Create Role</button>
    </div>
    </form>
</div>

{{-- Existing roles --}}
<div class="rol-card">
    <div class="rol-card-head">
        <div class="rol-card-icon purple">🛡️</div>
        <span class="rol-card-title">Existing Roles</span>
        <span class="rol-card-meta">{{ $roles->count() }} roles</span>
    </div>
    @foreach($roles as $role)
    <details class="rol-role-item">
        <summary class="rol-role-summary">
            <div class="rol-role-avatar" style="background:{{ $role->is_super ? '#f3e8ff' : ($role->is_admin_level ? '#e8f5e9' : '#f5f5f5') }}">
                {{ $role->is_super ? '👑' : ($role->is_admin_level ? '🛡️' : '👤') }}
            </div>
            <div class="flex-grow-min">
                <div class="rol-role-name">
                    {{ $role->name }}
                    @if($role->is_protected)<span class="rol-role-built-in">BUILT-IN</span>@endif
                </div>
                <div class="rol-role-desc">{{ $role->description ?: '—' }} · {{ $role->users_count }} user(s)</div>
            </div>
            <div class="rol-role-badge">
                @if($role->is_super) Full access
                @elseif(!$role->is_admin_level) No admin access
                @else {{ count($role->permissions ?? []) }} permission(s)
                @endif
            </div>
            <span class="rol-expand-icon">▾</span>
        </summary>

        <div class="rol-role-content">
        @if($role->is_super)
            <p class="text-13-slate">The Super Admin role always has complete access to everything, including role management. It cannot be edited or deleted.</p>
        @elseif(!$role->is_admin_level)
            <p class="text-13-slate">Regular marketplace account — no admin panel access. This built-in role cannot be edited.</p>
        @else
            <form method="post" action="/admin/roles/{{ $role->id }}">
            @csrf @method('PUT')
            <div class="grid-1-2-mb14">
                <input name="name" value="{{ $role->name }}" required {{ $role->is_protected ? 'readonly' : '' }} class="rol-input" {{ $role->is_protected ? 'style=background:#f8fafc' : '' }}>
                <input name="description" value="{{ $role->description }}" placeholder="Description" class="rol-input">
            </div>
            <div class="rol-perm-grid" class="mb-14">
                @foreach($permGroups as $group => $perms)
                <div class="rol-perm-group">
                    <div class="rol-perm-group-title">{{ $group }}</div>
                    @foreach($perms as $key => $perm)
                    <label class="rol-perm-item">
                        <input type="checkbox" name="permissions[]" value="{{ $key }}" @checked(in_array($key, $role->permissions ?? []))>
                        {{ $perm['label'] }}
                    </label>
                    @endforeach
                </div>
                @endforeach
            </div>
            <div class="flex-g10">
                <button type="submit" class="rol-btn-primary" class="fs-13">💾 Save Changes</button>
                @if(!$role->is_protected)
                <button type="submit" form="delRole{{ $role->id }}" class="rol-btn-danger" class="fs-13">🗑 Delete Role</button>
                @endif
            </div>
            </form>
            @if(!$role->is_protected)
            <form id="delRole{{ $role->id }}" method="post" action="/admin/roles/{{ $role->id }}" onsubmit="return confirm('Delete role \'{{ addslashes($role->name) }}\'?')">@csrf @method('DELETE')</form>
            @endif
        @endif
        </div>
    </details>
    @endforeach
</div>

@endsection
