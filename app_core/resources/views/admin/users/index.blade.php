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
        <table class="sa-table">
            <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td><b>{{ $user->name }}</b><small>#{{ $user->id }}</small></td>
                    <td>{{ $user->email }}</td>
                    <td><span class="sa-status approved">{{ $user->role ?? 'user' }}</span></td>
                    <td><span class="sa-status {{ $user->status ?? 'active' }}">{{ $user->status ?? 'active' }}</span></td>
                    <td>{{ $user->created_at?->format('Y-m-d') }}</td>
                    <td class="sa-actions-inline">
                        <form method="post" action="/admin/users/{{ $user->id }}/make-admin">@csrf<button>Admin</button></form>
                        <form method="post" action="/admin/users/{{ $user->id }}/make-super-admin">@csrf<button>Super</button></form>
                        @if(($user->status ?? 'active') === 'suspended')
                            <form method="post" action="/admin/users/{{ $user->id }}/activate">@csrf<button>Activate</button></form>
                        @else
                            <form method="post" action="/admin/users/{{ $user->id }}/suspend">@csrf<button class="danger">Suspend</button></form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">No users found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
</section>
@endsection
