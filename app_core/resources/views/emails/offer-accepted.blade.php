<!doctype html>
<html>
<head><meta charset="utf-8"><title>Your offer was accepted — Kegalle Marketplace</title></head>
<body style="margin:0;background:#f5f7fb;font-family:Arial,sans-serif;color:#0f172a;">
<div style="max-width:620px;margin:40px auto;background:#fff;border-radius:16px;padding:34px;">
    <p style="margin:0 0 20px;font-size:13px;color:#667085;">Kegalle Marketplace</p>

    <div style="background:#dcfce7;border-radius:12px;padding:18px 20px;margin-bottom:24px;display:flex;align-items:center;gap:12px;">
        <span style="font-size:28px;">🎉</span>
        <div>
            <div style="font-weight:700;font-size:16px;color:#15803d;">Your offer was accepted!</div>
            <div style="font-size:13px;color:#166534;margin-top:2px;">The seller is ready to proceed with your deal.</div>
        </div>
    </div>

    <p style="margin:0 0 6px;">Hello <strong>{{ optional($offer->buyer)->name ?? 'there' }}</strong>,</p>
    <p style="margin:0 0 20px;color:#374151;">The seller has accepted your offer on <strong>"{{ optional($offer->listing)->title ?? 'this listing' }}"</strong>.</p>

    <table style="width:100%;border:1px solid #e5e7eb;border-radius:10px;border-collapse:collapse;margin-bottom:24px;overflow:hidden;">
        <tr style="background:#f9fafb;">
            <td style="padding:11px 14px;font-size:13px;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Listing</td>
            <td style="padding:11px 14px;font-size:13px;color:#374151;border-bottom:1px solid #e5e7eb;">{{ optional($offer->listing)->title ?? '—' }}</td>
        </tr>
        <tr>
            <td style="padding:11px 14px;font-size:13px;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Your offer</td>
            <td style="padding:11px 14px;font-size:13px;color:#374151;border-bottom:1px solid #e5e7eb;font-weight:700;">LKR {{ number_format($offer->offered_price) }}</td>
        </tr>
        <tr style="background:#f9fafb;">
            <td style="padding:11px 14px;font-size:13px;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Payment method</td>
            <td style="padding:11px 14px;font-size:13px;color:#374151;border-bottom:1px solid #e5e7eb;">{{ $offer->payment_icon ?? '' }} {{ $offer->payment_label ?? ucfirst($offer->payment_method) }}</td>
        </tr>
        @if($offer->seller_note)
        <tr>
            <td style="padding:11px 14px;font-size:13px;font-weight:600;color:#374151;">Seller's note</td>
            <td style="padding:11px 14px;font-size:13px;color:#374151;">{{ $offer->seller_note }}</td>
        </tr>
        @endif
    </table>

    <div style="background:#eff6ff;border-radius:12px;padding:18px 20px;margin-bottom:24px;">
        <div style="font-weight:700;font-size:14px;margin-bottom:10px;color:#1e40af;">📋 What to do next</div>
        <ol style="margin:0;padding-left:18px;color:#374151;font-size:13px;line-height:1.9;">
            <li>Open the chat with the seller to confirm the meeting details.</li>
            <li>Agree on a safe public meeting place (bank, police station, or busy area).</li>
            <li>Inspect the item carefully before handing over any payment.</li>
            <li>Pay via <strong>{{ $offer->payment_label ?? ucfirst($offer->payment_method) }}</strong> as agreed.</li>
            <li>Leave a review after the deal is complete — it helps other buyers!</li>
        </ol>
    </div>

    <a href="{{ url('/dashboard/offers') }}"
       style="display:inline-block;background:#2563eb;color:#fff;padding:14px 24px;border-radius:10px;text-decoration:none;font-weight:700;font-size:14px;margin-bottom:12px;">
        View Your Offer →
    </a>
    &nbsp;
    <a href="{{ url('/dashboard/chat') }}"
       style="display:inline-block;background:#f1f5f9;color:#0f172a;padding:14px 24px;border-radius:10px;text-decoration:none;font-weight:700;font-size:14px;margin-bottom:12px;">
        💬 Open Chat
    </a>

    <div style="background:#fef9c3;border-radius:10px;padding:14px 16px;margin-top:20px;">
        <div style="font-weight:700;font-size:13px;color:#854d0e;margin-bottom:6px;">🛡 Safety reminder</div>
        <ul style="margin:0;padding-left:16px;color:#713f12;font-size:12px;line-height:1.8;">
            <li>Never pay in advance without seeing the item</li>
            <li>Meet in a public place</li>
            <li>Don't share OTPs or banking passwords</li>
        </ul>
    </div>

    <p style="color:#9ca3af;font-size:12px;margin-top:24px;">Questions? Contact us at <a href="mailto:info@kegalle.com" style="color:#2563eb;">info@kegalle.com</a></p>
</div>
</body>
</html>
