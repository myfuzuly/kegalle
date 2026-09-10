@extends('layouts.app')

@section('title', request('q') ? '"'.e(request('q')).'" — Kegalle Marketplace' : 'All Ads — Browse Classifieds & Products in Kegalle · Kegalle Marketplace')
@section('meta_description','Browse all classified ads and products for sale in Kegalle and nearby towns. Filter by category, location and price to find exactly what you need.')
@php
    $canonicalParams = array_filter(request()->only(['q','categories','location','ad_type','sort','min_price','max_price','page']));
    $canonicalUrl = url('/listings') . (count($canonicalParams) ? '?' . http_build_query($canonicalParams) : '');
@endphp
@section('canonical', $canonicalUrl)

@push('styles')
@endpush

@section('content')
{{-- Hero Header --}}
<div class="kfl-header k-page-header">
    <div class="k-page-header-inner">
        <div>
            @if(request('q'))
                <h1>Results for "{{ e(request('q')) }}"</h1>
                <p>{{ $listings->total() }} {{ Str::plural('result', $listings->total()) }} found in Kegalle district</p>
            @else
                <h1>All Ads in Kegalle</h1>
                <p>Browse classifieds and products from trusted local sellers across the Kegalle district.</p>
            @endif
        </div>
        <a href="{{ auth()->check() ? '/dashboard/listings/create' : '/login?redirect=/dashboard/listings/create' }}" class="kfl-post-btn">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
            Post Free Ad
        </a>
    </div>
    <div class="container">
        <div class="kfl-stats-row">
            <div class="kfl-stat">
                <span class="kfl-stat-n">{{ number_format($listings->total()) }}</span>
                <span class="kfl-stat-l">Active Ads</span>
            </div>
            <div class="kfl-stat">
                <span class="kfl-stat-n">{{ number_format($totalCatCount) }}+</span>
                <span class="kfl-stat-l">Product Categories</span>
            </div>
            <div class="kfl-stat">
                <span class="kfl-stat-n">{{ isset($locations) ? $locations->count() : 11 }}</span>
                <span class="kfl-stat-l">Towns</span>
            </div>
            <div class="kfl-stat">
                <span class="kfl-stat-n">Free</span>
                <span class="kfl-stat-l">To Post</span>
            </div>
        </div>
    </div>
</div>

<div class="container k-content-section k-content-section--pt">
    @if(request('q'))
        <div class="kfl-search-chips">
            <a href="/listings" class="k-search-chip">✕ Clear search</a>
            @foreach($categories->take(5) as $cat)
                <a href="/listings?q={{ urlencode(request('q')) }}&categories[]={{ $cat->slug }}" class="k-search-chip">{{ $cat->icon ?? '🛒' }} {{ $cat->name }}</a>
            @endforeach
        </div>
    @endif
    <div class="k-listing-page-grid">
        <!-- Filter Sidebar -->
        <button class="k-filter-toggle-btn" id="k-filter-toggle" type="button">
            <span class="k-ftb-inner">
                <svg class="k-ftb-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M2 4h12M4 8h8M6 12h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                <span class="k-ftb-label">Filter &amp; Sort</span>
            </span>
            <svg class="k-ftb-chevron" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 5l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <div class="k-sort-pills" id="kSortPills" role="group" aria-label="Quick sort">
            <button class="k-sort-pill{{ request('sort','') === '' ? ' active' : '' }}" data-sort="" type="button">🕒 Newest</button>
            <button class="k-sort-pill{{ request('sort','') === 'price_low' ? ' active' : '' }}" data-sort="price_low" type="button">💰 Price ↑</button>
            <button class="k-sort-pill{{ request('sort','') === 'price_high' ? ' active' : '' }}" data-sort="price_high" type="button">💎 Price ↓</button>
            <button class="k-sort-pill{{ request('sort','') === 'popular' ? ' active' : '' }}" data-sort="popular" type="button">🔥 Popular</button>
        </div>
        <div class="k-filter-sidebar k-filter-collapsed" id="k-filter-sidebar">
            <div class="k-filter-header">
                <span class="k-filter-title">
                    <svg class="k-filter-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>Filter
                </span>
                <a href="/listings{{ request('q') ? '?q='.urlencode(request('q')) : '' }}" class="k-filter-clear" id="kClearFilters">Clear Filters</a>
            </div>
            <form method="GET" action="/listings" id="kListingsForm">
                <div class="k-filter-section">
                    <h4>Type <span class="k-stoggle">−</span></h4>
                    <div class="k-filter-option"><input type="radio" name="type" value="" id="type-all" @checked(request('type')=='')><label for="type-all">All Types</label></div>
                    <div class="k-filter-option"><input type="radio" name="type" value="product" id="type-product" @checked(request('type')=='product')><label for="type-product">Product</label></div>
                    <div class="k-filter-option"><input type="radio" name="type" value="classified" id="type-classified" @checked(request('type')=='classified')><label for="type-classified">Classified</label></div>
                </div>

                <div class="k-filter-section" id="kCatDrillWrap">
                    <h4>Category <a href="#" id="kCatResetAll" class="k-cat-reset-link">(All)</a> <span class="k-stoggle">−</span></h4>
                    @php
                        $selectedCats = request('categories', []);
                        $_catTree = [];
                        foreach ($categories as $_c) {
                            $_subs = [];
                            foreach ($_c->children as $_ch) {
                                $_leaves = [];
                                foreach ($_ch->children as $_lf) {
                                    $_leaves[] = ['slug' => $_lf->slug, 'name' => $_lf->name, 'count' => $_lf->listings_count ?? 0];
                                }
                                $_ct = $_ch->listings_count ?? 0;
                                $_subs[] = ['slug' => $_ch->slug, 'name' => $_ch->name, 'count' => $_ct, 'leaves' => $_leaves];
                            }
                            $_tot = $_c->listings_count ?? 0;
                            $_catTree[] = ['slug' => $_c->slug, 'name' => $_c->name, 'count' => $_tot, 'subs' => $_subs];
                        }
                    @endphp
                    {{-- Hidden checkboxes used by buildParams() for form submission --}}
                    @foreach($categories as $category)
                        <input type="checkbox" name="categories[]" value="{{ $category->slug }}" id="hcat-{{ $category->slug }}" class="k-hidden" @checked(in_array($category->slug, $selectedCats))>
                        @foreach($category->children as $child)
                            <input type="checkbox" name="categories[]" value="{{ $child->slug }}" id="hcat-{{ $child->slug }}" class="k-hidden" @checked(in_array($child->slug, $selectedCats))>
                            @foreach($child->children as $leaf)
                                <input type="checkbox" name="categories[]" value="{{ $leaf->slug }}" id="hcat-{{ $leaf->slug }}" class="k-hidden" @checked(in_array($leaf->slug, $selectedCats))>
                            @endforeach
                        @endforeach
                    @endforeach
                    <div id="kCatDrillUI"></div>
                    <script nonce="{{ $cspNonce ?? '' }}">window._kCatTree=@json($_catTree);window._kCatSelected=@json($selectedCats);</script>
                </div>

                <div class="k-filter-section">
                    <h4>Location <span class="k-stoggle">−</span></h4>
                    @foreach(($locations ?? []) as $location)
                        <div class="k-filter-option">
                            <input type="checkbox" name="locations[]" value="{{ $location->id }}" id="loc-{{ $location->id }}" @checked(in_array($location->id, request('locations', [])))>
                            <label for="loc-{{ $location->id }}">📍 {{ $location->name }} <span class="cnt">{{ $location->listings_count ?? 0 }}</span></label>
                        </div>
                    @endforeach
                </div>

                <div class="k-filter-section k-nearme-section">
                    <h4>Near Me <span class="k-stoggle">−</span></h4>
                    <button type="button" id="kNearMeBtn" class="k-nearme-btn">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Use my location
                    </button>
                    <p id="kNearMeMsg" class="k-nearme-msg" style="display:none"></p>
                    <script nonce="{{ $cspNonce ?? '' }}">
                    (function(){
                    var TOWNS=[
                        {name:'Kegalle',id:'kegalle',lat:7.2513,lng:80.3464},
                        {name:'Mawanella',id:'mawanella',lat:7.2544,lng:80.4567},
                        {name:'Warakapola',id:'warakapola',lat:7.2530,lng:80.1319},
                        {name:'Rambukkana',id:'rambukkana',lat:7.3274,lng:80.3900},
                        {name:'Ruwanwella',id:'ruwanwella',lat:7.0489,lng:80.2564},
                        {name:'Yatiyantota',id:'yatiyantota',lat:6.9517,lng:80.2336},
                        {name:'Deraniyagala',id:'deraniyagala',lat:6.9228,lng:80.3359},
                        {name:'Bulathkohupitiya',id:'bulathkohupitiya',lat:7.0453,lng:80.5019},
                        {name:'Galigamuwa',id:'galigamuwa',lat:7.1714,lng:80.1567},
                        {name:'Aranayake',id:'aranayake',lat:7.3472,lng:80.2753},
                        {name:'Ambuluwawa',id:'ambuluwawa',lat:7.2625,lng:80.5381},
                        {name:'Dehiowita',id:'dehiowita',lat:6.8650,lng:80.2300}
                    ];
                    function dist(la1,lo1,la2,lo2){
                        var R=6371,dLa=(la2-la1)*Math.PI/180,dLo=(lo2-lo1)*Math.PI/180;
                        var a=Math.sin(dLa/2)*Math.sin(dLa/2)+Math.cos(la1*Math.PI/180)*Math.cos(la2*Math.PI/180)*Math.sin(dLo/2)*Math.sin(dLo/2);
                        return R*2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a));
                    }
                    document.getElementById('kNearMeBtn').addEventListener('click',function(){
                        var btn=this,msg=document.getElementById('kNearMeMsg');
                        if(!navigator.geolocation){msg.style.display='block';msg.textContent='Geolocation not supported.';return;}
                        btn.disabled=true;btn.textContent='Locating…';
                        navigator.geolocation.getCurrentPosition(function(pos){
                            var lat=pos.coords.latitude,lng=pos.coords.longitude;
                            var nearby=TOWNS.filter(function(t){return dist(lat,lng,t.lat,t.lng)<15;}).slice(0,3);
                            if(!nearby.length){nearby=[TOWNS.reduce(function(a,b){return dist(lat,lng,a.lat,a.lng)<dist(lat,lng,b.lat,b.lng)?a:b;})]; }
                            // Uncheck all location checkboxes
                            document.querySelectorAll('input[name="locations[]"]').forEach(function(cb){cb.checked=false;});
                            var matched=[];
                            nearby.forEach(function(town){
                                document.querySelectorAll('input[name="locations[]"]').forEach(function(cb){
                                    var label=cb.nextElementSibling?cb.nextElementSibling.textContent.toLowerCase():'';
                                    if(label.indexOf(town.name.toLowerCase())!==-1){cb.checked=true;matched.push(town.name);}
                                });
                            });
                            msg.style.display='block';
                            msg.textContent=matched.length?'Showing ads near: '+matched.join(', '):'No matching town found nearby. Try manual location filter.';
                            btn.textContent='Location set';
                            if(matched.length)document.querySelector('.k-filter-submit').click();
                        },function(){
                            msg.style.display='block';msg.textContent='Location access denied.';btn.disabled=false;btn.textContent='Use my location';
                        },{timeout:8000});
                    });
                    })();
                    </script>
                </div>

                <div class="k-filter-section">
                    <h4>Price Range <span class="k-stoggle">−</span></h4>
                    @php
                        $_maxP    = $maxListingPrice ?? 500000;
                        $_curMin  = (int) request('min_price', 0);
                        $_curMax  = (int) request('max_price', $_maxP);
                        if($_curMax > $_maxP || $_curMax === 0) $_curMax = $_maxP;
                    @endphp
                    <div class="k-pr-presets" id="kPrPresets">
                        <button type="button" class="k-pr-preset" data-min="0"       data-max="5000"   >Below 5K</button>
                        <button type="button" class="k-pr-preset" data-min="5000"    data-max="10000"  >5K – 10K</button>
                        <button type="button" class="k-pr-preset" data-min="10000"   data-max="25000"  >10K – 25K</button>
                        <button type="button" class="k-pr-preset" data-min="25000"   data-max="50000"  >25K – 50K</button>
                        <button type="button" class="k-pr-preset" data-min="50000"   data-max="100000" >50K – 100K</button>
                        <button type="button" class="k-pr-preset" data-min="100000"  data-max="250000" >100K – 250K</button>
                        <button type="button" class="k-pr-preset" data-min="250000"  data-max="500000" >250K – 500K</button>
                        <button type="button" class="k-pr-preset" data-min="500000"  data-max="1000000">500K – 1M</button>
                        <button type="button" class="k-pr-preset" data-min="1000000" data-max="0"      >Above 1M</button>
                    </div>
                    <div class="k-pr-vals">
                        <span class="k-pr-val-box" id="kPrMin">LKR {{ number_format($_curMin) }}</span>
                        <span class="k-pr-dash">—</span>
                        <span class="k-pr-val-box" id="kPrMax">{{ $_curMax >= $_maxP ? 'Any' : 'LKR '.number_format($_curMax) }}</span>
                    </div>
                    <div class="k-pr-wrap"
                         id="kPrWrap"
                         data-max="{{ $_maxP }}"
                         data-vmin="{{ $_curMin }}"
                         data-vmax="{{ $_curMax }}">
                        <div class="k-pr-track">
                            <div class="k-pr-fill" id="kPrFill"></div>
                        </div>
                        <div class="k-pr-handle" id="kPrHMin" tabindex="0" role="slider" aria-label="Minimum price" aria-valuemin="0" aria-valuemax="{{ $_maxP }}" aria-valuenow="{{ $_curMin }}"></div>
                        <div class="k-pr-handle" id="kPrHMax" tabindex="0" role="slider" aria-label="Maximum price" aria-valuemin="0" aria-valuemax="{{ $_maxP }}" aria-valuenow="{{ $_curMax }}"></div>
                    </div>
                    <input type="hidden" name="min_price" id="kPrInputMin" value="{{ $_curMin > 0 ? $_curMin : '' }}">
                    <input type="hidden" name="max_price" id="kPrInputMax" value="{{ $_curMax < $_maxP ? $_curMax : '' }}">
                </div>

                <div class="k-filter-section">
                    <h4>Condition <span class="k-stoggle">−</span></h4>
                    <div class="k-filter-option"><input type="radio" name="condition" value="" id="cond-all" @checked(!request('condition'))><label for="cond-all">Any</label></div>
                    <div class="k-filter-option"><input type="radio" name="condition" value="new" id="cond-new" @checked(request('condition')=='new')><label for="cond-new">✨ Brand New</label></div>
                    <div class="k-filter-option"><input type="radio" name="condition" value="used" id="cond-used" @checked(request('condition')=='used')><label for="cond-used">🔄 Used</label></div>
                </div>

                <div class="k-filter-section">
                    <h4>Date Posted <span class="k-stoggle">−</span></h4>
                    <div class="k-filter-option"><input type="radio" name="posted" value="" id="post-any" @checked(!request('posted'))><label for="post-any">Any time</label></div>
                    <div class="k-filter-option"><input type="radio" name="posted" value="today" id="post-today" @checked(request('posted')=='today')><label for="post-today">Today</label></div>
                    <div class="k-filter-option"><input type="radio" name="posted" value="week" id="post-week" @checked(request('posted')=='week')><label for="post-week">This week</label></div>
                    <div class="k-filter-option"><input type="radio" name="posted" value="month" id="post-month" @checked(request('posted')=='month')><label for="post-month">This month</label></div>
                </div>

                <button type="submit" class="k-filter-submit"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg> Apply Filter</button>
            </form>

            <div class="mt-20">
                @include('frontend.partials.ad-banner', ['location' => 'listings_sidebar', 'style' => 'box'])
            </div>
        </div>

        <!-- Listings Main -->
        <div>
            <!-- Recently Viewed -->
            <div id="kRecentlyViewedBar" class="k-rv-bar" style="display:none" aria-label="Recently viewed listings">
                <span class="k-rv-label" aria-hidden="true">Recently viewed</span>
                <div class="k-rv-list" role="list"></div>
            </div>
            <div class="k-listings-toolbar">
                <div class="k-listings-count" id="k-results-count">Showing <strong>{{ $listings->firstItem() ?? 0 }}–{{ $listings->lastItem() ?? 0 }}</strong> of <strong>{{ $listings->total() }} results</strong></div>
                @auth
                <button type="button" id="kSaveSearch" class="k-save-search-btn" title="Get email alerts when new matching ads are posted">🔔 Save Search</button>
                <form id="kSaveSearchForm" method="POST" action="/listings/save-search" style="display:none">
                    @csrf
                    <input type="hidden" name="params" id="kSaveSearchParams" value="{{ json_encode(request()->except('_token')) }}">
                </form>
                @endauth
                <div class="k-view-toggle" role="group" aria-label="View mode">
                    <button id="btnListView" class="active" onclick="setView('list')" title="List view">☰</button>
                    {{-- <button id="btnMapView" onclick="setView('map')" title="Map view">🗺</button> --}}
                </div>
                <div class="k-sort-row">
                    <span class="k-sort-label">Sort By:</span>
                    @php
                        $sortOpts = [];
                        if(request('q')) $sortOpts['relevance']='Best Match';
                        $sortOpts['']='Newest First';
                        $sortOpts['popular']='Most Popular';
                        $sortOpts['price_low']='Price: Low to High';
                        $sortOpts['price_high']='Price: High to Low';
                        $sortOpts['alpha']='A to Z';
                        $curSort = request('sort','');
                        $curSortLabel = $sortOpts[$curSort] ?? 'Newest First';
                    @endphp
                    <input type="hidden" id="k-sort-select" value="{{ $curSort }}">
                    <div class="kpd-wrap" id="klSortWrap">
                        <div class="kpd-trigger" id="klSortTrigger">
                            <span id="klSortLabel">{{ $curSortLabel }}</span>
                            <svg class="kpd-chevron" width="11" height="7" viewBox="0 0 12 8" fill="none"><path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div class="kpd-panel" id="klSortPanel" style="display:none">
                            <div class="kpd-list">
                                @foreach($sortOpts as $val=>$lbl)
                                <div class="kpd-item {{ $curSort===$val ? 'selected':'' }}" data-value="{{ $val }}" data-label="{{ $lbl }}">
                                    <span class="kpd-dot"></span>{{ $lbl }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <div id="k-map-container"></div> --}}

            <div id="k-listings-results">
                @include('frontend.listings.grid-partial', ['listings' => $listings])
            </div>
            <div class="k-ajax-spinner" id="k-spinner"></div>
        </div>
    </div>
</div>

<div class="container k-content-section--pb">
    <div class="kfl-cats-section">
        <div class="kfl-cats-header">
            <h2 class="kfl-cats-title">Browse by Category</h2>
            <a href="/listings" class="kfl-cats-link">All Categories →</a>
        </div>
        <div class="k-cats">
            @foreach($categories->take(8) as $category)
                @php
                    $catTotal = $category->listings_count ?? 0;
                @endphp
                <a href="/listings?categories[]={{ $category->slug }}" class="k-cat-pill">
                    <span class="icon">{{ $category->icon ?? '🛒' }}</span>
                    <span class="label">{{ $category->name }}</span>
                    <span class="count">{{ $catTotal }} {{ \Illuminate\Support\Str::plural('ad', $catTotal) }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
// ── Collapsible filter sections ───────────────────────────────
(function(){
    var autoCollapse = ['Location','Price Range','Condition','Date Posted'];
    document.querySelectorAll('.k-filter-section').forEach(function(sec){
        var h4 = sec.querySelector('h4');
        if(!h4) return;
        // Wrap all siblings after h4 into .k-fsec-body
        var body = document.createElement('div');
        body.className = 'k-fsec-body';
        var node = h4.nextSibling;
        while(node){ var next=node.nextSibling; body.appendChild(node); node=next; }
        sec.appendChild(body);
        // Check if section has an active filter value
        var hasActive = !![].slice.call(body.querySelectorAll('input')).find(function(el){
            if(el.type==='radio') return el.checked && el.value!=='' && el.value!=='0';
            if(el.type==='checkbox') return el.checked;
            if(el.type==='number') return el.value!=='';
            return false;
        });
        // Collapse if in auto-collapse list and no active filter
        var label = h4.textContent.trim();
        var isMobile = window.innerWidth <= 1100;
        var shouldCollapse = !hasActive && (isMobile || autoCollapse.some(function(t){ return label.indexOf(t)>-1; }));
        var tog = h4.querySelector('.k-stoggle');
        if(shouldCollapse){ sec.classList.add('k-sec-collapsed'); if(tog) tog.textContent='+'; }
        h4.addEventListener('click',function(e){
            if(e.target.tagName==='A') return;
            var collapsed = sec.classList.toggle('k-sec-collapsed');
            if(tog) tog.textContent = collapsed ? '+' : '−';
        });
    });
})();
// ── Category drill-down (ikman-style) ─────────────────────────
(function(){
    var tree = window._kCatTree || [];
    var selected = (window._kCatSelected || []).slice();
    var ui = document.getElementById('kCatDrillUI');
    var form = document.querySelector('.k-filter-sidebar form');
    if(!ui || !form) return;

    function esc(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    function kbAccess(container){
        container.querySelectorAll('.kcd-main-item,.kcd-sub-item,.kcd-leaf-item,.kcd-back,.kcd-all-item').forEach(function(el){
            if(!el.hasAttribute('tabindex')) el.setAttribute('tabindex','0');
            if(!el.getAttribute('role')) el.setAttribute('role','button');
            el.addEventListener('keydown',function(e){
                if(e.key==='Enter'||e.key===' '){e.preventDefault();el.click();}
            });
        });
    }

    function clearCatInputs(){
        document.querySelectorAll('#kCatDrillWrap input[type="checkbox"]').forEach(function(cb){ cb.checked = false; });
    }
    function selectSlug(slug){
        clearCatInputs();
        var cb = document.getElementById('hcat-'+slug);
        if(cb) cb.checked = true;
    }
    function triggerFilter(){
        form.dispatchEvent(new Event('submit', {bubbles:true, cancelable:true}));
    }

    function findContext(){
        for(var i=0;i<tree.length;i++){
            var m=tree[i];
            if(selected.indexOf(m.slug)>-1) return {main:m,sub:null};
            for(var j=0;j<m.subs.length;j++){
                var s=m.subs[j];
                if(selected.indexOf(s.slug)>-1) return {main:m,sub:s};
                for(var k=0;k<s.leaves.length;k++){
                    if(selected.indexOf(s.leaves[k].slug)>-1) return {main:m,sub:s};
                }
            }
        }
        return null;
    }

    function renderMain(){
        var html='';
        tree.forEach(function(m){
            var isSel=selected.indexOf(m.slug)>-1;
            html+='<div class="kcd-main-item'+(isSel?' kcd-selected':'')+'" data-slug="'+esc(m.slug)+'" data-has-subs="'+(m.subs.length?'1':'0')+'">'
                +'<span class="kcd-name">'+esc(m.name)+'</span>'
                +'<span class="kcd-count">'+m.count+'</span>'
                +(m.subs.length?'<span class="kcd-arrow">›</span>':'')
                +'</div>';
        });
        ui.innerHTML=html;
        ui.querySelectorAll('.kcd-main-item').forEach(function(el){
            el.addEventListener('click',function(){
                var mainObj=tree.filter(function(m){return m.slug===el.dataset.slug;})[0];
                if(!mainObj) return;
                if(mainObj.subs.length){ renderSub(mainObj); }
                else { selected=[mainObj.slug]; selectSlug(mainObj.slug); triggerFilter(); }
            });
        });
        kbAccess(ui);
    }

    function renderSub(mainObj){
        var html='<div class="kcd-back" id="kcdBack">‹ All Categories</div>'
            +'<div class="kcd-all-item" data-slug="'+esc(mainObj.slug)+'">'
            +'<span class="kcd-name">All '+esc(mainObj.name)+'</span>'
            +'<span class="kcd-count">'+mainObj.count+'</span>'
            +'</div>';
        mainObj.subs.forEach(function(s){
            var isSel=selected.indexOf(s.slug)>-1;
            html+='<div class="kcd-sub-item'+(isSel?' kcd-selected':'')+'" data-slug="'+esc(s.slug)+'" data-has-leaves="'+(s.leaves.length?'1':'0')+'">'
                +'<span class="kcd-sub-dot"></span>'
                +'<span class="kcd-name">'+esc(s.name)+'</span>'
                +'<span class="kcd-count">'+s.count+'</span>'
                +(s.leaves.length?'<span class="kcd-arrow">›</span>':'')
                +'</div>';
        });
        ui.innerHTML=html;
        document.getElementById('kcdBack').addEventListener('click',function(){
            selected=[]; clearCatInputs(); renderMain();
        });
        ui.querySelector('.kcd-all-item').addEventListener('click',function(){
            selected=[mainObj.slug]; selectSlug(mainObj.slug); triggerFilter();
        });
        ui.querySelectorAll('.kcd-sub-item').forEach(function(el){
            el.addEventListener('click',function(){
                var subObj=mainObj.subs.filter(function(s){return s.slug===el.dataset.slug;})[0];
                if(!subObj) return;
                if(subObj.leaves.length){ renderLeaf(mainObj,subObj); }
                else { selected=[subObj.slug]; selectSlug(subObj.slug); triggerFilter(); }
            });
        });
        kbAccess(ui);
    }

    function renderLeaf(mainObj,subObj){
        var html='<div class="kcd-back" id="kcdBack">‹ '+esc(mainObj.name)+'</div>'
            +'<div class="kcd-all-item" data-slug="'+esc(subObj.slug)+'">'
            +'<span class="kcd-name">All '+esc(subObj.name)+'</span>'
            +'<span class="kcd-count">'+subObj.count+'</span>'
            +'</div>';
        subObj.leaves.forEach(function(lf){
            var isSel=selected.indexOf(lf.slug)>-1;
            html+='<div class="kcd-leaf-item'+(isSel?' kcd-selected':'')+'" data-slug="'+esc(lf.slug)+'">'
                +'<span class="kcd-leaf-dot"></span>'
                +'<span class="kcd-name">'+esc(lf.name)+'</span>'
                +'<span class="kcd-count">'+lf.count+'</span>'
                +'</div>';
        });
        ui.innerHTML=html;
        document.getElementById('kcdBack').addEventListener('click',function(){
            selected=[]; clearCatInputs(); renderSub(mainObj);
        });
        ui.querySelector('.kcd-all-item').addEventListener('click',function(){
            selected=[subObj.slug]; selectSlug(subObj.slug); triggerFilter();
        });
        ui.querySelectorAll('.kcd-leaf-item').forEach(function(el){
            el.addEventListener('click',function(){
                selected=[el.dataset.slug]; selectSlug(el.dataset.slug);
                ui.querySelectorAll('.kcd-leaf-item').forEach(function(x){x.classList.remove('kcd-selected');});
                el.classList.add('kcd-selected');
                triggerFilter();
            });
        });
        kbAccess(ui);
    }

    var ctx=findContext();
    if(ctx){ ctx.sub ? renderLeaf(ctx.main,ctx.sub) : renderSub(ctx.main); }
    else { renderMain(); }

    var resetBtn = document.getElementById('kCatResetAll');
    if(resetBtn){ resetBtn.addEventListener('click', function(e){ e.preventDefault(); selected=[]; clearCatInputs(); renderMain(); triggerFilter(); }); }
})();
(function(){
    const form = document.querySelector('.k-filter-sidebar form');
    const results = document.getElementById('k-listings-results');
    const spinner = document.getElementById('k-spinner');
    const countEl = document.getElementById('k-results-count');
    const sortEl = document.getElementById('k-sort-select');
    let controller = null;

    function buildParams(){
        const fd = new FormData(form);
        const params = new URLSearchParams();
        for(const [k,v] of fd.entries()){ if(v) params.append(k,v); }
        if(sortEl.value) params.set('sort', sortEl.value);
        return params;
    }

    function skelHtml(){
        var s='<div class="k-grid-4 k-grid-tight">';
        for(var i=0;i<8;i++) s+='<div class="k-skel-card"><div class="k-skel-img"></div><div class="k-skel-body"><div class="k-skel-line k-skel-line-lg"></div><div class="k-skel-line k-skel-line-sm"></div><div class="k-skel-line k-skel-line-md"></div></div></div>';
        return s+'</div>';
    }

    function fetchListings(url){
        // Always stamp _ajax=1 so the proxy caches JSON and HTML separately
        if(url.indexOf('_ajax=1') === -1){
            url += (url.indexOf('?') > -1 ? '&' : '?') + '_ajax=1';
        }
        if(controller) controller.abort();
        controller = new AbortController();
        results.innerHTML = skelHtml();
        results.classList.add('k-ajax-loading');
        spinner.classList.remove('active');

        // Update save-search params
        var saveParamsEl = document.getElementById('kSaveSearchParams');
        if(saveParamsEl){
            var qStr = url.replace(/^[^?]*\??/,'').replace(/&?_ajax=1/,'');
            var obj={};
            qStr.split('&').forEach(function(p){ var kv=p.split('='); if(kv[0]) obj[decodeURIComponent(kv[0])]=decodeURIComponent(kv[1]||''); });
            saveParamsEl.value = JSON.stringify(obj);
        }

        fetch(url, {
            headers: {'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},
            signal: controller.signal
        })
        .then(r => r.json())
        .then(data => {
            results.innerHTML = data.html;
            countEl.innerHTML = 'Showing <strong>'+data.from+'–'+data.to+'</strong> of <strong>'+data.count+' results</strong>';
            results.classList.remove('k-ajax-loading');
            bindPagination();
            if (window.kMarkLoadedImages) { window.kMarkLoadedImages(); setTimeout(window.kMarkLoadedImages, 600); }
            // Scroll below fixed nav (approx 120px offset)
            var top = results.getBoundingClientRect().top + window.scrollY - 120;
            window.scrollTo({top: Math.max(0, top), behavior: 'smooth'});
            var cleanUrl = url.replace(/&?_ajax=1/,'');
        history.replaceState(null,'', cleanUrl);
        var q = new URLSearchParams(cleanUrl.split('?')[1]||'').get('q');
        document.title = (q ? '"'+q+'" — ' : '') + 'All Ads · Kegalle Marketplace';
        })
        .catch(e => {
            if(e.name !== 'AbortError'){
                results.classList.remove('k-ajax-loading');
                var retryUrl = url;
                results.innerHTML = '<div class="k-empty-state" style="padding:48px 0;text-align:center"><p style="color:var(--k-text-muted);margin-bottom:12px">Something went wrong loading results.</p><button onclick="fetchListings(\''+retryUrl.replace(/'/g,"\\'")+'\')" style="background:var(--k-primary);color:#fff;border:none;border-radius:8px;padding:10px 20px;font-size:14px;cursor:pointer;font-weight:600">Try Again</button></div>';
            }
        });
    }

    window.kApplyFilter = function(){ fetchListings('/listings?' + buildParams().toString()); };

    function applyFilter(e){
        if(e) e.preventDefault();
        const params = buildParams();
        const minP = parseFloat(params.get('min_price'));
        const maxP = parseFloat(params.get('max_price'));
        if(params.get('min_price') && params.get('max_price') && minP > maxP) {
            var priceErr = document.getElementById('k-price-error');
            if(!priceErr) {
                priceErr = document.createElement('p');
                priceErr.id = 'k-price-error';
                priceErr.style.cssText = 'color:#dc2626;font-size:12px;margin-top:4px';
                document.querySelector('.k-price-range').after(priceErr);
            }
            priceErr.textContent = 'Min price cannot exceed max price.';
            return;
        }
        fetchListings('/listings?' + params.toString());
    }

    function bindPagination(){
        results.querySelectorAll('.pagination a, .k-pagination a').forEach(function(a){
            a.addEventListener('click', function(e){
                e.preventDefault();
                var href = this.getAttribute('href');
                if(!href) return;
                var sep = href.indexOf('?') > -1 ? '&' : '?';
                fetchListings(href + sep + '_ajax=1');
            });
        });
    }

    form.addEventListener('submit', function(e){
        var btn = form.querySelector('.k-filter-submit');
        if(btn){ btn.classList.add('k-loading'); btn.disabled = true; }
        applyFilter(e);
    });

    form.querySelectorAll('input[type="radio"]').forEach(function(r){
        r.addEventListener('change', applyFilter);
    });

    // Premium sort dropdown
    (function(){
        var trigger=document.getElementById('klSortTrigger'),panel=document.getElementById('klSortPanel'),label=document.getElementById('klSortLabel');
        if(!trigger) return;
        var open=false;
        trigger.addEventListener('click',function(e){e.stopPropagation();open=!open;panel.classList.toggle('open',open);trigger.classList.toggle('open',open);});
        panel.addEventListener('click',function(e){
            var item=e.target.closest('.kpd-item');if(!item)return;
            var val=item.dataset.value,lbl=item.dataset.label;
            sortEl.value=val; label.textContent=lbl;
            panel.querySelectorAll('.kpd-item').forEach(function(i){i.classList.remove('selected');i.querySelector('.kpd-dot').style.opacity='0';});
            item.classList.add('selected');item.querySelector('.kpd-dot').style.opacity='1';
            // sync pills
            document.querySelectorAll('.k-sort-pill').forEach(function(p){ p.classList.toggle('active', p.dataset.sort===val); });
            open=false;panel.classList.remove('open');trigger.classList.remove('open');
            applyFilter();
        });
        document.addEventListener('click',function(e){if(open&&!trigger.closest('.kpd-wrap').contains(e.target)){open=false;panel.classList.remove('open');trigger.classList.remove('open');}});
    })();

    bindPagination();

    // Save Search
    var saveBtn = document.getElementById('kSaveSearch');
    var saveForm = document.getElementById('kSaveSearchForm');
    if(saveBtn && saveForm){
        saveBtn.addEventListener('click', function(){
            if(!confirm('Save this search? We\'ll email you when new matching ads are posted.')) return;
            saveForm.submit();
        });
    }

    // Sort pills — synced with dropdown label
    document.querySelectorAll('.k-sort-pill').forEach(function(pill){
        pill.addEventListener('click', function(){
            var val = this.dataset.sort;
            var lbl = this.textContent.trim();
            var sortEl = document.getElementById('k-sort-select');
            if(sortEl){ sortEl.value = val; }
            // sync dropdown label
            var lblEl = document.getElementById('klSortLabel');
            if(lblEl){ lblEl.textContent = lbl; }
            // sync dropdown selected state
            document.querySelectorAll('.kpd-item').forEach(function(i){
                var match = i.dataset.value === val;
                i.classList.toggle('selected', match);
                var dot = i.querySelector('.kpd-dot');
                if(dot) dot.style.opacity = match ? '1' : '0';
            });
            document.querySelectorAll('.k-sort-pill').forEach(function(p){ p.classList.remove('active'); });
            this.classList.add('active');
            if(window.kApplyFilter) window.kApplyFilter(); else applyFilter();
        });
    });

    // Mobile filter toggle / drawer
    var togBtn = document.getElementById('k-filter-toggle');
    var sidebar = document.getElementById('k-filter-sidebar');
    if(togBtn && sidebar){
        function openFilter(){
            sidebar.classList.add('k-filter-open');
            sidebar.classList.remove('k-filter-collapsed');
            togBtn.classList.add('open');
            togBtn.setAttribute('aria-expanded', 'true');
            if(window.innerWidth < 1100){
                document.body.classList.add('k-filter-active');
                document.body.style.overflow = 'hidden';
            }
        }
        function closeFilter(){
            sidebar.classList.remove('k-filter-open');
            sidebar.classList.add('k-filter-collapsed');
            togBtn.classList.remove('open');
            togBtn.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('k-filter-active');
            document.body.style.overflow = '';
        }
        togBtn.addEventListener('click', function(){
            sidebar.classList.contains('k-filter-open') ? closeFilter() : openFilter();
        });
        // Close on backdrop tap
        document.addEventListener('click', function(e){
            if(document.body.classList.contains('k-filter-active') && !sidebar.contains(e.target) && e.target !== togBtn && !togBtn.contains(e.target)){
                closeFilter();
            }
        });
        // Close on Escape
        document.addEventListener('keydown', function(e){
            if(e.key === 'Escape' && sidebar.classList.contains('k-filter-open')) closeFilter();
        });
        if(window.innerWidth > 1100){
            openFilter();
        }
    }
})();

function setView(mode){
    var listEl = document.getElementById('k-listings-results');
    var btnList = document.getElementById('btnListView');
    if(listEl) listEl.style.display = '';
    if(btnList) btnList.classList.add('active');
}

/* ---- Price range dual handle slider ---- */
(function(){
    var wrap   = document.getElementById('kPrWrap');
    if(!wrap) return;
    var maxV   = parseInt(wrap.dataset.max);
    var vMin   = parseInt(wrap.dataset.vmin);
    var vMax   = parseInt(wrap.dataset.vmax);
    var hMin   = document.getElementById('kPrHMin');
    var hMax   = document.getElementById('kPrHMax');
    var fill   = document.getElementById('kPrFill');
    var lblMin = document.getElementById('kPrMin');
    var lblMax = document.getElementById('kPrMax');
    var inpMin = document.getElementById('kPrInputMin');
    var inpMax = document.getElementById('kPrInputMax');

    function fmt(n){ return 'LKR '+n.toLocaleString(); }

    // Track range — changes when a preset is selected
    var trackMin = 0;
    var trackMax = Math.min(maxV, 500000); // default: 0 to 500K with sqrt scale
    var trackLinear = false; // preset mode uses linear; default uses sqrt

    function calcStep(lo, hi){
        var range = hi - lo;
        return Math.max(500, Math.round(range / 100 / 500) * 500);
    }
    var dispStep = calcStep(trackMin, trackMax);

    // Convert value → % position on track
    function valToPct(v){
        var ratio = (Math.min(v, trackMax) - trackMin) / (trackMax - trackMin);
        ratio = Math.max(0, Math.min(1, ratio));
        return (trackLinear ? ratio : Math.sqrt(ratio)) * 100;
    }
    // Convert % position → value
    function pctToVal(p){
        var ratio = trackLinear ? p/100 : Math.pow(p/100, 2);
        var v = ratio * (trackMax - trackMin) + trackMin;
        return Math.round(v / dispStep) * dispStep;
    }

    function render(){
        var minPct = valToPct(vMin);
        var maxPct = valToPct(vMax);
        hMin.style.left  = minPct+'%';
        hMax.style.left  = maxPct+'%';
        fill.style.left  = minPct+'%';
        fill.style.width = (maxPct - minPct)+'%';
        lblMin.textContent = fmt(vMin);
        lblMax.textContent = (vMax >= maxV) ? 'Any' : fmt(vMax);
        inpMin.value = vMin > 0    ? vMin : '';
        inpMax.value = vMax < maxV ? vMax : '';
        hMin.setAttribute('aria-valuenow', vMin);
        hMax.setAttribute('aria-valuenow', vMax);
        syncPresetActive();
    }

    function setTrack(lo, hi){
        trackMin  = lo;
        trackMax  = hi;
        trackLinear = true;
        dispStep  = calcStep(lo, hi);
    }
    function resetTrack(){
        trackMin  = 0;
        trackMax  = Math.min(maxV, 500000);
        trackLinear = false;
        dispStep  = calcStep(trackMin, trackMax);
    }

    function dragHandle(handle, isMin){
        var rect;
        function onMove(e){
            var cx = e.touches ? e.touches[0].clientX : e.clientX;
            var rawPct = Math.max(0, Math.min(100, (cx - rect.left) / rect.width * 100));
            var nv = Math.max(trackMin, Math.min(trackMax, pctToVal(rawPct)));
            if(isMin){ vMin = Math.min(nv, vMax - dispStep); }
            else      { vMax = Math.max(nv, vMin + dispStep); }
            render();
        }
        function onUp(){ document.removeEventListener('mousemove',onMove); document.removeEventListener('mouseup',onUp); document.removeEventListener('touchmove',onMove); document.removeEventListener('touchend',onUp); }
        handle.addEventListener('mousedown', function(e){ e.preventDefault(); rect=wrap.getBoundingClientRect(); document.addEventListener('mousemove',onMove); document.addEventListener('mouseup',onUp); });
        handle.addEventListener('touchstart', function(e){ rect=wrap.getBoundingClientRect(); document.addEventListener('touchmove',onMove,{passive:false}); document.addEventListener('touchend',onUp); },{passive:true});
        handle.addEventListener('keydown', function(e){
            var d = e.key==='ArrowRight'||e.key==='ArrowUp' ? dispStep : e.key==='ArrowLeft'||e.key==='ArrowDown' ? -dispStep : 0;
            if(!d) return; e.preventDefault();
            if(isMin){ vMin = Math.max(trackMin, Math.min(vMax-dispStep, vMin+d)); }
            else      { vMax = Math.min(trackMax, Math.max(vMin+dispStep, vMax+d)); }
            render();
        });
    }
    dragHandle(hMin, true);
    dragHandle(hMax, false);

    // Preset range buttons
    var presets = document.querySelectorAll('#kPrPresets .k-pr-preset');
    function syncPresetActive(){
        presets.forEach(function(btn){
            var pMin = parseInt(btn.dataset.min);
            var pMax = parseInt(btn.dataset.max) || maxV;
            btn.classList.toggle('active', trackLinear && trackMin===pMin && trackMax===pMax);
        });
    }
    presets.forEach(function(btn){
        btn.addEventListener('click', function(){
            var pMin = parseInt(btn.dataset.min);
            var pMax = parseInt(btn.dataset.max) || maxV;
            // Toggle off if already active
            if(trackLinear && trackMin===pMin && trackMax===pMax){
                resetTrack();
                vMin = 0; vMax = maxV;
            } else {
                setTrack(pMin, pMax);
                vMin = pMin;
                vMax = pMax;
            }
            render();
        });
    });

    render();
})();

/* ---- Filter active-count badges ---- */
(function(){
    var form = document.querySelector('#k-filter-sidebar form');
    if(!form) return;
    function updateBadges(){
        document.querySelectorAll('.k-filter-section').forEach(function(sec){
            var h4 = sec.querySelector('h4');
            if(!h4) return;
            var inputs = [].slice.call(sec.querySelectorAll('input'));
            var count = inputs.filter(function(el){
                if(el.type==='radio') return el.checked && el.value!=='' && el.value!=='0';
                if(el.type==='checkbox') return el.checked;
                if(el.type==='number') return el.value!=='';
                return false;
            }).length;
            var badge = h4.querySelector('.k-filter-badge');
            if(count>0){
                if(!badge){ badge=document.createElement('span'); badge.className='k-filter-badge'; h4.insertBefore(badge, h4.querySelector('.k-stoggle')); }
                badge.textContent = count;
            } else {
                if(badge) badge.remove();
            }
        });
    }
    form.addEventListener('change', updateBadges);
    updateBadges();
})();

/* ---- Recently Viewed on listings page ---- */
(function(){
    var key = 'k_recently_viewed';
    var wrap = document.getElementById('kRecentlyViewedBar');
    if(!wrap) return;
    var items;
    try { items = JSON.parse(localStorage.getItem(key)||'[]'); } catch(e){ items=[]; }
    if(!items.length){ wrap.style.display='none'; return; }
    var html = items.slice(0,6).map(function(item){
        if(!item||!item.url||typeof item.url!=='string'||item.url==='undefined'||!item.title||item.title==='undefined') return '';
        return '<a class="k-rv-chip" href="'+item.url+'"><img src="'+(item.img&&item.img!=='undefined'?item.img:'')+'" onerror="this.style.display=\'none\'"><span>'+item.title+'</span></a>';
    }).filter(Boolean).join('');
    wrap.querySelector('.k-rv-list').innerHTML = html;
    wrap.style.display = '';
})();
</script>
@endpush
