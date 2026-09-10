@extends('layouts.admin')
@section('title','Government Services')
@section('page','Government Services')
@section('heading','Government Services Management')
@section('subheading','Manage the services displayed on the Government Services page')
@section('actions')<a href="/admin/government-services/create" class="ka-btn ka-btn-primary">+ Add Service</a>@endsection
@push('styles')

@endpush
@section('content')
<section class="sa-card">
<div class="sa-card-head"><h2>Government Services</h2></div>
<div class="sa-table-wrap"><table class="sa-table sa-table-government-services"><thead><tr><th>Icon</th><th>Title</th><th>Description</th><th>Contact</th><th>Sort</th><th>Status</th><th>Action</th></tr></thead><tbody>
@forelse($services as $service)
<tr>
    <td class="w64-pr0">
        @if($service->image)
            <img class="thumb-44" src="{{ asset('storage/'.$service->image) }}" alt="{{ $service->title }}">
        @else
            <span style="width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;background:linear-gradient(135deg,{{ $service->icon_bg_start }},{{ $service->icon_bg_end }});color:#fff">{{ $service->icon }}</span>
        @endif
    </td>
    <td class="max-w-200"><b class="text-truncate text-truncate-190">{{ $service->title }}</b><small class="text-gray">{{ $service->items_count }} items</small></td>
    <td><small>{{ \Illuminate\Support\Str::limit($service->description, 60) }}</small></td>
    <td><small>{{ $service->phone ?: '—' }}</small></td>
    <td>{{ $service->sort_order }}</td>
    <td><span class="sa-status {{ $service->is_active ? 'active' : 'suspended' }}">{{ $service->is_active ? 'Active' : 'Inactive' }}</span></td>
    <td class="sa-actions-inline">
        <a href="/admin/government-services/{{ $service->id }}/items">Items ({{ $service->items_count }})</a>
        <a href="/admin/government-services/{{ $service->id }}/edit">Edit</a>
        <form method="post" action="/admin/government-services/{{ $service->id }}/toggle">@csrf<button>Toggle</button></form>
        <form method="post" action="/admin/government-services/{{ $service->id }}" onsubmit="return confirm('Delete this service?')">@csrf @method('DELETE')<button>Delete</button></form>
    </td>
</tr>
@empty
<tr><td colspan="7">No government services yet. Click "+ Add Service" to create one.</td></tr>
@endforelse
</tbody></table></div>{{ $services->links() }}</section>
@endsection
