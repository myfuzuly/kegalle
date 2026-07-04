<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Reset Password</title>
</head>
<body style="margin:0;background:#f5f7fb;font-family:Arial,sans-serif;color:#0f172a;">
<div style="max-width:620px;margin:40px auto;background:#fff;border-radius:20px;padding:34px;">
    <h1 style="margin-top:0;">Reset your password</h1>
    <p>You requested a password reset for your Kegalle Marketplace account.</p>
    <p>Click the button below to set a new password. This link expires in 60 minutes.</p>
    <p>
        <a href="{{ $resetUrl }}"
           style="display:inline-block;background:#00A76F;color:#fff;padding:14px 22px;border-radius:12px;text-decoration:none;font-weight:bold;">
            Reset Password
        </a>
    </p>
    <p style="color:#667085;font-size:13px;">If you did not request a password reset, ignore this email — your password will remain unchanged.</p>
</div>
</body>
</html>
