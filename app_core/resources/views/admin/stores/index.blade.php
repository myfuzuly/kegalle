@extends('layouts.admin')

@section('title','Store Management')

@section('page','Stores')
@section('heading','Store Management')
@section('subheading','Approve, suspend and feature stores across the marketplace')

@section('actions')
<div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
    <a href="/admin/stores/create" class="ka-btn ka-btn-primary">+ Create Store</a>
    <form class="sa-search" method="get" style="display:flex;gap:8px">
        <input name="q" value="{{ request('q') }}" placeholder="Search store" style="height:40px;border:1px solid var(--ka-border);border-radius:10px;padding:0 12px">
        <select name="featured" style="height:40px;border:1px solid var(--ka-border);border-radius:10px;padding:0 10px">
            <option value="">All Stores</option>
            <option value="1" @selected(request('featured')==='1')>Featured Only</option>
            <option value="0" @selected(request('featured')==='0')>Not Featured</option>
        </select>
        <button class="ka-btn ka-btn-light">Filter</button>
    </form>
</div>
@endsection

@section('content')
<section class="sa-card">
    <div class="sa-card-head"><h2>Stores from Database</h2><span>{{ $stores->total() }} stores</span></div>
    <div class="sa-table-wrap">
        <table class="sa-table sa-table-stores-mgmt">
            <thead><tr><th>Logo</th><th>Store</th><th>Owner</th><th>Contact</th><th>Listings</th><th>Status</th><th>Verified</th><th>Featured</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($stores as $store)
                <tr>
                    <td>
                        @if($store->logo)
                            <img src="{{ asset('storage/'.$store->logo) }}" alt="{{ $store->name }}" style="width:44px;height:44px;border-radius:9px;object-fit:cover">
                        @else
                            <span style="width:44px;height:44px;border-radius:9px;background:#f5f7fb;display:flex;align-items:center;justify-content:center;font-size:16px">🏬</span>
                        @endif
                    </td>
                    <td><b>{{ $store->name }}</b><small>/store/{{ $store->slug }}</small></td>
                    <td>{{ $store->user->name ?? 'Owner' }}</td>
                    <td>{{ $store->phone ?? '-' }}<small>{{ $store->email ?? '' }}</small></td>
                    <td>{{ $store->listings_count }}</td>
                    <td><span class="sa-status {{ $store->status }}">{{ ucfirst($store->status) }}</span></td>
                    <td><span class="sa-status {{ $store->is_verified ? 'active' : 'suspended' }}">{{ $store->is_verified ? '✓ Verified' : '✕ No' }}</span></td>
                    <td><span class="sa-status {{ $store->is_featured ? 'active' : 'suspended' }}">{{ $store->is_featured ? 'Featured' : 'Not Featured' }}</span></td>
                    <td class="sa-actions-inline">
                        <a href="/store/{{ $store->slug }}" target="_blank">View</a>
                        <a href="/admin/stores/{{ $store->id }}/edit">Edit</a>
                        <form method="post" action="/admin/stores/{{ $store->id }}/suspend" onsubmit="return confirm('Suspend this store? It will move to the Danger Zone.')">@csrf<button class="danger" title="Suspend — moves to Danger Zone">Suspend</button></form>
                        <form method="post" action="/admin/stores/{{ $store->id }}/verify">@csrf<button>{{ $store->is_verified ? 'Unverify' : 'Verify' }}</button></form>
                        <form method="post" action="/admin/stores/{{ $store->id }}/feature">@csrf<button>{{ $store->is_featured ? 'Unfeature' : 'Feature' }}</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9">No stores found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $stores->links('vendor.pagination.ka-admin') }}
</section>
@endsection
