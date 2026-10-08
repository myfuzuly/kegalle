@extends('layouts.admin')
@section('title','Membership Plans')
@section('page','Memberships')
@section('heading','Membership Plans')
@section('subheading','Create and manage paid membership tiers for sellers')
@section('actions')
<a href="#add-plan" id="scroll-to-add" class="ka-btn ka-btn-primary">+ Add Plan</a>
@endsection

@section('content')

@if(session('success'))
<div class="alert-success">✓ {{ session('success') }}</div>
@endif

{{-- Plans grid --}}
<div class="sa-card" style="margin-bottom:22px">
    <div class="sa-card-head">
        <h2 class="ka-flex-row-8"><span style="font-size:20px">💎</span> Active Plans</h2>
        <span>{{ $plans->count() }} plan{{ $plans->count() !== 1 ? 's' : '' }}</span>
    </div>

    @if($plans->count())
    <div class="ka-grid-auto-240" style="padding:20px">
        @foreach($plans as $plan)
        @php
          $accent = match(strtolower($plan->slug ?? $plan->name)) {
            'gold','gold plan'     => ['color'=>'#b45309','bg'=>'#fffbeb','border'=>'#fcd34d'],
            'platinum'             => ['color'=>'#6d28d9','bg'=>'#f5f3ff','border'=>'#c4b5fd'],
            'silver','silver plan' => ['color'=>'#475569','bg'=>'#f8fafc','border'=>'#cbd5e1'],
            default                => ['color'=>'#1b5e20','bg'=>'#f0fdf4','border'=>'#86efac'],
          };
        @endphp
        <div class="ka-plan-card" style="border-color:{{ $plan->is_active ? $accent['border'] : '#e2e8f0' }}">
            <div class="ka-plan-card-head" style="background:{{ $plan->is_active ? $accent['bg'] : '#f8fafc' }};border-bottom-color:{{ $accent['border'] }}">
                <div class="ka-plan-head-row">
                    <span class="ka-plan-name" style="color:{{ $plan->is_active ? $accent['color'] : '#94a3b8' }}">
                        {{ $plan->name }}
                    </span>
                    <span class="ka-plan-status-badge" style="background:{{ $plan->is_active ? '#dcfce7' : '#f1f5f9' }};color:{{ $plan->is_active ? '#15803d' : '#94a3b8' }}">
                        {{ $plan->is_active ? '● Active' : '○ Inactive' }}
                    </span>
                </div>
                <div class="ka-plan-price">
                    LKR {{ number_format($plan->price, 0) }}
                    <span class="ka-plan-price-sub">/ {{ $plan->duration_days }}d</span>
                </div>
            </div>
            <div class="ka-plan-card-body">
                <div class="ka-plan-stat-grid">
                    @foreach([
                        ['Ads',      $plan->ad_limit],
                        ['Products', $plan->product_limit],
                        ['Stores',   $plan->store_limit ?? 1],
                        ['Featured', $plan->featured_quota ?? 0],
                    ] as [$lbl, $val])
                    <div class="ka-plan-stat-box">
                        <div class="ka-plan-stat-label">{{ $lbl }}</div>
                        <div class="ka-plan-stat-val">{{ $val }}</div>
                    </div>
                    @endforeach
                </div>
                <div class="ka-plan-actions">
                    <a href="/admin/memberships/{{ $plan->id }}/edit" class="ka-btn ka-btn-light" style="font-size:12px;padding:5px 12px">✎ Edit</a>
                    <form method="post" action="/admin/memberships/{{ $plan->id }}/toggle" class="ka-plan-actions-col">@csrf
                        <button class="ka-btn {{ $plan->is_active ? 'ka-btn-light' : 'ka-btn-primary' }}" style="font-size:12px;padding:5px 12px">
                            {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    <form method="post" action="/admin/memberships/{{ $plan->id }}" class="ka-plan-actions-col" data-confirm="Delete this plan?">@csrf @method('DELETE')
                        <button class="ka-btn" style="font-size:12px;padding:5px 10px;background:#fef2f2;color:#b91c1c;border:1px solid #fca5a5">🗑</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="ka-empty-sm">
        <div class="ka-empty-icon">💎</div>
        <div class="ka-empty-title">No membership plans yet</div>
        <div class="ka-empty-sub">Add your first plan below</div>
    </div>
    @endif
</div>

{{-- Add Plan --}}
<div class="sa-card" id="add-plan">
    <div class="sa-card-head">
        <h2 class="ka-flex-row-8"><span style="font-size:18px">✚</span> Add New Plan</h2>
    </div>
    <form method="post" action="/admin/memberships" style="padding:20px">
    @csrf
    @if($errors->any())
    <div class="ka-error-banner">
        {{ $errors->first() }}
    </div>
    @endif
    <div class="ka-grid-auto-200">
        <div class="ka-grid-col-full">
            <label class="ka-label">Plan Name <span style="color:#ef4444">*</span></label>
            <input name="name" class="ka-input" placeholder="e.g. Gold Plan" value="{{ old('name') }}" required>
        </div>
        <div>
            <label class="ka-label">Slug <span style="color:#ef4444">*</span></label>
            <input name="slug" class="ka-input" placeholder="gold" value="{{ old('slug') }}" required>
            <div class="ka-fs-11 ka-text-muted" style="margin-top:4px">Lowercase, no spaces (e.g. gold, platinum)</div>
        </div>
        <div>
            <label class="ka-label">Price (LKR) <span style="color:#ef4444">*</span></label>
            <input name="price" type="number" step="0.01" class="ka-input" placeholder="0.00" value="{{ old('price') }}" required>
        </div>
        <div>
            <label class="ka-label">Duration (days) <span style="color:#ef4444">*</span></label>
            <input name="duration_days" type="number" class="ka-input" placeholder="30" value="{{ old('duration_days') }}" required>
        </div>
        <div>
            <label class="ka-label">Ad Limit <span style="color:#ef4444">*</span></label>
            <input name="ad_limit" type="number" class="ka-input" placeholder="10" value="{{ old('ad_limit') }}" required>
        </div>
        <div>
            <label class="ka-label">Product Limit <span style="color:#ef4444">*</span></label>
            <input name="product_limit" type="number" class="ka-input" placeholder="10" value="{{ old('product_limit') }}" required>
        </div>
        <div>
            <label class="ka-label">Store Limit <span style="color:#64748b;font-weight:400"> (default 1)</span></label>
            <input name="store_limit" type="number" class="ka-input" placeholder="1" value="{{ old('store_limit') }}">
        </div>
        <div>
            <label class="ka-label">Featured Quota <span style="color:#64748b;font-weight:400"> (default 0)</span></label>
            <input name="featured_quota" type="number" class="ka-input" placeholder="0" value="{{ old('featured_quota') }}">
        </div>
    </div>
    <div class="ka-plan-card-footer">
        <button type="submit" class="ka-btn ka-btn-primary">+ Add Plan</button>
        <a href="/admin/memberships" class="ka-btn ka-btn-light">Cancel</a>
    </div>
    </form>
</div>

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
// Smooth scroll to add-plan form
var scrollBtn = document.getElementById('scroll-to-add');
if (scrollBtn) {
    scrollBtn.addEventListener('click', function(e) {
        e.preventDefault();
        var target = document.getElementById('add-plan');
        if (target) target.scrollIntoView({ behavior: 'smooth' });
    });
}

// Confirm dialogs for forms with data-confirm
document.querySelectorAll('form[data-confirm]').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        if (!confirm(form.dataset.confirm)) e.preventDefault();
    });
});
</script>
@endpush
