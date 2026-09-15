@extends('layouts.app')
@section('title','FAQ · Kegalle Marketplace')
@section('meta_description','Answers to frequently asked questions about buying, selling, and using Kegalle Marketplace.')
@section('content')

<div class="k-help-hero">
    <div class="k-help-hero-inner">
        <div class="k-help-hero-badge">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Frequently Asked
        </div>
        <h1>FAQ</h1>
        <p>Quick answers to the most common questions about Kegalle Marketplace.</p>
    </div>
</div>

<div class="k-guide-wrap">
    <div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><a href="/help-center">Help Center</a><span class="sep">›</span><span class="current">FAQ</span></div>

    <div class="k-faq-pills">
        <a href="#buying" class="k-faq-pill">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            Buying
        </a>
        <a href="#selling" class="k-faq-pill">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
            Selling
        </a>
        <a href="#account" class="k-faq-pill">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Account
        </a>
        <a href="#safety" class="k-faq-pill">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Safety
        </a>
    </div>

    <div class="k-faq-section" id="buying">
        <h2 class="k-faq-section-h2">
            <div class="k-faq-section-h2-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            </div>
            Buying
        </h2>
        <details class="k-faq-item" open><summary>How do I contact a seller?</summary><div class="k-faq-ans"><p>Open any listing and use the Chat, Call, or WhatsApp buttons to reach the seller directly. You can also visit their store page for more contact options.</p></div></details>
        <details class="k-faq-item"><summary>How do I save a listing for later?</summary><div class="k-faq-ans"><p>Tap the heart icon on any listing card to save it. Your saved listings are stored on your device and accessible from the <a href="/saved">Saved</a> page.</p></div></details>
        <details class="k-faq-item"><summary>Can I negotiate the price?</summary><div class="k-faq-ans"><p>Yes — prices on most listings are negotiable. Use the Chat or WhatsApp button to discuss pricing with the seller directly.</p></div></details>
        <details class="k-faq-item"><summary>How do I search for specific items?</summary><div class="k-faq-ans"><p>Use the search bar at the top of any page. You can also filter by category, location, price range, and listing type on the <a href="/listings">All Ads</a> page.</p></div></details>
    </div>

    <div class="k-faq-section" id="selling">
        <h2 class="k-faq-section-h2">
            <div class="k-faq-section-h2-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
            </div>
            Selling
        </h2>
        <details class="k-faq-item"><summary>Is it free to post an ad?</summary><div class="k-faq-ans"><p>Yes, posting a standard classified ad is completely free. Optional paid features like featured placement and store accounts have their own pricing, visible in your <a href="/dashboard/membership">membership</a> options.</p></div></details>
        <details class="k-faq-item"><summary>How do I create a store?</summary><div class="k-faq-ans"><p>Register with the "Store / Business" account type, then create your store from your <a href="/dashboard/stores/create">dashboard</a>. Stores go live after a quick review (usually within 24 hours).</p></div></details>
        <details class="k-faq-item"><summary>How long does a listing stay active?</summary><div class="k-faq-ans"><p>Standard listings stay active for 30 days. You can renew or manage all your ads from <a href="/dashboard/listings">My Listings</a>.</p></div></details>
        <details class="k-faq-item"><summary>How do I make my ad more visible?</summary><div class="k-faq-ans"><p>You can feature your listing or upgrade to a premium membership for higher visibility. Featured listings appear at the top of search results and on the homepage.</p></div></details>
    </div>

    <div class="k-faq-section" id="account">
        <h2 class="k-faq-section-h2">
            <div class="k-faq-section-h2-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            Account &amp; Settings
        </h2>
        <details class="k-faq-item"><summary>How do I create an account?</summary><div class="k-faq-ans"><p>Visit the <a href="/register">registration page</a> and choose either "Store / Business" or "Personal / Classified" account type.</p></div></details>
        <details class="k-faq-item"><summary>I forgot my password. How do I reset it?</summary><div class="k-faq-ans"><p>Click "Forgot Password" on the <a href="/login">login page</a>, enter your email, and follow the reset link sent to your inbox.</p></div></details>
        <details class="k-faq-item"><summary>Can I change my account type later?</summary><div class="k-faq-ans"><p>Yes — contact our support team via the <a href="/contact-us">Contact Us</a> page and we'll help you upgrade or change your account type.</p></div></details>
    </div>

    <div class="k-faq-section" id="safety">
        <h2 class="k-faq-section-h2">
            <div class="k-faq-section-h2-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            Safety
        </h2>
        <details class="k-faq-item"><summary>What should I do if I suspect a scam?</summary><div class="k-faq-ans"><p>Use the "Report Ad" button on the listing, avoid sending any payment before seeing the item, and review our <a href="/safety-tips">Safety Tips</a> for detailed guidance.</p></div></details>
        <details class="k-faq-item"><summary>Is my personal information safe?</summary><div class="k-faq-ans"><p>Yes. We never share your personal details with other users. Only the contact information you choose to display on your listings or store is visible. Read our <a href="/privacy-policy">Privacy Policy</a> for details.</p></div></details>
        <details class="k-faq-item"><summary>How do I report a suspicious user?</summary><div class="k-faq-ans"><p>Use the Report button on any listing or store page, or contact us directly via the <a href="/contact-us">Contact Us</a> page with details.</p></div></details>
    </div>

    <div class="k-help-still">
        <div class="k-help-still-inner">
            <div class="k-help-still-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
            </div>
            <h3>Still have questions?</h3>
            <p>Our team is happy to help directly. We respond within 24 hours.</p>
            <div class="k-help-still-btns">
                <a href="/contact-us" class="k-btn k-btn-white">Contact Us</a>
                <a href="https://wa.me/94712930930?text={{ urlencode('Hi, I have a question about Kegalle Marketplace.') }}" target="_blank" rel="noopener" class="k-btn k-btn-outline-white">Chat on WhatsApp</a>
            </div>
        </div>
    </div>
</div>
@endsection
