@extends('layouts.app')
@section('title','Cookie Policy · Kegalle Marketplace')
@section('meta_description','Learn how Kegalle Marketplace uses cookies and how you can manage your cookie preferences.')
@section('content')

<div class="k-help-hero">
    <div class="k-help-hero-inner">
        <div class="k-help-hero-badge">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
            Cookies
        </div>
        <h1>Cookie Policy</h1>
        <p>How we use cookies and similar technologies.</p>
    </div>
</div>

<div class="k-guide-wrap">
    <div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><span class="current">Cookie Policy</span></div>
    <div class="k-policy-body">
        <p class="k-policy-last-updated">Last updated: {{ now()->format('F Y') }}</p>
        <p>Kegalle Marketplace uses cookies and similar technologies to make the platform work, remember your preferences, and understand how visitors use our site.</p>

        <h2>What Are Cookies?</h2>
        <p>Cookies are small text files stored on your device when you visit a website. They help the site remember information about your visit, such as your login status and items you have saved.</p>

        <h2>Cookies We Use</h2>

        <h3>Essential Cookies</h3>
        <p>These cookies are required for the platform to work. They cannot be turned off.</p>
        <ul>
            <li><strong>Session cookie</strong> — keeps you logged in as you move between pages</li>
            <li><strong>CSRF token</strong> — protects your account from cross-site request forgery attacks</li>
        </ul>

        <h3>Functional Cookies</h3>
        <p>These cookies remember your choices and improve your experience.</p>
        <ul>
            <li><strong>Recently viewed</strong> — stores listings you have viewed (saved in your browser, not our servers)</li>
            <li><strong>Saved listings</strong> — remembers which listings you saved when not logged in</li>
            <li><strong>Search filters</strong> — remembers your last filter preferences</li>
        </ul>

        <h3>Analytics Cookies</h3>
        <p>We use Google Analytics to understand how people use Kegalle Marketplace. This helps us improve the platform. No personally identifiable information is collected.</p>

        <h3>Marketing Cookies</h3>
        <p>If you have interacted with our Facebook page or advertisements, Meta may set cookies to measure ad effectiveness. These cookies are controlled by Meta under their own privacy policy.</p>

        <h2>Managing Cookies</h2>
        <p>You can control cookies through your browser settings. Most browsers allow you to:</p>
        <ul>
            <li>View and delete existing cookies</li>
            <li>Block cookies from specific sites</li>
            <li>Block all third-party cookies</li>
            <li>Clear all cookies when you close your browser</li>
        </ul>
        <p>Note that disabling essential cookies will prevent you from logging in or using the platform fully.</p>

        <h2>Changes to This Policy</h2>
        <p>We may update this Cookie Policy from time to time. Any changes will be posted on this page with an updated date.</p>

        <p>Questions about cookies? <a href="/contact-us">Contact us</a> any time.</p>
    </div>
</div>
@endsection
