@extends('layouts.app')
@section('title','Terms & Conditions · Kegalle Marketplace')
@section('content')
<section class="cats-hero"><div class="cats-hero-inner"><h1>Terms & Conditions</h1><p>Please read these terms carefully before using Kegalle Marketplace.</p></div></section>
<div class="container" style="padding-top:32px;padding-bottom:48px">
<div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><span class="current">Terms & Conditions</span></div>
<div class="blog-content">
<p>Last updated: {{ now()->format('F Y') }}</p>
<p>By accessing or using Kegalle Marketplace ("the Platform"), you agree to be bound by these Terms & Conditions. If you do not agree, please do not use the Platform.</p>
<h2>1. Using the Platform</h2>
<p>Kegalle Marketplace is a venue connecting buyers and sellers. We do not own, sell, resell, furnish, provide, deliver, or supply any listings posted by users. Any contract for sale is solely between the buyer and the seller.</p>
<h2>2. Account Responsibilities</h2>
<ul>
<li>You must provide accurate information when registering and posting listings</li>
<li>You are responsible for all activity that occurs under your account</li>
<li>You must not post fraudulent, illegal, or misleading content</li>
</ul>
<h2>3. Listings & Content</h2>
<p>We reserve the right to remove, edit, or reject any listing that violates these terms, local law, or our content guidelines, without prior notice.</p>
<h2>4. Fees & Memberships</h2>
<p>Basic classified ads are free to post. Featured placements, store accounts, and other premium features may require payment as described at the time of purchase.</p>
<h2>5. Limitation of Liability</h2>
<p>Kegalle Marketplace is provided "as is". We are not liable for disputes, losses, or damages arising from transactions between users. Always follow our <a href="/safety-tips">Safety Tips</a> when meeting buyers or sellers.</p>
<h2>6. Changes to These Terms</h2>
<p>We may update these Terms & Conditions from time to time. Continued use of the Platform after changes constitutes acceptance of the revised terms.</p>
<p>Questions about these terms? <a href="/contact-us">Contact us</a> any time.</p>
</div>
</div>
@endsection
