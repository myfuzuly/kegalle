@extends('layouts.app')
@section('title','About Us · Kegalle Marketplace')
@section('meta_description','Learn about Kegalle Marketplace — the local online marketplace connecting buyers, sellers and businesses across the Kegalle district of Sri Lanka.')
@section('content')

{{-- Hero --}}
<div class="k-about-hero">
    <div class="k-about-hero-inner">
        <div class="k-about-hero-badge">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Proudly Local · Kegalle, Sri Lanka
        </div>
        <h1>Built for <em>Kegalle</em>,<br>by Kegalle</h1>
        <p>The district's own marketplace — connecting buyers, sellers and businesses without the noise of nationwide platforms.</p>
        <div class="k-about-hero-ctas">
            <a href="/listings" class="k-btn k-btn-white">Browse Listings</a>
            <a href="/register" class="k-btn k-btn-outline-white">Join Free</a>
        </div>
    </div>
</div>

{{-- Stats Bar --}}
<div class="k-about-stats">
    <div class="k-about-stat">
        <strong>2024</strong>
        <span>Year founded</span>
    </div>
    <div class="k-about-stat-div"></div>
    <div class="k-about-stat">
        <strong>12+</strong>
        <span>Towns covered</span>
    </div>
    <div class="k-about-stat-div"></div>
    <div class="k-about-stat">
        <strong>Free</strong>
        <span>To post & browse</span>
    </div>
    <div class="k-about-stat-div"></div>
    <div class="k-about-stat">
        <strong>100%</strong>
        <span>Local listings</span>
    </div>
</div>

{{-- Main content --}}
<div class="k-about-wrap">

    {{-- Story --}}
    <div class="k-about-section">
        <div class="k-about-section-label">Our Story</div>
        <h2 class="k-about-section-h2">Why we built this</h2>
        <p class="k-about-section-p">Finding a trusted local buyer or seller online was harder than it should be. National classified sites are crowded with listings from across the country, making it difficult to find a genuine deal nearby. We built Kegalle Marketplace focused entirely on the Kegalle district — Kegalle Town, Mawanella, Ruwanwella, Aranayake, Warakapola and beyond — so every listing you see is realistically within reach.</p>
    </div>

    {{-- What We Offer --}}
    <div class="k-about-section">
        <div class="k-about-section-label">What We Offer</div>
        <h2 class="k-about-section-h2">Everything you need, locally</h2>
        <div class="k-about-features">
            <div class="k-about-feat">
                <div class="k-about-feat-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                </div>
                <div class="k-about-feat-body">
                    <strong>Classified Ads</strong>
                    <span>Post personal items, quick deals and second-hand goods for free — no subscription needed.</span>
                </div>
            </div>
            <div class="k-about-feat">
                <div class="k-about-feat-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <div class="k-about-feat-body">
                    <strong>Verified Storefronts</strong>
                    <span>Local shops and service providers get a full business profile with products, deals and reviews.</span>
                </div>
            </div>
            <div class="k-about-feat">
                <div class="k-about-feat-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
                <div class="k-about-feat-body">
                    <strong>Category & Location Browse</strong>
                    <span>Filter by category, town and price — find exactly what you need fast without scrolling through noise.</span>
                </div>
            </div>
            <div class="k-about-feat">
                <div class="k-about-feat-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                </div>
                <div class="k-about-feat-body">
                    <strong>Direct Contact</strong>
                    <span>Message sellers, chat on WhatsApp or call — no middlemen, no hidden platform fees.</span>
                </div>
            </div>
            <div class="k-about-feat">
                <div class="k-about-feat-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 10c-.83 0-1.5-.67-1.5-1.5v-5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5z"/><path d="M20.5 10H19V8.5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/><path d="M9.5 14c.83 0 1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5S8 21.33 8 20.5v-5c0-.83.67-1.5 1.5-1.5z"/><path d="M3.5 14H5v1.5c0 .83-.67 1.5-1.5 1.5S2 16.33 2 15.5 2.67 14 3.5 14z"/><path d="M14 14.5c0-.83.67-1.5 1.5-1.5h5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5h-5c-.83 0-1.5-.67-1.5-1.5z"/><path d="M15.5 19H14v1.5c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z"/><path d="M10 9.5C10 8.67 9.33 8 8.5 8h-5C2.67 8 2 8.67 2 9.5S2.67 11 3.5 11h5c.83 0 1.5-.67 1.5-1.5z"/><path d="M8.5 5H10V3.5C10 2.67 9.33 2 8.5 2S7 2.67 7 3.5 7.67 5 8.5 5z"/></svg>
                </div>
                <div class="k-about-feat-body">
                    <strong>Services Directory</strong>
                    <span>Find cleaning, repairs, IT, education and more from trusted local service providers.</span>
                </div>
            </div>
            <div class="k-about-feat">
                <div class="k-about-feat-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                </div>
                <div class="k-about-feat-body">
                    <strong>Deals & Events</strong>
                    <span>Discounted offers from local stores and community events happening around you.</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Commitment --}}
    <div class="k-about-commit">
        <div class="k-about-commit-inner">
            <div class="k-about-commit-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <h3>Our Commitment</h3>
            <p>We're committed to keeping Kegalle Marketplace safe, simple and genuinely local. We moderate listings, support verified stores and provide safety guidance for every transaction made through the platform. Our local team responds within 24 hours.</p>
        </div>
    </div>

    {{-- Contact CTA --}}
    <div class="k-about-cta-row">
        <div class="k-about-cta-card">
            <div class="k-about-cta-icon k-icon-green">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </div>
            <div class="k-about-cta-body">
                <strong>WhatsApp</strong>
                <span class="k-about-cta-value"><a href="https://wa.me/94712930930?text={{ urlencode('Hi, I need help with Kegalle Marketplace.') }}" target="_blank" rel="noopener">+94 712 930 930</a></span>
                <span class="k-about-cta-sub">Typically replies instantly</span>
            </div>
            <div class="k-about-cta-footer">
                <a href="https://wa.me/94712930930?text={{ urlencode('Hi, I need help with Kegalle Marketplace.') }}" target="_blank" rel="noopener" class="k-btn k-btn-primary k-btn-sm">Chat Now</a>
            </div>
        </div>
        <div class="k-about-cta-card">
            <div class="k-about-cta-icon k-icon-blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <div class="k-about-cta-body">
                <strong>Email</strong>
                <span class="k-about-cta-value"><a href="mailto:support@kegalle.com">support@kegalle.com</a></span>
                <span class="k-about-cta-sub">We reply within 24 hours</span>
            </div>
            <div class="k-about-cta-footer">
                <a href="mailto:support@kegalle.com" class="k-btn k-btn-outline k-btn-sm">Send Email</a>
            </div>
        </div>
        <div class="k-about-cta-card">
            <div class="k-about-cta-icon k-icon-amber">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 19.79 19.79 0 01.014 1.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/></svg>
            </div>
            <div class="k-about-cta-body">
                <strong>Phone</strong>
                <span class="k-about-cta-value"><a href="tel:+94712930930">+94 712 930 930</a></span>
                <span class="k-about-cta-sub">Mon–Sat, 9 AM–6 PM</span>
            </div>
            <div class="k-about-cta-footer">
                <a href="tel:+94712930930" class="k-btn k-btn-outline k-btn-sm">Call Now</a>
            </div>
        </div>
    </div>

    <p class="k-about-footer-note">Kegalle Marketplace — established 2024. Proudly local.</p>

</div>
@endsection
