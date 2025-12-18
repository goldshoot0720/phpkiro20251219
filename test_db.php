<?php
// 測試資料庫連接
$host = '127.0.0.1';
$port = '3306';
$dbname = 'goldshoot0720';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
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