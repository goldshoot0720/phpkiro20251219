<?php
// 處理AJAX請求
if (isset($_GET['action']) && $_GET['action'] === 'getVideos') {
    header('Content-Type: application/json');
    
    $videosDir = './videos/';
    $videos = [];
    
    if (is_dir($videosDir)) {
        $files = scandir($videosDir);
        
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $videosDir . $file;
                
                if (is_file($filePath)) {
                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv', '3gp'];
                    
                    if (in_array($extension, $videoExtensions)) {
                        $fileSize = filesize($filePath);
                        $fileSizeFormatted = formatFileSize($fileSize);
                        
                        $videos[] = [
                            'name' => $file,
                            'displayName' => $file,
                            'type' => getVideoType($extension) . ' (' . $fileSizeFormatted . ')',
                            'size' => $fileSize,
                            'extension' => $extension
                        ];
                    }
                }
            }
        }
    }
    
    // 按檔案名稱排序
    usort($videos, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });
    
    echo json_encode(['videos' => $videos]);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'getImages') {
    header('Content-Type: application/json');
    
    $imagesDir = './images/';
    $images = [];
    
    if (is_dir($imagesDir)) {
        $files = scandir($imagesDir);
        
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $imagesDir . $file;
                
                if (is_file($filePath)) {
                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
                    
                    if (in_array($extension, $imageExtensions)) {
                        $fileSize = filesize($filePath);
                        $fileSizeFormatted = formatFileSize($fileSize);
                        
                        $images[] = [
                            'name' => $file,
                            'displayName' => $file,
                            'type' => getImageType($extension) . ' (' . $fileSizeFormatted . ')',
                            'size' => $fileSize,
                            'extension' => $extension
                        ];
                    }
                }
            }
        }
    }
    
    // 按檔案名稱排序
    usort($images, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });
    
    echo json_encode(['images' => $images]);
    exit;
}

function formatFileSize($bytes) {
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' B';
    }
}

function getImageType($extension) {
    $types = [
        'jpg' => 'JPEG 圖片',
        'jpeg' => 'JPEG 圖片',
        'png' => 'PNG 圖片',
        'gif' => 'GIF 動圖',
        'webp' => 'WebP 圖片',
        'bmp' => 'BMP 圖片',
        'svg' => 'SVG 向量圖'
    ];
    
    return $types[$extension] ?? '圖片檔案';
}

function getVideoType($extension) {
    $types = [
        'mp4' => 'MP4 影片',
        'avi' => 'AVI 影片',
        'mov' => 'MOV 影片',
        'wmv' => 'WMV 影片',
        'flv' => 'FLV 影片',
        'webm' => 'WebM 影片',
        'mkv' => 'MKV 影片',
        '3gp' => '3GP 影片'
    ];
    
    return $types[$extension] ?? '影片檔案';
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>鋒兄AI資訊系統</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Microsoft JhengHei', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* 頂部導航 */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 18px;
            font-weight: bold;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: #ff6b6b;
            border-radius: 8px;
            margin-right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .nav-menu {
            display: flex;
            gap: 30px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-icon {
            margin-right: 8px;
            font-size: 16px;
        }

        /* 主要內容區域 */
        .main-content {
            text-align: center;
            padding: 80px 0;
        }

        .main-title {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-logo {
            width: 60px;
            height: 60px;
            background: #ff6b6b;
            border-radius: 12px;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .subtitle {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 60px;
        }

        /* 功能卡片區域 */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 60px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 30px;
            text-align: left;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .feature-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .feature-desc {
            opacity: 0.8;
            line-height: 1.6;
        }

        /* 版權信息 */
        .footer {
            text-align: center;
            padding: 40px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 80px;
            opacity: 0.7;
        }

        /* 圖片庫樣式 */
        .gallery-container {
            text-align: left;
            padding: 40px 0;
        }

        .gallery-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .search-box {
            padding: 12px 20px;
            border: none;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 16px;
            min-width: 300px;
            backdrop-filter: blur(10px);
        }

        .search-box::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .upload-btn {
            padding: 12px 25px;
            background: #ff6b6b;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .upload-btn:hover {
            background: #ff5252;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .image-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .image-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .image-preview {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: rgba(255, 255, 255, 0.3);
        }

        .image-info {
            padding: 15px;
        }

        .image-name {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .image-size {
            font-size: 14px;
            opacity: 0.7;
        }

        .empty-gallery {
            text-align: center;
            padding: 60px 20px;
            opacity: 0.7;
        }

        .empty-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        /* 影片庫樣式 */
        .video-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .video-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .video-preview {
            width: 100%;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: rgba(255, 255, 255, 0.3);
            position: relative;
        }

        .video-thumbnail {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .play-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.7);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .video-info {
            padding: 15px;
        }

        .video-name {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .video-size {
            font-size: 14px;
            opacity: 0.7;
        }

        /* 訂閱管理樣式 */
        .subscription-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 30px;
        }

        .subscription-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 25px;
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid #45b7d1;
        }

        .subscription-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .subscription-card.expired {
            border-left-color: #ff6b6b;
            background: rgba(255, 107, 107, 0.1);
        }

        .subscription-card.expiring-soon {
            border-left-color: #ffa726;
            background: rgba(255, 167, 38, 0.1);
        }

        .subscription-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .subscription-name {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
        }

        .subscription-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .action-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .edit-btn:hover {
            background: rgba(69, 183, 209, 0.3);
        }

        .delete-btn:hover {
            background: rgba(255, 107, 107, 0.3);
        }

        .subscription-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .detail-label {
            font-size: 14px;
            opacity: 0.8;
            font-weight: 500;
        }

        .detail-value {
            font-size: 16px;
            font-weight: bold;
        }

        .detail-value.price {
            color: #4ecdc4;
        }

        .detail-value.expired {
            color: #ff6b6b;
        }

        .detail-value.expiring-soon {
            color: #ffa726;
        }

        .detail-value.normal {
            color: #4ecdc4;
        }

        .detail-value a {
            color: #4ecdc4;
            text-decoration: none;
            transition: color 0.3s;
        }

        .detail-value a:hover {
            color: #45b7d1;
            text-decoration: underline;
        }

        /* 模態框樣式 */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 0;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }

        .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .subscription-form {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 16px;
            backdrop-filter: blur(10px);
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 2px rgba(69, 183, 209, 0.5);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .cancel-btn,
        .save-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .cancel-btn {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .cancel-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .save-btn {
            background: #4ecdc4;
            color: white;
        }

        .save-btn:hover {
            background: #45b7d1;
            transform: translateY(-1px);
        }

        /* 食品管理樣式 */
        .food-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 30px;
        }

        .food-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 25px;
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid #96ceb4;
        }

        .food-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .food-card.expired {
            border-left-color: #ff6b6b;
            background: rgba(255, 107, 107, 0.1);
        }

        .food-card.expiring-soon {
            border-left-color: #ffa726;
            background: rgba(255, 167, 38, 0.1);
        }

        .food-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .food-name {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
        }

        .food-actions {
            display: flex;
            gap: 10px;
        }

        .food-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        /* 響應式設計 */
        /* 儀表板特殊樣式 */
        .dashboard-button {
            transition: all 0.3s ease;
        }

        .dashboard-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        /* 手機版選單樣式 */
        .mobile-menu-toggle {
            display: none;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 12px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
            transition: all 0.3s;
            z-index: 100;
            position: relative;
            min-width: 50px;
            text-align: center;
            outline: none;
        }

        .mobile-menu-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
        }

        .mobile-menu-toggle:active {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0.95);
        }

        .mobile-menu-toggle:focus {
            outline: 2px solid rgba(255, 255, 255, 0.5);
            outline-offset: 2px;
        }

        .mobile-menu {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
        }

        .mobile-menu.show {
            opacity: 1;
            visibility: visible;
        }

        .mobile-menu-content {
            position: absolute;
            top: 0;
            right: 0;
            width: 280px;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s ease-out;
            overflow-y: auto;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
            }
            to {
                transform: translateX(0);
            }
        }

        .mobile-menu-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .mobile-menu-close {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 5px;
        }

        .mobile-nav-item {
            display: flex;
            align-items: center;
            padding: 15px 10px;
            margin-bottom: 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 16px;
        }

        .mobile-nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .mobile-nav-item.active {
            background: rgba(255, 255, 255, 0.2);
        }

        .mobile-nav-icon {
            margin-right: 12px;
            font-size: 18px;
        }

        /* 平板版本 (768px - 1024px) */
        @media (max-width: 1024px) and (min-width: 769px) {
            .container {
                padding: 0 30px;
            }
            
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
            }
            
            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 20px;
            }
            
            .main-title {
                font-size: 42px;
            }
            
            .nav-menu {
                gap: 20px;
            }
            
            .nav-item {
                padding: 10px 18px;
                font-size: 15px;
            }
            
            /* 儀表板平板優化 */
            .dashboard-button {
                padding: 12px 25px !important;
                font-size: 15px !important;
            }
        }

        /* 手機版本 (最大 768px) */
        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }
            
            .nav-menu {
                display: none;
            }
            
            .mobile-menu-toggle {
                display: block !important;
                background: rgba(255, 255, 255, 0.2) !important;
                border: 2px solid rgba(255, 255, 255, 0.3) !important;
            }
            
            .mobile-menu-toggle:hover {
                background: rgba(255, 255, 255, 0.3) !important;
            }
            
            .main-title {
                font-size: 28px;
                flex-direction: column;
                text-align: center;
            }
            
            .main-logo {
                margin-right: 0;
                margin-bottom: 10px;
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
            
            .subtitle {
                font-size: 16px;
                margin-bottom: 40px;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
                gap: 20px;
                margin-top: 40px;
            }
            
            .feature-card {
                padding: 25px 20px;
            }
            
            .feature-icon {
                width: 45px;
                height: 45px;
                font-size: 20px;
            }
            
            .feature-title {
                font-size: 18px;
            }

            .gallery-controls {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }

            .search-box {
                min-width: auto;
                width: 100%;
                padding: 15px 20px;
                font-size: 16px;
            }
            
            .upload-btn, .add-btn {
                padding: 15px 25px;
                font-size: 16px;
                width: 100%;
            }

            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 15px;
            }
            
            .image-card, .video-card {
                border-radius: 12px;
            }
            
            .image-preview, .video-preview {
                height: 120px;
            }

            .subscription-details, .food-details {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .subscription-header, .food-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .subscription-actions, .food-actions {
                align-self: flex-end;
                width: 100%;
                justify-content: flex-end;
            }
            
            .subscription-name, .food-name {
                font-size: 18px;
            }

            .modal-content {
                width: 95%;
                margin: 10px;
                max-height: 95vh;
            }
            
            .modal-header {
                padding: 20px;
            }
            
            .subscription-form {
                padding: 20px;
            }

            .form-actions {
                flex-direction: column;
                gap: 12px;
            }

            .cancel-btn, .save-btn {
                width: 100%;
                padding: 15px;
                font-size: 16px;
            }
            
            /* 儀表板手機優化 */
            .dashboard-button {
                padding: 12px 20px !important;
                font-size: 14px !important;
                width: 100% !important;
                margin-bottom: 10px;
            }
            
            /* 快速操作按鈕手機版 */
            .quick-actions-mobile {
                flex-direction: column;
                gap: 12px;
            }
            
            .quick-actions-mobile .dashboard-button {
                width: 100% !important;
                padding: 15px !important;
                font-size: 16px !important;
            }
        }

        /* 小手機版本 (最大 480px) */
        @media (max-width: 480px) {
            .container {
                padding: 0 10px;
            }
            
            .main-title {
                font-size: 24px;
            }
            
            .main-logo {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }
            
            .subtitle {
                font-size: 14px;
            }
            
            .feature-card {
                padding: 20px 15px;
            }
            
            .feature-title {
                font-size: 16px;
            }
            
            .feature-desc {
                font-size: 14px;
            }
            
            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 12px;
            }
            
            .image-preview, .video-preview {
                height: 100px;
            }
            
            .image-info, .video-info {
                padding: 12px;
            }
            
            .image-name, .video-name {
                font-size: 14px;
            }
            
            .image-size, .video-size {
                font-size: 12px;
            }
            
            .subscription-card, .food-card {
                padding: 20px 15px;
            }
            
            .detail-label {
                font-size: 12px;
            }
            
            .detail-value {
                font-size: 14px;
            }
            
            .action-btn {
                padding: 6px 10px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div id="app">
        <div class="container">
            <!-- 頂部導航 -->
            <nav class="navbar">
                <div class="logo">
                    <div class="logo-icon">🤖</div>
                    鋒兄AI資訊系統
                </div>
                <div class="nav-menu">
                    <div class="nav-item" @click="currentPage = 'dashboard'">
                        <span class="nav-icon">📊</span>
                        儀表板
                    </div>
                    <div class="nav-item" @click="currentPage = 'gallery'">
                        <span class="nav-icon">🖼️</span>
                        圖片庫
                    </div>
                    <div class="nav-item" @click="currentPage = 'videos'">
                        <span class="nav-icon">🎬</span>
                        影片庫
                    </div>
                    <div class="nav-item" @click="currentPage = 'subscriptions'">
                        <span class="nav-icon">📋</span>
                        訂閱管理
                    </div>
                    <div class="nav-item" @click="currentPage = 'foods'">
                        <span class="nav-icon">🍽️</span>
                        食品管理
                    </div>
                </div>
                
                <!-- 手機版漢堡選單 -->
                <button class="mobile-menu-toggle" @click="showMobileMenu = !showMobileMenu" type="button">
                    ☰
                </button>
            </nav>

            <!-- 手機版選單 -->
            <div class="mobile-menu" :class="{ show: showMobileMenu }" v-show="showMobileMenu" @click="showMobileMenu = false">
                <div class="mobile-menu-content" @click.stop>
                    <div class="mobile-menu-header">
                        <div style="display: flex; align-items: center;">
                            <div class="logo-icon" style="margin-right: 10px;">🤖</div>
                            <span style="font-weight: bold;">鋒兄AI系統</span>
                        </div>
                        <button class="mobile-menu-close" @click="showMobileMenu = false">✕</button>
                    </div>
                    
                    <div class="mobile-nav-item" :class="{ active: currentPage === 'home' }" @click="currentPage = 'home'; showMobileMenu = false">
                        <span class="mobile-nav-icon">🏠</span>
                        首頁
                    </div>
                    <div class="mobile-nav-item" :class="{ active: currentPage === 'dashboard' }" @click="currentPage = 'dashboard'; showMobileMenu = false">
                        <span class="mobile-nav-icon">📊</span>
                        儀表板
                    </div>
                    <div class="mobile-nav-item" :class="{ active: currentPage === 'gallery' }" @click="currentPage = 'gallery'; showMobileMenu = false">
                        <span class="mobile-nav-icon">🖼️</span>
                        圖片庫
                    </div>
                    <div class="mobile-nav-item" :class="{ active: currentPage === 'videos' }" @click="currentPage = 'videos'; showMobileMenu = false">
                        <span class="mobile-nav-icon">🎬</span>
                        影片庫
                    </div>
                    <div class="mobile-nav-item" :class="{ active: currentPage === 'subscriptions' }" @click="currentPage = 'subscriptions'; showMobileMenu = false">
                        <span class="mobile-nav-icon">📋</span>
                        訂閱管理
                    </div>
                    <div class="mobile-nav-item" :class="{ active: currentPage === 'foods' }" @click="currentPage = 'foods'; showMobileMenu = false">
                        <span class="mobile-nav-icon">🍽️</span>
                        食品管理
                    </div>
                </div>
            </div>

            <!-- 主要內容 -->
            <main class="main-content" v-if="currentPage === 'home'">
                <h1 class="main-title">
                    <div class="main-logo">🤖</div>
                    鋒兄AI資訊系統
                </h1>
                <p class="subtitle">年小管理您的影片和圖片收藏，支援智能分類和快速搜尋</p>

                <!-- 功能卡片 -->
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: #ff6b6b;">🔥</div>
                        <div class="feature-title">前端 - Vue SPA</div>
                        <div class="feature-desc">使用Vue.js單頁應用架構，提供流暢的用戶體驗</div>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon" style="background: #4ecdc4;">💧</div>
                        <div class="feature-title">後端 - PHP＋MySQL</div>
                        <div class="feature-desc">穩定可靠的PHP後端搭配MySQL資料庫</div>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon" style="background: #45b7d1;">🌐</div>
                        <div class="feature-title">網頁存放於 - InfinityFree</div>
                        <div class="feature-desc">免費穩定的網頁託管服務，全球訪問</div>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon" style="background: #96ceb4;">🎬</div>
                        <div class="feature-title">影片存放於 - InfinityFree</div>
                        <div class="feature-desc">高效的影片儲存和串流播放服務</div>
                    </div>
                </div>
            </main>

            <!-- 儀表板頁面 -->
            <main class="gallery-container" v-if="currentPage === 'dashboard'">
                <h1 class="main-title">
                    <div class="main-logo" style="background: #ff6b6b;">📊</div>
                    系統儀表板
                </h1>
                <p class="subtitle">即時監控訂閱和食品到期狀態</p>

                <!-- 統計卡片 -->
                <div class="features-grid" style="margin-top: 40px;">
                    <!-- 訂閱管理統計 -->
                    <div class="feature-card" style="background: rgba(69, 183, 209, 0.15); border-left: 4px solid #45b7d1;">
                        <div class="feature-icon" style="background: #45b7d1;">📋</div>
                        <div class="feature-title">訂閱管理</div>
                        <div class="feature-desc" style="margin-bottom: 20px;">訂閱服務到期提醒</div>
                        
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <div style="background: rgba(255, 255, 255, 0.1); padding: 15px; border-radius: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 14px; opacity: 0.9;">3天內到期</span>
                                    <span style="font-size: 24px; font-weight: bold; color: #ff6b6b;">{{ dashboardStats.subscriptions.threeDays }}</span>
                                </div>
                                <div v-if="dashboardStats.subscriptions.threeDays > 0" style="margin-top: 10px; font-size: 13px; color: #ff6b6b;">
                                    ⚠️ 請盡快處理即將到期的訂閱
                                </div>
                            </div>
                            
                            <div style="background: rgba(255, 255, 255, 0.1); padding: 15px; border-radius: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 14px; opacity: 0.9;">7天內到期</span>
                                    <span style="font-size: 24px; font-weight: bold; color: #ffa726;">{{ dashboardStats.subscriptions.sevenDays }}</span>
                                </div>
                                <div v-if="dashboardStats.subscriptions.sevenDays > 0" style="margin-top: 10px; font-size: 13px; color: #ffa726;">
                                    💡 建議提前準備續訂
                                </div>
                            </div>
                        </div>
                        
                        <button @click="currentPage = 'subscriptions'" class="dashboard-button" style="margin-top: 20px; width: 100%; padding: 10px; background: #45b7d1; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px;">
                            查看詳情 →
                        </button>
                    </div>

                    <!-- 食品管理統計 -->
                    <div class="feature-card" style="background: rgba(150, 206, 180, 0.15); border-left: 4px solid #96ceb4;">
                        <div class="feature-icon" style="background: #96ceb4;">🍽️</div>
                        <div class="feature-title">食品管理</div>
                        <div class="feature-desc" style="margin-bottom: 20px;">食品到期日期監控</div>
                        
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <div style="background: rgba(255, 255, 255, 0.1); padding: 15px; border-radius: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 14px; opacity: 0.9;">7天內到期</span>
                                    <span style="font-size: 24px; font-weight: bold; color: #ff6b6b;">{{ dashboardStats.foods.sevenDays }}</span>
                                </div>
                                <div v-if="dashboardStats.foods.sevenDays > 0" style="margin-top: 10px; font-size: 13px; color: #ff6b6b;">
                                    ⚠️ 請盡快食用即將過期的食品
                                </div>
                            </div>
                            
                            <div style="background: rgba(255, 255, 255, 0.1); padding: 15px; border-radius: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 14px; opacity: 0.9;">30天內到期</span>
                                    <span style="font-size: 24px; font-weight: bold; color: #ffa726;">{{ dashboardStats.foods.thirtyDays }}</span>
                                </div>
                                <div v-if="dashboardStats.foods.thirtyDays > 0" style="margin-top: 10px; font-size: 13px; color: #ffa726;">
                                    💡 注意食品保存期限
                                </div>
                            </div>
                        </div>
                        
                        <button @click="currentPage = 'foods'" class="dashboard-button" style="margin-top: 20px; width: 100%; padding: 10px; background: #96ceb4; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px;">
                            查看詳情 →
                        </button>
                    </div>

                    <!-- 總覽統計 -->
                    <div class="feature-card" style="background: rgba(255, 107, 107, 0.15); border-left: 4px solid #ff6b6b;">
                        <div class="feature-icon" style="background: #ff6b6b;">📈</div>
                        <div class="feature-title">系統總覽</div>
                        <div class="feature-desc" style="margin-bottom: 20px;">資料統計概覽</div>
                        
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <div style="background: rgba(255, 255, 255, 0.1); padding: 15px; border-radius: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 14px; opacity: 0.9;">訂閱總數</span>
                                    <span style="font-size: 24px; font-weight: bold; color: #4ecdc4;">{{ subscriptions.length }}</span>
                                </div>
                            </div>
                            
                            <div style="background: rgba(255, 255, 255, 0.1); padding: 15px; border-radius: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 14px; opacity: 0.9;">食品總數</span>
                                    <span style="font-size: 24px; font-weight: bold; color: #4ecdc4;">{{ foods.length }}</span>
                                </div>
                            </div>
                            
                            <div style="background: rgba(255, 255, 255, 0.1); padding: 15px; border-radius: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 14px; opacity: 0.9;">圖片總數</span>
                                    <span style="font-size: 24px; font-weight: bold; color: #4ecdc4;">{{ images.length }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 快速操作 -->
                <div style="margin-top: 60px; text-align: center;">
                    <h2 style="font-size: 24px; margin-bottom: 30px;">快速操作</h2>
                    <div class="quick-actions-mobile" style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                        <button @click="currentPage = 'subscriptions'; showAddSubscriptionModal = true" class="dashboard-button" style="padding: 15px 30px; background: #45b7d1; color: white; border: none; border-radius: 25px; cursor: pointer; font-size: 16px;">
                            ➕ 新增訂閱
                        </button>
                        <button @click="currentPage = 'foods'; showAddFoodModal = true" class="dashboard-button" style="padding: 15px 30px; background: #96ceb4; color: white; border: none; border-radius: 25px; cursor: pointer; font-size: 16px;">
                            ➕ 新增食品
                        </button>
                        <button @click="currentPage = 'gallery'" class="dashboard-button" style="padding: 15px 30px; background: #4ecdc4; color: white; border: none; border-radius: 25px; cursor: pointer; font-size: 16px;">
                            🖼️ 圖片庫
                        </button>
                        <button @click="currentPage = 'videos'" class="dashboard-button" style="padding: 15px 30px; background: #ff6b6b; color: white; border: none; border-radius: 25px; cursor: pointer; font-size: 16px;">
                            🎬 影片庫
                        </button>
                    </div>
                </div>
            </main>

            <!-- 圖片庫頁面 -->
            <main class="gallery-container" v-if="currentPage === 'gallery'">
                <h1 class="main-title">
                    <div class="main-logo" style="background: #4ecdc4;">🖼️</div>
                    圖片庫管理
                </h1>
                <p class="subtitle">基於 images 資料夾的圖片管理系統</p>

                <!-- 圖片庫控制項 -->
                <div class="gallery-controls">
                    <input 
                        type="text" 
                        class="search-box" 
                        placeholder="搜尋圖片名稱..." 
                        v-model="searchQuery"
                        @input="filterImages"
                    >
                    <button class="upload-btn" @click="refreshImages">
                        🔄 重新載入
                    </button>
                </div>

                <!-- 圖片網格 -->
                <div class="gallery-grid" v-if="filteredImages.length > 0">
                    <div 
                        class="image-card" 
                        v-for="image in filteredImages" 
                        :key="image.name"
                        @click="viewImage(image)"
                    >
                        <div class="image-preview">
                            <img 
                                :src="'images/' + image.name" 
                                :alt="image.name"
                                style="width: 100%; height: 100%; object-fit: cover;"
                                @error="handleImageError"
                            >
                        </div>
                        <div class="image-info">
                            <div class="image-name">{{ image.displayName }}</div>
                            <div class="image-size">{{ image.type }}</div>
                        </div>
                    </div>
                </div>

                <!-- 空狀態 -->
                <div class="empty-gallery" v-else>
                    <div class="empty-icon">📁</div>
                    <h3>暫無圖片</h3>
                    <p>請將圖片放入 images 資料夾中</p>
                </div>
            </main>

            <!-- 影片庫頁面 -->
            <main class="gallery-container" v-if="currentPage === 'videos'">
                <h1 class="main-title">
                    <div class="main-logo" style="background: #96ceb4;">🎬</div>
                    影片庫管理
                </h1>
                <p class="subtitle">基於 videos 資料夾的影片管理系統</p>

                <!-- 影片庫控制項 -->
                <div class="gallery-controls">
                    <input 
                        type="text" 
                        class="search-box" 
                        placeholder="搜尋影片名稱..." 
                        v-model="videoSearchQuery"
                        @input="filterVideos"
                    >
                    <button class="upload-btn" @click="refreshVideos">
                        🔄 重新載入
                    </button>
                </div>

                <!-- 影片網格 -->
                <div class="gallery-grid" v-if="filteredVideos.length > 0">
                    <div 
                        class="video-card" 
                        v-for="video in filteredVideos" 
                        :key="video.name"
                        @click="viewVideo(video)"
                    >
                        <div class="video-preview">
                            <video 
                                :src="'videos/' + video.name" 
                                class="video-thumbnail"
                                preload="metadata"
                                @error="handleVideoError"
                            ></video>
                            <div class="play-overlay">▶️</div>
                        </div>
                        <div class="video-info">
                            <div class="video-name">{{ video.displayName }}</div>
                            <div class="video-size">{{ video.type }}</div>
                        </div>
                    </div>
                </div>

                <!-- 空狀態 -->
                <div class="empty-gallery" v-else>
                    <div class="empty-icon">🎬</div>
                    <h3>暫無影片</h3>
                    <p>請將影片放入 videos 資料夾中</p>
                </div>
            </main>

            <!-- 訂閱管理頁面 -->
            <main class="gallery-container" v-if="currentPage === 'subscriptions'">
                <h1 class="main-title">
                    <div class="main-logo" style="background: #45b7d1;">📋</div>
                    訂閱管理系統
                </h1>
                <p class="subtitle">管理您的各種訂閱服務和會員資訊</p>

                <!-- 訂閱管理控制項 -->
                <div class="gallery-controls">
                    <input 
                        type="text" 
                        class="search-box" 
                        placeholder="搜尋訂閱名稱..." 
                        v-model="subscriptionSearchQuery"
                        @input="filterSubscriptions"
                    >
                    <button class="upload-btn" @click="showAddSubscriptionModal = true">
                        ➕ 新增訂閱
                    </button>
                </div>

                <!-- 訂閱列表 -->
                <div class="subscription-list" v-if="filteredSubscriptions.length > 0">
                    <div 
                        class="subscription-card" 
                        v-for="(subscription, index) in filteredSubscriptions" 
                        :key="index"
                        :class="{ 'expired': isExpired(subscription.nextdate), 'expiring-soon': isExpiringSoon(subscription.nextdate) }"
                    >
                        <div class="subscription-header">
                            <h3 class="subscription-name">{{ subscription.name }}</h3>
                            <div class="subscription-actions">
                                <button class="action-btn edit-btn" @click="editSubscription(subscription, index)">✏️</button>
                                <button class="action-btn delete-btn" @click="deleteSubscription(index)">🗑️</button>
                            </div>
                        </div>
                        <div class="subscription-details">
                            <div class="detail-item">
                                <span class="detail-label">下次付款日期:</span>
                                <span class="detail-value" :class="getDateClass(subscription.nextdate)">{{ formatDate(subscription.nextdate) }}</span>
                            </div>
                            <div class="detail-item" v-if="subscription.price && subscription.price > 0">
                                <span class="detail-label">價格:</span>
                                <span class="detail-value price">NT$ {{ subscription.price }}</span>
                            </div>
                            <div class="detail-item" v-if="subscription.site">
                                <span class="detail-label">網站:</span>
                                <span class="detail-value">
                                    <a :href="subscription.site" target="_blank" style="color: #4ecdc4; text-decoration: none;">
                                        {{ subscription.site }}
                                    </a>
                                </span>
                            </div>
                            <div class="detail-item" v-if="subscription.account">
                                <span class="detail-label">帳號:</span>
                                <span class="detail-value">{{ subscription.account }}</span>
                            </div>
                            <div class="detail-item" v-if="subscription.note">
                                <span class="detail-label">備註:</span>
                                <span class="detail-value">{{ subscription.note }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 空狀態 -->
                <div class="empty-gallery" v-else>
                    <div class="empty-icon">📋</div>
                    <h3>暫無訂閱記錄</h3>
                    <p>點擊「新增訂閱」開始管理您的訂閱服務</p>
                </div>

                <!-- 新增/編輯訂閱模態框 -->
                <div class="modal-overlay" v-if="showAddSubscriptionModal || showEditSubscriptionModal" @click="closeModals">
                    <div class="modal-content" @click.stop>
                        <div class="modal-header">
                            <h3>{{ showEditSubscriptionModal ? '編輯訂閱' : '新增訂閱' }}</h3>
                            <button class="close-btn" @click="closeModals">✕</button>
                        </div>
                        <form @submit.prevent="saveSubscription" class="subscription-form">
                            <div class="form-group">
                                <label>訂閱名稱 *</label>
                                <input type="text" v-model="currentSubscription.name" required>
                            </div>
                            <div class="form-group">
                                <label>下次付款日期 *</label>
                                <input type="date" v-model="currentSubscription.nextdate" required>
                            </div>
                            <div class="form-group">
                                <label>價格 (NT$)</label>
                                <input type="number" v-model="currentSubscription.price" min="0" placeholder="選填">
                            </div>
                            <div class="form-group">
                                <label>網站</label>
                                <input type="text" v-model="currentSubscription.site">
                            </div>
                            <div class="form-group">
                                <label>帳號</label>
                                <input type="text" v-model="currentSubscription.account">
                            </div>
                            <div class="form-group">
                                <label>備註</label>
                                <textarea v-model="currentSubscription.note" rows="3"></textarea>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="cancel-btn" @click="closeModals">取消</button>
                                <button type="submit" class="save-btn">{{ showEditSubscriptionModal ? '更新' : '新增' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>

            <!-- 食品管理頁面 -->
            <main class="gallery-container" v-if="currentPage === 'foods'">
                <h1 class="main-title">
                    <div class="main-logo" style="background: #96ceb4;">🍽️</div>
                    食品管理系統
                </h1>
                <p class="subtitle">管理您的食品庫存和到期日期</p>

                <!-- 食品管理控制項 -->
                <div class="gallery-controls">
                    <input 
                        type="text" 
                        class="search-box" 
                        placeholder="搜尋食品名稱或商店..." 
                        v-model="foodSearchQuery"
                        @input="filterFoods"
                    >
                    <button class="upload-btn" @click="showAddFoodModal = true">
                        ➕ 新增食品
                    </button>
                </div>

                <!-- 食品列表 -->
                <div class="food-list" v-if="filteredFoods.length > 0">
                    <div 
                        class="food-card" 
                        v-for="(food, index) in filteredFoods" 
                        :key="index"
                        :class="{ 'expired': isExpired(food.todate), 'expiring-soon': isExpiringSoon(food.todate) }"
                    >
                        <div class="food-header">
                            <h3 class="food-name">{{ food.name }}</h3>
                            <div class="food-actions">
                                <button class="action-btn edit-btn" @click="editFood(food, index)">✏️</button>
                                <button class="action-btn delete-btn" @click="deleteFood(index)">🗑️</button>
                            </div>
                        </div>
                        <div class="food-details">
                            <div class="detail-item">
                                <span class="detail-label">到期日期:</span>
                                <span class="detail-value" :class="getDateClass(food.todate)">{{ formatDate(food.todate) }}</span>
                            </div>
                            <div class="detail-item" v-if="food.amount">
                                <span class="detail-label">數量:</span>
                                <span class="detail-value">{{ food.amount }}</span>
                            </div>
                            <div class="detail-item" v-if="food.price && food.price > 0">
                                <span class="detail-label">價格:</span>
                                <span class="detail-value price">NT$ {{ food.price }}</span>
                            </div>
                            <div class="detail-item" v-if="food.shop">
                                <span class="detail-label">商店:</span>
                                <span class="detail-value">{{ food.shop }}</span>
                            </div>
                            <div class="detail-item" v-if="food.photo">
                                <span class="detail-label">圖片:</span>
                                <div style="margin-top: 10px;">
                                    <img 
                                        :src="food.photo" 
                                        :alt="food.name"
                                        style="max-width: 120px; height: auto; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.2); cursor: pointer;"
                                        @error="handleImageError"
                                        @click="viewImage(food.photo)"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 空狀態 -->
                <div class="empty-gallery" v-else>
                    <div class="empty-icon">🍽️</div>
                    <h3>暫無食品記錄</h3>
                    <p>點擊「新增食品」開始管理您的食品庫存</p>
                </div>

                <!-- 新增/編輯食品模態框 -->
                <div class="modal-overlay" v-if="showAddFoodModal || showEditFoodModal" @click="closeFoodModals">
                    <div class="modal-content" @click.stop>
                        <div class="modal-header">
                            <h3>{{ showEditFoodModal ? '編輯食品' : '新增食品' }}</h3>
                            <button class="close-btn" @click="closeFoodModals">✕</button>
                        </div>
                        <form @submit.prevent="saveFood" class="subscription-form">
                            <div class="form-group">
                                <label>食品名稱 *</label>
                                <input type="text" v-model="currentFood.name" required>
                            </div>
                            <div class="form-group">
                                <label>到期日期 *</label>
                                <input type="date" v-model="currentFood.todate" required>
                            </div>
                            <div class="form-group">
                                <label>數量</label>
                                <input type="number" v-model="currentFood.amount" min="0" placeholder="選填">
                            </div>
                            <div class="form-group">
                                <label>價格 (NT$)</label>
                                <input type="number" v-model="currentFood.price" min="0" placeholder="選填">
                            </div>
                            <div class="form-group">
                                <label>商店</label>
                                <input type="text" v-model="currentFood.shop" placeholder="購買商店">
                            </div>
                            <div class="form-group">
                                <label>圖片網址</label>
                                <input type="url" v-model="currentFood.photo" placeholder="https://example.com/image.jpg">
                                <div v-if="currentFood.photo" style="margin-top: 10px;">
                                    <img 
                                        :src="currentFood.photo" 
                                        alt="預覽圖片"
                                        style="max-width: 100px; height: auto; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"
                                        @error="handleImageError"
                                    >
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="cancel-btn" @click="closeFoodModals">取消</button>
                                <button type="submit" class="save-btn">{{ showEditFoodModal ? '更新' : '新增' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>

            <!-- 其他頁面內容 -->
            <main class="main-content" v-else-if="currentPage !== 'home' && currentPage !== 'dashboard' && currentPage !== 'gallery' && currentPage !== 'videos' && currentPage !== 'subscriptions' && currentPage !== 'foods'">
                <h1 class="main-title">{{ getPageTitle() }}</h1>
                <p class="subtitle">{{ getPageDescription() }}</p>
                <div style="background: rgba(255,255,255,0.1); padding: 40px; border-radius: 16px; margin-top: 40px;">
                    <p>此頁面功能開發中...</p>
                    <button @click="currentPage = 'home'" style="margin-top: 20px; padding: 10px 20px; background: #ff6b6b; color: white; border: none; border-radius: 8px; cursor: pointer;">
                        返回首頁
                    </button>
                </div>
            </main>

            <!-- 版權信息 -->
            <footer class="footer">
                <p>鋒兄雲端公開訊息 版權所有 2025 ~ 2125</p>
            </footer>
        </div>
    </div>

    <script>
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    currentPage: 'home',
                    images: [],
                    filteredImages: [],
                    searchQuery: '',
                    videos: [],
                    filteredVideos: [],
                    videoSearchQuery: '',
                    subscriptions: [],
                    filteredSubscriptions: [],
                    subscriptionSearchQuery: '',
                    showAddSubscriptionModal: false,
                    showEditSubscriptionModal: false,
                    currentSubscription: {
                        name: '',
                        nextdate: '',
                        price: '',
                        site: '',
                        note: '',
                        account: '',
                        originalName: '',
                        originalNextdate: ''
                    },
                    editingIndex: -1,
                    foods: [],
                    filteredFoods: [],
                    foodSearchQuery: '',
                    showAddFoodModal: false,
                    showEditFoodModal: false,
                    currentFood: {
                        name: '',
                        todate: '',
                        amount: '',
                        photo: '',
                        price: '',
                        shop: '',
                        originalName: '',
                        originalTodate: ''
                    },
                    editingFoodIndex: -1,
                    dashboardStats: {
                        subscriptions: {
                            threeDays: 0,
                            sevenDays: 0
                        },
                        foods: {
                            sevenDays: 0,
                            thirtyDays: 0
                        }
                    },
                    showMobileMenu: false
                }
            },
            mounted() {
                this.loadImages();
                this.loadVideos();
                this.loadSubscriptions();
                this.loadFoods();
                // 初始化完成後計算儀表板統計
                setTimeout(() => {
                    this.calculateDashboardStats();
                }, 1000);
            },
            methods: {
                getPageTitle() {
                    const titles = {
                        'dashboard': '📊 儀表板',
                        'gallery': '🖼️ 圖片庫',
                        'videos': '🎬 影片庫',
                        'subscriptions': '📋 訂閱管理',
                        'foods': '🍽️ 食品管理'
                    };
                    return titles[this.currentPage] || '鋒兄AI資訊系統';
                },
                getPageDescription() {
                    const descriptions = {
                        'dashboard': '查看系統統計數據和分析報告',
                        'gallery': '管理和瀏覽您的圖片收藏',
                        'videos': '管理和播放您的影片收藏',
                        'subscriptions': '管理用戶訂閱和會員資訊',
                        'foods': '食品資料庫管理系統'
                    };
                    return descriptions[this.currentPage] || '智能管理系統';
                },
                async loadImages() {
                    try {
                        const response = await fetch('?action=getImages');
                        const data = await response.json();
                        this.images = data.images || [];
                        this.filteredImages = [...this.images];
                    } catch (error) {
                        console.error('載入圖片失敗:', error);
                        this.images = [];
                        this.filteredImages = [];
                    }
                },
                filterImages() {
                    if (!this.searchQuery.trim()) {
                        this.filteredImages = [...this.images];
                    } else {
                        this.filteredImages = this.images.filter(image => 
                            image.displayName.toLowerCase().includes(this.searchQuery.toLowerCase())
                        );
                    }
                },
                refreshImages() {
                    this.loadImages();
                    this.searchQuery = '';
                },
                viewImage(imageSrc) {
                    // 在新視窗中開啟圖片
                    if (typeof imageSrc === 'string') {
                        // 如果是字串，直接開啟
                        window.open(imageSrc, '_blank');
                    } else if (imageSrc && imageSrc.name) {
                        // 如果是圖片物件，使用 images/ 路徑
                        window.open('images/' + imageSrc.name, '_blank');
                    }
                },
                handleImageError(event) {
                    // 圖片載入失敗時顯示預設圖示
                    event.target.style.display = 'none';
                    event.target.parentElement.innerHTML = '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:48px;color:rgba(255,255,255,0.3);">🖼️</div>';
                },
                async loadVideos() {
                    try {
                        const response = await fetch('?action=getVideos');
                        const data = await response.json();
                        this.videos = data.videos || [];
                        this.filteredVideos = [...this.videos];
                    } catch (error) {
                        console.error('載入影片失敗:', error);
                        this.videos = [];
                        this.filteredVideos = [];
                    }
                },
                filterVideos() {
                    if (!this.videoSearchQuery.trim()) {
                        this.filteredVideos = [...this.videos];
                    } else {
                        this.filteredVideos = this.videos.filter(video => 
                            video.displayName.toLowerCase().includes(this.videoSearchQuery.toLowerCase())
                        );
                    }
                },
                refreshVideos() {
                    this.loadVideos();
                    this.videoSearchQuery = '';
                },
                viewVideo(video) {
                    // 在新視窗中開啟影片
                    window.open('videos/' + video.name, '_blank');
                },
                handleVideoError(event) {
                    // 影片載入失敗時顯示預設圖示
                    event.target.style.display = 'none';
                    const playOverlay = event.target.parentElement.querySelector('.play-overlay');
                    if (playOverlay) playOverlay.style.display = 'none';
                    event.target.parentElement.innerHTML = '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:48px;color:rgba(255,255,255,0.3);">🎬</div>';
                },
                // 訂閱管理方法
                async loadSubscriptions() {
                    try {
                        const formData = new FormData();
                        formData.append('action', 'getSubscriptions');
                        
                        const response = await fetch('subscriptions.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();
                        
                        if (data.success) {
                            this.subscriptions = data.data || [];
                            this.filteredSubscriptions = [...this.subscriptions];
                            this.calculateDashboardStats();
                        } else {
                            console.error('載入訂閱失敗:', data.message);
                            this.subscriptions = [];
                            this.filteredSubscriptions = [];
                        }
                    } catch (error) {
                        console.error('載入訂閱失敗:', error);
                        this.subscriptions = [];
                        this.filteredSubscriptions = [];
                    }
                },
                filterSubscriptions() {
                    if (!this.subscriptionSearchQuery.trim()) {
                        this.filteredSubscriptions = [...this.subscriptions];
                    } else {
                        this.filteredSubscriptions = this.subscriptions.filter(subscription => 
                            subscription.name.toLowerCase().includes(this.subscriptionSearchQuery.toLowerCase()) ||
                            (subscription.site && subscription.site.toLowerCase().includes(this.subscriptionSearchQuery.toLowerCase()))
                        );
                    }
                },
                editSubscription(subscription, index) {
                    this.currentSubscription = { ...subscription };
                    this.currentSubscription.originalName = subscription.name;
                    this.currentSubscription.originalNextdate = subscription.nextdate;
                    this.editingIndex = index;
                    this.showEditSubscriptionModal = true;
                },
                async deleteSubscription(index) {
                    if (!confirm('確定要刪除這個訂閱嗎？')) return;
                    
                    try {
                        const subscription = this.filteredSubscriptions[index];
                        const formData = new FormData();
                        formData.append('action', 'deleteSubscription');
                        formData.append('name', subscription.name);
                        formData.append('nextdate', subscription.nextdate);
                        
                        const response = await fetch('subscriptions.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();
                        
                        if (data.success) {
                            await this.loadSubscriptions();
                            alert('訂閱刪除成功！');
                        } else {
                            alert('刪除失敗: ' + data.message);
                        }
                    } catch (error) {
                        console.error('刪除訂閱失敗:', error);
                        alert('刪除失敗，請稍後再試');
                    }
                },
                async saveSubscription() {
                    try {
                        const formData = new FormData();
                        formData.append('action', this.showEditSubscriptionModal ? 'updateSubscription' : 'addSubscription');
                        formData.append('name', this.currentSubscription.name);
                        formData.append('nextdate', this.currentSubscription.nextdate);
                        formData.append('price', this.currentSubscription.price);
                        formData.append('site', this.currentSubscription.site);
                        formData.append('note', this.currentSubscription.note);
                        formData.append('account', this.currentSubscription.account);
                        
                        if (this.showEditSubscriptionModal) {
                            formData.append('originalName', this.currentSubscription.originalName);
                            formData.append('originalNextdate', this.currentSubscription.originalNextdate);
                        }
                        
                        const response = await fetch('subscriptions.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();
                        
                        if (data.success) {
                            await this.loadSubscriptions();
                            this.closeModals();
                            alert(this.showEditSubscriptionModal ? '訂閱更新成功！' : '訂閱新增成功！');
                        } else {
                            alert('操作失敗: ' + data.message);
                        }
                    } catch (error) {
                        console.error('保存訂閱失敗:', error);
                        alert('操作失敗，請稍後再試');
                    }
                },
                closeModals() {
                    this.showAddSubscriptionModal = false;
                    this.showEditSubscriptionModal = false;
                    this.currentSubscription = {
                        name: '',
                        nextdate: '',
                        price: '',
                        site: '',
                        note: '',
                        account: '',
                        originalName: '',
                        originalNextdate: ''
                    };
                    this.editingIndex = -1;
                },
                formatDate(dateString) {
                    if (!dateString || dateString === '0000-00-00') return '未設定';
                    
                    try {
                        // 簡化日期處理
                        let date;
                        
                        if (typeof dateString === 'string' && dateString.match(/^\d{4}-\d{2}-\d{2}$/)) {
                            // YYYY-MM-DD 格式，直接分割避免時區問題
                            const parts = dateString.split('-');
                            date = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                        } else {
                            date = new Date(dateString);
                        }
                        
                        // 檢查日期是否有效
                        if (isNaN(date.getTime())) {
                            console.warn('Invalid date:', dateString);
                            return '日期格式錯誤';
                        }
                        
                        // 檢查年份是否合理
                        const year = date.getFullYear();
                        if (year < 1900 || year > 2100) {
                            console.warn('Suspicious year:', year, 'from:', dateString);
                            return '日期錯誤';
                        }
                        
                        return date.toLocaleDateString('zh-TW');
                    } catch (error) {
                        console.error('Date formatting error:', error, 'for:', dateString);
                        return '日期錯誤';
                    }
                },
                isExpired(dateString) {
                    if (!dateString || dateString === '0000-00-00') return false;
                    
                    let date;
                    if (typeof dateString === 'string' && dateString.match(/^\d{4}-\d{2}-\d{2}$/)) {
                        date = new Date(dateString + 'T00:00:00');
                    } else {
                        date = new Date(dateString);
                    }
                    
                    if (isNaN(date.getTime()) || date.getFullYear() === 1970) return false;
                    
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    return date < today;
                },
                isExpiringSoon(dateString) {
                    if (!dateString || dateString === '0000-00-00') return false;
                    
                    let date;
                    if (typeof dateString === 'string' && dateString.match(/^\d{4}-\d{2}-\d{2}$/)) {
                        date = new Date(dateString + 'T00:00:00');
                    } else {
                        date = new Date(dateString);
                    }
                    
                    if (isNaN(date.getTime()) || date.getFullYear() === 1970) return false;
                    
                    const today = new Date();
                    const sevenDaysLater = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000);
                    today.setHours(0, 0, 0, 0);
                    return date >= today && date <= sevenDaysLater;
                },
                getDateClass(dateString) {
                    if (this.isExpired(dateString)) return 'expired';
                    if (this.isExpiringSoon(dateString)) return 'expiring-soon';
                    return 'normal';
                },
                // 食品管理方法
                async loadFoods() {
                    try {
                        const formData = new FormData();
                        formData.append('action', 'getFoods');
                        
                        const response = await fetch('foods.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();
                        
                        if (data.success) {
                            this.foods = data.data || [];
                            this.filteredFoods = [...this.foods];
                            this.calculateDashboardStats();
                            
                            // 調試：檢查載入的資料
                            console.log('載入的食品資料:', this.foods);
                            this.foods.forEach((food, index) => {
                                console.log(`食品 ${index}:`, {
                                    name: food.name,
                                    todate: food.todate,
                                    todateType: typeof food.todate,
                                    amount: food.amount,
                                    price: food.price
                                });
                            });
                        } else {
                            console.error('載入食品失敗:', data.message);
                            this.foods = [];
                            this.filteredFoods = [];
                        }
                    } catch (error) {
                        console.error('載入食品失敗:', error);
                        this.foods = [];
                        this.filteredFoods = [];
                    }
                },
                filterFoods() {
                    if (!this.foodSearchQuery.trim()) {
                        this.filteredFoods = [...this.foods];
                    } else {
                        this.filteredFoods = this.foods.filter(food => 
                            food.name.toLowerCase().includes(this.foodSearchQuery.toLowerCase()) ||
                            (food.shop && food.shop.toLowerCase().includes(this.foodSearchQuery.toLowerCase()))
                        );
                    }
                },
                editFood(food, index) {
                    console.log('編輯食品 - 原始資料:', food);
                    this.currentFood = { ...food };
                    this.currentFood.originalName = food.name;
                    this.currentFood.originalTodate = food.todate;
                    this.editingFoodIndex = index;
                    console.log('編輯食品 - 複製後的資料:', this.currentFood);
                    this.showEditFoodModal = true;
                },
                async deleteFood(index) {
                    if (!confirm('確定要刪除這個食品嗎？')) return;
                    
                    try {
                        const food = this.filteredFoods[index];
                        const formData = new FormData();
                        formData.append('action', 'deleteFood');
                        formData.append('name', food.name);
                        formData.append('todate', food.todate);
                        
                        const response = await fetch('foods.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();
                        
                        if (data.success) {
                            await this.loadFoods();
                            alert('食品刪除成功！');
                        } else {
                            alert('刪除失敗: ' + data.message);
                        }
                    } catch (error) {
                        console.error('刪除食品失敗:', error);
                        alert('刪除失敗，請稍後再試');
                    }
                },
                async saveFood() {
                    try {
                        // 調試：檢查要傳送的資料
                        console.log('SaveFood - 當前食品資料:', this.currentFood);
                        console.log('SaveFood - 日期值:', this.currentFood.todate, '類型:', typeof this.currentFood.todate);
                        
                        const formData = new FormData();
                        formData.append('action', this.showEditFoodModal ? 'updateFood' : 'addFood');
                        formData.append('name', this.currentFood.name);
                        formData.append('todate', this.currentFood.todate);
                        formData.append('amount', this.currentFood.amount);
                        formData.append('photo', this.currentFood.photo);
                        formData.append('price', this.currentFood.price);
                        formData.append('shop', this.currentFood.shop);
                        
                        // 調試：檢查 FormData 內容
                        for (let [key, value] of formData.entries()) {
                            console.log('FormData:', key, '=', value);
                        }
                        
                        if (this.showEditFoodModal) {
                            formData.append('originalName', this.currentFood.originalName);
                            formData.append('originalTodate', this.currentFood.originalTodate);
                        }
                        
                        const response = await fetch('foods.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();
                        
                        if (data.success) {
                            await this.loadFoods();
                            this.closeFoodModals();
                            alert(this.showEditFoodModal ? '食品更新成功！' : '食品新增成功！');
                        } else {
                            alert('操作失敗: ' + data.message);
                        }
                    } catch (error) {
                        console.error('保存食品失敗:', error);
                        alert('操作失敗，請稍後再試');
                    }
                },
                closeFoodModals() {
                    this.showAddFoodModal = false;
                    this.showEditFoodModal = false;
                    this.currentFood = {
                        name: '',
                        todate: '',
                        amount: '',
                        photo: '',
                        price: '',
                        shop: '',
                        originalName: '',
                        originalTodate: ''
                    };
                    this.editingFoodIndex = -1;
                },
                // 儀表板統計方法
                calculateDashboardStats() {
                    // 計算訂閱統計
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    const threeDaysLater = new Date(today.getTime() + 3 * 24 * 60 * 60 * 1000);
                    const sevenDaysLater = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000);
                    const thirtyDaysLater = new Date(today.getTime() + 30 * 24 * 60 * 60 * 1000);
                    
                    // 訂閱統計
                    let subscriptionThreeDays = 0;
                    let subscriptionSevenDays = 0;
                    
                    this.subscriptions.forEach(subscription => {
                        if (!subscription.nextdate || subscription.nextdate === '0000-00-00') return;
                        
                        let date;
                        if (typeof subscription.nextdate === 'string' && subscription.nextdate.match(/^\d{4}-\d{2}-\d{2}$/)) {
                            date = new Date(subscription.nextdate + 'T00:00:00');
                        } else {
                            date = new Date(subscription.nextdate);
                        }
                        
                        if (isNaN(date.getTime()) || date.getFullYear() === 1970) return;
                        
                        if (date >= today && date <= threeDaysLater) {
                            subscriptionThreeDays++;
                        }
                        if (date >= today && date <= sevenDaysLater) {
                            subscriptionSevenDays++;
                        }
                    });
                    
                    // 食品統計
                    let foodSevenDays = 0;
                    let foodThirtyDays = 0;
                    
                    this.foods.forEach(food => {
                        if (!food.todate || food.todate === '0000-00-00') return;
                        
                        let date;
                        if (typeof food.todate === 'string' && food.todate.match(/^\d{4}-\d{2}-\d{2}$/)) {
                            date = new Date(food.todate + 'T00:00:00');
                        } else {
                            date = new Date(food.todate);
                        }
                        
                        if (isNaN(date.getTime()) || date.getFullYear() === 1970) return;
                        
                        if (date >= today && date <= sevenDaysLater) {
                            foodSevenDays++;
                        }
                        if (date >= today && date <= thirtyDaysLater) {
                            foodThirtyDays++;
                        }
                    });
                    
                    // 更新統計數據
                    this.dashboardStats = {
                        subscriptions: {
                            threeDays: subscriptionThreeDays,
                            sevenDays: subscriptionSevenDays
                        },
                        foods: {
                            sevenDays: foodSevenDays,
                            thirtyDays: foodThirtyDays
                        }
                    };
                    
                    console.log('儀表板統計更新:', this.dashboardStats);
                }
            },
            watch: {
                currentPage(newPage) {
                    if (newPage === 'gallery') {
                        this.loadImages();
                    } else if (newPage === 'videos') {
                        this.loadVideos();
                    } else if (newPage === 'subscriptions') {
                        this.loadSubscriptions();
                    } else if (newPage === 'foods') {
                        this.loadFoods();
                    } else if (newPage === 'dashboard') {
                        this.calculateDashboardStats();
                    }
                }
            }
        }).mount('#app');
    </script>
</body>
</html>