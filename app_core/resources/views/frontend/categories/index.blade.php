@extends('layouts.app')

@section('title','Browse All Categories · Kegalle Marketplace')
@section('meta_description','Explore listings by category in Kegalle — electronics, vehicles, property, fashion, jobs, services and more.')
@section('canonical', url('/categories'))

@section('content')
@php
    $totalAds = $parents->sum(function ($m) use ($subMap, $leafMap) {
        $sub = $subMap->get($m->id, collect());
        return $m->listings_count
             + $sub->sum(fn($s) => $s->listings_count + $leafMap->get($s->id, collect())->sum('listings_count'));
    });
    $mainCount = $parents->count();
    $subCount  = $subMap->sum(fn($subs) => $subs->count());
    $leafCount = $leafMap->sum(fn($leaves) => $leaves->count());
    $totalCount = $mainCount + $subCount + $leafCount;
@endphp

<div class="k-page-header">
    <div class="k-page-header-inner">
        <div>
            <h1>Browse All Categories</h1>
            <p>{{ number_format($totalCount) }}+ categories · {{ number_format($totalAds) }} active ads across Kegalle</p>
        </div>
        <form class="k-page-header-search" action="/listings" method="GET">
            <input type="text" name="q" placeholder="Search listings…">
            <button type="submit">Search</button>
        </form>
    </div>
</div>

{{-- ── Stats strip ──────────────────────────────────────────────────── --}}
<div class="kcat-stats-strip">
    <div class="kcat-stats-inner">
        <div class="kcat-stat">
            <div class="kcat-stat-icon kcat-stat-icon--green">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1b5e20" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
            </div>
            <div>
                <div class="kcat-stat-val">{{ number_format($totalCount) }}+</div>
                <div class="kcat-stat-lbl">Total Categories</div>
            </div>
        </div>
        <div class="kcat-stat">
            <div class="kcat-stat-icon kcat-stat-icon--blue">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            </div>
            <div>
                <div class="kcat-stat-val">{{ number_format($mainCount) }}</div>
                <div class="kcat-stat-lbl">Main Categories</div>
            </div>
        </div>
        <div class="kcat-stat">
            <div class="kcat-stat-icon kcat-stat-icon--orange">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="3" cy="6" r="1" fill="#ea580c" stroke="none"/><circle cx="3" cy="12" r="1" fill="#ea580c" stroke="none"/><circle cx="3" cy="18" r="1" fill="#ea580c" stroke="none"/></svg>
            </div>
            <div>
                <div class="kcat-stat-val">{{ number_format($subCount) }}</div>
                <div class="kcat-stat-lbl">Sub Categories</div>
            </div>
        </div>
        <div class="kcat-stat">
            <div class="kcat-stat-icon kcat-stat-icon--purple">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><circle cx="7" cy="7" r="1" fill="#7c3aed" stroke="none"/></svg>
            </div>
            <div>
                <div class="kcat-stat-val">{{ number_format($leafCount) }}</div>
                <div class="kcat-stat-lbl">Leaf Categories</div>
            </div>
        </div>
    </div>
</div>

<div class="container k-content-section">
    <div class="k-breadcrumb">
        <a href="/">Home</a><span class="sep">›</span><span class="current">All Categories</span>
    </div>

    {{-- ── Popular Categories ──────────────────────────────────────────────── --}}
    <div class="k-section" style="margin-bottom:32px">
        <div class="kcat-popular-head">
            <h2 class="kcat-popular-title">Popular Categories</h2>
            <a href="/listings" class="kcat-popular-link">View all listings →</a>
        </div>
        <div class="kcat-popular-grid">
            @foreach($topCats as $tc)
                @php
                    $sub = $subMap->get($tc->id, collect());
                    $tcAds = $tc->listings_count + $sub->sum(fn($s) => $s->listings_count + $leafMap->get($s->id, collect())->sum('listings_count'));
                @endphp
                <a href="/listings?categories[]={{ $tc->slug }}" class="kcat-pop-pill">
                    <div class="kcat-pop-icon">
                        @if(!empty($tc->image))
                            <img src="{{ asset('storage/'.$tc->image) }}" alt="{{ $tc->name }}" loading="lazy">
                        @else
                            <span>{{ $tc->icon ?: '🛒' }}</span>
                        @endif
                    </div>
                    <div class="kcat-pop-name">{{ $tc->name }}</div>
                    @if($tcAds > 0)
                        <div class="kcat-pop-count">{{ number_format($tcAds) }} ads</div>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    @include('frontend.partials.ad-banner', ['location' => 'category_page', 'style' => 'top'])

    {{-- ── All Categories ─────────────────────────────────────────────── --}}
    <div class="kcat-all-head">
        <div>
            <h2 class="kcat-all-title">All Categories</h2>
            <span class="kcat-all-meta">{{ $parents->count() }} main categories</span>
        </div>
        <div class="kcat-filter-wrap">
            <svg class="kcat-filter-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" id="kCatFilter" class="kcat-filter-input" placeholder="Filter categories…" autocomplete="off">
            <button type="button" id="kCatFilterClear" class="kcat-filter-clear" style="display:none">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
    </div>
    <div id="kCatNoResults" class="kcat-no-results" style="display:none">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <p>No categories match "<span id="kCatNoTerm"></span>"</p>
    </div>

    <div class="cat-grid" id="catGrid">
        @foreach($parents as $parent)
            @php
                $subs    = $subMap->get($parent->id, collect());
                $subAds  = $subs->sum(fn($s) => $s->listings_count + $leafMap->get($s->id, collect())->sum('listings_count'));
                $totalCatAds = $parent->listings_count + $subAds;
                $subNames = $subs->pluck('name')->implode(' ');
            @endphp
            <div class="cat-card" id="card-{{ $parent->id }}" data-name="{{ strtolower($parent->name) }}" data-subs="{{ strtolower($subNames) }}">

                <a href="/listings?categories[]={{ $parent->slug }}" class="cat-card-head">
                    <div class="cat-card-icon">
                        @if(!empty($parent->image))
                            <img src="{{ asset('storage/'.$parent->image) }}" alt="{{ $parent->name }}" loading="lazy">
                        @else
                            <span>{{ $parent->icon ?: '🛒' }}</span>
                        @endif
                    </div>
                    <div class="cat-card-info">
                        <h3>{{ $parent->name }}</h3>
                        <p>{{ number_format($totalCatAds) }} ads</p>
                    </div>
                    <svg class="cat-card-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                </a>

                @if($subs->count())
                <div class="cat-card-subs">
                    @php $visibleSubs = 5; $hasMoreSubs = $subs->count() > $visibleSubs; @endphp

                    @foreach($subs->take($visibleSubs) as $sub)
                        @php
                            $leaves    = $leafMap->get($sub->id, collect());
                            $subTotal  = $sub->listings_count + $leaves->sum('listings_count');
                        @endphp
                        <div class="cat-sub-item">
                            <div class="cat-sub-row cat-sub-toggle {{ $leaves->count() ? 'has-leaves' : '' }}"
                                 @if($leaves->count()) data-sub-id="{{ $sub->id }}" @endif>
                                <a href="/listings?categories[]={{ $sub->slug }}" class="cat-sub-name">
                                    {{ $sub->name }}
                                </a>
                                <div class="cat-sub-row-right">
                                    @if($subTotal > 0)<span class="cat-sub-badge">{{ $subTotal }}</span>@endif
                                    @if($leaves->count())
                                        <svg class="cat-leaf-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                                    @endif
                                </div>
                            </div>
                            @if($leaves->count())
                            <div class="cat-leaf-wrap" id="leaves-{{ $sub->id }}" style="display:none">
                                <div class="cat-leaf-tags">
                                    @foreach($leaves as $leaf)
                                        <a href="/listings?categories[]={{ $leaf->slug }}" class="cat-leaf-tag">
                                            {{ $leaf->name }}
                                            @if($leaf->listings_count > 0)<span>({{ $leaf->listings_count }})</span>@endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    @endforeach

                    @if($hasMoreSubs)
                        <div class="cat-sub-more-wrap" id="more-subs-{{ $parent->id }}" style="display:none">
                            @foreach($subs->slice($visibleSubs) as $sub)
                                @php
                                    $leaves   = $leafMap->get($sub->id, collect());
                                    $subTotal = $sub->listings_count + $leaves->sum('listings_count');
                                @endphp
                                <div class="cat-sub-item">
                                    <div class="cat-sub-row cat-sub-toggle {{ $leaves->count() ? 'has-leaves' : '' }}"
                                         @if($leaves->count()) data-sub-id="{{ $sub->id }}" @endif>
                                        <a href="/listings?categories[]={{ $sub->slug }}" class="cat-sub-name">
                                            {{ $sub->name }}
                                        </a>
                                        <div class="cat-sub-row-right">
                                            @if($subTotal > 0)<span class="cat-sub-badge">{{ $subTotal }}</span>@endif
                                            @if($leaves->count())
                                                <svg class="cat-leaf-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                                            @endif
                                        </div>
                                    </div>
                                    @if($leaves->count())
                                    <div class="cat-leaf-wrap" id="leaves-{{ $sub->id }}" style="display:none">
                                        <div class="cat-leaf-tags">
                                            @foreach($leaves as $leaf)
                                                <a href="/listings?categories[]={{ $leaf->slug }}" class="cat-leaf-tag">
                                                    {{ $leaf->name }}@if($leaf->listings_count > 0)<span>({{ $leaf->listings_count }})</span>@endif
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <button class="cat-show-more" data-parent-id="{{ $parent->id }}">
                            <span>+{{ $subs->count() - $visibleSubs }} more subcategories</span>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                    @endif
                </div>
                @endif

                <a href="/listings?categories[]={{ $parent->slug }}" class="cat-card-foot">
                    <span>View all {{ $parent->name }}</span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>
        @endforeach
    </div>
</div>

<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    // Category filter
    var input = document.getElementById('kCatFilter');
    var clear = document.getElementById('kCatFilterClear');
    var noRes = document.getElementById('kCatNoResults');
    var noTerm = document.getElementById('kCatNoTerm');
    var grid  = document.getElementById('catGrid');
    if (input && grid) {
        var cards = grid.querySelectorAll('.cat-card');
        input.addEventListener('input', function() {
            var q = this.value.trim().toLowerCase();
            clear.style.display = q ? 'flex' : 'none';
            var visible = 0;
            cards.forEach(function(c) {
                var match = !q || (c.dataset.name||'').indexOf(q) !== -1 || (c.dataset.subs||'').indexOf(q) !== -1;
                c.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            if (noRes) noRes.style.display = (q && visible === 0) ? 'flex' : 'none';
            if (noTerm) noTerm.textContent = this.value.trim();
        });
        if (clear) clear.addEventListener('click', function() {
            input.value = '';
            input.dispatchEvent(new Event('input'));
            input.focus();
        });
    }

    // Event delegation for leaf toggles and show-more buttons
    if (grid) grid.addEventListener('click', function(e) {
        // Leaf expand/collapse
        var toggle = e.target.closest('.cat-sub-toggle.has-leaves');
        if (toggle && !e.target.closest('.cat-sub-name')) {
            var subId = toggle.dataset.subId;
            var wrap = document.getElementById('leaves-' + subId);
            if (!wrap) return;
            var open = wrap.style.display !== 'none';
            wrap.style.display = open ? 'none' : 'block';
            toggle.classList.toggle('open', !open);
            return;
        }
        // Show more subcategories
        var moreBtn = e.target.closest('.cat-show-more');
        if (moreBtn) {
            var parentId = moreBtn.dataset.parentId;
            var el = document.getElementById('more-subs-' + parentId);
            if (!el) return;
            var isOpen = el.style.display !== 'none';
            el.style.display = isOpen ? 'none' : 'block';
            var count = el.querySelectorAll('.cat-sub-item').length;
            var span = moreBtn.querySelector('span');
            var svg  = moreBtn.querySelector('svg');
            if (span) span.textContent = isOpen ? '+' + count + ' more subcategories' : 'Show less';
            if (svg)  svg.style.transform = isOpen ? '' : 'rotate(180deg)';
        }
    });
})();
</script>
@endsection
