<?php
// 新增 Kiro Pro 訂閱
require_once 'config.php';

// 設定本地環境變數
$_SERVER['HTTP_HOST'] = 'localhost';

try {
    $pdo = getDatabase();
    $pdo->exec("SET NAMES utf8mb4");
    
    echo "<h2>新增 Kiro Pro 訂閱</h2>";
    
    // 準備資料
    $name = "Kiro Pro";
    $nextdate = "2026-01-01";
    $price = 640;
    $site = "https://app.kiro.dev/account/usage";
    $note = "";
    $account = "";
    
    echo "<div style='background: #f0f0f0; padding: 15px; margin: 15px 0; border-radius: 8px;'>";
    echo "<h3>準備新增的訂閱資料：</h3>";
    echo "<p><strong>名稱:</strong> " . htmlspecialchars($name) . "</p>";
    echo "<p><strong>下次付款日期:</strong> " . htmlspecialchars($nextdate) . "</p>";
    echo "<p><strong>價格:</strong> NT$ " . htmlspecialchars($price) . "</p>";
    echo "<p><strong>網站:</strong> <a href='" . htmlspecialchars($site) . "' target='_blank'>" . htmlspecialchars($site) . "</a></p>";
    echo "</div>";
    
    // 檢查是否已存在相同的訂閱
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM subscription WHERE name = ? AND nextdate = ?");
    $checkStmt->execute([$name, $nextdate]);
    $exists = $checkStmt->fetchColumn();
    
    if ($exists > 0) {
        echo "<p style='color: orange;'>⚠️ 相同的訂閱已存在，跳過新增</p>";
    } else {
        // 執行插入
        $stmt = $pdo->prepare("INSERT INTO subscription (name, nextdate, price, site, note, account) VALUES (?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([$name, $nextdate, $price, $site, $note, $account]);
        
        if ($result) {
            echo "<p style='color: green;'>✅ 訂閱新增成功！</p>";
            
            // 查詢剛新增的資料
            echo "<h3>新增的訂閱：</h3>";
            $stmt = $pdo->prepare("SELECT * FROM subscription WHERE name = ? AND nextdate = ?");
            $stmt->execute([$name, $nextdate]);
            $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($subscription) {
                echo "<div style='background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 400px;'>";
                echo "<h4>" . htmlspecialchars($subscription['name']) . "</h4>";
                echo "<p><strong>下次付款日期:</strong> " . htmlspecialchars($subscription['nextdate']) . "</p>";
                echo "<p><strong>價格:</strong> NT$ " . htmlspecialchars($subscription['price']) . "</p>";
                echo "<p><strong>網站:</strong> <a href='" . htmlspecialchars($subscription['site']) . "' target='_blank'>" . htmlspecialchars($subscription['site']) . "</a></p>";
                echo "</div>";
            }
        } else {
            echo "<p style='color: red;'>❌ 新增失敗</p>";
        }
    }
    
    // 顯示所有訂閱
    echo "<h3>目前所有訂閱：</h3>";
    $stmt = $pdo->query("SELECT * FROM subscription ORDER BY nextdate ASC");
    $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div style='display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;'>";
    foreach ($subscriptions as $sub) {
        $isExpired = strtotime($sub['nextdate']) < time();
        $isExpiringSoon = strtotime($sub['nextdate']) < strtotime('+7 days');
        
        $borderColor = '#45b7d1';
        if ($isExpired) $borderColor = '#ff6b6b';
        elseif ($isExpiringSoon) $borderColor = '#ffa726';
        
        echo "<div style='background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-left: 4px solid $borderColor;'>";
        echo "<h4 style='margin: 0 0 10px 0; color: #333;'>" . htmlspecialchars($sub['name']) . "</h4>";
        echo "<p style='margin: 5px 0; color: #666;'><strong>下次付款:</strong> " . htmlspecialchars($sub['nextdate']) . "</p>";
        
        if ($sub['price'] && $sub['price'] > 0) {
            echo "<p style='margin: 5px 0; color: #4ecdc4; font-weight: bold;'><strong>價格:</strong> NT$ " . htmlspecialchars($sub['price']) . "</p>";
        }
        
        if ($sub['site']) {
            echo "<p style='margin: 5px 0; color: #666;'><strong>網站:</strong> <a href='" . htmlspecialchars($sub['site']) . "' target='_blank' style='color: #45b7d1; text-decoration: none;'>" . htmlspecialchars($sub['site']) . "</a></p>";
        }
        
        if ($sub['account']) {
            echo "<p style='margin: 5px 0; color: #666;'><strong>帳號:</strong> " . htmlspecialchars($sub['account']) . "</p>";
        }
        
        if ($sub['note']) {
            echo "<p style='margin: 5px 0; color: #666;'><strong>備註:</strong> " . htmlspecialchars($sub['note']) . "</p>";
        }
        
        echo "</div>";
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>錯誤: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<style>
body {
    font-family: 'Microsoft JhengHei', sans-serif;
    margin: 20px;
    background: #f5f5f5;
    color: #333;
}
h2, h3, h4 {
    color: #333;
}
a {
    color: #45b7d1;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style>