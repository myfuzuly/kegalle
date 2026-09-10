<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Listing {{ $statusLabel }} — Kegalle Marketplace</title>
</head>
<body style="margin:0;background:#f5f7fb;font-family:Arial,sans-serif;color:#0f172a;">
<div style="max-width:620px;margin:40px auto;background:#fff;border-radius:16px;padding:34px;">
    <p style="margin:0 0 20px;font-size:13px;color:#667085;">Kegalle Marketplace</p>
    <h1 style="margin:0 0 12px;font-size:22px;">Your listing has been {{ $statusLabel }}</h1>
    <p>Hello {{ optional($listing->user)->name ?? 'Seller' }},</p>
    @if($approved)
    <p>Great news! Your listing <strong>"{{ $listing->title }}"</strong> has been <strong style="color:#16a34a;">approved</strong> and is now live on Kegalle Marketplace.</p>
    <p>
        <a href="{{ url('/listings/'.$listing->slug) }}"
           style="display:inline-block;background:#00A76F;color:#fff;padding:14px 22px;border-radius:10px;text-decoration:none;font-weight:bold;">
            View Your Listing →
        </a>
    </p>
    @else
    <p>Unfortunately, your listing <strong>"{{ $listing->title }}"</strong> has been <strong style="color:#dc2626;">rejected</strong> by our moderation team.</p>
    <p>Common reasons include: prohibited items, unclear description, or missing images. Please review our <a href="{{ url('/safety-tips') }}" style="color:#00A76F;">guidelines</a> and repost with the required details.</p>
    <p>
        <a href="{{ url('/dashboard/listings/create') }}"
           style="display:inline-block;background:#0f172a;color:#fff;padding:14px 22px;border-radius:10px;text-decoration:none;font-weight:bold;">
            Post a New Listing
        </a>
    </p>
    @endif
    <p style="color:#667085;font-size:13px;margin-top:24px;">If you have questions, contact us at <a href="mailto:info@kegalle.com" style="color:#00A76F;">info@kegalle.com</a></p>
</div>
</body>
</html>
