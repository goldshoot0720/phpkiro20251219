<?php
// 調試食品資料
require_once 'config.php';

try {
    $pdo = getDatabase();
    
    echo "<h2>食品資料調試</h2>";
    
    // 查詢所有食品資料
    $stmt = $pdo->query("SELECT * FROM food ORDER BY todate ASC LIMIT 10");
    $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>資料庫中的食品記錄 (前10筆):</h3>";
    
    if (count($foods) > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 20px 0; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th style='padding: 10px;'>名稱</th>";
        echo "<th style='padding: 10px;'>到期日期 (原始)</th>";
        echo "<th style='padding: 10px;'>到期日期 (格式化)</th>";
        echo "<th style='padding: 10px;'>數量</th>";
        echo "<th style='padding: 10px;'>價格</th>";
        echo "<th style='padding: 10px;'>商店</th>";
        echo "<th style='padding: 10px;'>圖片</th>";
        echo "</tr>";
        
        foreach ($foods as $food) {
            echo "<tr>";
            echo "<td style='padding: 10px;'>" . htmlspecialchars($food['name']) . "</td>";
            echo "<td style='padding: 10px; font-family: monospace;'>" . htmlspecialchars($food['todate']) . "</td>";
            
            // 格式化日期
            $formattedDate = '錯誤';
            if ($food['todate'] && $food['todate'] !== '0000-00-00') {
                $date = new DateTime($food['todate']);
                $formattedDate = $date->format('Y/m/d');
            }
            echo "<td style='padding: 10px;'>" . $formattedDate . "</td>";
            
            echo "<td style='padding: 10px;'>" . ($food['amount'] ?? 'NULL') . "</td>";
            echo "<td style='padding: 10px;'>" . ($food['price'] ?? 'NULL') . "</td>";
            echo "<td style='padding: 10px;'>" . htmlspecialchars($food['shop']) . "</td>";
            echo "<td style='padding: 10px;'>" . htmlspecialchars($food['photo']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // 測試 AJAX API
        echo "<h3>測試 AJAX API:</h3>";
        echo "<button onclick='testFoodsAPI()'>測試載入食品</button>";
        echo "<div id='apiResult'></div>";
        
    } else {
        echo "<p>資料庫中沒有食品記錄</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>錯誤: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<script>
async function testFoodsAPI() {
    const resultDiv = document.getElementById('apiResult');
    resultDiv.innerHTML = '<p>測試中...</p>';
    
    try {
        const formData = new FormData();
        formData.append('action', 'getFoods');
        
        const response = await fetch('foods_api.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            let html = '<h4>API 返回的資料:</h4>';
            html += '<pre style="background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto;">';
            html += JSON.stringify(data.data, null, 2);
            html += '</pre>';
            resultDiv.innerHTML = html;
        } else {
            resultDiv.innerHTML = '<p style="color: red;">API 錯誤: ' + data.message + '</p>';
        }
    } catch (error) {
        resultDiv.innerHTML = '<p style="color: red;">請求錯誤: ' + error.message + '</p>';
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