<?php
header('Content-Type: text/plain; charset=UTF-8');
require_once '/home/kegalle/app_core/app/Helpers/ImageHelper.php';

// Test on a realistic photo-like background
$testPath = '/home/kegalle/public_html/wm_test.jpg';
$img = imagecreatetruecolor(800, 600);

// Gradient-like background to simulate a real photo
for ($y = 0; $y < 600; $y++) {
    for ($x = 0; $x < 800; $x++) {
        $r = (int)(80 + ($x / 800) * 120);
        $g = (int)(100 + ($y / 600) * 80);
        $b = (int)(60 + ($x / 800) * 60);
        $c = imagecolorallocate($img, $r, $g, $b);
        imagesetpixel($img, $x, $y, $c);
    }
}
imagejpeg($img, $testPath, 90);
imagedestroy($img);

$result = \App\Helpers\ImageHelper::addWatermark($testPath, 'KEGALLE.COM');
echo "Result: " . ($result ? 'OK' : 'FAILED') . "\n";
echo "View: https://kurulla.com/wm_test.jpg\n";
echo "(Open the URL to see the watermark)\n";
unlink(__FILE__);
