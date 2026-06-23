@extends('layouts.app')

@section('title','My Listings - Kegalle')

@section('content')
@php
    $user = auth()->user();
    $items = isset($listings) ? $listings : \App\Models\Listing::with(['category','store'])->where('user_id',$user->id)->latest()->paginate(12);
@endphp
<section class="kg-dash-shell">
    <aside class="kg-dash-sidebar">
        <div class="kg-dash-user"><div>{{ strtoupper(substr($user->name,0,1)) }}</div><b>{{ $user->name }}</b><small>Seller Dashboard</small></div>
        <a href="/dashboard">Overview</a>
        <a class="active" href="/dashboard/listings">My Listings</a>
        <a href="/dashboard/listings/create">Post New Ad</a>
        <a href="/dashboard/stores">My Stores</a>
    </aside>
    <main class="kg-dash-main">
        <div class="kg-dash-hero compact"><div><span>Listing Manager</span><h1>My Listings</h1><p>Create, monitor and manage your products and classified ads.</p></div><a href="/dashboard/listings/create">+ New Listing</a></div>
        <div class="kg-dash-table-card">
            <div class="kg-dash-table-head"><span>Listing</span><span>Type</span><span>Status</span><span>Price</span><span>Action</span></div>
            @forelse($items as $listing)
                <div class="kg-dash-table-row">
                    <span><b>{{ $listing->title }}</b><small>{{ $listing->category->name ?? 'General' }}</small></span>
                    <span>{{ ucfirst($listing->type ?? 'listing') }}</span>
                    <span><em class="status {{ $listing->status ?? 'pending' }}">{{ ucfirst($listing->status ?? 'pending') }}</em></span>
                    <span>LKR {{ number_format($listing->price ?? 0) }}</span>
                    <span><a href="/listings/{{ $listing->slug }}">View</a></span>
                </div>
            @empty
                <div class="kg-dash-empty"><h3>No listings found</h3><p>Start by posting your first listing.</p><a href="/dashboard/listings/create">Post Listing</a></div>
            @endforelse
        </div>
        @if(method_exists($items,'links')) <div class="kg-pagination">{{ $items->links() }}</div> @endif
    </main>
</section>
@endsection
