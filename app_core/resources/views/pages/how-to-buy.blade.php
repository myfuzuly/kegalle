@extends('layouts.app')
@section('title','How to Buy · Kegalle Marketplace')
@section('meta_description','A simple step-by-step guide to finding, contacting sellers, and safely buying items on Kegalle Marketplace.')
@section('content')

<div class="k-help-hero">
    <div class="k-help-hero-inner">
        <div class="k-help-hero-badge">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            Buyer's Guide
        </div>
        <h1>How to Buy</h1>
        <p>A simple guide to finding and buying what you need on Kegalle Marketplace.</p>
    </div>
</div>

<div class="k-guide-wrap">
    <div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><a href="/help-center">Help Center</a><span class="sep">›</span><span class="current">How to Buy</span></div>

    <div class="k-guide-steps">
        <div class="k-guide-step">
            <div class="k-guide-step-num">1</div>
            <div class="k-guide-step-body">
                <h3>Search or Browse</h3>
                <p>Use the search bar to look for a specific item, or browse by <a href="/categories">category</a> or <a href="/locations">location</a> to discover listings near you.</p>
            </div>
        </div>
        <div class="k-guide-step">
            <div class="k-guide-step-num">2</div>
            <div class="k-guide-step-body">
                <h3>Filter Your Results</h3>
                <p>Narrow down listings by price range, category, location, and ad type using the filters on the <a href="/listings">All Ads</a> page.</p>
            </div>
        </div>
        <div class="k-guide-step">
            <div class="k-guide-step-num">3</div>
            <div class="k-guide-step-body">
                <h3>Review the Listing</h3>
                <p>Check photos, description, price, and seller details carefully. Look for the "Verified Store" badge for added confidence when buying from a business.</p>
            </div>
        </div>
        <div class="k-guide-step">
            <div class="k-guide-step-num">4</div>
            <div class="k-guide-step-body">
                <h3>Contact the Seller</h3>
                <p>Use the Chat, Call, or WhatsApp options on any listing to ask questions or arrange a viewing — directly with the seller, no middlemen.</p>
            </div>
        </div>
        <div class="k-guide-step">
            <div class="k-guide-step-num">5</div>
            <div class="k-guide-step-body">
                <h3>Meet Safely &amp; Complete the Deal</h3>
                <p>Always meet in a safe, public location and inspect the item before paying. Read our full <a href="/safety-tips">Safety Tips</a> before completing any transaction.</p>
            </div>
        </div>
    </div>

    <div class="k-help-still">
        <div class="k-help-still-inner">
            <div class="k-help-still-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <h3>Stay Safe</h3>
            <p>Before you meet a seller, take a moment to review our safety guide for in-person transactions.</p>
            <div class="k-help-still-btns">
                <a href="/safety-tips" class="k-btn k-btn-white">Read Safety Tips</a>
                <a href="/faq" class="k-btn k-btn-outline-white">Browse FAQ</a>
            </div>
        </div>
    </div>
</div>
@endsection
