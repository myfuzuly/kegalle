@extends('layouts.app')
@section('title', $service->title . ' — Kegalle Marketplace')
@section('meta_description', Str::limit(strip_tags($service->description ?? $service->title . ' service in ' . $service->location), 155))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/kegalle-listing-detail.css') }}?v=4">
@endpush

@section('content')
<div class="svc-detail-wrap">

  {{-- Breadcrumb --}}
  <div class="svc-detail-breadcrumb">
    <a href="/">Home</a><span>›</span>
    <a href="{{ route('services.index') }}">Services</a><span>›</span>
    <span>{{ $service->title }}</span>
  </div>

  <div class="svc-detail-grid" style="margin-top:20px">

    {{-- ── Left: Image + Tabs ─────────────────────────────── --}}
    <div>
      {{-- Image --}}
      <div class="svc-img-zone">
        @if($service->image)
          <img src="{{ asset('storage/'.$service->image) }}" alt="{{ $service->title }}">
        @else
          <div class="svc-img-ph">
            @php
              $phIcons=['cleaning'=>'🧹','repair'=>'🔧','consulting'=>'💼','it'=>'💻','education'=>'📚','transport'=>'🚐','construction'=>'🏗️','healthcare'=>'🏥','beauty'=>'💄','events'=>'🎉','legal'=>'⚖️','brokering'=>'🤝'];
              echo '<span class="svc-img-ph-icon">'.($phIcons[$service->service_type] ?? '🛠️').'</span>';
            @endphp
            <span class="svc-img-ph-label">{{ ucfirst(str_replace('_',' ',$service->service_type)) }}</span>
          </div>
        @endif
        <span class="svc-type-stamp">{{ ucfirst(str_replace('_',' ',$service->service_type)) }}</span>
        @if($service->experience_years > 0)
          <span class="svc-exp-stamp">🏆 {{ $service->experience_years }}yr exp</span>
        @endif
      </div>

      {{-- Tabs --}}
      <div class="svc-tabs">
        <button class="svc-tab active" data-stab="about">About</button>
        <button class="svc-tab" data-stab="more">More from Provider</button>
      </div>

      <div class="svc-tab-panel active" id="stab-about">
        @if($service->description)
          <div class="svc-about-text">{{ $service->description }}</div>
        @else
          <div class="svc-no-desc">
            <div style="font-size:32px;margin-bottom:8px">📝</div>
            <div>No description provided for this service yet.</div>
          </div>
        @endif
        @if($service->areas_covered)
        <div class="svc-areas-box">
          <div class="svc-areas-label">Areas Covered</div>
          <div class="svc-areas-val">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2" style="margin-top:2px;flex-shrink:0"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            {{ $service->areas_covered }}
          </div>
        </div>
        @endif
      </div>

      <div class="svc-tab-panel" id="stab-more">
        @if($otherServices->isEmpty())
          <div class="svc-no-desc">
            <div style="font-size:28px;margin-bottom:8px">🛠️</div>
            <div>No other services from this provider.</div>
          </div>
        @else
          <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px">
            @foreach($otherServices as $other)
            <a href="{{ route('services.show', $other->slug) }}" style="text-decoration:none;color:inherit;display:block;border:1.5px solid var(--k-border);border-radius:12px;overflow:hidden;background:var(--k-surface);transition:box-shadow .15s" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)'" onmouseout="this.style.boxShadow=''">
              <div style="height:100px;background:linear-gradient(135deg,#f0fdf4,#dcfce7);display:flex;align-items:center;justify-content:center;font-size:28px">
                @if($other->image)<img src="{{ asset('storage/'.$other->image) }}" alt="{{ $other->title }}" style="width:100%;height:100%;object-fit:cover">@else🛠️@endif
              </div>
              <div style="padding:10px 12px">
                <div style="font-size:13px;font-weight:600;color:var(--k-text);margin-bottom:4px">{{ Str::limit($other->title, 45) }}</div>
                <div style="font-size:12px;color:#15803d;font-weight:700">
                  @if($other->pricing_model==='free_quote') Free Quote
                  @elseif($other->price) LKR {{ number_format($other->price) }}
                  @else Negotiable @endif
                </div>
              </div>
            </a>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    {{-- ── Middle: Info + Actions ──────────────────────────── --}}
    <div class="svc-info-col">
      <div class="svc-badge-row">
        <span class="svc-badge svc-badge-type">{{ ucfirst(str_replace('_',' ',$service->service_type)) }}</span>
        @if($service->category)<span class="svc-badge svc-badge-cat">{{ $service->category->name }}</span>@endif
      </div>

      <h1 class="svc-title">{{ $service->title }}</h1>

      <div class="svc-meta-row">
        <span>{{ $service->views }} views</span>
        <span class="svc-meta-dot">·</span>
        <span>{{ $service->created_at?->diffForHumans() }}</span>
      </div>

      {{-- Price box --}}
      <div class="svc-price-box">
        <div>
          @if($service->pricing_model === 'free_quote')
            <div class="svc-price-free">Free Quote</div>
            <div class="svc-price-unit">Request a custom quote</div>
          @elseif($service->price)
            <div class="svc-price-amount">LKR {{ number_format($service->price) }}</div>
            <div class="svc-price-unit">
              @if($service->pricing_model === 'hourly') Per Hour
              @elseif($service->pricing_model === 'daily') Per Day
              @elseif($service->pricing_model === 'fixed') Fixed Price
              @else Per Service @endif
            </div>
          @else
            <div class="svc-price-neg">Negotiable</div>
            <div class="svc-price-unit">Contact for pricing</div>
          @endif
        </div>
        <span class="svc-price-icon">
          @if($service->pricing_model === 'free_quote')💬
          @elseif($service->pricing_model === 'hourly')⏱️
          @else💰@endif
        </span>
      </div>

      {{-- Location + Exp --}}
      <div class="svc-loc-row">
        <div class="svc-loc-item">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <strong>{{ $service->location }}</strong>
        </div>
        @if($service->experience_years > 0)
        <div class="svc-loc-item">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
          <strong>{{ $service->experience_years }} yr{{ $service->experience_years > 1 ? 's' : '' }} experience</strong>
        </div>
        @endif
      </div>

      {{-- Specs --}}
      <div class="svc-specs">
        @if($service->category)
        <div class="svc-spec-row">
          <span class="svc-spec-label">Category</span>
          <span class="svc-spec-val">{{ $service->category->name }}</span>
        </div>
        @endif
        <div class="svc-spec-row">
          <span class="svc-spec-label">Service Type</span>
          <span class="svc-spec-val">{{ ucfirst(str_replace('_',' ',$service->service_type)) }}</span>
        </div>
        <div class="svc-spec-row">
          <span class="svc-spec-label">Pricing</span>
          <span class="svc-spec-val">{{ ucfirst(str_replace('_',' ',$service->pricing_model)) }}</span>
        </div>
        @if($service->experience_years > 0)
        <div class="svc-spec-row">
          <span class="svc-spec-label">Experience</span>
          <span class="svc-spec-val">{{ $service->experience_years }} year{{ $service->experience_years > 1 ? 's' : '' }}</span>
        </div>
        @endif
        @if($service->areas_covered)
        <div class="svc-spec-row">
          <span class="svc-spec-label">Areas</span>
          <span class="svc-spec-val">{{ $service->areas_covered }}</span>
        </div>
        @endif
      </div>

      {{-- Contact buttons --}}
      @php
        $waNum  = preg_replace('/[^0-9]/', '', $service->whatsapp ?? $service->phone ?? '');
        $waMsg  = base64_encode('Hi, I\'m interested in your service: '.$service->title.' — '.url('/services/'.$service->slug));
        $waB64  = base64_encode($waNum);
        $telB64 = base64_encode($service->phone ?? '');
        $telFmt = $service->phone ?? '';
      @endphp
      <div class="svc-contact-block">
        @if($waNum)
        <button type="button" class="svc-btn-wa k-reveal-wa" data-wa="{{ $waB64 }}" data-msg="{{ $waMsg }}">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
          Chat on WhatsApp
        </button>
        @endif
        @if($service->phone)
        <button type="button" class="svc-btn-call k-reveal-call" data-tel="{{ $telB64 }}" data-fmt="{{ $telFmt }}">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.42 2 2 0 0 1 3.6 1h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 8.6a16 16 0 0 0 6 6l.95-.93a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          Tap to Call
        </button>
        @elseif($service->email)
        <a href="mailto:{{ $service->email }}" class="svc-btn-call">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          Send Email
        </a>
        @endif
      </div>

      {{-- Share --}}
      <div class="svc-share-row">
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/services/'.$service->slug)) }}" target="_blank" rel="noopener" class="svc-share-btn">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
          Share
        </a>
        <a href="https://wa.me/?text={{ urlencode($service->title.' — '.url('/services/'.$service->slug)) }}" target="_blank" rel="noopener" class="svc-share-btn">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
          WhatsApp
        </a>
      </div>
    </div>

    {{-- ── Right Sidebar ───────────────────────────────────── --}}
    <div class="svc-sidebar">
      {{-- Provider card --}}
      <div class="svc-provider-card">
        <div class="svc-provider-card-top">
          <div class="svc-prov-avatar">{{ strtoupper(substr(optional($service->user)->name ?? 'P', 0, 1)) }}</div>
          <div>
            <div class="svc-prov-eyebrow">Service Provider</div>
            <div class="svc-prov-name">{{ optional($service->user)->name ?? 'Provider' }}</div>
            @if($service->experience_years > 0)
              <div class="svc-prov-exp">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                {{ $service->experience_years }} yrs experience
              </div>
            @endif
          </div>
        </div>
        <div class="svc-prov-stats">
          <div class="svc-prov-stat">
            <strong>{{ optional($service->user)->created_at?->format('Y') ?? '—' }}</strong>
            <span>Member Since</span>
          </div>
          <div class="svc-prov-stat">
            <strong>{{ \App\Models\Service::where('user_id', $service->user_id)->where('status','approved')->count() }}</strong>
            <span>Services</span>
          </div>
          <div class="svc-prov-stat">
            <strong style="font-size:13px">{{ $service->location }}</strong>
            <span>Location</span>
          </div>
        </div>
      </div>

      {{-- Safety tips --}}
      <div class="svc-safety-card">
        <div class="svc-safety-title">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7v5c0 5.25 3.75 10.15 9 11.35C17.25 22.15 21 17.25 21 12V7L12 2z" fill="#d97706" opacity=".2"/><path d="M12 2L3 7v5c0 5.25 3.75 10.15 9 11.35C17.25 22.15 21 17.25 21 12V7L12 2z" stroke="#b45309" stroke-width="1.5" stroke-linejoin="round"/><path d="M9 12l2 2 4-4" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Safety Tips
        </div>
        @foreach(['Verify credentials before hiring','Agree on price before work begins','Avoid large advance payments','Meet at a safe, public location'] as $tip)
        <div class="svc-safety-item">
          <div class="svc-safety-dot">
            <svg width="10" height="10" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          <span>{{ $tip }}</span>
        </div>
        @endforeach
      </div>

      {{-- Location card --}}
      <div class="svc-loc-card">
        <div class="svc-loc-card-head">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Location
        </div>
        <div class="svc-loc-card-body">
          <div style="font-size:36px;margin-bottom:10px">📍</div>
          <div class="svc-loc-city">{{ $service->location }}</div>
          <div class="svc-loc-district">Kegalle District, Sri Lanka</div>
        </div>
        <a href="https://www.google.com/maps/search/{{ urlencode($service->location.' Kegalle Sri Lanka') }}" target="_blank" rel="noopener" class="svc-loc-map-btn">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
          View on Google Maps
        </a>
      </div>
    </div>

  </div>
</div>

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
  // Tab switching
  document.querySelectorAll('.svc-tab[data-stab]').forEach(function(tab){
    tab.addEventListener('click', function(){
      document.querySelectorAll('.svc-tab').forEach(function(t){t.classList.remove('active');});
      document.querySelectorAll('.svc-tab-panel').forEach(function(p){p.classList.remove('active');});
      tab.classList.add('active');
      var panel = document.getElementById('stab-' + tab.dataset.stab);
      if(panel) panel.classList.add('active');
    });
  });
  // Reveal WhatsApp
  document.querySelectorAll('.k-reveal-wa').forEach(function(btn){
    btn.addEventListener('click', function(){
      var num = atob(btn.dataset.wa);
      var msg = atob(btn.dataset.msg);
      var a = document.createElement('a');
      a.href = 'https://wa.me/' + num + '?text=' + encodeURIComponent(msg);
      a.target = '_blank'; a.rel = 'noopener noreferrer';
      a.className = btn.className.replace('k-reveal-wa','').trim() + ' svc-btn-wa';
      a.innerHTML = btn.innerHTML;
      btn.replaceWith(a);
      a.click();
    });
  });
  // Reveal Call
  document.querySelectorAll('.k-reveal-call').forEach(function(btn){
    btn.addEventListener('click', function(){
      var tel = atob(btn.dataset.tel);
      var fmt = btn.dataset.fmt || tel;
      var a = document.createElement('a');
      a.href = 'tel:' + tel;
      a.className = btn.className.replace('k-reveal-call','').trim() + ' svc-btn-call';
      a.innerHTML = btn.innerHTML.replace('Tap to Call', fmt);
      btn.replaceWith(a);
    });
  });
})();
</script>
@endpush
@endsection
