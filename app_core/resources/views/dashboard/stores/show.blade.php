@extends('layouts.store-dashboard')

@section('title', ($store->name ?? 'Store') . ' Dashboard')
@section('eyebrow', 'Store panel')
@section('heading', $store->name ?? 'Store Dashboard')
@section('subheading', 'How your store is doing, and what to do next.')

@section('actions')
<a href="/store/{{ $store->slug }}" target="_blank" rel="noopener" class="ka-btn ka-btn-light">View public store</a>
<a href="/dashboard/stores/{{ $store->id }}/products/create" class="ka-btn ka-btn-primary">Add product</a>
@endsection

@section('content')
@php
    $sid = $store->id;
    $pill = fn (?string $s) => match ($s) { 'approved', 'active' => 'ok', 'rejected' => 'bad', 'sold' => '', default => 'warn' };
    $storeStatus = $store->status ?? 'pending';
    $checksDone = collect($profileChecks ?? [])->where(1, true)->count();
    $checksTotal = count($profileChecks ?? []);
@endphp

@if($storeStatus === 'pending')
<div class="kx-sysbar is-on">
    <span><span class="kx-status-dot" aria-hidden="true"></span><strong>Your store is waiting for approval.</strong> You can add products now; they go live once the store is approved.</span>
</div>
@elseif($storeStatus === 'rejected' || $storeStatus === 'suspended')
<div class="kx-sysbar is-on">
    <span><span class="kx-status-dot" aria-hidden="true"></span><strong>Your store is {{ $storeStatus }}.</strong> It isn't visible to buyers. Update your store profile or contact support.</span>
    <a href="/dashboard/contact-admin" class="ka-btn ka-btn-light kx-btn-sm">Contact support</a>
</div>
@endif

<div class="kx-kpis">
    <a class="kx-kpi" href="/dashboard/stores/{{ $sid }}/products?status=approved">
        <span class="kx-kpi-label">Live products</span>
        <span class="kx-kpi-value">{{ number_format($stats['approved'] ?? 0) }}</span>
        <span class="kx-kpi-meta">of {{ number_format($stats['products'] ?? 0) }} total{{ ($stats['pending'] ?? 0) ? ' · '.$stats['pending'].' awaiting review' : '' }}</span>
    </a>
    <a class="kx-kpi" href="/dashboard/stores/{{ $sid }}/analytics">
        <span class="kx-kpi-label">Product views</span>
        <span class="kx-kpi-value">{{ number_format($stats['views'] ?? 0) }}</span>
        <span class="kx-kpi-meta">All-time, across your products</span>
    </a>
    <a class="kx-kpi" href="/dashboard/offers/received">
        <span class="kx-kpi-label">Offers to answer</span>
        <span class="kx-kpi-value">{{ number_format($stats['offers_pending'] ?? 0) }}</span>
        <span class="kx-kpi-meta">{{ ($stats['offers_pending'] ?? 0) ? 'Buyers are waiting for a reply' : 'No pending price offers' }}</span>
    </a>
    <a class="kx-kpi" href="/dashboard/stores/{{ $sid }}/reviews">
        <span class="kx-kpi-label">Rating</span>
        <span class="kx-kpi-value">{{ ($stats['reviews'] ?? 0) ? number_format($stats['rating'], 1).' / 5' : '—' }}</span>
        <span class="kx-kpi-meta">{{ ($stats['reviews'] ?? 0) ? number_format($stats['reviews']).' '.\Illuminate\Support\Str::plural('review', $stats['reviews']) : 'No reviews yet' }}</span>
    </a>
</div>

<div class="kx-grid">
    <section class="kx-card kx-col-8" aria-labelledby="spH">
        <div class="kx-card-head">
            <div><h2 id="spH">Recent products</h2><p>Your newest products and their review status.</p></div>
            <a href="/dashboard/stores/{{ $sid }}/products">Manage all</a>
        </div>
        @if(($products ?? collect())->isEmpty())
            <div class="kx-empty">
                <p><strong>No products yet.</strong> Add your first product so buyers can find your store.</p>
                <a href="/dashboard/stores/{{ $sid }}/products/create" class="ka-btn ka-btn-primary">Add your first product</a>
            </div>
        @else
            <ul class="kx-rows" role="list">
                @foreach($products as $listing)
                    <li class="kx-row">
                        <div class="kx-row-main">
                            <a class="kx-row-title" href="/dashboard/stores/{{ $sid }}/products/{{ $listing->id }}/edit">{{ $listing->title }}</a>
                            <span class="kx-row-meta">{{ $listing->category->name ?? 'Uncategorised' }} · LKR {{ number_format((float) ($listing->price ?? 0)) }} · {{ $listing->created_at?->diffForHumans() }}</span>
                        </div>
                        <div class="kx-row-side">
                            <span class="kx-pill {{ $pill($listing->status) }}">{{ ucfirst($listing->status ?? 'pending') }}</span>
                            <a href="/dashboard/stores/{{ $sid }}/products/{{ $listing->id }}/edit" class="ka-btn ka-btn-light kx-btn-sm">Edit</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <div class="kx-col-4 kx-stack-col">
        <section class="kx-card" aria-labelledby="ssH">
            <div class="kx-card-head">
                <div>
                    <h2 id="ssH">Store profile</h2>
                    <p>{{ $checksDone === $checksTotal ? 'Your store profile is complete.' : $checksDone.' of '.$checksTotal.' done — complete stores get more enquiries.' }}</p>
                </div>
                <span class="kx-pill {{ $pill($storeStatus) }}">{{ ucfirst($storeStatus) }}</span>
            </div>
            <div class="kx-card-body">
                <progress class="kx-progress" max="{{ max(1, $checksTotal) }}" value="{{ $checksDone }}">{{ $checksDone }} of {{ $checksTotal }}</progress>
                <ul class="kx-checklist" role="list">
                    @foreach($profileChecks ?? [] as [$label, $done])
                        <li class="{{ $done ? 'is-done' : '' }}">
                            <span class="kx-check" aria-hidden="true">{!! $done ? '<svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>' : '' !!}</span>
                            <span class="kx-check-label">{{ $label }}<span class="kx-sr">{{ $done ? ' (done)' : ' (missing)' }}</span></span>
                            @if(!$done)
                                <a href="{{ $label === 'At least one live product' ? '/dashboard/stores/'.$sid.'/products/create' : '/dashboard/stores/'.$sid.'/edit' }}">Add</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        <section class="kx-card" aria-labelledby="sqH">
            <div class="kx-card-head"><div><h2 id="sqH">Shortcuts</h2></div></div>
            <ul class="kx-shortcuts" role="list">
                <li><a href="/dashboard/stores/{{ $sid }}/edit">Edit store profile</a></li>
                <li><a href="/dashboard/stores/{{ $sid }}/import">Bulk import</a></li>
                <li><a href="/dashboard/stores/{{ $sid }}/analytics">Analytics</a></li>
                <li><a href="/dashboard/stores/{{ $sid }}/reviews">Reviews</a></li>
                <li><a href="/dashboard/deals/create">Submit a deal</a></li>
                <li><a href="/dashboard/membership">Membership</a></li>
            </ul>
        </section>
    </div>
</div>
@endsection
