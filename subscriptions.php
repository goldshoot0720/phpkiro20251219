<?php
// 引入動態資料庫配置
require_once 'config.php';

try {
    $pdo = getDatabase();
} catch(Exception $e) {
    die("資料庫連接失敗: " . $e->getMessage());
}

// 處理AJAX請求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'getSubscriptions':
            try {
                $stmt = $pdo->query("SELECT *, ROW_NUMBER() OVER (ORDER BY nextdate ASC) as id FROM subscription ORDER BY nextdate ASC");
                $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $subscriptions]);
            } catch(PDOException $e) {
                echo json_encode(['success' => false, 'message' => '獲取訂閱資料失敗: ' . $e->getMessage()]);
            }
            break;
            
        case 'addSubscription':
            try {
                $name = $_POST['name'] ?? '';
                $nextdate = $_POST['nextdate'] ?? '';
                $price = $_POST['price'] ?? null;
                $price = ($price === '' || $price === null) ? null : (int)$price;
                $site = $_POST['site'] ?? '';
                $note = $_POST['note'] ?? '';
                $account = $_POST['account'] ?? '';
                
                $stmt = $pdo->prepare("INSERT INTO subscription (name, nextdate, price, site, note, account) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $nextdate, $price, $site, $note, $account]);
                
                echo json_encode(['success' => true, 'message' => '訂閱新增成功']);
            } catch(PDOException $e) {
                echo json_encode(['success' => false, 'message' => '新增訂閱失敗: ' . $e->getMessage()]);
            }
            break;
            
        case 'updateSubscription':
            try {
                $originalName = $_POST['originalName'] ?? '';
                $originalNextdate = $_POST['originalNextdate'] ?? '';
                $name = $_POST['name'] ?? '';
                $nextdate = $_POST['nextdate'] ?? '';
                $price = $_POST['price'] ?? null;
                $price = ($price === '' || $price === null) ? null : (int)$price;
                $site = $_POST['site'] ?? '';
                $note = $_POST['note'] ?? '';
                $account = $_POST['account'] ?? '';
                
                $stmt = $pdo->prepare("UPDATE subscription SET name=?, nextdate=?, price=?, site=?, note=?, account=? WHERE name=? AND nextdate=?");
                $stmt->execute([$name, $nextdate, $price, $site, $note, $account, $originalName, $originalNextdate]);
                
                echo json_encode(['success' => true, 'message' => '訂閱更新成功']);
            } catch(PDOException $e) {
                echo json_encode(['success' => false, 'message' => '更新訂閱失敗: ' . $e->getMessage()]);
            }
            break;
            
        case 'deleteSubscription':
            try {
                $name = $_POST['name'] ?? '';
                $nextdate = $_POST['nextdate'] ?? '';
                
                $stmt = $pdo->prepare("DELETE FROM subscription WHERE name=? AND nextdate=?");
                $stmt->execute([$name, $nextdate]);
                
                echo json_encode(['success' => true, 'message' => '訂閱刪除成功']);
            } catch(PDOException $e) {
                echo json_encode(['success' => false, 'message' => '刪除訂閱失敗: ' . $e->getMessage()]);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => '無效的操作']);
    }
    exit;
}
?>