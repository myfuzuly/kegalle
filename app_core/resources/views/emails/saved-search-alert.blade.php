<!doctype html>
<html>
<head><meta charset="utf-8"><title>New Listings Match Your Search</title></head>
<body style="margin:0;background:#f5f7fb;font-family:Arial,sans-serif;color:#0f172a;">
<div style="max-width:620px;margin:40px auto;background:#fff;border-radius:16px;padding:34px;">
    <p style="margin:0 0 20px;font-size:13px;color:#667085;">Kegalle Marketplace</p>
    <h1 style="margin:0 0 12px;font-size:22px;">{{ $listings->count() }} new {{ Str::plural('listing', $listings->count()) }} match your saved search</h1>
    <p>Here are the latest ads that match your saved search:</p>

    @foreach($listings->take(5) as $listing)
    <div style="border:1px solid #e5e7eb;border-radius:10px;padding:14px 16px;margin:14px 0;display:flex;gap:14px;align-items:flex-start">
        <div style="flex:1">
            <div style="font-weight:700;font-size:14px;margin-bottom:4px">{{ $listing->title }}</div>
            <div style="font-size:12px;color:#667085">{{ optional($listing->category)->name ?? 'General' }} · {{ optional($listing->locationModel)->name ?? $listing->location ?? 'Kegalle' }}</div>
            <div style="font-size:14px;font-weight:700;color:#00A76F;margin-top:6px">{{ ($listing->price ?? 0) > 0 ? 'LKR '.number_format($listing->price) : 'Contact Seller' }}</div>
        </div>
        <a href="{{ url('/listings/'.$listing->slug) }}" style="background:#00A76F;color:#fff;padding:8px 14px;border-radius:8px;text-decoration:none;font-size:12px;font-weight:700;white-space:nowrap;flex-shrink:0">View Ad</a>
    </div>
    @endforeach

    @php
        $qs = http_build_query($searchParams);
    @endphp
    <p style="margin-top:20px">
        <a href="{{ url('/listings?'.$qs) }}" style="display:inline-block;background:#0f172a;color:#fff;padding:12px 20px;border-radius:10px;text-decoration:none;font-weight:bold;">
            View All Results →
        </a>
    </p>
    <p style="color:#667085;font-size:12px;margin-top:24px;">You saved this search on Kegalle Marketplace. <a href="{{ url('/dashboard') }}" style="color:#00A76F">Manage saved searches</a>.</p>
</div>
</body>
</html>
