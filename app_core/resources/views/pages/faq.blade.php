@extends('layouts.app')
@section('title','FAQ · Kegalle Marketplace')
@section('meta_description','Answers to frequently asked questions about buying, selling, and using Kegalle Marketplace.')
@section('content')
<section class="cats-hero"><div class="cats-hero-inner"><h1>Frequently Asked Questions</h1><p>Quick answers to common questions about Kegalle Marketplace.</p></div></section>
<div class="container" style="padding-top:32px;padding-bottom:48px">
<div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><a href="/help-center">Help Center</a><span class="sep">›</span><span class="current">FAQ</span></div>

<div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:28px">
    <a href="#buying" class="k-btn k-btn-outline" style="font-size:13px;padding:8px 16px;border-radius:20px">🛍 Buying</a>
    <a href="#selling" class="k-btn k-btn-outline" style="font-size:13px;padding:8px 16px;border-radius:20px">📦 Selling</a>
    <a href="#account" class="k-btn k-btn-outline" style="font-size:13px;padding:8px 16px;border-radius:20px">👤 Account</a>
    <a href="#safety" class="k-btn k-btn-outline" style="font-size:13px;padding:8px 16px;border-radius:20px">🛡 Safety</a>
</div>

<div class="blog-content" style="max-width:none;padding:0">

<h2 id="buying" style="margin-top:0">🛍 Buying</h2>

<details open><summary><strong>How do I contact a seller?</strong></summary>
<p>Open any listing and use the Chat, Call, or WhatsApp buttons to reach the seller directly. You can also visit their store page for more contact options.</p></details>

<details><summary><strong>How do I save a listing for later?</strong></summary>
<p>Tap the heart icon on any listing card to save it. Your saved listings are stored on your device and accessible from the <a href="/saved">Saved</a> page.</p></details>

<details><summary><strong>Can I negotiate the price?</strong></summary>
<p>Yes — prices on most listings are negotiable. Use the Chat or WhatsApp button to discuss pricing with the seller directly.</p></details>

<details><summary><strong>How do I search for specific items?</strong></summary>
<p>Use the search bar at the top of any page. You can also filter by category, location, price range, and listing type on the <a href="/listings">All Ads</a> page.</p></details>

<h2 id="selling">📦 Selling</h2>

<details><summary><strong>Is it free to post an ad?</strong></summary>
<p>Yes, posting a standard classified ad is completely free. Optional paid features like featured placement and store accounts have their own pricing, visible in your <a href="/dashboard/membership">membership</a> options.</p></details>

<details><summary><strong>How do I create a store?</strong></summary>
<p>Register with the "Store / Business" account type, then create your store from your <a href="/dashboard/stores/create">dashboard</a>. Stores go live after a quick review (usually within 24 hours).</p></details>

<details><summary><strong>How long does a listing stay active?</strong></summary>
<p>Standard listings stay active for 30 days. Featured and premium listings stay active based on your membership plan. You can renew or manage all your ads from <a href="/dashboard/listings">My Listings</a>.</p></details>

<details><summary><strong>How do I make my ad more visible?</strong></summary>
<p>You can feature your listing or upgrade to a premium membership for higher visibility. Featured listings appear at the top of search results and on the homepage.</p></details>

<h2 id="account">👤 Account & Settings</h2>

<details><summary><strong>How do I create an account?</strong></summary>
<p>Visit the <a href="/register">registration page</a> and choose either "Store / Business" or "Personal / Classified" account type. You can also sign up with Google or Facebook for faster access.</p></details>

<details><summary><strong>I forgot my password. How do I reset it?</strong></summary>
<p>Click "Forgot Password" on the <a href="/login">login page</a>, enter your email, and follow the reset link sent to your inbox.</p></details>

<details><summary><strong>Can I change my account type later?</strong></summary>
<p>Yes — contact our support team via the <a href="/contact-us">Contact Us</a> page and we'll help you upgrade or change your account type.</p></details>

<h2 id="safety">🛡 Safety</h2>

<details><summary><strong>What should I do if I suspect a scam?</strong></summary>
<p>Use the "Report Ad" button on the listing, avoid sending any payment before seeing the item, and review our <a href="/safety-tips">Safety Tips</a> for detailed guidance.</p></details>

<details><summary><strong>Is my personal information safe?</strong></summary>
<p>Yes. We never share your personal details with other users. Only the contact information you choose to display on your listings or store is visible. Read our <a href="/privacy-policy">Privacy Policy</a> for details.</p></details>

<details><summary><strong>How do I report a suspicious user?</strong></summary>
<p>Use the Report button on any listing or store page, or contact us directly via the <a href="/contact-us">Contact Us</a> page with details.</p></details>

</div>

<div style="margin-top:32px;padding:24px;background:var(--k-bg-secondary,#f0fdf4);border-radius:16px;text-align:center">
    <h3 style="margin:0 0 8px">Still have questions?</h3>
    <p style="color:var(--k-text-secondary);margin:0 0 16px">Our team is happy to help directly.</p>
    <a href="/contact-us" class="k-btn k-btn-primary">Contact Us</a>
</div>
</div>
@endsection
