@extends('layouts.app')
@section('title','Help Center · Kegalle Marketplace')
@section('meta_description','Guides and answers to help you buy, sell, and stay safe on Kegalle Marketplace.')
@section('content')

{{-- Hero --}}
<div class="k-help-hero">
    <div class="k-help-hero-inner">
        <div class="k-help-hero-badge">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
            Support & Guides
        </div>
        <h1>Help Center</h1>
        <p>Guides and answers to help you get the most out of Kegalle Marketplace.</p>
    </div>
</div>

<div class="k-help-wrap">
    <div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><span class="current">Help Center</span></div>

    {{-- Topic Cards --}}
    <div class="k-help-cards">
        <a href="/how-to-buy" class="k-help-card">
            <div class="k-help-card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            </div>
            <div class="k-help-card-body">
                <strong>How to Buy</strong>
                <span>Learn how to search, filter, and safely contact sellers on the platform.</span>
            </div>
            <div class="k-help-card-arrow">Read guide <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></div>
        </a>
        <a href="/how-to-sell" class="k-help-card">
            <div class="k-help-card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>
            </div>
            <div class="k-help-card-body">
                <strong>How to Sell</strong>
                <span>Post your first ad and grow your sales on the platform.</span>
            </div>
            <div class="k-help-card-arrow">Read guide <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></div>
        </a>
        <a href="/safety-tips" class="k-help-card">
            <div class="k-help-card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <div class="k-help-card-body">
                <strong>Safety Tips</strong>
                <span>Stay safe when meeting buyers and sellers in person.</span>
            </div>
            <div class="k-help-card-arrow">Read guide <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></div>
        </a>
        <a href="/faq" class="k-help-card">
            <div class="k-help-card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div class="k-help-card-body">
                <strong>FAQ</strong>
                <span>Quick answers to the most common questions.</span>
            </div>
            <div class="k-help-card-arrow">Read guide <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg></div>
        </a>
    </div>

    {{-- Still need help --}}
    <div class="k-help-still">
        <div class="k-help-still-inner">
            <div class="k-help-still-icon">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
            </div>
            <h3>Still need help?</h3>
            <p>Can't find what you're looking for? Our local team in Kegalle is happy to help directly — we respond within 24 hours.</p>
            <div class="k-help-still-btns">
                <a href="/contact-us" class="k-btn k-btn-white">Contact Us</a>
                <a href="https://wa.me/94712930930?text={{ urlencode('Hi, I need help with Kegalle Marketplace.') }}" target="_blank" rel="noopener" class="k-btn k-btn-outline-white">Chat on WhatsApp</a>
            </div>
        </div>
    </div>
</div>
@endsection
