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
        <h2 style="display:flex;align-items:center;gap:8px"><span style="font-size:20px">💎</span> Active Plans</h2>
        <span>{{ $plans->count() }} plan{{ $plans->count() !== 1 ? 's' : '' }}</span>
    </div>

    @if($plans->count())
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;padding:20px">
        @foreach($plans as $plan)
        @php
          $accent = match(strtolower($plan->slug ?? $plan->name)) {
            'gold','gold plan'     => ['color'=>'#b45309','bg'=>'#fffbeb','border'=>'#fcd34d'],
            'platinum'             => ['color'=>'#6d28d9','bg'=>'#f5f3ff','border'=>'#c4b5fd'],
            'silver','silver plan' => ['color'=>'#475569','bg'=>'#f8fafc','border'=>'#cbd5e1'],
            default                => ['color'=>'#1b5e20','bg'=>'#f0fdf4','border'=>'#86efac'],
          };
        @endphp
        <div style="border:2px solid {{ $plan->is_active ? $accent['border'] : '#e2e8f0' }};border-radius:12px;overflow:hidden;background:var(--ka-surface,#fff)">
            <div style="background:{{ $plan->is_active ? $accent['bg'] : '#f8fafc' }};padding:16px 18px;border-bottom:1px solid {{ $accent['border'] }}">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px">
                    <span style="font-size:17px;font-weight:800;color:{{ $plan->is_active ? $accent['color'] : '#94a3b8' }}">
                        {{ $plan->name }}
                    </span>
                    <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;background:{{ $plan->is_active ? '#dcfce7' : '#f1f5f9' }};color:{{ $plan->is_active ? '#15803d' : '#94a3b8' }}">
                        {{ $plan->is_active ? '● Active' : '○ Inactive' }}
                    </span>
                </div>
                <div style="font-size:22px;font-weight:800;color:var(--ka-text,#0f172a)">
                    LKR {{ number_format($plan->price, 0) }}
                    <span style="font-size:13px;font-weight:400;color:#64748b">/ {{ $plan->duration_days }}d</span>
                </div>
            </div>
            <div style="padding:14px 18px">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:14px">
                    @foreach([
                        ['Ads',      $plan->ad_limit],
                        ['Products', $plan->product_limit],
                        ['Stores',   $plan->store_limit ?? 1],
                        ['Featured', $plan->featured_quota ?? 0],
                    ] as [$lbl, $val])
                    <div style="background:var(--ka-bg,#f8fafc);border-radius:7px;padding:8px 10px">
                        <div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.04em">{{ $lbl }}</div>
                        <div style="font-size:16px;font-weight:800;color:var(--ka-text,#0f172a)">{{ $val }}</div>
                    </div>
                    @endforeach
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <a href="/admin/memberships/{{ $plan->id }}/edit" class="ka-btn ka-btn-light" style="font-size:12px;padding:5px 12px">✎ Edit</a>
                    <form method="post" action="/admin/memberships/{{ $plan->id }}/toggle" style="display:contents">@csrf
                        <button class="ka-btn {{ $plan->is_active ? 'ka-btn-light' : 'ka-btn-primary' }}" style="font-size:12px;padding:5px 12px">
                            {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    <form method="post" action="/admin/memberships/{{ $plan->id }}" style="display:contents" data-confirm="Delete this plan?">@csrf @method('DELETE')
                        <button class="ka-btn" style="font-size:12px;padding:5px 10px;background:#fef2f2;color:#b91c1c;border:1px solid #fca5a5">🗑</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div style="text-align:center;padding:48px;color:#94a3b8">
        <div style="font-size:40px;margin-bottom:10px">💎</div>
        <div style="font-weight:600;font-size:14px">No membership plans yet</div>
        <div style="font-size:13px;margin-top:4px">Add your first plan below</div>
    </div>
    @endif
</div>

{{-- Add Plan --}}
<div class="sa-card" id="add-plan">
    <div class="sa-card-head">
        <h2 style="display:flex;align-items:center;gap:8px"><span style="font-size:18px">✚</span> Add New Plan</h2>
    </div>
    <form method="post" action="/admin/memberships" style="padding:20px">
    @csrf
    @if($errors->any())
    <div style="background:#fef2f2;color:#b91c1c;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px">
        {{ $errors->first() }}
    </div>
    @endif
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px">
        <div style="grid-column:1/-1">
            <label class="ka-label">Plan Name <span style="color:#ef4444">*</span></label>
            <input name="name" class="ka-input" placeholder="e.g. Gold Plan" value="{{ old('name') }}" required>
        </div>
        <div>
            <label class="ka-label">Slug <span style="color:#ef4444">*</span></label>
            <input name="slug" class="ka-input" placeholder="gold" value="{{ old('slug') }}" required>
            <div style="font-size:11px;color:#94a3b8;margin-top:4px">Lowercase, no spaces (e.g. gold, platinum)</div>
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
    <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--ka-border,#e2e8f0);display:flex;gap:10px">
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
