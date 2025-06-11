<?php
$config = require __DIR__ . '/config.php';

// Получаем список изображений
$images = [];
foreach (scandir($config['gallery_dir']) as $file) {
    if (preg_match('/^(.+)\\.(jpg|jpeg|png)$/i', $file, $m)) {
        $images[$m[1]] = $file;
    }
}
// Ограничим до 10 изображений
$images = array_slice($images, 0, 10);

include __DIR__ . '/view_gallery.php';
?>