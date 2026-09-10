@extends('layouts.app')
@section('title','Safety Tips · Kegalle Marketplace')
@section('meta_description','Simple safety precautions to follow when buying or selling on Kegalle Marketplace.')
@section('content')

<div class="k-help-hero">
    <div class="k-help-hero-inner">
        <div class="k-help-hero-badge">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Stay Safe
        </div>
        <h1>Safety Tips</h1>
        <p>Simple precautions to keep every transaction safe.</p>
    </div>
</div>

<div class="k-guide-wrap">
    <div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><a href="/help-center">Help Center</a><span class="sep">›</span><span class="current">Safety Tips</span></div>

    <div class="k-guide-section">
        <h2 class="k-guide-section-h2">
            <div class="k-guide-section-h2-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            When Meeting in Person
        </h2>
        <ul class="k-guide-list">
            <li>Meet in a safe, public, well-lit location — preferably during daytime</li>
            <li>Bring a friend or family member along if possible</li>
            <li>Tell someone where you're going and who you're meeting</li>
        </ul>
    </div>

    <div class="k-guide-section">
        <h2 class="k-guide-section-h2">
            <div class="k-guide-section-h2-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            Before You Pay
        </h2>
        <ul class="k-guide-list">
            <li>Inspect the item carefully and confirm it matches the listing description</li>
            <li>Avoid sending advance payments to sellers you haven't met or verified</li>
            <li>Use cash or secure payment methods for in-person transactions</li>
        </ul>
    </div>

    <div class="k-guide-section">
        <h2 class="k-guide-section-h2">
            <div class="k-guide-section-h2-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            Spotting Red Flags
        </h2>
        <ul class="k-guide-list">
            <li>Prices that seem too good to be true</li>
            <li>Sellers who pressure you to pay immediately or off-platform</li>
            <li>Requests for unusual payment methods (gift cards, wire transfers to strangers)</li>
        </ul>
    </div>

    <div class="k-guide-section">
        <h2 class="k-guide-section-h2">
            <div class="k-guide-section-h2-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
            </div>
            Reporting a Problem
        </h2>
        <div class="k-guide-report">
            <p>If you encounter a suspicious listing or user, use the "Report Ad" option on the listing page, or <a href="/contact-us">contact us</a> directly. We review every report promptly.</p>
        </div>
    </div>

    <div class="k-help-still">
        <div class="k-help-still-inner">
            <div class="k-help-still-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
            </div>
            <h3>Need to report something?</h3>
            <p>Our local team is ready to help. We take every report seriously.</p>
            <div class="k-help-still-btns">
                <a href="/contact-us" class="k-btn k-btn-white">Contact Us</a>
                <a href="https://wa.me/94712930930?text={{ urlencode('Hi, I need to report a problem on Kegalle Marketplace.') }}" target="_blank" rel="noopener" class="k-btn k-btn-outline-white">WhatsApp Us</a>
            </div>
        </div>
    </div>
</div>
@endsection
