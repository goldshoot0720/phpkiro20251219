<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>訂閱管理測試</title>
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
        .subscription-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .subscription-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 20px;
            border-left: 4px solid #45b7d1;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .subscription-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        .subscription-card.expired {
            border-left-color: #ff6b6b;
        }
        .subscription-card.expiring-soon {
            border-left-color: #ffa726;
        }
        .subscription-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #FFD700;
        }
        .subscription-details {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            font-size: 14px;
        }
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        .detail-label {
            opacity: 0.8;
            font-size: 12px;
            margin-bottom: 4px;
        }
        .detail-value {
            font-weight: bold;
            font-size: 16px;
        }
        .detail-value.price {
            color: #4ecdc4;
            font-size: 18px;
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
            word-break: break-all;
        }
        .detail-value a:hover {
            color: #45b7d1;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="text-align: center; margin-bottom: 30px;">📋 訂閱管理系統 - 連結測試</h1>
        
        <?php
        require_once 'config.php';
        
        // 設定本地環境
        $_SERVER['HTTP_HOST'] = 'localhost';
        
        try {
            $pdo = getDatabase();
            $pdo->exec("SET NAMES utf8mb4");
            
            $stmt = $pdo->query("SELECT * FROM subscription ORDER BY nextdate ASC");
            $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($subscriptions)) {
                echo "<p style='text-align: center; font-size: 18px; opacity: 0.8;'>暫無訂閱資料</p>";
            } else {
                echo "<div class='subscription-grid'>";
                
                foreach ($subscriptions as $sub) {
                    $nextdate = strtotime($sub['nextdate']);
                    $today = time();
                    $sevenDaysLater = $today + (7 * 24 * 60 * 60);
                    
                    $cardClass = 'subscription-card';
                    $dateClass = 'normal';
                    if ($nextdate < $today) {
                        $cardClass .= ' expired';
                        $dateClass = 'expired';
                    } elseif ($nextdate <= $sevenDaysLater) {
                        $cardClass .= ' expiring-soon';
                        $dateClass = 'expiring-soon';
                    }
                    
                    echo "<div class='$cardClass'>";
                    echo "<div class='subscription-name'>" . htmlspecialchars($sub['name']) . "</div>";
                    
                    echo "<div class='subscription-details'>";
                    
                    // 下次付款日期
                    echo "<div class='detail-item'>";
                    echo "<span class='detail-label'>下次付款日期:</span>";
                    echo "<span class='detail-value $dateClass'>" . htmlspecialchars($sub['nextdate']) . "</span>";
                    echo "</div>";
                    
                    // 價格
                    if (!empty($sub['price']) && $sub['price'] > 0) {
                        echo "<div class='detail-item'>";
                        echo "<span class='detail-label'>價格:</span>";
                        echo "<span class='detail-value price'>NT$ " . htmlspecialchars($sub['price']) . "</span>";
                        echo "</div>";
                    }
                    
                    // 網站 (可點擊連結)
                    if (!empty($sub['site'])) {
                        echo "<div class='detail-item'>";
                        echo "<span class='detail-label'>網站:</span>";
                        echo "<span class='detail-value'>";
                        echo "<a href='" . htmlspecialchars($sub['site']) . "' target='_blank'>" . htmlspecialchars($sub['site']) . "</a>";
                        echo "</span>";
                        echo "</div>";
                    }
                    
                    // 帳號
                    if (!empty($sub['account'])) {
                        echo "<div class='detail-item'>";
                        echo "<span class='detail-label'>帳號:</span>";
                        echo "<span class='detail-value'>" . htmlspecialchars($sub['account']) . "</span>";
                        echo "</div>";
                    }
                    
                    // 備註
                    if (!empty($sub['note'])) {
                        echo "<div class='detail-item'>";
                        echo "<span class='detail-label'>備註:</span>";
                        echo "<span class='detail-value'>" . htmlspecialchars($sub['note']) . "</span>";
                        echo "</div>";
                    }
                    
                    echo "</div>"; // subscription-details
                    echo "</div>"; // subscription-card
                }
                
                echo "</div>"; // subscription-grid
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