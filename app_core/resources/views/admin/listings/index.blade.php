@extends('layouts.admin')
@section('title','Products & Ads')
@section('page','Products / Ads')
@section('heading','Product / Ads Management')
@section('subheading','Post, edit, approve and promote marketplace listings')
@section('actions')<a href="/admin/listings/create" class="ka-btn ka-btn-primary">+ Post Ad</a>@endsection
@section('content')
<section class="sa-card">
<div class="sa-head"><div><p>Super Admin</p><h1>Product / Ads Management</h1></div>
<form class="sa-search" method="get"><input name="q" value="{{ request('q') }}" placeholder="Search listing"><select name="type"><option value="">All Types</option><option value="product" @selected(request('type')==='product')>Product</option><option value="classified" @selected(request('type')==='classified')>Classified</option></select><select name="status"><option value="">All Status</option><option value="pending" @selected(request('status')==='pending')>Pending</option><option value="approved" @selected(request('status')==='approved')>Approved</option><option value="rejected" @selected(request('status')==='rejected')>Rejected</option></select><button>Filter</button></form></div>
<div class="sa-table-wrap"><table class="sa-table"><thead><tr><th>Listing</th><th>Type</th><th>Seller / Store</th><th>Category</th><th>Location</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($listings as $listing)
<tr><td><b>{{ $listing->title }}</b><small>{{ $listing->slug }}</small></td><td>{{ ucfirst($listing->type ?? 'listing') }}</td><td>{{ $listing->store->name ?? $listing->user->name ?? 'Seller' }}</td><td>{{ $listing->category->name ?? 'General' }}</td><td>{{ $listing->locationModel->name ?? $listing->location ?? '-' }}</td><td>LKR {{ number_format($listing->price ?? 0) }}</td><td><span class="sa-status {{ $listing->status }}">{{ ucfirst($listing->status) }}</span></td><td class="sa-actions-inline"><a href="/listings/{{ $listing->slug }}" target="_blank" title="View">View</a><a href="/admin/listings/{{ $listing->id }}/edit" title="Edit">Edit</a><form method="post" action="/admin/listings/{{ $listing->id }}/approve">@csrf<button title="Approve">Approve</button></form><form method="post" action="/admin/listings/{{ $listing->id }}/reject">@csrf<button class="danger" title="Reject">Reject</button></form><form method="post" action="/admin/listings/{{ $listing->id }}/feature">@csrf<button title="Feature">Feature</button></form><form method="post" action="/admin/listings/{{ $listing->id }}/top">@csrf<button title="Top">Top</button></form></td></tr>
@empty <tr><td colspan="8">No listings found.</td></tr>@endforelse
</tbody></table></div>{{ $listings->links() }}</section>
@endsection
