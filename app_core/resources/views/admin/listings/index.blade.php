@extends('layouts.admin')
@section('title','Products & Ads')
@section('page','Products / Ads')
@section('heading','Product / Ads Management')
@section('subheading','Post, edit, approve and promote store products and other listings — see Classifieds for personal ads')
@section('actions')<a href="/admin/listings/create" class="ka-btn ka-btn-primary">+ Post Ad</a>@endsection
@section('content')
<section class="sa-card">
<div class="sa-head"><div><p>Super Admin</p><h1>Product / Ads Management</h1></div>
<form class="sa-search" method="get"><input name="q" value="{{ request('q') }}" placeholder="Search listing"><select name="type"><option value="">All Types</option><option value="product" @selected(request('type')==='product')>Product</option><option value="buy" @selected(request('type')==='buy')>Buy</option><option value="sell" @selected(request('type')==='sell')>Sell</option><option value="exchange" @selected(request('type')==='exchange')>Exchange</option><option value="job" @selected(request('type')==='job')>Job</option><option value="to-let" @selected(request('type')==='to-let')>To-Let</option></select><button>Filter</button></form></div>
<div class="sa-table-wrap"><table class="sa-table sa-table-with-thumb"><thead><tr><th>Image</th><th>Name</th><th>Location</th><th>Seller</th><th>Price</th><th>Status</th><th>Action</th></tr></thead><tbody>
@forelse($listings as $listing)
@php $thumb = optional($listing->images->first())->path ?? $listing->image ?? null; @endphp
@php
    $statusYes = $listing->status === 'approved';
    $statusPending = $listing->status === 'pending';
    $markClass = $statusYes ? 'is-yes' : ($statusPending ? 'is-pending' : 'is-no');
    $markChar = $statusYes ? '✓' : ($statusPending ? '…' : '✕');
@endphp
<tr><td>@if($thumb)<img src="{{ asset('storage/'.ltrim($thumb,'/')) }}" alt="{{ $listing->title }}" style="width:40px;height:40px;border-radius:8px;object-fit:cover">@else<span style="width:40px;height:40px;border-radius:8px;background:var(--ka-bg,#f5f7fb);display:flex;align-items:center;justify-content:center;font-size:16px">🛍️</span>@endif</td><td><b>{{ $listing->title }}</b><small>{{ $listing->slug }}</small></td><td>{{ $listing->locationModel->name ?? $listing->location ?? '-' }}</td><td>{{ $listing->store->name ?? $listing->user->name ?? 'Seller' }}</td><td>LKR {{ number_format($listing->price ?? 0) }}</td><td><span style="display:inline-flex;align-items:center;gap:5px;padding:4px 11px;border-radius:20px;font-size:11.5px;font-weight:700;white-space:nowrap;{{ $statusYes ? 'background:#E8F5E9;color:#2E7D32' : ($statusPending ? 'background:#FFF3E0;color:#E65100' : 'background:#FFEBEE;color:#C62828') }}">{{ $markChar }} {{ ucfirst($listing->status) }}</span></td><td class="sa-actions-inline"><a href="/listings/{{ $listing->slug }}" target="_blank" title="View">View</a><a href="/admin/listings/{{ $listing->id }}/edit" title="Edit">Edit</a><form method="post" action="/admin/listings/{{ $listing->id }}/reject" onsubmit="return confirm('Reject this ad? It will move to the Danger Zone.')">@csrf<button class="danger" title="Reject — moves to Danger Zone">Reject</button></form><form method="post" action="/admin/listings/{{ $listing->id }}/feature">@csrf<button title="Feature">Feature</button></form><form method="post" action="/admin/listings/{{ $listing->id }}/top">@csrf<button title="Top">Top</button></form></td></tr>
@empty <tr><td colspan="7">No listings found.</td></tr>@endforelse
</tbody></table></div>{{ $listings->links('vendor.pagination.ka-admin') }}</section>
@endsection
