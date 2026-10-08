@extends('layouts.admin')
@section('title','Services')
@section('page','Services')
@section('heading','Service Management')
@section('subheading','Review, approve and manage service listings from providers')

@section('content')

@if(session('success'))
<div class="alert-success">{{ session('success') }}</div>
@endif

<section class="sa-card">
    <div class="flex-row justify-between ka-card-head-row">
        <div>
            <h2 class="ka-section-title-row">All Services</h2>
            <p class="ka-section-sub-row">{{ $services->total() }} total services</p>
        </div>
    </div>

    <table class="ka-table">
        <thead>
            <tr>
                <th>Service</th>
                <th>Provider</th>
                <th>Type</th>
                <th>Price</th>
                <th>Location</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($services as $service)
        <tr>
            <td>
                <div class="ka-flex-row-10">
                    <div class="ka-svc-thumb">
                        @if($service->image)
                            <img src="{{ asset('storage/'.$service->image) }}" alt="">
                        @else
                            <span style="font-size:18px">🛠️</span>
                        @endif
                    </div>
                    <div>
                        <div class="ka-svc-name">{{ Str::limit($service->title, 50) }}</div>
                        @if($service->category)<div class="ka-svc-type">{{ $service->category->name }}</div>@endif
                    </div>
                </div>
            </td>
            <td class="ka-fs-13">{{ $service->user->name ?? '—' }}</td>
            <td><span class="ka-svc-pill active">{{ ucfirst($service->service_type) }}</span></td>
            <td class="ka-svc-fee">{{ $service->price_label }}</td>
            <td class="ka-fs-13">{{ $service->location }}</td>
            <td>
                @if($service->status === 'approved')
                    <span class="ka-svc-pill active">Approved</span>
                @elseif($service->status === 'pending')
                    <span class="ka-svc-pill pending">Pending</span>
                @else
                    <span class="ka-svc-pill rejected">Rejected</span>
                @endif
            </td>
            <td class="ka-fs-12 ka-text-muted">{{ $service->created_at?->format('d M Y') }}</td>
            <td>
                <div class="ka-svc-actions">
                    <a href="/services/{{ $service->slug }}" target="_blank" class="ka-btn ka-btn-light" style="font-size:12px;padding:4px 10px">View</a>
                    @if($service->status !== 'approved')
                    <form method="post" action="/admin/services/{{ $service->id }}/approve" class="ka-d-inline">@csrf
                        <button class="ka-btn" style="font-size:12px;padding:4px 10px;background:#dcfce7;color:#166534;border-color:#bbf7d0">✓</button>
                    </form>
                    @endif
                    @if($service->status !== 'rejected')
                    <form method="post" action="/admin/services/{{ $service->id }}/reject" onsubmit="return confirm('Reject this service?')" class="ka-d-inline">@csrf
                        <button class="ka-btn" style="font-size:12px;padding:4px 10px;background:#fee2e2;color:#991b1b;border-color:#fecaca">✕</button>
                    </form>
                    @endif
                    <form method="post" action="/admin/services/{{ $service->id }}" onsubmit="return confirm('Delete this service permanently?')" class="ka-d-inline">@csrf @method('DELETE')
                        <button class="ka-btn ka-btn-danger" style="font-size:12px;padding:4px 10px">🗑</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="ka-empty-sm">No services found.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="p14-18">{{ $services->links() }}</div>
</section>

@endsection
