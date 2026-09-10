@extends('layouts.app')

@section('title', $date->format('F Y') . ' Events — Kegalle')

@section('content')

{{-- Breadcrumb --}}
<div class="ked-breadcrumb">
    <div class="container">
        <a href="/">Home</a> <span>/</span>
        <a href="/events">Events</a> <span>/</span>
        <span class="ked-breadcrumb-current">{{ $date->format('F Y') }}</span>
    </div>
</div>

{{-- Month Header --}}
<section class="kem-header">
    <div class="container">
        <div class="kem-header-inner">
            <a href="/events/calendar/{{ $date->copy()->subMonth()->year }}/{{ $date->copy()->subMonth()->month }}" class="kem-nav-arrow">← {{ $date->copy()->subMonth()->format('M') }}</a>
            <div class="kem-header-center">
                <h1 class="kem-header-title">📅 {{ $date->format('F Y') }}</h1>
                <p class="kem-header-sub">{{ $events instanceof \Illuminate\Pagination\LengthAwarePaginator ? $events->total() : $events->count() }} events this month in Kegalle District</p>
            </div>
            <a href="/events/calendar/{{ $date->copy()->addMonth()->year }}/{{ $date->copy()->addMonth()->month }}" class="kem-nav-arrow">{{ $date->copy()->addMonth()->format('M') }} →</a>
        </div>

        {{-- Month Tabs --}}
        <div class="kem-month-tabs">
            @foreach($months as $mo)
                <a href="/events/calendar/{{ $mo['y'] }}/{{ $mo['m'] }}" class="kem-month-tab {{ $mo['y'] == $year && $mo['m'] == $month ? 'kem-month-tab-active' : '' }}">{{ $mo['date']->format('M Y') }}</a>
            @endforeach
        </div>
    </div>
</section>

{{-- Event Listings --}}
<section class="k-content-section-v">
    <div class="container">
    <div class="kem-layout">

        {{-- Main listing --}}
        <div class="kem-main">
            @php
                $evIcons = ['Music & Concerts'=>'🎵','Workshops'=>'💻','Food & Drinks'=>'🍔','Sports'=>'🏏','Festivals'=>'🎪','Business'=>'💼','Family Events'=>'🧒','Exhibitions'=>'🖼️'];
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
            @endphp
            @forelse($events as $ev)
                @php
                    $catName = $ev->category->name ?? '';
                    $evIcon = $catName ? ($evIcons[$catName] ?? guessEventIcon($ev->title)) : guessEventIcon($ev->title);
                    $evTime = $ev->starts_at && $ev->ends_at ? $ev->starts_at->format('g:i A') . ' - ' . $ev->ends_at->format('g:i A') : 'TBA';
                @endphp
                <article class="kem-listing">
                    <div class="kem-listing-date-col">
                        <div class="kem-listing-date">
                            <strong>{{ $ev->event_date->format('d') }}</strong>
                            <small>{{ strtoupper($ev->event_date->format('M')) }}</small>
                        </div>
                        <span class="kem-listing-day">{{ $ev->event_date->format('D') }}</span>
                    </div>
                    <div class="kem-listing-thumb">
                        <div class="kem-listing-placeholder">{{ $evIcon }}</div>
                        @if($ev->is_featured)<span class="ke-event-tag ke-event-tag-featured">Featured</span>@endif
                    </div>
                    <div class="kem-listing-body">
                        <div class="kem-listing-cat">{{ $ev->category->name ?? 'General' }}</div>
                        <h3 class="kem-listing-title"><a href="/events/{{ $ev->slug }}">{{ $ev->title }}</a></h3>
                        <p class="kem-listing-desc">{{ \Illuminate\Support\Str::limit(strip_tags($ev->description ?? ''), 120) }}</p>
                        <div class="kem-listing-meta">
                            <span>📍 {{ $ev->venue }}, {{ $ev->location }}</span>
                            <span>🕐 {{ $evTime }}</span>
                        </div>
                    </div>
                    <div class="kem-listing-side">
                        <div class="kem-listing-price">{{ $ev->is_free || $ev->price == 0 ? 'Free' : 'LKR '.number_format($ev->price) }}</div>
                        <a href="/events/{{ $ev->slug }}" class="ke-view-btn">View Details</a>
                        <button class="ked-btn-outline k-event-save-btn">🔖 Save</button>
                    </div>
                </article>
            @empty
                <div class="k-event-empty-state">
                    <div class="k-event-empty-icon">📅</div>
                    <h3 class="k-event-empty-title">No events in {{ $date->format('F Y') }}</h3>
                    <p class="k-text-muted k-event-empty-desc">Check other months or browse all events.</p>
                    <a href="/events" class="ke-view-btn k-event-empty-btn">Browse All Events</a>
                </div>
            @endforelse

            @if($events instanceof \Illuminate\Pagination\LengthAwarePaginator && $events->hasPages())
                <div class="k-event-pagination">
                    {{ $events->links() }}
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <aside class="kem-sidebar">
            <div class="ked-sidebar-card">
                <h3>Browse by Month</h3>
                <div class="kem-sidebar-months">
                    @foreach($months as $mo)
                        <a href="/events/calendar/{{ $mo['y'] }}/{{ $mo['m'] }}" class="kem-sidebar-month {{ $mo['y'] == $year && $mo['m'] == $month ? 'kem-sidebar-month-active' : '' }}">
                            📅 {{ $mo['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="ked-sidebar-card">
                <h3>Filter by Location</h3>
                @foreach($locations as $loc)
                    <a href="/events?location={{ $loc }}" class="ke-loc-item k-event-sidebar-loc">
                        📍 {{ $loc }}
                    </a>
                @endforeach
            </div>

            <div class="ked-sidebar-card ked-sidebar-cta">
                <h3>Promote Your Event</h3>
                <p>Reach thousands in Kegalle district.</p>
                <a href="/contact-us" class="ked-btn-primary k-btn-center k-event-cta-btn">Post Your Event</a>
            </div>
        </aside>
    </div>
    </div>
</section>

@endsection
