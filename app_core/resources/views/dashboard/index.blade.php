@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('actions')
<a href="/dashboard/listings/create" class="ka-btn ka-btn-primary">Post a new ad</a>
@endsection

@section('content')
@php
    $me = auth()->user();
    $pill = fn (?string $s) => match ($s) {
        'approved', 'active' => 'ok',
        'rejected' => 'bad',
        'sold' => '',
        default => 'warn',
    };
    $plan = $currentSlug ?? 'free';
@endphp

{{-- Things that need the seller's attention, most important first --}}
@if(!$me->phone_verified_at)
<div class="kx-sysbar is-on">
    <span><span class="kx-status-dot" aria-hidden="true"></span><strong>Verify your phone number.</strong> A one-time SMS code confirms your number and builds buyer trust.</span>
    <a href="/phone/verify" class="ka-btn ka-btn-light kx-btn-sm">Verify by SMS</a>
</div>
@endif

@if(isset($emptyStores) && $emptyStores->isNotEmpty())
<div class="kx-sysbar">
    <span><span class="kx-status-dot" aria-hidden="true"></span><strong>{{ $emptyStores->count() === 1 ? $emptyStores->first()->name.' is live but has no products yet.' : $emptyStores->count().' of your stores are live but have no products yet.' }}</strong> Buyers can't find you until you post a listing.</span>
    <a href="/dashboard/listings/create" class="ka-btn ka-btn-light kx-btn-sm">Post your first listing</a>
</div>
@endif

{{-- Store upgrade: offered once a seller keeps posting; "Not now" hides it for a week --}}
@if(($stats['approved'] ?? 0) >= 3 && $plan === 'free')
<div class="kx-sysbar" id="dbiStoreNudge" data-snooze="7" hidden>
    <span><strong>You've posted {{ $stats['approved'] }} ads. Get a store page for your business:</strong> your own page, a verified badge and room for more products.</span>
    <span class="kx-row-side">
        <a href="{{ route('dashboard.membership') }}" class="ka-btn ka-btn-light kx-btn-sm">See plans</a>
        <button type="button" class="ka-btn kx-btn-sm" data-snooze-close>Not now</button>
    </span>
</div>
@endif

@if($profileIncomplete ?? false)
<div class="kx-sysbar" id="dbiProfileRemind">
    <span>
        <strong>{{ ($isFirstLogin ?? false) ? 'Welcome, '.\Illuminate\Support\Str::of($me->name)->before(' ').'! ' : '' }}Finish your profile</strong>
        to get more responses:
        {{ collect(['photo' => $me->avatar, 'phone number' => $me->phone, 'location' => $me->location_id])->filter(fn ($v) => empty($v))->keys()->join(', ', ' and ') }} missing.
    </span>
    <span class="kx-row-side">
        <a href="{{ route('dashboard.profile') }}" class="ka-btn ka-btn-light kx-btn-sm">Complete profile</a>
        <button type="button" class="kx-alert-x" id="dbiPrClose" aria-label="Dismiss">&times;</button>
    </span>
</div>
@endif

{{-- KPI summary: each card opens the filtered list behind it --}}
<div class="kx-kpis kx-kpis-5">
    <a class="kx-kpi" href="/dashboard/listings">
        <span class="kx-kpi-label">Total listings</span>
        <span class="kx-kpi-value">{{ number_format($stats['listings'] ?? 0) }}</span>
        <span class="kx-kpi-meta">All ads you've posted</span>
    </a>
    <a class="kx-kpi" href="/dashboard/listings?status=approved">
        <span class="kx-kpi-label">Live</span>
        <span class="kx-kpi-value">{{ number_format($stats['approved'] ?? 0) }}</span>
        <span class="kx-kpi-meta">Visible to buyers</span>
    </a>
    <a class="kx-kpi" href="/dashboard/listings?status=pending">
        <span class="kx-kpi-label">Awaiting review</span>
        <span class="kx-kpi-value">{{ number_format($stats['pending'] ?? 0) }}</span>
        <span class="kx-kpi-meta">{{ ($stats['pending'] ?? 0) ? 'Usually approved within a day' : 'Nothing pending' }}</span>
    </a>
    <a class="kx-kpi" href="/dashboard/offers/received">
        <span class="kx-kpi-label">Offers received</span>
        <span class="kx-kpi-value">{{ number_format($stats['offers'] ?? 0) }}</span>
        <span class="kx-kpi-meta">Price offers from buyers</span>
    </a>
    <a class="kx-kpi" href="/dashboard/stores">
        <span class="kx-kpi-label">Stores</span>
        <span class="kx-kpi-value">{{ number_format($stats['stores'] ?? 0) }}</span>
        <span class="kx-kpi-meta">{{ ($stats['stores'] ?? 0) ? 'Manage your storefronts' : 'Open a store to sell more' }}</span>
    </a>
</div>

<div class="kx-grid">
    {{-- Recent listings --}}
    <section class="kx-card kx-col-8" aria-labelledby="dbRecentH">
        <div class="kx-card-head">
            <div><h2 id="dbRecentH">Recent listings</h2><p>Your six newest ads and their status.</p></div>
            <a href="/dashboard/listings">View all</a>
        </div>
        @if(($latestListings ?? collect())->isEmpty())
            <div class="kx-empty">
                <p><strong>No listings yet.</strong> Post your first ad and reach local buyers in Kegalle.</p>
                <a href="/dashboard/listings/create" class="ka-btn ka-btn-primary">Post your first ad</a>
            </div>
        @else
            <ul class="kx-rows" role="list">
                @foreach($latestListings as $listing)
                    <li class="kx-row">
                        <div class="kx-row-main">
                            <a class="kx-row-title" href="/dashboard/listings/{{ $listing->id }}/edit">{{ $listing->title }}</a>
                            <span class="kx-row-meta">
                                {{ $listing->category->name ?? 'Uncategorised' }}
                                @if(!is_null($listing->price)) · LKR {{ number_format((float) $listing->price) }}@endif
                                · {{ $listing->created_at?->diffForHumans() }}
                            </span>
                            @if($listing->status === 'rejected' && !empty($listing->rejection_reason))
                                <span class="kx-row-meta kx-text-danger">Rejected: {{ $listing->rejection_reason }}</span>
                            @endif
                        </div>
                        <div class="kx-row-side">
                            <span class="kx-pill {{ $pill($listing->status) }}">{{ ucfirst($listing->status ?? 'pending') }}</span>
                            <a href="/dashboard/listings/{{ $listing->id }}/edit" class="ka-btn ka-btn-light kx-btn-sm">Edit</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <div class="kx-col-4 kx-stack-col">
        {{-- Getting started checklist --}}
        @if($showOnboarding ?? false)
        <section class="kx-card" aria-labelledby="dbObH">
            <div class="kx-card-head">
                <div><h2 id="dbObH">Getting started</h2><p>{{ $onboardingDone }} of {{ $onboardingTotal }} steps done</p></div>
            </div>
            <div class="kx-card-body">
                <progress class="kx-progress" max="{{ $onboardingTotal }}" value="{{ $onboardingDone }}">{{ $onboardingDone }} of {{ $onboardingTotal }}</progress>
                <ul class="kx-checklist" role="list">
                    @foreach([
                        ['account_created', 'Create your account', null],
                        ['profile_complete', 'Complete your profile', '/dashboard/profile'],
                        ['store_created', 'Create your store', '/dashboard/stores/create'],
                        ['store_with_logo', 'Add a logo to your store', '/dashboard/stores'],
                        ['first_listing', 'Post your first listing', '/dashboard/listings/create'],
                        ['first_approved', 'Get your first listing approved', '/dashboard/listings'],
                    ] as [$key, $label, $href])
                        @continue(!array_key_exists($key, $onboarding))
                        @php $done = $onboarding[$key] ?? false; @endphp
                        <li class="{{ $done ? 'is-done' : '' }}">
                            <span class="kx-check" aria-hidden="true">{!! $done ? '<svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>' : '' !!}</span>
                            <span class="kx-check-label">{{ $label }}<span class="kx-sr">{{ $done ? ' (done)' : ' (to do)' }}</span></span>
                            @if(!$done && $href)<a href="{{ $href }}">Start</a>@endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
        @endif

        {{-- Membership --}}
        <section class="kx-card" aria-labelledby="dbPlanH">
            <div class="kx-card-head">
                <div>
                    <h2 id="dbPlanH">{{ $plan === 'free' ? 'Free plan' : ucfirst($plan).' plan' }}</h2>
                    <p>{{ $plan === 'free' ? 'Upgrade for more listings, a featured badge and priority support.' : 'Your membership is active.' }}</p>
                </div>
                <a href="/dashboard/membership">{{ $plan === 'free' ? 'View plans' : 'Manage' }}</a>
            </div>
        </section>

        {{-- Shortcuts --}}
        <section class="kx-card" aria-labelledby="dbShortH">
            <div class="kx-card-head"><div><h2 id="dbShortH">Shortcuts</h2></div></div>
            <ul class="kx-shortcuts" role="list">
                <li><a href="/dashboard/stores/create">Create a store</a></li>
                <li><a href="/dashboard/deals/create">Submit a deal</a></li>
                <li><a href="/dashboard/services/create">Post a service</a></li>
                <li><a href="/dashboard/chat">Messages</a></li>
                <li><a href="/dashboard/favorites">Saved favourites</a></li>
                <li><a href="/dashboard/contact-admin">Get help</a></li>
            </ul>
        </section>

        {{-- Saved searches --}}
        @if(isset($savedSearches) && $savedSearches->isNotEmpty())
        <section class="kx-card" aria-labelledby="dbSavedH">
            <div class="kx-card-head"><div><h2 id="dbSavedH">Saved searches</h2></div></div>
            <ul class="kx-rows" role="list">
                @foreach($savedSearches as $ss)
                    @php $params = json_decode($ss->params ?? '{}', true) ?: []; @endphp
                    <li class="kx-row">
                        <a class="kx-row-title kx-row-main" href="/listings?{{ http_build_query($params) }}">{{ isset($params['q']) ? '"'.$params['q'].'"' : 'All ads' }}</a>
                        <form method="POST" action="{{ route('listings.saved-search.delete', $ss->id) }}" data-confirm="Remove this saved search?">
                            @csrf @method('DELETE')
                            <button class="ka-btn ka-btn-light kx-btn-sm" type="submit" aria-label="Remove saved search">Remove</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </section>
        @endif
    </div>
</div>

<script nonce="{{ $cspNonce ?? '' }}">
(function(){var c=document.getElementById('dbiPrClose');if(c)c.addEventListener('click',function(){var b=document.getElementById('dbiProfileRemind');if(b)b.remove();});})();
</script>
@endsection
