@extends('layouts.dashboard')
@section('banner_sub', 'Choose the right plan to grow your presence on Kegalle.')
@section('title','Membership')
@section('eyebrow','Account')
@section('heading','Membership')
@section('subheading','Your current plan and available upgrades.')

@section('content')

@if(session('success'))
<div class="prf-alert prf-alert-success" style="margin-bottom:20px">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
  {{ session('success') }}
</div>
@endif

@php
$plans = [
  ['key'=>'free','name'=>'Free','icon'=>'🆓','price'=>'LKR 0','period'=>'Forever','accent'=>'#64748b','bg'=>'#f8fafc',
   'features'=>['3 active listings','Basic store profile','WhatsApp contact button','Kegalle community listing'],
   'ctaCls'=>'mem2-cta-outline'],
  ['key'=>'silver','name'=>'Silver','icon'=>'🥈','price'=>'LKR 990','period'=>'/ month','accent'=>'#475569','bg'=>'#f1f5f9',
   'features'=>['15 active listings','Store analytics dashboard','Priority listing review','Remove ads from your store','Email support'],
   'ctaCls'=>'mem2-cta-silver'],
  ['key'=>'gold','name'=>'Gold','icon'=>'🥇','price'=>'LKR 2,490','period'=>'/ month','accent'=>'#b45309','bg'=>'linear-gradient(135deg,#fffbeb 0%,#fef3c7 100%)',
   'popular'=>true,
   'features'=>['50 active listings','Featured badge on store','Homepage spotlight (monthly)','Bulk import listings','Dedicated support','Priority search placement'],
   'ctaCls'=>'mem2-cta-gold'],
  ['key'=>'platinum','name'=>'Platinum','icon'=>'💎','price'=>'LKR 4,990','period'=>'/ month','accent'=>'#6d28d9','bg'=>'linear-gradient(135deg,#f5f3ff 0%,#ede9fe 100%)',
   'features'=>['200 active listings','Top-of-search placement','Verified seller badge','Featured on homepage carousel','Early access to new features','Dedicated account manager'],
   'ctaCls'=>'mem2-cta-platinum'],
];
$activePlan = $currentSlug ?? 'free';
@endphp



<div class="mem2-grid">
  @foreach($plans as $plan)
  @php
    $isCurrent = $plan['key'] === $activePlan;
    $isPending  = in_array($plan['key'], $pendingSlugs ?? []);
    $extra = $isCurrent ? 'mem2-card-current' : ($plan['key']==='gold' ? 'mem2-card-gold' : ($plan['key']==='platinum' ? 'mem2-card-platinum' : ''));
  @endphp
  <div class="mem2-card {{ $extra }}">

    @if($isCurrent)
      <div class="mem2-badge-wrap"><div class="mem2-badge-current">✓ Your Plan</div></div>
    @elseif(!empty($plan['popular']))
      <div class="mem2-badge-wrap"><div class="mem2-badge-popular">Most Popular</div></div>
    @endif

    <div class="mem2-header" style="background:{{ $plan['bg'] }}">
      <span class="mem2-icon">{{ $plan['icon'] }}</span>
      <div class="mem2-tier" style="color:{{ $plan['accent'] }}">{{ $plan['name'] }}</div>
      <div class="mem2-price-row">
        <span class="mem2-price">{{ $plan['price'] }}</span>
        <span class="mem2-period">{{ $plan['period'] }}</span>
      </div>
    </div>

    <div class="mem2-divider"></div>

    <div class="mem2-features">
      @foreach($plan['features'] as $feat)
      <div class="mem2-feature">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="{{ $plan['accent'] }}" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        {{ $feat }}
      </div>
      @endforeach
    </div>

    <div class="mem2-foot">
      @if($isCurrent)
        <div class="mem2-cta mem2-cta-active">✓ Active Plan</div>
        @if($plan['key'] !== 'free' && isset($expiresAt))
          <span class="mem2-expiry">Expires {{ $expiresAt->format('M d, Y') }}</span>
        @endif
      @elseif($isPending)
        <div class="mem2-pending-chip">⏳ Under Review</div>
      @elseif($plan['key'] === 'free')
        <div class="mem2-cta mem2-cta-active">✓ Default Plan</div>
      @else
        <a href="/dashboard/membership/upgrade/{{ $plan['key'] }}" class="mem2-cta {{ $plan['ctaCls'] }}">Upgrade Now →</a>
      @endif
    </div>

  </div>
  @endforeach
</div>

{{-- Offline payment note --}}
<div class="mem2-payment-note">
  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
  All plans are activated via bank transfer. Upload your payment slip and get approved within 24 hours.
  <a href="mailto:support@kegalle.com">Questions? support@kegalle.com</a>
</div>


@endsection
