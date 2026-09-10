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
                <div style="display:flex;align-items:center;gap:10px">
                    <div style="width:40px;height:40px;border-radius:8px;overflow:hidden;flex-shrink:0;background:#f1f5f9;display:flex;align-items:center;justify-content:center">
                        @if($service->image)
                            <img src="{{ asset('storage/'.$service->image) }}" alt="" style="width:100%;height:100%;object-fit:cover">
                        @else
                            <span style="font-size:18px">🛠️</span>
                        @endif
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:13px">{{ Str::limit($service->title, 50) }}</div>
                        @if($service->category)<div style="font-size:11px;color:#64748b">{{ $service->category->name }}</div>@endif
                    </div>
                </div>
            </td>
            <td style="font-size:13px">{{ $service->user->name ?? '—' }}</td>
            <td><span style="font-size:11px;padding:2px 8px;border-radius:12px;background:#f0fdf4;color:#15803d;font-weight:600">{{ ucfirst($service->service_type) }}</span></td>
            <td style="font-size:13px">{{ $service->price_label }}</td>
            <td style="font-size:13px">{{ $service->location }}</td>
            <td>
                @if($service->status === 'approved')
                    <span style="font-size:11px;padding:2px 8px;border-radius:12px;background:#dcfce7;color:#166534;font-weight:600">Approved</span>
                @elseif($service->status === 'pending')
                    <span style="font-size:11px;padding:2px 8px;border-radius:12px;background:#fef9c3;color:#854d0e;font-weight:600">Pending</span>
                @else
                    <span style="font-size:11px;padding:2px 8px;border-radius:12px;background:#fee2e2;color:#991b1b;font-weight:600">Rejected</span>
                @endif
            </td>
            <td style="font-size:12px;color:#64748b">{{ $service->created_at?->format('d M Y') }}</td>
            <td>
                <div style="display:flex;gap:6px;flex-wrap:wrap">
                    <a href="/services/{{ $service->slug }}" target="_blank" class="ka-btn ka-btn-light" style="font-size:12px;padding:4px 10px">View</a>
                    @if($service->status !== 'approved')
                    <form method="post" action="/admin/services/{{ $service->id }}/approve" style="display:inline">@csrf
                        <button class="ka-btn" style="font-size:12px;padding:4px 10px;background:#dcfce7;color:#166534;border-color:#bbf7d0">✓</button>
                    </form>
                    @endif
                    @if($service->status !== 'rejected')
                    <form method="post" action="/admin/services/{{ $service->id }}/reject" onsubmit="return confirm('Reject this service?')" style="display:inline">@csrf
                        <button class="ka-btn" style="font-size:12px;padding:4px 10px;background:#fee2e2;color:#991b1b;border-color:#fecaca">✕</button>
                    </form>
                    @endif
                    <form method="post" action="/admin/services/{{ $service->id }}" onsubmit="return confirm('Delete this service permanently?')" style="display:inline">@csrf @method('DELETE')
                        <button class="ka-btn ka-btn-danger" style="font-size:12px;padding:4px 10px">🗑</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;padding:32px;color:#94a3b8">No services found.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="padding:16px">{{ $services->links() }}</div>
</section>

@endsection
