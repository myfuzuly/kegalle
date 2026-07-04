@extends('layouts.dashboard')

@section('title','My Listings')
@section('heading','My Listings')
@section('subheading','Create, monitor and manage your products and classified ads.')

@section('actions')
<a href="/dashboard/listings/create" class="kd-btn kd-btn-primary">+ New Listing</a>
@endsection

@section('content')
@if(session('listing_submitted'))
<div style="background:linear-gradient(135deg,#E8F5E9,#F1F8E9);border:1.5px solid #A5D6A7;border-radius:16px;padding:24px;margin-bottom:22px;text-align:center">
    <div style="font-size:40px;margin-bottom:8px">🎉</div>
    <h2 style="font-size:19px;font-weight:800;color:#1B5E20;margin-bottom:6px">"{{ session('listing_submitted') }}" has been submitted!</h2>
    <p style="font-size:13.5px;color:#2E7D32;margin-bottom:14px">What happens next: our team reviews your ad (usually within a few hours) → once approved it goes live and buyers can contact you directly.</p>
    <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap">
        <a href="/dashboard/listings/create" class="kd-btn kd-btn-primary">+ Post Another Ad</a>
        <a href="/listings" class="kd-btn" style="background:#fff;border:1.5px solid #A5D6A7;color:#1B5E20">Browse Marketplace</a>
    </div>
</div>
@endif
@php
    $user = auth()->user();
    $items = isset($listings) ? $listings : \App\Models\Listing::with(['category','store'])->where('user_id',$user->id)->latest()->paginate(12);
@endphp

<section class="kd-card">
    <div class="kd-card-head"><h2>All Listings</h2><span>{{ $items->total() ?? $items->count() }} total</span></div>
    @forelse($items as $listing)
        <div class="kd-row">
            <div style="flex:1;min-width:0">
                <strong>{{ $listing->title }}</strong>
                <small>{{ $listing->category->name ?? 'General' }} · {{ ucfirst($listing->type ?? 'listing') }}</small>
            </div>
            <div style="text-align:right;white-space:nowrap">
                <strong style="color:var(--kd-brand)">LKR {{ number_format($listing->price ?? 0) }}</strong>
                <small><span class="kd-status-pill kd-status-{{ $listing->status ?? 'pending' }}">{{ ucfirst($listing->status ?? 'pending') }}</span></small>
            </div>
            <div style="display:flex;gap:6px;align-items:center">
                <a href="/listings/{{ $listing->slug }}" class="kd-mini-btn" target="_blank">View</a>
                <a href="/dashboard/listings/{{ $listing->id }}/edit" class="kd-mini-btn">Edit</a>
                <form method="post" action="/dashboard/listings/{{ $listing->id }}" onsubmit="return confirm('Delete this listing permanently?')" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="kd-mini-btn" style="color:#d32f2f;border-color:#ffcdd2;cursor:pointer">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div class="kd-empty">
            <strong>No listings found</strong>
            <p>Start by posting your first listing.</p>
            <a href="/dashboard/listings/create" class="kd-btn kd-btn-primary">Post Listing</a>
        </div>
    @endforelse
</section>
@if(method_exists($items,'links'))<div style="margin-top:20px">{{ $items->links() }}</div>@endif
@endsection
