@extends('layouts.admin')
@section('title','Ad Spaces')
@section('page','Ad Spaces')
@section('eyebrow','Monetization')
@section('page_heading','Ad Space Management')
@section('actions')<a href="/admin/ad-banners/create" class="ka-btn ka-btn-primary">+ Add Ad Banner</a>@endsection

@push('styles')

@endpush

@section('content')

{{-- Placement overview --}}
<section class="sa-card" class="mb-18">
    <div class="sa-card-head"><h2>Ad Placement Slots</h2><span>{{ count(\App\Models\AdBanner::LOCATIONS) }} locations</span></div>
    <div class="kaa-slots">
        @foreach(\App\Models\AdBanner::LOCATIONS as $key => $label)
            @php
                $parts = explode('—', $label);
                $slotName = trim($parts[0] ?? $label);
                $slotDesc = trim($parts[1] ?? '');
                $activeCount = \App\Models\AdBanner::activeForLocation($key)->count();
                $totalCount  = \App\Models\AdBanner::where('location', $key)->count();
                preg_match('/\(([^)]+)\)/', $slotDesc, $m);
                $size = $m[1] ?? '';
            @endphp
            <div class="kaa-slot">
                <div class="kaa-slot-label">{{ $slotName }}</div>
                <div class="kaa-slot-name">{{ preg_replace('/\s*\([^)]+\)/','',$slotDesc) ?: $slotDesc }}</div>
                @if($size)<div class="kaa-slot-size">{{ $size }}</div>@endif
                <div class="kaa-slot-count {{ $activeCount > 0 ? 'has' : 'empty' }}">
                    {{ $activeCount > 0 ? "✓ {$activeCount} active" : '— No active' }}
                    @if($totalCount > $activeCount) <span class="opacity-6">/ {{ $totalCount }} total</span>@endif
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- Banner table --}}
<section class="sa-card">
    <div class="sa-card-head"><h2>All Banners</h2><span>{{ $banners->total() }} banners</span></div>

    <div class="kaa-filter-bar">
        <form method="get"  class="d-contents">
            <select name="location">
                <option value="">All Locations</option>
                @foreach($locations as $key => $label)
                    <option value="{{ $key }}" @selected(request('location')===$key)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="kaa-filter-btn">Filter</button>
            @if(request('location'))<a href="/admin/ad-banners" class="kaa-filter-clear">Clear</a>@endif
        </form>
    </div>

    <div class="sa-table-wrap">
        <table class="sa-table sa-table-adbanners">
            <thead><tr>
                <th class="w-80">Image</th>
                <th class="w-200">Title</th>
                <th>Placement</th>
                <th class="w-160">Active Dates</th>
                <th class="w-70">Clicks</th>
                <th class="w-100">Status</th>
                <th class="w-180">Action</th>
            </tr></thead>
            <tbody>
            @forelse($banners as $banner)
            <tr>
                <td class="w80-pr0">
                    @if($banner->image)
                        <img src="{{ asset('storage/'.$banner->image) }}" alt="{{ $banner->title }}" class="thumb-banner">
                    @else
                        <span class="icon-box-64x48">📢</span>
                    @endif
                </td>
                <td class="max-w-200">
                    <b class="text-truncate text-truncate-190">{{ $banner->title }}</b>
                    @if($banner->link_url)<small class="trunc-190-muted">{{ $banner->link_url }}</small>@endif
                </td>
                <td>
                    <b class="fs-12">{{ $locations[$banner->location] ?? $banner->location }}</b>
                </td>
                <td>
                    @if($banner->starts_at || $banner->ends_at)
                        @php
                            $expired = $banner->ends_at && $banner->ends_at->lt(now());
                            $upcoming = $banner->starts_at && $banner->starts_at->gt(now());
                        @endphp
                        <span class="banner-date-label {{ $expired ? 'banner-date-expired' : ($upcoming ? 'banner-date-upcoming' : 'banner-date-live') }}">
                            {{ $expired ? '✕ Expired' : ($upcoming ? '⏳ Upcoming' : '● Live') }}
                        </span>
                        <small class="text-muted">{{ $banner->starts_at?->format('M d, Y') ?? 'Always' }} – {{ $banner->ends_at?->format('M d, Y') ?? 'Always' }}</small>
                    @else
                        <span class="fs115-green800">● Always running</span>
                    @endif
                </td>
                <td class="fw7-tnum">{{ number_format($banner->clicks) }}</td>
                <td><span class="sa-status {{ $banner->is_active ? 'active' : 'suspended' }}">{{ $banner->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td class="sa-actions-inline">
                    <a href="/admin/ad-banners/{{ $banner->id }}/edit" title="Edit">Edit</a>
                    <form method="post" action="/admin/ad-banners/{{ $banner->id }}/toggle">@csrf<button title="{{ $banner->is_active ? 'Deactivate' : 'Activate' }}">{{ $banner->is_active ? 'Off' : 'On' }}</button></form>
                    <form method="post" action="/admin/ad-banners/{{ $banner->id }}" onsubmit="return confirm('Delete this ad banner?')">@csrf @method('DELETE')<button class="danger" title="Delete">Delete</button></form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="td-empty">No ad banners yet. <a href="/admin/ad-banners/create">Add your first banner →</a></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $banners->links('vendor.pagination.ka-admin') }}
</section>
@endsection
