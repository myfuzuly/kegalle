@extends('layouts.app')

@section('title', 'Events in Kegalle — Concerts, Workshops, Festivals & More')
@section('meta_description', 'Discover amazing events in Kegalle district — concerts, workshops, festivals, exhibitions, sports and community events happening near you.')

@section('content')

{{-- ========== HERO ========== --}}
<section class="ke-hero">
    <div class="container">
        <div class="ke-hero-content">
            <span class="ke-hero-eyebrow">Discover Amazing</span>
            <h1 class="ke-hero-title">Events in <span>Kegalle</span></h1>
            <p class="ke-hero-desc">Find concerts, workshops, festivals, exhibitions and community events happening around you.</p>
            <form class="ke-hero-search" action="/events" method="GET">
                <div class="ke-hero-search-field">
                    <span>🔍</span>
                    <input type="text" name="q" placeholder="Search events, venues or organizers..." value="{{ request('q') }}">
                </div>
                <div class="ke-hero-search-field">
                    <span>📍</span>
                    <select name="location">
                        <option value="">Kegalle, Sri Lanka</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc }}" {{ request('location') === $loc ? 'selected' : '' }}>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="ke-hero-search-btn">Search Events</button>
            </form>
            <div class="ke-hero-badges">
                <span>🎯 Local Events</span>
                <span>✓ Trusted Organizers</span>
                <span>🔒 Secure Booking</span>
                <span>🎫 Easy Cancellation</span>
            </div>
        </div>
        <div class="ke-hero-highlight">
            @if($featuredEvents->isNotEmpty())
                @php $hero = $featuredEvents->first(); @endphp
                <div class="ke-highlight-card">
                    <div class="ke-highlight-head">
                        <span>Upcoming Highlight</span>
                        <em class="ke-tag-featured">Featured</em>
                    </div>
                    <div class="ke-highlight-body">
                        <div class="ke-highlight-date"><strong>{{ $hero->event_date->format('d') }}</strong><small>{{ strtoupper($hero->event_date->format('M')) }}</small></div>
                        <div>
                            <strong>{{ $hero->title }}</strong>
                            <small>📍 {{ $hero->venue }}</small>
                            @if($hero->starts_at && $hero->ends_at)
                                <small>🕐 {{ $hero->starts_at->format('g:i A') }} - {{ $hero->ends_at->format('g:i A') }}</small>
                            @endif
                        </div>
                    </div>
                    <a href="/events/{{ $hero->slug }}" class="ke-highlight-btn">View Details</a>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- ========== CATEGORY SHORTCUTS ========== --}}
<section class="container k-event-cats-section">
    <div class="ke-cats">
        @php
            $evCatIcons = ['🎵','🎪','🎓','⚽','🖼️','👨‍👩‍👧‍👦','🍔','💼','•••'];
            $evCatNames = ['Music & Concerts','Festivals','Workshops','Sports','Exhibitions','Family Events','Food & Drinks','Business','More'];
        @endphp
        @foreach($evCatNames as $i => $catName)
            <a href="/events?category={{ $i + 1 }}" class="ke-cat-item">
                <div class="ke-cat-icon">{{ $evCatIcons[$i] }}</div>
                <span>{{ $catName }}</span>
            </a>
        @endforeach
    </div>
</section>

{{-- ========== MAIN LAYOUT: FILTER + GRID + FEATURED SIDEBAR ========== --}}
<section class="container k-event-main-section">
    <div class="ke-layout">

        {{-- LEFT: FILTERS --}}
        <aside class="ke-filters">
            <button class="ke-filter-toggle" onclick="this.nextElementSibling.classList.toggle('ke-filters-open')">☰ Show Filters</button>
            <div class="ke-filters-body">
            <div class="ke-filters-head"><h3>Filter Events</h3><a href="/events">Clear All</a></div>

            <div class="ke-filter-group">
                <div class="ke-filter-label">Date</div>
                <label class="ke-filter-check"><input type="checkbox" name="date_filter" value="today" {{ request('date_filter') === 'today' ? 'checked' : '' }}> Today</label>
                <label class="ke-filter-check"><input type="checkbox" name="date_filter" value="weekend" {{ request('date_filter') === 'weekend' ? 'checked' : '' }}> This Weekend</label>
                <label class="ke-filter-check"><input type="checkbox" name="date_filter" value="month" {{ request('date_filter') === 'month' ? 'checked' : '' }}> This Month</label>
            </div>

            <div class="ke-filter-group">
                <div class="ke-filter-label">Category</div>
                <select class="ke-filter-select" name="category">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="ke-filter-group">
                <div class="ke-filter-label">Location</div>
                <select class="ke-filter-select" name="location">
                    <option value="">All Locations</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc }}" {{ request('location') === $loc ? 'selected' : '' }}>{{ $loc }}</option>
                    @endforeach
                </select>
            </div>

            <div class="ke-filter-group">
                <div class="ke-filter-label">Price Range</div>
                <label class="ke-filter-check"><input type="checkbox" name="price" value="free" {{ request('price') === 'free' ? 'checked' : '' }}> Free Events</label>
                <label class="ke-filter-check"><input type="checkbox" name="price" value="1-1000" {{ request('price') === '1-1000' ? 'checked' : '' }}> LKR 1 - 1,000</label>
                <label class="ke-filter-check"><input type="checkbox" name="price" value="1000-5000" {{ request('price') === '1000-5000' ? 'checked' : '' }}> LKR 1,000 - 5,000</label>
                <label class="ke-filter-check"><input type="checkbox" name="price" value="5000+" {{ request('price') === '5000+' ? 'checked' : '' }}> Above LKR 5,000</label>
            </div>

            <div class="ke-filter-group">
                <div class="ke-filter-label">Event Type</div>
                <label class="ke-filter-check"><input type="checkbox"> Online</label>
                <label class="ke-filter-check"><input type="checkbox"> Offline</label>
                <label class="ke-filter-check"><input type="checkbox"> Hybrid</label>
            </div>

            <button class="k-btn k-btn-primary k-btn-center k-event-filter-btn">Apply Filters</button>
            </div>
        </aside>

        {{-- CENTER: EVENT GRID --}}
        <div class="ke-center">
            <div class="ke-grid-head">
                <h2>Upcoming Events</h2>
                <div class="ke-grid-sort">
                    <label>Sort by:</label>
                    <select onchange="window.location.href='/events?sort='+this.value+'&{{ http_build_query(request()->except('sort','page')) }}'">
                        <option value="date" {{ request('sort','date') === 'date' ? 'selected' : '' }}>Date: Soonest</option>
                        <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </div>
            </div>

            @php
                if (!function_exists('guessEventIcon')) { function guessEventIcon($title) {
                    $t = strtolower($title);
                    if (preg_match('/music|concert|band|sing/', $t)) return '🎵';
                    if (preg_match('/food|carnival|cook|culinary/', $t)) return '🍔';
                    if (preg_match('/festival|fair|vesak|perahera/', $t)) return '🎪';
                    if (preg_match('/cricket|sport|tournament|marathon/', $t)) return '🏏';
                    if (preg_match('/workshop|class|masterclass|training/', $t)) return '🎓';
                    if (preg_match('/business|entrepreneur|summit|meeting/', $t)) return '💼';
                    if (preg_match('/photo|art|exhibit|gallery/', $t)) return '📸';
                    if (preg_match('/yoga|wellness|health|retreat/', $t)) return '🧘';
                    if (preg_match('/kid|child|family|summer/', $t)) return '👨‍👩‍👧‍👦';
                    if (preg_match('/digital|marketing|tech|IT/', $t)) return '💻';
                    if (preg_match('/women|empower/', $t)) return '👩‍💼';
                    if (preg_match('/buddhist|temple|religious/', $t)) return '🙏';
                    return '📅';
                } }
            @endphp
            <div class="ke-event-grid">
                @forelse($events as $ev)
                    @php
                        $catName = $ev->category->name ?? '';
                        $evIcons = ['Music & Concerts'=>'🎵','Workshops'=>'💻','Food & Drinks'=>'🍔','Sports'=>'🏏','Festivals'=>'🎪','Business'=>'💼','Family Events'=>'🧒','Exhibitions'=>'🖼️'];
                        $evIcon = $catName ? ($evIcons[$catName] ?? guessEventIcon($ev->title)) : guessEventIcon($ev->title);
                        $evTime = $ev->starts_at && $ev->ends_at ? $ev->starts_at->format('g:i A') . ' - ' . $ev->ends_at->format('g:i A') : 'TBA';
                    @endphp
                    <div class="ke-event-card">
                        <div class="ke-event-card-img">
                            <div class="ke-event-card-placeholder">{{ $evIcon }}</div>
                            @if($ev->is_featured)<span class="ke-event-tag ke-event-tag-featured">Featured</span>@endif
                            <div class="ke-event-card-date"><strong>{{ $ev->event_date->format('d') }}</strong><small>{{ strtoupper($ev->event_date->format('M')) }}</small></div>
                        </div>
                        <div class="ke-event-card-body">
                            <h3 class="ke-event-card-title">{{ $ev->title }}</h3>
                            <div class="ke-event-card-meta">📍 {{ $ev->venue }}</div>
                            <div class="ke-event-card-meta">🕐 {{ $evTime }}</div>
                            <div class="ke-event-card-price">{{ $ev->is_free || $ev->price == 0 ? 'Free' : 'LKR '.number_format($ev->price) }}</div>
                            <div class="ke-event-card-actions">
                                <button class="ke-action-icon" title="Save"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg></button>
                                <button class="ke-action-icon" title="Share"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg></button>
                                <a href="/events/{{ $ev->slug }}" class="ke-view-btn">View Details</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="k-event-empty-state">
                        <div class="k-event-empty-icon">📅</div>
                        <h3 class="k-event-empty-title">No upcoming events found</h3>
                        <p class="k-text-muted k-event-empty-desc">Check back soon for new events in the Kegalle district.</p>
                        <a href="/events" class="ke-view-btn k-event-empty-btn">View All Events</a>
                    </div>
                @endforelse
            </div>

            @if($events instanceof \Illuminate\Pagination\LengthAwarePaginator && $events->hasPages())
                <div class="k-event-pagination">
                    {{ $events->links() }}
                </div>
            @endif
        </div>

        {{-- RIGHT: FEATURED + LOCATIONS --}}
        <aside class="ke-sidebar-right">
            @if($featuredEvents->isNotEmpty())
            <div class="ke-sidebar-section">
                <div class="ke-sidebar-head"><h3>Featured Events</h3><a href="/events?featured=1">View all</a></div>
                @foreach($featuredEvents->take(4) as $fev)
                    @php
                        $fevCat = $fev->category->name ?? '';
                        $fevIcons = ['Music & Concerts'=>'🎵','Workshops'=>'💻','Food & Drinks'=>'🍔','Sports'=>'🏏','Festivals'=>'🎪','Business'=>'💼','Family Events'=>'🧒'];
                        $fevIcon = $fevCat ? ($fevIcons[$fevCat] ?? guessEventIcon($fev->title)) : guessEventIcon($fev->title);
                    @endphp
                    <a href="/events/{{ $fev->slug }}" class="ke-featured-item">
                        <div class="ke-featured-thumb">{{ $fevIcon }}</div>
                        <div>
                            <strong>{{ \Illuminate\Support\Str::limit($fev->title, 24) }}</strong>
                            <small>{{ $fev->event_date->format('d M Y') }}</small>
                            <small class="k-event-price-highlight">{{ $fev->is_free || $fev->price == 0 ? 'Free' : 'LKR '.number_format($fev->price) }}</small>
                        </div>
                    </a>
                @endforeach
            </div>
            @endif

            <div class="ke-sidebar-section">
                <div class="ke-sidebar-head"><h3>Popular Locations</h3><a href="/locations">View all</a></div>
                @foreach($locations as $loc)
                    <a href="/events?location={{ $loc }}" class="ke-loc-item">
                        <span>📍 {{ $loc }}</span>
                    </a>
                @endforeach
            </div>

            <div class="ke-promo-card">
                <h3>Want to Promote Your Event?</h3>
                <p>Get more visibility and sell more tickets.</p>
                <a href="/contact-us" class="ke-promo-btn">Post Your Event</a>
            </div>
        </aside>
    </div>
</section>

{{-- ========== BROWSE BY MONTH ========== --}}
<section class="container k-event-main-section">
    <h2 class="k-event-browse-title">Browse Events by Month</h2>
    <div class="ke-months">
        @php
            $months = [];
            for ($m = 0; $m < 4; $m++) {
                $d = now()->addMonths($m);
                $months[] = ['label' => $d->format('F Y'), 'date' => $d];
            }
        @endphp
        @foreach($months as $mo)
            <a href="/events/calendar/{{ $mo['date']->year }}/{{ $mo['date']->month }}" class="ke-month-card">
                <div class="ke-month-icon">📅</div>
                <strong>{{ $mo['label'] }}</strong>
            </a>
        @endforeach
        <a href="/events" class="ke-month-card">
            <div class="ke-month-icon">→</div>
            <strong>View Calendar</strong>
        </a>
    </div>
</section>

{{-- ========== TRUST BADGES ========== --}}
<section class="ke-trust">
    <div class="container">
        <div class="ke-trust-grid">
            <div class="ke-trust-item"><span class="ke-trust-icon">✓</span><div><strong>Trusted Organizers</strong><br><small>Verified local event organizers</small></div></div>
            <div class="ke-trust-item"><span class="ke-trust-icon">⚡</span><div><strong>Instant Confirmation</strong><br><small>Get instant confirmation for most events</small></div></div>
            <div class="ke-trust-item"><span class="ke-trust-icon">↩</span><div><strong>Easy Refunds</strong><br><small>Cancel easily and get refunds</small></div></div>
            <div class="ke-trust-item"><span class="ke-trust-icon">💬</span><div><strong>24/7 Support</strong><br><small>We're here to help you anytime</small></div></div>
        </div>
    </div>
</section>

{{-- ========== NEWSLETTER ========== --}}
<section class="ke-newsletter">
    <div class="container ke-newsletter-inner">
        <div class="ke-newsletter-info">
            <span class="ke-newsletter-icon">✉️</span>
            <div>
                <div class="ke-newsletter-heading">Subscribe to our newsletter</div>
                <div class="ke-newsletter-sub">Get the latest events, offers and updates delivered to your inbox.</div>
            </div>
        </div>
        <form class="ke-newsletter-form" onsubmit="event.preventDefault()">
            <input type="email" placeholder="Enter your email" class="ke-newsletter-email">
            <button type="submit" class="k-btn ke-newsletter-btn">Subscribe</button>
        </form>
    </div>
</section>

@endsection
