<?php
$galleryDir = 'gallery';
$currentDir = isset($_GET['dir']) ? $_GET['dir'] : '';
$subdirPath = $galleryDir . '/' . $currentDir;

// 验证目录
if (empty($currentDir) || !is_dir($subdirPath)) {
    header('Location: index.php');
    exit;
}

// 获取图片列表
$images = [];
$files = scandir($subdirPath);
foreach ($files as $file) {
    if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
        $images[] = $file;
    }
}

// 获取随机文字
$lines = [];
$linesFile = $subdirPath . '/lines.txt';
if (file_exists($linesFile)) {
    $lines = file($linesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

// 如果没有文字，使用默认文字
if (empty($lines)) {
    $lines = ['这是一张美丽的图片', '欣赏这美好的时刻', '留下难忘的回忆'];
}

// 为每个图片随机分配一行文字
$imageData = [];
foreach ($images as $index => $image) {
    $randomLine = $lines[array_rand($lines)];
    $imageData[] = [
        'file' => $image,
        'text' => $randomLine
    ];
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($currentDir); ?> - 图片轮播</title>
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
            display: flex;
            flex-direction: column;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            color: white;
            font-size: 1.8em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .back-button {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .back-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
        
        .slideshow-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            max-width: 100%;
            margin: 0 auto;
        }
        
        .slideshow-wrapper {
            position: relative;
            max-width: 800px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .slide {
            display: none;
            text-align: center;
        }
        
        .slide.active {
            display: block;
        }
        
        .slide img {
            width: 100%;
            height: 500px;
            object-fit: contain;
            background: #f5f5f5;
        }
        
        .slide-caption {
            padding: 20px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
            color: white;
            font-size: 1.2em;
            font-style: italic;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .navigation {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 15px 20px;
            cursor: pointer;
            font-size: 18px;
            transition: background 0.3s ease;
            z-index: 10;
        }
        
        .navigation:hover {
            background: rgba(0, 0, 0, 0.8);
        }
        
        .prev {
            left: 10px;
            border-radius: 0 5px 5px 0;
        }
        
        .next {
            right: 10px;
            border-radius: 5px 0 0 5px;
        }
        
        .dots-container {
            text-align: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            margin-top: 20px;
            border-radius: 50px;
        }
        
        .dot {
            height: 12px;
            width: 12px;
            margin: 0 8px;
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            display: inline-block;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .dot.active {
            background-color: white;
            transform: scale(1.2);
        }
        
        .empty-gallery {
            text-align: center;
            color: white;
            font-size: 1.5em;
            margin-top: 50px;
        }
        
        .slide-counter {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .slideshow-wrapper {
                margin: 10px;
            }
            
            .slide img {
                height: 300px;
            }
            
            .header {
                flex-direction: column;
                gap: 10px;
            }
            
            .header h1 {
                font-size: 1.5em;
            }
        }
        .fullscreen-mode {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: black;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .fullscreen-mode .slideshow-wrapper {
            width: 100%;
            height: 100%;
            max-width: none;
            border-radius: 0;
            box-shadow: none;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .fullscreen-mode .slide img {
            height: calc(100vh - 100px);
            object-fit: contain;
        }
        
        .fullscreen-mode .header,
        .fullscreen-mode .dots-container,
        .fullscreen-mode .navigation,
        .fullscreen-mode .slide-counter,
        .fullscreen-mode .time-control {
            display: none;
        }
        
        .fullscreen-mode .slide-caption {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 20px;
            font-size: 1.5em;
            text-align: center;
        }
        
        .time-control {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 15px;
            border-radius: 25px;
            backdrop-filter: blur(10px);
        }
        
        .time-control label {
            color: white;
            font-size: 14px;
        }
        
        .time-control select {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            padding: 5px 10px;
            font-size: 14px;
            cursor: pointer;
        }
        
        .time-control select option {
            background: #333;
            color: white;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h1>🖼️ <?php echo htmlspecialchars($currentDir); ?></h1>
            <div class="time-control">
                <label for="interval-select">切换时间:</label>
                <select id="interval-select">
                    <option value="3000">3秒</option>
                    <option value="5000">5秒</option>
                    <option value="10000" selected>10秒</option>
                    <option value="15000">15秒</option>
                    <option value="30000">30秒</option>
                    <option value="60000">60秒</option>
                </select>
            </div>
        </div>
        <div class="header-right">
            <a href="index.php" class="back-button">← 返回画廊列表</a>
        </div>
    </div>
    
    <div class="slideshow-container">
        <?php if (empty($imageData)): ?>
            <div class="empty-gallery">
                📸 该目录下暂无图片
                <br>
                <a href="index.php" class="back-button" style="margin-top: 20px; display: inline-block;">返回画廊列表</a>
            </div>
        <?php else: ?>
            <div class="slideshow-wrapper">
                <?php foreach ($imageData as $index => $data): ?>
                    <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>">
                        <img src="<?php echo htmlspecialchars($galleryDir . '/' . $currentDir . '/' . $data['file']); ?>" alt="<?php echo htmlspecialchars($data['file']); ?>">
                        <div class="slide-caption">
                            <?php echo htmlspecialchars($data['text']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="slide-counter">
                    <span id="current-slide">1</span> / <?php echo count($imageData); ?>
                </div>
                
                <button class="navigation prev" id="prevBtn">❮</button>
                <button class="navigation next" id="nextBtn">❯</button>
            </div>
            
            <div class="dots-container">
                <?php foreach ($imageData as $index => $data): ?>
                    <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index + 1; ?>"></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        let slideIndex = 1;
        let autoSlideInterval;
        let slideInterval = 10000; // 默认10秒
        let isFullscreen = false;
        
        function showSlide(n) {
            const slides = document.getElementsByClassName('slide');
            const dots = document.getElementsByClassName('dot');
            const currentSlideElement = document.getElementById('current-slide');
            
            if (n > slides.length) { slideIndex = 1; }
            if (n < 1) { slideIndex = slides.length; }
            
            for (let i = 0; i < slides.length; i++) {
                slides[i].classList.remove('active');
            }
            
            for (let i = 0; i < dots.length; i++) {
                dots[i].classList.remove('active');
            }
            
            if (slides[slideIndex - 1]) {
                slides[slideIndex - 1].classList.add('active');
            }
            
            if (dots[slideIndex - 1]) {
                dots[slideIndex - 1].classList.add('active');
            }
            
            if (currentSlideElement) {
                currentSlideElement.textContent = slideIndex;
            }
        }
        
        function changeSlide(n) {
            slideIndex += n;
            showSlide(slideIndex);
            resetAutoSlide();
        }
        
        function currentSlide(n) {
            slideIndex = n;
            showSlide(slideIndex);
            resetAutoSlide();
        }
        
        function startAutoSlide() {
            clearInterval(autoSlideInterval);
            autoSlideInterval = setInterval(function() {
                slideIndex++;
                showSlide(slideIndex);
            }, slideInterval);
        }
        
        function resetAutoSlide() {
            clearInterval(autoSlideInterval);
            startAutoSlide();
        }
        
        function toggleFullscreen() {
            isFullscreen = !isFullscreen;
            const body = document.body;
            
            if (isFullscreen) {
                body.classList.add('fullscreen-mode');
                document.body.style.cursor = 'none';
                
                // 3秒后隐藏鼠标
                setTimeout(() => {
                    if (isFullscreen) {
                        document.body.style.cursor = 'none';
                    }
                }, 3000);
            } else {
                body.classList.remove('fullscreen-mode');
                document.body.style.cursor = 'default';
            }
        }
        
        // 双击切换全屏
        document.addEventListener('dblclick', toggleFullscreen);
        
        // 键盘控制
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                changeSlide(-1);
            } else if (e.key === 'ArrowRight') {
                changeSlide(1);
            } else if (e.key === ' ') {
                e.preventDefault();
                changeSlide(1);
            } else if (e.key === 'f' || e.key === 'F') {
                toggleFullscreen();
            } else if (e.key === 'Escape' && isFullscreen) {
                toggleFullscreen();
            }
        });
        
        // 时间间隔控制
        const intervalSelect = document.getElementById('interval-select');
        if (intervalSelect) {
            intervalSelect.addEventListener('change', function() {
                slideInterval = parseInt(this.value);
                resetAutoSlide();
                
                // 显示提示
                const notification = document.createElement('div');
                notification.style.cssText = `
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background: rgba(0, 0, 0, 0.8);
                    color: white;
                    padding: 15px 25px;
                    border-radius: 10px;
                    z-index: 2000;
                    font-size: 16px;
                `;
                notification.textContent = `切换间隔已设置为 ${this.options[this.selectedIndex].text}`;
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 2000);
            });
        }
        
        // 鼠标移动时显示鼠标
        document.addEventListener('mousemove', function() {
            if (isFullscreen) {
                document.body.style.cursor = 'default';
                clearTimeout(window.cursorTimeout);
                window.cursorTimeout = setTimeout(() => {
                    if (isFullscreen) {
                        document.body.style.cursor = 'none';
                    }
                }, 3000);
            }
        });
        
        // 添加按钮和圆点事件监听器
        document.addEventListener('DOMContentLoaded', function() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const dots = document.getElementsByClassName('dot');
            
            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    changeSlide(-1);
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    changeSlide(1);
                });
            }
            
            // 为每个圆点添加点击事件
            for (let i = 0; i < dots.length; i++) {
                dots[i].addEventListener('click', function() {
                    const slideNumber = parseInt(this.getAttribute('data-slide'));
                    currentSlide(slideNumber);
                });
            }
        });
        
        // 初始化
        showSlide(slideIndex);
        <?php if (!empty($imageData)): ?>
        startAutoSlide();
        <?php endif; ?>
    </script>
</body>
</html>