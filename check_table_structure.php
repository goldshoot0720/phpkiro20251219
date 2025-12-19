<?php
// 檢查資料表結構
require_once 'config.php';

try {
    $pdo = getDatabase();
    
    echo "<h2>Food 表結構檢查</h2>";
    
    // 檢查表結構
    $stmt = $pdo->query("DESCRIBE food");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>表結構:</h3>";
    echo "<table border='1' style='border-collapse: collapse; margin: 20px 0; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th style='padding: 10px;'>欄位名稱</th>";
    echo "<th style='padding: 10px;'>資料類型</th>";
    echo "<th style='padding: 10px;'>允許NULL</th>";
    echo "<th style='padding: 10px;'>預設值</th>";
    echo "<th style='padding: 10px;'>Key</th>";
    echo "<th style='padding: 10px;'>Extra</th>";
    echo "</tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td style='padding: 10px;'><strong>" . htmlspecialchars($column['Field']) . "</strong></td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td style='padding: 10px; color: " . ($column['Null'] === 'YES' ? 'green' : 'red') . ";'>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($column['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 測試插入語句
    echo "<h3>測試插入語句:</h3>";
    $testName = 'TEST_' . date('His');
    $testDate = '2025-12-20';
    $testAmount = 999;
    $testPhoto = 'test.jpg';
    $testPrice = 888;
    $testShop = 'TestShop';
    $testPhotohash = 'testhash';
    
    echo "<p><strong>準備插入的資料:</strong></p>";
    echo "<ul>";
    echo "<li>name: $testName</li>";
    echo "<li>todate: $testDate</li>";
    echo "<li>amount: $testAmount</li>";
    echo "<li>photo: $testPhoto</li>";
    echo "<li>price: $testPrice</li>";
    echo "<li>shop: $testShop</li>";
    echo "<li>photohash: $testPhotohash</li>";
    echo "</ul>";
    
    $stmt = $pdo->prepare("INSERT INTO food (name, todate, amount, photo, price, shop, photohash) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([$testName, $testDate, $testAmount, $testPhoto, $testPrice, $testShop, $testPhotohash]);
    
    if ($result) {
        echo "<p style='color: green;'>✅ 測試插入成功</p>";
        
        // 查詢剛插入的資料
        $stmt = $pdo->prepare("SELECT * FROM food WHERE name = ?");
        $stmt->execute([$testName]);
        $inserted = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<h4>插入後查詢結果:</h4>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'>";
        foreach ($inserted as $key => $value) {
            echo "<th style='padding: 8px;'>$key</th>";
        }
        echo "</tr>";
        echo "<tr>";
        foreach ($inserted as $key => $value) {
            echo "<td style='padding: 8px;'>" . htmlspecialchars($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
        echo "</table>";
        
        // 刪除測試資料
        $stmt = $pdo->prepare("DELETE FROM food WHERE name = ?");
        $stmt->execute([$testName]);
        echo "<p><em>測試資料已清除</em></p>";
        
    } else {
        echo "<p style='color: red;'>❌ 測試插入失敗</p>";
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