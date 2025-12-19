<?php
// 新增張君雅食品的測試腳本
require_once 'config.php';

// 設定本地環境變數，強制使用本地資料庫
$_SERVER['HTTP_HOST'] = 'localhost';

try {
    $pdo = getDatabase();
    
    // 設定字符集為 UTF-8
    $pdo->exec("SET NAMES utf8mb4");
    
    echo "<h2>新增【張君雅】日式串燒休閒丸子</h2>";
    
    // 準備資料
    $name = "【張君雅】日式串燒休閒丸子";
    $todate = "2026-01-07";
    $amount = 6;
    $photo = "https://online.carrefour.com.tw/on/demandware.static/-/Sites-carrefour-tw-m-inner/default/dwd792433f/images/large/0246532.jpeg";
    $price = 0; // 沒有提供價格，設為0
    $shop = "家樂福"; // 從圖片URL推測是家樂福
    $photohash = "";
    
    echo "<div style='background: #f0f0f0; padding: 15px; margin: 15px 0; border-radius: 8px;'>";
    echo "<h3>準備新增的資料：</h3>";
    echo "<p><strong>名稱:</strong> " . htmlspecialchars($name) . "</p>";
    echo "<p><strong>到期日期:</strong> " . htmlspecialchars($todate) . "</p>";
    echo "<p><strong>數量:</strong> " . htmlspecialchars($amount) . "</p>";
    echo "<p><strong>圖片:</strong> " . htmlspecialchars($photo) . "</p>";
    echo "<p><strong>價格:</strong> NT$ " . htmlspecialchars($price) . "</p>";
    echo "<p><strong>商店:</strong> " . htmlspecialchars($shop) . "</p>";
    echo "</div>";
    
    // 檢查是否已存在相同的食品
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM food WHERE name = ? AND todate = ?");
    $checkStmt->execute([$name, $todate]);
    $exists = $checkStmt->fetchColumn();
    
    if ($exists > 0) {
        echo "<p style='color: orange;'>⚠️ 相同的食品已存在，跳過新增</p>";
    } else {
        // 執行插入
        $stmt = $pdo->prepare("INSERT INTO food (name, todate, amount, photo, price, shop, photohash) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([$name, $todate, $amount, $photo, $price, $shop, $photohash]);
        
        if ($result) {
            echo "<p style='color: green;'>✅ 食品新增成功！</p>";
            
            // 查詢剛新增的資料
            echo "<h3>新增的資料：</h3>";
            $stmt = $pdo->prepare("SELECT * FROM food WHERE name = ? AND todate = ?");
            $stmt->execute([$name, $todate]);
            $food = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($food) {
                echo "<div style='background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
                echo "<h4>" . htmlspecialchars($food['name']) . "</h4>";
                echo "<p><strong>到期日期:</strong> " . htmlspecialchars($food['todate']) . "</p>";
                echo "<p><strong>數量:</strong> " . htmlspecialchars($food['amount']) . "</p>";
                echo "<p><strong>價格:</strong> NT$ " . htmlspecialchars($food['price']) . "</p>";
                echo "<p><strong>商店:</strong> " . htmlspecialchars($food['shop']) . "</p>";
                
                if ($food['photo']) {
                    echo "<div style='margin: 15px 0;'>";
                    echo "<p><strong>圖片:</strong></p>";
                    echo "<img src='" . htmlspecialchars($food['photo']) . "' alt='" . htmlspecialchars($food['name']) . "' style='max-width: 300px; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);' onerror='this.style.display=\"none\"; this.nextElementSibling.style.display=\"block\";'>";
                    echo "<div style='display: none; padding: 20px; background: #f0f0f0; border-radius: 8px; text-align: center;'>圖片載入失敗</div>";
                    echo "</div>";
                }
                echo "</div>";
            }
        } else {
            echo "<p style='color: red;'>❌ 新增失敗</p>";
        }
    }
    
    // 顯示所有食品
    echo "<h3>目前所有食品：</h3>";
    $stmt = $pdo->query("SELECT * FROM food ORDER BY todate ASC");
    $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div style='display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;'>";
    foreach ($foods as $food) {
        $isExpired = strtotime($food['todate']) < time();
        $isExpiringSoon = strtotime($food['todate']) < strtotime('+7 days');
        
        $borderColor = '#96ceb4';
        if ($isExpired) $borderColor = '#ff6b6b';
        elseif ($isExpiringSoon) $borderColor = '#ffa726';
        
        echo "<div style='background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-left: 4px solid $borderColor;'>";
        echo "<h4 style='margin: 0 0 10px 0; color: #333;'>" . htmlspecialchars($food['name']) . "</h4>";
        echo "<p style='margin: 5px 0; color: #666;'><strong>到期:</strong> " . htmlspecialchars($food['todate']) . "</p>";
        echo "<p style='margin: 5px 0; color: #666;'><strong>數量:</strong> " . htmlspecialchars($food['amount']) . "</p>";
        echo "<p style='margin: 5px 0; color: #666;'><strong>商店:</strong> " . htmlspecialchars($food['shop']) . "</p>";
        
        if ($food['photo']) {
            echo "<div style='margin: 10px 0;'>";
            echo "<img src='" . htmlspecialchars($food['photo']) . "' alt='" . htmlspecialchars($food['name']) . "' style='width: 100%; max-width: 250px; height: auto; border-radius: 4px;' onerror='this.style.display=\"none\"; this.nextElementSibling.style.display=\"block\";'>";
            echo "<div style='display: none; padding: 10px; background: #f0f0f0; border-radius: 4px; text-align: center; font-size: 12px;'>圖片載入失敗</div>";
            echo "</div>";
        }
        echo "</div>";
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>錯誤: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<style>
body {
    font-family: 'Microsoft JhengHei', sans-serif;
    margin: 20px;
    background: #f5f5f5;
    color: #333;
}
h2, h3, h4 {
    color: #333;
}
</style>