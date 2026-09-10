@extends('layouts.store-dashboard')

@section('title', ($store->name ?? 'Store') . ' Dashboard')
@section('eyebrow', 'Overview')
@section('heading', $store->name ?? 'Store Dashboard')

@section('actions')
<a href="/dashboard/stores/{{ $store->id }}/products/create" class="kdl-tb-btn kdl-tb-btn-primary">
  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
  Add Product
</a>
@endsection

@section('content')

{{-- Stat cards --}}
<div class="dbi-grid">
    <a href="/dashboard/stores/{{ $store->id }}/products" class="dbi-stat dbi-stat-blue">
        <div class="dbi-ic dbi-ic-blue">📦</div>
        <div>
            <div class="dbi-num">{{ $stats['products'] ?? 0 }}</div>
            <div class="dbi-lbl">Total Products</div>
        </div>
    </a>
    <a href="/dashboard/stores/{{ $store->id }}/products?status=approved" class="dbi-stat dbi-stat-green">
        <div class="dbi-ic dbi-ic-green">✅</div>
        <div>
            <div class="dbi-num">{{ $stats['approved'] ?? 0 }}</div>
            <div class="dbi-lbl">Live Products</div>
        </div>
    </a>
    <a href="/dashboard/stores/{{ $store->id }}/products?status=pending" class="dbi-stat dbi-stat-amber">
        <div class="dbi-ic dbi-ic-amber">⏳</div>
        <div>
            <div class="dbi-num">{{ $stats['pending'] ?? 0 }}</div>
            <div class="dbi-lbl">Pending</div>
        </div>
    </a>
    <a href="/dashboard/stores/{{ $store->id }}/analytics" class="dbi-stat dbi-stat-purple">
        <div class="dbi-ic dbi-ic-purple">👁</div>
        <div>
            <div class="dbi-num">{{ $stats['views'] ?? 0 }}</div>
            <div class="dbi-lbl">Total Views</div>
        </div>
    </a>
</div>

{{-- Main two-column --}}
<div class="dbi-cols">

    {{-- Products list --}}
    <section class="dbi-card">
        <div class="dbi-card-head">
            <h3>Store Products</h3>
            <a href="/dashboard/stores/{{ $store->id }}/products">Manage all →</a>
        </div>
        @forelse(($products ?? []) as $listing)
        @php
            $pillCls = match($listing->status ?? 'pending') {
                'approved','active' => 'dbi-pill-green',
                'rejected' => 'dbi-pill-red',
                'sold' => 'dbi-pill-gray',
                default => 'dbi-pill-amber',
            };
        @endphp
        <div class="dbi-row">
            <div class="flex-grow-min">
                <div class="dbi-row-title">{{ $listing->title }}</div>
                <div class="dbi-row-meta">
                    <span class="dbi-pill {{ $pillCls }}">{{ ucfirst($listing->status ?? 'pending') }}</span>
                    <span class="dbi-row-time">Rs. {{ number_format($listing->price ?? 0) }}</span>
                </div>
            </div>
            <a href="/dashboard/stores/{{ $store->id }}/products/{{ $listing->id }}/edit" class="dbi-row-edit">Edit</a>
        </div>
        @empty
        <div class="dbi-empty">
            <strong>No products yet</strong>
            <p>Add your first product so buyers can find you.</p>
            <a href="/dashboard/stores/{{ $store->id }}/products/create" class="dbi-empty-btn">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add First Product
            </a>
        </div>
        @endforelse
    </section>

    {{-- Right column --}}
    <div class="flex-col gap-20">

        {{-- Store status --}}
        <section class="dbi-card">
            <div class="dbi-card-head"><h3>Store Status</h3></div>
            @php
                $storeStatus = $store->status ?? 'pending';
                $statusCls = match($storeStatus) { 'approved','active' => 'dbi-status-approved', 'rejected' => 'dbi-status-rejected', default => 'dbi-status-pending' };
                $statusIcon = match($storeStatus) { 'approved','active' => '✅', 'rejected' => '❌', default => '⏳' };
                $statusColor = match($storeStatus) { 'approved','active' => '#15803d', 'rejected' => '#dc2626', default => '#92400e' };
            @endphp
            <div class="dbi-status-box {{ $statusCls }}">
                <div class="fs-24">{{ $statusIcon }}</div>
                <div>
                    <div style="font-size:14px;font-weight:700;color:{{ $statusColor }}">{{ ucfirst($storeStatus) }}</div>
                    <div class="fs12-slate-mt2">Verification &amp; approval status</div>
                </div>
            </div>
        </section>

        {{-- Quick actions --}}
        <section class="dbi-card">
            <div class="dbi-card-head"><h3>Quick Actions</h3></div>
            <div class="dbi-qa">
                <a href="/dashboard/stores/{{ $store->id }}/products/create" class="dbi-qa-btn"><span class="dbi-qa-icon">➕</span><span class="dbi-qa-text">Add Product</span></a>
                <a href="/dashboard/stores/{{ $store->id }}/edit" class="dbi-qa-btn"><span class="dbi-qa-icon">✏️</span><span class="dbi-qa-text">Edit Profile</span></a>
                <a href="/dashboard/stores/{{ $store->id }}/analytics" class="dbi-qa-btn"><span class="dbi-qa-icon">📊</span><span class="dbi-qa-text">Analytics</span></a>
                <a href="/dashboard/stores/{{ $store->id }}/reviews" class="dbi-qa-btn"><span class="dbi-qa-icon">⭐</span><span class="dbi-qa-text">Reviews</span></a>
                <a href="/dashboard/stores/{{ $store->id }}/import" class="dbi-qa-btn"><span class="dbi-qa-icon">📥</span><span class="dbi-qa-text">Bulk Import</span></a>
                <a href="/dashboard/deals/create" class="dbi-qa-btn"><span class="dbi-qa-icon">🔥</span><span class="dbi-qa-text">Submit Deal</span></a>
                <a href="/dashboard/membership" class="dbi-qa-btn"><span class="dbi-qa-icon">💳</span><span class="dbi-qa-text">Membership</span></a>
                <a href="/store/{{ $store->slug }}" target="_blank" class="dbi-qa-btn"><span class="dbi-qa-icon">🌐</span><span class="dbi-qa-text">View Store</span></a>
            </div>
        </section>

    </div>
</div>

@endsection
