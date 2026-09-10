@extends('layouts.admin')
@section('title','Danger Zone')
@section('page','Danger Zone')
@section('eyebrow','Operations')
@section('page_heading','Danger Zone')
@section('subheading','Rejected, suspended and expired items — reactivate or delete permanently')

@section('content')

@if(session('success'))
<div class="alert-success">{{ session('success') }}</div>
@endif

{{-- Inactive Stores --}}
<section class="sa-card" class="mb-24">
    <div class="sa-card-head">
        <h2>Inactive Stores</h2>
        <span>{{ $inactiveStores->count() }} items</span>
    </div>
    @forelse($inactiveStores as $store)
        <div class="section-header-row">
            <div class="avatar-46-gray">
                @if($store->logo)
                    <img class="img-grayscale" src="{{ asset('storage/'.$store->logo) }}" alt="{{ $store->name }}">
                @else
                    {{ strtoupper(substr($store->name,0,1)) }}
                @endif
            </div>
            <div class="flex-grow-min">
                <b class="fs-14">{{ $store->name }}</b>
                <span class="badge-danger-tag">{{ strtoupper($store->status) }}</span>
                <div class="fs-12h text-gray">
                    Owner: {{ $store->user->name ?? '—' }} · {{ $store->city ?? 'Kegalle' }} · {{ $store->listings_count }} listing(s)
                </div>
            </div>
            <div class="flex-gap8-ns">
                <a href="/admin/stores/{{ $store->id }}/edit" class="ka-btn ka-btn-light" class="fs-12 btn-sm-pad">View</a>
                <form method="post" action="/admin/stores/{{ $store->id }}/approve">@csrf
                    <button class="ka-btn" class="btn-approve">↻ Reactivate</button>
                </form>
                <form method="post" action="/admin/stores/{{ $store->id }}" onsubmit="return confirm('Permanently DELETE store \'{{ addslashes($store->name) }}\'? This cannot be undone.')">@csrf @method('DELETE')
                    <button class="ka-btn btn-danger-sm">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div class="empty-state-sm">No inactive stores.</div>
    @endforelse
</section>

{{-- Inactive Listings --}}
<section class="sa-card">
    <div class="sa-card-head">
        <h2>Inactive Listings</h2>
        <span>{{ $inactiveListings->count() }} items</span>
    </div>
    @forelse($inactiveListings as $listing)
        <div class="section-header-row">
            <div class="icon-box-56b">
                @if($listing->images->first())
                    <img class="img-grayscale" src="{{ asset('storage/'.$listing->images->first()->path) }}" alt="{{ $listing->title }}">
                @else
                    <span class="fs-13 text-muted">IMG</span>
                @endif
            </div>
            <div class="flex-grow-min">
                <b class="fs-14">{{ $listing->title }}</b>
                <span class="badge-danger-tag">{{ strtoupper($listing->status) }}</span>
                <div class="fs-12h text-gray">
                    {{ $listing->store->name ?? $listing->user->name ?? '—' }} · {{ $listing->category->name ?? 'General' }} · LKR {{ number_format($listing->price ?? 0) }} · {{ $listing->updated_at?->diffForHumans() }}
                </div>
            </div>
            <div class="flex-gap8-ns">
                <a href="/admin/listings/{{ $listing->id }}/edit" class="ka-btn ka-btn-light" class="fs-12 btn-sm-pad">Edit</a>
                <form method="post" action="/admin/listings/{{ $listing->id }}/approve">@csrf
                    <button class="ka-btn" class="btn-approve">↻ Reactivate</button>
                </form>
                <form method="post" action="/admin/listings/{{ $listing->id }}" onsubmit="return confirm('Permanently DELETE listing \'{{ addslashes($listing->title) }}\' and its images? This cannot be undone.')">@csrf @method('DELETE')
                    <button class="ka-btn btn-danger-sm">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div class="empty-state-sm">No inactive listings.</div>
    @endforelse
</section>

@endsection
