@extends('layouts.app')

@section('title', 'Best Deals & Offers in Kegalle — Kegalle Marketplace')
@section('meta_description', 'Discover amazing discounts on products, vehicles, properties and more from trusted local sellers in Kegalle. Flash deals, coupons & exclusive offers.')

@section('content')

{{-- ========== HERO BANNER ========== --}}
<section class="k-deals-hero">
    <div class="container">
        <div class="k-deals-hero-content">
            <span class="k-deals-hero-badge">🔥 LIMITED TIME OFFERS</span>
            <h1 class="k-deals-hero-title">Best Deals & Offers<br>in <span style="color:var(--k-accent)">Kegalle</span></h1>
            <p class="k-deals-hero-desc">Discover amazing discounts on products, vehicles, properties and more from trusted local sellers.</p>
            <form class="k-deals-search" action="/deals" method="GET">
                <span class="k-deals-search-icon">🔍</span>
                <input type="text" name="q" placeholder="Search deals, products or stores..." value="{{ request('q') }}">
                <button type="submit" class="k-deals-search-btn">Search Deals</button>
            </form>
            <div class="k-deals-hero-badges">
                <span>✓ Best Prices</span>
                <span>🔥 Top Sellers</span>
                <span>✓ Secure Deals</span>
                <span>🚚 Fast Delivery</span>
            </div>
        </div>
        <div class="k-deals-hero-visual">
            <div class="k-deals-hero-discount">
                <span class="k-deals-hero-upto">UP TO</span>
                <span class="k-deals-hero-percent">70<small>%</small></span>
                <span class="k-deals-hero-off">OFF</span>
            </div>
            <div class="k-deals-hero-countdown">
                <div class="k-deals-hero-countdown-label">Deal of the Day ends in</div>
                <div class="k-deals-countdown-boxes" id="dealCountdown">
                    <div class="k-countdown-box"><span id="cdHours">08</span><small>Hrs</small></div>
                    <span class="k-countdown-sep">:</span>
                    <div class="k-countdown-box"><span id="cdMins">24</span><small>Mins</small></div>
                    <span class="k-countdown-sep">:</span>
                    <div class="k-countdown-box"><span id="cdSecs">59</span><small>Secs</small></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ========== CATEGORY SHORTCUTS ========== --}}
<section class="container" style="padding-top:32px;padding-bottom:20px">
    <div class="k-deals-cats">
        @php
            $dealCatIcons = ['📱','💻','🚗','🏠','🪑','👗','🔧','⚽','•••'];
            $dealCatNames = ['Mobiles','Electronics','Vehicles','Property','Furniture','Fashion','Services','Sports','More'];
        @endphp
        @foreach($categories->take(8) as $i => $cat)
            <a href="/listings?categories[]={{ $cat->slug }}" class="k-deals-cat-item">
                <div class="k-deals-cat-icon">{{ $dealCatIcons[$i] ?? '📦' }}</div>
                <span>{{ $cat->name }}</span>
            </a>
        @endforeach
        @if($categories->count() < 8)
            @for($i = $categories->count(); $i < 8; $i++)
                <a href="/categories" class="k-deals-cat-item">
                    <div class="k-deals-cat-icon">{{ $dealCatIcons[$i] ?? '📦' }}</div>
                    <span>{{ $dealCatNames[$i] ?? 'More' }}</span>
                </a>
            @endfor
        @endif
        <a href="/categories" class="k-deals-cat-item">
            <div class="k-deals-cat-icon">•••</div>
            <span>More</span>
        </a>
    </div>
</section>

{{-- ========== FLASH DEALS ========== --}}
<section class="container" style="padding-bottom:32px">
    <div class="k-section-header">
        <div style="display:flex;align-items:center;gap:12px">
            <h2 class="k-section-title">⚡ Flash Deals</h2>
            <div class="k-flash-timer-badge">
                Ending in
                <span class="k-flash-timer" id="flashTimer">03 : 14 : 29</span>
            </div>
        </div>
        <a href="/listings?sort=popular" class="k-view-all">View all Flash Deals →</a>
    </div>

    <div class="k-flash-deals-grid">
        @php $flashSource = ($useRealDeals ?? false) && $flashDeals->isNotEmpty() ? $flashDeals : $featuredListings->take(5); @endphp
        @foreach($flashSource as $i => $item)
            @php
                $listing = ($useRealDeals ?? false) && isset($item->listing) ? $item->listing : $item;
                $img = optional($listing->images->first())->path ?? null;
                $imgUrl = $img ? asset('storage/'.ltrim($img,'/')) : null;
                if (($useRealDeals ?? false) && $item instanceof \App\Models\Deal) {
                    $originalPrice = $item->original_price;
                    $salePrice = $item->deal_price;
                    $discountPct = $item->discount_percent;
                    $soldPct = $item->stock_qty > 0 ? min(100, (int)($item->sold_count / $item->stock_qty * 100)) : [68,52,73,41,25][$i%5];
                } else {
                    $originalPrice = $listing->price > 0 ? $listing->price : rand(50000, 300000);
                    $discountPct = [18, 24, 20, 16, 30][$i % 5];
                    $salePrice = (int)($originalPrice * (1 - $discountPct/100));
                    $soldPct = [68, 52, 73, 41, 25][$i % 5];
                }
                $location = optional($listing->locationModel)->name ?? $listing->location ?? 'Kegalle';
            @endphp
            <a href="/listings/{{ $listing->slug }}" class="k-flash-deal-card">
                <div class="k-flash-deal-img">
                    @if($imgUrl)
                        <img src="{{ $imgUrl }}" alt="{{ $listing->title }}" loading="lazy">
                    @else
                        <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:48px;background:var(--k-surface-2)">🛍️</div>
                    @endif
                    <span class="k-deal-badge-pct">-{{ number_format($discountPct, 0) }}%</span>
                    <button class="k-deal-heart" aria-label="Save">♡</button>
                </div>
                <div class="k-flash-deal-info">
                    <h3 class="k-flash-deal-title">{{ \Illuminate\Support\Str::limit($listing->title, 28) }}</h3>
                    <div class="k-flash-deal-loc">📍 {{ $location }}</div>
                    <div class="k-flash-deal-prices">
                        <span class="k-flash-deal-sale">LKR {{ number_format($salePrice) }}</span>
                        <span class="k-flash-deal-orig">LKR {{ number_format($originalPrice) }}</span>
                    </div>
                    <div class="k-flash-deal-progress">
                        <div class="k-flash-deal-bar"><div class="k-flash-deal-bar-fill" style="width:{{ $soldPct }}%"></div></div>
                        <span class="k-flash-deal-sold">{{ $soldPct }}% Sold</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</section>

{{-- ========== COUPONS ========== --}}
<section class="container" style="padding-bottom:32px">
    <div class="k-coupons-row">
        <div class="k-coupon-card k-coupon-green">
            <div class="k-coupon-icon">🎫</div>
            <div class="k-coupon-info">
                <div class="k-coupon-code">NEWUSER20</div>
                <div class="k-coupon-desc">20% OFF for new users<br>Min. spend LKR 10,000</div>
            </div>
            <button class="k-coupon-copy" onclick="navigator.clipboard.writeText('NEWUSER20');this.textContent='Copied!';setTimeout(()=>this.textContent='Copy Code',2000)">Copy Code</button>
        </div>
        <div class="k-coupon-card k-coupon-blue">
            <div class="k-coupon-icon">🚚</div>
            <div class="k-coupon-info">
                <div class="k-coupon-code">FREEDELIVERY</div>
                <div class="k-coupon-desc">Free delivery on orders<br>above LKR 5,000</div>
            </div>
            <button class="k-coupon-copy" onclick="navigator.clipboard.writeText('FREEDELIVERY');this.textContent='Copied!';setTimeout(()=>this.textContent='Copy Code',2000)">Copy Code</button>
        </div>
        <div class="k-coupon-card k-coupon-red">
            <div class="k-coupon-icon">🎁</div>
            <div class="k-coupon-info">
                <div class="k-coupon-code">MEGADEAL</div>
                <div class="k-coupon-desc">Up to 30% OFF on<br>selected items</div>
            </div>
            <button class="k-coupon-copy" onclick="navigator.clipboard.writeText('MEGADEAL');this.textContent='Copied!';setTimeout(()=>this.textContent='Copy Code',2000)">Copy Code</button>
        </div>
    </div>
</section>

{{-- ========== FILTER + FEATURED DEALS GRID ========== --}}
<section class="container" style="padding-bottom:40px">
    <div class="k-deals-layout">
        {{-- Sidebar --}}
        <aside class="k-deals-sidebar">
            <h3 style="font-size:16px;font-weight:700;margin-bottom:16px">Filter Deals</h3>
            <button class="k-deals-filter-clear" onclick="window.location='/deals'">Clear All</button>

            <div class="k-filter-group">
                <div class="k-filter-label">Discount</div>
                <label class="k-filter-check"><input type="checkbox"> 10% and above</label>
                <label class="k-filter-check"><input type="checkbox"> 25% and above</label>
                <label class="k-filter-check"><input type="checkbox"> 50% and above</label>
            </div>

            <div class="k-filter-group">
                <div class="k-filter-label">Price Range</div>
                <div style="display:flex;gap:8px">
                    <input type="number" placeholder="Min Price" class="k-filter-input">
                    <input type="number" placeholder="Max Price" class="k-filter-input">
                </div>
            </div>

            <div class="k-filter-group">
                <div class="k-filter-label">Location</div>
                <label class="k-filter-check"><input type="checkbox"> Kegalle</label>
                <label class="k-filter-check"><input type="checkbox"> Mawanella</label>
                <label class="k-filter-check"><input type="checkbox"> Warakapola</label>
                <label class="k-filter-check"><input type="checkbox"> Rambukkana</label>
                <label class="k-filter-check"><input type="checkbox"> Aranayake</label>
                <button class="k-filter-more">Show more ▾</button>
            </div>

            <div class="k-filter-group">
                <div class="k-filter-label">Sort by</div>
                <select class="k-filter-select">
                    <option>Newest Deals</option>
                    <option>Most Popular</option>
                    <option>Highest Discount</option>
                    <option>Price: Low to High</option>
                    <option>Price: High to Low</option>
                </select>
            </div>

            <button class="k-btn k-btn-primary" style="width:100%;justify-content:center;margin-top:8px">Apply Filters</button>
        </aside>

        {{-- Main Grid --}}
        <div>
            <div class="k-section-header" style="margin-bottom:16px">
                <h2 class="k-section-title">Featured Deals</h2>
                <select class="k-filter-select" style="width:auto">
                    <option>Newest Deals</option>
                    <option>Most Popular</option>
                    <option>Highest Discount</option>
                </select>
            </div>

            <div class="k-featured-deals-grid">
                @php $gridSource = ($useRealDeals ?? false) && (is_object($featuredDeals) && method_exists($featuredDeals, 'total') ? $featuredDeals->total() > 0 : $featuredDeals->isNotEmpty()) ? $featuredDeals : $latestListings; @endphp
                @foreach($gridSource as $i => $item)
                    @php
                        $listing = ($useRealDeals ?? false) && $item instanceof \App\Models\Deal ? $item->listing : $item;
                        if (!$listing) continue;
                        $img = optional($listing->images->first())->path ?? null;
                        $imgUrl = $img ? asset('storage/'.ltrim($img,'/')) : null;
                        if ($item instanceof \App\Models\Deal) {
                            $originalPrice = $item->original_price;
                            $salePrice = $item->deal_price;
                            $discountPct = $item->discount_percent;
                        } else {
                            $originalPrice = $listing->price > 0 ? $listing->price : rand(50000, 300000);
                            $discountPct = [15, 22, 17, 24, 19, 20, 13, 28, 10, 25, 18, 30][$i % 12];
                            $salePrice = (int)($originalPrice * (1 - $discountPct/100));
                        }
                        $location = optional($listing->locationModel)->name ?? $listing->location ?? 'Kegalle';
                        $store = $item instanceof \App\Models\Deal ? $item->store : ($listing->store ?? null);
                        $storeName = $store->name ?? 'Personal Seller';
                        $storeSlug = $store->slug ?? null;
                    @endphp
                    <a href="/listings/{{ $listing->slug }}" class="k-deal-card">
                        <div class="k-deal-card-img">
                            @if($imgUrl)
                                <img src="{{ $imgUrl }}" alt="{{ $listing->title }}" loading="lazy">
                            @else
                                <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:48px;background:var(--k-surface-2)">🛍️</div>
                            @endif
                            <span class="k-deal-badge-pct">-{{ number_format($discountPct, 0) }}%</span>
                            @if($item instanceof \App\Models\Deal && $item->is_featured)
                                <span style="position:absolute;top:10px;right:46px;background:#FFF8E1;color:#B45309;font-size:10px;font-weight:800;padding:3px 9px;border-radius:12px;box-shadow:0 1px 4px rgba(0,0,0,.12)">⭐ FEATURED</span>
                            @endif
                            <button class="k-deal-heart" aria-label="Save">♡</button>
                        </div>
                        <div class="k-deal-card-info">
                            <h3 class="k-deal-card-title">{{ \Illuminate\Support\Str::limit($listing->title, 30) }}</h3>
                            <div class="k-deal-card-loc">📍 {{ $location }}</div>
                            <div class="k-deal-card-prices">
                                <span class="k-deal-card-sale">LKR {{ number_format($salePrice) }}</span>
                                <span class="k-deal-card-orig">LKR {{ number_format($originalPrice) }}</span>
                            </div>
                            <div class="k-deal-card-store">🏪 @if($storeSlug)<span class="k-deal-store-link" role="link" tabindex="0" style="cursor:pointer" onclick="event.preventDefault();event.stopPropagation();window.location='/store/{{ $storeSlug }}'">{{ $storeName }}</span>@else{{ $storeName }}@endif</div>
                        </div>
                    </a>
                @endforeach
            </div>
            @if(($useRealDeals ?? false) && method_exists($featuredDeals, 'links'))
                <div style="margin-top:16px">{{ $featuredDeals->links() }}</div>
            @endif
        </div>
    </div>
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
    <div class="container" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:20px">
        <div style="display:flex;align-items:center;gap:16px">
            <span style="font-size:32px">✉️</span>
            <div>
                <div style="font-size:18px;font-weight:700;color:#fff">Subscribe to our newsletter</div>
                <div style="font-size:13px;color:rgba(255,255,255,.8)">Get the latest deals, offers and updates delivered to your inbox.</div>
            </div>
        </div>
        <form style="display:flex;gap:8px;flex:1;max-width:400px" onsubmit="event.preventDefault()">
            <input type="email" placeholder="Enter your email" style="flex:1;padding:10px 16px;border:none;border-radius:var(--k-radius-sm);font-size:14px">
            <button type="submit" class="k-btn" style="background:var(--k-accent);color:#fff;white-space:nowrap">Subscribe</button>
        </form>
    </div>
</section>

@endsection

@push('scripts')
<script>
// Countdown timer
(function(){
    function pad(n){return n<10?'0'+n:n}
    var end = new Date();
    end.setHours(23,59,59,0);
    function tick(){
        var now=new Date(),d=Math.max(0,end-now),
            h=Math.floor(d/36e5),m=Math.floor(d%36e5/6e4),s=Math.floor(d%6e4/1e3);
        document.getElementById('cdHours').textContent=pad(h);
        document.getElementById('cdMins').textContent=pad(m);
        document.getElementById('cdSecs').textContent=pad(s);
        var ft=document.getElementById('flashTimer');
        if(ft)ft.textContent=pad(h)+' : '+pad(m)+' : '+pad(s);
    }
    tick();setInterval(tick,1000);
})();
</script>
@endpush
