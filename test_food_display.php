<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>食品展示測試</title>
    <style>
        body {
            font-family: 'Microsoft JhengHei', sans-serif;
            margin: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .food-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .food-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 20px;
            border-left: 4px solid #96ceb4;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .food-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        .food-card.expired {
            border-left-color: #ff6b6b;
        }
        .food-card.expiring-soon {
            border-left-color: #ffa726;
        }
        .food-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #FFD700;
        }
        .food-image {
            width: 100%;
            max-width: 200px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            cursor: pointer;
            transition: transform 0.3s;
        }
        .food-image:hover {
            transform: scale(1.05);
        }
        .food-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 14px;
        }
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        .detail-label {
            opacity: 0.8;
            font-size: 12px;
            margin-bottom: 2px;
        }
        .detail-value {
            font-weight: bold;
        }
        .image-error {
            width: 100%;
            height: 150px;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: rgba(255,255,255,0.3);
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="text-align: center; margin-bottom: 30px;">🍽️ 食品管理系統 - 圖片展示測試</h1>
        
        <?php
        require_once 'config.php';
        
        // 設定本地環境
        $_SERVER['HTTP_HOST'] = 'localhost';
        
        try {
            $pdo = getDatabase();
            $pdo->exec("SET NAMES utf8mb4");
            
            $stmt = $pdo->query("SELECT * FROM food ORDER BY todate ASC");
            $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($foods)) {
                echo "<p style='text-align: center; font-size: 18px; opacity: 0.8;'>暫無食品資料</p>";
            } else {
                echo "<div class='food-grid'>";
                
                foreach ($foods as $food) {
                    $todate = strtotime($food['todate']);
                    $today = time();
                    $sevenDaysLater = $today + (7 * 24 * 60 * 60);
                    
                    $cardClass = 'food-card';
                    if ($todate < $today) {
                        $cardClass .= ' expired';
                    } elseif ($todate <= $sevenDaysLater) {
                        $cardClass .= ' expiring-soon';
                    }
                    
                    echo "<div class='$cardClass'>";
                    echo "<div class='food-name'>" . htmlspecialchars($food['name']) . "</div>";
                    
                    // 顯示圖片
                    if (!empty($food['photo'])) {
                        echo "<img src='" . htmlspecialchars($food['photo']) . "' alt='" . htmlspecialchars($food['name']) . "' class='food-image' onclick='window.open(\"" . htmlspecialchars($food['photo']) . "\", \"_blank\")' onerror='this.style.display=\"none\"; this.nextElementSibling.style.display=\"flex\";'>";
                        echo "<div class='image-error' style='display: none;'>🖼️</div>";
                    } else {
                        echo "<div class='image-error'>📷</div>";
                    }
                    
                    echo "<div class='food-details'>";
                    echo "<div class='detail-item'>";
                    echo "<span class='detail-label'>到期日期:</span>";
                    echo "<span class='detail-value'>" . htmlspecialchars($food['todate']) . "</span>";
                    echo "</div>";
                    
                    if (!empty($food['amount'])) {
                        echo "<div class='detail-item'>";
                        echo "<span class='detail-label'>數量:</span>";
                        echo "<span class='detail-value'>" . htmlspecialchars($food['amount']) . "</span>";
                        echo "</div>";
                    }
                    
                    if (!empty($food['price']) && $food['price'] > 0) {
                        echo "<div class='detail-item'>";
                        echo "<span class='detail-label'>價格:</span>";
                        echo "<span class='detail-value'>NT$ " . htmlspecialchars($food['price']) . "</span>";
                        echo "</div>";
                    }
                    
                    if (!empty($food['shop'])) {
                        echo "<div class='detail-item'>";
                        echo "<span class='detail-label'>商店:</span>";
                        echo "<span class='detail-value'>" . htmlspecialchars($food['shop']) . "</span>";
                        echo "</div>";
                    }
                    
                    echo "</div>"; // food-details
                    echo "</div>"; // food-card
                }
                
                echo "</div>"; // food-grid
            }
            
        } catch (Exception $e) {
            echo "<p style='color: #ff6b6b; text-align: center;'>錯誤: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="index.php" style="background: rgba(255,255,255,0.2); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-block;">
                返回主系統
            </a>
        </div>
    </div>
</body>
</html>