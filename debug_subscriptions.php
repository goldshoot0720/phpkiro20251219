<?php
// 引入動態資料庫配置
require_once 'config.php';

echo "<h2>訂閱管理調試資訊</h2>";

// 顯示當前配置
$currentConfig = getCurrentConfig();
echo "<div style='background: #f0f0f0; padding: 15px; margin: 15px 0; border-radius: 8px; color: #333;'>";
echo "<h3>當前配置資訊</h3>";
echo "<p><strong>當前網址:</strong> " . $currentConfig['current_host'] . "</p>";
echo "<p><strong>環境:</strong> " . ($currentConfig['environment'] === 'local' ? '本地開發' : '遠端生產') . "</p>";
echo "<p><strong>資料庫主機:</strong> " . $currentConfig['database_host'] . "</p>";
echo "<p><strong>資料庫名稱:</strong> " . $currentConfig['database_name'] . "</p>";
echo "<p><strong>資料庫用戶:</strong> " . $currentConfig['database_user'] . "</p>";
echo "</div>";

try {
    $pdo = getDatabase();
    
    echo "<p style='color: green;'>✅ 資料庫連接成功！</p>";
    
    // 檢查資料表是否存在
    $stmt = $pdo->query("SHOW TABLES LIKE 'subscription'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "<p style='color: green;'>✅ subscription 資料表存在</p>";
        
        // 查詢所有資料
        $stmt = $pdo->query("SELECT * FROM subscription ORDER BY nextdate ASC");
        $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p>📊 找到 " . count($subscriptions) . " 筆訂閱記錄</p>";
        
        if (count($subscriptions) > 0) {
            echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
            echo "<tr style='background: #f0f0f0;'>";
            echo "<th style='padding: 10px;'>名稱</th>";
            echo "<th style='padding: 10px;'>下次付款日期</th>";
            echo "<th style='padding: 10px;'>價格</th>";
            echo "<th style='padding: 10px;'>網站</th>";
            echo "<th style='padding: 10px;'>帳號</th>";
            echo "<th style='padding: 10px;'>備註</th>";
            echo "</tr>";
            
            foreach ($subscriptions as $sub) {
                echo "<tr>";
                echo "<td style='padding: 10px;'>" . htmlspecialchars($sub['name']) . "</td>";
                echo "<td style='padding: 10px;'>" . htmlspecialchars($sub['nextdate']) . "</td>";
                echo "<td style='padding: 10px;'>NT$ " . htmlspecialchars($sub['price']) . "</td>";
                echo "<td style='padding: 10px;'>" . htmlspecialchars($sub['site']) . "</td>";
                echo "<td style='padding: 10px;'>" . htmlspecialchars($sub['account']) . "</td>";
                echo "<td style='padding: 10px;'>" . htmlspecialchars($sub['note']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // 測試 AJAX 請求
        echo "<h3>測試 AJAX 請求</h3>";
        echo "<button onclick='testAjax()'>測試載入訂閱</button>";
        echo "<div id='ajaxResult'></div>";
        
    } else {
        echo "<p style='color: red;'>❌ subscription 資料表不存在</p>";
    }
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ 資料庫連接失敗: " . $e->getMessage() . "</p>";
}
?>

<script>
async function testAjax() {
    try {
        const formData = new FormData();
        formData.append('action', 'getSubscriptions');
        
        const response = await fetch('subscriptions.php', {
            method: 'POST',
            body: formData
        });
        
        const text = await response.text();
        console.log('Raw response:', text);
        
        const data = JSON.parse(text);
        
        document.getElementById('ajaxResult').innerHTML = 
            '<h4>AJAX 測試結果:</h4>' +
            '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
            
    } catch (error) {
        document.getElementById('ajaxResult').innerHTML = 
            '<h4 style="color: red;">AJAX 測試失敗:</h4>' +
            '<pre>' + error.message + '</pre>';
    }
}
</script>