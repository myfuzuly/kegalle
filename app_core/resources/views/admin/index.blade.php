@extends('layouts.admin')

@section('title','Dashboard')
@section('page','Dashboard')
@section('heading','Dashboard')
@section('subheading','Marketplace Control Center · Kegalle')

@section('actions')
<a href="/admin/listings" class="ka-btn ka-btn-primary">Moderate Listings</a>
@endsection

@section('content')

<div class="ka-stat-grid">
    <article class="ka-stat-card">
        <span>Total Users</span>
        <strong>{{ number_format($stats['users'] ?? 0) }}</strong>
        <small>{{ number_format($stats['active_users'] ?? 0) }} active accounts</small>
    </article>

    <article class="ka-stat-card">
        <span>Stores</span>
        <strong>{{ number_format($stats['stores'] ?? 0) }}</strong>
        <small>{{ number_format($stats['pending_stores'] ?? 0) }} pending approval</small>
    </article>

    <article class="ka-stat-card">
        <span>Listings</span>
        <strong>{{ number_format($stats['listings'] ?? 0) }}</strong>
        <small>{{ number_format($stats['pending_listings'] ?? 0) }} pending approval</small>
    </article>

    <article class="ka-stat-card ka-stat-highlight">
        <span>Marketplace Value</span>
        <strong>LKR {{ number_format($stats['total_value'] ?? 0) }}</strong>
        <small>Approved listing value</small>
    </article>
</div>

<div class="ka-grid-2">
    <section class="ka-panel">
        <div class="ka-panel-head">
            <h2>Quick Actions</h2>
            <span>Super Admin</span>
        </div>

        <div class="ka-shortcut-grid">
            <a href="/admin/users"><i>👥</i><b>Users</b><small>Roles & access</small></a>
            <a href="/admin/stores"><i>🏬</i><b>Stores</b><small>Approve & feature</small></a>
            <a href="/admin/listings"><i>📦</i><b>Products / Ads</b><small>Moderate listings</small></a>
            <a href="/admin/categories"><i>🗂</i><b>Categories</b><small>Manage taxonomy</small></a>
            <a href="/admin/locations"><i>📍</i><b>Locations</b><small>District / city / town</small></a>
            <a href="/admin/settings"><i>⚙️</i><b>Settings</b><small>Platform config</small></a>
        </div>
    </section>

    <section class="ka-panel">
        <div class="ka-panel-head">
            <h2>Approval Queue</h2>
            <a href="/admin/listings">View all →</a>
        </div>

        @forelse($approvalQueue ?? [] as $listing)
            <div class="ka-queue-row">
                <div>
                    <b>{{ $listing->title }}</b>
                    <small>{{ $listing->category->name ?? 'General' }} · {{ ucfirst($listing->type ?? 'listing') }}</small>
                </div>

                <form method="post" action="/admin/listings/{{ $listing->id }}/approve">
                    @csrf
                    <button>Approve</button>
                </form>
            </div>
        @empty
            <div class="ka-empty-state">
                <b>✓</b>
                <p>All clear — no pending listings.</p>
            </div>
        @endforelse

        <div class="ka-health">
            <div><strong>{{ $stats['approved_listings'] ?? 0 }}</strong><span>Listings</span></div>
            <div><strong>{{ $stats['approved_stores'] ?? 0 }}</strong><span>Stores</span></div>
            <div><strong>{{ $stats['products'] ?? 0 }}</strong><span>Products</span></div>
            <div><strong>{{ $stats['classified'] ?? 0 }}</strong><span>Classifieds</span></div>
        </div>
    </section>
</div>

<section class="ka-panel">
    <div class="ka-panel-head">
        <h2>Latest Listings</h2>
        <a href="/admin/listings">Manage →</a>
    </div>

    <div class="ka-table-wrap">
        <table class="ka-table">
            <thead>
            <tr>
                <th>Listing</th>
                <th>Type</th>
                <th>Seller / Store</th>
                <th>Price</th>
                <th>Status</th>
                <th>View</th>
            </tr>
            </thead>
            <tbody>
            @forelse($latestListings ?? [] as $listing)
                <tr>
                    <td>
                        <b>{{ $listing->title }}</b>
                        <small>{{ $listing->category->name ?? 'General' }}</small>
                    </td>
                    <td><span class="ka-badge">{{ ucfirst($listing->type ?? 'listing') }}</span></td>
                    <td>{{ $listing->store->name ?? $listing->user->name ?? 'Seller' }}</td>
                    <td>LKR {{ number_format($listing->price ?? 0) }}</td>
                    <td><span class="ka-status {{ $listing->status }}">{{ ucfirst($listing->status) }}</span></td>
                    <td><a href="/listings/{{ $listing->slug }}" target="_blank" class="ka-link">Open</a></td>
                </tr>
            @empty
                <tr><td colspan="6">No listings found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>

@endsection
