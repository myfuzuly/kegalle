@extends('layouts.dashboard')

@section('title','My Deals')
@section('heading','🔥 My Deals')
@section('subheading','Submit your listings as deals with special prices. Admin approval required before going live.')

@section('actions')
<a href="/dashboard/deals/create" class="kd-btn kd-btn-primary">+ Submit Deal</a>
@endsection

@section('content')
<section class="kd-card">
    <div class="kd-card-head"><h2>Deal Submissions</h2><span>{{ $deals->total() }} total</span></div>
    @forelse($deals as $deal)
        @php
            $statusColor = match($deal->status) {
                'approved' => '#047857',
                'rejected' => '#991b1b',
                default => '#d97706',
            };
            $statusBg = match($deal->status) {
                'approved' => '#ecfdf5',
                'rejected' => '#fee2e2',
                default => '#fffbeb',
            };
        @endphp
        <div class="kd-row" style="flex-wrap:wrap">
            <div style="flex:1;min-width:200px">
                <strong>{{ \Illuminate\Support\Str::limit($deal->listing->title ?? 'Deleted', 35) }}</strong>
                <small>{{ optional($deal->listing->category ?? null)->name ?? '' }}@if($deal->is_flash) · ⚡ Flash Deal @endif</small>
            </div>
            <div style="text-align:center;min-width:120px">
                <strong style="color:var(--kd-brand)">LKR {{ number_format($deal->deal_price) }}</strong>
                <small><s style="color:var(--kd-muted)">LKR {{ number_format($deal->original_price) }}</s></small>
            </div>
            <div style="text-align:center;min-width:60px">
                <strong style="color:#dc2626">-{{ number_format($deal->discount_percent, 0) }}%</strong>
            </div>
            <div style="text-align:center;min-width:130px">
                <small>{{ $deal->starts_at->format('M d') }} — {{ $deal->ends_at->format('M d, Y') }}</small>
            </div>
            <div style="text-align:center;min-width:80px">
                <span style="display:inline-block;padding:4px 12px;border-radius:10px;font-size:12px;font-weight:800;background:{{ $statusBg }};color:{{ $statusColor }}">{{ ucfirst($deal->status) }}</span>
            </div>
            <div style="display:flex;gap:8px;align-items:center">
                @if($deal->listing)<a href="/listings/{{ $deal->listing->slug }}" target="_blank" class="kd-mini-btn">View</a>@endif
                <a href="/dashboard/deals/{{ $deal->id }}/edit" class="kd-mini-btn">Edit</a>
                @if($deal->status !== 'approved')
                    <form method="post" action="/dashboard/deals/{{ $deal->id }}" style="display:inline">@csrf @method('DELETE')
                        <button class="kd-mini-btn" style="background:#fee2e2;color:#991b1b;border:none;cursor:pointer">Delete</button>
                    </form>
                @endif
            </div>
        </div>
        @if($deal->status === 'rejected' && $deal->admin_note)
            <div style="padding:0 0 12px;font-size:13px;color:#991b1b;font-weight:600">⚠ Admin note: {{ $deal->admin_note }}</div>
        @endif
    @empty
        <div class="kd-empty">
            <strong>No deals yet</strong>
            <p>Submit your first deal to get featured on the Deals page.</p>
            <a href="/dashboard/deals/create" class="kd-btn kd-btn-primary">Submit Deal</a>
        </div>
    @endforelse
</section>
@if(method_exists($deals,'links'))<div style="margin-top:20px">{{ $deals->links() }}</div>@endif
@endsection
