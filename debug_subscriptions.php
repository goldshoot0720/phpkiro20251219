<?php
// 調試版本的訂閱管理
$host = '127.0.0.1';
$port = '3306';
$dbname = 'goldshoot0720';
$username = 'root';
$password = '';

echo "<h2>訂閱管理調試資訊</h2>";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
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