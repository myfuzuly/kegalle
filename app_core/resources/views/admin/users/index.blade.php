@extends('layouts.admin')

@section('title','User Management')

@section('page','Users')
@section('heading','User Management')
@section('subheading','Manage roles, status and access for all marketplace users')

@section('actions')
<form class="sa-search" method="get" style="display:flex;gap:8px">
    <input name="q" value="{{ request('q') }}" placeholder="Search name or email" style="height:40px;border:1px solid var(--ka-border);border-radius:10px;padding:0 12px">
    <button class="ka-btn ka-btn-light">Search</button>
</form>
@endsection

@section('content')
<section class="sa-card">
    <div class="sa-card-head"><h2>Users from Database</h2><span>{{ $users->total() }} users</span></div>
    <div class="sa-table-wrap">
        <table class="sa-table sa-table-users">
            <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Status</th><th>Store Limit</th><th>Email Verified</th><th>Joined</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td><b>{{ $user->name }}</b><small>#{{ $user->id }}</small></td>
                    <td>{{ $user->email }}</td>
                    <td><span class="sa-status approved">{{ $user->role ?? 'user' }}</span></td>
                    <td><span class="sa-status {{ $user->status ?? 'active' }}">{{ ucfirst($user->status ?? 'active') }}</span></td>
                    <td>
                        <form method="post" action="/admin/users/{{ $user->id }}/store-limit" style="display:inline-flex;gap:4px;align-items:center">@csrf
                            <input type="number" name="store_limit" value="{{ $user->store_limit ?? 1 }}" min="1" max="999" style="width:58px;height:30px;border:1px solid #e5e8ef;border-radius:8px;font-size:12.5px;padding:0 6px;text-align:center" title="Max stores this user can create">
                            <button type="submit" style="height:30px;padding:0 10px;border:none;border-radius:8px;background:#e8f5e9;color:#2e7d32;font-size:11.5px;font-weight:700;cursor:pointer">Set</button>
                        </form>
                    </td>
                    <td><span class="sa-status {{ $user->email_verified_at ? 'active' : 'suspended' }}">{{ $user->email_verified_at ? 'Verified' : 'Not Verified' }}</span></td>
                    <td>{{ $user->created_at?->format('Y-m-d') }}</td>
                    <td class="sa-actions-inline">
                        @if(auth()->user()->role === 'super_admin')
                            @php $allRoles = \App\Models\Role::orderBy('sort_order')->get(); @endphp
                            <form method="post" action="/admin/users/{{ $user->id }}/assign-role" style="display:inline-flex;gap:4px;align-items:center">@csrf
                                <select name="role_key" style="height:30px;border:1px solid #e5e8ef;border-radius:8px;font-size:12px;padding:0 6px" onchange="this.form.submit()">
                                    @foreach($allRoles as $r)
                                        <option value="{{ $r->key }}" @selected($user->role === $r->key)>{{ $r->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @endif
                        @if(in_array($user->status ?? 'active', ['inactive','suspended']))
                            <form method="post" action="/admin/users/{{ $user->id }}/activate">@csrf<button>Activate</button></form>
                        @else
                            <form method="post" action="/admin/users/{{ $user->id }}/suspend">@csrf<button class="danger">Suspend</button></form>
                        @endif
                        @if($user->id !== auth()->id() && !in_array($user->role, ['admin','super_admin']))
                            <form method="post" action="/admin/users/{{ $user->id }}" onsubmit="return confirm('Permanently delete this user? This cannot be undone.')">@csrf @method('DELETE')<button class="danger">Delete</button></form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8">No users found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
</section>
@endsection
