<!doctype html>
<html>
<head><meta charset="utf-8"><title>Listing Expiring Soon</title></head>
<body style="margin:0;background:#f5f7fb;font-family:Arial,sans-serif;color:#0f172a;">
<div style="max-width:620px;margin:40px auto;background:#fff;border-radius:16px;padding:34px;">
    <p style="margin:0 0 20px;font-size:13px;color:#667085;">Kegalle Marketplace</p>
    <h1 style="margin:0 0 12px;font-size:22px;">Your listing expires in 7 days</h1>
    <p>Hello {{ optional($listing->user)->name ?? 'Seller' }},</p>
    <p>Your listing <strong>"{{ $listing->title }}"</strong> is set to expire on <strong>{{ $listing->expires_at?->format('d M Y') }}</strong>.</p>
    <p>Renew it now to stay visible to buyers in Kegalle.</p>
    <p style="margin:24px 0">
        <a href="{{ url('/listings/'.$listing->slug) }}"
           style="display:inline-block;background:#00A76F;color:#fff;padding:14px 22px;border-radius:10px;text-decoration:none;font-weight:bold;">
            View My Listing →
        </a>
        &nbsp;
        <a href="{{ url('/dashboard/listings') }}"
           style="display:inline-block;background:#0f172a;color:#fff;padding:14px 22px;border-radius:10px;text-decoration:none;font-weight:bold;margin-top:8px">
            Manage All Listings
        </a>
    </p>
    <p style="color:#667085;font-size:12px;margin-top:24px;">You're receiving this because you have an active listing on Kegalle Marketplace.</p>
</div>
</body>
</html>
