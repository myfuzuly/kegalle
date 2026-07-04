@extends('layouts.admin')
@section('title','Danger Zone')
@section('page','Danger Zone')
@section('heading','⚠️ Danger Zone')
@section('subheading','Rejected, suspended and expired items — reactivate them or delete permanently')
@section('content')

@if(session('success'))
<div style="background:#E8F5E9;color:#2E7D32;padding:12px 18px;border-radius:12px;margin-bottom:18px;font-weight:600;font-size:13px">{{ session('success') }}</div>
@endif

{{-- Inactive Stores --}}
<section class="sa-card" style="margin-bottom:24px">
    <div class="sa-card-head">
        <h2>🏬 Inactive Stores</h2>
        <span>{{ $inactiveStores->count() }} items</span>
    </div>
    @forelse($inactiveStores as $store)
        <div style="display:flex;align-items:center;gap:16px;padding:14px 20px;border-bottom:1px solid #f0f2f7">
            <div style="width:46px;height:46px;border-radius:12px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;font-weight:700;color:#9e9e9e;font-size:16px;flex-shrink:0;overflow:hidden">
                @if($store->logo)
                    <img src="{{ asset('storage/'.$store->logo) }}" alt="{{ $store->name }}" style="width:100%;height:100%;object-fit:cover;filter:grayscale(60%)">
                @else
                    {{ strtoupper(substr($store->name,0,1)) }}
                @endif
            </div>
            <div style="flex:1;min-width:0">
                <b style="font-size:14px">{{ $store->name }}</b>
                <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:12px;background:#ffebee;color:#c62828;margin-left:6px">{{ strtoupper($store->status) }}</span>
                <div style="font-size:12.5px;color:#667085">
                    Owner: {{ $store->user->name ?? '—' }} · {{ $store->city ?? 'Kegalle' }} · {{ $store->listings_count }} listing(s)
                </div>
            </div>
            <div style="display:flex;gap:8px;flex-shrink:0">
                <a href="/admin/stores/{{ $store->id }}/edit" class="ka-btn ka-btn-light" style="font-size:12px;padding:8px 14px">View</a>
                <form method="post" action="/admin/stores/{{ $store->id }}/approve">@csrf
                    <button class="ka-btn" style="background:#2e7d32;color:#fff;border:none;font-size:12px;padding:8px 14px;cursor:pointer">↻ Reactivate</button>
                </form>
                <form method="post" action="/admin/stores/{{ $store->id }}" onsubmit="return confirm('Permanently DELETE store \'{{ addslashes($store->name) }}\'? This cannot be undone.')">@csrf @method('DELETE')
                    <button class="ka-btn" style="background:#c62828;color:#fff;border:none;font-size:12px;padding:8px 14px;cursor:pointer">🗑 Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div style="padding:28px 20px;text-align:center;color:#98a2b3;font-size:13.5px">No inactive stores.</div>
    @endforelse
</section>

{{-- Inactive Listings --}}
<section class="sa-card">
    <div class="sa-card-head">
        <h2>📦 Inactive Listings</h2>
        <span>{{ $inactiveListings->count() }} items</span>
    </div>
    @forelse($inactiveListings as $listing)
        <div style="display:flex;align-items:center;gap:16px;padding:14px 20px;border-bottom:1px solid #f0f2f7">
            <div style="width:56px;height:56px;border-radius:10px;background:#f5f7fa;flex-shrink:0;overflow:hidden;display:flex;align-items:center;justify-content:center;font-size:20px">
                @if($listing->images->first())
                    <img src="{{ asset('storage/'.$listing->images->first()->path) }}" alt="{{ $listing->title }}" style="width:100%;height:100%;object-fit:cover;filter:grayscale(60%)">
                @else
                    🛒
                @endif
            </div>
            <div style="flex:1;min-width:0">
                <b style="font-size:14px">{{ $listing->title }}</b>
                <span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:12px;background:#ffebee;color:#c62828;margin-left:6px">{{ strtoupper($listing->status) }}</span>
                <div style="font-size:12.5px;color:#667085">
                    {{ $listing->store->name ?? $listing->user->name ?? '—' }} · {{ $listing->category->name ?? 'General' }} · LKR {{ number_format($listing->price ?? 0) }} · {{ $listing->updated_at?->diffForHumans() }}
                </div>
            </div>
            <div style="display:flex;gap:8px;flex-shrink:0">
                <a href="/admin/listings/{{ $listing->id }}/edit" class="ka-btn ka-btn-light" style="font-size:12px;padding:8px 14px">Edit</a>
                <form method="post" action="/admin/listings/{{ $listing->id }}/approve">@csrf
                    <button class="ka-btn" style="background:#2e7d32;color:#fff;border:none;font-size:12px;padding:8px 14px;cursor:pointer">↻ Reactivate</button>
                </form>
                <form method="post" action="/admin/listings/{{ $listing->id }}" onsubmit="return confirm('Permanently DELETE listing \'{{ addslashes($listing->title) }}\' and its images? This cannot be undone.')">@csrf @method('DELETE')
                    <button class="ka-btn" style="background:#c62828;color:#fff;border:none;font-size:12px;padding:8px 14px;cursor:pointer">🗑 Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div style="padding:28px 20px;text-align:center;color:#98a2b3;font-size:13.5px">No inactive listings.</div>
    @endforelse
</section>

@endsection
