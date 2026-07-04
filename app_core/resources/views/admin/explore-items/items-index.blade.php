@extends('layouts.admin')
@section('title', $explore->title . ' — Items')
@section('page','Explore Kegalle')
@section('heading', $explore->title . ' — Sub Items')
@section('subheading','Manage individual entries under ' . $explore->title)
@section('actions')
<a href="/admin/explore-items/{{ $explore->id }}/items/create" class="ka-btn ka-btn-primary">+ Add Item</a>
<a class="ka-btn ka-btn-light" href="/admin/explore-items">← All Explore Cards</a>
@endsection
@section('content')
<section class="sa-card">
<div class="sa-head"><div><p>{{ $explore->title }}</p><h1>Sub Items ({{ $items->total() }})</h1></div></div>
<div class="sa-table-wrap"><table class="sa-table"><thead><tr><th>#</th><th>Name</th><th>Phone</th><th>Address</th><th>Sort</th><th>Status</th><th>Action</th></tr></thead><tbody>
@forelse($items as $item)
<tr>
    <td>{{ $item->id }}</td>
    <td><b>{{ $item->name }}</b>@if($item->description)<br><small style="color:#667085">{{ \Illuminate\Support\Str::limit($item->description, 50) }}</small>@endif</td>
    <td><small>{{ $item->phone ?: '—' }}</small></td>
    <td><small>{{ \Illuminate\Support\Str::limit($item->address, 40) ?: '—' }}</small></td>
    <td>{{ $item->sort_order }}</td>
    <td><span class="sa-status {{ $item->is_active ? 'active' : 'suspended' }}">{{ $item->is_active ? 'Active' : 'Inactive' }}</span></td>
    <td class="sa-actions-inline">
        <a href="/admin/explore-items/{{ $explore->id }}/items/{{ $item->id }}/edit">Edit</a>
        <form method="post" action="/admin/explore-items/{{ $explore->id }}/items/{{ $item->id }}/toggle">@csrf<button>Toggle</button></form>
        <form method="post" action="/admin/explore-items/{{ $explore->id }}/items/{{ $item->id }}" onsubmit="return confirm('Delete this item?')">@csrf @method('DELETE')<button>Delete</button></form>
    </td>
</tr>
@empty
<tr><td colspan="7">No items yet. Click "+ Add Item" to create one.</td></tr>
@endforelse
</tbody></table></div>{{ $items->links() }}</section>
@endsection
