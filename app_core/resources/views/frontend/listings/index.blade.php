@extends('layouts.app')

@section('title','All Ads — Browse Classifieds & Products in Kegalle · Kegalle Marketplace')
@section('meta_description','Browse all classified ads and products for sale in Kegalle and nearby towns. Filter by category, location and price to find exactly what you need.')
@section('canonical', url('/listings'))

@section('content')
<div class="container k-content-section">
    @if(request('q'))
        <div class="k-search-header">
            <h1>Search results for "<span class="k-search-query">{{ request('q') }}</span>"</h1>
            <p>{{ $listings->total() }} {{ Str::plural('result', $listings->total()) }} found in Kegalle district</p>
            <div class="k-search-suggestions">
                <a href="/listings" class="k-search-chip">✕ Clear search</a>
                @foreach($categories->take(5) as $cat)
                    <a href="/listings?q={{ urlencode(request('q')) }}&categories[]={{ $cat->slug }}" class="k-search-chip">{{ $cat->icon ?? '🛒' }} {{ $cat->name }}</a>
                @endforeach
            </div>
        </div>
    @else
        <h1 class="k-page-title">All Ads in Kegalle</h1>
        <p class="k-page-subtitle">Browse classifieds and products from trusted local sellers across the Kegalle district.</p>
    @endif
    <div class="k-listing-page-grid">
        <!-- Filter Sidebar -->
        <button class="k-filter-toggle-btn" id="k-filter-toggle" type="button">🔽 Filter & Sort</button>
        <div class="k-filter-sidebar k-filter-collapsed" id="k-filter-sidebar">
            <div class="k-filter-header">
                <span class="k-filter-title">🔽 Filter</span>
                <a href="/listings" class="k-filter-clear">Clear All</a>
            </div>
            <form method="GET" action="/listings">
                <div class="k-filter-section">
                    <h4>Type <span>−</span></h4>
                    <div class="k-filter-option"><input type="radio" name="type" value="" id="type-all" @checked(request('type')=='')><label for="type-all">All Types</label></div>
                    <div class="k-filter-option"><input type="radio" name="type" value="product" id="type-product" @checked(request('type')=='product')><label for="type-product">Product</label></div>
                    <div class="k-filter-option"><input type="radio" name="type" value="classified" id="type-classified" @checked(request('type')=='classified')><label for="type-classified">Classified</label></div>
                </div>

                <div class="k-filter-section">
                    <h4>Category <span>−</span></h4>
                    @php $selectedCats = request('categories', []); @endphp
                    @foreach($categories as $category)
                        @php
                            $childSlugs = $category->children->pluck('slug')->toArray();
                            $hasChildren = $category->children->isNotEmpty();
                            $isExpanded = in_array($category->slug, $selectedCats) || count(array_intersect($childSlugs, $selectedCats)) > 0;
                            $totalCount = $category->listings_count + $category->children->sum('listings_count');
                        @endphp
                        @continue($totalCount === 0)
                        <div class="k-cat-tree-item">
                            <div class="k-filter-option k-cat-parent">
                                @if($hasChildren)
                                    <button type="button" class="k-cat-toggle {{ $isExpanded ? 'open' : '' }}" onclick="this.classList.toggle('open');this.closest('.k-cat-tree-item').querySelector('.k-cat-children').classList.toggle('k-cat-children-open')">▸</button>
                                @endif
                                <input type="checkbox" name="categories[]" value="{{ $category->slug }}" id="cat-{{ $category->slug }}" @checked(in_array($category->slug, $selectedCats))>
                                <label for="cat-{{ $category->slug }}">{{ $category->icon ?? '🛒' }} {{ $category->name }} <span class="cnt">{{ $totalCount }}</span></label>
                            </div>
                            @if($hasChildren)
                                <div class="k-cat-children {{ $isExpanded ? 'k-cat-children-open' : '' }}">
                                    @foreach($category->children as $child)
                                        <div class="k-filter-option k-cat-child">
                                            <input type="checkbox" name="categories[]" value="{{ $child->slug }}" id="cat-{{ $child->slug }}" @checked(in_array($child->slug, $selectedCats))>
                                            <label for="cat-{{ $child->slug }}">{{ $child->name }} <span class="cnt">{{ $child->listings_count ?? 0 }}</span></label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="k-filter-section">
                    <h4>Location <span>−</span></h4>
                    @foreach(($locations ?? []) as $location)
                        <div class="k-filter-option">
                            <input type="checkbox" name="locations[]" value="{{ $location->id }}" id="loc-{{ $location->id }}" @checked(in_array($location->id, request('locations', [])))>
                            <label for="loc-{{ $location->id }}">📍 {{ $location->name }} <span class="cnt">{{ $location->listings_count ?? 0 }}</span></label>
                        </div>
                    @endforeach
                </div>

                <div class="k-filter-section">
                    <h4>Price Range <span>−</span></h4>
                    <div class="k-price-range">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min Price">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max Price">
                    </div>
                </div>

                <button type="submit" class="k-btn k-btn-primary w-full k-btn-center k-filter-submit">▽ Apply Filter</button>
            </form>

            <div class="mt-20">
                @include('frontend.partials.ad-banner', ['location' => 'listings_sidebar', 'style' => 'box'])
            </div>
        </div>

        <!-- Listings Main -->
        <div>
            <div class="k-listings-toolbar">
                <div class="k-listings-count" id="k-results-count">Showing <strong>{{ $listings->firstItem() ?? 0 }}–{{ $listings->lastItem() ?? 0 }}</strong> of <strong>{{ $listings->total() }} results</strong></div>
                <div class="k-sort-row">
                    <span class="k-sort-label">Sort By:</span>
                    <select id="k-sort-select" class="k-sort-select">
                        <option value="">Newest First</option>
                        <option value="popular" @selected(request('sort')=='popular')>Most Popular</option>
                        <option value="price_low" @selected(request('sort')=='price_low')>Price: Low to High</option>
                        <option value="price_high" @selected(request('sort')=='price_high')>Price: High to Low</option>
                    </select>
                </div>
            </div>

            <div id="k-listings-results">
                @include('frontend.listings.grid-partial', ['listings' => $listings])
            </div>
            <div class="k-ajax-spinner" id="k-spinner"></div>
        </div>
    </div>
</div>

<section class="k-main pt-0">
    <div class="k-section">
        <div class="k-section-header">
            <h2 class="k-section-title">Popular Categories</h2>
            <a href="/listings" class="k-section-link">View All Categories →</a>
        </div>
        <div class="k-cats">
            @foreach($categories->take(8) as $category)
                <a href="/listings?categories[]={{ $category->slug }}" class="k-cat-pill">
                    <span class="icon">{{ $category->icon ?? '🛒' }}</span>
                    <span class="label">{{ $category->name }}</span>
                    <span class="count">{{ $category->listings_count ?? 0 }} ads</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
(function(){
    // Parent category: check = select all subs + expand, uncheck = deselect subs + collapse
    document.querySelectorAll('.k-cat-tree-item').forEach(function(item){
        var parentCb = item.querySelector('.k-cat-parent input[type="checkbox"]');
        var toggle = item.querySelector('.k-cat-toggle');
        var childrenBox = item.querySelector('.k-cat-children');
        if(!parentCb || !childrenBox) return;
        var childCbs = childrenBox.querySelectorAll('input[type="checkbox"]');

        parentCb.addEventListener('change', function(){
            childCbs.forEach(function(cb){ cb.checked = parentCb.checked; });
            if(parentCb.checked){
                childrenBox.classList.add('k-cat-children-open');
                if(toggle) toggle.classList.add('open');
            } else {
                childrenBox.classList.remove('k-cat-children-open');
                if(toggle) toggle.classList.remove('open');
            }
            if(typeof Event === 'function'){
                parentCb.closest('form').dispatchEvent(new Event('change', {bubbles:false}));
            }
        });

        // Unchecking a sub keeps others; if all subs unchecked, uncheck parent too
        childCbs.forEach(function(cb){
            cb.addEventListener('change', function(){
                var anyChecked = Array.prototype.some.call(childCbs, function(c){ return c.checked; });
                if(!anyChecked) parentCb.checked = false;
            });
        });
    });
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

    function fetchListings(url){
        if(controller) controller.abort();
        controller = new AbortController();
        results.classList.add('k-ajax-loading');
        spinner.classList.add('active');

        fetch(url, {
            headers: {'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},
            signal: controller.signal
        })
        .then(r => r.json())
        .then(data => {
            results.innerHTML = data.html;
            countEl.innerHTML = 'Showing <strong>'+data.from+'–'+data.to+'</strong> of <strong>'+data.count+' results</strong>';
            results.classList.remove('k-ajax-loading');
            spinner.classList.remove('active');
            bindPagination();
            if (window.kMarkLoadedImages) { window.kMarkLoadedImages(); setTimeout(window.kMarkLoadedImages, 600); }
            results.scrollIntoView({behavior:'smooth', block:'start'});
            history.replaceState(null,'', url.replace(/&?_ajax=1/,''));
        })
        .catch(e => {
            if(e.name !== 'AbortError'){
                results.classList.remove('k-ajax-loading');
                spinner.classList.remove('active');
            }
        });
    }

    function applyFilter(e){
        if(e) e.preventDefault();
        const params = buildParams();
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

    form.addEventListener('submit', applyFilter);

    form.querySelectorAll('input[type="radio"]').forEach(function(r){
        r.addEventListener('change', applyFilter);
    });

    sortEl.addEventListener('change', applyFilter);

    bindPagination();

    // Mobile filter toggle
    var togBtn = document.getElementById('k-filter-toggle');
    var sidebar = document.getElementById('k-filter-sidebar');
    if(togBtn && sidebar){
        togBtn.addEventListener('click', function(){
            sidebar.classList.toggle('k-filter-collapsed');
            togBtn.classList.toggle('open');
        });
        if(window.innerWidth > 1100){
            sidebar.classList.remove('k-filter-collapsed');
        }
    }
})();
</script>
@endpush
