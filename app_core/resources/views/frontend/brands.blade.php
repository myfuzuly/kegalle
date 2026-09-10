@extends('layouts.app')

@section('title','All Brands in Kegalle — Shop by Brand · Kegalle Marketplace')
@section('meta_description','Browse all brands available on Kegalle Marketplace. Shop products by brand from trusted local sellers and stores across the Kegalle district.')
@section('canonical', url('/brand'))

@section('content')
<section class="cats-hero"><div class="cats-hero-inner"><h1>Shop by Brand</h1><p>Browse {{ $brands->count() }} brands available from sellers across Kegalle.</p></div></section>

<div class="container k-brands-wrap">
    <div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><span class="current">Brands</span></div>

    <div class="k-brand-search-wrap">
        <input type="text" id="brandSearch" class="k-brand-search" placeholder="Search brands..." oninput="filterBrands(this.value)">
    </div>

    @php
        $grouped = $brands->groupBy(fn ($b) => strtoupper(substr($b->name, 0, 1)));
    @endphp

    @foreach($grouped as $letter => $group)
        <div class="brand-letter-group k-brand-letter-group">
            <div class="k-brand-letter-head">{{ $letter }}</div>
            <div class="k-brand-grid">
                @foreach($group as $brand)
                    <a href="/brand/{{ $brand->slug }}" class="brand-tile k-brand-item" data-name="{{ strtolower($brand->name) }}">
                        <span class="k-brand-name">{{ $brand->name }}</span>
                        @if($brand->listings_count > 0)
                            <span class="k-brand-count">{{ $brand->listings_count }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach

    <div id="brandNoResults" class="k-brand-no-results">No brands match your search.</div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
function filterBrands(q) {
    q = q.trim().toLowerCase();
    var any = false;
    document.querySelectorAll('.brand-tile').forEach(function(tile) {
        var show = !q || tile.dataset.name.indexOf(q) > -1;
        tile.style.display = show ? '' : 'none';
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
