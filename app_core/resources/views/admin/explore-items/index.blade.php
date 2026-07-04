@extends('layouts.admin')
@section('title','Explore Kegalle')
@section('page','Explore Kegalle')
@section('heading','Explore in Kegalle — Section Management')
@section('subheading','Manage the homepage cards under "Explore in Kegalle – culture, nature, history and more"')
@section('actions')<a href="/admin/explore-items/create" class="ka-btn ka-btn-primary">+ Add Explore Card</a>@endsection
@section('content')
<section class="sa-card">
<div class="sa-head"><div><p>Super Admin</p><h1>Explore in Kegalle</h1></div></div>
<div class="sa-table-wrap"><table class="sa-table"><thead><tr><th>Card</th><th>Title</th><th>Description</th><th>Sort</th><th>Status</th><th>Action</th></tr></thead><tbody>
@forelse($items as $item)
<tr>
    <td>
        @if($item->image)
            <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}" style="width:54px;height:40px;border-radius:8px;object-fit:cover">
        @else
            <span style="width:54px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px;background:linear-gradient(135deg,{{ $item->gradient_start }},{{ $item->gradient_end }})">{{ $item->icon }}</span>
        @endif
    </td>
    <td><b>{{ $item->title }}</b><br><small style="color:#667085">/explore/{{ $item->slug }}</small></td>
    <td><small>{{ \Illuminate\Support\Str::limit($item->description ?: $item->items, 60) }}</small></td>
    <td>{{ $item->sort_order }}</td>
    <td><span class="sa-status {{ $item->is_active ? 'active' : 'suspended' }}">{{ $item->is_active ? 'Active' : 'Inactive' }}</span></td>
    <td class="sa-actions-inline">
        <a href="/admin/explore-items/{{ $item->id }}/items">Items ({{ $item->sub_items_count }})</a>
        <a href="/admin/explore-items/{{ $item->id }}/edit">Edit</a>
        <form method="post" action="/admin/explore-items/{{ $item->id }}/toggle">@csrf<button>Toggle</button></form>
        <form method="post" action="/admin/explore-items/{{ $item->id }}" onsubmit="return confirm('Delete this explore card?')">@csrf @method('DELETE')<button>Delete</button></form>
    </td>
</tr>
@empty
<tr><td colspan="6">No explore cards yet. Click "Add Explore Card" to create one.</td></tr>
@endforelse
</tbody></table></div>{{ $items->links() }}</section>
@endsection
