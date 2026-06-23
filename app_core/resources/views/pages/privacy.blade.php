@extends('layouts.app')
@section('title','Privacy Policy · Kegalle Marketplace')
@section('meta_description','Read the Kegalle Marketplace Privacy Policy to understand how we collect, use, and protect your information.')
@section('content')
<section class="cats-hero"><div class="cats-hero-inner"><h1>Privacy Policy</h1><p>How we collect, use, and protect your information.</p></div></section>
<div class="container" style="padding-top:32px;padding-bottom:48px">
<div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><span class="current">Privacy Policy</span></div>
<div class="blog-content">
<p>Last updated: {{ now()->format('F Y') }}</p>
<p>This Privacy Policy explains what information Kegalle Marketplace collects, how we use it, and the choices you have.</p>
<h2>Information We Collect</h2>
<ul>
<li>Account information you provide: name, email, phone number, and location</li>
<li>Listing content you post: titles, descriptions, prices, and images</li>
<li>Usage data: pages visited, searches made, and device/browser information</li>
</ul>
<h2>How We Use Your Information</h2>
<p>We use your information to operate the marketplace, connect buyers with sellers, send important account notifications, and improve the platform. We do not sell your personal information to third parties.</p>
<h2>Sharing Your Information</h2>
<p>Information you include in a public listing (such as your store name or contact number) is visible to other users by design. We only share account-level information with third parties when required by law or to provide core services (such as payment processing).</p>
<h2>Your Choices</h2>
<p>You can update or delete your account information at any time from <a href="/dashboard/profile">Profile Settings</a>. You may also request account deletion by contacting us.</p>
<h2>Data Security</h2>
<p>We take reasonable measures to protect your information, including secure password storage. However, no online platform can guarantee absolute security.</p>
<p>Questions about this policy? <a href="/contact-us">Contact us</a> any time.</p>
</div>
</div>
@endsection
