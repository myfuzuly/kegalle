@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'What needs attention today, and how the marketplace is trending.')

@php
    $u = auth()->user();
    $canApprove = $u->hasPermission('approvals') || $u->hasPermission('listings');
    $delta = function (int $now, int $prev): array {
        if ($prev === 0) return $now > 0 ? ['new', 'up'] : ['no change', 'flat'];
        $p = (int) round(($now - $prev) / $prev * 100);
        return [($p > 0 ? '+' : '').$p.'%', $p > 0 ? 'up' : ($p < 0 ? 'down' : 'flat')];
    };
    $statusPill = fn (?string $s) => match ($s) {
        'approved', 'active', 'published' => 'ok',
        'pending' => 'warn',
        'rejected', 'suspended' => 'bad',
        default => '',
    };
    $pendingTotal = collect($pending)->sum('count');
    [$uD, $uC] = $delta($stats['users_7d'], $stats['users_prev_7d']);
    [$lD, $lC] = $delta($stats['listings_7d'], $stats['listings_prev_7d']);
    [$rD, $rC] = $delta((int) $stats['revenue_30d'], (int) $stats['revenue_prev_30d']);
    $maintenanceOn = (\App\Models\Setting::getValue('maintenance_mode', '0') === '1');
@endphp

@if($pendingTotal && $canApprove)
@section('actions')
<a href="/admin/approvals" class="ka-btn ka-btn-primary">Review {{ number_format($pendingTotal) }} pending</a>
@endsection
@endif

@section('content')

@if($u->hasPermission('settings'))
<div class="kx-sysbar {{ $maintenanceOn ? 'is-on' : '' }}" role="status">
    <span><span class="kx-status-dot" aria-hidden="true"></span>
        @if($maintenanceOn)<strong>Maintenance mode is on.</strong> Visitors see the maintenance page; admins can still browse.
        @else<strong>Site is live.</strong> Maintenance mode is off.@endif
    </span>
    <form method="POST" action="/admin/maintenance/toggle"
          data-confirm="{{ $maintenanceOn ? 'Turn maintenance mode off and make the site public again?' : 'Turn on maintenance mode? Visitors will see a maintenance page until you turn it off.' }}">
        @csrf
        <button type="submit" class="ka-btn ka-btn-light kx-btn-sm">{{ $maintenanceOn ? 'Turn off maintenance' : 'Turn on maintenance' }}</button>
    </form>
</div>
@endif

{{-- KPI summary — every card opens the page behind the number --}}
<div class="kx-kpis">
    <a class="kx-kpi" href="/admin/approvals">
        <span class="kx-kpi-label">Waiting for review</span>
        <span class="kx-kpi-value">{{ number_format($pendingTotal) }}</span>
        <span class="kx-kpi-meta">{{ $pendingTotal ? 'items across '.collect($pending)->where('count', '>', 0)->count().' queues' : 'All queues are clear' }}</span>
    </a>
    <a class="kx-kpi" href="/admin/users">
        <span class="kx-kpi-label">New users · 7 days</span>
        <span class="kx-kpi-value">{{ number_format($stats['users_7d']) }}</span>
        <span class="kx-kpi-meta"><span class="kx-delta {{ $uC }}">{{ $uD }}</span> vs previous 7 days · {{ number_format($stats['users']) }} total</span>
    </a>
    <a class="kx-kpi" href="/admin/listings">
        <span class="kx-kpi-label">New listings · 7 days</span>
        <span class="kx-kpi-value">{{ number_format($stats['listings_7d']) }}</span>
        <span class="kx-kpi-meta"><span class="kx-delta {{ $lC }}">{{ $lD }}</span> vs previous 7 days · {{ number_format($stats['live_listings']) }} live</span>
    </a>
    <a class="kx-kpi" href="/admin/payments">
        <span class="kx-kpi-label">Membership revenue · 30 days</span>
        <span class="kx-kpi-value">LKR {{ number_format($stats['revenue_30d']) }}</span>
        <span class="kx-kpi-meta"><span class="kx-delta {{ $rC }}">{{ $rD }}</span> · {{ number_format($stats['paid_count_30d']) }} paid {{ \Illuminate\Support\Str::plural('payment', $stats['paid_count_30d']) }}</span>
    </a>
</div>

<div class="kx-grid">
    {{-- Work queues --}}
    <section class="kx-card kx-col-4" aria-labelledby="kxTasksH">
        <div class="kx-card-head">
            <div><h2 id="kxTasksH">Pending tasks</h2><p>Open a queue to act on it.</p></div>
        </div>
        <ul class="kx-tasks" role="list">
            @foreach(collect($pending)->sortByDesc('count') as $t)
                <li>
                    <a href="{{ $t['url'] }}" class="kx-task {{ $t['count'] ? '' : 'is-zero' }}">
                        <span class="kx-task-count">{{ number_format($t['count']) }}</span>
                        <span class="kx-task-label">{{ $t['label'] }}</span>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
                    </a>
                </li>
            @endforeach
        </ul>
        @if(!$pendingTotal)
            <p class="kx-allclear"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg> Nothing is waiting. Nice work.</p>
        @endif
    </section>

    {{-- Listings awaiting approval, approvable in place --}}
    <section class="kx-card kx-col-8" aria-labelledby="kxQueueH">
        <div class="kx-card-head">
            <div><h2 id="kxQueueH">Listings awaiting approval</h2><p>Newest first. Approve here or open the full review.</p></div>
            <a href="/admin/approvals">Open approval center</a>
        </div>
        @if($approvalQueue->isEmpty())
            <p class="kx-empty">No listings are waiting for approval.</p>
        @else
            <ul class="kx-rows" role="list">
                @foreach($approvalQueue as $listing)
                    <li class="kx-row">
                        <div class="kx-row-main">
                            <a class="kx-row-title" href="/admin/listings/{{ $listing->id }}/edit">{{ $listing->title }}</a>
                            <span class="kx-row-meta">
                                {{ $listing->category->name ?? 'Uncategorised' }} · {{ ucfirst($listing->type ?? 'listing') }}
                                · {{ optional($listing->store)->name ?? optional($listing->user)->name ?? 'Unknown seller' }}
                                · {{ $listing->created_at?->diffForHumans() }}
                            </span>
                        </div>
                        <div class="kx-row-side">
                            @if(!is_null($listing->price))<span class="kx-pill">LKR {{ number_format((float) $listing->price) }}</span>@endif
                            @if($canApprove)
                            <form method="post" action="/admin/listings/{{ $listing->id }}/approve">
                                @csrf
                                <button type="submit" class="ka-btn ka-btn-primary kx-btn-sm">Approve</button>
                            </form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    {{-- 14-day activity: inline SVG, no chart library --}}
    <section class="kx-card kx-col-8" aria-labelledby="kxActH">
        @php
            $maxV = max(1, collect($activity)->max(fn ($d) => max($d['listings'], $d['users'])));
            $W = 700; $H = 180; $pad = 24; $bw = ($W - $pad) / count($activity); $barW = max(4, ($bw - 6) / 2);
            $sumL = collect($activity)->sum('listings'); $sumU = collect($activity)->sum('users');
        @endphp
        <div class="kx-card-head">
            <div><h2 id="kxActH">Activity · last 14 days</h2><p>{{ number_format($sumL) }} new listings and {{ number_format($sumU) }} new users.</p></div>
        </div>
        <div class="kx-card-body">
            <div class="kx-legend" aria-hidden="true"><span><i class="lg-l"></i>Listings</span><span><i class="lg-u"></i>Users</span></div>
            <svg class="kx-chart" viewBox="0 0 {{ $W }} {{ $H + 22 }}" role="img" aria-label="Daily new listings and users for the last 14 days">
                <line class="axis" x1="{{ $pad }}" y1="{{ $H }}" x2="{{ $W }}" y2="{{ $H }}"/>
                <text x="0" y="12">{{ $maxV }}</text><text x="0" y="{{ $H }}">0</text>
                @foreach($activity as $i => $d)
                    @php $x = $pad + $i * $bw + 3; $hl = $d['listings'] / $maxV * ($H - 16); $hu = $d['users'] / $maxV * ($H - 16); @endphp
                    <rect class="bar-l" x="{{ round($x, 1) }}" y="{{ round($H - $hl, 1) }}" width="{{ round($barW, 1) }}" height="{{ round($hl, 1) }}" rx="2"><title>{{ \Carbon\Carbon::parse($d['date'])->format('D j M') }}: {{ $d['listings'] }} listings</title></rect>
                    <rect class="bar-u" x="{{ round($x + $barW, 1) }}" y="{{ round($H - $hu, 1) }}" width="{{ round($barW, 1) }}" height="{{ round($hu, 1) }}" rx="2"><title>{{ \Carbon\Carbon::parse($d['date'])->format('D j M') }}: {{ $d['users'] }} users</title></rect>
                    @if($i % 2 === 0 || $loop->last)
                        <text x="{{ round($x + $barW, 1) }}" y="{{ $H + 16 }}" text-anchor="middle">{{ \Carbon\Carbon::parse($d['date'])->format('j M') }}</text>
                    @endif
                @endforeach
            </svg>
        </div>
    </section>

    {{-- Stores waiting for approval --}}
    <section class="kx-card kx-col-4" aria-labelledby="kxStoresH">
        <div class="kx-card-head">
            <div><h2 id="kxStoresH">Stores to approve</h2><p>{{ number_format($stats['live_stores']) }} of {{ number_format($stats['stores']) }} stores are live.</p></div>
            <a href="/admin/stores?status=pending">View all</a>
        </div>
        @if($pendingStores->isEmpty())
            <p class="kx-empty">No stores are waiting.</p>
        @else
            <ul class="kx-rows" role="list">
                @foreach($pendingStores as $store)
                    <li class="kx-row">
                        <div class="kx-row-main">
                            <a class="kx-row-title" href="/admin/stores/{{ $store->id }}/edit">{{ $store->name }}</a>
                            <span class="kx-row-meta">{{ optional($store->user)->email ?? 'No owner email' }} · {{ $store->created_at?->diffForHumans() }}</span>
                        </div>
                        @if($u->hasPermission('stores'))
                        <form method="post" action="/admin/stores/{{ $store->id }}/approve" class="kx-row-side">
                            @csrf
                            <button type="submit" class="ka-btn ka-btn-light kx-btn-sm">Approve</button>
                        </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    {{-- Recent listings --}}
    <section class="kx-card kx-col-8" aria-labelledby="kxRecentH">
        <div class="kx-card-head">
            <div><h2 id="kxRecentH">Recently added listings</h2><p>{{ number_format($stats['products']) }} products · {{ number_format($stats['classified']) }} classifieds in total.</p></div>
            <a href="/admin/listings">Manage listings</a>
        </div>
        @if($latestListings->isEmpty())
            <p class="kx-empty">No listings yet.</p>
        @else
            <ul class="kx-rows" role="list">
                @foreach($latestListings as $listing)
                    <li class="kx-row">
                        <div class="kx-row-main">
                            <a class="kx-row-title" href="/admin/listings/{{ $listing->id }}/edit">{{ $listing->title }}</a>
                            <span class="kx-row-meta">{{ $listing->category->name ?? 'Uncategorised' }} · {{ optional($listing->store)->name ?? 'No store' }} · {{ $listing->created_at?->format('j M Y') }}</span>
                        </div>
                        <span class="kx-pill {{ $statusPill($listing->status) }}">{{ ucfirst($listing->status ?? 'unknown') }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    {{-- Category mix --}}
    <section class="kx-card kx-col-4" aria-labelledby="kxCatsH">
        <div class="kx-card-head">
            <div><h2 id="kxCatsH">Top categories</h2><p>By number of listings.</p></div>
            @if($u->hasPermission('categories'))<a href="/admin/categories">Manage</a>@endif
        </div>
        <div class="kx-card-body">
            @php $catMax = max(1, (int) $topCategories->max('listings_count')); @endphp
            @if($topCategories->isEmpty() || $topCategories->sum('listings_count') === 0)
                <p class="kx-empty">No listings in any category yet.</p>
            @else
                <ul class="kx-bars" role="list">
                    @foreach($topCategories as $cat)
                        <li>
                            <span>{{ $cat->name }}</span><b>{{ number_format($cat->listings_count) }}</b>
                            <svg class="track" viewBox="0 0 100 6" preserveAspectRatio="none" aria-hidden="true"><rect class="fill" width="{{ max(1, round($cat->listings_count / $catMax * 100)) }}" height="6" rx="3"/></svg>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>
</div>
@endsection
