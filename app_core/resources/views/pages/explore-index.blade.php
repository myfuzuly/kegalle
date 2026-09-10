@extends('layouts.app')
@section('title','Explore Kegalle — Culture, Nature, History & More · Kegalle Marketplace')
@section('meta_description','Discover the best of Kegalle district — activities, tourist attractions, natural resources and historic places, all in one place.')
@section('content')

{{-- ── Hero ─────────────────────────────────────────────────────────────────── --}}
<div class="ex-hero">
    <div class="ex-hero-overlay"></div>
    <div class="ex-hero-inner">
        <div class="ex-hero-badge">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Kegalle District · Sri Lanka
        </div>
        <h1 class="ex-hero-title">Explore Kegalle</h1>
        <p class="ex-hero-sub">Discover culture, nature, history and hidden gems across the Kegalle district.</p>
        <div class="ex-hero-pills">
            <span class="ex-hero-pill">🐘 Pinnawala</span>
            <span class="ex-hero-pill">🌿 Kadugannawa</span>
            <span class="ex-hero-pill">🏛️ Historic Temples</span>
            <span class="ex-hero-pill">🌊 Rivers & Falls</span>
        </div>
    </div>
</div>

{{-- ── Stats bar ─────────────────────────────────────────────────────────────── --}}
<div class="ex-stats-bar">
    <div class="ex-stat"><span class="ex-stat-n">15+</span><span class="ex-stat-l">Attractions</span></div>
    <div class="ex-stat-div"></div>
    <div class="ex-stat"><span class="ex-stat-n">6</span><span class="ex-stat-l">Categories</span></div>
    <div class="ex-stat-div"></div>
    <div class="ex-stat"><span class="ex-stat-n">95km</span><span class="ex-stat-l">via A1 Road</span></div>
    <div class="ex-stat-div"></div>
    <div class="ex-stat"><span class="ex-stat-n">Free</span><span class="ex-stat-l">Explore Guide</span></div>
</div>

{{-- ── Content ───────────────────────────────────────────────────────────────── --}}
<div class="ex-wrap">
    <div class="ex-breadcrumb"><a href="/">Home</a><span>›</span><span>Explore Kegalle</span></div>

    <div class="ex-section-head">
        <h2 class="ex-section-title">What to Discover</h2>
        <p class="ex-section-sub">Everything worth seeing and doing in the Kegalle district.</p>
    </div>

    @php
    $placeholders = [
        ['icon'=>'🐘','title'=>'Wildlife & Nature','desc'=>'Pinnawala Elephant Orphanage, scenic forests and the Kelani River valley await nature lovers.','gradient'=>'linear-gradient(135deg,#1b5e20,#2e7d32)','items'=>['Pinnawala Elephant Orphanage','Kelani River','Sinharaja Fringe','Kithulgala Rafting']],
        ['icon'=>'🏛️','title'=>'Historic Places','desc'=>'Ancient temples, colonial-era landmarks and archaeological sites spanning centuries of history.','gradient'=>'linear-gradient(135deg,#4a148c,#7b1fa2)','items'=>['Aranayake Temple','Mawanella Mosque','Pinnawala Ruins','Colonial Bridges']],
        ['icon'=>'🌊','title'=>'Activities & Adventure','desc'=>'White-water rafting, trekking, cycling and outdoor adventures for thrill-seekers.','gradient'=>'linear-gradient(135deg,#01579b,#0277bd)','items'=>['Kithulgala Rafting','Jungle Trekking','Cycling Trails','Rock Climbing']],
        ['icon'=>'🎭','title'=>'Culture & Heritage','desc'=>'Traditional festivals, Kandyan arts, local crafts and the vibrant cultural life of Kegalle.','gradient'=>'linear-gradient(135deg,#bf360c,#d84315)','items'=>['Perahera Festivals','Kandyan Dance','Mask Carving','Weaving Traditions']],
        ['icon'=>'🍛','title'=>'Food & Dining','desc'=>'Authentic Sri Lankan cuisine, local markets and roadside gems that define Kegalle flavours.','gradient'=>'linear-gradient(135deg,#e65100,#f57c00)','items'=>['Local Rice & Curry','Kithul Treacle','Coconut Roti','Fruit Markets']],
        ['icon'=>'🏔️','title'=>'Scenic Viewpoints','desc'=>'Breathtaking vistas from Kadugannawa Pass, mountain lookouts and lush valley panoramas.','gradient'=>'linear-gradient(135deg,#004d40,#00695c)','items'=>['Kadugannawa Pass','Ambuluwawa Tower','Bible Rock','Utuwankanda']],
    ];
    @endphp

    <div class="ex-grid">
    @forelse($exploreItems as $explore)
        <a href="/explore/{{ $explore->slug }}" class="ex-card" style="text-decoration:none">
            <div class="ex-card-icon" style="background:{{ $explore->image ? 'center/cover no-repeat url(\''.asset('storage/'.$explore->image).'\')' : 'linear-gradient(135deg,'.($explore->gradient_start ?? '#1b5e20').','.($explore->gradient_end ?? '#2e7d32').')' }}">
                @unless($explore->image)<span class="ex-card-emoji">{{ $explore->icon }}</span>@endunless
            </div>
            <div class="ex-card-body">
                <h3 class="ex-card-title">{{ $explore->title }}</h3>
                @if($explore->items)
                    @php $pts = array_slice(array_filter(array_map('trim', preg_split('/[\n,]+/', $explore->items))), 0, 4); @endphp
                    @if(count($pts))
                        <ul class="ex-card-list">@foreach($pts as $pt)<li>{{ $pt }}</li>@endforeach</ul>
                    @else
                        <p class="ex-card-desc">{{ $explore->description }}</p>
                    @endif
                @else
                    <p class="ex-card-desc">{{ $explore->description }}</p>
                @endif
            </div>
            <div class="ex-card-footer">
                <span class="ex-card-cta">Explore <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg></span>
            </div>
        </a>
    @empty
        @foreach($placeholders as $p)
        <div class="ex-card ex-card-placeholder">
            <div class="ex-card-icon" style="background:{{ $p['gradient'] }}">
                <span class="ex-card-emoji">{{ $p['icon'] }}</span>
            </div>
            <div class="ex-card-body">
                <h3 class="ex-card-title">{{ $p['title'] }}</h3>
                <ul class="ex-card-list">@foreach($p['items'] as $it)<li>{{ $it }}</li>@endforeach</ul>
                <p class="ex-card-desc" style="margin-top:8px;font-size:13px">{{ $p['desc'] }}</p>
            </div>
            <div class="ex-card-footer">
                <span class="ex-card-coming">Coming soon</span>
            </div>
        </div>
        @endforeach
    @endforelse
    </div>

    {{-- CTA banner --}}
    <div class="ex-cta">
        <div class="ex-cta-inner">
            <div>
                <h3 class="ex-cta-title">Know a Hidden Gem?</h3>
                <p class="ex-cta-sub">Help locals and visitors discover the best of Kegalle — suggest a place, attraction or experience.</p>
            </div>
            <a href="/contact" class="ex-cta-btn">Suggest a Place</a>
        </div>
    </div>
</div>

@endsection
