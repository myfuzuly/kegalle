@forelse($users as $u)
@php
    $initials = collect(explode(' ', $u->name))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->implode('');
    $roleMap = ['super_admin'=>['Super Admin','purple'],'admin'=>['Site Admin','blue'],'seller'=>['Seller','amber'],'user'=>['User','slate']];
    [$roleLabel,$roleColor] = $roleMap[$u->role] ?? ['Unknown','slate'];
    $isActive   = $u->status === 'active';
    $isSuspended= $u->status === 'suspended';
    $statusLabel = ucfirst($u->status ?? 'inactive');
    $statusColor = $isActive ? 'green' : ($isSuspended ? 'red' : 'slate');
    $verified = !is_null($u->email_verified_at);
@endphp
<tr>
    {{-- User --}}
    <td>
        <div class="um-user-cell">
            @if($u->avatar)
                <img class="um-avatar" src="{{ asset('storage/'.$u->avatar) }}" alt="{{ $u->name }}">
            @else
                <div class="um-avatar-init">{{ $initials }}</div>
            @endif
            <div>
                <a href="/admin/users/{{ $u->id }}" class="um-user-name" title="{{ $u->name }}" style="text-decoration:none;color:inherit">{{ $u->name }}</a>
                <div class="um-user-id">#{{ $u->id }}</div>
            </div>
        </div>
    </td>

    {{-- Email --}}
    <td style="font-size:12px;color:#475569;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $u->email }}">
        {{ $u->email }}
        @if($u->phone)
        <div style="color:#94a3b8;font-size:11px;margin-top:1px;">{{ $u->phone }}</div>
        @endif
    </td>

    {{-- Role --}}
    <td>
        <span class="um-badge um-badge-{{ $roleColor }}">{{ $roleLabel }}</span>
    </td>

    {{-- Status --}}
    <td>
        <span class="um-badge um-badge-{{ $statusColor }}">{{ $statusLabel }}</span>
    </td>

    {{-- Store Limit --}}
    <td>
        <form method="POST" action="/admin/users/{{ $u->id }}/store-limit" style="display:contents">
            @csrf @method('PATCH')
            <div class="um-limit-form">
                <input class="um-limit-input" type="number" name="store_limit" value="{{ $u->store_limit ?? 1 }}" min="0" max="99">
                <button type="submit" class="um-limit-btn" title="Save">✓</button>
            </div>
        </form>
    </td>

    {{-- Email verified --}}
    <td>
        @if($verified)
            <span class="um-badge um-badge-green">✓ Verified</span>
        @else
            <span class="um-badge um-badge-amber">Pending</span>
        @endif
    </td>

    {{-- Joined --}}
    <td style="white-space:nowrap;color:#64748b;font-size:12px;">
        {{ $u->created_at?->format('d M Y') }}
    </td>

    {{-- Actions --}}
    <td>
        <div class="um-actions">
            @if($u->role !== 'super_admin')
            <form method="POST" action="/admin/users/{{ $u->id }}/role">
                @csrf @method('PATCH')
                <select name="role" class="">
                    <option value="user"        {{ $u->role==='user'        ? 'selected':'' }}>User</option>
                    <option value="seller"      {{ $u->role==='seller'      ? 'selected':'' }}>Seller</option>
                    <option value="admin"       {{ $u->role==='admin'       ? 'selected':'' }}>Site Admin</option>
                    <option value="super_admin" {{ $u->role==='super_admin' ? 'selected':'' }}>Super Admin</option>
                </select>
            </form>
            @endif

            @if($isSuspended)
            <form method="POST" action="/admin/users/{{ $u->id }}/activate">
                @csrf @method('PATCH')
                <button type="submit" class="um-act-btn green">Activate</button>
            </form>
            @elseif($isActive)
            <form method="POST" action="/admin/users/{{ $u->id }}/suspend">
                @csrf @method('PATCH')
                <button type="submit" class="um-act-btn red">Suspend</button>
            </form>
            @endif

            <a href="/admin/users/{{ $u->id }}" class="um-act-btn slate">View</a>

            @if($u->role !== 'super_admin')
            <form method="POST" action="/admin/users/{{ $u->id }}" class="um-delete-form">
                @csrf @method('DELETE')
                <button type="submit" class="um-act-btn red" data-confirm="Permanently delete {{ addslashes($u->name) }}? This cannot be undone.">Delete</button>
            </form>
            @endif
        </div>
    </td>
</tr>
@empty
<tr><td colspan="8" class="um-empty">No users found.</td></tr>
@endforelse
