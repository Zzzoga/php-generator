<?php
$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/model.php';

$name = isset($_GET['name']) ? basename($_GET['name']) : '';
$size = isset($_GET['size']) ? $_GET['size'] : '';

if (!$name || !$size) {
    http_response_code(400);
    exit('Missing parameters');
}

$sizeData = get_image_size($size, $config);
if (!$sizeData) {
    http_response_code(404);
    exit('Size not found');
}

$srcFile = '';
foreach (['jpg', 'jpeg', 'png'] as $ext) {
    $try = $config['gallery_dir'] . $name . '.' . $ext;
    if (file_exists($try)) {
        $srcFile = $try;
        break;
    }
}
if (!$srcFile) {
    http_response_code(404);
    exit('Source image not found');
}

$cacheFile = $config['cache_dir'] . "{$name}_{$size}.jpg";
if (file_exists($cacheFile)) {
    header('Content-Type: image/jpeg');
    readfile($cacheFile);
    exit;
}

$imgInfo = getimagesize($srcFile);
switch ($imgInfo[2]) {
    case IMAGETYPE_JPEG: $srcImg = imagecreatefromjpeg($srcFile); break;
    case IMAGETYPE_PNG:  $srcImg = imagecreatefrompng($srcFile); break;
    default:
        http_response_code(415);
        exit('Unsupported image type');
}

$srcW = $imgInfo[0];
$srcH = $imgInfo[1];
$ratio = min($sizeData['width'] / $srcW, $sizeData['height'] / $srcH, 1);
$newW = (int)($srcW * $ratio);
$newH = (int)($srcH * $ratio);

$dstImg = imagecreatetruecolor($newW, $newH);
imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);

imagejpeg($dstImg, $cacheFile, 85);

header('Content-Type: image/jpeg');
readfile($cacheFile);

imagedestroy($srcImg);
imagedestroy($dstImg);
?>