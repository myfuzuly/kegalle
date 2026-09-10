@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/kegalle-classified-premium.css') }}?v=5">
@endpush

@section('title','Classified Ads in Kegalle — Buy, Sell, Rent & Exchange · Kegalle Marketplace')
@section('meta_description','Post and browse free personal classified ads in Kegalle — buy, sell, rent or exchange items quickly with local buyers and sellers near you.')
@section('canonical', url('/classified'))

@section('content')

@php
    $palette = ['linear-gradient(135deg,#E3F2FD,#BBDEFB)','linear-gradient(135deg,#E8F5E9,#C8E6C9)','linear-gradient(135deg,#FFF3E0,#FFE0B2)','linear-gradient(135deg,#F3E5F5,#E1BEE7)','linear-gradient(135deg,#FFF8E1,#FFF176)','linear-gradient(135deg,#E0F2F1,#B2DFDB)'];
    $totalAll = $listings->total();
    $countFor = fn($type) => $classifiedCounts[$type] ?? 0;
    $newThisWeek = $classifiedNewThisWeek ?? 0;
@endphp

<div class="kfl-header k-page-header">
    <div class="k-page-header-inner">
        <div>
            @if(request('q'))
                <h1>Results for "{{ e(request('q')) }}"</h1>
                <p>{{ $listings->total() }} {{ Str::plural('result', $listings->total()) }} found in Kegalle district</p>
            @else
                <h1>Classified Ads in Kegalle</h1>
                <p>Post personal buy, sell, wanted or exchange ads quickly and for free.</p>
            @endif
        </div>
        <a href="{{ auth()->check() ? '/dashboard/listings/create?type=classified' : '/login?redirect=/dashboard/listings/create' }}" class="kfl-post-btn">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
            Post Classified Ad
        </a>
    </div>
    <div class="container">
        <div class="kfl-stats-row">
            <div class="kfl-stat">
                <span class="kfl-stat-n">{{ number_format($totalAll) }}</span>
                <span class="kfl-stat-l">Classified Ads</span>
            </div>
            <div class="kfl-stat">
                <span class="kfl-stat-n">{{ number_format($newThisWeek) }}+</span>
                <span class="kfl-stat-l">New This Week</span>
            </div>
            <div class="kfl-stat">
                <span class="kfl-stat-n">{{ number_format($totalCatCount) }}+</span>
                <span class="kfl-stat-l">Categories</span>
            </div>
            <div class="kfl-stat">
                <span class="kfl-stat-n">Free</span>
                <span class="kfl-stat-l">To Post</span>
            </div>
        </div>
    </div>
</div>

<!-- Category tabs -->
<div class="classified-tabs">
    <div class="classified-tabs-inner">
        <a href="/classified" class="classified-tab {{ !request('ad_type') ? 'active' : '' }}">All <span class="cnt">{{ number_format($totalAll) }}</span></a>
        @if($countFor('sale') > 0 || request('ad_type')==='sale')<a href="/classified?ad_type=sale" class="classified-tab {{ request('ad_type')==='sale' ? 'active' : '' }}">For Sale <span class="cnt">{{ number_format($countFor('sale')) }}</span></a>@endif
        @if($countFor('wanted') > 0 || request('ad_type')==='wanted')<a href="/classified?ad_type=wanted" class="classified-tab {{ request('ad_type')==='wanted' ? 'active' : '' }}">Wanted <span class="cnt">{{ number_format($countFor('wanted')) }}</span></a>@endif
        @if($countFor('rent') > 0 || request('ad_type')==='rent')<a href="/classified?ad_type=rent" class="classified-tab {{ request('ad_type')==='rent' ? 'active' : '' }}">For Rent <span class="cnt">{{ number_format($countFor('rent')) }}</span></a>@endif
        @if($countFor('exchange') > 0 || request('ad_type')==='exchange')<a href="/classified?ad_type=exchange" class="classified-tab {{ request('ad_type')==='exchange' ? 'active' : '' }}">Exchange <span class="cnt">{{ number_format($countFor('exchange')) }}</span></a>@endif
        @if($countFor('free') > 0 || request('ad_type')==='free')<a href="/classified?ad_type=free" class="classified-tab {{ request('ad_type')==='free' ? 'active' : '' }}">Free <span class="cnt">{{ number_format($countFor('free')) }}</span></a>@endif
    </div>
</div>

<div class="container k-content-section">
    <div class="k-layout-sidebar">
        <!-- Sidebar -->
        <div>
        <button class="k-filter-toggle-btn" id="k-filter-toggle" type="button">
            <span class="k-ftb-inner">
                <svg class="k-ftb-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M2 4h12M4 8h8M6 12h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                <span>Filter &amp; Sort</span>
            </span>
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M3 5l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <div class="k-filter-sidebar k-filter-collapsed" id="k-filter-sidebar">
            <div class="k-filter-header">
                <span class="k-filter-title"><svg class="k-filter-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>Filter</span>
                <a href="/classified" class="k-filter-clear" id="kClearFilters">Clear Filters</a>
            </div>
            <form method="GET" action="/classified" onsubmit="var btn=this.querySelector('.k-filter-submit');if(btn){btn.classList.add('k-loading');btn.disabled=true;}">

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
                            $_ct = ($_ch->listings_count ?? 0) + $_ch->children->sum('listings_count');
                            $_subs[] = ['slug' => $_ch->slug, 'name' => $_ch->name, 'count' => $_ct, 'leaves' => $_leaves];
                        }
                        $_tot = ($_c->listings_count ?? 0)
                            + $_c->children->sum('listings_count')
                            + $_c->children->sum(fn($_s) => $_s->children->sum('listings_count'));
                        $_catTree[] = ['slug' => $_c->slug, 'name' => $_c->name, 'count' => $_tot, 'subs' => $_subs];
                    }
                @endphp

                <div class="k-filter-section" id="kCatDrillWrap">
                    <h4>Category <a href="#" id="kCatResetAll" class="k-cat-reset-link">(All)</a> <span class="k-stoggle">−</span></h4>
                    {{-- Hidden checkboxes --}}
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
                    <h4>Ad Type <span class="k-stoggle">−</span></h4>
                    <div class="k-filter-option"><input type="radio" name="ad_type" value="" id="at-all" @checked(!request('ad_type'))><label for="at-all">All Types</label></div>
                    <div class="k-filter-option"><input type="radio" name="ad_type" value="sale" id="at-sale" @checked(request('ad_type')==='sale')><label for="at-sale">For Sale</label></div>
                    <div class="k-filter-option"><input type="radio" name="ad_type" value="wanted" id="at-wanted" @checked(request('ad_type')==='wanted')><label for="at-wanted">Wanted</label></div>
                    <div class="k-filter-option"><input type="radio" name="ad_type" value="rent" id="at-rent" @checked(request('ad_type')==='rent')><label for="at-rent">For Rent</label></div>
                    <div class="k-filter-option"><input type="radio" name="ad_type" value="exchange" id="at-exchange" @checked(request('ad_type')==='exchange')><label for="at-exchange">Exchange</label></div>
                    <div class="k-filter-option"><input type="radio" name="ad_type" value="free" id="at-free" @checked(request('ad_type')==='free')><label for="at-free">Free</label></div>
                </div>

                <div class="k-filter-section">
                    <h4>Location <span class="k-stoggle">−</span></h4>
                    @foreach(($locations ?? []) as $location)
                        <div class="k-filter-option">
                            <input type="checkbox" name="locations[]" value="{{ $location->id }}" id="cloc-{{ $location->id }}" @checked(in_array($location->id, request('locations', [])))>
                            <label for="cloc-{{ $location->id }}">📍 {{ $location->name }} <span class="cnt">{{ $location->listings_count ?? 0 }}</span></label>
                        </div>
                    @endforeach
                </div>

                <div class="k-filter-section">
                    <h4>Price Range <span class="k-stoggle">−</span></h4>
                    <div class="k-price-range-grid">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min (LKR)" class="k-form-control k-form-control-sm">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max (LKR)" class="k-form-control k-form-control-sm">
                    </div>
                </div>

                <button type="submit" class="k-filter-submit"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg> Apply Filter</button>
            </form>
        </div>
        </div>

        <!-- Ads list -->
        <div>
            <div class="k-listings-toolbar">
                <span class="k-listings-count">Showing <strong>{{ $listings->firstItem() ?? 0 }}–{{ $listings->lastItem() ?? 0 }}</strong> of <strong>{{ $listings->total() }} ads</strong></span>
                <form method="GET" id="kcSortForm" class="k-sort-row">
                    @foreach(request()->except('sort','page') as $k=>$v)
                        @if(is_array($v)) @foreach($v as $vv)<input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">@endforeach @else <input type="hidden" name="{{ $k }}" value="{{ $v }}"> @endif
                    @endforeach
                    <input type="hidden" name="sort" id="kcSortVal" value="{{ request('sort') }}">
                    <span class="k-sort-label">Sort:</span>
                    @php $kcSortLabel = request('sort')==='price_low' ? 'Price: Low → High' : (request('sort')==='price_high' ? 'Price: High → Low' : (request('sort')==='popular' ? 'Most Relevant' : 'Newest First')); @endphp
                    <div class="kpd-wrap">
                        <div class="kpd-trigger" id="kcSortTrigger">
                            <span id="kcSortLabel">{{ $kcSortLabel }}</span>
                            <svg class="kpd-chevron" width="11" height="7" viewBox="0 0 12 8" fill="none"><path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div class="kpd-panel" id="kcSortPanel" style="display:none">
                            <div class="kpd-list">
                                <div class="kpd-item {{ !request('sort') ? 'selected':'' }}" data-value="" data-label="Newest First"><span class="kpd-dot"></span>Newest First</div>
                                <div class="kpd-item {{ request('sort')==='price_low' ? 'selected':'' }}" data-value="price_low" data-label="Price: Low → High"><span class="kpd-dot"></span>Price: Low → High</div>
                                <div class="kpd-item {{ request('sort')==='price_high' ? 'selected':'' }}" data-value="price_high" data-label="Price: High → Low"><span class="kpd-dot"></span>Price: High → Low</div>
                                <div class="kpd-item {{ request('sort')==='popular' ? 'selected':'' }}" data-value="popular" data-label="Most Relevant"><span class="kpd-dot"></span>Most Relevant</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="k-classified-list">
                @forelse($listings as $listing)
                    @php
                        $img = optional($listing->images->first())->path ?? $listing->image ?? null;
                        $imgUrl = $img ? asset('storage/'.ltrim($img,'/')) : null;
                        $webpUrl = $img ? asset('storage/'.ltrim(\App\Helpers\ImageHelper::webpPath($img),'/')) : null;
                        $bg = $palette[$loop->index % count($palette)];
                        $location = optional($listing->locationModel)->name ?? $listing->location ?? 'Kegalle';
                        $isFree = ($listing->price ?? 0) == 0 && ($listing->ad_type ?? '') === 'free';
                        $isFavorited = isset($userFavoriteIds) && $userFavoriteIds->has($listing->id);
                        $tagClass = match($listing->ad_type ?? 'sale') {
                            'rent' => 'k-tag-rent',
                            'wanted' => 'k-tag-wanted',
                            'free' => 'k-tag-free',
                            default => 'k-tag-new',
                        };
                    @endphp
                    <div class="classified-list-card" data-href="/listings/{{ $listing->slug }}">
                        <div class="classified-list-img" style="{{ $imgUrl ? '' : 'background:'.$bg }}">
                            @if($imgUrl)
                                <img src="{{ $imgUrl }}" alt="{{ $listing->title }}" class="k-cover-img">
                            @else
                                {{ optional($listing->category)->icon ?: '🛍️' }}
                            @endif
                        </div>
                        <div class="classified-list-body">
                            <div class="k-tag-row">
                                <span class="k-tag {{ $tagClass }}">{{ ucfirst($listing->ad_type ?? 'sale') }}</span>
                                @if($listing->is_featured)<span class="k-tag k-tag-featured">Featured</span>@endif
                                @if($listing->is_urgent ?? false)<span class="classified-urgent">🔥 Urgent</span>@endif
                            </div>
                            <div class="classified-list-title">{{ $listing->title }}</div>
                            <div class="classified-list-desc">{{ \Illuminate\Support\Str::limit(strip_tags($listing->description ?? ''), 140) }}</div>
                            <div class="classified-list-meta">
                                <div class="classified-list-meta-item">📍 {{ $location }}</div>
                                <div class="classified-list-meta-item">🕐 {{ $listing->created_at?->diffForHumans() }}</div>
                                <div class="classified-list-meta-item">👁 {{ $listing->views ?? 0 }} views</div>
                                <div class="classified-list-meta-item">{{ optional($listing->category)->icon ?: '🏷️' }} {{ optional($listing->category)->name ?? 'General' }}</div>
                            </div>
                        </div>
                        <div class="classified-list-right">
                            <div>
                                @if($isFree)
                                    <div class="k-price k-price-lg k-text-green">FREE</div>
                                @elseif(($listing->ad_type ?? '') === 'wanted')
                                    <div class="k-price k-price-lg">Budget:</div>
                                    <div class="k-price-budget">{{ ($listing->price ?? 0) > 0 ? 'LKR '.number_format($listing->price).'+' : 'Negotiable' }}</div>
                                @else
                                    <div class="k-price k-price-lg">{{ ($listing->price ?? 0) > 0 ? 'LKR '.number_format($listing->price) : 'Contact' }}</div>
                                    @if(($listing->ad_type ?? '') === 'rent')<div class="k-price-suffix">/month</div>@endif
                                @endif
                            </div>
                            <div class="k-classified-actions">
                                <a href="/listings/{{ $listing->slug }}" class="k-btn k-btn-primary k-btn-sm">Contact</a>
                                <button class="js-favorite-btn k-fav-btn {{ $isFavorited ? 'is-favorited' : '' }}" data-listing-id="{{ $listing->id }}" data-authed="{{ auth()->check() ? '1' : '0' }}" aria-label="{{ $isFavorited ? 'Remove from favorites' : 'Save ad' }}">{{ $isFavorited ? '❤️' : '🤍' }}</button>
                            </div>
                        </div>
                    </div>
                @empty
                    @php $hasFilters = request()->hasAny(['categories','ad_type','locations','min_price','max_price','q']); @endphp
                    <div class="k-empty-state-box">
                        <div class="k-empty-state-emoji">{{ $hasFilters ? '🔍' : '📌' }}</div>
                        <h3 class="k-empty-state-heading">{{ $hasFilters ? 'No ads match your filters' : 'No Classified Ads Yet' }}</h3>
                        <p class="k-text-secondary">{{ $hasFilters ? 'Try adjusting or clearing your filters.' : 'Be the first to post a free classified ad and reach thousands of buyers in Kegalle!' }}</p>
                        @if($hasFilters)
                            <a href="/classified" class="k-btn k-btn-outline k-btn-mt">← Clear Filters</a>
                        @else
                            <a href="{{ auth()->check() ? '/dashboard/listings/create?type=classified' : '/register' }}" class="k-btn k-btn-primary k-btn-mt">+ Post Free Classified Ad</a>
                        @endif
                    </div>
                @endforelse
            </div>

            {{ $listings->links('vendor.pagination.k-theme') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
// ── Collapsible filter sections ──────────────────────────────
(function(){
    var autoCollapse = ['Location','Price Range'];
    document.querySelectorAll('.k-filter-section').forEach(function(sec){
        var h4 = sec.querySelector('h4');
        if(!h4) return;
        var body = document.createElement('div');
        body.className = 'k-fsec-body';
        var node = h4.nextSibling;
        while(node){ var next=node.nextSibling; body.appendChild(node); node=next; }
        sec.appendChild(body);
        var hasActive = !![].slice.call(body.querySelectorAll('input')).find(function(el){
            if(el.type==='radio') return el.checked && el.value!=='' && el.value!=='0';
            if(el.type==='checkbox') return el.checked;
            return false;
        });
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

// ── Category drill-down ──────────────────────────────────────
(function(){
    var tree = window._kCatTree || [];
    var selected = (window._kCatSelected || []).slice();
    var ui = document.getElementById('kCatDrillUI');
    var form = document.querySelector('#k-filter-sidebar form');
    if(!ui || !form) return;

    function esc(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    function clearCatInputs(){
        document.querySelectorAll('#kCatDrillWrap input[type="checkbox"]').forEach(function(cb){ cb.checked=false; });
    }
    function selectSlug(slug){
        clearCatInputs();
        var cb = document.getElementById('hcat-'+slug);
        if(cb) cb.checked = true;
    }
    function triggerFilter(){
        form.dispatchEvent(new Event('submit',{bubbles:true,cancelable:true}));
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
            html+='<div class="kcd-main-item'+(isSel?' kcd-selected':'')+'" data-slug="'+esc(m.slug)+'">'
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
    }

    function renderSub(mainObj){
        var html='<div class="kcd-back" id="kcdBack">‹ All Categories</div>'
            +'<div class="kcd-all-item" data-slug="'+esc(mainObj.slug)+'">'
            +'<span class="kcd-name">All '+esc(mainObj.name)+'</span>'
            +'<span class="kcd-count">'+mainObj.count+'</span></div>';
        mainObj.subs.forEach(function(s){
            var isSel=selected.indexOf(s.slug)>-1;
            html+='<div class="kcd-sub-item'+(isSel?' kcd-selected':'')+'" data-slug="'+esc(s.slug)+'">'
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
    }

    function renderLeaf(mainObj,subObj){
        var html='<div class="kcd-back" id="kcdBack">‹ '+esc(mainObj.name)+'</div>'
            +'<div class="kcd-all-item" data-slug="'+esc(subObj.slug)+'">'
            +'<span class="kcd-name">All '+esc(subObj.name)+'</span>'
            +'<span class="kcd-count">'+subObj.count+'</span></div>';
        subObj.leaves.forEach(function(lf){
            var isSel=selected.indexOf(lf.slug)>-1;
            html+='<div class="kcd-leaf-item'+(isSel?' kcd-selected':'')+'" data-slug="'+esc(lf.slug)+'">'
                +'<span class="kcd-leaf-dot"></span>'
                +'<span class="kcd-name">'+esc(lf.name)+'</span>'
                +'<span class="kcd-count">'+lf.count+'</span></div>';
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
    }

    var ctx=findContext();
    if(ctx){ ctx.sub ? renderLeaf(ctx.main,ctx.sub) : renderSub(ctx.main); }
    else { renderMain(); }

    var resetBtn=document.getElementById('kCatResetAll');
    if(resetBtn){ resetBtn.addEventListener('click',function(e){ e.preventDefault(); selected=[]; clearCatInputs(); renderMain(); triggerFilter(); }); }
})();

// ── Mobile filter toggle ─────────────────────────────────────
(function(){
    var togBtn = document.getElementById('k-filter-toggle');
    var sidebar = document.getElementById('k-filter-sidebar');
    if(!togBtn || !sidebar) return;
    togBtn.addEventListener('click', function(){
        var open = sidebar.classList.toggle('k-filter-collapsed');
        togBtn.classList.toggle('open', !open);
    });
    if(window.innerWidth > 1100){
        sidebar.classList.remove('k-filter-collapsed');
        togBtn.classList.add('open');
    }
})();
// ── Classified card click ────────────────────────────────────
document.querySelectorAll('.classified-list-card[data-href]').forEach(function(card){
    card.style.cursor='pointer';
    card.addEventListener('click',function(e){
        if(e.target.closest('a,button')) return;
        window.location=card.dataset.href;
    });
});
// Premium sort dropdown
(function(){
    var trigger=document.getElementById('kcSortTrigger'),panel=document.getElementById('kcSortPanel'),valEl=document.getElementById('kcSortVal'),label=document.getElementById('kcSortLabel'),form=document.getElementById('kcSortForm');
    if(!trigger) return;
    var open=false;
    trigger.addEventListener('click',function(e){e.stopPropagation();open=!open;panel.classList.toggle('open',open);trigger.classList.toggle('open',open);});
    panel.addEventListener('click',function(e){
        var item=e.target.closest('.kpd-item');if(!item)return;
        valEl.value=item.dataset.value; label.textContent=item.dataset.label;
        panel.querySelectorAll('.kpd-item').forEach(function(i){i.classList.remove('selected');i.querySelector('.kpd-dot').style.opacity='0';});
        item.classList.add('selected');item.querySelector('.kpd-dot').style.opacity='1';
        open=false;panel.classList.remove('open');trigger.classList.remove('open');
        form.submit();
    });
    document.addEventListener('click',function(e){if(open&&!trigger.closest('.kpd-wrap').contains(e.target)){open=false;panel.classList.remove('open');trigger.classList.remove('open');}});
})();
</script>
@endpush
