<?php
/**
 * One-time watermark logo setup.
 * Upload your logo → auto-converts to white-on-transparent PNG → saved as watermark.
 * Delete this file after use.
 */

$out   = __DIR__ . '/images/watermark.png';
$error = '';
$done  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['logo'])) {
    $tmp = $_FILES['logo']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));

    $src = match($ext) {
        'jpg','jpeg' => @imagecreatefromjpeg($tmp),
        'png'        => @imagecreatefrompng($tmp),
        'webp'       => @imagecreatefromwebp($tmp),
        default      => null,
    };

    if (!$src) {
        $error = 'Could not load image. Use JPG, PNG or WEBP.';
    } else {
        $w = imagesx($src);
        $h = imagesy($src);

        // Create output canvas (transparent)
        $dst = imagecreatetruecolor($w, $h);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefilledrectangle($dst, 0, 0, $w, $h, $transparent);
        imagealphablending($dst, true);

        // Convert: bright pixels → transparent, dark/coloured pixels → white
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgba  = imagecolorat($src, $x, $y);
                $r     = ($rgba >> 16) & 0xFF;
                $g     = ($rgba >>  8) & 0xFF;
                $b     =  $rgba        & 0xFF;
                $alpha = ($rgba >> 24) & 0x7F; // 0=opaque 127=transparent (GD)

                $lum = 0.299 * $r + 0.587 * $g + 0.114 * $b;

                // Already transparent in source → keep transparent
                if ($alpha > 60) {
                    $c = imagecolorallocatealpha($dst, 255, 255, 255, 127);
                    imagesetpixel($dst, $x, $y, $c);
                    continue;
                }

                // Bright background (white/near-white) → transparent
                if ($lum > 200) {
                    $a = (int) round(($lum - 200) / 55 * 127);
                    $c = imagecolorallocatealpha($dst, 255, 255, 255, min(127, $a));
                    imagesetpixel($dst, $x, $y, $c);
                } else {
                    // Content pixel → white, semi-transparent
                    $a = (int) round($lum / 200 * 80);
                    $c = imagecolorallocatealpha($dst, 255, 255, 255, $a);
                    imagesetpixel($dst, $x, $y, $c);
                }
            }
        }

        imagedestroy($src);

        if (!is_dir(dirname($out))) mkdir(dirname($out), 0755, true);
        if (imagepng($dst, $out)) {
            $done = true;
        } else {
            $error = 'Failed to save watermark PNG. Check write permissions on /images/';
        }
        imagedestroy($dst);
    }
}
?>
<!doctype html><html><head><meta charset="utf-8">
<title>Watermark Logo Setup — Kegalle.com</title>
<style>
body{font-family:sans-serif;max-width:560px;margin:60px auto;padding:0 20px;color:#222}
h1{font-size:22px;margin-bottom:4px}
p{color:#555;font-size:14px}
.box{border:2px dashed #ccc;border-radius:12px;padding:30px;text-align:center;margin:24px 0}
input[type=file]{margin:12px 0}
button{background:#1B5E20;color:#fff;border:none;padding:12px 28px;border-radius:8px;font-size:15px;font-weight:700;cursor:pointer}
.err{background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:14px;color:#b91c1c;margin:16px 0}
.ok{background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:14px;color:#166534;margin:16px 0}
img{max-width:100%;border-radius:8px;margin-top:12px;background:#333;padding:10px}
</style></head><body>
<h1>🖼 Watermark Logo Setup</h1>
<p>Upload your store logo — it will be auto-converted to <strong>white on transparent</strong> and used as the watermark on all uploaded images.</p>

<?php if ($done): ?>
<div class="ok">
    ✅ <strong>Watermark saved!</strong> It is now active for all new image uploads.<br>
    <strong>Delete this file from the server after you are done.</strong>
    <br><br>Preview (on dark background):
    <br><img src="/images/watermark.png?<?= time() ?>" alt="Watermark preview">
</div>
<?php elseif ($error): ?>
<div class="err">❌ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <div class="box">
        <div style="font-size:36px">📤</div>
        <div style="font-weight:700;margin:8px 0">Upload Logo</div>
        <div style="font-size:13px;color:#888;margin-bottom:12px">JPG, PNG or WEBP · any size</div>
        <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" required>
        <br><br>
        <button type="submit">Convert &amp; Save as Watermark</button>
    </div>
</form>

<p style="font-size:12px;color:#999">Saves to: <code>/public_html/images/watermark.png</code></p>
</body></html>
