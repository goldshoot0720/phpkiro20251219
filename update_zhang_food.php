<?php
// 更新張君雅食品資料
require_once 'config.php';

// 設定本地環境變數
$_SERVER['HTTP_HOST'] = 'localhost';

try {
    $pdo = getDatabase();
    $pdo->exec("SET NAMES utf8mb4");
    
    echo "<h2>更新【張君雅】日式串燒休閒丸子資料</h2>";
    
    // 更新資料
    $name = "【張君雅】日式串燒休閒丸子";
    $todate = "2026-01-07";
    $amount = 6;
    $shop = "家樂福";
    
    $stmt = $pdo->prepare("UPDATE food SET amount = ?, shop = ? WHERE name = ? AND todate = ?");
    $result = $stmt->execute([$amount, $shop, $name, $todate]);
    
    if ($result) {
        echo "<p style='color: green;'>✅ 資料更新成功！</p>";
        
        // 查詢更新後的資料
        $stmt = $pdo->prepare("SELECT * FROM food WHERE name = ? AND todate = ?");
        $stmt->execute([$name, $todate]);
        $food = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($food) {
            echo "<div style='background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 400px;'>";
            echo "<h3>" . htmlspecialchars($food['name']) . "</h3>";
            echo "<p><strong>到期日期:</strong> " . htmlspecialchars($food['todate']) . "</p>";
            echo "<p><strong>數量:</strong> " . htmlspecialchars($food['amount']) . "</p>";
            echo "<p><strong>商店:</strong> " . htmlspecialchars($food['shop']) . "</p>";
            
            if ($food['photo']) {
                echo "<div style='margin: 15px 0;'>";
                echo "<p><strong>圖片:</strong></p>";
                echo "<img src='" . htmlspecialchars($food['photo']) . "' alt='" . htmlspecialchars($food['name']) . "' style='max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);' onerror='this.style.display=\"none\"; this.nextElementSibling.style.display=\"block\";'>";
                echo "<div style='display: none; padding: 20px; background: #f0f0f0; border-radius: 8px; text-align: center;'>圖片載入失敗</div>";
                echo "</div>";
            }
            echo "</div>";
        }
    } else {
        echo "<p style='color: red;'>❌ 更新失敗</p>";
    }
    
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
</style>