@extends('layouts.app')
@section('title', 'Services in Kegalle — Kegalle Marketplace')
@section('meta_description', 'Find trusted local service providers in Kegalle — repair, consulting, cleaning, transport and more. Browse ' . $services->total() . ' verified services.')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/kegalle-services.css') }}?v=1">
@endpush

@section('content')

{{-- Hero Banner --}}
<div class="svc-hero">
  <div class="svc-hero-inner">
    <div class="svc-hero-eyebrow">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
      Local Services
    </div>
    <h1>Find Trusted Services<br>in Kegalle</h1>
    <p>Browse verified local service providers — cleaning, repair, consulting, IT, education and more.</p>

    <form method="GET" action="{{ route('services.index') }}" class="svc-hero-search">
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Search services…" autocomplete="off">
      <select name="type">
        <option value="">All Types</option>
        @foreach(['cleaning'=>'Cleaning','consulting'=>'Consulting','repair'=>'Repair','transport'=>'Transport','brokering'=>'Brokering','construction'=>'Construction','legal'=>'Legal','it'=>'IT / Tech','education'=>'Education','healthcare'=>'Healthcare','beauty'=>'Beauty','events'=>'Events','general'=>'General'] as $v => $l)
          <option value="{{ $v }}" {{ request('type') === $v ? 'selected' : '' }}>{{ $l }}</option>
        @endforeach
      </select>
      <button type="submit">Search</button>
    </form>

    <div class="svc-hero-stats">
      <div class="svc-hero-stat"><strong>{{ $services->total() }}</strong> services available</div>
      <div class="svc-hero-stat"><strong>Kegalle</strong> district</div>
      <div class="svc-hero-stat"><strong>Free</strong> to browse</div>
    </div>
  </div>
</div>

<div class="svc-body">

  {{-- Post CTA --}}
  <div class="svc-post-strip">
    <div class="svc-post-strip-text">
      <h3>Offer a Service?</h3>
      <p>List your service for free and reach thousands of local customers in Kegalle.</p>
    </div>
    @auth
    <a href="{{ route('dashboard.services.create') }}" class="k-btn k-btn-primary" style="white-space:nowrap">+ Post a Service</a>
    @else
    <a href="{{ route('login') }}" class="k-btn k-btn-primary" style="white-space:nowrap">+ Post a Service</a>
    @endauth
  </div>

  {{-- Type pill filters --}}
  <div class="svc-pill-row">
    <a href="{{ route('services.index', array_merge(request()->except('type','page'), [])) }}" class="svc-pill {{ !request('type') ? 'active' : '' }}">
      <span class="svc-pill-icon">🛠️</span> All Services
    </a>
    @foreach(['cleaning'=>['🧹','Cleaning'],'repair'=>['🔧','Repair'],'consulting'=>['💼','Consulting'],'it'=>['💻','IT / Tech'],'education'=>['📚','Education'],'transport'=>['🚐','Transport'],'construction'=>['🏗️','Construction'],'healthcare'=>['🏥','Healthcare'],'beauty'=>['💄','Beauty'],'events'=>['🎉','Events'],'legal'=>['⚖️','Legal'],'brokering'=>['🤝','Brokering'],'general'=>['✨','General']] as $v => $meta)
    <a href="{{ route('services.index', array_merge(request()->except('type','page'), ['type'=>$v])) }}"
       class="svc-pill {{ request('type') === $v ? 'active' : '' }}">
      <span class="svc-pill-icon">{{ $meta[0] }}</span> {{ $meta[1] }}
    </a>
    @endforeach
  </div>

  {{-- Top bar --}}
  <div class="svc-top-bar">
    <div class="svc-count">
      <strong>{{ $services->total() }}</strong> {{ Str::plural('service', $services->total()) }} found
      @if(request('q') || request('type'))
        @if(request('q'))<span> for "<em>{{ request('q') }}</em>"</span>@endif
        @if(request('type'))<span> in <em>{{ ucfirst(request('type')) }}</em></span>@endif
        &nbsp;<a href="{{ route('services.index') }}" style="font-size:12px;color:#1b5e20;font-weight:600">Clear filters ×</a>
      @endif
    </div>
  </div>

  @if($services->isEmpty())
    <div class="svc-empty">
      <div class="svc-empty-icon">🛠️</div>
      <h3>No services found</h3>
      <p>Try a different search or browse all service types.</p>
      <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
        <a href="{{ route('services.index') }}" class="k-btn k-btn-outline">Clear Filters</a>
        @auth
        <a href="{{ route('dashboard.services.create') }}" class="k-btn k-btn-primary">+ Post a Service</a>
        @endauth
      </div>
    </div>
  @else
    <div class="svc-grid">
      @foreach($services as $service)
      <a href="{{ route('services.show', $service->slug) }}" class="svc-card">
        <div class="svc-card-img">
          @if($service->image)
            <img src="{{ asset('storage/'.$service->image) }}" alt="{{ $service->title }}" loading="lazy">
          @else
            <div class="svc-card-ph">
              @php
                $icons=['cleaning'=>'🧹','repair'=>'🔧','consulting'=>'💼','it'=>'💻','education'=>'📚','transport'=>'🚐','construction'=>'🏗️','healthcare'=>'🏥','beauty'=>'💄','events'=>'🎉','legal'=>'⚖️','brokering'=>'🤝'];
                echo $icons[$service->service_type] ?? '🛠️';
              @endphp
            </div>
          @endif
          <span class="svc-type-badge">{{ ucfirst(str_replace('_',' ',$service->service_type)) }}</span>
          @if($service->experience_years > 0)
            <span class="svc-exp-badge">{{ $service->experience_years }}yr exp</span>
          @endif
        </div>
        <div class="svc-card-body">
          <div class="svc-card-title">{{ Str::limit($service->title, 65) }}</div>
          <div class="svc-card-loc">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            {{ $service->location }}
            @if($service->category) · {{ $service->category->name }}@endif
          </div>
          <div class="svc-card-footer">
            <div>
              @if($service->pricing_model === 'free_quote')
                <div class="svc-price-free">Free Quote</div>
              @elseif($service->price)
                <div class="svc-price">LKR {{ number_format($service->price) }}
                  @if($service->pricing_model === 'hourly')<span style="font-size:11px;font-weight:400;color:var(--k-text-muted)">/hr</span>@endif
                </div>
              @else
                <div class="svc-price-neg">Negotiable</div>
              @endif
            </div>
            <div class="svc-provider">
              <div class="svc-avatar">{{ strtoupper(substr(optional($service->user)->name ?? 'P', 0, 1)) }}</div>
              <div class="svc-provider-name">{{ Str::limit(optional($service->user)->name ?? 'Provider', 18) }}</div>
            </div>
          </div>
        </div>
      </a>
      @endforeach
    </div>

    <div style="margin-top:36px">{{ $services->links() }}</div>
  @endif

</div>
@endsection
