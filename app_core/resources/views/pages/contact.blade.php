@extends('layouts.app')
@section('title','Contact Us · Kegalle Marketplace')
@section('meta_description','Get in touch with the Kegalle Marketplace team — send us a message, call, or chat with us on WhatsApp.')
@section('content')
<section class="cats-hero"><div class="cats-hero-inner"><h1>Contact Us</h1><p>Questions, feedback, or need help? We're here for you.</p></div></section>
<div class="container" style="padding-top:32px;padding-bottom:48px">
<div class="k-breadcrumb"><a href="/">Home</a><span class="sep">›</span><span class="current">Contact Us</span></div>

<div class="k-layout-sidebar-right" style="gap:32px;max-width:980px;margin:0 auto">
    <div class="k-card k-card-body" style="border-radius:var(--k-radius-lg)">
        <h3 style="font-family:var(--font-display);font-size:18px;font-weight:800;margin-bottom:18px">Send Us a Message</h3>
        @if(session('success'))
            <div style="background:#E8F5E9;color:#2E7D32;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-weight:600;font-size:13.5px">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div style="background:#FFEBEE;color:#C62828;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13.5px">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif
        <form method="POST" action="/contact-us" style="display:grid;gap:14px">
            @csrf
            <div class="k-form-group">
                <label class="k-form-label" for="contact-name">Your Name</label>
                <input id="contact-name" type="text" name="name" value="{{ old('name') }}" class="k-form-control" required>
            </div>
            <div class="k-form-group">
                <label class="k-form-label" for="contact-email">Email Address</label>
                <input id="contact-email" type="email" name="email" value="{{ old('email') }}" class="k-form-control" required>
            </div>
            <div class="k-form-group">
                <label class="k-form-label" for="contact-message">Message</label>
                <textarea id="contact-message" name="message" required class="k-form-control" style="min-height:140px">{{ old('message') }}</textarea>
            </div>
            <button type="submit" class="k-btn k-btn-primary k-btn-lg" style="justify-content:center;border:none;cursor:pointer">Send Message</button>
        </form>
    </div>

    <div>
        <div class="k-info-card mb-16">
            <h3>Get in Touch</h3>
            <div class="k-info-row"><div class="k-info-icon">📞</div><div class="k-info-val"><a href="tel:+94766930930">+94 76 693 0930</a><small style="display:block;font-size:11px;color:#98a2b3">Hotline</small></div></div>
            <div class="k-info-row"><div class="k-info-icon">📱</div><div class="k-info-val"><a href="tel:+94706930930">+94 70 693 0930</a><small style="display:block;font-size:11px;color:#98a2b3">Mobile</small></div></div>
            <div class="k-info-row"><div class="k-info-icon">☎️</div><div class="k-info-val"><a href="tel:+94352234433">+94 35 223 4433</a><small style="display:block;font-size:11px;color:#98a2b3">Telephone</small></div></div>
            <div class="k-info-row"><div class="k-info-icon">✉</div><div class="k-info-val"><a href="mailto:support@kurulla.com">support@kurulla.com</a></div></div>
            <div class="k-info-row"><div class="k-info-icon">📍</div><div class="k-info-val">Kegalle, Sri Lanka</div></div>
            <div class="k-info-row"><div class="k-info-icon">💬</div><div class="k-info-val"><a href="https://wa.me/94706930930" target="_blank">Chat on WhatsApp</a></div></div>
        </div>
        <div class="k-info-card">
            <h3>Office Hours</h3>
            <p style="font-size:13px;color:var(--k-text-secondary);line-height:1.7">Monday – Saturday<br>9:00 AM – 6:00 PM</p>
        </div>
    </div>
</div>
</div>
@endsection
