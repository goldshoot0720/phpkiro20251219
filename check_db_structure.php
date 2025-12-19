<?php
// 檢查資料庫結構
require_once 'config.php';

try {
    $pdo = getDatabase();
    
    echo "<h2>資料庫結構檢查</h2>";
    
    // 檢查 food 表結構
    echo "<h3>Food 表結構:</h3>";
    $stmt = $pdo->query("DESCRIBE food");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th style='padding: 10px;'>欄位名稱</th>";
    echo "<th style='padding: 10px;'>資料類型</th>";
    echo "<th style='padding: 10px;'>允許NULL</th>";
    echo "<th style='padding: 10px;'>預設值</th>";
    echo "<th style='padding: 10px;'>額外資訊</th>";
    echo "</tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td style='padding: 10px; color: " . ($column['Null'] === 'YES' ? 'green' : 'red') . ";'>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($column['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 檢查 subscription 表結構
    echo "<h3>Subscription 表結構:</h3>";
    $stmt = $pdo->query("DESCRIBE subscription");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th style='padding: 10px;'>欄位名稱</th>";
    echo "<th style='padding: 10px;'>資料類型</th>";
    echo "<th style='padding: 10px;'>允許NULL</th>";
    echo "<th style='padding: 10px;'>預設值</th>";
    echo "<th style='padding: 10px;'>額外資訊</th>";
    echo "</tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td style='padding: 10px; color: " . ($column['Null'] === 'YES' ? 'green' : 'red') . ";'>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($column['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 測試插入
    echo "<h3>測試資料插入:</h3>";
    echo "<button onclick='testInsert()'>測試新增食品</button>";
    echo "<div id='testResult'></div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>錯誤: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<script>
async function testInsert() {
    const resultDiv = document.getElementById('testResult');
    resultDiv.innerHTML = '<p>測試中...</p>';
    
    try {
        const formData = new FormData();
        formData.append('action', 'addFood');
        formData.append('name', '測試食品');
        formData.append('todate', '2025-12-31');
        formData.append('amount', '');
        formData.append('photo', '');
        formData.append('price', '');
        formData.append('shop', '測試商店');
        formData.append('photohash', '');
        
        const response = await fetch('foods_api.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            resultDiv.innerHTML = '<p style="color: green;">✅ 測試成功: ' + data.message + '</p>';
        } else {
            resultDiv.innerHTML = '<p style="color: red;">❌ 測試失敗: ' + data.message + '</p>';
        }
    } catch (error) {
        resultDiv.innerHTML = '<p style="color: red;">❌ 測試錯誤: ' + error.message + '</p>';
    }
}
</script>

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
button {
    background: #4CAF50;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}
button:hover {
    background: #45a049;
}
</style>