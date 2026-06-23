@extends('layouts.app')

@section('title','Kegalle - Buy, Sell & Discover')

@section('content')
@php
    $classifiedItems = ($latestClassified ?? collect())->take(5);
    $blogItems = ($blogs ?? collect())->take(5);
    $palette = ['#E8F5E9','#E3F2FD','#FCE4EC','#FFF8E1','#F3E8FD','#E0F2F1'];
@endphp

<!-- Hero -->
<section class="home-hero">
    <div class="home-hero-photo"></div>
    <div class="home-hero-inner">
        <div>
            <h1>Buy, Sell & Discover<br>the best in <span>Kegalle</span></h1>
            <p>Find great deals on products, vehicles, properties and more from trusted sellers and local stores.</p>
            <div class="hero-cta-group">
                <a href="/listings" class="k-btn k-btn-primary k-btn-lg">Browse Listings</a>
                <a href="/register" class="k-btn k-btn-outline k-btn-lg">Post Free Ad</a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat"><strong>10K+</strong><span>Happy Users</span></div>
                <div class="hero-stat"><strong>5K+</strong><span>Active Listings</span></div>
                <div class="hero-stat"><strong>300+</strong><span>Trusted Stores</span></div>
                <div class="hero-stat"><strong>100%</strong><span>Local Support</span></div>
            </div>
        </div>
        <div class="hero-actions-card">
            <h3>What would you like to do?</h3>
            <div class="hero-action-grid">
                <a href="/register?account_type=store" class="hero-action-btn selected">
                    <div class="hero-action-icon">🏪</div>
                    <strong>Store / Business</strong>
                    <span>Sell products or services with your store</span>
                </a>
                <a href="/register?account_type=user" class="hero-action-btn">
                    <div class="hero-action-icon">👤</div>
                    <strong>Personal / Classified</strong>
                    <span>Post ads for personal use or services</span>
                </a>
            </div>
        </div>
    </div>
</section>

<div class="k-main">
    <div class="k-layout">
        <!-- Main Content -->
        <div>
            <!-- Ad Banner -->
            <div class="k-ad-banner mt-20">
                <span class="k-ad-label">ADS 1200×120</span>
                <div class="k-ad-text">
                    <strong>Grow your business with premium advertising</strong>
                    <span>Reach thousands of local buyers in Kegalle</span>
                </div>
                <a href="/dashboard/membership" class="k-btn k-btn-primary k-btn-sm" style="flex-shrink:0">Advertise Now</a>
            </div>

            <!-- Categories -->
            <div class="k-section">
                <div class="k-section-header">
                    <h2 class="k-section-title">Browse Categories</h2>
                    <a href="/categories" class="k-section-link">View all categories →</a>
                </div>
                <div class="k-cats">
                    @foreach($categories->take(8) as $category)
                        <a href="/listings?categories[]={{ $category->slug }}" class="k-cat-pill">
                            <span class="icon">{{ $category->icon ?: '🛒' }}</span>
                            <span class="label">{{ $category->name }}</span>
                            <span class="count">{{ $category->listings_count ?? 0 }} ads</span>
                        </a>
                    @endforeach
                    <a href="/listings" class="k-cat-pill"><span class="icon">⋯</span><span class="label">More</span><span class="count">See all</span></a>
                </div>
            </div>

            <!-- Featured Ads -->
            <div class="k-section">
                <div class="k-section-header">
                    <h2 class="k-section-title">Featured Ads</h2>
                    <a href="/listings?featured=1" class="k-section-link">View all featured →</a>
                </div>
                <div class="k-grid-4">
                    @forelse($featuredListings->take(4) as $listing)
                        @include('frontend.listings.card',['listing'=>$listing])
                    @empty
                        <div style="padding:30px;text-align:center;color:var(--k-text-tertiary)">No featured ads found.</div>
                    @endforelse
                </div>
            </div>

            <!-- Latest Ads -->
            <div class="k-section">
                <div class="k-section-header">
                    <h2 class="k-section-title">Latest Ads</h2>
                    <a href="/listings" class="k-section-link">View all ads →</a>
                </div>
                <div class="k-grid-5">
                    @forelse($latestListings->take(5) as $listing)
                        @include('frontend.listings.card',['listing'=>$listing])
                    @empty
                        <div style="padding:30px;text-align:center;color:var(--k-text-tertiary)">No latest ads found.</div>
                    @endforelse
                </div>
            </div>

            <!-- CTA Banner -->
            <div class="k-cta-banner mb-20">
                <div>
                    <h3>Stand Out with Featured Ads</h3>
                    <p>Get more views and sell faster with premium placement.</p>
                </div>
                <a href="/dashboard/membership" class="k-btn k-btn-white k-btn-lg">Feature Your Ad</a>
            </div>

            <!-- Explore Section -->
            <div class="k-section">
                <div class="k-section-header">
                    <div>
                        <h2 class="k-section-title">Explore in <span>Kegalle</span></h2>
                        <p class="text-secondary text-sm mt-8">Discover the best of Kegalle – culture, nature, history and more.</p>
                    </div>
                    <a href="#" class="k-section-link">View all →</a>
                </div>
                <div class="k-explore-grid">
                    <div class="k-explore-card">
                        <div class="k-explore-placeholder" style="background:linear-gradient(135deg,#388E3C,#1B5E20)"><div style="font-size:40px">🚶</div></div>
                        <div class="k-explore-overlay">
                            <h4>Activities</h4>
                            <ul><li>Hiking & Trekking</li><li>Water Activities</li><li>Camping & Outdoor</li></ul>
                            <a href="#" class="k-explore-link">Explore Activities →</a>
                        </div>
                    </div>
                    <div class="k-explore-card">
                        <div class="k-explore-placeholder" style="background:linear-gradient(135deg,#0288D1,#01579B)"><div style="font-size:40px">🐘</div></div>
                        <div class="k-explore-overlay">
                            <h4>Tourist Places</h4>
                            <ul><li>Pinnawala Elephant Orphanage</li><li>Bopath Ella</li><li>Bellena Cave</li></ul>
                            <a href="#" class="k-explore-link">Explore Places →</a>
                        </div>
                    </div>
                    <div class="k-explore-card">
                        <div class="k-explore-placeholder" style="background:linear-gradient(135deg,#2E7D32,#558B2F)"><div style="font-size:40px">🌊</div></div>
                        <div class="k-explore-overlay">
                            <h4>Natural Resources</h4>
                            <ul><li>Samanala Reserve Forest</li><li>Kegalle Reservoir</li><li>Rivers & Waterfalls</li></ul>
                            <a href="#" class="k-explore-link">Explore Nature →</a>
                        </div>
                    </div>
                    <div class="k-explore-card">
                        <div class="k-explore-placeholder" style="background:linear-gradient(135deg,#5D4037,#4E342E)"><div style="font-size:40px">🏛️</div></div>
                        <div class="k-explore-overlay">
                            <h4>Historic Places</h4>
                            <ul><li>Old Kegalle Dutch Fort</li><li>Warakapola</li><li>Archaeological Sites</li></ul>
                            <a href="#" class="k-explore-link">Explore History →</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popular Stores -->
            <div class="k-section">
                <div class="k-section-header">
                    <h2 class="k-section-title">Popular Stores</h2>
                    <a href="/stores" class="k-section-link">View all stores →</a>
                </div>
                <div class="k-grid-3">
                    @forelse($featuredStores->take(5) as $store)
                        @php $storeBg = $palette[$loop->index % count($palette)]; @endphp
                        <a href="/store/{{ $store->slug }}" class="k-store-card">
                            <div class="k-store-logo" style="background:{{ $storeBg }}">
                                @if($store->logo)
                                    <img src="{{ asset('storage/'.ltrim($store->logo,'/')) }}" alt="{{ $store->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit">
                                @else
                                    <span style="font-weight:700;color:var(--k-primary)">{{ strtoupper(substr($store->name,0,1)) }}</span>
                                @endif
                            </div>
                            <div>
                                <div class="k-store-name">{{ $store->name }}</div>
                                <div class="k-stars">★★★★★ <span>(4.8 · {{ $store->listings_count ?? 0 }} products)</span></div>
                                <div class="k-store-meta mt-8">{{ $store->listings_count ?? 0 }}+ Products · {{ $store->city ?? 'Kegalle' }}</div>
                            </div>
                        </a>
                    @empty
                        <div style="padding:30px;text-align:center;color:var(--k-text-tertiary)">No stores found.</div>
                    @endforelse
                    <a href="/register?account_type=store" class="k-store-card" style="background:var(--k-primary-xlight);border-color:#C8E6C9;justify-content:center;text-align:center;flex-direction:column">
                        <div style="font-size:28px;margin-bottom:8px">+</div>
                        <div class="k-store-name" style="color:var(--k-primary)">Add Your Store</div>
                        <div class="k-store-meta">Start selling today</div>
                    </a>
                </div>
            </div>

            <!-- Stats -->
            <div class="k-stats-bar">
                <div class="k-stat-item"><div class="k-stat-number">10K+</div><div class="k-stat-label">Happy Users</div></div>
                <div class="k-stat-item"><div class="k-stat-number">5K+</div><div class="k-stat-label">Active Listings</div></div>
                <div class="k-stat-item"><div class="k-stat-number">300+</div><div class="k-stat-label">Trusted Stores</div></div>
                <div class="k-stat-item"><div class="k-stat-number">100%</div><div class="k-stat-label">Local Support</div></div>
            </div>
        </div>

        <!-- Sidebar -->
        <div style="margin-top:20px">
            <!-- Classified Section -->
            <div class="k-classified-sidebar mb-20">
                <div class="k-classified-sidebar-header">
                    <span class="k-classified-sidebar-title">Classified Section <span class="k-tag k-tag-new" style="font-size:10px;margin-left:4px">New</span></span>
                    <a href="/classified" class="k-section-link" style="font-size:12px">View all →</a>
                </div>
                <div style="padding:14px 16px;background:var(--k-primary-xlight);border-bottom:1px solid var(--k-border)">
                    <p style="font-size:13px;color:var(--k-text-secondary);margin-bottom:10px">Post personal ads or find what you need in your area.</p>
                    <a href="/classified" class="k-btn k-btn-primary w-full" style="justify-content:center">Explore Classifieds</a>
                </div>
                <div class="k-classified-sidebar-header" style="padding-top:14px">
                    <span style="font-size:13px;font-weight:700;color:var(--k-text-primary)">Latest Classified Ads</span>
                    <a href="/classified" class="k-section-link" style="font-size:11px">View all →</a>
                </div>
                @forelse($classifiedItems as $item)
                    @php
                        $img = optional($item->images->first())->path ?? $item->image ?? null;
                        $imgUrl = $img ? asset('storage/'.ltrim($img,'/')) : null;
                        $miniBg = $palette[($item->id ?? 0) % count($palette)];
                    @endphp
                    <a href="/listings/{{ $item->slug }}" class="k-classified-item">
                        @if($imgUrl)
                            <div class="k-classified-thumb" style="overflow:hidden"><img src="{{ $imgUrl }}" alt="{{ $item->title }}" style="width:100%;height:100%;object-fit:cover"></div>
                        @else
                            <div class="k-classified-thumb" style="background:{{ $miniBg }}">🛍️</div>
                        @endif
                        <div class="k-classified-info">
                            <h4>{{ $item->title }}</h4>
                            <div class="loc">📍 {{ optional($item->locationModel)->name ?? $item->location ?? 'Kegalle' }}</div>
                            <div class="k-classified-price">{{ ($item->price ?? 0) > 0 ? 'LKR '.number_format($item->price) : 'Contact Seller' }}</div>
                        </div>
                        <div class="k-classified-time">{{ $item->created_at?->diffForHumans() ?? '' }}</div>
                    </a>
                @empty
                    <p style="padding:14px 16px;font-size:13px;color:var(--k-text-tertiary)">No classifieds yet.</p>
                @endforelse
            </div>

            <!-- Premium Ad Space -->
            <div style="background:var(--k-gold-light);border:1.5px dashed var(--k-gold);border-radius:var(--k-radius-lg);padding:20px;text-align:center;margin-bottom:20px">
                <div style="font-size:10px;font-weight:700;color:#8D6E63;margin-bottom:6px">ADS 300×250</div>
                <div style="font-size:14px;font-weight:700;color:#5D4037">Premium Ad Space</div>
                <a href="/dashboard/membership" class="k-btn k-btn-sm" style="background:#F9A825;color:#5D4037;margin-top:10px">Advertise Here</a>
            </div>

            <!-- Latest Blog -->
            <div>
                <div class="k-section-header">
                    <h3 style="font-family:var(--font-display);font-size:15px;font-weight:700">Latest Blog</h3>
                    <a href="/blog" class="k-section-link" style="font-size:12px">View all →</a>
                </div>
                <div style="display:flex;flex-direction:column;gap:10px">
                    @forelse($blogItems as $post)
                        <a href="/blog/{{ $post->slug }}" class="k-blog-card">
                            @if($post->image)
                                <div class="k-blog-thumb" style="overflow:hidden"><img src="{{ asset('storage/'.ltrim($post->image,'/')) }}" alt="{{ $post->title }}" style="width:100%;height:100%;object-fit:cover"></div>
                            @else
                                <div class="k-blog-thumb">📰</div>
                            @endif
                            <div>
                                <div class="k-blog-title">{{ $post->title }}</div>
                                <div class="k-blog-date">{{ optional($post->published_at ?? $post->created_at)->format('M d, Y') }}</div>
                            </div>
                        </a>
                    @empty
                        @foreach(['Top 10 Places to Visit in Kegalle' => 'May 10, 2025','Best Local Restaurants in Kegalle' => 'May 5, 2025','Kegalle Travel Guide 2025' => 'April 28, 2025'] as $title => $date)
                            <a href="#" class="k-blog-card">
                                <div class="k-blog-thumb">📰</div>
                                <div><div class="k-blog-title">{{ $title }}</div><div class="k-blog-date">{{ $date }}</div></div>
                            </a>
                        @endforeach
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
