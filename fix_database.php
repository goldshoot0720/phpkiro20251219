<?php
// 修復資料庫問題
require_once 'config.php';

try {
    $pdo = getDatabase();
    
    echo "<h2>資料庫修復工具</h2>";
    
    // 檢查當前表結構
    echo "<h3>1. 檢查當前表結構</h3>";
    $stmt = $pdo->query("DESCRIBE food");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>欄位</th><th>類型</th><th>NULL</th><th>預設值</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 檢查問題資料
    echo "<h3>2. 檢查問題資料</h3>";
    $stmt = $pdo->query("SELECT * FROM food WHERE todate NOT REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' OR todate IS NULL OR todate = ''");
    $problemData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($problemData) > 0) {
        echo "<p style='color: red;'>發現 " . count($problemData) . " 筆問題資料:</p>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>名稱</th><th>到期日期</th><th>數量</th><th>價格</th><th>商店</th></tr>";
        foreach ($problemData as $data) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($data['name']) . "</td>";
            echo "<td style='color: red;'>" . htmlspecialchars($data['todate']) . "</td>";
            echo "<td>" . htmlspecialchars($data['amount']) . "</td>";
            echo "<td>" . htmlspecialchars($data['price']) . "</td>";
            echo "<td>" . htmlspecialchars($data['shop']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h4>修復選項:</h4>";
        echo "<form method='POST'>";
        echo "<button type='submit' name='action' value='delete_problem' onclick='return confirm(\"確定要刪除這些問題資料嗎？\")'>刪除問題資料</button> ";
        echo "<button type='submit' name='action' value='fix_dates' onclick='return confirm(\"嘗試修復日期格式？\")'>嘗試修復日期</button>";
        echo "</form>";
    } else {
        echo "<p style='color: green;'>✅ 沒有發現問題資料</p>";
    }
    
    // 處理修復操作
    if ($_POST['action'] ?? '') {
        echo "<h3>3. 執行修復</h3>";
        
        switch ($_POST['action']) {
            case 'delete_problem':
                $stmt = $pdo->prepare("DELETE FROM food WHERE todate NOT REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$' OR todate IS NULL OR todate = ''");
                $result = $stmt->execute();
                $affected = $stmt->rowCount();
                echo "<p style='color: green;'>✅ 已刪除 $affected 筆問題資料</p>";
                break;
                
            case 'fix_dates':
                // 嘗試修復只有年份的日期
                $stmt = $pdo->prepare("UPDATE food SET todate = CONCAT(todate, '-01-01') WHERE todate REGEXP '^[0-9]{4}$'");
                $result = $stmt->execute();
                $affected = $stmt->rowCount();
                echo "<p style='color: green;'>✅ 已修復 $affected 筆日期資料（設為年份-01-01）</p>";
                break;
        }
        
        echo "<p><a href='fix_database.php'>重新檢查</a></p>";
    }
    
    // 顯示所有資料
    echo "<h3>4. 當前所有資料</h3>";
    $stmt = $pdo->query("SELECT * FROM food ORDER BY name");
    $allData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($allData) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>名稱</th><th>到期日期</th><th>數量</th><th>價格</th><th>商店</th><th>圖片</th><th>操作</th>";
        echo "</tr>";
        
        foreach ($allData as $data) {
            $dateClass = '';
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['todate'])) {
                $dateClass = 'style="background: #ffcccc;"';
            }
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($data['name']) . "</td>";
            echo "<td $dateClass>" . htmlspecialchars($data['todate']) . "</td>";
            echo "<td>" . htmlspecialchars($data['amount']) . "</td>";
            echo "<td>" . htmlspecialchars($data['price']) . "</td>";
            echo "<td>" . htmlspecialchars($data['shop']) . "</td>";
            echo "<td>" . htmlspecialchars($data['photo']) . "</td>";
            echo "<td>";
            echo "<form method='POST' style='display: inline;'>";
            echo "<input type='hidden' name='delete_name' value='" . htmlspecialchars($data['name']) . "'>";
            echo "<input type='hidden' name='delete_todate' value='" . htmlspecialchars($data['todate']) . "'>";
            echo "<button type='submit' name='action' value='delete_single' onclick='return confirm(\"確定刪除？\")'>刪除</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>沒有資料</p>";
    }
    
    // 處理單筆刪除
    if (($_POST['action'] ?? '') === 'delete_single') {
        $stmt = $pdo->prepare("DELETE FROM food WHERE name = ? AND todate = ?");
        $stmt->execute([$_POST['delete_name'], $_POST['delete_todate']]);
        echo "<script>location.reload();</script>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>錯誤: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { border-collapse: collapse; margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
th { background: #f0f0f0; }
button { padding: 5px 10px; margin: 2px; cursor: pointer; }
.delete-btn { background: #ff4444; color: white; border: none; border-radius: 3px; }
.fix-btn { background: #4CAF50; color: white; border: none; border-radius: 3px; }
</style>