@extends('layouts.app')

@section('title', ($event ? $event->title : 'Event Not Found') . ' — Kegalle Events')
@if($event)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($event->description ?? ''), 155) ?: $event->title.' — '.($event->event_date ? $event->event_date->format('d M Y') : '').' at '.($event->venue ?? 'Kegalle').'. Find events in Kegalle district.')
@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Event',
    'name' => $event->title,
    'description' => \Illuminate\Support\Str::limit(strip_tags($event->description ?? ''), 300),
    'startDate' => $event->starts_at ? $event->starts_at->toIso8601String() : ($event->event_date ? $event->event_date->toDateString() : null),
    'endDate' => $event->ends_at ? $event->ends_at->toIso8601String() : null,
    'eventStatus' => 'https://schema.org/EventScheduled',
    'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
    'location' => [
        '@type' => 'Place',
        'name' => $event->venue ?? 'Kegalle',
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => $event->location ?? 'Kegalle',
            'addressCountry' => 'LK',
        ],
    ],
    'organizer' => [
        '@type' => 'Organization',
        'name' => $event->organizer_name ?: 'Kegalle Marketplace',
    ],
    'offers' => [
        '@type' => 'Offer',
        'price' => ($event->is_free || ($event->price ?? 0) == 0) ? '0' : number_format($event->price, 2, '.', ''),
        'priceCurrency' => 'LKR',
        'availability' => 'https://schema.org/InStock',
        'url' => url('/events/'.$event->slug),
    ],
    'url' => url('/events/'.$event->slug),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Events', 'item' => url('/events')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $event->title],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush
@endif

@section('content')

@if(!$event)
    <div class="container k-event-notfound">
        <h1 class="k-event-notfound-title">Event Not Found</h1>
        <p class="k-text-muted k-event-notfound-desc">The event you're looking for doesn't exist or has been removed.</p>
        <a href="/events" class="ke-view-btn k-event-notfound-btn">Browse All Events</a>
    </div>
@else

@php
    if (!function_exists('guessEventIcon')) {
        function guessEventIcon($title) {
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
        }
    }
    $evIcons = ['Music & Concerts'=>'🎵','Workshops'=>'💻','Food & Drinks'=>'🍔','Sports'=>'🏏','Festivals'=>'🎪','Business'=>'💼','Family Events'=>'🧒','Exhibitions'=>'🖼️'];
    $catName = $event->category->name ?? '';
    $img = $catName ? ($evIcons[$catName] ?? guessEventIcon($event->title)) : guessEventIcon($event->title);
    $time = $event->starts_at && $event->ends_at ? $event->starts_at->format('g:i A') . ' - ' . $event->ends_at->format('g:i A') : 'TBA';
    $isFree = $event->is_free || $event->price == 0;
    $cat = $event->category->name ?? 'General';
    $organizer = $event->organizer_name ?: 'Event Organizer';
@endphp

{{-- Breadcrumb --}}
<div class="ked-breadcrumb">
    <div class="container">
        <a href="/">Home</a> <span>/</span>
        <a href="/events">Events</a> <span>/</span>
        <span class="ked-breadcrumb-current">{{ \Illuminate\Support\Str::limit($event->title, 40) }}</span>
    </div>
</div>

{{-- Hero Banner --}}
<section class="ked-hero">
    <div class="container">
        <div class="ked-hero-inner">
            <div class="ked-hero-img">
                <div class="ked-hero-placeholder">{{ $img }}</div>
                @if($event->is_featured)<span class="ke-event-tag ke-event-tag-featured">Featured</span>@endif
            </div>
            <div class="ked-hero-info">
                <div class="ked-hero-cat">{{ $cat }}</div>
                <h1 class="ked-hero-title">{{ $event->title }}</h1>
                <div class="ked-hero-meta-row">
                    <div class="ked-meta-item"><span class="ked-meta-icon">📅</span><div><strong>{{ $event->event_date->format('l, d F Y') }}</strong><small>{{ $time }}</small></div></div>
                    <div class="ked-meta-item"><span class="ked-meta-icon">📍</span><div><strong>{{ $event->venue }}</strong><small>{{ $event->location }}, Sri Lanka</small></div></div>
                </div>
                <div class="ked-hero-actions">
                    <div class="ked-price-box">
                        @if($isFree)
                            <span class="ked-price-free">FREE</span>
                            <small>No ticket required</small>
                        @else
                            <span class="ked-price-amount">LKR {{ number_format($event->price) }}</span>
                            <small>per person</small>
                        @endif
                    </div>
                    <div class="ked-action-btns">
                        <button class="ked-btn-primary">🎫 Get Tickets</button>
                        <button class="ked-btn-outline">🔖 Save Event</button>
                        <button class="ked-btn-outline">↗ Share</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Main Content --}}
<section class="k-content-section-v">
    <div class="container">
    <div class="ked-layout">

        {{-- LEFT: Details --}}
        <div class="ked-main">

            <div class="ked-section">
                <h2 class="ked-section-title">About This Event</h2>
                <div class="ked-description">{!! nl2br(e($event->description)) !!}</div>
            </div>

            <div class="ked-section">
                <h2 class="ked-section-title">Event Details</h2>
                <div class="ked-details-grid">
                    <div class="ked-detail-card">
                        <span class="ked-detail-icon">📅</span>
                        <div><strong>Date & Time</strong><p>{{ $event->event_date->format('d M Y') }}<br>{{ $time }}</p></div>
                    </div>
                    <div class="ked-detail-card">
                        <span class="ked-detail-icon">📍</span>
                        <div><strong>Venue</strong><p>{{ $event->venue }}<br>{{ $event->location }}, Sri Lanka</p></div>
                    </div>
                    <div class="ked-detail-card">
                        <span class="ked-detail-icon">🏷️</span>
                        <div><strong>Category</strong><p>{{ $cat }}</p></div>
                    </div>
                    <div class="ked-detail-card">
                        <span class="ked-detail-icon">🌐</span>
                        <div><strong>Event Type</strong><p>{{ ucfirst($event->event_type ?? 'offline') }}</p></div>
                    </div>
                    <div class="ked-detail-card">
                        <span class="ked-detail-icon">👥</span>
                        <div><strong>Capacity</strong><p>{{ $event->capacity ?? '—' }} attendees</p></div>
                    </div>
                    <div class="ked-detail-card">
                        <span class="ked-detail-icon">💰</span>
                        <div><strong>Price</strong><p>{{ $isFree ? 'Free Entry' : 'LKR ' . number_format($event->price) . ' per person' }}</p></div>
                    </div>
                </div>
            </div>

            <div class="ked-section">
                <h2 class="ked-section-title">Location</h2>
                <div class="ked-map-embed">
                    <iframe src="https://maps.google.com/maps?q={{ urlencode($event->venue . ', ' . $event->location . ', Sri Lanka') }}&output=embed&z=14" width="100%" height="300" class="k-event-map-iframe" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="k-event-location-bar">
                    <div><strong class="k-text-sm">{{ $event->venue }}</strong><br><small class="k-text-muted">{{ $event->location }}, Kegalle District, Sri Lanka</small></div>
                    <a href="https://www.google.com/maps/search/{{ urlencode($event->venue . ' ' . $event->location . ' Sri Lanka') }}" target="_blank" rel="noopener" class="ked-btn-outline-dark k-event-maps-link">Open in Maps →</a>
                </div>
            </div>

        </div>

        {{-- RIGHT: Sidebar --}}
        <aside class="ked-sidebar">

            <div class="ked-sidebar-card">
                <h3>Organizer</h3>
                <div class="ked-organizer">
                    <div class="ked-organizer-avatar">{{ mb_substr($organizer, 0, 1) }}</div>
                    <div>
                        <strong>{{ $organizer }}</strong>
                        <small>Event Organizer</small>
                    </div>
                </div>
                <button class="ked-btn-outline-dark k-btn-center k-event-contact-btn">Contact Organizer</button>
            </div>

            <div class="ked-sidebar-card">
                <h3>Event Countdown</h3>
                @php
                    $startTime24 = $event->starts_at ? $event->starts_at->format('H:i') : '00:00';
                @endphp
                <div class="ked-countdown" id="kedCountdown" data-end="{{ $event->event_date->format('Y-m-d') }}T{{ $startTime24 }}:00">
                    <div class="ked-countdown-item"><span id="kedDays">--</span><small>Days</small></div>
                    <div class="ked-countdown-item"><span id="kedHrs">--</span><small>Hours</small></div>
                    <div class="ked-countdown-item"><span id="kedMins">--</span><small>Mins</small></div>
                    <div class="ked-countdown-item"><span id="kedSecs">--</span><small>Secs</small></div>
                </div>
            </div>

            <div class="ked-sidebar-card">
                <h3>Share This Event</h3>
                @php $shareUrl = url('/events/' . $event->slug); $shareText = urlencode($event->title . ' — ' . $event->event_date->format('d M Y') . ' at ' . $event->venue); @endphp
                <div class="ked-share-row">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noopener" class="ked-share-btn ked-share-facebook">f</a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ $shareText }}" target="_blank" rel="noopener" class="ked-share-btn ked-share-twitter">𝕏</a>
                    <a href="https://wa.me/?text={{ $shareText }}%20{{ urlencode($shareUrl) }}" target="_blank" rel="noopener" class="ked-share-btn ked-share-whatsapp">W</a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}" target="_blank" rel="noopener" class="ked-share-btn ked-share-linkedin">in</a>
                </div>
            </div>

            <div class="ked-sidebar-card ked-sidebar-cta">
                <h3>Want to Promote Your Event?</h3>
                <p>Reach thousands of people in Kegalle district.</p>
                <a href="/contact-us" class="ked-btn-primary k-btn-center k-event-cta-btn">Post Your Event</a>
            </div>

        </aside>
    </div>
    </div>
</section>

{{-- Related Events --}}
@if($relatedEvents->isNotEmpty())
<section class="ked-related">
    <div class="container">
        <h2 class="ked-section-title k-event-related-heading">You May Also Like</h2>
        <div class="ked-related-grid">
            @foreach($relatedEvents->take(4) as $r)
                @php
                    $rCat = $r->category->name ?? '';
                    $rIcon = $rCat ? ($evIcons[$rCat] ?? guessEventIcon($r->title)) : guessEventIcon($r->title);
                    $rTime = $r->starts_at && $r->ends_at ? $r->starts_at->format('g:i A') . ' - ' . $r->ends_at->format('g:i A') : 'TBA';
                @endphp
                <div class="ke-event-card">
                    <a href="/events/{{ $r->slug }}" class="k-card-link">
                        <div class="ke-event-card-img">
                            <div class="ke-event-card-placeholder">{{ $rIcon }}</div>
                            @if($r->is_featured)<span class="ke-event-tag ke-event-tag-featured">Featured</span>@endif
                            <div class="ke-event-card-date"><strong>{{ $r->event_date->format('d') }}</strong><small>{{ strtoupper($r->event_date->format('M')) }}</small></div>
                        </div>
                        <div class="ke-event-card-body">
                            <h3 class="ke-event-card-title">{{ $r->title }}</h3>
                            <div class="ke-event-card-meta">📍 {{ $r->venue }}</div>
                            <div class="ke-event-card-meta">🕐 {{ $rTime }}</div>
                            <div class="ke-event-card-price">{{ $r->is_free || $r->price == 0 ? 'Free' : 'LKR '.number_format($r->price) }}</div>
                            <div class="ke-event-card-actions"><a href="/events/{{ $r->slug }}" class="ke-view-btn">View Details</a></div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var el = document.getElementById('kedCountdown');
    if(!el) return;
    var end = new Date(el.dataset.end).getTime();
    function tick(){
        var now = Date.now(), diff = Math.max(0, end - now);
        var d = Math.floor(diff/86400000), h = Math.floor((diff%86400000)/3600000);
        var m = Math.floor((diff%3600000)/60000), s = Math.floor((diff%60000)/1000);
        document.getElementById('kedDays').textContent = String(d).padStart(2,'0');
        document.getElementById('kedHrs').textContent = String(h).padStart(2,'0');
        document.getElementById('kedMins').textContent = String(m).padStart(2,'0');
        document.getElementById('kedSecs').textContent = String(s).padStart(2,'0');
    }
    tick(); setInterval(tick, 1000);
})();
</script>

@endif
@endsection
