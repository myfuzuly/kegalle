<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Verify Account</title>
</head>
<body style="margin:0;background:#f5f7fb;font-family:Arial,sans-serif;color:#0f172a;">
<div style="max-width:620px;margin:40px auto;background:#fff;border-radius:20px;padding:34px;">
    <h1 style="margin-top:0;">Verify your Kegalle account</h1>
    <p>Hello {{ $user->name }},</p>
    <p>Please verify your email address to activate your Kegalle marketplace account.</p>
    <p>
        <a href="{{ url('/email/verify/'.$user->verification_token) }}"
           style="display:inline-block;background:#00A76F;color:#fff;padding:14px 22px;border-radius:12px;text-decoration:none;font-weight:bold;">
            Verify Email
        </a>
    </p>
    <p style="color:#667085;font-size:13px;">If you did not create this account, ignore this email.</p>
</div>
</body>
</html>
