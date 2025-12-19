<?php
// 測試食品插入
require_once 'config.php';

if ($_POST) {
    try {
        $pdo = getDatabase();
        
        $name = $_POST['name'] ?? '';
        $todate = $_POST['todate'] ?? '';
        $amount = $_POST['amount'] ?? null;
        $amount = ($amount === '' || $amount === null) ? null : (int)$amount;
        $photo = $_POST['photo'] ?? '';
        $price = $_POST['price'] ?? 0;
        $price = ($price === '' || $price === null) ? 0 : (int)$price;
        $shop = $_POST['shop'] ?? '';
        $photohash = '';
        
        echo "<h3>接收到的資料:</h3>";
        echo "<pre>";
        echo "name: " . var_export($name, true) . "\n";
        echo "todate: " . var_export($todate, true) . "\n";
        echo "amount: " . var_export($amount, true) . "\n";
        echo "photo: " . var_export($photo, true) . "\n";
        echo "price: " . var_export($price, true) . "\n";
        echo "shop: " . var_export($shop, true) . "\n";
        echo "</pre>";
        
        $stmt = $pdo->prepare("INSERT INTO food (name, todate, amount, photo, price, shop, photohash) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([$name, $todate, $amount, $photo, $price, $shop, $photohash]);
        
        if ($result) {
            echo "<p style='color: green;'>✅ 插入成功！</p>";
            
            // 查詢剛插入的資料
            $stmt = $pdo->prepare("SELECT * FROM food WHERE name = ? AND todate = ? ORDER BY name DESC LIMIT 1");
            $stmt->execute([$name, $todate]);
            $inserted = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "<h3>插入後的資料:</h3>";
            echo "<pre>";
            print_r($inserted);
            echo "</pre>";
        } else {
            echo "<p style='color: red;'>❌ 插入失敗</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>錯誤: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>測試食品插入</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { background: #f5f5f5; padding: 20px; border-radius: 8px; }
        label { display: block; margin: 10px 0 5px 0; font-weight: bold; }
        input { width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-top: 15px; }
        button:hover { background: #45a049; }
        pre { background: white; padding: 15px; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>
    <h2>測試食品插入</h2>
    
    <form method="POST">
        <label>食品名稱:</label>
        <input type="text" name="name" value="測試食品" required>
        
        <label>到期日期:</label>
        <input type="date" name="todate" value="2025-12-19" required>
        
        <label>數量:</label>
        <input type="number" name="amount" value="10">
        
        <label>價格:</label>
        <input type="number" name="price" value="100">
        
        <label>商店:</label>
        <input type="text" name="shop" value="測試商店">
        
        <label>圖片:</label>
        <input type="text" name="photo" value="test.jpg">
        
        <button type="submit">測試插入</button>
    </form>
    
    <h3>現有食品資料:</h3>
    <?php
    try {
        $pdo = getDatabase();
        $stmt = $pdo->query("SELECT * FROM food ORDER BY todate DESC LIMIT 5");
        $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th style='padding: 8px;'>名稱</th>";
        echo "<th style='padding: 8px;'>到期日期</th>";
        echo "<th style='padding: 8px;'>數量</th>";
        echo "<th style='padding: 8px;'>價格</th>";
        echo "<th style='padding: 8px;'>商店</th>";
        echo "<th style='padding: 8px;'>圖片</th>";
        echo "</tr>";
        
        foreach ($foods as $food) {
            echo "<tr>";
            echo "<td style='padding: 8px;'>" . htmlspecialchars($food['name']) . "</td>";
            echo "<td style='padding: 8px;'>" . htmlspecialchars($food['todate']) . "</td>";
            echo "<td style='padding: 8px;'>" . ($food['amount'] ?? 'NULL') . "</td>";
            echo "<td style='padding: 8px;'>" . ($food['price'] ?? 'NULL') . "</td>";
            echo "<td style='padding: 8px;'>" . htmlspecialchars($food['shop']) . "</td>";
            echo "<td style='padding: 8px;'>" . htmlspecialchars($food['photo']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>查詢錯誤: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    ?>
</body>
</html>