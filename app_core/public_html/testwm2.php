<?php
header('Content-Type: text/plain; charset=UTF-8');
require_once '/home/kegalle/app_core/app/Helpers/ImageHelper.php';

$wmFile = '/home/kegalle/public_html/images/watermark.png';
echo "Watermark PNG exists: " . (file_exists($wmFile) ? 'YES' : 'NO') . "\n";

$testPath = '/home/kegalle/public_html/wm_test2.jpg';
$img = imagecreatetruecolor(800, 600);
for ($y = 0; $y < 600; $y++) {
    for ($x = 0; $x < 800; $x++) {
        $r = (int)(180 + ($x / 800) * 60);
        $g = (int)(190 + ($y / 600) * 40);
        $b = (int)(200 + ($x / 800) * 40);
        imagesetpixel($img, $x, $y, imagecolorallocate($img, $r, $g, $b));
    }
}
imagejpeg($img, $testPath, 90);
imagedestroy($img);

$result = \App\Helpers\ImageHelper::addWatermark($testPath, 'KEGALLE.COM');
echo "Watermark result: " . ($result ? 'OK' : 'FAILED') . "\n";
echo "View: https://kurulla.com/wm_test2.jpg\n";
unlink(__FILE__);
