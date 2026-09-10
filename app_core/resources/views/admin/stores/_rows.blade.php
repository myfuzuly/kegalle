@forelse($stores as $store)
    <tr>
        <td class="w64-pr0">
            @if($store->logo)
                <img class="thumb-44b" src="{{ asset('storage/'.$store->logo) }}" alt="{{ $store->name }}">
            @else
                <span class="icon-box-44b">🏬</span>
            @endif
        </td>
        <td class="mw-180">
            <b class="trunc-170">{{ $store->name }}</b>
            <small>{{ $store->status }}</small>
        </td>
        <td>{{ $store->user->name ?? 'Owner' }}</td>
        <td>{{ $store->phone ?? '-' }}<small>{{ $store->email ?? '' }}</small></td>
        <td>{{ $store->listings_count }}</td>
        <td>
            @php $limit = (int)($store->user->store_limit ?? 1); $owned = $store->user?->stores->count() ?? 0; @endphp
            <span style="font-weight:700;color:{{ $owned >= $limit ? '#c62828' : '#1b5e20' }}">{{ $owned }}/{{ $limit }}</span>
            @if($owned >= $limit)<span class="fs10-red-block">Locked</span>@endif
        </td>
        <td><span class="sa-status {{ $store->status }}">{{ ucfirst($store->status) }}</span></td>
        <td><span class="sa-status {{ $store->is_verified ? 'active' : 'suspended' }}">{{ $store->is_verified ? '✓ Verified' : '✕ No' }}</span></td>
        <td><span class="sa-status {{ $store->is_featured ? 'active' : 'suspended' }}">{{ $store->is_featured ? '★ Featured' : '— No' }}</span></td>
        <td class="sa-actions-inline">
            <a href="/store/{{ $store->slug }}" target="_blank" title="View">View</a>
            <a href="/admin/stores/{{ $store->id }}/edit" title="Edit">Edit</a>
            <form method="post" action="/admin/stores/{{ $store->id }}/suspend" onsubmit="return confirm('Suspend this store?')">@csrf<button class="danger" title="Suspend">Suspend</button></form>
            <form method="post" action="/admin/stores/{{ $store->id }}/verify">@csrf<button title="{{ $store->is_verified ? 'Unverify' : 'Verify' }}">{{ $store->is_verified ? 'Unverify' : 'Verify' }}</button></form>
            <form method="post" action="/admin/stores/{{ $store->id }}/feature">@csrf<button title="{{ $store->is_featured ? 'Unfeature' : 'Feature' }}">{{ $store->is_featured ? 'Unfeature' : 'Feature' }}</button></form>
        </td>
    </tr>
@empty
    <tr><td colspan="10" class="td-empty">No stores found.</td></tr>
@endforelse
