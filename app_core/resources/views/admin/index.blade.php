@extends('layouts.admin')



@section('title','Dashboard')
@section('page','Dashboard')
@section('heading','Dashboard')
@section('eyebrow','Overview')
@section('page_heading','Dashboard')

@section('actions')
<a href="/admin/listings" class="ka-btn ka-btn-primary">Moderate Listings</a>
@endsection

@section('content')

@php $maintenanceOn = (\App\Models\Setting::getValue('maintenance_mode','0') === '1'); @endphp
<div class="ka-maintenance-bar {{ $maintenanceOn ? 'ka-maint-on' : 'ka-maint-off' }}">
    <span>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ $maintenanceOn ? 'Maintenance Mode is ON — public site shows maintenance page' : 'Maintenance Mode is OFF — site is live' }}
    </span>
    <form method="POST" action="/admin/maintenance/toggle" style="display:inline">
        @csrf
        <button type="submit" class="ka-maint-toggle-btn">
            {{ $maintenanceOn ? '✅ Disable Maintenance' : '🚧 Enable Maintenance' }}
        </button>
    </form>
</div>

<div class="ka-stat-grid">
    <article class="ka-stat-card ka-stat-card-blue">
        <div class="ka-stat-card-icon">
            <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <span>Total Users</span>
        <strong>{{ number_format($stats['users'] ?? 0) }}</strong>
        <small>{{ number_format($stats['active_users'] ?? 0) }} active accounts</small>
    </article>

    <article class="ka-stat-card ka-stat-card-green">
        <div class="ka-stat-card-icon">
            <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
        <span>Stores</span>
        <strong>{{ number_format($stats['stores'] ?? 0) }}</strong>
        <small>{{ number_format($stats['pending_stores'] ?? 0) }} pending approval</small>
    </article>

    <article class="ka-stat-card ka-stat-card-amber">
        <div class="ka-stat-card-icon">
            <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
        </div>
        <span>Listings</span>
        <strong>{{ number_format($stats['listings'] ?? 0) }}</strong>
        <small>{{ number_format($stats['pending_listings'] ?? 0) }} pending approval</small>
    </article>

    <article class="ka-stat-card ka-stat-card-purple ka-stat-highlight">
        <div class="ka-stat-card-icon">
            <svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <span>Marketplace Value</span>
        <strong>LKR {{ number_format($stats['total_value'] ?? 0) }}</strong>
        <small>Approved listing value</small>
    </article>
</div>

<div class="ka-grid-2">
    <section class="ka-panel">
        <div class="ka-panel-head">
            <h2>Quick Actions</h2>
            <span>Super Admin</span>
        </div>

        <div class="ka-shortcut-grid">
            <a href="/admin/users">
                <i><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></i>
                <div><b>Users</b><small>Roles &amp; access</small></div>
            </a>
            <a href="/admin/stores">
                <i><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></i>
                <div><b>Stores</b><small>Approve &amp; feature</small></div>
            </a>
            <a href="/admin/listings">
                <i><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg></i>
                <div><b>Products / Ads</b><small>Moderate listings</small></div>
            </a>
            <a href="/admin/categories">
                <i><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg></i>
                <div><b>Categories</b><small>Manage taxonomy</small></div>
            </a>
            <a href="/admin/locations">
                <i><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></i>
                <div><b>Locations</b><small>District / city / town</small></div>
            </a>
            <a href="/admin/settings">
                <i><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></i>
                <div><b>Settings</b><small>Platform config</small></div>
            </a>
        </div>
    </section>

    <section class="ka-panel">
        <div class="ka-panel-head">
            <h2>Approval Queue</h2>
            <a href="/admin/listings">View all →</a>
        </div>

        @forelse($approvalQueue ?? [] as $listing)
            <div class="ka-queue-row">
                <div>
                    <b>{{ $listing->title }}</b>
                    <small>{{ $listing->category->name ?? 'General' }} · {{ ucfirst($listing->type ?? 'listing') }}</small>
                </div>

                <form method="post" action="/admin/listings/{{ $listing->id }}/approve">
                    @csrf
                    <button>Approve</button>
                </form>
            </div>
        @empty
            <div class="ka-empty-state">
                <b>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </b>
                <p>All clear — no pending listings.</p>
            </div>
        @endforelse

        <div class="ka-health">
            <div><strong>{{ $stats['approved_listings'] ?? 0 }}</strong><span>Listings</span></div>
            <div><strong>{{ $stats['approved_stores'] ?? 0 }}</strong><span>Stores</span></div>
            <div><strong>{{ $stats['products'] ?? 0 }}</strong><span>Products</span></div>
            <div><strong>{{ $stats['classified'] ?? 0 }}</strong><span>Classifieds</span></div>
        </div>
    </section>
</div>

{{-- ── 7-day Activity Charts ───────────────────────────────────────────── --}}
@if(isset($chartData))
<script src="/js/chart.umd.min.js"></script>
<section class="ka-panel mb-1r5">
    <div class="ka-panel-head">
        <h2>7-Day Activity</h2>
        <span class="fs-8rem-o6">Last 7 days</span>
    </div>
    <div class="grid-auto-260">
        <div><span class="chart-label">New Listings</span><canvas id="chartListings" height="150"></canvas></div>
        <div><span class="chart-label">New Users</span><canvas id="chartUsers"    height="150"></canvas></div>
        <div><span class="chart-label">Listing Value (LKR)</span><canvas id="chartRevenue"  height="150"></canvas></div>
    </div>
</section>
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    var D = @json($chartData);
    var accent = getComputedStyle(document.documentElement).getPropertyValue('--ka-accent').trim() || '#00A76F';
    function mkChart(id, label, data, color){
        var ctx = document.getElementById(id);
        if(!ctx) return;
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: D.labels,
                datasets:[{ label: label, data: data,
                    borderColor: color, backgroundColor: color+'22',
                    fill: true, tension: 0.4, pointRadius: 4,
                    pointBackgroundColor: color, borderWidth: 2
                }]
            },
            options:{
                plugins:{ legend:{ labels:{ color:'#888', font:{size:11} } } },
                scales:{
                    x:{ ticks:{ color:'#888' }, grid:{ color:'#f1f5f9' } },
                    y:{ ticks:{ color:'#888', precision:0 }, grid:{ color:'#f1f5f9' }, beginAtZero:true }
                },
                responsive: true, maintainAspectRatio: true
            }
        });
    }
    mkChart('chartListings','New Listings', D.listings, accent);
    mkChart('chartUsers',   'New Users',    D.users,    '#3B82F6');
    mkChart('chartRevenue', 'Listing Value (LKR)', D.revenue, '#F59E0B');
})();
</script>
@endif

<script nonce="{{ $cspNonce ?? '' }}">
document.querySelectorAll('.ka-cat-bar[data-w]').forEach(function(el){
    el.style.width = el.dataset.w + 'px';
});
</script>

@if(isset($pendingStores) && $pendingStores->isNotEmpty())
<section class="ka-panel mb-1r5">
    <div class="ka-panel-head">
        <h2>Pending Stores ({{ $pendingStores->count() }})</h2>
        <a href="/admin/stores">View all →</a>
    </div>
    @foreach($pendingStores as $store)
        <div class="ka-queue-row">
            <div>
                <b>{{ $store->name }}</b>
                <small>{{ optional($store->user)->email }}</small>
            </div>
            <form method="post" action="/admin/stores/{{ $store->id }}/approve">
                @csrf
                <button>Approve</button>
            </form>
        </div>
    @endforeach
</section>
@endif

<div class="ka-grid-2">
    <section class="ka-panel">
        <div class="ka-panel-head">
            <h2>Categories</h2>
            <a href="/admin/categories">Manage →</a>
        </div>

        {{-- Category stats --}}
        @if(isset($categoryStats))
        <div class="grid-3-g10">
            <div class="thumb-bg">
                <div class="fs20-fw7-accent">{{ $categoryStats['main'] }}</div>
                <div class="caption-muted">Main</div>
            </div>
            <div class="thumb-bg">
                <div class="fs20-fw7-blue">{{ $categoryStats['total'] - $categoryStats['main'] }}</div>
                <div class="caption-muted">Sub+Leaf</div>
            </div>
            <div class="thumb-bg">
                <div class="fs20-fw7-text">{{ $categoryStats['total'] }}</div>
                <div class="caption-muted">Total</div>
            </div>
        </div>
        @endif

        {{-- Top categories by listings --}}
        <div class="label-upper-xs">Top by Listings</div>
        @foreach(($topCategories ?? collect())->take(8) as $cat)
            <div class="ka-queue-row">
                <div>
                    <b>{{ $cat->name }}</b>
                    <small>{{ $cat->parent ? $cat->parent->name.' › ' : '' }}{{ number_format($cat->listings_count) }} listings</small>
                </div>
                <div class="ka-cat-bar" data-w="{{ $topCategories->max('listings_count') > 0 ? round($cat->listings_count / $topCategories->max('listings_count') * 100) : 4 }}"></div>
            </div>
        @endforeach

        {{-- Quick links by main cat --}}
        <div class="mt14-bt">
            <div class="label-upper-xs">Browse by Main</div>
            <div class="flex-fw-g6">
                @foreach(\App\Models\Category::whereNull('parent_id')->orderBy('sort_order')->take(10)->get(['id','name']) as $main)
                <a class="pill-default" href="/admin/categories?parent_id={{ $main->id }}">{{ $main->name }}</a>
                @endforeach
                <a class="pill-accent" href="/admin/categories">All →</a>
            </div>
        </div>
    </section>

    <section class="ka-panel">
        <div class="ka-panel-head">
            <h2>Latest Listings</h2>
            <a href="/admin/listings">Manage →</a>
        </div>
        @forelse($latestListings ?? [] as $listing)
            <div class="ka-queue-row">
                <div>
                    <b>{{ $listing->title }}</b>
                    <small>{{ $listing->category->name ?? 'General' }} · <span class="ka-status {{ $listing->status }}">{{ ucfirst($listing->status) }}</span></small>
                </div>
                <a href="/listings/{{ $listing->slug }}" target="_blank" class="ka-link" class="flex-shrink-0">Open</a>
            </div>
        @empty
            <div class="ka-empty-state"><p>No listings yet.</p></div>
        @endforelse
    </section>
</div>


@endsection

{{-- chart.js loaded inline above the chart section --}}
