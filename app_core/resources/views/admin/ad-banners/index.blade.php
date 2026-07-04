@extends('layouts.admin')
@section('title','Ad Spaces')
@section('page','Ad Spaces')
@section('heading','Premium Ad Space Management')
@section('subheading','Manage sponsored banners shown across the site, by placement location')
@section('actions')<a href="/admin/ad-banners/create" class="ka-btn ka-btn-primary">+ Add Ad Banner</a>@endsection
@section('content')
<section class="sa-card">
<div class="sa-head"><div><p>Super Admin</p><h1>Premium Ad Space Management</h1></div>
<form class="sa-search" method="get">
    <select name="location">
        <option value="">All Locations</option>
        @foreach($locations as $key => $label)
            <option value="{{ $key }}" @selected(request('location') === $key)>{{ $label }}</option>
        @endforeach
    </select>
    <button>Filter</button>
</form>
</div>
<div class="sa-table-wrap"><table class="sa-table"><thead><tr><th>Image</th><th>Title</th><th>Location</th><th>Active Dates</th><th>Clicks</th><th>Status</th><th>Action</th></tr></thead><tbody>
@forelse($banners as $banner)
<tr>
    <td>@if($banner->image)<img src="{{ asset('storage/'.$banner->image) }}" alt="{{ $banner->title }}" style="width:54px;height:40px;border-radius:8px;object-fit:cover">@else<span style="width:54px;height:40px;border-radius:8px;background:#f5f7fb;display:flex;align-items:center;justify-content:center;font-size:16px">📢</span>@endif</td>
    <td><b>{{ $banner->title }}</b>@if($banner->link_url)<small>{{ $banner->link_url }}</small>@endif</td>
    <td>{{ $locations[$banner->location] ?? $banner->location }}</td>
    <td>
        @if($banner->starts_at || $banner->ends_at)
            <small>{{ $banner->starts_at?->format('M d, Y') ?? 'Always' }} – {{ $banner->ends_at?->format('M d, Y') ?? 'Always' }}</small>
        @else
            <small>Always running</small>
        @endif
    </td>
    <td>{{ number_format($banner->clicks) }}</td>
    <td><span class="sa-status {{ $banner->is_active ? 'active' : 'suspended' }}">{{ $banner->is_active ? 'Active' : 'Inactive' }}</span></td>
    <td class="sa-actions-inline">
        <a href="/admin/ad-banners/{{ $banner->id }}/edit" title="Edit">Edit</a>
        <form method="post" action="/admin/ad-banners/{{ $banner->id }}/toggle">@csrf<button title="Toggle">Toggle</button></form>
        <form method="post" action="/admin/ad-banners/{{ $banner->id }}" onsubmit="return confirm('Delete this ad banner?')">@csrf @method('DELETE')<button class="danger" title="Delete">Delete</button></form>
    </td>
</tr>
@empty
<tr><td colspan="7">No ad banners yet. Click "Add Ad Banner" to create one.</td></tr>
@endforelse
</tbody></table></div>{{ $banners->links() }}</section>
@endsection
