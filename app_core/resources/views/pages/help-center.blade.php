@extends('layouts.app')
@section('title','Help Center · Kegalle Marketplace')
@section('content')
<section class="cats-hero"><div class="cats-hero-inner"><h1>Help Center</h1><p>Guides and answers to help you get the most out of Kegalle Marketplace.</p></div></section>
<div class="container" style="padding-top:32px;padding-bottom:48px">
<div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><span class="current">Help Center</span></div>
<div class="k-grid-4" style="gap:18px">
    <a href="/how-to-buy" class="k-info-card" style="text-decoration:none;color:inherit;display:block"><h3>🛍️ How to Buy</h3><p style="font-size:13px;color:var(--k-text-secondary)">Learn how to search, filter, and safely contact sellers.</p></a>
    <a href="/how-to-sell" class="k-info-card" style="text-decoration:none;color:inherit;display:block"><h3>📦 How to Sell</h3><p style="font-size:13px;color:var(--k-text-secondary)">Post your first ad and grow your sales on the platform.</p></a>
    <a href="/safety-tips" class="k-info-card" style="text-decoration:none;color:inherit;display:block"><h3>🛡️ Safety Tips</h3><p style="font-size:13px;color:var(--k-text-secondary)">Stay safe when meeting buyers and sellers in person.</p></a>
    <a href="/faq" class="k-info-card" style="text-decoration:none;color:inherit;display:block"><h3>❓ FAQ</h3><p style="font-size:13px;color:var(--k-text-secondary)">Quick answers to the most common questions.</p></a>
</div>
<div class="blog-content" style="margin-top:32px;max-width:none;padding:0">
<h2>Still need help?</h2>
<p>If you can't find what you're looking for, our team is happy to help directly. Reach out via the <a href="/contact-us">Contact Us</a> page or chat with us on WhatsApp.</p>
</div>
</div>
@endsection
