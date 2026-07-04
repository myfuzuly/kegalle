@extends('layouts.admin')
@section('title','Pending Approvals')
@section('page','Approvals')
@section('heading','Pending Approvals')
@section('subheading','Review and approve new listings and store registrations in one place')
@section('content')

@if(session('success'))
<div style="background:#E8F5E9;color:#2E7D32;padding:12px 18px;border-radius:12px;margin-bottom:18px;font-weight:600;font-size:13px">{{ session('success') }}</div>
@endif

{{-- Pending Stores --}}
<section class="sa-card" style="margin-bottom:24px">
    <div class="sa-card-head">
        <h2>🏬 Stores Waiting for Approval</h2>
        <span>{{ $pendingStores->count() }} pending</span>
    </div>
    @forelse($pendingStores as $store)
        <div style="display:flex;align-items:center;gap:16px;padding:14px 20px;border-bottom:1px solid #f0f2f7">
            <div style="width:46px;height:46px;border-radius:12px;background:#e8f5e9;display:flex;align-items:center;justify-content:center;font-weight:700;color:#2e7d32;font-size:16px;flex-shrink:0;overflow:hidden">
                @if($store->logo)
                    <img src="{{ asset('storage/'.$store->logo) }}" alt="{{ $store->name }}" style="width:100%;height:100%;object-fit:cover">
                @else
                    {{ strtoupper(substr($store->name,0,1)) }}
                @endif
            </div>
            <div style="flex:1;min-width:0">
                <b style="font-size:14px">{{ $store->name }}</b>
                <div style="font-size:12.5px;color:#667085">
                    Owner: {{ $store->user->name ?? '—' }} · {{ $store->city ?? 'Kegalle' }} · {{ $store->listings_count }} listings · Applied {{ $store->created_at?->diffForHumans() }}
                </div>
            </div>
            <div style="display:flex;gap:8px;flex-shrink:0">
                <a href="/admin/stores/{{ $store->id }}/edit" class="ka-btn ka-btn-light" style="font-size:12px;padding:8px 14px">View</a>
                <form method="post" action="/admin/stores/{{ $store->id }}/approve">@csrf
                    <button class="ka-btn" style="background:#2e7d32;color:#fff;border:none;font-size:12px;padding:8px 14px;cursor:pointer">✓ Approve</button>
                </form>
                <form method="post" action="/admin/stores/{{ $store->id }}/suspend" onsubmit="return confirm('Reject / suspend this store?')">@csrf
                    <button class="ka-btn" style="background:#ffebee;color:#c62828;border:none;font-size:12px;padding:8px 14px;cursor:pointer">✕ Reject</button>
                </form>
            </div>
        </div>
    @empty
        <div style="padding:28px 20px;text-align:center;color:#98a2b3;font-size:13.5px">No stores waiting for approval. 🎉</div>
    @endforelse
</section>

{{-- Pending Listings --}}
<section class="sa-card">
    <div class="sa-card-head">
        <h2>📦 Listings Waiting for Approval</h2>
        <span>{{ $pendingListings->count() }} pending</span>
    </div>
    @forelse($pendingListings as $listing)
        <div style="display:flex;align-items:center;gap:16px;padding:14px 20px;border-bottom:1px solid #f0f2f7">
            <div style="width:56px;height:56px;border-radius:10px;background:#f5f7fa;flex-shrink:0;overflow:hidden;display:flex;align-items:center;justify-content:center;font-size:20px">
                @if($listing->images->first())
                    <img src="{{ asset('storage/'.$listing->images->first()->path) }}" alt="{{ $listing->title }}" style="width:100%;height:100%;object-fit:cover">
                @else
                    🛒
                @endif
            </div>
            <div style="flex:1;min-width:0">
                <b style="font-size:14px">{{ $listing->title }}</b>
                <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:12px;background:{{ $listing->type === 'classified' ? '#fff3e0' : '#e3f2fd' }};color:{{ $listing->type === 'classified' ? '#e65100' : '#1565c0' }};margin-left:6px">{{ strtoupper($listing->type ?? 'PRODUCT') }}</span>
                <div style="font-size:12.5px;color:#667085">
                    {{ $listing->store->name ?? $listing->user->name ?? '—' }} · {{ $listing->category->name ?? 'General' }} · LKR {{ number_format($listing->price ?? 0) }} · {{ $listing->created_at?->diffForHumans() }}
                </div>
            </div>
            <div style="display:flex;gap:8px;flex-shrink:0">
                <a href="/listings/{{ $listing->slug }}" target="_blank" class="ka-btn ka-btn-light" style="font-size:12px;padding:8px 14px">Preview</a>
                <a href="/admin/listings/{{ $listing->id }}/edit" class="ka-btn ka-btn-light" style="font-size:12px;padding:8px 14px">Edit</a>
                <form method="post" action="/admin/listings/{{ $listing->id }}/approve">@csrf
                    <button class="ka-btn" style="background:#2e7d32;color:#fff;border:none;font-size:12px;padding:8px 14px;cursor:pointer">✓ Approve</button>
                </form>
                <form method="post" action="/admin/listings/{{ $listing->id }}/reject" onsubmit="return confirm('Reject this listing?')">@csrf
                    <button class="ka-btn" style="background:#ffebee;color:#c62828;border:none;font-size:12px;padding:8px 14px;cursor:pointer">✕ Reject</button>
                </form>
            </div>
        </div>
    @empty
        <div style="padding:28px 20px;text-align:center;color:#98a2b3;font-size:13.5px">No listings waiting for approval. 🎉</div>
    @endforelse
</section>

@endsection
