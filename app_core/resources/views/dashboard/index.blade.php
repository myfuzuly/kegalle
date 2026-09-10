@extends('layouts.dashboard')
@section('title','Dashboard')
@section('eyebrow','Overview')
@section('heading','My Dashboard')

@section('actions')
<a href="/dashboard/listings/create" class="kdl-tb-btn kdl-tb-btn-primary">
  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
  Post New Ad
</a>
@endsection

@section('content')

{{-- Phone verify --}}
@if(!auth()->user()->phone_verified_at)
<div class="dbi-verify">
    <div class="dbi-verify-icon">📱</div>
    <div class="dbi-verify-body">
        <strong>Verify your phone number</strong>
        <span>One-time SMS code confirms your number and builds buyer trust.</span>
    </div>
    <a href="/phone/verify" class="dbi-verify-btn">Verify via SMS →</a>
</div>
@endif

{{-- Store nudge --}}
@if(isset($emptyStores) && $emptyStores->isNotEmpty())
<div class="dbi-nudge">
    <div class="dbi-nudge-icon">🏪</div>
    <div class="dbi-nudge-body">
        <strong>Your store is live — add your first product!</strong>
        <span>Buyers can't find you until you post a listing. It only takes 2 minutes.</span>
    </div>
    <a href="/dashboard/listings/create" class="dbi-nudge-btn">Post First Listing</a>
</div>
@endif

{{-- Profile completion prompt --}}
@if($profileIncomplete ?? false)
  @if($isFirstLogin ?? false)
  <div class="dbi-profile-welcome" id="dbiProfileWelcome">
    <div class="dbi-pw-left">
      <div class="dbi-pw-avatar">
        @if(auth()->user()->avatar)
          <img src="{{ auth()->user()->avatar }}" alt="">
        @else
          <span>{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
        @endif
        <div class="dbi-pw-avatar-badge">👋</div>
      </div>
      <div class="dbi-pw-text">
        <div class="dbi-pw-title">Welcome, {{ explode(' ', auth()->user()->name)[0] }}! 🎉</div>
        <div class="dbi-pw-sub">Complete your profile to post ads, build trust with buyers, and get found faster.</div>
        <div class="dbi-pw-checklist">
          <span class="{{ auth()->user()->avatar ? 'dbi-pwc-done' : 'dbi-pwc-todo' }}">{{ auth()->user()->avatar ? '✓' : '○' }} Profile photo</span>
          <span class="{{ auth()->user()->phone ? 'dbi-pwc-done' : 'dbi-pwc-todo' }}">{{ auth()->user()->phone ? '✓' : '○' }} Phone number</span>
          <span class="{{ auth()->user()->location_id ? 'dbi-pwc-done' : 'dbi-pwc-todo' }}">{{ auth()->user()->location_id ? '✓' : '○' }} Location</span>
        </div>
      </div>
    </div>
    <div class="dbi-pw-right">
      <a href="{{ route('dashboard.profile') }}" class="dbi-pw-btn">Complete Profile →</a>
      <button class="dbi-pw-dismiss" id="dbiPwDismiss" aria-label="Dismiss">×</button>
    </div>
  </div>
  @else
  <div class="dbi-profile-remind" id="dbiProfileRemind">
    <span class="dbi-pr-icon">👤</span>
    <span class="dbi-pr-text">Your profile is incomplete — <a href="{{ route('dashboard.profile') }}">add your photo, phone &amp; location</a> to get more responses.</span>
    <button class="dbi-pr-close" id="dbiPrClose" aria-label="Dismiss">×</button>
  </div>
  @endif
@endif

{{-- Stat cards --}}
<div class="dbi-grid">
    <a href="/dashboard/listings" class="dbi-stat dbi-stat-blue">
        <div class="dbi-ic dbi-ic-blue">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        </div>
        <div>
            <div class="dbi-num">{{ $stats['listings'] ?? 0 }}</div>
            <div class="dbi-lbl">Total Ads</div>
        </div>
    </a>
    <a href="/dashboard/listings?status=approved" class="dbi-stat dbi-stat-green">
        <div class="dbi-ic dbi-ic-green">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div>
            <div class="dbi-num">{{ $stats['approved'] ?? 0 }}</div>
            <div class="dbi-lbl">Approved</div>
        </div>
    </a>
    <a href="/dashboard/listings?status=pending" class="dbi-stat dbi-stat-amber">
        <div class="dbi-ic dbi-ic-amber">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
            <div class="dbi-num">{{ $stats['pending'] ?? 0 }}</div>
            <div class="dbi-lbl">Pending</div>
        </div>
    </a>
    <a href="/dashboard/stores" class="dbi-stat dbi-stat-purple">
        <div class="dbi-ic dbi-ic-purple">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
        <div>
            <div class="dbi-num">{{ $stats['stores'] ?? 0 }}</div>
            <div class="dbi-lbl">My Stores</div>
        </div>
    </a>
    <a href="/dashboard/offers/received" class="dbi-stat dbi-stat-rose">
        <div class="dbi-ic dbi-ic-rose">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <div>
            <div class="dbi-num">{{ $stats['offers'] ?? 0 }}</div>
            <div class="dbi-lbl">Offers</div>
        </div>
    </a>
</div>

{{-- Onboarding --}}
@if($showOnboarding ?? false)
<div class="dbi-ob">
    <div class="dbi-ob-head">
        <div>
            <div class="dbi-ob-title">🚀 Getting Started</div>
            <div class="dbi-ob-sub">{{ $onboardingDone }}/{{ $onboardingTotal }} steps complete</div>
        </div>
        <div class="dbi-ob-bar">
            <div class="dbi-ob-fill" style="width:{{ round($onboardingDone/$onboardingTotal*100) }}%"></div>
        </div>
    </div>
    @foreach([
        ['key'=>'account_created','label'=>'Create your account','href'=>null],
        ['key'=>'profile_complete','label'=>'Complete your profile','href'=>'/dashboard/profile'],
        ['key'=>'store_created','label'=>'Create your store','href'=>'/dashboard/stores/create'],
        ['key'=>'store_with_logo','label'=>'Add a logo to your store','href'=>'/dashboard/stores'],
        ['key'=>'first_listing','label'=>'Post your first listing','href'=>'/dashboard/listings/create'],
        ['key'=>'first_approved','label'=>'Get your first listing approved','href'=>'/dashboard/listings'],
    ] as $step)
    @if(!array_key_exists($step['key'], $onboarding)) @continue @endif
    @php $done = $onboarding[$step['key']] ?? false; @endphp
    <div class="dbi-ob-step {{ $done ? 'done' : '' }}">
        <div class="dbi-ob-dot {{ $done ? 'done' : 'todo' }}">{{ $done ? '✓' : '·' }}</div>
        <span style="{{ $done ? 'text-decoration:line-through' : '' }}">{{ $step['label'] }}</span>
        @if(!$done && $step['href'])<a href="{{ $step['href'] }}" class="dbi-ob-do">Do it →</a>@endif
    </div>
    @endforeach
</div>
@endif

{{-- Main content columns --}}
<div class="dbi-cols">

    {{-- Recent Ads --}}
    <section class="dbi-card">
        <div class="dbi-card-head">
            <h3>Recent Listings</h3>
            <a href="/dashboard/listings">View all →</a>
        </div>
        @forelse(($latestListings ?? []) as $listing)
        @php
            $pillCls = match($listing->status ?? 'pending') {
                'approved','active' => 'dbi-pill-green',
                'rejected' => 'dbi-pill-red',
                'sold' => 'dbi-pill-gray',
                default => 'dbi-pill-amber',
            };
            $pillLabel = ucfirst($listing->status ?? 'Pending');
        @endphp
        <div class="dbi-row">
            <div class="flex-grow-min">
                <div class="dbi-row-title">{{ $listing->title }}</div>
                <div class="dbi-row-meta">
                    <span class="dbi-pill {{ $pillCls }}">{{ $pillLabel }}</span>
                    <span class="dbi-row-time">{{ $listing->created_at?->diffForHumans() }}</span>
                </div>
                @if($listing->status === 'rejected' && !empty($listing->rejection_reason))
                <div class="fs115-red-mt3">⚠ {{ $listing->rejection_reason }}</div>
                @endif
            </div>
            <a href="/dashboard/listings/{{ $listing->id }}/edit" class="dbi-row-edit">Edit</a>
        </div>
        @empty
        <div class="dbi-empty">
            <div class="dbi-empty-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <strong>No listings yet</strong>
            <p>Post your first ad and reach thousands of local buyers.</p>
            <a href="/dashboard/listings/create" class="dbi-empty-btn">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Post First Ad
            </a>
        </div>
        @endforelse
    </section>

    {{-- Right column --}}
    <div class="dbi-right-col">

        {{-- Membership plan badge --}}
        @php $plan = $currentSlug ?? 'free'; @endphp
        @if($plan === 'free')
        <div class="dbi-upgrade-card">
            <div class="dbi-upgrade-icon">⭐</div>
            <div class="dbi-upgrade-body">
                <div class="dbi-upgrade-title">Upgrade your plan</div>
                <div class="dbi-upgrade-sub">Get more listings, featured badge &amp; priority support.</div>
            </div>
            <a href="/dashboard/membership" class="dbi-upgrade-btn">View Plans</a>
        </div>
        @else
        <div class="dbi-plan-badge dbi-plan-{{ $plan }}">
            <span class="dbi-plan-icon">{{ $plan === 'gold' ? '🥇' : ($plan === 'platinum' ? '💎' : '🥈') }}</span>
            <div>
                <div class="dbi-plan-name">{{ ucfirst($plan) }} Plan</div>
                <div class="dbi-plan-sub">Active membership</div>
            </div>
            <a href="/dashboard/membership" class="dbi-plan-link">Manage →</a>
        </div>
        @endif

        {{-- Quick Actions --}}
        <section class="dbi-card">
            <div class="dbi-card-head"><h3>Quick Actions</h3></div>
            <div class="dbi-qa">
                <a href="/dashboard/listings/create" class="dbi-qa-btn">
                    <span class="dbi-qa-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    </span>
                    <span class="dbi-qa-text">Post Ad</span>
                </a>
                <a href="/dashboard/stores/create" class="dbi-qa-btn">
                    <span class="dbi-qa-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </span>
                    <span class="dbi-qa-text">Create Store</span>
                </a>
                <a href="/dashboard/deals/create" class="dbi-qa-btn">
                    <span class="dbi-qa-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                    </span>
                    <span class="dbi-qa-text">Submit Deal</span>
                </a>
                <a href="/dashboard/chat" class="dbi-qa-btn">
                    <span class="dbi-qa-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                    </span>
                    <span class="dbi-qa-text">Messages</span>
                </a>
                <a href="/dashboard/offers/received" class="dbi-qa-btn">
                    <span class="dbi-qa-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </span>
                    <span class="dbi-qa-text">Offers</span>
                </a>
                <a href="/dashboard/membership" class="dbi-qa-btn">
                    <span class="dbi-qa-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    </span>
                    <span class="dbi-qa-text">Membership</span>
                </a>
                <a href="/dashboard/favorites" class="dbi-qa-btn">
                    <span class="dbi-qa-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                    </span>
                    <span class="dbi-qa-text">Favorites</span>
                </a>
                <a href="/dashboard/contact-admin" class="dbi-qa-btn">
                    <span class="dbi-qa-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </span>
                    <span class="dbi-qa-text">Get Help</span>
                </a>
            </div>
        </section>

        {{-- Saved Searches --}}
        @if(isset($savedSearches) && $savedSearches->isNotEmpty())
        <section class="dbi-card">
            <div class="dbi-card-head"><h3>Saved Searches</h3></div>
            @foreach($savedSearches as $ss)
            @php $params = json_decode($ss->params ?? '{}', true) ?: []; @endphp
            <div class="dbi-row">
                <a class="flex1-green-trunc" href="/listings?{{ http_build_query($params) }}">
                    🔍 {{ isset($params['q']) ? '"'.$params['q'].'"' : 'All ads' }}
                </a>
                <form method="POST" action="{{ route('listings.saved-search.delete', $ss->id) }}" class="d-inline flex-shrink-0">
                    @csrf @method('DELETE')
                    <button class="btn-bare-muted" type="submit" title="Remove">✕</button>
                </form>
            </div>
            @endforeach
        </section>
        @endif

    </div>
</div>

<script nonce="{{ $cspNonce ?? '' }}">
function dbiDismissProfile(){
    var el=document.getElementById('dbiProfileWelcome');
    if(el){el.style.opacity='0';el.style.transition='opacity .3s';setTimeout(function(){el.remove();},300);}
}
(function(){
    var d=document.getElementById('dbiPwDismiss');
    if(d) d.addEventListener('click',dbiDismissProfile);
    var c=document.getElementById('dbiPrClose');
    if(c) c.addEventListener('click',function(){c.parentElement.remove();});
})();
</script>


@endsection
