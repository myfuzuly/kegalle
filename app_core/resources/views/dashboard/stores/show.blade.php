@extends('layouts.store-dashboard')

@section('title', ($store->name ?? 'Store') . ' Dashboard')
@section('heading', $store->name ?? 'Store Dashboard')
@section('subheading','Control store profile, products, enquiries and growth.')

@section('actions')
<a href="/dashboard/stores/{{ $store->id }}/products/create" class="kd-btn kd-btn-primary">+ Add Product</a>
@endsection

@section('content')
<div class="kd-widget-grid">
    <article class="kd-widget"><span>Products</span><strong>{{ $stats['products'] ?? 0 }}</strong><small>Total store products</small></article>
    <article class="kd-widget"><span>Approved</span><strong>{{ $stats['approved'] ?? 0 }}</strong><small>Live products</small></article>
    <article class="kd-widget"><span>Pending</span><strong>{{ $stats['pending'] ?? 0 }}</strong><small>Waiting approval</small></article>
    <article class="kd-widget"><span>Views</span><strong>{{ $stats['views'] ?? 0 }}</strong><small>Total visibility</small></article>
</div>

<div class="kd-grid-2">
    <section class="kd-card">
        <div class="kd-card-head"><h2>Store Products</h2><a href="/dashboard/stores/{{ $store->id }}/products">Manage</a></div>
        @forelse(($products ?? []) as $listing)
            <div class="kd-row"><div><strong>{{ $listing->title }}</strong><small>{{ ucfirst($listing->status ?? 'pending') }} · Rs. {{ number_format($listing->price ?? 0) }}</small></div><a href="/dashboard/stores/{{ $store->id }}/products/{{ $listing->id }}/edit" class="kd-mini-btn">Edit</a></div>
        @empty
            <div class="kd-empty"><strong>No products yet</strong><p>Add your first product to this store.</p><a href="/dashboard/stores/{{ $store->id }}/products/create" class="kd-btn kd-btn-primary">Add Product</a></div>
        @endforelse
    </section>

    <section class="kd-card">
        <div class="kd-card-head"><h2>Store Status</h2></div>
        <div class="kd-status-box"><strong>{{ ucfirst($store->status ?? 'pending') }}</strong><p>Verification and approval status for this store.</p></div>
        <div class="kd-action-grid">
            <a href="/dashboard/stores/{{ $store->id }}/edit">Edit Store Profile</a>
            <a href="/store/{{ $store->slug }}" target="_blank">View Public Store</a>
            <a href="/dashboard/stores/{{ $store->id }}/analytics">Analytics</a>
            <a href="/dashboard/stores/{{ $store->id }}/reviews">Reviews</a>
        </div>
    </section>
</div>
@endsection
