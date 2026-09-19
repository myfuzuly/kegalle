@extends('layouts.app')
@section('title','Kegalle Wholesale Market Prices')
@section('meta_description','Daily wholesale market prices from Kegalle pola — vegetables, fruits, grains, spices and more. Updated every morning.')

@section('content')

{{-- Hero --}}
<div class="mp-hero">
    <div class="mp-hero-inner">
        <div class="mp-hero-badge">
            <span class="mp-badge-dot"></span>
            Live Market Data
        </div>
        <h1 class="mp-hero-h1">Kegalle Wholesale<br><em>Market Prices</em></h1>
        <p class="mp-hero-sub">Daily prices from Kegalle pola. Updated every morning at 11:00 AM.</p>
        @if($updatedAt)
        <div class="mp-hero-date">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Last updated: <strong>{{ $updatedAt }}</strong>
        </div>
        @endif
    </div>
</div>

<div class="mp-wrap">

@if($grouped->isEmpty())
    <div class="mp-empty">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
        <p>Prices are updated daily at 11:00 AM. Check back soon.</p>
    </div>
@else

    {{-- Category nav --}}
    <div class="mp-cat-nav">
        @foreach($grouped->keys() as $cat)
        <a href="#cat-{{ Str::slug($cat) }}" class="mp-cat-pill">{{ $cat }}</a>
        @endforeach
    </div>

    {{-- Price tables by category --}}
    @foreach($grouped as $category => $items)
    <div class="mp-section" id="cat-{{ Str::slug($category) }}">
        <div class="mp-section-head">
            <h2 class="mp-section-title">{{ $category }}</h2>
            <span class="mp-section-count">{{ count($items) }} items</span>
        </div>
        <div class="mp-table-wrap">
            <table class="mp-table">
                <thead>
                    <tr>
                        <th>Commodity</th>
                        <th>Unit</th>
                        <th class="mp-num">Min (LKR)</th>
                        <th class="mp-num">Max (LKR)</th>
                        <th class="mp-num mp-col-avg">Avg (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($items as $row)
                <tr>
                    <td class="mp-commodity">{{ $row->commodity }}</td>
                    <td class="mp-unit">{{ $row->unit }}</td>
                    <td class="mp-num">{{ $row->min_price ? number_format($row->min_price, 2) : '—' }}</td>
                    <td class="mp-num">{{ $row->max_price ? number_format($row->max_price, 2) : '—' }}</td>
                    <td class="mp-num mp-col-avg mp-avg-val">{{ $row->avg_price ? number_format($row->avg_price, 2) : '—' }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

@endif

    <p class="mp-footer-note">
        Prices are indicative wholesale rates sourced from Kegalle market.
        Retail prices may vary. Updated daily at 11:00 AM (Sri Lanka Standard Time).
    </p>
</div>

<style>
.mp-hero{background:linear-gradient(135deg,#0f3d2a 0%,#1a5c3a 60%,#2d7a52 100%);padding:3rem 1rem 2.5rem;text-align:center}
.mp-hero-inner{max-width:640px;margin:0 auto}
.mp-hero-badge{display:inline-flex;align-items:center;gap:.45rem;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:999px;padding:.3rem .9rem;font-size:.75rem;font-weight:600;color:rgba(255,255,255,.9);letter-spacing:.04em;margin-bottom:1.25rem;text-transform:uppercase}
.mp-badge-dot{width:7px;height:7px;background:#4ade80;border-radius:50%;animation:mpPulse 2s ease-in-out infinite}
@keyframes mpPulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.6;transform:scale(.85)}}
.mp-hero-h1{font-size:clamp(1.6rem,4vw,2.4rem);font-weight:800;color:#fff;line-height:1.2;margin:0 0 .75rem}
.mp-hero-h1 em{font-style:normal;color:#86efac}
.mp-hero-sub{color:rgba(255,255,255,.7);font-size:.95rem;margin:0 0 1rem}
.mp-hero-date{display:inline-flex;align-items:center;gap:.4rem;font-size:.8rem;color:rgba(255,255,255,.6);background:rgba(255,255,255,.08);padding:.3rem .8rem;border-radius:6px}
.mp-hero-date strong{color:rgba(255,255,255,.85)}
.mp-wrap{max-width:860px;margin:0 auto;padding:1.5rem 1rem 3rem}
.mp-cat-nav{display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1.75rem}
.mp-cat-pill{display:inline-flex;align-items:center;padding:.3rem .85rem;border-radius:999px;font-size:.78rem;font-weight:600;background:rgba(var(--k-green-rgb,22,101,52),.08);color:var(--k-green,#166534);border:1px solid rgba(var(--k-green-rgb,22,101,52),.15);text-decoration:none;transition:background .15s,color .15s}
.mp-cat-pill:hover{background:var(--k-green,#166534);color:#fff}
.mp-section{margin-bottom:2.25rem}
.mp-section-head{display:flex;align-items:baseline;justify-content:space-between;margin-bottom:.75rem;border-bottom:2px solid var(--k-green,#166534);padding-bottom:.5rem}
.mp-section-title{font-size:1rem;font-weight:700;color:var(--k-green,#166534);margin:0}
.mp-section-count{font-size:.75rem;color:#94a3b8}
.mp-table-wrap{overflow-x:auto}
.mp-table{width:100%;border-collapse:collapse;font-size:.875rem}
.mp-table thead th{padding:.55rem .75rem;background:rgba(var(--k-green-rgb,22,101,52),.06);color:#475569;font-size:.72rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;border-bottom:1px solid #e2e8f0;white-space:nowrap}
.mp-table tbody tr{border-bottom:1px solid #f1f5f9;transition:background .12s}
.mp-table tbody tr:last-child{border-bottom:none}
.mp-table tbody tr:hover{background:#f8fafc}
.mp-table td{padding:.55rem .75rem;color:#1e293b;vertical-align:middle}
.mp-num{text-align:right;font-variant-numeric:tabular-nums}
.mp-commodity{font-weight:600;color:#1e293b}
.mp-unit{color:#64748b;font-size:.8rem}
.mp-col-avg{font-weight:700;color:var(--k-green,#166534)}
.mp-avg-val{color:var(--k-green,#166534)!important}
.mp-empty{text-align:center;padding:4rem 1rem;color:#94a3b8}
.mp-empty p{margin:.75rem 0 0;font-size:.9rem}
.mp-footer-note{text-align:center;font-size:.78rem;color:#94a3b8;margin-top:2rem;line-height:1.6}
@media(max-width:480px){.mp-col-avg{display:none}}
@media(prefers-color-scheme:dark){
:root:not([data-theme="light"]) .mp-table thead th{background:rgba(134,239,172,.06);color:#94a3b8;border-color:#1e293b}
:root:not([data-theme="light"]) .mp-table tbody tr{border-color:#1e293b}
:root:not([data-theme="light"]) .mp-table tbody tr:hover{background:#0f172a}
:root:not([data-theme="light"]) .mp-table td{color:#e2e8f0}
:root:not([data-theme="light"]) .mp-commodity{color:#f1f5f9}
:root:not([data-theme="light"]) .mp-cat-pill{background:rgba(134,239,172,.08);color:#86efac;border-color:rgba(134,239,172,.15)}
:root:not([data-theme="light"]) .mp-cat-pill:hover{background:#166534;color:#fff}
}
</style>

@endsection
