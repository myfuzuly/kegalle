@forelse($locations as $location)
<tr>
    <td>
        <b>{{ $location->name }}</b>
        <small>#{{ $location->id }}</small>
    </td>
    <td>{{ $location->parent->name ?? '-' }}</td>
    <td>{{ ucfirst($location->type) }}</td>
    <td>{{ $location->slug }}</td>
    <td>
        <span class="sa-status {{ $location->is_active ? 'active' : 'suspended' }}">
            {{ $location->is_active ? 'Active' : 'Inactive' }}
        </span>
    </td>
    <td>{{ $location->sort_order }}</td>
    <td class="sa-actions-inline">
        <a href="/admin/locations/{{ $location->id }}/edit">Edit</a>
        <form method="post" action="/admin/locations/{{ $location->id }}/toggle">@csrf<button>Toggle</button></form>
        <form method="post" action="/admin/locations/{{ $location->id }}" data-confirm="Delete this location?" >@csrf @method('DELETE')<button class="danger">Delete</button></form>
    </td>
</tr>
@empty
<x-admin.table-empty colspan="7" message="No locations found." hint="Try a different filter or clear the search." />
@endforelse
