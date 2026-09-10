@extends('layouts.app')
@section('title','Public Services · Kegalle Marketplace')
@section('meta_description','Find local public services in Kegalle district — DS Office, Municipal Council, Police, Hospitals, Schools, Courts, Fire Rescue, Ambulance, Disaster Management and more.')
@section('content')

{{-- ── Hero ─────────────────────────────────────────────────────────────────── --}}
<div class="gs-hero">
    <div class="gs-hero-overlay"></div>
    <div class="gs-hero-inner">
        <div class="gs-hero-badge">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
            Kegalle District · Sri Lanka
        </div>
        <h1 class="gs-hero-title">Public Services</h1>
        <p class="gs-hero-sub">Essential public services for residents of Kegalle district — all in one place.</p>
        <div class="gs-hero-pills">
            <span class="gs-hero-pill">🏛️ DS Office</span>
            <span class="gs-hero-pill">🚔 Police</span>
            <span class="gs-hero-pill">🏥 Hospitals</span>
            <span class="gs-hero-pill">🚒 Fire Rescue</span>
        </div>
    </div>
</div>

{{-- ── Stats bar ─────────────────────────────────────────────────────────────── --}}
<div class="gs-stats-bar">
    <div class="gs-stat"><span class="gs-stat-n">12+</span><span class="gs-stat-l">Services</span></div>
    <div class="gs-stat-div"></div>
    <div class="gs-stat"><span class="gs-stat-n">Free</span><span class="gs-stat-l">To Access</span></div>
    <div class="gs-stat-div"></div>
    <div class="gs-stat"><span class="gs-stat-n">24/7</span><span class="gs-stat-l">Emergency</span></div>
    <div class="gs-stat-div"></div>
    <div class="gs-stat"><span class="gs-stat-n">1</span><span class="gs-stat-l">District Hub</span></div>
</div>

{{-- ── Content ───────────────────────────────────────────────────────────────── --}}
<div class="gs-wrap">
    <div class="gs-breadcrumb"><a href="/">Home</a><span>›</span><span>Public Services</span></div>

    <div class="gs-section-head">
        <h2 class="gs-section-title">Public Services Directory</h2>
        <p class="gs-section-sub">All essential government and public services available to Kegalle district residents.</p>
    </div>

@php
$govSvgMap = [
    'ds-office'            => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/>',
    'municipal-council'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>',
    'police'               => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>',
    'hospitals'            => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    'private-hospitals'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>',
    'ambulance'            => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>',
    'fire-rescue'          => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z"/>',
    'disaster-management'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>',
    'schools'              => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>',
    'public-services'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>',
    'courts'               => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.031.352 5.988 5.988 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 01-2.031.352 5.989 5.989 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971z"/>',
    'road-transport'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>',
    'medical-laboratories' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/>',
    'public-ground'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5"/>',
    'banks'                => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>',
];

$gsAccents = [
    'ds-office'           => ['#1a237e','#283593'],
    'municipal-council'   => ['#1565c0','#1976d2'],
    'police'              => ['#0d47a1','#1565c0'],
    'hospitals'           => ['#b71c1c','#c62828'],
    'private-hospitals'   => ['#880e4f','#ad1457'],
    'ambulance'           => ['#bf360c','#e64a19'],
    'fire-rescue'         => ['#e65100','#f57c00'],
    'disaster-management' => ['#4a148c','#6a1b9a'],
    'schools'             => ['#1b5e20','#2e7d32'],
    'public-services'     => ['#006064','#00838f'],
    'courts'              => ['#37474f','#546e7a'],
    'road-transport'      => ['#33691e','#558b2f'],
    'medical-laboratories'=> ['#4527a0','#512da8'],
    'public-ground'       => ['#2e7d32','#388e3c'],
    'banks'               => ['#0d47a1','#1565c0'],
];

$placeholders = [
    ['slug'=>'ds-office','title'=>'DS Office','desc'=>'Divisional Secretariat — civil registration, permits, welfare services.'],
    ['slug'=>'municipal-council','title'=>'Municipal Council','desc'=>'Urban council services, local permits and civic administration.'],
    ['slug'=>'police','title'=>'Police Station','desc'=>'Local law enforcement, crime reporting and community safety.'],
    ['slug'=>'hospitals','title'=>'Government Hospital','desc'=>'Kegalle General Hospital — emergency, outpatient and inpatient care.'],
    ['slug'=>'private-hospitals','title'=>'Private Hospitals','desc'=>'Specialist care and private medical facilities in Kegalle.'],
    ['slug'=>'ambulance','title'=>'Ambulance Service','desc'=>'Emergency medical transport — dial 1990 for Suwa Seriya.'],
    ['slug'=>'fire-rescue','title'=>'Fire & Rescue','desc'=>'Fire suppression, search and rescue — dial 110 in emergency.'],
    ['slug'=>'disaster-management','title'=>'Disaster Management','desc'=>'Flood, landslide and disaster preparedness and response.'],
];
@endphp

    <div class="gs-grid">
    @forelse($services as $service)
        @php
            $c = $gsAccents[$service->slug] ?? ['#1a237e','#283593'];
            $grad = 'linear-gradient(135deg,'.$c[0].','.$c[1].')';
        @endphp
        <a href="/public-services/{{ $service->slug }}" class="gs-card">
            <div class="gs-card-icon" style="background:{{ $grad }}">
                @if($service->image)
                    <img src="{{ asset('storage/'.$service->image) }}" alt="{{ $service->title }}" style="width:48px;height:48px;object-fit:contain;filter:brightness(0) invert(1)">
                @elseif(isset($govSvgMap[$service->slug]))
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.95)" stroke-width="1.6" aria-hidden="true">{!! $govSvgMap[$service->slug] !!}</svg>
                @endif
            </div>
            <div class="gs-card-body">
                <h3 class="gs-card-title">{{ $service->title }}</h3>
                @if($service->description)<p class="gs-card-desc">{{ Str::limit($service->description, 100) }}</p>@endif
            </div>
            <div class="gs-card-footer">
                <span class="gs-card-cta">View Details <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg></span>
            </div>
        </a>
    @empty
        @foreach($placeholders as $p)
        @php
            $c = $gsAccents[$p['slug']] ?? ['#1a237e','#283593'];
            $grad = 'linear-gradient(135deg,'.$c[0].','.$c[1].')';
        @endphp
        <div class="gs-card gs-card-placeholder">
            <div class="gs-card-icon" style="background:{{ $grad }}">
                @if(isset($govSvgMap[$p['slug']]))
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.95)" stroke-width="1.6" aria-hidden="true">{!! $govSvgMap[$p['slug']] !!}</svg>
                @endif
            </div>
            <div class="gs-card-body">
                <h3 class="gs-card-title">{{ $p['title'] }}</h3>
                <p class="gs-card-desc">{{ $p['desc'] }}</p>
            </div>
            <div class="gs-card-footer">
                <span class="gs-card-coming">Coming soon</span>
            </div>
        </div>
        @endforeach
    @endforelse
    </div>

    {{-- Emergency contacts strip --}}
    <div class="gs-emergency">
        <h3 class="gs-emergency-title">Emergency Numbers</h3>
        <div class="gs-emergency-grid">
            <a href="tel:119" class="gs-emergency-item">
                <span class="gs-emergency-num">119</span>
                <span class="gs-emergency-label">Police</span>
            </a>
            <a href="tel:110" class="gs-emergency-item">
                <span class="gs-emergency-num">110</span>
                <span class="gs-emergency-label">Fire & Rescue</span>
            </a>
            <a href="tel:1990" class="gs-emergency-item">
                <span class="gs-emergency-num">1990</span>
                <span class="gs-emergency-label">Ambulance</span>
            </a>
            <a href="tel:117" class="gs-emergency-item">
                <span class="gs-emergency-num">117</span>
                <span class="gs-emergency-label">Disaster Mgmt</span>
            </a>
        </div>
    </div>

    {{-- CTA banner --}}
    <div class="gs-cta">
        <div class="gs-cta-inner">
            <div>
                <h3 class="gs-cta-title">Can't Find the Service You Need?</h3>
                <p class="gs-cta-sub">Our team can help you locate the right government office or contact in the Kegalle district.</p>
            </div>
            <a href="/contact" class="gs-cta-btn">Contact Us</a>
        </div>
    </div>
</div>

@endsection
