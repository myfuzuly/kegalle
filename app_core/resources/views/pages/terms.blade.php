@extends('layouts.app')
@section('title','Terms & Conditions · Kegalle Marketplace')
@section('meta_description','Read the Terms & Conditions for using Kegalle Marketplace.')
@section('content')

<div class="k-help-hero">
    <div class="k-help-hero-inner">
        <div class="k-help-hero-badge">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Legal
        </div>
        <h1>Terms &amp; Conditions</h1>
        <p>Please read these terms carefully before using Kegalle Marketplace.</p>
    </div>
</div>

<div class="k-guide-wrap">
    <div class="k-breadcrumb">
        <a href="/">Home</a><span class="sep">›</span><span class="current">Terms &amp; Conditions</span>
    </div>

    <div class="k-terms-layout">
        {{-- Sidebar TOC --}}
        <aside class="k-terms-toc">
            <div class="k-terms-toc-title">On this page</div>
            <ul class="k-terms-toc-list">
                <li><a href="#using-the-platform">1. Using the Platform</a></li>
                <li><a href="#account-responsibilities">2. Account Responsibilities</a></li>
                <li><a href="#listings-content">3. Listings &amp; Content</a></li>
                <li><a href="#fees-memberships">4. Fees &amp; Memberships</a></li>
                <li><a href="#limitation-of-liability">5. Limitation of Liability</a></li>
                <li><a href="#changes">6. Changes to These Terms</a></li>
            </ul>
        </aside>

        {{-- Policy body --}}
        <div class="k-policy-body">
            <p class="k-policy-last-updated">Last updated: {{ now()->format('F Y') }}</p>
            <p>By accessing or using Kegalle Marketplace ("the Platform"), you agree to be bound by these Terms &amp; Conditions. If you do not agree, please do not use the Platform.</p>

            <h2 id="using-the-platform">1. Using the Platform</h2>
            <p>Kegalle Marketplace is a venue connecting buyers and sellers. We do not own, sell, resell, furnish, provide, deliver, or supply any listings posted by users. Any contract for sale is solely between the buyer and the seller.</p>

            <h2 id="account-responsibilities">2. Account Responsibilities</h2>
            <ul>
                <li>You must provide accurate information when registering and posting listings</li>
                <li>You are responsible for all activity that occurs under your account</li>
                <li>You must not post fraudulent, illegal, or misleading content</li>
            </ul>

            <h2 id="listings-content">3. Listings &amp; Content</h2>
            <p>We reserve the right to remove, edit, or reject any listing that violates these terms, local law, or our content guidelines, without prior notice.</p>

            <h2 id="fees-memberships">4. Fees &amp; Memberships</h2>
            <p>Basic classified ads are free to post. Featured placements, store accounts, and other premium features may require payment as described at the time of purchase.</p>

            <h2 id="limitation-of-liability">5. Limitation of Liability</h2>
            <p>Kegalle Marketplace is provided "as is". We are not liable for disputes, losses, or damages arising from transactions between users. Always follow our <a href="/safety-tips">Safety Tips</a> when meeting buyers or sellers.</p>

            <h2 id="changes">6. Changes to These Terms</h2>
            <p>We may update these Terms &amp; Conditions from time to time. Continued use of the Platform after changes constitutes acceptance of the revised terms.</p>

            <p>Questions about these terms? <a href="/contact-us">Contact us</a> any time.</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const links = document.querySelectorAll('.k-terms-toc-list a');
    const sections = Array.from(links).map(a => document.querySelector(a.getAttribute('href')));
    function update() {
        let active = sections[0];
        sections.forEach(s => { if (s && s.getBoundingClientRect().top < 100) active = s; });
        links.forEach(a => a.classList.toggle('active', a.getAttribute('href') === '#' + (active?.id||'')));
    }
    window.addEventListener('scroll', update, {passive:true});
    update();
})();
</script>
@endpush

@endsection
