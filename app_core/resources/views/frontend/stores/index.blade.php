@extends('layouts.app')

@section('title','All Stores — Verified Local Businesses in Kegalle · Kegalle Marketplace')
@section('meta_description','Discover trusted, verified stores and businesses across Kegalle, Mawanella, Ruwanwella and beyond. Browse local shops and contact sellers directly.')
@section('canonical', url('/stores'))

@push('styles')
@endpush

@section('content')

{{-- ── PAGE HEADER ── --}}
<div class="k-page-header">
  <div class="k-page-header-inner">
    <div>
      <h1>All Stores in Kegalle</h1>
      <p>Discover trusted stores and verified sellers across Kegalle, Mawanella, Ruwanwella and beyond.</p>
    </div>
    <a href="/register?account_type=store" class="kfs-header-btn">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
      Add Your Store
    </a>
  </div>
</div>

{{-- ── BREADCRUMB ── --}}
<div class="container k-bc-pad">
  <nav class="k-breadcrumb" aria-label="Breadcrumb">
    <a href="/">Home</a><span>›</span>
    @if(request('category'))
      <a href="/stores">All Stores</a><span>›</span>
      <span aria-current="page">{{ request('category') }}</span>
    @elseif(request('q'))
      <a href="/stores">All Stores</a><span>›</span>
      <span aria-current="page">Search: "{{ e(request('q')) }}"</span>
    @else
      <span aria-current="page">All Stores</span>
    @endif
  </nav>
</div>

{{-- ── MAIN CONTENT ── --}}
<div class="container k-content-section k-content-pt">

  {{-- Mobile filter toggle --}}
  <button type="button" id="kfs-filter-toggle" aria-expanded="false" aria-controls="kfs-filter-sidebar">
    <span class="k-ftb-inner">
      <svg width="15" height="15" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M2 4h12M4 8h8M6 12h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
      Filter &amp; Sort
    </span>
    <svg class="k-ftb-chevron" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M3 5l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
  </button>

  <div class="kfs-page-grid">

    {{-- ── SIDEBAR ── --}}
    <div class="k-filter-sidebar" id="kfs-filter-sidebar">
      <div class="k-filter-header">
        <span class="k-filter-title"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="k-filter-icon"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>Filter</span>
        <a href="/stores" class="k-filter-clear">Clear Filters</a>
      </div>
      @php
        $_storeCatTree = [];
        foreach (($categories ?? collect()) as $_c) {
          $_subs = [];
          foreach ($_c->children as $_s) {
            $_subs[] = ['slug' => $_s->slug, 'name' => $_s->name, 'count' => $_s->listings_count ?? 0];
          }
          $_total = ($_c->listings_count ?? 0) + $_c->children->sum('listings_count');
          $_storeCatTree[] = ['slug' => $_c->slug, 'name' => $_c->name, 'count' => $_total, 'subs' => $_subs];
        }
      @endphp
      <div class="k-filter-section">
        <h4>Category <span class="k-stoggle">−</span></h4>
        <div id="kStoreCatUI"></div>
        <script nonce="{{ $cspNonce ?? '' }}">
        (function(){
          var tree = @json($_storeCatTree);
          var selected = '{{ request('category') }}';
          var ui = document.getElementById('kStoreCatUI');
          if (!ui) return;
          function esc(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
          function renderTop(){
            var html='<a href="/stores" class="kcd-all-item'+(selected?'':' kcd-selected')+'"><span class="kcd-name">All Categories</span></a>';
            tree.forEach(function(cat){
              if(cat.count===0 && selected!==cat.slug && !cat.subs.some(function(s){return s.slug===selected;})) return;
              var isSel=selected===cat.slug||cat.subs.some(function(s){return s.slug===selected;});
              html+='<div class="kcd-main-item'+(isSel?' kcd-selected':'')+'" data-slug="'+esc(cat.slug)+'">'
                +'<span class="kcd-name">'+esc(cat.name)+'</span>'
                +'<span class="kcd-count">'+cat.count+'</span>'
                +(cat.subs.length?'<span class="kcd-arrow">›</span>':'')
                +'</div>';
            });
            ui.innerHTML=html;
            ui.querySelectorAll('.kcd-main-item').forEach(function(el){
              el.addEventListener('click',function(){
                var cat=tree.filter(function(c){return c.slug===el.dataset.slug;})[0];
                if(cat&&cat.subs.length){renderSub(cat);}
                else{window.location='/stores?category='+el.dataset.slug;}
              });
            });
          }
          function renderSub(cat){
            var html='<div class="kcd-back">‹ '+esc(cat.name)+'</div>'
              +'<a href="/stores?category='+esc(cat.slug)+'" class="kcd-all-item'+(selected===cat.slug?' kcd-selected':'')+'">'
              +'<span class="kcd-name">All '+esc(cat.name)+'</span>'
              +'<span class="kcd-count">'+cat.count+'</span></a>';
            cat.subs.forEach(function(sub){
              var isSel=selected===sub.slug;
              html+='<div class="kcd-sub-item'+(isSel?' kcd-selected':'')+'" data-slug="'+esc(sub.slug)+'">'
                +'<span class="kcd-sub-dot"></span>'
                +'<span class="kcd-name">'+esc(sub.name)+'</span>'
                +'<span class="kcd-count">'+sub.count+'</span></div>';
            });
            ui.innerHTML=html;
            ui.querySelector('.kcd-back').addEventListener('click',renderTop);
            ui.querySelectorAll('.kcd-sub-item').forEach(function(el){
              el.addEventListener('click',function(){window.location='/stores?category='+el.dataset.slug;});
            });
          }
          var activeParent=null;
          if(selected){tree.forEach(function(cat){if(cat.subs.some(function(s){return s.slug===selected;}))activeParent=cat;});}
          if(activeParent){renderSub(activeParent);}else{renderTop();}
        })();
        </script>
      </div>
    </div>

    {{-- ── MAIN COLUMN ── --}}
    <div>

      {{-- Toolbar: search + count --}}
      <div class="kfs-toolbar">
        <form method="GET" class="kfs-search-form" id="kfsForm">
          <div class="kfs-search-wrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <label for="kfsSearchInput" class="sr-only">Search stores</label>
            <input id="kfsSearchInput" type="text" name="q" value="{{ request('q') }}" placeholder="Search stores…" class="kfs-search-input" aria-label="Search stores">
          </div>

          {{-- Location custom dropdown --}}
          <input type="hidden" name="location" id="kfsLocVal" value="{{ request('location') }}">
          <div class="kfs-dd" id="kfsLocDd">
            <div class="kfs-dd-trigger" id="kfsLocTrigger">
              <span class="kfs-dd-label {{ request('location') ? '' : 'placeholder' }}" id="kfsLocLabel">{{ request('location') ?: 'All Locations' }}</span>
              <svg class="kfs-dd-chevron" width="12" height="12" viewBox="0 0 12 8" fill="none"><path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div class="kfs-dd-panel" id="kfsLocPanel">
              <div class="kfs-dd-list" id="kfsLocList">
                <div class="kfs-dd-item {{ !request('location') ? 'selected' : '' }}" data-value="" data-label="All Locations">
                  <span class="kfs-dd-dot"></span>All Locations
                </div>
                <div class="kfs-dd-divider"></div>
                @foreach(($locations ?? []) as $loc)
                  @if($loc->name !== 'Other')
                  <div class="kfs-dd-item {{ request('location')===$loc->name ? 'selected' : '' }}" data-value="{{ $loc->name }}" data-label="{{ $loc->name }}">
                    <span class="kfs-dd-dot"></span>{{ $loc->name }}
                  </div>
                  @endif
                @endforeach
              </div>
            </div>
          </div>

          {{-- Sort custom dropdown --}}
          <input type="hidden" name="sort" id="kfsSortVal" value="{{ request('sort') }}">
          <div class="kfs-dd kfs-dd-sort" id="kfsSortDd">
            <div class="kfs-dd-trigger" id="kfsSortTrigger">
              @php $sortLabel = request('sort')==='newest' ? 'Newest' : (request('sort')==='products' ? 'Most Products' : 'Popular'); @endphp
              <span class="kfs-dd-label {{ request('sort') ? '' : 'placeholder' }}" id="kfsSortLabel">{{ $sortLabel }}</span>
              <svg class="kfs-dd-chevron" width="12" height="12" viewBox="0 0 12 8" fill="none"><path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div class="kfs-dd-panel" id="kfsSortPanel">
              <div class="kfs-dd-list kfs-dd-list-p" id="kfsSortList">
                <div class="kfs-dd-item {{ !request('sort') ? 'selected' : '' }}" data-value="" data-label="Popular"><span class="kfs-dd-dot"></span>Popular</div>
                <div class="kfs-dd-item {{ request('sort')==='newest' ? 'selected' : '' }}" data-value="newest" data-label="Newest"><span class="kfs-dd-dot"></span>Newest</div>
                <div class="kfs-dd-item {{ request('sort')==='products' ? 'selected' : '' }}" data-value="products" data-label="Most Products"><span class="kfs-dd-dot"></span>Most Products</div>
              </div>
            </div>
          </div>

          <button type="submit" class="kfs-search-btn">Search</button>
        </form>
        <div class="kfs-count-text">
          <strong>{{ $stores->total() }}</strong> stores
        </div>
      </div>

      {{-- Skeleton loader --}}
      <div id="kfs-skeleton" class="kfs-skel-grid">
        @for($i = 0; $i < 6; $i++)
        <div class="kfs-skel-card">
          <div class="kfs-skel-banner"></div>
          <div class="kfs-skel-body">
            <div class="kfs-skel-line kfs-skel-lg"></div>
            <div class="kfs-skel-line kfs-skel-md"></div>
            <div class="kfs-skel-line kfs-skel-sm"></div>
          </div>
        </div>
        @endfor
      </div>

      {{-- Store card grid --}}
      <div class="kfs-grid" id="kfs-grid">
        @forelse($stores as $store)
        @php
          $logo   = !empty($store->logo)   ? asset('storage/'.ltrim($store->logo,'/'))   : null;
          $v      = $store->updated_at?->timestamp ?? time();
          $banner = !empty($store->banner) ? asset('storage/'.ltrim($store->banner,'/')).('?v='.$v)
                  : (!empty($store->cover_image) ? asset('storage/'.ltrim($store->cover_image,'/')).('?v='.$v) : null);
          $avg    = ($store->approved_reviews_count ?? 0) > 0
                    ? round($store->approved_reviews_avg_rating ?? 0, 1) : 0;
        @endphp
        <a href="/store/{{ $store->slug }}" class="kfs-card">
          {{-- Banner --}}
          <div class="kfs-card-banner">
            @if($banner)<img src="{{ $banner }}" alt="{{ $store->name }}" loading="lazy">@endif
            <div class="kfs-logo-ring">
              <div class="kfs-logo-init">{{ strtoupper(substr($store->name,0,2)) }}</div>
              @if($logo)<img src="{{ $logo }}" alt="{{ $store->name }}" loading="lazy" class="kfs-logo-img">@endif
            </div>
            @if(!empty($store->rank_label))
              <span class="kfs-rank-badge kfs-rank-{{ $store->rank ?? 'bronze' }}">{{ $store->rank_label }}</span>
            @endif
          </div>
          {{-- Body --}}
          <div class="kfs-card-body">
            <div class="kfs-card-name" title="{{ $store->name }}">{{ $store->name }}</div>
            @if($avg > 0)
            <div class="kfs-rating">★ {{ $avg }} <span>({{ $store->approved_reviews_count }})</span></div>
            @endif
            <div class="kfs-desc">{{ \Illuminate\Support\Str::limit($store->description ?? 'Trusted local seller in Kegalle.', 75) }}</div>
            {{-- Verification badges where location used to be --}}
            <div class="kfs-badges kfs-badges-mb">
              @if($store->is_verified)
                <span class="kfs-badge kfs-badge-v">✓ Verified Store</span>
              @endif
              @if($store->user?->phone_verified_at)
                <span class="kfs-badge kfs-badge-p"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="k-badge-svg"><polyline points="20 6 9 17 4 12"/></svg>Phone Verified</span>
              @endif
            </div>
            <div class="kfs-card-footer">
              <div class="kfs-prod-count">
                <strong>{{ $store->listings_count ?? 0 }}</strong>
                {{ \Illuminate\Support\Str::plural('Product', $store->listings_count ?? 0) }}
              </div>
              <span class="kfs-visit-btn">
                Visit Store
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
              </span>
            </div>
          </div>
        </a>
        @empty
        <div class="kfs-empty">
          <span class="kfs-empty-icon">🏪</span>
          <strong>No stores found</strong>
          <p>Try a different search or browse all categories.</p>
        </div>
        @endforelse
      </div>

      {{-- Pagination --}}
      <div style="margin-top:20px">{{ $stores->links('vendor.pagination.k-theme') }}</div>

      {{-- Stats bar --}}
      @php
        $statStores    = \App\Models\Store::approved()->count();
        $statProducts  = \App\Models\Listing::where('status','approved')->count();
        $statReviews   = \App\Models\Review::where('status','approved')->count();
        $statLocations = \App\Models\Location::where('is_active',1)->count();
      @endphp
      <div class="kfs-stats">
        <div class="kfs-stat"><div class="kfs-stat-n">{{ $statStores }}</div><div class="kfs-stat-l">Verified Stores</div></div>
        <div class="kfs-stat"><div class="kfs-stat-n">{{ $statProducts }}</div><div class="kfs-stat-l">Active Listings</div></div>
        @if($statReviews > 0)<div class="kfs-stat"><div class="kfs-stat-n">{{ $statReviews }}</div><div class="kfs-stat-l">Customer Reviews</div></div>@endif
        <div class="kfs-stat"><div class="kfs-stat-n">{{ $statLocations }}</div><div class="kfs-stat-l">Towns Covered</div></div>
      </div>

    </div>{{-- /main column --}}
  </div>{{-- /page-grid --}}
</div>{{-- /container --}}

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded', function(){
  // Hide skeleton once real grid is ready
  var sk = document.getElementById('kfs-skeleton');
  if(sk) sk.style.display = 'none';

  // Collapsible sidebar sections
  var sidebar = document.getElementById('kfs-filter-sidebar');
  if(sidebar){
    sidebar.querySelectorAll('.k-filter-section').forEach(function(sec){
      var h4 = sec.querySelector('h4');
      if(!h4) return;
      if(!sec.querySelector('.k-fsec-body')){
        var body = document.createElement('div');
        body.className = 'k-fsec-body';
        var node = h4.nextSibling;
        while(node){ var next = node.nextSibling; body.appendChild(node); node = next; }
        sec.appendChild(body);
      }
      var tog = h4.querySelector('.k-stoggle');
      h4.addEventListener('click', function(){
        var collapsed = sec.classList.toggle('k-sec-collapsed');
        if(tog) tog.textContent = collapsed ? '+' : '−';
      });
    });
  }

  // Mobile toggle
  var togBtn = document.getElementById('kfs-filter-toggle');
  var sidebarEl = document.getElementById('kfs-filter-sidebar');
  if(togBtn && sidebarEl){
    togBtn.addEventListener('click', function(){
      var open = sidebarEl.classList.toggle('k-filter-open');
      togBtn.classList.toggle('open', open);
      togBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }
});

// ── Premium dropdowns ──────────────────────────────────────────
(function(){
  function makeDD(triggerId, panelId, valId, labelId, searchId, listId){
    var trigger=document.getElementById(triggerId),
        panel=document.getElementById(panelId),
        valEl=document.getElementById(valId),
        labelEl=document.getElementById(labelId),
        searchEl=searchId?document.getElementById(searchId):null,
        list=document.getElementById(listId);
    if(!trigger||!panel) return;
    var open=false;
    function openDD(){
      open=true; panel.classList.add('open'); trigger.classList.add('open');
      if(searchEl){searchEl.value=''; filterList(''); searchEl.focus();}
    }
    function closeDD(){ open=false; panel.classList.remove('open'); trigger.classList.remove('open'); }
    trigger.addEventListener('click',function(e){e.stopPropagation(); open?closeDD():openDD();});
    if(searchEl) searchEl.addEventListener('input',function(){ filterList(this.value.toLowerCase()); });
    function filterList(q){
      var items=list.querySelectorAll('.kfs-dd-item'), any=false;
      items.forEach(function(i){
        var m=!q||i.dataset.label.toLowerCase().indexOf(q)!==-1;
        i.style.display=m?'':'none'; if(m)any=true;
      });
    }
    list.addEventListener('click',function(e){
      var item=e.target.closest('.kfs-dd-item'); if(!item) return;
      var val=item.dataset.value, lbl=item.dataset.label;
      valEl.value=val;
      labelEl.textContent=lbl;
      labelEl.classList.toggle('placeholder', val==='');
      list.querySelectorAll('.kfs-dd-item').forEach(function(i){ i.classList.remove('selected'); i.querySelector('.kfs-dd-dot').style.opacity='0'; });
      item.classList.add('selected'); item.querySelector('.kfs-dd-dot').style.opacity='1';
      closeDD();
    });
    document.addEventListener('click',function(e){
      if(open && !trigger.closest('.kfs-dd').contains(e.target)) closeDD();
    });
  }
  makeDD('kfsLocTrigger','kfsLocPanel','kfsLocVal','kfsLocLabel',null,'kfsLocList');
  makeDD('kfsSortTrigger','kfsSortPanel','kfsSortVal','kfsSortLabel',null,'kfsSortList');
})();
</script>
@endpush
