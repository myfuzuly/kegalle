@extends('layouts.store-dashboard')

@section('title', 'Analytics — ' . ($store->name ?? 'Store'))
@section('eyebrow', 'Analytics')
@section('heading', 'Store Analytics')

@section('actions')
<a href="/dashboard/stores/{{ $store->id }}" class="kdl-tb-btn kdl-tb-btn-light">
  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
  Back to Overview
</a>
@endsection

@section('content')

{{-- Stat cards --}}
<div class="dbi-grid">
    <div class="dbi-stat dbi-stat-blue">
        <div class="dbi-ic dbi-ic-blue">📦</div>
        <div>
            <div class="dbi-num">{{ $totalListings }}</div>
            <div class="dbi-lbl">Total Listings</div>
        </div>
    </div>
    <div class="dbi-stat dbi-stat-green">
        <div class="dbi-ic dbi-ic-green">✅</div>
        <div>
            <div class="dbi-num">{{ $activeListings }}</div>
            <div class="dbi-lbl">Live Products</div>
        </div>
    </div>
    <div class="dbi-stat dbi-stat-amber">
        <div class="dbi-ic dbi-ic-amber">👁</div>
        <div>
            <div class="dbi-num">{{ number_format($totalViews) }}</div>
            <div class="dbi-lbl">Total Views</div>
        </div>
    </div>
    <div class="dbi-stat dbi-stat-purple">
        <div class="dbi-ic dbi-ic-purple">💬</div>
        <div>
            <div class="dbi-num">{{ $totalInquiries }}</div>
            <div class="dbi-lbl">Inquiries</div>
        </div>
    </div>
</div>

{{-- Two columns --}}
<div class="dbi-cols">

    {{-- Top Performing Listings --}}
    <section class="dbi-card">
        <div class="dbi-card-head">
            <h3>Top Performing Listings</h3>
        </div>
        @if($topListings->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="dan-table">
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
                    @php
                        $pillCls = match($listing->status ?? 'pending') {
                            'approved','active' => 'dbi-pill-green',
                            'rejected' => 'dbi-pill-red',
                            'sold' => 'dbi-pill-gray',
                            default => 'dbi-pill-amber',
                        };
                    @endphp
                    <tr>
                        <td><a href="/dashboard/stores/{{ $store->id }}/products/{{ $listing->id }}/edit">{{ Str::limit($listing->title, 38) }}</a></td>
                        <td><span class="dbi-pill {{ $pillCls }}">{{ ucfirst($listing->status ?? 'pending') }}</span></td>
                        <td>{{ number_format($listing->views ?? 0) }}</td>
                        <td>{{ $listing->fav_count ?? 0 }}</td>
                        <td class="muted-fs12">{{ $listing->created_at ? $listing->created_at->format('M d, Y') : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="dbi-empty">
            <strong>No listings yet</strong>
            <p>Add products to your store to see performance data.</p>
        </div>
        @endif
    </section>

    {{-- Right column --}}
    <div class="flex-col gap-20">

        {{-- Status breakdown --}}
        <section class="dbi-card">
            <div class="dbi-card-head"><h3>Listings by Status</h3></div>
            @php $maxStatus = max($statusBreakdown['approved'], $statusBreakdown['pending'], $statusBreakdown['rejected'], 1); @endphp
            <div class="dan-bar-row">
                <span class="dan-bar-label">Approved</span>
                <div class="dan-bar-track"><div class="dan-bar-fill dan-bar-fill-green" style="width:{{ round($statusBreakdown['approved'] / $maxStatus * 100) }}%"></div></div>
                <span class="dan-bar-count">{{ $statusBreakdown['approved'] }}</span>
            </div>
            <div class="dan-bar-row">
                <span class="dan-bar-label">Pending</span>
                <div class="dan-bar-track"><div class="dan-bar-fill dan-bar-fill-amber" style="width:{{ round($statusBreakdown['pending'] / $maxStatus * 100) }}%"></div></div>
                <span class="dan-bar-count">{{ $statusBreakdown['pending'] }}</span>
            </div>
            <div class="dan-bar-row">
                <span class="dan-bar-label">Rejected</span>
                <div class="dan-bar-track"><div class="dan-bar-fill dan-bar-fill-red" style="width:{{ round($statusBreakdown['rejected'] / $maxStatus * 100) }}%"></div></div>
                <span class="dan-bar-count">{{ $statusBreakdown['rejected'] }}</span>
            </div>
            <div class="dan-fav-row">
                <span class="dan-fav-label">Total store favorites</span>
                <span class="dan-fav-val">❤️ {{ $totalFavorites }}</span>
            </div>
        </section>

    </div>
</div>

{{-- Recent Activity --}}
<section class="dbi-card" class="mt-20">
    <div class="dbi-card-head"><h3>Recent Activity</h3></div>
    @forelse($recentActivity as $listing)
    @php
        $pillCls = match($listing->status ?? 'pending') {
            'approved','active' => 'dbi-pill-green',
            'rejected' => 'dbi-pill-red',
            'sold' => 'dbi-pill-gray',
            default => 'dbi-pill-amber',
        };
    @endphp
    <div class="dbi-row">
        <div class="flex-grow-min">
            <div class="fs135-fw6-trunc">{{ Str::limit($listing->title, 55) }}</div>
            <div class="flex-g8-mt4">
                <span class="dbi-pill {{ $pillCls }}">{{ ucfirst($listing->status ?? 'pending') }}</span>
                <span class="text-115-muted">Updated {{ $listing->updated_at ? $listing->updated_at->diffForHumans() : '—' }}</span>
            </div>
        </div>
        <a href="/dashboard/stores/{{ $store->id }}/products/{{ $listing->id }}/edit" class="dbi-row-edit">Edit</a>
    </div>
    @empty
    <div class="dbi-empty">
        <strong>No recent activity</strong>
        <p>Your listing updates will appear here.</p>
    </div>
    @endforelse
</section>

@endsection
