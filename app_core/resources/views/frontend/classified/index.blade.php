@extends('layouts.app')

@section('title','Classified Ads in Kegalle — Buy, Sell, Rent & Exchange · Kegalle Marketplace')
@section('meta_description','Post and browse free personal classified ads in Kegalle — buy, sell, rent or exchange items quickly with local buyers and sellers near you.')
@section('canonical', url('/classified'))

@section('content')
@php
    $palette = ['linear-gradient(135deg,#E3F2FD,#BBDEFB)','linear-gradient(135deg,#E8F5E9,#C8E6C9)','linear-gradient(135deg,#FFF3E0,#FFE0B2)','linear-gradient(135deg,#F3E5F5,#E1BEE7)','linear-gradient(135deg,#FFF8E1,#FFF176)','linear-gradient(135deg,#E0F2F1,#B2DFDB)'];
    $totalAll = $listings->total();
    $countFor = fn($type) => \App\Models\Listing::published()->where('type', 'classified')->where('ad_type', $type)->count();
@endphp

<!-- Hero -->
<section class="classified-hero">
    <div class="classified-hero-inner">
        <div>
            <span class="k-tag" style="background:rgba(255,255,255,.15);color:#fff;margin-bottom:12px;display:inline-flex">📌 Classified Section</span>
            <h1>Personal Classified Ads</h1>
            <p>Post personal buy, sell, wanted or exchange ads quickly and for free.</p>
            <div style="display:flex;gap:10px;margin-top:16px">
                <a href="/dashboard/listings/create?type=classified" class="k-btn k-btn-primary k-btn-lg">+ Post Classified Ad</a>
                <a href="/listings" class="k-btn k-btn-lg" style="background:rgba(255,255,255,.15);color:#fff;border:1.5px solid rgba(255,255,255,.3)">Browse All</a>
            </div>
            <div class="classified-stats mt-20">
                <div class="classified-stat"><strong>{{ number_format($totalAll) }}+</strong><span>Classified Ads</span></div>
                <div class="classified-stat"><strong>{{ number_format(\App\Models\Listing::published()->where('type','classified')->where('created_at','>=',now()->subDays(7))->count()) }}+</strong><span>New This Week</span></div>
                <div class="classified-stat"><strong>Free</strong><span>To Post</span></div>
            </div>
        </div>
        <div style="background:rgba(255,255,255,.08);border:1.5px solid rgba(255,255,255,.15);border-radius:var(--k-radius-xl);padding:24px;min-width:260px">
            <h3 style="font-family:var(--font-display);font-size:16px;font-weight:700;margin-bottom:14px">Quick Post</h3>
            <form action="/dashboard/listings/create" method="GET">
                <select name="category" class="k-form-select" style="margin-bottom:10px">
                    <option value="">Select Category</option>
                    @foreach($categories->take(8) as $category)
                        <option value="{{ $category->slug }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="ad_type" class="k-form-select" style="margin-bottom:10px">
                    <option value="">Ad Type</option>
                    <option value="sale">For Sale</option>
                    <option value="wanted">Wanted</option>
                    <option value="rent">For Rent</option>
                    <option value="exchange">Exchange</option>
                </select>
                <button type="submit" class="k-btn k-btn-primary w-full" style="justify-content:center;border:none;cursor:pointer">Start Posting →</button>
            </form>
        </div>
    </div>
</section>

<!-- Category tabs -->
<div class="classified-tabs">
    <div class="classified-tabs-inner">
        <a href="/classified" class="classified-tab {{ !request('ad_type') ? 'active' : '' }}">All <span class="cnt">{{ number_format($totalAll) }}</span></a>
        <a href="/classified?ad_type=sale" class="classified-tab {{ request('ad_type')==='sale' ? 'active' : '' }}">For Sale <span class="cnt">{{ number_format($countFor('sale')) }}</span></a>
        <a href="/classified?ad_type=wanted" class="classified-tab {{ request('ad_type')==='wanted' ? 'active' : '' }}">Wanted <span class="cnt">{{ number_format($countFor('wanted')) }}</span></a>
        <a href="/classified?ad_type=rent" class="classified-tab {{ request('ad_type')==='rent' ? 'active' : '' }}">For Rent <span class="cnt">{{ number_format($countFor('rent')) }}</span></a>
        <a href="/classified?ad_type=exchange" class="classified-tab {{ request('ad_type')==='exchange' ? 'active' : '' }}">Exchange <span class="cnt">{{ number_format($countFor('exchange')) }}</span></a>
        <a href="/classified?ad_type=free" class="classified-tab {{ request('ad_type')==='free' ? 'active' : '' }}">Free <span class="cnt">{{ number_format($countFor('free')) }}</span></a>
    </div>
</div>

<div class="container" style="padding-top:28px;padding-bottom:48px">
    <div class="k-layout-sidebar">
        <!-- Sidebar -->
        <div class="k-filter-sidebar">
            <div class="k-filter-header"><span class="k-filter-title">🔽 Filter Ads</span><a href="/classified" class="k-filter-clear">Reset</a></div>
            <form method="GET" action="/classified">
                <div class="k-filter-section">
                    <div class="k-filter-section-title">Category <span>−</span></div>
                    <div class="k-filter-option"><input type="checkbox" checked disabled><label>All Categories <span class="cnt">{{ number_format($totalAll) }}</span></label></div>
                    @foreach($categories->take(8) as $category)
                        <div class="k-filter-option">
                            <input type="checkbox" name="categories[]" value="{{ $category->slug }}" id="ccat-{{ $category->slug }}" @checked(in_array($category->slug, request('categories', [])))>
                            <label for="ccat-{{ $category->slug }}">{{ $category->name }} <span class="cnt">{{ $category->listings_count ?? 0 }}</span></label>
                        </div>
                    @endforeach
                </div>
                <div class="k-filter-section">
                    <div class="k-filter-section-title">Ad Type <span>−</span></div>
                    <div class="k-filter-option"><input type="radio" name="ad_type" value="" id="at-all" @checked(!request('ad_type'))><label for="at-all">All Types</label></div>
                    <div class="k-filter-option"><input type="radio" name="ad_type" value="sale" id="at-sale" @checked(request('ad_type')==='sale')><label for="at-sale">For Sale</label></div>
                    <div class="k-filter-option"><input type="radio" name="ad_type" value="wanted" id="at-wanted" @checked(request('ad_type')==='wanted')><label for="at-wanted">Wanted</label></div>
                    <div class="k-filter-option"><input type="radio" name="ad_type" value="rent" id="at-rent" @checked(request('ad_type')==='rent')><label for="at-rent">For Rent</label></div>
                    <div class="k-filter-option"><input type="radio" name="ad_type" value="exchange" id="at-exchange" @checked(request('ad_type')==='exchange')><label for="at-exchange">Exchange</label></div>
                    <div class="k-filter-option"><input type="radio" name="ad_type" value="free" id="at-free" @checked(request('ad_type')==='free')><label for="at-free">Free</label></div>
                </div>
                <div class="k-filter-section">
                    <div class="k-filter-section-title">Location <span>−</span></div>
                    @foreach(($locations ?? [])->take(5) as $location)
                        <div class="k-filter-option">
                            <input type="checkbox" name="locations[]" value="{{ $location->id }}" id="cloc-{{ $location->id }}" @checked(in_array($location->id, request('locations', [])))>
                            <label for="cloc-{{ $location->id }}">{{ $location->name }} <span class="cnt">{{ $location->listings_count ?? 0 }}</span></label>
                        </div>
                    @endforeach
                </div>
                <div class="k-filter-section" style="border-bottom:none;padding-bottom:0;margin-bottom:0">
                    <div class="k-filter-section-title">Price Range</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="k-form-control" style="padding:8px 10px;font-size:12px">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="k-form-control" style="padding:8px 10px;font-size:12px">
                    </div>
                </div>
                <button type="submit" class="k-btn k-btn-primary w-full mt-16" style="justify-content:center;border:none;cursor:pointer">Apply Filters</button>
            </form>
        </div>

        <!-- Ads list -->
        <div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
                <span style="font-size:14px;color:var(--k-text-secondary)">Showing <strong>{{ $listings->firstItem() ?? 0 }}–{{ $listings->lastItem() ?? 0 }}</strong> of <strong>{{ $listings->total() }} ads</strong></span>
                <form method="GET" style="display:flex;gap:8px;align-items:center">
                    @foreach(request()->except('sort','page') as $k=>$v)
                        @if(is_array($v)) @foreach($v as $vv)<input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">@endforeach @else <input type="hidden" name="{{ $k }}" value="{{ $v }}"> @endif
                    @endforeach
                    <span style="font-size:13px;color:var(--k-text-secondary)">Sort:</span>
                    <select name="sort" class="k-form-select" style="min-width:140px;padding:7px 28px 7px 10px;font-size:13px" onchange="this.form.submit()">
                        <option value="">Newest First</option>
                        <option value="price_low" @selected(request('sort')=='price_low')>Price: Low → High</option>
                        <option value="price_high" @selected(request('sort')=='price_high')>Price: High → Low</option>
                        <option value="popular" @selected(request('sort')=='popular')>Most Relevant</option>
                    </select>
                </form>
            </div>

            <div style="display:flex;flex-direction:column;gap:12px">
                @forelse($listings as $listing)
                    @php
                        $img = optional($listing->images->first())->path ?? $listing->image ?? null;
                        $imgUrl = $img ? asset('storage/'.ltrim($img,'/')) : null;
                        $bg = $palette[$loop->index % count($palette)];
                        $location = optional($listing->locationModel)->name ?? $listing->location ?? 'Kegalle';
                        $isFree = ($listing->price ?? 0) == 0 && ($listing->ad_type ?? '') === 'free';
                        $isFavorited = auth()->check() && \App\Models\Favorite::where('user_id', auth()->id())->where('listing_id', $listing->id)->exists();
                        $tagClass = match($listing->ad_type ?? 'sale') {
                            'rent' => 'k-tag-rent',
                            'wanted' => 'k-tag-wanted',
                            'free' => 'k-tag-free',
                            default => 'k-tag-new',
                        };
                    @endphp
                    <div class="classified-list-card" onclick="window.location='/listings/{{ $listing->slug }}'">
                        <div class="classified-list-img" style="{{ $imgUrl ? '' : 'background:'.$bg }}">
                            @if($imgUrl)
                                <img src="{{ $imgUrl }}" alt="{{ $listing->title }}" style="width:100%;height:100%;object-fit:cover">
                            @else
                                {{ optional($listing->category)->icon ?: '🛍️' }}
                            @endif
                        </div>
                        <div class="classified-list-body">
                            <div style="display:flex;gap:6px;margin-bottom:8px">
                                <span class="k-tag {{ $tagClass }}">{{ ucfirst($listing->ad_type ?? 'sale') }}</span>
                                @if($listing->is_featured)<span class="k-tag k-tag-featured">Featured</span>@endif
                                @if($listing->is_urgent ?? false)<span class="classified-urgent">🔥 Urgent</span>@endif
                            </div>
                            <div class="classified-list-title">{{ $listing->title }}</div>
                            <div class="classified-list-desc">{{ \Illuminate\Support\Str::limit($listing->description ?? '', 140) }}</div>
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
                                    <div class="k-price k-price-lg" style="color:#2E7D32">FREE</div>
                                @elseif(($listing->ad_type ?? '') === 'wanted')
                                    <div class="k-price k-price-lg">Budget:</div>
                                    <div style="font-size:14px;font-weight:700;color:var(--k-primary)">{{ ($listing->price ?? 0) > 0 ? 'LKR '.number_format($listing->price).'+' : 'Negotiable' }}</div>
                                @else
                                    <div class="k-price k-price-lg">{{ ($listing->price ?? 0) > 0 ? 'LKR '.number_format($listing->price) : 'Contact' }}</div>
                                    @if(($listing->ad_type ?? '') === 'rent')<div style="font-size:11px;color:var(--k-text-muted)">/month</div>@endif
                                @endif
                            </div>
                            <div style="display:flex;flex-direction:column;gap:6px">
                                <a href="/listings/{{ $listing->slug }}" class="k-btn k-btn-primary k-btn-sm">Contact</a>
                                <button class="js-favorite-btn {{ $isFavorited ? 'is-favorited' : '' }}" style="border:none;background:none;cursor:pointer;font-size:18px" data-listing-id="{{ $listing->id }}" data-authed="{{ auth()->check() ? '1' : '0' }}" aria-label="{{ $isFavorited ? 'Remove from favorites' : 'Save ad' }}">{{ $isFavorited ? '❤️' : '🤍' }}</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="padding:60px 20px;text-align:center;background:var(--k-surface);border:1px dashed var(--k-border);border-radius:var(--k-radius-lg)">
                        <h3 style="font-family:var(--font-display);margin-bottom:8px">No classified ads found</h3>
                        <p style="color:var(--k-text-secondary)">Try changing filters or be the first to post one!</p>
                    </div>
                @endforelse
            </div>

            {{ $listings->links('vendor.pagination.k-theme') }}
        </div>
    </div>
</div>
@endsection
