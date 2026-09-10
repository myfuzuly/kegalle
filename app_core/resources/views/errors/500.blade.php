@extends('layouts.app')
@section('title', 'Something Went Wrong · Kegalle Marketplace')
@section('content')
<section style="min-height:55vh;display:flex;align-items:center;justify-content:center;padding:60px 20px">
    <div style="text-align:center;max-width:480px">
        <div style="font-size:88px;font-weight:800;color:var(--k-primary,#1b5e20);line-height:1">500</div>
        <h1 style="font-size:24px;margin:14px 0 8px">Something went wrong</h1>
        <p style="color:#667085;font-size:15px;margin-bottom:26px">We're sorry — something on our end went wrong. Our team has been notified. Please try again in a moment, or head back home.</p>
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
            <a href="/" class="k-btn k-btn-primary" style="padding:12px 26px">← Back to Home</a>
            <a href="javascript:location.reload()" class="k-btn k-btn-outline" style="padding:12px 26px">Try Again</a>
        </div>
    </div>
</section>
@endsection
