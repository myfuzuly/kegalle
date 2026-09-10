@extends('layouts.admin')
@section('title','Pending Approvals')
@section('page','Approvals')
@section('eyebrow','Overview')
@section('page_heading','Approval Center')
@section('subheading','Review and approve new listings and store registrations in one place')

@section('content')

@if(session('success'))
<div class="alert-success">{{ session('success') }}</div>
@endif

{{-- Summary count bar --}}
@php $totalPending = $pendingStores->count() + $pendingListings->count() + $pendingServices->count() + (isset($pendingDeals) ? $pendingDeals->count() : 0); @endphp
<div class="approval-summary-bar">
    <div class="approval-summary-item">
        <div class="approval-summary-icon" style="background:#dbeafe;">🏪</div>
        <div>
            <div class="approval-summary-count">{{ $pendingStores->count() }}</div>
            <div class="approval-summary-label">Stores</div>
        </div>
    </div>
    <div class="approval-summary-item">
        <div class="approval-summary-icon" style="background:#dcfce7;">📦</div>
        <div>
            <div class="approval-summary-count">{{ $pendingListings->count() }}</div>
            <div class="approval-summary-label">Listings</div>
        </div>
    </div>
    <div class="approval-summary-item">
        <div class="approval-summary-icon" style="background:#ede9fe;">🛠️</div>
        <div>
            <div class="approval-summary-count">{{ $pendingServices->count() }}</div>
            <div class="approval-summary-label">Services</div>
        </div>
    </div>
    @isset($pendingDeals)
    <div class="approval-summary-item">
        <div class="approval-summary-icon" style="background:#fef3c7;">⚡</div>
        <div>
            <div class="approval-summary-count">{{ $pendingDeals->count() }}</div>
            <div class="approval-summary-label">Deals</div>
        </div>
    </div>
    @endisset
    <div class="approval-summary-item approval-summary-item--total">
        <div class="approval-summary-icon" style="background:#f0fdf4;">✅</div>
        <div>
            <div class="approval-summary-count" style="color:{{ $totalPending > 0 ? '#b45309' : '#15803d' }};">{{ $totalPending }}</div>
            <div class="approval-summary-label">Total Pending</div>
        </div>
    </div>
</div>

{{-- Pending Stores --}}
<section class="sa-card" class="mb-24">
    <div class="sa-card-head">
        <h2>Stores Waiting for Approval</h2>
        <span>{{ $pendingStores->count() }} pending</span>
    </div>
    @forelse($pendingStores as $store)
        <div class="section-header-row">
            <div class="avatar-46-green">
                @if($store->logo)
                    <img src="{{ asset('storage/'.$store->logo) }}" alt="{{ $store->name }}" class="img-cover">
                @else
                    {{ strtoupper(substr($store->name,0,1)) }}
                @endif
            </div>
            <div class="flex-grow-min">
                <b class="fs-14">{{ $store->name }}</b>
                <div class="fs-12h text-gray">
                    Owner: {{ $store->user->name ?? '—' }} · {{ $store->city ?? 'Kegalle' }} · {{ $store->listings_count }} listings · Applied {{ $store->created_at?->diffForHumans() }}
                </div>
            </div>
            <div class="flex-gap8-ns">
                <a href="/admin/stores/{{ $store->id }}/edit" class="ka-btn ka-btn-light" class="fs-12 btn-sm-pad">View</a>
                <form method="post" action="/admin/stores/{{ $store->id }}/approve">@csrf
                    <button class="ka-btn btn-approve-green">✓ Approve</button>
                </form>
                <form method="post" action="/admin/stores/{{ $store->id }}/suspend" onsubmit="return confirm('Reject / suspend this store?')">@csrf
                    <button class="ka-btn btn-reject-red">✕ Reject</button>
                </form>
            </div>
        </div>
    @empty
        <div class="approval-empty-state"><span class="approval-empty-icon">🏪</span><span>No stores waiting for approval</span></div>
    @endforelse
</section>

{{-- Pending Listings --}}
<section class="sa-card" class="mb-24">
    <div class="sa-card-head">
        <h2>Listings Waiting for Approval</h2>
        <span>{{ $pendingListings->count() }} pending</span>
    </div>
    @forelse($pendingListings as $listing)
        <div class="section-header-row">
            <div class="icon-box-56">
                @if($listing->images->first())
                    <img src="{{ asset('storage/'.$listing->images->first()->path) }}" alt="{{ $listing->title }}" class="img-cover">
                @else
                    <span class="text-muted">IMG</span>
                @endif
            </div>
            <div class="flex-grow-min">
                <b class="fs-14">{{ $listing->title }}</b>
                <span class="listing-type-badge {{ $listing->type === 'classified' ? 'badge-classified' : 'badge-product' }}">{{ strtoupper($listing->type ?? 'PRODUCT') }}</span>
                <div class="fs-12h text-gray">
                    {{ $listing->store->name ?? $listing->user->name ?? '—' }} · {{ $listing->category->name ?? 'General' }} · LKR {{ number_format($listing->price ?? 0) }} · {{ $listing->created_at?->diffForHumans() }}
                </div>
            </div>
            <div class="flex-gap8-ns">
                <a href="/listings/{{ $listing->slug }}" target="_blank" class="ka-btn ka-btn-light" class="fs-12 btn-sm-pad">Preview</a>
                <a href="/admin/listings/{{ $listing->id }}/edit" class="ka-btn ka-btn-light" class="fs-12 btn-sm-pad">Edit</a>
                <form method="post" action="/admin/listings/{{ $listing->id }}/approve">@csrf
                    <button class="ka-btn btn-approve-green">✓ Approve</button>
                </form>
                <form method="post" action="/admin/listings/{{ $listing->id }}/reject" onsubmit="return confirm('Reject this listing?')">@csrf
                    <button class="ka-btn btn-reject-red">✕ Reject</button>
                </form>
            </div>
        </div>
    @empty
        <div class="approval-empty-state"><span class="approval-empty-icon">📦</span><span>No listings waiting for approval</span></div>
    @endforelse
</section>

{{-- Pending Services --}}
<section class="sa-card" class="mb-24">
    <div class="sa-card-head">
        <h2>Services Waiting for Approval</h2>
        <span>{{ $pendingServices->count() }} pending</span>
    </div>
    @forelse($pendingServices as $service)
        <div class="section-header-row">
            <div class="icon-box-56">
                @if($service->image)
                    <img src="{{ asset('storage/'.$service->image) }}" alt="{{ $service->title }}" class="img-cover">
                @else
                    <span class="emoji-icon-lg">🛠️</span>
                @endif
            </div>
            <div class="flex-grow-min">
                <b class="fs-14">{{ $service->title }}</b>
                <span class="badge-service-type">{{ ucfirst($service->service_type) }}</span>
                <div class="fs-12h text-gray">
                    By {{ $service->user->name ?? '—' }}
                    @if($service->category) · {{ $service->category->name }}@endif
                    · {{ $service->location }}
                    · {{ $service->price_label }}
                    · Submitted {{ $service->created_at?->diffForHumans() }}
                </div>
            </div>
            <div class="flex-gap8-ns">
                <a href="/services/{{ $service->slug }}" target="_blank" class="ka-btn ka-btn-light">Preview</a>
                <form method="post" action="/admin/services/{{ $service->id }}/approve">@csrf
                    <button class="ka-btn btn-approve-green">✓ Approve</button>
                </form>
                <form method="post" action="/admin/services/{{ $service->id }}/reject" onsubmit="return confirm('Reject this service?')">@csrf
                    <button class="ka-btn btn-reject-red">✕ Reject</button>
                </form>
            </div>
        </div>
    @empty
        <div class="approval-empty-state"><span class="approval-empty-icon">🛠️</span><span>No services waiting for approval</span></div>
    @endforelse
</section>

{{-- Pending Deals --}}
<section class="sa-card">
    <div class="sa-card-head">
        <h2>Deals Waiting for Approval</h2>
        <span>{{ $pendingDeals->count() }} pending</span>
    </div>
    @forelse($pendingDeals as $deal)
        @php $thumb = $deal->listing?->images?->first()?->path ?? null; @endphp
        <div class="section-header-row">
            <div class="icon-box-56">
                @if($thumb)
                    <img src="{{ asset('storage/'.ltrim($thumb,'/')) }}" alt="" class="img-cover">
                @else
                    <span class="text-muted">IMG</span>
                @endif
            </div>
            <div class="flex-grow-min">
                <b class="fs-14">{{ optional($deal->listing)->title ?? 'Deleted listing' }}</b>
                <div class="fs-12h text-gray">
                    {{ $deal->store->name ?? $deal->user->name ?? '—' }}
                    &nbsp;·&nbsp;
                    <b class="text-green">LKR {{ number_format($deal->deal_price) }}</b>
                    <s class="muted2-ml4">{{ number_format($deal->original_price) }}</s>
                    &nbsp;·&nbsp; -{{ number_format($deal->discount_percent, 0) }}% off
                    &nbsp;·&nbsp; {{ optional($deal->starts_at)->format('M d') ?? '—' }} – {{ optional($deal->ends_at)->format('M d') ?? '—' }}
                    &nbsp;·&nbsp; Submitted {{ $deal->created_at?->diffForHumans() }}
                </div>
                @if($deal->admin_note)
                <div class="fs12-orange-mt3">Note: {{ $deal->admin_note }}</div>
                @endif
            </div>
            <div class="flex-gap8-ns">
                <a href="/admin/deals/{{ $deal->id }}/edit" class="ka-btn ka-btn-light" class="fs-12 btn-sm-pad">Edit</a>
                <form method="post" action="/admin/deals/{{ $deal->id }}/approve">@csrf
                    <button class="ka-btn btn-approve-green">✓ Approve</button>
                </form>
                <form method="post" action="/admin/deals/{{ $deal->id }}/reject" onsubmit="return confirm('Reject this deal?')">@csrf
                    <button class="ka-btn btn-reject-red">✕ Reject</button>
                </form>
            </div>
        </div>
    @empty
        <div class="empty-state-sm">No deals waiting for approval.</div>
    @endforelse
</section>

@endsection
