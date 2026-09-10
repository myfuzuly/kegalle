@extends('layouts.app')
@section('title', 'Session Expired · kegalle')
@section('content')
<section style="min-height:55vh;display:flex;align-items:center;justify-content:center;padding:60px 20px">
    <div style="text-align:center;max-width:480px">
        <div style="font-size:88px;font-weight:800;color:var(--k-primary,#1b5e20);line-height:1">⏱</div>
        <h1 style="font-size:24px;margin:14px 0 8px">Session Expired</h1>
        <p style="color:#667085;font-size:15px;margin-bottom:26px">Your session has timed out or the page was open too long. Please go back and try again — it only takes a second.</p>
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
            <a href="javascript:history.back()" class="k-btn k-btn-primary" style="padding:12px 26px">← Go Back & Retry</a>
            <a href="/" class="k-btn k-btn-outline" style="padding:12px 26px">Back to Home</a>
        </div>
    </div>
</section>
@endsection
