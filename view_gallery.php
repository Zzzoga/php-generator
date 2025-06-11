<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Галерея</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css"/>
    <style>
        .gallery { display: flex; flex-wrap: wrap; gap: 10px; }
        .gallery a { display: block; width: 150px; }
        .gallery img { width: 100%; height: auto; display: block; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h1>Галерея</h1>
    <div class="gallery">
        <?php foreach ($images as $name => $file): ?>
            <a data-fancybox="gallery" href="generator.php?name=<?=urlencode($name)?>&size=big">
                <img src="generator.php?name=<?=urlencode($name)?>&size=min" alt="<?=htmlspecialchars($name)?>">
            </a>
        <?php endforeach; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
</body>
</html>