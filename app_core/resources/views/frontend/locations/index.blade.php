@extends('layouts.app')

@section('title','Browse Locations in Kegalle · Kegalle Marketplace')
@section('meta_description','Find listings near you by location in Kegalle district — browse towns and areas across Kegalle, Sri Lanka.')
@section('canonical', url('/locations'))

@section('content')

<div class="k-page-header">
    <div class="k-page-header-inner">
        <div>
            <h1>Browse by Location</h1>
            <p>{{ $parents->count() }} areas · {{ number_format($totalAds) }} active ads across Kegalle district</p>
        </div>
        <form class="k-page-header-search" action="/listings" method="GET">
            <input type="text" name="q" placeholder="Search listings…">
            <button type="submit">Search</button>
        </form>
    </div>
</div>

<div class="container k-content-section">
    <div class="k-breadcrumb">
        <a href="/">Home</a><span class="sep">›</span><span class="current">Locations</span>
    </div>

    <div class="kloc-head">
        <div>
            <h2 class="kloc-title">All Areas</h2>
            <span class="kloc-meta">{{ $parents->count() }} locations in Kegalle district</span>
        </div>
        <div class="kcat-filter-wrap">
            <svg class="kcat-filter-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" id="kLocFilter" class="kcat-filter-input" placeholder="Filter locations…" autocomplete="off">
            <button type="button" id="kLocFilterClear" class="kcat-filter-clear" style="display:none">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
    </div>

    <div id="kLocNoResults" class="kcat-no-results" style="display:none">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <p>No locations match "<span id="kLocNoTerm"></span>"</p>
    </div>

    <div class="kloc-grid" id="kLocGrid">
        @foreach($parents->sortByDesc('total_count') as $loc)
            @php $children = $childMap->get($loc->id, collect()); @endphp
            <div class="kloc-card" data-name="{{ strtolower($loc->name) }}" data-children="{{ $children->pluck('name')->implode(' ') }}">
                <a href="/listings?location={{ $loc->slug }}" class="kloc-card-head">
                    <div class="kloc-card-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div class="kloc-card-info">
                        <h3>{{ $loc->name }}</h3>
                        <p>{{ number_format($loc->total_count) }} ads</p>
                    </div>
                    <svg class="cat-card-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                </a>

                @if($children->count())
                <div class="kloc-children">
                    @foreach($children->sortByDesc('listings_count') as $child)
                        <a href="/listings?location={{ $child->slug }}" class="kloc-child-link">
                            <span>{{ $child->name }}</span>
                            @if($child->listings_count > 0)
                                <span class="cat-sub-badge">{{ $child->listings_count }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
                @endif

                <a href="/listings?location={{ $loc->slug }}" class="cat-card-foot">
                    <span>View all in {{ $loc->name }}</span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>
        @endforeach
    </div>

    @if($parents->isEmpty())
        <div class="kcat-no-results" style="display:flex;padding:64px 0">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <p>No locations found. <a href="/listings">Browse all listings</a></p>
        </div>
    @endif
</div>

<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var input = document.getElementById('kLocFilter');
    var clear = document.getElementById('kLocFilterClear');
    var noRes = document.getElementById('kLocNoResults');
    var noTerm = document.getElementById('kLocNoTerm');
    var grid  = document.getElementById('kLocGrid');
    if (!input || !grid) return;
    var cards = grid.querySelectorAll('.kloc-card');
    input.addEventListener('input', function() {
        var q = this.value.trim().toLowerCase();
        clear.style.display = q ? 'flex' : 'none';
        var visible = 0;
        cards.forEach(function(c) {
            var match = !q || (c.dataset.name||'').indexOf(q) !== -1 || (c.dataset.children||'').indexOf(q) !== -1;
            c.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        if (noRes) noRes.style.display = (q && visible === 0) ? 'flex' : 'none';
        if (noTerm) noTerm.textContent = this.value.trim();
    });
    clear.addEventListener('click', function() {
        input.value = '';
        input.dispatchEvent(new Event('input'));
        input.focus();
    });
})();
</script>
@endsection
