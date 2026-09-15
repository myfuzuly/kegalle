@extends('layouts.dashboard')
@section('banner_sub', 'Manage your business stores on Kegalle Marketplace.')
@section('title','My Stores')
@section('heading','My Stores')
@section('subheading','Manage your business profiles and store dashboards.')

@section('actions')
@if(($stores ?? collect())->count() < (int)(auth()->user()->store_limit ?? 1) || in_array(auth()->user()->role ?? '', ['admin','super_admin']))
<a href="/dashboard/stores/create" class="kdl-tb-btn kdl-tb-btn-primary">
  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
  Create Store
</a>
@endif
@endsection

@section('content')

@if(session('error'))
<div class="si-banner-alert">
  <div class="fs34-lh1-ns">🔒</div>
  <div class="flex-1-min180">
    <div class="fw8-fs145-rose">Store Limit Reached</div>
    <div class="fs13-rose-lh">{{ session('error') }}</div>
  </div>
  <div class="si-banner-alert-btns">
    <a href="https://wa.me/94712930930?text=Hi%2C%20I%20need%20help%20with%20my%20store%20limit%20on%20kegalle.com" target="_blank" class="si-banner-alert-btn si-ban-wa">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.126.556 4.123 1.528 5.855L.057 23.882l6.233-1.638A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.894a9.885 9.885 0 01-5.032-1.371l-.36-.214-3.742.982 1-3.645-.235-.374A9.861 9.861 0 012.106 12C2.106 6.579 6.579 2.106 12 2.106S21.894 6.579 21.894 12 17.421 21.894 12 21.894z"/></svg>
      WhatsApp
    </a>
    <a href="mailto:support@kegalle.com?subject=Store%20Limit%20Upgrade%20Request" class="si-banner-alert-btn si-ban-email">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
      Email
    </a>
  </div>
</div>
@endif

@if(session('success'))
<div class="si-success">✓ {{ session('success') }}</div>
@endif

@if(!auth()->user()->phone_verified_at)
<div class="si-verify-banner">
  <div class="fs32-ns">📱</div>
  <div class="flex-1-min180">
    <div class="fw7-fs14-amber-mb2">Verify your phone number</div>
    <div class="fs125-amber">Phone verification builds buyer trust and is required to unlock all store features.</div>
  </div>
  <a href="/phone/verify" class="si-verify-btn">Verify via SMS →</a>
</div>
@endif

<div class="si-grid">
@forelse(($stores ?? []) as $store)
@php $status = $store->status ?? 'pending'; @endphp
<div class="si-card">
  <div class="si-card-banner">
    @if($store->banner)
      <img src="{{ asset('storage/'.$store->banner) }}" alt="{{ $store->name }} banner">
    @endif
    <div class="si-logo-ring">
      @if($store->logo)
        <img src="{{ asset('storage/'.$store->logo) }}" alt="{{ $store->name }} logo">
      @else
        <div class="si-logo-init">{{ strtoupper(substr($store->name,0,1)) }}</div>
      @endif
    </div>
  </div>
  <div class="si-card-body">
    <div class="si-card-name">{{ $store->name }}</div>
    <div class="si-card-row">
      <span class="si-pill si-pill-{{ $status }}">{{ ucfirst($status) }}</span>
      <span class="si-count">{{ $store->listings_count ?? 0 }} product{{ ($store->listings_count ?? 0) === 1 ? '' : 's' }}</span>
    </div>
    <div class="si-card-actions">
      <a href="/dashboard/stores/{{ $store->id }}" class="si-btn-open">
        Open Store Panel
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
      </a>
      <a href="/dashboard/stores/{{ $store->id }}/edit" class="si-btn-edit">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Edit
      </a>
    </div>
  </div>
</div>
@empty
<div class="si-empty">
  <div class="si-empty-icon">🏪</div>
  <strong>No store yet</strong>
  <p>Create a store to list products under your business name and build buyer trust.</p>
  <a href="/dashboard/stores/create" class="si-empty-btn">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    Create Your First Store
  </a>
</div>
@endforelse
</div>

@endsection
