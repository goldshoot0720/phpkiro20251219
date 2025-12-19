<?php
// 引入動態資料庫配置
require_once 'config.php';

// 顯示當前配置
$currentConfig = getCurrentConfig();
echo "<h2>資料庫連接測試</h2>";
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
    
    echo "資料庫連接成功！<br>";
    
    // 測試查詢
    $stmt = $pdo->query("SELECT * FROM subscription");
    $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "找到 " . count($subscriptions) . " 筆訂閱記錄：<br>";
    
    foreach ($subscriptions as $sub) {
        echo "- " . $sub['name'] . " (下次付款: " . $sub['nextdate'] . ", 價格: NT$" . $sub['price'] . ")<br>";
    }
    
} catch(PDOException $e) {
    echo "資料庫連接失敗: " . $e->getMessage();
}
?>