@extends('layouts.admin')
@section('title','Brands by Category')
@section('page','Brands')
@section('eyebrow','Marketplace')
@section('page_heading','Brands by Category')
@section('subheading','Browse brands organised by parent → sub-category')
@section('actions')
<a href="/admin/brands" class="ka-btn ka-btn-light mr-6">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"  class="icon-inline"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    All Brands
</a>
<a href="/admin/brands/create" class="ka-btn ka-btn-primary">+ Add Brand</a>
@endsection

@push('styles')

@endpush

@section('content')

{{-- Stats --}}
@php
$totalSubcats = $parents->sum(fn($p) => $p->children->count());
$totalAssigned = $parents->sum(fn($p) => $p->all_brands->count());
@endphp
<div class="bbc-summary">
    <div class="bbc-stat"><span class="bbc-stat-num">{{ $totalBrands }}</span><span class="bbc-stat-label">Total Brands</span></div>
    <div class="bbc-stat"><span class="bbc-stat-num">{{ $parents->count() }}</span><span class="bbc-stat-label">Parent Categories</span></div>
    <div class="bbc-stat"><span class="bbc-stat-num">{{ $totalSubcats }}</span><span class="bbc-stat-label">Sub-Categories</span></div>
    <div class="bbc-stat"><span class="bbc-stat-num" style="{{ $uncategorized->isNotEmpty() ? 'color:#e11d48' : '' }}">{{ $uncategorized->count() }}</span><span class="bbc-stat-label">Uncategorized</span></div>
</div>

{{-- Parent category sections --}}
@foreach($parents as $parent)
@php $parentTotalBrands = $parent->all_brands->count(); @endphp
<div class="bbc-parent {{ $parentTotalBrands > 0 ? 'open' : '' }}">
    <div class="bbc-parent-head" data-accordion-toggle="bbc-parent">
        <span class="bbc-parent-icon">{{ $parent->icon ?: '📦' }}</span>
        <span class="bbc-parent-name">{{ $parent->name }}</span>
        <span class="bbc-parent-meta">{{ $parent->children->count() }} sub-categories</span>
        <span class="bbc-parent-badge {{ $parentTotalBrands === 0 ? 'zero' : '' }}">{{ $parentTotalBrands }} brands</span>
        <svg class="bbc-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
    </div>
    <div class="bbc-parent-body">

        {{-- Direct brands on parent category (if any) --}}
        @if($parent->brands->isNotEmpty())
        <div class="bbc-direct">
            <div class="bbc-direct-label">Directly under {{ $parent->name }}</div>
            <div class="bbc-brand-list">
                @foreach($parent->brands as $brand)
                <a href="/admin/brands/{{ $brand->id }}/edit" class="bbc-brand-chip {{ $brand->is_active ? '' : 'inactive' }}">
                    <span class="bbc-chip-dot {{ $brand->is_active ? '' : 'off' }}"></span>{{ $brand->name }}
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Sub-categories --}}
        @forelse($parent->children as $sub)
        @php $subCount = $sub->all_brands->count(); @endphp
        <div class="bbc-sub {{ $subCount > 0 ? 'open' : '' }}">
            <div class="bbc-sub-head" data-accordion-toggle="bbc-sub">
                <span class="bbc-sub-dot"></span>
                <span class="bbc-sub-name">{{ $sub->name }}</span>
                <span class="bbc-sub-count">{{ $subCount }} brand{{ $subCount !== 1 ? 's' : '' }}</span>
                <span class="bbc-sub-badge {{ $subCount === 0 ? 'zero' : '' }}">{{ $subCount }}</span>
                <svg class="bbc-sub-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
            </div>
            <div class="bbc-sub-body">
                @if($sub->all_brands->isEmpty())
                    <div class="bbc-empty">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                        No brands assigned
                        <a href="/admin/brands/create" class="bbc-add-link">+ Add</a>
                    </div>
                @else
                    <div class="bbc-brand-list">
                        @foreach($sub->all_brands as $brand)
                        <a href="/admin/brands/{{ $brand->id }}/edit" class="bbc-brand-chip {{ $brand->is_active ? '' : 'inactive' }}">
                            <span class="bbc-chip-dot {{ $brand->is_active ? '' : 'off' }}"></span>{{ $brand->name }}
                        </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        @empty
        <div class="p16-18-muted">No sub-categories</div>
        @endforelse
    </div>
</div>
@endforeach

{{-- Uncategorized --}}
@if($uncategorized->isNotEmpty())
<div class="bbc-uncategorized">
    <div class="bbc-unc-head">
        <span class="fs-20">⚠️</span>
        <div class="flex-1">
            <div class="fs14-fw7-amber">Uncategorized Brands</div>
            <div class="fs11-amber-mt1">These brands have no category — they won't appear in listing forms</div>
        </div>
        <span class="badge-amber-sm">{{ $uncategorized->count() }}</span>
    </div>
    <div class="p14-18">
        <div class="bbc-brand-list">
            @foreach($uncategorized as $brand)
            <a href="/admin/brands/{{ $brand->id }}/edit" class="bbc-brand-chip {{ $brand->is_active ? '' : 'inactive' }}">
                <span class="bbc-chip-dot {{ $brand->is_active ? '' : 'off' }}"></span>{{ $brand->name }}
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('click',function(e){
    var head=e.target.closest('[data-accordion-toggle]');
    if(!head) return;
    head.closest('.'+head.dataset.accordionToggle).classList.toggle('open');
});
</script>
@endpush
