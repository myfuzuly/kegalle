@extends('layouts.store-dashboard')

@section('title', 'Store Analytics')
@section('heading', 'Analytics — ' . ($store->name ?? 'Store'))
@section('subheading', 'Track your store performance and listing insights.')

@section('actions')
<a href="/dashboard/stores/{{ $store->id }}" class="kd-btn kd-btn-light">← Back to Store</a>
@endsection

@push('styles')
<style>
.kd-analytics-bar-wrap{margin:8px 0}
.kd-analytics-bar{height:22px;border-radius:4px;display:flex;align-items:center;padding:0 10px;font-size:12px;font-weight:600;color:#fff;min-width:32px;transition:width .4s ease}
.kd-analytics-bar.green{background:#22c55e}
.kd-analytics-bar.yellow{background:#eab308;color:#1a1a1a}
.kd-analytics-bar.red{background:#ef4444}
.kd-status-row{display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid rgba(0,0,0,.06)}
.kd-status-row:last-child{border-bottom:none}
.kd-status-label{min-width:80px;font-weight:600;font-size:14px}
.kd-status-count{min-width:40px;text-align:right;font-size:14px;font-weight:700}
.kd-bar-track{flex:1;background:rgba(0,0,0,.06);border-radius:4px;height:22px;overflow:hidden}
.kd-badge{display:inline-block;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;text-transform:uppercase}
.kd-badge-approved{background:#dcfce7;color:#166534}
.kd-badge-pending{background:#fef9c3;color:#854d0e}
.kd-badge-rejected{background:#fee2e2;color:#991b1b}
.kd-analytics-table{width:100%;border-collapse:collapse;font-size:14px}
.kd-analytics-table th{text-align:left;padding:10px 8px;border-bottom:2px solid rgba(0,0,0,.1);font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:#64748b}
.kd-analytics-table td{padding:10px 8px;border-bottom:1px solid rgba(0,0,0,.06)}
.kd-analytics-table a{color:inherit;text-decoration:underline;text-underline-offset:2px}
.kd-time-ago{color:#94a3b8;font-size:12px}
</style>
@endpush

@section('content')

{{-- Stats Widgets --}}
<div class="kd-widget-grid">
    <article class="kd-widget"><span>Total Listings</span><strong>{{ $totalListings }}</strong><small>All statuses</small></article>
    <article class="kd-widget"><span>Active Listings</span><strong>{{ $activeListings }}</strong><small>Approved &amp; live</small></article>
    <article class="kd-widget"><span>Total Views</span><strong>{{ number_format($totalViews) }}</strong><small>Across all listings</small></article>
    <article class="kd-widget"><span>Inquiries</span><strong>{{ $totalInquiries }}</strong><small>Chat threads received</small></article>
</div>

<div class="kd-grid-2">

    {{-- Top Performing Listings --}}
    <section class="kd-card">
        <div class="kd-card-head"><h2>Top Performing Listings</h2></div>
        @if($topListings->isNotEmpty())
        <div style="overflow-x:auto">
            <table class="kd-analytics-table">
                <thead>
                    <tr>
                        <th>Listing</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Favs</th>
                        <th>Posted</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topListings as $listing)
                    <tr>
                        <td><a href="/dashboard/stores/{{ $store->id }}/products/{{ $listing->id }}/edit">{{ Str::limit($listing->title, 35) }}</a></td>
                        <td><span class="kd-badge kd-badge-{{ $listing->status ?? 'pending' }}">{{ ucfirst($listing->status ?? 'pending') }}</span></td>
                        <td>{{ number_format($listing->views ?? 0) }}</td>
                        <td>{{ $listing->fav_count ?? 0 }}</td>
                        <td class="kd-time-ago">{{ $listing->created_at ? $listing->created_at->format('M d, Y') : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="kd-empty"><strong>No listings yet</strong><p>Add products to your store to see performance data.</p></div>
        @endif
    </section>

    {{-- Listings by Status --}}
    <section class="kd-card">
        <div class="kd-card-head"><h2>Listings by Status</h2></div>
        @php
            $maxStatus = max($statusBreakdown['approved'], $statusBreakdown['pending'], $statusBreakdown['rejected'], 1);
        @endphp
        <div style="padding:8px 0">
            <div class="kd-status-row">
                <span class="kd-status-label">Approved</span>
                <div class="kd-bar-track">
                    <div class="kd-analytics-bar green" style="width:{{ round($statusBreakdown['approved'] / $maxStatus * 100) }}%">{{ $statusBreakdown['approved'] }}</div>
                </div>
                <span class="kd-status-count">{{ $statusBreakdown['approved'] }}</span>
            </div>
            <div class="kd-status-row">
                <span class="kd-status-label">Pending</span>
                <div class="kd-bar-track">
                    <div class="kd-analytics-bar yellow" style="width:{{ round($statusBreakdown['pending'] / $maxStatus * 100) }}%">{{ $statusBreakdown['pending'] }}</div>
                </div>
                <span class="kd-status-count">{{ $statusBreakdown['pending'] }}</span>
            </div>
            <div class="kd-status-row">
                <span class="kd-status-label">Rejected</span>
                <div class="kd-bar-track">
                    <div class="kd-analytics-bar red" style="width:{{ round($statusBreakdown['rejected'] / $maxStatus * 100) }}%">{{ $statusBreakdown['rejected'] }}</div>
                </div>
                <span class="kd-status-count">{{ $statusBreakdown['rejected'] }}</span>
            </div>
        </div>

        {{-- Quick summary --}}
        <div style="padding:12px 0;border-top:1px solid rgba(0,0,0,.06);font-size:13px;color:#64748b">
            Total favorites across store: <strong style="color:#1a1a1a">{{ $totalFavorites }}</strong>
        </div>
    </section>

</div>

{{-- Recent Activity --}}
<section class="kd-card" style="margin-top:16px">
    <div class="kd-card-head"><h2>Recent Activity</h2></div>
    @forelse($recentActivity as $listing)
        <div class="kd-row">
            <div>
                <strong>{{ Str::limit($listing->title, 50) }}</strong>
                <small>
                    <span class="kd-badge kd-badge-{{ $listing->status ?? 'pending' }}">{{ ucfirst($listing->status ?? 'pending') }}</span>
                    &middot; Updated {{ $listing->updated_at ? $listing->updated_at->diffForHumans() : '—' }}
                </small>
            </div>
            <a href="/dashboard/stores/{{ $store->id }}/products/{{ $listing->id }}/edit" class="kd-mini-btn">Edit</a>
        </div>
    @empty
        <div class="kd-empty"><strong>No recent activity</strong><p>Your listing updates will appear here.</p></div>
    @endforelse
</section>

@endsection
