@extends('layouts.dashboard')

@section('title','User Dashboard')
@section('heading','Welcome back, ' . (auth()->user()->name ?? 'User'))
@section('subheading','Manage your personal classified ads, enquiries and marketplace activity.')

@section('actions')
<a href="/dashboard/listings/create" class="kd-btn kd-btn-primary">+ Post Ad</a>
@endsection

@section('content')

@if(($stats['listings'] ?? 0) === 0 && ($stats['stores'] ?? 0) === 0)
<div class="kd-onboarding">
    <h2>Welcome to Kegalle Marketplace!</h2>
    <p>Get started in 3 simple steps to reach thousands of buyers in the Kegalle district.</p>
    <div class="kd-onboarding-steps">
        <div class="kd-onboarding-step">
            <div class="kd-step-num">1</div>
            <strong>Create Your Store</strong>
            <small>Set up your business profile with logo and contact details</small>
            <a href="/dashboard/stores/create" class="kd-btn kd-btn-primary" style="margin-top:10px;display:inline-block;font-size:12px;padding:6px 14px">Create Store</a>
        </div>
        <div class="kd-onboarding-step">
            <div class="kd-step-num">2</div>
            <strong>Post Your First Ad</strong>
            <small>Add products or services with photos and pricing</small>
            <a href="/dashboard/listings/create" class="kd-btn kd-btn-primary" style="margin-top:10px;display:inline-block;font-size:12px;padding:6px 14px">Post Ad</a>
        </div>
        <div class="kd-onboarding-step">
            <div class="kd-step-num">3</div>
            <strong>Start Selling</strong>
            <small>Buyers will contact you via WhatsApp or chat</small>
        </div>
    </div>
</div>
@endif

<div class="kd-widget-grid">
    <article class="kd-widget"><span>Total Ads</span><strong>{{ $stats['listings'] ?? 0 }}</strong><small>Your classified listings</small></article>
    <article class="kd-widget"><span>Approved</span><strong>{{ $stats['approved'] ?? 0 }}</strong><small>Live on marketplace</small></article>
    <article class="kd-widget"><span>Pending</span><strong>{{ $stats['pending'] ?? 0 }}</strong><small>Waiting approval</small></article>
    <article class="kd-widget"><span>Stores</span><strong>{{ $stats['stores'] ?? 0 }}</strong><small>Your business profiles</small></article>
</div>

<div class="kd-grid-2">
    <section class="kd-card">
        <div class="kd-card-head"><h2>Recent Ads</h2><a href="/dashboard/listings">View all</a></div>
        @forelse(($latestListings ?? []) as $listing)
            <div class="kd-row"><div><strong>{{ $listing->title }}</strong><small>{{ ucfirst($listing->status ?? 'pending') }} · {{ $listing->created_at?->diffForHumans() }}</small></div><a href="/dashboard/listings/{{ $listing->id }}/edit" class="kd-mini-btn">Edit</a></div>
        @empty
            <div class="kd-empty"><strong>No ads yet</strong><p>Create your first classified ad.</p><a href="/dashboard/listings/create" class="kd-btn kd-btn-primary">Post Ad</a></div>
        @endforelse
    </section>

    <section class="kd-card">
        <div class="kd-card-head"><h2>Quick Actions</h2></div>
        <div class="kd-action-grid">
            <a href="/dashboard/listings/create">Post classified ad</a>
            <a href="/dashboard/stores/create">Create store</a>
            <a href="/dashboard/chat">View messages</a>
            <a href="/dashboard/membership">Upgrade membership</a>
        </div>
    </section>
</div>
@endsection
