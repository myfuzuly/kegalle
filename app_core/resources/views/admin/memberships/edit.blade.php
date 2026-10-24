@extends('layouts.admin')

@section('title','Edit Membership Plan')
@section('page','Memberships')
@section('heading','Edit Membership Plan')
@section('subheading','Update pricing, limits and visibility')

@section('actions')
<a class="ka-btn ka-btn-light" href="/admin/memberships">← Back to Plans</a>
@endsection

@section('content')

@if($errors->any())
<div class="ka-error-banner">
    <strong>Please fix the errors below:</strong>
    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

@php
$accent = match(strtolower($plan->slug ?? '')) {
    'gold'     => ['color'=>'#b45309','bg'=>'#fffbeb','border'=>'#fcd34d'],
    'platinum' => ['color'=>'#6d28d9','bg'=>'#f5f3ff','border'=>'#c4b5fd'],
    'silver'   => ['color'=>'#475569','bg'=>'#f8fafc','border'=>'#cbd5e1'],
    default    => ['color'=>'#1b5e20','bg'=>'#f0fdf4','border'=>'#86efac'],
};
@endphp

<div class="ka-grid-1-340">

  {{-- Main form --}}
  <div class="sa-card">
    <div class="sa-card-head">
        <h2>Plan Details</h2>
        <span class="ka-fs-13 ka-text-muted">ID #{{ $plan->id }}</span>
    </div>
    <form method="post" action="/admin/memberships/{{ $plan->id }}" style="padding:20px">
        @csrf
        @method('PUT')

        <div class="ka-grid-2">
            <div class="ka-grid-col-full">
                <label class="ka-label">Plan Name <span class="ks-c9e348">*</span></label>
                <input name="name" class="ka-input" value="{{ old('name', $plan->name) }}" required>
            </div>
            <div>
                <label class="ka-label">Slug <span class="ks-c9e348">*</span></label>
                <input name="slug" class="ka-input" value="{{ old('slug', $plan->slug) }}" required>
                <div class="ka-fs-11 ka-text-muted ks-a18ac8">Used in upgrade URL. Lowercase, no spaces.</div>
            </div>
            <div>
                <label class="ka-label">Price (LKR) <span class="ks-c9e348">*</span></label>
                <input name="price" type="number" step="0.01" class="ka-input" value="{{ old('price', $plan->price) }}" required>
            </div>
            <div>
                <label class="ka-label">Duration (days) <span class="ks-c9e348">*</span></label>
                <input name="duration_days" type="number" class="ka-input" value="{{ old('duration_days', $plan->duration_days) }}" required>
            </div>
            <div>
                <label class="ka-label">Ad Limit <span class="ks-c9e348">*</span></label>
                <input name="ad_limit" type="number" class="ka-input" value="{{ old('ad_limit', $plan->ad_limit) }}" required>
            </div>
            <div>
                <label class="ka-label">Product Limit <span class="ks-c9e348">*</span></label>
                <input name="product_limit" type="number" class="ka-input" value="{{ old('product_limit', $plan->product_limit) }}" required>
            </div>
            <div>
                <label class="ka-label">Store Limit</label>
                <input name="store_limit" type="number" class="ka-input" value="{{ old('store_limit', $plan->store_limit) }}" placeholder="1">
            </div>
            <div>
                <label class="ka-label">Featured Quota</label>
                <input name="featured_quota" type="number" class="ka-input" value="{{ old('featured_quota', $plan->featured_quota) }}" placeholder="0">
            </div>
        </div>

        <div class="ka-plan-card-footer">
            <button type="submit" class="ka-btn ka-btn-primary">Save Changes</button>
            <a class="ka-btn ka-btn-light" href="/admin/memberships">Cancel</a>
        </div>
    </form>
  </div>

  {{-- Sidebar: plan preview + toggle --}}
  <div>
    <div class="sa-card ka-overflow-hidden">
      <div class="ka-plan-card-head" style="background:{{ $accent['bg'] }};border-bottom-color:{{ $accent['border'] }}">
        <div class="ka-plan-stat-label">Previewing</div>
        <div class="ka-plan-name" style="color:{{ $accent['color'] }}">{{ $plan->name }}</div>
        <div class="ka-plan-price ks-a18ac8">LKR {{ number_format($plan->price,0) }}</div>
        <div class="ka-fs-12 ka-text-muted">{{ $plan->duration_days }} days · {{ $plan->ad_limit }} ads</div>
      </div>
      <div class="ks-1349f2">
        <div class="ka-plan-head-row ks-fa3e1c">
            <span class="ka-fw-600 ka-fs-13">Status</span>
            <span class="ka-plan-status-badge" style="background:{{ $plan->is_active ? '#dcfce7' : '#f1f5f9' }};color:{{ $plan->is_active ? '#15803d' : '#94a3b8' }}">
                {{ $plan->is_active ? '● Active' : '○ Inactive' }}
            </span>
        </div>
        <form method="post" action="/admin/memberships/{{ $plan->id }}/toggle">
            @csrf
            <button type="submit" class="ka-btn {{ $plan->is_active ? 'ka-btn-light' : 'ka-btn-primary' }}" style="width:100%;justify-content:center">
                {{ $plan->is_active ? 'Deactivate Plan' : 'Activate Plan' }}
            </button>
        </form>
        <div class="ks-e1bfb8">
            <form method="post" action="/admin/memberships/{{ $plan->id }}" data-confirm="Permanently delete this plan? This cannot be undone.">
                @csrf @method('DELETE')
                <button type="submit" class="ka-btn ks-578f9f">
                    🗑 Delete Plan
                </button>
            </form>
        </div>
      </div>
    </div>
  </div>

</div>

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.querySelectorAll('form[data-confirm]').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        if (!confirm(form.dataset.confirm)) e.preventDefault();
    });
});
</script>
@endpush
