@extends('layouts.admin')

@section('title','Store Management')

@section('page','Stores')
@section('heading','Store Management')
@section('subheading','Approve, suspend and feature stores across the marketplace')

@section('actions')
<form class="sa-search" method="get" style="display:flex;gap:8px">
    <input name="q" value="{{ request('q') }}" placeholder="Search store" style="height:40px;border:1px solid var(--ka-border);border-radius:10px;padding:0 12px">
    <select name="status" style="height:40px;border:1px solid var(--ka-border);border-radius:10px;padding:0 10px">
        <option value="">All Status</option>
        <option value="pending" @selected(request('status')==='pending')>Pending</option>
        <option value="approved" @selected(request('status')==='approved')>Approved</option>
        <option value="suspended" @selected(request('status')==='suspended')>Suspended</option>
    </select>
    <button class="ka-btn ka-btn-light">Filter</button>
</form>
@endsection

@section('content')
<section class="sa-card">
    <div class="sa-card-head"><h2>Stores from Database</h2><span>{{ $stores->total() }} stores</span></div>
    <div class="sa-table-wrap">
        <table class="sa-table">
            <thead><tr><th>Store</th><th>Owner</th><th>Contact</th><th>Listings</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($stores as $store)
                <tr>
                    <td><b>{{ $store->name }}</b><small>/store/{{ $store->slug }}</small></td>
                    <td>{{ $store->user->name ?? 'Owner' }}</td>
                    <td>{{ $store->phone ?? '-' }}<small>{{ $store->email ?? '' }}</small></td>
                    <td>{{ $store->listings_count }}</td>
                    <td><span class="sa-status {{ $store->status }}">{{ ucfirst($store->status) }}</span></td>
                    <td class="sa-actions-inline">
                        <a href="/store/{{ $store->slug }}" target="_blank">View</a>
                        <form method="post" action="/admin/stores/{{ $store->id }}/approve">@csrf<button>Approve</button></form>
                        <form method="post" action="/admin/stores/{{ $store->id }}/suspend">@csrf<button class="danger">Suspend</button></form>
                        <form method="post" action="/admin/stores/{{ $store->id }}/feature">@csrf<button>Feature</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">No stores found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $stores->links() }}
</section>
@endsection
