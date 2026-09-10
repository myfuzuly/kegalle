@extends('layouts.app')
@section('title', 'How It Works — Kegalle Marketplace')
@section('meta_description', 'Learn how to buy and sell on Kegalle Marketplace. Simple steps to post your ad, find great deals, and connect with local buyers and sellers in Kegalle district.')
@section('canonical', url('/how-it-works'))
@section('content')

<div class="hiw-wrap">
    <div class="k-breadcrumb"><a href="/">Home</a><span>›</span><span class="current">How It Works</span></div>

    <div class="hiw-hero-inner">
        <h1>How Kegalle Marketplace Works</h1>
        <p>Buy and sell locally in minutes. No fees, no middlemen — just direct deals between real people in the Kegalle district.</p>
    </div>

    {{-- Tabs --}}
    <div class="hiw-tabs">
        <button class="hiw-tab active" data-hiw="buyers">For Buyers</button>
        <button class="hiw-tab" data-hiw="sellers">For Sellers</button>
    </div>

    {{-- Buyers section --}}
    <div class="hiw-section" id="hiw-buyers">
        <div class="hiw-cards">
            @foreach([
                ['step'=>'1','title'=>'Browse or Search','desc'=>'Use the search bar or filter by category, location, or price. Find exactly what you need from local sellers.','icon'=>'<path d="M21 21l-4.35-4.35"/><circle cx="11" cy="11" r="8"/>'],
                ['step'=>'2','title'=>'View the Listing','desc'=>'Check photos, specs, price, and seller info. Read reviews from other buyers before contacting.','icon'=>'<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>'],
                ['step'=>'3','title'=>'Make an Offer','desc'=>'Send an offer with your price and preferred payment method. Start a chat with the seller directly.','icon'=>'<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>'],
                ['step'=>'4','title'=>'Complete the Deal','desc'=>'Meet the seller safely, inspect the item, and pay. Rate the seller after your purchase.','icon'=>'<path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>'],
            ] as $s)
            <div class="hiw-card">
                <div class="hiw-card-step">Step {{ $s['step'] }}</div>
                <div class="hiw-card-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $s['icon'] !!}</svg>
                </div>
                <h3>{{ $s['title'] }}</h3>
                <p>{{ $s['desc'] }}</p>
            </div>
            @endforeach
        </div>
        <div class="hiw-tips hiw-tips-buyers">
            <h4>Safety Tips for Buyers</h4>
            <ul>
                @foreach(['Meet in a public place like a bank or police station','Inspect the item thoroughly before paying','Avoid paying full price before seeing the item','Check the seller\'s rating and reviews','Never share OTPs or banking passwords'] as $tip)
                <li><span class="hiw-tips-check">✓</span>{{ $tip }}</li>
                @endforeach
            </ul>
        </div>
        <div class="hiw-cta"><a href="/listings" class="k-btn k-btn-primary k-btn-lg">Browse All Listings</a></div>
    </div>

    {{-- Sellers section --}}
    <div class="hiw-section" id="hiw-sellers" hidden>
        <div class="hiw-cards">
            @foreach([
                ['step'=>'1','title'=>'Create an Account','desc'=>'Sign up free in under a minute. Choose Store / Business or Personal depending on how you sell.','icon'=>'<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>'],
                ['step'=>'2','title'=>'Post with AI Help','desc'=>'Use the AI Listing Assistant — just pick a category and our AI writes your title and description instantly.','icon'=>'<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>'],
                ['step'=>'3','title'=>'Get Approved','desc'=>'Our team reviews your listing within a few hours. You\'ll get notified when it goes live.','icon'=>'<path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>'],
                ['step'=>'4','title'=>'Receive Offers & Chat','desc'=>'Buyers send offers and messages directly. Accept or negotiate — you\'re in full control.','icon'=>'<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>'],
            ] as $s)
            <div class="hiw-card">
                <div class="hiw-card-step">Step {{ $s['step'] }}</div>
                <div class="hiw-card-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $s['icon'] !!}</svg>
                </div>
                <h3>{{ $s['title'] }}</h3>
                <p>{{ $s['desc'] }}</p>
            </div>
            @endforeach
        </div>
        <div class="hiw-tips hiw-tips-sellers">
            <h4>Tips for Selling Faster</h4>
            <ul>
                @foreach(['Upload clear photos from multiple angles in good lighting','Write a detailed, honest description (min 20 characters)','Set a fair price — check similar listings first','Add your WhatsApp number for quick buyer contact','Respond to offers and chats promptly'] as $tip)
                <li><span class="hiw-tips-arrow">→</span>{{ $tip }}</li>
                @endforeach
            </ul>
        </div>
        <div class="hiw-cta hiw-cta-row">
            <a href="/register?account_type=store" class="k-btn k-btn-primary k-btn-lg">Create Free Account</a>
            <a href="/dashboard/listings/create" class="k-btn k-btn-outline k-btn-lg">Post an Ad</a>
        </div>
    </div>

    {{-- FAQ --}}
    <div class="hiw-faq-wrap">
        <h2>Common Questions</h2>
        <div class="hiw-faq-list">
            @foreach([
                ['q'=>'Is it free to post a listing?','a'=>'Yes, posting basic listings is completely free. Featured and promoted listings may have a small fee.'],
                ['q'=>'How long does listing approval take?','a'=>'Usually within a few hours. Our team reviews all listings to keep the marketplace trustworthy.'],
                ['q'=>'How do I pay for items?','a'=>'Payment is arranged directly between buyer and seller — cash on pickup, bank transfer, or COD. We don\'t process payments ourselves.'],
                ['q'=>'Can I post classified ads?','a'=>'Yes! You can post classified ads (jobs, services, notices) as well as product listings.'],
                ['q'=>'Is Kegalle Marketplace only for Kegalle?','a'=>'We focus on the Kegalle district, but sellers and buyers from nearby towns are welcome too.'],
            ] as $faq)
            <details class="k-faq-item">
                <summary>{{ $faq['q'] }}</summary>
                <div class="k-faq-ans"><p>{{ $faq['a'] }}</p></div>
            </details>
            @endforeach
        </div>
    </div>
</div>

<script nonce="{{ $cspNonce ?? '' }}">
(function(){
    document.querySelectorAll('.hiw-tab').forEach(function(tab){
        tab.addEventListener('click', function(){
            document.querySelectorAll('.hiw-tab').forEach(function(t){ t.classList.remove('active'); });
            tab.classList.add('active');
            document.querySelectorAll('.hiw-section').forEach(function(s){ s.hidden = true; });
            var target = document.getElementById('hiw-' + tab.dataset.hiw);
            if(target) target.hidden = false;
        });
    });
})();
</script>
@endsection
