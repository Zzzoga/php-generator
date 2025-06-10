<?php
// Настройки
$galleryDir = __DIR__ . '/gallery/';
$cacheDir = __DIR__ . '/cache/';

// Подключение к MySQL
$mysqli = new mysqli('localhost', 'u2366139_php', 'k13jbh1I!', 'u2366139_php');
if ($mysqli->connect_errno) {
    http_response_code(500);
    exit('DB connection error');
}

// Получение параметров
$name = isset($_GET['name']) ? basename($_GET['name']) : '';
$size = isset($_GET['size']) ? $_GET['size'] : '';

if (!$name || !$size) {
    http_response_code(400);
    exit('Missing parameters');
}

// Получение размера из БД
$stmt = $mysqli->prepare("SELECT width, height FROM image_sizes WHERE code = ?");
$stmt->bind_param('s', $size);
$stmt->execute();
$stmt->bind_result($maxWidth, $maxHeight);
if (!$stmt->fetch()) {
    http_response_code(404);
    exit('Size not found');
}
$stmt->close();

// Поиск исходного файла
$srcFile = '';
foreach (['jpg', 'jpeg', 'png'] as $ext) {
    $try = $galleryDir . $name . '.' . $ext;
    if (file_exists($try)) {
        $srcFile = $try;
        break;
    }
}
if (!$srcFile) {
    http_response_code(404);
    exit('Source image not found');
}

// Кэш-файл
$cacheFile = $cacheDir . "{$name}_{$size}.jpg";
if (file_exists($cacheFile)) {
    header('Content-Type: image/jpeg');
    readfile($cacheFile);
    exit;
}

// Загрузка изображения
$imgInfo = getimagesize($srcFile);
switch ($imgInfo[2]) {
    case IMAGETYPE_JPEG: $srcImg = imagecreatefromjpeg($srcFile); break;
    case IMAGETYPE_PNG:  $srcImg = imagecreatefrompng($srcFile); break;
    default:
        http_response_code(415);
        exit('Unsupported image type');
}

// Вычисление размеров
$srcW = $imgInfo[0];
$srcH = $imgInfo[1];
$ratio = min($maxWidth / $srcW, $maxHeight / $srcH, 1);
$newW = (int)($srcW * $ratio);
$newH = (int)($srcH * $ratio);

// Масштабирование
$dstImg = imagecreatetruecolor($newW, $newH);
imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);

// Сохранение в кэш
imagejpeg($dstImg, $cacheFile, 85);

// Вывод
header('Content-Type: image/jpeg');
readfile($cacheFile);

// Очистка
imagedestroy($srcImg);
imagedestroy($dstImg);
?>