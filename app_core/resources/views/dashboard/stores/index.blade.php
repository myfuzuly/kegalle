@extends('layouts.dashboard')

@section('title','My Stores')
@section('heading','My Stores')
@section('subheading','Manage your business profiles and store dashboards.')

@section('actions')
<a href="/dashboard/stores/create" class="kd-btn kd-btn-primary">+ Create Store</a>
@endsection

@section('content')
<div class="kd-card">
    <div class="kd-card-head"><h2>Your Stores</h2><span>{{ ($stores ?? collect())->count() }} stores</span></div>
    <div class="kd-store-grid">
        @forelse(($stores ?? []) as $store)
            <a href="/dashboard/stores/{{ $store->id }}" class="kd-store-tile">
                <div class="kd-store-avatar">{{ strtoupper(substr($store->name,0,1)) }}</div>
                <strong>{{ $store->name }}</strong>
                <small>{{ ucfirst($store->status ?? 'pending') }} · {{ $store->listings_count ?? 0 }} products</small>
                <span>Open Store Panel →</span>
            </a>
        @empty
            <div class="kd-empty"><strong>No store profile yet</strong><p>Create a store to list products under your business.</p><a href="/dashboard/stores/create" class="kd-btn kd-btn-primary">Create Store</a></div>
        @endforelse
    </div>
</div>
@endsection
