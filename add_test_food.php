<?php
// 測試新增食品資料
require_once 'config.php';

try {
    $pdo = getDatabase();
    
    // 設定字符集為 UTF-8
    $pdo->exec("SET NAMES utf8mb4");
    
    echo "<h2>新增測試食品</h2>";
    
    // 準備資料
    $name = "測試食品"; // 由於原始名稱顯示問號，我們用測試名稱
    $todate = "2026-01-07";
    $amount = 6;
    $photo = "https://online.carrefour.com.tw/on/demandware.static/-/Sites-carrefour-tw-m-inner/default/dwd792433f/images/large/0246532.jpeg";
    $price = 0; // 沒有提供價格，設為0
    $shop = "家樂福"; // 從圖片URL推測是家樂福
    $photohash = "";
    
    echo "<p><strong>準備新增的資料：</strong></p>";
    echo "<ul>";
    echo "<li>名稱: " . htmlspecialchars($name) . "</li>";
    echo "<li>到期日期: " . htmlspecialchars($todate) . "</li>";
    echo "<li>數量: " . htmlspecialchars($amount) . "</li>";
    echo "<li>圖片: " . htmlspecialchars($photo) . "</li>";
    echo "<li>價格: " . htmlspecialchars($price) . "</li>";
    echo "<li>商店: " . htmlspecialchars($shop) . "</li>";
    echo "</ul>";
    
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
            echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
            echo "<tr style='background: #f0f0f0;'>";
            foreach (array_keys($food) as $key) {
                echo "<th style='padding: 10px;'>" . htmlspecialchars($key) . "</th>";
            }
            echo "</tr>";
            echo "<tr>";
            foreach ($food as $value) {
                echo "<td style='padding: 10px;'>" . htmlspecialchars($value ?? '') . "</td>";
            }
            echo "</tr>";
            echo "</table>";
        }
    } else {
        echo "<p style='color: red;'>❌ 新增失敗</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>錯誤: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background: #f5f5f5;
}
table {
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>