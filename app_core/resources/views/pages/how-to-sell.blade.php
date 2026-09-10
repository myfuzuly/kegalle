@extends('layouts.app')
@section('title','How to Sell · Kegalle Marketplace')
@section('meta_description','Learn how to post your first ad and start selling to local buyers on Kegalle Marketplace.')
@section('content')

<div class="k-help-hero">
    <div class="k-help-hero-inner">
        <div class="k-help-hero-badge">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
            Seller's Guide
        </div>
        <h1>How to Sell</h1>
        <p>Post your first ad and start reaching local buyers in Kegalle today.</p>
    </div>
</div>

<div class="k-guide-wrap">
    <div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><a href="/help-center">Help Center</a><span class="sep">›</span><span class="current">How to Sell</span></div>

    <div class="k-guide-steps">
        <div class="k-guide-step">
            <div class="k-guide-step-num">1</div>
            <div class="k-guide-step-body">
                <h3>Create an Account</h3>
                <p><a href="/register">Sign up</a> as a Store / Business if you plan to sell regularly, or as Personal / Classified for one-off items. It's free to get started.</p>
            </div>
        </div>
        <div class="k-guide-step">
            <div class="k-guide-step-num">2</div>
            <div class="k-guide-step-body">
                <h3>Post Your Ad</h3>
                <p>From your <a href="/dashboard/listings/create">dashboard</a>, choose a category, add clear photos, write an honest description, and set a fair price.</p>
                <div class="k-guide-tip">
                    <div class="k-guide-tip-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                    <div class="k-guide-tip-body">
                        <h4>Photo Tip</h4>
                        <p>Listings with multiple clear photos and a detailed description get significantly more views and faster sales.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="k-guide-step">
            <div class="k-guide-step-num">3</div>
            <div class="k-guide-step-body">
                <h3>Respond Quickly</h3>
                <p>Buyers are more likely to follow through when sellers respond promptly. Check your <a href="/dashboard/chat">Messages</a> regularly.</p>
            </div>
        </div>
        <div class="k-guide-step">
            <div class="k-guide-step-num">4</div>
            <div class="k-guide-step-body">
                <h3>Get Featured</h3>
                <p>Want more visibility? Upgrade your <a href="/dashboard/membership">membership</a> to feature your ads at the top of search results and category pages.</p>
            </div>
        </div>
        <div class="k-guide-step">
            <div class="k-guide-step-num">5</div>
            <div class="k-guide-step-body">
                <h3>Build Trust</h3>
                <p>Accurate listings, fast responses, and positive reviews all help build a trustworthy seller profile over time.</p>
            </div>
        </div>
    </div>

    <div class="k-help-still">
        <div class="k-help-still-inner">
            <div class="k-help-still-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            </div>
            <h3>Ready to post?</h3>
            <p>Create your first listing now — it's free and takes less than 2 minutes.</p>
            <div class="k-help-still-btns">
                <a href="/dashboard/listings/create" class="k-btn k-btn-white">Post a Free Ad</a>
                <a href="/faq" class="k-btn k-btn-outline-white">Browse FAQ</a>
            </div>
        </div>
    </div>
</div>
@endsection
