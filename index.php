<?php
$galleryDir = 'gallery';
$subdirs = [];

if (is_dir($galleryDir)) {
    $items = scandir($galleryDir);
    foreach ($items as $item) {
        if ($item !== '.' && $item !== '..' && is_dir($galleryDir . '/' . $item)) {
            $subdirs[] = $item;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>图片画廊</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Microsoft YaHei', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1 {
            text-align: center;
            color: white;
            margin-bottom: 40px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .gallery-item {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        
        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }
        
        .gallery-name {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .gallery-info {
            color: #666;
            text-align: center;
            margin-bottom: 15px;
        }
        
        .view-button {
            display: block;
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: opacity 0.3s ease;
        }
        
        .view-button:hover {
            opacity: 0.9;
        }
        
        .empty-message {
            text-align: center;
            color: white;
            font-size: 1.2em;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎨 图片画廊</h1>
        
        <?php if (empty($subdirs)): ?>
            <div class="empty-message">
                暂无画廊目录，请在 gallery 目录下创建子目录
            </div>
        <?php else: ?>
            <div class="gallery-grid">
                <?php foreach ($subdirs as $subdir): ?>
                    <?php
                    $imageCount = 0;
                    $subdirPath = $galleryDir . '/' . $subdir;
                    if (is_dir($subdirPath)) {
                        $files = scandir($subdirPath);
                        foreach ($files as $file) {
                            if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
                                $imageCount++;
                            }
                        }
                    }
                    ?>
                    <div class="gallery-item" onclick="location.href='slideshow.php?dir=<?php echo urlencode($subdir); ?>'">
                        <div class="gallery-name">📁 <?php echo htmlspecialchars($subdir); ?></div>
                        <div class="gallery-info">
                            包含 <?php echo $imageCount; ?> 张图片
                        </div>
                        <a href="slideshow.php?dir=<?php echo urlencode($subdir); ?>" class="view-button" onclick="event.stopPropagation()">
                            查看画廊
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>