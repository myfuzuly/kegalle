<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>New message from {{ $senderName }}</title>
</head>
<body style="margin:0;background:#f5f7fb;font-family:Arial,sans-serif;color:#0f172a;">
<div style="max-width:620px;margin:40px auto;background:#fff;border-radius:16px;padding:34px;">
    <p style="margin:0 0 20px;font-size:13px;color:#667085;">Kegalle Marketplace</p>
    <h1 style="margin:0 0 12px;font-size:22px;">New message from {{ $senderName }}</h1>
    <p>You have a new message regarding <strong>"{{ optional($thread->listing)->title ?? 'your listing' }}"</strong>.</p>
    <div style="background:#f8fafc;border-left:4px solid #00A76F;border-radius:4px;padding:14px 18px;margin:20px 0;font-size:14px;line-height:1.6;color:#334155;">
        {{ $chatMessage->message }}
    </div>
    <p>
        <a href="{{ url('/dashboard/chat/'.$thread->id) }}"
           style="display:inline-block;background:#00A76F;color:#fff;padding:14px 22px;border-radius:10px;text-decoration:none;font-weight:bold;">
            Reply to Message →
        </a>
    </p>
    <p style="color:#667085;font-size:12px;margin-top:24px;">You are receiving this email because you have an active conversation on Kegalle Marketplace.</p>
</div>
</body>
</html>
