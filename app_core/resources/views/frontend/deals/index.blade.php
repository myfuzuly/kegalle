@extends('layouts.app')

@section('title', 'Best Deals & Offers in Kegalle — Kegalle Marketplace')
@section('meta_description', 'Discover amazing discounts on products, vehicles, properties and more from trusted local sellers in Kegalle. Flash deals, coupons & exclusive offers.')

@section('content')

<div class="k-page-header">
    <div class="k-page-header-inner">
        <div>
            <h1>🔥 Best Deals &amp; Offers in Kegalle</h1>
            <p>Discover amazing discounts on products, vehicles, properties and more from trusted local sellers.</p>
        </div>
        <form class="k-page-header-search" action="/deals" method="GET" id="dealsSearchForm">
            @foreach(request()->except(['q','page']) as $k => $v)
                @if(is_array($v))
                    @foreach($v as $item)<input type="hidden" name="{{ $k }}[]" value="{{ $item }}">@endforeach
                @else
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endif
            @endforeach
            <label for="deals-search" class="sr-only">Search deals</label>
            <input id="deals-search" type="text" name="q" placeholder="Search deals..." value="{{ request('q') }}" aria-label="Search deals">
            <button type="submit" aria-label="Submit search">Search</button>
        </form>
    </div>
</div>

{{-- ========== CATEGORY SHORTCUTS ========== --}}
<section class="container k-deals-section--top">
    <div class="k-deals-cats">
        @php
            $dealCatIcons = ['📱','💻','🚗','🏠','🪑','👗','🔧','⚽'];
        @endphp
        @foreach($categories->take(8) as $i => $cat)
            <a href="/listings?categories[]={{ $cat->slug }}" class="k-deals-cat-item {{ in_array($cat->slug, (array)request('categories', [])) ? 'active' : '' }}">
                <div class="k-deals-cat-icon">{{ $dealCatIcons[$i] ?? '📦' }}</div>
                <span>{{ $cat->name }}</span>
            </a>
        @endforeach
        <a href="/categories" class="k-deals-cat-item">
            <div class="k-deals-cat-icon">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect x="2" y="2" width="6" height="6" rx="1.5" fill="currentColor" opacity=".7"/><rect x="12" y="2" width="6" height="6" rx="1.5" fill="currentColor" opacity=".7"/><rect x="2" y="12" width="6" height="6" rx="1.5" fill="currentColor" opacity=".7"/><rect x="12" y="12" width="6" height="6" rx="1.5" fill="currentColor" opacity=".7"/></svg>
            </div>
            <span>More</span>
        </a>
    </div>
</section>

{{-- ========== FLASH DEALS ========== --}}
@if(($useRealDeals ?? false) && $flashDeals->isNotEmpty())
@php $flashIsTrue = $flashDeals->first()?->ends_at
    && $flashDeals->first()->ends_at->gt(now())
    && $flashDeals->first()->ends_at->lt(now()->addHours(24)); @endphp
<section class="container k-content-section--pb">
    <div class="k-section-header">
        <div class="k-section-header-row">
            <h2 class="k-section-title">{{ $flashIsTrue ? '⚡ Flash Deals' : '🏷️ Limited Deals' }}</h2>
            @if($flashDeals->first()?->ends_at)
            <div class="k-flash-timer-badge">
                Ending in
                <span class="k-flash-timer" id="flashTimer"
                      data-ends="{{ $flashDeals->first()->ends_at->toIso8601String() }}">
                    --:--:--
                </span>
            </div>
            @endif
        </div>
        <a href="/deals" class="k-view-all">View all Deals →</a>
    </div>

    <div class="k-flash-deals-grid">
        @foreach($flashDeals as $item)
            @php
                $listing = $item->listing;
                if (!$listing) continue;
                $img    = optional($listing->images->first())->path ?? null;
                $imgUrl = $img ? asset('storage/'.ltrim($img,'/')) : null;
                $soldPct = $item->stock_qty > 0 ? min(100, (int)($item->sold_count / $item->stock_qty * 100)) : 0;
                $location = optional($listing->locationModel)->name ?? $listing->location ?? 'Kegalle';
            @endphp
            <a href="/listings/{{ $listing->slug }}" class="k-flash-deal-card">
                <div class="k-flash-deal-img">
                    @if($imgUrl)
                        <img src="{{ $imgUrl }}" alt="{{ $listing->title }}" loading="lazy">
                    @else
                        <div class="k-deal-img-ph">🛍️</div>
                    @endif
                    <span class="k-deal-badge-pct">-{{ number_format($item->discount_percent, 0) }}%</span>
                    <button class="k-deal-heart" aria-label="Save" type="button">♡</button>
                </div>
                <div class="k-flash-deal-info">
                    <h3 class="k-flash-deal-title">{{ \Illuminate\Support\Str::limit($listing->title, 28) }}</h3>
                    <div class="k-flash-deal-loc">📍 {{ $location }}</div>
                    <div class="k-flash-deal-prices">
                        <span class="k-flash-deal-sale">LKR {{ number_format($item->deal_price) }}</span>
                        <span class="k-flash-deal-orig">LKR {{ number_format($item->original_price) }}</span>
                    </div>
                    @if($soldPct > 0)
                    <div class="k-flash-deal-progress">
                        <div class="k-flash-deal-bar"><div class="k-flash-deal-bar-fill" style="width:{{ $soldPct }}%"></div></div>
                        <span class="k-flash-deal-sold">{{ $soldPct }}% Sold</span>
                    </div>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- ========== SELLER CTA STRIP ========== --}}
<section class="container k-content-section--pb">
    <div class="k-deals-seller-strip">
        <div class="k-deals-seller-icon">🏪</div>
        <div class="k-deals-seller-text">
            <strong>Got something to sell?</strong>
            <span>List your products for free and reach thousands of buyers in Kegalle district.</span>
        </div>
        <div class="k-deals-seller-actions">
            <a href="/register?account_type=store" class="k-btn k-btn-primary">Open Free Store</a>
            <a href="/dashboard/listings/create" class="k-btn k-btn-outline k-btn-white">Post an Ad</a>
        </div>
    </div>
</section>

{{-- ========== FILTER + FEATURED DEALS / LISTINGS GRID ========== --}}
<section class="container k-content-section--pbl">
    <form method="GET" action="/deals" id="dealsFilterForm">
        @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
        <div class="k-deals-layout">
            {{-- Sidebar --}}
            <aside class="k-deals-sidebar">
                <div class="k-section-header k-section-header--compact">
                    <h3 class="k-deals-filter-title">Filter Deals</h3>
                    <a href="/deals" class="k-deals-filter-clear">Clear All</a>
                </div>

                @if($useRealDeals ?? false)
                <div class="k-filter-group">
                    <div class="k-filter-label">Discount</div>
                    @foreach([10 => '10% and above', 25 => '25% and above', 50 => '50% and above'] as $val => $label)
                    <label class="k-filter-check">
                        <input type="radio" name="discount" value="{{ $val }}" {{ request('discount') == $val ? 'checked' : '' }}>
                        {{ $label }}
                    </label>
                    @endforeach
                </div>

                <div class="k-filter-group">
                    <div class="k-filter-label">Price Range (LKR)</div>
                    <div class="k-size-grid">
                        <input type="number" name="min_price" placeholder="Min" class="k-filter-input" value="{{ request('min_price') }}">
                        <input type="number" name="max_price" placeholder="Max" class="k-filter-input" value="{{ request('max_price') }}">
                    </div>
                </div>

                <div class="k-filter-group">
                    <div class="k-filter-label">Sort by</div>
                    <select name="sort" class="k-filter-select">
                        <option value="newest"    {{ request('sort','newest') === 'newest'    ? 'selected' : '' }}>Newest Deals</option>
                        <option value="popular"   {{ request('sort') === 'popular'   ? 'selected' : '' }}>Most Popular</option>
                        <option value="discount"  {{ request('sort') === 'discount'  ? 'selected' : '' }}>Highest Discount</option>
                        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high"{{ request('sort') === 'price_high'? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </div>

                <button type="submit" class="k-btn k-btn-primary k-btn-full k-btn-mt-sm">Apply Filters</button>
                @else
                <p class="k-filter-note">No active deals yet. <a href="{{ auth()->check() ? '/dashboard/deals/create' : '/register' }}" style="color:var(--k-primary)">Post a deal →</a></p>
                @endif
            </aside>

            {{-- Main Grid --}}
            <div>
                @if(!($useRealDeals ?? false))
                <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #86efac;border-radius:12px;padding:20px 24px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
                    <div>
                        <strong style="display:block;font-size:14px;color:#166534;margin-bottom:4px">🏷️ No active deals yet — be the first!</strong>
                        <span style="font-size:13px;color:#15803d">Store owners can post discounted deals to attract more buyers.</span>
                    </div>
                    <a href="{{ auth()->check() ? '/dashboard/deals/create' : '/register' }}" class="k-btn k-btn-primary k-btn-sm" style="white-space:nowrap">Post a Deal →</a>
                </div>
                @endif
                <div class="k-section-header k-section-header--compact">
                    <h2 class="k-section-title">
                        {{ ($useRealDeals ?? false) ? 'Featured Deals' : 'Browse Listings' }}
                    </h2>
                    @if($useRealDeals ?? false)
                    <select class="k-filter-select k-filter-select--auto" onchange="document.querySelector('[name=sort]').value=this.value;document.getElementById('dealsFilterForm').submit()">
                        <option value="newest"    {{ request('sort','newest') === 'newest'    ? 'selected' : '' }}>Newest</option>
                        <option value="popular"   {{ request('sort') === 'popular'   ? 'selected' : '' }}>Most Popular</option>
                        <option value="discount"  {{ request('sort') === 'discount'  ? 'selected' : '' }}>Highest Discount</option>
                        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price ↑</option>
                        <option value="price_high"{{ request('sort') === 'price_high'? 'selected' : '' }}>Price ↓</option>
                    </select>
                    @endif
                </div>

                @php
                    $gridItems = ($useRealDeals ?? false) ? $featuredDeals : $latestListings;
                    $isEmpty   = is_object($gridItems) && method_exists($gridItems, 'isEmpty') ? $gridItems->isEmpty() : ($gridItems->count() === 0);
                @endphp

                @if($isEmpty)
                    <div class="k-empty-state-box k-empty-state-box--centered">
                        <span class="k-empty-icon">🔍</span>
                        <h3 class="k-empty-heading">No deals found</h3>
                        <p class="k-empty-text">Try adjusting your filters or <a href="/deals">clear all</a>.</p>
                    </div>
                @else
                <div class="k-featured-deals-grid">
                    @foreach($gridItems as $i => $item)
                        @php
                            if (($useRealDeals ?? false) && $item instanceof \App\Models\Deal) {
                                $listing     = $item->listing;
                                $salePrice   = $item->deal_price;
                                $origPrice   = $item->original_price;
                                $discountPct = (int)$item->discount_percent;
                                $isFeatured  = $item->is_featured;
                                $store       = $item->store;
                            } else {
                                $listing     = $item;
                                $salePrice   = null;
                                $origPrice   = $item->price > 0 ? $item->price : null;
                                $discountPct = null;
                                $isFeatured  = $item->is_featured ?? false;
                                $store       = $item->store ?? null;
                            }
                            if (!$listing) continue;
                            $img      = optional($listing->images->first())->path ?? null;
                            $imgUrl   = $img ? asset('storage/'.ltrim($img,'/')) : null;
                            $location = optional($listing->locationModel)->name ?? $listing->location ?? 'Kegalle';
                            $storeNm  = $store->name ?? 'Personal Seller';
                            $storeSlug= $store->slug ?? null;
                        @endphp
                        <a href="/listings/{{ $listing->slug }}" class="k-deal-card">
                            <div class="k-deal-card-img">
                                @if($imgUrl)
                                    <img src="{{ $imgUrl }}" alt="{{ $listing->title }}" loading="lazy">
                                @else
                                    <div class="k-deal-img-ph">🛍️</div>
                                @endif
                                @if($discountPct)
                                    <span class="k-deal-badge-pct">-{{ $discountPct }}%</span>
                                @endif
                                @if($isFeatured)
                                    <span class="k-deals-featured-badge">⭐ FEATURED</span>
                                @endif
                                <button class="k-deal-heart" aria-label="Save" type="button">♡</button>
                            </div>
                            <div class="k-deal-card-info">
                                <h3 class="k-deal-card-title">{{ \Illuminate\Support\Str::limit($listing->title, 30) }}</h3>
                                <div class="k-deal-card-loc">📍 {{ $location }}</div>
                                <div class="k-deal-card-prices">
                                    @if($salePrice)
                                        <span class="k-deal-card-sale">LKR {{ number_format($salePrice) }}</span>
                                        @if($origPrice)
                                        <span class="k-deal-card-orig">LKR {{ number_format($origPrice) }}</span>
                                        @endif
                                    @elseif($origPrice)
                                        <span class="k-deal-card-sale">LKR {{ number_format($origPrice) }}</span>
                                    @else
                                        <span class="k-deal-card-sale">Contact Seller</span>
                                    @endif
                                </div>
                                <div class="k-deal-card-store">🏪
                                    @if($storeSlug)
                                        <a class="k-deal-store-link" href="/store/{{ $storeSlug }}" onclick="event.stopPropagation()">{{ $storeNm }}</a>
                                    @else
                                        {{ $storeNm }}
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                @endif

                @if(($useRealDeals ?? false) && method_exists($featuredDeals, 'links'))
                    <div class="k-deals-pagination">{{ $featuredDeals->links() }}</div>
                @endif
            </div>
        </div>
    </form>
</section>

{{-- ========== TRUST BADGES ========== --}}
<section class="k-deals-trust">
    <div class="container">
        <div class="k-deals-trust-grid">
            <div class="k-deals-trust-item">
                <span class="k-deals-trust-icon">✓</span>
                <div><strong>Best Price Guarantee</strong><br><small>Get the best prices in Kegalle</small></div>
            </div>
            <div class="k-deals-trust-item">
                <span class="k-deals-trust-icon">🔒</span>
                <div><strong>Verified Sellers</strong><br><small>Trusted stores in Kegalle</small></div>
            </div>
            <div class="k-deals-trust-item">
                <span class="k-deals-trust-icon">🚚</span>
                <div><strong>Fast Local Delivery</strong><br><small>Quick delivery across Kegalle</small></div>
            </div>
            <div class="k-deals-trust-item">
                <span class="k-deals-trust-icon">💬</span>
                <div><strong>24/7 Support</strong><br><small>We're here to help you</small></div>
            </div>
        </div>
    </div>
</section>

{{-- ========== NEWSLETTER ========== --}}
<section class="k-deals-newsletter">
    <div class="k-newsletter-inner">
        <div class="k-newsletter-left">
            <span class="k-newsletter-icon">✉️</span>
            <div>
                <div class="k-newsletter-title">Subscribe to our newsletter</div>
                <div class="k-newsletter-sub">Get the latest deals, offers and updates delivered to your inbox.</div>
            </div>
        </div>
        <form class="k-newsletter-form" id="dealsNewsletterForm" onsubmit="dealsNewsletterSubmit(event)">
            @csrf
            <input type="email" name="email" placeholder="Enter your email" class="k-newsletter-input" required>
            <button type="submit" class="k-btn k-btn-accent" id="dealsNewsletterBtn">Subscribe</button>
        </form>
        <script nonce="{{ $cspNonce ?? '' }}">
        function dealsNewsletterSubmit(e){
            e.preventDefault();
            var form=document.getElementById('dealsNewsletterForm');
            var btn=document.getElementById('dealsNewsletterBtn');
            var email=form.querySelector('input[name=email]').value;
            btn.disabled=true; btn.textContent='Subscribing…';
            fetch('/newsletter/subscribe',{
                method:'POST',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':form.querySelector('input[name=_token]').value},
                body:JSON.stringify({email:email})
            }).then(function(r){return r.json();}).then(function(d){
                btn.textContent='✓ Subscribed!';
                form.querySelector('input[name=email]').value='';
                setTimeout(function(){btn.textContent='Subscribe';btn.disabled=false;},3000);
            }).catch(function(){btn.textContent='Try again';btn.disabled=false;});
        }
        </script>
    </div>
</section>

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
// Flash deal countdown — driven by real ends_at from DB
(function(){
    var timerEl = document.getElementById('flashTimer');
    if (!timerEl) return;
    var end = new Date(timerEl.dataset.ends);
    if (isNaN(end)) return;
    function pad(n){ return n < 10 ? '0'+n : n; }
    function tick(){
        var now = new Date(), d = Math.max(0, end - now);
        if (d === 0) { timerEl.textContent = 'Ended'; return; }
        var days = Math.floor(d/864e5), h = Math.floor(d%864e5/36e5), m = Math.floor(d%36e5/6e4), s = Math.floor(d%6e4/1e3);
        timerEl.textContent = days > 0
            ? days+'d '+pad(h)+'h '+pad(m)+'m '+pad(s)+'s'
            : pad(h)+' : '+pad(m)+' : '+pad(s);
    }
    tick(); setInterval(tick, 1000);
})();

// Auto-submit filter sidebar on sort-select change
(function(){
    var sel = document.querySelector('#dealsFilterForm [name="sort"]');
    if (sel) sel.addEventListener('change', function(){ document.getElementById('dealsFilterForm').submit(); });
})();
</script>
@endpush
