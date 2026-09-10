@extends('layouts.dashboard')
@section('banner_sub', 'Post and manage your service offerings on Kegalle.')
@section('title','My Services')
@section('eyebrow','Services')
@section('heading','My Services')

@section('actions')
<a href="{{ route('dashboard.services.create') }}" class="kdl-tb-btn kdl-tb-btn-primary">
  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
  Post a Service
</a>
@endsection

@section('content')

<div class="di-info-banner">
  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
  <div>
    <strong>How services work</strong>
    Post your service offering — repair, consulting, brokering, transport, and more. Admin reviews and approves before it appears publicly.
  </div>
</div>

@if($services->isEmpty())
  <div class="di-empty-state">
    <div class="di-empty-icon">🛠️</div>
    <div class="di-empty-title">No services yet</div>
    <div class="di-empty-sub">Post your first service to reach customers across Kegalle.</div>
    <a href="{{ route('dashboard.services.create') }}" class="kdl-tb-btn kdl-tb-btn-primary mt-3">Post a Service</a>
  </div>
@else
  <div class="di-listings-grid">
    @foreach($services as $service)
    <div class="di-listing-card">
      <div class="di-listing-img-wrap">
        @if($service->image)
          <img src="{{ asset('storage/'.$service->image) }}" alt="{{ $service->title }}" class="di-listing-img">
        @else
          <div class="di-listing-img-ph">🛠️</div>
        @endif
        <span class="di-listing-status di-status-{{ $service->status }}">{{ ucfirst($service->status) }}</span>
      </div>
      <div class="di-listing-body">
        <div class="di-listing-title">{{ $service->title }}</div>
        <div class="di-listing-meta">
          {{ optional($service->category)->name ?? $service->service_type }}
          · {{ $service->location }}
        </div>
        <div class="di-listing-price">
          @if($service->price && $service->pricing_model !== 'free_quote')
            LKR {{ number_format($service->price) }}
            @if($service->pricing_model === 'hourly')<span class="di-price-unit">/ hr</span>@endif
          @else
            <span class="di-price-fq">Free Quote</span>
          @endif
        </div>
      </div>
      <div class="di-listing-actions">
        <a href="{{ route('dashboard.services.edit', $service) }}" class="di-act-btn" title="Edit">✏️</a>
        <form method="POST" action="{{ route('dashboard.services.destroy', $service) }}" data-confirm="Delete this service?">
          @csrf @method('DELETE')
          <button type="submit" class="di-act-btn di-act-del" title="Delete">🗑</button>
        </form>
        @if($service->status === 'approved')
          <a href="{{ route('services.show', $service->slug) }}" target="_blank" class="di-act-btn" title="View">👁</a>
        @endif
      </div>
    </div>
    @endforeach
  </div>
  <div class="di-pagination">{{ $services->links() }}</div>
@endif

@endsection
