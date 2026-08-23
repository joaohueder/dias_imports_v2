<?php
$img = imagecreatetruecolor(400, 400);
$bg = imagecolorallocate($img, 124, 105, 255);
imagefilledrectangle($img, 0, 0, 400, 400, $bg);
imagepng($img, __DIR__ . '/test_upload.png');
imagedestroy($img);
echo "Image created: " . filesize(__DIR__ . '/test_upload.png') . " bytes\n";
