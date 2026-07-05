@extends('layouts.app')

@section('title','All Brands in Kegalle — Shop by Brand · Kegalle Marketplace')
@section('meta_description','Browse all brands available on Kegalle Marketplace. Shop products by brand from trusted local sellers and stores across the Kegalle district.')
@section('canonical', url('/brand'))

@section('content')
<section class="cats-hero"><div class="cats-hero-inner"><h1>Shop by Brand</h1><p>Browse {{ $brands->count() }} brands available from sellers across Kegalle.</p></div></section>

<div class="container" style="padding-top:28px;padding-bottom:48px">
    <div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><span class="current">Brands</span></div>

    {{-- Search filter --}}
    <div style="max-width:420px;margin:18px 0">
        <input type="text" id="brandSearch" placeholder="Search brands..." style="width:100%;height:46px;border:1.5px solid var(--k-border,#E5E8EF);border-radius:12px;padding:0 16px;font-size:14px;outline:none" oninput="filterBrands(this.value)">
    </div>

    @php
        $grouped = $brands->groupBy(fn ($b) => strtoupper(substr($b->name, 0, 1)));
    @endphp

    @foreach($grouped as $letter => $group)
        <div class="brand-letter-group" style="margin-bottom:26px">
            <div style="font-size:15px;font-weight:800;color:var(--k-primary,#1B5E20);border-bottom:2px solid var(--k-primary-xlight,#E8F5E9);padding-bottom:6px;margin-bottom:14px">{{ $letter }}</div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px">
                @foreach($group as $brand)
                    <a href="/brand/{{ $brand->slug }}" class="brand-tile" data-name="{{ strtolower($brand->name) }}"
                       style="display:flex;align-items:center;justify-content:space-between;gap:8px;background:var(--k-surface,#fff);border:1.5px solid var(--k-border,#E5E8EF);border-radius:12px;padding:13px 16px;text-decoration:none;color:var(--k-text-primary,#1a202c);font-weight:600;font-size:13.5px;transition:all .15s"
                       onmouseover="this.style.borderColor='var(--k-primary,#1B5E20)';this.style.transform='translateY(-2px)'"
                       onmouseout="this.style.borderColor='var(--k-border,#E5E8EF)';this.style.transform='none'">
                        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $brand->name }}</span>
                        @if($brand->listings_count > 0)
                            <span style="flex-shrink:0;background:var(--k-primary-xlight,#E8F5E9);color:var(--k-primary,#1B5E20);font-size:11px;font-weight:700;padding:2px 8px;border-radius:12px">{{ $brand->listings_count }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach

    <div id="brandNoResults" style="display:none;text-align:center;padding:40px 20px;color:#98a2b3;font-size:14px">No brands match your search.</div>
</div>
@endsection

@push('scripts')
<script>
function filterBrands(q) {
    q = q.trim().toLowerCase();
    var any = false;
    document.querySelectorAll('.brand-tile').forEach(function(tile) {
        var show = !q || tile.dataset.name.indexOf(q) > -1;
        tile.style.display = show ? 'flex' : 'none';
        if (show) any = true;
    });
    document.querySelectorAll('.brand-letter-group').forEach(function(group) {
        var visible = Array.prototype.some.call(group.querySelectorAll('.brand-tile'), function(t) { return t.style.display !== 'none'; });
        group.style.display = visible ? '' : 'none';
    });
    document.getElementById('brandNoResults').style.display = any ? 'none' : 'block';
}
</script>
@endpush
