<?php
// 測試食品資料
require_once 'config.php';

try {
    $pdo = getDatabase();
    
    echo "<h2>食品資料測試</h2>";
    
    // 直接查詢資料庫
    echo "<h3>直接資料庫查詢:</h3>";
    $stmt = $pdo->query("SELECT * FROM food LIMIT 5");
    $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<pre>";
    print_r($foods);
    echo "</pre>";
    
    // 測試 API
    echo "<h3>測試 API 回應:</h3>";
    echo "<button onclick='testAPI()'>測試 getFoods API</button>";
    echo "<div id='apiResult'></div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>錯誤: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<script>
async function testAPI() {
    const resultDiv = document.getElementById('apiResult');
    resultDiv.innerHTML = '<p>測試中...</p>';
    
    try {
        const formData = new FormData();
        formData.append('action', 'getFoods');
        
        const response = await fetch('foods_api.php', {
            method: 'POST',
            body: formData
        });
        
        const text = await response.text();
        console.log('Raw API response:', text);
        
        const data = JSON.parse(text);
        console.log('Parsed API response:', data);
        
        if (data.success) {
            let html = '<h4>API 成功回應:</h4>';
            html += '<pre style="background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto;">';
            html += JSON.stringify(data.data, null, 2);
            html += '</pre>';
            
            // 測試第一筆資料的編輯
            if (data.data.length > 0) {
                const firstFood = data.data[0];
                html += '<h4>測試編輯第一筆資料:</h4>';
                html += '<p><strong>名稱:</strong> ' + firstFood.name + ' (類型: ' + typeof firstFood.name + ')</p>';
                html += '<p><strong>到期日期:</strong> ' + firstFood.todate + ' (類型: ' + typeof firstFood.todate + ')</p>';
                html += '<p><strong>數量:</strong> ' + firstFood.amount + ' (類型: ' + typeof firstFood.amount + ')</p>';
                html += '<p><strong>價格:</strong> ' + firstFood.price + ' (類型: ' + typeof firstFood.price + ')</p>';
                
                // 測試日期格式化
                const testDate = new Date(firstFood.todate);
                html += '<p><strong>日期測試:</strong> ' + testDate.toString() + '</p>';
                html += '<p><strong>格式化日期:</strong> ' + testDate.toLocaleDateString('zh-TW') + '</p>';
            }
            
            resultDiv.innerHTML = html;
        } else {
            resultDiv.innerHTML = '<p style="color: red;">API 錯誤: ' + data.message + '</p>';
        }
    } catch (error) {
        resultDiv.innerHTML = '<p style="color: red;">請求錯誤: ' + error.message + '</p>';
        console.error('Error:', error);
    }
}
</script>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background: #f5f5f5;
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
pre {
    background: white;
    padding: 15px;
    border-radius: 5px;
    border: 1px solid #ddd;
    overflow-x: auto;
}
</style>